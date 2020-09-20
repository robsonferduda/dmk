<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use App\ProcessoTaxaHonorario;
use App\Balanco;
use App\CategoriaDespesa;
use App\Conta;
use App\TipoDespesa;
use App\Processo;
use App\Exports\BalancoDetalhadoExport;
use App\Exports\BalancoSumarizadoExport;
use App\Despesa;
use App\AnexoFinanceiro;
use App\BaixaHonorario;
use Illuminate\Support\Facades\Response;
use Hazzard\Filepicker\Handler;
use Hazzard\Filepicker\Uploader;
use Hazzard\Config\Repository as Config;
use Intervention\Image\ImageManager;

class FinanceiroController extends Controller
{
    public $conta;

    public function __construct()
    {
        $this->middleware('auth');
        $this->conta = \Session::get('SESSION_CD_CONTA');
    }

    public function balancoIndex()
    {
        return view('financeiro/balanco');
    }

    public function entradaIndex()
    {
        if (!session('flBuscar')) {
            \Session::put('dtInicio', date('d/m/Y', strtotime(date("Y-m-01"))));
            \Session::put('dtFim', date('d/m/Y', strtotime(date("Y-m-t"))));
            \Session::put('dtInicioBaixa', null);
            \Session::put('dtFimBaixa', null);
            \Session::put('cliente', null);
            \Session::put('nmCliente', null);
            \Session::put('todas', null);
            \Session::put('verificadas', null);
            \Session::put('parcialmente', null);
            \Session::put('pago', null);
            \Session::put('nenhum', null);

            $dtInicio = date('d/m/Y', strtotime(date("Y-m-01")));
            $dtFim = date('d/m/Y', strtotime(date("Y-m-t")));
            $dtInicioBaixa = '';
            $dtFimBaixa = '';
            $cliente = '';
            $nmCliente = '';
            $todas = '';
            $verificadas = '';
            $parcialmente = '';
            $pago = '';
            $nenhum = '';
        } else {
            $dtInicio = session('dtInicio');
            $dtFim = session('dtFim');
            $dtInicioBaixa = session('dtInicioBaixa');
            $dtFimBaixa = session('dtFimBaixa');
            $cliente = session('cliente');
            $nmCliente = session('nmCliente');
            $todas = session('todas');
            $verificadas = session('verificadas');
            $parcialmente = session('parcialmente');
            $pago = session('pago');
            $nenhum = session('nenhum');
        }

        $entradas = ProcessoTaxaHonorario::with(array('tipoServico' => function ($query) {
            $query->select('cd_tipo_servico_tse', 'nm_tipo_servico_tse');
        }))->with(array('processo' => function ($query) {
            $query->select('cd_processo_pro', 'nu_processo_pro', 'cd_cliente_cli', 'cd_correspondente_cor', 'dt_prazo_fatal_pro');
            $query->with(array('correspondente' => function ($query) {
                $query->select('cd_conta_con');
                $query->with(array('contaCorrespondenteTrashedToo' => function ($query) {
                    $query->select('nm_conta_correspondente_ccr', 'cd_correspondente_cor');
                }));
            }));
            $query->with(array('cliente' => function ($query) {
                $query->select('cd_cliente_cli', 'nm_razao_social_cli');
                $query->where('cd_conta_con', $this->conta);
            }));
            $query->with(array('tiposDespesa' => function ($query) {
                $query->wherePivot('cd_tipo_entidade_tpe', \TipoEntidade::CLIENTE);
                $query->wherePivot('fl_despesa_reembolsavel_pde', 'S');
            }));
        }))->has('processo');
                        
        $entradas = $entradas->whereHas('processo', function ($query) use ($dtInicio, $dtFim) {
            $query->when(!empty($dtInicio) && !empty($dtFim), function ($query) use ($dtInicio,$dtFim) {
                $dtInicio = date('Y-m-d', strtotime(str_replace('/', '-', $dtInicio)));
                $dtFim    = date('Y-m-d', strtotime(str_replace('/', '-', $dtFim)));
                return $query->whereBetween('dt_prazo_fatal_pro', [$dtInicio,$dtFim]);
            });
            $query->when(!empty($dtInicio) && empty($dtFim), function ($query) use ($dtInicio) {
                $dtInicio = date('Y-m-d', strtotime(str_replace('/', '-', $dtInicio)));
                return $query->where('dt_prazo_fatal_pro', $dtInicio);
            });
            $query->when(empty($dtInicio) && !empty($dtFim), function ($query) use ($dtFim) {
                $dtFim = date('Y-m-d', strtotime(str_replace('/', '-', $dtFim)));
                return $query->where('dt_prazo_fatal_pro', $dtFim);
            });
        });

        if (!empty($dtInicioBaixa) || !empty($dtFimBaixa)) {
            $entradas = $entradas->whereHas('baixaHonorario', function ($query) use ($dtInicioBaixa,$dtFimBaixa) {
                $query->when(!empty($dtInicioBaixa) && !empty($dtFimBaixa), function ($query) use ($dtInicioBaixa,$dtFimBaixa) {
                    $dtInicioBaixa = date('Y-m-d', strtotime(str_replace('/', '-', $dtInicioBaixa)));
                    $dtFimBaixa    = date('Y-m-d', strtotime(str_replace('/', '-', $dtFimBaixa)));
                    return $query->whereBetween('dt_baixa_honorario_bho', [$dtInicioBaixa,$dtFimBaixa]);
                })
                ->when(!empty($dtInicioBaixa) && empty($dtFimBaixa), function ($query) use ($dtInicioBaixa) {
                    $dtInicioBaixa = date('Y-m-d', strtotime(str_replace('/', '-', $dtInicioBaixa)));
                    return $query->where('dt_baixa_honorario_bho', $dtInicioBaixa);
                })

                ->when(empty($dtInicioBaixa) && !empty($dtFimBaixa), function ($query) use ($dtFimBaixa) {
                    $dtFimBaixa = date('Y-m-d', strtotime(str_replace('/', '-', $dtFimBaixa)));
                    return $query->where('dt_baixa_honorario_bho', $dtFimBaixa);
                });
            });
        } else {
            $entradas = $entradas->with('baixaHonorario');
        }

        if (!empty($cliente)) {
            $entradas = $entradas->whereHas('processo', function ($query) use ($cliente) {
                $query->where('cd_cliente_cli', $cliente);
            });
        }

        $opcoes = array();

        if (!empty($parcialmente)) {
            $opcoes[]  = 'P';
        }

        if (!empty($pago)) {
            $opcoes[]  = 'S';
        }

        if (!empty($nenhum)) {
            $opcoes[]  = 'N';
        }

        if (empty($opcoes)) {
            $entradas = $entradas->whereIn('fl_pago_cliente_pth', ['N','P']);
        } else {
            $entradas = $entradas->whereIn('fl_pago_cliente_pth', $opcoes);
        }

        $entradas = $entradas->where('cd_conta_con', $this->conta)->select('cd_processo_taxa_honorario_pth', 'vl_taxa_honorario_cliente_pth', 'vl_taxa_honorario_correspondente_pth', 'cd_processo_pro', 'cd_tipo_servico_tse', 'fl_pago_cliente_pth', 'vl_taxa_cliente_pth', 'nu_cliente_nota_fiscal_pth')->get()->sortBy('processo.dt_prazo_fatal_pro');

        return view('financeiro/entrada', ['entradas' => $entradas])->with(['dtInicio' => $dtInicio,'dtFim' => $dtFim,'cliente' => $cliente, 'nmCliente' => $nmCliente]);
    }

    public function saidaIndex()
    {
        if (!session('flBuscar')) {
            \Session::put('dtInicio', date('d/m/Y', strtotime(date("Y-m-01"))));
            \Session::put('dtFim', date('d/m/Y', strtotime(date("Y-m-t"))));
            \Session::put('dtInicioBaixa', null);
            \Session::put('dtFimBaixa', null);
            \Session::put('correspondente', null);
            \Session::put('nmCorrespondente', null);
            \Session::put('todas', null);
            \Session::put('verificadas', null);
            \Session::put('parcialmente', null);
            \Session::put('pago', null);
            \Session::put('nenhum', null);

            $dtInicio = date('d/m/Y', strtotime(date("Y-m-01")));
            $dtFim = date('d/m/Y', strtotime(date("Y-m-t")));
            $dtInicioBaixa = '';
            $dtFimBaixa = '';
            $correspondente = '';
            $nmCorrespondente = '';
            $todas = '';
            $verificadas = '';
            $parcialmente = '';
            $pago = '';
            $nenhum = '';
        } else {
            $dtInicio = session('dtInicio');
            $dtFim = session('dtFim');
            $dtInicioBaixa = session('dtInicioBaixa');
            $dtFimBaixa = session('dtFimBaixa');
            $correspondente = session('correspondente');
            $nmCorrespondente = session('nmCorrespondente');
            $todas = session('todas');
            $verificadas = session('verificadas');
            $parcialmente = session('parcialmente');
            $pago = session('pago');
            $nenhum = session('nenhum');
        }

        $saidas = ProcessoTaxaHonorario::with(array('tipoServicoCorrespondente' => function ($query) {
            $query->select('cd_tipo_servico_tse', 'nm_tipo_servico_tse');
        }))->whereHas('processo', function ($query) {
            $query->has('correspondente');
            $query->select('cd_processo_pro', 'nu_processo_pro', 'cd_cliente_cli', 'cd_correspondente_cor', 'dt_prazo_fatal_pro');
        })->whereNotNull('cd_tipo_servico_correspondente_tse');
        
        $saidas = $saidas->whereHas('processo', function ($query) use ($dtInicio, $dtFim) {
            $query->when(!empty($dtInicio) && !empty($dtFim), function ($query) use ($dtInicio,$dtFim) {
                $dtInicio = date('Y-m-d', strtotime(str_replace('/', '-', $dtInicio)));
                $dtFim    = date('Y-m-d', strtotime(str_replace('/', '-', $dtFim)));
                return $query->whereBetween('dt_prazo_fatal_pro', [$dtInicio,$dtFim]);
            });
            $query->when(!empty($dtInicio) && empty($dtFim), function ($query) use ($dtInicio) {
                $dtInicio = date('Y-m-d', strtotime(str_replace('/', '-', $dtInicio)));
                return $query->where('dt_prazo_fatal_pro', $dtInicio);
            });
            $query->when(empty($dtInicio) && !empty($dtFim), function ($query) use ($dtFim) {
                $dtFim = date('Y-m-d', strtotime(str_replace('/', '-', $dtFim)));
                return $query->where('dt_prazo_fatal_pro', $dtFim);
            });
        });

        if (!empty($dtInicioBaixa) || !empty($dtFimBaixa)) {
            $saidas = $saidas->whereHas('baixaHonorario', function ($query) use ($dtInicioBaixa,$dtFimBaixa) {
                $query->when(!empty($dtInicioBaixa) && !empty($dtFimBaixa), function ($query) use ($dtInicioBaixa,$dtFimBaixa) {
                    $dtInicioBaixa = date('Y-m-d', strtotime(str_replace('/', '-', $dtInicioBaixa)));
                    $dtFimBaixa    = date('Y-m-d', strtotime(str_replace('/', '-', $dtFimBaixa)));
                    return $query->whereBetween('dt_baixa_honorario_bho', [$dtInicioBaixa,$dtFimBaixa]);
                })
                ->when(!empty($dtInicioBaixa) && empty($dtFimBaixa), function ($query) use ($dtInicioBaixa) {
                    $dtInicioBaixa = date('Y-m-d', strtotime(str_replace('/', '-', $dtInicioBaixa)));
                    return $query->where('dt_baixa_honorario_bho', $dtInicioBaixa);
                })

                ->when(empty($dtInicioBaixa) && !empty($dtFimBaixa), function ($query) use ($dtFimBaixa) {
                    $dtFimBaixa = date('Y-m-d', strtotime(str_replace('/', '-', $dtFimBaixa)));
                    return $query->where('dt_baixa_honorario_bho', $dtFimBaixa);
                });
            });
        } else {
            $saidas = $saidas->with('baixaHonorario');
        }

        if (!empty($correspondente)) {
            $saidas = $saidas->whereHas('processo', function ($query) use ($correspondente) {
                $query->where('cd_correspondente_cor', $correspondente);
            });
        }

        $opcoes = array();

        if (!empty($parcialmente)) {
            $opcoes[]  = 'P';
        }

        if (!empty($pago)) {
            $opcoes[]  = 'S';
        }

        if (!empty($nenhum)) {
            $opcoes[]  = 'N';
        }

        if (empty($opcoes)) {
            $saidas = $saidas->whereIn('fl_pago_correspondente_pth', ['N','P']);
        } else {
            $saidas = $saidas->whereIn('fl_pago_correspondente_pth', $opcoes);
        }


        $saidas = $saidas->where('cd_conta_con', $this->conta)->select('cd_processo_taxa_honorario_pth', 'vl_taxa_honorario_cliente_pth', 'vl_taxa_honorario_correspondente_pth', 'cd_processo_pro', 'cd_tipo_servico_correspondente_tse', 'fl_pago_correspondente_pth')->get()->sortBy('processo.dt_prazo_fatal_pro');
        //dd($saidas);
        \Session::put('flBuscar', false);
        //dd($saidas[0]->processo->processoDespesa);

        return view('financeiro/saida', ['saidas' => $saidas])->with(['dtInicio' => $dtInicio,'dtFim' => $dtFim,'correspondente' => $correspondente, 'nmCorrespondente' => $nmCorrespondente]);
    }

