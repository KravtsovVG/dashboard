<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;

class ProjectUser extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'project_user';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'project_id', 'user_id', 'invitation', 'is_owner'];
    protected $hidden = ['created_at', 'updated_at'];

    public function user() {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function project() {
        return $this->hasOne('App\Models\Project', 'id', 'project_id');
    }

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    public static function validate($data) {


        $rule = array(
            'project_id' => 'required',
            'user_id' => 'required',
        );

        $messages = array(
            'required' => 'The :attribute field is required.',
        );


        $data = Validator::make($data, $rule, $messages);
        return $data;
    }

}
