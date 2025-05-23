<?php
function url_limpa($texto_url) {

	$texto_url = str_replace("–", "-", $texto_url);
	$texto_url = str_replace("°", "", $texto_url);
	$texto_url = str_replace("´", "", $texto_url);
	$texto_url = str_replace("`", "", $texto_url);
	$texto_url = str_replace(" ", "-", $texto_url);
	$texto_url = str_replace("  ", "-", $texto_url);
	$texto_url = str_replace("\"", "", $texto_url);
	$texto_url = str_replace("º", "", $texto_url);
	$texto_url = str_replace("ª", "", $texto_url);	
	
	$texto_url = str_replace("Á", "A", $texto_url);
	$texto_url = str_replace("À", "A", $texto_url);
	$texto_url = str_replace("Ä", "A", $texto_url);
	$texto_url = str_replace("Ã", "A", $texto_url);
	$texto_url = str_replace("Â", "A", $texto_url);
	$texto_url = str_replace("á", "a", $texto_url);
	$texto_url = str_replace("à", "a", $texto_url);
	$texto_url = str_replace("ä", "a", $texto_url);
	$texto_url = str_replace("ã", "a", $texto_url);
	$texto_url = str_replace("â", "a", $texto_url);

	$texto_url = str_replace("É", "E", $texto_url);
	$texto_url = str_replace("È", "E", $texto_url);
	$texto_url = str_replace("Ë", "E", $texto_url);
	$texto_url = str_replace("Ê", "E", $texto_url);	
	$texto_url = str_replace("é", "e", $texto_url);
	$texto_url = str_replace("è", "e", $texto_url);
	$texto_url = str_replace("ë", "e", $texto_url);
	$texto_url = str_replace("ê", "e", $texto_url);
	
	$texto_url = str_replace("Í", "I", $texto_url);
	$texto_url = str_replace("Ì", "I", $texto_url);
	$texto_url = str_replace("Ï", "I", $texto_url);
	$texto_url = str_replace("Î", "I", $texto_url);
	
	$texto_url = str_replace("í", "i", $texto_url);
	$texto_url = str_replace("ì", "i", $texto_url);
	$texto_url = str_replace("ï", "i", $texto_url);
	$texto_url = str_replace("î", "i", $texto_url);
	
	$texto_url = str_replace("Ó", "O", $texto_url);
	$texto_url = str_replace("Ò", "O", $texto_url);
	$texto_url = str_replace("Ö", "O", $texto_url);
	$texto_url = str_replace("Õ", "O", $texto_url);
	$texto_url = str_replace("Ô", "O", $texto_url);

	$texto_url = str_replace("ó", "o", $texto_url);
	$texto_url = str_replace("ò", "o", $texto_url);
	$texto_url = str_replace("ö", "o", $texto_url);
	$texto_url = str_replace("õ", "o", $texto_url);
	$texto_url = str_replace("ô", "o", $texto_url);

	$texto_url = str_replace("Ú", "U", $texto_url);
	$texto_url = str_replace("Ù", "U", $texto_url);
	$texto_url = str_replace("Ü", "U", $texto_url);
	$texto_url = str_replace("Û", "U", $texto_url);
	
	$texto_url = str_replace("ú", "u", $texto_url);
	$texto_url = str_replace("ù", "u", $texto_url);
	$texto_url = str_replace("ü", "u", $texto_url);
	$texto_url = str_replace("û", "u", $texto_url);
	
	$texto_url = str_replace("Ç", "c", $texto_url);
	$texto_url = str_replace("ç", "c", $texto_url);
	$texto_url = str_replace("\'", "-", $texto_url);
	$texto_url = str_replace(",", "-", $texto_url);
	$texto_url = str_replace(".", "-", $texto_url);
	$texto_url = str_replace(";", "-", $texto_url);
	$texto_url = str_replace("(", "-", $texto_url);
	$texto_url = str_replace(")", "-", $texto_url);
	$texto_url = str_replace(":", "-", $texto_url);
	$texto_url = str_replace("?", "-", $texto_url);
	$texto_url = str_replace("!", "-", $texto_url);
	$texto_url = str_replace("'", "-", $texto_url);
	$texto_url = str_replace("\"", "-", $texto_url);
	
	$texto_url = str_replace("%", "-", $texto_url);
	$texto_url = str_replace("$", "-", $texto_url);
	$texto_url = str_replace("#", "-", $texto_url);
	$texto_url = str_replace("@", "-", $texto_url);
	$texto_url = str_replace("&", "-", $texto_url);
	$texto_url = str_replace("*", "-", $texto_url);
	$texto_url = str_replace("+", "-", $texto_url);
	$texto_url = str_replace("=", "-", $texto_url);

	$texto_url = strtolower($texto_url);
		
	return $texto_url;
	}
?>