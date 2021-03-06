<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class FileUploadController extends Controller
{
    //
    public function uploadFile(Request $request)
    {
        $milliseconds = round(microtime(true) * 1000);
        if ($request->has('photo')) {
            $file_name = $milliseconds . '.jpg';
            $path = public_path($request->user);

            if (!File::isDirectory('images/'.$path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            $path = $request->file('photo')->move(public_path("/images/".$request->username), $file_name);
            echo $file_name;
//            return response()->json([
//                'code' => Response::HTTP_OK, 'message' => "false", 'url' => $file_name
//                ,
//            ], Response::HTTP_OK);
        } else if ($request->has('video')) {
            $file_name = $milliseconds . '.mp4';
            $path = $request->file('video')->move(public_path("/videos"), $file_name);
            $photo_url = url('/videos/' . $file_name);
            echo $file_name;
//            return response()->json([
//                'code' => Response::HTTP_OK, 'message' => "false", 'url' => $file_name
//                ,
//            ], Response::HTTP_OK);
        } else {
//            return response()->json([
//                'code' => 401, 'message' => "false", 'url' => "sdfsdfsd"
//                ,
//            ], 401);

        }

    }

    public function asterisk(Request $request, $id)
    {

        return 5;
    }
}
