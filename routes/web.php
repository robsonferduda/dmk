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
	Route::get('busca-valor-cliente/{cliente}/{cidade}/{tipoServico}','ProcessoController@buscaValorCliente');
	Route::get('busca-valor-correspondente/{correspondente}/{cidade}/{tipoServico}','ProcessoController@buscaValorCorrespondente');
	Route::resource('clientes','ClienteController');

	Route::get('autocompleteCliente','ClienteController@search');
	Route::get('autocompleteCorrespondente', 'CorrespondenteController@search');
	Route::get('processos/novo','ProcessoController@novo');
	Route::get('processos/editar/{cdProcesso}','ProcessoController@editar');
	Route::get('processos/detalhes/{id}','ProcessoController@detalhes');
	Route::get('processos/despesas/{id}','ProcessoController@financas');
	Route::post('processos/despesas/salvar','ProcessoController@salvarDespesas');
	Route::post('processo/honorarios/salvar','ProcessoController@salvarHonorarios');
	Route::get('processos/buscar','ProcessoController@buscar');
	Route::get('processos/clonar/{id}','ProcessoController@clonar');
	Route::get('processos/acompanhar','ProcessoController@acompanhar');
	Route::get('processos/relatorio/{id}','ProcessoController@relatorio');
	
	Route::get('configuracoes/areas','AreaController@index');
	Route::get('configuracoes/tipos-de-servico','TipoServicoController@index');
	Route::get('configuracoes/tipos-de-contato','TipoContatoController@index');
	Route::get('configuracoes/tipos-de-despesa','TipoDespesaController@index');
	Route::get('configuracoes/varas','VaraController@index');
	Route::get('configuracoes/cargos','CargoController@index');
	Route::get('configuracoes/departamentos','DepartamentoController@index');
	Route::get('configuracoes/categorias-de-despesas','CategoriaDespesaController@index');
	Route::get('configuracoes/grupos-de-cidades','GrupoCidadeController@index');
	Route::get('configuracoes/tipos-de-processo','TipoProcessoController@index');
	Route::get('configuracoes/despesas-valores','TipoDespesaController@indexValorReembolsavel');
	Route::put('configuracoes/despesas-valores/salvar','TipoDespesaController@valorReembolsavelSalvar');

	Route::resource('contatos','ContatoController');
	Route::get('contatos','ContatoController@index');
	Route::get('contato/novo','ContatoController@novo');
	Route::get('contato/buscar/{inicial}','ContatoController@buscar');
	Route::get('contato/detalhes/{id}','ContatoController@detalhes');
	Route::get('contato/editar/{id}','ContatoController@editar');
	Route::post('contato/salvar','ContatoController@salvar');

	Route::resource('correspondentes', 'CorrespondenteController');
	Route::get('correspondente/{entidade}/cidades-por-estado/{estado}','CidadeController@buscaCidadePorEstadoCorrespondente');
	Route::get('correspondente/painel','CorrespondenteController@painel');
	Route::get('correspondente/dados/{id}','CorrespondenteController@dados');
	Route::get('correspondente/detalhes/{id}','CorrespondenteController@detalhes');
	Route::get('correspondente/buscar','CorrespondenteController@buscar');
	Route::get('correspondente/novo','CorrespondenteController@novo')->name('novo-correspondente');
	Route::get('correspondente/todos','CorrespondenteController@buscarTodos');
	Route::get('correspondente/honorarios/{id}','CorrespondenteController@honorarios');
	Route::get('correspondente/despesas/{id}','CorrespondenteController@despesas');
	Route::get('correspondente/buscar-honorarios','CorrespondenteController@buscarHonorarios');
	Route::get('correspondente/limpar-selecao/{id}','CorrespondenteController@limparSelecao');
	Route::post('correspondente/honorarios/salvar','CorrespondenteController@salvarHonorarios');
	Route::post('correspondente/adicionar','CorrespondenteController@adicionar');
	Route::post('correspondente/remover','CorrespondenteController@remover');
	Route::post('correspondente/convidar','CorrespondenteController@convidar');
	Route::post('correspondente/despesas','CorrespondenteController@adicionarDespesas');

	//Rotas para a ROLE correspondente
	Route::get('correspondente/clientes','CorrespondenteController@clientes');
	Route::get('correspondente/dados','CorrespondenteController@dados');
	Route::get('correspondente/processos','CorrespondenteController@processos');
	Route::get('correspondente/dashboard/{id}','CorrespondenteController@dashboard');
	Route::get('correspondente/perfil/{id}','CorrespondenteController@perfil');
	Route::get('correspondente/ficha/{id}','CorrespondenteController@ficha');
	Route::get('correspondente/atuacao/{id}','CorrespondenteController@listarAtuacao');
	Route::get('correspondente/atuacao/excluir/{id}','CorrespondenteController@excluirAtuacao');
	Route::post('correspondente/atuacao/adicionar','CorrespondenteController@adicionarAtuacao');
	Route::put('correspondente/editar','CorrespondenteController@editar');

	Route::get('email/entidade/{id}','EnderecoEletronicoController@email');
	Route::get('email/excluir/{id}','EnderecoEletronicoController@excluir');

	Route::get('fones/entidade/{id}','FoneController@fones');
	Route::get('fones/excluir/{id}','FoneController@excluir');

	Route::get('usuarios','UsuarioController@index');
	Route::get('usuarios/buscar','UsuarioController@buscar');
	Route::get('usuarios/novo','UsuarioController@novo');
	Route::get('usuarios/detalhes/{id}','UsuarioController@detalhes');
	Route::get('usuarios/editar/{cdUsuario}','UsuarioController@editar');
	Route::put('usuarios/alterar-senha/{id}','UsuarioController@alterarSenha');
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
	Route::resource('tipos-de-contato','TipoContatoController');
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

	//Rotas de permissão
	Route::get('roles','RoleController@index');
	Route::get('role/usuario/{id}','RoleController@roleUser');
	Route::get('role/novo','RoleController@novo');
	Route::get('permissoes','PermissaoController@index');
	Route::get('users','PermissaoController@users');
	Route::get('role/{role}/usuario/delete/{user}','RoleController@deleteRoleUser');
	Route::post('role/usuario/adicionar','RoleController@adicionarRole');

	Route::post('varas/importar','VaraController@importar');

	Route::get('layouts/varas-importar', function(){

		 return response()->download( public_path().'/resources/layouts/varas_importar.xlsx');
	});

});