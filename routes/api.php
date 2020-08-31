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

Route::get('mensagem/processo/{id}', function(){});
Route::get('mensagem/nao-lidas', function(){});

Route::get('processo/andamento', 'ProcessoController@getProcessosAndamento');
Route::get('processo/correspondente/andamento', 'ProcessoController@getProcessosAndamentoCorrespondente');
Route::get('processo/situacao/prazo', 'ProcessoController@getStatusPrazo');