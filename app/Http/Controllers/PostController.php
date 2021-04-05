<?php

namespace App\Http\Controllers;

use App\Constants;
use App\Likes;
use App\Notifications;
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

            $milliseconds = round(microtime(true) * 1000);
            $post = new Posts();
            $post->user_id = $request->id;
            $post->images_url = $request->images_url;
            $post->video_url = $request->video_url;
            $post->video_image_url = $request->video_image_url;
            $post->post_type = $request->post_type;
            $post->deleted = $request->deleted;
            $post->random_id = $request->random_id;
            $post->time = $milliseconds;

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
                $post = Posts::find($request->postId);

                if ($request->userId != $post->user_id) {


                    $user = User::find($request->userId);
                    $notifications = new Notifications();
                    $notifications->title = $user->name . " liked your post.";
                    $notifications->message = "Click to view";
                    $notifications->my_id = $post->user_id;
                    $notifications->his_id = $request->userId;
                    $notifications->post_id = $request->postId;
                    $notifications->type = "post";
                    $notifications->time = round(microtime(true) * 1000);
                    $notifications->save();

                }
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

            $str = "";
            $publicUser = User::where('type', 1)->get()->pluck('id');
            foreach ($publicUser as $us) {
                if ($str == "") {
                    $str = $us;
                } else {
                    $str = $str . "," . $us;
                }
            }
            $str = $str . "," . $request->friends;


//            return $request->friends;
            $posts = DB::select("Select * from posts where user_id in (" . $str . ") order by id desc limit 100");

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

    public
    function explorePosts(Request $request)
    {
        if ($request->api_username != Constants::$API_USERNAME && $request->api_password != Constants::$API_PASSOWRD) {
            return response()->json([
                'code' => Response::HTTP_FORBIDDEN, 'message' => "Wrong api credentials"
            ], Response::HTTP_OK);
        } else {

            $posts = DB::select("SELECT posts.id,posts.user_id,posts.post_type,posts.images_url,users.username
FROM posts
LEFT JOIN users
ON posts.user_id = users.id
WHERE    (posts.id, posts.user_id) IN (
           SELECT   MAX(posts.id), posts.user_id
           FROM     posts
    		where user_id in (select id from users where users.type=1)
           GROUP BY user_id)");




            return response()->json([
                'code' => Response::HTTP_OK, 'message' => "false", 'posts' => $posts
                ,
            ], Response::HTTP_OK);
        }

    }

    public function addPostmanPost(Request $request)
    {

        $data = json_decode($request->getContent(), true);
        $milliseconds = round(microtime(true) * 1000);
        for ($i = 0; $i < sizeof($data); $i++) {

            $post = new Posts();
            $post->images_url = $data[$i]['images_url'];
            $post->user_id = $data[$i]['user_id'];
            $post->post_type = $data[$i]['post_type'];
            $post->time = $milliseconds;
            $post->random_id = $milliseconds;
            $post->save();

        }


    }
}
