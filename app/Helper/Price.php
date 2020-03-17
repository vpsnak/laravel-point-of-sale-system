<?php

namespace App\Helper;

use Money\Currency;
use Money\Money;

class Price
{
    public static function calculateItemDiscount(array $item, string $currency)
    {
        $price = new Money($item['price']['amount'], new Currency($currency));
        return static::calculateDiscount($price, $item['discount']);
    }

    public static function calculateDiscount(Money $price, array $discount)
    {
        if (isset($discount['amount']) && isset($discount['type'])) {
            switch ($discount['type']) {
                case 'flat':
                    $amount = new Money($discount['amount'], $price->getCurrency());
                    $price = $price->subtract($amount);
                    break;
                case 'percentage':
                    $percentage = $price->multiply($price['amount'] / 100);
                    $price = $price->subtract($percentage);
                    break;
                default:
                    break;
            }
        }
        return $price;
    }
}
