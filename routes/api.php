<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\TodoController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Register Route
Route::post('register', [ApiController::class, 'register']);

// Verification Route
Route::post('verify', [ApiController::class, 'verification']);

// Login Route
Route::post('login', [ApiController::class, 'authenticate']);

Route::group(['middleware' => ['jwt.verify']], function() {
    // logout Route
    Route::get('logout', [ApiController::class, 'logout']);

    // Authorized and Authenticated User can only view his profile
    Route::get('profile', [ApiController::class, 'get_user']);

    // Authorized and Authenticated User can only view his todo lists
    Route::get('todos', [TodoController::class, 'index']);

    // Authorized and Authenticated User can search his todo list by id
    Route::get('todo/{id}', [TodoController::class, 'show']);

    // Authorized and Authenticated User can create his todo list
    Route::post('create', [TodoController::class, 'store']);

    // Authorized and Authenticated User can update his todo list by id
    Route::post('update/{id}',  [TodoController::class, 'update']);

    // Authorized and Authenticated User can delete his todo list by id
    Route::delete('delete/{id}',  [TodoController::class, 'destroy']);
});