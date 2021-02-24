<?php

namespace App\Jobs;

use App\TaxaHonorario;
use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;

class HonorarioCorrespondenteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $dados;

    public function __construct(array $dados)
    {
        $this->dados = $dados;
    }

    public function handle()
    {
        
        if(!empty($this->dados['valores']) && count($this->dados['valores']) > 0){

            $conta = $this->dados['conta'];
            $entidade = $this->dados['entidade'];
            $valores = $this->dados['valores'];         
                            
            for($i = 0; $i < count($valores); $i++) {
              
                $valor = TaxaHonorario::where('cd_conta_con',$conta)
                                      ->where('cd_entidade_ete',$entidade)
                                      ->where('cd_cidade_cde',$valores[$i]->cidade)
                                      ->where('cd_tipo_servico_tse',$valores[$i]->servico)->first();
                
                if(!empty($valor)){

                    $valor->nu_taxa_the = str_replace(",", ".", $valores[$i]->valor);
                    $valor->saveOrFail();

                }else{

                    $valor = TaxaHonorario::create([
                        'cd_entidade_ete'           => $entidade,
                        'cd_conta_con'              => $conta, 
                        'cd_tipo_servico_tse'       => $valores[$i]->servico,
                        'cd_cidade_cde'             => $valores[$i]->cidade,
                        'nu_taxa_the'               => str_replace(",", ".", $valores[$i]->valor),
                        'dc_observacao_the'         => "--"
                    ]);
                }
                
            }
        
        }
    }

}