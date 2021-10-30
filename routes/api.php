<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartsApiController;

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
//Products Endpoints
Route::post('/get-all-flavours', [App\Http\Controllers\ProductsApiController::class, 'getAllFlavours']);
Route::post('/get-by-category', [App\Http\Controllers\ProductsApiController::class, 'getAllByCategory']);
Route::post('/get-flavour-by-id', [App\Http\Controllers\ProductsApiController::class, 'getFlavourById']);
Route::post('/get-flavour-by-ids', [App\Http\Controllers\ProductsApiController::class, 'getFlavourByIds']);
Route::post('/filter-flavours', [App\Http\Controllers\ProductsApiController::class, 'filterFlavours']);
Route::get('/get-all-categories', [App\Http\Controllers\ProductsApiController::class, 'getAllCategories']);


//Users Endpoints
Route::get('/users/get-all-users', [App\Http\Controllers\UsersApiController::class, 'getAllUsers']);
Route::post('/users/get-user-by-id', [App\Http\Controllers\UsersApiController::class, 'getUserById']);
Route::post('/users/register-user', [App\Http\Controllers\UsersApiController::class, 'registerUser']);
Route::post('/users/login-user', [App\Http\Controllers\UsersApiController::class, 'loginUser']);
Route::put('/users/update-user/{id}', [App\Http\Controllers\UsersApiController::class, 'updateUser']);


//Cart Endpoints
Route::group(['prefix' => 'cart', 'as' => 'cart.'], function () {
    Route::post('/get-cart', [App\Http\Controllers\CartsApiController::class, 'getCart']);
    Route::post('/add-to-cart', [App\Http\Controllers\CartsApiController::class, 'addToCart']);
    Route::post('/remove-from-cart', [App\Http\Controllers\CartsApiController::class, 'removeFromCart']);

});
//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
