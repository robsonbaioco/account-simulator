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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/contas/{number}/saldo', 'App\Http\Controllers\AccountController@saldo');
Route::post('/contas/{number}/sacar/{value}', 'App\Http\Controllers\AccountController@sacar');
Route::post('/contas/{number}/depositar/{value}', 'App\Http\Controllers\AccountController@depositar');
Route::get('/contas', 'App\Http\Controllers\AccountController@listar_contas');
