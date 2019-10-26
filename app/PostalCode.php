<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostalCode extends Model
{
    protected $table = 'postalcode';

    public function grid()
    {
        return $this->belongsTo(PostalCodeGrid::class, 'postalcode_id', 'postcode_id');
    }
}
