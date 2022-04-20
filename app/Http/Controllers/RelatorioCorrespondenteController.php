<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Laracasts\Flash\Flash;
use App\RelatorioJasper;
use Illuminate\Http\Request;
use App\Conta;
use App\Exports\Correspondente\PagamentoCorrespondenteDetalhadoXlsExport;
use App\Exports\Correspondente\PagamentoCorrespondenteSumarizadoXlsExport;

class RelatorioCorrespondenteController extends Controller
{
    public $conta;

    public function __construct()
    {
        $this->middleware('auth');
        $this->conta = \Session::get('SESSION_CD_CONTA');
    }

    public function relatorios()
    {
        return view('correspondente/relatorios', ['arquivos' => $this->getFiles()]);

        // $fileName = "correspondente";
        // $sourceName = "extrato-correspondentes.jrxml";

        // $bancoQuery = '';

        // if(!empty($bancoQuery)) $bancoQuery = " AND t2.cd_banco_ban = '002' ";

        // $parametros = array('bancoQuery' => $bancoQuery);

        // $jasper = new RelatorioJasper();
        // return $jasper->processar($parametros,$sourceName,$fileName);
    }

    public function buscar(Request $request)
    {
        $blade = false;

        if (\Helper::validaData($request->dtInicio) && \Helper::validaData($request->dtFim) && strtotime(str_replace('/', '-', $request->dtInicio)) <= strtotime(str_replace('/', '-', $request->dtFim))) {
            $conta = Conta::where('cd_conta_con', $this->conta)->select('nm_razao_social_con')->first();


            $dtInicio = date('Y-m-d', strtotime(str_replace('/', '-', $request->dtInicio)));
            $dtFim    = date('Y-m-d', strtotime(str_replace('/', '-', $request->dtFim)));

            $dataQuery = " AND dt_prazo_fatal_pro between '$dtInicio' and '$dtFim' ";

            $empresa = $conta->nm_razao_social_con;

            $bancoQuery = '';
            $correspondenteQuery = '';
            $statusQuery = '';

            if ($request->relatorio == 'pagamento-correspondentes-por-processo') {
                $fileName   = 'Pagamento_de_Correspondentes_Detalhado';

                if ($request->extensao == 'pdf') {
                    $sourceName = 'extrato-correspondentes-por-processo.jrxml';
                } else {

                
                    $retorno = $this->consultaPagamentoCorrespondenteDetalhado(
                        $request->cd_correspondente_cor,
                        $request->cd_banco_ban,
                        $request->finalizado,
                        $dtInicio,
                        $dtFim,
                        $this->conta
                    );
    
                    $processos = [];
                    $registrosBancarios = [];
                    $idProcessoAnt = '';
                    $valorTotal = 0;
                    foreach($retorno as $ret) {

                        if($idProcessoAnt != $ret->cd_processo_pro) {
                            
                            $registrosBancarios = [];
                            $valorTotal = $valorTotal + $ret->vl_taxa_honorario_correspondente_pth + $ret->vl_processo_despesa_pde;
                           
                        }
                        
                        $registrosBancarios[] = array(
                            'titular' => $ret->nm_titular_dba,
                            'tipo' => $ret->nm_tipo_conta_tcb,
                            'cpf_cnpj' => $ret->nu_cpf_cnpj_dba,
                            'banco' => $ret->cd_banco_ban.' - '.$ret->nm_banco_ban,
                            'agencia' => $ret->nu_agencia_dba,
                            'conta' => $ret->nu_conta_dba,
                            'pix' => $ret->dc_pix_dba
                             
                        );

                        $processos[$ret->cd_processo_pro] = array(
                            'processo' => $ret->nu_processo_pro,
                            'valor' => (float) $ret->vl_taxa_honorario_correspondente_pth + (float) $ret->vl_processo_despesa_pde,
                            'tipo' => $ret->nm_tipo_servico_tse,
                            'data' => date('d-m-Y', strtotime($ret->dt_prazo_fatal_pro)),
                            'registros_bancarios' => $registrosBancarios,
                            'razao_social' => $ret->nm_razao_social_con,
                            'emails' => $ret->emails
                        );


                        $idProcessoAnt = $ret->cd_processo_pro;

                    }

                    $dados = [
                        'processos' => $processos,
                        'valor_total' => $valorTotal,
                        'empresa' => $empresa
                    ];

                    \File::makeDirectory(storage_path().'/arquivos/'.$this->conta.'/reports/correspondente', $mode = 0744, true, true);

                    $path = $this->conta.'/reports/correspondente/'. time() . "_$fileName.xlsx";
     
                    \Excel::store(new PagamentoCorrespondenteDetalhadoXlsExport($dados), $path, 'arquivos', \Maatwebsite\Excel\Excel::XLSX);

                    $blade = true;
                   
                    //$sourceName = 'extrato-correspondentes-por-processo-xls.jrxml';
                }

                

                if (!empty($request->cd_correspondente_cor)) {
                    $correspondenteQuery = "  AND t8.cd_correspondente_cor =  {$request->cd_correspondente_cor} ";
                }

                if (!empty($request->cd_banco_ban)) {
                    $bancoQuery = " AND t2.cd_banco_ban = '".str_pad($request->cd_banco_ban, 3, '0', STR_PAD_LEFT)."' ";
                }
            }

            if ($request->relatorio == 'pagamento-correspondentes-sumarizado') {

                $fileName   = 'Pagamento_de_Correspondentes_Sumarizado';

                if ($request->extensao == 'pdf') {
                    $sourceName = 'extrato-correspondentes.jrxml';
                } else {
                   
                    $retorno = $this->consultaPagamentoCorrespondenteSumarizado(
                        $request->cd_correspondente_cor,
                        $request->cd_banco_ban,
                        $request->finalizado,
                        $dtInicio,
                        $dtFim,
                        $this->conta
                    );
    
                    $correspondentes = [];
                    $registrosBancarios = [];
                    $idCorrespondente = '';
                    $valorTotal = 0;

                    //dd($retorno);

                    foreach($retorno as $ret) {

                        if($idCorrespondente != $ret->cd_correspondente_cor) {
                            
                            $registrosBancarios = [];
                            $valorTotal = $valorTotal + $ret->vl_taxa_honorario_correspondente_pth + $ret->vl_processo_despesa_pde;
                           
                        }
                        
                        $registrosBancarios[] = array(
                            'titular' => $ret->nm_titular_dba,
                            'tipo' => $ret->nm_tipo_conta_tcb,
                            'cpf_cnpj' => $ret->nu_cpf_cnpj_dba,
                            'banco' => $ret->cd_banco_ban.' - '.$ret->nm_banco_ban,
                            'agencia' => $ret->nu_agencia_dba,
                            'conta' => $ret->nu_conta_dba,
                            'pix' => $ret->dc_pix_dba
                             
                        );

                        $correspondentes[$ret->cd_correspondente_cor] = array(                    
                            'valor' => (float) $ret->vl_taxa_honorario_correspondente_pth + (float) $ret->vl_processo_despesa_pde,                      
                            'registros_bancarios' => $registrosBancarios,
                            'razao_social' => $ret->nm_razao_social_con,
                            'emails' => $ret->emails
                        );


                        $idCorrespondente = $ret->cd_correspondente_cor;

                    }

                    //dd($correspondentes);

                    $dados = [
                        'correspondentes' => $correspondentes,
                        'valor_total' => $valorTotal,
                        'empresa' => $empresa
                    ];

                    \File::makeDirectory(storage_path().'/arquivos/'.$this->conta.'/reports/correspondente', $mode = 0744, true, true);

                    $path = $this->conta.'/reports/correspondente/'. time() . "_$fileName.xlsx";
     
                    \Excel::store(new PagamentoCorrespondenteSumarizadoXlsExport($dados), $path, 'arquivos', \Maatwebsite\Excel\Excel::XLSX);

                    $blade = true;

                }
               
                if (!empty($request->cd_correspondente_cor)) {
                    $correspondenteQuery = "  AND t8.cd_correspondente_cor =  {$request->cd_correspondente_cor} ";
                }

                if (!empty($request->cd_banco_ban)) {
                    $bancoQuery = " AND t4.cd_banco_ban = '".str_pad($request->cd_banco_ban, 3, '0', STR_PAD_LEFT)."' ";
                }
            }

            if (!empty($request->finalizado)) {
                $statusQuery = ' AND t3.cd_status_processo_stp = '.\StatusProcesso::FINALIZADO;
            }
            
            $parametros = array('bancoQuery'          => $bancoQuery,
                                'dataQuery'           => $dataQuery,
                                'conta'               => $this->conta,
                                'dataInicio'          => $request->dtInicio,
                                'dataFim'             => $request->dtFim,
                                'empresa'             => $empresa,
                                'correspondenteQuery' => $correspondenteQuery,
                                'statusQuery'         => $statusQuery);

            $jasper = new RelatorioJasper();

            if($blade === false) {
                $jasper->processar($parametros, $sourceName, $fileName, false, $request->extensao);
            }
        } else {
            Flash::error('Data(s) inválida(s) !');
        }

        return \Redirect::back()->with('dtInicio', str_replace('/', '', $request->dtInicio))
                                ->with('dtFim', str_replace('/', '', $request->dtFim))
                                ->with('relatorio', $request->relatorio)
                                ->with('extensao', $request->extensao)
                                ->with('finalizado', $request->finalizado)
                                ->with('banco', $request->cd_banco_ban)
                                ->with('correspondente', $request->cd_correspondente_cor)
                                ->with('nmCorrespondente', $request->nm_correspondente_cor); //view('correspondente/relatorios',['arquivos' => $this->getFiles()]);
    }

