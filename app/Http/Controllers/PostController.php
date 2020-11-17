<?php

namespace App\Http\Controllers;

use App\Constants;
use App\Likes;
use App\Posts;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use function sizeof;

class PostController extends Controller
{
    //

    public function addPost(Request $request)
    {
        if ($request->api_username != Constants::$API_USERNAME && $request->api_password != Constants::$API_PASSOWRD) {
            return response()->json([
                'code' => Response::HTTP_FORBIDDEN, 'message' => "Wrong api credentials"
            ], Response::HTTP_OK);
        } else {

            $post = new Posts();
            $post->user_id = $request->id;
            $post->images_url = $request->images_url;
            $post->video_url = $request->video_url;
            $post->video_image_url = $request->video_image_url;
            $post->post_type = $request->post_type;
            $post->deleted = $request->deleted;
            $post->random_id = $request->random_id;
            $post->time = $request->time;

            $post->save();
            return response()->json([
                'code' => Response::HTTP_OK, 'message' => "Post Added"
                ,
            ], Response::HTTP_OK);
        }

    }

    public function deletePost(Request $request)
    {
        if ($request->api_username != Constants::$API_USERNAME && $request->api_password != Constants::$API_PASSOWRD) {
            return response()->json([
                'code' => Response::HTTP_FORBIDDEN, 'message' => "Wrong api credentials"
            ], Response::HTTP_OK);
        } else {

            $post = Posts::find($request->post_id);
            $post->delete();

            return response()->json([
                'code' => Response::HTTP_OK, 'message' => "Post deleted"
                ,
            ], Response::HTTP_OK);
        }

    }

