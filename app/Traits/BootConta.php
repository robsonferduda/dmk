<?php 

namespace App\Traits;
use App\TipoContato;
use App\TipoServico;
use App\TipoProcesso;

trait BootConta
{
   
    public function bootConta($cdConta)
    {
       $this->criarTipoContato($cdConta);
       $this->criarTipoServico($cdConta);
       $this->criarTipoProcesso($cdConta);
    }

    private function criarTipoContato($cdConta){

    	$tipo = new TipoContato();
  
        $tipo->create([ 'cd_conta_con' => $cdConta,
        			 	'nm_tipo_contato_tct' => 'Advogado',
        			 	'fl_tipo_padrao_tct' => 'S'
        			 ]);   
    }

    private function criarTipoServico($cdConta){
    	
    	$tiposServico = array(
    		  'AUDIÊNCIA DE CONCILIAÇÃO (ADVOGADO E PREPOSTO)',
    		  'AUDIÊNCIA DE CONCILIAÇÃO (ADVOGADO)',
    		  'AUDIÊNCIA DE CONCILIAÇÃO (PREPOSTO)',
    		  'AUDIÊNCIA DE INSTRUÇÃO (ADVOGADO E PREPOSTO)',
    		  'AUDIÊNCIA DE INSTRUÇÃO (ADVOGADO)',
    		  'AUDIÊNCIA DE INSTRUÇÃO (PREPOSTO)',
    		  'AUDIÊNCIA PROCON (ADVOGADO E PREPOSTO)',
    		  'AUDIÊNCIA PROCON (ADVOGADO)',
    		  'AUDIÊNCIA PROCON (PREPOSTO)',
    		  'AUDIÊNCIA TRABALHISTA DE CONCILIAÇÃO (ADVOGADO E PREPOSTO)',
    		  'AUDIÊNCIA TRABALHISTA DE CONCILIAÇÃO (ADVOGADO)',
    		  'AUDIÊNCIA TRABALHISTA DE CONCILIAÇÃO (PREPOSTO)',
    		  'AUDIÊNCIA TRABALHISTA DE INSTRUÇÃO (ADVOGADO E PREPOSTO)',
    		  'AUDIÊNCIA TRABALHISTA DE INSTRUÇÃO (ADVOGADO)',
    		  'AUDIÊNCIA TRABALHISTA DE INSTRUÇÃO (PREPOSTO)',
    		  'AUDIÊNCIA VARA CÍVEL (ADVOGADO E PREPOSTO)',
    		  'CÓPIAS',
    		  'DESPACHO',
    		  'PROTOCOLO FÍSICO',
    		  'PROTOCOLO VIRTUAL');

    	foreach ($tiposServico as $tipo) {
    		
    		$tipoServico = new TipoServico();

    		$tipoServico->create([
    					    'cd_conta_con' => $cdConta,
    						'nm_tipo_servico_tse' => $tipo
    					  ]);
    	}
    }

    private function criarTipoProcesso($cdConta){

    	$tiposServico = array(
    		'Audiências e Protocolos',
    		'Diligências em Geral',
    		'Processos Particulares'
    		);

    	foreach ($tiposServico as $tipo) {
    		$tipoProcesso = new TipoProcesso();

    		$tipoProcesso->create([ 
    						'nm_tipo_processo_tpo' => $tipo,
    						'cd_conta_con' => $cdConta
    			]);
    	}
    }
}
