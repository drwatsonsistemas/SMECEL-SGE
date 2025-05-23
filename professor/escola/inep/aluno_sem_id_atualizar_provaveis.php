<?php require_once('../../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include('../fnc/inverteData.php'); ?>


<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "../../../index.php?saiu=true";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "1,2,99";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && false) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "../../../index.php?err=true";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
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

include "../usuLogado.php";
include "../fnc/anoLetivo.php";

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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_alunosemid = "SELECT aluno_id, aluno_cod_inep, aluno_nome, aluno_nascimento, aluno_filiacao1, aluno_filiacao2, aluno_municipio_nascimento_ibge FROM smc_aluno";
$alunosemid = mysql_query($query_alunosemid, $SmecelNovo) or die(mysql_error());
$row_alunosemid = mysql_fetch_assoc($alunosemid);
$totalRows_alunosemid = mysql_num_rows($alunosemid);
?>



<?php
// Abre o Arquvio no Modo r (para leitura)
//$arquivo = fopen ('RESULTADO_CERTO.txt', 'r');
	
// Lê o conteúdo do arquivo 
//while(!feof($arquivo))
//{
//Mostra uma linha do arquivo
//$linha = fgets($arquivo, 1024);
//echo $linha.'<br />';
//}

// Fecha arquivo aberto
//fclose($arquivo);
?>



<?php

$f = fopen("RESULTADO_PROVAVEIS.txt", "r");

while (!feof($f)) { 
      $arrM = explode("|",fgets($f));
	  
	  //echo $arrM[0]."|".$arrM[1]."|".$arrM[2]."|".$arrM[3]."|".$arrM[4]."|".$arrM[5]."|".$arrM[6]."<br>";

	  
	  $id = $arrM[0];
	  $cpf = $arrM[1];
	  $certidao = $arrM[2];
	  $nome = $arrM[3];
	  $nascimento = $arrM[4];
	  $filiacao1 = $arrM[5];
	  $filiacao2 = $arrM[6];
	  $ibge = $arrM[7];
	  $inep = $arrM[8];

	  //$up = "UPDATE smc_aluno SET aluno_cod_inep=''";
	  
	  //$up = "UPDATE smc_aluno SET aluno_cod_inep='$inep' WHERE aluno_id='$id'";
	  //mysql_select_db($database_SmecelNovo, $SmecelNovo);
	  //$Result1 = mysql_query($up, $SmecelNovo) or die(mysql_error());
	  
	  
		if(mysql_affected_rows() > 0){
			//echo "Aluno com INEP ".$inep." atualizado corretamente!<br>";
			echo $arrM[0]."|".$arrM[1]."|".$arrM[2]."|".$arrM[3]."|".$arrM[4]."|".$arrM[5]."|".$arrM[6]."|".$arrM[7]."|".$arrM[8]."<br>";
		}
	  

	  
}

		if(mysql_affected_rows() == 0){
			echo "Lista atualizada com sucesso!<br>";
			//echo $arrM[0]."|".$arrM[1]."|".$arrM[2]."|".$arrM[3]."|".$arrM[4]."|".$arrM[5]."|".$arrM[6]."<br>";
		}

fclose($f);

?>



<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($alunosemid);
?>
