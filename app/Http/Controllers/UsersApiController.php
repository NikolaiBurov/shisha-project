<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\GetUserByEmailRequest;
use App\Models\Flavour;
use Illuminate\Http\Request;
use  Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Services\ImageService;
use App\Http\Services\ErrorService;
use App\Http\Constants\StatusCodes;
use App\Models\PublicUser;
use Illuminate\Support\Js;
use Psy\Util\Json;
use Validator;
use Illuminate\Support\Facades\Hash;

class UsersApiController extends BaseApiController
{
    private $fields = [];

    /**
     * @todo remove this and add UserRepository
     * @var PublicUser
     */
    private PublicUser $userModel;

    /**
     * @var StatusCodes
     */
    private StatusCodes $statusCodes;

    /**
     * @var ErrorService
     */
    private ErrorService $errorService;

    /**
     * @param PublicUser $userModel
     * @param StatusCodes $statusCodes
     * @param ErrorService $errorService
     */
    public function __construct(
        PublicUser $userModel,
        StatusCodes $statusCodes,
        ErrorService $errorService
    ) {
        $this->userModel = $userModel;
        $this->statusCodes = $statusCodes;
        $this->errorService = $errorService;
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
        if (!$this->userModel::all()->isEmpty()) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->statusCodes->postRequests()))[0],
                'data' => $this->userModel::all(),
                'error_message' => null
            ];
            return new JsonResponse($response);
        }
        $response = [
            'status_code' => array_keys(get_object_vars($this->statusCodes->postRequests()))[1],
            'error_message' => $this->statusCodes->postRequests()->{"200"}['empty_users'],
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
                'status_code' => array_keys(get_object_vars($this->statusCodes->postRequests()))[3],
                'error_message' => $this->statusCodes->postRequests()->{"406"}['incorrect_Data'],
                'data' => null
            ];
            return new JsonResponse($response);
        }
        $user = $this->userModel::where('id', $user_id)
            ->first();

        if (is_null($user)) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->statusCodes->postRequests()))[4],
                'error_message' => $this->statusCodes->postRequests(['id' => $user_id]
                )->{"200"}['non_existent_user_id'],
                'data' => null
            ];
            return new JsonResponse($response);
        }

        $response = [
            'status_code' => array_keys(get_object_vars($this->statusCodes->postRequests()))[0],
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
        $email = $request->get('email');

        $password = $request->get('password');

        if (empty($email) || empty($password)) {
            return $this->buildResult(
                array_keys(get_object_vars($this->statusCodes->postRequests()))[3],
                $this->statusCodes->postRequests()->{"406"}['incorrect_Data'],
            );
        }

        $loaded_user = $this->userModel::whereEmail($email)->first();

        if (empty($loaded_user)) {
            return $this->buildResult(
                StatusCodes::HTTP_NOT_FOUND,
                $this->statusCodes->postRequests()->{"200"}['non_existent_user'],
            );
        }
        $loaded_user->makeHidden(['password', 'salt']);

        if ($loaded_user->password === $password) {
            return $this->buildResult(
                StatusCodes::HTTP_OK,
                null,
                $loaded_user
            );
        } else {
            return $this->buildResult(
                StatusCodes::HTTP_NOT_FOUND,
                $this->statusCodes->postRequests()->{"200"}['incorrect_password'],
            );
        }
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function registerUser(Request $request): JsonResponse
    {
        $this->setFields(
            [
                'username',
                'first_name',
                'last_name',
                'password',
                'salt',
                'email_token',
                'email',
                'password_reset_token',
                'city',
                'address',
                'created_at'
            ]
        );

        $user_data = $request->get('user_data');

        if (empty($user_data)) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->statusCodes->postRequests()))[3],
                'error_message' => $this->statusCodes->postRequests()->{"406"}['incorrect_Data'],
                'data' => null
            ];
            return new JsonResponse($response);
        }


        $fields = $this->getFields();
        $new_fields = [];

        array_walk($fields, function ($a) use (&$new_fields) {
            $new_fields[$a] = 'present';
        });

        $validate_fields = array_merge(
            $new_fields,
            ['email' => 'unique:public_users|required', 'username' => 'unique:public_users|required']
        );


        $validator = Validator::make($user_data, $validate_fields);

        if ($validator->errors()->any()) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->statusCodes->postRequests()))[2],
                'error_message' => $this->errorService->convertErrors($validator->messages()->toArray()),
                'data' => null
            ];

            return new JsonResponse($response);
        }
        try {
            $user = PublicUser::create($user_data);
            unset($user->password);
            unset($user->salt);

            $response = [
                'status_code' => array_keys(get_object_vars($this->statusCodes->postRequests()))[0],
                'data' => $user,
                'error_message' => null
            ];
        } catch (\Exception $e) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->statusCodes->postRequests()))[2],
                'error_message' => $e->getMessage(),
                'data' => null
            ];
            return new JsonResponse($response);
        }


        return new JsonResponse($response);
    }

    public function updateUser(Request $request): JsonResponse
    {
        $user_data = $request->get('user_data');

        if (empty($user_data)) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->statusCodes->postRequests()))[3],
                'error_message' => $this->statusCodes->postRequests()->{"406"}['incorrect_Data'],
                'data' => null
            ];
            return new JsonResponse($response);
        }
        $id = $user_data['id'];

        $loaded_user = $this->userModel::find($id);

        if (!isset($loaded_user)) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->statusCodes->postRequests()))[2],
                'error_message' => $this->statusCodes->postRequests(['id' => $id])->{"200"}['non_existent_user_id'],
                'data' => null
            ];
            return new JsonResponse($response);
        }

        $validator = Validator::make($user_data, [
            'email' => "unique:public_users,email,{$id}",
            'username' => "unique:public_users,username,{$id}"
        ]);

        if ($validator->errors()->any()) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->statusCodes->postRequests()))[2],
                'error_message' => $this->errorService->convertErrors($validator->messages()->toArray()),
                'data' => null
            ];

            return new JsonResponse($response);
        }

        if (!$loaded_user->update($user_data)) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->statusCodes->postRequests()))[2],
                'error_message' => $this->statusCodes->postRequests()->{"200"}['update_failed'],
                'data' => null
            ];
            return new JsonResponse($response);
        }

        $response = [
            'status_code' => array_keys(get_object_vars($this->statusCodes->postRequests()))[0],
            'data' => $this->statusCodes->postRequests()->{"200"}['success_update'],
            'error_message' => null
        ];

        return new JsonResponse($response);
    }

    /**
     * @return JsonResponse
     */
    public function getUserByEmail(GetUserByEmailRequest $request): JsonResponse
    {
        $user = $this->userModel->whereEmail($request->get('email'))->first();

        if (is_null($user)) {
            return $this->buildResult(StatusCodes::HTTP_NOT_FOUND, 'User not found', null);
        }

        return $this->buildResult(StatusCodes::HTTP_OK, null, $user);
    }

    public function getUserByEmailToken(Request $request): JsonResponse
    {
        if (!$request->get('email_token')) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->statusCodes->postRequests()))[4],
                'error_message' => $this->statusCodes->postRequests()->{"200"}['non_existent_user_email_token'],
                'data' => null
            ];
            return new JsonResponse($response);
        }
        $user = $this->userModel::where('email_token', $request->get('email_token'));

        if ($user->exists()) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->statusCodes->postRequests()))[0],
                'error_message' => null,
                'data' => $user->first()->makeHidden(['password', 'salt'])->toArray()
            ];
            return new JsonResponse($response);
        }

        $response = [
            'status_code' => array_keys(get_object_vars($this->statusCodes->postRequests()))[4],
            'error_message' => $this->statusCodes->postRequests()->{"200"}['non_existent_user_email_token'],
            'data' => null
        ];
        return new JsonResponse($response);
    }


}
