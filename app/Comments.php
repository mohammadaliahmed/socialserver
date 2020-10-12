<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comments extends Model
{
    //
    protected $fillable = [
        'id', 'user_id', 'post_id','text','time'
    ];
}
