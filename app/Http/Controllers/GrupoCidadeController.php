<?php

namespace App\Http\Controllers;

use App\GrupoCidade;
use App\GrupoCidadeRelacionamento;
use App\Cidade;
use App\Estado;
use App\Http\Requests\GrupoCidadeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Laracasts\Flash\Flash;
use Illuminate\Support\Facades\DB;

class GrupoCidadeController extends Controller
{

    private $cdContaCon;

    public function __construct()
    {
        $this->middleware('auth');
        $this->cdContaCon = 1;
    }

    public function index()
    {

        $gruposCidades = GrupoCidade::where('cd_conta_con', $this->cdContaCon)->orderBy('nm_grupo_cidade_grc')->get();   

        //dd($gruposCidades[0]->grupoCidadeRelacionamentos[0]->cidade->estado);

        return view('configuracoes/grupos-de-cidades',['gruposCidades' => $gruposCidades]);
    }

    public function novo()
    {

        //$cidades = Cidade::orderBy('cd_estado_est')->orderBy('nm_cidade_cde')->get();   
        $estados = Estado::orderBy('nm_estado_est')->get();   

        return view('configuracoes/novo-grupo-de-cidades',['estados' => $estados]);
    }

    public function editar($cdGrupo){

        $estadosSelecionados = array();

        $grupo = GrupoCidade::where('cd_grupo_cidade_grc',$cdGrupo)->where('cd_conta_con', $this->cdContaCon)->first();

        $relacionamentos = GrupoCidadeRelacionamento::with(['cidade' => function($query){
            $query->with('estado');

        }])->where('cd_grupo_cidade_grc', $cdGrupo)->where('cd_conta_con', $this->cdContaCon)->get(); 

        // foreach ($relacionamentos as $relacionamento) {

        //     if(!in_array($relacionamento->cidade->cd_estado_est, $estadosSelecionados)){
        //          $estadosSelecionados[] = $relacionamento->cidade->cd_estado_est;
        //     }

        // }
        
        //$cidades = Cidade::whereIn('cd_estado_est',$estadosSelecionados)->orderBy('cd_estado_est')->orderBy('nm_cidade_cde')->get();   
        $estados = Estado::orderBy('nm_estado_est')->get();   

        return view('configuracoes/editar-grupo-de-cidades',['estados' => $estados,'relacionamentos' => $relacionamentos,'grupo' => $grupo]);
    }


    public function show($id)
    {
        $gruposCidades = GrupoCidade::findOrFail($id);     
        return response()->json($gruposCidades);  
    }

    public function store(GrupoCidadeRequest $request)
    {

        DB::beginTransaction();
    
        $grupoCidades = GrupoCidade::create([
            'nm_grupo_cidade_grc' => $request->nm_grupo_cidade_grc,
            'cd_conta_con'        => $this->cdContaCon
        ]);

        $request->cidades = array_unique($request->cidades);
     
        if($grupoCidades){
            
            foreach ($request->cidades as $cidade) {
                
                $grupoCidadeRelacionamento = GrupoCidadeRelacionamento::create([

                        'cd_cidade_cde'       => (int)$cidade,
                        'cd_conta_con'        => $this->cdContaCon,
                        'cd_grupo_cidade_grc' => $grupoCidades->cd_grupo_cidade_grc

                ]);

                if(!$grupoCidadeRelacionamento){
                    DB::rollBack();
                    Flash::error('Erro ao inserir dados');
                    return redirect('configuracoes/grupos-de-cidades');
                }

            }
        }

        DB::commit();    
       	Flash::success('Dados inseridos com sucesso');
        return redirect('configuracoes/grupos-de-cidades');

    }

    public function update(GrupoCidadeRequest $request,$id)
    {

        DB::beginTransaction();
        
        $request->cidades = array_unique($request->cidades);
        
        $gruposCidades = GrupoCidade::where('cd_conta_con',$this->cdContaCon)->where('cd_grupo_cidade_grc',$id)->first();

        $data = array_fill_keys($request->cidades, array('cd_conta_con' => $this->cdContaCon));

        $ret = $gruposCidades->cidades()->sync($data);
          
        if($ret){

            $gruposCidades->fill(['nm_grupo_cidade_grc' => $request->nm_grupo_cidade_grc]);
            
            if($gruposCidades->saveOrFail()){
                
                Flash::success('Dados atualizados com sucesso');
                DB::commit(); 
            }
        	
        }else{
			Flash::error('Erro ao atualizar dados');
            DB::rollBack();
        }

        return redirect('configuracoes/grupos-de-cidades');
    }

    public function destroy($id)
    {

        DB::beginTransaction();

        $gruposCidades = GrupoCidade::where('cd_conta_con',$this->cdContaCon)->where('cd_grupo_cidade_grc',$id)->first();

        $ret = $gruposCidades->cidades()->sync([]);
        
        if(!$ret){

            Flash::error('Erro ao atualizar dados');
            DB::rollBack();
            return Response::json(array('message' => 'Erro ao excluir o registro'), 500);
        	
        }else{

            $ret = $gruposCidades->delete();

            if(!$ret){
                
                Flash::error('Erro ao atualizar dados');
                DB::rollBack();
                return Response::json(array('message' => 'Erro ao excluir o registro'), 500);
                 
            }
        	
        }

        Flash::success('Dados atualizados com sucesso');
        DB::commit();
        return Response::json(array('message' => 'Registro excluído com sucesso'), 200);
    }

    public function cidades($id){

        $cidades = GrupoCidadeRelacionamento::with('cidade')->where('cd_grupo_cidade_grc',$id)->get();
        echo json_encode($cidades);

    }
}