<?php

use App\Http\Controllers\BlogController;
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

Route::group(["prefix" => "/"], function () {
    Route::get("/", [BlogController::class, "index"]);
    Route::get("page/{page?}", [BlogController::class, "index"])->where("page", "[0-9]+");

    Route::get("refresh", [BlogController::class, "refresh"]);
    Route::get("new", [BlogController::class, "form"]);
    Route::post('do_save_post', [BlogController::class, "do_save_post"]);

    Route::group(
        [
            'prefix' => 'view/{post_id}',
            'where' => [
                "post_id" => "[0-9]+"
            ]
        ],
        function () {
            Route::get('/', [BlogController::class, "view"]);
            Route::get("edit", [BlogController::class, "form"]);
            Route::get("delete", [BlogController::class, "delete"]);
            Route::post('do_comment', [BlogController::class, "do_comment"]);
            Route::post('do_save_post', [BlogController::class, "do_save_post"]);
        }
    );

    Route::group(
        [
            "prefix" => "u/{userId}",
            "where"  => ["userId" =>  "[0-9]+"]
        ],
        function () {
            Route::get("/", [BlogController::class, "index"]);
            Route::get("page/{page}", [BlogController::class, "index"]);
        }
    );

    Route::get("archive/{date}", [BlogController::class, "index"]);
    Route::get("archive/{date}/page/{page}", [BlogController::class, "index"])->where("page", "[0-9]+");
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');
