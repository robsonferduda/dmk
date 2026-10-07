<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Mail\Mailable;

class AtualizacaoCadastroMail extends Mailable
{
    /** @var string */
    public $nome;

    /** @var string */
    public $login;

    /** @var string */
    public $prazo;

    /** @var string */
    public $link;

    /** @var string|null */
    public $pixel;

    public function __construct(string $nome, string $login, Carbon $prazo, string $link, ?string $pixel = null)
    {
        $this->nome = $nome;
        $this->login = $login;
        $this->prazo = self::dataPorExtenso($prazo);
        $this->link = $link;
        $this->pixel = $pixel;
    }

    public function build()
    {
        return $this->from(config('mail.from.address'), config('cadastro.remetente_nome'))
            ->replyTo(config('cadastro.responder_para.email'), config('cadastro.responder_para.nome'))
            ->subject($this->subject ?: 'Atualização cadastral – DMK Advogados')
            ->view('emails.atualizacao-cadastro')
            ->text('emails.atualizacao-cadastro-texto');
    }

    public static function dataPorExtenso(Carbon $data): string
    {
        $meses = [1 => 'janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'];

        return $data->day . ' de ' . $meses[$data->month] . ' de ' . $data->year;
    }

    /**
     * "ADNEY MARTINS MODESTO" → "Adney Martins Modesto", mantendo preposições em minúsculas.
     */
    public static function nomeFormatado(?string $nome): string
    {
        $nome = trim(preg_replace('/\s+/', ' ', (string) $nome));
        if ($nome === '') {
            return 'correspondente';
        }

        $palavras = explode(' ', mb_convert_case(mb_strtolower($nome), MB_CASE_TITLE));
        foreach ($palavras as $i => $palavra) {
            if ($i > 0 && in_array(mb_strtolower($palavra), ['de', 'da', 'do', 'das', 'dos', 'e'], true)) {
                $palavras[$i] = mb_strtolower($palavra);
            }
        }

        return implode(' ', $palavras);
    }
}
