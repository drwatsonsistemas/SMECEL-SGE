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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EscolaLogada = "SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, escola_telefone2, escola_email, escola_inep, escola_cnpj FROM smc_escola WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

$colname_ExibeVinculo = "-1";
if (isset($_GET['hash'])) {
  $colname_ExibeVinculo = $_GET['hash'];
}

$colname_nome = "-1";
if (isset($_GET['nome'])) {
  $colname_nome = $_GET['nome'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ExibeVinculo = sprintf("SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao FROM smc_vinculo_aluno WHERE vinculo_aluno_hash = %s", GetSQLValueString($colname_ExibeVinculo, "text"));
$ExibeVinculo = mysql_query($query_ExibeVinculo, $SmecelNovo) or die(mysql_error());
$row_ExibeVinculo = mysql_fetch_assoc($ExibeVinculo);
$totalRows_ExibeVinculo = mysql_num_rows($ExibeVinculo);

$turma = $row_ExibeVinculo['vinculo_aluno_id_turma'];
$aluno = $row_ExibeVinculo['vinculo_aluno_id_aluno'];
$colname_nome;

if ($totalRows_ExibeVinculo == 0) {
	
	header("Location: vinculoAlunoExibir.php?erro");
	
	}

	if ($row_UsuLogado['usu_delete']=="N") {
		header(sprintf("Location: vinculoAlunoExibirTurma.php?permissao"));
		break;
	}

if ((isset($_GET['hash'])) && ($_GET['hash'] != "")) {
  $deleteSQL = sprintf("DELETE FROM smc_vinculo_aluno WHERE vinculo_aluno_hash=%s",
                       GetSQLValueString($_GET['hash'], "text"));

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
	'17', 
	'(ID: $aluno - $colname_nome, TURMA: $turma)', 
	'$dat')
	";
	mysql_select_db($database_SmecelNovo, $SmecelNovo);
	$Result1 = mysql_query($sql, $SmecelNovo) or die(mysql_error());
// ** REGISTRO DE LOG DE USUÁRIO **

  
  
  
  $deleteGoTo = "vinculoAlunoExibirTurma.php?ct=$turma&excluido";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">
<head>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-117872281-1');
</script>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sem título</title>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>

<body>
</body>
</html>
<?php
mysql_free_result($ExibeVinculo);
?>
