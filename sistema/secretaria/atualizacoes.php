<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}
// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF'] . "?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")) {
  $logoutAction .= "&" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) && ($_GET['doLogout'] == "true")) {
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
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup)
{
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
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("", $MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?"))
    $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0)
    $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo . $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: " . $MM_restrictGoTo);
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
$query_Atualizacoes = "
SELECT 
    atualizacoes_id, 
    atualizacoes_painel, 
    atualizacoes_modulo, 
    atualizacoes_texto, 
    atualizacoes_data,
    CASE atualizacoes_painel
        WHEN '1' THEN '<span class=\"ls-tag-primary\">SECRETARIA DE EDUCAÇÃO</span>'
        WHEN 2 THEN '<span class=\"ls-tag-success\">PAINEL ESCOLA</span>' 
        WHEN 3 THEN '<span class=\"ls-tag-info\">PAINEL DO PROFESSOR</span>' 
        WHEN 4 THEN '<span class=\"ls-tag-warning\">PAINEL DO ALUNO</span>' 
        WHEN 5 THEN '<span class=\"ls-tag-danger\">PORTARIA</span>' 
        WHEN 6 THEN '<span class=\"ls-tag-danger\">PSE</span>' 
        WHEN 99 THEN '<span class=\"ls-tag\">GPI</span>' 
    END AS atualizacoes_painel_tag
FROM smc_atualizacoes 
WHERE atualizacoes_painel = '1' 
ORDER BY atualizacoes_id DESC";

$Atualizacoes = mysql_query($query_Atualizacoes, $SmecelNovo) or die(mysql_error());
$totalRows_Atualizacoes = mysql_num_rows($Atualizacoes);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AtualizacoesUltima = "
SELECT * FROM smc_atualizacoes WHERE atualizacoes_painel = 1 ORDER BY atualizacoes_id DESC LIMIT 0,1";
$AtualizacoesUltima = mysql_query($query_AtualizacoesUltima, $SmecelNovo) or die(mysql_error());
$row_AtualizacoesUltima = mysql_fetch_assoc($AtualizacoesUltima);
$totalRows_AtualizacoesUltima = mysql_num_rows($AtualizacoesUltima);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AtualizacoesVisualizadas = "SELECT atualizacao_ver_id, atualizacao_ver_cod_atualizacao, atualizacao_ver_cod_usuario, atualizacao_ver_sec, atualizacao_ver_escola, atualizacao_ver_professor, atualizacao_ver_aluno, atualizacao_ver_data FROM smc_atualizacao_ver WHERE atualizacao_ver_cod_atualizacao = '$row_AtualizacoesUltima[atualizacoes_id]' AND atualizacao_ver_sec = '$row_Secretaria[sec_id]' AND atualizacao_ver_cod_usuario = '$row_UsuarioLogado[usu_id]'";
$AtualizacoesVisualizadas = mysql_query($query_AtualizacoesVisualizadas, $SmecelNovo) or die(mysql_error());
$row_AtualizacoesVisualizadas = mysql_fetch_assoc($AtualizacoesVisualizadas);
$totalRows_AtualizacoesVisualizadas = mysql_num_rows($AtualizacoesVisualizadas);

if ($totalRows_AtualizacoesVisualizadas == 0) {

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $insertSQL = "
INSERT INTO smc_atualizacao_ver (
	atualizacao_ver_cod_atualizacao, 
	atualizacao_ver_cod_usuario, 
	atualizacao_ver_sec 
) VALUES ('$row_AtualizacoesUltima[atualizacoes_id]', '$row_UsuarioLogado[usu_id]', '$row_Secretaria[sec_id]')
";
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

}




?>

<!DOCTYPE html>
<html class="ls-theme-green">

<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
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

  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>

<body>
  <?php include_once("menu_top.php"); ?>
  <?php include_once "menu.php"; ?>
  <main class="ls-main">
    <div class="container-fluid">
      <h1 class="ls-title-intro ls-ico-home">ATUALIZAÇÕES</h1>
      <!-- CONTEUDO -->

      PÁGINA EM MANUTENÇÃO



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

mysql_free_result($Atualizacoes);
?>