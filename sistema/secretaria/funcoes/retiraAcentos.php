<?php

function retiraAcentos ($string) {
		$string = str_replace('Á', 'A', $string);
		$string = str_replace('À', 'A', $string);
		$string = str_replace('Â', 'A', $string);
		$string = str_replace('Ã', 'A', $string);
		$string = str_replace('É', 'E', $string);
		$string = str_replace('È', 'E', $string);
		$string = str_replace('Ê', 'E', $string);
		$string = str_replace('Í', 'I', $string);
		$string = str_replace('Ì', 'I', $string);
		$string = str_replace('Î', 'I', $string);
		$string = str_replace('Ó', 'O', $string);
		$string = str_replace('Ò', 'O', $string);
		$string = str_replace('Õ', 'O', $string);
		$string = str_replace('Ô', 'O', $string);
		$string = str_replace('Ú', 'U', $string);
		$string = str_replace('Ù', 'U', $string);
		$string = str_replace('Û', 'U', $string);
		$string = str_replace('Ü', 'U', $string);
		$string = str_replace('Ç', 'C', $string);
		$string = str_replace('\'', '', $string);
		$string = str_replace('  ', ' ', $string);
	return $string;
	}

?>