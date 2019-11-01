<?php

namespace App\Http\Controllers;

use App\Despesa;
use App\TipoDespesa;
use App\CategoriaDespesa;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use App\Http\Requests\DespesaRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;

class DespesasController extends Controller
{

    private $conta;

    public function __construct()
    {
        $this->middleware('auth');
        $this->conta = \Session::get('SESSION_CD_CONTA');
    }

    public function index()
    {

    }

    public function getTiposMultiple(Request $request)
    {

        $categorias = explode(',',$request->categorias);

        if(!empty($categorias[0])){

            $tipos = TipoDespesa::where('cd_conta_con',$this->conta)->whereIn('cd_categoria_despesa_cad',$categorias)->orderBy('nm_tipo_despesa_tds','ASC')->get();     

            return response()->json($tipos); 
        
        }else{
            return null;

        }
    }

    public function getTipos($id)
    {
        $tipos = TipoDespesa::where('cd_conta_con',$this->conta)->where('cd_categoria_despesa_cad',$id)->orderBy('nm_tipo_despesa_tds','ASC')->get();     
        return response()->json($tipos); 
    }

     public function getCategorias($id)
    {
        $categorias = CategoriaDespesa::where('cd_conta_con',$this->conta)->where('cd_categoria_despesa_cad',$id)->orderBy('nm_categoria_despesa_cad','ASC')->first();     
        return response()->json($categorias); 
    }

    public function show($id)
    {   
        $despesa = Despesa::findOrFail($id);   
        return view('despesas/detalhes', ['despesa' => $despesa ]);
    }

    public function novo()
    {   
        $categorias = CategoriaDespesa::where('cd_conta_con',$this->conta)->orderBy('nm_categoria_despesa_cad','ASC')->get();
        $despesas = TipoDespesa::where('cd_conta_con',$this->conta)->orderBy('nm_tipo_despesa_tds','ASC')->get();

        return view('despesas/novo', ['despesas' => $despesas, 'categorias' => $categorias ]); 
    }

    public function editar($id)
    {   
        $despesa = Despesa::findOrFail($id);  
        $categorias = CategoriaDespesa::where('cd_conta_con',$this->conta)->orderBy('nm_categoria_despesa_cad','ASC')->get();
        $despesas = TipoDespesa::where('cd_conta_con',$this->conta)->orderBy('nm_tipo_despesa_tds','ASC')->get();

        return view('despesas/editar', ['despesa' => $despesa, 'despesas' => $despesas, 'categorias' => $categorias ]); 
    }

    public function buscar(Request $request)
    {
        
        \Session::put('fl_buscar_despesa',true);
        ($request->dt_vencimento_inicial) ? \Session::put('dt_vencimento_inicial',date('Y-m-d',strtotime(str_replace('/','-',$request->dt_vencimento_inicial)))) : \Session::put('dt_vencimento_inicial',null);
        ($request->dt_vencimento_final) ? \Session::put('dt_vencimento_final',date('Y-m-d',strtotime(str_replace('/','-',$request->dt_vencimento_final)))) : \Session::put('dt_vencimento_final',null);
        ($request->dt_pagamento_inicial) ? \Session::put('dt_pagamento_inicial',date('Y-m-d',strtotime(str_replace('/','-',$request->dt_pagamento_inicial)))) : \Session::put('dt_pagamento_inicial',null);
        ($request->dt_pagamento_final) ? \Session::put('dt_pagamento_final',date('Y-m-d',strtotime(str_replace('/','-',$request->dt_pagamento_final)))) : \Session::put('dt_pagamento_final',null);
        \Session::put('categoria',$request->cd_categoria_despesa_cad);
        \Session::put('despesa',$request->cd_tipo_despesa_tds);
        \Session::put('situacao',$request->situacao);

        return redirect('despesas/lancamentos');
    }

