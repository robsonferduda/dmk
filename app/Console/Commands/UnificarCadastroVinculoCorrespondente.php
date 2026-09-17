<?php

namespace App\Console\Commands;

use App\CidadeAtuacao;
use App\ContaCorrespondente;
use App\Endereco;
use App\EnderecoEletronico;
use App\Enums\TipoEntidade;
use App\Enums\TipoIdentificacao;
use App\Fone;
use App\Identificacao;
use App\ReembolsoTipoDespesa;
use App\RegistroBancario;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Copia dados cadastrais da entidade da pessoa (tipo 6) para a entidade do vínculo (tipo 11)
 * apenas quando o vínculo estiver sem o respectivo dado.
 *
 * Uso:
 *   php artisan correspondente:unificar-cadastro-vinculo
 *   php artisan correspondente:unificar-cadastro-vinculo --dry-run
 *   php artisan correspondente:unificar-cadastro-vinculo --conta=64
 */
class UnificarCadastroVinculoCorrespondente extends Command
{
    protected $signature = 'correspondente:unificar-cadastro-vinculo
                            {--dry-run : Não persiste, apenas reporta o que seria copiado.}
                            {--conta= : Restringe aos vínculos de um escritório (cd_conta_con).}';

    protected $description = 'Copia CPF/OAB/RG/endereço/bancos/comarcas da entidade pessoa para a entidade do vínculo quando vazios.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $contaEscritorio = $this->option('conta');

        $query = ContaCorrespondente::query()
            ->whereNotNull('cd_entidade_ete')
            ->whereNull('deleted_at');

        if ($contaEscritorio) {
            $query->where('cd_conta_con', $contaEscritorio);
        }

        $vinculos = $query->with(['entidade', 'correspondente.entidade'])->get();

        $this->info('[unificar] vínculos=' . $vinculos->count() . ($dryRun ? '  DRY-RUN' : ''));

        $totais = [
            'identificacao' => 0,
            'endereco' => 0,
            'fone' => 0,
            'email' => 0,
            'banco' => 0,
            'comarca' => 0,
            'reembolso' => 0,
            'sem_pessoa' => 0,
        ];

        DB::beginTransaction();

        try {
            foreach ($vinculos as $vinculo) {
                $entidadePessoa = optional($vinculo->correspondente)->entidade;
                if (! $entidadePessoa || (int) $entidadePessoa->cd_tipo_entidade_tpe !== TipoEntidade::CORRESPONDENTE) {
                    // Conta::entidade() pode não filtrar tipo; busca explícita
                    $entidadePessoa = \App\Entidade::where('cd_conta_con', $vinculo->cd_correspondente_cor)
                        ->where('cd_tipo_entidade_tpe', TipoEntidade::CORRESPONDENTE)
                        ->first();
                }

                if (! $entidadePessoa || ! $vinculo->cd_entidade_ete) {
                    $totais['sem_pessoa']++;
                    continue;
                }

                $totais['identificacao'] += $this->copiarIdentificacoes($entidadePessoa->cd_entidade_ete, $vinculo, $dryRun);
                $totais['endereco'] += $this->copiarEndereco($entidadePessoa->cd_entidade_ete, $vinculo, $dryRun);
                $totais['fone'] += $this->copiarFones($entidadePessoa->cd_entidade_ete, $vinculo, $dryRun);
                $totais['email'] += $this->copiarEmails($entidadePessoa->cd_entidade_ete, $vinculo, $dryRun);
                $totais['banco'] += $this->copiarBancos($entidadePessoa->cd_entidade_ete, $vinculo, $dryRun);
                $totais['comarca'] += $this->copiarComarcas($entidadePessoa->cd_entidade_ete, $vinculo, $dryRun);
                $totais['reembolso'] += $this->copiarReembolsos($entidadePessoa->cd_entidade_ete, $vinculo, $dryRun);
            }

            if ($dryRun) {
                DB::rollBack();
            } else {
                DB::commit();
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('[unificar] falha: ' . $e->getMessage());
            return 1;
        }

        foreach ($totais as $chave => $valor) {
            $this->line("  {$chave}: {$valor}");
        }

        $this->info('[unificar] concluído.');
        return 0;
    }

