<?php

namespace App\Http\Controllers;

use App\Area;
use App\Conta;
use App\TipoServico;
use App\TaxaHonorario;
use App\ContaCorrespondente;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use App\Http\Requests\TipoServicoRequest;

class TipoServicoController extends Controller
{

    private $cdContaCon;

    public function __construct()
    {
        $this->middleware('auth');
        $this->cdContaCon = \Session::get('SESSION_CD_CONTA');
    }

    public function index()
    {

        $tipos = TipoServico::where('cd_conta_con', $this->cdContaCon)->orderBy('nm_tipo_servico_tse')->get();
        
        return view('configuracoes/tipos-de-servico',['tipos' => $tipos]);
    }

    public function show($id)
    {
        
        $area = AreaDireito::findOrFail($id);     
        return response()->json($area);  
    }

    public function store(TipoServicoRequest $request)
    {
        $tipo = new TipoServico();

        if(isset($request->fl_honorario_tse)){
            $request->merge(['fl_honorario_tse' => 'S']);
        }else{
            $request->merge(['fl_honorario_tse' => 'N']);
        }

        if(isset($request->fl_despesa_tse)){
            $request->merge(['fl_despesa_tse' => 'S']);
        }else{
            $request->merge(['fl_despesa_tse' => 'N']);
        }
        
        $request->merge(['cd_conta_con' => $this->cdContaCon]);

        $tipo->fill($request->all());

        if($tipo->saveOrFail())
        	Flash::success('Dados inseridos com sucesso');
        else
			Flash::error('Erro ao inserir dados');
        
        return redirect('configuracoes/tipos-de-servico');

    }

    public function update(Request $request,$id)
    {
        $tipo = TipoServico::where('cd_conta_con',$this->cdContaCon)->findOrFail($id);

        if(isset($request->fl_honorario_tse)){
            $request->merge(['fl_honorario_tse' => 'S']);
        }else{
            $request->merge(['fl_honorario_tse' => 'N']);
        }

        if(isset($request->fl_despesa_tse)){
            $request->merge(['fl_despesa_tse' => 'S']);
        }else{
            $request->merge(['fl_despesa_tse' => 'N']);
        }
        
        $request->merge(['cd_conta_con' => $this->cdContaCon]);

        $tipo->fill($request->all());

        if($tipo->saveOrFail())
        	Flash::success('Dados atualizados com sucesso');
        else
			Flash::error('Erro ao atualizar dados');

        return redirect('configuracoes/tipos-de-servico');
    }

    public function destroy($id)
    {
        $tipo = TipoServico::where('cd_conta_con',$this->cdContaCon)->findOrFail($id);
        
        if($tipo->delete())
        	return Response::json(array('message' => 'Registro excluído com sucesso'), 200);
        else
        	return Response::json(array('message' => 'Erro ao excluir o registro'), 500);
    }

    public function consultar($id)
    {
        $tipo = TipoServico::where('cd_conta_con',$this->cdContaCon)->findOrFail($id);
        $correspondentes = array();
         //Carrega os serviços
        $honorarios = TaxaHonorario::where('cd_conta_con',$this->cdContaCon)
                                    ->select('cd_entidade_ete')
                                    ->where('cd_tipo_servico_tse',$id)
                                    ->groupBy('cd_entidade_ete')
                                    ->get(); 

        foreach ($honorarios as $h) {

            $cc = ContaCorrespondente::where('cd_entidade_ete',$h->cd_entidade_ete)->first();
            
            if($cc){

                $correspondentes[] = $cc->nm_conta_correspondente_ccr;
            }

        }

        $retorno = array('dados' => $correspondentes, 'total' => count($correspondentes));
        
        return Response::json($retorno);

    }
}