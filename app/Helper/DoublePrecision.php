<?php

namespace App\Helper;

class DoublePrecision
{
    public static function doublePrecision(float $float, int $precision)
    {
        return bcdiv($float, 1, $precision);
    }
}
