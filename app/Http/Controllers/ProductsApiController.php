<?php

namespace App\Http\Controllers;

use App\Models\Flavour;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use  Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Services\ImageService;
use App\Http\Constants\StatusCodes;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Models\Category;
use App\Http\Services\TranslationsHelper;

class ProductsApiController extends Controller
{

    public function getAllFlavours(Request $request): ?JsonResponse
    {
        $lang = $request->get('language');

        $request->items_per_page = $request->filled('items_per_page') ? $request->get('items_per_page') : 6;

        $current_page = $request->filled('page') ? $request->get('page') : 1;

        $paginated = $this->flavours->getFlavoursByRequest($request);

        $result = $this->translation_helper->translateFilteredResults($paginated, $request->get('language'), $request, $this->status_codes, $current_page);

        return new JsonResponse($result);
    }

    /**
     * @return JsonResponse
     * Returns all products by given category id
     * @var category_id Request int
     */
    public function getAllByCategory(Request $request): ?JsonResponse
    {
        /** @var  $category_id */
        $category_id = $request->get('category_id');

        /** @var  $lang */
        $lang = $request->get('language');

        if (empty($category_id)) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[3],
                'error_message' => $this->status_codes->postRequests()->{"406"}['incorrect_Data'],
                'data' => null
            ];
            return new JsonResponse($response);
        }
        $products = $this->translation_helper->languangeMapper(
            $lang,
            Flavour::where('category_id', $category_id)
                ->orderBy('id', 'asc')
                ->withTranslations($lang)
                ->get(),
            $request
        );

        if (is_null($products)) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[1],
                'error_message' => $this->status_codes->postRequests()->{"200"}['empty_flavours'],
                'data' => null
            ];
            return new JsonResponse($response);
        }


        $response = ['status' => (new Response())->status(), 'data' => $products, 'error_message' => null];

        return new JsonResponse($response);
    }

    /**
     * @param Request $request
     */
    public function getFlavourById(Request $request): JsonResponse
    {
        /** @var  $flavour_id */
        $flavour_id = $request->get('id');


        /** @var  $lang */
        $lang = $request->get('language');

        /** @var  $response */
        $response = [];

        if (empty($flavour_id)) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[3],
                'error_message' => $this->status_codes->postRequests()->{"406"}['incorrect_Data'],
                'data' => null
            ];
            return new JsonResponse($response);
        }
        $product = $this->translation_helper->languangeMapper($lang,
            Flavour::where('id', $flavour_id)
                ->withTranslations($lang)
                ->get(),
            $request);


        if (is_null($product)) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[1],
                'error_message' => $this->status_codes->postRequests()->{"200"}['non_existent_product'],
                'data' => null
            ];
            return new JsonResponse($response);
        }


        $response = ['status_code' => (new Response())->status(), 'data' => $product, 'error_message' => null];

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
                'error_message' => $this->status_codes->postRequests()->{"200"}['empty_categories'],
                'data' => null
            ];
            return new JsonResponse($response);
        }


        $response = ['status' => (new Response())->status(), 'data' => $categories, 'error_message' => null];

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

        /** @var  $lang */
        $lang = $request->get('language');

        /** @var  $response */
        $response = [];

        /** @var  $found_ids */
        $found_ids = [];

        if (empty($flavour_ids)) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[3],
                'error_message' => $this->status_codes->postRequests()->{"406"}['incorrect_Data'],
                'data' => null
            ];
            return new JsonResponse($response);
        }
        if (!is_array($flavour_ids)) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[0],
                'error_message' => $this->status_codes->postRequests()->{"200"}['wrong_data_type'],
                'data' => null
            ];
            return new JsonResponse($response);
        }
        $flavours = $this->translation_helper->languangeMapper($lang,
            Flavour::whereIn('id', $flavour_ids)
                ->orderBy('id', 'asc')
                ->withTranslations($lang)
                ->get(),
            $request);

        if (is_null($flavours)) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[0],
                'error_message' => $this->status_codes->postRequests()->{"200"}['product_list_empty'],
                'data' => null
            ];
            return new JsonResponse($response);
        }

        foreach ($flavours as $id => $item) {
            $found_ids[] = $item['id'];
        }

        $not_found_ids = array_diff($flavour_ids, $found_ids)
            ? implode(",", array_diff($flavour_ids, $found_ids))
            : [];


        $response = [
            'status_code' => (new Response())->status(),
            'data' => $flavours,
            'error_message' => null,
            'not_found_flavour_ids' => $not_found_ids
        ];

        return new JsonResponse($response);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * This method goes through the variations relation of flavours and filter them accoring to what
     * is given from request
     * @with  used to filter them - goes through flavour_Variations relation
     * @wherehas is to include them in the object -  goes through flavour_Variations relation
     * @where - filters flavours table
     */
    public function filterFlavours(Request $request): JsonResponse
    {

        $request->items_per_page = $request->filled('items_per_page') ? $request->get('items_per_page') : 6;

        $current_page = $request->filled('page') ? $request->get('page') : 1;

        $flavours = $this->flavours->getFlavoursByRequest($request);

        $result = $this->translation_helper->translateFilteredResults($flavours, $request->get('language'), $request, $this->status_codes, $current_page);

        return new JsonResponse($result);


    }

    public function relatedProducts(Request $request)
    {
        if (empty($request->get('flavour_type')) || empty($request->get('language')) || empty($request->get('related_flavour_id'))) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[3],
                'error_message' => $this->status_codes->postRequests()->{"406"}['incorrect_Data'],
                'data' => null
            ];
            return new JsonResponse($response);
        }
        $result = $this->translation_helper->languangeMapper($request->get('language'),
            $this->flavours::where('flavour_type', $request->get('flavour_type'))
                ->where('id', '!=', $request->get('related_flavour_id'))
                ->orderBy(DB::raw('RAND()'))->get(),
            $request);

        if (empty($result)) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[1],
                'error_message' => $this->status_codes->postRequests()->{"200"}['no_products'],
                'data' => null
            ];
            return new JsonResponse($response);
        }
        $response = [
            'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[0],
            'error_message' => null,
            'data' => $result
        ];

        return new JsonResponse($response);
    }
}
