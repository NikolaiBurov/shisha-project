<?php

namespace App\Http\Controllers;

use App\Models\Flavour;
use Illuminate\Http\Request;
use  Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Services\ImageService;
use App\Http\Constants\StatusCodes;
use App\Models\PublicUser;
use Validator;
use Illuminate\Support\Facades\Hash;

class UsersApiController
{
    private $users;
    private $status_codes = [];
    private $fields = [];

    public function __construct(PublicUser $users, StatusCodes $status_codes)
    {
        $this->users = $users;
        $this->status_codes = $status_codes;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     */
    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllUsers(Request $request): JsonResponse
    {
        if (!$this->users::all()->isEmpty()) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[0],
                'users' => $this->users::all()
            ];
            return new JsonResponse($response);
        }
        $response = [
            'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[1],
            'data' => $this->status_codes->postRequests()->{"200"}{'empty_users'}
        ];
        return new JsonResponse($response);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserById(Request $request): JsonResponse
    {
        /** @var  $request */
        $user_id = $request->get('user_id');
        if (empty($user_id)) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[3],
                'data' => $this->status_codes->postRequests()->{"406"}{'incorrect_Data'}
            ];
            return new JsonResponse($response);
        }
        $user = $this->users::where('id', $user_id)
            ->orderBy('firstname', 'desc')
            ->first();

        if (is_null($user)) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[1],
                'data' => $this->status_codes->postRequests()->{"200"}{'empty_users'}
            ];
            return new JsonResponse($response);
        }

        $response = [
            'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[1],
            'user' => $user
        ];

        return new JsonResponse($response);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loginUser(Request $request): JsonResponse
    {
        $username = $request->get('username');
        $password = $request->get('password');

        if (empty($username) || empty($password)) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[3],
                'data' => $this->status_codes->postRequests()->{"406"}{'incorrect_Data'}
            ];
            return new JsonResponse($response);
        }
        $loaded_user = $this->users::where('username', $username)
            ->first();
        if (empty($loaded_user)) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[0],
                'data' => $this->status_codes->postRequests()->{"200"}{'non_existent_user'}
            ];
            return new JsonResponse($response);
        }
        if ($loaded_user->password === $password) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[0],
                'user' => $loaded_user
            ];
            return new JsonResponse($response);
        } else {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[1],
                'data' => $this->status_codes->postRequests()->{"200"}{'incorrect_password'}
            ];
            return new JsonResponse($response);
        }

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function registerUser(Request $request): JsonResponse
    {
        $this->setFields(['username', 'first_name', 'last_name', 'password', 'email', 'city', 'address', 'created_at']);

        $user_data = $request->get('user_data');

        if (empty($user_data)) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[3],
                'data' => $this->status_codes->postRequests()->{"406"}{'incorrect_Data'}
            ];
            return new JsonResponse($response);
        }


        $fields = $this->getFields();
        $new_fields = [];

        array_walk($fields, function ($a) use (&$new_fields) {
            $new_fields[$a] = 'present';
        });

        $validate_fields = array_merge($new_fields, ['email' => 'unique:public_users|required']);


        $validator = Validator::make($user_data, $validate_fields);

        if ($validator->errors()->any()) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[0],
                'data' => $validator->errors()
            ];

            return new JsonResponse($response);
        }
        try {
            $user = PublicUser::create($user_data);

            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[0],
                'data' => $this->status_codes->postRequests(['id' => $user->id])->{"200"}{'user_created'}
            ];

        } catch (\Exception $e) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[2],
                'data' => $e->getMessage()
            ];
            return new JsonResponse($response);
        }


        return new JsonResponse($response);
    }

}
