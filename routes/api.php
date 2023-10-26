<?php

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/users', [\App\Http\Controllers\API\UserController::class, 'register']);
Route::post('/users/login', [\App\Http\Controllers\API\UserController::class, 'login']);

Route::middleware('apiAuth')->group(function () {
    // Route::middleware(\App\Http\Middleware\ApiAuthMiddleware::class)->group(function () {
    Route::get('/users/current', [\App\Http\Controllers\API\UserController::class, 'get']);
    Route::patch('/users/current', [\App\Http\Controllers\API\UserController::class, 'update'])->middleware('userAkses:admin');
    Route::delete('/users/logout', [\App\Http\Controllers\API\UserController::class, 'logout']);

    Route::post('/contacts', [\App\Http\Controllers\API\ContactController::class, 'create']);
    Route::post('/balances', [\App\Http\Controllers\API\BalanceController::class, 'create']);

    Route::get('/contacts', [\App\Http\Controllers\API\ContactController::class, 'search']);
    Route::get('/balances', [\App\Http\Controllers\API\BalanceController::class, 'search']);

    // id harus number
    // Route::get('/contacts/{id=[0-9]}', [\App\Http\Controllers\API\ContactController::class, 'get']);

    Route::get('/contacts/{id}', [\App\Http\Controllers\API\ContactController::class, 'get'])->where('id', '[0-9]+');
    Route::get('/balances/{id}', [\App\Http\Controllers\API\BalanceController::class, 'get'])->where('id', '[0-9]+');

    Route::put('/contacts/{id}', [\App\Http\Controllers\API\ContactController::class, 'update'])->where('id', '[0-9]+')->middleware('userAkses:admin');
    Route::put('/balances/{id}', [\App\Http\Controllers\API\BalanceController::class, 'update'])->where('id', '[0-9]+')->middleware('userAkses:admin');

    Route::put('/balances/add/{id}', [\App\Http\Controllers\API\BalanceController::class, 'add'])->where('id', '[0-9]+');
    Route::put('/balances/subtract/{id}', [\App\Http\Controllers\API\BalanceController::class, 'subtract'])->where('id', '[0-9]+');

    Route::delete('/contacts/{id}', [\App\Http\Controllers\API\ContactController::class, 'delete'])->where('id', '[0-9]+');

    Route::post('/contacts/{idContact}/addresses', [\App\Http\Controllers\API\AddressController::class, 'create'])
        ->where('idContact', '[0-9]+');
    Route::get('/contacts/{idContact}/addresses', [\App\Http\Controllers\API\AddressController::class, 'list'])
        ->where('idContact', '[0-9]+');
    Route::get('/contacts/{idContact}/addresses/{idAddress}', [\App\Http\Controllers\API\AddressController::class, 'get'])
        ->where('idContact', '[0-9]+')
        ->where('idAddress', '[0-9]+');
    Route::put('/contacts/{idContact}/addresses/{idAddress}', [\App\Http\Controllers\API\AddressController::class, 'update'])
        ->where('idContact', '[0-9]+')
        ->where('idAddress', '[0-9]+')
        ->middleware('userAkses:admin');
    Route::delete('/contacts/{idContact}/addresses/{idAddress}', [\App\Http\Controllers\API\AddressController::class, 'delete'])
        ->where('idContact', '[0-9]+')
        ->where('idAddress', '[0-9]+');
});
