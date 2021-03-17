<?php

namespace App\Imports;

use App\Processo;
use App\Cliente;
use App\Contato;
use App\Entidade;
use App\Vara;
use App\Cidade;
use App\TipoServico;
use App\TipoProcesso;
use App\ProcessoTaxaHonorario;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Events\EventNotification;

class ProcessoImport implements ToCollection, WithHeadingRow, WithValidation
{
    private $rows = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $cliente = $cliente = Cliente::where('nu_cliente_cli', trim($row['cliente']))->where('cd_conta_con', \Session::get('SESSION_CD_CONTA'))->select('cd_cliente_cli')->first();
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
            $ts = TipoServico::where('nu_tipo_servico_tse', trim($row['tipo_do_servico']))
                                ->where('cd_conta_con', \Session::get('SESSION_CD_CONTA'))
                                ->select('cd_tipo_servico_tse')
                                ->first();

            $entidade = Entidade::create([
                'cd_conta_con'         => \Session::get('SESSION_CD_CONTA'),
                'cd_tipo_entidade_tpe' => \TipoEntidade::PROCESSO
            ]);

            $processo = Processo::create([
                'cd_entidade_ete' => $entidade->cd_entidade_ete,
                'cd_conta_con' => \Session::get('SESSION_CD_CONTA'),
                'cd_cliente_cli' => $cliente->cd_cliente_cli,
                'cd_contato_cot' => !empty($contato) ? $contato->cd_contato_cot : null,
                'dt_solicitacao_pro' => !empty(trim($row['data_da_solicitcao'])) ? date('Y-m-d', strtotime(str_replace('/', '-', trim($row['data_da_solicitcao'])))) : null,
                'dt_prazo_fatal_pro' => date('Y-m-d', strtotime(str_replace('/', '-', trim($row['data_prazo_fatal'])))),
                'nm_autor_pro' => trim($row['autor']),
                'nm_reu_pro' => trim($row['reu']),
                'nu_processo_pro' => trim($row['numero_do_processo']),
                'cd_vara_var' => !empty($vara) ? $vara->cd_vara_var : null,
                'cd_cidade_cde' => trim($row['comarca']),
                'cd_tipo_processo_tpo' => $tp->cd_tipo_processo_tpo,
                'nu_acompanhamento_pro' => trim($row['numero_externo'])
            ]);

            $honorario = ProcessoTaxaHonorario::create([
                'cd_conta_con' => \Session::get('SESSION_CD_CONTA'),
                'cd_processo_pro' => $processo->cd_processo_pro,
                'cd_tipo_servico_tse' => $ts->cd_tipo_servico_tse,
                'vl_taxa_honorario_cliente_pth' => !empty(trim($row['honorarios'])) ? trim($row['honorarios']) : null
            ]);

            $this->rows++;

            $i = ($this->rows*100) / count($rows)*100;
            
            event(new EventNotification(array('canal' => 'notificacao', 'conta' => 999, 'total' => $i/100, 'mensagens' => "teste")));

            usleep(50000);
        }
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
            'data_da_solicitcao' => function ($attribute, $value, $onFailure) {
                if (!empty(trim($value))) {
                    $data = explode('/', trim($value));

                    if (strlen($data[1]) != 2 || strlen($data[0]) != 2 || strlen($data[2]) != 4 || !is_numeric($data[1]) || !is_numeric($data[0]) || !is_numeric($data[2]) || !checkdate($data[1], $data[0], $data[2])) {
                        $onFailure('Data da Solicitação ('.trim($value).') inválida.');
                    }
                }
            },
            'data_prazo_fatal' => function ($attribute, $value, $onFailure) {
                if (empty(trim($value))) {
                    $onFailure('A coluna Data Prazo Fatal é obrigatória.');
                }

                if (!empty(trim($value))) {
                    $data = explode('/', trim($value));

                    if (strlen($data[1]) != 2 || strlen($data[0]) != 2 || strlen($data[2]) != 4 || !is_numeric($data[1]) || !is_numeric($data[0]) || !is_numeric($data[2]) || !checkdate($data[1], $data[0], $data[2])) {
                        $onFailure('Data Prazo Fatal ('.trim($value).') inválida.');
                    }
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
            'numero_do_processo' => function ($attribute, $value, $onFailure) {
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
            'comarca' => function ($attribute, $value, $onFailure) {
                if (empty(trim($value))) {
                    $onFailure('A coluna Comarca é obrigatória.');
                }

                if (!empty(trim($value)) && is_numeric((trim($value)))) {
                    $cidade = Cidade::where('cd_cidade_cde', trim($value))
                               ->select('cd_cidade_cde')
                               ->first();
                    if (!$cidade) {
                        $onFailure('Comarca ('.trim($value).') não encontrada.');
                    }
                } else {
                    $onFailure('Comarca ('.trim($value).') inválida.');
                }
            },
            'tipo_do_servico' => function ($attribute, $value, $onFailure) {
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
            'honorarios' => function ($attribute, $value, $onFailure) {
                if (!empty(trim($value))) {
                    $honorario = str_replace(",", ".", $value);

                    if (!is_numeric(trim($honorario))) {
                        $onFailure('Honorários ('.trim($value).') é inválido.');
                    }
                }
            },
        ];
    }
}
