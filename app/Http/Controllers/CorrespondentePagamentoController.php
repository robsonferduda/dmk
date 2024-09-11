<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use App\Conta;
use App\Processo;
use App\AnexoFinanceiro;

class CorrespondentePagamentoController extends Controller
{
    public $conta;

    public function __construct()
    {
        $this->middleware('auth');
        $this->conta = \Session::get('SESSION_CD_CONTA');
    }

    public function pagamentos()
    {
        return view('correspondente/pagamentos');
    }
}