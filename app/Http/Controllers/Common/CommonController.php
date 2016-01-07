<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use ResponseManager;
use Hash;
use App\User;
use App\Models\ProjectUser;
use App\Models\Project;
use Auth;
use Session;
use Request;

class CommonController extends Controller {

    public function __construct() {
        $this->middleware('auth', ['except' => ['dbSetup', 'invitation']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function profile() {
        if (Auth::check()) {
            $user = Auth::User()->toArray();
            $message = 'Success';
            return Response()->json(ResponseManager::getResult($user, 10, $message));
        } else {
            $message = 'Error';
            return Response()->json(ResponseManager::getError('', 10, $message));
        }
    }

    public function updatePassword() {
        $input = Request::all();

        $data['password'] = Hash::make($input['password']);
        $user = User::find(Auth::User()->id);
        if (Hash::check($input['current'], $user->password)) {
            $user = User::where('id', Auth::User()->id)->update($data);
            if ($user) {
                $message = 'Your password has been changed successfully';
                return Response()->json(ResponseManager::getResult($user, 10, $message));
            } else {
                $message = 'Error in changing password';
                return Response()->json(ResponseManager::getError('', 10, $message));
            }
        } else {
            $message = 'Your current password is invalid';
            return Response()->json(ResponseManager::getError('', 10, $message));
        }
    }

    public function updateProfile() {
        $input = Request::all();
        $id = Auth::User()->id;
        $validation = User::validateProfile($input, $id);
        if ($validation->fails()) {
            $message = $validation->messages()->first();
            return Response()->json(ResponseManager::getError('', 10, $message));
        }

        $user = User::where('id', $id)->update($input);
        if ($user) {
            $message = 'User profile successfully updated';
            return Response()->json(ResponseManager::getResult($input, 10, $message));
        } else {
            $message = 'Error in  updating user profile';
            return Response()->json(ResponseManager::getError('', 10, $message));
        }
    }

    public function invitation($code) {
        if (Auth::check()) {
            $string = base64_decode($code);
            $ary = explode('-', $string);
            $email = $ary[0];
            $pId = $ary[1];
            $user = User::where('email', $email)->first();
            if ($user) {
                $user = $user->toArray();
                if (Auth::check()) {
                    if (Auth::user()->email == $user['email']) {
                        ProjectUser::where('project_id', $pId)->where('user_id', $user['id'])->update(['invitation' => 1]);
                        $project = Project::find($pId)->toArray();
                        $message = 'You are now member of project ' . $project['name'];
                        return Response()->json(ResponseManager::getResult($ary, 10, $message));
                    } else {
                        Auth::logout();
                        Session::flush();
                        $message = 'Please login with the email id on which you receive the invitation.';
                        return Response()->json(ResponseManager::getError('', 101, $message));
                    }
                } else {
                    $message = 'Plese login to accept the invitation';
                    return Response()->json(ResponseManager::getError('', 1010, $message));
                }
            } else {
                $message = 'You are not register with us. Please register with us.';
                return Response()->json(ResponseManager::getError('', 2020, $message));
            }
        } else {
            $message = 'Plese login to accept the invitation';
            return Response()->json(ResponseManager::getError('', 10, $message));
        }
    }

}
