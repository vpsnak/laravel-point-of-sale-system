<?php

namespace App\Helper;

class PhpHelper
{
    public static function debug($variable,  $use_dd = true, $die = true, $limits = false)
    {
        if (!$limits) {
            ini_set('xdebug.var_display_max_depth', '20');
            ini_set('xdebug.var_display_max_children', '512');
            ini_set('xdebug.var_display_max_data', '2048');
        }
        if ($use_dd) {
            dd($variable);
        } else {
            var_dump($variable);
            if ($die) {
                die;
            }
        }
    }
}
