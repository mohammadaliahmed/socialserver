<?php

namespace App\Http\Controllers;

use App\Friends;
use App\SendNotification;
use App\User;
use function array_merge;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class FriendsController extends Controller
{
    //
    public function sendFriendRequest(Request $request)
    {

        $data = DB::table('friends')
            ->where('user_one', $request->id)
            ->where('user_two', $request->his_id)
            ->get();


        if ($data != null && $data->count() > 0) {

            return response()->json([
                'code' => Response::HTTP_OK, 'message' => "Already Sent"
                ,
            ], Response::HTTP_OK);
        } else {


            $friend = new Friends();
            $friend->user_one = $request->id;
            $friend->user_two = $request->his_id;
            $friend->type = 'request';
            $friend->save();

            $user1 = User::find($request->id);
            $user2 = User::find($request->his_id);


            $notification = new SendNotification();
            $notification->sendPushNotification($user2->fcmKey,
                'New friend request',
                $user1->name . ' sent a friend request',
                $request->his_id,
                'request'
            );

            return response()->json([
                'code' => Response::HTTP_OK, 'message' => "Request Sent"
                ,
            ], Response::HTTP_OK);
        }


    }

    public function acceptRequest(Request $request)
    {

        $data = DB::table('friends')
            ->where('user_one', $request->his_id)
            ->where('user_two', $request->id)
            ->first();
        if ($data != null) {
            $data = Friends::find($data->id);
            $data->type = 'friend';
            $data->update();

            return response()->json([
                'code' => Response::HTTP_OK, 'message' => "Accepted"

            ], Response::HTTP_OK);
        } else {

            return response()->json([
                'code' => Response::HTTP_OK, 'message' => "Not Found"
                ,
            ], Response::HTTP_OK);
        }


    }

    public function removeAsFriend(Request $request)
    {

        $data = DB::select("DELETE from friends
                          where (user_one=" . $request->id . " and user_two=" . $request->his_id . ")
                          or (user_one=" . $request->his_id . " and user_two=" . $request->id . ")");


        return response()->json([
            'code' => Response::HTTP_OK, 'message' => "Removed"

        ], Response::HTTP_OK);


    }

    public function getAllRequestTypes(Request $request)
    {

        $sql = DB::select("SELECT * FROM `friends` WHERE `user_one` in (" . $request->id . ")");


        return response()->json([
            'code' => Response::HTTP_OK, 'message' => "Request Sent"
            , 'data' => $sql
        ], Response::HTTP_OK);
    }

    public
    function getMyFriends(Request $request)
    {
        $friend1 = DB::select("select * from users where id IN
                              (Select user_two from friends  WHERE `user_one` =" . $request->id . " and `type`='friend')");
        $friends2 = DB::select("select * from users where id IN
                              (Select user_one from friends  WHERE `user_two` =" . $request->id . " and `type`='friend')");

        $friends = array_merge($friend1, $friends2);
        return response()->json([
            'code' => Response::HTTP_OK, 'message' => "false"
            , 'friends' => $friends
        ], Response::HTTP_OK);
    }

    public
    function getHisFriends(Request $request)
    {
        $hisFriends1 = DB::select("select * from users where id IN
                              (Select user_two from friends  WHERE `user_one` =" . $request->his_id . " and `type`='friend')");
        $hisFriends2 = DB::select("select * from users where id IN
                              (Select user_one from friends  WHERE `user_two` =" . $request->his_id . " and `type`='friend')");
        $hisFriends = array_merge($hisFriends1, $hisFriends2);

        return response()->json([
            'code' => Response::HTTP_OK, 'message' => "false"
            , 'hisFriends' => $hisFriends
        ], Response::HTTP_OK);
    }
}
