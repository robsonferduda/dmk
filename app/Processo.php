<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Processo extends Model
{

	use SoftDeletes;

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
}
