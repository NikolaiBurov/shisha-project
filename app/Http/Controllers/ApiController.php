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

    public  function  __construct(){
        $this->status_codes = (new StatusCodes());
    }
    public function getAllFlavours(Request $request) : ?JsonResponse
    {   $flavours = Flavour::all();

        $response = ['status' => (new Response())->status() ,'data'=> $flavours];

        return new JsonResponse($response);
    }

    /**
     * @var category_id Request int
     * @return JsonResponse
     * Returns all products by given category id
     */
    public function getAllByCategory(Request $request) :?JsonResponse
    {
        $category_id  = $request->get('category_id');

        $products = Flavour::where('category_id', $category_id)
               ->orderBy('title', 'desc')
               ->get();

        foreach ($products as $key => $flavours) {

            if ($flavours->image){
                $flavours->image  = ImageService::absolutePath($flavours,$request);
            }
        }
        $response = ['status' => (new Response())->status() ,'data'=> $products];

        return new JsonResponse($response);
    }

    /**
     * @param Request $request
     */
    public function  getFlavourById(Request $request): JsonResponse
    {
        /** @var TYPE_NAME $flavour_id */
        $flavour_id  = $request->get('id');

        /** @var TYPE_NAME $response */
        $response = [];

        if(empty($flavour_id)){
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[3],
                'data' => $this->status_codes->postRequests()->{"406"}{'incorrect_Data'}
            ];
            return new JsonResponse($response);
        }
        $product = Flavour::where('id', $flavour_id)
            ->orderBy('title', 'desc')
            ->first();

        if(empty($product)){
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[0],
                'data' => $this->status_codes->postRequests()->{"200"}{'non_existent_product'}
            ];
            return new JsonResponse($response);
        }
        if ($product['image']) {
            $product['image'] = ImageService::absolutePath($product, $request);
        }

        $response = ['status' => (new Response())->status() ,'data'=> $product];

        return new JsonResponse($response);


    }

    public function getAllCategories(Request  $request): JsonResponse
    {

        /** @var TYPE_NAME $response */
        $response = [];

        $categories = Category::all();

        if(empty($categories)){
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[0],
                'data' => $this->status_codes->postRequests()->{"200"}{'empty_categories'}
            ];
            return new JsonResponse($response);
        }


        $response = ['status' => (new Response())->status() ,'data'=> $categories];

        return new JsonResponse($response);

    }
}
