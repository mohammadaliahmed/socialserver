<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Friends extends Model
{
    //

    protected $fillable = [
        'id', 'user_one', 'user_two', 'type'
    ];


}