    private function getFiles()
    {
        \File::makeDirectory(storage_path().'/arquivos/'. $this->conta .'/reports/correspondente', $mode = 0744, true, true);

        $arquivos = array();

        $files = collect(\File::allFiles(storage_path().'/arquivos/'. $this->conta .'/reports/correspondente'))
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
        \Storage::disk('arquivos')->delete($this->conta .'/reports/correspondente/'.$nome);

        return \Response::json(array('message' => 'Registro excluído com sucesso'), 200);
    }

    public function arquivo($nome)
    {
        //dd(\Storage::disk('reports')->get("$this->conta/".$nome));
        return response()->download(storage_path('arquivos/'. $this->conta .'/reports/correspondente/'.$nome));
    }

    private function consultaPagamentoCorrespondenteDetalhado($correspondente = NULL, $banco = NULL, $status = NULL, $dtInicio, $dtFim, $conta) {

        $sql = "SELECT 
                    t3.cd_processo_pro,
                    nu_processo_pro,
                    t2.nm_titular_dba,
                    t2.nu_cpf_cnpj_dba,
                    t4.cd_banco_ban,
                    t4.nm_banco_ban,  
                    t2.nu_agencia_dba,
                    t2.dc_pix_dba,
                    t2.nu_conta_dba,
                    COALESCE (t5.vl_taxa_honorario_correspondente_pth, 0) as vl_taxa_honorario_correspondente_pth,
                    COALESCE(sum(t9.vl_processo_despesa_pde),0) as vl_processo_despesa_pde ,
                    t6.cd_tipo_conta_tcb,
                    t6.nm_tipo_conta_tcb,
                    t8.nm_conta_correspondente_ccr as nm_razao_social_con,
                    t7.nm_tipo_servico_tse,
                    t3.dt_prazo_fatal_pro,
                    (SELECT array_to_string (ARRAY(select dc_endereco_eletronico_ede from endereco_eletronico_ele a1 where a1.cd_entidade_ete  = t8.cd_entidade_ete), ' | '::text)  AS array_to_string) as emails
                FROM
                    processo_pro t3
                    INNER JOIN processo_taxa_honorario_pth t5 ON (t3.cd_processo_pro = t5.cd_processo_pro and t5.deleted_at is null)
                    INNER JOIN tipo_servico_tse t7 ON (t5.cd_tipo_servico_correspondente_tse = t7.cd_tipo_servico_tse)
                    INNER JOIN conta_correspondente_ccr t8 ON (t3.cd_correspondente_cor = t8.cd_correspondente_cor)
                    LEFT JOIN dados_bancarios_dba t2 ON (t8.cd_entidade_ete = t2.cd_entidade_ete  and t2.deleted_at is null)
                    LEFT JOIN banco_ban t4 ON (t2.cd_banco_ban = t4.cd_banco_ban)
                    LEFT JOIN tipo_conta_banco_tcb t6 ON (t2.cd_tipo_conta_tcb = t6.cd_tipo_conta_tcb)
                    LEFT JOIN processo_despesa_pde t9 ON (t3.cd_processo_pro = t9.cd_processo_pro and fl_despesa_reembolsavel_pde = 'S' and cd_tipo_entidade_tpe = 6 and t3.deleted_at is null)
                WHERE
                    t3.deleted_at IS NULL
                    and
                    t8.cd_conta_con = $conta
                    and
                    t3.cd_conta_con = $conta
                    and 
                    dt_prazo_fatal_pro between '$dtInicio' and '$dtFim'";
                
                if(!empty($correspondente))
                    $sql .=  " and t8.cd_correspondente_cor =  $correspondente ";

                if(!empty($banco))
                    $sql .= " and t4.cd_banco_ban = '".str_pad($banco, 3, '0', STR_PAD_LEFT)."'";

                if(!empty($status))
                    $sql .= " and t3.cd_status_processo_stp = ".\StatusProcesso::FINALIZADO;
               
                $sql .= "
                    GROUP BY 
                        t3.cd_processo_pro,
                        nu_processo_pro,
                        t2.nm_titular_dba,
                        t2.nu_cpf_cnpj_dba,
                        t4.cd_banco_ban,
                        t4.nm_banco_ban,  
                        t2.nu_agencia_dba,
                        t2.dc_pix_dba,
                        t2.nu_conta_dba,
                        t5.vl_taxa_honorario_correspondente_pth,
                        t6.cd_tipo_conta_tcb,
                        t6.nm_tipo_conta_tcb,
                        t8.nm_conta_correspondente_ccr,
                        t7.nm_tipo_servico_tse,
                        t3.dt_prazo_fatal_pro,
                        t8.cd_entidade_ete
                    ORDER BY 
                        t3.cd_processo_pro desc
                    ";

        $resultado = \DB::select($sql);

        return $resultado;

    }

