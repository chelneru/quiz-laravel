<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddedNeededFieldsForUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('u_password');
            $table->string('u_email');
            $table->timestamp('u_email_verified_at');
            $table->string('u_remember_token');
            $table->tinyInteger('u_role');
            $table->string('u_first_name');
            $table->string('u_last_name');
            $table->tinyInteger('u_active');
            $table->timestamp('u_last_login');
            $table->string('u_position');
            $table->string('u_department');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
