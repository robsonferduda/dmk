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

Route::get('home', 'HomeController@index');
Route::get('correspondente', function(){ return view('correspondente/cadastro'); });
Route::resource('contas','ContaController');
Route::post('correspondente/cadastro', 'CorrespondenteController@cadastro');

Auth::routes();

Route::group(['middleware' => ['web']], function () {

	Route::get('permissao/teste','PermissaoController@index');

	Route::get('image-crop', 'ImageController@imageCrop');
	Route::post('image-crop', 'ImageController@imageCropPost');
    
	Route::get('configuracao/menu/{id}','HomeController@menu');
	Route::get('configuracao/minify','HomeController@minify');
	Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');

	Route::get('cliente/limpar-selecao/{id}','ClienteController@limparSelecao');
	Route::get('cliente/buscar','ClienteController@buscar');
	Route::get('cliente/buscar-honorarios','ClienteController@buscarHonorarios');
	Route::get('cliente/novo','ClienteController@novo');
	Route::get('cliente/detalhes/{id}','ClienteController@detalhes');
	Route::get('cliente/editar/{id}','ClienteController@editar');
	Route::get('cliente/honorarios/{id}','ClienteController@honorarios');
	Route::post('cliente/honorarios/salvar','ClienteController@salvarHonorarios');
	Route::get('advogados-por-cliente/{cliente}','ClienteController@buscaAdvogados');
	Route::resource('clientes','ClienteController');

	Route::get('autocompleteCliente', 'ClienteController@search');
	Route::get('processos/novo','ProcessoController@novo');
	Route::get('processos/editar/{cdProcesso}','ProcessoController@editar');
	Route::get('processos/detalhes/{id}','ProcessoController@detalhes');
	Route::get('processos/financas/{id}','ProcessoController@financas');

	Route::get('configuracoes/areas','AreaController@index');
	Route::get('configuracoes/tipos-de-servico','TipoServicoController@index');
	Route::get('configuracoes/tipos-de-despesa','TipoDespesaController@index');
	Route::get('configuracoes/varas','VaraController@index');
	Route::get('configuracoes/cargos','CargoController@index');
	Route::get('configuracoes/departamentos','DepartamentoController@index');
	Route::get('configuracoes/categorias-de-despesas','CategoriaDespesaController@index');
	Route::get('configuracoes/grupos-de-cidades','GrupoCidadeController@index');
	Route::get('configuracoes/tipos-de-processo','TipoProcessoController@index');

	Route::resource('correspondentes', 'CorrespondenteController');
	Route::get('correspondente/painel','CorrespondenteController@painel');
	Route::get('correspondente/dados/{id}','CorrespondenteController@dados');
	Route::get('correspondente/detalhes/{id}','CorrespondenteController@detalhes');
	Route::get('correspondente/buscar','CorrespondenteController@buscar');
	Route::get('correspondente/novo','CorrespondenteController@novo')->name('novo-correspondente');
	Route::get('correspondente/todos','CorrespondenteController@buscarTodos');
	Route::get('correspondente/honorarios/{id}','CorrespondenteController@honorarios');
	Route::get('correspondente/buscar-honorarios','CorrespondenteController@buscarHonorarios');
	Route::post('correspondente/adicionar','CorrespondenteController@adicionar');
	Route::post('correspondente/remover','CorrespondenteController@remover');
	Route::post('correspondente/convidar','CorrespondenteController@convidar');

	//Rotas para a ROLE correspondente
	Route::get('correspondente/clientes','CorrespondenteController@clientes');
	Route::get('correspondente/dados','CorrespondenteController@dados');
	Route::get('correspondente/processos','CorrespondenteController@processos');
	Route::get('correspondente/dashboard/{id}','CorrespondenteController@dashboard');
	Route::get('correspondente/perfil/{id}','CorrespondenteController@perfil');
	Route::get('correspondente/ficha/{id}','CorrespondenteController@ficha');
	Route::put('correspondente/editar','CorrespondenteController@editar');

	Route::get('usuarios','UsuarioController@index');
	Route::get('usuarios/buscar','UsuarioController@buscar');
	Route::get('usuarios/novo','UsuarioController@novo');
	Route::get('usuarios/detalhes/{id}','UsuarioController@detalhes');
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

	Route::get('grupo/cidade/{id}','GrupoCidadeController@cidades');

	Route::resource('areas','AreaController');
	Route::resource('tipos-de-servico','TipoServicoController');
	Route::resource('tipos-de-processo','TipoProcessoController');
	Route::resource('tipos-de-despesa','TipoDespesaController');
	Route::resource('varas','VaraController');
	Route::resource('cargos','CargoController');
	Route::resource('departamentos','DepartamentoController');
	Route::resource('categorias-de-despesas','CategoriaDespesaController');
	Route::resource('grupos-de-cidades','GrupoCidadeController');
	Route::resource('processos','ProcessoController');
	Route::resource('usuarios','UsuarioController');

});