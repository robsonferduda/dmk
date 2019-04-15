<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxaHonorario extends Model
{
    use SoftDeletes;
    
    protected $table = 'taxa_honorario_entidade_the';
    protected $primaryKey = 'cd_taxa_honorario_entidade_the';
    protected $dates = ['deleted_at'];
    protected $fillable = [
    						'cd_conta_con',
                            'cd_entidade_ete',
                            'cd_tipo_servico_tse',
                            'cd_cidade_cde',
                            'nu_taxa_the',
                            'dc_observacao_the'
    					  ];

    public $timestamps = true;

    public function entidade()
    {
        return $this->hasOne('App\Estado','cd_entidade_ete', 'cd_entidade_ete');
    }

    public function tipoServico()
    {
        return $this->hasOne('App\Estado','cd_tipo_servico_tse', 'cd_tipo_servico_tse');
    }

    public function cidade()
    {
        return $this->hasOne('App\Cidade','cd_cidade_cde', 'cd_cidade_cde');
    }

}