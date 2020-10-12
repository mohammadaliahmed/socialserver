<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Posts extends Model
{
    protected $fillable = [
        'id', 'user_id', 'images_url', 'video_url', 'post_type', 'deleted', 'created_at','random_id','time','video_image_url'
    ];
    //
}
