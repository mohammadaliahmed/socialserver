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

            $room = DB::table('rooms')->where('users', $request->userIds)->first();
            if ($room != null) {
                return response()->json([
                    'code' => Response::HTTP_OK, 'message' => "Room Already Exists",
                    "room" => $room
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
