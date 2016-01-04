<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use Auth;
use ResponseManager;
use Session;
use App\User;
use Request;
use Hash;
use Config;
use JWT;
use GuzzleHttp;

class LoginController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        if (Auth::check()) {
            return view('admin');
        } else {
            return view('login');
        }
    }

    public function logout() {
        Auth::logout();
        Session::flush();
        $message = 'Logout successful';
        return Response()->json(ResponseManager::getResult('', 10, $message));
    }

    public function doLogin() {
        $input = Request::all();

        Auth::attempt(array(
            'email' => $input['email'],
            'password' => $input['password'])
                , true);

        if (Auth::check()) {
            $message = 'Success';
            return Response()->json(ResponseManager::getResult($input, 10, $message));
        } else {
            $message = 'Username or password is incorrect';
            return Response()->json(ResponseManager::getError('', 10, $message));
        }
    }

    public function logginuser() {
        if (Auth::check()) {
            $user = User::find(Auth::User()->id);
            $message = 'Success';
            return Response()->json(ResponseManager::getResult($user, 10, $message));
        } else {
            $message = 'Please login';
            return Response()->json(ResponseManager::getError('', 10, $message));
        }
    }

    public function doSignup() {
        $input = Request::all();
        $validation = User::validate($input);
        if ($validation->fails()) {
            $message = $validation->messages()->first();
            return Response()->json(ResponseManager::getError('', 10, $message));
        }
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
        if ($user) {
            Auth::loginUsingId($user['id']);
            $message = 'Registration Successfully.';
            return Response()->json(ResponseManager::getResult($user, 10, $message));
        } else {
            $message = 'Something went wrong. Please try again.';
            return Response()->json(ResponseManager::getError('', 10, $message));
        }
    }

    protected function createToken($user) {
        $payload = [
            'sub' => $user->id,
            'iat' => time(),
            'exp' => time() + (2 * 7 * 24 * 60 * 60)
        ];
        return JWT::encode($payload, Config::get('app.token_secret'));
    }

    public function google(Request $request) {
        $input = Request::all();
        $params = [
            'code' => $input['code'],
            'client_id' => $input['clientId'],
            'client_secret' => Config::get('app.google_secret'),
            'redirect_uri' => $input['redirectUri'],
            'grant_type' => 'authorization_code',
        ];
        $fields_string = '';
        foreach ($params as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }
        rtrim($fields_string, '&');
        $client = new GuzzleHttp\Client();
        // Step 1. Exchange authorization code for access token.
//        $accessTokenResponse = $client->request('POST', 'https://accounts.google.com/o/oauth2/token', [
//            'form_params' => $params
//        ]);
        $url = 'https://accounts.google.com/o/oauth2/token';
        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false); //disable SSL check
        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $fields_string);
        $json_response = curl_exec($curl_handle);
        curl_close($curl_handle);
        $response = json_decode($json_response);
        $response = (array) $response;
        // Step 2. Retrieve profile information about the current user.
        $profileResponse = $client->get('https://www.googleapis.com/plus/v1/people/me/openIdConnect', ['headers' => array('Authorization' => 'Bearer ' . $response['access_token'])]);
        $profile = json_decode($profileResponse->getBody(), true);

        //chk user already exist with sub id

        $user = User::where('google', $profile['sub'])->first();
        if ($user) {
            $a = $user->toArray();
            Auth::loginUsingId($a['id']);
            return response()->json(['token' => $this->createToken($user)]);
        }

        $user = User::where('email', $profile['email'])->first();
        if ($user) {
            $update = User::where('email', $profile['email'])->update(['google' => $profile['sub']]);
            $a = $user->toArray();
            Auth::loginUsingId($a['id']);
            return response()->json(['token' => $this->createToken($user)]);
        }
        $user = new User;
        $user->google = $profile['sub'];
        $user->name = $profile['name'];
        return response()->json(['token' => $this->createToken($user), 'data' => $profile]);
    }
    
    public function googleReg(){
        
    }

}
