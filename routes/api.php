<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
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
Route::options('/{any}', function () {
    return response()->json(null,204);
})->where('any', '.*');

Route::prefix("/auth")->group(function () {
    Route::post("/login", [AuthController::class, "login"]);
    Route::middleware("auth:api")->group(function () {
        Route::delete("/logout", [AuthController::class, "logout"]);
    });
});

Route::middleware("admin")->group(function () {
    Route::prefix("/files")->group(function () {
        Route::get("/avatar/{user_id}", [FileController::class, "avatar"])->where(['user_id' => '[0-9]+']);
    });
   Route::apiResource("user", UserController::class)->where(['user' => '[0-9]+']);
   Route::apiResource("task", TaskController::class)->where(['task' => '[0-9]+']);
   Route::apiResource("semester", SemesterController::class)->where(['semester' => '[0-9]+']);
});

Route::middleware("auth:api")->group(function () {
   Route::prefix("/files")->group(function () {
      Route::get("/myavatar", [FileController::class, "myAvatar"]);
   });
});
