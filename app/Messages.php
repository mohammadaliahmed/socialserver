<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Messages extends Model
{
    //
    protected $fillable = [
        'id', 'messageText', 'messageType', 'messageByName', 'imageUrl', 'audioUrl', 'messageById', 'roomId', 'time'
    ];
}
