<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicUser extends Model
{
    use HasFactory;

    const CONFIRMED_EMAIL_STATE = false;

    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'email',
        'password',
        'address',
        'city',
        'confirmed_email',
        'email_token',
        'password_reset_token'
    ];

    protected $attributes = [
        'confirmed_email' => self::CONFIRMED_EMAIL_STATE,
    ];

    protected $dispatchesEvents = [
        'created' => \App\Events\NewUser::class
    ];

    public static function DTO(PublicUser $user): string
    {
        return "USERNAME:{$user->username} ,FIRSTNAME:{$user->first_name},LASTNAME:{$user->last_name} ,EMAIL:{$user->email} ,ADDRESS:{$user->address}, CITY:{$user->city}";
    }
}
