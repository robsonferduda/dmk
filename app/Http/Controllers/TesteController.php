<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\RegistroBancario;
use App\Events\EventNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redis;

class TesteController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        event(new EventNotification(array('canal' => 'notificacao', 'conta' => 999, 'total' => 0, 'mensagens' => "")));
    }

}