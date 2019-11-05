<?php

namespace App\Http\Controllers;

use App\Product;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductController extends BaseController
{
    protected $model = Product::class;

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'sku' => 'required|string',
            'url' => 'nullable|string',
            'photo_url' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $validatedExtra = $request->validate([
            'id' => 'nullable|exists:products,id',
            'categories' => 'required|array',
            'stores' => 'required|array',
            'final_price' => 'required|numeric',
        ]);

        $product = $this->getProduct($validatedExtra['id'] ?? null, $validatedData);

        $product->categories()->sync($validatedExtra['categories']);

        foreach ($validatedExtra['stores'] as $store) {
            if (!empty($store['pivot'])) {
                $product->stores()->syncWithoutDetaching(
                    [$store['id'] => ['qty' => $store['pivot']['qty'] ?? 0]]
                );
            }
        }

//        @TODO fix amount create or update
        $product->price()->updateOrCreate(['amount' => $validatedExtra['final_price']]);
        $product->price->save();

//        $product->price->amount = $validatedExtra['final_price'];
//        $product->price->save();
        return response($product, 200);
    }

    private function getProduct($id, $data)
    {
        if (!empty($id)) {
            $this->model::updateData($id, $data);
            $model = $this->model::getOne($id);
        } else {
            $model = $this->model::store($data);
        }
        return $model;
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required|string'
        ]);

        return $this->searchResult(
            ['sku', 'name', 'description'],
            $validatedData['keyword'],
            true
        );
    }

    /**
     * @return ResponseFactory|Response
     */
    public function all()
    {
        if (!isset($this->model)) {
            return response('Model not found', 404);
        }

        return response($this->model::paginate(), 200);
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
