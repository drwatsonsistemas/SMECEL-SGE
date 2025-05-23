<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php require_once('../../Connections/SmecelNovo.php'); ?>
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
  $insertSQL = sprintf("INSERT INTO smc_te_veiculo (te_cad_veiculo_id_sec, te_cad_veiculo_tipo, te_cad_veiculo_marca_id, te_cad_veiculo_modelo_id, te_cad_veiculo_placa, te_cad_veiculo_renavan, te_cad_veiculo_ano_fab, te_cad_veiculo_ano_modelo, te_cad_veiculo_chassi, te_cad_veiculo_tipo_frota, te_cad_veiculo_situacao, te_cad_veiculo_limite_passageiros, te_cad_veiculo_adaptado_pne, te_cad_veiculo_obs) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['te_cad_veiculo_id_sec'], "int"),
                       GetSQLValueString($_POST['te_cad_veiculo_tipo'], "int"),
                       GetSQLValueString($_POST['te_cad_veiculo_marca_id'], "int"),
                       GetSQLValueString($_POST['te_cad_veiculo_modelo_id'], "int"),
                       GetSQLValueString($_POST['te_cad_veiculo_placa'], "text"),
                       GetSQLValueString($_POST['te_cad_veiculo_renavan'], "text"),
                       GetSQLValueString($_POST['te_cad_veiculo_ano_fab'], "text"),
                       GetSQLValueString($_POST['te_cad_veiculo_ano_modelo'], "text"),
                       GetSQLValueString($_POST['te_cad_veiculo_chassi'], "text"),
                       GetSQLValueString($_POST['te_cad_veiculo_tipo_frota'], "text"),
                       GetSQLValueString($_POST['te_cad_veiculo_situacao'], "text"),
                       GetSQLValueString($_POST['te_cad_veiculo_limite_passageiros'], "text"),
                       GetSQLValueString($_POST['te_cad_veiculo_adaptado_pne'], "text"),
                       GetSQLValueString($_POST['te_cad_veiculo_obs'], "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());
  
    $insertGoTo = "veiculos.php?cadastrado";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form2")) {
  $updateSQL = sprintf("UPDATE smc_te_veiculo SET te_cad_veiculo_tipo=%s, te_cad_veiculo_marca_id=%s, te_cad_veiculo_modelo_id=%s, te_cad_veiculo_placa=%s, te_cad_veiculo_renavan=%s, te_cad_veiculo_ano_fab=%s, te_cad_veiculo_ano_modelo=%s, te_cad_veiculo_chassi=%s, te_cad_veiculo_tipo_frota=%s, te_cad_veiculo_situacao=%s, te_cad_veiculo_limite_passageiros=%s, te_cad_veiculo_adaptado_pne=%s, te_cad_veiculo_obs=%s WHERE te_cad_veiculo_id=%s",
                       GetSQLValueString($_POST['te_cad_veiculo_tipo'], "text"),
                       GetSQLValueString($_POST['te_cad_veiculo_marca_id'], "int"),
                       GetSQLValueString($_POST['te_cad_veiculo_modelo_id'], "int"),
                       GetSQLValueString($_POST['te_cad_veiculo_placa'], "text"),
                       GetSQLValueString($_POST['te_cad_veiculo_renavan'], "text"),
                       GetSQLValueString($_POST['te_cad_veiculo_ano_fab'], "text"),
                       GetSQLValueString($_POST['te_cad_veiculo_ano_modelo'], "text"),
                       GetSQLValueString($_POST['te_cad_veiculo_chassi'], "text"),
                       GetSQLValueString($_POST['te_cad_veiculo_tipo_frota'], "text"),
                       GetSQLValueString($_POST['te_cad_veiculo_situacao'], "text"),
                       GetSQLValueString($_POST['te_cad_veiculo_limite_passageiros'], "text"),
                       GetSQLValueString($_POST['te_cad_veiculo_adaptado_pne'], "text"),
                       GetSQLValueString($_POST['te_cad_veiculo_obs'], "text"),
                       GetSQLValueString($_POST['te_cad_veiculo_id'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

  $updateGoTo = "veiculos.php?editado";
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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_marca = "SELECT te_veiculos_marca_id, te_veiculos_marca_nome FROM smc_te_veiculos_marca ORDER BY te_veiculos_marca_nome ASC";
$marca = mysql_query($query_marca, $SmecelNovo) or die(mysql_error());
$row_marca = mysql_fetch_assoc($marca);
$totalRows_marca = mysql_num_rows($marca);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_marcaEdit = "SELECT te_veiculos_marca_id, te_veiculos_marca_nome FROM smc_te_veiculos_marca ORDER BY te_veiculos_marca_nome ASC";
$marcaEdit = mysql_query($query_marcaEdit, $SmecelNovo) or die(mysql_error());
$row_marcaEdit = mysql_fetch_assoc($marcaEdit);
$totalRows_marcaEdit = mysql_num_rows($marcaEdit);



mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Veiculos = "
SELECT 
te_cad_veiculo_id, te_cad_veiculo_id_sec, 
CASE te_cad_veiculo_tipo
WHEN 1 THEN 'ONIBUS'
WHEN 2 THEN 'MICRO-ONIBUS'
WHEN 3 THEN 'VANS/KOMBI'
WHEN 4 THEN 'BICICLETA'
WHEN 5 THEN 'TRAÇÃO ANIMAL'
WHEN 6 THEN 'BARCO/LANCHA'
WHEN 99 THEN 'OUTROS'
END AS te_cad_veiculo_tipo, 
te_cad_veiculo_marca_id, te_cad_veiculo_modelo_id, te_cad_veiculo_placa, 
te_cad_veiculo_renavan, te_cad_veiculo_ano_fab, te_cad_veiculo_ano_modelo, te_cad_veiculo_chassi, 
CASE te_cad_veiculo_tipo_frota
WHEN 1 THEN 'PRÓPRIA'
WHEN 2 THEN 'TERCEIRIZADA'
WHEN 3 THEN 'CEDIDA'
WHEN 99 THEN 'OUTROS'
END AS te_cad_veiculo_tipo_frota, 
te_cad_veiculo_situacao, te_cad_veiculo_limite_passageiros, te_cad_veiculo_adaptado_pne, te_cad_veiculo_obs 
FROM smc_te_veiculo
WHERE te_cad_veiculo_id_sec = '$row_Secretaria[sec_id]'
ORDER BY te_cad_veiculo_tipo ASC
";
$Veiculos = mysql_query($query_Veiculos, $SmecelNovo) or die(mysql_error());
$row_Veiculos = mysql_fetch_assoc($Veiculos);
$totalRows_Veiculos = mysql_num_rows($Veiculos);

$colname_VeiculosEditar = "-1";
if (isset($_GET['codigo'])) {
  $colname_VeiculosEditar = $_GET['codigo'];

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_VeiculosEditar = sprintf("
SELECT te_cad_veiculo_id, te_cad_veiculo_id_sec, te_cad_veiculo_tipo, te_cad_veiculo_marca_id, te_cad_veiculo_modelo_id, te_cad_veiculo_placa, 
te_cad_veiculo_renavan, te_cad_veiculo_ano_fab, te_cad_veiculo_ano_modelo, te_cad_veiculo_chassi, te_cad_veiculo_tipo_frota, 
te_cad_veiculo_situacao, te_cad_veiculo_limite_passageiros, te_cad_veiculo_adaptado_pne, te_cad_veiculo_obs, te_veiculos_modelo_id, te_veiculos_modelo_nome 
FROM smc_te_veiculo
INNER JOIN smc_te_veiculos_modelo ON te_veiculos_modelo_id = te_cad_veiculo_modelo_id 
WHERE te_cad_veiculo_id_sec = '$row_UsuarioLogado[usu_sec]' AND te_cad_veiculo_id = %s", GetSQLValueString($colname_VeiculosEditar, "int"));
$VeiculosEditar = mysql_query($query_VeiculosEditar, $SmecelNovo) or die(mysql_error());
$row_VeiculosEditar = mysql_fetch_assoc($VeiculosEditar);
$totalRows_VeiculosEditar = mysql_num_rows($VeiculosEditar);

mysql_free_result($VeiculosEditar);

if ($totalRows_VeiculosEditar  < 1) {
	$red = "index.php?erro&cod=veiculosfex11c";
	header(sprintf("Location: %s", $red));
	}

}

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
    <h1 class="ls-title-intro ls-ico-flag">VEÍCULOS</h1>
    <!-- CONTEUDO -->
    
    <?php if (isset($_GET["cadastrado"])) { ?>
      <div class="ls-alert-success">Veículo cadastrado com sucesso!</div>
      <?php } ?>
      
    <button data-ls-module="modal" data-target="#myAwesomeModal" class="ls-btn-primary ls-ico-plus">CADASTRAR VEÍCULO</button>
    <a href="impressao/rel_veiculos.php" class="ls-btn" target="_blank">IMPRIMIR</a>

    <hr>
    
    <?php if ($totalRows_Veiculos > 0) { ?>
    <table class="ls-table ls-sm-space">
      <thead>
        <tr>
          <th class="ls-txt-center1" width="40"></th>
          <th class="ls-txt-center1">TIPO</th>
          <th class="ls-txt-center1">PLACA</th>
          <th class="ls-txt-center1">LIMITE PASS.</th>
          <th class="ls-txt-center1">FROTA</th>
          <th class="ls-txt-center1" width="40"></th>
        </tr>
      </thead>
      <tbody>
        <?php $num = 1; do { ?>
          <tr>
            <td><strong><?php echo $num; $num++; ?></strong></td>
            <td><?php echo $row_Veiculos['te_cad_veiculo_tipo']; ?></td>
            <td class="ls-txt-center1"><?php echo $row_Veiculos['te_cad_veiculo_placa']; ?></td>
            <td class="ls-txt-center1"><?php echo $row_Veiculos['te_cad_veiculo_limite_passageiros']; ?></td>
            <td class="ls-txt-center1"><?php echo $row_Veiculos['te_cad_veiculo_tipo_frota']; ?></td>
            <td class="ls-txt-center1"><a href="veiculos.php?codigo=<?php echo $row_Veiculos['te_cad_veiculo_id']; ?>"><span class="ls-ico-edit-admin ls-ico-right"></span></a></td>
          </tr>
          <?php } while ($row_Veiculos = mysql_fetch_assoc($Veiculos)); ?>
      </tbody>
    </table>
    <?php } else { ?>
    <div class="ls-alert-info">Nenhum veículo cadastrado</div>
    <?php } ?>
    <!-- CONTEUDO --> 
  </div>
</main>
<div class="ls-modal" id="myAwesomeModalAtualizar">
  <div class="ls-modal-large">
    <div class="ls-modal-header"> <a href="pontos.php" data-dismiss="modal">&times;</a>
      <h4 class="ls-modal-title">ATUALIZAR VEÍCULO</h4>
    </div>
    <div class="ls-modal-body" id="myAwesomeModalAtualizar">
    <?php if (isset($_GET["editado"])) { ?>
      <div class="ls-alert-success">Dados atualizados com sucesso!</div>
      <?php } ?>
    <form method="post" name="form2" action="<?php echo $editFormAction; ?>" class="ls-form-horizontal">
      <label class="ls-label col-md-3 col-sm-12" required>
      <b class="ls-label-text">TIPO</b>
      <div class="ls-custom-select">
        <select name="te_cad_veiculo_tipo">
          <option value="1" <?php if (!(strcmp(1, htmlentities($row_VeiculosEditar['te_cad_veiculo_tipo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>1 - ONIBUS</option>
          <option value="2" <?php if (!(strcmp(2, htmlentities($row_VeiculosEditar['te_cad_veiculo_tipo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>2 - MICRO-ONIBUS</option>
          <option value="3" <?php if (!(strcmp(3, htmlentities($row_VeiculosEditar['te_cad_veiculo_tipo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>3 - VANS/KOMBI</option>
          <option value="4" <?php if (!(strcmp(4, htmlentities($row_VeiculosEditar['te_cad_veiculo_tipo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>4 - BICICLETA</option>
          <option value="5" <?php if (!(strcmp(5, htmlentities($row_VeiculosEditar['te_cad_veiculo_tipo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>5 - TRAÇÃO ANIMAL</option>
          <option value="6" <?php if (!(strcmp(6, htmlentities($row_VeiculosEditar['te_cad_veiculo_tipo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>6 - BARCO/LANCHA</option>
          <option value="99" <?php if (!(strcmp(99, htmlentities($row_VeiculosEditar['te_cad_veiculo_tipo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>99 - OUTROS</option>
        </select>
      </div>
      </label>
      <label class="ls-label col-md-4">
      <b class="ls-label-text">MARCA</b>
      <div class="ls-custom-select">
        <select name="te_cad_veiculo_marca_id" id="te_cad_veiculo_marca_id_edit" required>
          <?php do { ?>
          <option value="<?php echo $row_marcaEdit['te_veiculos_marca_id']?>" <?php if (!(strcmp($row_marcaEdit['te_veiculos_marca_id'], htmlentities($row_VeiculosEditar['te_cad_veiculo_marca_id'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>><?php echo $row_marcaEdit['te_veiculos_marca_nome']?></option>
          <?php } while ($row_marcaEdit = mysql_fetch_assoc($marcaEdit)); ?>
        </select>
      </div>
      </label>
      <label class="ls-label col-md-5">
      <b class="ls-label-text">MODELO</b>
      <div class="ls-custom-select">
        <select name="te_cad_veiculo_modelo_id" id="te_cad_veiculo_modelo_id_edit" required>
          <option value="<?php echo htmlentities($row_VeiculosEditar['te_cad_veiculo_modelo_id'], ENT_COMPAT, 'utf-8'); ?>"><?php echo $row_VeiculosEditar['te_veiculos_modelo_nome']?></option>
        </select>
      </div>
      </label>
      <label class="ls-label col-md-2 col-sm-12"> <b class="ls-label-text">PLACA</b>
        <input type="text" name="te_cad_veiculo_placa" value="<?php echo htmlentities($row_VeiculosEditar['te_cad_veiculo_placa'], ENT_COMPAT, 'utf-8'); ?>" size="32">
      </label>
      <label class="ls-label col-md-3 col-sm-12"> <b class="ls-label-text">RENAVAN</b>
        <input type="text" name="te_cad_veiculo_renavan" value="<?php echo htmlentities($row_VeiculosEditar['te_cad_veiculo_renavan'], ENT_COMPAT, 'utf-8'); ?>" size="32">
      </label>
      <label class="ls-label col-md-2 col-sm-12"> <b class="ls-label-text">ANO/FAB</b>
        <input type="text" name="te_cad_veiculo_ano_fab" value="<?php echo htmlentities($row_VeiculosEditar['te_cad_veiculo_ano_fab'], ENT_COMPAT, 'utf-8'); ?>" size="32">
      </label>
      <label class="ls-label col-md-2 col-sm-12"> <b class="ls-label-text">ANO/MOD</b>
        <input type="text" name="te_cad_veiculo_ano_modelo" value="<?php echo htmlentities($row_VeiculosEditar['te_cad_veiculo_ano_modelo'], ENT_COMPAT, 'utf-8'); ?>" size="32">
      </label>
      <label class="ls-label col-md-3 col-sm-12"> <b class="ls-label-text">CHASSI</b>
        <input type="text" name="te_cad_veiculo_chassi" value="<?php echo htmlentities($row_VeiculosEditar['te_cad_veiculo_chassi'], ENT_COMPAT, 'utf-8'); ?>" size="32">
      </label>
      <label class="ls-label col-md-4 col-sm-12">
      <b class="ls-label-text">FROTA</b>
      <div class="ls-custom-select">
        <select name="te_cad_veiculo_tipo_frota">
          <option value="1" <?php if (!(strcmp(1, htmlentities($row_VeiculosEditar['te_cad_veiculo_tipo_frota'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>1 - PRÓPRIA</option>
          <option value="2" <?php if (!(strcmp(2, htmlentities($row_VeiculosEditar['te_cad_veiculo_tipo_frota'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>2 - TERCEIRIZADA</option>
          <option value="3" <?php if (!(strcmp(3, htmlentities($row_VeiculosEditar['te_cad_veiculo_tipo_frota'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>3 - CEDIDA</option>
          <option value="99" <?php if (!(strcmp(99, htmlentities($row_VeiculosEditar['te_cad_veiculo_tipo_frota'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>99 - OUTRAS</option>
        </select>
      </div>
      </label>
      <label class="ls-label col-md-3 col-sm-12">
      <b class="ls-label-text">SITUAÇÃO</b>
      <div class="ls-custom-select">
        <select name="te_cad_veiculo_situacao">
          <option value="1" <?php if (!(strcmp(1, htmlentities($row_VeiculosEditar['te_cad_veiculo_situacao'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>1 - ATIVO</option>
          <option value="2" <?php if (!(strcmp(2, htmlentities($row_VeiculosEditar['te_cad_veiculo_situacao'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>2 - INATIVO</option>
        </select>
      </div>
      </label>
      <label class="ls-label col-md-2 col-sm-12"> <b class="ls-label-text">MAX. PASS.</b>
        <input type="text" name="te_cad_veiculo_limite_passageiros" value="<?php echo htmlentities($row_VeiculosEditar['te_cad_veiculo_limite_passageiros'], ENT_COMPAT, 'utf-8'); ?>" size="32">
      </label>
      <label class="ls-label col-md-3 col-sm-12">
      <b class="ls-label-text">ADAPT. P/ PNE's</b>
      <div class="ls-custom-select">
        <select name="te_cad_veiculo_adaptado_pne">
          <option value="">ESCOLHA</option>
          <option value="1" <?php if (!(strcmp(1, htmlentities($row_VeiculosEditar['te_cad_veiculo_adaptado_pne'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>1 - SIM</option>
          <option value="2" <?php if (!(strcmp(2, htmlentities($row_VeiculosEditar['te_cad_veiculo_adaptado_pne'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>2 - NÃO</option>
        </select>
      </div>
      </label>
      <label class="ls-label col-md-12"> <b class="ls-label-text">OBSERVAÇÕES</b>
        <textarea name="te_cad_veiculo_obs" cols="50" rows="5"><?php echo htmlentities($row_VeiculosEditar['te_cad_veiculo_obs'], ENT_COMPAT, 'utf-8'); ?></textarea>
      </label>
      <input type="hidden" name="MM_update" value="form2">
      <input type="hidden" name="te_cad_veiculo_id" value="<?php echo $row_VeiculosEditar['te_cad_veiculo_id']; ?>">
      </div>
      <div class="ls-modal-footer">
        <input type="submit" value="ATUALIZAR" class="ls-btn-primary">
        <a href="veiculos.php" class="ls-btn ls-float-right" data-dismiss="modal">FECHAR</a> </div>
    </form>
  </div>
</div>
<!-- /.modal -->

<div class="ls-modal" data-modal-blocked1 id="myAwesomeModal">
  <div class="ls-modal-large">
  <div class="ls-modal-header">
    <button data-dismiss="modal">&times;</button>
    <h4 class="ls-modal-title">NOVO VEÍCULO</h4>
  </div>
  <div class="ls-modal-body" id="myModalBody">
  <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form-horizontal">
    <fieldset>
      <label class="ls-label col-md-3 col-sm-12">
      <b class="ls-label-text">TIPO</b>
      <div class="ls-custom-select">
        <select name="te_cad_veiculo_tipo" required>
          <option value="" <?php if (!(strcmp(-1, ""))) {echo "SELECTED";} ?>>ESCOLHA</option>
          <option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>1 - ONIBUS</option>
          <option value="2" <?php if (!(strcmp(2, ""))) {echo "SELECTED";} ?>>2 - MICRO-ONIBUS</option>
          <option value="3" <?php if (!(strcmp(3, ""))) {echo "SELECTED";} ?>>3 - VANS/KOMBI</option>
          <option value="4" <?php if (!(strcmp(4, ""))) {echo "SELECTED";} ?>>4 - BICICLETA</option>
          <option value="5" <?php if (!(strcmp(5, ""))) {echo "SELECTED";} ?>>5 - TRAÇÃO ANIMAL</option>
          <option value="6" <?php if (!(strcmp(6, ""))) {echo "SELECTED";} ?>>6 - BARCO/LANCHA</option>
          <option value="99" <?php if (!(strcmp(99, ""))) {echo "SELECTED";} ?>>99 - OUTROS</option>
        </select>
      </div>
      </label>
      <label class="ls-label col-md-4">
      <b class="ls-label-text">MARCA</b>
      <div class="ls-custom-select">
        <select name="te_cad_veiculo_marca_id" id="te_cad_veiculo_marca_id" required>
          <option value="" <?php if (!(strcmp(-1, ""))) {echo "SELECTED";} ?>>ESCOLHA</option>
          <?php do {  ?>
          <option value="<?php echo $row_marca['te_veiculos_marca_id']?>" ><?php echo $row_marca['te_veiculos_marca_nome']?></option>
          <?php  } while ($row_marca = mysql_fetch_assoc($marca));  ?>
          <option value="9999" <?php if (!(strcmp(9999, ""))) {echo "SELECTED";} ?>>OUTROS</option>
        </select>
      </div>
      </label>
      <label class="ls-label col-md-5">
      <b class="ls-label-text">MODELO</b>
      <div class="ls-custom-select">
        <select name="te_cad_veiculo_modelo_id" id="te_cad_veiculo_modelo_id" required>
          <option value="">SELECIONE A MARCA</option>
        </select>
      </div>
      </label>
      <label class="ls-label col-md-2 col-sm-12"> <b class="ls-label-text">PLACA</b>
        <input type="text" name="te_cad_veiculo_placa" value="" size="32" required>
      </label>
      <label class="ls-label col-md-3 col-sm-12"> <b class="ls-label-text">RENAVAN</b>
        <input type="text" name="te_cad_veiculo_renavan" value="" size="32">
      </label>
      <label class="ls-label col-md-2 col-sm-12"> <b class="ls-label-text">ANO/FAB</b>
        <input type="text" name="te_cad_veiculo_ano_fab" value="" size="32">
      </label>
      <label class="ls-label col-md-2 col-sm-12"> <b class="ls-label-text">ANO/MOD</b>
        <input type="text" name="te_cad_veiculo_ano_modelo" value="" size="32">
      </label>
      <label class="ls-label col-md-3 col-sm-12"> <b class="ls-label-text">CHASSI</b>
        <input type="text" name="te_cad_veiculo_chassi" value="" size="32">
      </label>
      <label class="ls-label col-md-4 col-sm-12">
      <b class="ls-label-text">FROTA</b>
      <div class="ls-custom-select">
        <select name="te_cad_veiculo_tipo_frota" required>
          <option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>1 - PROPRIA</option>
          <option value="2" <?php if (!(strcmp(2, ""))) {echo "SELECTED";} ?>>2  - TERCEIRIZADA</option>
          <option value="3" <?php if (!(strcmp(3, ""))) {echo "SELECTED";} ?>>3  - CEDIDA</option>
          <option value="99" <?php if (!(strcmp(99, ""))) {echo "SELECTED";} ?>>99 - OUTROS</option>
        </select>
      </div>
      </label>
      <label class="ls-label col-md-3 col-sm-12">
      <b class="ls-label-text">SITUAÇÃO</b>
      <div class="ls-custom-select">
        <select name="te_cad_veiculo_situacao" required>
          <option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>1 - ATIVO</option>
          <option value="2" <?php if (!(strcmp(2, ""))) {echo "SELECTED";} ?>>2 - INATIVO</option>
        </select>
      </div>
      </label>
      <label class="ls-label col-md-2 col-sm-12"> <b class="ls-label-text">MAX. PASS.</b>
        <input type="text" name="te_cad_veiculo_limite_passageiros" value="" size="32">
      </label>
      <label class="ls-label col-md-3 col-sm-12">
      <b class="ls-label-text">ADAPT. P/ PNE's</b>
      <div class="ls-custom-select">
        <select name="te_cad_veiculo_adaptado_pne">
          <option value="" <?php if (!(strcmp(-1, ""))) {echo "SELECTED";} ?>>ESCOLHA</option>
          <option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>1 - SIM</option>
          <option value="2" <?php if (!(strcmp(2, ""))) {echo "SELECTED";} ?>>2 - NÃO</option>
        </select>
      </div>
      </label>
      <label class="ls-label col-md-12"> <b class="ls-label-text">OBSERVAÇÕES</b>
        <textarea name="te_cad_veiculo_obs" cols="50" rows="5"></textarea>
      </label>
      <input type="hidden" name="te_cad_veiculo_id_sec" value="<?php echo $row_Secretaria['sec_id']; ?>">
      <input type="hidden" name="MM_insert" value="form1">
    </fieldset>
    </div>
    <div class="ls-modal-footer"> <a class="ls-btn ls-float-right" data-dismiss="modal">FECHAR</a>
      <input type="submit" value="CADASTRAR VEÍCULO" class="ls-btn-primary">
    </div>
    </div>
  </form>
</div>
<!-- /.modal -->
<?php include_once "notificacoes.php"; ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script> 
<script type="text/javascript">
    $(document).ready(function(){
        $('#te_cad_veiculo_marca_id').change(function(){
            $('#te_cad_veiculo_modelo_id').load('marcas.php?marca='+$('#te_cad_veiculo_marca_id').val());
      $("#te_cad_veiculo_modelo_id").focus();
        });
    });
    </script> 
<script type="text/javascript">
    $(document).ready(function(){
        $('#te_cad_veiculo_marca_id_edit').change(function(){
            $('#te_cad_veiculo_modelo_id_edit').load('marcasEdit.php?marca='+$('#te_cad_veiculo_marca_id_edit').val());
      $("#te_cad_veiculo_modelo_id_edit").focus();
        });
    });
    </script>
<?php if (isset($_GET["codigo"])) { ?>
  <script>
		locastyle.modal.open("#myAwesomeModalAtualizar");
    </script>
  <?php } ?>
</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Veiculos);

mysql_free_result($Secretaria);

mysql_free_result($marca);

mysql_free_result($marcaEdit);

?>