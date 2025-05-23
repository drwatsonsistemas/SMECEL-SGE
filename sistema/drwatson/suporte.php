<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php require_once('funcoes/configuracoes.php'); ?>

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
$MM_authorizedUsers = "99";
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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form2")) {
  $updateSQL = sprintf("UPDATE smc_usu SET usu_escola=%s WHERE usu_id=%s",
                       GetSQLValueString($_POST['usu_escola'], "int"),
                       GetSQLValueString($_POST['usu_id'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

  $updateGoTo = "suporte.php?mudouEscola";
  if (isset($_SERVER['QUERY_STRING'])) {
    //$updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    //$updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE smc_usu SET usu_sec=%s, usu_escola='0' WHERE usu_id=%s",
                       GetSQLValueString($_POST['usu_sec'], "int"),
                       GetSQLValueString($_POST['usu_id'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

  $updateGoTo = "suporte.php?mudouPrefeitura";
  if (isset($_SERVER['QUERY_STRING'])) {
    //$updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    //$updateGoTo .= $_SERVER['QUERY_STRING'];
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
$query_Prefeituras = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_ibge_municipio, sec_regra_media, sec_bloqueada FROM smc_sec WHERE sec_bloqueada = 'N' ORDER BY sec_prefeitura ASC";
$Prefeituras = mysql_query($query_Prefeituras, $SmecelNovo) or die(mysql_error());
$row_Prefeituras = mysql_fetch_assoc($Prefeituras);
$totalRows_Prefeituras = mysql_num_rows($Prefeituras);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_PrefSelecionada = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_ibge_municipio, sec_regra_media FROM smc_sec WHERE sec_id = '$row_UsuarioLogado[usu_sec]'";
$PrefSelecionada = mysql_query($query_PrefSelecionada, $SmecelNovo) or die(mysql_error());
$row_PrefSelecionada = mysql_fetch_assoc($PrefSelecionada);
$totalRows_PrefSelecionada = mysql_num_rows($PrefSelecionada);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Escolas = "SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_ue, escola_situacao, escola_localizacao, escola_ibge_municipio FROM smc_escola WHERE escola_situacao = '1' AND escola_id_sec = '$row_PrefSelecionada[sec_id]' ORDER BY escola_nome ASC";
$Escolas = mysql_query($query_Escolas, $SmecelNovo) or die(mysql_error());
$row_Escolas = mysql_fetch_assoc($Escolas);
$totalRows_Escolas = mysql_num_rows($Escolas);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EscSelecionada = "SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_ue, escola_situacao, escola_localizacao, escola_ibge_municipio FROM smc_escola WHERE escola_situacao = '1' AND escola_id = '$row_UsuarioLogado[usu_escola]'";
$EscSelecionada = mysql_query($query_EscSelecionada, $SmecelNovo) or die(mysql_error());
$row_EscSelecionada = mysql_fetch_assoc($EscSelecionada);
$totalRows_EscSelecionada = mysql_num_rows($EscSelecionada);
?>

<!DOCTYPE html>
<html class="<?php echo COR_TEMA ?>">
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
  <!-- SELECT2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body>
<?php include_once("menu_top.php"); ?>
<?php include_once "menu.php"; ?>
<main class="ls-main">
  <div class="container-fluid">
    <h1 class="ls-title-intro ls-ico-home">ACESSO AO SUPORTE</h1>
    <div class="ls-box"> 
    
    <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form">

      <label class="ls-label col-md-10">
      <b class="ls-label-text">ESCOLHA A ENTIDADE</b>
      <div class="ls-custom-select">
      <select name="usu_sec" id="usu_sec" class="ls-select">
            <option value="">-</option>
            <?php  do { ?>
            <option value="<?php echo $row_Prefeituras['sec_id']?>" <?php if (!(strcmp($row_Prefeituras['sec_id'], htmlentities($row_UsuarioLogado['usu_sec'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>><?php echo $row_Prefeituras['sec_prefeitura']?></option>
            <?php } while ($row_Prefeituras = mysql_fetch_assoc($Prefeituras)); ?>
          </select>
          </div>
          </label>
          
          
              <div class="ls-actions-btn">
	            <input type="submit" class="ls-btn-primary" value="DEFINIR PREFEITURA">
                <?php if ($totalRows_PrefSelecionada > 0) { // Show if recordset not empty ?>
    			<a href="../secretaria" target="_blank" class="ls-ico-export ls-ico-right ls-btn-danger painel_sec" id="painel_sec">Secretaria </a>
    			<a href="../pse" target="_blank" class="ls-ico-export ls-ico-right ls-btn-danger painel_pse" id="painel_pse">PSE</a>
    			<a href="../ctutelar" target="_blank" class="ls-ico-export ls-ico-right ls-btn-danger painel_ct" id="painel_ct">C. Tutelar</a>
    			<a href="professores_senha.php" target="_blank" class="ls-ico-export ls-ico-right ls-btn-danger painel_ct" id="painel_ct">Professores</a>
    			<a href="login_hora_cidade.php?cidade=<?php echo $row_PrefSelecionada['sec_id']; ?>" target="_blank" class="ls-ico-export ls-ico-right ls-btn-danger painel_sec" id="painel_sec">Login/hora</a>
				<?php } // Show if recordset not empty ?>
             </div>
             
          
      <input type="hidden" name="MM_update" value="form1">
      <input type="hidden" name="usu_id" value="<?php echo $row_UsuarioLogado['usu_id']; ?>">
      
    </form>
    
  <?php if ($totalRows_PrefSelecionada > 0) { // Show if recordset not empty ?>
    <div class="ls-alert-info">Selecionada: <strong><?php echo $row_PrefSelecionada['sec_prefeitura']; ?></strong></div>
  <?php } // Show if recordset not empty ?>

    </div>
    
  
  <?php if ($totalRows_Escolas > 0) { // Show if recordset not empty ?>
  <div class="ls-box">
  
  <form method="post" name="form2" action="<?php echo $editFormAction; ?>"  class="ls-form">
    
    <label class="ls-label col-md-10">
      <b class="ls-label-text">ESCOLHA A ESCOLA</b>
      <div class="ls-custom-select">
    <select name="usu_escola" id="usu_escola" class="ls-select">
          <option value="">-</option>
          <?php do { ?>
          <option value="<?php echo $row_Escolas['escola_id']?>" <?php if (!(strcmp($row_Escolas['escola_id'], htmlentities($row_UsuarioLogado['usu_escola'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>><?php echo $row_Escolas['escola_id'] ." - ". $row_Escolas['escola_nome']?></option>
          <?php } while ($row_Escolas = mysql_fetch_assoc($Escolas)); ?>
        </select>
        </div>
        </label>
        
         <div class="ls-actions-btn">
	            <input type="submit" class="ls-btn-primary" value="DEFINIR ESCOLA">
                <?php if ($totalRows_EscSelecionada > 0) { // Show if recordset not empty ?>
    
    			<a href="../escola" target="_blank" class="ls-ico-export ls-ico-right ls-btn-danger" id="painel_escola_a">Escola </a>
    			<a href="../portaria" target="_blank" class="ls-ico-export ls-ico-right ls-btn-danger" id="painel_escola_b">Portaria </a>
    
    
  <?php } // Show if recordset not empty ?>
         </div>
        
        
       <input type="hidden" name="MM_update" value="form2">
    <input type="hidden" name="usu_id" value="<?php echo $row_UsuarioLogado['usu_id']; ?>">
  </form>
  
    <?php if ($totalRows_EscSelecionada > 0) { // Show if recordset not empty ?>
    <div class="ls-alert-info">Selecionada: <strong><?php echo $row_EscSelecionada['escola_nome']; ?></strong>    
    </div>
  <?php } // Show if recordset not empty ?>

  
  </div>
    <?php } // Show if recordset not empty ?>

  
  
  <p>&nbsp;</p>
  </div>
</main>
<?php include_once "notificacoes.php"; ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="https://assets.locaweb.com.br/locastyle/3.8.4/javascripts/locastyle.js" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script type="text/javascript">
  //Popula campo cidades com base na escolha do campo estados
    $(document).ready(function(){
        $('#usu_sec').change(function(){
			$("#painel_sec, #painel_pse, #painel_ct").css("display", "none");
        });
		
		$('#usu_escola').change(function(){
			$("#painel_escola_a, #painel_escola_b").css("display", "none");
        });
		
        $('#usu_escola').select2();
    });
    </script>


</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Prefeituras);

mysql_free_result($PrefSelecionada);

mysql_free_result($Escolas);

mysql_free_result($EscSelecionada);
?>