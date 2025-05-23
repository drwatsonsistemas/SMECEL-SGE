<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php require_once('funcoes/anoLetivo.php'); ?>
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
	
  $logoutGoTo = "../../index.php?exit";
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
$MM_authorizedUsers = "1,2,3";
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

$MM_restrictGoTo = "../../index.php?acessorestrito";
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

$colname_UsuarioLogado = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_UsuarioLogado = $_SESSION['MM_Username'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_UsuarioLogado = sprintf("SELECT usu_id, usu_nome, usu_email, usu_senha, usu_tipo, usu_sec, usu_escola, usu_status, usu_cadastro FROM smc_usu WHERE usu_email = %s", GetSQLValueString($colname_UsuarioLogado, "text"));
$UsuarioLogado = mysql_query($query_UsuarioLogado, $SmecelNovo) or die(mysql_error());
$row_UsuarioLogado = mysql_fetch_assoc($UsuarioLogado);
$totalRows_UsuarioLogado = mysql_num_rows($UsuarioLogado);

$colname_Etapa = "-1";
if (isset($_GET['c'])) {
  $colname_Etapa = $_GET['c'];
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Etapa = "
SELECT etapa_id, etapa_nome 
FROM smc_etapa
WHERE etapa_id = $colname_Etapa 
";
$Etapa = mysql_query($query_Etapa, $SmecelNovo) or die(mysql_error());
$row_Etapa = mysql_fetch_assoc($Etapa);
$totalRows_Etapa = mysql_num_rows($Etapa);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_TurmasFiltro = "

/*
SELECT turma_id, turma_id_escola, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turno_ano_letivo, etapa_id, etapa_id_filtro, etapa_filtro_id, etapa_filtro_nome, COUNT(*) AS total_filtro_etapas 
FROM smc_turma 
INNER JOIN smc_etapa 
ON etapa_id = turma_etapa 
INNER JOIN smc_etapa_filtro 
ON etapa_filtro_id = etapa_id
WHERE turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' 
GROUP BY etapa_filtro_id 
ORDER BY turma_etapa
*/

SELECT turma_id, turma_id_escola, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo, escola_id, escola_nome 
FROM smc_turma 
INNER JOIN smc_escola 
ON escola_id = turma_id_escola
WHERE (turma_etapa = $colname_Etapa AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]')
ORDER BY turma_etapa

";
$TurmasFiltro = mysql_query($query_TurmasFiltro, $SmecelNovo) or die(mysql_error());
$row_TurmasFiltro = mysql_fetch_assoc($TurmasFiltro);
$totalRows_TurmasFiltro = mysql_num_rows($TurmasFiltro);
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Painel Administrativo</title>
<link rel="stylesheet" href="../../css/foundation.css">
<link rel="stylesheet" href="../../css/normalize.css">
  <!-- This is how you would link your custom stylesheet -->
  <link rel="stylesheet" href="../css/app-painel.css">
  <script src="../../js/vendor/modernizr.js"></script>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>

<body>

<?php include "menu.php"; ?>
<div class="row">
	<div class="small-12 columns">
    	<h2>TURMAS POR ETAPA</h2>
		<h1><?php echo $row_Etapa['etapa_nome']; ?></h1>
        <table class="ls-table ls-no-hover ls-table-striped" width="100%">
        <thead>
          <tr>
            <th class="text-center">ESCOLA</th>
            <th class="text-center">TURMA</th>
          </tr>
          </thead>
          <tbody>
          <?php do { ?>
            <tr>
              <td class="text-left"><?php echo $row_TurmasFiltro['escola_nome']; ?></td>
              <td class="text-center"><?php echo $row_TurmasFiltro['turma_nome']; ?></td>
   		    </tr>
            <?php } while ($row_TurmasFiltro = mysql_fetch_assoc($TurmasFiltro)); ?>
        	</tbody>
        </table>
		
		<a href="rel_turmas_etapa.php" class="small button">Voltar</a>   
		
    </div>
</div>

<?php include "rodape.php"; ?>

<script src="../../js/vendor/jquery.js"></script>
  <script src="../../js/foundation.min.js"></script>
  <script src="js/foundation/foundation.dropdown.js"></script>
<script>
    $(document).foundation();
  </script>
</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($TurmasFiltro);
?>
