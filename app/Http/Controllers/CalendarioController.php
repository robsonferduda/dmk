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
        $events = [];

        $processos = Processo::all();

        foreach ($processos as $processo) {

            //dd($processo);
            $events[] = \Calendar::event(
                $processo->nu_processo_pro, //event title
                false, //full day event?
                $processo->dt_prazo_fatal_pro, //start time (you can also use Carbon instead of DateTime)
                $processo->dt_prazo_fatal_pro, //end time (you can also use Carbon instead of DateTime)
                0, //optionally, you can specify an event ID
                [
                    'url' => 'http://192.168.99.100/processos/detalhes/'.\Crypt::encrypt($processo->cd_processo_pro),
                   // 'color' => '#800'
                ]
            );
        }

        // $events[] = \Calendar::event(
        //     "Valentine's Day", //event title
        //     true, //full day event?
        //     new \DateTime('2015-02-14'), //start time (you can also use Carbon instead of DateTime)
        //     new \DateTime('2015-02-14'), //end time (you can also use Carbon instead of DateTime)
        //     'stringEventId' //optionally, you can specify an event ID
        // );

        //$eloquentEvent = EventModel::first(); //EventModel implements MaddHatter\LaravelFullcalendar\Event

        // get all future events on a calendar
        $googleEvents = Event::get();
        //dd($events);
        foreach($googleEvents as $event){
            //print_r($event->start);exit;
            $events[] = \Calendar::event(
                $event->summary, //event title
                false, //full day event?
                $event->start->getDateTime(), //start time (you can also use Carbon instead of DateTime)
                $event->end->getDateTime(), //end time (you can also use Carbon instead of DateTime)
                0, //optionally, you can specify an event ID
                [
                    'url' => 'http://full-calendar.io',
                    'color' => '#800'
                ]
            );
        }
        
        $calendar = \Calendar::addEvents($events)->setOptions(['lang' => 'pt-br']);

        return view('calendario/index', compact('calendar'));
        dd( $calendar);
        
        
        exit;
    }
}