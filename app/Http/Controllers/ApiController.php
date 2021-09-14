<?php

namespace App\Http\Controllers;
use App\Models\Flavour;
use Illuminate\Http\Request;
use  Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{

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


        $response = ['status' => (new Response())->status() ,'data'=> $products];

        return new JsonResponse($response);
    }
}