    private function copiarIdentificacoes(int $origem, ContaCorrespondente $vinculo, bool $dryRun): int
    {
        $tipos = [
            TipoIdentificacao::CPF,
            TipoIdentificacao::CNPJ,
            TipoIdentificacao::OAB,
            TipoIdentificacao::RG,
        ];

        $copiados = 0;
        foreach ($tipos as $tipo) {
            $temVinculo = Identificacao::where('cd_entidade_ete', $vinculo->cd_entidade_ete)
                ->where('cd_tipo_identificacao_tpi', $tipo)
                ->whereNotNull('nu_identificacao_ide')
                ->where('nu_identificacao_ide', '!=', '')
                ->exists();

            if ($temVinculo) {
                continue;
            }

            $origemIde = Identificacao::where('cd_entidade_ete', $origem)
                ->where('cd_tipo_identificacao_tpi', $tipo)
                ->whereNotNull('nu_identificacao_ide')
                ->where('nu_identificacao_ide', '!=', '')
                ->first();

            if (! $origemIde) {
                continue;
            }

            $copiados++;
            if ($dryRun) {
                continue;
            }

            Identificacao::create([
                'cd_entidade_ete' => $vinculo->cd_entidade_ete,
                'cd_conta_con' => $origemIde->cd_conta_con ?: $vinculo->cd_correspondente_cor,
                'cd_tipo_identificacao_tpi' => $tipo,
                'nu_identificacao_ide' => $origemIde->nu_identificacao_ide,
            ]);
        }

        return $copiados;
    }

    private function copiarEndereco(int $origem, ContaCorrespondente $vinculo, bool $dryRun): int
    {
        $temVinculo = Endereco::where('cd_entidade_ete', $vinculo->cd_entidade_ete)
            ->whereNotNull('dc_logradouro_ede')
            ->where('dc_logradouro_ede', '!=', '')
            ->exists();

        if ($temVinculo) {
            return 0;
        }

        $origemEnd = Endereco::where('cd_entidade_ete', $origem)
            ->whereNotNull('dc_logradouro_ede')
            ->where('dc_logradouro_ede', '!=', '')
            ->first();

        if (! $origemEnd) {
            return 0;
        }

        if (! $dryRun) {
            Endereco::create([
                'cd_entidade_ete' => $vinculo->cd_entidade_ete,
                'cd_conta_con' => $origemEnd->cd_conta_con ?: $vinculo->cd_correspondente_cor,
                'nu_cep_ede' => $origemEnd->getAttributes()['nu_cep_ede'] ?? $origemEnd->nu_cep_ede,
                'dc_logradouro_ede' => $origemEnd->dc_logradouro_ede,
                'nu_numero_ede' => $origemEnd->nu_numero_ede,
                'nm_bairro_ede' => $origemEnd->nm_bairro_ede,
                'dc_complemento_ede' => $origemEnd->dc_complemento_ede,
                'cd_cidade_cde' => $origemEnd->cd_cidade_cde,
            ]);
        }

        return 1;
    }

    private function copiarFones(int $origem, ContaCorrespondente $vinculo, bool $dryRun): int
    {
        if (Fone::where('cd_entidade_ete', $vinculo->cd_entidade_ete)->exists()) {
            return 0;
        }

        $fones = Fone::where('cd_entidade_ete', $origem)->get();
        if ($fones->isEmpty()) {
            return 0;
        }

        if (! $dryRun) {
            foreach ($fones as $fone) {
                Fone::create([
                    'cd_entidade_ete' => $vinculo->cd_entidade_ete,
                    'cd_conta_con' => $fone->cd_conta_con ?: $vinculo->cd_correspondente_cor,
                    'cd_tipo_fone_tfo' => $fone->cd_tipo_fone_tfo,
                    'nu_fone_fon' => $fone->nu_fone_fon,
                ]);
            }
        }

        return $fones->count();
    }

