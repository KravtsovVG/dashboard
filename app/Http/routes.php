<?php

/*
  |--------------------------------------------------------------------------
  | Routes File
  |--------------------------------------------------------------------------
  |
  | Here is where you will register all of the routes in an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the controller to call when that URI is requested.
  |
 */

//Route::get('/', function () {
//    return view('admin');
//});


/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | This route group applies the "web" middleware group to every route
  | it contains. The "web" middleware group is defined in your HTTP
  | kernel and includes session state, CSRF protection, and more.
  |
 */

Route::group(['middleware' => ['web']], function () {

    Route::get('/', 'Common\LoginController@index');
//Authentication Module
    Route::post('/auth/login', 'Common\LoginController@doLogin');
    Route::post('/auth/signup', 'Common\LoginController@doSignup');
    Route::post('/auth/google', 'Common\LoginController@google');
    Route::post('/auth/google-signup', 'Common\LoginController@googleReg');
    Route::get('/acceptInvitation/{code}', 'Common\CommonController@invitation');
    Route::get('/loggedinuser', 'Common\LoginController@logginuser');
    Route::get('/logout', 'Common\LoginController@logout');

    Route::post('/update-password', 'Common\CommonController@updatePassword');
    Route::post('/update-profile', 'Common\CommonController@updateProfile');

    //User Ctrl
    Route::resource('/user', 'UserController');

    //Project Master
    Route::resource('/project', 'ProjectController');
    Route::post('/delete-project-user', 'ProjectController@deleteProUser');
    Route::post('/make-project-owner', 'ProjectController@makeProOwner');


    
});
