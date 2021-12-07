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
Route::group(['prefix' => 'users'], function () {
    Route::get('/get-all-users', [App\Http\Controllers\UsersApiController::class, 'getAllUsers']);
    Route::post('/get-user-by-id', [App\Http\Controllers\UsersApiController::class, 'getUserById']);
    Route::post('/register-user', [App\Http\Controllers\UsersApiController::class, 'registerUser']);
    Route::post('/login-user', [App\Http\Controllers\UsersApiController::class, 'loginUser']);
    Route::put('/update-user/{id}', [App\Http\Controllers\UsersApiController::class, 'updateUser']);
    Route::post('/get-user-by-email', [App\Http\Controllers\UsersApiController::class, 'getUserByEmail']);
});

//Cart Endpoints
Route::group(['prefix' => 'cart', 'as' => 'cart.'], function () {
    Route::post('/get-cart', [App\Http\Controllers\CartsApiController::class, 'getCart']);
    Route::post('/add-to-cart', [App\Http\Controllers\CartsApiController::class, 'addToCart']);
    Route::post('/remove-from-cart', [App\Http\Controllers\CartsApiController::class, 'removeFromCart']);

});
//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
