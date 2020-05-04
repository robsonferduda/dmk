<?php

namespace App\Http\Controllers;

use App\Conta;
use App\Despesa;
use App\Processo;
use App\AnexoProcesso;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\File;
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
        
        $destino = "despesas/$this->conta/$id_despesa";        

        //Verificar se existe a pasta da conta, se não existir, criar a pasta com permissões de escrita
        if(!is_dir($destino)){
            @mkdir(storage_path($destino), 0775);
        }

        $config['debug'] = true;
        $config['upload_dir'] = storage_path($destino);
        $config['upload_url'] = storage_path($destino);

    }

    public function inicializaPastaProcesso($id_processo){

        $this->handler = new Handler(
            new Uploader($config = new Config, new ImageManager)
        );
        
        $destino = "processos/$id_processo";        

        //Verificar se existe a pasta da conta, se não existir, criar a pasta com permissões de escrita
        if(!is_dir($destino)){
            @mkdir(storage_path($destino), 0775);
        }

        $config['debug'] = true;
        $config['upload_dir'] = storage_path($destino);
        $config['upload_url'] = storage_path($destino);

    }

    public function handle(Request $request)
    {

        $this->inicializaPastaDestino($request->id_despesa);
        return $this->handler->handle($request);
    }

    public function arquivosProcesso(Request $request)
    {

        $this->inicializaPastaProcesso($request->id_processo);

        $method = $request->get('_method', $request->getMethod());

        if($method == 'GET'){

            $anexos = AnexoProcesso::where('cd_processo_pro',$request->id_processo)->get();

            $files = null;

            foreach ($anexos as $key => $anexo) {
                $files[$key] = new File(storage_path($anexo['nm_local_anexo_processo_apr'].$anexo['nm_anexo_processo_apr']));
                $files[$key]->tipo = $anexo->cd_tipo_anexo_processo_tap;
                $files[$key]->responsavel = Conta::where('cd_conta_con', $anexo->cd_conta_con)->first()->nm_razao_social_con;
            }

            foreach ($files as &$file) {

                $tipo = $file->tipo;
                $responsavel = $file->responsavel;
                
                $file = $this->handler->fileToArray($file);
                $file['tipo'] = $tipo;
                $file['responsavel'] = $responsavel;
                
            }

            return $this->handler->json(compact('files', count($anexos)));

        }else{

            return $this->handler->handle($request);

        }
    }
}