<?php

namespace App\Http\Controllers;

use App\Product;
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
            'category_ids' => 'required|array',
            'final_price' => 'required|numeric',
        ]);

        $product = $this->getProduct($validatedExtra['id'] ?? null, $validatedData);

        $product->categories()->sync($validatedExtra['category_ids']);

//        @TODO fix amount create or update
//        $product->price()->updateOrCreate(['amount' => $validatedExtra['final_price']]);
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
            'keyword' => 'required|string',
            'per_page' => 'nullable|numeric',
            'page' => 'nullable|numeric',
        ]);

        return $this->searchResult(['sku', 'name'],
            $validatedData['keyword'],
            array_key_exists('per_page', $validatedData) ? $validatedData['per_page'] : 0,
            array_key_exists('page', $validatedData) ? $validatedData['page'] : 0
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

        return response($this->model::limit(30)->get(), 200);
    }
}
