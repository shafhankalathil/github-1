<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductCategories extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id', 'categories',
    ];
}
