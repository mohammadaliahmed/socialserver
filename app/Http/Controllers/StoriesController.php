<?php

namespace App\Http\Controllers;

use App\Constants;
use App\Stories;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use function sizeof;

class StoriesController extends Controller
{
    //
    public function addStory(Request $request)
    {
        if ($request->api_username != Constants::$API_USERNAME && $request->api_password != Constants::$API_PASSOWRD) {
            return response()->json([
                'code' => Response::HTTP_FORBIDDEN, 'message' => "Wrong api credentials"
            ], Response::HTTP_OK);
        } else {

//            $abc = $request->json()->all();

            $dataArray = $request->urls;
            $str_arr = preg_split("/\,/", $dataArray);  //            $data = json_decode($dataArray, true);

            for ($i = 0; $i < sizeof($str_arr); $i++) {
                $milliseconds = round(microtime(true) * 1000);

                $story = new Stories();
                $story->user_id = $request->id;
                $story->url = $str_arr[$i];
                $story->time = $milliseconds;
                $story->save();
            }


//                return sizeof($dataArray);

            return response()->json([
                'code' => Response::HTTP_OK, 'message' => "Story Added"
                ,
            ], Response::HTTP_OK);

        }


    }

    public function allStories(Request $request)
    {
        if ($request->api_username != Constants::$API_USERNAME && $request->api_password != Constants::$API_PASSOWRD) {
            return response()->json([
                'code' => Response::HTTP_FORBIDDEN, 'message' => "Wrong api credentials"
            ], Response::HTTP_OK);
        } else {

//            $stories = DB::table('stories')
////                ->where('user_id', $request->id)->orderBy('id', 'desc')
//                ->orderBy('id', 'desc')
//                ->get();
            $milliseconds = round(microtime(true) * 1000);
            $stories = DB::select("Select * from stories where user_id in (" . $request->friends . ") 
                                        and  time > (".$milliseconds." - 84600000)
                                        order by id asc");


            foreach ($stories as $story) {
                $user = User::find($story->user_id);
                $story->user = $user;
            }
            $storyViews = DB::select("select * from views where story_id in
            (SELECT id FROM `stories` WHERE `user_id` =" . $request->id . " and time > (".$milliseconds." - 84600000))");
            foreach ($storyViews as $storyView) {
                $user = User::find($storyView->user_id);
                $storyView->user = $user;
            }

            return response()->json([
                'code' => Response::HTTP_OK, 'message' => "false", 'stories' => $stories,
                'storyViews' => $storyViews
                ,
            ], Response::HTTP_OK);
        }

    }

    public function deleteStory(Request $request)
    {
        if ($request->api_username != Constants::$API_USERNAME && $request->api_password != Constants::$API_PASSOWRD) {
            return response()->json([
                'code' => Response::HTTP_FORBIDDEN, 'message' => "Wrong api credentials"
            ], Response::HTTP_OK);
        } else {

            $story = Stories::find($request->id);
            $story->delete();
            return response()->json([
                'code' => Response::HTTP_OK, 'message' => "Deleted"
                ,
            ], Response::HTTP_OK);
        }

    }


}