    private function copiarEmails(int $origem, ContaCorrespondente $vinculo, bool $dryRun): int
    {
        if (EnderecoEletronico::where('cd_entidade_ete', $vinculo->cd_entidade_ete)->exists()) {
            return 0;
        }

        $emails = EnderecoEletronico::where('cd_entidade_ete', $origem)->get();
        if ($emails->isEmpty()) {
            return 0;
        }

        if (! $dryRun) {
            foreach ($emails as $email) {
                EnderecoEletronico::create([
                    'cd_entidade_ete' => $vinculo->cd_entidade_ete,
                    'cd_conta_con' => $email->cd_conta_con ?: $vinculo->cd_correspondente_cor,
                    'cd_tipo_endereco_eletronico_tee' => $email->cd_tipo_endereco_eletronico_tee,
                    'dc_endereco_eletronico_ede' => $email->dc_endereco_eletronico_ede,
                ]);
            }
        }

        return $emails->count();
    }

    private function copiarBancos(int $origem, ContaCorrespondente $vinculo, bool $dryRun): int
    {
        if (RegistroBancario::where('cd_entidade_ete', $vinculo->cd_entidade_ete)->exists()) {
            return 0;
        }

        $bancos = RegistroBancario::where('cd_entidade_ete', $origem)->get();
        if ($bancos->isEmpty()) {
            return 0;
        }

        if (! $dryRun) {
            foreach ($bancos as $banco) {
                RegistroBancario::create([
                    'cd_entidade_ete' => $vinculo->cd_entidade_ete,
                    'cd_conta_con' => $banco->cd_conta_con ?: $vinculo->cd_correspondente_cor,
                    'nm_titular_dba' => $banco->nm_titular_dba,
                    'nu_cpf_cnpj_dba' => $banco->getAttributes()['nu_cpf_cnpj_dba'] ?? $banco->nu_cpf_cnpj_dba,
                    'nu_agencia_dba' => $banco->nu_agencia_dba,
                    'nu_conta_dba' => $banco->nu_conta_dba,
                    'cd_banco_ban' => $banco->cd_banco_ban,
                    'dc_pix_dba' => $banco->dc_pix_dba,
                    'cd_tipo_conta_tcb' => $banco->cd_tipo_conta_tcb,
                ]);
            }
        }

        return $bancos->count();
    }

    private function copiarComarcas(int $origem, ContaCorrespondente $vinculo, bool $dryRun): int
    {
        if (CidadeAtuacao::where('cd_entidade_ete', $vinculo->cd_entidade_ete)->exists()) {
            return 0;
        }

        $comarcas = CidadeAtuacao::where('cd_entidade_ete', $origem)->get();
        if ($comarcas->isEmpty()) {
            return 0;
        }

        if (! $dryRun) {
            foreach ($comarcas as $comarca) {
                CidadeAtuacao::create([
                    'cd_entidade_ete' => $vinculo->cd_entidade_ete,
                    'cd_cidade_cde' => $comarca->cd_cidade_cde,
                    'fl_origem_cat' => $comarca->fl_origem_cat,
                ]);
            }
        }

        return $comarcas->count();
    }

    private function copiarReembolsos(int $origem, ContaCorrespondente $vinculo, bool $dryRun): int
    {
        $temVinculo = ReembolsoTipoDespesa::where('cd_entidade_ete', $vinculo->cd_entidade_ete)
            ->where('cd_conta_con', $vinculo->cd_conta_con)
            ->exists();

        if ($temVinculo) {
            return 0;
        }

        $reembolsos = ReembolsoTipoDespesa::where('cd_entidade_ete', $origem)
            ->where('cd_conta_con', $vinculo->cd_conta_con)
            ->get();

        if ($reembolsos->isEmpty()) {
            return 0;
        }

        if (! $dryRun) {
            foreach ($reembolsos as $reembolso) {
                ReembolsoTipoDespesa::create([
                    'cd_entidade_ete' => $vinculo->cd_entidade_ete,
                    'cd_conta_con' => $vinculo->cd_conta_con,
                    'cd_tipo_despesa_tds' => $reembolso->cd_tipo_despesa_tds,
                ]);
            }
        }

        return $reembolsos->count();
    }
}
