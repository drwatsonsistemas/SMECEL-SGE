<?php

function idade ($idade) {
	
	if ($idade <> "") {

    // Declara a data! :P
    $data = $idade;
   
    // Separa em dia, mês e ano
    list($ano, $mes, $dia) = explode('-', $data);
   
    // Descobre que dia é hoje e retorna a unix timestamp
    $hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
    // Descobre a unix timestamp da data de nascimento do fulano
    $nascimento = mktime( 0, 0, 0, $mes, $dia, $ano);
   
    // Depois apenas fazemos o cálculo já citado :)
    $idade = floor((((($hoje - $nascimento) / 60) / 60) / 24) / 365.25);
	
	} else {
		$idade = "-";
	}
	
    return $idade;
	
}
?>