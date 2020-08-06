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

	static function gerar_senha($tamanho, $maiusculas, $minusculas, $numeros, $simbolos){
		
	  $senha = "";
	  $ma = "ABCDEFGHIJKLMNOPQRSTUVYXWZ"; // $ma contem as letras maiúsculas
	  $mi = "abcdefghijklmnopqrstuvyxwz"; // $mi contem as letras minusculas
	  $nu = "0123456789"; // $nu contem os números
	  $si = "!@#$%¨&*()_+="; // $si contem os símbolos
	 
	  if ($maiusculas){
	        // se $maiusculas for "true", a variável $ma é embaralhada e adicionada para a variável senha
	        $senha .= str_shuffle($ma);
	  }
	 
	    if ($minusculas){
	        // se $minusculas for "true", a variável $mi é embaralhada e adicionada para a variável senha
	        $senha .= str_shuffle($mi);
	    }
	 
	    if ($numeros){
	        // se $numeros for "true", a variável $nu é embaralhada e adicionada para a variável senha
	        $senha .= str_shuffle($nu);
	    }
	 
	    if ($simbolos){
	        // se $simbolos for "true", a variável $si é embaralhada e adicionada para a variável senha
	        $senha .= str_shuffle($si);
	    }
	 
	    // retorna a senha embaralhada com "str_shuffle" com o tamanho definido pela variável tamanho
	    return substr(str_shuffle($senha),0,$tamanho);
	}

	static function get_post_action($name)
	{
	    $params = func_get_args();

	    foreach ($params as $name) {
	        if (isset($_GET[$name])) {
	            return $name;
	        }
	    }
	}

}