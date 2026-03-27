<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxaHonorarioAlteracao extends Model
{
    use SoftDeletes;

    protected $table      = 'taxa_honorario_alteracao_tha';
    protected $primaryKey = 'cd_taxa_honorario_alteracao_tha';
    protected $dates      = ['deleted_at'];
    protected $fillable   = [
        'cd_taxa_honorario_entidade_the',
        'cd_processo_pro',
        'nu_valor_antigo_tha',
        'nu_valor_novo_tha',
        'fl_aceito_tha',
    ];

    public $timestamps = true;

    public function taxaHonorario()
    {
        return $this->belongsTo('App\TaxaHonorario', 'cd_taxa_honorario_entidade_the', 'cd_taxa_honorario_entidade_the');
    }

    public function processo()
    {
        return $this->belongsTo('App\Processo', 'cd_processo_pro', 'cd_processo_pro');
    }

    public function isPendente()
    {
        return is_null($this->fl_aceito_tha);
    }

    public function isAprovado()
    {
        return $this->fl_aceito_tha === true;
    }

    public function isReprovado()
    {
        return $this->fl_aceito_tha === false;
    }
}
