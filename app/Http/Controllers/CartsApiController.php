<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use  Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Services\ImageService;
use App\Http\Services\ErrorService;
use App\Http\Constants\StatusCodes;
use App\Models\PublicUser;
use App\Models\Cart;
use Validator;

class CartsApiController extends Controller
{
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

    public function getCart(Request $request): JsonResponse
    {
        $cart = Cart::all();
        return new JsonResponse($cart);
    }

    public function addToCart(Request $request): JsonResponse
    {
        $this->setFields(['user_id', 'flavour_id', 'flavour_variation_id', 'quantity']);
        $data = $request->all();

        $fields = $this->getFields();
        $new_fields = [];

        array_walk($fields, function ($a) use (&$new_fields) {
            $new_fields[$a] = 'present';
        });
        $validator = Validator::make($data, $new_fields);

        if ($validator->errors()->any()) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[2],
                'error_message' => $this->error_service->convertErrors($validator->messages()->toArray()),
                'data' => null
            ];

            return new JsonResponse($response);
        }

        //check if exists
        $cart = Cart::where('user_id', $data['user_id'])
            ->where('flavour_id', $data['flavour_id'])
            ->where('flavour_variation_id', $data['flavour_variation_id'])->get()->toArray();

        if (!empty($cart)) {
            try {
                $cart_update = Cart::find($cart[0]['id']);
                $cart_update->quantity = $cart_update->quantity += 1;
                $update = $cart_update->save();

            } catch (\Exception $e) {
                $response = ['status_code' => 404, 'data' => null, 'error_message' => $e->getMessage()];
            }

            $response = ['status_code' => 200, 'data' => $update ? 'Updated' : 'Failed', 'error_message' => null];
        } else {

            try {
                $save = Cart::create($data);
            } catch (\Exception $e) {
                $response = ['status_code' => 404, 'data' => null, 'error_message' => $e->getMessage()];
            }

            $response = ['status_code' => 200, 'data' => $save->exists ? 'Created' : 'Failed', 'error_message' => null];

        }


        return new JsonResponse($response);
    }

    public function removeFromCart(Request $request): JsonResponse
    {
        return new JsonResponse('todo');
    }
}
