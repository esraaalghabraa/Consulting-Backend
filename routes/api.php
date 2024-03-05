<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ExpertAuthController;
use App\Http\Controllers\ExpertController;
use App\Http\Controllers\UserAuthController;
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


Route::prefix('expert')->group(function () {
    Route::post('login', [ExpertAuthController::class,'login']);
    Route::post('register', [ExpertAuthController::class,'register']);
    Route::middleware('auth.experts')->group(function () {
        Route::post('logout', [ExpertAuthController::class,'logout']);
        Route::get('get-categories-and-days',[ExpertController::class,'getCategoriesAndDays']);
        Route::post('add-expert-info',[ExpertController::class,'addInfo']);
        Route::post('get-expert-info',[ExpertController::class,'getInfo']);
        Route::post('get-expert-bookings',[ExpertController::class, 'getExpertBookings']);
        Route::post('get-expert-follows',[ExpertController::class, 'getExpertFollows']);
    });
});

Route::prefix('user')->group(function () {
    Route::post('login', [UserAuthController::class,'login']);
    Route::post('register', [UserAuthController::class,'register']);
    Route::middleware(['auth.users'])->group(function () {
        Route::post('get-user-info',[UserController::class,'getUserInfo']);
        Route::get('get-categories',[UserController::class,'getCategories']);
        Route::post('get-favorites',[UserController::class, 'getFavorites']);
        Route::post('get-bookings',[UserController::class,'getBookings']);
        Route::post('get-category-experts',[UserController::class, 'getCategoryExperts']);
        Route::post('get-expert',[UserController::class,'getExpert']);
        Route::post('get-available-date',[UserController::class, 'getAvailableDate']);
        Route::post('add-booking',[UserController::class,'addBooking']);
        Route::post('add-export-favorite',[UserController::class,'changeFavorite']);
        Route::post('add-rating',[UserController::class,'addRating']);
        Route::post('logout', [UserAuthController::class,'logout']);

    });
});

