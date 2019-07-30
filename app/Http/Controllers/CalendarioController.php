<?php

namespace App\Http\Controllers;

use Spatie\GoogleCalendar\Event;
use App\Processo;

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
        
        $client = new \Google_Client();

        $scopes = [ \Google_Service_Calendar::CALENDAR ];

        $client->setScopes($scopes);

        $client->setAuthConfig(storage_path().'/app/MyProject-e1636b6e4e7e.json');
        
        $service = new \Google_Service_Calendar($client);

        //$calendar = new \Google_Service_Calendar_Calendar();
        //$calendar->setSummary('teste');
        //dd($service->calendarList->listCalendarList()->getItems()[0]->getId());

        $rule = new \Google_Service_Calendar_AclRule();
        $scope = new \Google_Service_Calendar_AclRuleScope();

        $scope->setType("default");
        //$scope->setValue("rafael01costa@gmail.com");
        $rule->setScope($scope);
        $rule->setRole("reader");

        $createdRule = $service->acl->insert($service->calendarList->listCalendarList()->getItems()[0]->getId(), $rule);

        //$service->acl->delete('h9paatlg82b3tu6dkmhuvumc3k@group.calendar.google.com', 'user:rafael01costa@gmail.com');

        //dd($service->calendarList->listCalendarList()->getItems()[0]->getId());

        dd($service->acl->listAcl($service->calendarList->listCalendarList()->getItems()[0]->getId()));

        dd($calendar->listAcll($service->calendarList->listCalendarList()->getItems()[0]->getId()));
        //$createdCalendar = $service->calendars->insert($calendar);

        dd($service->calendarList->listCalendarList()->getItems()[0]->getId());

        
        exit;
    }
}