    public function lancamentos()
    {   

        if(!session('fl_buscar_despesa')){

            \Session::put('dt_vencimento_inicial',null);
            \Session::put('dt_vencimento_final',null);
            \Session::put('dt_pagamento_inicial',null);
            \Session::put('dt_pagamento_final',null);
            \Session::put('categoria',null);
            \Session::put('despesa',null);
            \Session::put('situacao',null);

            $dt_vencimento_inicial = null;
            $dt_vencimento_final = null;
            $dt_pagamento_inicial = null;
            $dt_pagamento_final = null;
            $categoria = null;
            $despesa = null;
            $situacao = null;

        }else{
            $dt_vencimento_inicial = session('dt_vencimento_inicial');
            $dt_vencimento_final = session('dt_vencimento_final');
            $dt_pagamento_inicial = session('dt_pagamento_inicial');
            $dt_pagamento_final = session('dt_pagamento_final');
            $categoria = session('categoria');
            $despesa = session('despesa');
            $situacao = session('situacao');
        }

        $categorias = CategoriaDespesa::where('cd_conta_con',$this->conta)->orderBy('nm_categoria_despesa_cad','ASC')->get();
        $despesas = TipoDespesa::where('cd_conta_con',$this->conta)->orderBy('nm_tipo_despesa_tds','ASC')->get();
        
        $lancamentos = Despesa::with('tipo')
                                ->with('tipo.categoriaDespesa')
                                ->where('despesa_des.cd_conta_con', $this->conta)
                                ->when(!session('fl_buscar_despesa'), function($sql){
                                    $sql->whereNull('dt_pagamento_des');
                                })
                                ->when(!empty($categoria), function($join) use($categoria){
                            
                                    $join->join('tipo_despesa_tds', function($join) use ($categoria){
                                        $join->on('despesa_des.cd_tipo_despesa_tds','=','tipo_despesa_tds.cd_tipo_despesa_tds');
                                            $join->join('categoria_despesa_cad', function($join) use ($categoria){
                                                $join->on('tipo_despesa_tds.cd_categoria_despesa_cad','=','categoria_despesa_cad.cd_categoria_despesa_cad');
                                                $join->where('tipo_despesa_tds.cd_categoria_despesa_cad','=',$categoria);
                                            });                                                            
                                    });
                                })
                                //Operações com datas. Ao preencher uma data, a outra é obrigatória
                                ->when(!empty($dt_vencimento_inicial) and !empty($dt_vencimento_final), function($sql) use($dt_vencimento_inicial,$dt_vencimento_final){
                                    $sql->whereBetween('dt_vencimento_des',[$dt_vencimento_inicial,$dt_vencimento_final]);
                                })
                                ->when(!empty($dt_pagamento_inicial) and !empty($dt_pagamento_final), function($sql) use($dt_pagamento_inicial,$dt_pagamento_final){
                                    $sql->whereBetween('dt_pagamento_des',[$dt_pagamento_inicial,$dt_pagamento_final]);
                                })
                                //Fim das operações com data
                                ->when(!empty($despesa), function($sql) use($despesa){
                                    $sql->where('despesa_des.cd_tipo_despesa_tds',$despesa);
                                })
                                ->when(!empty($situacao), function($sql) use($situacao){

                                    if($situacao == 2){
                                        $sql->whereNull('dt_pagamento_des');
                                    }
                                    if($situacao == 1){

                                        $sql->whereNotNull('dt_pagamento_des');
                                    }                                   
                                })
                                ->orderBy('dt_vencimento_des')
                                ->get();
                               

        \Session::put('fl_buscar_despesa',false);

        return view('despesas/lancamentos', ['lancamentos' => $lancamentos, 'despesas' => $despesas, 'categorias' => $categorias]); 
    }

