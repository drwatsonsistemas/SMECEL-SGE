<?php require_once('../../Connections/SmecelNovo.php'); ?>
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

$escola_nome = "ERRO AO EXIBIR";
if (isset($_GET['nome'])) {
  $escola_nome = $_GET['nome'];
}

$codigo = "-1";
if (isset($_GET['codigo'])) {
  $codigo = $_GET['codigo'];
}

require_once('funcoes/usuLogado.php');
require_once('funcoes/anoLetivo.php');


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);


$situacao = 1;
if (isset($_GET['situacao'])) {
$situacao = $_GET['situacao'];
	switch ($situacao) {
    case 1:
        $situacao = 1;
        break;
    case 2:
        $situacao = 2;
        break;
    case 3:
        $situacao = 3;
        break;
    default:
       $situacao = 1;
	}
  }


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Escolas = "SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_ue, escola_situacao, escola_localizacao FROM smc_escola WHERE escola_situacao = $situacao AND escola_id_sec = '$row_UsuarioLogado[usu_sec]' AND escola_ue = '1' AND escola_situacao = '1' ORDER BY escola_nome ASC";
$Escolas = mysql_query($query_Escolas, $SmecelNovo) or die(mysql_error());
$row_Escolas = mysql_fetch_assoc($Escolas);
$totalRows_Escolas = mysql_num_rows($Escolas);




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
    <h1 class="ls-title-intro ls-ico-home">UNIDADES ESCOLARES</h1>
    <div class="ls-box ls-board-box">
      <?php if (isset($_GET["cadastrado"])) { ?>
        <div class="ls-alert-success ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> <strong>Aviso:</strong> Escola <strong><?php echo $escola_nome; ?></strong> cadastrada com sucesso! </div>
        <?php } ?>
      <?php if (isset($_GET["editada"])) { ?>
        <div class="ls-alert-success ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> <strong>Aviso:</strong> Escola <strong><?php echo $escola_nome; ?></strong> editada com sucesso! </div>
        <?php } ?>
      <?php if (isset($_GET["erro"])) { ?>
        <div class="ls-alert-danger ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> <strong>Ops! </strong> Isso não deveria ter acontecido. Um e-mail foi enviado ao administrador do sistema! </div>
        <?php } ?>
      <a href="escolas_cadastrar.php" class="ls-btn-primary ls-ico-plus">CADASTRAR UNIDADE ESCOLAR/DEPARTAMENTO</a>
      <div class="ls-group-btn ls-group-active ls-float-right"> <a href="escolas.php?situacao=1" class="ls-btn-primary <?php if ($situacao=="1") { ?> ls-active<?php } ?>">ATIVAS</a> <a href="escolas.php?situacao=2" class="ls-btn-primary <?php if ($situacao=="2") { ?> ls-active<?php } ?>">PARALISADAS</a> <a href="escolas.php?situacao=3" class="ls-btn-primary <?php if ($situacao=="3") { ?> ls-active<?php } ?>">EXTINTAS</a> </div>

      <?php
      // Contagem de escolas por localização
      $total_urbana = 0;
      $total_campo = 0;
      $total_geral = 0;
      
      if ($totalRows_Escolas > 0) {
          do {
              if ($row_Escolas['escola_localizacao'] == 'U') {
                  $total_urbana++;
              } else {
                  $total_campo++;
              }
              $total_geral++;
          } while ($row_Escolas = mysql_fetch_assoc($Escolas));
          
          // Reset do ponteiro do resultado
          mysql_data_seek($Escolas, 0);
          $row_Escolas = mysql_fetch_assoc($Escolas);
      }
      ?>

      <div class="ls-box ls-board-box">
          <div class="row">
              <div class="col-md-4">
                  <div class="ls-box">
                      <div class="ls-box-head">
                          <h6 class="ls-title-4">TOTAL DE ESCOLAS</h6>
                      </div>
                      <div class="ls-box-body">
                          <span class="ls-board-data">
                              <strong class="ls-color-theme"><?php echo $total_geral; ?></strong>
                          </span>
                      </div>
                  </div>
              </div>
              <div class="col-md-4">
                  <div class="ls-box">
                      <div class="ls-box-head">
                          <h6 class="ls-title-4">ESCOLAS URBANAS</h6>
                      </div>
                      <div class="ls-box-body">
                          <span class="ls-board-data">
                              <strong class="ls-color-info"><?php echo $total_urbana; ?></strong>
                          </span>
                      </div>
                  </div>
              </div>
              <div class="col-md-4">
                  <div class="ls-box">
                      <div class="ls-box-head">
                          <h6 class="ls-title-4">ESCOLAS DO CAMPO</h6>
                      </div>
                      <div class="ls-box-body">
                          <span class="ls-board-data">
                              <strong class="ls-color-success"><?php echo $total_campo; ?></strong>
                          </span>
                      </div>
                  </div>
              </div>
          </div>
      </div>

      <?php if ($totalRows_Escolas > 0) { // Show if recordset not empty ?>

<table class="ls-table ls-sm-space">
        <thead>
          <tr>
            <th width="120">INEP</th>
            <th>ESCOLAS DO MUNICÍPIO</th>
            <th >LOCALIZAÇÃO</th>
            <th width="100"></th>
            <th width="100"></th>
          </tr>
        </thead>
        <?php do { ?>
          <tr>
            <td><a href="escola.php?escola=<?php echo $row_Escolas['escola_id']; ?>"><?php echo $row_Escolas['escola_inep']; ?></a>
              <?php if ($codigo == $row_Escolas['escola_id']) { ?>
              <i class="ls-ico-checkbox-checked"></i>
              <?php } ?>
            </td>
            <td><?php echo $row_Escolas['escola_nome']; ?></td>
            <td><?php echo $row_Escolas['escola_localizacao'] == 'U' ? 'URBANA' : 'DO CAMPO'; ?></td>
            <td><a href="escola.php?escola=<?php echo $row_Escolas['escola_id']; ?>" class="ls-ico-edit-admin ls-float-right">Detalhes</a></td>
            <td><a href="escolas_editar.php?codigo=<?php echo $row_Escolas['escola_id']; ?>" class="ls-ico-edit-admin ls-float-right">Editar</a></td>
          </tr>
          <?php } while ($row_Escolas = mysql_fetch_assoc($Escolas)); ?>
      </table>
      
<?php } else { ?>
<hr>
<div class="ls-alert-warning"><strong>Cuidado:</strong> Nenhuma escola cadastrada.</div>
<?php } // Show if recordset not empty ?>

<!-- <p class="ls-float-right">Total de Escolas: <?php echo $totalRows_Escolas; ?></p> -->
    </div>
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

mysql_free_result($Escolas);
?>