<?php

use App\Http\Controllers\ExampleController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//user related routes
Route::get('/', [UserController::class, "showCorrectHomepage"])->name('login');
Route::get('/about', [ExampleController::class, "aboutPage"]);
Route::post('/register', [UserController::class, "register"])->middleware('guest');
Route::post('/login', [UserController::class, "login"])->middleware('guest');
Route::post('/logout', [UserController::class, "logout"])->middleware('MustBeLoggedIn');

//blog related routes
Route::get('/create-post', [PostController::class, "showCreateForm"])->middleware('MustBeLoggedIn');
Route::post('/create-post', [PostController::class, "storeNewPost"])->middleware('MustBeLoggedIn');
Route::get('/post/{post}', [PostController::class, "viewSinglePost"])->middleware('MustBeLoggedIn');