    public function store(DespesaRequest $request)
    {
        try {

            $request->vl_valor_des = str_replace(",", ".", $request->vl_valor_des);
            if(!empty($request->dt_vencimento_des)) $request->merge(['dt_vencimento_des' => date('Y-m-d',strtotime(str_replace('/','-',$request->dt_vencimento_des)))]);
            if(!empty($request->dt_pagamento_des)) $request->merge(['dt_pagamento_des' => date('Y-m-d',strtotime(str_replace('/','-',$request->dt_pagamento_des)))]);
            $request->merge(['vl_valor_des' => $request->vl_valor_des]);
            $request->merge(['cd_conta_con' => $this->conta]);

            if($request->file){
                //Parte responsável pelo upload
                $destino = "despesas/$this->conta/";
     
                $fileName = time().'.'.request()->file->getClientOriginalExtension();

                if(!is_dir($destino)){
                    @mkdir(storage_path($destino), 0775);
                }
         
                if(request()->file->move(storage_path($destino), $fileName)){
                    $request->merge(['anexo_des' => $fileName]);
                }
                //Fim do upload
            }

            $despesa = new Despesa();
            $despesa->fill($request->all());

            if($despesa->save()){
                Flash::success('Despesa cadastrada com sucesso');
                return redirect('despesas/lancamentos');
            }else{
                Flash::error('Erro ao cadastrar despesa. Verifique os dados e tente novamente');
                return redirect()->back();
            }
            
        }catch (Exception $e) {
            


        }

    }

    public function update(DespesaRequest $request, $id)
    {

        $request->vl_valor_des = str_replace(",", ".", $request->vl_valor_des);
        if(!empty($request->dt_vencimento_des)) $request->merge(['dt_vencimento_des' => date('Y-m-d',strtotime(str_replace('/','-',$request->dt_vencimento_des)))]);
        if(!empty($request->dt_pagamento_des)) $request->merge(['dt_pagamento_des' => date('Y-m-d',strtotime(str_replace('/','-',$request->dt_pagamento_des)))]);
        $request->merge(['vl_valor_des' => $request->vl_valor_des]);
        $request->merge(['cd_conta_con' => $this->conta]);

        if($request->file){
            //Parte responsável pelo upload
            $destino = "despesas/$this->conta/";
     
            $fileName = time().'.'.request()->file->getClientOriginalExtension();

            if(!is_dir($destino)){
                @mkdir(storage_path($destino), 0775);
            }
         
            if(request()->file->move(storage_path($destino), $fileName)){
                $request->merge(['anexo_des' => $fileName]);
            }
            //Fim do upload
        }

        $despesa = Despesa::findOrFail($id);
        $despesa->fill($request->all());

        if($despesa->save()){
            Flash::success('Despesa atualizada com sucesso');
            return redirect('despesas/lancamentos');
        }else{
            Flash::error('Erro ao atualizar despesa. Verifique os dados e tente novamente');
            return redirect()->back();
        }

    }

    public function destroy($id)
    {   

        $despesa = Despesa::findOrFail($id);
        
        if($despesa->delete())
            return Response::json(array('message' => 'Registro excluído com sucesso'), 200);
        else
            return Response::json(array('message' => 'Erro ao excluir o registro'), 500);
        
    }

    public function balanco()
    {   
        $categorias = CategoriaDespesa::where('cd_conta_con',$this->conta)->orderBy('nm_categoria_despesa_cad','ASC')->get();
        $despesas = TipoDespesa::where('cd_conta_con',$this->conta)->orderBy('nm_tipo_despesa_tds','ASC')->get();
        $lancamentos = Despesa::where('cd_conta_con', $this->conta)->orderBy('dt_vencimento_des')->get();

        return view('despesas/balanco', ['lancamentos' => $lancamentos, 'despesas' => $despesas, 'categorias' => $categorias]); 
    }

    public function download($id)
    { 
        $id = \Crypt::decrypt($id);
        $conta = $this->conta;
        $despesa = Despesa::findOrFail($id);
        return response()->download(storage_path('despesas/'.$conta.'/'.$despesa->anexo_des));
    }
}