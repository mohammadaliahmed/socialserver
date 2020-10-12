<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stories extends Model
{
    //
    protected $fillable = [
        'id', 'user_id', 'image_url', 'video_url', 'story_type', 'deleted', 'created_at'
    ];
}
