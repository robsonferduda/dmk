<?php

namespace App\Http\Controllers;

use App\User;
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

    public function inicializaPastaDestino($id_despesa)
    {
        $this->handler = new Handler(
            new Uploader($config = new Config, new ImageManager)
        );
        
        $destino = "arquivos/$this->conta/despesas/$id_despesa";

        //Verificar se existe a pasta da conta, se não existir, criar a pasta com permissões de escrita
        if (!is_dir($destino)) {
            @mkdir(storage_path($destino), 0775);
        }

        $config['debug'] = true;
        $config['upload_dir'] = storage_path($destino);
        $config['upload_url'] = storage_path($destino);
    }

    public function inicializaPastaProcesso($id_processo)
    {
        $this->handler = new Handler(
            new Uploader($config = new Config, new ImageManager)
        );
        
        $conta = Processo::where('cd_processo_pro', $id_processo)->select('cd_conta_con')->first()['cd_conta_con'];
        
        $destino = "arquivos/$conta/processos/$id_processo";

        //Verificar se existe a pasta da conta, se não existir, criar a pasta com permissões de escrita
        if (!is_dir($destino)) {
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

        $anexos = array();
        $files = array();

        if ($method == 'GET') {
            $anexos = AnexoProcesso::where('cd_processo_pro', $request->id_processo)->orderBy('created_at', 'DESC')->get();

            $files = array();

            foreach ($anexos as $key => $anexo) {
                $nome_arquivo = $anexo['nm_local_anexo_processo_apr'].$anexo['nm_anexo_processo_apr'];
                
                //Se o registro do arquivo existe na pasta, adicona ele na liatagem
                if (file_exists(storage_path($nome_arquivo))) {
                    $files[$key] = new File(storage_path($nome_arquivo));
                    $files[$key]->tipo = ($anexo->cd_tipo_anexo_processo_tap) ? $anexo->cd_tipo_anexo_processo_tap : null;
                    $files[$key]->responsavel = User::where('cd_entidade_ete', $anexo->cd_entidade_ete)->withTrashed()->first()->name;
                }
            }

            foreach ($files as &$file) {
                $tipo = $file->tipo;
                $responsavel = $file->responsavel;
                
                $file = $this->handler->fileToArray($file);
                $file['tipo'] = $tipo;
                $file['responsavel'] = $responsavel;
            }

            return $this->handler->json(compact('files', count($anexos)));
        } else {
            return $this->handler->handle($request);
        }
    }
}
