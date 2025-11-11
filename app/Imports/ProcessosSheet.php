<?php

namespace App\Imports;

use Auth;
use App\Processo;
use App\Cliente;
use App\Contato;
use App\Entidade;
use App\Vara;
use App\Cidade;
use App\Estado;
use App\TipoServico;
use App\TipoProcesso;
use App\TaxaHonorario;
use App\ProcessoTaxaHonorario;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Events\EventNotification;

class ProcessosSheet implements ToCollection, WithHeadingRow, WithValidation
{
    private $importador;

    public function __construct($importador)
    {
        $this->canal = 'user-'.Auth::user()->cd_conta_con.'-'.Auth::user()->id;
        $this->importador = $importador;
    }

    public function collection(Collection $rows)
    {
        $progressKey = 'import_progress_' . \Auth::user()->id;
        $totalLinhas = count($rows);
        
        foreach ($rows as $index => $row) {

            try {
                $cliente = $cliente = Cliente::where('nu_cliente_cli', trim($row['cliente']))->where('cd_conta_con', \Session::get('SESSION_CD_CONTA'))->select('cd_cliente_cli', 'taxa_imposto_cli', 'cd_entidade_ete')->first();
                $contato = Contato::where('nu_contato_cot', trim($row['advogado_solicitante']))
                                   ->where('cd_conta_con', \Session::get('SESSION_CD_CONTA'))
                                   ->select('cd_contato_cot')
                                   ->first();
                $vara = Vara::where('nu_vara_var', trim($row['vara']))
                                   ->where('cd_conta_con', \Session::get('SESSION_CD_CONTA'))
                                   ->select('cd_vara_var')
                                   ->first();
                $tp = TipoProcesso::where('nu_tipo_processo_tpo', trim($row['tipo_de_processo']))
                                    ->where('cd_conta_con', \Session::get('SESSION_CD_CONTA'))
                                    ->select('cd_tipo_processo_tpo')
                                    ->first();
                $ts = TipoServico::where('nu_tipo_servico_tse', trim($row['tipo_de_servico']))
                                    ->where('cd_conta_con', \Session::get('SESSION_CD_CONTA'))
                                    ->select('cd_tipo_servico_tse')
                                    ->first();

                $entidade = Entidade::create([
                    'cd_conta_con'         => \Session::get('SESSION_CD_CONTA'),
                    'cd_tipo_entidade_tpe' => \TipoEntidade::PROCESSO
                ]);

                $processo = Processo::create([
                    'cd_entidade_ete' => $entidade->cd_entidade_ete,
                    'cd_area_direito_ado' => $row['area_do_direito'],
                    'cd_conta_con' => \Session::get('SESSION_CD_CONTA'),
                    'cd_cliente_cli' => $cliente->cd_cliente_cli,
                    'cd_contato_cot' => !empty($contato) ? $contato->cd_contato_cot : null,
                    'dt_solicitacao_pro' => !empty(trim($row['data_solicitacao'])) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['data_solicitacao']) : null,
                    'dt_prazo_fatal_pro' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['data_prazo_fatal']),
                    'nm_autor_pro' => trim($row['autor']),
                    'nm_reu_pro' => trim($row['reu']),
                    'nu_processo_pro' => trim($row['numero_processo']),
                    'cd_vara_var' => !empty($vara) ? $vara->cd_vara_var : null,
                    'cd_cidade_cde' => trim($row['comarca']),
                    'cd_tipo_processo_tpo' => $tp->cd_tipo_processo_tpo,
                    'nu_acompanhamento_pro' => trim($row['numero_externo']),
                    'hr_audiencia_pro' => !empty($row['hora']) ?  \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['hora'])->format('H:i') : null,
                    'cd_status_processo_stp' => \App\Enums\StatusProcesso::PENDENTE_ANALISE,
                    'nu_lote' => rand(100000,999999)
                ]);

                $valor = TaxaHonorario::where('cd_conta_con', \Session::get('SESSION_CD_CONTA'))
                                      ->where('cd_tipo_servico_tse', $ts->cd_tipo_servico_tse)
                                      ->where('cd_cidade_cde', $row['comarca'])
                                      ->where('cd_entidade_ete', $cliente->cd_entidade_ete)
                                      ->select('nu_taxa_the')->first();

                $honorario = ProcessoTaxaHonorario::create([
                    'cd_conta_con' => \Session::get('SESSION_CD_CONTA'),
                    'cd_processo_pro' => $processo->cd_processo_pro,
                    'cd_tipo_servico_tse' => $ts->cd_tipo_servico_tse,
                    'vl_taxa_honorario_cliente_pth' => !empty($valor) ? $valor->nu_taxa_the : null,
                    'vl_taxa_cliente_pth' => !empty($cliente->taxa_imposto_cli) ? $cliente->taxa_imposto_cli : null
                 ]);

