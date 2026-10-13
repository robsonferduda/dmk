<?php

namespace App\Http\Controllers;

use App\Estado;
use App\Services\Cadastro\CampanhaAtualizacaoService;
use App\Services\Cadastro\FormularioCadastro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Formulário público de atualização cadastral (link exclusivo por correspondente, sem login).
 */
class FormularioCadastroController extends Controller
{
    /** @var FormularioCadastro */
    private $formulario;

    /** @var CampanhaAtualizacaoService */
    private $campanha;

    public function __construct(FormularioCadastro $formulario, CampanhaAtualizacaoService $campanha)
    {
        $this->formulario = $formulario;
        $this->campanha = $campanha;
    }

    public function exibir($chave)
    {
        $vinculo = FormularioCadastro::vinculoDaChave($chave);
        if (! $vinculo) {
            return response()->view('cadastro.link-invalido', [], 404);
        }

        try {
            $this->campanha->registrarAcesso($vinculo);
        } catch (\Throwable $e) {
            Log::warning('[cadastro] Falha ao registrar acesso ao formulário do CCR ' . $vinculo->cd_conta_correspondente_ccr . ': ' . $e->getMessage());
        }

        $dados = $this->formulario->dadosAtuais($vinculo);
        $anterior = old();
        if ($anterior) {
            $dados = array_merge($dados, array_map(function ($v) { return $v ?? ''; }, $anterior));
            $dados['comarcas'] = FormularioCadastro::listaComarcas($anterior['comarcas'] ?? '');
        }

        return view('cadastro.formulario', [
            'chave'    => $chave,
            'dados'    => $dados,
            'comarcas' => $this->formulario->cidades($dados['comarcas']),
            'estados'  => Estado::orderBy('nm_estado_est')->get(['cd_estado_est', 'nm_estado_est', 'sg_estado_est']),
            'bancos'   => DB::table('banco_ban')->whereNull('deleted_at')->orderBy('cd_banco_ban')->get(['cd_banco_ban', 'nm_banco_ban']),
            'ufs'      => FormularioCadastro::UFS,
        ]);
    }

    public function salvar(Request $request, $chave)
    {
        $vinculo = FormularioCadastro::vinculoDaChave($chave);
        if (! $vinculo) {
            return response()->view('cadastro.link-invalido', [], 404);
        }

        $dados = $this->formulario->validar($request->all());
        $this->formulario->salvar($vinculo, $dados);

        try {
            $this->campanha->registrarConfirmacao($vinculo->fresh());
        } catch (\Throwable $e) {
            Log::warning('[cadastro] Falha ao registrar envio do formulário do CCR ' . $vinculo->cd_conta_correspondente_ccr . ': ' . $e->getMessage());
        }

        return redirect('atualizar-dados/' . $chave . '/concluido');
    }

    public function concluido($chave)
    {
        $vinculo = FormularioCadastro::vinculoDaChave($chave);
        if (! $vinculo) {
            return response()->view('cadastro.link-invalido', [], 404);
        }

        return view('cadastro.concluido', ['chave' => $chave, 'nome' => $vinculo->nm_conta_correspondente_ccr]);
    }

    public function cidades($chave, $estado)
    {
        abort_unless(FormularioCadastro::vinculoDaChave($chave), 404);

        return DB::table('cidade_cde')->where('cd_estado_est', (int) $estado)->orderBy('nm_cidade_cde')
            ->get(['cd_cidade_cde as id', 'nm_cidade_cde as nome']);
    }
}
