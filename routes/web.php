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

Route::get('/', 'HomeController@index');

Route::get('home', 'ContaController@index');
Route::resource('contas','ContaController');

Auth::routes();

Route::group(['middleware' => ['web']], function () {
    
	Route::get('configuracao/menu/{id}','HomeController@menu');
	Route::get('configuracao/minify','HomeController@minify');
	Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');

	Route::get('cliente/buscar','ClienteController@buscar');
	Route::get('cliente/novo','ClienteController@novo');
	Route::get('cliente/detalhes/{id}','ClienteController@detalhes');
	Route::resource('clientes','ClienteController');

	Route::get('processos/novo','ProcessoController@novo');

	Route::get('configuracoes/areas','AreaController@index');
	Route::get('configuracoes/tipos-de-servico','TipoServicoController@index');
	Route::get('configuracoes/tipos-de-despesa','TipoDespesaController@index');
	Route::get('configuracoes/varas','VaraController@index');
	Route::get('configuracoes/cargos','CargoController@index');
	Route::get('configuracoes/departamentos','DepartamentoController@index');
	Route::get('configuracoes/categorias-de-despesas','CategoriaDespesaController@index');
	Route::get('configuracoes/grupos-de-cidades','GrupoCidadeController@index');
	Route::get('usuarios','UsuarioController@index');
	Route::get('usuarios/buscar','UsuarioController@buscar');
	Route::get('usuarios/novo','UsuarioController@novo');
	Route::get('usuarios/editar/{cdUsuario}','UsuarioController@editar');
	Route::get('configuracoes/novo-grupo-de-cidades','GrupoCidadeController@novo');
	Route::get('configuracoes/editar-grupo-de-cidades/{cdGrupo}','GrupoCidadeController@editar');
	Route::get('cidades-por-estado/{estados}','CidadeController@buscaCidadePorEstado');

	Route::get('conta/detalhes/{id}','ContaController@detalhes');
	Route::get('conta/atualizar/{id}','ContaController@editar');
	Route::post('conta/update','ContaController@update');
	Route::post('conta/telefone/adicionar','ContaController@adicionarTelefone');

	Route::get('entidade/teste','EntidadeController@index');

	Route::get('erro-permissao',function(){ return view('errors/permissao'); });

	Route::resource('areas','AreaController');
	Route::resource('tipos-de-servico','TipoServicoController');
	Route::resource('tipos-de-despesa','TipoDespesaController');
	Route::resource('varas','VaraController');
	Route::resource('cargos','CargoController');
	Route::resource('departamentos','DepartamentoController');
	Route::resource('categorias-de-despesas','CategoriaDespesaController');
	Route::resource('grupos-de-cidades','GrupoCidadeController');
	Route::resource('usuarios','UsuarioController');

});