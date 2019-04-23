<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Processo extends Model implements AuditableContract
{

	use SoftDeletes;
    use Auditable;

    protected $table = 'processo_pro';
    protected $primaryKey = 'cd_processo_pro';
    protected $dates = ['deleted_at'];
    protected $fillable = [
    						'cd_cidade_cde',
    						'cd_cliente_cli',
    						'cd_conta_con',
    						'cd_contato_cot',
    						'cd_correspondente_cor',
    						'cd_entidade_ete',
    						'cd_tipo_processo_tpo',
    						'cd_vara_var',
    						'dc_observacao_pro',
    						'dt_audiencia_pro',
    						'dt_prazo_fatal_pro',
    						'hr_audiencia_pro',
    						'nm_advogado_pro',
    						'nm_autor_pro',
    						'nm_preposto_pro',
    						'nm_reu_pro',
    						'nu_processo_pro'
    					  ];

    public $timestamps = true;

    public function cliente()
    {
        return $this->hasOne('App\Cliente','cd_cliente_cli', 'cd_cliente_cli');
    }

    public function cidade()
    {
        return $this->hasOne('App\Cidade','cd_cidade_cde', 'cd_cidade_cde');
    }

    public function advogadoSolicitante()
    {
        return $this->hasOne('App\Contato','cd_contato_cot', 'cd_contato_cot')->where('cd_tipo_contato_tct', \TipoContato::ADVOGADO);
    }

    public function tipoProcesso()
    {
        return $this->hasOne('App\TipoProcesso','cd_tipo_processo_tpo', 'cd_tipo_processo_tpo');
    }

    public function vara()
    {
        return $this->hasOne('App\Vara','cd_vara_var', 'cd_vara_var');
    }
}
