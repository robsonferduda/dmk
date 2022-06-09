<?php

namespace App\Http\Controllers;

use App\Vara;
use App\Conta;
use App\TipoServico;
use App\Contato;
use App\TipoProcesso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Laracasts\Flash\Flash;

class ManutencaoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function gerarNuVaravar()
    {
        set_time_limit(500);
        ini_set('memory_limit', '1024M');
        $contas = Conta::where('fl_correspondente_con', 'N')
                  ->withTrashed()
                  ->get();

        //dd($contas);

        foreach ($contas as $conta) {
            $varas = Vara::where('cd_conta_con', $conta->cd_conta_con)
                    ->orderBy('created_at')
                    ->withTrashed()
                    ->get();
            
            $i = 1;
            foreach ($varas as $vara) {
                $vara->nu_vara_var = 'V'.$i;
                $vara->save();

                $i++;
            }
        }
    }

    public function gerarNuTipoServicotse()
    {
        set_time_limit(500);
        ini_set('memory_limit', '1024M');
        $contas = Conta::where('fl_correspondente_con', 'N')
                  ->withTrashed()
                  ->get();

        foreach ($contas as $conta) {
            $tipos = TipoServico::where('cd_conta_con', $conta->cd_conta_con)
                    ->orderBy('created_at')
                    ->withTrashed()
                    ->get();
            
            $i = 1;
            foreach ($tipos as $tipo) {
                $tipo->nu_tipo_servico_tse = 'TS'.$i;
                $tipo->save();

                $i++;
            }
        }
    }

    public function gerarNuContatocot()
    {
        set_time_limit(500);
        ini_set('memory_limit', '1024M');
        $contas = Conta::where('fl_correspondente_con', 'N')
                  ->withTrashed()
                  ->get();

        foreach ($contas as $conta) {
            $contatos = Contato::where('cd_conta_con', $conta->cd_conta_con)
                    ->orderBy('created_at')
                    ->withTrashed()
                    ->get();
            
            $i = 1;
            foreach ($contatos as $contato) {
                $contato->nu_contato_cot = 'CO'.$i;
                $contato->save();

                $i++;
            }
        }
    }

    public function gerarTipoProcessotpo()
    {
        set_time_limit(500);
        ini_set('memory_limit', '1024M');
        $contas = Conta::where('fl_correspondente_con', 'N')
                  ->withTrashed()
                  ->get();

        foreach ($contas as $conta) {
            $tiposProcesso = TipoProcesso::where('cd_conta_con', $conta->cd_conta_con)
                    ->orderBy('created_at')
                    ->withTrashed()
                    ->get();
            
            $i = 1;
            foreach ($tiposProcesso as $tipoProcesso) {
                $tipoProcesso->nu_tipo_processo_tpo = 'TP'.$i;
                $tipoProcesso->save();

                $i++;
            }
        }
    }
}
