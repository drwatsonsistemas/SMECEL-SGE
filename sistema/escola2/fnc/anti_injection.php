<?php 
function anti_injection($sql)
{
	// remove palavras que contenham sintaxe sql
	$sql = trim($sql);//limpa espaços vazio
	$sql = strip_tags($sql);//tira tags html e php
	$sql = addslashes($sql);//Adiciona barras invertidas a uma string
	$sql = mysql_real_escape_string($sql);
	$sql = get_magic_quotes_gpc() == 0 ? addslashes($sql) : $sql;
	return preg_replace("@(--|\#|\*|;|=')@s", "", $sql);
	
//	return $sql;
}

?>