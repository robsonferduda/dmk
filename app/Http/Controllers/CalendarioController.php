<?php

namespace App\Http\Controllers;

use Spatie\GoogleCalendar\Event;
use App\Processo;
use App\Calendario;
use Illuminate\Http\Request;

class CalendarioController extends Controller
{

    private $cdContaCon;

    public function __construct()
    {
        $this->middleware('auth');
        $this->cdContaCon = \Session::get('SESSION_CD_CONTA');
    }

    public function index()
    {
    
        // Adicionar permissão
        /*$rule = new \Google_Service_Calendar_AclRule();
        $scope = new \Google_Service_Calendar_AclRuleScope();

        $scope->setType("user");
        $scope->setValue("rafael01costa@gmail.com");
        $rule->setScope($scope);
        $rule->setRole("writer");

        $createdRule = $service->acl->insert($calendario->id_calendario_google_cal, $rule);
        echo $createdRule->getId();
        */

        $calendario = Calendario::where('cd_conta_con',$this->cdContaCon)->first();

        $calendar = $this->getServiceCalendario()->calendars->get($calendario->id_calendario_google_cal);

        $optParams['timeMin'] = date("c", strtotime(date('2019-07-30 23:00:00')));
        $optParams['timeMax'] = date("c", strtotime(date('2019-07-31 23:00:00')));

        $events = $this->getServiceCalendario()->events->listEvents($this->getIdCalenderio(), $optParams);

        return view('calendario/index');
    }

    public function adicionar(Request $request){

        $ret = new \StdClass();

        $dtFim = null;

        if(!empty($request->horaInicio) && empty($request->horaFim))
            $request->horaFim = $request->horaInicio;

        if(!empty($request->horaInicio)){
            $dtInicio = str_replace('/', '-', $request->inicio.' '.$request->horaInicio);
            $dtInicio = date("Y-m-d H:i", strtotime($dtInicio));
            $dtInicio = date("c", strtotime($dtInicio));
        }else{
            $dtInicio = str_replace('/', '-', $request->inicio);      
            $dtInicio = date("Y-m-d", strtotime($dtInicio));     
        }   

        if(!empty($request->fim)){
            if(!empty($request->horaFim)){
                $dtFim = str_replace('/', '-', $request->fim.' '.$request->horaFim);
                $dtFim = date("Y-m-d H:i", strtotime($dtFim));
                $dtFim = date("c", strtotime($dtFim));
            }else{
                $dtFim = str_replace('/', '-', $request->fim);
                $dtFim = date("Y-m-d", strtotime($dtFim));    
            }           
        }

        $event = new \Google_Service_Calendar_Event(array(
            'summary' => $request->titulo       
        ));

        if(!empty($request->descricao))
            $event['description'] = $request->descricao;

        if(!empty($request->horaInicio)){
            $event['start'] = array('dateTime' => $dtInicio, 'timeZone' => 'America/Sao_Paulo');
        }else{
             $event['start'] = array('date' => $dtInicio, 'timeZone' => 'America/Sao_Paulo');
        }

        if(!empty($request->horaFim)){
            $event['end'] = array('dateTime' => $dtFim, 'timeZone' => 'America/Sao_Paulo');
        }else{
             $event['end'] = array('date' => $dtFim, 'timeZone' => 'America/Sao_Paulo');
        }
        
        $calendarId = $this->getIdCalenderio();
        $event = $this->getServiceCalendario()->events->insert($calendarId, $event);
        
        if(!empty($event) && !empty($event->id)){
            $ret->id = true;            
        }else{
            $ret->id = false;
            
        }
        
        echo json_encode($ret);
    }

    public function buscarEventosPorData(Request $request){

        $calendario = Calendario::where('cd_conta_con',$this->cdContaCon)->first();

        $optParams['timeMin'] = date("c", strtotime(current(explode("(",$request->start))));
        $optParams['timeMax'] = date("c", strtotime(current(explode("(",$request->end))));

        $events = $this->getServiceCalendario()->events->listEvents($calendario->id_calendario_google_cal, $optParams);

        $eventos = array();
        foreach ($events->getItems() as $event) {
            
            $obj = new \StdClass();
            $obj->title = $event->summary;

            if(!empty($event->start->getDateTime())){
                $obj->start = $event->start->getDateTime();
            }else{
                $obj->start = $event->start->getDate();
            }

            $eventos[] = $obj;
        }

        echo json_encode($eventos);

    }

    private function getIdCalenderio(){

        $calendario = Calendario::where('cd_conta_con',$this->cdContaCon)->first();

        return $calendario->id_calendario_google_cal;
    }

    private function getServiceCalendario(){

        $scopes = [ \Google_Service_Calendar::CALENDAR ];

        $client = new \Google_Client();
        $client->setScopes($scopes);
        $client->setAuthConfig(storage_path().'/app/calendario-dmk.json');

        $service = new \Google_Service_Calendar($client);

        return $service;

    }
}