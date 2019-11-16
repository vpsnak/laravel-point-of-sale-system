<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostalCodeGrid extends Model
{
    protected $table = 'postalcodegrid';

    public function group()
    {
        return $this->belongsTo(PostalGroup::class, 'postalgroupgrid_id', 'postalgroup_id');
    }
}
