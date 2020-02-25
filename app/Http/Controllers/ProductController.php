<?php

namespace App\Http\Controllers;

use App\Product;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductController extends Controller
{

    public function all()
    {
        return response(Product::paginate());
    }

    public function getOne($model)
    {
        return response(Product::findOrFail($model));
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'sku' => 'required|string',
            'url' => 'nullable|string',
            'photo_url' => 'nullable|string',
            'description' => 'nullable|string',
            'editable_price' => 'nullable|boolean'
        ]);

        $validatedExtra = $request->validate([
            'categories' => 'nullable|array',
            'stores' => 'required|array',
            'final_price' => 'required|numeric',
        ]);

        $product = Product::create($validatedData);

        $product->price()->updateOrCreate(['amount' => $validatedExtra['final_price']]);

        $product->categories()->sync($validatedExtra['categories']);

        foreach ($validatedExtra['stores'] as $store) {
            if (!empty($store['pivot'])) {
                $product->stores()->syncWithoutDetaching(
                    [$store['id'] => ['qty' => $store['pivot']['qty'] ?? 0]]
                );
            }
        }
        return response(['info' => ['Product ' . $product->name . ' created successfully!']], 201);
    }


    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|exists:products,id',
            'name' => 'required|string',
            'sku' => 'required|string',
            'url' => 'nullable|string',
            'photo_url' => 'nullable|string',
            'description' => 'nullable|string',
            'editable_price' => 'nullable|boolean',
        ]);

        $validatedExtra = $request->validate([
            'categories' => 'nullable|array',
            'stores' => 'required|array',
            'final_price' => 'required|numeric',
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

        $product->price->amount = $validatedExtra['final_price'];

        $product->fill($validatedData);
        $product->save();

        return response(['info' => ["Product {$product->name} updated successfully!"]]);
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required|string'
        ]);

        $columns = ['sku', 'name', 'description'];
        $query = Product::query()->search($columns, $validatedData['keyword']);

        return response($query->paginate());
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
        ], 200);
    }
}
