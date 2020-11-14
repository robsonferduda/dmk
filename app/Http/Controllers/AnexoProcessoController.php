<?php

namespace App\Http\Controllers;

use Auth;
use App\Processo;
use App\AnexoProcesso;
use App\Enums\TipoAnexoProcesso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Laracasts\Flash\Flash;

class AnexoProcessoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->conta = \Session::get('SESSION_CD_CONTA');
        $this->entidade = \Session::get('SESSION_CD_ENTIDADE');
    }

    public function show($id)
    {
        $id = \Crypt::decrypt($id);
        $anexo = AnexoProcesso::where('cd_anexo_processo_apr', $id)->first();
        return response()->download(storage_path($anexo->nm_local_anexo_despesa_des.$anexo->nm_anexo_despesa_des));
    }

    public function getSizeFolder()
    {
        $destino = "arquivos/{$this->conta}";

        $file_size = 0;

        foreach (File::allFiles(storage_path($destino)) as $file) {
            $file_size += $file->getSize();
        }
        
        $percentual = $this->getPercentualEspaco($file_size);
        $size = $this->getSymbolByQuantity($file_size);

        $dados = array('size' => $size, 'percentual' => $percentual);

        return Response::json($dados);
    }

    public function getSymbolByQuantity($bytes)
    {
        $symbols = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $exp = floor(log($bytes)/log(1024));

        return sprintf('%.2f '.$symbols[$exp], ($bytes/pow(1024, floor($exp))));
    }

    public function getPercentualEspaco($bytes)
    {

        //Espeço fixo de 30GB em bytes
        $percentual = ($bytes*100)/32212254720;
        return sprintf('%.1f ', $percentual)."%";
    }

    public function showPlugin($id, $file)
    {
        $anexo = AnexoProcesso::where('cd_processo_pro', $id)->where('nm_anexo_processo_apr', 'ilike', $file)->first();

        $nome_arquivo = $anexo['nm_local_anexo_processo_apr'].$anexo['nm_anexo_processo_apr'];

        if (file_exists(storage_path($nome_arquivo))) {
            return response()->download(storage_path($nome_arquivo));
        } else {
            $processo = Processo::where('cd_processo_pro', $anexo->cd_processo_pro)->first();
            return view('errors/processo-file-not-found', ['anexo' => $anexo, 'processo' => $processo]);
        }
    }

    public function create(Request $request)
    {

        $conta = Processo::where('cd_processo_pro', $request->id_processo)->select('cd_conta_con')->first()['cd_conta_con'];
        
        $local = "arquivos/{$conta}/processos/{$request->id_processo}/";
        $tipo = (Auth::user()->cd_nivel_niv == 3) ? TipoAnexoProcesso::CORRESPONDENTE : TipoAnexoProcesso::CONTA;

        AnexoProcesso::create([
            'cd_conta_con'                => $this->conta,
            'cd_entidade_ete'             => $this->entidade,
            'cd_tipo_anexo_processo_tap'  => $tipo,
            'cd_processo_pro'             => $request->id_processo,
            'nm_anexo_processo_apr'       => $request->nome_arquivo,
            'nm_local_anexo_processo_apr' => $local
        ]);
    }

    public function destroy(Request $request)
    {
        $anexo = AnexoProcesso::where('cd_processo_pro', $request->id)->where('nm_anexo_processo_apr', $request->nome_arquivo)->first();

        if ($anexo->delete()) {
            return Response::json(array('message' => 'Registro excluído com sucesso'), 200);
        } else {
            return Response::json(array('message' => 'Erro ao excluir o registro'), 500);
        }
    }

    public function destroyAndRemoveFile($id)
    {
    }

    public function downloadAll($id_processo)
    {
        $id_processo = \Crypt::decrypt($id_processo);

        $conta = Processo::where('cd_processo_pro', $id_processo)->select('cd_conta_con')->first()['cd_conta_con'];
        
        $id_file = date("YmdHis");
        $origem = "arquivos/$conta/processos/$id_processo/";
        $destino = "arquivos/$conta/processos/$id_processo/$id_file/";
        $destino_zip = "arquivos/$conta/processos/$id_processo/anexos/";

        if (!is_dir($destino)) {
            @mkdir(storage_path($destino), 0775);
        }

        foreach (File::allFiles(storage_path($origem)) as $file) {
            @copy(storage_path($origem.$file->getFileName()), storage_path($destino.$file->getFileName()));
        }

        //Gerar zip
        $zips = glob(storage_path($destino));
        \Zipper::make(storage_path($destino_zip.$id_file.'_anexos.zip'))->add($zips)->close();

        //Excluir diretorio temp
        foreach (File::allFiles(storage_path($destino)) as $file) {
            unlink($file->getRealPath());
        }

        rmdir(storage_path($destino));

        return response()->download(storage_path($destino_zip.$id_file.'_anexos.zip'));
    }
}
