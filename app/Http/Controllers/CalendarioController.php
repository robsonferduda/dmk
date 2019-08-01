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

    public function buscarEventosPorData(){

        echo json_encode('teste');

        // $optParams['timeMin'] = date("c", strtotime(date('2019-07-30 23:00:00')));
        // $optParams['timeMax'] = date("c", strtotime(date('2019-09-30 23:00:00')));

        // $events = $service->events->listEvents('primary', $optParams);

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