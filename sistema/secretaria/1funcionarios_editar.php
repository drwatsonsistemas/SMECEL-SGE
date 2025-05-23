<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php require_once('funcoes/inverteData.php'); ?>
<?php require_once('funcoes/retiraAcentos.php'); ?>
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE smc_func SET func_nome=%s, func_mae=%s, func_pai=%s, func_data_nascimento=%s, func_uf_nascimento=%s, func_municipio_nascimento=%s, func_estado_civil=%s, func_sexo=%s, func_escolaridade=%s, func_cpf=%s, func_rg_numero=%s, func_rg_emissor=%s, func_titulo=%s, func_titulo_secao=%s, func_titulo_zona=%s, func_pis=%s, func_cnh_num=%s, func_categoria=%s, func_ctps=%s, func_ctps_serie=%s, func_reservista=%s, func_endereco=%s, func_endereco_numero=%s, func_endereco_bairro=%s, func_endereco_cep=%s, func_endereco_uf=%s, func_endereco_cidade=%s, func_matricula=%s, func_admissao=%s, func_decreto=%s, func_lotacao=%s, func_cargo=%s, func_regime=%s, func_grupo_sanquineo=%s, func_fator_rh=%s, func_email=%s, func_telefone=%s, func_celular1=%s, func_celular2=%s, func_agencia_banco=%s, func_conta_banco=%s, func_nome_banco=%s, func_area_concurso=%s, func_formacao=%s, func_situacao=%s, func_carga_horaria_semanal=%s, func_vacina_covid19=%s WHERE func_id=%s",
                       GetSQLValueString($_POST['func_nome'], "text"),
                       GetSQLValueString($_POST['func_mae'], "text"),
                       GetSQLValueString($_POST['func_pai'], "text"),
                       GetSQLValueString(inverteData($_POST['func_data_nascimento']), "date"),
                       GetSQLValueString($_POST['func_uf_nascimento'], "text"),
                       GetSQLValueString($_POST['func_municipio_nascimento'], "text"),
                       GetSQLValueString($_POST['func_estado_civil'], "int"),
                       GetSQLValueString($_POST['func_sexo'], "int"),
                       GetSQLValueString($_POST['func_escolaridade'], "int"),
                       GetSQLValueString($_POST['func_cpf'], "text"),
                       GetSQLValueString($_POST['func_rg_numero'], "text"),
                       GetSQLValueString($_POST['func_rg_emissor'], "text"),
                       GetSQLValueString($_POST['func_titulo'], "text"),
                       GetSQLValueString($_POST['func_titulo_secao'], "text"),
                       GetSQLValueString($_POST['func_titulo_zona'], "text"),
                       GetSQLValueString($_POST['func_pis'], "text"),
                       GetSQLValueString($_POST['func_cnh_num'], "text"),
                       GetSQLValueString($_POST['func_categoria'], "text"),
                       GetSQLValueString($_POST['func_ctps'], "text"),
                       GetSQLValueString($_POST['func_ctps_serie'], "text"),
                       GetSQLValueString($_POST['func_reservista'], "text"),
                       GetSQLValueString($_POST['func_endereco'], "text"),
                       GetSQLValueString($_POST['func_endereco_numero'], "text"),
                       GetSQLValueString($_POST['func_endereco_bairro'], "text"),
                       GetSQLValueString($_POST['func_endereco_cep'], "text"),
                       GetSQLValueString($_POST['func_endereco_uf'], "text"),
                       GetSQLValueString($_POST['func_endereco_cidade'], "text"),
                       GetSQLValueString($_POST['func_matricula'], "text"),
                       GetSQLValueString(inverteData($_POST['func_admissao']), "date"),
                       GetSQLValueString($_POST['func_decreto'], "text"),
                       GetSQLValueString($_POST['func_lotacao'], "text"),
                       GetSQLValueString($_POST['func_cargo'], "int"),
                       GetSQLValueString($_POST['func_regime'], "int"),
                       GetSQLValueString($_POST['func_grupo_sanquineo'], "text"),
                       GetSQLValueString($_POST['func_fator_rh'], "text"),
                       GetSQLValueString($_POST['func_email'], "text"),
                       GetSQLValueString($_POST['func_telefone'], "text"),
                       GetSQLValueString($_POST['func_celular1'], "text"),
                       GetSQLValueString($_POST['func_celular2'], "text"),
                       GetSQLValueString($_POST['func_agencia_banco'], "text"),
                       GetSQLValueString($_POST['func_conta_banco'], "text"),
                       GetSQLValueString($_POST['func_nome_banco'], "text"),
                       GetSQLValueString($_POST['func_area_concurso'], "int"),
                       GetSQLValueString($_POST['func_formacao'], "int"),
                       GetSQLValueString($_POST['func_situacao'], "int"),
					   GetSQLValueString($_POST['func_carga_horaria_semanal'], "text"),
					   GetSQLValueString($_POST['func_vacina_covid19'], "text"),
                       GetSQLValueString($_POST['func_id'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

  $updateGoTo = "funcionarios.php?editado";
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

$colname_Funcionarios = "-1";
if (isset($_GET['cod'])) {
  $colname_Funcionarios = $_GET['cod'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Funcionarios = sprintf("
SELECT func_id, func_id_sec, func_nome, func_mae, func_pai, func_data_nascimento, func_uf_nascimento, func_municipio_nascimento, func_estado_civil, func_sexo, func_escolaridade, 
func_cpf, func_rg_numero, func_rg_emissor, func_titulo, func_titulo_secao, func_titulo_zona, func_pis, func_cnh_num, func_categoria, func_ctps, func_ctps_serie, func_reservista, func_endereco, 
func_endereco_numero, func_endereco_bairro, func_endereco_cep, func_endereco_uf, func_endereco_cidade, func_matricula, func_admissao, func_decreto, func_lotacao, func_cargo, func_regime, func_grupo_sanquineo, 
func_fator_rh, func_email, func_telefone, func_celular1, func_celular2, func_agencia_banco, func_conta_banco, func_nome_banco, func_area_concurso, func_formacao, func_situacao, func_foto, func_senha, func_senha_ativa, 
func_carga_horaria_semanal, func_vacina_covid19 FROM smc_func 
WHERE func_id_sec = '$row_Secretaria[sec_id]' AND func_id = %s", GetSQLValueString($colname_Funcionarios, "int"));
$Funcionarios = mysql_query($query_Funcionarios, $SmecelNovo) or die(mysql_error());
$row_Funcionarios = mysql_fetch_assoc($Funcionarios);
$totalRows_Funcionarios = mysql_num_rows($Funcionarios);

if ($totalRows_Funcionarios < 1) {
	$redireciona = "index.php?erro";
	header(sprintf("Location: %s", $redireciona));
	}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Cargo = "SELECT funcao_id, funcao_secretaria_id, funcao_nome, funcao_observacoes, funcao_docencia FROM smc_funcao WHERE funcao_secretaria_id = '$row_Secretaria[sec_id]'";
$Cargo = mysql_query($query_Cargo, $SmecelNovo) or die(mysql_error());
$row_Cargo = mysql_fetch_assoc($Cargo);
$totalRows_Cargo = mysql_num_rows($Cargo);
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
    <h1 class="ls-title-intro ls-ico-home">EDITAR DADOS</h1>
    <div class="ls-box ls-board-box">
      <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form ls-form-horizontal row">
        <fieldset>
		
		
		
		
          <label class="ls-label col-md-4 col-xs-12"> <b class="ls-label-text">Nome completo</b>
            <input type="text" name="func_nome" value="<?php echo htmlentities($row_Funcionarios['func_nome'], ENT_COMPAT, 'utf-8'); ?>" size="32" required>
          </label>
          <label class="ls-label col-md-4 col-xs-12"> <b class="ls-label-text">Nome da mãe</b>
            <input type="text" name="func_mae" value="<?php echo htmlentities($row_Funcionarios['func_mae'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label class="ls-label col-md-4 col-xs-12"> <b class="ls-label-text">Nome do pai</b>
            <input type="text" name="func_pai" value="<?php echo htmlentities($row_Funcionarios['func_pai'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
		  
		  
		  
		  
		  
		  
          <label class="ls-label col-md-2 col-xs-12"> <b class="ls-label-text">Data de nascimento</b>
            <input type="text" name="func_data_nascimento" value="<?php echo htmlentities(inverteData($row_Funcionarios['func_data_nascimento']), ENT_COMPAT, 'utf-8'); ?>" size="32" class="placeholder">
          </label>
          <label class="ls-label col-md-2 col-xs-12">
          <b class="ls-label-text">UF Nascimento</b>
          <div class="ls-custom-select">
            <select name="func_uf_nascimento">
              <option value="-1" <?php if (!(strcmp(-1, ""))) {echo "SELECTED";} ?>>ESCOLHA...</option>
              <option value="AC" <?php if (!(strcmp("AC", htmlentities($row_Funcionarios['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>AC</option>
              <option value="AL" <?php if (!(strcmp("AL", htmlentities($row_Funcionarios['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>AL</option>
              <option value="AP" <?php if (!(strcmp("AP", htmlentities($row_Funcionarios['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>AP</option>
              <option value="AM" <?php if (!(strcmp("AM", htmlentities($row_Funcionarios['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>AM</option>
              <option value="BA" <?php if (!(strcmp("BA", htmlentities($row_Funcionarios['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>BA</option>
              <option value="CE" <?php if (!(strcmp("CE", htmlentities($row_Funcionarios['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>CE</option>
              <option value="DF" <?php if (!(strcmp("DF", htmlentities($row_Funcionarios['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>DF</option>
              <option value="ES" <?php if (!(strcmp("ES", htmlentities($row_Funcionarios['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>ES</option>
              <option value="GO" <?php if (!(strcmp("GO", htmlentities($row_Funcionarios['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>GO</option>
              <option value="MA" <?php if (!(strcmp("MA", htmlentities($row_Funcionarios['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>MA</option>
              <option value="MT" <?php if (!(strcmp("MT", htmlentities($row_Funcionarios['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>MT</option>
              <option value="MS" <?php if (!(strcmp("MS", htmlentities($row_Funcionarios['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>MS</option>
              <option value="MG" <?php if (!(strcmp("MG", htmlentities($row_Funcionarios['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>MG</option>
              <option value="PA" <?php if (!(strcmp("PA", htmlentities($row_Funcionarios['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>PA</option>
              <option value="PB" <?php if (!(strcmp("PB", htmlentities($row_Funcionarios['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>PB</option>
              <option value="PR" <?php if (!(strcmp("PR", htmlentities($row_Funcionarios['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>PR</option>
              <option value="PE" <?php if (!(strcmp("PE", htmlentities($row_Funcionarios['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>PE</option>
              <option value="PI" <?php if (!(strcmp("PI", htmlentities($row_Funcionarios['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>PI</option>
              <option value="RJ" <?php if (!(strcmp("RJ", htmlentities($row_Funcionarios['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>RJ</option>
              <option value="RN" <?php if (!(strcmp("RN", htmlentities($row_Funcionarios['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>RN</option>
              <option value="RS" <?php if (!(strcmp("RS", htmlentities($row_Funcionarios['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>RS</option>
              <option value="RO" <?php if (!(strcmp("RO", htmlentities($row_Funcionarios['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>RO</option>
              <option value="RR" <?php if (!(strcmp("RR", htmlentities($row_Funcionarios['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>RR</option>
              <option value="SC" <?php if (!(strcmp("SC", htmlentities($row_Funcionarios['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>SC</option>
              <option value="SP" <?php if (!(strcmp("SP", htmlentities($row_Funcionarios['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>SP</option>
              <option value="SE" <?php if (!(strcmp("SE", htmlentities($row_Funcionarios['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>SE</option>
              <option value="TO" <?php if (!(strcmp("TO", htmlentities($row_Funcionarios['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>TO</option>
            </select>
          </div>
          </label>
          <label class="ls-label col-md-3 col-xs-12"> <b class="ls-label-text">Município de nascimento</b>
            <input type="text" name="func_municipio_nascimento" value="<?php echo htmlentities($row_Funcionarios['func_municipio_nascimento'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label class="ls-label col-md-2 col-xs-12">
          <b class="ls-label-text">Estado civil</b>
          <div class="ls-custom-select">
            <select name="func_estado_civil">
              <option value="-1" <?php if (!(strcmp(-1, htmlentities($row_Funcionarios['func_estado_civil'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>ESCOLHA...</option>
              <option value="1" <?php if (!(strcmp(1, htmlentities($row_Funcionarios['func_estado_civil'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>1 - SOLTEIRO</option>
              <option value="2" <?php if (!(strcmp(2, htmlentities($row_Funcionarios['func_estado_civil'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>2 - CASADO</option>
              <option value="3" <?php if (!(strcmp(3, htmlentities($row_Funcionarios['func_estado_civil'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>3 - VIUVO</option>
              <option value="4" <?php if (!(strcmp(4, htmlentities($row_Funcionarios['func_estado_civil'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>4 - UNIÃO ESTÁVEL</option>
              <option value="5" <?php if (!(strcmp(5, htmlentities($row_Funcionarios['func_estado_civil'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>5 - OUTROS</option>
            </select>
          </div>
          </label>
          <label class="ls-label col-md-3 col-xs-12">
          <b class="ls-label-text">Sexo</b>
          <div class="ls-custom-select">
            <select name="func_sexo">
              <option value="-1" <?php if (!(strcmp(-1, htmlentities($row_Funcionarios['func_sexo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>ESCOLHA...</option>
              <option value="1" <?php if (!(strcmp(1, htmlentities($row_Funcionarios['func_sexo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>1 - MASCULINO</option>
              <option value="2" <?php if (!(strcmp(2, htmlentities($row_Funcionarios['func_sexo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>2 - FEMININO</option>
            </select>
          </div>
          </label>

          <label class="ls-label col-md-6 col-xs-12"> <b class="ls-label-text">CPF</b>
            <input type="text" name="func_cpf" value="<?php echo htmlentities($row_Funcionarios['func_cpf'], ENT_COMPAT, 'utf-8'); ?>" size="32" id="cpf" onblur="javascript: validarCPF(this);" onkeypress="javascript: mascara(this, cpf_mask);" class="cpf">
          </label>
          <label class="ls-label col-md-2 col-xs-12"> <b class="ls-label-text">RG</b>
            <input type="text" name="func_rg_numero" value="<?php echo htmlentities($row_Funcionarios['func_rg_numero'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label class="ls-label col-md-2 col-xs-12"> <b class="ls-label-text">Órgão Emissor</b>
            <input type="text" name="func_rg_emissor" value="<?php echo htmlentities($row_Funcionarios['func_rg_emissor'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label class="ls-label col-md-2 col-xs-12"> <b class="ls-label-text">Título de Eleitor</b>
            <input type="text" name="func_titulo" value="<?php echo htmlentities($row_Funcionarios['func_titulo'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
		  
          <label class="ls-label col-md-2 col-xs-12"> <b class="ls-label-text">Seção</b>
            <input type="text" name="func_titulo_secao" value="<?php echo htmlentities($row_Funcionarios['func_titulo_secao'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label class="ls-label col-md-3 col-xs-12"> <b class="ls-label-text">Zona</b>
            <input type="text" name="func_titulo_zona" value="<?php echo htmlentities($row_Funcionarios['func_titulo_zona'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
		  
		  
		            <label class="ls-label col-md-3 col-xs-12">
          <b class="ls-label-text">Escolaridade</b>
          <div class="ls-custom-select">
            <select name="func_escolaridade">
              <option value="-1" <?php if (!(strcmp(-1, htmlentities($row_Funcionarios['func_escolaridade'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>ESCOLHA...</option>
              <option value="1" <?php if (!(strcmp(1, htmlentities($row_Funcionarios['func_escolaridade'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>1 - ENSINO FUNDAMENTAL</option>
              <option value="2" <?php if (!(strcmp(2, htmlentities($row_Funcionarios['func_escolaridade'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>2 - ENSINO MÉDIO</option>
              <option value="3" <?php if (!(strcmp(3, htmlentities($row_Funcionarios['func_escolaridade'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>3 - GRADUAÇAO</option>
              <option value="4" <?php if (!(strcmp(4, htmlentities($row_Funcionarios['func_escolaridade'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>4 - PÓS-GRADUAÇÃO</option>
              <option value="5" <?php if (!(strcmp(5, htmlentities($row_Funcionarios['func_escolaridade'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>5 - MESTRADO</option>
            </select>
          </div>
          </label>
		  
          <label class="ls-label col-md-4 col-xs-12"> <b class="ls-label-text">PIS</b>
            <input type="text" name="func_pis" value="<?php echo htmlentities($row_Funcionarios['func_pis'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label class="ls-label col-md-4 col-xs-12"> <b class="ls-label-text">CNH</b>
            <input type="text" name="func_cnh_num" value="<?php echo htmlentities($row_Funcionarios['func_cnh_num'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label class="ls-label col-md-4 col-xs-12">
          <b class="ls-label-text">Categoria CNH</b>
          <div class="ls-custom-select">
            <select name="func_categoria">
              <option value="-1" <?php if (!(strcmp("-1", htmlentities($row_Funcionarios['func_categoria'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>ESCOLHA...</option>
              <option value="A" <?php if (!(strcmp("A", htmlentities($row_Funcionarios['func_categoria'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>A</option>
              <option value="B" <?php if (!(strcmp("B", htmlentities($row_Funcionarios['func_categoria'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>B</option>
              <option value="AB" <?php if (!(strcmp("AB", htmlentities($row_Funcionarios['func_categoria'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>AB</option>
              <option value="C" <?php if (!(strcmp("C", htmlentities($row_Funcionarios['func_categoria'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>C</option>
              <option value="D" <?php if (!(strcmp("D", htmlentities($row_Funcionarios['func_categoria'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>D</option>
              <option value="E" <?php if (!(strcmp("E", htmlentities($row_Funcionarios['func_categoria'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>E</option>
            </select>
          </div>
          </label>
          <label class="ls-label col-md-2 col-xs-12"> <b class="ls-label-text">CTPS</b>
            <input type="text" name="func_ctps" value="<?php echo htmlentities($row_Funcionarios['func_ctps'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label class="ls-label col-md-2 col-xs-12"> <b class="ls-label-text">Série CTPS</b>
            <input type="text" name="func_ctps_serie" value="<?php echo htmlentities($row_Funcionarios['func_ctps_serie'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label class="ls-label col-md-4 col-xs-12"> <b class="ls-label-text">Reservista</b>
            <input type="text" name="func_reservista" value="<?php echo htmlentities($row_Funcionarios['func_reservista'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label class="ls-label col-md-8 col-xs-12"> <b class="ls-label-text">Endereço</b>
            <input type="text" name="func_endereco" value="<?php echo htmlentities($row_Funcionarios['func_endereco'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
		  
		  
		  
          <label class="ls-label col-md-2 col-xs-12"> <b class="ls-label-text">Número</b>
            <input type="text" name="func_endereco_numero" value="<?php echo htmlentities($row_Funcionarios['func_endereco_numero'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label class="ls-label col-md-3 col-xs-12"> <b class="ls-label-text">Bairro</b>
            <input type="text" name="func_endereco_bairro" value="<?php echo htmlentities($row_Funcionarios['func_endereco_bairro'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label class="ls-label col-md-4 col-xs-12"> <b class="ls-label-text">CEP</b>
            <input type="text" name="func_endereco_cep" value="<?php echo htmlentities($row_Funcionarios['func_endereco_cep'], ENT_COMPAT, 'utf-8'); ?>" size="32" class="cep">
          </label>
          <label class="ls-label col-md-3 col-xs-12">
          <b class="ls-label-text">UF Residência</b>
          <div class="ls-custom-select">
            <select name="func_endereco_uf">
              <option value="-1" <?php if (!(strcmp(-1, ""))) {echo "SELECTED";} ?>>ESCOLHA...</option>
              <option value="AC" <?php if (!(strcmp("AC", htmlentities($row_Funcionarios['func_endereco_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>AC</option>
              <option value="AL" <?php if (!(strcmp("AL", htmlentities($row_Funcionarios['func_endereco_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>AL</option>
              <option value="AP" <?php if (!(strcmp("AP", htmlentities($row_Funcionarios['func_endereco_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>AP</option>
              <option value="AM" <?php if (!(strcmp("AM", htmlentities($row_Funcionarios['func_endereco_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>AM</option>
              <option value="BA" <?php if (!(strcmp("BA", htmlentities($row_Funcionarios['func_endereco_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>BA</option>
              <option value="CE" <?php if (!(strcmp("CE", htmlentities($row_Funcionarios['func_endereco_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>CE</option>
              <option value="DF" <?php if (!(strcmp("DF", htmlentities($row_Funcionarios['func_endereco_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>DF</option>
              <option value="ES" <?php if (!(strcmp("ES", htmlentities($row_Funcionarios['func_endereco_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>ES</option>
              <option value="GO" <?php if (!(strcmp("GO", htmlentities($row_Funcionarios['func_endereco_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>GO</option>
              <option value="MA" <?php if (!(strcmp("MA", htmlentities($row_Funcionarios['func_endereco_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>MA</option>
              <option value="MT" <?php if (!(strcmp("MT", htmlentities($row_Funcionarios['func_endereco_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>MT</option>
              <option value="MS" <?php if (!(strcmp("MS", htmlentities($row_Funcionarios['func_endereco_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>MS</option>
              <option value="MG" <?php if (!(strcmp("MG", htmlentities($row_Funcionarios['func_endereco_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>MG</option>
              <option value="PA" <?php if (!(strcmp("PA", htmlentities($row_Funcionarios['func_endereco_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>PA</option>
              <option value="PB" <?php if (!(strcmp("PB", htmlentities($row_Funcionarios['func_endereco_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>PB</option>
              <option value="PR" <?php if (!(strcmp("PR", htmlentities($row_Funcionarios['func_endereco_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>PR</option>
              <option value="PE" <?php if (!(strcmp("PE", htmlentities($row_Funcionarios['func_endereco_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>PE</option>
              <option value="PI" <?php if (!(strcmp("PI", htmlentities($row_Funcionarios['func_endereco_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>PI</option>
              <option value="RJ" <?php if (!(strcmp("RJ", htmlentities($row_Funcionarios['func_endereco_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>RJ</option>
              <option value="RN" <?php if (!(strcmp("RN", htmlentities($row_Funcionarios['func_endereco_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>RN</option>
              <option value="RS" <?php if (!(strcmp("RS", htmlentities($row_Funcionarios['func_endereco_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>RS</option>
              <option value="RO" <?php if (!(strcmp("RO", htmlentities($row_Funcionarios['func_endereco_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>RO</option>
              <option value="RR" <?php if (!(strcmp("RR", htmlentities($row_Funcionarios['func_endereco_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>RR</option>
              <option value="SC" <?php if (!(strcmp("SC", htmlentities($row_Funcionarios['func_endereco_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>SC</option>
              <option value="SP" <?php if (!(strcmp("SP", htmlentities($row_Funcionarios['func_endereco_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>SP</option>
              <option value="SE" <?php if (!(strcmp("SE", htmlentities($row_Funcionarios['func_endereco_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>SE</option>
              <option value="TO" <?php if (!(strcmp("TO", htmlentities($row_Funcionarios['func_endereco_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>TO</option>
            </select>
          </div>
          </label>
          <label class="ls-label col-md-4 col-xs-12"> <b class="ls-label-text">Cidade</b>
            <input type="text" name="func_endereco_cidade" value="<?php echo htmlentities($row_Funcionarios['func_endereco_cidade'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label class="ls-label col-md-4 col-xs-12"> <b class="ls-label-text">Matrícula</b>
            <input type="text" name="func_matricula" value="<?php echo htmlentities($row_Funcionarios['func_matricula'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label class="ls-label col-md-4 col-xs-12"> <b class="ls-label-text">Admissão</b>
            <input type="text" name="func_admissao" value="<?php echo htmlentities(inverteData($row_Funcionarios['func_admissao']), ENT_COMPAT, 'utf-8'); ?>" size="32" class="placeholder">
          </label>
          <label class="ls-label col-md-4 col-xs-12"> <b class="ls-label-text">Decreto Nº</b>
            <input type="text" name="func_decreto" value="<?php echo htmlentities($row_Funcionarios['func_decreto'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label class="ls-label col-md-4 col-xs-12"> <b class="ls-label-text">Local de lotação</b>
            <input type="text" name="func_lotacao" value="<?php echo htmlentities($row_Funcionarios['func_lotacao'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label class="ls-label col-md-4 col-xs-12">
          <b class="ls-label-text">Função / Cargo</b>
          <div class="ls-custom-select">
            <select name="func_cargo" required>
              <option value="">Escolha...</option>
              <?php do {  ?>
              <option value="<?php echo $row_Cargo['funcao_id']?>" <?php if (!(strcmp($row_Cargo['funcao_id'], htmlentities($row_Funcionarios['func_cargo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>><?php echo $row_Cargo['funcao_nome']?></option>
              <?php } while ($row_Cargo = mysql_fetch_assoc($Cargo)); ?>
            </select>
          </div>
          </label>
		  
          <label class="ls-label col-md-6 col-xs-12">
          <b class="ls-label-text">Regime</b>
          <div class="ls-custom-select">
            <select name="func_regime" required>
              <option value="" <?php if (!(strcmp(-1, htmlentities($row_Funcionarios['func_regime'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>ESCOLHA...</option>
              <option value="1" <?php if (!(strcmp(1, htmlentities($row_Funcionarios['func_regime'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>1 - EFETIVO</option>
              <option value="2" <?php if (!(strcmp(2, htmlentities($row_Funcionarios['func_regime'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>2 - TEMPORÁRIO</option>
              <option value="3" <?php if (!(strcmp(3, htmlentities($row_Funcionarios['func_regime'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>3 - COMISSIONADO</option>
              <option value="4" <?php if (!(strcmp(4, htmlentities($row_Funcionarios['func_regime'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>4 - NOMEADO</option>
              <option value="5" <?php if (!(strcmp(5, htmlentities($row_Funcionarios['func_regime'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>5 - TERCEIRIZADO</option>
            </select>
          </div>
          </label>
          <label class="ls-label col-md-6 col-xs-12">
          <b class="ls-label-text">Grupo sanguíneo</b>
          <div class="ls-custom-select">
            <select name="func_grupo_sanquineo">
              <option value="-1" <?php if (!(strcmp(-1, htmlentities($row_Funcionarios['func_grupo_sanquineo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>ESCOLHA...</option>
              <option value="A" <?php if (!(strcmp("A", htmlentities($row_Funcionarios['func_grupo_sanquineo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>A</option>
              <option value="B" <?php if (!(strcmp("B", htmlentities($row_Funcionarios['func_grupo_sanquineo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>B</option>
              <option value="AB" <?php if (!(strcmp("AB", htmlentities($row_Funcionarios['func_grupo_sanquineo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>AB</option>
              <option value="O" <?php if (!(strcmp("O", htmlentities($row_Funcionarios['func_grupo_sanquineo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>O</option>
            </select>
          </div>
          </label>
          <label class="ls-label col-md-6 col-xs-12">
          <b class="ls-label-text">Fator RH</b>
          <div class="ls-custom-select">
            <select name="func_fator_rh">
              <option value="-1" <?php if (!(strcmp(-1, htmlentities($row_Funcionarios['func_fator_rh'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>ESCOLHA...</option>
              <option value="+" <?php if (!(strcmp("+", htmlentities($row_Funcionarios['func_fator_rh'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>POSITIVO (+)</option>
              <option value="-" <?php if (!(strcmp("-", htmlentities($row_Funcionarios['func_fator_rh'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>NEGATIVO (-)</option>
            </select>
          </div>
          </label>
          <label class="ls-label col-md-3 col-xs-12"> <b class="ls-label-text">E-mail</b>
            <input type="email" name="func_email" value="<?php echo htmlentities($row_Funcionarios['func_email'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label class="ls-label col-md-3 col-xs-12"> <b class="ls-label-text">Telefone</b>
            <input type="text" name="func_telefone" value="<?php echo htmlentities($row_Funcionarios['func_telefone'], ENT_COMPAT, 'utf-8'); ?>" size="32" class="phone_with_ddd">
          </label>
          <label class="ls-label col-md-3 col-xs-12"> <b class="ls-label-text">Celular</b>
            <input type="text" name="func_celular1" value="<?php echo htmlentities($row_Funcionarios['func_celular1'], ENT_COMPAT, 'utf-8'); ?>" size="32" class="celular">
          </label>
          <label class="ls-label col-md-3 col-xs-12"> <b class="ls-label-text">Celular</b>
            <input type="text" name="func_celular2" value="<?php echo htmlentities($row_Funcionarios['func_celular2'], ENT_COMPAT, 'utf-8'); ?>" size="32" class="celular">
          </label>
          <label class="ls-label col-md-3 col-xs-12"> <b class="ls-label-text">Agência Bancária</b>
            <input type="text" name="func_agencia_banco" value="<?php echo htmlentities($row_Funcionarios['func_agencia_banco'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label class="ls-label col-md-3 col-xs-12"> <b class="ls-label-text">Conta Bancária</b>
            <input type="text" name="func_conta_banco" value="<?php echo htmlentities($row_Funcionarios['func_conta_banco'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label class="ls-label col-md-6 col-xs-12"> <b class="ls-label-text">Banco</b>
            <input type="text" name="func_nome_banco" value="<?php echo htmlentities($row_Funcionarios['func_nome_banco'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label class="ls-label col-md-6 col-xs-12">
          <b class="ls-label-text">Carga horária semanal</b>
          <div class="ls-custom-select">
            <select name="func_carga_horaria_semanal">
              <option value="-1" <?php if (!(strcmp(-1, htmlentities($row_Funcionarios['func_carga_horaria_semanal'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>ESCOLHA...</option>
              <option value="20" <?php if (!(strcmp("20", htmlentities($row_Funcionarios['func_carga_horaria_semanal'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>20h</option>
              <option value="30" <?php if (!(strcmp("30", htmlentities($row_Funcionarios['func_carga_horaria_semanal'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>30h</option>
              <option value="40" <?php if (!(strcmp("40", htmlentities($row_Funcionarios['func_carga_horaria_semanal'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>40h</option>
            </select>
          </div>
          </label>
          <label class="ls-label col-md-12 col-xs-12">
          <b class="ls-label-text">Área do Concurso (Docente)</b>
          <div class="ls-custom-select">
            <select name="func_area_concurso">
              <option value="-1" <?php if (!(strcmp(-1, htmlentities($row_Funcionarios['func_area_concurso'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>ESCOLHA...</option>
              <option value="1" <?php if (!(strcmp(1, htmlentities($row_Funcionarios['func_area_concurso'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>1 - FUNDAMENTAL I</option>
              <option value="2" <?php if (!(strcmp(2, htmlentities($row_Funcionarios['func_area_concurso'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>2 - FUNDAMENTAL II</option>
              <option value="3" <?php if (!(strcmp(3, htmlentities($row_Funcionarios['func_area_concurso'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>3 - EDUCAÇÃO INFANTIL</option>
            </select>
          </div>
          </label>
          <label class="ls-label col-md-12 col-xs-12">
          <b class="ls-label-text">Situação</b>
          <div class="ls-custom-select">
            <select name="func_situacao" required>
              <option value="" <?php if (!(strcmp(-1, htmlentities($row_Funcionarios['func_situacao'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>ESCOLHA...</option>
              <option value="1" <?php if (!(strcmp(1, htmlentities($row_Funcionarios['func_situacao'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>1 - EM ATIVIDADE</option>
              <option value="2" <?php if (!(strcmp(2, htmlentities($row_Funcionarios['func_situacao'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>2 - AFASTADO</option>
              <option value="3" <?php if (!(strcmp(3, htmlentities($row_Funcionarios['func_situacao'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>3 - APOSENTADO</option>
            </select>
          </div>
          </label>
		  
		  <label class="ls-label col-md-12 col-xs-12">
          <b class="ls-label-text">Situação de vacinação (COVID19)</b>
          <div class="ls-custom-select">
            <select name="func_vacina_covid19" required>
              <option value="" <?php if (!(strcmp(-1, htmlentities($row_Funcionarios['func_vacina_covid19'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>ESCOLHA...</option>
              <option value="0" <?php if (!(strcmp(0, htmlentities($row_Funcionarios['func_vacina_covid19'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>NENHUMA DOSE</option>
              <option value="1" <?php if (!(strcmp(1, htmlentities($row_Funcionarios['func_vacina_covid19'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>1ª DOSE</option>
              <option value="2" <?php if (!(strcmp(2, htmlentities($row_Funcionarios['func_vacina_covid19'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>2ª DOSE</option>
              <option value="U" <?php if (!(strcmp("U", htmlentities($row_Funcionarios['func_vacina_covid19'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>DOSE ÚNICA</option>
            </select>
          </div>
          </label>

		  
          <div class="ls-actions-btn">
            <input type="submit" class="ls-btn" value="Salvar">
            <a href="funcionarios.php" class="ls-btn-danger">Cancelar</a> </div>
          <input type="hidden" name="MM_update" value="form1">
          <input type="hidden" name="func_id" value="<?php echo $row_Funcionarios['func_id']; ?>">
        </fieldset>
      </form>
      <p>&nbsp;</p>
    </div>
  </div>
</main>
<?php include_once "notificacoes.php"; ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script> 
<script src="../js/jquery.mask.js"></script> 
<script src="js/mascara.js"></script> 
<script src="js/maiuscula.js"></script> 
<script src="js/semAcentos.js"></script> 
<script src="js/validarCPF.js"></script>
</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

mysql_free_result($Funcionarios);

mysql_free_result($Cargo);
?>