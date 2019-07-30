<?php

namespace App\Http\Controllers;

use Spatie\GoogleCalendar\Event;
use App\Processo;
use App\Traits\BootConta;

class CalendarioController extends Controller
{

    use BootConta;
    
    private $cdContaCon;

    public function __construct()
    {
        $this->middleware('auth');
        $this->cdContaCon = \Session::get('SESSION_CD_CONTA');
    }

    public function index()
    {
        
        $this->bootConta($this->cdContaCon);

        
        exit;
    }
}