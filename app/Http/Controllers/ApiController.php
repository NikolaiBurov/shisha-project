<?php

namespace App\Http\Controllers;

use App\Models\Flavour;
use Illuminate\Http\Request;
use  Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Services\ImageService;
use App\Http\Constants\StatusCodes;
use TCG\Voyager\Models\Category;

class ApiController extends Controller
{

    /**
     * @var StatusCodes
     */
    private $status_codes;

    public function __construct()
    {
        $this->status_codes = (new StatusCodes());
    }

    public function getAllFlavours(Request $request): ?JsonResponse
    {
        $flavours = Flavour::all();
        if (empty($flavours)) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[1],
                'data' => $this->status_codes->postRequests()->{"200"}{'empty_flavours'}
            ];
            return new JsonResponse($response);
        }

         foreach ($flavours as $key => $flavour) {
            if ($flavour->image) {
                $flavour->image = ImageService::absolutePath($flavour, $request);
            }
        }
        $response = ['status' => (new Response())->status(),
            'flavours' => $flavours];

        return new JsonResponse($response);
    }

    /**
     * @return JsonResponse
     * Returns all products by given category id
     * @var category_id Request int
     */
    public function getAllByCategory(Request $request): ?JsonResponse
    {
        $category_id = $request->get('category_id');

        if (empty($category_id)) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[3],
                'data' => $this->status_codes->postRequests()->{"406"}{'incorrect_Data'}
            ];
            return new JsonResponse($response);
        }
        $products = Flavour::where('category_id', $category_id)
            ->orderBy('title', 'desc')
            ->get();

        if ($products->isEmpty()) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[1],
                'data' => $this->status_codes->postRequests()->{"200"}{'empty_flavours'}
            ];
            return new JsonResponse($response);
        }
        foreach ($products as $key => $flavours) {

            if ($flavours->image) {
                $flavours->image = ImageService::absolutePath($flavours, $request);
            }
        }
        $response = ['status' => (new Response())->status(), 'flavours' => $products];

        return new JsonResponse($response);
    }

    /**
     * @param Request $request
     */
    public function getFlavourById(Request $request): JsonResponse
    {
        /** @var TYPE_NAME $flavour_id */
        $flavour_id = $request->get('id');

        /** @var TYPE_NAME $response */
        $response = [];

        if (empty($flavour_id)) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[3],
                'data' => $this->status_codes->postRequests()->{"406"}{'incorrect_Data'}
            ];
            return new JsonResponse($response);
        }
        $product = Flavour::where('id', $flavour_id)
            ->orderBy('title', 'desc')
            ->first();

        if (empty($product)) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[0],
                'data' => $this->status_codes->postRequests()->{"200"}{'non_existent_product'}
            ];
            return new JsonResponse($response);
        }
        if ($product['image']) {
            $product['image'] = ImageService::absolutePath($product, $request);
        }

        $response = ['status' => (new Response())->status(), 'flavours' => $product];

        return new JsonResponse($response);


    }

    public function getAllCategories(Request $request): JsonResponse
    {

        /** @var TYPE_NAME $response */
        $response = [];

        $categories = Category::all();

        if (empty($categories)) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[0],
                'data' => $this->status_codes->postRequests()->{"200"}{'empty_categories'}
            ];
            return new JsonResponse($response);
        }


        $response = ['status' => (new Response())->status(), 'categories' => $categories];

        return new JsonResponse($response);

    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getFlavourByIds(Request $request): JsonResponse
    {
        /** @var  $flavour_ids */
        $flavour_ids = $request->get('flavour_ids');

        /** @var  $response */
        $response = [];

        /** @var  $found_ids */
        $found_ids = [];
        if (empty($flavour_ids)) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[3],
                'data' => $this->status_codes->postRequests()->{"406"}{'incorrect_Data'}
            ];
            return new JsonResponse($response);
        }
        if (!is_array($flavour_ids)) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[0],
                'data' => $this->status_codes->postRequests()->{"200"}{'wrong_data_type'}
            ];
            return new JsonResponse($response);
        }
        $flavours = Flavour::whereIn('id', $flavour_ids)
            ->orderBy('title', 'desc')
            ->get();


        if (empty($flavours)) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[0],
                'data' => $this->status_codes->postRequests()->{"200"}{'product_list_empty'}
            ];
            return new JsonResponse($response);
        }


        foreach ($flavours as $key => $flavour) {
            $found_ids[] = $flavour->id;
            if ($flavour->image) {
                $flavour->image = ImageService::absolutePath($flavour, $request);
            }
        }
        $not_found_ids = array_diff($flavour_ids, $found_ids)
            ? implode(",", array_diff($flavour_ids, $found_ids))
            : [];


        $response = ['status' => (new Response())->status(),
            'flavours' => $flavours,
            'not_found_flavour_ids' => $not_found_ids
        ];

        return new JsonResponse($response);
    }
}
