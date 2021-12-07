<?php

namespace App\Http\Controllers;

use App\Models\Flavour;
use Illuminate\Http\Request;
use  Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Services\ImageService;
use App\Http\Services\ErrorService;
use App\Http\Constants\StatusCodes;
use App\Models\PublicUser;
use Validator;
use Illuminate\Support\Facades\Hash;

class UsersApiController
{
    private $users;
    private $status_codes = [];
    private $error_service;
    private $fields = [];

    public function __construct(PublicUser $users, StatusCodes $status_codes, ErrorService $errorService)
    {
        $this->users = $users;
        $this->status_codes = $status_codes;
        $this->error_service = $errorService;
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
                'data' => $this->users::all(),
                'error_message' => null
            ];
            return new JsonResponse($response);
        }
        $response = [
            'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[1],
            'error_message' => $this->status_codes->postRequests()->{"200"}{'empty_users'},
            'data' => null
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
                'error_message' => $this->status_codes->postRequests()->{"406"}{'incorrect_Data'},
                'data' => null
            ];
            return new JsonResponse($response);
        }
        $user = $this->users::where('id', $user_id)
            ->first();

        if (is_null($user)) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[4],
                'error_message' => $this->status_codes->postRequests(['id' => $user_id])->{"200"}{'non_existent_user_id'},
                'data' => null
            ];
            return new JsonResponse($response);
        }

        $response = [
            'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[0],
            'data' => $user,
            'error_message' => null
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
                'error_message' => $this->status_codes->postRequests()->{"406"}{'incorrect_Data'},
                'data' => null
            ];
            return new JsonResponse($response);
        }
        $loaded_user = $this->users::where('username', $username)
            ->first();

        if (empty($loaded_user)) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[4],
                'error_message' => $this->status_codes->postRequests()->{"200"}{'non_existent_user'},
                'data' => null
            ];
            return new JsonResponse($response);
        }
        if ($loaded_user->password === $password) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[0],
                'data' => $loaded_user,
                'error_message' => null
            ];
            return new JsonResponse($response);
        } else {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[1],
                'error_message' => $this->status_codes->postRequests()->{"200"}{'incorrect_password'},
                'data' => null
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
                'error_message' => $this->status_codes->postRequests()->{"406"}{'incorrect_Data'},
                'data' => null
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
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[2],
                'error_message' => $this->error_service->convertErrors($validator->messages()->toArray()),
                'data' => null
            ];

            return new JsonResponse($response);
        }
        try {
            $user = PublicUser::create($user_data);

            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[0],
                'data' => $user,
                'error_message' => null
            ];

        } catch (\Exception $e) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[2],
                'error_message' => $e->getMessage(),
                'data' => null
            ];
            return new JsonResponse($response);
        }


        return new JsonResponse($response);
    }

    public function updateUser(Request $request, $id): JsonResponse
    {
        $user_data = $request->get('user_data');

        if (empty($user_data)) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[3],
                'error_message' => $this->status_codes->postRequests()->{"406"}{'incorrect_Data'},
                'data' => null
            ];
            return new JsonResponse($response);
        }
        $loaded_user = $this->users::find($id);

        if (!isset($loaded_user)) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[2],
                'error_message' => $this->status_codes->postRequests(['id' => $id])->{"200"}{'non_existent_user_id'},
                'data' => null
            ];
            return new JsonResponse($response);
        }

        $validator = Validator::make($user_data, ['email' => "unique:public_users,email,{$id}"]);

        if ($validator->errors()->any()) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[2],
                'error_message' => $this->error_service->convertErrors($validator->messages()->toArray()),
                'data' => null
            ];

            return new JsonResponse($response);
        }

        if (!$loaded_user->update($user_data)) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[2],
                'error_message' => $this->status_codes->postRequests()->{"200"}{'update_failed'},
                'data' => null
            ];
            return new JsonResponse($response);
        }

        $response = [
            'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[0],
            'data' => $this->status_codes->postRequests()->{"200"}{'success_update'},
            'error_message' => null
        ];

        return new JsonResponse($response);
    }

    /**
     * @return JsonResponse
     */
    public function getUserByEmail(Request $request): JsonResponse
    {
        $email = $request->get('email');

        if (empty($email)) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[3],
                'error_message' => $this->status_codes->postRequests()->{"406"}{'incorrect_Data'},
                'data' => null
            ];
            return new JsonResponse($response);
        }
        $loaded_user = $this->users::where('email', $email)->first();

        if (empty($loaded_user)) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[4],
                'error_message' => $this->status_codes->postRequests()->{"200"}{'non_existent_user_email'},
                'data' => null
            ];
            return new JsonResponse($response);
        }
        $response = [
            'status_code' =>  array_keys(get_object_vars($this->status_codes->postRequests()))[0],
            'data' => $loaded_user,
            'error_message' => null
        ];

        return new JsonResponse($response);
    }
}
