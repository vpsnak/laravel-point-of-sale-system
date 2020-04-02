<?php

namespace App\Http\Controllers;

use App\Product;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function all()
    {
        return response(Product::paginate(9));
    }

    public function getOne(Product $model)
    {
        return response($model);
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'sku' => 'required|string|unique:products,sku',
            'url' => 'nullable|string',
            'photo_url' => 'nullable|string',
            'description' => 'nullable|string',
            'is_discountable' => 'required|boolean',
            'is_price_editable' => 'required|boolean',
            'price' => 'required|array',
            'price.amount' => 'required|integer',
            'price.currency' => 'required|string|size:3',
        ]);

        $validatedExtra = $request->validate([
            'categories' => 'nullable|array',
            'stores' => 'nullable|array'
        ]);
        $product = Product::create($validatedData);
        $product->categories()->sync($validatedExtra['categories']);
        foreach ($validatedExtra['stores'] as $store) {
            if (!empty($store['pivot'])) {
                $product->stores()->syncWithoutDetaching(
                    [$store['id'] => ['qty' => $store['pivot']['qty'] ?? 0]]
                );
            }
        }

        return response(['notification' => [
            'msg' => ["Product {$product->name} created successfully"],
            'type' => 'success'
        ]]);
    }


    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|exists:products,id',
            'name' => 'required|string',
            'sku' => 'required|string|unique:products,sku,' . $request->id,
            'url' => 'nullable|string',
            'photo_url' => 'nullable|string',
            'description' => 'nullable|string',
            'is_price_editable' => 'nullable|boolean',
            'is_discountable' => 'required|boolean',
            'price' => 'required|array',
            'price.amount' => 'required|integer',
            'price.currency' => 'required|string|size:3',
        ]);

        $validatedExtra = $request->validate([
            'categories' => 'nullable|array',
            'stores' => 'nullable|array'
        ]);

        $product = Product::findOrFail($validatedData['id']);

        $product->categories()->sync($validatedExtra['categories']);

        foreach ($validatedExtra['stores'] as $store) {
            if (!empty($store['pivot'])) {
                $product->stores()->syncWithoutDetaching(
                    [$store['id'] => ['qty' => $store['pivot']['qty'] ?? 0]]
                );
            }
        }

        $product->update($validatedData);

        return response(['notification' => [
            'msg' => ["Product {$product->name} updated successfully"],
            'type' => 'success'
        ]]);
    }


    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required|string'
        ]);

        $columns = ['sku', 'name', 'description'];
        $query = Product::query()->search($columns, $validatedData['keyword']);

        return response($query->paginate(10));
    }

    public function getBarcode(Product $product)
    {
        $barcode = new BarcodeGenerator();
        $barcode->setText($product->sku);
        $barcode->setType(BarcodeGenerator::Code39);
        $barcode->setScale(2);
        $barcode->setThickness(25);
        $barcode->setFontSize(10);
        $code = $barcode->generate();
        return response([
            'barcode' => $code,
            'type' => 'data:image/png;base64'
        ]);
    }

    public function productBarcode(Product $product)
    {
        return view('product_barcode')->with([
            'product' => $product,
        ]);
    }
}
