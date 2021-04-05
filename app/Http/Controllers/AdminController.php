<?php

namespace App\Http\Controllers;

use App\Posts;
use App\Stories;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Mockery\Exception;

class AdminController extends Controller
{

    public function admin()
    {
        $picArray = array();
        $posts = Posts::orderBy('id', 'desc')->get()->limit(100);
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

    public function stories()
    {
        $stories = Stories::orderBy('id', 'desc')->get();
        foreach ($stories as $story) {
            $story->user = User::find($story->user_id);

        }
//        return $posts;
        return view('story', compact('stories'));


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

    public function deleteStory($id)
    {
        $story = Stories::find($id);

        $file_path = 'public/images/' . $story->url;
        try {
            // do something
            unlink($file_path);
        } catch (\Throwable $e) {
        }

        $story->delete();


        return Redirect()->back();


    }
}
