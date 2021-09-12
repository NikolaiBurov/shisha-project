<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('forbidden');
// });

Route::get('/', [App\Http\Controllers\HomeController::class, 'forbidden'])->name('forbidden');
Route::get('/get-all-flavours', [App\Http\Controllers\ApiController::class, 'getAllFlavours']);
Route::get('/get-by-category', [App\Http\Controllers\ApiController::class, 'getAllByCategory']);
//Auth::routes();



// Route::get('/', function () {
//     return view('/home');
// });


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
