<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "funcoes/funcoes.php"; ?>
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
$MM_authorizedUsers = "1";
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
  $updateSQL = sprintf("UPDATE smc_func SET func_id_sec=%s, func_nome=%s, func_mae=%s, func_pai=%s, func_data_nascimento=%s, func_uf_nascimento=%s, func_municipio_nascimento=%s, func_estado_civil=%s, func_sexo=%s, func_escolaridade=%s, func_cpf=%s, func_rg_numero=%s, func_rg_emissor=%s, func_titulo=%s, func_titulo_secao=%s, func_titulo_zona=%s, func_pis=%s, func_cnh_num=%s, func_categoria=%s, func_ctps=%s, func_ctps_serie=%s, func_reservista=%s, func_endereco=%s, func_endereco_numero=%s, func_endereco_bairro=%s, func_endereco_cep=%s, func_endereco_uf=%s, func_endereco_cidade=%s, func_matricula=%s, func_admissao=%s, func_decreto=%s, func_lotacao=%s, func_cargo=%s, func_regime=%s, func_grupo_sanquineo=%s, func_fator_rh=%s, func_email=%s, func_telefone=%s, func_celular1=%s, func_celular2=%s, func_agencia_banco=%s, func_conta_banco=%s, func_nome_banco=%s, func_area_concurso=%s, func_situacao=%s WHERE func_id=%s",
                       GetSQLValueString($_POST['func_id_sec'], "int"),
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
                       GetSQLValueString($_POST['func_situacao'], "int"),
                       GetSQLValueString($_POST['func_id'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

  $updateGoTo = "funcionarioExibir.php?editado";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
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
$query_Cargo = "SELECT funcao_id, funcao_nome, funcao_observacoes FROM smc_funcao ORDER BY funcao_nome ASC";
$Cargo = mysql_query($query_Cargo, $SmecelNovo) or die(mysql_error());
$row_Cargo = mysql_fetch_assoc($Cargo);
$totalRows_Cargo = mysql_num_rows($Cargo);

$colname_EditaFunc = "-1";
if (isset($_GET['c'])) {
  $colname_EditaFunc = $_GET['c'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EditaFunc = sprintf("SELECT func_id, func_id_sec, func_nome, func_mae, func_pai, func_data_nascimento, func_uf_nascimento, func_municipio_nascimento, func_estado_civil, func_sexo, func_escolaridade, func_cpf, func_rg_numero, func_rg_emissor, func_titulo, func_titulo_secao, func_titulo_zona, func_pis, func_cnh_num, func_categoria, func_ctps, func_ctps_serie, func_reservista, func_endereco, func_endereco_numero, func_endereco_bairro, func_endereco_cep, func_endereco_uf, func_endereco_cidade, func_matricula, func_admissao, func_decreto, func_lotacao, func_cargo, func_regime, func_grupo_sanquineo, func_fator_rh, func_email, func_telefone, func_celular1, func_celular2, func_agencia_banco, func_conta_banco, func_nome_banco, func_area_concurso, func_formacao, func_situacao, func_foto FROM smc_func WHERE func_id = %s", GetSQLValueString($colname_EditaFunc, "int"));
$EditaFunc = mysql_query($query_EditaFunc, $SmecelNovo) or die(mysql_error());
$row_EditaFunc = mysql_fetch_assoc($EditaFunc);
$totalRows_EditaFunc = mysql_num_rows($EditaFunc);
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
  <script language="Javascript">
function confirmacao(id) {
     var resposta = confirm("Deseja remover esse cadastro?");
 
     if (resposta == true) {
          window.location.href = "funcionarioExcluir.php?c="+id;
     }
}
</script>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>

<body>

<?php include "menu.php"; ?>

<div class="row">

  <div class="small-12 columns">
  	<a href="funcionarioExibir.php" class="button tiny">VOLTAR</a>
  </div>

	<div class="small-12 columns">
      <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
      <fieldset>
      <legend>EDITAR FUNCIONÁRIO</legend>
        
            <div class="small-12 columns">
            <label>Nome completo:
          <input type="text" name="func_nome" value="<?php echo htmlentities($row_EditaFunc['func_nome'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            </div>

            <div class="small-12 medium-6 columns">
            <label>Nome do pai:
          <input type="text" name="func_pai" value="<?php echo htmlentities($row_EditaFunc['func_pai'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            </div>

            <div class="small-12 medium-6 columns">
            <label>Nome da mãe:
          <input type="text" name="func_mae" value="<?php echo htmlentities($row_EditaFunc['func_mae'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            </div>

            <div class="small-12 medium-3 columns">
            <label>Data de Nascimento:
          <input type="text" name="func_data_nascimento" value="<?php echo htmlentities(inverteData($row_EditaFunc['func_data_nascimento']), ENT_COMPAT, 'utf-8'); ?>" size="32" class="placeholder">
            </label>
            </div>

            <div class="small-12 medium-2 columns">
            <label>UF Nascimento:
          <select name="func_uf_nascimento">
              <option value="-1" <?php if (!(strcmp("-1", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Escolha...</option>
              <option value="AC" <?php if (!(strcmp("AC", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>AC</option>
              <option value="AL" <?php if (!(strcmp("AL", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>AL</option>
              <option value="AP" <?php if (!(strcmp("AP", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>AP</option>
              <option value="AM" <?php if (!(strcmp("AM", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>AM</option>
              <option value="BA" <?php if (!(strcmp("BA", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>BA</option>
              <option value="CE" <?php if (!(strcmp("CE", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>CE</option>
              <option value="DF" <?php if (!(strcmp("DF", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>DF</option>
              <option value="ES" <?php if (!(strcmp("ES", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>ES</option>
              <option value="GO" <?php if (!(strcmp("GO", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>GO</option>
              <option value="MA" <?php if (!(strcmp("MA", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>MA</option>
              <option value="MT" <?php if (!(strcmp("MT", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>MT</option>
              <option value="MS" <?php if (!(strcmp("MS", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>MS</option>
              <option value="MG" <?php if (!(strcmp("MG", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>MG</option>
              <option value="PA" <?php if (!(strcmp("PA", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>PA</option>
              <option value="PB" <?php if (!(strcmp("PB", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>PB</option>
              <option value="PR" <?php if (!(strcmp("PR", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>PR</option>
              <option value="PE" <?php if (!(strcmp("PE", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>PE</option>
              <option value="PI" <?php if (!(strcmp("PI", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>PI</option>
              <option value="RJ" <?php if (!(strcmp("RJ", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>RJ</option>
              <option value="RN" <?php if (!(strcmp("RN", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>RN</option>
              <option value="RS" <?php if (!(strcmp("RS", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>RS</option>
              <option value="RO" <?php if (!(strcmp("RO", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>RO</option>
              <option value="RR" <?php if (!(strcmp("RR", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>RR</option>
              <option value="SC" <?php if (!(strcmp("SC", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>SC</option>
              <option value="SP" <?php if (!(strcmp("SP", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>SP</option>
              <option value="SE" <?php if (!(strcmp("SE", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>SE</option>
              <option value="TO" <?php if (!(strcmp("TO", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>TO</option>              
            </select>
            </label>
            </div>

            <div class="small-12 medium-7 columns">
            <label>Município de Nascimento:
          <input type="text" name="func_municipio_nascimento" value="<?php echo htmlentities($row_EditaFunc['func_municipio_nascimento'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            </div>
          

            <div class="small-12 medium-3 columns">
            <label>Estado Civil:
          <select name="func_estado_civil">
              <option value="-1" <?php if (!(strcmp(-1, htmlentities($row_EditaFunc['func_estado_civil'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>ESCOLHA...</option>
              <option value="1" <?php if (!(strcmp(1, htmlentities($row_EditaFunc['func_estado_civil'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>1 - SOLTEIRO</option>
              <option value="2" <?php if (!(strcmp(2, htmlentities($row_EditaFunc['func_estado_civil'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>2 - CASADO</option>
              <option value="3" <?php if (!(strcmp(3, htmlentities($row_EditaFunc['func_estado_civil'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>3 - VIÚVO</option>
              <option value="4" <?php if (!(strcmp(4, htmlentities($row_EditaFunc['func_estado_civil'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>4 - UNIÃO ESTÁVEL</option>
              <option value="5" <?php if (!(strcmp(5, htmlentities($row_EditaFunc['func_estado_civil'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>5 - OUTROS</option>
            </select>
            </label>
            </div>

            <div class="small-12 medium-3 columns">
            <label>Sexo:
          <select name="func_sexo">
              <option value="-1" <?php if (!(strcmp(-1, htmlentities($row_EditaFunc['func_sexo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>ESCOLHA...</option>
              <option value="1" <?php if (!(strcmp(1, htmlentities($row_EditaFunc['func_sexo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>MASCULINO</option>
              <option value="2" <?php if (!(strcmp(2, htmlentities($row_EditaFunc['func_sexo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>FEMININO</option>
            </select>
            </label>
            </div>

            <div class="small-12 medium-3 columns">
            <label>Escolaridade:
          <select name="func_escolaridade">
              <option value="-1" <?php if (!(strcmp(-1, htmlentities($row_EditaFunc['func_escolaridade'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>ESCOLHA...</option>
              <option value="1" <?php if (!(strcmp(1, htmlentities($row_EditaFunc['func_escolaridade'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>1 - ENSINO FUNDAMENTAL</option>
              <option value="2" <?php if (!(strcmp(2, htmlentities($row_EditaFunc['func_escolaridade'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>2 - ENSINO MÉDIO</option>
              <option value="3" <?php if (!(strcmp(3, htmlentities($row_EditaFunc['func_escolaridade'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>3 - GRADUAÇÃO</option>
              <option value="4" <?php if (!(strcmp(4, htmlentities($row_EditaFunc['func_escolaridade'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>4 - PÓS-GRADUAÇÃO</option>
              <option value="5" <?php if (!(strcmp(5, htmlentities($row_EditaFunc['func_escolaridade'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>5 - MESTRADO</option>
            </select>
            </label>
            </div>
            
            <div class="small-12 medium-3 columns">
            <label>Área Concurso:
            <select name="func_area_concurso">
              <option value="-1" <?php if (!(strcmp(-1, htmlentities($row_EditaFunc['func_area_concurso'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>ESCOLHA...</option>
              <option value="1" <?php if (!(strcmp("BA", htmlentities($row_EditaFunc['func_area_concurso'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>1 - FUNDAMENTAL I</option>
              <option value="2" <?php if (!(strcmp("SP", htmlentities($row_EditaFunc['func_area_concurso'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>2 - FUNDAMENTAL II</option>
            </select>
            </label>
            </div>
            
            <hr>
           
            <div class="small-12 medium-4 columns">
            <label>CPF:
           <input type="text" name="func_cpf" id="cpf" value="<?php echo htmlentities($row_EditaFunc['func_cpf'], ENT_COMPAT, 'utf-8'); ?>" size="32" onblur="javascript: validarCPF(this);" onkeypress="javascript: mascara(this, cpf_mask);">
           </label>
            </div>
         
            <div class="small-12 medium-4 columns">
            <label>RG:
          <input type="text" name="func_rg_numero" value="<?php echo htmlentities($row_EditaFunc['func_rg_numero'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            </div>

            <div class="small-12 medium-4 columns">
            <label>Órgão Emissor:
          <input type="text" name="func_rg_emissor" value="<?php echo htmlentities($row_EditaFunc['func_rg_emissor'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            </div>

            <div class="small-12 medium-5 columns">
            <label>Título de Eleitor:
          <input type="text" name="func_titulo" value="<?php echo htmlentities($row_EditaFunc['func_titulo'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            </div>

            <div class="small-12 medium-5 columns">
            <label>Seção:
          <input type="text" name="func_titulo_secao" value="<?php echo htmlentities($row_EditaFunc['func_titulo_secao'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            </div>

            <div class="small-12 medium-2 columns">
            <label>Zona Eleitoral:
           <input type="text" name="func_titulo_zona" value="<?php echo htmlentities($row_EditaFunc['func_titulo_zona'], ENT_COMPAT, 'utf-8'); ?>" size="32">
           </label>
            </div>

            <div class="small-12 medium-6 columns">
            <label>PIS/PASEP:
          <input type="text" name="func_pis" value="<?php echo htmlentities($row_EditaFunc['func_pis'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            </div>

            <div class="small-12 medium-4 columns">
            <label>CNH:
          <input type="text" name="func_cnh_num" value="<?php echo htmlentities($row_EditaFunc['func_cnh_num'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            </div>

            <div class="small-12 medium-2 columns">
            <label>Categoria:
          <select name="func_categoria">
              <option value="-1" <?php if (!(strcmp("-1", htmlentities($row_EditaFunc['func_categoria'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Escolha...</option>
              <option value="A" <?php if (!(strcmp("A", htmlentities($row_EditaFunc['func_categoria'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>A</option>
              <option value="B" <?php if (!(strcmp("B", htmlentities($row_EditaFunc['func_categoria'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>B</option>
              <option value="C" <?php if (!(strcmp("C", htmlentities($row_EditaFunc['func_categoria'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>C</option>
              <option value="D" <?php if (!(strcmp("D", htmlentities($row_EditaFunc['func_categoria'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>D</option>
              <option value="E" <?php if (!(strcmp("E", htmlentities($row_EditaFunc['func_categoria'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>E</option>
              <option value="AB" <?php if (!(strcmp("AB", htmlentities($row_EditaFunc['func_categoria'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>AB</option>
              <option value="AC" <?php if (!(strcmp("AC", htmlentities($row_EditaFunc['func_categoria'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>AC</option>
              <option value="AD" <?php if (!(strcmp("AD", htmlentities($row_EditaFunc['func_categoria'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>AD</option>
              <option value="AE" <?php if (!(strcmp("AE", htmlentities($row_EditaFunc['func_categoria'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>AE</option>
            </select>
            </label>
            </div>
          
            <div class="small-12 medium-4 columns">
            <label>CTPS:
           <input type="text" name="func_ctps" value="<?php echo htmlentities($row_EditaFunc['func_ctps'], ENT_COMPAT, 'utf-8'); ?>" size="32">
           </label>
            </div>

            <div class="small-12 medium-4 columns">
            <label>Série:
          <input type="text" name="func_ctps_serie" value="<?php echo htmlentities($row_EditaFunc['func_ctps_serie'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            </div>
          
            <div class="small-12 medium-4 columns">
            <label>Reservista:
          <input type="text" name="func_reservista" value="<?php echo htmlentities($row_EditaFunc['func_reservista'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            </div>
          
            <div class="small-12 medium-10 columns">
            <label>Endereço:
          <input type="text" name="func_endereco" value="<?php echo htmlentities($row_EditaFunc['func_endereco'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            </div>
          
            <div class="small-12 medium-2 columns">
            <label>Número:
          <input type="text" name="func_endereco_numero" value="<?php echo htmlentities($row_EditaFunc['func_endereco_numero'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            </div>

            <div class="small-12 medium-8 columns">
            <label>Bairro:
          <input type="text" name="func_endereco_bairro" value="<?php echo htmlentities($row_EditaFunc['func_endereco_bairro'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            </div>
          
            <div class="small-12 medium-4 columns">
            <label>CEP:
          <input type="text" name="func_endereco_cep" value="<?php echo htmlentities($row_EditaFunc['func_endereco_cep'], ENT_COMPAT, 'utf-8'); ?>" size="32" class="cep">
            </label>
            </div>
          
            <div class="small-12 medium-6 columns">
            <label>UF:
          <select name="func_endereco_uf">
              <option value="-1" <?php if (!(strcmp(-1, htmlentities($row_EditaFunc['func_endereco_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>ESCOLHA...</option>
              <option value="AC" <?php if (!(strcmp("AC", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>AC</option>
              <option value="AL" <?php if (!(strcmp("AL", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>AL</option>
              <option value="AP" <?php if (!(strcmp("AP", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>AP</option>
              <option value="AM" <?php if (!(strcmp("AM", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>AM</option>
              <option value="BA" <?php if (!(strcmp("BA", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>BA</option>
              <option value="CE" <?php if (!(strcmp("CE", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>CE</option>
              <option value="DF" <?php if (!(strcmp("DF", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>DF</option>
              <option value="ES" <?php if (!(strcmp("ES", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>ES</option>
              <option value="GO" <?php if (!(strcmp("GO", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>GO</option>
              <option value="MA" <?php if (!(strcmp("MA", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>MA</option>
              <option value="MT" <?php if (!(strcmp("MT", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>MT</option>
              <option value="MS" <?php if (!(strcmp("MS", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>MS</option>
              <option value="MG" <?php if (!(strcmp("MG", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>MG</option>
              <option value="PA" <?php if (!(strcmp("PA", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>PA</option>
              <option value="PB" <?php if (!(strcmp("PB", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>PB</option>
              <option value="PR" <?php if (!(strcmp("PR", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>PR</option>
              <option value="PE" <?php if (!(strcmp("PE", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>PE</option>
              <option value="PI" <?php if (!(strcmp("PI", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>PI</option>
              <option value="RJ" <?php if (!(strcmp("RJ", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>RJ</option>
              <option value="RN" <?php if (!(strcmp("RN", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>RN</option>
              <option value="RS" <?php if (!(strcmp("RS", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>RS</option>
              <option value="RO" <?php if (!(strcmp("RO", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>RO</option>
              <option value="RR" <?php if (!(strcmp("RR", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>RR</option>
              <option value="SC" <?php if (!(strcmp("SC", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>SC</option>
              <option value="SP" <?php if (!(strcmp("SP", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>SP</option>
              <option value="SE" <?php if (!(strcmp("SE", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>SE</option>
              <option value="TO" <?php if (!(strcmp("TO", htmlentities($row_EditaFunc['func_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>TO</option>              
            </select>
            </label>
            </div>

            <div class="small-12 medium-6 columns">
            <label>Cidade:
          <input type="text" name="func_endereco_cidade" value="<?php echo htmlentities($row_EditaFunc['func_endereco_cidade'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            </div>
          
            <div class="small-12 medium-4 columns">
            <label>Matrícula:
          <input type="text" name="func_matricula" value="<?php echo htmlentities($row_EditaFunc['func_matricula'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            </div>
          

            <div class="small-12 medium-4 columns">
            <label>Admissão:
          <input type="text" name="func_admissao" value="<?php echo htmlentities(inverteData($row_EditaFunc['func_admissao']), ENT_COMPAT, 'utf-8'); ?>" size="32" class="placeholder">
            </label>
            </div>
          
            <div class="small-12 medium-4 columns">
            <label>Decreto:
          <input type="text" name="func_decreto" value="<?php echo htmlentities($row_EditaFunc['func_decreto'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            </div>

            <div class="small-12 medium-6 columns">
            <label>Cargo/Função:
          <select name="func_cargo">
              <option value="-1" <?php if (!(strcmp(-1, htmlentities($row_Cargo['funcao_id'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>ESCOLHA...</option>         
              <?php 
				  do {  
				  ?>
				<option value="<?php echo $row_Cargo['funcao_id']?>" <?php if (!(strcmp($row_Cargo['funcao_id'], htmlentities($row_EditaFunc['func_cargo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>><?php echo $row_Cargo['funcao_nome']?></option>
								<?php
				  } while ($row_Cargo = mysql_fetch_assoc($Cargo));
				  ?>
            </select>
            </label>
            </div>

            <div class="small-12 medium-6 columns">
            <label>Lotação:
          <input type="text" name="func_lotacao" value="<?php echo htmlentities($row_EditaFunc['func_lotacao'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            </div>
          
            <div class="small-12 medium-4 columns">
            <label>Regime:
          <select name="func_regime">
              <option value="-1" <?php if (!(strcmp(-1, htmlentities($row_EditaFunc['func_regime'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>ESCOLHA...</option>
              <option value="1" <?php if (!(strcmp(1, htmlentities($row_EditaFunc['func_regime'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>1 - EFETIVO</option>
              <option value="2" <?php if (!(strcmp(2, htmlentities($row_EditaFunc['func_regime'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>2 - CONTRATADO</option>
            </select>
            </label>
            </div>

            <div class="small-12 medium-4 columns">
            <label>Grupo Sanguíneo:
          <select name="func_grupo_sanquineo">
              <option value="-1" <?php if (!(strcmp(-1, htmlentities($row_EditaFunc['func_grupo_sanquineo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>ESCOLHA...</option>
              <option value="A" <?php if (!(strcmp("A", htmlentities($row_EditaFunc['func_grupo_sanquineo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>A</option>
              <option value="B" <?php if (!(strcmp("B", htmlentities($row_EditaFunc['func_grupo_sanquineo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>B</option>
              <option value="AB" <?php if (!(strcmp("AB", htmlentities($row_EditaFunc['func_grupo_sanquineo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>AB</option>
              <option value="O" <?php if (!(strcmp("O", htmlentities($row_EditaFunc['func_grupo_sanquineo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>O</option>
            </select>
            </label>
            </div>

            <div class="small-12 medium-4 columns">
            <label>Fator RH:
           <select name="func_fator_rh">
              <option value="-1" <?php if (!(strcmp(-1, htmlentities($row_EditaFunc['func_fator_rh'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>ESCOLHA...</option>
              <option value="+" <?php if (!(strcmp("+", htmlentities($row_EditaFunc['func_fator_rh'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>POSITIVO+</option>
              <option value="-" <?php if (!(strcmp("-", htmlentities($row_EditaFunc['func_fator_rh'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>NEGATIVO-</option>
            </select>
           </label>
            </div>
            
            <hr>

            <div class="small-12 medium-4 columns">
            <label>E-mail:
          <input type="email" name="func_email" value="<?php echo htmlentities($row_EditaFunc['func_email'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            </div>
          
            <div class="small-12 medium-4 columns">
            <label>Telefone:
          <input type="text" name="func_telefone" value="<?php echo htmlentities($row_EditaFunc['func_telefone'], ENT_COMPAT, 'utf-8'); ?>" size="32" class="phone_with_ddd">
            </label>
            </div>

            <div class="small-12 medium-4 columns">
            <label>Celular:
           <input type="text" name="func_celular1" value="<?php echo htmlentities($row_EditaFunc['func_celular1'], ENT_COMPAT, 'utf-8'); ?>" size="32" class="celular">
           </label>
            </div>
          
            <div class="small-12 medium-4 columns">
            <label>Celular:
          <input type="text" name="func_celular2" value="<?php echo htmlentities($row_EditaFunc['func_celular2'], ENT_COMPAT, 'utf-8'); ?>" size="32" class="celular">
            </label>
            </div>
          
            <div class="small-12 medium-4 columns">
            <label>Agência:
          <input type="text" name="func_agencia_banco" value="<?php echo htmlentities($row_EditaFunc['func_agencia_banco'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            </div>
          
            <div class="small-12 medium-4 columns">
            <label>Conta:
          <input type="text" name="func_conta_banco" value="<?php echo htmlentities($row_EditaFunc['func_conta_banco'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            </div>
          
            <div class="small-12 medium-8 columns">
            <label>Banco:
          <input type="text" name="func_nome_banco" value="<?php echo htmlentities($row_EditaFunc['func_nome_banco'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            </div>
          
            <div class="small-12 medium-4 columns">
            <label>Situação:
           <select name="func_situacao">
              <option value="-1" <?php if (!(strcmp(-1, htmlentities($row_EditaFunc['func_situacao'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>ESCOLHA...</option>
              <option value="1" <?php if (!(strcmp(1, htmlentities($row_EditaFunc['func_situacao'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>1 - EM ATIVIDADE</option>
              <option value="2" <?php if (!(strcmp(2, htmlentities($row_EditaFunc['func_situacao'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>2 - AFASTADO</option>
            </select>
           </label>
            </div>
          
            <div class="small-12 columns">
          <input type="submit" value="SALVAR ALTERAÇÕES" class="button">
          <a href="titulacaoCadastrar.php?c=<?php echo $row_EditaFunc['func_id']; ?>" class="button success">INCLUIR FORMAÇÃO</a>
          <a href="javascript:func()" onclick="confirmacao('<?php echo $row_EditaFunc['func_id']; ?>')"class="button alert right">EXCLUIR</a>
            </div>
          
          
          
        <input type="hidden" name="func_id" value="<?php echo $row_EditaFunc['func_id']; ?>">
        <input type="hidden" name="func_id_sec" value="<?php echo htmlentities($row_EditaFunc['func_id_sec'], ENT_COMPAT, 'utf-8'); ?>">
        <input type="hidden" name="MM_update" value="form1">
        <input type="hidden" name="func_id" value="<?php echo $row_EditaFunc['func_id']; ?>">
      </fieldset>
      </form>
      <p>&nbsp;</p>
	</div>
</div>


<script src="../../js/vendor/jquery.js"></script>
  <script src="../../js/foundation.min.js"></script>
  <script src="js/foundation/foundation.dropdown.js"></script>
  <script src="../../js/jquery.mask.js"></script>
<script>
    $(document).foundation();
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
 
 <script language="javascript">
  function noTilde(objResp) {
  var varString = new String(objResp.value);
  var stringAcentos = new String('àâêôûãõáéíóúçüÀÂÊÔÛÃÕÁÉÍÓÚÇÜ[]');
  var stringSemAcento = new String('aaeouaoaeioucuAAEOUAOAEIOUCU');
  
  var i = new Number();
  var j = new Number();
  var cString = new String();
  var varRes = "";
  
	for (i = 0; i < varString.length; i++) {
	  cString = varString.substring(i, i + 1);
		for (j = 0; j < stringAcentos.length; j++) {
		if (stringAcentos.substring(j, j + 1) == cString){
		cString = stringSemAcento.substring(j, j + 1);
		}
	  }
	  varRes += cString;
	}
	objResp.value = varRes;
	}
  $(function() {
	  $("input:text").keyup(function() {
  noTilde(this);
  });
  });
</script>

<script type="text/javascript" language="javascript">
	function validarCPF( cpf ){
		var vcpf = cpf.value;
		var filtro = /^\d{3}.\d{3}.\d{3}-\d{2}$/i;
		
		if(!filtro.test(vcpf))
		{
			window.alert("CPF inválido. Tente novamente.");vcpf
			return false;
		}
	   
		vcpf = remove(vcpf, ".");
		vcpf = remove(vcpf, "-");
	    
		if(vcpf.length != 11 || vcpf == "00000000000" || vcpf == "11111111111" ||
			vcpf == "22222222222" || vcpf == "33333333333" || vcpf == "44444444444" ||
			vcpf == "55555555555" || vcpf == "66666666666" || vcpf == "77777777777" ||
			vcpf == "88888888888" || vcpf == "99999999999")
		{
			window.alert("CPF inválido. Tente novamente.");vcpf
			return false;
	   }

		soma = 0;
		for(i = 0; i < 9; i++)
		{
			soma += parseInt(vcpf.charAt(i)) * (10 - i);
		}
		
		resto = 11 - (soma % 11);
		if(resto == 10 || resto == 11)
		{
			resto = 0;
		}
		if(resto != parseInt(vcpf.charAt(9))){
			window.alert("CPF inválido. Tente novamente.");vcpf
			return false;
		}
		
		soma = 0;
		for(i = 0; i < 10; i ++)
		{
			soma += parseInt(vcpf.charAt(i)) * (11 - i);
		}
		resto = 11 - (soma % 11);
		if(resto == 10 || resto == 11)
		{
			resto = 0;
		}
		
		if(resto != parseInt(vcpf.charAt(10))){
			window.alert("CPF inválido. Tente novamente.");vcpf
			return false;
		}
		
		return true;
	 }
	 
	function remove(str, sub) {
		i = str.indexOf(sub);
		r = "";
		if (i == -1) return str;
		{
			r += str.substring(0,i) + remove(str.substring(i + sub.length), sub);
		}
		
		return r;
	}
	
	/**
	   * MASCARA ( mascara(o,f) e execmascara() ) CRIADAS POR ELCIO LUIZ
	   * elcio.com.br - https://elcio.com.br/ajax/mascara/
	   */
	function mascara(o,f){
		v_obj=o
		v_fun=f
		setTimeout("execmascara()",1)
	}

	function execmascara(){
		v_obj.value=v_fun(v_obj.value)
	}

	function cpf_mask(v){
		v=v.replace(/\D/g,"")                 //Remove tudo o que não é dígito
		v=v.replace(/(\d{3})(\d)/,"$1.$2")    //Coloca ponto entre o terceiro e o quarto dígitos
		v=v.replace(/(\d{3})(\d)/,"$1.$2")    //Coloca ponto entre o setimo e o oitava dígitos
		v=v.replace(/(\d{3})(\d)/,"$1-$2")   //Coloca ponto entre o decimoprimeiro e o decimosegundo dígitos
		return v
	}
</script>


</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Cargo);

mysql_free_result($EditaFunc);
?>
