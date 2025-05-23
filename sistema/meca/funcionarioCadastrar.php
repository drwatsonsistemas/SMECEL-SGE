<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "funcoes/funcoes.php"; ?>
<?php include "funcoes/retiraAcentos.php"; ?>
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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO smc_func (func_id_sec, func_nome, func_mae, func_pai, func_data_nascimento, func_uf_nascimento, func_municipio_nascimento, func_estado_civil, func_sexo, func_escolaridade, func_cpf, func_rg_numero, func_rg_emissor, func_titulo, func_titulo_secao, func_titulo_zona, func_pis, func_cnh_num, func_categoria, func_ctps, func_ctps_serie, func_reservista, func_endereco, func_endereco_numero, func_endereco_bairro, func_endereco_cep, func_endereco_uf, func_endereco_cidade, func_matricula, func_admissao, func_decreto, func_lotacao, func_cargo, func_regime, func_grupo_sanquineo, func_fator_rh, func_email, func_telefone, func_celular1, func_celular2, func_agencia_banco, func_conta_banco, func_nome_banco, func_area_concurso, func_situacao) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['func_id_sec'], "int"),
                       GetSQLValueString(retiraAcentos($_POST['func_nome']), "text"),
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
                       GetSQLValueString($_POST['func_situacao'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

  $nome = retiraAcentos($_POST['func_nome']);

  $insertGoTo = "funcionarioExibirCadastrado.php?nome=$nome";
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
$query_Cargo = "SELECT funcao_id, funcao_nome FROM smc_funcao ORDER BY funcao_nome ASC";
$Cargo = mysql_query($query_Cargo, $SmecelNovo) or die(mysql_error());
$row_Cargo = mysql_fetch_assoc($Cargo);
$totalRows_Cargo = mysql_num_rows($Cargo);
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
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>

<body>

<?php include "menu.php"; ?>

<div class="row">

	<div class="small-12 columns">
      <form method="post" name="form1" action="<?php echo $editFormAction; ?>" data-abide>
      
      <fieldset>
      <legend>CADASTRAR FUNCIONÁRIO</legend>
      
            <div class="small-12 columns">
            <label>Nome completo:
             <input type="text" name="func_nome" value="" size="32" required>
            </label>
            </div>
          
            <div class="small-12 medium-6 columns">
            <label>Nome do pai:
            <input type="text" name="func_pai" value="" size="32">
            </label>
            </div>

            <div class="small-12 medium-6 columns">
            <label>Nome da mãe:             
            <input type="text" name="func_mae" value="" size="32">
            </label>
            </div>
             
            <div class="small-12 medium-3 columns">
            <label>Data de nascimento:
            <input type="text" name="func_data_nascimento" value="" size="32" class="placeholder">
            </label>
            </div>
             
          
            <div class="small-12 medium-2 columns">
            <label>UF Nascimento:
             <select name="func_uf_nascimento">
              <option value="-1" <?php if (!(strcmp(-1, ""))) {echo "SELECTED";} ?>>ESCOLHA...</option>
              <option value="AC" <?php if (!(strcmp("AC", ""))) {echo "SELECTED";} ?>>AC</option>
              <option value="AL" <?php if (!(strcmp("AL", ""))) {echo "SELECTED";} ?>>AL</option>
              <option value="AP" <?php if (!(strcmp("AP", ""))) {echo "SELECTED";} ?>>AP</option>
              <option value="AM" <?php if (!(strcmp("AM", ""))) {echo "SELECTED";} ?>>AM</option>
              <option value="BA" <?php if (!(strcmp("BA", ""))) {echo "SELECTED";} ?>>BA</option>
              <option value="CE" <?php if (!(strcmp("CE", ""))) {echo "SELECTED";} ?>>CE</option>
              <option value="DF" <?php if (!(strcmp("DF", ""))) {echo "SELECTED";} ?>>DF</option>
              <option value="ES" <?php if (!(strcmp("ES", ""))) {echo "SELECTED";} ?>>ES</option>
              <option value="GO" <?php if (!(strcmp("GO", ""))) {echo "SELECTED";} ?>>GO</option>
              <option value="MA" <?php if (!(strcmp("MA", ""))) {echo "SELECTED";} ?>>MA</option>
              <option value="MT" <?php if (!(strcmp("MT", ""))) {echo "SELECTED";} ?>>MT</option>
              <option value="MS" <?php if (!(strcmp("MS", ""))) {echo "SELECTED";} ?>>MS</option>              
              <option value="MG" <?php if (!(strcmp("MG", ""))) {echo "SELECTED";} ?>>MG</option>
              <option value="PA" <?php if (!(strcmp("PA", ""))) {echo "SELECTED";} ?>>PA</option>
              <option value="PB" <?php if (!(strcmp("PB", ""))) {echo "SELECTED";} ?>>PB</option>
              <option value="PR" <?php if (!(strcmp("PR", ""))) {echo "SELECTED";} ?>>PR</option>
              <option value="PE" <?php if (!(strcmp("PE", ""))) {echo "SELECTED";} ?>>PE</option>
              <option value="PI" <?php if (!(strcmp("PI", ""))) {echo "SELECTED";} ?>>PI</option>
              <option value="RJ" <?php if (!(strcmp("RJ", ""))) {echo "SELECTED";} ?>>RJ</option>
              <option value="RN" <?php if (!(strcmp("RN", ""))) {echo "SELECTED";} ?>>RN</option>
              <option value="RS" <?php if (!(strcmp("RS", ""))) {echo "SELECTED";} ?>>RS</option>
              <option value="RO" <?php if (!(strcmp("RO", ""))) {echo "SELECTED";} ?>>RO</option>
              <option value="RR" <?php if (!(strcmp("RR", ""))) {echo "SELECTED";} ?>>RR</option>
              <option value="SC" <?php if (!(strcmp("SC", ""))) {echo "SELECTED";} ?>>SC</option>
              <option value="SP" <?php if (!(strcmp("SP", ""))) {echo "SELECTED";} ?>>SP</option>
              <option value="SE" <?php if (!(strcmp("SE", ""))) {echo "SELECTED";} ?>>SE</option>
              <option value="TO" <?php if (!(strcmp("TO", ""))) {echo "SELECTED";} ?>>TO</option>
            </select>
			</label>
            </div>
          
            <div class="small-12 medium-7 columns">
            <label>Município de Nascimento:
             <input type="text" name="func_municipio_nascimento" value="" size="32">            
            </label>
            </div>
          
            <div class="small-12 medium-3 columns">
            <label>Estado Civil:
             <select name="func_estado_civil">
              <option value="-1" <?php if (!(strcmp(-1, ""))) {echo "SELECTED";} ?>>ESCOLHA...</option>
              <option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>1 - SOLTEIRO</option>
              <option value="2" <?php if (!(strcmp(2, ""))) {echo "SELECTED";} ?>>2 - CASADO</option>
              <option value="3" <?php if (!(strcmp(3, ""))) {echo "SELECTED";} ?>>3 - VIÚVO</option>
              <option value="4" <?php if (!(strcmp(4, ""))) {echo "SELECTED";} ?>>4 - UNIÃO ESTÁVEL</option>
              <option value="5" <?php if (!(strcmp(5, ""))) {echo "SELECTED";} ?>>5 - OUTROS</option>
            </select>            
            </label>
            </div>
          
            <div class="small-12 medium-3 columns">
            <label>Sexo:
            <select name="func_sexo">
              <option value="-1" <?php if (!(strcmp(-1, ""))) {echo "SELECTED";} ?>>ESCOLHA...</option>
              <option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>1 - MASCULINO</option>
              <option value="2" <?php if (!(strcmp(2, ""))) {echo "SELECTED";} ?>>2 - FEMININO</option>
            </select>
            </label>
            </div>
          
            <div class="small-12 medium-3 columns">
            <label>Escolaridade:
             <select name="func_escolaridade">
              <option value="-1" <?php if (!(strcmp(-1, ""))) {echo "SELECTED";} ?>>ESCOLHA...</option>
              <option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>1 - ENSINO FUNDAMENTAL</option>
              <option value="2" <?php if (!(strcmp(2, ""))) {echo "SELECTED";} ?>>2 - ENSINO MÉDIO</option>
              <option value="3" <?php if (!(strcmp(3, ""))) {echo "SELECTED";} ?>>3 - GRADUAÇÃO</option>
              <option value="4" <?php if (!(strcmp(4, ""))) {echo "SELECTED";} ?>>4 - PÓS-GRADUAÇÃO</option>
              <option value="5" <?php if (!(strcmp(5, ""))) {echo "SELECTED";} ?>>5 - MESTRADO</option>
            </select>            
            </label>
            </div>
            
            <div class="small-12 medium-3 columns">
            <label>Área de Concurso:
             <select name="func_area_concurso">
              <option value="-1" <?php if (!(strcmp(-1, ""))) {echo "SELECTED";} ?>>ESCOLHA...</option>
              <option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>1 - FUNDAMENTAL I</option>
              <option value="2" <?php if (!(strcmp(2, ""))) {echo "SELECTED";} ?>>2 - FUNDAMENTAL II</option>
            </select>           
            </label>
            </div>
            <hr>
            
            <div class="small-12 medium-4 columns">
            <label>CPF:
             <input type="text" name="func_cpf" id="cpf" value="" size="32" onblur="javascript: validarCPF(this);" onkeypress="javascript: mascara(this, cpf_mask);"> 
           
            </label>
            </div>
          
            <div class="small-12 medium-4 columns">
            <label>RG:
             <input type="text" name="func_rg_numero" value="" size="32">
            </label>
            </div>
          
            <div class="small-12 medium-4 columns">
            <label>Órgão Emissor:
             <input type="text" name="func_rg_emissor" value="" size="32">
            </label>
            </div>
          
            <div class="small-12 medium-5 columns">
            <label>Título de Eleitor:
             <input type="text" name="func_titulo" value="" size="32">
            </label>
            </div>
          
            <div class="small-12 medium-3 columns">
            <label>Zona Eleitoral:
             <input type="text" name="func_titulo_zona" value="" size="32">
            </label>
            </div>

            <div class="small-12 medium-4 columns">
            <label>Seção Eleitoral:
             <input type="text" name="func_titulo_secao" value="" size="32">
            </label>
            </div>
          
            <div class="small-12 medium-6 columns">
            <label>PIS/PASEP:
             <input type="text" name="func_pis" value="" size="32">
            </label>
            </div>
          
            <div class="small-12 medium-4 columns">
            <label>CNH:
            <input type="text" name="func_cnh_num" value="" size="32">
            </label>
            </div>
          
            <div class="small-12 medium-2 columns">
            <label>Categoria:
             <select name="func_categoria">
              <option value="-1" <?php if (!(strcmp(-1, ""))) {echo "SELECTED";} ?>>ESCOLHA...</option>
              <option value="A" <?php if (!(strcmp("A", ""))) {echo "SELECTED";} ?>>A</option>
              <option value="B" <?php if (!(strcmp("B", ""))) {echo "SELECTED";} ?>>B</option>
              <option value="AB" <?php if (!(strcmp("AB", ""))) {echo "SELECTED";} ?>>AB</option>
              <option value="C" <?php if (!(strcmp("C", ""))) {echo "SELECTED";} ?>>C</option>
              <option value="D" <?php if (!(strcmp("D", ""))) {echo "SELECTED";} ?>>D</option>
              <option value="E" <?php if (!(strcmp("E", ""))) {echo "SELECTED";} ?>>E</option>
             </select>            
            </label>
            </div>
          
            <div class="small-12 medium-4 columns">
            <label>CTPS:
             <input type="text" name="func_ctps" value="" size="32">
            </label>
            </div>
          
            <div class="small-12 medium-4 columns">
            <label>Série / CTPS:
             <input type="text" name="func_ctps_serie" value="" size="32">
            </label>
            </div>
          
            <div class="small-12 medium-4 columns">
            <label>Reservista:
             <input type="text" name="func_reservista" value="" size="32">
            </label>
            </div>
          
            <div class="small-12 medium-10 columns">
            <label>Endereço:
             <input type="text" name="func_endereco" value="" size="32">
            </label>
            </div>
          
            <div class="small-12 medium-2 columns">
            <label>Número:
             <input type="text" name="func_endereco_numero" value="" size="32">
            </label>
            </div>
          
            <div class="small-12 medium-8 columns">
            <label>Bairro:
             <input type="text" name="func_endereco_bairro" value="" size="32">
            </label>
            </div>
          
            <div class="small-12 medium-4 columns">
            <label>CEP:
             <input type="text" name="func_endereco_cep" value="" size="32" class="cep">
            </label>
            </div>
          
            <div class="small-12 medium-6 columns">
            <label>Estado:
             <select name="func_endereco_uf">
              <option value="-1" <?php if (!(strcmp(-1, ""))) {echo "SELECTED";} ?>>ESCOLHA...</option>
              <option value="AC" <?php if (!(strcmp("AC", ""))) {echo "SELECTED";} ?>>AC</option>
              <option value="AL" <?php if (!(strcmp("AL", ""))) {echo "SELECTED";} ?>>AL</option>
              <option value="AP" <?php if (!(strcmp("AP", ""))) {echo "SELECTED";} ?>>AP</option>
              <option value="AM" <?php if (!(strcmp("AM", ""))) {echo "SELECTED";} ?>>AM</option>
              <option value="BA" <?php if (!(strcmp("BA", ""))) {echo "SELECTED";} ?>>BA</option>
              <option value="CE" <?php if (!(strcmp("CE", ""))) {echo "SELECTED";} ?>>CE</option>
              <option value="DF" <?php if (!(strcmp("DF", ""))) {echo "SELECTED";} ?>>DF</option>
              <option value="ES" <?php if (!(strcmp("ES", ""))) {echo "SELECTED";} ?>>ES</option>
              <option value="GO" <?php if (!(strcmp("GO", ""))) {echo "SELECTED";} ?>>GO</option>
              <option value="MA" <?php if (!(strcmp("MA", ""))) {echo "SELECTED";} ?>>MA</option>
              <option value="MT" <?php if (!(strcmp("MT", ""))) {echo "SELECTED";} ?>>MT</option>
              <option value="MS" <?php if (!(strcmp("MS", ""))) {echo "SELECTED";} ?>>MS</option>              
              <option value="MG" <?php if (!(strcmp("MG", ""))) {echo "SELECTED";} ?>>MG</option>
              <option value="PA" <?php if (!(strcmp("PA", ""))) {echo "SELECTED";} ?>>PA</option>
              <option value="PB" <?php if (!(strcmp("PB", ""))) {echo "SELECTED";} ?>>PB</option>
              <option value="PR" <?php if (!(strcmp("PR", ""))) {echo "SELECTED";} ?>>PR</option>
              <option value="PE" <?php if (!(strcmp("PE", ""))) {echo "SELECTED";} ?>>PE</option>
              <option value="PI" <?php if (!(strcmp("PI", ""))) {echo "SELECTED";} ?>>PI</option>
              <option value="RJ" <?php if (!(strcmp("RJ", ""))) {echo "SELECTED";} ?>>RJ</option>
              <option value="RN" <?php if (!(strcmp("RN", ""))) {echo "SELECTED";} ?>>RN</option>
              <option value="RS" <?php if (!(strcmp("RS", ""))) {echo "SELECTED";} ?>>RS</option>
              <option value="RO" <?php if (!(strcmp("RO", ""))) {echo "SELECTED";} ?>>RO</option>
              <option value="RR" <?php if (!(strcmp("RR", ""))) {echo "SELECTED";} ?>>RR</option>
              <option value="SC" <?php if (!(strcmp("SC", ""))) {echo "SELECTED";} ?>>SC</option>
              <option value="SP" <?php if (!(strcmp("SP", ""))) {echo "SELECTED";} ?>>SP</option>
              <option value="SE" <?php if (!(strcmp("SE", ""))) {echo "SELECTED";} ?>>SE</option>
              <option value="TO" <?php if (!(strcmp("TO", ""))) {echo "SELECTED";} ?>>TO</option>
            </select>
            </label>
            </div>
          
            <div class="small-12 medium-6 columns">
            <label>Cidade:
             <input type="text" name="func_endereco_cidade" value="" size="32">
            </label>
            </div>
          
            <div class="small-12 medium-4 columns">
            <label>Matrícula:
             <input type="text" name="func_matricula" value="" size="32">
            </label>
            </div>
          
            <div class="small-12 medium-4 columns">
            <label>Data de Admissão:
             <input type="text" name="func_admissao" value="" size="32" class="placeholder">
            </label>
            </div>
          
            <div class="small-12 medium-4 columns">
            <label>Decreto:
             <input type="text" name="func_decreto" value="" size="32">
            </label>
            </div>
            
            <div class="small-12 medium-6 columns">
            <label>Cargo de concurso:
             <select name="func_cargo">
                <option value="-1" <?php if (!(strcmp(-1, ""))) {echo "SELECTED";} ?>>ESCOLHA...</option>
              <?php do { ?>
				<option value="<?php echo $row_Cargo['funcao_id']?>" ><?php echo $row_Cargo['funcao_nome']?></option>
			  <?php } while ($row_Cargo = mysql_fetch_assoc($Cargo)); ?>
            </select>
            </label>
            </div>

            <div class="small-12 medium-6 columns">
            <label>Local de lotação:
             <input type="text" name="func_lotacao" value="" size="32">
            </label>
            </div>          

            <div class="small-12 medium-4 columns">
            <label>Regime:
             <select name="func_regime">
              <option value="-1" <?php if (!(strcmp(-1, ""))) {echo "SELECTED";} ?>>ESCOLHA...</option>
              <option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>1 - EFETIVO</option>
              <option value="2" <?php if (!(strcmp(2, ""))) {echo "SELECTED";} ?>>2 - CONTRATADO</option>
            </select>
            </label>
            </div>
          
            <div class="small-12 medium-4 columns">
            <label>Grupo Sanguíneo:
             <select name="func_grupo_sanquineo">
              <option value="-1" <?php if (!(strcmp(-1, ""))) {echo "SELECTED";} ?>>ESCOLHA...</option>
              <option value="A" <?php if (!(strcmp("A", ""))) {echo "SELECTED";} ?>>A</option>
              <option value="B" <?php if (!(strcmp("B", ""))) {echo "SELECTED";} ?>>B</option>
              <option value="AB" <?php if (!(strcmp("AB", ""))) {echo "SELECTED";} ?>>AB</option>
              <option value="O" <?php if (!(strcmp("O", ""))) {echo "SELECTED";} ?>>O</option>
            </select>
            </label>
            </div>
          
            <div class="small-12 medium-4 columns">
            <label>Fator RH:
             <select name="func_fator_rh">
              <option value="-1" <?php if (!(strcmp(-1, ""))) {echo "SELECTED";} ?>>ESCOLHA...</option>
              <option value="+" <?php if (!(strcmp("+", ""))) {echo "SELECTED";} ?>>POSITIVO+</option>
              <option value="-" <?php if (!(strcmp("-", ""))) {echo "SELECTED";} ?>>NEGATIVO-</option>
            </select>
            </label>
            </div><hr>
          
            <div class="small-12 medium-4 columns">
            <label>E-mail:
             <input name="func_email" value="" size="32" type="email">
            </label>
            </div>
          
            <div class="small-12 medium-4 columns">
            <label>Telefone:
             <input type="text" name="func_telefone" value="" size="32" class="phone_with_ddd">
            </label>
            </div>
          
            <div class="small-12 medium-4 columns">
            <label>Celular:
             <input type="text" name="func_celular1" value="" size="32" class="celular">
            </label>
            </div>
          
            <div class="small-12 medium-4 columns">
            <label>Celular:
             <input type="text" name="func_celular2" value="" size="32" class="celular">
            </label>
            </div>
          
            <div class="small-12 medium-4 columns">
            <label>Agência:
             <input type="text" name="func_agencia_banco" value="" size="32">
            </label>
            </div>
          
            <div class="small-12 medium-4 columns">
            <label>Conta:
             <input type="text" name="func_conta_banco" value="" size="32">            
            </label>
            </div>
          
            <div class="small-12 medium-8 columns">
            <label>Nome do Banco:
             <input type="text" name="func_nome_banco" value="" size="32">            
            </label>
            </div>
          
            <div class="small-12 medium-4 columns">
            <label>Situação:
             <select name="func_situacao">
              <option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>1 - EM ATIVIDADE</option>
              <option value="2" <?php if (!(strcmp(2, ""))) {echo "SELECTED";} ?>>2 - AFASTADO</option>
            </select>           
            </label>
            </div>
          
            <div class="small-12 columns">
             <input type="submit" value="CADASTRAR" class="button"> <a href="funcionarioExibir.php" class="button alert">VOLTAR</a>
            </div>
          
        <input type="hidden" name="func_id_sec" value="<?php echo $row_UsuarioLogado['usu_sec']; ?>">
        <input type="hidden" name="MM_insert" value="form1">
        
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
?>
