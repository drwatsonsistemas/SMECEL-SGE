<?php require_once('../../../Connections/SmecelNovo.php'); ?>
<?php require_once('../../../Connections/SmecelNovo.php'); ?>
<?php require_once('../../../Connections/SmecelNovo.php'); ?>
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
	
  $logoutGoTo = "../../../index.php?exit";
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

$MM_restrictGoTo = "../../../index.php?acessorestrito";
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
  
    $insertGoTo = "../veiculos.php?cadastrado";
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

  $updateGoTo = "../veiculos.php?editado";
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
require_once('../funcoes/anoLetivo.php');

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
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
<link rel="stylesheet" type="text/css" href="//assets.locaweb.com.br/locastyle/edge/stylesheets/locastyle.css">
<script src="//assets.locaweb.com.br/locastyle/edge/javascripts/locastyle.js"></script>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="stylesheet" type="text/css" href="../css/impressao.css">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<body onload="self.print();">

    <table class="bordasimples1" width="100%">
    	<tr>
        	<td class="ls-txt-center" width="60"></td>
        	<td class="ls-txt-center">
              	<?php if ($row_Secretaria['sec_logo'] <> "") { ?>
				  <img src="../../../img/logo/secretaria/<?php echo $row_Secretaria['sec_logo']; ?>" alt="Logo da <?php echo $row_Secretaria['sec_nome']; ?>" title="Logo da <?php echo $row_EscolaLogada['sec_nome']; ?>"  width="60" />
				<?php } else { ?>
				  <img src="../../../img/brasao_republica.png" width="60">
				<?php } ?>
              <h3><?php echo $row_Secretaria['sec_prefeitura']; ?></h3>
              <?php echo $row_Secretaria['sec_nome']; ?>
            </td>
        	<td class="ls-txt-center" width="60"></td>
        </tr>
    </table>
    <br>
    
    <h2 class="ls-txt-center">RELATÓRIO</h2>
    <br>
    <h3 class="ls-txt-center">RELAÇÃO DE FROTA</h3>
    <br>

    
    <?php if ($totalRows_Veiculos > 0) { ?>
    <table class="bordasimples" width="100%">
      <thead>
        <tr>
          <th class="ls-txt-center1" width="40"></th>
          <th class="ls-txt-center1">TIPO</th>
          <th class="ls-txt-center1">PLACA</th>
          <th class="ls-txt-center1">LIMITE PASS.</th>
          <th class="ls-txt-center1">FROTA</th>
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
          </tr>
          <?php } while ($row_Veiculos = mysql_fetch_assoc($Veiculos)); ?>
      </tbody>
    </table>
    <?php } else { ?>
    <div class="ls-alert-info">Nenhum veículo cadastrado</div>
    <?php } ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="https://assets.locaweb.com.br/locastyle/3.8.4/javascripts/locastyle.js" type="text/javascript"></script> 
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