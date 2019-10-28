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
Route::get('seleciona/perfil/{perfil}', 'Auth\LoginController@selecionaPerfil')->name('seleciona.perfil');
Route::get('msg-filiacao', function(){ return view('errors/msg_filiacao'); })->name('msg-filiacao');
Route::get('correspondente', function(){ return view('correspondente/cadastro'); })->name('correspondente');
Route::get('correspondente/login', function(){ return view('auth/correspondente'); })->name('autenticacao.correspondente');;
Route::post('autenticacao', 'Auth\LoginController@loginCorrespondente')->name('autenticacao');
Route::post('correspondente/cadastro', 'CorrespondenteController@cadastro');
Route::resource('contas','ContaController');

Auth::routes();

Route::group(['middleware' => ['web']], function () {

	Route::get('seleciona/perfil', 'UsuarioController@selecionaPerfil');
	Route::post('selecionar-nivel', 'UsuarioController@validarSelecao')->name('selecionar-nivel');
	Route::post('login-perfil', 'UsuarioController@loginPerfil')->name('login-perfil');

	Route::get('image-crop', 'ImageController@imageCrop');
	Route::post('image-crop', 'ImageController@imageCropPost');
    
	Route::get('configuracao/menu/{id}','HomeController@menu');
	Route::get('configuracao/minify','HomeController@minify');
	Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');

	Route::get('cliente/limpar-selecao/{id}','ClienteController@limparSelecao');
	Route::get('cliente/buscar','ClienteController@buscar');
	Route::get('cliente/buscar-honorarios/{id}','ClienteController@buscarHonorarios');
	Route::get('cliente/honorarios/buscar/{id}','ClienteController@buscarHonorariosSalvos');
	Route::get('cliente/novo','ClienteController@novo');
	Route::get('cliente/detalhes/{id}','ClienteController@detalhes');
	Route::get('cliente/editar/{id}','ClienteController@editar');
	Route::get('cliente/contatos/{id}','ClienteController@contatos');
	Route::get('cliente/honorarios/organizar/{ordem}','ClienteController@organizar');
	Route::get('cliente/honorarios/adicionar/{id}','ClienteController@adicionarHonorario');
	Route::get('cliente/{id}/contato/novo','ClienteController@novoContato');
	Route::get('cliente/{id}/contato/buscar/{inicial}','ClienteController@buscarContato');
	Route::get('cliente/honorarios/{id}','ClienteController@honorarios');
	Route::get('advogados-por-cliente/{cliente}','ClienteController@buscaAdvogados');
	Route::get('busca-valor-cliente/{cliente}/{cidade}/{tipoServico}','ProcessoController@buscaValorCliente');
	Route::get('busca-valor-correspondente/{correspondente}/{cidade}/{tipoServico}','ProcessoController@buscaValorCorrespondente');
	Route::post('cliente/advogado','ClienteController@novoAdvogado');
	Route::post('cliente/contato/novo/{id}','ClienteController@createContato');
	Route::post('cliente/honorarios/salvar','ClienteController@salvarHonorarios');
	Route::delete('cliente/honorarios/{entidade}/{tipo}/excluir/{id}','ClienteController@excluirHonorarios');
	Route::resource('clientes','ClienteController');


	Route::get('autocompleteConta','CorrespondenteController@searchConta');
	Route::get('autocompleteCliente','ClienteController@search');
	Route::get('autocompleteCorrespondente', 'CorrespondenteController@search');
	Route::get('autocompleteResponsavel', 'UsuarioController@search');
	Route::get('processos/novo','ProcessoController@novo');
	Route::get('processos/editar/{cdProcesso}','ProcessoController@editar');
	Route::get('processos/detalhes/{id}','ProcessoController@detalhes')->name('processos.detalhes');
	Route::get('processos/despesas/{id}','ProcessoController@financas');
	Route::get('processos/acompanhamento','ProcessoController@acompanhar');
	Route::get('processos/acompanhamento/{id}','ProcessoController@acompanhamento')->name('processo.acompanhar');
	Route::post('processos/despesas/salvar','ProcessoController@salvarDespesas');
	Route::post('processo/honorarios/salvar','ProcessoController@salvarHonorarios');
	Route::post('processo/atualizar-status','ProcessoController@atualizarStatus');
	Route::post('processo/finalizar-processo','ProcessoController@finalizarProcesso');
	Route::get('processos/buscar','ProcessoController@buscar');
	Route::get('processos/clonar/{id}','ProcessoController@clonar');
	Route::get('processos/notificar/{id}','ProcessoController@notificarCorrespondente');
	Route::get('processos/acompanhar','ProcessoController@acompanhar');
	Route::get('processos/relatorio/{id}','ProcessoController@relatorio');
	Route::get('processos/atualiza/enviados/{id}','ProcessoController@atualizaAnexosEnviados');
	Route::get('processos/atualiza/recebidos/{id}','ProcessoController@atualizaAnexosRecebidos');
	Route::get('processo/notificacao/resposta/{resposta}/{id}','ProcessoController@responderNotificacao')->name('resposta');
	Route::get('processos/relatorios','RelatorioProcessoController@relatorios');
	Route::post('processo/relatorios/buscar', 'RelatorioProcessoController@buscar');
	Route::post('processo/pauta-diaria', 'RelatorioProcessoController@pautaDiaria');	
	Route::get('tipos-de-servico/cliente/{cliente}/cidade/{cidade}','TipoServicoController@consultarClienteCidade');
	Route::get('tipos-de-servico/correspondente/{correspondente}/cidade/{cidade}','TipoServicoController@consultarCorrespondenteCidade');

	Route::delete('processo/mensagem/excluir/{id}','MensagemController@excluir');
	Route::post('processo/mensagem/enviar','MensagemController@enviar');
	
	Route::get('configuracoes/areas','AreaController@index');
	Route::get('configuracoes/notificacoes','NotificacaoController@preferencias');
	Route::get('configuracoes/tipos-de-servico','TipoServicoController@index');
	Route::get('configuracoes/tipos-de-servico/consulta/{id}','TipoServicoController@consultar');
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
	Route::put('configuracoes/notificacoes/salvar','NotificacaoController@salvarPreferencias');

	Route::resource('contatos','ContatoController');
	Route::get('contatos','ContatoController@index');
	Route::get('contato/novo','ContatoController@novo');
	Route::get('contato/buscar/{inicial}','ContatoController@buscar');
	Route::post('contato/buscar','ContatoController@filtrar');
	Route::get('contato/detalhes/{id}','ContatoController@detalhes');
	Route::get('contato/editar/{id}','ContatoController@editar');
	Route::post('contato/salvar','ContatoController@salvar');

	Route::resource('correspondentes', 'CorrespondenteController');
	Route::get('correspondente/categorias','CategoriaCorrespondenteController@index');
	Route::get('correspondente/{entidade}/cidades-por-estado/{estado}','CidadeController@buscaCidadePorEstadoCorrespondente');
	Route::get('correspondente/painel','CorrespondenteController@painel');
	Route::get('correspondente/dados/{id}','CorrespondenteController@dados');
	Route::get('correspondente/detalhes/{id}','CorrespondenteController@detalhes');
	Route::get('correspondente/buscar','CorrespondenteController@buscar');
	Route::get('correspondente/todos',function(){ return view('correspondente/todos'); });
	Route::get('correspondente/todos/buscar','CorrespondenteController@buscarTodos');
	Route::get('correspondente/novo','CorrespondenteController@novo')->name('novo-correspondente');
	Route::get('correspondente/honorarios/{id}','CorrespondenteController@honorarios');
	Route::get('correspondente/notificacao/{id}','CorrespondenteController@notificacao');
	Route::get('correspondente/honorarios/organizar/{ordem}','CorrespondenteController@ordenarHonorarios');
	Route::get('correspondente/despesas/{id}','CorrespondenteController@despesas');
	Route::get('correspondente/buscar-honorarios/{id}','CorrespondenteController@buscarHonorarios');
	Route::get('correspondente/limpar-selecao/{id}','CorrespondenteController@limparSelecao');
	Route::get('correspondente/convite/{token}','CorrespondenteController@aceitarConvite')->name("correspondente.convite");
	Route::get('correspondente/filiacao/{token}','CorrespondenteController@aceitarFiliacao')->name("correspondente.filiacao");
	Route::get('correspondente/acompanhamento/{id}','CorrespondenteController@acompanhamento')->name('processo.correspondente');;
	Route::post('correspondente/honorarios/salvar','CorrespondenteController@salvarHonorarios');
	Route::post('correspondente/adicionar','CorrespondenteController@adicionar');
	Route::get('correspondente/processo/buscar','CorrespondenteController@buscarProcesso');
	Route::post('correspondente/remover','CorrespondenteController@remover');
	Route::post('correspondente/convidar','CorrespondenteController@convidar');
	Route::post('correspondente/despesas','CorrespondenteController@adicionarDespesas');
	Route::post('correspondente/cadastro/conta','CorrespondenteController@novoCorrespondenteConta');
	Route::post('correspondente/honorarios/remover','CorrespondenteController@excluirTodosHonorarios');

	//Rotas para a ROLE correspondente
	Route::get('correspondente/clientes','CorrespondenteController@clientes');
	Route::get('correspondente/cliente/{cliente}/dados','CorrespondenteController@dadosCliente');
	Route::get('correspondente/cliente/{cliente}/processos','CorrespondenteController@processosCliente');
	Route::get('correspondente/dados','CorrespondenteController@dados');
	Route::get('correspondente/processos','CorrespondenteController@processos');
	Route::get('correspondente/dashboard/{id}','CorrespondenteController@dashboard');
	Route::get('correspondente/perfil/{id}','CorrespondenteController@perfil');
	Route::get('correspondente/ficha/{id}','CorrespondenteController@ficha');
	Route::get('correspondente/atuacao/{id}','CorrespondenteController@listarAtuacao');
	Route::get('correspondente/origem/{id}','CorrespondenteController@listarOrigem');
	Route::get('correspondente/atuacao/excluir/{id}','CorrespondenteController@excluirAtuacao');
	Route::post('correspondente/atuacao/adicionar','CorrespondenteController@adicionarAtuacao');
	Route::put('correspondente/editar','CorrespondenteController@editar');

	Route::get('despesas/balanco','DespesasController@balanco');
	Route::get('despesas/novo','DespesasController@novo');
	Route::get('despesa/editar/{id}','DespesasController@editar');
	Route::get('despesas/lancamentos','DespesasController@lancamentos')->name('lancamentos');
	Route::get('despesas/categoria/tipo/{id}','DespesasController@getTipos');
	Route::post('despesas/categorias/tipo','DespesasController@getTiposMultiple');
	Route::get('despesas/tipo/categoria/{id}','DespesasController@getCategorias');
	Route::get('despesas/anexos/{arquivo}','DespesasController@download');
	Route::post('despesas/buscar','DespesasController@buscar');
	Route::resource('despesas','DespesasController');

	Route::get('email/entidade/{id}','EnderecoEletronicoController@email');
	Route::get('email/excluir/{id}','EnderecoEletronicoController@excluir');

	Route::resource('files','FileUploadController');
	Route::post('file-upload', 'FileUploadController@upload')->name('file-upload');

	Route::get('fones/entidade/{id}','FoneController@fones');
	Route::get('fones/excluir/{id}','FoneController@excluir');
	Route::post('fones/editar','FoneController@editar');

	Route::get('registro-bancario/entidade/{id}','RegistroBancarioController@registros');
	Route::get('registro-bancario/id/{id}','RegistroBancarioController@registro');
	Route::get('registro-bancario/excluir/{id}','RegistroBancarioController@excluir');

	Route::get('usuarios','UsuarioController@index');
	Route::get('usuarios/buscar','UsuarioController@buscar');
	Route::get('usuarios/novo','UsuarioController@novo');
	Route::get('usuarios/detalhes/{id}','UsuarioController@detalhes');
	Route::get('usuarios/editar/{cdUsuario}','UsuarioController@editar');
	Route::put('usuarios/alterar-senha/{id}','UsuarioController@alterarSenha');
	Route::get('configuracoes/novo-grupo-de-cidades','GrupoCidadeController@novo');
	Route::get('configuracoes/editar-grupo-de-cidades/{cdGrupo}','GrupoCidadeController@editar');
	Route::get('cidades-por-estado/{estados}','CidadeController@buscaCidadePorEstado');

	Route::resource('contas','ContaController');
	Route::get('conta/detalhes/{id}','ContaController@detalhes');
	Route::get('conta/atualizar/{id}','ContaController@editar');
	Route::post('conta/update','ContaController@update');
	Route::post('conta/telefone/adicionar','ContaController@adicionarTelefone');
	Route::post('conta/configuracoes/flag_envio','ContaController@atualizarFlagEnvio');

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
	Route::resource('categorias-correspondentes','CategoriaCorrespondenteController');
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

	Route::get('permissoes/usuario/{id}','PermissaoController@permissaoUsuario');
	Route::get('permissoes/adicionar','PermissaoController@adicionar');
	Route::get('permissoes/role/adicionar','PermissaoController@atribuirRole');
	Route::get('permissoes/todas/adicionar','PermissaoController@atribuirPermissao');


	Route::post('varas/importar','VaraController@importar');

	Route::get('layouts/varas-importar', function(){

		 return response()->download( public_path().'/resources/layouts/varas_importar.xlsx');
	});
	

	Route::delete('correspondente/painel/reports/{nome}','RelatorioPainelCorrespondenteController@excluir');
	Route::get('correspondente/painel/reports/{nome}','RelatorioPainelCorrespondenteController@arquivo');
	Route::post('correspondente/painel/relatorios/buscar', 'RelatorioPainelCorrespondenteController@buscar');
	Route::get('correspondente/painel/relatorios', 'RelatorioPainelCorrespondenteController@index');
	Route::get('correspondente/relatorios', 'RelatorioCorrespondenteController@relatorios');
	Route::post('correspondente/relatorios/buscar', 'RelatorioCorrespondenteController@buscar');
	Route::delete('correspondente/reports/{nome}','RelatorioCorrespondenteController@excluir');
	Route::get('correspondente/arquivo/{nome}','RelatorioCorrespondenteController@arquivo');

	Route::get('calendario','CalendarioController@index');

	Route::post('calendario/eventos-por-data','CalendarioController@buscarEventosPorData');	
	Route::post('calendario/evento/adicionar','CalendarioController@adicionar');	
	Route::post('calendario/evento/editar','CalendarioController@editar');	
	Route::post('calendario/evento/excluir','CalendarioController@excluir');	
	Route::get('calendario/evento/gerar-link','CalendarioController@gerarLink');
	Route::get('calendario/evento/gerar-evento-processos','CalendarioController@gerarEventoProcessos');

	Route::get('financeiro/entradas','FinanceiroController@entradaIndex');
	Route::get('financeiro/saidas','FinanceiroController@saidaIndex');
	Route::post('financeiro/entrada/buscar','FinanceiroController@entradaBuscar');
	Route::post('financeiro/saida/buscar','FinanceiroController@saidaBuscar');
	Route::post('financeiro/cliente/baixa','FinanceiroController@baixaCliente');
	Route::post('financeiro/correspondente/baixa','FinanceiroController@baixaCorrespondente');
	Route::get('financeiro/balanco','FinanceiroController@balancoIndex');
	Route::post('financeiro/balanco/buscar','FinanceiroController@balancoBuscar');
	Route::get('financeiro/relatorio/balanco/detalhado','FinanceiroController@relatorioBalancoDetalhado');
	Route::get('financeiro/relatorio/balanco/sumarizado','FinanceiroController@relatorioBalancoSumarizado');
	Route::get('financeiro/relatorio/balanco/buscar','FinanceiroController@relatorioBuscar');
	Route::get('financeiro/relatorios','FinanceiroController@relatorios');
	
	

	Route::get('processos/arquivo/{nome}','RelatorioProcessoController@arquivo');
	Route::delete('processos/reports/{nome}','RelatorioProcessoController@excluir');

});