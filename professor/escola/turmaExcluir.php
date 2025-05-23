<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "fnc/session.php"; ?>
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

include "usuLogado.php";
include "fnc/anoLetivo.php";


	if ($row_UsuLogado['usu_delete']=="N") {
		header(sprintf("Location: turmaListar.php?permissao"));
		die;
	}


$codTurma = "-1";
if (isset($_GET['c'])) {
  $codTurma = $_GET['c'];
}

$turma = "-1";
if (isset($_GET['turma'])) {
  $turma = $_GET['turma'];
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_verificaTurma = "SELECT turma_id, turma_id_escola FROM smc_turma WHERE (turma_id = '$codTurma' AND turma_id_escola = '$row_UsuLogado[usu_escola]')";
$verificaTurma = mysql_query($query_verificaTurma, $SmecelNovo) or die(mysql_error());
$row_verificaTurma = mysql_fetch_assoc($verificaTurma);
$totalRows_verificaTurma = mysql_num_rows($verificaTurma);

//Se a turma for de outra escola
if ($totalRows_verificaTurma == "") {
	header("Location:turmaListar.php?erro");
	exit;
}

if ((isset($_GET['c'])) && ($_GET['c'] != "")) {



//Excluir os vinculos
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_listaVinculo = "SELECT vinculo_aluno_id, vinculo_aluno_id_turma, vinculo_aluno_id_escola FROM smc_vinculo_aluno WHERE (vinculo_aluno_id_turma = '$codTurma' AND vinculo_aluno_id_escola = '$row_UsuLogado[usu_escola]')";
$listaVinculo = mysql_query($query_listaVinculo, $SmecelNovo) or die(mysql_error());
$row_listaVinculo = mysql_fetch_assoc($listaVinculo);
$totalRows_listaVinculo = mysql_num_rows($listaVinculo);

if ($totalRows_listaVinculo > 0) {
  
  $deleteVinculo = "DELETE FROM smc_vinculo_aluno WHERE (vinculo_aluno_id_turma = '$codTurma' AND vinculo_aluno_id_escola = '$row_UsuLogado[usu_escola]')";
  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result2 = mysql_query($deleteVinculo, $SmecelNovo) or die(mysql_error());
  
}

	
  $deleteSQL = sprintf("DELETE FROM smc_turma WHERE (turma_id_escola = $row_UsuLogado[usu_escola] AND turma_id=%s)",
                       GetSQLValueString($_GET['c'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());
  
  
    // ** REGISTRO DE LOG DE USUÁRIO **
	$usu = $row_UsuLogado['usu_id'];
	$esc = $row_UsuLogado['usu_escola'];
	
	date_default_timezone_set('America/Bahia');
	$dat = date('Y-m-d H:i:s');

	$sql = "
	INSERT INTO smc_registros (
	registros_id_escola, 
	registros_id_usuario, 
	registros_tipo, 
	registros_complemento, 
	registros_data_hora
	) VALUES (
	'$esc', 
	'$usu', 
	'22', 
	'($turma)', 
	'$dat')
	";
	mysql_select_db($database_SmecelNovo, $SmecelNovo);
	$Result1 = mysql_query($sql, $SmecelNovo) or die(mysql_error());
// ** REGISTRO DE LOG DE USUÁRIO **

  

  $deleteGoTo = "turmaListar.php?turmaexcluida";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EscolaLogada = "
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, 
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, sec_id, sec_cidade, sec_uf 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

?>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($verificaTurma);
?>
