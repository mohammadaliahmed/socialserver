<?php

namespace App\Http\Controllers;

use App\Constants;
use App\Posts;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class NotificationsController extends Controller
{
    //

    public function getMyNotifications(Request $request)
    {

        if ($request->api_username != Constants::$API_USERNAME && $request->api_password != Constants::$API_PASSOWRD) {
            return response()->json([
                'code' => Response::HTTP_FORBIDDEN, 'message' => "Wrong api credentials"
            ], Response::HTTP_FORBIDDEN);
        } else {
            $notifications = DB::table('notifications')->where('my_id', $request->id)->get();
            foreach ($notifications as $notification) {
                if ($notification->type == 'request') {
                    $user = User::find($notification->his_id);
                    $notification->picture = $user->thumbnailUrl;
                }
                else if ($notification->type == 'post') {
                    $post = Posts::find($notification->post_id);
                    $notification->picture = $post->images_url;
                }
            }
            return response()->json([
                'code' => Response::HTTP_OK, 'message' => "false"
                , 'notifications' => $notifications
            ], Response::HTTP_OK);
        }
    }
}
