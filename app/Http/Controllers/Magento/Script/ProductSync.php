<?php


namespace App\Http\Controllers\Magento\Script;


use App\Http\Controllers\Magento\Helper;
use App\Http\Controllers\Magento\Product;
use Carbon\Carbon;

class ProductSync
{
    protected const productFieldsToParse = [
        'entity_id',
        'url',
        'name',
        'sku',
        'price',
//        'final_price',
//        'description',
        'image_url',
//        'manage_stock',
//        'qty',
    ];

    protected const productFieldsToRename = [
        'entity_id' => 'magento_id',
        'image_url' => 'photo_url',
    ];

    public static function getFromMagento()
    {
        $client = new Product();

        $page = 1;
        $per_page = 100;
        $categoriesRetrieved = $per_page;
        while ($categoriesRetrieved == $per_page) {
            $products = $client->getAllEntries($per_page, $page++);
            if (empty($products)) {
                break;
            }
            foreach ($products as $product) {
                $storedProduct = \App\Product::getFirst('sku', $product->sku);
                $productUpdateAt = Carbon::parse($product->updated_at);
//                if (empty($storedProduct) || $productUpdateAt->greaterThan($storedProduct->updated_at)) {
                    $parsedProduct = Helper::getParsedData($product, self::productFieldsToParse,
                        self::productFieldsToRename);
                    $storedProduct = \App\Product::updateOrCreate(
                        ['sku' => $product->sku],
                        $parsedProduct
                    );
                $storedProduct->price()->updateOrCreate([
                    'amount' => $product->final_price ?? 0
                ]);;
                    $storedProduct->stores()->sync([2 => ['qty' => $product->qty ?? 0]]);
//                    dd($storedProduct);
                }
//            }
        }
    }
}
