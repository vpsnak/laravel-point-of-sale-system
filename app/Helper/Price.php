<?php

namespace App\Helper;

use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;

class Price
{
    public static function newFormatter()
    {
        $currencies = new ISOCurrencies();
        return new DecimalMoneyFormatter($currencies);
    }

    public static function newMoney(array $price = null)
    {
        $amount = 0;
        $currency = 'USD';

        if (isset($price['amount'])) {
            $amount = (int) $price['amount'];
        }
        if (isset($price['currency'])) {
            $currency = $price['currency'];
        }

        return new Money($amount, static::newCurrency($currency));
    }

    public static function newCurrency(string $currency = null)
    {
        if ($currency) {
            return new Currency($currency);
        } else {
            return new Currency('USD');
        }
    }

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
