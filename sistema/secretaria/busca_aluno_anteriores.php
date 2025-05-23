<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include "../escola/fnc/inverteData.php"; ?>
<?php include "../escola/fnc/anti_injection.php"; ?>
<?php include('../funcoes/url_base.php'); ?>


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
	
  $logoutGoTo = "../../index.php?saiu=true";
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
$MM_authorizedUsers = "1,99";
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

$MM_restrictGoTo = "../../index.php?err=true";
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


require_once('funcoes/usuLogado.php');
require_once('funcoes/anoLetivo.php');


if(isset($_POST['queryString'])) {
				
		//$queryString = $_POST['queryString'];
		$queryString = anti_injection($_POST['queryString']);
		
		if(strlen($queryString) > 3) {
			$query = mysql_query("
			SELECT 
			aluno_id, aluno_nome, DATE_FORMAT(aluno_nascimento, '%d/%m/%Y') AS aluno_nascimento, aluno_filiacao1, aluno_foto,
			vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, vinculo_aluno_hash, vinculo_aluno_id_turma,
			escola_id, escola_nome, 
			CASE vinculo_aluno_situacao
			WHEN 1 THEN 'MATRICULADO'
			WHEN 2 THEN 'TRANSFERIDO'
			WHEN 3 THEN 'DEIXOU DE FREQUENTAR'
			WHEN 4 THEN 'FALECIDO'
			WHEN 5 THEN 'OUTROS'
			END AS vinculo_aluno_situacao,
			turma_id, turma_nome, 
			CASE turma_turno
			WHEN 1 THEN 'MATUTINO'
			WHEN 2 THEN 'VESPERTINO'
			WHEN 3 THEN 'NOTURNO'
			END AS turma_turno,
			vinculo_aluno_id_sec
			FROM smc_aluno
			INNER JOIN smc_vinculo_aluno ON vinculo_aluno_id_aluno = aluno_id
			INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
			INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola
			WHERE (aluno_nome LIKE '$queryString%' OR aluno_filiacao1 LIKE '$queryString%')
			AND vinculo_aluno_id_sec = '$row_UsuarioLogado[usu_sec]'
			ORDER BY aluno_nome ASC, vinculo_aluno_ano_letivo DESC
			LIMIT 50"
			) or die("Erro na consulta");
			
			//$row_Noticia = mysql_fetch_assoc($query);
			$totalRows_Noticia = mysql_num_rows($query);
			
			
			
			echo '
			<p><hr>
			'.$totalRows_Noticia.' aluno(s) encontrado(s).
			</p>';
			echo '
			<table class="ls-table ls-table-bordered ls-bg-header ls-table-striped ls-sm-space">
			<thead>
			<tr>
				<th width="75">FOTO</th>
				<th>DADOS DO(A) ALUNO(A)</th>
				<th>DADOS DA TURMA</th>
			</tr>
			</thead>
			<tbody>
			';
						
			while ($result = mysql_fetch_array($query)) {
			
					
			
			if ($result[4]=="") {
				$foto = URL_BASE.'aluno/fotos/semfoto.jpg';
			} else {
				$foto = URL_BASE.'aluno/fotos/'.$result[4];
			}

			
			echo '
			
			<tr>
				<td height="100" width="100">
					<img src="'.$foto.'" width="75px">
				</td>
				<td>
					
					<p>NOME: <strong>'.$result[1].'</strong></p> 
					<p>DATA DE NASCIMENTO: <strong>'.$result[2].'</strong></p>
					<p>FILIAÇÃO: <strong>'.$result[3].'</strong></p>
					<p>ANO LETIVO: <strong>'.$result[8].'</strong></p>
					
				</td>
				<td>
					
					<p>ESCOLA: <strong>'.$result[12].'</strong></p>
					<p>TURMA: <strong>'.$result[15].'</strong></p>
					<p>TURNO: <strong>'.$result[16].'</strong></p>
					<p>SITUAÇÃO: <strong>'.$result[13].'</strong></p>
					
				</td>
			</tr>
			
			
			
			<!--
			<tr>
				<td height="100" width="100">
					<a style="cursor:pointer;" onClick="exibe(\''.$result[9].'\');" onClick="fill(\''.$result[9].'\');"><img src="'.$foto.'" width="75px"></a>
				</td>
				<td>
					<a style="cursor:pointer;" onClick="exibe(\''.$result[9].'\');" onClick="fill(\''.$result[9].'\');">
					<p>NOME: <strong>'.$result[1].'</strong></p> <p>DATA DE NASCIMENTO: <strong>'.$result[2].'</strong></p><p>FILIAÇÃO: <strong>'.$result[3].'</strong></p>
					</a>
				</td>
				<td>
					<a style="cursor:pointer;" onClick="exibe(\''.$result[9].'\');" onClick="fill(\''.$result[9].'\');">
					<p>TURMA: <strong>'.$result[13].'</strong></p>
					<p>TURNO: <strong>'.$result[14].'</strong></p>
					<p>SITUAÇÃO: <strong>'.$result[11].'</strong></p>
					</a>
				</td>
				<td>
					<a class="ls-btn-primary" onClick="exibe(\''.$result[9].'\');" onClick="fill(\''.$result[9].'\');">Ver aluno</a>
				</td>
			</tr>
			-->
			
			';
				
			
				}
				
			echo '
			</tbody>
			</table>';
			
			mysql_free_result($query);
		}
	}	else {
		if ($totalRows_Noticia == 0) { 
		mysql_free_result($query);
		header("Location: vinculoAlunoExibirTurma.php?erro");
		exit;
}
	}


?>
<?php

?>
