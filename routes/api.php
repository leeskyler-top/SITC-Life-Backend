<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::prefix("/auth")->group(function () {
    Route::post("/login", "AuthController@login");
    Route::middleware("auth:api")->group(function () {
        Route::get("/logout", "AuthController@logout");
    });
});

Route::middleware("auth:api")->group(function () {
   Route::apiResource("user", "UserController")->where(['id' => '[0-9]+']);
   Route::apiResource("task", "TaskController")->where(['id' => '[0-9]+']);
   Route::apiResource("semester", "SemesterController")->where(['id' => '[0-9]+']);
});
