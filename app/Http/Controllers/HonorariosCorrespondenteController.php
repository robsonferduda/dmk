<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Correspondente;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class HonorariosCorrespondenteController extends Controller
{

    public $conta;

    public function __construct()
    {
        $this->middleware('auth');        
        $this->conta = \Session::get('SESSION_CD_CONTA');
    }

    public function index()
    {

    }

    public function getHonorariosOrdenados()
    {

    }
}