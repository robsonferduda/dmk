<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Requests\UsuarioRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Laracasts\Flash\Flash;

class UsuarioController extends Controller
{

    private $cdContaCon;

    public function __construct()
    {
        $this->middleware('auth');
        $this->cdContaCon = 1;
    }

    public function index()
    {

        $usuarios = User::where('cd_conta_con', $this->cdContaCon)->orderBy('name')->get();   
        return view('usuario/usuarios',['usuarios' => $usuarios]);
    }

    public function show($id)
    {
        $vara = Usuario::findOrFail($id);     
        return response()->json($vara);  
    }

    public function store(UsuarioRequest $request)
    {
        $vara = new Usuario();
 
        $request->merge(['cd_conta_con' => $this->cdContaCon]);

        $vara->fill($request->all());

        if($vara->saveOrFail())
        	Flash::success('Dados inseridos com sucesso');
        else
			Flash::error('Erro ao inserir dados');
        
        return redirect('configuracoes/usuarios');

    }

    public function update(Request $request,$id)
    {
        $vara = Usuario::where('cd_conta_con',$this->cdContaCon)->findOrFail($id);

        $request->merge(['cd_conta_con' => $this->cdContaCon]);

        $vara->fill($request->all());

        if($vara->saveOrFail())
        	Flash::success('Dados atualizados com sucesso');
        else
			Flash::error('Erro ao atualizar dados');

        return redirect('configuracoes/usuarios');
    }

    public function destroy($id)
    {
        $vara = Usuario::where('cd_conta_con',$this->cdContaCon)->findOrFail($id);
        
        if($vara->delete())
        	return Response::json(array('message' => 'Registro excluído com sucesso'), 200);
        else
        	return Response::json(array('message' => 'Erro ao excluir o registro'), 500);
    }
}