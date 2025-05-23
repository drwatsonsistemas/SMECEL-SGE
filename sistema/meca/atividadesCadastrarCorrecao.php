<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "funcoes/inverteData.php"; ?>
<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}
date_default_timezone_set('America/Sao_Paulo');

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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}



if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
	$hash = md5(time());
	
	$id_escola 	= $_POST['smc_ativ_corr_id_escola'];
	$id_turma 	= $_POST['smc_ativ_corr_id_turma'];
	$novo_nome 	= md5(time());
	$hora 		= date('Y-m-d H:i:s');
	
include('funcoes/class.upload.php');

$handle = new Upload($_FILES['smc_ativ_corr_caminho']);

if ($handle->uploaded) 
{ 

$handle->file_new_name_body = $novo_nome;
//$handle->file_name_body_add = "_atividade";
$handle->mime_check = false;
$handle->Process('../../atividades/'.$id_escola.'/'.$id_turma.'/');

if ($handle->processed) 
{

$nome_do_arquivo = $handle->file_dst_name;

  $insertSQL = sprintf("INSERT INTO smc_atividade_correcao (smc_ativ_corr_data, smc_ativ_corr_hora, smc_ativ_corr_id_escola, smc_ativ_corr_id_turma, smc_ativ_corr_status, smc_ativ_corr_obs, smc_ativ_corr_hash, smc_ativ_corr_caminho) VALUES (%s, '$hora', %s, %s, %s, %s, '$novo_nome', '$nome_do_arquivo')",
                       GetSQLValueString(inverteData($_POST['smc_ativ_corr_data']), "date"),
                       GetSQLValueString($_POST['smc_ativ_corr_id_escola'], "int"),
                       GetSQLValueString($_POST['smc_ativ_corr_id_turma'], "int"),
                       GetSQLValueString($_POST['smc_ativ_corr_status'], "int"),
                       GetSQLValueString($_POST['smc_ativ_corr_obs'], "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());
  
  $insertGoTo = "atividadesListarCorrecao.php?sucesso";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
} 
else 
{
echo '<span class="alert panel">';
echo ' Erro ao enviar arquivo: ' . $handle->error . '';
echo '</span>';
}
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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ListaEscolas = "SELECT escola_id, escola_nome FROM smc_escola ORDER BY escola_nome ASC";
$ListaEscolas = mysql_query($query_ListaEscolas, $SmecelNovo) or die(mysql_error());
$row_ListaEscolas = mysql_fetch_assoc($ListaEscolas);
$totalRows_ListaEscolas = mysql_num_rows($ListaEscolas);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ListaTurmas = "SELECT turma_id, turma_id_escola, turma_nome, turma_turno FROM smc_turma ORDER BY turma_nome ASC";
$ListaTurmas = mysql_query($query_ListaTurmas, $SmecelNovo) or die(mysql_error());
$row_ListaTurmas = mysql_fetch_assoc($ListaTurmas);
$totalRows_ListaTurmas = mysql_num_rows($ListaTurmas);
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
  <link rel="stylesheet" href="../../css/foundation-datepicker.css">

<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>

<body>

<?php include "menu.php"; ?>

<div class="row">
	<div class="small-12 columns">
	
	<h1>CADASTRAR ATIVIDADE</h1>
	
		<form method="post" enctype="multipart/form-data" name="form1" action="<?php echo $editFormAction; ?>">
		
		<fieldset>

		            <div class="row">
					<div class="small-12 large-2 columns">
					<label class="text-center"><strong>DATA</strong>
					<input type="text" id="smc_ativ_corr_data" name="smc_ativ_corr_data" value="<?php echo date('d/m/Y'); ?>" size="32" class="date text-center" required>
					</label>
					</div>

		            <div class="small-12 large-10 columns">
					<label class="text-center"><strong>ESCOLA</strong>
					<select id="escola" name="smc_ativ_corr_id_escola" required>
						<option value="" select >SELECIONE UMA ESCOLA...</option>
					<?php do { ?>
						<option value="<?php echo $row_ListaEscolas['escola_id']?>" ><?php echo $row_ListaEscolas['escola_nome']?></option>
					<?php } while ($row_ListaEscolas = mysql_fetch_assoc($ListaEscolas)); ?>
					</select>
					</label>
					</div>
					</div>

		            <div class="row">
					
					<div class="small-12 columns">
					<label class="text-center"><strong>TURMA</strong>
					<select id="smc_ativ_corr_id_turma" name="smc_ativ_corr_id_turma" required>
						<option value=""  disabled="disabled">SELECIONE UMA ESCOLA ANTES</option>
					</select>
					</label>
					</div>
					</div>
					<p></p>
		            <div class="row">
					
					</div>
					<p></p>
		            <div class="row">
					
					<div class="small-12 columns">
					<label class="text-center"><strong>OBSERVAÇÕES</strong>
					<input type="text" name="smc_ativ_corr_obs" value="" size="32">
					</label>
					</div>
					
					</div>
					
					<p>
					
					<div class="row">
					<div class="small-12 columns">
					<input type="file" name="smc_ativ_corr_caminho" class="panel" value="" required>
					</div>
					</div>
					
					</p>
					
		            <div class="row">
					<div class="small-12 columns">	
					<input type="submit" value="CADASTRAR" class="button">
					<a href="atividadesListarHoje.php" class="button alert right">CANCELAR</a>
					</div>
					</div>
					
					<input type="hidden" name="MM_insert" value="form1">
					<input type="hidden" id="smc_ativ_corr_status" name="smc_ativ_corr_status" value="0">
		
		</fieldset>
		
		</form>
		
		<p>&nbsp;</p>
	</div>
</div>

<?php include "rodape.php"; ?>

<script src="../../js/vendor/jquery.js"></script>
  <script src="../../js/foundation.min.js"></script>
  <script src="js/foundation/foundation.dropdown.js"></script>
  <script src="../../js/jquery.mask.js"></script>
  
  
    <script src="../../js/foundation-datepicker.js"></script>
  <script src="../../js/foundation-datepicker.pt-br.js"></script>
<!-- ... -->

<script>
$(function(){
	$('#smc_ativ_corr_data').fdatepicker({
		//initialDate: '02/12/1989',
		format: 'dd/mm/yyyy',
		disableDblClickSelection: true,
		language: 'pt-br',
		leftArrow:'<<',
		rightArrow:'>>',
		closeIcon:'X',
		closeButton: false
	});
});
</script>

  
<script>
    $(document).foundation();
  </script>
  
<script type="text/javascript">
	//Popula campo cidades com base na escolha do campo estados
    $(document).ready(function(){
        $('#escola').change(function(){
            $('#smc_ativ_corr_id_turma').load('listar_turmas.php?escola='+$('#escola').val());
			//$("#smc_ativ_id_turma").focus();
        });
    });
    </script> 
<script>
$(document).ready(function(){
  $('.date').mask('00/00/0000');
  $('.time').mask('00:00:00');
  $('.date_time').mask('00/00/0000 00:00:00');
  $('.cep').mask('00000-000');
  $('.phone').mask('0000-0000');
  $('.phone_with_ddd').mask('(00) 0000-0000');
  $('.phone_us').mask('(000) 000-0000');
  $('.mixed').mask('AAA 000-S0S');
  $('.cpf').mask('000.000.000-00', {reverse: true});
  $('.money').mask('000.000.000.000.000,00', {reverse: true});
  $('.money2').mask("#.##0,00", {reverse: true});
  $('.ip_address').mask('099.099.099.099');
  $('.percent').mask('##0,00%', {reverse: true});
  $('.clear-if-not-match').mask("00/00/0000", {clearIfNotMatch: true});
  $('.placeholder').mask("00/00/0000", {placeholder: "__/__/____"});
  $('.placeholdercpf').mask("000.000.000-00", {placeholder: "___.___.___-__"});

$('.celular').focusout(function(){
    var phone, element;
    element = $(this);
    element.unmask();
    phone = element.val().replace(/\D/g, '');
    if(phone.length > 10) {
        element.mask("(99) 99999-9999");
    } else {
        element.mask("(99) 9999-99999");
    }
}).trigger('focusout');

});
</script>

  <script>
jQuery(document).ready(function($) {
 // Chamada da funcao upperText(); ao carregar a pagina
 upperText();
 // Funcao que faz o texto ficar em uppercase
 function upperText() {
// Para tratar o colar
 $("input").bind('paste', function(e) {
 var el = $(this);
 setTimeout(function() {
 var text = $(el).val();
 el.val(text.toUpperCase());
 }, 100);
 });
 
// Para tratar quando é digitado
 $("input").keypress(function() {
 var el = $(this);
 setTimeout(function() {
 var text = $(el).val();
 el.val(text.toUpperCase());
 }, 100);
 });
 }
 });
 </script>
 

</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($ListaEscolas);

mysql_free_result($ListaTurmas);
?>