    public function balancoBuscar(Request $request)
    {
        $dtInicio       = $request->dtInicio;
        $dtFim          = $request->dtFim;
        $dtInicioBaixa  = $request->dtInicioBaixa;
        $dtFimBaixa     = $request->dtFimBaixa;
        $dtLancamentoInicio = $request->dtLancamentoInicio;
        $dtLancamentoFim    = $request->dtLancamentoFim;
        $dtPagamentoInicio  = $request->dtPagamentoInicio;
        $dtPagamentoFim     = $request->dtPagamentoFim;

        $finalizado     = $request->finalizado;
        $cliente        = $request->cd_cliente_cli;
        $correspondente = $request->cd_correspondente_cor;
        $tipo           = $request->tipo;
       
        $entradasVetor = [];

        if (!empty($request->entradas)) {
            $entradas = Processo::whereHas('honorario', function ($query) use ($dtInicioBaixa,$dtFimBaixa) {
                if (!empty($dtInicioBaixa) || !empty($dtFimBaixa)) {
                    $query->whereHas('baixaHonorario', function ($query) use ($dtInicioBaixa,$dtFimBaixa) {
                        $query->when(!empty($dtInicioBaixa) && !empty($dtFimBaixa), function ($query) use ($dtInicioBaixa,$dtFimBaixa) {
                            $dtInicioBaixa = date('Y-m-d', strtotime(str_replace('/', '-', $dtInicioBaixa)));
                            $dtFimBaixa    = date('Y-m-d', strtotime(str_replace('/', '-', $dtFimBaixa)));
                            return $query->whereBetween('dt_baixa_honorario_bho', [$dtInicioBaixa,$dtFimBaixa]);
                        })
                                            ->when(!empty($dtInicioBaixa) && empty($dtFimBaixa), function ($query) use ($dtInicioBaixa) {
                                                $dtInicioBaixa = date('Y-m-d', strtotime(str_replace('/', '-', $dtInicioBaixa)));
                                                return $query->where('dt_baixa_honorario_bho', $dtInicioBaixa);
                                            })

                                            ->when(empty($dtInicioBaixa) && !empty($dtFimBaixa), function ($query) use ($dtFimBaixa) {
                                                $dtFimBaixa = date('Y-m-d', strtotime(str_replace('/', '-', $dtFimBaixa)));
                                                return $query->where('dt_baixa_honorario_bho', $dtFimBaixa);
                                            });
                    });
                } else {
                    $query->with('baixaHonorario');
                }
            })
                                 ->with('cliente')
                                 ->with('tiposDespesa')
                                 ->where('cd_conta_con', $this->conta)
                                  ->when(!empty($cliente), function ($query) use ($cliente) {
                                      return $query->where('cd_cliente_cli', $cliente);
                                  })
                                 ->when(!empty($correspondente), function ($query) use ($correspondente) {
                                     return $query->where('cd_correspondente_cor', $correspondente);
                                 })
                                 ->when(!empty($finalizado), function ($query) {
                                     return $query->where('cd_status_processo_stp', \StatusProcesso::FINALIZADO);
                                 })
                                 ->when(!empty($dtInicio) && !empty($dtFim), function ($query) use ($dtInicio,$dtFim) {
                                     $dtInicio = date('Y-m-d', strtotime(str_replace('/', '-', $dtInicio)));
                                     $dtFim    = date('Y-m-d', strtotime(str_replace('/', '-', $dtFim)));
                                     return $query->whereBetween('dt_prazo_fatal_pro', [$dtInicio,$dtFim]);
                                 })
                                 ->when(!empty($dtInicio) && empty($dtFim), function ($query) use ($dtInicio) {
                                     $dtInicio = date('Y-m-d', strtotime(str_replace('/', '-', $dtInicio)));
                                     return $query->where('dt_prazo_fatal_pro', $dtInicio);
                                 })
                                 ->when(empty($dtInicio) && !empty($dtFim), function ($query) use ($dtFim) {
                                     $dtFim = date('Y-m-d', strtotime(str_replace('/', '-', $dtFim)));
                                     return $query->where('dt_prazo_fatal_pro', $dtFim);
                                 })
                                 ->get();
        } else {
            $entradas = array();
        }

        foreach ($entradas as $entrada) {
            $totalDespesas = 0;
            $total = 0;
          
            if ($tipo == 'P') {
                foreach ($entrada->tiposDespesa as $despesa) {
                    if ($despesa->pivot->cd_tipo_entidade_tpe == \TipoEntidade::CLIENTE && $despesa->pivot->fl_despesa_reembolsavel_pde == 'S') {
                        $totalDespesas += $despesa->pivot->vl_processo_despesa_pde;
                    }
                }

                $entrada->honorario->vl_taxa_honorario_cliente_pth = $entrada->honorario->vl_taxa_honorario_cliente_pth - (($entrada->honorario->vl_taxa_honorario_cliente_pth * $entrada->honorario->vl_taxa_cliente_pth)/100);

                if (array_key_exists($entrada->cliente->cd_cliente_cli, $entradasVetor)) {
                    $entrada->honorario->vl_taxa_honorario_cliente_pth += round($entradasVetor[$entrada->cliente->cd_cliente_cli]['valor'], 2);
                    $totalDespesas += $entradasVetor[$entrada->cliente->cd_cliente_cli]['despesa'];
                }

                $total = $entrada->honorario->vl_taxa_honorario_cliente_pth + $totalDespesas;

                $entradasVetor[$entrada->cliente->cd_cliente_cli] = array('cliente' => $entrada->cliente->nm_razao_social_cli, 'valor' => $entrada->honorario->vl_taxa_honorario_cliente_pth, 'despesa' => $totalDespesas, 'total' => $total);
            } else {
                $total = $entrada->honorario->baixaHonorario->where('cd_tipo_financeiro_tfn', \TipoFinanceiro::ENTRADA)->where('cd_tipo_baixa_honorario_bho', \TipoBaixaHonorario::HONORARIO)->sum('vl_baixa_honorario_bho');

                if (!empty($total)) {
                    $total = $total - (($total * $entrada->honorario->vl_taxa_cliente_pth)/100);
                }


                $totalDespesas = $entrada->honorario->baixaHonorario->where('cd_tipo_financeiro_tfn', \TipoFinanceiro::ENTRADA)->where('cd_tipo_baixa_honorario_bho', \TipoBaixaHonorario::DESPESA)->sum('vl_baixa_honorario_bho');


                if (array_key_exists($entrada->cliente->cd_cliente_cli, $entradasVetor)) {
                    $total += round($entradasVetor[$entrada->cliente->cd_cliente_cli]['valor'], 2);
                    $totalDespesas += $entradasVetor[$entrada->cliente->cd_cliente_cli]['despesa'];
                }

                $entradasVetor[$entrada->cliente->cd_cliente_cli] = array('cliente' => $entrada->cliente->nm_razao_social_cli, 'valor' => $total, 'despesa' => $totalDespesas, 'total' => 0);
            }
        }

        // exit;

        $saidasVetor = [];

        if (!empty($request->saidas)) {
            $saidas = Processo::whereHas('honorario.tipoServicoCorrespondente')
                                ->whereHas('honorario', function ($query) use ($dtInicioBaixa,$dtFimBaixa) {
                                    if (!empty($dtInicioBaixa) || !empty($dtFimBaixa)) {
                                        $query->whereHas('baixaHonorario', function ($query) use ($dtInicioBaixa,$dtFimBaixa) {
                                            $query->when(!empty($dtInicioBaixa) && !empty($dtFimBaixa), function ($query) use ($dtInicioBaixa,$dtFimBaixa) {
                                                $dtInicioBaixa = date('Y-m-d', strtotime(str_replace('/', '-', $dtInicioBaixa)));
                                                $dtFimBaixa    = date('Y-m-d', strtotime(str_replace('/', '-', $dtFimBaixa)));
                                                return $query->whereBetween('dt_baixa_honorario_bho', [$dtInicioBaixa,$dtFimBaixa]);
                                            })
                                            ->when(!empty($dtInicioBaixa) && empty($dtFimBaixa), function ($query) use ($dtInicioBaixa) {
                                                $dtInicioBaixa = date('Y-m-d', strtotime(str_replace('/', '-', $dtInicioBaixa)));
                                                return $query->where('dt_baixa_honorario_bho', $dtInicioBaixa);
                                            })

                                            ->when(empty($dtInicioBaixa) && !empty($dtFimBaixa), function ($query) use ($dtFimBaixa) {
                                                $dtFimBaixa = date('Y-m-d', strtotime(str_replace('/', '-', $dtFimBaixa)));
                                                return $query->where('dt_baixa_honorario_bho', $dtFimBaixa);
                                            });
                                        });
                                    } else {
                                        $query->with('baixaHonorario');
                                    }
                                })
                                ->whereHas('correspondente')
                                ->with('tiposDespesa')
                                ->where('cd_conta_con', $this->conta)
                                ->when(!empty($cliente), function ($query) use ($cliente) {
                                    return $query->where('cd_cliente_cli', $cliente);
                                })
                                ->when(!empty($correspondente), function ($query) use ($correspondente) {
                                    return $query->where('cd_correspondente_cor', $correspondente);
                                })
                                ->when(!empty($finalizado), function ($query) {
                                    return $query->where('cd_status_processo_stp', \StatusProcesso::FINALIZADO);
                                })
                                ->when(!empty($dtInicio) && !empty($dtFim), function ($query) use ($dtInicio,$dtFim) {
                                    $dtInicio = date('Y-m-d', strtotime(str_replace('/', '-', $dtInicio)));
                                    $dtFim    = date('Y-m-d', strtotime(str_replace('/', '-', $dtFim)));
                                    return $query->whereBetween('dt_prazo_fatal_pro', [$dtInicio,$dtFim]);
                                })
                                ->when(!empty($dtInicio) && empty($dtFim), function ($query) use ($dtInicio) {
                                    $dtInicio = date('Y-m-d', strtotime(str_replace('/', '-', $dtInicio)));
                                    return $query->where('dt_prazo_fatal_pro', $dtInicio);
                                })
                                ->when(empty($dtInicio) && !empty($dtFim), function ($query) use ($dtFim) {
                                    $dtFim = date('Y-m-d', strtotime(str_replace('/', '-', $dtFim)));
                                    return $query->where('dt_prazo_fatal_pro', $dtFim);
                                })
                                ->get();
        } else {
            $saidas = array();
        }

        foreach ($saidas as $saida) {
            $totalDespesas = 0;
            $total = 0;

            if ($tipo == 'P') {
                foreach ($saida->tiposDespesa as $despesa) {
                    if ($despesa->pivot->cd_tipo_entidade_tpe == \TipoEntidade::CORRESPONDENTE && $despesa->pivot->fl_despesa_reembolsavel_pde == 'S') {
                        $totalDespesas += $despesa->pivot->vl_processo_despesa_pde;
                    }
                }

                if (array_key_exists($saida->correspondente->cd_conta_con, $saidasVetor)) {
                    $saida->honorario->vl_taxa_honorario_correspondente_pth += round($saidasVetor[$saida->correspondente->cd_conta_con]['valor'], 2);
                    $totalDespesas += $saidasVetor[$saida->correspondente->cd_conta_con]['despesa'];
                }

                $total = $saida->honorario->vl_taxa_honorario_correspondente_pth + $totalDespesas;

                $saidasVetor[$saida->correspondente->cd_conta_con] = array('correspondente' => $saida->correspondente->contaCorrespondenteTrashedToo->nm_conta_correspondente_ccr, 'valor' => $saida->honorario->vl_taxa_honorario_correspondente_pth, 'despesa' => $totalDespesas, 'total' => $total);
            } else {
                $total = $saida->honorario->baixaHonorario->where('cd_tipo_financeiro_tfn', \TipoFinanceiro::SAIDA)->sum('vl_baixa_honorario_bho');

                if (array_key_exists($saida->correspondente->cd_conta_con, $saidasVetor)) {
                    $total += round($saidasVetor[$saida->correspondente->cd_conta_con]['valor'], 2);
                }

                $saidasVetor[$saida->correspondente->cd_conta_con] = array('correspondente' => $saida->correspondente->contaCorrespondenteTrashedToo->nm_conta_correspondente_ccr, 'valor' => $total, 'despesa' => 0, 'total' => 0);
            }
        }

        if (!empty($request->despesas)) {
            $despesas = Despesa::where('cd_conta_con', $this->conta)
                                ->when(!empty($dtPagamentoInicio) && !empty($dtPagamentoFim), function ($query) use ($dtPagamentoInicio,$dtPagamentoFim) {
                                    $dtPagamentoInicio = date('Y-m-d', strtotime(str_replace('/', '-', $dtPagamentoInicio)));
                                    $dtPagamentoFim    = date('Y-m-d', strtotime(str_replace('/', '-', $dtPagamentoFim)));
                                    return $query->whereBetween('dt_pagamento_des', [$dtPagamentoInicio,$dtPagamentoFim]);
                                })
                                ->when(!empty($dtPagamentoInicio) && empty($dtPagamentoFim), function ($query) use ($dtPagamentoInicio) {
                                    $dtPagamentoInicio = date('Y-m-d', strtotime(str_replace('/', '-', $dtPagamentoInicio)));
                                    return $query->where('dt_pagamento_des', $dtPagamentoInicio);
                                })
                                ->when(empty($dtPagamentoInicio) && !empty($dtPagamentoFim), function ($query) use ($dtPagamentoFim) {
                                    $dtPagamentoFim = date('Y-m-d', strtotime(str_replace('/', '-', $dtPagamentoFim)));
                                    return $query->where('dt_pagamento_des', $dtPagamentoFim);
                                })

                                ->when(!empty($dtLancamentoInicio) && !empty($dtLancamentoFim), function ($query) use ($dtLancamentoInicio,$dtLancamentoFim) {
                                    $dtLancamentoInicio = date('Y-m-d', strtotime(str_replace('/', '-', $dtLancamentoInicio)));
                                    $dtLancamentoFim    = date('Y-m-d', strtotime(str_replace('/', '-', $dtLancamentoFim)));
                                    return $query->whereBetween('dt_vencimento_des', [$dtLancamentoInicio,$dtLancamentoFim]);
                                })
                                ->when(!empty($dtLancamentoInicio) && empty($dtLancamentoFim), function ($query) use ($dtLancamentoInicio) {
                                    $dtLancamentoInicio = date('Y-m-d', strtotime(str_replace('/', '-', $dtLancamentoInicio)));
                                    return $query->where('dt_vencimento_des', $dtLancamentoInicio);
                                })
                                ->when(empty($dtLancamentoInicio) && !empty($dtLancamentoFim), function ($query) use ($dtPagamentoFim) {
                                    $dtLancamentoFim = date('Y-m-d', strtotime(str_replace('/', '-', $dtLancamentoFim)));
                                    return $query->where('dt_vencimento_des', $dtLancamentoFim);
                                })
                                
                                ->when($tipo != 'P', function ($query) {
                                    return $query->whereNotNull('dt_pagamento_des');
                                })
                                ->get();
        } else {
            $despesas = array();
        }

        $despesasVetor = [];

        foreach ($despesas as $despesa) {
            if (array_key_exists($despesa->tipo->categoriaDespesa->cd_categoria_despesa_cad, $despesasVetor)) {
                $despesa->vl_valor_des += $despesasVetor[$despesa->tipo->categoriaDespesa->cd_categoria_despesa_cad]['valor'];
            }

            $despesasVetor[$despesa->tipo->categoriaDespesa->cd_categoria_despesa_cad] = array('despesa' => $despesa->tipo->categoriaDespesa->nm_categoria_despesa_cad, 'valor' => $despesa->vl_valor_des);
        }

        $dados = array('entradas' => $entradasVetor,'saidas' => $saidasVetor, 'despesas' => $despesasVetor,'flagEntradas' => $request->entradas,'flagSaidas' => $request->saidas, 'flagDespesas' => $request->despesas);



        $entradaTotal = 0;
        $saidaTotal = 0;
        $despesaTotal = 0;
        
        
        foreach ($dados['entradas'] as $dado) {
            $entradaTotal += $dado['valor']+$dado['despesa'];
        }

        foreach ($dados['saidas'] as $dado) {
            $saidaTotal += $dado['valor']+$dado['despesa'];
        }

        foreach ($dados['despesas'] as $dado) {
            $despesaTotal += $dado['valor'];
        }

       
        $total = $entradaTotal - ($despesaTotal+$saidaTotal);

        if (empty($request->despesas)) {
            $request->despesas = 'N';
        }

        if (empty($request->saidas)) {
            $request->saidas = 'N';
        }

        if (empty($request->entradas)) {
            $request->entradas = 'N';
        }

        return \Redirect::back()->with('dtInicio', str_replace('/', '', $request->dtInicio))
                                ->with('dtFim', str_replace('/', '', $request->dtFim))
                                ->with('dtInicioBaixa', str_replace('/', '', $request->dtInicioBaixa))
                                ->with('dtFimBaixa', str_replace('/', '', $request->dtFimBaixa))
                                ->with('dtLancamentoInicio', str_replace('/', '', $request->dtLancamentoInicio))
                                ->with('dtLancamentoFim', str_replace('/', '', $request->dtLancamentoFim))
                                ->with('dtPagamentoInicio', str_replace('/', '', $request->dtPagamentoInicio))
                                ->with('dtPagamentoFim', str_replace('/', '', $request->dtPagamentoFim))
                                ->with('finalizado', $request->finalizado)
                                ->with('cliente', $request->cd_cliente_cli)
                                ->with('nmCliente', $request->nm_cliente_cli)
                                ->with('correspondente', $request->cd_correspondente_cor)
                                ->with('nmCorrespondente', $request->nm_correspondente_cor)
                                ->with('despesas', $request->despesas)
                                ->with('saidas', $request->saidas)
                                ->with('entradas', $request->entradas)
                                ->with('entradaTotal', $entradaTotal)
                                ->with('saidaTotal', $saidaTotal)
                                ->with('despesaTotal', $despesaTotal)
                                ->with('total', $total)
                                ->with('tipo', $tipo);
    }

