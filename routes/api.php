<?php


use Illuminate\Http\Request;
//use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'user'], function () {

    Route::post('register', 'UserController@register');
    Route::post('login', 'UserController@login');
    Route::post('updateProfilePicture', 'UserController@updateProfilePicture');
    Route::post('updateFcmKey', 'UserController@updateFcmKey');
    Route::post('searchUsers', 'UserController@searchUsers');
    Route::post('userProfile', 'UserController@userProfile');
});
Route::group(['prefix' => 'post'], function () {

    Route::post('addPost', 'PostController@addPost');
    Route::post('getUsersByPostLikes', 'PostController@getUsersByPostLikes');
    Route::post('likeUnlikePost', 'PostController@likeUnlikePost');
    Route::post('allPosts', 'PostController@allPosts');
    Route::post('ViewPost', 'PostController@ViewPost');
    Route::post('deletePost', 'PostController@deletePost');
    Route::post('updatePost', 'PostController@updatePost');
    Route::post('getUserPosts', 'PostController@getUserPosts');
    Route::post('myPosts', 'PostController@myPosts');
    Route::post('getPost', 'PostController@getPost');
});
Route::group(['prefix' => 'story'], function () {

    Route::post('addStory', 'StoriesController@addStory');
    Route::post('allStories', 'StoriesController@allStories');
    Route::post('deleteStory', 'StoriesController@deleteStory');
});
Route::group(['prefix' => 'comments'], function () {

    Route::post('getAllComments', 'CommentsController@getAllComments');
    Route::post('addComment', 'CommentsController@addComment');

});
Route::group(['prefix' => 'views'], function () {

    Route::post('addStoryView', 'ViewsController@addStoryView');

});
Route::group(['prefix' => 'friends'], function () {

    Route::post('getAllRequestTypes', 'FriendsController@getAllRequestTypes');
    Route::post('getMyFriends', 'FriendsController@getMyFriends');
    Route::post('getHisFriends', 'FriendsController@getHisFriends');
    Route::post('sendFriendRequest', 'FriendsController@sendFriendRequest');
    Route::post('acceptRequest', 'FriendsController@acceptRequest');
    Route::post('removeAsFriend', 'FriendsController@removeAsFriend');

});

Route::group(['prefix' => 'room'], function () {

    Route::post('createRoom', 'RoomsController@createRoom');
});
Route::group(['prefix' => 'message'], function () {

    Route::post('createMessage', 'MessageController@createMessage');
    Route::post('allRoomMessages', 'MessageController@allRoomMessages');
    Route::post('userMessages', 'MessageController@userMessages');
    Route::post('sendStoryMessage', 'MessageController@sendStoryMessage');
});


Route::post('uploadFile', 'FileUploadController@uploadFile');
Route::get('asterisk/{id}', 'FileUploadController@asterisk');



