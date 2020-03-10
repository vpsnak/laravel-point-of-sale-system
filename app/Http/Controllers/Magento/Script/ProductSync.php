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
        'description',
        'image_url',
        'plantcare_pdf',
    ];

    protected const productFieldsToRename = [
        'entity_id' => 'magento_id',
        'image_url' => 'photo_url',
    ];

    public static function getFromMagento($force = false)
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
                $storedProduct = \App\Product::where('sku', $product->sku)->first();
                $productUpdateAt = Carbon::parse($product->updated_at);
                if ($force || empty($storedProduct) || $productUpdateAt->greaterThan($storedProduct->updated_at)) {
                    $parsedProduct = Helper::getParsedData(
                        $product,
                        self::productFieldsToParse,
                        self::productFieldsToRename
                    );

                    $parsedProduct = static::parsePrice($parsedProduct, $product);

                    $storedProduct = \App\Product::updateOrCreate(
                        ['sku' => $product->sku],
                        $parsedProduct
                    );

                    $storedProduct->stores()->syncWithoutDetaching(
                        [
                            \App\Product::LARAVEL_STORE_ID => ['qty' => $product->qty ?? 0],
                            \App\Product::MAGENTO_STORE_ID => ['qty' => $product->qty ?? 0]
                        ]
                    );
                }
            }
        }
    }

    private static function parsePrice(array $parsedProduct, object $product)
    {
        $price = $product->price;
        $final_price = $product->final_price;

        unset($parsedProduct['price']);
        unset($parsedProduct['final_price']);

        $parsedProduct['price'] = [
            'amount' => $price * 100 ?? 0,
            'currency' => 'USD',
        ];
        $discountAmount = ($price - $final_price) * 100;

        $parsedProduct['discount'] = [
            'amount' => $discountAmount,
            'currency' => 'USD',
            'type' => $discountAmount > 0 ? 'flat' : 'none',

        ];

        return $parsedProduct;
    }

    /**
     * Used to sync product qty with magento and calculate new qty
     *
     * @param \App\Product $product
     */
    public static function syncStock(\App\Product $product)
    {
        $inventory_api = new Inventory();
        $snapshot_stock = $product->magento_stock;
        $current_stock = $product->laravel_stock;
        $stock_data = $inventory_api->getStockData($product->stock_id);
        $live_stock = (int) $stock_data->qty;

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
        if (app()->runningInConsole()) {
            echo $product->magento_id;
            echo ' Started: ' . $snapshot_stock;
            echo ' Laravel: ' . $current_stock;
            echo ' Magento: ' . $live_stock;
            echo ' Difference: ' . $difference;
            echo ' Final: ' . $final_stock;
            echo PHP_EOL;
        }

        if ($final_stock != $live_stock) {
            $inventory_api->updateStockData([
                'item_id' => $stock_data->item_id,
                'qty' => $final_stock,
            ]);
            $product->stores()->syncWithoutDetaching(
                [
                    \App\Product::LARAVEL_STORE_ID => ['qty' => $final_stock],
                    \App\Product::MAGENTO_STORE_ID => ['qty' => $final_stock]
                ]
            );
        }
    }
}
