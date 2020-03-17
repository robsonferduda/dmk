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
        
        $destino = "despesas/$this->conta/$id_despesa";        

        //Verificar se existe a pasta da conta, se não existir, criar a pasta com permissões de escrita
        if(!is_dir($destino)){
            @mkdir(storage_path($destino), 0775);
        }

        $config['debug'] = true;
        $config['upload_dir'] = storage_path($destino);
        $config['upload_url'] = storage_path($destino);

    }

    /**
     * Handle an incoming HTTP request.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request)
    {

        $this->inicializaPastaDestino($request->id_despesa);

        //Ação de enviar arquivo
        return $this->handler->handle($request);
    }
}