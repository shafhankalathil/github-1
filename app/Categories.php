<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_name', 'parent_id',
    ];

    // One level child
    public function child() {
        return $this->hasMany('App\Categories', 'parent_id');
    }

    // Recursive children
    public function children() {
        return $this->hasMany('App\Categories', 'parent_id')->with('children');
    }

    // One level parent
    public function parent() {
        return $this->belongsTo('App\Categories', 'parent_id');
    }

    // Recursive parents
    public function parents() {
        return $this->belongsTo('App\Categories', 'parent_id')->with('parent');
    }

}
