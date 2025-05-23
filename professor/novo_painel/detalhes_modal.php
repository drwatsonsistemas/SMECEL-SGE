<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
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
<?php 

$id = "-1";
if (isset($_GET['id'])) {
	$id = anti_injection($_GET['id']);
}

$etapa = "-1";
if (isset($_GET['etapa'])) {
	$etapa = anti_injection($_GET['etapa']);
}

$tipo_etapa = "-1";
if (isset($_GET['tipoetapa'])) {
	$tipo_etapa = anti_injection($_GET['tipoetapa']);
}
if($tipo_etapa == "EI"){
	
	
	mysql_select_db($database_SmecelNovo, $SmecelNovo);
	$query_matriz_disc_ac = 
	sprintf("
		SELECT * FROM smc_campos_exp_objetivos 
		WHERE campos_exp_obj_id_campos_exp = %s AND campos_exp_obj_faixa_et_cod = %s
		", 
		GetSQLValueString($id, 'int'),
		GetSQLValueString($etapa, 'int'));
	$matrizDisciplinasAC = mysql_query($query_matriz_disc_ac, $SmecelNovo) or die(mysql_error());
	$rowDisciplinasAC = mysql_fetch_assoc($matrizDisciplinasAC);
	$totalRowsDisciplinasAC = mysql_num_rows($matrizDisciplinasAC);
	$campos_exp_obj_faixa_et_nome = utf8_encode($rowDisciplinasAC['campos_exp_obj_faixa_et_nome']);
	$campos_exp_obj_campos_exp = utf8_encode($rowDisciplinasAC['campos_exp_obj_campos_exp']);
	

	echo "
	<p>
	<table class='ls-table ls-sm-space'>
	<thead>
	<tr>
	<th > OBJETIVOS DE APRENDIZAGEM E DESENVOLVIMENTO </th>
	</tr>
	<tr>
	<th> $campos_exp_obj_faixa_et_nome </th>
	</tr>";
	do {
		echo 
		"
		<tr><th>".
		utf8_encode($rowDisciplinasAC['campos_exp_obj_campos_exp']) 
		. "</th></tr>";
	} while ($rowDisciplinasAC = mysql_fetch_assoc($matrizDisciplinasAC));

	echo "</thead>
	</table>
	</p>
	";

}else{
	echo "
	<p>
	<table class='ls-table ls-sm-space'>
	<thead>
	<tr>
	<th > OBJETIVOS DE APRENDIZAGEM E DESENVOLVIMENTO </th>
	</tr>
	<tr>
	</tr>
		<tr><th>Não há descrição dos Objetivos de Aprendizagem e Desenvolvimento definidas pela BNCC na parte diversificada.</th></tr>
	</thead>
	</table>
	</p>
	";
}



?>