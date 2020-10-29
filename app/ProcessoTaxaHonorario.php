<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class ProcessoTaxaHonorario extends Model implements AuditableContract
{

	use SoftDeletes;
    use Auditable;

    protected $table = 'processo_taxa_honorario_pth';
    protected $primaryKey = 'cd_processo_taxa_honorario_pth';
    protected $dates = ['deleted_at'];
    protected $fillable = [
    						'vl_taxa_honorario_cliente_pth',
                            'vl_taxa_honorario_correspondente_pth',
    					    'cd_processo_pro',
                            'cd_conta_con',
                            'cd_tipo_servico_tse',
                            'cd_tipo_servico_correspondente_tse',
                            'vl_taxa_cliente_pth',                            
                            'nu_cliente_nota_fiscal_pth',
                            'fl_pago_cliente_pth',
                            'fl_pago_correspondente_pth'
                                               
    					  ];

    public $timestamps = true;

    public function tipoServico()
    {
        return $this->hasOne('App\TipoServico','cd_tipo_servico_tse', 'cd_tipo_servico_tse');
    }
     public function tipoServicoCorrespondente()
    {
        return $this->hasOne('App\TipoServico','cd_tipo_servico_tse', 'cd_tipo_servico_correspondente_tse');
    }

    public function processo()
    {
        return $this->hasOne('App\Processo','cd_processo_pro', 'cd_processo_pro');
    }

    public function baixaHonorario()
    {
        return $this->hasMany('App\BaixaHonorario','cd_processo_taxa_honorario_pth', 'cd_processo_taxa_honorario_pth');
    }

}
