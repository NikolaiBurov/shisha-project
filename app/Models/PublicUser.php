<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'email',
        'password',
        'address',
        'city'
    ];

    protected $dispatchesEvents = [
        'created' =>  \App\Events\NewUser::class
    ];

    public static  function UserDTO(PublicUser $user) : string {
        return "USERNAME:{$user->username} ,FIRSTNAME:{$user->first_name},LASTNAME:{$user->last_name} ,EMAIL:{$user->email} ,ADDRESS:{$user->address}, CITY:{$user->city}";
    }
}