                $this->importador->setRowCount();
                
                // Atualizar progresso no cache
                $progress = \Cache::get($progressKey, [
                    'total' => $totalLinhas,
                    'processadas' => 0,
                    'sucesso' => 0,
                    'erros' => 0
                ]);
                
                $progress['processadas'] = $this->importador->getRowCount();
                $progress['sucesso'] = $this->importador->getRowCount();
                $progress['status'] = 'processando';
                
                \Cache::put($progressKey, $progress, 600);

            } catch (\Exception $e) {
                // Em caso de erro, atualizar contador de erros
                $progress = \Cache::get($progressKey, [
                    'total' => $totalLinhas,
                    'processadas' => 0,
                    'sucesso' => 0,
                    'erros' => 0
                ]);
                
                $progress['processadas'] = ($index + 1);
                $progress['erros'] = $progress['erros'] + 1;
                $progress['status'] = 'processando';
                
                \Cache::put($progressKey, $progress, 600);
                
                // Re-lançar exceção para que a validação padrão funcione
                throw $e;
            }

            $i = ($this->importador->getRowCount()*100) / count($rows)*100;
            
            //event(new EventNotification(array('canal' => $this->canal, 'total' => $i/100)));

            usleep(50000);
        }
    }

    public function prepareForValidation($data, $index)
    {
        // Limpa área do direito (extrai apenas o código do formato "53 - CÍVEL")
        if (!empty($data['area_do_direito'])) {
            // Se vier no formato "53 - CÍVEL", extrai apenas o número
            if (strpos($data['area_do_direito'], ' - ') !== false) {
                $data['area_do_direito'] = trim(explode(' - ', $data['area_do_direito'])[0]);
            } else {
                $data['area_do_direito'] = $this->limpaCodigo($data['area_do_direito']);
            }
        }
        
        $data['cliente'] = $this->limpaCodigo($data['cliente']);
        $data['advogado_solicitante'] = $this->limpaCodigo($data['advogado_solicitante']);
        $data['vara'] = $this->limpaCodigo($data['vara']);
        $data['tipo_de_servico'] = $this->limpaCodigo($data['tipo_de_servico']);
        $data['tipo_de_processo'] = $this->limpaCodigo($data['tipo_de_processo']);

        $estado = Estado::where('sg_estado_est', trim($data['estado']))->first();

        if (!empty($estado)) {
            $data['estado'] = $estado->cd_estado_est;
            $cidade = Cidade::where('nm_cidade_cde', trim($data['comarca']))
                    ->where('cd_estado_est', $estado->cd_estado_est)
                    ->first();
        } else {
            $data['estado'] = null;
        }
        
        if (!empty($cidade)) {
            $data['comarca'] = $cidade->cd_cidade_cde;
        } else {
            $data['comarca'] = null;
        }

        return $data;
    }

    private function limpaCodigo($string)
    {
        $start = '---';
        $end = '---';
        
        $ini = strpos($string, $start);
        if ($ini == 0) {
            return null;
        }
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    public function getRowCount(): int
    {
        return $this->rows;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            foreach ($validator->getData() as $key => $data) {
                if (!empty($data['advogado_solicitante'])) {
                    $cliente = Cliente::where('nu_cliente_cli', trim($data['cliente']))->where('cd_conta_con', \Session::get('SESSION_CD_CONTA'))->first();
                    if ($cliente) {
                        $contato = Contato::where('nu_contato_cot', trim($data['advogado_solicitante']))
                                   ->where('cd_conta_con', \Session::get('SESSION_CD_CONTA'))
                                   ->where('cd_entidade_ete', $cliente->cd_entidade_ete)
                                   ->first();
                        if (empty($contato)) {
                            $msg = "Advogado Solicitante (".trim($data['advogado_solicitante']).") não pertence ao cliente.";
                            $validator->errors()->add($key.'.advogado_solicitante', $msg);
                        }
                    }
                }

                if (!empty($data['estado']) && empty($data['comarca'])) {
                    $msg = "Comarca não pertence ao Estado escolhido.";
                    $validator->errors()->add($key.'.comarca', $msg);
                }
            }
        });
    }

    public function rules(): array
    {
        return [
            'cliente' => function ($attribute, $value, $onFailure) {
                if (empty(trim($value))) {
                    $onFailure('A coluna Cliente é obrigatória.');
                }

                if (!empty(trim($value))) {
                    $cliente = Cliente::where('nu_cliente_cli', trim($value))->where('cd_conta_con', \Session::get('SESSION_CD_CONTA'))->select('cd_cliente_cli')->first();
                    
                    if (!$cliente) {
                        $onFailure('Cliente ('.trim($value).') não encontrado.');
                    }
                }
            },
            'advogado_solicitante' => function ($attribute, $value, $onFailure) {
                if (!empty(trim($value))) {
                    $contato = Contato::where('nu_contato_cot', trim($value))
                               ->where('cd_conta_con', \Session::get('SESSION_CD_CONTA'))
                               ->select('cd_contato_cot')
                               ->first();
                    if (!$contato) {
                        $onFailure('Advogado Solicitante ('.trim($value).') não encontrado.');
                    }
                }
            },
            'data_solicitacao' => function ($attribute, $value, $onFailure) {
                if (is_string($value)) {
                    $onFailure('Data da Solicitação ('.trim($value).') deve ser do tipo Data.');
                }
            },
            'data_prazo_fatal' => function ($attribute, $value, $onFailure) {
                if (is_string($value)) {
                    $onFailure('Data Prazo Fatal ('.trim($value).') deve ser do tipo Data.');
                }
            },
            'hora' => function ($attribute, $value, $onFailure) {
                if (is_string($value)) {
                    $onFailure('Hora ('.trim($value).') deve ser do tipo Hora e Minuto.');
                }
            },
            'autor' => function ($attribute, $value, $onFailure) {
                if (!empty(trim($value)) && strlen(trim($value)) > 255) {
                    $onFailure('O tamanho da coluna Autor é inválido ('.trim($value).'). Permitido somente 255 caracteres.');
                }
            },
            'reu' => function ($attribute, $value, $onFailure) {
                if (!empty(trim($value)) && strlen(trim($value)) > 255) {
                    $onFailure('O tamanho da coluna Réu é inválido ('.trim($value).'). Permitido somente 255 caracteres.');
                }
            },
            'numero_processo' => function ($attribute, $value, $onFailure) {
                if (empty(trim($value))) {
                    $onFailure('A coluna Número do Processo é obrigatória.');
                }

                if (!empty(trim($value)) && strlen(trim($value)) > 255) {
                    $onFailure('O tamanho da coluna Número do Processo é inválido ('.trim($value).'). Permitido somente 255 caracteres.');
                }
            },
            'numero_externo' => function ($attribute, $value, $onFailure) {
                if (!empty(trim($value)) && strlen(trim($value)) > 255) {
                    $onFailure('O tamanho da coluna Número Externo é inválido ('.trim($value).'). Permitido somente 255 caracteres.');
                }
            },
            'vara' => function ($attribute, $value, $onFailure) {
                if (!empty(trim($value))) {
                    $vara = Vara::where('nu_vara_var', trim($value))
                               ->where('cd_conta_con', \Session::get('SESSION_CD_CONTA'))
                               ->select('cd_vara_var')
                               ->first();
                    if (!$vara) {
                        $onFailure('Vara ('.trim($value).') não encontrada.');
                    }
                }
            },
            'estado' => function ($attribute, $value, $onFailure) {
                if (empty(trim($value))) {
                    $onFailure('A coluna Estado é obrigatória.');
                }
            },
            'comarca' => function ($attribute, $value, $onFailure) {
                if (empty(trim($value))) {
                    $onFailure('A coluna Comarca é obrigatória.');
                }
            },
            'tipo_de_servico' => function ($attribute, $value, $onFailure) {
                if (empty(trim($value))) {
                    $onFailure('A coluna Tipo de Serviço é obrigatória.');
                }

                if (!empty(trim($value))) {
                    $tp = TipoServico::where('nu_tipo_servico_tse', trim($value))
                                ->where('cd_conta_con', \Session::get('SESSION_CD_CONTA'))
                                ->select('cd_tipo_servico_tse')
                                ->first();
                    if (!$tp) {
                        $onFailure('Tipo de Serviço ('.trim($value).') não encontrado.');
                    }
                }
            },
            'tipo_de_processo' => function ($attribute, $value, $onFailure) {
                if (empty(trim($value))) {
                    $onFailure('A coluna Tipo de Processo é obrigatória.');
                }

                if (!empty(trim($value))) {
                    $tp = TipoProcesso::where('nu_tipo_processo_tpo', trim($value))
                                ->where('cd_conta_con', \Session::get('SESSION_CD_CONTA'))
                                ->select('cd_tipo_processo_tpo')
                                ->first();
                    if (!$tp) {
                        $onFailure('Tipo de Processo ('.trim($value).') não encontrado.');
                    }
                }
            },
            'area_do_direito' => function ($attribute, $value, $onFailure) {
                if (empty(trim($value))) {
                    $onFailure('A coluna Área do Direito é obrigatória.');
                }

                if (!empty(trim($value))) {
                    $areas_permitidas = [53, 55]; // 53 = CÍVEL, 55 = TRABALHISTA
                    if (!in_array((int)trim($value), $areas_permitidas)) {
                        $onFailure('Área do Direito ('.trim($value).') inválida. Valores permitidos: 53 (CÍVEL) ou 55 (TRABALHISTA).');
                    }
                }
            },
        ];
    }
}
