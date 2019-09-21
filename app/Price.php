<?php

namespace App;

class Price extends BaseModel
{
    public function priceable()
    {
        return $this->morphTo();
    }
}
