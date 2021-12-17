<?php

namespace App\Http\Controllers;

use App\Models\Flavour;
use App\Models\FlavourVariation;
use Illuminate\Http\Request;
use  Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Services\ImageService;
use App\Http\Services\ErrorService;
use App\Http\Constants\StatusCodes;
use App\Models\PublicUser;
use App\Models\Cart;
use  App\Http\Services\CartHelper;
use Validator;

class CartsApiController extends Controller
{
    private $fields = [];

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
        $this->setFields(['user_id']);
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
        $cart = Cart::where('user_id', $data['user_id'])->orderBy('id', 'ASC')->get()->toArray();

        if (empty($cart)) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[1],
                'error_message' => $this->status_codes->postRequests()->{"200"}{'empty_cart'},
                'data' => null
            ];
            return new JsonResponse($response);
        }

        $mapped_flavours = $this->cart_helper->mapProducts($cart,$request);

        $response = [
            'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[0],
            'data' => $mapped_flavours,
            'error_message' => null
        ];
        return new JsonResponse($response);
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
        //Check for if data exsists
        if (!$this->users::where('id', $data['user_id'])->exists()) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[1],
                'error_message' => $this->status_codes->postRequests()->{"200"}{'user_not_exists'},
                'data' => null
            ];

            return new JsonResponse($response);
        }

        if (!$this->flavours::where('id', $data['flavour_id'])->exists()) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[1],
                'error_message' => $this->status_codes->postRequests()->{"200"}{'flavour_not_exists'},
                'data' => null
            ];

            return new JsonResponse($response);
        }

        if (!$this->flavour_variations::where('id', $data['flavour_variation_id'])->exists()) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[1],
                'error_message' => $this->status_codes->postRequests()->{"200"}{'flavour_variation_exists'},
                'data' => null
            ];

            return new JsonResponse($response);
        }
        //check if this variation belongs to given flavour
        if (!$this->flavour_variations::where('id', $data['flavour_variation_id'])
            ->where('flavour_id', $data['flavour_id'])
            ->exists()) {

            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[1],
                'error_message' => $this->status_codes->postRequests()->{"200"}{'wrong_variation'},
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
                $cart_update->quantity = $cart_update->quantity += $data['quantity'];
                $update = $cart_update->save();

            } catch (\Exception $e) {
                $response = ['status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[1], 'data' => null, 'error_message' => $e->getMessage()];
            }

            $response = ['status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[0],
                'data' => $update ? 'Updated' : 'Failed',
                'error_message' => null
            ];
        } else {

            try {
                $save = Cart::create($data);
            } catch (\Exception $e) {
                $response = ['status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[1], 'data' => null, 'error_message' => $e->getMessage()];
            }

            $response = ['status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[0],
                'data' => $save->exists ? 'Created' : 'Failed',
                'error_message' => null
            ];

        }

        return new JsonResponse($response);
    }

    public function removeFromCart(Request $request): JsonResponse
    {
        $this->setFields(['user_id', 'flavour_id', 'flavour_variation_id', 'quantity_remove']);
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
        //Check for if data exsists
        if (!$this->users::where('id', $data['user_id'])->exists()) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[1],
                'error_message' => $this->status_codes->postRequests()->{"200"}{'user_not_exists'},
                'data' => null
            ];

            return new JsonResponse($response);
        }

        if (!$this->flavours::where('id', $data['flavour_id'])->exists()) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[1],
                'error_message' => $this->status_codes->postRequests()->{"200"}{'flavour_not_exists'},
                'data' => null
            ];

            return new JsonResponse($response);
        }

        if (!$this->flavour_variations::where('id', $data['flavour_variation_id'])->exists()) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[1],
                'error_message' => $this->status_codes->postRequests()->{"200"}{'flavour_variation_exists'},
                'data' => null
            ];

            return new JsonResponse($response);
        }

        $cart = Cart::where('user_id', $data['user_id'])
            ->where('flavour_id', $data['flavour_id'])
            ->where('flavour_variation_id', $data['flavour_variation_id'])->get()->toArray();

        if (!empty($cart)) {
            $cart_update = Cart::find($cart[0]['id']);
            if ($cart_update->quantity >= 1) {
                $cart_update->quantity = $cart_update->quantity -= $data['quantity_remove'];
                if ($cart_update->quantity == 0) {
                    Cart::destroy([$cart_update->id]);
                    $response['data'] = $this->status_codes->postRequests($cart_update)->{"200"}{'cart_entity_deleted'};
                } else {
                    $cart_update->save();
                    $response['data'] = $cart_update;
                }
            }

        } else {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[1],
                'error_message' => $this->status_codes->postRequests()->{"200"}{'cart_exists'},
                'data' => null
            ];
        }
        $response['status_code'] = array_keys(get_object_vars($this->status_codes->postRequests()))[0];
        $response['error_message'] = null;
        return new JsonResponse($response);
    }
}
