<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTokenMail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('public_users', function ($table) {
            $table->string('email_token');
            $table->boolean('confirmed_email');
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
            $table->dropColumn('email_token');
            $table->boolean('confirmed_email');
        });
    }
}
