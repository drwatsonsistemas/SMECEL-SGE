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
  $insertSQL = sprintf("INSERT INTO smc_atualizacoes (atualizacoes_painel, atualizacoes_modulo, atualizacoes_texto) VALUES (%s, %s, %s)",
                       GetSQLValueString($_POST['atualizacoes_painel'], "text"),
                       GetSQLValueString($_POST['atualizacoes_modulo'], "text"),
                       GetSQLValueString($_POST['atualizacoes_texto'], "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

  $insertGoTo = "atualizacoes.php?novo";
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
$query_Atualizacoes = "
SELECT atualizacoes_id, atualizacoes_painel, atualizacoes_modulo, atualizacoes_texto, atualizacoes_data,
CASE atualizacoes_painel
WHEN 1 THEN '<span class=\"ls-tag-primary\">SECRETARIA DE EDUCAÇÃO</span>' 
WHEN 2 THEN '<span class=\"ls-tag-success\">PAINEL ESCOLA</span>' 
WHEN 3 THEN '<span class=\"ls-tag-info\">PAINEL DO PROFESSOR</span>' 
WHEN 4 THEN '<span class=\"ls-tag-warning\">PAINEL DO ALUNO</span>' 
WHEN 5 THEN '<span class=\"ls-tag-danger\">PORTARIA</span>' 
WHEN 6 THEN '<span class=\"ls-tag-danger\">PSE</span>' 
WHEN 99 THEN '<span class=\"ls-tag\">GPI</span>' 
END AS atualizacoes_painel
FROM smc_atualizacoes 
ORDER BY atualizacoes_id DESC";
$Atualizacoes = mysql_query($query_Atualizacoes, $SmecelNovo) or die(mysql_error());
$row_Atualizacoes = mysql_fetch_assoc($Atualizacoes);
$totalRows_Atualizacoes = mysql_num_rows($Atualizacoes);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AtualizacoesUltima = "
SELECT *
FROM smc_atualizacoes 
ORDER BY atualizacoes_id DESC";
$AtualizacoesUltima = mysql_query($query_AtualizacoesUltima, $SmecelNovo) or die(mysql_error());
$row_AtualizacoesUltima = mysql_fetch_assoc($AtualizacoesUltima);
$totalRows_AtualizacoesUltima = mysql_num_rows($AtualizacoesUltima);

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
</head>
<body>
<?php include_once("menu_top.php"); ?>
<?php include_once "menu.php"; ?>
<main class="ls-main">
  <div class="container-fluid">
    <h1 class="ls-title-intro ls-ico-home">ATUALIZAÇÕES</h1>
    <div class="ls-box ls-board-box"> 
      <!-- CONTEUDO -->
      
      <button data-ls-module="modal" data-target="#myAwesomeModal" class="ls-btn-primary">CADASTRAR NOTA DE VERSÃO</button>
      <p>Última versão 1.<?php echo $row_AtualizacoesUltima['atualizacoes_id']; ?></p>
      
      <hr>
    
    <?php do { ?>


      <?php 
      
      mysql_select_db($database_SmecelNovo, $SmecelNovo);
      $query_AtualizacoesUltimaVer = "
      SELECT *
      FROM smc_atualizacao_ver 
      INNER JOIN smc_usu ON usu_id = atualizacao_ver_cod_usuario
      INNER JOIN smc_sec ON sec_id = usu_sec 
      WHERE atualizacao_ver_cod_atualizacao = $row_Atualizacoes[atualizacoes_id]
      ORDER BY usu_sec
      ";
      $AtualizacoesUltimaVer = mysql_query($query_AtualizacoesUltimaVer, $SmecelNovo) or die(mysql_error());
      $row_AtualizacoesUltimaVer = mysql_fetch_assoc($AtualizacoesUltimaVer);
      $totalRows_AtualizacoesUltimaVer = mysql_num_rows($AtualizacoesUltimaVer);
        
      ?>

      <div data-ls-module="collapse" data-target="#<?php echo $row_Atualizacoes['atualizacoes_id']; ?>" class="ls-collapse" aria-expanded="false"> 
      <small class="ls-collapse-title ls-collapse-header"> <?php echo $row_Atualizacoes['atualizacoes_painel']; ?> 
        <p> <?php echo date('d/m/Y - H\hi', strtotime($row_Atualizacoes['atualizacoes_data'])); ?> - <strong><?php echo $totalRows_AtualizacoesUltimaVer; ?> visualizações</strong></p>
        </small>
        <div class="ls-collapse-body" id="<?php echo $row_Atualizacoes['atualizacoes_id']; ?>">
          <p> <i><small><b><?php echo $row_Atualizacoes['atualizacoes_modulo']; ?></b></small><br> <?php echo $row_Atualizacoes['atualizacoes_texto']; ?></i></p>
          <p>
          <?php
              do {
                echo "-".$row_AtualizacoesUltimaVer['sec_cidade']." | ".$row_AtualizacoesUltimaVer['usu_nome']."<br>";  
              } while ($row_AtualizacoesUltimaVer = mysql_fetch_assoc($AtualizacoesUltimaVer));

          ?>
          </p>
        </div>
      </div>
      <?php } while ($row_Atualizacoes = mysql_fetch_assoc($Atualizacoes)); ?>
      
    
    <!-- CONTEUDO --> 
    
  </div>
  </div>
</main>

<div class="ls-modal" id="myAwesomeModal">
        <div class="ls-modal-box">
          <div class="ls-modal-header">
            <button data-dismiss="modal">&times;</button>
            <h4 class="ls-modal-title">CADASTRAR NOTA DE VERSÃO</h4>
          </div>
          <div class="ls-modal-body" id="myModalBody">
          <p>
          <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form">
            <label class="ls-label col-md-12">
            <b class="ls-label-text">Painel</b>
            <div class="ls-custom-select">
              <select name="atualizacoes_painel" required>
                <option value="">Escolha...</option>
                <option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>SECRETARIA DE EDUCAÇÃO</option>
                <option value="2" <?php if (!(strcmp(2, ""))) {echo "SELECTED";} ?>>ESCOLA</option>
                <option value="3" <?php if (!(strcmp(3, ""))) {echo "SELECTED";} ?>>PROFESSOR</option>
                <option value="4" <?php if (!(strcmp(4, ""))) {echo "SELECTED";} ?>>ALUNO</option>
                <option value="5" <?php if (!(strcmp(5, ""))) {echo "SELECTED";} ?>>PORTARIA</option>
                <option value="6" <?php if (!(strcmp(6, ""))) {echo "SELECTED";} ?>>PSE</option>
                <option value="99" <?php if (!(strcmp(99, ""))) {echo "SELECTED";} ?>>GPI</option>
              </select>
            </div>
            </label>
            
            <label class="ls-label col-md-12">
              <b class="ls-label-text">Módulo</b>
              <p class="ls-label-info">(Turmas, Alunos, Cardápios etc.)</p>
              <input type="text" name="atualizacoes_modulo">
            </label>
            
            <label class="ls-label col-md-12"> <b class="ls-label-text">Descrição da atualização</b>
              <textarea name="atualizacoes_texto" id="mytextarea" cols="50" rows="5"></textarea>
            </label>
            <input type="hidden" name="MM_insert" value="form1">
            </p>
            </div>
            <div class="ls-modal-footer">
            <span class="ls-btn ls-float-right" data-dismiss="modal">CANCELAR</span>
            <button type="submit" class="ls-btn-primary">SALVAR</button>
          </form>
        </div>
      </div>
    </div>
    <!-- /.modal -->
    
<?php include_once "notificacoes.php"; ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="https://assets.locaweb.com.br/locastyle/3.8.4/javascripts/locastyle.js" type="text/javascript"></script>
<script src="//cdn.tinymce.com/4/tinymce.min.js"></script> 

<script src="https://cdn.tiny.cloud/1/iq1gy8qt7s1b9bj92c2fc7whie95augu8r8kz97dgoufkli8/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>

<script>

	tinymce.init({
	  selector: '#mytextarea',
	  height: 300,
	  toolbar: 'bold italic | bullist numlist | image | alignleft aligncenter alignright alignjustify | link h2 h3 blockquote',
	  plugins : 'advlist autolink link autolink image imagetools lists charmap print preview paste',
	  statusbar: false,
	  menubar: false,
	  paste_as_text: true,
	  content_css: '//www.tinymce.com/css/codepen.min.css'
	});

</script> 
</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Atualizacoes);
?>