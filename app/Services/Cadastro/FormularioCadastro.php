<?php

namespace App\Services\Cadastro;

use App\Cidade;
use App\CidadeAtuacao;
use App\Conta;
use App\ContaCorrespondente;
use App\Endereco;
use App\EnderecoEletronico;
use App\Enums\TipoConta;
use App\Enums\TipoEnderecoEletronico;
use App\Enums\TipoFone;
use App\Enums\TipoIdentificacao;
use App\Fone;
use App\Identificacao;
use App\RegistroBancario;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Formulário público de atualização cadastral do correspondente (sem login).
 * O endereço é fixo por vínculo e assinado com a APP_KEY, para não dar para chegar no cadastro de outro trocando o número.
 * Grava nos mesmos registros que o contrato lê (entidade do vínculo).
 */
class FormularioCadastro
{
    const PESSOA_FISICA = 1;
    const PESSOA_JURIDICA = 2;

    const UFS = ['AC', 'AL', 'AM', 'AP', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MG', 'MS', 'MT', 'PA', 'PB', 'PE', 'PI', 'PR', 'RJ', 'RN', 'RO', 'RR', 'RS', 'SC', 'SE', 'SP', 'TO'];

    public static function chave(int $cdVinculo): string
    {
        return $cdVinculo . '-' . self::assinatura($cdVinculo);
    }

    public static function url(int $cdVinculo): string
    {
        return rtrim(config('cadastro.url_publica'), '/') . '/atualizar-dados/' . self::chave($cdVinculo);
    }

    public static function vinculoDaChave(string $chave): ?ContaCorrespondente
    {
        if (! preg_match('/^(\d+)-([a-f0-9]{20})$/', $chave, $m) || ! hash_equals(self::assinatura((int) $m[1]), $m[2])) {
            return null;
        }

        $vinculo = ContaCorrespondente::with('correspondente')->find((int) $m[1]);

        return $vinculo && $vinculo->cd_entidade_ete ? $vinculo : null;
    }

    private static function assinatura(int $cdVinculo): string
    {
        return substr(hash_hmac('sha256', 'atualizacao-cadastral:' . $cdVinculo, (string) config('app.key')), 0, 20);
    }

    /**
     * Valores atuais do cadastro, no formato dos campos do formulário.
     */
    public function dadosAtuais(ContaCorrespondente $vinculo): array
    {
        $ent = $vinculo->cd_entidade_ete;
        $conta = $vinculo->correspondente;

        $documento = Identificacao::where('cd_entidade_ete', $ent)
            ->whereIn('cd_tipo_identificacao_tpi', [TipoIdentificacao::CPF, TipoIdentificacao::CNPJ])
            ->where('nu_identificacao_ide', '<>', '')
            ->orderBy('cd_identificacao_ide')
            ->value('nu_identificacao_ide');
        $rg = Identificacao::where('cd_entidade_ete', $ent)->where('cd_tipo_identificacao_tpi', TipoIdentificacao::RG)
            ->orderBy('cd_identificacao_ide')->value('nu_identificacao_ide');
        $oab = Identificacao::where('cd_entidade_ete', $ent)->where('cd_tipo_identificacao_tpi', TipoIdentificacao::OAB)
            ->orderBy('cd_identificacao_ide')->value('nu_identificacao_ide');

        $endereco = Endereco::with('cidade')->where('cd_entidade_ete', $ent)->orderBy('cd_endereco_ede')->first();
        [$oabUf, $oabNumero] = self::separarOab($oab);

        $bancaria = RegistroBancario::where('cd_entidade_ete', $ent)->where('cd_tipo_conta_tcb', '<>', TipoConta::PIX)
            ->orderBy('cd_dados_bancarios_dba')->first();
        $pix = RegistroBancario::where('cd_entidade_ete', $ent)->where('cd_tipo_conta_tcb', TipoConta::PIX)
            ->orderBy('cd_dados_bancarios_dba')->first();

        $origem = CidadeAtuacao::where('cd_entidade_ete', $ent)->where('fl_origem_cat', 'S')->value('cd_cidade_cde');

        return [
            'nome'               => trim((string) ($vinculo->nm_conta_correspondente_ccr ?: optional($conta)->nm_razao_social_con)),
            'documento'          => (string) $documento,
            'rg'                 => (string) $rg,
            'atuacao'            => optional($conta)->fl_advogado_con === false ? 'preposto' : 'advogado',
            'oab_uf'             => (string) $oabUf,
            'oab_numero'         => (string) $oabNumero,
            'cep'                => $endereco && $endereco->nu_cep_ede ? self::mascararCep($endereco->nu_cep_ede) : '',
            'logradouro'         => (string) optional($endereco)->dc_logradouro_ede,
            'numero'             => (string) optional($endereco)->nu_numero_ede,
            'complemento'        => (string) optional($endereco)->dc_complemento_ede,
            'bairro'             => (string) optional($endereco)->nm_bairro_ede,
            'estado'             => (string) optional(optional($endereco)->cidade)->cd_estado_est,
            'cidade'             => (string) optional($endereco)->cd_cidade_cde,
            'telefone'           => (string) Fone::where('cd_entidade_ete', $ent)->orderBy('cd_fone_fon')->value('nu_fone_fon'),
            'whatsapp'           => (string) optional($conta)->nu_telefone_whatsapp_con,
            'email'              => (string) optional($this->emailPrincipal($ent))->dc_endereco_eletronico_ede,
            'titular'            => (string) (optional($bancaria)->nm_titular_dba ?: optional($pix)->nm_titular_dba),
            'titular_documento'  => (string) (optional($bancaria)->nu_cpf_cnpj_dba ?: optional($pix)->nu_cpf_cnpj_dba),
            'banco'              => (string) optional($bancaria)->cd_banco_ban,
            'tipo_conta'         => (string) (optional($bancaria)->cd_tipo_conta_tcb ?: TipoConta::CORRENTE),
            'agencia'            => (string) optional($bancaria)->nu_agencia_dba,
            'conta'              => (string) optional($bancaria)->nu_conta_dba,
            'pix'                => (string) (optional($pix)->dc_pix_dba ?: optional($bancaria)->dc_pix_dba),
            'origem_estado'      => $origem ? (string) Cidade::where('cd_cidade_cde', $origem)->value('cd_estado_est') : '',
            'origem'             => (string) $origem,
            'comarcas'           => CidadeAtuacao::where('cd_entidade_ete', $ent)->where('fl_origem_cat', '<>', 'S')
                                        ->pluck('cd_cidade_cde')->map(function ($id) { return (int) $id; })->all(),
        ];
    }

    /**
     * Comarcas atuais com nome e UF, para montar a lista na tela.
     *
     * @param int[] $ids
     */
    public function cidades(array $ids): array
    {
        if (! $ids) {
            return [];
        }

        return DB::table('cidade_cde as c')
            ->join('estado_est as e', 'e.cd_estado_est', '=', 'c.cd_estado_est')
            ->whereIn('c.cd_cidade_cde', $ids)
            ->orderBy('e.sg_estado_est')->orderBy('c.nm_cidade_cde')
            ->get(['c.cd_cidade_cde as id', 'c.nm_cidade_cde as nome', 'e.sg_estado_est as uf', 'e.cd_estado_est as estado'])
            ->map(function ($c) { return (array) $c; })
            ->all();
    }

    /**
     * Valida e normaliza a entrada do formulário.
     *
     * @throws ValidationException
     */
    public function validar(array $entrada): array
    {
        $digitos = function ($valor) { return preg_replace('/\D+/', '', (string) $valor); };
        foreach (['telefone', 'whatsapp'] as $campo) {
            $numero = $digitos($entrada[$campo] ?? '');
            if (in_array(strlen($numero), [12, 13], true) && strpos($numero, '55') === 0) {
                $entrada[$campo] = substr($numero, 2);
            }
        }
        $entrada['comarcas'] = self::listaComarcas($entrada['comarcas'] ?? []);
        $documento = $digitos($entrada['documento'] ?? '');
        $pessoaJuridica = strlen($documento) === 14;

        $validador = Validator::make($entrada, [
            'nome'              => 'required|string|max:150',
            'documento'         => 'required',
            'rg'                => ($pessoaJuridica ? 'nullable' : 'required') . '|string|max:30',
            'atuacao'           => 'required|in:advogado,preposto',
            'oab_uf'            => 'required_if:atuacao,advogado|required_with:oab_numero|nullable|in:' . implode(',', self::UFS),
            'oab_numero'        => 'required_if:atuacao,advogado|required_with:oab_uf|nullable|string|max:20',
            'cep'               => 'required',
            'logradouro'        => 'required|string|max:200',
            'numero'            => 'required|string|max:20',
            'complemento'       => 'nullable|string|max:100',
            'bairro'            => 'required|string|max:100',
            'cidade'            => 'required|integer|exists:cidade_cde,cd_cidade_cde',
            'telefone'          => 'required',
            'whatsapp'          => 'nullable',
            'email'             => 'required|email|max:150',
            'titular'           => 'required|string|max:150',
            'titular_documento' => 'required',
            'banco'             => 'required|exists:banco_ban,cd_banco_ban',
            'tipo_conta'        => 'required|in:' . TipoConta::CORRENTE . ',' . TipoConta::POUPANCA,
            'agencia'           => 'required|string|max:20',
            'conta'             => 'required|string|max:30',
            'pix'               => 'required|string|max:150',
            'origem'            => 'required|integer|exists:cidade_cde,cd_cidade_cde',
            'comarcas'          => 'array|max:6000',
            'confirmacao'       => 'accepted',
        ], [
            'required'            => 'Informe :attribute.',
            'required_if'         => 'Informe :attribute.',
            'required_with'       => 'Informe :attribute.',
            'email'               => 'Informe um e-mail válido.',
            'max'                 => ':Attribute está muito longo.',
            'exists'              => 'Selecione :attribute na lista.',
            'in'                  => 'Selecione :attribute na lista.',
            'integer'             => 'Selecione :attribute na lista.',
            'accepted'            => 'Confirme que os dados estão corretos.',
        ], [
            'nome' => 'o nome completo', 'documento' => 'o CPF ou CNPJ', 'rg' => 'o RG', 'atuacao' => 'como você atua',
            'oab_uf' => 'a seccional da OAB', 'oab_numero' => 'o número da OAB', 'cep' => 'o CEP', 'logradouro' => 'o endereço',
            'numero' => 'o número do endereço', 'complemento' => 'o complemento', 'bairro' => 'o bairro', 'cidade' => 'a cidade',
            'telefone' => 'o telefone', 'whatsapp' => 'o WhatsApp', 'email' => 'o e-mail', 'titular' => 'o titular da conta',
            'titular_documento' => 'o CPF ou CNPJ do titular', 'banco' => 'o banco', 'tipo_conta' => 'o tipo de conta',
            'agencia' => 'a agência', 'conta' => 'a conta', 'pix' => 'a chave PIX', 'origem' => 'a comarca de origem',
            'comarcas' => 'as comarcas',
        ]);

        $validador->after(function ($v) use ($entrada, $digitos, $documento) {
            if ($documento !== '' && ! self::documentoValido($documento)) {
                $v->errors()->add('documento', 'CPF ou CNPJ inválido.');
            }
            $titular = $digitos($entrada['titular_documento'] ?? '');
            if ($titular !== '' && ! self::documentoValido($titular)) {
                $v->errors()->add('titular_documento', 'CPF ou CNPJ do titular inválido.');
            }
            if (($entrada['cep'] ?? '') !== '' && strlen($digitos($entrada['cep'])) !== 8) {
                $v->errors()->add('cep', 'O CEP deve ter 8 dígitos.');
            }
            foreach (['telefone' => 'telefone', 'whatsapp' => 'WhatsApp'] as $campo => $rotulo) {
                $numero = $digitos($entrada[$campo] ?? '');
                if ($numero !== '' && ! in_array(strlen($numero), [10, 11], true)) {
                    $v->errors()->add($campo, "Informe o {$rotulo} com DDD (10 ou 11 dígitos).");
                }
            }
            if ($entrada['comarcas'] && DB::table('cidade_cde')->whereIn('cd_cidade_cde', $entrada['comarcas'])->count() !== count($entrada['comarcas'])) {
                $v->errors()->add('comarcas', 'Uma das comarcas não foi encontrada. Remova e adicione de novo.');
            }
        });

        $validador->validate();

        $oabNumero = trim((string) ($entrada['oab_numero'] ?? ''));

        return [
            'nome'              => mb_strtoupper(trim($entrada['nome'])),
            'tipo_pessoa'       => $pessoaJuridica ? self::PESSOA_JURIDICA : self::PESSOA_FISICA,
            'documento'         => self::mascararDocumento($documento),
            'rg'                => trim((string) ($entrada['rg'] ?? '')),
            'advogado'          => $entrada['atuacao'] === 'advogado',
            'oab'               => $oabNumero !== '' ? 'OAB/' . $entrada['oab_uf'] . ' ' . $oabNumero : null,
            'cep'               => $digitos($entrada['cep']),
            'logradouro'        => trim($entrada['logradouro']),
            'numero'            => trim($entrada['numero']),
            'complemento'       => trim((string) ($entrada['complemento'] ?? '')) ?: null,
            'bairro'            => trim($entrada['bairro']),
            'cidade'            => (int) $entrada['cidade'],
            'telefone'          => self::mascararTelefone($digitos($entrada['telefone'])),
            'telefone_celular'  => strlen($digitos($entrada['telefone'])) === 11,
            'whatsapp'          => $digitos($entrada['whatsapp'] ?? '') ?: null,
            'email'             => mb_strtolower(trim($entrada['email'])),
            'titular'           => mb_strtoupper(trim($entrada['titular'])),
            'titular_documento' => $digitos($entrada['titular_documento']),
            'banco'             => (string) $entrada['banco'],
            'tipo_conta'        => (int) $entrada['tipo_conta'],
            'agencia'           => trim($entrada['agencia']),
            'conta'             => trim($entrada['conta']),
            'pix'               => trim($entrada['pix']),
            'origem'            => (int) $entrada['origem'],
            'comarcas'          => $entrada['comarcas'],
        ];
    }

    /**
     * Grava o cadastro. Onde havia registros duplicados (endereço, CPF, RG, OAB), fica só o informado agora,
     * porque o contrato lê um registro só e não daria para saber qual.
     */
    public function salvar(ContaCorrespondente $vinculo, array $d): void
    {
        DB::transaction(function () use ($vinculo, $d) {
            $ent = $vinculo->cd_entidade_ete;
            $cdConta = $vinculo->cd_correspondente_cor;

            $vinculo->nm_conta_correspondente_ccr = $d['nome'];
            $vinculo->cd_tipo_pessoa_tpp = $d['tipo_pessoa'];
            $vinculo->fl_atualizacao_cadastro_ccr = true;
            $vinculo->save();

            $conta = Conta::find($cdConta);
            if ($conta) {
                $conta->nm_razao_social_con = $d['nome'];
                $conta->cd_tipo_pessoa_tpp = $d['tipo_pessoa'];
                $conta->fl_advogado_con = $d['advogado'];
                if ($d['whatsapp']) {
                    $conta->nu_telefone_whatsapp_con = $d['whatsapp'];
                }
                $conta->save();
            }

            $tipoDocumento = $d['tipo_pessoa'] === self::PESSOA_JURIDICA ? TipoIdentificacao::CNPJ : TipoIdentificacao::CPF;
            $this->identificacao($ent, $cdConta, [TipoIdentificacao::CPF, TipoIdentificacao::CNPJ], $tipoDocumento, $d['documento']);
            if ($d['rg'] !== '') {
                $this->identificacao($ent, $cdConta, [TipoIdentificacao::RG], TipoIdentificacao::RG, $d['rg']);
            }
            if ($d['oab']) {
                $this->identificacao($ent, $cdConta, [TipoIdentificacao::OAB], TipoIdentificacao::OAB, $d['oab']);
            } else {
                Identificacao::where('cd_entidade_ete', $ent)->where('cd_tipo_identificacao_tpi', TipoIdentificacao::OAB)->get()
                    ->each(function ($oab) { $oab->delete(); });
            }

            $this->unicoRegistro(Endereco::where('cd_entidade_ete', $ent)->orderBy('cd_endereco_ede')->get(), Endereco::class, [
                'cd_entidade_ete'    => $ent,
                'cd_conta_con'       => $cdConta,
                'nu_cep_ede'         => $d['cep'],
                'dc_logradouro_ede'  => $d['logradouro'],
                'nu_numero_ede'      => $d['numero'],
                'dc_complemento_ede' => $d['complemento'],
                'nm_bairro_ede'      => $d['bairro'],
                'cd_cidade_cde'      => $d['cidade'],
            ]);

            $fone = Fone::where('cd_entidade_ete', $ent)->orderBy('cd_fone_fon')->first() ?: new Fone(['cd_entidade_ete' => $ent, 'cd_conta_con' => $cdConta]);
            $fone->nu_fone_fon = $d['telefone'];
            $fone->cd_tipo_fone_tfo = $d['telefone_celular'] ? TipoFone::CELULAR : TipoFone::FIXO;
            $fone->save();

            $email = $this->emailPrincipal($ent) ?: new EnderecoEletronico([
                'cd_entidade_ete'                 => $ent,
                'cd_conta_con'                    => $cdConta,
                'cd_tipo_endereco_eletronico_tee' => TipoEnderecoEletronico::NOTIFICACAO,
            ]);
            $email->dc_endereco_eletronico_ede = $d['email'];
            $email->save();

            $bancaria = RegistroBancario::where('cd_entidade_ete', $ent)->where('cd_tipo_conta_tcb', '<>', TipoConta::PIX)
                ->orderBy('cd_dados_bancarios_dba')->first() ?: new RegistroBancario(['cd_entidade_ete' => $ent, 'cd_conta_con' => $cdConta]);
            $bancaria->fill([
                'nm_titular_dba'    => $d['titular'],
                'nu_cpf_cnpj_dba'   => $d['titular_documento'],
                'cd_banco_ban'      => $d['banco'],
                'cd_tipo_conta_tcb' => $d['tipo_conta'],
                'nu_agencia_dba'    => $d['agencia'],
                'nu_conta_dba'      => $d['conta'],
                'dc_pix_dba'        => $d['pix'],
            ])->save();

            $pix = RegistroBancario::where('cd_entidade_ete', $ent)->where('cd_tipo_conta_tcb', TipoConta::PIX)
                ->orderBy('cd_dados_bancarios_dba')->first() ?: new RegistroBancario(['cd_entidade_ete' => $ent, 'cd_conta_con' => $cdConta, 'cd_tipo_conta_tcb' => TipoConta::PIX]);
            $pix->fill(['nm_titular_dba' => $d['titular'], 'nu_cpf_cnpj_dba' => $d['titular_documento'], 'dc_pix_dba' => $d['pix']])->save();

            $this->comarcas($ent, $d['origem'], $d['comarcas']);
        });
    }

    private function identificacao(int $ent, int $cdConta, array $tiposExistentes, int $tipo, string $valor): void
    {
        $registros = Identificacao::where('cd_entidade_ete', $ent)->whereIn('cd_tipo_identificacao_tpi', $tiposExistentes)
            ->orderBy('cd_identificacao_ide')->get();

        $this->unicoRegistro($registros, Identificacao::class, [
            'cd_entidade_ete'           => $ent,
            'cd_conta_con'              => $cdConta,
            'cd_tipo_identificacao_tpi' => $tipo,
            'nu_identificacao_ide'      => $valor,
        ]);
    }

    /**
     * Atualiza o primeiro registro (ou cria) e apaga (soft delete) os demais.
     */
    private function unicoRegistro($registros, string $classe, array $valores): void
    {
        $principal = $registros->shift() ?: new $classe();
        $principal->fill($valores)->save();

        foreach ($registros as $sobra) {
            $sobra->delete();
        }
    }

    /**
     * Deixa as comarcas da entidade iguais às informadas: origem marcada com 'S', as demais com 'N'.
     */
    private function comarcas(int $ent, int $origem, array $demais): void
    {
        $desejadas = array_values(array_unique(array_merge([$origem], $demais)));

        CidadeAtuacao::where('cd_entidade_ete', $ent)->whereNotIn('cd_cidade_cde', $desejadas)->delete();

        $existentes = CidadeAtuacao::where('cd_entidade_ete', $ent)->pluck('cd_cidade_cde')->map(function ($id) { return (int) $id; })->all();
        $agora = Carbon::now();
        $novas = array_map(function ($cidade) use ($ent, $agora) {
            return ['cd_entidade_ete' => $ent, 'cd_cidade_cde' => $cidade, 'fl_origem_cat' => 'N', 'created_at' => $agora, 'updated_at' => $agora];
        }, array_values(array_diff($desejadas, $existentes)));
        foreach (array_chunk($novas, 400) as $lote) {
            CidadeAtuacao::insert($lote);
        }

        CidadeAtuacao::where('cd_entidade_ete', $ent)->where('cd_cidade_cde', '<>', $origem)->where('fl_origem_cat', 'S')
            ->update(['fl_origem_cat' => 'N', 'updated_at' => $agora]);
        CidadeAtuacao::where('cd_entidade_ete', $ent)->where('cd_cidade_cde', $origem)
            ->update(['fl_origem_cat' => 'S', 'updated_at' => $agora]);
    }

    /** E-mail que o contrato usa: notificação, senão contato, senão o primeiro. */
    private function emailPrincipal(int $ent): ?EnderecoEletronico
    {
        $emails = EnderecoEletronico::where('cd_entidade_ete', $ent)
            ->whereNotNull('dc_endereco_eletronico_ede')->where('dc_endereco_eletronico_ede', '<>', '')
            ->orderBy('cd_endereco_eletronico_ele')->get();

        return $emails->firstWhere('cd_tipo_endereco_eletronico_tee', TipoEnderecoEletronico::NOTIFICACAO)
            ?: ($emails->firstWhere('cd_tipo_endereco_eletronico_tee', TipoEnderecoEletronico::CONTATO) ?: $emails->first());
    }

    /**
     * As comarcas chegam num campo só ("1,2,3"): há correspondentes com milhares, acima do max_input_vars do PHP.
     *
     * @param string|array|null $valor
     * @return int[]
     */
    public static function listaComarcas($valor): array
    {
        $itens = is_array($valor) ? $valor : explode(',', (string) $valor);

        return array_values(array_unique(array_map('intval', array_filter(array_map('trim', $itens), 'ctype_digit'))));
    }

    /** @return array{0:?string,1:?string} [UF, número] */
    public static function separarOab(?string $oab): array
    {
        $oab = trim((string) $oab);
        if ($oab === '') {
            return [null, null];
        }
        if (preg_match('/^(?:OAB\s*[\/\-]?\s*)?([A-Za-z]{2})\s*[\/\-]?\s*(\S.*)$/u', $oab, $m) && in_array(strtoupper($m[1]), self::UFS, true)) {
            return [strtoupper($m[1]), trim($m[2])];
        }
        if (preg_match('/^(.*\d)\s*[\/\-]?\s*([A-Za-z]{2})$/u', $oab, $m) && in_array(strtoupper($m[2]), self::UFS, true)) {
            return [strtoupper($m[2]), trim($m[1])];
        }

        return [null, $oab];
    }

    public static function documentoValido(string $digitos): bool
    {
        if (strlen($digitos) === 11) {
            if (preg_match('/^(\d)\1{10}$/', $digitos)) {
                return false;
            }
            for ($t = 9; $t < 11; $t++) {
                $soma = 0;
                for ($i = 0; $i < $t; $i++) {
                    $soma += $digitos[$i] * (($t + 1) - $i);
                }
                if ((int) $digitos[$t] !== ((10 * $soma) % 11) % 10) {
                    return false;
                }
            }
            return true;
        }

        if (strlen($digitos) === 14) {
            if (preg_match('/^(\d)\1{13}$/', $digitos)) {
                return false;
            }
            foreach ([12 => [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2], 13 => [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]] as $posicao => $pesos) {
                $soma = 0;
                foreach ($pesos as $i => $peso) {
                    $soma += $digitos[$i] * $peso;
                }
                $resto = $soma % 11;
                if ((int) $digitos[$posicao] !== ($resto < 2 ? 0 : 11 - $resto)) {
                    return false;
                }
            }
            return true;
        }

        return false;
    }

    public static function mascararDocumento(string $digitos): string
    {
        if (strlen($digitos) === 11) {
            return preg_replace('/^(\d{3})(\d{3})(\d{3})(\d{2})$/', '$1.$2.$3-$4', $digitos);
        }
        if (strlen($digitos) === 14) {
            return preg_replace('/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/', '$1.$2.$3/$4-$5', $digitos);
        }

        return $digitos;
    }

    public static function mascararTelefone(string $digitos): string
    {
        if (strlen($digitos) === 11) {
            return preg_replace('/^(\d{2})(\d{5})(\d{4})$/', '($1) $2-$3', $digitos);
        }
        if (strlen($digitos) === 10) {
            return preg_replace('/^(\d{2})(\d{4})(\d{4})$/', '($1) $2-$3', $digitos);
        }

        return $digitos;
    }

    public static function mascararCep($cep): string
    {
        $digitos = str_pad(preg_replace('/\D+/', '', (string) $cep), 8, '0', STR_PAD_LEFT);

        return substr($digitos, 0, 5) . '-' . substr($digitos, 5);
    }
}
