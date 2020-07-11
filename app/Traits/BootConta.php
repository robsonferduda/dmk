<?php 

namespace App\Traits;
use App\TipoContato;
use App\TipoServico;
use App\TipoProcesso;
use App\Conta;
use App\Calendario;
use App\Vara;

trait BootConta
{
   
    public function bootConta($cdConta)
    {
       $this->criarTipoContato($cdConta);
       $this->criarTipoServico($cdConta);
       $this->criarTipoProcesso($cdConta);
       $this->criarVara($cdConta);
       if(getenv('APP_ENV') == 'production')
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

        $rule = new \Google_Service_Calendar_AclRule();
        $scope = new \Google_Service_Calendar_AclRuleScope();

        $scope->setType("default");
        $rule->setScope($scope);
        $rule->setRole("reader");

        $createdRule = $service->acl->insert($createdCalendar->getId(), $rule);
        
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

    private function criarVara($cdConta){

        $varas = array(
            "10ª DELEGACIA DE POLÍCIA",
            "10ª TURMA DE RECURSOS",
            "10ª VARA CÍVEL",
            "10ª VARA DO TRABALHO",
            "10ª VARA FEDERAL",
            "10º OFÍCIO DE REGISTRO DE IMÓVEIS",
            "11ª CÂMARA DE DIREITO COMERCIAL",
            "11ª PROMOTORIA DE JUSTIÇA DA COMARCA DE SÃO JOSÉ",
            "11ª VARA CÍVEL",
            "11ª VARA DO TRABALHO",
            "11ª VARA FEDERAL",
            "12ª VARA DO TRABALHO",
            "13ª PROMOTORIA DE JUSTIÇA",
            "15ª DELEGACIA REGIONAL DE POLÍCIA - DIC",
            "16ª CÂMARA CÍVEL",
            "16ª VARA DO TRABALHO DE CURITIBA",
            "16ª VARA FEDERAL",
            "19ª VARA FEDERAL",
            "1ª CÂMARA DE DIREITO CIVIL",
            "1ª CÂMARA DE DIREITO COMERCIAL TRIBUNAL JUSTIÇA",
            "1ª CAMARA DE ENFRENTAMENTO DE ACERVOS",
            "1ª DELEGACIA DE POLÍCIA",
            "1ª JUIZADO ESPECIAL CÍVEL",
            "1ª JUIZADO ESPECIAL CÍVEL E CRIMINAL",
            "1ª PROMOTORIA DE JUSTIÇA",
            "1ª TURMA DE RECURSOS",
            "1ª TURMA DO TRIBUNAL DE JUSTIÇA",
            "1ª UNIDADE JUDICIÁRIA DE COOPERAÇÃO",
            "1ª VARA CÍVEL",
            "1ª VARA COMERCIAL",
            "1ª VARA CRIMINAL",
            "1ª VARA DA FAMÍLIA",
            "1ª VARA DA FAZENDA ACIDENTES DO TRABALHO",
            "1ª VARA DA FAZENDA ACIDENTES DO TRABALHO E REGISTRO PÚBLICO",
            "1ª VARA DA FAZENDA PÚBLICA",
            "1ª VARA DE DIREITO BANCÁRIO",
            "1ª VARA DE EXECUÇÕES FISCAIS",
            "1ª VARA DE FALÊNCIAS E RECUPERAÇÃO JUDICIAL",
            "1ª VARA DO TRABALHO",
            "1ª VARA FEDERAL",
            "1° CARTÓRIO DE TÍTULOS E DOCUMENTOS",
            "1º CARTÓRIO DE PROTESTO",
            "1º OFÍCIO DE REGISTRO DE IMÓVEIS",
            "1º REGISTRO DE IMÓVEIS",
            "1º TABELIONATO DE NOTAS E PROTESTOS DA CAPITAL",
            "1º TABELIONATO DE NOTAS, REGISTRO DE IMÓVEIS E PROTESTO",
            "20ª VARA CÍVEL",
            "21ª VARA CÍVEL",
            "23ª VARA FEDERAL",
            "'28ª PROMOTORIA DE JUSTIÇA - MINISTÉRIO PÚBLICO ESTADUAL",
            "29ª PROMOTORIA DE JUSTIÇA",
            "2ª CÂMARA DE DIREITO CIVIL",
            "2ª CÂMARA DE DIREITO COMERCIAL TRIBUNAL JUSTIÇA",
            "2ª CAMÂRA DO CONSELHO MUNICIPAL DE CONTRIBUINTES",
            "2ª DELEGACIA DE POLÍCIA",
            "2ª JUIZADO ESPECIAL CÍVEL",
            "2ª TURMA DE RECURSOS",
            "2ª TURMA TRT",
            "2ª VARA CÍVEL",
            "2ª VARA CÍVEL - CONTINENTE",
            "2ª VARA CRIMINAL",
            "2ª VARA DA FAMÍLIA",
            "2ª VARA DA FAZENDA",
            "2ª VARA DA FAZENDA E EXECUCAO FISCAL",
            "2ª VARA DA FAZENDA PÚBLICA",
            "2ª VARA DE DIREITO BANCÁRIO",
            "2ª VARA DO TRABALHO",
            "2ª VARA FEDERAL",
            "2º CARTÓRIO DE PROTESTO",
            "2º OFÍCIO DE REGISTRO DE IMÓVEIS",
            "2º TABELIONATO DE NOTAS, REGISTRO DE IMÓVEIS E PROTESTO",
            "30ª PROMOTORIA DE JUSTIÇA",
            "31ª PROMOTORIA DE JUSTIÇA",
            "32º PROMOTORIA DE JUSTIÇA DE DEFESA DO MEIO AMBIENTE",
            "3ª CÂMARA DE DIREITO CIVIL",
            "3ª CAMARA DE DIREITO COMERCIAL",
            "3ª CAMARA DE DIREITO COMERCIAL TRIBUNAL JUSTIÇA",
            "3ª CAMARA DE DIREITO PRIVADO",
            "3ª CAMARA DE DIREITO PÚBLICO",
            "3ª COMISSÃO DE INSTRUÇÃO DE PROCESSOS ÉTICOS-DISCIPLINARES DA OAB/SC",
            "3ª COMPANHIA DO BATALHÃO DA POLÍCIA MILITAR AMBIENTAL",
            "3ª DELEGACIA DE POLÍCIA",
            "3ª PROMOTORIA DE JUSTIÇA",
            "3ª TURMA DE RECURSOS",
            "3ª TURMA TRIBUNAL REGIONAL DO TRABALHO",
            "3ª VARA CÍVEL",
            "3ª VARA CRIMINAL",
            "3ª VARA DA FAZENDA PÚBLICA",
            "3ª VARA DE DIREITO BANCÁRIO",
            "3ª VARA DO TRABALHO",
            "3ª VARA FEDERAL",
            "3ª VICE PRESIDENCIA DO TRIBUNAL",
            "3º CARTÓRIO DE PROTESTO",
            "3º JUIZADO ESPECIAL CÍVEL",
            "3º OFÍCIO DE REGISTRO DE IMÓVEIS",
            "4ª CÂMARA DE DIREITO CIVIL",
            "4ª CÂMARA DE DIREITO COMERCIAL",
            "4ª CÂMARA DE DIREITO PÚBLICO",
            "4ª COMPANHIA DO BATALÃO DA POLÍCIA AMBIENTAL ESPECIALIZADA",
            "4ª COMPANHIA DO BATALHÃO DA POLÍCIA MILITAR AMBIENTAL",
            "4ª DELEGACIA DE POLÍCIA",
            "4ª JUIZADO ESPECIAL CÍVEL",
            "4ª PROMOTORIA DE JUSTIÇA",
            "4ª TURMA DE RECURSOS",
            "4ª VARA CÍVEL",
            "4ª VARA CRIMINAL",
            "4ª VARA DO TRABALHO",
            "4ª VARA FEDERAL",
            "4ª VARA REGIONAL DE DIREITO BANCARIO",
            "4º CARTÓRIO DE PROTESTO",
            "4º OFÍCIO DE REGISTRO DE IMÓVEIS",
            "5ª CÂMARA DE DIREITO CIVIL",
            "5ª CÂMARA DE DIREITO COMERCIAL",
            "5ª CAMARA DO TRT",
            "5ª DELEGACIA DE POLÍCIA",
            "5ª JUIZADO ESPECIAL CÍVEL",
            "5ª PROMOTORIA DE JUSTIÇA",
            "5ª TURMA DE RECURSOS",
            "5ª VARA CÍVEL",
            "5ª VARA CRIMINAL",
            "5ª VARA DO TRABALHO",
            "5ª VARA FEDERAL",
            "5º CARTÓRIO DE PROTESTO",
            "5º OFÍCIO DE REGISTRO DE IMÓVEIS",
            "6ª CÂMARA DE DIREITO CÍVEL",
            "6ª CÂMARA DE DIREITO COMERCIAL DO TRIBUNAL DE JUSTIÇA",
            "6ª DELEGACIA DE POLÍCIA",
            "6ª PROMOTORIA DE JUSTIÇA CÍVEL",
            "6ª TURMA DE RECURSOS",
            "6ª VARA CÍVEL",
            "6ª VARA CRIMINAL",
            "6ª VARA DO TRABALHO",
            "6ª VARA FEDERAL",
            "6º OFÍCIO DE REGISTRO DE IMÓVEIS",
            "7ª DELEGACIA DE POLÍCIA",
            "7ª TURMA DE RECURSOS",
            "7ª VARA CÍVEL",
            "7ª VARA DO TRABALHO",
            "7ª VARA FEDERAL",
            "7º OFÍCIO DE REGISTRO DE IMÓVEIS",
            "8ª DELEGACIA DE POLÍCIA",
            "8ª TURMA DE RECURSOS",
            "8ª VARA CÍVEL",
            "8ª VARA DO TRABALHO",
            "8ª VARA FEDERAL",
            "8º OFÍCIO DE REGISTRO DE IMÓVEIS",
            "9ª VARA CÍVEL",
            "9ª VARA DO TRABALHO",
            "9ª VARA FEDERAL",
            "ACIF",
            "AEROPORTO",
            "AGÊNCIA DA CAIXA ECONOMICA FEDERAL",
            "ANVISA",
            "'ASSEMBLEIA GERAL",
            "BAÍA SUL MEDICAL CENTER",
            "BANCO VIACREDI COOPERATIVA",
            "BATALHAO DA POLICIAL MILITAR AMBIENTAL",
            "CÂMARA CÍVEL ESPECIAL",
            "CÂMARA DE DIREITO ESPECIAL",
            "CÂMARA DOS VEREADORES",
            "CÂMARA ESPECIAL REGIONAL",
            "CAMARA ESPECIAL REGIONAL DE CHAPECO",
            "CAPITANIA DOS PORTOS",
            "CARTÓRIO",
            "CARTÓRIO DE IMÓVEIS",
            "CARTÓRIO DE PROTESTO",
            "CARTÓRIO DE REGISTRO CÍVEL",
            "CARTÓRIO DE REGISTRO DE IMÓVEIS",
            "CARTÓRIO DO 1º REGISTRO DE IMÓVEIS",
            "CARTÓRIO DO 2º REGISTRO DE IMÓVEIS",
            "CARTÓRIO DO 3º REGISTRO DE IMÓVEIS",
            "CARTÓRIO DO 4º REGISTRO DE IMÓVEIS",
            "CARTÓRIO DO 5º REGISTRO DE IMÓVEIS",
            "CARTÓRIO DO 6º REGISTRO DE IMÓVEIS",
            "CARTÓRIO DO 7º REGISTRO DE IMÓVEIS",
            "CARTÓRIO DO 8º REGISTRO DE IMÓVEIS",
            "CASA DA CIDADANIA",
            "CASAN",
            "CEJUSC",
            "CEJUSCON",
            "CEJUSC TRABALHISTA FLORIANÓPOLIS",
            "CELESC",
            "CENTRO DE INFORMÁTICA E AUTOMAÇÃO DO ES- TADO DE SANTA CATARINA - CIASC",
            "CERC",
            "CONSELHO DE RECURSOS PREVIDÊNCIA SOCIAL",
            "CONSELHO DO FUNDO DE REAPARELHAMENTO DA JUSTIÇA",
            "CONSELHO REGIONAL DE ADMINISTRAÇÃO",
            "CONSELHO REGIONAL DE ENGENHARIA E AGRONOMIA",
            "CONSELHO REGIONAL DE MEDICINA",
            "CONSELHO SUPERIOR DO MINISTÉRIO PÚBLICO",
            "CONTADORIA",
            "CORREIOS",
            "CREA",
            "DEIC",
            "DELEGACIA DE POLÍCIA",
            "DELEGACIA DE POLÍCIA CIVIL",
            "DELEGACIA DE POLÍCIA DE FRONTEIRA",
            "DELEGACIA DE POLÍCIA FEDERAL",
            "DELEGACIA DE POLÍCIA GERAL DA POLÍCIA CIVIL",
            "DELEGACIA DE REPRESSÃO A ROUBOS DA CAPITAL",
            "DELEGACIA REGIONAL DO TRABALHI DE SANTA CATARINA",
            "DELEGACIA RODOVIÁRIA FEDERAL",
            "DEPARTAMENTO DE ADMINISTRAÇÃO PRISIONAL",
            "DESLOCAMENTO",
            "DETRAN",
            "DIRETORIA RECURSOS E INCIDENTE",
            "DISTRIBUIÇÃO",
            "DISTRIBUIÇÃO ESTADUAL",
            "DISTRIBUIÇÃO FEDERAL",
            "DIVISÃO DE INVESTIGAÇÕES CRIMINAIS",
            "ESCRITÓRIO DMK",
            "ESCRITÓRIO - FILIAL",
            "ESCRIVANIA DE PAZ DE CACHOEIRA DO BOM JESUS",
            "EXECUÇÃO FISCAL",
            "EXECUÇÕES CONTRA A FAZENDA PÚBLICA E PRECATÓRIOS",
            "FAZENDA PÚBLICA",
            "FERROVIARIA",
            "FORUM",
            "FORÚM CENTRAL - JUSTIÇA",
            "FÓRUM DE DISTRIBUIÇÃO",
            "FÓRUM FEDERAL DA COMARCA",
            "FUJAMA",
            "FUNDAÇÃO DO MEIO AMBIENTE – FÁTIMA TUBARÃO",
            "GERÊNCIA DE GESTÃO DE BENS E SERVIÇOS",
            "GERÊNCIA REGIONAL DO TRABALHO E EMPREGO DE FLORIANOPOLIS",
            "GRUPO DE CAMARAS DE DIREITO",
            "HOSPITAL",
            "IBAMA",
            "ICMBIO AMBIENTAL",
            "INMETRO",
            "INQUÉRITO CIVIL",
            "INSS",
            "IPEM",
            "JEC EDUARDO LUZ",
            "JUIZADDO ESPECIAL CÍVEL E CRIMINAL DA TRINDADE",
            "JUIZADO ESPECIAL CÍVEL",
            "JUIZADO ESPECIAL CÍVEL - CONTINENTE",
            "JUIZADO ESPECIAL CÍVEL E CRIMINAL",
            "JUIZADO ESPECIAL CÍVEL NORTE DA ILHA - SACO GRANDE",
            "JUIZADO ESPECIAL CÍVEL - UFSC",
            "JUIZADO ESPECIAL CIVIL - SANTO ANTÔNIO DE LISBOA",
            "JUIZADO ESPECIAL CRIMINAL E DELITOS DE TRÂNSITO",
            "JUIZADO ESPECIAL FEDERAL",
            "JUNTA COMERCIAL",
            "JUSTIÇA DO TRABALHO",
            "JUSTIÇA ESTADUAL",
            "JUSTIÇA FEDERAL",
            "MINISTÉRIO DO TRABALHO",
            "MINISTÉRIO PÚBLICO DO TRABALHO",
            "MINISTÉRIO PÚBLICO ESTADUAL",
            "MINISTÉRIO PÚBLICO FEDERAL",
            "MPT",
            "NPJ – NÚCLEO DE PRÁTICA JURÍDICA DA FACULDADE SINERGIA",
            "NUCLEO DE CONC. E ORIENTAÇÃO JURÍDICA",
            "NÚCLEO DE PRÁTICA JURÍDICA DA FACULDADE SINERGIA",
            "OAB",
            "OFÍCIO DE REGISTRO DE IMÓVEIS",
            "PACE - POSTO AVANÇADO DE CONCILIAÇÃO EXTRA PROCESSUAL",
            "PGE - Procuradoria Geral do Estado",
            "POLÍCIA AMBIENTAL",
            "POLÍCIA FEDERAL",
            "POLICIA RODOVIARIA FEDERAL",
            "POSTO DE ATENDIMENTO E CONCILIAÇÃO – PAC/FCJ",
            "PRECATÓRIAS, RECUPERAÇÕES JUDICIAIS E FALÊNCIAS",
            "PREFEITURA",
            "PRIMEIRA TURMA DE RECURSOS",
            "PRÓ-CIDADÃO",
            "PROCON",
            "PROCON ESTADUAL",
            "PROCON MUNICIPAL",
            "PROCURADORIA DA REPÚBLICA",
            "PROCURADORIA GERAL DA FAZENDA ESTADUAL",
            "PROCURADORIA GERAL DO ESTADO",
            "PROCURADORIA GERAL DO MUNICÍPIO",
            "PROCURADORIA MUNICIPAL DA PREFEITURA DE FLORIANÓPOLIS",
            "PROCURADORIA REGIONAL DO TRABALHO",
            "PROMOTORIA DE JUSTIÇA",
            "RAÍSSA BRUM SACCOMORI",
            "RECEITA FEDERAL",
            "RECEITA MUNICIPAL",
            "REGISTRO DE IMÓVEIS",
            "RODOVIÁRIA",
            "SECRETARIA DA 1ª TURMA DO TRIBUNAL",
            "SECRETARIA DA 2ª TURMA DO TRIBUNAL",
            "SECRETARIA DA 3ª TURMA DO TRIBUNAL",
            "SECRETARIA DA FAZENDA",
            "SECRETARIA DE ESTADO DA JUSTIÇA E CIDADANIA DE SANTA CATARINA",
            "SECRETARIA DE URBANISMO E SERVIÇOS PÚBLICOS",
            "SECRETARIA EDUCAÇÃO DA PREFEITURA",
            "SECRETARIA MUNICIPAL DE MEIO AMBIENTE",
            "SEFAZ",
            "SETOR DE ARRECARDAÇÃO",
            "SETOR DE ARRECARDAÇÃO DA PREFEITURA",
            "SINDICATO",
            "SOCIESC",
            "SUPERINTENDÊNCIA REGIONAL DO TRABALHO",
            "TCE",
            "TRIBUNAL DE CONTAS DA UNIÃO",
            "TRIBUNAL DE CONTAS DO ESTADO DE SC",
            "TRIBUNAL DE JUSTIÇA",
            "TRIBUNAL REGIONAL DO TRABALHO",
            "TRIBUNAL REGIONAL ELEITORAL",
            "TRIBUNAL REGIONAL FEDERAL",
            "TURMA RECURSAL",
            "UFSC",
            "UNIDADE DA FAZENDA PÚBLICA",
            "UNIDADE DE DIREITO BANCÁRIO",
            "UNIDADE JUDICIÁRIA DE COOPERAÇÃO",
            "UNIDADE JUDICIÁRIA DE COOPERAÇÃO - UNESC",
            "UNISOCIESC DE JOINVILLE",
            "VARA AMBIENTAL",
            "VARA AMBIENTAL FEDERAL",
            "VARA CÍVEL",
            "VARA COMERCIAL",
            "VARA CRIMINAL",
            "VARA DA FAMÍLIA E SUCESSÕES",
            "VARA DA FAZENDA ACIDENTES DO TRAB E REG. PÚBLICO",
            "VARA DA FAZENDA PÚBLICA",
            "VARA DA INFÂNCIA E DA JUVENTUDE",
            "VARA DAS EXECUÇÕES CONTRA A FAZENDA PÚBLICA E PRECATÓRIOS",
            "VARA DE DIREITO BANCÁRIO",
            "VARA DE EXECUÇÃO FISCAIS MUNICIPAIS E ESTADUAIS",
            "VARA DE EXECUÇÃO FISCAL",
            "VARA DE EXECUÇÃO FISCAL ESTADUAL",
            "VARA DE EXECUÇÕES FISCAIS DO MUNICÍPIO",
            "VARA DE EXECUÇÕES PENAIS",
            "VARA DE FALÊNCIA",
            "VARA DE FALÊNCIA PÚBLICA",
            "VARA DE PRECATÓRIAS",
            "VARA DE SUCESSÕES DA CAPITAL",
            "VARA DE SUCESSÕES E REGISTRO PÚBLICO",
            "VARA DO FORO CENTRAL",
            "VARA DO MEIO AMBIENTE",
            "VARA DOS EXECUTIVOS FISCAIS",
            "VARA DO TRABALHO",
            "VARA FEDERAL",
            "VARA FEDERAL AMBIENTAL",
            "VARA FEDERAL CÍVEL",
            "VARA IMETRO",
            "Vara Regional de Cartas precatorias",
            "VARA REGIONAL DE DIREITO BANCÁRIO",
            "VARA REGIONAL DE REC. JUDICIAIS, FALêNCIAS E CONCORDATAS",
            "VARA REGIONAL DO FORO",
            "VARA SEFAZ",
            "VARA ÚNICA"
        );

        foreach ($varas as $var) {
            $vara = new Vara();

            $vara->create([ 
                            'nm_vara_var' => $var,
                            'cd_conta_con' => $cdConta
                ]);
        }
    }
}
