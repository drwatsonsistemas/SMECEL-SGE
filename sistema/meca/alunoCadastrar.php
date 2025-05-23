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
  $insertSQL = sprintf("INSERT INTO smc_aluno (aluno_cod_inep, aluno_cpf, aluno_nome, aluno_nascimento, aluno_filiacao1, aluno_filiacao2, aluno_sexo, aluno_raca, aluno_nacionalidade, aluno_uf_nascimento, aluno_municipio_nascimento, aluno_aluno_com_deficiencia, aluno_nis, aluno_identidade, aluno_emissor, aluno_uf_emissor, aluno_data_espedicao, aluno_tipo_certidao, aluno_termo, aluno_folhas, aluno_livro, aluno_emissao_certidao, aluno_uf_cartorio, aluno_mucicipio_cartorio, aluno_nome_cartorio, aluno_num_matricula_modelo_novo, aluno_localizacao, aluno_cep, aluno_endereco, aluno_numero, aluno_complemento, aluno_bairro, aluno_uf, aluno_municipio, aluno_hash) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['aluno_cod_inep'], "text"),
                       GetSQLValueString($_POST['aluno_cpf'], "text"),
                       GetSQLValueString($_POST['aluno_nome'], "text"),
                       GetSQLValueString($_POST['aluno_nascimento'], "date"),
                       GetSQLValueString($_POST['aluno_filiacao1'], "text"),
                       GetSQLValueString($_POST['aluno_filiacao2'], "text"),
                       GetSQLValueString($_POST['aluno_sexo'], "int"),
                       GetSQLValueString($_POST['aluno_raca'], "int"),
                       GetSQLValueString($_POST['aluno_nacionalidade'], "int"),
                       GetSQLValueString($_POST['aluno_uf_nascimento'], "int"),
                       GetSQLValueString($_POST['aluno_municipio_nascimento'], "int"),
                       GetSQLValueString($_POST['aluno_aluno_com_deficiencia'], "int"),
                       GetSQLValueString($_POST['aluno_nis'], "text"),
                       GetSQLValueString($_POST['aluno_identidade'], "text"),
                       GetSQLValueString($_POST['aluno_emissor'], "text"),
                       GetSQLValueString($_POST['aluno_uf_emissor'], "int"),
                       GetSQLValueString($_POST['aluno_data_espedicao'], "date"),
                       GetSQLValueString($_POST['aluno_tipo_certidao'], "int"),
                       GetSQLValueString($_POST['aluno_termo'], "text"),
                       GetSQLValueString($_POST['aluno_folhas'], "text"),
                       GetSQLValueString($_POST['aluno_livro'], "text"),
                       GetSQLValueString($_POST['aluno_emissao_certidao'], "date"),
                       GetSQLValueString($_POST['aluno_uf_cartorio'], "int"),
                       GetSQLValueString($_POST['aluno_mucicipio_cartorio'], "int"),
                       GetSQLValueString($_POST['aluno_nome_cartorio'], "text"),
                       GetSQLValueString($_POST['aluno_num_matricula_modelo_novo'], "text"),
                       GetSQLValueString($_POST['aluno_localizacao'], "int"),
                       GetSQLValueString($_POST['aluno_cep'], "text"),
                       GetSQLValueString($_POST['aluno_endereco'], "text"),
                       GetSQLValueString($_POST['aluno_numero'], "text"),
                       GetSQLValueString($_POST['aluno_complemento'], "text"),
                       GetSQLValueString($_POST['aluno_bairro'], "text"),
                       GetSQLValueString($_POST['aluno_uf'], "int"),
                       GetSQLValueString($_POST['aluno_municipio'], "int"),
                       GetSQLValueString($_POST['aluno_hash'], "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());
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
		<h1>Cadastro de Aluno</h1>
		<form method="post" name="form1" action="<?php echo $editFormAction; ?>">
			<hr>
			<strong>IDENTIFICAÇÃO</strong>
			<hr>
			<div class="row">
				<div class="small-12 large-6 columns">
					<label>Identificação única (código gerado pelo Inep)
						<input type="text" name="aluno_cod_inep" value="" size="32">
					</label>
				</div>
				<div class="small-12 large-6 columns">
					<label>Número do CPF
						<input type="text" name="aluno_cpf" value="" size="32">
					</label>
				</div>
			</div>
			<div class="row">
				<div class="small-12 large-8 columns">
					<label>Nome completo
						<input type="text" name="aluno_nome" value="" size="32">
					</label>
				</div>
				<div class="small-12 large-4 columns">
					<label>Nascimento
						<input type="text" name="aluno_nascimento" value="" size="32">
					</label>
				</div>
			</div>
			<div class="row">
				<div class="small-12 large-6 columns">
					<label>Filiação 1
						<input type="text" name="aluno_filiacao1" value="" size="32">
					</label>
				</div>
				<div class="small-12 large-6 columns">
					<label>Filiação 2
						<input type="text" name="aluno_filiacao2" value="" size="32">
					</label>
				</div>
			</div>
			<div class="row">
				<div class="small-12 large-3 columns">
					<label>Sexo
						<select name="aluno_sexo">
							<option value="-1" <?php if (!(strcmp(-1, ""))) {echo "SELECTED";} ?>>Escolha...</option>
							<option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>Masculino</option>
							<option value="2" <?php if (!(strcmp(2, ""))) {echo "SELECTED";} ?>>Feminino</option>
						</select>
					</label>
				</div>
				<div class="small-12 large-4 columns">
					<label>Cor/Raça
						<select name="aluno_raca">
							<option value="-1" <?php if (!(strcmp(-1, ""))) {echo "SELECTED";} ?>>Escolha...</option>
							<option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>Branca</option>
							<option value="2" <?php if (!(strcmp(2, ""))) {echo "SELECTED";} ?>>Preta</option>
							<option value="3" <?php if (!(strcmp(3, ""))) {echo "SELECTED";} ?>>Parda</option>
							<option value="4" <?php if (!(strcmp(4, ""))) {echo "SELECTED";} ?>>Amarela</option>
							<option value="5" <?php if (!(strcmp(5, ""))) {echo "SELECTED";} ?>>Indígena</option>
							<option value="6" <?php if (!(strcmp(6, ""))) {echo "SELECTED";} ?>>Não declarada</option>
						</select>
					</label>
				</div>
				<div class="small-12 large-5 columns">
					<label>Nacionalidade
						<select name="aluno_nacionalidade">
							<option value="1" <?php if (!(strcmp(1, 1))) {echo "SELECTED";} ?>>Brasileira</option>
							<option value="2" <?php if (!(strcmp(2, 1))) {echo "SELECTED";} ?>>Brasileira - Nascido no exterior ou naturalizado</option>
							<option value="3" <?php if (!(strcmp(3, 1))) {echo "SELECTED";} ?>>Estrangeiro</option>
						</select>
					</label>
				</div>
			</div>
			<br>
			<div class="row">
				<div class="small-12 large-6 columns">
					<label>UF de nascimento
						<select name="aluno_uf_nascimento">
							<option value="BA" <?php if (!(strcmp("BA", ""))) {echo "SELECTED";} ?>>BA</option>
						</select>
					</label>
				</div>
				<div class="small-12 large-6 columns">
					<label>Município de nascimento
						<select name="aluno_municipio_nascimento">
							<option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>ITAGIMIRIM</option>
						</select>
					</label>
				</div>
			</div>
			<br>
			<div class="row">
				<div class="small-12 columns">
					<label>Aluno com deficiência, transtorno global do desenvolvimento ou altas habilidades/superdotação
						<select name="aluno_aluno_com_deficiencia">
							<option value="-1" <?php if (!(strcmp(-1, ""))) {echo "SELECTED";} ?>>Escolha...</option>
							<option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>Sim</option>
							<option value="2" <?php if (!(strcmp(2, ""))) {echo "SELECTED";} ?>>Não</option>
						</select>
					</label>
				</div>
			</div>
			<hr>
			<strong>DOCUMENTO</strong>
			<hr>
			<div class="row">
				<div class="small-12 columns">
					<label>Número de Identificação Social (NIS)
						<input type="text" name="aluno_nis" value="" size="32">
					</label>
				</div>
			</div>
			<div class="row">
				<div class="small-12 large-4 columns">
					<label>Número da identidade
						<input type="text" name="aluno_identidade" value="" size="32">
					</label>
				</div>
				<div class="small-12 large-3 columns">
					<label>Órgão emissor
						<input type="text" name="aluno_emissor" value="" size="32">
					</label>
				</div>
				<div class="small-12 large-2 columns">
					<label>UF emissor
						<select name="aluno_uf_emissor">
							<option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>BA</option>
						</select>
					</label>
				</div>
				<div class="small-12 large-3 columns">
					<label>Data espedição
						<input type="text" name="aluno_data_espedicao" value="" size="32">
					</label>
				</div>
			</div>
			<div class="row">
				<div class="small-12 large-3 columns">
					<label>Tipo de certidão
						<select name="aluno_tipo_certidao">
							<option value="-1" <?php if (!(strcmp(-1, ""))) {echo "SELECTED";} ?>>Escolha...</option>
							<option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>Modelo antigo</option>
							<option value="2" <?php if (!(strcmp(2, ""))) {echo "SELECTED";} ?>>Modelo novo</option>
						</select>
					</label>
				</div>
				<div class="small-12 large-2 columns">
					<label>Termos
						<input type="text" name="aluno_termo" value="" size="32">
					</label>
				</div>
				<div class="small-12 large-2 columns">
					<label>Folhas
						<input type="text" name="aluno_folhas" value="" size="32">
					</label>
				</div>
				<div class="small-12 large-2 columns">
					<label>Livro
						<input type="text" name="aluno_livro" value="" size="32">
					</label>
				</div>
				<div class="small-12 large-3 columns">
					<label>Emissão
						<input type="text" name="aluno_emissao_certidao" value="" size="32">
					</label>
				</div>
			</div>
			<div class="row">
				<div class="small-12 large-2 columns">
					<label>Cartório
						<select name="aluno_uf_cartorio">
							<option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>BA</option>
						</select>
					</label>
				</div>
				<div class="small-12 large-4 columns">
					<label>Município
						<select name="aluno_mucicipio_cartorio">
							<option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>Itagimirim</option>
						</select>
					</label>
				</div>
				<div class="small-12 large-6 columns">
					<label>Nome de Cartório
						<input type="text" name="aluno_nome_cartorio" value="" size="32">
					</label>
				</div>
			</div>
			<div class="row">
				<div class="small-12 columns">
					<label>Matrícula modelo novo
						<input type="text" name="aluno_num_matricula_modelo_novo" value="" size="32">
					</label>
				</div>
			</div>
			<hr>
			<strong>LOCALIZAÇÃO RESIDENCIAL</strong>
			<hr>			
			<div class="row">
				<div class="small-12 large-2 columns">
					<label>Localização
						<select name="aluno_localizacao">
							<option value="-1" <?php if (!(strcmp(-1, ""))) {echo "SELECTED";} ?>>Escolha...</option>
							<option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>Urbana</option>
							<option value="2" <?php if (!(strcmp(2, ""))) {echo "SELECTED";} ?>>Rural</option>
						</select>
					</label>
				</div>
				<div class="small-12 large-2 columns">
					<label>CEP
						<input type="text" name="aluno_cep" value="" size="32">
					</label>
				</div>
				<div class="small-12 large-6 columns">
					<label>Endereço
						<input type="text" name="aluno_endereco" value="" size="32">
					</label>
				</div>
				<div class="small-12 large-2 columns">
					<label>Número
						<input type="text" name="aluno_numero" value="" size="32">
					</label>
				</div>
			</div>
			<div class="row">
				<div class="small-12 large-3 columns">
					<label>Complemento
						<input type="text" name="aluno_complemento" value="" size="32">
					</label>
				</div>
				<div class="small-12 large-3 columns">
					<label>Bairro
						<input type="text" name="aluno_bairro" value="" size="32">
					</label>
				</div>
				<div class="small-12 large-2 columns">
					<label>UF
						<select name="aluno_uf">
							<option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>BA</option>
						</select>
					</label>
				</div>
				<div class="small-12 large-4 columns">
					<label>Município
						<select name="aluno_municipio">
							<option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>Itagimirim</option>
						</select>
					</label>
				</div>
			</div>
			<br>
			<div class="row">
				<div class="small-12 columns">
					<input type="submit" value="CADASTRAR ALUNO" class="button">
				</div>
			</div>
			<input type="hidden" name="aluno_hash" value="">
			<input type="hidden" name="MM_insert" value="form1">
		</form>
		<p>&nbsp;</p>
	</div>
</div>
<script src="../../js/vendor/jquery.js"></script> 
<script src="../../js/foundation.min.js"></script> 
<script src="js/foundation/foundation.dropdown.js"></script> 
<script>
    $(document).foundation();
  </script>
</body>
</html>
<?php
mysql_free_result($UsuarioLogado);
?>
