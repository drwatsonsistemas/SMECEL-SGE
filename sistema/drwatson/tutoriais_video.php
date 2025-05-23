<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php require_once('funcoes/configuracoes.php'); ?>

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
$MM_authorizedUsers = "99";
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO smc_tutoriais_video (tutoriais_video_titulo, tutoriais_video_url, tutoriais_video_painel) VALUES (%s, %s, %s)",
                       GetSQLValueString($_POST['tutoriais_video_titulo'], "text"),
                       GetSQLValueString($_POST['tutoriais_video_url'], "text"),
                       GetSQLValueString($_POST['tutoriais_video_painel'], "double"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

  $insertGoTo = "tutoriais_video.php?inserido";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Tutoriais = "
SELECT tutoriais_video_id, tutoriais_video_titulo, tutoriais_video_url, tutoriais_video_painel,
CASE tutoriais_video_painel
WHEN 1 THEN '<span class=ls-tag-primary>SECRETARIA</span>'
WHEN 2 THEN '<span class=ls-tag-success>ESCOLA</span>'
WHEN 3 THEN '<span class=ls-tag-info>PROFESSOR</span>'
WHEN 4 THEN '<span class=ls-tag-warning>ALUNO</span>'
WHEN 5 THEN '<span class=ls-tag-danger>PORTARIA</span>'
WHEN 6 THEN '<span class=ls-tag>PSE</span>'
END AS tutoriais_video_painel_descricao 
FROM smc_tutoriais_video
WHERE 
tutoriais_video_painel IN (1, 2, 3, 4, 5, 6)
ORDER BY tutoriais_video_painel ASC";
$Tutoriais = mysql_query($query_Tutoriais, $SmecelNovo) or die(mysql_error());
$row_Tutoriais = mysql_fetch_assoc($Tutoriais);
$totalRows_Tutoriais = mysql_num_rows($Tutoriais);
?>

<!DOCTYPE html>
<html class="<?php echo COR_TEMA ?>">
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
<link rel="stylesheet" type="text/css" href="//assets.locaweb.com.br/locastyle/edge/stylesheets/locastyle.css">
<script src="//assets.locaweb.com.br/locastyle/edge/javascripts/locastyle.js"></script>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
<style>
  df-messenger {
   --df-messenger-bot-message: white;
   --df-messenger-button-titlebar-color: #075e54;
   --df-messenger-chat-background-color: #ece5dd;
   --df-messenger-font-color: black;
   --df-messenger-send-icon: #878fac;
   --df-messenger-user-message: #dcf8c6;
  }
</style>
</head>
<body>
<?php include_once("menu_top.php"); ?>
<?php include_once "menu.php"; ?>
<main class="ls-main">
  <div class="container-fluid">
    <h1 class="ls-title-intro ls-ico-home">TUTORIAIS (VÍDEO)</h1>
    <div class="ls-box ls-board-box"> 
    <!-- CONTEUDO -->

    
    <button data-ls-module="modal" data-target="#myAwesomeModal" class="ls-btn-primary">INSERIR VÍDEO</button>

<div class="ls-modal" id="myAwesomeModal">
  <div class="ls-modal-box">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title">Modal title</h4>
    </div>
    <div class="ls-modal-body" id="myModalBody">
      
      
          <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form">
      
      <label class="ls-label col-md-12">
      <b class="ls-label-text">TÍTULO DO VÍDEO</b>
      <input type="text" name="tutoriais_video_titulo" value="" size="32" required>
    </label>
    
      
      <label class="ls-label col-md-12">
      <b class="ls-label-text">URL DO VÍDEO</b>
    <input type="text" name="tutoriais_video_url" value="" size="32" required>
    </label>
      
       <label class="ls-label col-md-12">
      <b class="ls-label-text">PAINEL</b>
       <div class="ls-custom-select">
      <select name="tutoriais_video_painel" class="ls-select" required>
            <option value="">-</option>
            <option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>SECRETARIA</option>
            <option value="2" <?php if (!(strcmp(2, ""))) {echo "SELECTED";} ?>>ESCOLA</option>
            <option value="3" <?php if (!(strcmp(3, ""))) {echo "SELECTED";} ?>>PROFESSOR</option>
            <option value="4" <?php if (!(strcmp(4, ""))) {echo "SELECTED";} ?>>ALUNO</option>
            <option value="5" <?php if (!(strcmp(5, ""))) {echo "SELECTED";} ?>>PORTARIA</option>
            <option value="6" <?php if (!(strcmp(6, ""))) {echo "SELECTED";} ?>>PSE</option>
          </select>
          </div>
          </label>
          
           
          
      <input type="hidden" name="MM_insert" value="form1">
    
      
      
    </div>
    <div class="ls-modal-footer">
      <a class="ls-btn ls-float-right" data-dismiss="modal">FECHAR</a>
      <input type="submit" value="SALVAR" class="ls-btn">
    </div></form>
  </div>
</div><!-- /.modal -->
    
    
    <p>&nbsp;</p>
    <table class="ls-table">
      <tr>
        <td width="100">Cód</td>
        <td>Título</td>
        <td width="100">Painel</td>
      </tr>
      <?php do { ?>
      <?php $link = explode("=", $row_Tutoriais['tutoriais_video_url']); ?>

       
        <tr>
          <td>#<?php echo $row_Tutoriais['tutoriais_video_id']; ?></td>
          <td><a style="cursor:pointer" data-ls-module="modal" data-action="" data-content='<iframe width="100%" height="320" src="https://www.youtube.com/embed/<?php echo $link[1]; ?>" title="<?php echo $row_Tutoriais['tutoriais_video_titulo']; ?>" frameborder="0" allow="" allowfullscreen></iframe>' data-title="<?php echo $row_Tutoriais['tutoriais_video_titulo']; ?>" data-class="ls-btn-danger" data-save="FECHAR" data-close="CANCELAR"><?php echo $row_Tutoriais['tutoriais_video_titulo']; ?></a></td>
          <td><?php echo $row_Tutoriais['tutoriais_video_painel_descricao']; ?></td>
        </tr>
        
        <?php } while ($row_Tutoriais = mysql_fetch_assoc($Tutoriais)); ?>
    </table>
<!-- CONTEUDO -->    
    </div>
  </div>
</main>
<?php include_once "notificacoes.php"; ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="https://assets.locaweb.com.br/locastyle/3.8.4/javascripts/locastyle.js" type="text/javascript"></script>
<script src="https://www.gstatic.com/dialogflow-console/fast/messenger/bootstrap.js?v=1"></script>
<df-messenger
  chat-icon="https:&#x2F;&#x2F;storage.googleapis.com&#x2F;cloudprod-apiai&#x2F;d6fbe379-bf37-4b42-b738-5ec0762d62da_x.png"
  intent="WELCOME"
  chat-title="Smecel-FAQ"
  agent-id="553bbcb9-1afc-4a31-9d38-44ae05426572"
  language-code="pt-br"
></df-messenger>	
	
</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Tutoriais);
?>