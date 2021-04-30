<?php

namespace App\Helpers;

class Helper
{
    public static function formatarCnpjCpf($value)
    {
        $cnpj_cpf = preg_replace("/\D/", '', $value);
      
        if (strlen($cnpj_cpf) === 11) {
            return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cnpj_cpf);
        }
      
        return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj_cpf);
    }

    public static function limparCnpjCpf($value)
    {
        return str_replace(array('/','.','-'), '', $value);
    }

    public static function validaData($dat)
    {
        $data = explode("/", "$dat"); // fatia a string $dat em pedados, usando / como referência
        $d = $data[0];
        $m = $data[1];
        $y = $data[2];

        // verifica se a data é válida!
        // 1 = true (válida)
        // 0 = false (inválida)
        return checkdate($m, $d, $y);
    }

    public static function validaHoras($campo)
    {
        if (preg_match('/^[0-9]{2}:[0-9]{2}$/', $campo)) {
            $horas = substr($campo, 0, 2);
            $minutos = substr($campo, 3, 2);
            if (($horas > "23") or ($minutos > "59")) {
                return false;
            }

            return true;
        }

        return false;
    }

    public static function customRequestCaptcha()
    {
        return new \ReCaptcha\RequestMethod\Post();
    }
}
