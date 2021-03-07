<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attributes extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'attribute_sets_id','attribute_name',
    ];
}
