<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\RegistroBancario;
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
        $redis = Redis::connection();

        $redis->set('user_details', json_encode([
                        'first_name' => 'Alex', 
                        'last_name' => 'Richards'
                    ])
                );
    }

}