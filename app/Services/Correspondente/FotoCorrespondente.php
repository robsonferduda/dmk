<?php

namespace App\Services\Correspondente;

use App\Enums\Nivel;
use App\User;

/**
 * Fotos de perfil enviadas pelo próprio usuário (ImageController): public/img/users/ent{cd_entidade_ete}.png.
 * O arquivo em disco é a fonte da verdade; users.img_user_usu só existe nos envios mais recentes.
 */
class FotoCorrespondente
{
    const PASTA = 'img/users';

    private $entidades;

    /**
     * Mapa cd_entidade_ete => timestamp do arquivo, lido uma única vez por requisição.
     */
    public function entidadesComFoto()
    {
        if ($this->entidades === null) {
            $this->entidades = [];

            foreach (glob(public_path(self::PASTA . '/ent*.png')) ?: [] as $arquivo) {
                if (preg_match('/^ent(\d+)\.png$/', basename($arquivo), $m) && filesize($arquivo) > 0) {
                    $this->entidades[(int) $m[1]] = filemtime($arquivo);
                }
            }
        }

        return $this->entidades;
    }

    public function url($cdEntidade)
    {
        $entidades = $this->entidadesComFoto();
        $cdEntidade = (int) $cdEntidade;

        if (!isset($entidades[$cdEntidade])) {
            return null;
        }

        return asset(self::PASTA . '/ent' . $cdEntidade . '.png') . '?v=' . $entidades[$cdEntidade];
    }

    /**
     * Primeira foto encontrada entre as entidades informadas (lista ou string separada por vírgula).
     */
    public function urlPrimeira($entidades)
    {
        if (is_string($entidades)) {
            $entidades = array_filter(explode(',', $entidades));
        }

        foreach ((array) $entidades as $cdEntidade) {
            if ($url = $this->url($cdEntidade)) {
                return $url;
            }
        }

        return null;
    }

    /**
     * Foto do correspondente a partir da conta dele (usuários de nível correspondente).
     */
    public function urlPorConta($cdContaCorrespondente)
    {
        $entidades = User::withTrashed()
            ->where('cd_conta_con', $cdContaCorrespondente)
            ->where('cd_nivel_niv', Nivel::CORRESPONDENTE)
            ->orderBy('id')
            ->pluck('cd_entidade_ete')
            ->all();

        return $this->urlPrimeira($entidades);
    }
}
