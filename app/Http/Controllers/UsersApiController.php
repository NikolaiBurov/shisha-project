<?php

namespace App\Http\Controllers;

use App\Models\Flavour;
use Illuminate\Http\Request;
use  Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Services\ImageService;
use App\Http\Constants\StatusCodes;
use App\Models\PublicUser;

class UsersApiController
{
    private $user;
    private $status_codes = [];
    public function __construct(PublicUser $user)
    {
        $this->user = $user;
        $this->status_codes = new StatusCodes();
    }

    public function getAllUsers(Request $request)
    {
        return $this->user::all();
    }
}
