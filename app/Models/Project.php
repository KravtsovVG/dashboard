<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;

class Project extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'projects';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'name', 'user_id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function users() {
        return $this->hasMany('App\Models\ProjectUser', 'project_id', 'id')->with('user')->where('invitation', 1);
    }

    public function user() {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    public static function validate($data) {


        $rule = array(
            'name' => 'required|unique:projects',
            'user_id' => 'required',
        );

        $messages = array(
            'required' => 'The :attribute field is required.',
        );


        $data = Validator::make($data, $rule, $messages);
        return $data;
    }

    public static function validateUpdate($data, $id) {

        $rule = array(
            'name' => 'required|unique:projects,name,' . $id . ',id',
            'user_id' => 'required',
        );

        $messages = array(
            'required' => 'The :attribute field is required.',
        );

        $data = Validator::make($data, $rule, $messages);
        return $data;
    }

}
