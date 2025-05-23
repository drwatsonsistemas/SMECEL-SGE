<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
	function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
	{
		if (PHP_VERSION < 6) {
			$theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
		}

		$theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

		switch ($theType) {
			case "text":
			$theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
			break;    
			case "long":
			case "int":
			$theValue = ($theValue != "") ? intval($theValue) : "NULL";
			break;
			case "double":
			$theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
			break;
			case "date":
			$theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
			break;
			case "defined":
			$theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
			break;
		}
		return $theValue;
	}
}
?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
<?php include "../../sistema/escola/fnc/calculos.php"; ?>
<?php 


$colname_VerificaVinculo = "-1";
if (isset($_POST['id'])) {
	$colname_VerificaVinculo = anti_injection($_POST['id']);
}


$colname_VerificaMatricula = "-1";
if (isset($_POST['matricula'])) {
	$colname_VerificaMatricula = anti_injection($_POST['matricula']);
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matricula = sprintf("
	SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, 
	vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, 
	vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, 
	vinculo_aluno_vacina_atualizada, aluno_id, aluno_nome, aluno_foto,
	CASE vinculo_aluno_situacao
	WHEN 1 THEN 'MATRICULADO'
	WHEN 2 THEN 'TRANSFERIDO(A)'
	WHEN 3 THEN 'DESISTENTE'
	WHEN 4 THEN 'FALECIDO(A)'
	WHEN 5 THEN 'OUTROS'
	END AS vinculo_aluno_situacao_nome 
	FROM smc_vinculo_aluno 
	INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
	WHERE vinculo_aluno_id = %s", GetSQLValueString($colname_VerificaMatricula, "int"));
$Matricula = mysql_query($query_Matricula, $SmecelNovo) or die(mysql_error());
$row_Matricula = mysql_fetch_assoc($Matricula);
$totalRows_Matricula = mysql_num_rows($Matricula);


if ((isset($_POST['id'])) && ($_POST['id'] != "")) {
	

	$deleteSQL = sprintf("DELETE FROM smc_notas_qq WHERE qq_id_matricula = '$row_Matricula[vinculo_aluno_id]' AND qq_id=%s",
		GetSQLValueString($colname_VerificaVinculo, "int"));

	mysql_select_db($database_SmecelNovo, $SmecelNovo);
	$Result1 = mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());

}

?>

