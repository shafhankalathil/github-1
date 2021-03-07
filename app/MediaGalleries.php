<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MediaGalleries extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid', 'media_type', 'path', 'filename'
    ];
}
