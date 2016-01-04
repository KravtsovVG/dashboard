<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Validator;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

    use Authenticatable,
        CanResetPassword;

    protected $table = 'users';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'password', 'email', 'google'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'created_at', 'updated_at', 'remember_token'];

    public static function validate($data) {


        $rule = array(
            'name' => 'required',
            'password' => 'required',
            'email' => 'required|unique:users',
        );

        $messages = array(
            'required' => 'The :attribute field is required.',
            'unique' => 'The :attribute already Exist.',
        );


        $data = Validator::make($data, $rule, $messages);
        return $data;
    }

    public static function validateProfile($data, $id) {

        $rule = array(
            'name' => 'sometimes|required',
            'email' => 'sometimes|required|unique:users,email,' . $id . ',id',
        );

        $messages = array(
            'required' => 'The :attribute field is required.',
            'unique' => 'The :attribute already Exist.',
        );

        $data = Validator::make($data, $rule, $messages);
        return $data;
    }

}
