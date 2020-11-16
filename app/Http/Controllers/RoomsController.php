<?php

namespace App\Http\Controllers;

use App\Constants;
use App\Rooms;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class RoomsController extends Controller
{
    //
    public function createRoom(Request $request)
    {
        if ($request->api_username != Constants::$API_USERNAME && $request->api_password != Constants::$API_PASSOWRD) {
            return response()->json([
                'code' => Response::HTTP_FORBIDDEN, 'message' => "Wrong api credentials"
            ], Response::HTTP_FORBIDDEN);
        } else {

            $userIds = $request->userIds;
            $myArray = explode(',', $userIds);


            $room1 = DB::table('rooms')->where('users', $userIds)->first();
            if ($room1 != null) {

                return response()->json([
                    'code' => Response::HTTP_OK, 'message' => "Room Already Exists",
                    "room" => $room1
                ], Response::HTTP_OK);


            } else {
                $newUserIds = $myArray[1] . ',' . $myArray[0];
                $room2 = DB::table('rooms')->where('users', $newUserIds)->first();
                if ($room2 != null) {
                    return response()->json([
                        'code' => Response::HTTP_OK, 'message' => "Room Already Exists",
                        "room" => $room2
                    ], Response::HTTP_OK);
                } else {
                    $room = new Rooms();
                    $room->users = $request->userIds;
                    $room->save();
                    return response()->json([
                        'code' => Response::HTTP_OK, 'message' => "Room Created",
                        "room" => $room
                    ], Response::HTTP_OK);
                }


            }

        }
    }

}
