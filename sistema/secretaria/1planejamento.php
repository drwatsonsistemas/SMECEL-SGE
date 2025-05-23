<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php require_once('funcoes/anti_injection.php'); ?>
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

require_once('funcoes/usuLogado.php');
require_once('funcoes/anoLetivo.php');

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Escola = "SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_ue, escola_situacao, escola_localizacao, escola_ibge_municipio, escola_tema, escola_unidade_executora, escola_caixa_ux_prestacao_contas FROM smc_escola WHERE escola_id_sec = '$row_UsuarioLogado[usu_sec]' AND escola_situacao = '1' AND escola_ue = '1'";
$Escola = mysql_query($query_Escola, $SmecelNovo) or die(mysql_error());
$row_Escola = mysql_fetch_assoc($Escola);
$totalRows_Escola = mysql_num_rows($Escola);


$colname_Escola = "-1";
if (isset($_GET['escola'])) {
  $colname_Escola = (int)anti_injection($_GET['escola']);
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Ac = "
SELECT ac_id, ac_id_professor, ac_id_etapa, ac_id_componente, ac_id_escola, ac_ano_letivo, ac_data_inicial, ac_data_final, ac_conteudo, ac_objetivo_especifico, ac_objeto_conhecimento, ac_metodologia, ac_recursos, ac_avaliacao, ac_criacao,
func_id, func_nome, disciplina_id, disciplina_nome, etapa_id, etapa_nome, escola_id, escola_id_sec, escola_nome 
FROM smc_ac
LEFT JOIN smc_func ON func_id = ac_id_professor
LEFT JOIN smc_disciplina ON disciplina_id = ac_id_componente 
LEFT JOIN smc_etapa ON etapa_id = ac_id_etapa
LEFT JOIN smc_escola ON escola_id = ac_id_escola
WHERE ac_id_escola = '$colname_Escola' AND escola_id_sec = '$row_UsuarioLogado[usu_sec]'
ORDER BY ac_id DESC
";
$Ac = mysql_query($query_Ac, $SmecelNovo) or die(mysql_error());
$row_Ac = mysql_fetch_assoc($Ac);
$totalRows_Ac = mysql_num_rows($Ac);

$nomeMenu = "ESCOLHA UMA ESCOLA";
if (isset($_GET['escola']) && $totalRows_Ac > 0) {
  $nomeMenu = $row_Ac['escola_nome'];
}


?>

<!DOCTYPE html>
<html class="ls-theme-green">
<head>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-117872281-1');
</script>

<title>SMECEL - Sistema de Gestão Escolar</title>
<meta charset="utf-8">
<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<meta name="description" content="">
<meta name="keywords" content="">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<link rel="stylesheet" type="text/css" href="css/locastyle.css">
<script src="js/locastyle.js"></script><link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<body>
<?php include_once("menu_top.php"); ?>
<?php include_once "menu.php"; ?>
<main class="ls-main">
  <div class="container-fluid">
    <h1 class="ls-title-intro ls-ico-home">PLANEJAMENTO (A/C)</h1>
    <!-- CONTEUDO -->
    

<div data-ls-module="dropdown" class="ls-dropdown">
  <a href="#" class="ls-btn-primary"><?php echo $nomeMenu; ?></a>
  <ul class="ls-dropdown-nav">
	<?php do { ?>
      <li><a href="planejamento.php?escola=<?php echo $row_Escola['escola_id']; ?>"><?php echo $row_Escola['escola_nome']; ?></a></li>
    <?php } while ($row_Escola = mysql_fetch_assoc($Escola)); ?>
  </ul>
</div>      


<hr>

  <?php if ($totalRows_Ac > 0) { // Show if recordset not empty ?>
  <table class="ls-table ls-sm-space">
    <thead>
      <tr>
        <th>PROFESSOR</th>
        <th class="ls-txt-center">COMPONENTE</th>
        <th class="ls-txt-center">ETAPA</th>
        <th class="ls-txt-center" width="100">ANO</th>
        <th class="ls-txt-center" width="120">DATA INICIAL</th>
        <th class="ls-txt-center" width="120">DATA FINAL</th>
        <th class="ls-txt-center" width="120">DIAS</th>
        <th class="ls-txt-center">CADASTRO</th>
        <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
      <?php 
	  
	  $trocar = array("\"", "\'","'");
	  
	  do { ?>
        <tr>
          <td><?php echo $row_Ac['func_nome']; ?></td>
          <td class="ls-txt-center"><?php echo $row_Ac['disciplina_nome']; ?></td>
          <td class="ls-txt-center"><?php echo $row_Ac['etapa_nome']; ?></td>
          <td class="ls-txt-center"><?php echo $row_Ac['ac_ano_letivo']; ?></td>
          <td class="ls-txt-center"><?php echo date("d/m/y", strtotime($row_Ac['ac_data_inicial'])); ?></td>
          <td class="ls-txt-center"><?php echo date("d/m/y", strtotime($row_Ac['ac_data_final'])); ?></td>
          <td class="ls-txt-center"><?php $diferenca = strtotime($row_Ac['ac_data_final']) - strtotime($row_Ac['ac_data_inicial']); echo $dias = floor($diferenca / (60 * 60 * 24))+1; ?></td>
          <td class="ls-txt-center"><?php echo date("d/m/y - H:i", strtotime($row_Ac['ac_criacao'])); ?></td>
		  <td class="ls-txt-center">
		  <button data-ls-module="modal" data-action="" data-content="
		  
		  <div class='ls-box'>
		  <h4>CONTEUDO</h4>
		  <p><?php echo str_replace($trocar, "", $row_Ac['ac_conteudo']); ?></p>
		  </div>
		  
		  <div class='ls-box'>
		  <h4>DIREITO DE APRENDIZAGEM</h4>
		  <p><?php echo str_replace($trocar, "", $row_Ac['ac_objetivo_especifico']); ?></p>
		  </div>
		  
		  <div class='ls-box'>
		  <h4>OBJETIVO DE APRENDIZAGEM</h4>
		  <p><?php echo str_replace($trocar, "", $row_Ac['ac_objeto_conhecimento']); ?></p>
		  </div>
		  
		  <div class='ls-box'>
		  <h4>METODOLOGIA</h4>
		  <p><?php echo str_replace($trocar, "", $row_Ac['ac_metodologia']); ?></p>
		  </div>
		  
		  <div class='ls-box'>
		  <h4>AVALIAÇÃO</h4>
		  <p><?php echo str_replace($trocar, "", $row_Ac['ac_avaliacao']); ?></p>
		  </div>
		  
		  " data-title="<?php echo $row_Ac['func_nome']; ?> - Componente: <?php echo $row_Ac['disciplina_nome']; ?> - Etapa: <?php echo $row_Ac['etapa_nome']; ?>" data-class="ls-btn-danger" data-save="" data-close="Fechar" class="ls-btn-primary"> Ver planejamento </button>
		  
		  </td>
        </tr>
        <?php } while ($row_Ac = mysql_fetch_assoc($Ac)); ?>
    </tbody>
  </table>
  <?php } else { ?>
  
  <p>Escolha uma Unidade Escolar</p>
  
  <?php } // Show if recordset not empty ?>
      
      
<p>&nbsp;</p>
    <!-- CONTEUDO -->    
  </div>
</main>
<?php include_once "notificacoes.php"; ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>
</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

mysql_free_result($Escola);

mysql_free_result($Ac);
?>