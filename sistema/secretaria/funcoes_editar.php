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


function formatarParaBanco($valor) {
  return str_replace(',', '.', str_replace('.', '', $valor));
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $media_salarial = formatarParaBanco($_POST['funcao_media_salarial']);
  $updateSQL = sprintf(
    "UPDATE smc_funcao SET funcao_nome=%s, funcao_observacoes=%s, funcao_docencia=%s, funcao_gestor_escolar=%s, funcao_media_salarial=%s WHERE funcao_id=%s",
    GetSQLValueString($_POST['funcao_nome'], "text"),
    GetSQLValueString($_POST['funcao_observacoes'], "text"),
    GetSQLValueString(isset($_POST['funcao_docencia']) ? "true" : "", "defined", "'S'", "'N'"),
    GetSQLValueString(isset($_POST['funcao_gestor_escolar']) ? "true" : "", "defined", "'S'", "'N'"),
    GetSQLValueString($media_salarial, "int"),
    GetSQLValueString($_POST['funcao_id'], "int")
  );

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

  $updateGoTo = "funcoes.php?editado";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

require_once('funcoes/usuLogado.php');
require_once('funcoes/anoLetivo.php');

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

$colname_Funcoes = "-1";
if (isset($_GET['cod'])) {
  $colname_Funcoes = $_GET['cod'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Funcoes = sprintf("SELECT * FROM smc_funcao WHERE funcao_secretaria_id = '$row_Secretaria[sec_id]' AND funcao_id = %s", GetSQLValueString($colname_Funcoes, "int"));
$Funcoes = mysql_query($query_Funcoes, $SmecelNovo) or die(mysql_error());
$row_Funcoes = mysql_fetch_assoc($Funcoes);
$totalRows_Funcoes = mysql_num_rows($Funcoes);

if ($totalRows_Funcoes < 1) {
  $red = "index.php?erro&cod=fex11c";
  header(sprintf("Location: %s", $red));
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
  <script src="js/locastyle.js"></script>
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
      <h1 class="ls-title-intro ls-ico-home">EDITAR CARGO/FUNÇÃO</h1>
      <div class="ls-box ls-board-box">
        <!-- CONTEUDO -->
        <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form-horizontal">

          <label class="ls-label col-md-12">
            <b class="ls-label-text">NOME DA FUNÇÃO/CARGO</b>
            <p class="ls-label-info">Informe o nome da função</p>
            <input type="text" name="funcao_nome"
              value="<?php echo htmlentities($row_Funcoes['funcao_nome'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>

          <label class="ls-label col-md-12">
            <b class="ls-label-text">MÉDIA SALARIAL</b>
            <p class="ls-label-info">Insira a média salarial da função</p>
            <div class="ls-prefix-group ls-field-md">
              <span class="ls-label-text-prefix">R$</span>
              <input type="text" class="money2" name="funcao_media_salarial" value="<?php echo $row_Funcoes['funcao_media_salarial'] ?>">
            </div>
          </label>

          <label class="ls-label col-md-12">
            <b class="ls-label-text">ATIVIDADE DE DOCÊNCIA</b>
            <input type="checkbox" name="funcao_docencia" value="" <?php if (!(strcmp(htmlentities($row_Funcoes['funcao_docencia'], ENT_COMPAT, 'utf-8'), "S"))) {
              echo "checked=\"checked\"";
            } ?>>
            <p class="ls-label-info">Marque se essa função exerce docência, no caso, a função de Professor</p>
          </label>

          <label class="ls-label col-md-12">
            <b class="ls-label-text">ATIVIDADE DE GESTÃO ESCOLAR</b>
            <input type="checkbox" name="funcao_gestor_escolar" value="" <?php if (!(strcmp(htmlentities($row_Funcoes['funcao_gestor_escolar'], ENT_COMPAT, 'utf-8'), "S"))) {
              echo "checked=\"checked\"";
            } ?>>
            <p class="ls-label-info">Marque se essa função exerce gestão escolar, no caso direção escolar</p>
          </label>

          <label class="ls-label col-md-12">
            <b class="ls-label-text">OBSERVAÇÕES</b>
            <p class="ls-label-info">Informe detalhes sobre a função</p>
            <textarea name="funcao_observacoes" cols="50"
              rows="5"><?php echo htmlentities($row_Funcoes['funcao_observacoes'], ENT_COMPAT, 'utf-8'); ?></textarea>
          </label>

          <div class="ls-modal-footer">
            <button type="submit" class="ls-btn-primary">SALVAR</button>
            <a href="funcoes.php" class="ls-btn ls-float-right">CANCELAR</a>
          </div>

          <input type="hidden" name="MM_update" value="form1">
          <input type="hidden" name="funcao_id" value="<?php echo $row_Funcoes['funcao_id']; ?>">
        </form>
        <p>&nbsp;</p>
        <!-- CONTEUDO -->
      </div>
    </div>
  </main>
  <?php include_once "notificacoes.php"; ?>

  <!-- We recommended use jQuery 1.10 or up -->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="js/locastyle.js"></script>
  <script src="js/mascara.js"></script>
</body>

</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

mysql_free_result($Funcoes);

mysql_free_result($Escolas);
?>