    public function entradaBuscar(Request $request)
    {
        \Session::put('flBuscar', true);
        \Session::put('cliente', $request->cd_cliente_cli);
        \Session::put('nmCliente', $request->nm_cliente_cli);

        if (!empty($request->parcialmente)) {
            \Session::put('parcialmente', 'S');
        } else {
            \Session::put('parcialmente', null);
        }

        if (!empty($request->pago)) {
            \Session::put('pago', 'S');
        } else {
            \Session::put('pago', null);
        }

        if (!empty($request->nenhum)) {
            \Session::put('nenhum', 'S');
        } else {
            \Session::put('nenhum', null);
        }

        \Session::put('dtInicio', $request->dtInicio);
        \Session::put('dtFim', $request->dtFim);
        \Session::put('dtInicioBaixa', $request->dtInicioBaixa);
        \Session::put('dtFimBaixa', $request->dtFimBaixa);


        if (!empty($request->dtInicio) && !\Helper::validaData($request->dtInicio)) {
            Flash::error('Data prazo falta inicial inválida!');
        }

        if (!empty($request->dtFim) && !\Helper::validaData($request->dtFim)) {
            Flash::error('Data prazo falta final inválida!');
        }

        if (!empty($request->dtInicioBaixat) && !\Helper::validaData($request->dtInicioBaixa)) {
            Flash::error('Data da baixa inicial inválida!');
        }

        if (!empty($request->dtFimBaixa) && !\Helper::validaData($request->dtFimBaixa)) {
            Flash::error('Data da baixa final inválida!');
        }

        
        return redirect('financeiro/entradas');
    }

