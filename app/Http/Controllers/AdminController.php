<?php

namespace App\Http\Controllers;

use App\Posts;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Mockery\Exception;

class AdminController extends Controller
{

    public function admin()
    {
        $picArray = array();
        $posts = Posts::orderBy('id', 'desc')->get();
        foreach ($posts as $post) {
            $post->user = User::find($post->user_id);
            if (str_contains($post->images_url, ',')) {
                $picArray = explode(",", $post->images_url);
                $post->pictures = $picArray;
            }
        }
//        return $posts;
        return view('admin', compact('posts'));


    }

    public function deletepicture($id)
    {
        $picArray = array();
        $post = Posts::find($id);
        if (str_contains($post->images_url, ',')) {
            $picArray = explode(",", $post->images_url);

        }
        foreach ($picArray as $pic) {


            $file_path = 'public/images/' . $pic;
            try {
                // do something
                unlink($file_path);
            } catch (\Throwable $e) {


            }
        }

        $post->delete();


        return Redirect()->back();


    }
}
