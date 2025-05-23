<?php
function exibeVazio($dados){
	
	if ($dados=="") {
		$dados = "-";
		} else {
			$dados = $dados;
			}
			return $dados;
	}
?>