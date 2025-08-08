<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\StatusProcesso;
use App\Events\EventNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redis;

class StatusProcessoController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $status = StatusProcesso::orderBy('nu_ordem_stp')->get();
        return view('processo/status/index', compact('status'));
    }
}