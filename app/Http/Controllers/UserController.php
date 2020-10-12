<?php

namespace App\Http\Controllers;


use App\Constants;
use App\User;
use function contains;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;
use function report;

class UserController extends Controller
{
    //

    public function register(Request $request)
    {

        if ($request->api_username != Constants::$API_USERNAME && $request->api_password != Constants::$API_PASSOWRD) {
            return response()->json([
                'code' => Response::HTTP_FORBIDDEN, 'message' => "Wrong api credentials"
            ], Response::HTTP_OK);
        } else {

            $user = DB::table('users')
                ->where('email', $request->email)
                ->orWhere('username', $request->username)
                ->orWhere('phone', $request->phone)
                ->first();
            if ($user != null) {
                return response()->json([
                    'code' => 302, 'message' => 'Account already exist',
                ], Response::HTTP_OK);
            } else {

                if ($request->name == null) {
                    return response()->json([
                        'code' => 302, 'message' => 'Empty params',
                    ], Response::HTTP_OK);
                } else {


                    $user = new User();
                    $user->name = $request->name;
                    $user->email = $request->email;
                    $user->username = $request->username;
                    $user->dob = $request->dob;
                    $user->gender = $request->gender;
                    $user->password = md5($request->password);
                    $user->fcmKey = $request->fcmKey;
                    $user->phone = $request->phone;
                    $user->save();
//            $this->sendMail($request->email);
                    return response()->json([
                        'code' => Response::HTTP_OK, 'message' => "false", 'user' => $user
                        ,
                    ], Response::HTTP_OK);
                }

            }
        }

    }

    public function updateFcmKey(Request $request)
    {
        if ($request->api_username != Constants::$API_USERNAME && $request->api_password != Constants::$API_PASSOWRD) {
            return response()->json([
                'code' => Response::HTTP_FORBIDDEN, 'message' => "Wrong api credentials"
            ], Response::HTTP_OK);
        } else {

            $userr = User::find($request->id);
            $userr->fcmKey = $request->fcmKey;

            $userr->update();

            $requestsSent = DB::table('friends')
                ->where('user_one', $request->id)
                ->where('type', 'request')
                ->get();
            $requestsReceived = DB::table('friends')
                ->where('user_two', $request->id)
                ->where('type', 'request')
                ->get();

            $friends1 = DB::table('friends')->where('user_one', $request->id)->where('type', 'friend')->get();
            $friends2 = DB::table('friends')->where('user_two', $request->id)->where('type', 'friend')->get();
            $friends = array_merge($friends1->pluck('user_two')->toArray(), $friends2->pluck('user_one')->toArray());
            $userr->friendsCount = count($friends);
            $userr->requestsSent = $requestsSent->pluck('user_two');
            $userr->friends = $friends;
            $userr->requestsReceived = $requestsReceived->pluck('user_one');


            return response()->json([
                'code' => 200, 'message' => "false",
                'user' => $userr

            ], Response::HTTP_OK);

        }
    }

    public function updateProfilePicture(Request $request)
    {
        if ($request->api_username != Constants::$API_USERNAME && $request->api_password != Constants::$API_PASSOWRD) {
            return response()->json([
                'code' => Response::HTTP_FORBIDDEN, 'message' => "Wrong api credentials"
            ], Response::HTTP_OK);
        } else {

            $userr = User::find($request->id);
            $userr->fcmKey = $request->fcmKey;
            $userr->picUrl = $request->picUrl;
            $userr->thumbnailUrl = $request->thumbnailUrl;

            $userr->update();

            return response()->json([
                'code' => 200, 'message' => "false", 'user' => $userr
                ,
            ], Response::HTTP_OK);

        }
    }

    public function login(Request $request)
    {

        if ($request->api_username != Constants::$API_USERNAME && $request->api_password != Constants::$API_PASSOWRD) {
            return response()->json([
                'code' => Response::HTTP_FORBIDDEN, 'message' => "Wrong api credentials"
            ], Response::HTTP_OK);
        } else {

            $user = DB::select('SELECT * FROM `users` 
            WHERE ( `email` like "' . $request->username . '" or username like "' . $request->username . '"  or phone like "' . $request->username . '") and password like "' . md5($request->password) . '"');

            if ($user == null) {
                return response()->json([
                    'code' => 302, 'message' => 'Wrong credentials',
                ], Response::HTTP_OK);
            } else {


                return response()->json([
                    'code' => 200, 'message' => "false", 'user' => $user[0]
                    ,
                ], Response::HTTP_OK);
            }

        }


    }

    public function searchUsers(Request $request)
    {
        if ($request->api_username != Constants::$API_USERNAME && $request->api_password != Constants::$API_PASSOWRD) {
            return response()->json([
                'code' => Response::HTTP_FORBIDDEN, 'message' => "Wrong api credentials"
            ], Response::HTTP_FORBIDDEN);
        } else {
            $users = DB::select("SELECT * FROM `users` WHERE (name 
                                  like '%" . $request->search . "%'
                                   or email like '%" . $request->search . "%' 
                                  or username like '%" . $request->search . "%') and id !=" . $request->id ." order by name asc");
            return response()->json([
                'code' => Response::HTTP_OK, 'message' => "false"
                , 'users' => $users
            ], Response::HTTP_OK);
        }
    }

    public function userProfile(Request $request)
    {
        if ($request->api_username != Constants::$API_USERNAME && $request->api_password != Constants::$API_PASSOWRD) {
            return response()->json([
                'code' => Response::HTTP_FORBIDDEN, 'message' => "Wrong api credentials"
            ], Response::HTTP_OK);
        } else {

            $use = User::find($request->id);

            $friendCount = DB::select("Select id from friends where
                                            (user_one=" . $request->id . " or user_two =" . $request->id . ") 
                                            and type='friend' ");
            $friendCount = count($friendCount);
            return response()->json([
                'code' => Response::HTTP_OK, 'message' => "false"
                , 'friendCount' => $friendCount
                , 'user' => $use
            ], Response::HTTP_OK);
        }

    }
}
