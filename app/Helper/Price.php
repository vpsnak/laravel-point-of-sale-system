<?php


namespace App\Helper;


class Price
{
    public static function calculateDiscount($price, $type, $amount)
    {
        switch (strtolower($type)) {
            case 'percentage':
                $price = $price - ($price * ($amount / 100));
                break;
            case 'flat':
                $price = $price - $amount;
                break;
        }
        return $price;
    }

    public static function calculateTax($price, $tax)
    {
        return $price + ($price * ($tax / 100));
    }
}