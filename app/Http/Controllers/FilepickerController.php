<?php

namespace App\Http\Controllers;

use App\Despesa;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Hazzard\Filepicker\Handler;
use Hazzard\Filepicker\Uploader;
use Illuminate\Support\Facades\Session;
use Intervention\Image\ImageManager;
use Hazzard\Config\Repository as Config;

class FilepickerController extends Controller
{

    protected $handler;

    public function __construct()
    {

        $this->middleware('auth');
        $this->conta = \Session::get('SESSION_CD_CONTA');
        
    }

    public function index()
    {
        return view('upload/index');
    }

    public function inicializaPastaDestino($id_despesa){

        $this->handler = new Handler(
            new Uploader($config = new Config, new ImageManager)
        );
        
        //Nesse ponto, ele verifica se existe despesa cadastrada. Se existir, ele atribui o id, caso contrário, ele cria uma despesa e atribui o id para ela
        if($id_despesa == null){

            $despesa = new Despesa();
            $despesa->cd_conta_con = $this->conta;
            $despesa->cd_tipo_despesa_tds = 12;
            $despesa->save();

            $id_despesa = $despesa->cd_despesa_des;
            \Session::put('id_despesa_folder',$id_despesa);
        }

        $destino = "despesas/$this->conta/$id_despesa";        

        //Verificar se existe a pasta da conta, se não existir, criar a pasta com permissões de escrita
        if(!is_dir($destino)){
            @mkdir(storage_path($destino), 0775);
        }

        $config['upload_dir'] =  storage_path($destino);
        $config['upload_url'] = storage_path($destino);

        return $id_despesa;

    }

    /**
     * Handle an incoming HTTP request.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request)
    {

        $method = $request->get('_method', $request->getMethod());
        $id_despesa = $this->inicializaPastaDestino($request->id_despesa);
        $request->merge(['id_despesa' => $id_despesa]);

        //Ação de enviar arquivo
        if($method == 'POST'){

            try {
                return $this->handler->postAction($request);
            } catch (UploadException $e) {
                echo 'Upload error: ' . $e->getMessage();
            }

        }else{

            return $this->handler->handle($request);

        }
    }
}