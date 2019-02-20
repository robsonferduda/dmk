<?php

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

Route::get('/', 'HomeController@index')->name('home');

Auth::routes();

Route::group(['middleware' => ['web']], function () {
    
	Route::get('configuracao/menu/{id}','HomeController@menu');
	Route::get('configuracao/minify','HomeController@minify');
	Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');

	Route::get('clientes','ClienteController@listar');
	Route::get('cliente/novo','ClienteController@novo');

	Route::get('configuracoes/areas','AreaController@index');
	Route::get('configuracoes/tipos-de-servico','TipoServicoController@index');
	Route::get('configuracoes/tipos-de-despesa','TipoDespesaController@index');

	Route::get('entidade/teste','EntidadeController@index');

	Route::resource('areas','AreaController');
	Route::resource('tipos-de-servico','TipoServicoController');
	Route::resource('tipos-de-despesa','TipoDespesaController');

});