    public function saidaBuscar(Request $request)
    {
        \Session::put('dtInicio', $request->dtInicio);
        \Session::put('dtFim', $request->dtFim);
        \Session::put('flBuscar', true);
        \Session::put('correspondente', $request->cd_correspondente_cor);
        \Session::put('nmCorrespondente', $request->nm_correspondente_cor);

        if (!empty($request->parcialmente)) {
            \Session::put('parcialmente', 'S');
        } else {
            \Session::put('parcialmente', null);
        }

        if (!empty($request->pago)) {
            \Session::put('pago', 'S');
        } else {
            \Session::put('pago', null);
        }

        if (!empty($request->nenhum)) {
            \Session::put('nenhum', 'S');
        } else {
            \Session::put('nenhum', null);
        }
        
        
        \Session::put('dtInicio', $request->dtInicio);
        \Session::put('dtFim', $request->dtFim);
        \Session::put('dtInicioBaixa', $request->dtInicioBaixa);
        \Session::put('dtFimBaixa', $request->dtFimBaixa);


        if (!empty($request->dtInicio) && !\Helper::validaData($request->dtInicio)) {
            Flash::error('Data prazo falta inicial inválida!');
        }

        if (!empty($request->dtFim) && !\Helper::validaData($request->dtFim)) {
            Flash::error('Data prazo falta final inválida!');
        }

        if (!empty($request->dtInicioBaixat) && !\Helper::validaData($request->dtInicioBaixa)) {
            Flash::error('Data da baixa inicial inválida!');
        }

        if (!empty($request->dtFimBaixa) && !\Helper::validaData($request->dtFimBaixa)) {
            Flash::error('Data da baixa final inválida!');
        }

        return redirect('financeiro/saidas');
    }

    public function buscarBaixaEntrada($id)
    {
        $baixaHonorario = BaixaHonorario::with('anexoFinanceiro')->with('tipoBaixaHonorario')->where('cd_conta_con', $this->conta)->where('cd_processo_taxa_honorario_pth', $id)->where('cd_tipo_financeiro_tfn', \TipoFinanceiro::ENTRADA)->orderBy('cd_baixa_honorario_bho')->get();

        echo json_encode($baixaHonorario);
    }

    public function buscarBaixaSaida($id)
    {
        $baixaHonorario = BaixaHonorario::with('anexoFinanceiro')->with('tipoBaixaHonorario')->where('cd_conta_con', $this->conta)->where('cd_processo_taxa_honorario_pth', $id)->where('cd_tipo_financeiro_tfn', \TipoFinanceiro::SAIDA)->orderBy('cd_baixa_honorario_bho')->get();

        echo json_encode($baixaHonorario);
    }


    public function baixaCliente(Request $request)
    {
        $baixaHonorario = new BaixaHonorario();

        if (empty($request->dtBaixa)) {
            $baixaHonorario->dt_baixa_honorario_bho = null;
        } else {
            $baixaHonorario->dt_baixa_honorario_bho = date('Y-m-d', strtotime(str_replace('/', '-', $request->dtBaixa)));
        }

        if (empty($request->notaFiscal)) {
            $baixaHonorario->nu_nota_fiscal_bho = null;
        } else {
            $baixaHonorario->nu_nota_fiscal_bho = $request->notaFiscal;
        }

        $baixaHonorario->cd_processo_taxa_honorario_pth =  $request->cdBaixaFinanceiro;
        $baixaHonorario->vl_baixa_honorario_bho         =  str_replace(',', '.', $request->valor);
        $baixaHonorario->cd_tipo_financeiro_tfn         = \TipoFinanceiro::ENTRADA;
        $baixaHonorario->cd_conta_con                   = $this->conta;
        $baixaHonorario->cd_tipo_baixa_honorario_bho    = $request->tipo;

        $response = $baixaHonorario->saveOrFail();

        $destino = "entradas/$baixaHonorario->cd_processo_taxa_honorario_pth/$baixaHonorario->cd_baixa_honorario_bho/";
 
        if (!is_dir($destino)) {
            @mkdir(storage_path($destino), 0775);
        }

        if ($request->file('file')) {
            $file = $request->file('file');

            $fileName = $file->getClientOriginalName();
        
            if ($file->move(storage_path($destino), $fileName)) {
                $anexo = AnexoFinanceiro::create([
                        'cd_conta_con'                  => $this->conta,
                        'cd_baixa_honorario_bho'        => $baixaHonorario->cd_baixa_honorario_bho,
                        'nm_anexo_financeiro_afn'       => $file->getClientOriginalName(),
                        'nm_local_anexo_financeiro_afn' => $destino.$file->getClientOriginalName(),
                        'cd_tipo_financeiro_tfn'        => \TipoFinanceiro::ENTRADA
                    ]);

            //return response()->json(['success'=>'Arquivo enviado com sucesso']);
            } else {
                //return Response::json(array('message' => 'Erro ao inserir arquivo'), 500);
            }
        }

        $baixaHonorarioList = BaixaHonorario::with('anexoFinanceiro')->with('tipoBaixaHonorario')->where('cd_conta_con', $this->conta)->where('cd_processo_taxa_honorario_pth', $request->cdBaixaFinanceiro)->where('cd_tipo_financeiro_tfn', \TipoFinanceiro::ENTRADA)->orderBy('cd_baixa_honorario_bho')->get();

        $processoTaxaHonorario = ProcessoTaxaHonorario::where('cd_conta_con', $this->conta)->where('cd_processo_taxa_honorario_pth', $request->cdBaixaFinanceiro)->first();

        $totalDespesas = $processoTaxaHonorario->processo->tiposDespesa->where('pivot.fl_despesa_reembolsavel_pde', 'S')->where('pivot.cd_tipo_entidade_tpe', \TipoEntidade::CLIENTE)->sum('pivot.vl_processo_despesa_pde');

        if ($baixaHonorarioList->where('cd_tipo_financeiro_tfn', \TipoFinanceiro::ENTRADA)->sum('vl_baixa_honorario_bho') >= ($processoTaxaHonorario->vl_taxa_honorario_cliente_pth-((($processoTaxaHonorario->vl_taxa_honorario_cliente_pth)*$processoTaxaHonorario->vl_taxa_cliente_pth)/100))+$totalDespesas) {
            $processoTaxaHonorario->fl_pago_cliente_pth = 'S';
        } else {
            $processoTaxaHonorario->fl_pago_cliente_pth = 'P';
        }

        $processoTaxaHonorario->saveOrFail();

        echo json_encode($baixaHonorarioList);
    }

    public function baixaCorrespondente(Request $request)
    {
        $baixaHonorario = new BaixaHonorario();

        if (empty($request->dtBaixa)) {
            $baixaHonorario->dt_baixa_honorario_bho = null;
        } else {
            $baixaHonorario->dt_baixa_honorario_bho = date('Y-m-d', strtotime(str_replace('/', '-', $request->dtBaixa)));
        }

        if (empty($request->notaFiscal)) {
            $baixaHonorario->nu_nota_fiscal_bho = null;
        } else {
            $baixaHonorario->nu_nota_fiscal_bho = $request->notaFiscal;
        }

        $baixaHonorario->cd_processo_taxa_honorario_pth =  $request->cdBaixaFinanceiro;
        $baixaHonorario->vl_baixa_honorario_bho         =  str_replace(',', '.', $request->valor);
        $baixaHonorario->cd_tipo_financeiro_tfn         = \TipoFinanceiro::SAIDA;
        $baixaHonorario->cd_conta_con                   = $this->conta;
        $baixaHonorario->cd_tipo_baixa_honorario_bho    = $request->tipo;

        $response = $baixaHonorario->saveOrFail();

        $destino = "saidas/$baixaHonorario->cd_processo_taxa_honorario_pth/$baixaHonorario->cd_baixa_honorario_bho/";
 
        if (!is_dir($destino)) {
            @mkdir(storage_path($destino), 0775);
        }

        if ($request->file('file')) {
            $file = $request->file('file');

            $fileName = $file->getClientOriginalName();
        
            if ($file->move(storage_path($destino), $fileName)) {
                $anexo = AnexoFinanceiro::create([
                        'cd_conta_con'                  => $this->conta,
                        'cd_baixa_honorario_bho'        => $baixaHonorario->cd_baixa_honorario_bho,
                        'nm_anexo_financeiro_afn'       => $file->getClientOriginalName(),
                        'nm_local_anexo_financeiro_afn' => $destino.$file->getClientOriginalName(),
                        'cd_tipo_financeiro_tfn'        => \TipoFinanceiro::SAIDA
                    ]);

            //return response()->json(['success'=>'Arquivo enviado com sucesso']);
            } else {
                //return Response::json(array('message' => 'Erro ao inserir arquivo'), 500);
            }
        }

        $baixaHonorarioList = BaixaHonorario::with('anexoFinanceiro')->with('tipoBaixaHonorario')->where('cd_conta_con', $this->conta)->where('cd_processo_taxa_honorario_pth', $request->cdBaixaFinanceiro)->where('cd_tipo_financeiro_tfn', \TipoFinanceiro::SAIDA)->orderBy('cd_baixa_honorario_bho')->get();

        $processoTaxaHonorario = ProcessoTaxaHonorario::where('cd_conta_con', $this->conta)->where('cd_processo_taxa_honorario_pth', $request->cdBaixaFinanceiro)->first();

        $totalDespesas = $processoTaxaHonorario->processo->tiposDespesa->where('pivot.fl_despesa_reembolsavel_pde', 'S')->where('pivot.cd_tipo_entidade_tpe', \TipoEntidade::CORRESPONDENTE)->sum('pivot.vl_processo_despesa_pde');

        if ($baixaHonorarioList->where('cd_tipo_financeiro_tfn', \TipoFinanceiro::SAIDA)->sum('vl_baixa_honorario_bho') >= $processoTaxaHonorario->vl_taxa_honorario_correspondente_pth+$totalDespesas) {
            $processoTaxaHonorario->fl_pago_correspondente_pth = 'S';
        } else {
            $processoTaxaHonorario->fl_pago_correspondente_pth = 'P';
        }

        $processoTaxaHonorario->saveOrFail();

        echo json_encode($baixaHonorarioList);
    }