    private function consultaPagamentoCorrespondenteSumarizado($correspondente = NULL, $banco = NULL, $status = NULL, $dtInicio, $dtFim, $conta) {

        $sql = "SELECT 
                    t8.cd_correspondente_cor,
                    t2.nm_titular_dba,
                    t2.nu_cpf_cnpj_dba,
                    t4.cd_banco_ban,
                    t4.nm_banco_ban,  
                    t2.nu_agencia_dba,
                    t2.dc_pix_dba,
                    t2.nu_conta_dba,
                    COALESCE (sum(t5.vl_taxa_honorario_correspondente_pth),0) as vl_taxa_honorario_correspondente_pth,
                    COALESCE(sum(t9.vl_processo_despesa_pde),0) as vl_processo_despesa_pde ,
                    t6.cd_tipo_conta_tcb,
                    t6.nm_tipo_conta_tcb,
                    t8.nm_conta_correspondente_ccr as nm_razao_social_con,
                    (SELECT array_to_string (ARRAY(select dc_endereco_eletronico_ede from endereco_eletronico_ele a1 where a1.cd_entidade_ete  = t8.cd_entidade_ete), ' | '::text)  AS array_to_string) as emails
                FROM
                    processo_pro t3
                    INNER JOIN processo_taxa_honorario_pth t5 ON (t3.cd_processo_pro = t5.cd_processo_pro and t5.deleted_at is null)
                    INNER JOIN tipo_servico_tse t7 ON (t5.cd_tipo_servico_correspondente_tse = t7.cd_tipo_servico_tse)
                    INNER JOIN conta_correspondente_ccr t8 ON (t3.cd_correspondente_cor = t8.cd_correspondente_cor)
                    LEFT JOIN dados_bancarios_dba t2 ON (t8.cd_entidade_ete = t2.cd_entidade_ete  and t2.deleted_at is null)
                    LEFT JOIN banco_ban t4 ON (t2.cd_banco_ban = t4.cd_banco_ban)
                    LEFT JOIN tipo_conta_banco_tcb t6 ON (t2.cd_tipo_conta_tcb = t6.cd_tipo_conta_tcb)
                    LEFT JOIN processo_despesa_pde t9 ON (t3.cd_processo_pro = t9.cd_processo_pro and fl_despesa_reembolsavel_pde = 'S' and cd_tipo_entidade_tpe = 6 and t3.deleted_at is null)
                WHERE
                    t3.deleted_at IS NULL
                    and
                    t8.cd_conta_con = $conta
                    and
                    t3.cd_conta_con = $conta
                    and 
                    dt_prazo_fatal_pro between '$dtInicio' and '$dtFim'";
                
                if(!empty($correspondente))
                    $sql .=  " and t8.cd_correspondente_cor =  $correspondente ";

                if(!empty($banco))
                    $sql .= " and t4.cd_banco_ban = '".str_pad($banco, 3, '0', STR_PAD_LEFT)."'";

                if(!empty($status))
                    $sql .= " and t3.cd_status_processo_stp = ".\StatusProcesso::FINALIZADO;
               
                $sql .= "
                    GROUP BY 
                        t8.cd_correspondente_cor,
                        t2.nm_titular_dba,
                        t2.nu_cpf_cnpj_dba,
                        t4.cd_banco_ban,
                        t4.nm_banco_ban,  
                        t2.nu_agencia_dba,
                        t2.dc_pix_dba,
                        t2.nu_conta_dba,                        
                        t6.cd_tipo_conta_tcb,
                        t6.nm_tipo_conta_tcb,
                        t8.nm_conta_correspondente_ccr,
                        t8.cd_entidade_ete 
                    ORDER BY cd_correspondente_cor    
                    ";

        $resultado = \DB::select($sql);

        return $resultado;

    }
}
