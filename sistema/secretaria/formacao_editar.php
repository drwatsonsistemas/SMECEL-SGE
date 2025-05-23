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


$colname_Formacao = "-1";
if (isset($_GET['codigo'])) {
  $colname_Formacao = $_GET['codigo'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Formacao = sprintf("SELECT curso_form_id, curso_form_id_sec, curso_form_nome, curso_form_descricao, curso_form_data_inicio, curso_form_data_fim, curso_form_responsavel, curso_form_ch, curso_form_hash, curso_form_aberto FROM smc_curso_formacao WHERE curso_form_hash = %s", GetSQLValueString($colname_Formacao, "text"));
$Formacao = mysql_query($query_Formacao, $SmecelNovo) or die(mysql_error());
$row_Formacao = mysql_fetch_assoc($Formacao);
$totalRows_Formacao = mysql_num_rows($Formacao);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE smc_curso_formacao SET curso_form_nome=%s, curso_form_descricao=%s, curso_form_data_inicio=%s, curso_form_data_fim=%s, curso_form_responsavel=%s, curso_form_ch=%s, curso_form_aberto=%s WHERE curso_form_id=%s",
                       GetSQLValueString($_POST['curso_form_nome'], "text"),
                       GetSQLValueString($_POST['curso_form_descricao'], "text"),
                       GetSQLValueString($_POST['curso_form_data_inicio'], "date"),
                       GetSQLValueString($_POST['curso_form_data_fim'], "date"),
                       GetSQLValueString($_POST['curso_form_responsavel'], "text"),
                       GetSQLValueString($_POST['curso_form_ch'], "text"),
					   GetSQLValueString($_POST['curso_form_aberto'], "text"),
                       GetSQLValueString($_POST['curso_form_id'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

  $updateGoTo = "formacao.php?editado";
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
    <h1 class="ls-title-intro ls-ico-chart-bar-up">EDITAR FORMAÇÃO</h1>
    <!-- CONTEUDO -->

<a href="formacao.php" class="ls-btn-primary">Voltar</a>

<hr>    
    
<form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form ls-form-horizontal row">
    
    <fieldset>
      
        <label class="ls-label col-sm-12">
          <b class="ls-label-text">Formação</b>
          <p class="ls-label-info">Digite o nome da formação/curso</p>
          <input type="text" name="curso_form_nome" value="<?php echo htmlentities($row_Formacao['curso_form_nome'], ENT_COMPAT, 'utf-8'); ?>" size="32">
        </label>
        
        <label class="ls-label col-sm-12">
          <b class="ls-label-text">Descrição</b>
          <p class="ls-label-info">Informe uma breve descrição da formação/curso</p>
          <textarea id="formacao_textarea" name="curso_form_descricao" cols="50" rows="2"><?php echo htmlentities($row_Formacao['curso_form_descricao'], ENT_COMPAT, 'utf-8'); ?></textarea>
        </label>
        
        <label class="ls-label col-sm-4">
          <b class="ls-label-text">Data de início</b>
          <input type="date" name="curso_form_data_inicio" value="<?php echo htmlentities($row_Formacao['curso_form_data_inicio'], ENT_COMPAT, 'utf-8'); ?>" size="32">
        </label>
      
        <label class="ls-label col-sm-4">
          <b class="ls-label-text">Data final</b>
          <input type="date" name="curso_form_data_fim" value="<?php echo htmlentities($row_Formacao['curso_form_data_fim'], ENT_COMPAT, 'utf-8'); ?>" size="32">
        </label>

        <label class="ls-label col-sm-4">
          <b class="ls-label-text">Carga horária</b>
          <input type="text" name="curso_form_ch" value="<?php echo htmlentities($row_Formacao['curso_form_ch'], ENT_COMPAT, 'utf-8'); ?>" size="32">
        </label>
              
        <label class="ls-label col-sm-12">
          <b class="ls-label-text">Responsável pela formação</b>
          <input type="text" name="curso_form_responsavel" value="<?php echo htmlentities($row_Formacao['curso_form_responsavel'], ENT_COMPAT, 'utf-8'); ?>" size="32">
        </label>
        
    <div class="ls-label col-md-12">
      <p>Status da formação:</p>
      <label class="ls-label-text">
        <input type="radio" name="curso_form_aberto" class="ls-field-radio" value="S" <?php if (!(strcmp(htmlentities($row_Formacao['curso_form_aberto'], ENT_COMPAT, 'utf-8'),"S"))) {echo "checked=\"checked\"";} ?>>
        Aberto
      </label>
      <label class="ls-label-text">
        <input type="radio" name="curso_form_aberto" class="ls-field-radio" value="N" <?php if (!(strcmp(htmlentities($row_Formacao['curso_form_aberto'], ENT_COMPAT, 'utf-8'),"N"))) {echo "checked=\"checked\"";} ?>>
        Fechado
      </label>
    </div>
      
           
      </fieldset>
    
       <div class="ls-modal-footer">
      <button type="submit" class="ls-btn-primary">SALVAR</button>
    </div>
    
      <input type="hidden" name="MM_update" value="form1">
      <input type="hidden" name="curso_form_id" value="<?php echo $row_Formacao['curso_form_id']; ?>">
    </form>
    <p>&nbsp;</p>
<p>&nbsp;</p>
    <!-- CONTEUDO -->    
  </div>
</main>
<?php include_once "notificacoes.php"; ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>

		<script src="https://cdn.tiny.cloud/1/iq1gy8qt7s1b9bj92c2fc7whie95augu8r8kz97dgoufkli8/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
		<script src="langs/pt_BR.js"></script>


    <script>

	tinymce.init({
	  selector: '#formacao_textarea',
	  height: 500,
	  toolbar: 'bold italic | bullist numlist | image emoticons | alignleft aligncenter alignright alignjustify | link h2 h3 blockquote',
	  plugins : 'advlist autolink link autolink image imagetools lists charmap print preview paste emoticons',
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

mysql_free_result($Secretaria);

mysql_free_result($Formacao);
?>