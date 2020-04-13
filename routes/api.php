<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('mensagem/processo/{id}', function () {
    return response()->json(['message' => 'Jobs API', 'status' => 'Connected']);;
});

Route::get('mensagem/nao-lidas', function () {
    return response()->json(['message' => 'Jobs API', 'status' => 'Connected']);;
});

Route::get('processo/situacao/total', function () {
    return response()->json(['message' => 'Jobs API', 'status' => 'Connected']);;
});