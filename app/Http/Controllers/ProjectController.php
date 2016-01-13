<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use ResponseManager;
use Request;
use Response;
use App\Models\Project;
use App\Models\ProjectUser;
use Auth;
use Mail;

class ProjectController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {

        //get Login user project ids
        $pId = Project::where('user_id', Auth::User()->id)->lists('id')->toArray();
        $proId = ProjectUser::where('user_id', Auth::User()->id)->lists('project_id')->toArray();
        $projIds = array_unique(array_merge($pId, $proId));
        $projects = Project::with(['user', 'users'])->whereIn('id', $projIds)->get()->toArray();
        if (count($projects) > 0) {
            foreach ($projects as &$pro) {
                array_push($pro['users'], $pro['user']);
            }
            $message = 'Success';
            return Response()->json(ResponseManager::getResult($projects, 10, $message));
        } else {
            $message = 'Error';
            return Response()->json(ResponseManager::getError('', 10, $message));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store() {
        $input = Request::all();
        $input['user_id'] = Auth::User()->id;
        $validation = Project::validate($input);
        if ($validation->fails()) {
            $message = $validation->messages()->first();
            return Response()->json(ResponseManager::getError('', 10, $message));
        }
        $project = Project::create($input);
        if ($project) {
//            if (array_key_exists('users', $input)) {
//                foreach ($input['users'] as $user) {
//                    $data = ['project_id' => $project['id'], 'user_id' => $user['id'], 'email' => $user['email']];
//                    ProjectUser::create($data);
//                    $email['email'] = $user['email'];
//                    $email['user'] = $user['name'];
//                    $email['msg'] = $input['message'];
//                    $email['pname'] = $project['name'];
//                    $email['code'] = base64_encode($email['email'] . '-' . $project['id']);
//                    Mail::send('emails.invite', $email, function( $message ) use ($email) {
//                        $message->to($email['email'])->subject(Auth::User()->name . ' want to add you to ' . $email['pname']);
//                    });
//                }
//            }
            $message = 'Added Successfully.';
            return Response()->json(ResponseManager::getResult($project, 10, $message));
        } else {
            $message = 'Something went wrong. Please try again.';
            return Response()->json(ResponseManager::getError('', 10, $message));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        $project = Project::with(['user', 'users'])->find($id);
        if ($project) {
            $project = $project->toArray();
            $user = Auth::User()->id;
            $users = [];
            $project['user']['is_owner'] = 1;
            if ($user == $project['user_id']) {
                $project['user']['owner'] = 1;
            }

            array_push($users, $project['user']);
            if (count($project['users']) > 0) {
                foreach ($project['users'] as &$us) {
                    $us['user']['pid'] = $us['id'];
                    if ($us['is_owner']) {
                        $us['user']['is_owner'] = 1;
                    }
                    array_push($users, $us['user']);
                }
            }
            unset($project['users']);
            unset($project['user']);
            $project['users'] = $users;
            $message = 'Success.';
            return Response()->json(ResponseManager::getResult($project, 10, $message));
        } else {
            $message = 'Something went wrong. Please try again.';
            return Response()->json(ResponseManager::getError('', 10, $message));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        //
        $project = Project::with(['user', 'users'])->find($id);
        if ($project) {
            $project = $project->toArray();
            $user = Auth::User()->id;
            $users = [];
            $project['user']['is_owner'] = 1;
            if ($user == $project['user_id']) {
                $project['owner'] = true;
                $project['user']['owner'] = 1;
            }

            array_push($users, $project['user']);
            if (count($project['users']) > 0) {
                foreach ($project['users'] as &$us) {
                    $us['user']['pid'] = $us['id'];
                    if ($us['is_owner']) {
                        $us['user']['is_owner'] = 1;
                        if ($user == $us['user_id']) {
                            $project['owner'] = true;
                            $us['user']['owner'] = 1;
                        }
                    }
                    array_push($users, $us['user']);
                }
            }
            unset($project['users']);
            unset($project['user']);
            $project['users'] = $users;
            $message = 'Success.';
            return Response()->json(ResponseManager::getResult($project, 10, $message));
        } else {
            $message = 'Something went wrong. Please try again.';
            return Response()->json(ResponseManager::getError('', 10, $message));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id) {
        $input = Request::all();
        $input['user_id'] = Auth::User()->id;
        $validation = Project::validateUpdate($input, $id);
        if ($validation->fails()) {
            $message = $validation->messages()->first();
            return Response()->json(ResponseManager::getError('', 10, $message));
        }
        if (array_key_exists('users', $input)) {
            foreach ($input['users'] as $user) {
                $data = ['project_id' => $id, 'user_id' => $user['id']];
                $chkExist = ProjectUser::where('project_id', $id)->where('user_id', $user['id'])->count();
                print_r($chkExist);
                exit;
                if ($chkExist == 0) {
                    ProjectUser::create($data);
                    $email['email'] = $user['email'];
                    $email['user'] = $user['name'];
                    $email['msg'] = $input['message'];
                    $email['pname'] = $input['name'];
                    $email['code'] = base64_encode($email['email'] . '-' . $id);
                    Mail::send('emails.invite', $email, function( $message ) use ($email) {
                        $message->to($email['email'])->subject(Auth::User()->name . ' want to add you to ' . $email['pname']);
                    });
                }
            }
        }
        unset($input['users']);
        unset($input['message']);
        $project = Project::where('id', $id)->update($input);
        if ($project) {
            $message = 'update Successfully.';
            return Response()->json(ResponseManager::getResult($project, 10, $message));
        } else {
            $message = 'Something went wrong. Please try again.';
            return Response()->json(ResponseManager::getError('', 10, $message));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        
    }

    public function deleteProUser() {
        $input = Request::all();
        //chk login user is whether project owner or not
        $project = Project::with(['user', 'users'])->find($input['project_id']);
        if ($project) {
            $user = Auth::User()->id;
            $project = $project->toArray();
            $owner = false;
            if ($user == $project['user_id']) {
                $owner = true;
            }
            if (count($project['users']) > 0) {
                foreach ($project['users'] as &$us) {
                    if ($us['is_owner'] && $user == $us['user_id']) {
                        $owner = true;
                    }
                }
            }
            if ($owner) {
                $delete = ProjectUser::where('id', $input['id'])->delete();
                if ($delete) {
                    $message = 'Remove Successfully.';
                    return Response()->json(ResponseManager::getResult($project, 10, $message));
                } else {
                    $message = 'Something went wrong. Please try again.';
                    return Response()->json(ResponseManager::getError('', 10, $message));
                }
            } else {
                $message = 'You are not authorize to do it.';
                return Response()->json(ResponseManager::getError('', 10, $message));
            }
        } else {
            $message = 'You are not authorize to do it.';
            return Response()->json(ResponseManager::getError('', 10, $message));
        }
    }

    public function makeProOwner() {
        $input = Request::all(); // project_id,id
        //chk login user is whether project owner or not
        $project = Project::with(['user', 'users'])->find($input['project_id']);
        if ($project) {
            $user = Auth::User()->id;
            $project = $project->toArray();
            $owner = false;
            if ($user == $project['user_id']) {
                $owner = true;
            }
            if (count($project['users']) > 0) {
                foreach ($project['users'] as &$us) {
                    if ($us['is_owner'] && $user == $us['user_id']) {
                        $owner = true;
                    }
                }
            }
            if ($owner) {
                $update = ProjectUser::where('id', $input['id'])->update(['is_owner' => 1]);
                if ($update) {
                    $message = 'Success.';
                    return Response()->json(ResponseManager::getResult($project, 10, $message));
                } else {
                    $message = 'Something went wrong. Please try again.';
                    return Response()->json(ResponseManager::getError('', 10, $message));
                }
            } else {
                $message = 'You are not authorize to do it.';
                return Response()->json(ResponseManager::getError('', 10, $message));
            }
        } else {
            $message = 'You are not authorize to do it.';
            return Response()->json(ResponseManager::getError('', 10, $message));
        }
    }

}
