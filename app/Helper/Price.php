<?php

namespace App\Helper;

use Money\Currency;
use Money\Money;

class Price
{
    public static function calculateItemDiscount(array $item, string $currency)
    {
        $price = new Money($item['price']['amount'], new Currency($currency));
        if (isset($item['discount']) && isset($item['discount']['amount']) && isset($item['discount']['type'])) {
            switch ($item['discount']['type']) {
                case 'flat':
                    $amount = new Money($item['discount']['amount'], new Currency($currency));
                    $price = $price->subtract($amount);
                    break;
                case 'percentage':
                    $percentage = $price->multiply($item['discount']['amount'] / 100);
                    $price = $price->subtract($percentage);
                    break;
                default:
                    break;
            }
        }
        return $price;
    }
}
