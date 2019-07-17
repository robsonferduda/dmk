<?php

namespace App; 

use Auth;
use Illuminate\Support\Facades\Session;
use PHPJasper\PHPJasper;

class RelatorioJasper
{
 
    public function __construct()
    {
        $this->host       = env('DB_HOST', null);
        $this->dbName     = env('DB_DATABASE', null);
        $this->user       = env('DB_USERNAME', null);
        $this->password   = env('DB_PASSWORD', null);
        $this->parametros = array();
        $this->porta      = env('DB_PORT',null);
        $this->conta      = \Session::get('SESSION_CD_CONTA');
        
    }

    public function getDatabaseConfig()
    {
        return [
            'driver' => 'postgres',
            'database' => "{$this->dbName}",
            'username' => "{$this->user}",
            'password' => "{$this->password}",
            'jdbc_driver' => 'org.postgresql.Driver',
            'port'        => "{$this->porta}",
            'host'        => "{$this->host}"
           // 'jdbc_url' => "jdbc:postgresql://{$this->host}/{$this->dbName}",
           // 'jdbc_dir' => base_path() . env('JDBC_DIR', '/vendor/lavela/phpjasper/bin/jasperstarter/jdbc')
        ];
    }

    public function getJSONConfig($json)
    {

        $fp = fopen(public_path().'/reports/relatorio.json', "w");
        fwrite($fp, $json);
        fclose($fp);

        return [
            'driver' => 'json',
            'data_file' => public_path().'/reports/relatorio.json',  
            'json_query' => ''    
        ];
    }

    public function processar($parametros = array(),$sourceName,$fileName,$download=true)
    {
    
        \File::makeDirectory(storage_path().'/reports/'.$this->conta, $mode = 0744, true, true);

        $output = storage_path().'/reports/'.$this->conta.'/'. time() . "_$fileName";

        $report = new PHPJasper;

        $this->parametros = array_merge($parametros,$this->parametros);

        $input = resource_path()."/reports/$sourceName";   
        $output = $output;
        $options = [
            'format' => ['xls'],
            'locale' => 'pt',
            'params' => $this->parametros,
            'db_connection' => $this->getDatabaseConfig()
        ];

        $report->process(
            $input,
            $output,
            $options
        )->execute();

        $file = $output . '.xls';
        $path = $file;

        // caso o arquivo não tenha sido gerado retorno um erro 404
        if (!file_exists($file)) {
            abort(404);
        }
        
        if($download){
            //caso tenha sido gerado pego o conteudo
            $file = file_get_contents($file);
            //deleto o arquivo gerado, pois iremos mandar o conteudo para o navegador
            unlink($path);
            // retornamos o conteudo para o navegador que íra abrir o PDF

            return response($file, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="'.$fileName.'.pdf"');
        }
    }

    public function processarWithJSON($json,$fileName){


        $output = public_path().'/reports/' . time() . '_Vestibular';
    
        $report = new PHPJasper;

        $report->process(
            resource_path()."/reports/$fileName",
            $output,
            ['pdf'],
            $this->parametros,
            $this->getJSONConfig($json)
        )->execute();
        $file = $output . '.pdf';
        $path = $file;

        // caso o arquivo não tenha sido gerado retorno um erro 404
        if (!file_exists($file)) {
            abort(404);
        }
        //caso tenha sido gerado pego o conteudo
        $file = file_get_contents($file);
        //deleto o arquivo gerado, pois iremos mandar o conteudo para o navegador
        unlink($path);
        unlink(public_path().'/reports/'.'relatorio.json');
        // retornamos o conteudo para o navegador que íra abrir o PDF
        return response($file, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="vestibular.pdf"');
       
    }

}
