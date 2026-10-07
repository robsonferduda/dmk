<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CampanhaCadastro extends Model
{
    const ATIVA = 'ativa';
    const PAUSADA = 'pausada';
    const CONCLUIDA = 'concluida';

    protected $table = 'campanha_cadastro_cmc';
    protected $primaryKey = 'cd_campanha_cadastro_cmc';
    protected $fillable = [
        'cd_conta_con',
        'nm_campanha_cmc',
        'dc_criterio_cmc',
        'dc_status_cmc',
        'nu_prazo_dias_cmc',
        'cd_usuario_cmc',
        'dt_inicio_cmc',
        'dt_conclusao_cmc',
    ];
    protected $dates = ['dt_inicio_cmc', 'dt_conclusao_cmc'];

    public function envios()
    {
        return $this->hasMany('App\CampanhaCadastroEnvio', 'cd_campanha_cadastro_cmc', 'cd_campanha_cadastro_cmc');
    }
}
