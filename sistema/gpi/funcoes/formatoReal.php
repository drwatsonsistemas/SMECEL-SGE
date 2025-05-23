<?php 
function formatoReal($valor) {
	
	$real = 'R$ ' . number_format($valor, 2, ',', '.');
	
	return $real;
	}
?>