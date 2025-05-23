<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php require_once('funcoes/inverteData.php'); ?>

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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO smc_me_cardapio (cardapio_id_sec, cardapio_etapa, cardapio_data_inicial) VALUES (%s, %s, %s)",
                       GetSQLValueString($_POST['cardapio_id_sec'], "int"),
                       GetSQLValueString($_POST['cardapio_etapa'], "int"),
                       GetSQLValueString($_POST['cardapio_data_inicial'], "date"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

  $insertGoTo = "cardapio_datas.php?cardapio=".mysql_insert_id();
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

require_once('funcoes/usuLogado.php');
require_once('funcoes/anoLetivo.php');

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Cardapios = "
SELECT cardapio_id, cardapio_id_sec, cardapio_etapa, cardapio_data_inicial,
CASE cardapio_etapa
WHEN 1 THEN 'CRECHE (7-11 meses)'
WHEN 2 THEN 'CRECHE (1-3 anos)'
WHEN 3 THEN 'PRE-ESCOLA (4-5 anos)'
WHEN 4 THEN 'ENSINO FUNDAMENTAL (6-10 anos)'
WHEN 5 THEN 'ENSINO FUNDAMENTAL (11-15 anos)'
WHEN 6 THEN 'ENSINO MÉDIO (16-18 anos)'
WHEN 7 THEN 'EJA (19-30 anos)'
WHEN 8 THEN 'EJA (31-60 anos)'
END AS cardapio_etapa 
FROM smc_me_cardapio
WHERE cardapio_id_sec = '$row_Secretaria[sec_id]'
ORDER BY cardapio_data_inicial DESC 
";
$Cardapios = mysql_query($query_Cardapios, $SmecelNovo) or die(mysql_error());
$row_Cardapios = mysql_fetch_assoc($Cardapios);
$totalRows_Cardapios = mysql_num_rows($Cardapios);
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
    <h1 class="ls-title-intro ls-ico-home">CARDÁPIO</h1>
    <!-- CONTEUDO -->
    
    <button data-ls-module="modal" data-target="#myAwesomeModal" class="ls-btn-primary ls-ico-plus">CARDÁPIO SEMANAL</button>
    
    
    
    <p>&nbsp;</p>
    <?php if ($totalRows_Cardapios > 0) { // Show if recordset not empty ?>
  <table class="ls-table">
    <thead>
      <tr>
        <th width="120">SEMANA</th>
        <th>ETAPA</th>
        <th width="80"></th>
        </tr>
    </thead>
    <tbody>
      <?php do { ?>
        <tr>
          <td><?php echo date('d/m/Y', strtotime($row_Cardapios['cardapio_data_inicial'])); ?></td>
          <td><?php echo $row_Cardapios['cardapio_etapa']; ?></td>
          <td><a href="cardapio_datas.php?cardapio=<?php echo $row_Cardapios['cardapio_id']; ?>" class="ls-ico-plus"></a></td>
        </tr>
        <?php } while ($row_Cardapios = mysql_fetch_assoc($Cardapios)); ?>
    </tbody>
  </table>
  <?php } else { ?>
  
      <div class="ls-alert-info"><strong>Atenção:</strong> Clique no botão "CARDÁPIO SEMANAL" para inserir um novo cardápio.</div>

  
  <?php } // Show if recordset not empty ?>
<p>&nbsp;</p>
    <!-- CONTEUDO -->    
  </div>
</main>

    <div class="ls-modal" id="myAwesomeModal">
  <div class="ls-modal-box">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title">CARDÁPIO SEMANAL</h4>
    </div>
    <div class="ls-modal-body" id="myModalBody">
    
    <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form" data-ls-module="form" autocomplete="off">
      
      <label class="ls-label col-md-12">
      <b class="ls-label-text">ETAPA</b>
        <div class="ls-custom-select">
        <select name="cardapio_etapa" class="ls-select" required>
            <option value="">ESCOLHA...</option>
            <option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>CRECHE (7-11 MESES)</option>
            <option value="2" <?php if (!(strcmp(2, ""))) {echo "SELECTED";} ?>>CRECHE (1-3 ANOS)</option>
            <option value="3" <?php if (!(strcmp(3, ""))) {echo "SELECTED";} ?>>PRE-ESCOLA</option>
            <option value="4" <?php if (!(strcmp(4, ""))) {echo "SELECTED";} ?>>E. FUNDAMENTAL (6-10 ANOS)</option>
            <option value="5" <?php if (!(strcmp(5, ""))) {echo "SELECTED";} ?>>E. FUNDAMENTAL (11-15  ANOS)</option>
            <option value="6" <?php if (!(strcmp(6, ""))) {echo "SELECTED";} ?>>ENSINO MEDIO (16-18 ANOS)</option>
            <option value="7" <?php if (!(strcmp(7, ""))) {echo "SELECTED";} ?>>EJA (19-30 ANOS)</option>
            <option value="8" <?php if (!(strcmp(8, ""))) {echo "SELECTED";} ?>>EJA (31-60 ANOS)</option>
      </select>
      </div>
      </label>
          
      <label class="ls-label col-md-12">
      <b class="ls-label-text">PRIMEIRO DIA SEMANA</b>
      <input type="date" name="cardapio_data_inicial" value="" class=""  placeholder="dd/mm/aaaa" size="32" autocomplete="off" required>
      </label>
          
          <label class="ls-label col-md-12">
          <input type="submit" value="PROSSEGUIR" class="ls-btn-primary">
                <button class="ls-btn ls-float-right" data-dismiss="modal">FECHAR</button>

          </label>
          
      <input type="hidden" name="cardapio_id_sec" value="<?php echo $row_Secretaria['sec_id']; ?>">
      <input type="hidden" name="MM_insert" value="form1">
    </form>
    
    
        </div>

  </div>
</div><!-- /.modal -->



<?php include_once "notificacoes.php"; ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>
</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

mysql_free_result($Cardapios);
?>