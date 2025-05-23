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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO smc_te_ponto (te_ponto_id_sec, te_ponto_descricao, te_ponto_endereco, te_ponto_num, te_ponto_bairro, te_ponto_latitude, te_ponto_longitude, te_ponto_obs) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['te_ponto_id_sec'], "int"),
                       GetSQLValueString($_POST['te_ponto_descricao'], "text"),
                       GetSQLValueString($_POST['te_ponto_endereco'], "text"),
                       GetSQLValueString($_POST['te_ponto_num'], "text"),
                       GetSQLValueString($_POST['te_ponto_bairro'], "text"),
                       GetSQLValueString($_POST['te_ponto_latitude'], "text"),
                       GetSQLValueString($_POST['te_ponto_longitude'], "text"),
                       GetSQLValueString($_POST['te_ponto_obs'], "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

  $insertGoTo = "../pontos.php?cadastrado";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form2")) {
  $updateSQL = sprintf("UPDATE smc_te_ponto SET te_ponto_descricao=%s, te_ponto_endereco=%s, te_ponto_num=%s, te_ponto_bairro=%s, te_ponto_latitude=%s, te_ponto_longitude=%s, te_ponto_obs=%s WHERE te_ponto_id=%s",
                       GetSQLValueString($_POST['te_ponto_descricao'], "text"),
                       GetSQLValueString($_POST['te_ponto_endereco'], "text"),
                       GetSQLValueString($_POST['te_ponto_num'], "text"),
                       GetSQLValueString($_POST['te_ponto_bairro'], "text"),
                       GetSQLValueString($_POST['te_ponto_latitude'], "text"),
                       GetSQLValueString($_POST['te_ponto_longitude'], "text"),
                       GetSQLValueString($_POST['te_ponto_obs'], "text"),
                       GetSQLValueString($_POST['te_ponto_id'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

  $updateGoTo = "../pontos.php?editado";
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

$colname_editarPonto = "-1";
if (isset($_GET['ponto_edita'])) {
  $colname_editarPonto = $_GET['ponto_edita'];

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_editarPonto = sprintf("
SELECT te_ponto_id, te_ponto_id_sec, te_ponto_descricao, te_ponto_endereco, te_ponto_num, te_ponto_bairro, te_ponto_latitude, te_ponto_longitude, te_ponto_obs 
FROM smc_te_ponto 
WHERE te_ponto_id_sec = '$row_Secretaria[sec_id]' AND te_ponto_id = %s", GetSQLValueString($colname_editarPonto, "int"));
$editarPonto = mysql_query($query_editarPonto, $SmecelNovo) or die(mysql_error());
$row_editarPonto = mysql_fetch_assoc($editarPonto);
$totalRows_editarPonto = mysql_num_rows($editarPonto);

if ($totalRows_editarPonto < 1) {
	$red = "index.php?erro&cod=pontosgs54d";
	header(sprintf("Location: %s", $red));
	}
	
mysql_free_result($editarPonto);

}


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Pontos = "
SELECT te_ponto_id, te_ponto_id_sec, te_ponto_descricao, te_ponto_endereco, te_ponto_num, te_ponto_bairro, te_ponto_latitude, te_ponto_longitude, te_ponto_obs 
FROM smc_te_ponto
WHERE te_ponto_id_sec = '$row_Secretaria[sec_id]'
ORDER BY te_ponto_descricao ASC
";
$Pontos = mysql_query($query_Pontos, $SmecelNovo) or die(mysql_error());
$row_Pontos = mysql_fetch_assoc($Pontos);
$totalRows_Pontos = mysql_num_rows($Pontos);
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
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
<link rel="stylesheet" type="text/css" href="../css/impressao.css">

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
    <h3 class="ls-txt-center">RELAÇÃO DE PONTOS</h3>
    <br>
   
	
	<?php if ($totalRows_Pontos > 0) { ?>
    
	<table class="bordasimples" width="100%">
    <thead>
      <tr>
        <th class="ls-txt-center1">DESCRIÇÃO</th>
        <th class="ls-txt-center1">ENDEREÇO</th>
        <th width="200" class="ls-txt-center1">LATITUDE/LONGITUDE</th>
      </tr>
      </thead>
      <tbody>
      <?php do { ?>
        <tr>
          <td><?php echo $row_Pontos['te_ponto_descricao']; ?></td>
          <td><?php echo $row_Pontos['te_ponto_endereco']; ?> <?php echo $row_Pontos['te_ponto_num']; ?> <?php echo $row_Pontos['te_ponto_bairro']; ?></td>
          <td><a href="https://www.google.com/maps/search/<?php echo $row_Pontos['te_ponto_latitude']; ?>,+<?php echo $row_Pontos['te_ponto_longitude']; ?>" target="_blank"><?php echo $row_Pontos['te_ponto_latitude']; ?> <?php echo $row_Pontos['te_ponto_longitude']; ?></a></td>
        </tr>
        <?php } while ($row_Pontos = mysql_fetch_assoc($Pontos)); ?>
      </tbody>
    </table>
	<?php } else { ?>
	
    <div class="ls-alert-info">Nenhum ponto cadastrado</div>
	
	<?php } ?>
	

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="https://assets.locaweb.com.br/locastyle/3.8.4/javascripts/locastyle.js" type="text/javascript"></script>

<?php if (isset($_GET["ponto_edita"])) { ?>
	<script>
		locastyle.modal.open("#myAwesomeModalAtualizar");
    </script>
<?php } ?>

</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

mysql_free_result($Pontos);
?>