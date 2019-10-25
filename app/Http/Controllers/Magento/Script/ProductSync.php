<?php


namespace App\Http\Controllers\Magento\Script;


use App\Http\Controllers\Magento\Helper;
use App\Http\Controllers\Magento\Inventory;
use App\Http\Controllers\Magento\Product;
use Carbon\Carbon;

class ProductSync
{
    protected const productFieldsToParse = [
        'entity_id',
        'url',
        'name',
        'sku',
        'stock_id',
//        'price',
//        'final_price',
        'description',
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
                $price = $storedProduct->price()->updateOrCreate([
                    'amount' => $product->final_price ?? 0
                ]);
                $discount = $price->discount()->updateOrCreate([
                    'type' => 'flat',
                    'amount' => $product->price
                ]);
                $price->discount_id = $discount->id;
                $price->save();

                $storedProduct->stores()->sync(
                    [
                        1 => ['qty' => $product->qty + rand(-10, 10) ?? 0],
                        2 => ['qty' => $product->qty ?? 0]
                    ]
                );
//                }
            }
        }
    }

    public static function syncStock(\App\Product $product)
    {
        $inventory_api = new Inventory();
        $snapshot_stock = $product->magento_stock;
        $current_stock = $product->laravel_stock;
        $stock_data = $inventory_api->getStockData($product->stock_id);
        $live_stock = (int)$stock_data->qty;

        if (
            is_null($snapshot_stock) ||
            is_null($current_stock) ||
            is_null($stock_data) ||
            is_null($live_stock)
        ) {
            return;
        }

        // final qty after sells and refunds
        $difference = $snapshot_stock - $current_stock;

        $final_stock = $live_stock - $difference;
        echo $product->magento_id;
        echo ' Started: ' . $snapshot_stock;
        echo ' Laravel: ' . $current_stock;
        echo ' Magento: ' . $live_stock;
        echo ' Difference: ' . $difference;
        echo ' Final: ' . $final_stock;
        echo PHP_EOL;

        if ($final_stock != $live_stock) {
            $inventory_api->updateStockData([
                'item_id' => $stock_data->item_id,
                'qty' => $final_stock,
            ]);
            $product->stores()->sync(
                [
                    1 => ['qty' => $product->qty ?? 0],
                    2 => ['qty' => $product->qty ?? 0]
                ]
            );
        }
    }
}
