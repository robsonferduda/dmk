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
    
        // $rule = new \Google_Service_Calendar_AclRule();
        // $scope = new \Google_Service_Calendar_AclRuleScope();

        // $scope->setType("default");
        // $rule->setScope($scope);
        // $rule->setRole("reader");

        // $createdRule = $this->getServiceCalendario()->acl->insert($this->getIdCalenderio(), $rule);
       
        // $calendar = $this->getServiceCalendario()->calendars->get($this->getIdCalenderio());
        // $acl = $this->getServiceCalendario()->acl->listAcl($this->getIdCalenderio());

        //$this->getServiceCalendario()->acl->delete($this->getIdCalenderio(), 'default');


        //dd($acl);


        return view('calendario/index');
    }

    public function adicionar(Request $request){

        $ret = new \StdClass();

        if(!\Helper::validaData($request->inicio)){
            $ret->id  = false;
            $ret->msg = 'Data início inválida.';
            echo json_encode($ret);
            exit;
        }

        $dtFim = null;

        if(!empty($request->horaInicio) && empty($request->horaFim))
            $request->horaFim = $request->horaInicio;

        if(!empty($request->horaInicio)){

            if(!\Helper::validaHoras($request->horaInicio)){
                $ret->id  = false;
                $ret->msg = 'Hora início inválida.';
                echo json_encode($ret);
                exit;
            }

            $dtInicio = str_replace('/', '-', $request->inicio.' '.$request->horaInicio);
            $dtInicio = date("Y-m-d H:i", strtotime($dtInicio));
            $dtInicio = date("c", strtotime($dtInicio));
        }else{
            $dtInicio = str_replace('/', '-', $request->inicio);      
            $dtInicio = date("Y-m-d", strtotime($dtInicio));     
        }   

        if(!empty($request->fim)){

            if(!\Helper::validaData($request->fim)){
                $ret->id  = false;
                $ret->msg = 'Data fim inválida.';
                echo json_encode($ret);
                exit;
            }

            if(!empty($request->horaFim)){

                if(!\Helper::validaHoras($request->horaFim)){
                    $ret->id  = false;
                    $ret->msg = 'Hora fim inválida.';
                    echo json_encode($ret);
                    exit;
                }

                $dtFim = str_replace('/', '-', $request->fim.' '.$request->horaFim);
                $dtFim = date("Y-m-d H:i", strtotime($dtFim));
                $dtFim = date("c", strtotime($dtFim));
            }else{
                $dtFim = str_replace('/', '-', $request->fim);
                $dtFim = date("Y-m-d", strtotime($dtFim.' + 1 days'));    
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

     public function editar(Request $request){

        $ret = new \StdClass();

        if(!\Helper::validaData($request->inicio)){
            $ret->id  = false;
            $ret->msg = 'Data início inválida.';
            echo json_encode($ret);
            exit;
        }

        $dtFim = null;

        if(!empty($request->horaInicio) && empty($request->horaFim))
            $request->horaFim = $request->horaInicio;

        if(!empty($request->horaInicio)){

            if(!\Helper::validaHoras($request->horaInicio)){
                $ret->id  = false;
                $ret->msg = 'Hora início inválida.';
                echo json_encode($ret);
                exit;
            }

            $dtInicio = str_replace('/', '-', $request->inicio.' '.$request->horaInicio);
            $dtInicio = date("Y-m-d H:i", strtotime($dtInicio));
            $dtInicio = date("c", strtotime($dtInicio));
        }else{
            $dtInicio = str_replace('/', '-', $request->inicio);      
            $dtInicio = date("Y-m-d", strtotime($dtInicio));     
        }   

        if(!empty($request->fim)){

            if(!\Helper::validaData($request->fim)){
                $ret->id  = false;
                $ret->msg = 'Data fim inválida.';
                echo json_encode($ret);
                exit;
            }

            if(!empty($request->horaFim)){

                if(!\Helper::validaHoras($request->horaFim)){
                    $ret->id  = false;
                    $ret->msg = 'Hora fim inválida.';
                    echo json_encode($ret);
                    exit;
                }

                $dtFim = str_replace('/', '-', $request->fim.' '.$request->horaFim);
                $dtFim = date("Y-m-d H:i", strtotime($dtFim));
                $dtFim = date("c", strtotime($dtFim));
            }else{
                $dtFim = str_replace('/', '-', $request->fim);
                $dtFim = date("Y-m-d", strtotime($dtFim.' + 1 days'));    
            }           
        }

        $calendarId = $this->getIdCalenderio();
        $event = $this->getServiceCalendario()->events->get($calendarId, $request->id);

        $event->summary = $request->titulo;

        if(!empty($request->descricao))
            $event->description = $request->descricao;

        if(!empty($request->horaInicio)){
            $event->start = array('dateTime' => $dtInicio, 'timeZone' => 'America/Sao_Paulo');
        }else{
            $event->start = array('date' => $dtInicio, 'timeZone' => 'America/Sao_Paulo');
        }

        if(!empty($request->horaFim)){
            $event->end = array('dateTime' => $dtFim, 'timeZone' => 'America/Sao_Paulo');
        }else{
             $event->end = array('date' => $dtFim, 'timeZone' => 'America/Sao_Paulo');
        }
        
       
        $event = $this->getServiceCalendario()->events->update($calendarId,$event->getId(),$event);
        
        if(!empty($event) && !empty($event->id)){
            $ret->id = true;            
        }else{
            $ret->id = false;
            
        }
        
        echo json_encode($ret);
    }

    public function excluir(Request $request){

        $ret = new \StdClass();

        $this->getServiceCalendario()->events->delete($this->getIdCalenderio(), $request->id);
        $ret->id = true;
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
            $obj->description = $event->description;
            $obj->googleCalendarId = $event->id;

            if(!empty($event->start->getDateTime())){
                $obj->start = $event->start->getDateTime();
            }else{
                $obj->start = $event->start->getDate();
            }

            if(!empty($event->end->getDateTime())){
                $obj->end = $event->end->getDateTime();
            }else{
                $obj->end = $event->end->getDate();
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