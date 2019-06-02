<?php 

namespace App;


class Utils
{
	static function limpaCPF_CNPJ($valor){

		$valor = trim($valor);
	 	$valor = str_replace(".", "", $valor);
	 	$valor = str_replace(",", "", $valor);
	 	$valor = str_replace("-", "", $valor);
	 	$valor = str_replace("/", "", $valor);

	 	return $valor;
	}
}
