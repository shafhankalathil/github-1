<?php

namespace App\Traits;

use App\User;
use Illuminate\Support\Str;

trait UtilityTraits{
    public function randomStringGenerator($length = 0){
        return Str::random($length);
    }

}
