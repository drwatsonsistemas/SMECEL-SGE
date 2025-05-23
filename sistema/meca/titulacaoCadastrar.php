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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO smc_titulacao (titulacao_func_id, titulacao_tipo, titulacao_id_formacao, titulacao_horas, titulacao_data_inicio, titulacao_data_final, titulacao_data_entrega, titulacao_observacao) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['titulacao_func_id'], "int"),
                       GetSQLValueString($_POST['titulacao_tipo'], "int"),
                       GetSQLValueString($_POST['titulacao_id_formacao'], "int"),
                       GetSQLValueString($_POST['titulacao_horas'], "text"),
                       GetSQLValueString(inverteData($_POST['titulacao_data_inicio']), "date"),
                       GetSQLValueString(inverteData($_POST['titulacao_data_final']), "date"),
                       GetSQLValueString(inverteData($_POST['titulacao_data_entrega']), "date"),
                       GetSQLValueString($_POST['titulacao_observacao'], "text"));

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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_formacao = "SELECT formacao_id, formacao_nome, formacao_descricao FROM smc_formacao ORDER BY formacao_nome ASC";
$formacao = mysql_query($query_formacao, $SmecelNovo) or die(mysql_error());
$row_formacao = mysql_fetch_assoc($formacao);
$totalRows_formacao = mysql_num_rows($formacao);

