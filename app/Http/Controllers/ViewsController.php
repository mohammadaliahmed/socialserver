<?php

namespace App\Http\Controllers;

use App\Constants;
use App\Views;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ViewsController extends Controller
{
    //
    public function addStoryView(Request $request)
    {
        if ($request->api_username != Constants::$API_USERNAME && $request->api_password != Constants::$API_PASSOWRD) {
            return response()->json([
                'code' => Response::HTTP_FORBIDDEN, 'message' => "Wrong api credentials"
            ], Response::HTTP_OK);
        } else {

            $storyViews = DB::table('views')
                ->where('user_id', $request->user_id)
                ->where('story_id', $request->story_id)
                ->first();
            if ($storyViews == null) {
                $view = new Views();
                $view->user_id = $request->user_id;
                $view->story_id = $request->story_id;
                $view->save();
            } else {

            }
            return response()->json([
                'code' => Response::HTTP_OK, 'message' => "View Added"
                ,
            ], Response::HTTP_OK);
        }
    }


}
