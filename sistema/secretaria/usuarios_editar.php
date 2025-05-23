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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

  $email = strtoupper($_POST['usu_email']);

  $updateSQL = sprintf("UPDATE smc_usu SET usu_nome=%s, usu_email=%s, usu_tipo=%s, usu_sec=%s, usu_escola=%s, usu_status=%s, usu_nota_aluno_escola=%s, usu_insert=%s, usu_update=%s, usu_delete=%s, usu_m_ava=%s, usu_m_administrativo=%s, usu_m_formacao=%s, usu_m_transporte=%s, usu_m_merenda=%s, usu_m_patrimonio=%s, usu_m_relatorios=%s, usu_m_graficos=%s, usu_m_configuracoes=%s WHERE usu_id=%s",
                       GetSQLValueString($_POST['usu_nome'], "text"),
                       GetSQLValueString($email, "text"),
                       //GetSQLValueString($_POST['usu_senha'], "text"),
                       GetSQLValueString($_POST['usu_tipo'], "int"),
                       GetSQLValueString($_POST['usu_sec'], "int"),
                       GetSQLValueString($_POST['usu_escola'], "int"),
                       GetSQLValueString($_POST['usu_status'], "int"),
					   GetSQLValueString(isset($_POST['usu_nota_aluno_escola']) ? "true" : "", "defined","'S'","'N'"),
					   GetSQLValueString(isset($_POST['usu_insert']) ? "true" : "", "defined","'S'","'N'"),
					   GetSQLValueString(isset($_POST['usu_update']) ? "true" : "", "defined","'S'","'N'"),
					   GetSQLValueString(isset($_POST['usu_delete']) ? "true" : "", "defined","'S'","'N'"),
					   GetSQLValueString(isset($_POST['usu_m_ava']) ? "true" : "", "defined","'S'","'N'"),
					   GetSQLValueString(isset($_POST['usu_m_administrativo']) ? "true" : "", "defined","'S'","'N'"),
					   GetSQLValueString(isset($_POST['usu_m_formacao']) ? "true" : "", "defined","'S'","'N'"),
					   GetSQLValueString(isset($_POST['usu_m_transporte']) ? "true" : "", "defined","'S'","'N'"),
					   GetSQLValueString(isset($_POST['usu_m_merenda']) ? "true" : "", "defined","'S'","'N'"),
					   GetSQLValueString(isset($_POST['usu_m_patrimonio']) ? "true" : "", "defined","'S'","'N'"),
					   GetSQLValueString(isset($_POST['usu_m_relatorios']) ? "true" : "", "defined","'S'","'N'"),
					   GetSQLValueString(isset($_POST['usu_m_graficos']) ? "true" : "", "defined","'S'","'N'"),
					   GetSQLValueString(isset($_POST['usu_m_configuracoes']) ? "true" : "", "defined","'S'","'N'"),
                       GetSQLValueString($_POST['usu_id'], "int"));
					   
					   


  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

  $updateGoTo = "usuarios.php?editado";
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

