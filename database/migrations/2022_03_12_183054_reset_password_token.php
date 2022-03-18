<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ResetPasswordToken extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
    {
         Schema::table('public_users', function ($table) {
            $table->string('password_reset_token');
        });;
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
    {
         Schema::table('public_users', function ($table) {
            $table->dropColumn('password_reset_token');
        });
    }
}
