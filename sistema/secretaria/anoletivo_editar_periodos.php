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

require_once('funcoes/usuLogado.php');
require_once('funcoes/anoLetivo.php');

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

$colname_AnoLetivo = "-1";
if (isset($_GET['codigo'])) {
  $colname_AnoLetivo = $_GET['codigo'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AnoLetivo = sprintf("SELECT ano_letivo_id, ano_letivo_ano, ano_letivo_inicio, ano_letivo_fim, ano_letivo_aberto, ano_letivo_id_sec, ano_letivo_data_rematricula, ano_letivo_resultado_final FROM smc_ano_letivo WHERE ano_letivo_id_sec = '$row_Secretaria[sec_id]' AND ano_letivo_id = %s", GetSQLValueString($colname_AnoLetivo, "int"));
$AnoLetivo = mysql_query($query_AnoLetivo, $SmecelNovo) or die(mysql_error());
$row_AnoLetivo = mysql_fetch_assoc($AnoLetivo);
$totalRows_AnoLetivo = mysql_num_rows($AnoLetivo);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Periodos = "SELECT per_unid_id, per_unid_id_ano, per_unid_id_sec, per_unid_periodo, per_unid_data_inicio, per_unid_data_fim, per_unid_data_bloqueio, per_unid_hash FROM smc_unidades WHERE per_unid_id_ano = $colname_AnoLetivo";
$Periodos = mysql_query($query_Periodos, $SmecelNovo) or die(mysql_error());
$row_Periodos = mysql_fetch_assoc($Periodos);
$totalRows_Periodos = mysql_num_rows($Periodos);


if ($totalRows_AnoLetivo < 1) {
	$semEscolas = "index.php?erro";
	header(sprintf("Location: %s", $semEscolas));
	}


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {
	
  $hash = md5(date("YmdHis").$_POST['per_unid_id_ano'].$_POST['per_unid_id_sec']);		
	
  $insertSQL = sprintf("INSERT INTO smc_unidades (per_unid_id_ano, per_unid_id_sec, per_unid_periodo, per_unid_hash) VALUES (%s, %s, %s, '$hash')",
                       GetSQLValueString($_POST['per_unid_id_ano'], "int"),
                       GetSQLValueString($_POST['per_unid_id_sec'], "int"),
                       GetSQLValueString($_POST['per_unid_periodo'], "text")
                       //GetSQLValueString($_POST['per_unid_hash'], "text")
					   );

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

  $insertGoTo = "ano_letivo_periodo_editar.php?periodo=$hash";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
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

  <script src="js/locastyle.js"></script>

<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<body>
<?php include_once("menu_top.php"); ?>

<?php include_once "menu.php"; ?>

<main class="ls-main">
  <div class="container-fluid">
    <h1 class="ls-title-intro ls-ico-home">EDITAR ANO LETIVO</h1>
	
    <a href="anoletivo.php" class="ls-btn">VOLTAR</a>  
	<button data-ls-module="modal" data-target="#myAwesomeModal" class="ls-btn-primary">CADASTRAR PERÍODO</button>

      
      
      <?php if ($totalRows_Periodos>0) { ?>
      <table class="ls-table">
       <thead>
        <tr>
          <th width="100">PERÍODO</th>
          <th class="ls-txt-center">INÍCIO DE UNIDADE</th>
          <th class="ls-txt-center">FINAL DE UNIDADE</th>
          <th class="ls-txt-center">LIMITE DE BLOQUEIO</th>
          <th width="80"></th>
        </tr>
        </thead>
        <tbody>
        <?php do { ?>
          <tr>
            <td><span class="ls-tag"><?php echo $row_Periodos['per_unid_periodo']; ?>º PERÍODO</span></td>
            <td class="ls-txt-center"><?php echo inverteData($row_Periodos['per_unid_data_inicio']); ?></td>
            <td class="ls-txt-center"><?php echo inverteData($row_Periodos['per_unid_data_fim']); ?></td>
            <td class="ls-txt-center"><?php echo inverteData($row_Periodos['per_unid_data_bloqueio']); ?></td>
            <td><a href="ano_letivo_periodo_editar.php?periodo=<?php echo $row_Periodos['per_unid_hash']; ?>" class="ls-ico-pencil2"></a></td>
          </tr>
          <?php } while ($row_Periodos = mysql_fetch_assoc($Periodos)); ?>
      	</tbody>
      </table>
      <?php } else { ?>
      
      <hr>
      Nenhum período cadastrado.
      
	  <?php } ?>
   
  </div>
</main>

<?php include_once "notificacoes.php"; ?>


<div class="ls-modal" id="myAwesomeModal">
  <div class="ls-modal-box">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title">CADASTRAR PERÍODO</h4>
    </div>
    <div class="ls-modal-body" id="myModalBody">
      <p>
      
      
            <form method="post" name="form2" action="<?php echo $editFormAction; ?>" class="ls-form">
         
         <label class="ls-label col-md-12">
      <b class="ls-label-text">PERÍODO/UNIDADE/BIMESTRE/TRIMESTRE</b>
      <div class="ls-custom-select">   
        <select name="per_unid_periodo" class="ls-custom">
              <option value="" <?php if (!(strcmp("", ""))) {echo "SELECTED";} ?>>ESCOLHA...</option>      
              <option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>1º PERÍODO/UNIDADE/BIMESTRE/TRIMESTRE</option>
              <option value="2" <?php if (!(strcmp(2, ""))) {echo "SELECTED";} ?>>2º PERÍODO/UNIDADE/BIMESTRE/TRIMESTRE</option>
              <option value="3" <?php if (!(strcmp(3, ""))) {echo "SELECTED";} ?>>3º PERÍODO/UNIDADE/BIMESTRE/TRIMESTRE</option>
              <option value="4" <?php if (!(strcmp(4, ""))) {echo "SELECTED";} ?>>4º PERÍODO/UNIDADE/BIMESTRE/TRIMESTRE</option>
              <option value="5" <?php if (!(strcmp(5, ""))) {echo "SELECTED";} ?>>5º PERÍODO/UNIDADE/BIMESTRE/TRIMESTRE</option>
              <option value="6" <?php if (!(strcmp(6, ""))) {echo "SELECTED";} ?>>6º PERÍODO/UNIDADE/BIMESTRE/TRIMESTRE</option>
            </select>
            </div>
            </label>
            
            
        <input type="hidden" name="per_unid_id_ano" value="<?php echo $row_AnoLetivo['ano_letivo_id']; ?>">
        <input type="hidden" name="per_unid_id_sec" value="<?php echo $row_Secretaria['sec_id']; ?>">
        <input type="hidden" name="per_unid_hash" value="">
        <input type="hidden" name="MM_insert" value="form2">
      

      
      </p>
    </div>
    <div class="ls-modal-footer">
    <input type="submit" value="SALVAR E PROSSEGUIR..." class="ls-btn-primary">
      <a class="ls-btn ls-float-right" data-dismiss="modal">FECHAR</a>
    </div>
    </form>
  </div>
</div><!-- /.modal -->
<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>
<script src="../js/jquery.mask.js"></script> 
<script src="js/mascara.js"></script> 
<script src="js/maiuscula.js"></script> 
<script src="js/semAcentos.js"></script>

</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

mysql_free_result($AnoLetivo);

mysql_free_result($Periodos);
?>