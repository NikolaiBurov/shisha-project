<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('/get-all-flavours', [App\Http\Controllers\ApiController::class, 'getAllFlavours']);
Route::post('/get-by-category', [App\Http\Controllers\ApiController::class, 'getAllByCategory']);
Route::post('/get-product-by-id', [App\Http\Controllers\ApiController::class, 'getFlavourById']);
//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
