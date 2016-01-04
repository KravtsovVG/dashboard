<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProjectUsers extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('project_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('project_id');
            $table->integer('user_id');
            $table->tinyInteger('invitation')->default(0);
            $table->tinyInteger('is_owner')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('project_user');
    }

}