$colname_Usuarios = "-1";
if (isset($_GET['codigo'])) {
  $colname_Usuarios = $_GET['codigo'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Usuarios = sprintf("
SELECT usu_id, usu_nome, usu_email, usu_senha, usu_tipo, usu_sec, usu_escola, usu_status, usu_cadastro, usu_insert, usu_update, usu_delete,
usu_m_ava, usu_m_administrativo, usu_m_formacao, usu_m_transporte, usu_m_merenda, usu_m_patrimonio, usu_m_relatorios, usu_m_graficos, usu_m_configuracoes, usu_nota_aluno_escola
FROM smc_usu WHERE usu_sec = '$row_Secretaria[sec_id]' AND usu_id = %s", GetSQLValueString($colname_Usuarios, "int"));
$Usuarios = mysql_query($query_Usuarios, $SmecelNovo) or die(mysql_error());
$row_Usuarios = mysql_fetch_assoc($Usuarios);
$totalRows_Usuarios = mysql_num_rows($Usuarios);

if ($totalRows_Usuarios < 1) {
	$redireciona = "index.php?erro";
	header(sprintf("Location: %s", $redireciona));
	}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Escolas = "SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_ue, escola_situacao FROM smc_escola WHERE escola_id_sec = '$row_Secretaria[sec_id]' ORDER BY escola_nome ASC";
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
    <h1 class="ls-title-intro ls-ico-home">EDITAR USUÁRIO</h1>
    <div class="ls-box ls-board-box">
      <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form-horizontal">
        <label class="ls-label col-md-12">
        <b class="ls-label-text">NOME DO USUÁRIO</b>
        <p class="ls-label-info">Informe o nome completo</p>
        <input type="text" name="usu_nome" value="<?php echo htmlentities($row_Usuarios['usu_nome'], ENT_COMPAT, 'utf-8'); ?>" size="32" required>
        </label>
        <label class="ls-label col-md-6">
        <b class="ls-label-text">E-MAIL</b>
        <p class="ls-label-info">Informe o e-mail que será utilizado como login</p>
        <input type="email" name="usu_email" value="<?php echo htmlentities($row_Usuarios['usu_email'], ENT_COMPAT, 'utf-8'); ?>" size="32" required>
        </label>
        <label class="ls-label col-md-6">
        <b class="ls-label-text">SENHA</b>
        <p class="ls-label-info">Informe a senha do usuário</p>
        <div class="ls-prefix-group">
          <input type="password" id="password_field" name="usu_senha" value="" size="32" disabled>
          <a class="ls-label-text-prefix ls-toggle-pass ls-ico-eye" data-toggle-class="ls-ico-eye, ls-ico-eye-blocked" data-target="#password_field" href="#"> </a> </div>
        </label>
        <label class="ls-label col-md-12">
        <b class="ls-label-text">TIPO DE USUÁRIO</b>
        <p class="ls-label-info">Informe o tipo de acesso do usuário</p>
        <div class="ls-custom-select">
          <select name="usu_tipo" required>
            <option value="">ESCOLHA...</option>
            <option value="1" <?php if (!(strcmp(1, htmlentities($row_Usuarios['usu_tipo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>SECRETARIA MUNICIPAL DE EDUCAÇÃO</option>
            <option value="2" <?php if (!(strcmp(2, htmlentities($row_Usuarios['usu_tipo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>USUÁRIO ESCOLAR/SETOR</option>
            <option value="4" <?php if (!(strcmp(4, htmlentities($row_Usuarios['usu_tipo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>PSE - PROGRAMA SAÚDE NA ESCOLA</option>
            <option value="5" <?php if (!(strcmp(5, htmlentities($row_Usuarios['usu_tipo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>PORTARIA</option>
          </select>
        </div>
        </label>
        <label class="ls-label col-md-12">
        <b class="ls-label-text">LOCAL</b>
        <p class="ls-label-info">Informe a Unidade Escolar ou setor</p>
        <div class="ls-custom-select">
          <select name="usu_escola" required>
            <option value="" >ESCOLHA...</option>
            <option value="0" <?php if (!(strcmp(0, htmlentities($row_Usuarios['usu_escola'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>SECRETARIA DE EDUCAÇÃO</option>
            <?php if ($totalRows_Escolas > 0) { // Show if recordset not empty ?>
            <?php do {  ?>
            <option value="<?php echo $row_Escolas['escola_id']?>" <?php if (!(strcmp($row_Escolas['escola_id'], htmlentities($row_Usuarios['usu_escola'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?> ><?php echo $row_Escolas['escola_inep']?> - <?php echo $row_Escolas['escola_nome']?></option>
            <?php } while ($row_Escolas = mysql_fetch_assoc($Escolas)); ?>
		    <?php } // Show if recordset not empty ?>
          </select>
        </div>
        </label>
        <label class="ls-label col-md-12">
        <b class="ls-label-text">STATUS</b>
        <p class="ls-label-info">Informe o status do usuário</p>
        <div class="ls-custom-select">
          <select name="usu_status">
            <option value="1" <?php if (!(strcmp(1, htmlentities($row_Usuarios['usu_status'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>1 - ATIVO</option>
            <option value="2" <?php if (!(strcmp(2, htmlentities($row_Usuarios['usu_status'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>2 - INATIVO</option>
          </select>
        </div>
        </label>
		
		<div class="">

		
		  <label class="ls-label col-md-4 col-sm-12">
          <b class="ls-label-text">INSERIR</b>
          <br><p class="ls-label-info">Permite cadastros
          <div data-ls-module="switchButton" class="ls-switch-btn">
          <input type="checkbox" name="usu_insert" id="usu_insert" value=""  <?php if (!(strcmp(htmlentities($row_Usuarios['usu_insert'], ENT_COMPAT, 'utf-8'),"S"))) {echo "checked=\"checked\"";} ?>>
          <label class="ls-switch-label" for="usu_insert" name="label-teste" ls-switch-off="Não permite" ls-switch-on="Permite"><span></span></label>
          </div>
          </p>
		  </label>
		  
		  <label class="ls-label col-md-4 col-sm-12">
          <b class="ls-label-text">EDITAR</b>
          <br><p class="ls-label-info">Permite atualizações
          <div data-ls-module="switchButton" class="ls-switch-btn">
          <input type="checkbox" name="usu_update" id="usu_update" value=""  <?php if (!(strcmp(htmlentities($row_Usuarios['usu_update'], ENT_COMPAT, 'utf-8'),"S"))) {echo "checked=\"checked\"";} ?>>
          <label class="ls-switch-label" for="usu_update" name="label-teste" ls-switch-off="Não permite" ls-switch-on="Permite"><span></span></label>
          </div>
          </p>
		  </label>
		  
		  <label class="ls-label col-md-4 com-sm-12">
          <b class="ls-label-text">EXCLUIR</b>
          <br><p class="ls-label-info">Permite exclusões
          <div data-ls-module="switchButton" class="ls-switch-btn">
          <input type="checkbox" name="usu_delete" id="usu_delete" value=""  <?php if (!(strcmp(htmlentities($row_Usuarios['usu_delete'], ENT_COMPAT, 'utf-8'),"S"))) {echo "checked=\"checked\"";} ?>>
          <label class="ls-switch-label" for="usu_delete" name="label-teste" ls-switch-off="Não permite" ls-switch-on="Permite"><span></span></label>
          </div>
          </p>
		  </label>
		  
		  </div>
		  
          <label class="ls-label col-md-12">
		  <hr>
		  <h3>Outras permissões</h3>
		  </hr>
		  </label>

		  
		  <label class="ls-label col-md-4 com-sm-12">
          <b class="ls-label-text">NOTAS</b>
          <br><p class="ls-label-info">Permite alterar notas dos alunos no painel da escola?
          <div data-ls-module="switchButton" class="ls-switch-btn">
          <input type="checkbox" name="usu_nota_aluno_escola" id="usu_nota_aluno_escola" value=""  <?php if (!(strcmp(htmlentities($row_Usuarios['usu_nota_aluno_escola'], ENT_COMPAT, 'utf-8'),"S"))) {echo "checked=\"checked\"";} ?>>
          <label class="ls-switch-label" for="usu_nota_aluno_escola" name="usu_nota_aluno_escola" ls-switch-off="Não permite" ls-switch-on="Permite"><span></span></label>
          </div>
          </p>
		  </label>
		  
		  
		  <label class="ls-label col-md-12">
		  <hr>
		  <h3>Acesso aos módulos</h3>
		  </hr>
		  </label>
		  


		  <label class="ls-label col-md-4 col-sm-12">
          <b class="ls-label-text">AVA</b>
          <br><p class="ls-label-info">
          <div data-ls-module="switchButton" class="ls-switch-btn">
          <input type="checkbox" name="usu_m_ava" id="usu_m_ava" value=""  <?php if (!(strcmp(htmlentities($row_Usuarios['usu_m_ava'], ENT_COMPAT, 'utf-8'),"S"))) {echo "checked=\"checked\"";} ?>>
          <label class="ls-switch-label" for="usu_m_ava" name="label-teste" ls-switch-off="NÃO" ls-switch-on="SIM"><span></span></label>
          </div>
          </p>
		  </label>

		  <label class="ls-label col-md-4 col-sm-12">
          <b class="ls-label-text">ADM</b>
          <br><p class="ls-label-info">
          <div data-ls-module="switchButton" class="ls-switch-btn">
          <input type="checkbox" name="usu_m_administrativo" id="usu_m_administrativo" value=""  <?php if (!(strcmp(htmlentities($row_Usuarios['usu_m_administrativo'], ENT_COMPAT, 'utf-8'),"S"))) {echo "checked=\"checked\"";} ?>>
          <label class="ls-switch-label" for="usu_m_administrativo" name="label-teste" ls-switch-off="NÃO" ls-switch-on="SIM"><span></span></label>
          </div>
          </p>
		  </label>

		  <label class="ls-label col-md-4 col-sm-12">
          <b class="ls-label-text">CURSOS</b>
          <br><p class="ls-label-info">
          <div data-ls-module="switchButton" class="ls-switch-btn">
          <input type="checkbox" name="usu_m_formacao" id="usu_m_formacao" value=""  <?php if (!(strcmp(htmlentities($row_Usuarios['usu_m_formacao'], ENT_COMPAT, 'utf-8'),"S"))) {echo "checked=\"checked\"";} ?>>
          <label class="ls-switch-label" for="usu_m_formacao" name="label-teste" ls-switch-off="NÃO" ls-switch-on="SIM"><span></span></label>
          </div>
          </p>
		  </label>

		  

		  <label class="ls-label col-md-4 col-sm-12">
          <b class="ls-label-text">TRANSPORTE</b>
          <br><p class="ls-label-info">
          <div data-ls-module="switchButton" class="ls-switch-btn">
          <input type="checkbox" name="usu_m_transporte" id="usu_m_transporte" value=""  <?php if (!(strcmp(htmlentities($row_Usuarios['usu_m_transporte'], ENT_COMPAT, 'utf-8'),"S"))) {echo "checked=\"checked\"";} ?>>
          <label class="ls-switch-label" for="usu_m_transporte" name="label-teste" ls-switch-off="NÃO" ls-switch-on="SIM"><span></span></label>
          </div>
          </p>
		  </label>

		  <label class="ls-label col-md-4 col-sm-12">
          <b class="ls-label-text">MERENDA</b>
          <br><p class="ls-label-info">
          <div data-ls-module="switchButton" class="ls-switch-btn">
          <input type="checkbox" name="usu_m_merenda" id="usu_m_merenda" value=""  <?php if (!(strcmp(htmlentities($row_Usuarios['usu_m_merenda'], ENT_COMPAT, 'utf-8'),"S"))) {echo "checked=\"checked\"";} ?>>
          <label class="ls-switch-label" for="usu_m_merenda" name="label-teste" ls-switch-off="NÃO" ls-switch-on="SIM"><span></span></label>
          </div>
          </p>
		  </label>

		  <label class="ls-label col-md-4 col-sm-12">
          <b class="ls-label-text">PATRIMÔNIO</b>
          <br><p class="ls-label-info">
          <div data-ls-module="switchButton" class="ls-switch-btn">
          <input type="checkbox" name="usu_m_patrimonio" id="usu_m_patrimonio" value=""  <?php if (!(strcmp(htmlentities($row_Usuarios['usu_m_patrimonio'], ENT_COMPAT, 'utf-8'),"S"))) {echo "checked=\"checked\"";} ?>>
          <label class="ls-switch-label" for="usu_m_patrimonio" name="label-teste" ls-switch-off="NÃO" ls-switch-on="SIM"><span></span></label>
          </div>
          </p>
		  </label>

		  <label class="ls-label col-md-4 col-sm-12">
          <b class="ls-label-text">RELATÓRIOS</b>
          <br><p class="ls-label-info">
          <div data-ls-module="switchButton" class="ls-switch-btn">
          <input type="checkbox" name="usu_m_relatorios" id="usu_m_relatorios" value=""  <?php if (!(strcmp(htmlentities($row_Usuarios['usu_m_relatorios'], ENT_COMPAT, 'utf-8'),"S"))) {echo "checked=\"checked\"";} ?>>
          <label class="ls-switch-label" for="usu_m_relatorios" name="label-teste" ls-switch-off="NÃO" ls-switch-on="SIM"><span></span></label>
          </div>
          </p>
		  </label>

		  
		  <label class="ls-label col-md-4 col-sm-12">
          <b class="ls-label-text">GRÁFICOS</b>
          <br><p class="ls-label-info">
          <div data-ls-module="switchButton" class="ls-switch-btn">
          <input type="checkbox" name="usu_m_graficos" id="usu_m_graficos" value=""  <?php if (!(strcmp(htmlentities($row_Usuarios['usu_m_graficos'], ENT_COMPAT, 'utf-8'),"S"))) {echo "checked=\"checked\"";} ?>>
          <label class="ls-switch-label" for="usu_m_graficos" name="label-teste" ls-switch-off="NÃO" ls-switch-on="SIM"><span></span></label>
          </div>
          </p>
		  </label>

		  <label class="ls-label col-md-4 col-sm-12">
          <b class="ls-label-text">CONFIGURAÇÕES</b>
          <br><p class="ls-label-info">
          <div data-ls-module="switchButton" class="ls-switch-btn">
          <input type="checkbox" name="usu_m_configuracoes" id="usu_m_configuracoes" value=""  <?php if (!(strcmp(htmlentities($row_Usuarios['usu_m_configuracoes'], ENT_COMPAT, 'utf-8'),"S"))) {echo "checked=\"checked\"";} ?>>
          <label class="ls-switch-label" for="usu_m_configuracoes" name="label-teste" ls-switch-off="NÃO" ls-switch-on="SIM"><span></span></label>
          </div>
          </p>
		  </label>

		  









		  
		  
		  
        <label class="ls-label col-md-12">
          <input type="submit" value="SALVAR" class="ls-btn-primary">
          <a href="usuarios.php" class="ls-btn">CANCELAR</a> </label>
        <input type="hidden" name="usu_id" value="<?php echo $row_Usuarios['usu_id']; ?>">
        <input type="hidden" name="usu_sec" value="<?php echo htmlentities($row_Usuarios['usu_sec'], ENT_COMPAT, 'utf-8'); ?>">
        <input type="hidden" name="MM_update" value="form1">
      </form>
      <p>&nbsp;</p>
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

mysql_free_result($Usuarios);

mysql_free_result($Escolas);
?>