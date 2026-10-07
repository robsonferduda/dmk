<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CampanhaCadastroEnvio extends Model
{
    const PENDENTE = 'pendente';
    const ENVIADO = 'enviado';
    const FALHA = 'falha';

    protected $table = 'campanha_cadastro_envio_cme';
    protected $primaryKey = 'cd_campanha_cadastro_envio_cme';
    protected $fillable = [
        'cd_campanha_cadastro_cmc',
        'cd_conta_correspondente_ccr',
        'nm_destinatario_cme',
        'dc_email_cme',
        'dt_ultimo_processo_cme',
        'nu_ordem_cme',
        'dc_token_cme',
        'dc_status_cme',
        'nu_tentativas_cme',
        'nu_envios_cme',
        'dc_erro_cme',
        'dt_envio_cme',
        'dt_prazo_cme',
        'dt_abertura_cme',
        'dt_clique_cme',
        'dt_confirmacao_cme',
        'dc_pendencias_cme',
        'dt_pendencias_cme',
    ];
    protected $casts = [
        'nu_ordem_cme'      => 'integer',
        'nu_tentativas_cme' => 'integer',
        'nu_envios_cme'     => 'integer',
    ];
    protected $dates = [
        'dt_ultimo_processo_cme', 'dt_envio_cme', 'dt_prazo_cme', 'dt_abertura_cme',
        'dt_clique_cme', 'dt_confirmacao_cme', 'dt_pendencias_cme',
    ];

    public function campanha()
    {
        return $this->belongsTo('App\CampanhaCadastro', 'cd_campanha_cadastro_cmc', 'cd_campanha_cadastro_cmc');
    }

    public function vinculo()
    {
        return $this->belongsTo('App\ContaCorrespondente', 'cd_conta_correspondente_ccr', 'cd_conta_correspondente_ccr');
    }

    /**
     * @return string[]|null null enquanto não houver avaliação
     */
    public function pendencias(): ?array
    {
        return $this->dt_pendencias_cme ? (json_decode((string) $this->dc_pendencias_cme, true) ?: []) : null;
    }
}
