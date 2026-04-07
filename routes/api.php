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
Route::get('mensagem/destinatario/nao-lidas/{id}', 'MensagemController@getMensagensByDestinatario');

Route::get('cliente/processo/andamento', 'ClienteProcessoController@getProcessosAndamento');
Route::get('processo/andamento', 'ProcessoController@getProcessosAndamento');
Route::get('processo/correspondente/andamento', 'ProcessoController@getProcessosAndamentoCorrespondente');
Route::get('processo/situacao/prazo', 'ProcessoController@getStatusPrazo');
Route::get('processo/{id}', 'ProcessoController@getDados');
Route::post('processo/pauta', 'ProcessoController@listarPauta');
Route::post('cliente/processo/pauta', 'ClienteProcessoController@listarPauta');

Route::get('cliente/dashboard/contadores',  'ClienteProcessoController@dashboardContadores');
Route::get('cliente/dashboard/pauta-hoje',  'ClienteProcessoController@dashboardPautaHoje');
Route::get('cliente/dashboard/proximas',    'ClienteProcessoController@dashboardProximas');
Route::get('cliente/dashboard/mensagens',   'ClienteProcessoController@dashboardMensagens');
Route::get('cliente/dashboard/status',      'ClienteProcessoController@dashboardStatus');