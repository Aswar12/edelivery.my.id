<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\ProductCategoryController;
use App\Http\Controllers\API\UserLocationController;

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


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('user', [UserController::class, 'fetch']);
    Route::post('user', [UserController::class, 'updateProfile']);
    Route::post('user-photo', [UserController::class, 'uploadPhoto']);
    Route::post('logout', [UserController::class, 'logout']);
    Route::get('address-list', [UserController::class,'address_list']);
    Route::post('address-add', [UserController::class,'add_new_address']);
    Route::put('address-update/{id}',[UserController::class,'update_address']);
    Route::delete('address-delete/{id}',[UserController::class,'delete_address']);
    Route::resource('address', UserLocationController::class);
    Route::post('geocode-api', [UserController::class,'geocode_api']);

    Route::get('transactions', [TransactionController::class, 'all']);
    Route::post('checkout', [TransactionController::class, 'checkout']);
});


Route::get('products', [ProductController::class, 'all']);
Route::get('categories', [ProductCategoryController::class, 'all']);

Route::post('login', [UserController::class, 'login']);
Route::post('register', [UserController::class, 'register']);