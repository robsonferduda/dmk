<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class BaixaHonorario extends Model implements AuditableContract
{

	use SoftDeletes;
    use Auditable;

    protected $table = 'baixa_honorario_bho';
    protected $primaryKey = 'cd_baixa_honorario_bho';
    protected $fillable = [
    						'dt_baixa_honorario_bho',
                            'vl_baixa_honoraria_bho',
    					    'cd_tipo_financeiro_tfn',
                            'nu_nota_fiscal_bho',
                            'cd_processo_taxa_honorario_pth',
                            'cd_conta_con'
                                               
    					  ];

    public $timestamps = true;
    protected $dates = ['deleted_at'];

    public function anexoFinanceiro()
    {
        return $this->hasOne('App\AnexoFinanceiro','cd_baixa_honorario_bho', 'cd_baixa_honorario_bho');
    }

    public function tipoFinanceiro()
    {
        return $this->hasOne('App\TipoFinanceiro','cd_tipo_financeiro_tfn', 'cd_tipo_financeiro_tfn');
    }

    public function tipoBaixaHonorario()
    {
        return $this->hasOne('App\TipoBaixaHonorario','cd_tipo_baixa_honorario_bho', 'cd_tipo_baixa_honorario_bho');
    }

    public function getDtBaixaHonorarioBhoAttribute($value){

        if(!empty($value)){
            return date('d/m/Y',strtotime($value));
        }else{
             return '';
        }

        
    }

    public function getNuNotaFiscalBhoAttribute($value){

        if(!empty($value)){
            return $value;
        }else{
             return '';
        }

        
    }

}