$colname_Funcionario = "-1";
if (isset($_GET['c'])) {
  $colname_Funcionario = $_GET['c'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Funcionario = sprintf("SELECT func_id, func_id_sec, func_nome, func_mae, func_pai, func_data_nascimento, func_uf_nascimento, func_municipio_nascimento, func_estado_civil, func_sexo, func_escolaridade, func_cpf, func_rg_numero, func_rg_emissor, func_titulo, func_titulo_secao, func_titulo_zona, func_pis, func_cnh_num, func_categoria, func_ctps, func_ctps_serie, func_reservista, func_endereco, func_endereco_numero, func_endereco_bairro, func_endereco_cep, func_endereco_uf, func_endereco_cidade, func_matricula, func_admissao, func_decreto, func_lotacao, func_cargo, func_regime, func_grupo_sanquineo, func_fator_rh, func_email, func_telefone, func_celular1, func_celular2, func_agencia_banco, func_conta_banco, func_nome_banco, func_area_concurso, func_formacao, func_situacao, func_foto FROM smc_func WHERE func_id = %s", GetSQLValueString($colname_Funcionario, "int"));
$Funcionario = mysql_query($query_Funcionario, $SmecelNovo) or die(mysql_error());
$row_Funcionario = mysql_fetch_assoc($Funcionario);
$totalRows_Funcionario = mysql_num_rows($Funcionario);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Titulos = "SELECT titulacao_id, titulacao_func_id, titulacao_tipo, titulacao_id_formacao, titulacao_horas, titulacao_data_inicio, titulacao_data_final, titulacao_data_entrega, titulacao_observacao, formacao_id, formacao_nome FROM smc_titulacao INNER JOIN smc_formacao ON  formacao_id = titulacao_id_formacao WHERE titulacao_func_id = '$row_Funcionario[func_id]' ORDER BY titulacao_tipo DESC";
$Titulos = mysql_query($query_Titulos, $SmecelNovo) or die(mysql_error());
$row_Titulos = mysql_fetch_assoc($Titulos);
$totalRows_Titulos = mysql_num_rows($Titulos);
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
    
    <?php if ($totalRows_Funcionario == 0) {?>
    
    <hr>
    
    <div data-alert class="alert-box alert round">
 	 Funcionário não encontrado. <a href="funcionarioExibir.php" class="label">Voltar</a>
 	<a href="#" class="close">&times;</a>
	</div>
       
    <?php } else { ?>

    
    
      <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
      
      <fieldset>
      <legend>Cadastro de Formação</legend>

           <div class="small-12 columns">
          <label><span class="label">FUNCIONARIO:</span><h1><?php echo $row_Funcionario['func_nome']; ?></h1>
          </label>
          </div>

      
          <div class="small-12 columns">
          <p></p>
          <label>TIPO	
            <select name="titulacao_tipo" required>
	          <option value="">ESCOLHA...</option>
              <option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>GRADUAÇÃO</option>
              <option value="2" <?php if (!(strcmp(2, ""))) {echo "SELECTED";} ?>>PÓS-GRADUAÇÃO</option>
              <option value="3" <?php if (!(strcmp(3, ""))) {echo "SELECTED";} ?>>MESTRADO</option>
              <option value="4" <?php if (!(strcmp(4, ""))) {echo "SELECTED";} ?>>DOUTORADO</option>
            </select>
          </label>
          </div>

          <div class="small-12 columns">
          <p></p>
          <label>FORMAÇÃO	
            <select name="titulacao_id_formacao" required>
             <option value="" selected>ESCOLHA...</option>
		    <?php do { ?>
				<option value="<?php echo $row_formacao['formacao_id']?>" ><?php echo $row_formacao['formacao_nome']?></option>
			  <?php	} while ($row_formacao = mysql_fetch_assoc($formacao));  ?>
            </select>
            <a href="formacaoCadastrarNovo.php?c=<?php echo $row_Funcionario['func_id']; ?>">Cadastrar formação</a>
          </label>
          <p></p>
          </div>

        <div class="small-12 medium-6 large-3 columns">
          <label>TOTAL DE HORAS	
            <input type="text" name="titulacao_horas" value="" size="32">
          </label>
          </div>
            
        <div class="small-12 medium-6 large-3 columns">
          <label>DATA DE INÍCIO	            
            <input type="text" name="titulacao_data_inicio" value="" size="32" class="date">
          </label>
          </div>
           
        <div class="small-12 medium-6 large-3 columns">
          <label>DATA DE CONCLUSÃO	
            <input type="text" name="titulacao_data_final" value="" size="32" class="date">
          </label>
          </div>
            
        <div class="small-12 medium-6 large-3 columns">
          <label>DATA DE ENTREGA	
            <input type="text" name="titulacao_data_entrega" value="" size="32" class="date">
          </label>
          </div>
            
           <div class="small-12 columns">
          <label>OBSERVAÇÃO	
            <textarea name="titulacao_observacao" cols="50" rows="5"></textarea>
          </label>
          </div>
            
           <div class="small-12 columns">
           <p></p>
            <input type="submit" value="INSERIR" class="button"> <a href="funcionarioExibir.php" class="button alert">VOLTAR</a>
          </div>
          
        <input type="hidden" name="titulacao_func_id" value="<?php echo $row_Funcionario['func_id']; ?>">
        <input type="hidden" name="MM_insert" value="form1">
      </fieldset>
      </form>
      
      <?php } ?>
      
      <p>&nbsp;</p>
	</div>
</div>

<div class="row">
	<div class="small-12 columns">
    
    <?php if ($totalRows_Titulos > 0) { // Show if recordset not empty ?>

      <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <thead>
        <tr>
          <td>TIPO</td>
          <td>FORMAÇÃO</td>
          <td>CONCLUSÃO</td>
        </tr>
        </thead>
      <?php do { ?>
          <tr>
            <td><?php 
			
			switch ($row_Titulos['titulacao_tipo']) {
				case 1:
					echo "GRADUAÇÃO";
					break;
				case 2:
					echo "PÓS-GRADUAÇÃO";
					break;
				case 3:
					echo "MESTRADO";
					break;
				case 4:
					echo "DOUTORADO";
					break;
			}
			 
			 ?></td>
            <td><?php echo $row_Titulos['formacao_nome']; ?></td>
            <td><?php echo inverteData($row_Titulos['titulacao_data_final']); ?></td>
          </tr>
          <?php } while ($row_Titulos = mysql_fetch_assoc($Titulos)); ?>
      </table>

  <?php } else { ?>
  
  <span class="panel">Nenhum título cadastrado</span>
  
  <?php } // Show if recordset not empty ?>
      
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
});
</script>
</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($formacao);

mysql_free_result($Funcionario);

mysql_free_result($Titulos);
?>