    public function relatorioBalancoSumarizado($request)
    {
        $dtInicio           = $request->dtInicio;
        $dtFim              = $request->dtFim;
        $dtInicioBaixa      = $request->dtInicioBaixa;
        $dtFimBaixa         = $request->dtFimBaixa;
        $dtLancamentoInicio = $request->dtLancamentoInicio;
        $dtLancamentoFim    = $request->dtLancamentoFim;
        $dtPagamentoInicio  = $request->dtPagamentoInicio;
        $dtPagamentoFim     = $request->dtPagamentoFim;
        $finalizado         = $request->finalizado;
        $cliente            = $request->cd_cliente_cli;
        $correspondente     = $request->cd_correspondente_cor;
        $tipo               = $request->tipo;

        $conta = Conta::where('cd_conta_con', $this->conta)->select('nm_razao_social_con')->first();

        $entradasVetor = [];

        if (!empty($request->entradas) || !empty($request->balanco)) {
            $entradas = Processo::whereHas('honorario', function ($query) use ($dtInicioBaixa,$dtFimBaixa) {
                if (!empty($dtInicioBaixa) || !empty($dtFimBaixa)) {
                    $query->whereHas('baixaHonorario', function ($query) use ($dtInicioBaixa,$dtFimBaixa) {
                        $query->when(!empty($dtInicioBaixa) && !empty($dtFimBaixa), function ($query) use ($dtInicioBaixa,$dtFimBaixa) {
                            $dtInicioBaixa = date('Y-m-d', strtotime(str_replace('/', '-', $dtInicioBaixa)));
                            $dtFimBaixa    = date('Y-m-d', strtotime(str_replace('/', '-', $dtFimBaixa)));
                            return $query->whereBetween('dt_baixa_honorario_bho', [$dtInicioBaixa,$dtFimBaixa]);
                        })
                                            ->when(!empty($dtInicioBaixa) && empty($dtFimBaixa), function ($query) use ($dtInicioBaixa) {
                                                $dtInicioBaixa = date('Y-m-d', strtotime(str_replace('/', '-', $dtInicioBaixa)));
                                                return $query->where('dt_baixa_honorario_bho', $dtInicioBaixa);
                                            })

                                            ->when(empty($dtInicioBaixa) && !empty($dtFimBaixa), function ($query) use ($dtFimBaixa) {
                                                $dtFimBaixa = date('Y-m-d', strtotime(str_replace('/', '-', $dtFimBaixa)));
                                                return $query->where('dt_baixa_honorario_bho', $dtFimBaixa);
                                            });
                    });
                } else {
                    $query->with('baixaHonorario');
                }
            })
                                 ->with('cliente')
                                 ->with('tiposDespesa')
                                 ->where('cd_conta_con', $this->conta)
                                  ->when(!empty($cliente), function ($query) use ($cliente) {
                                      return $query->where('cd_cliente_cli', $cliente);
                                  })
                                 ->when(!empty($correspondente), function ($query) use ($correspondente) {
                                     return $query->where('cd_correspondente_cor', $correspondente);
                                 })
                                 ->when(!empty($finalizado), function ($query) {
                                     return $query->where('cd_status_processo_stp', \StatusProcesso::FINALIZADO);
                                 })
                                 ->when(!empty($dtInicio) && !empty($dtFim), function ($query) use ($dtInicio,$dtFim) {
                                     $dtInicio = date('Y-m-d', strtotime(str_replace('/', '-', $dtInicio)));
                                     $dtFim    = date('Y-m-d', strtotime(str_replace('/', '-', $dtFim)));
                                     return $query->whereBetween('dt_prazo_fatal_pro', [$dtInicio,$dtFim]);
                                 })
                                 ->when(!empty($dtInicio) && empty($dtFim), function ($query) use ($dtInicio) {
                                     $dtInicio = date('Y-m-d', strtotime(str_replace('/', '-', $dtInicio)));
                                     return $query->where('dt_prazo_fatal_pro', $dtInicio);
                                 })
                                 ->when(empty($dtInicio) && !empty($dtFim), function ($query) use ($dtFim) {
                                     $dtFim = date('Y-m-d', strtotime(str_replace('/', '-', $dtFim)));
                                     return $query->where('dt_prazo_fatal_pro', $dtFim);
                                 })
                                 ->get()->sortBy('cliente.nm_razao_social_cli');
        } else {
            $entradas = array();
        }

        foreach ($entradas as $entrada) {
            $totalDespesas = 0;
            $total = 0;
            $entradaTotal = 0;

            if ($tipo == 'P') {
                foreach ($entrada->tiposDespesa as $despesa) {
                    if ($despesa->pivot->cd_tipo_entidade_tpe == \TipoEntidade::CLIENTE && $despesa->pivot->fl_despesa_reembolsavel_pde == 'S') {
                        $totalDespesas += $despesa->pivot->vl_processo_despesa_pde;
                    }
                }

                $entrada->honorario->vl_taxa_honorario_cliente_pth = $entrada->honorario->vl_taxa_honorario_cliente_pth - (($entrada->honorario->vl_taxa_honorario_cliente_pth * $entrada->honorario->vl_taxa_cliente_pth)/100);

                if (array_key_exists($entrada->cliente->cd_cliente_cli, $entradasVetor)) {
                    $entrada->honorario->vl_taxa_honorario_cliente_pth += round($entradasVetor[$entrada->cliente->cd_cliente_cli]['valor'], 2);
                    $totalDespesas += $entradasVetor[$entrada->cliente->cd_cliente_cli]['despesa'];
                }

                $total = $entrada->honorario->vl_taxa_honorario_cliente_pth + $totalDespesas;

                $entradasVetor[$entrada->cliente->cd_cliente_cli] = array('cliente' => $entrada->cliente->nm_razao_social_cli, 'valor' => $entrada->honorario->vl_taxa_honorario_cliente_pth, 'despesa' => $totalDespesas, 'total' => $total);
            } else {
                $entradaTotal = $entrada->honorario->baixaHonorario->where('cd_tipo_financeiro_tfn', \TipoFinanceiro::ENTRADA)->where('cd_tipo_baixa_honorario_bho', \TipoBaixaHonorario::HONORARIO)->sum('vl_baixa_honorario_bho');

                if (!empty($entradaTotal)) {
                    $entradaTotal = $entradaTotal - (($entradaTotal * $entrada->honorario->vl_taxa_cliente_pth)/100);
                }


                $totalDespesas = $entrada->honorario->baixaHonorario->where('cd_tipo_financeiro_tfn', \TipoFinanceiro::ENTRADA)->where('cd_tipo_baixa_honorario_bho', \TipoBaixaHonorario::DESPESA)->sum('vl_baixa_honorario_bho');


                if (array_key_exists($entrada->cliente->cd_cliente_cli, $entradasVetor)) {
                    $entradaTotal += round($entradasVetor[$entrada->cliente->cd_cliente_cli]['valor'], 2);
                    $totalDespesas += $entradasVetor[$entrada->cliente->cd_cliente_cli]['despesa'];
                }

                $total = $entradaTotal + $totalDespesas;

                $entradasVetor[$entrada->cliente->cd_cliente_cli] = array('cliente' => $entrada->cliente->nm_razao_social_cli, 'valor' => $entradaTotal, 'despesa' => $totalDespesas, 'total' => $total);
            }
        }

        $saidasVetor = [];

        if (!empty($request->saidas) || !empty($request->balanco)) {
            $saidas = Processo::whereHas('honorario.tipoServicoCorrespondente')
                                ->whereHas('honorario', function ($query) use ($dtInicioBaixa,$dtFimBaixa) {
                                    if (!empty($dtInicioBaixa) || !empty($dtFimBaixa)) {
                                        $query->whereHas('baixaHonorario', function ($query) use ($dtInicioBaixa,$dtFimBaixa) {
                                            $query->when(!empty($dtInicioBaixa) && !empty($dtFimBaixa), function ($query) use ($dtInicioBaixa,$dtFimBaixa) {
                                                $dtInicioBaixa = date('Y-m-d', strtotime(str_replace('/', '-', $dtInicioBaixa)));
                                                $dtFimBaixa    = date('Y-m-d', strtotime(str_replace('/', '-', $dtFimBaixa)));
                                                return $query->whereBetween('dt_baixa_honorario_bho', [$dtInicioBaixa,$dtFimBaixa]);
                                            })
                                            ->when(!empty($dtInicioBaixa) && empty($dtFimBaixa), function ($query) use ($dtInicioBaixa) {
                                                $dtInicioBaixa = date('Y-m-d', strtotime(str_replace('/', '-', $dtInicioBaixa)));
                                                return $query->where('dt_baixa_honorario_bho', $dtInicioBaixa);
                                            })

                                            ->when(empty($dtInicioBaixa) && !empty($dtFimBaixa), function ($query) use ($dtFimBaixa) {
                                                $dtFimBaixa = date('Y-m-d', strtotime(str_replace('/', '-', $dtFimBaixa)));
                                                return $query->where('dt_baixa_honorario_bho', $dtFimBaixa);
                                            });
                                        });
                                    } else {
                                        $query->with('baixaHonorario');
                                    }
                                })
                                ->whereHas('correspondente.contaCorrespondenteTrashedToo')
                                ->with('tiposDespesa')
                                ->where('cd_conta_con', $this->conta)
                                ->when(!empty($cliente), function ($query) use ($cliente) {
                                    return $query->where('cd_cliente_cli', $cliente);
                                })
                                ->when(!empty($correspondente), function ($query) use ($correspondente) {
                                    return $query->where('cd_correspondente_cor', $correspondente);
                                })
                                ->when(!empty($finalizado), function ($query) {
                                    return $query->where('cd_status_processo_stp', \StatusProcesso::FINALIZADO);
                                })
                                ->when(!empty($dtInicio) && !empty($dtFim), function ($query) use ($dtInicio,$dtFim) {
                                    $dtInicio = date('Y-m-d', strtotime(str_replace('/', '-', $dtInicio)));
                                    $dtFim    = date('Y-m-d', strtotime(str_replace('/', '-', $dtFim)));
                                    return $query->whereBetween('dt_prazo_fatal_pro', [$dtInicio,$dtFim]);
                                })
                                ->when(!empty($dtInicio) && empty($dtFim), function ($query) use ($dtInicio) {
                                    $dtInicio = date('Y-m-d', strtotime(str_replace('/', '-', $dtInicio)));
                                    return $query->where('dt_prazo_fatal_pro', $dtInicio);
                                })
                                ->when(empty($dtInicio) && !empty($dtFim), function ($query) use ($dtFim) {
                                    $dtFim = date('Y-m-d', strtotime(str_replace('/', '-', $dtFim)));
                                    return $query->where('dt_prazo_fatal_pro', $dtFim);
                                })
                                ->get()
                                ->sortBy(function ($q) {
                                    return iconv('UTF-8', 'ASCII//TRANSLIT', $q->correspondente->contaCorrespondenteTrashedToo->nm_conta_correspondente_ccr);
                                });
        } else {
            $saidas = array();
        }
        
        foreach ($saidas as $saida) {
            $totalDespesas = 0;
            $total = 0;
            $saidaTotal = 0;

            if ($tipo == 'P') {
                foreach ($saida->tiposDespesa as $despesa) {
                    if ($despesa->pivot->cd_tipo_entidade_tpe == \TipoEntidade::CORRESPONDENTE && $despesa->pivot->fl_despesa_reembolsavel_pde == 'S') {
                        $totalDespesas += $despesa->pivot->vl_processo_despesa_pde;
                    }
                }

                if (array_key_exists($saida->correspondente->cd_conta_con, $saidasVetor)) {
                    $saida->honorario->vl_taxa_honorario_correspondente_pth += round($saidasVetor[$saida->correspondente->cd_conta_con]['valor'], 2);
                    $totalDespesas += $saidasVetor[$saida->correspondente->cd_conta_con]['despesa'];
                }

                $total = $saida->honorario->vl_taxa_honorario_correspondente_pth + $totalDespesas;

                $saidasVetor[$saida->correspondente->cd_conta_con] = array('correspondente' => $saida->correspondente->contaCorrespondenteTrashedToo->nm_conta_correspondente_ccr, 'valor' => $saida->honorario->vl_taxa_honorario_correspondente_pth, 'despesa' => $totalDespesas, 'total' => $total);
            } else {
                $saidaTotal = $saida->honorario->baixaHonorario->where('cd_tipo_financeiro_tfn', \TipoFinanceiro::SAIDA)->where('cd_tipo_baixa_honorario_bho', \TipoBaixaHonorario::HONORARIO)->sum('vl_baixa_honorario_bho');

                $totalDespesas = $saida->honorario->baixaHonorario->where('cd_tipo_financeiro_tfn', \TipoFinanceiro::SAIDA)->where('cd_tipo_baixa_honorario_bho', \TipoBaixaHonorario::DESPESA)->sum('vl_baixa_honorario_bho');

                if (array_key_exists($saida->correspondente->cd_conta_con, $saidasVetor)) {
                    $saidaTotal += round($saidasVetor[$saida->correspondente->cd_conta_con]['valor'], 2);
                    $totalDespesas += $saidasVetor[$saida->correspondente->cd_conta_con]['despesa'];
                }

                $total = $saidaTotal + $totalDespesas;

                $saidasVetor[$saida->correspondente->cd_conta_con] = array('correspondente' => $saida->correspondente->contaCorrespondenteTrashedToo->nm_conta_correspondente_ccr, 'valor' => $saidaTotal, 'despesa' => $totalDespesas, 'total' => $total);
            }
        }

        if (!empty($request->despesas) || !empty($request->balanco)) {
            $despesas = Despesa::where('cd_conta_con', $this->conta)
                                 ->when(!empty($dtPagamentoInicio) && !empty($dtPagamentoFim), function ($query) use ($dtPagamentoInicio,$dtPagamentoFim) {
                                     $dtPagamentoInicio = date('Y-m-d', strtotime(str_replace('/', '-', $dtPagamentoInicio)));
                                     $dtPagamentoFim    = date('Y-m-d', strtotime(str_replace('/', '-', $dtPagamentoFim)));
                                     return $query->whereBetween('dt_pagamento_des', [$dtPagamentoInicio,$dtPagamentoFim]);
                                 })
                                 ->when(!empty($dtPagamentoInicio) && empty($dtPagamentoFim), function ($query) use ($dtPagamentoInicio) {
                                     $dtPagamentoInicio = date('Y-m-d', strtotime(str_replace('/', '-', $dtPagamentoInicio)));
                                     return $query->where('dt_pagamento_des', $dtPagamentoInicio);
                                 })
                                 ->when(empty($dtPagamentoInicio) && !empty($dtPagamentoFim), function ($query) use ($dtPagamentoFim) {
                                     $dtPagamentoFim = date('Y-m-d', strtotime(str_replace('/', '-', $dtPagamentoFim)));
                                     return $query->where('dt_pagamento_des', $dtPagamentoFim);
                                 })

                                ->when(!empty($dtLancamentoInicio) && !empty($dtLancamentoFim), function ($query) use ($dtLancamentoInicio,$dtLancamentoFim) {
                                    $dtLancamentoInicio = date('Y-m-d', strtotime(str_replace('/', '-', $dtLancamentoInicio)));
                                    $dtLancamentoFim    = date('Y-m-d', strtotime(str_replace('/', '-', $dtLancamentoFim)));
                                    return $query->whereBetween('dt_vencimento_des', [$dtLancamentoInicio,$dtLancamentoFim]);
                                })
                                 ->when(!empty($dtLancamentoInicio) && empty($dtLancamentoFim), function ($query) use ($dtLancamentoInicio) {
                                     $dtLancamentoInicio = date('Y-m-d', strtotime(str_replace('/', '-', $dtLancamentoInicio)));
                                     return $query->where('dt_vencimento_des', $dtLancamentoInicio);
                                 })
                                 ->when(empty($dtLancamentoInicio) && !empty($dtLancamentoFim), function ($query) use ($dtPagamentoFim) {
                                     $dtLancamentoFim = date('Y-m-d', strtotime(str_replace('/', '-', $dtLancamentoFim)));
                                     return $query->where('dt_vencimento_des', $dtLancamentoFim);
                                 })
                                 
                                 ->when($tipo != 'P', function ($query) {
                                     return $query->whereNotNull('dt_pagamento_des');
                                 })
                                 ->get()
                                 ->sortBy('tipo.categoriaDespesa.nm_categoria_despesa_cad');
        } else {
            $despesas = array();
        }

        $despesasVetor = [];

        foreach ($despesas as $despesa) {
            if (array_key_exists($despesa->tipo->categoriaDespesa->cd_categoria_despesa_cad, $despesasVetor)) {
                $despesa->vl_valor_des += $despesasVetor[$despesa->tipo->categoriaDespesa->cd_categoria_despesa_cad]['valor'];
            }

            $despesasVetor[$despesa->tipo->categoriaDespesa->cd_categoria_despesa_cad] = array('despesa' => $despesa->tipo->categoriaDespesa->nm_categoria_despesa_cad, 'valor' => $despesa->vl_valor_des);
        }

        $dados = array('entradas' => $entradasVetor,'conta' => $conta,'saidas' => $saidasVetor, 'despesas' => $despesasVetor,'flagEntradas' => $request->entradas,'flagSaidas' => $request->saidas, 'flagDespesas' => $request->despesas, 'flagBalanco' => $request->balanco);

        
        \Excel::store(new BalancoSumarizadoExport($dados), "/financeiro/balanco/{$this->conta}/".time().'_Relatório_Sumarizado.xlsx', 'reports', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function relatorioBalancoDetalhado($request)
    {
        $dtInicio            = $request->dtInicio;
        $dtFim               = $request->dtFim;
        $dtInicioBaixa       = $request->dtInicioBaixa;
        $dtFimBaixa          = $request->dtFimBaixa;
        $dtLancamentoInicio  = $request->dtLancamentoInicio;
        $dtLancamentoFim     = $request->dtLancamentoFim;
        $dtPagamentoInicio   = $request->dtPagamentoInicio;
        $dtPagamentoFim      = $request->dtPagamentoFim;

        $finalizado     = $request->finalizado;
        $cliente        = $request->cd_cliente_cli;
        $correspondente = $request->cd_correspondente_cor;
        $tipo           = $request->tipo;


        $conta = Conta::where('cd_conta_con', $this->conta)->select('nm_razao_social_con')->first();

        if (!empty($request->entradas) || !empty($request->balanco)) {
            $entradas = Processo::whereHas('honorario', function ($query) use ($dtInicioBaixa,$dtFimBaixa) {
                if (!empty($dtInicioBaixa) || !empty($dtFimBaixa)) {
                    $query->whereHas('baixaHonorario', function ($query) use ($dtInicioBaixa,$dtFimBaixa) {
                        $query->when(!empty($dtInicioBaixa) && !empty($dtFimBaixa), function ($query) use ($dtInicioBaixa,$dtFimBaixa) {
                            $dtInicioBaixa = date('Y-m-d', strtotime(str_replace('/', '-', $dtInicioBaixa)));
                            $dtFimBaixa    = date('Y-m-d', strtotime(str_replace('/', '-', $dtFimBaixa)));
                            return $query->whereBetween('dt_baixa_honorario_bho', [$dtInicioBaixa,$dtFimBaixa]);
                        })
                                            ->when(!empty($dtInicioBaixa) && empty($dtFimBaixa), function ($query) use ($dtInicioBaixa) {
                                                $dtInicioBaixa = date('Y-m-d', strtotime(str_replace('/', '-', $dtInicioBaixa)));
                                                return $query->where('dt_baixa_honorario_bho', $dtInicioBaixa);
                                            })

                                            ->when(empty($dtInicioBaixa) && !empty($dtFimBaixa), function ($query) use ($dtFimBaixa) {
                                                $dtFimBaixa = date('Y-m-d', strtotime(str_replace('/', '-', $dtFimBaixa)));
                                                return $query->where('dt_baixa_honorario_bho', $dtFimBaixa);
                                            });
                    });
                } else {
                    $query->with('baixaHonorario');
                }
            })
                                 ->with('cliente')
                                 ->with('tiposDespesa')
                                 ->where('cd_conta_con', $this->conta)
                                 ->when(!empty($cliente), function ($query) use ($cliente) {
                                     return $query->where('cd_cliente_cli', $cliente);
                                 })
                                 ->when(!empty($correspondente), function ($query) use ($correspondente) {
                                     return $query->where('cd_correspondente_cor', $correspondente);
                                 })
                                 ->when(!empty($finalizado), function ($query) {
                                     return $query->where('cd_status_processo_stp', \StatusProcesso::FINALIZADO);
                                 })
                                 ->when(!empty($dtInicio) && !empty($dtFim), function ($query) use ($dtInicio,$dtFim) {
                                     $dtInicio = date('Y-m-d', strtotime(str_replace('/', '-', $dtInicio)));
                                     $dtFim    = date('Y-m-d', strtotime(str_replace('/', '-', $dtFim)));
                                     return $query->whereBetween('dt_prazo_fatal_pro', [$dtInicio,$dtFim]);
                                 })
                                 ->when(!empty($dtInicio) && empty($dtFim), function ($query) use ($dtInicio) {
                                     $dtInicio = date('Y-m-d', strtotime(str_replace('/', '-', $dtInicio)));
                                     return $query->where('dt_prazo_fatal_pro', $dtInicio);
                                 })
                                 ->when(empty($dtInicio) && !empty($dtFim), function ($query) use ($dtFim) {
                                     $dtFim = date('Y-m-d', strtotime(str_replace('/', '-', $dtFim)));
                                     return $query->where('dt_prazo_fatal_pro', $dtFim);
                                 })
                                 ->orderBy('dt_prazo_fatal_pro')
                                 ->get();
        } else {
            $entradas = array();
        }

        if (!empty($request->saidas) || !empty($request->balanco)) {
            $saidas = Processo::whereHas('honorario.tipoServicoCorrespondente')
                                ->whereHas('honorario', function ($query) use ($dtInicioBaixa,$dtFimBaixa) {
                                    if (!empty($dtInicioBaixa) || !empty($dtFimBaixa)) {
                                        $query->whereHas('baixaHonorario', function ($query) use ($dtInicioBaixa,$dtFimBaixa) {
                                            $query->when(!empty($dtInicioBaixa) && !empty($dtFimBaixa), function ($query) use ($dtInicioBaixa,$dtFimBaixa) {
                                                $dtInicioBaixa = date('Y-m-d', strtotime(str_replace('/', '-', $dtInicioBaixa)));
                                                $dtFimBaixa    = date('Y-m-d', strtotime(str_replace('/', '-', $dtFimBaixa)));
                                                return $query->whereBetween('dt_baixa_honorario_bho', [$dtInicioBaixa,$dtFimBaixa]);
                                            })
                                            ->when(!empty($dtInicioBaixa) && empty($dtFimBaixa), function ($query) use ($dtInicioBaixa) {
                                                $dtInicioBaixa = date('Y-m-d', strtotime(str_replace('/', '-', $dtInicioBaixa)));
                                                return $query->where('dt_baixa_honorario_bho', $dtInicioBaixa);
                                            })

                                            ->when(empty($dtInicioBaixa) && !empty($dtFimBaixa), function ($query) use ($dtFimBaixa) {
                                                $dtFimBaixa = date('Y-m-d', strtotime(str_replace('/', '-', $dtFimBaixa)));
                                                return $query->where('dt_baixa_honorario_bho', $dtFimBaixa);
                                            });
                                        });
                                    } else {
                                        $query->with('baixaHonorario');
                                    }
                                })
                                ->whereHas('correspondente')
                                ->with('tiposDespesa')
                                ->where('cd_conta_con', $this->conta)
                                ->when(!empty($cliente), function ($query) use ($cliente) {
                                    return $query->where('cd_cliente_cli', $cliente);
                                })
                                ->when(!empty($correspondente), function ($query) use ($correspondente) {
                                    return $query->where('cd_correspondente_cor', $correspondente);
                                })
                                ->when(!empty($finalizado), function ($query) {
                                    return $query->where('cd_status_processo_stp', \StatusProcesso::FINALIZADO);
                                })
                                ->when(!empty($dtInicio) && !empty($dtFim), function ($query) use ($dtInicio,$dtFim) {
                                    $dtInicio = date('Y-m-d', strtotime(str_replace('/', '-', $dtInicio)));
                                    $dtFim    = date('Y-m-d', strtotime(str_replace('/', '-', $dtFim)));
                                    return $query->whereBetween('dt_prazo_fatal_pro', [$dtInicio,$dtFim]);
                                })
                                ->when(!empty($dtInicio) && empty($dtFim), function ($query) use ($dtInicio) {
                                    $dtInicio = date('Y-m-d', strtotime(str_replace('/', '-', $dtInicio)));
                                    return $query->where('dt_prazo_fatal_pro', $dtInicio);
                                })
                                ->when(empty($dtInicio) && !empty($dtFim), function ($query) use ($dtFim) {
                                    $dtFim = date('Y-m-d', strtotime(str_replace('/', '-', $dtFim)));
                                    return $query->where('dt_prazo_fatal_pro', $dtFim);
                                })
                                ->orderBy('dt_prazo_fatal_pro')
                                ->get();
        } else {
            $saidas = array();
        }

        if (!empty($request->despesas) || !empty($request->balanco)) {
            $despesas = Despesa::where('cd_conta_con', $this->conta)
                                 ->when(!empty($dtPagamentoInicio) && !empty($dtPagamentoFim), function ($query) use ($dtPagamentoInicio,$dtPagamentoFim) {
                                     $dtPagamentoInicio = date('Y-m-d', strtotime(str_replace('/', '-', $dtPagamentoInicio)));
                                     $dtPagamentoFim    = date('Y-m-d', strtotime(str_replace('/', '-', $dtPagamentoFim)));
                                     return $query->whereBetween('dt_pagamento_des', [$dtPagamentoInicio,$dtPagamentoFim]);
                                 })
                                 ->when(!empty($dtPagamentoInicio) && empty($dtPagamentoFim), function ($query) use ($dtPagamentoInicio) {
                                     $dtPagamentoInicio = date('Y-m-d', strtotime(str_replace('/', '-', $dtPagamentoInicio)));
                                     return $query->where('dt_pagamento_des', $dtPagamentoInicio);
                                 })
                                 ->when(empty($dtPagamentoInicio) && !empty($dtPagamentoFim), function ($query) use ($dtPagamentoFim) {
                                     $dtPagamentoFim = date('Y-m-d', strtotime(str_replace('/', '-', $dtPagamentoFim)));
                                     return $query->where('dt_pagamento_des', $dtPagamentoFim);
                                 })

                                ->when(!empty($dtLancamentoInicio) && !empty($dtLancamentoFim), function ($query) use ($dtLancamentoInicio,$dtLancamentoFim) {
                                    $dtLancamentoInicio = date('Y-m-d', strtotime(str_replace('/', '-', $dtLancamentoInicio)));
                                    $dtLancamentoFim    = date('Y-m-d', strtotime(str_replace('/', '-', $dtLancamentoFim)));
                                    return $query->whereBetween('dt_vencimento_des', [$dtLancamentoInicio,$dtLancamentoFim]);
                                })
                                 ->when(!empty($dtLancamentoInicio) && empty($dtLancamentoFim), function ($query) use ($dtLancamentoInicio) {
                                     $dtLancamentoInicio = date('Y-m-d', strtotime(str_replace('/', '-', $dtLancamentoInicio)));
                                     return $query->where('dt_vencimento_des', $dtLancamentoInicio);
                                 })
                                 ->when(empty($dtLancamentoInicio) && !empty($dtLancamentoFim), function ($query) use ($dtPagamentoFim) {
                                     $dtLancamentoFim = date('Y-m-d', strtotime(str_replace('/', '-', $dtLancamentoFim)));
                                     return $query->where('dt_vencimento_des', $dtLancamentoFim);
                                 })

                                 ->when($tipo != 'P', function ($query) {
                                     return $query->whereNotNull('dt_pagamento_des');
                                 })
                                 ->orderBy('dt_vencimento_des')
                                 ->get();
        } else {
            $despesas = array();
        }

        $dados = array('entradas' => $entradas,'conta' => $conta,'saidas' => $saidas, 'despesas' => $despesas,'flagEntradas' => $request->entradas,'flagSaidas' => $request->saidas, 'flagDespesas' => $request->despesas, 'flagBalanco' => $request->balanco, 'tipo' => $request->tipo);

        \Excel::store(new BalancoDetalhadoExport($dados), "/financeiro/balanco/{$this->conta}/".time().'_Relatório_Detalhado.xlsx', 'reports', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function relatorios()
    {
        return view('financeiro/relatorios', ['arquivos' => $this->getFiles()]);
    }

    public function relatorioBuscar(Request $request)
    {
        $erro = false;
        if (!empty($request->dtInicio)) {
            if (\Helper::validaData($request->dtInicio) != true) {
                $erro = true;
                Flash::error('Data(s) inválida(s) !');
            }
        }

        if (!empty($request->dtFim)) {
            if (\Helper::validaData($request->dtFim) != true) {
                $erro = true;
                Flash::error('Data(s) inválida(s) !');
            }
        }

        if (!empty($request->dtInicioBaixa)) {
            if (\Helper::validaData($request->dtInicioBaixa) != true) {
                $erro = true;
                Flash::error('Data(s) inválida(s) !');
            }
        }

        if (!empty($request->dtFimBaixa)) {
            if (\Helper::validaData($request->dtFimBaixa) != true) {
                $erro = true;
                Flash::error('Data(s) inválida(s) !');
            }
        }

        if (!empty($request->dtLancamentoInicio)) {
            if (\Helper::validaData($request->dtLancamentoInicio) != true) {
                $erro = true;
                Flash::error('Data(s) inválida(s) !');
            }
        }

        if (!empty($request->dtLancamentoFim)) {
            if (\Helper::validaData($request->dtLancamentoFim) != true) {
                $erro = true;
                Flash::error('Data(s) inválida(s) !');
            }
        }

        if (!empty($request->dtPagamentoInicio)) {
            if (\Helper::validaData($request->dtPagamentoInicio) != true) {
                $erro = true;
                Flash::error('Data(s) inválida(s) !');
            }
        }

        if (!empty($request->dtPagamentoFim)) {
            if (\Helper::validaData($request->dtPagamentoFim) != true) {
                $erro = true;
                Flash::error('Data(s) inválida(s) !');
            }
        }

        if (empty($request->despesas) && empty($request->saidas) && empty($request->entradas) && empty($request->balanco)) {
            $erro = true;
            Flash::error('É preciso gerar relatório para pelo menos um dos itens!');
        }

        if ($erro == false) {
            if ($request->relatorio == 'relatorio-por-processo') {
                $this->relatorioBalancoDetalhado($request);
            }

            if ($request->relatorio == 'relatorio-sumarizado') {
                $this->relatorioBalancoSumarizado($request);
            }
        }

        if (empty($request->despesas)) {
            $request->despesas = 'N';
        }

        if (empty($request->saidas)) {
            $request->saidas = 'N';
        }

        if (empty($request->entradas)) {
            $request->entradas = 'N';
        }

        if (empty($request->balanco)) {
            $request->balanco = 'N';
        }


        return \Redirect::back()->with('dtInicio', str_replace('/', '', $request->dtInicio))
                                ->with('dtFim', str_replace('/', '', $request->dtFim))
                                ->with('dtInicioBaixa', str_replace('/', '', $request->dtInicioBaixa))
                                ->with('dtFimBaixa', str_replace('/', '', $request->dtFimBaixa))
                                ->with('relatorio', $request->relatorio)
                                ->with('finalizado', $request->finalizado)
                                ->with('cliente', $request->cd_cliente_cli)
                                ->with('nmCliente', $request->nm_cliente_cli)
                                ->with('correspondente', $request->cd_correspondente_cor)
                                ->with('nmCorrespondente', $request->nm_correspondente_cor)
                                ->with('despesas', $request->despesas)
                                ->with('saidas', $request->saidas)
                                ->with('entradas', $request->entradas)
                                ->with('balanco', $request->balanco)
                                ->with('tipo', $request->tipo)
                                ->with('dtLancamentoInicio', str_replace('/', '', $request->dtLancamentoInicio))
                                ->with('dtLancamentoFim', str_replace('/', '', $request->dtLancamentoFim))
                                ->with('dtPagamentoInicio', str_replace('/', '', $request->dtPagamentoInicio))
                                ->with('dtPagamentoFim', str_replace('/', '', $request->dtPagamentoFim));
    }

    private function getFiles()
    {
        \File::makeDirectory(storage_path().'/reports/financeiro/balanco/'.$this->conta, $mode = 0777, true, true);

        $arquivos = array();

        $files = collect(\File::allFiles(storage_path()."/reports/financeiro/balanco/".$this->conta))
                 ->sortByDesc(function ($file) {
                     return $file->getCTime();
                 });
        
        foreach ($files as $file) {
            $arquivos[] = array('nome' => $file->getFilename(), 'data' => date('d/m/Y H:i:s', $file->getCTime()),'tamanho' => round($file->getSize()/1024, 2) );
        }

        return $arquivos;
    }

    public function excluir($nome)
    {
        \Storage::disk('reports')->delete("/financeiro/balanco/$this->conta/".$nome);
        
        return \Response::json(array('message' => 'Registro excluído com sucesso'), 200);
    }

    public function arquivo($nome)
    {
        //dd(\Storage::disk('reports')->get("$this->conta/".$nome));
        return response()->download(storage_path('reports/financeiro/balanco/'."$this->conta/".$nome));
    }

    public function excluirBaixa($id)
    {
        $baixaProcesso = BaixaHonorario::with('anexoFinanceiro')->where('cd_conta_con', $this->conta)->where('cd_baixa_honorario_bho', $id)->first();

        $processoTaxa = $baixaProcesso->cd_processo_taxa_honorario_pth;

        BaixaHonorario::where('cd_conta_con', $this->conta)
                                    ->where('cd_baixa_honorario_bho', $id)
                                    ->delete();


        $baixaHonorario = BaixaHonorario::with('anexoFinanceiro')->with('tipoBaixaHonorario')->where('cd_conta_con', $this->conta)->where('cd_processo_taxa_honorario_pth', $baixaProcesso->cd_processo_taxa_honorario_pth)->where('cd_tipo_financeiro_tfn', \TipoFinanceiro::ENTRADA)->orderBy('cd_baixa_honorario_bho')->get();

        if (!empty($baixaProcesso->anexoFinanceiro)) {
            $this->entradaFileExcluir($baixaProcesso->anexoFinanceiro->cd_anexo_financeiro_afn);
        }


        $baixaProcessoDepois = BaixaHonorario::with('anexoFinanceiro')->where('cd_conta_con', $this->conta)->where('cd_processo_taxa_honorario_pth', $baixaProcesso->cd_processo_taxa_honorario_pth)->where('cd_tipo_financeiro_tfn', \TipoFinanceiro::ENTRADA)->get();

        $processoTaxaHonorario = ProcessoTaxaHonorario::where('cd_conta_con', $this->conta)->where('cd_processo_taxa_honorario_pth', $processoTaxa)->first();

        $totalDespesas = $processoTaxaHonorario->processo->tiposDespesa->where('pivot.fl_despesa_reembolsavel_pde', 'S')->where('pivot.cd_tipo_entidade_tpe', \TipoEntidade::CLIENTE)->sum('pivot.vl_processo_despesa_pde');

        if ($baixaProcessoDepois->where('cd_tipo_financeiro_tfn', \TipoFinanceiro::ENTRADA)->sum('vl_baixa_honorario_bho') < $processoTaxaHonorario->vl_taxa_honorario_cliente_pth+$totalDespesas && $baixaProcessoDepois->where('cd_tipo_financeiro_tfn', \TipoFinanceiro::ENTRADA)->sum('vl_baixa_honorario_bho') > 0) {
            $processoTaxaHonorario->fl_pago_cliente_pth = 'P';
        }

        if ($baixaProcessoDepois->where('cd_tipo_financeiro_tfn', \TipoFinanceiro::ENTRADA)->sum('vl_baixa_honorario_bho') <= 0) {
            $processoTaxaHonorario->fl_pago_cliente_pth = 'N';
        }

        $processoTaxaHonorario->saveOrFail();


        echo json_encode($baixaHonorario);
    }

    public function excluirBaixaSaida($id)
    {
        $baixaProcesso = BaixaHonorario::with('anexoFinanceiro')->where('cd_conta_con', $this->conta)->where('cd_baixa_honorario_bho', $id)->first();

        $processoTaxa = $baixaProcesso->cd_processo_taxa_honorario_pth;

        BaixaHonorario::where('cd_conta_con', $this->conta)
                                    ->where('cd_baixa_honorario_bho', $id)
                                    ->delete();


        $baixaHonorario = BaixaHonorario::with('anexoFinanceiro')->with('tipoBaixaHonorario')->where('cd_conta_con', $this->conta)->where('cd_processo_taxa_honorario_pth', $baixaProcesso->cd_processo_taxa_honorario_pth)->where('cd_tipo_financeiro_tfn', \TipoFinanceiro::SAIDA)->orderBy('cd_baixa_honorario_bho')->get();

        if (!empty($baixaProcesso->anexoFinanceiro)) {
            $this->entradaFileExcluir($baixaProcesso->anexoFinanceiro->cd_anexo_financeiro_afn);
        }


        $baixaProcessoDepois = BaixaHonorario::with('anexoFinanceiro')->where('cd_conta_con', $this->conta)->where('cd_processo_taxa_honorario_pth', $baixaProcesso->cd_processo_taxa_honorario_pth)->where('cd_tipo_financeiro_tfn', \TipoFinanceiro::SAIDA)->get();

        $processoTaxaHonorario = ProcessoTaxaHonorario::where('cd_conta_con', $this->conta)->where('cd_processo_taxa_honorario_pth', $processoTaxa)->first();

        $totalDespesas = $processoTaxaHonorario->processo->tiposDespesa->where('pivot.fl_despesa_reembolsavel_pde', 'S')->where('pivot.cd_tipo_entidade_tpe', \TipoEntidade::CORRESPONDENTE)->sum('pivot.vl_processo_despesa_pde');

        if ($baixaProcessoDepois->where('cd_tipo_financeiro_tfn', \TipoFinanceiro::SAIDA)->sum('vl_baixa_honorario_bho') < $processoTaxaHonorario->vl_taxa_honorario_correspondente_pth+$totalDespesas && $baixaProcessoDepois->where('cd_tipo_financeiro_tfn', \TipoFinanceiro::SAIDA)->sum('vl_baixa_honorario_bho') > 0) {
            $processoTaxaHonorario->fl_pago_correspondente_pth = 'P';
        }

        if ($baixaProcessoDepois->where('cd_tipo_financeiro_tfn', \TipoFinanceiro::SAIDA)->sum('vl_baixa_honorario_bho') <= 0) {
            $processoTaxaHonorario->fl_pago_correspondente_pth = 'N';
        }

        $processoTaxaHonorario->saveOrFail();

        echo json_encode($baixaHonorario);
    }

    public function entradaFile($id)
    {
        $anexo = AnexoFinanceiro::where('cd_anexo_financeiro_afn', $id)->where('cd_conta_con', $this->conta)->first();

        //  dd($anexo);
        return response()->download(storage_path($anexo->nm_local_anexo_financeiro_afn));
    }

    private function entradaFileExcluir($id)
    {
        $anexo = AnexoFinanceiro::where('cd_anexo_financeiro_afn', $id)->first();

        if ($anexo->delete()) {

            //Após excluir o registro, exclui o arquivo também
            if (file_exists(storage_path($anexo->nm_local_anexo_financeiro_afn))) {
                unlink(storage_path($anexo->nm_local_anexo_financeiro_afn));
            }

            return Response::json(array('message' => 'Registro excluído com sucesso'), 200);
        } else {
            return Response::json(array('message' => 'Erro ao excluir o registro'), 500);
        }
    }

    public function copiaArquivos($ids, $path, $retorno)
    {
        $name = json_decode($retorno->getContent())[0]->name;
        $source = $path.$ids[0].'/'.$name;

        for ($i=1; $i < count($ids); $i++) {
            $destino = $path.$ids[$i];

            if (!is_dir($destino)) {
                @mkdir(storage_path($destino), 0775);
            }
            copy(storage_path($source), storage_path($destino.'/'.$name));
        }
    }

    public function deletaArquivos($ids, $path, $request)
    {
        $name = $request->input('files')[0];

        for ($i=1; $i < count($ids); $i++) {
            $destino = $path.$ids[$i];

            unlink(storage_path($destino.'/'.$name));
        }
    }

    public function entradaAnexo(Request $request)
    {
        $ids = json_decode($request->id_processo_baixa);

        if (!empty($ids)) {
            $this->inicializaPastaDestinoEntrada($ids[0]);

            //Ação de enviar arquivo
            $retorno = $this->handler->handle($request);

            if (count($ids) > 1 and $request->isMethod('post')) {
                $path = "arquivos/$this->conta/entradas/anexos/";
                $this->copiaArquivos($ids, $path, $retorno);
            }

            if (count($ids) > 1 and $request->isMethod('delete')) {
                $path = "arquivos/$this->conta/entradas/anexos/";
                $this->deletaArquivos($ids, $path, $request);
            }

            return $retorno;
        } else {
            return '';
        }
    }

    public function inicializaPastaDestinoEntrada($id_processo_baixa)
    {
        $this->handler = new Handler(
            new Uploader($config = new Config, new ImageManager)
        );
        
        $destino = "arquivos/$this->conta/entradas/anexos/$id_processo_baixa";

        //Verificar se existe a pasta da conta, se não existir, criar a pasta com permissões de escrita
        if (!is_dir($destino)) {
            @mkdir(storage_path($destino), 0775);
        }

        $config['debug'] = true;
        $config['upload_dir'] = storage_path($destino);
        $config['upload_url'] = storage_path($destino);
    }

    public function saidaAnexo(Request $request)
    {
        $ids = json_decode($request->id_processo_baixa);

        if (!empty($ids)) {
            $this->inicializaPastaDestinoSaida($ids[0]);

            //Ação de enviar arquivo
            $retorno = $this->handler->handle($request);

            if (count($ids) > 1 and $request->isMethod('post')) {
                $path = "arquivos/$this->conta/saidas/anexos/";
                $this->copiaArquivos($ids, $path, $retorno);
            }

            if (count($ids) > 1 and $request->isMethod('delete')) {
                $path = "arquivos/$this->conta/saidas/anexos/";
                $this->deletaArquivos($ids, $path, $request);
            }

            return $retorno;
        } else {
            return '';
        }
    }

    public function inicializaPastaDestinoSaida($id_processo_baixa)
    {
        $this->handler = new Handler(
            new Uploader($config = new Config, new ImageManager)
        );
        
        $destino = "arquivos/$this->conta/saidas/anexos/$id_processo_baixa";

        //Verificar se existe a pasta da conta, se não existir, criar a pasta com permissões de escrita
        if (!is_dir($destino)) {
            @mkdir(storage_path($destino), 0775);
        }

        $config['debug'] = true;
        $config['upload_dir'] = storage_path($destino);
        $config['upload_url'] = storage_path($destino);
    }
}
