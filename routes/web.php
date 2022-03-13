<?php

use App\Http\Controllers\VoyagerLabelsController;
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

//Auth::routes();



// Route::get('/', function () {
//     return view('/home');
// });


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
    Route::get('/labels', [VoyagerLabelsController::class, 'index'])->name('voyager_labels')->middleware('labels');;
    Route::post('/labels-edit', [VoyagerLabelsController::class, 'editLabel'])->name('voyager_labels_edit');

});
