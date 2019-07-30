<?php 

namespace App\Traits;
use App\TipoContato;
use App\TipoServico;
use App\TipoProcesso;
use App\Conta;
use App\Calendario;

trait BootConta
{
   
    public function bootConta($cdConta)
    {
       //$this->criarTipoContato($cdConta);
       //$this->criarTipoServico($cdConta);
       //$this->criarTipoProcesso($cdConta);
       $this->criarCalendario($cdConta);
    }

    private function criarCalendario($cdConta){
       
        $scopes = [ \Google_Service_Calendar::CALENDAR ];

        $conta = Conta::where('cd_conta_con',$cdConta)->first();

        $client = new \Google_Client();
        $client->setScopes($scopes);
        $client->setAuthConfig(storage_path().'/app/calendario-dmk.json');

        $service = new \Google_Service_Calendar($client);

        $calendar = new \Google_Service_Calendar_Calendar();
        $calendar->setSummary('Calendário - '.$conta->nm_razao_social_con);
        $calendar->setTimeZone(config('app.timezone'));
        $createdCalendar = $service->calendars->insert($calendar);

        $calendario = new Calendario();
        
        $calendario->create([ 'cd_conta_con' => $cdConta,
                        'id_calendario_google_cal' => $createdCalendar->getId(),
                     ]); 
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