    public function getUsersByPostLikes(Request $request)
    {
        if ($request->api_username != Constants::$API_USERNAME && $request->api_password != Constants::$API_PASSOWRD) {
            return response()->json([
                'code' => Response::HTTP_FORBIDDEN, 'message' => "Wrong api credentials"
            ], Response::HTTP_OK);
        } else {

            $users = DB::select('Select * from users where id in 
                  (Select user_id from likes where post_id=' . $request->id . ')');

            return response()->json([
                'code' => Response::HTTP_OK, 'message' => "false", 'users' => $users
                ,
            ], Response::HTTP_OK);
        }

    }

    public function likeUnlikePost(Request $request)
    {
        if ($request->api_username != Constants::$API_USERNAME && $request->api_password != Constants::$API_PASSOWRD) {
            return response()->json([
                'code' => Response::HTTP_FORBIDDEN, 'message' => "Wrong api credentials"
            ], Response::HTTP_OK);
        } else {

            $likes = DB::table('likes')->where('user_id', $request->userId)
                ->where('post_id', $request->postId)->get();

            if (sizeof($likes)) {
                $likes = Likes::find($likes[0]->id);
                $likes->delete();
            } else {
                $likes = new Likes();
                $likes->user_id = $request->userId;
                $likes->post_id = $request->postId;
                $likes->save();
            }

            return response()->json([
                'code' => Response::HTTP_OK, 'message' => "Liked"
                ,
            ], Response::HTTP_OK);
        }

    }

    public function allPosts(Request $request)
    {
        if ($request->api_username != Constants::$API_USERNAME && $request->api_password != Constants::$API_PASSOWRD) {
            return response()->json([
                'code' => Response::HTTP_FORBIDDEN, 'message' => "Wrong api credentials"
            ], Response::HTTP_OK);
        } else {

            $posts = DB::select("Select * from posts where user_id in (" . $request->friends . ") order by id desc limit 100");
            foreach ($posts as $post) {
                $user = User::find($post->user_id);
                $post->user = $user;
                $commentCount = DB::table('comments')->where('post_id', $post->id)->get()->count();
                $lastComment = DB::select("select users.name, comments.text from users,
                                            comments where users.id=comments.user_id and post_id=" . $post->id . " limit 1");
//                $lastComment = DB::table('comments')
//                    ->where('post_id', $post->id)->limit(1)->get();

                $likesCount = DB::table('likes')->where('post_id', $post->id)->count();
                $post->likesCount = $likesCount;

                $post->commentsCount = $commentCount;

                $post->lastComment = $lastComment;


            }

            $likes = DB::table('likes')->where('user_id', $request->id)
                ->orderBy('id', 'desc')->limit(50)->get()->pluck('post_id');
            return response()->json([
                'code' => Response::HTTP_OK, 'message' => "false", 'posts' => $posts
                , 'likes' => $likes

                ,
            ], Response::HTTP_OK);
        }

    }

    public function viewPost(Request $request)
    {
        if ($request->api_username != Constants::$API_USERNAME && $request->api_password != Constants::$API_PASSOWRD) {
            return response()->json([
                'code' => Response::HTTP_FORBIDDEN, 'message' => "Wrong api credentials"
            ], Response::HTTP_OK);
        } else {

            $posts = DB::select("Select * from posts where id=" . $request->post_id);
            foreach ($posts as $post) {
                $user = User::find($post->git);
                $post->user = $user;
                $commentCount = DB::table('comments')->where('post_id', $post->id)->get()->count();
                $lastComment = DB::select("select users.name, comments.text from users,
                                            comments where users.id=comments.user_id and post_id=" . $post->id . " limit 1");
//                $lastComment = DB::table('comments')
//                    ->where('post_id', $post->id)->limit(1)->get();

                $likesCount = DB::table('likes')->where('post_id', $post->id)->count();
                $post->likesCount = $likesCount;

                $post->commentsCount = $commentCount;

                $post->lastComment = $lastComment;


            }

            $likes = DB::table('likes')->where('user_id', $request->id)
                ->orderBy('id', 'desc')->limit(50)->get()->pluck('post_id');
            return response()->json([
                'code' => Response::HTTP_OK, 'message' => "false", 'posts' => $posts
                , 'likes' => $likes

                ,
            ], Response::HTTP_OK);
        }

    }


    public
    function myPosts(Request $request)
    {
        if ($request->api_username != Constants::$API_USERNAME && $request->api_password != Constants::$API_PASSOWRD) {
            return response()->json([
                'code' => Response::HTTP_FORBIDDEN, 'message' => "Wrong api credentials"
            ], Response::HTTP_OK);
        } else {

            $posts = DB::table('posts')
                ->where('user_id', $request->id)->orderBy('id', 'desc')
                ->get();
            foreach ($posts as $post) {
                $user = User::find($post->user_id);
                $post->user = $user;
                $commentCount = DB::table('comments')->where('post_id', $post->id)->get()->count();
                $lastComment = DB::select("select users.name, comments.text from users,
                                            comments where users.id=comments.user_id and post_id=" . $post->id . " limit 1");
                $likesCount = DB::table('likes')->where('post_id', $post->id)->count();
                $post->likesCount = $likesCount;
                $post->commentsCount = $commentCount;

                $post->lastComment = $lastComment;
            }
            $likes = DB::table('likes')->where('user_id', $request->id)
                ->orderBy('id', 'desc')->limit(50)->get()->pluck('post_id');
            return response()->json([
                'code' => Response::HTTP_OK, 'message' => "false", 'posts' => $posts, 'likes' => $likes
                ,
            ], Response::HTTP_OK);
        }

    }

    public
    function getUserPosts(Request $request)
    {
        if ($request->api_username != Constants::$API_USERNAME && $request->api_password != Constants::$API_PASSOWRD) {
            return response()->json([
                'code' => Response::HTTP_FORBIDDEN, 'message' => "Wrong api credentials"
            ], Response::HTTP_OK);
        } else {

            $posts = DB::table('posts')
                ->where('user_id', $request->id)->orderBy('id', 'desc')
                ->get();
            foreach ($posts as $post) {
                $user = User::find($post->user_id);
                $post->user = $user;
                $commentCount = DB::table('comments')->where('post_id', $post->id)->get()->count();
                $lastComment = DB::select("select users.name, comments.text from users,
                                            comments where users.id=comments.user_id and post_id=" . $post->id . " limit 1");

                $likesCount = DB::table('likes')->where('post_id', $post->id)->count();
                $post->likesCount = $likesCount;
                $post->commentsCount = $commentCount;


                $post->lastComment = $lastComment;
            }
            $likes = DB::table('likes')->where('user_id', $request->id)
                ->orderBy('id', 'desc')->limit(50)->get()->pluck('post_id');
            return response()->json([
                'code' => Response::HTTP_OK, 'message' => "false", 'posts' => $posts, 'likes' => $likes
                ,
            ], Response::HTTP_OK);
        }

    }
}
