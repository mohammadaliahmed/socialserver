<?php

namespace App\Http\Controllers;

use App\Comments;
use App\Constants;
use App\Posts;
use App\SendNotification;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class CommentsController extends Controller
{
    //
    function addComment(Request $request)
    {

        if ($request->api_username != Constants::$API_USERNAME && $request->api_password != Constants::$API_PASSOWRD) {
            return response()->json([
                'code' => Response::HTTP_FORBIDDEN, 'message' => "Wrong api credentials"
            ], Response::HTTP_OK);
        } else {

            $comments = new Comments();
            $comments->text = $request->text;
            $comments->user_id = $request->id;
            $comments->post_id = $request->post_id;
            $comments->time = $request->time;
            $comments->save();
            $user = User::find($comments->user_id);
            $comments->user = $user;

            return response()->json([
                'code' => Response::HTTP_OK, 'message' => "false", 'comment' => $comments
                ,
            ], Response::HTTP_OK);
        }
    }

    function getAllComments(Request $request)
    {

        if ($request->api_username != Constants::$API_USERNAME && $request->api_password != Constants::$API_PASSOWRD) {
            return response()->json([
                'code' => Response::HTTP_FORBIDDEN, 'message' => "Wrong api credentials"
            ], Response::HTTP_OK);
        } else {

            $comments = DB::table('comments')
                ->where('post_id', $request->post_id)
                ->get();
            foreach ($comments as $comment) {
                $user = User::find($comment->user_id);
                $comment->user = $user;
            }

            $post=Posts::find($request->post_id);
            $postByUser = User::find($post->user_id);

            return response()->json([
                'code' => Response::HTTP_OK, 'message' => "false",
                'user' => $postByUser,
                'comments' => $comments

                ,
            ], Response::HTTP_OK);
        }
    }

}
