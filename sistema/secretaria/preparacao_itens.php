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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO smc_me_preparacao_item (preparacao_item_id_preparacao, preparacao_item_id_alimento, preparacao_item_quantidade, preparacao_item_medida) VALUES (%s, %s, %s, %s)",
                       GetSQLValueString($_POST['preparacao_item_id_preparacao'], "int"),
                       GetSQLValueString($_POST['preparacao_item_id_alimento'], "int"),
                       GetSQLValueString($_POST['preparacao_item_quantidade'], "double"),
                       GetSQLValueString($_POST['preparacao_item_medida'], "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());
}

require_once('funcoes/usuLogado.php');
require_once('funcoes/anoLetivo.php');

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

$colname_Preparacao = "-1";
if (isset($_GET['preparacao'])) {
  $colname_Preparacao = $_GET['preparacao'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Preparacao = sprintf("
SELECT preparacao_id, preparacao_id_sec, preparacao_nome_preparacao, preparacao_modo_preparo, preparacao_hash 
FROM smc_me_preparacao 
WHERE preparacao_id_sec = '$row_Secretaria[sec_id]' AND preparacao_hash = %s", GetSQLValueString($colname_Preparacao, "text"));
$Preparacao = mysql_query($query_Preparacao, $SmecelNovo) or die(mysql_error());
$row_Preparacao = mysql_fetch_assoc($Preparacao);
$totalRows_Preparacao = mysql_num_rows($Preparacao);

if ($totalRows_Preparacao  < 1) {
	$red = "index.php?erro&cod=preparacao_itens.php";
	header(sprintf("Location: %s", $red));
	}


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Alimento = "SELECT TACO_ID, NUMERO_ALIMENTO, CATEGORIA_ID, DESCRICAO_DOS_ALIMENTOS, UMIDADE, ENERGIA, ENERGIA_KJ, PROTEINA, LIPIDEOS, COLESTEROL, CARBOIDRATO, FIBRA_ALIMENTAR, CINZAS, CALCIO, MAGNESIO, MANGANES, FOSFORO, FERRO, SODIO, POTASSIO, COBRE, ZINCO, RETINOL, RE, RAE_MCG, TIAMINA, RIBOFLAVINA, PIRIDOXINA, NIACINA, VITAMINA_C, SATURADOS, MONOINSATURADOS, POLIINSATURADOS, `12_0`, `14_0`, `16_0`, `18_0`, `20_0`, `22_0`, `24_0`, `14_1`, `16_1`, `18_1`, `20_1`, `18_2_N_6`, `18_3_N_3`, `20_4`, `20_5`, `22_5`, `22_6`, `18_1T`, `18_2T`, TRIPTOFANO, TREONINA, ISOLEUCINA, LEUCINA, LISINA, METIONINA, CISTINA, FENILALANINA, TIROSINA, VALINA, ARGININA, HISTIDINA, ALANINA, ACIDO_ASPARTICO, ACIDO_GLUTAMICO, GLICINA, PROLINA, SERINA FROM smc_taco ORDER BY DESCRICAO_DOS_ALIMENTOS ASC";
$Alimento = mysql_query($query_Alimento, $SmecelNovo) or die(mysql_error());
$row_Alimento = mysql_fetch_assoc($Alimento);
$totalRows_Alimento = mysql_num_rows($Alimento);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Itens = "
SELECT preparacao_item_id, preparacao_item_id_preparacao, preparacao_item_id_alimento, preparacao_item_quantidade, preparacao_item_medida,
taco_id, descricao_dos_alimentos, energia, energia_kj, proteina, carboidrato, lipideos, fibra_alimentar,  calcio, ferro, magnesio, zinco, sodio,
vitamina_c, re, saturados, monoinsaturados, poliinsaturados 
FROM smc_me_preparacao_item
INNER JOIN smc_taco ON taco_id = preparacao_item_id_alimento
WHERE preparacao_item_id_preparacao = '$row_Preparacao[preparacao_id]'
ORDER BY descricao_dos_alimentos ASC
";
$Itens = mysql_query($query_Itens, $SmecelNovo) or die(mysql_error());
$row_Itens = mysql_fetch_assoc($Itens);
$totalRows_Itens = mysql_num_rows($Itens);

if ((isset($_GET['item'])) && ($_GET['item'] != "")) {
  $deleteSQL = sprintf("DELETE FROM smc_me_preparacao_item WHERE preparacao_item_id=%s",
                       GetSQLValueString($_GET['item'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());

  $deleteGoTo = "preparacao_itens.php?preparacao=$colname_Preparacao";
  //if (isset($_SERVER['QUERY_STRING'])) {
    //$deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    //$deleteGoTo .= $_SERVER['QUERY_STRING'];
  //}
  header(sprintf("Location: %s", $deleteGoTo));
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
    <h1 class="ls-title-intro ls-ico-home">PREPARAÇÃO</h1>
    <!-- CONTEUDO -->
    
    
<p>
<a href="preparacao.php" class="ls-btn ls-ico-chevron-left"> VOLTAR</a>
<a href="impressao/rel_preparacao_itens.php?preparacao=<?php echo $row_Preparacao['preparacao_hash']; ?>" class="ls-btn" target="_blank">IMPRIMIR PREPARAÇÃO</a>
<a href="impressao/rel_preparacao_itens_ficha_tecnica.php?preparacao=<?php echo $row_Preparacao['preparacao_hash']; ?>" class="ls-btn" target="_blank">IMPRIMIR PREPARAÇÃO (COM FICHA TÉCNICA)</a>
</p>   


<div class="ls-box ls-lg-space ls-ico-cart ls-ico-bg">
  <h1 class="ls-title-1 ls-color-theme"><?php echo $row_Preparacao['preparacao_nome_preparacao']; ?></h1>
  <p><?php echo $row_Preparacao['preparacao_modo_preparo']; ?></p>
  <a data-ls-module="modal" data-target="#myAwesomeModal" class="ls-btn-primary ls-ico-plus" href="#">Inserir ítens da composição</a>
</div>
    
   
	<?php if ($totalRows_Itens > 0) { // Show if recordset not empty ?>
  <table  class="ls-table ls-bg-header">
    <thead>
    <tr>
      <th width="50" class="ls-txt-center"></th>
      <th class="ls-txt-center">ÍTEM</th>
      <th class="ls-txt-center">QUANTIDADE</th>
      <th width="50"></th>
    </tr>
    </thead>
    <tbody>
    <?php 
	$energia = 0;
	$energia_kj = 0;
	$proteina = 0;
	$carboidrato = 0;
	$lipideos = 0;
	$fibra_alimentar = 0;
	$calcio = 0;
	$ferro = 0;
	$magnesio = 0;
	$zinco = 0;
	$sodio = 0;
	$vitamina_c = 0;
	$vitamina_a = 0;
	$saturados = 0;
	$monoinsaturados = 0;
	$poliinsaturados = 0;
	?>
    <?php $num = 1; do { ?>
      <tr>
        <td class="ls-txt-center"><strong><?php echo $num; ?></strong></td>
        <td><?php echo utf8_encode($row_Itens['descricao_dos_alimentos']); ?></td>
        <td class="ls-txt-center"><?php echo $row_Itens['preparacao_item_quantidade']; ?> <?php echo $row_Itens['preparacao_item_medida']; ?></td>
        <td><a href="preparacao_itens.php?preparacao=<?php echo $colname_Preparacao; ?>&item=<?php echo $row_Itens['preparacao_item_id']; ?>"><span class="ls-ico-cancel-circle ls-ico-right ls-color-danger"></span></a></td>
      </tr>

	  
	  
      <!--
      <?php $energia = $energia + ($row_Itens['energia']/100)*$row_Itens['preparacao_item_quantidade']; ?>
      <?php $energia_kj = $energia_kj + ($row_Itens['energia_kj']/100)*$row_Itens['preparacao_item_quantidade']; ?>
      <?php $proteina = $proteina + ($row_Itens['proteina']/100)*$row_Itens['preparacao_item_quantidade']; ?>
      <?php $carboidrato = $carboidrato + ($row_Itens['carboidrato']/100)*$row_Itens['preparacao_item_quantidade']; ?>
      <?php $lipideos = $lipideos + ($row_Itens['lipideos']/100)*$row_Itens['preparacao_item_quantidade']; ?>
      <?php $fibra_alimentar = $fibra_alimentar + ($row_Itens['fibra_alimentar']/100)*$row_Itens['preparacao_item_quantidade']; ?>
      <?php $calcio = $calcio + ($row_Itens['calcio']/100)*$row_Itens['preparacao_item_quantidade']; ?>
      <?php $ferro = $ferro + ($row_Itens['ferro']/100)*$row_Itens['preparacao_item_quantidade']; ?>
      <?php $magnesio = $magnesio + ($row_Itens['magnesio']/100)*$row_Itens['preparacao_item_quantidade']; ?>
      <?php $zinco = $zinco + ($row_Itens['zinco']/100)*$row_Itens['preparacao_item_quantidade']; ?>
      <?php $sodio = $sodio + ($row_Itens['sodio']/100)*$row_Itens['preparacao_item_quantidade']; ?>
      <?php $vitamina_c = $vitamina_c + ($row_Itens['vitamina_c']/100)*$row_Itens['preparacao_item_quantidade']; ?>
      <?php $vitamina_a = $vitamina_a + ($row_Itens['re']/100)*$row_Itens['preparacao_item_quantidade']; ?>
      <?php $saturados = $saturados + ($row_Itens['saturados']/100)*$row_Itens['preparacao_item_quantidade']; ?>
      <?php $monoinsaturados = $monoinsaturados + ($row_Itens['monoinsaturados']/100)*$row_Itens['preparacao_item_quantidade']; ?>
      <?php $poliinsaturrados = $poliinsaturrados + ($row_Itens['poliinsaturrados']/100)*$row_Itens['preparacao_item_quantidade']; ?>
      -->
      <?php $num++; } while ($row_Itens = mysql_fetch_assoc($Itens)); ?>
  	</tbody>
  </table>
  

  <?php } else { ?>
  
  <div class="ls-alert-info">Clique no botão acima para inserir os alimentos dessa preparação.</div>

  <?php } // Show if recordset not empty ?>
  
<?php if ($totalRows_Itens > 0) { ?>  

<div class="ls-box ls-board-box">
  <header class="ls-info-header">
    <h2 class="ls-title-3">FICHA TÉCNICA</h2>
    <p class="ls-float-right ls-float-none-xs ls-small-info"><strong></strong></p>
  </header>
  
  
  
  <table class="ls-table ls-sm-space">
  <thead>
    <tr>
      <th></th>
      <th class="ls-txt-center">ATUAL</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>ENERGIA (kcal)</td>
      <td class="ls-txt-center"><strong><?php echo number_format($energia,2,',',''); ?></strong></td>
    </tr>
    <tr>
      <td>ENERGIA (kJ)</td>
      <td class="ls-txt-center"><strong><?php echo number_format($energia_kj,2,',',''); ?></strong></td>
    </tr>
    <tr>
      <td>PROTEÍNA (g)</td>
      <td class="ls-txt-center"><strong><?php echo number_format($proteina,2,',',''); ?></strong></td>
    </tr>
    <tr>
      <td>CARBOIDRATO (g)</td>
      <td class="ls-txt-center"><strong><?php echo number_format($carboidrato,2,',',''); ?></strong></td>
    </tr>
    <tr>
      <td>LIPÍDEOS (g)</td>
      <td class="ls-txt-center"><strong><?php echo number_format($lipideos,2,',',''); ?></strong></td>
    </tr>    
    <tr>
      <td>FIBRA ALIMENTAR (g)</td>
      <td class="ls-txt-center"><strong><?php echo number_format($fibra_alimentar,2,',',''); ?></strong></td>
    </tr>    
    <tr>
      <td>CÁLCIO (mg)</td>
      <td class="ls-txt-center"><strong><?php echo number_format($calcio,2,',',''); ?></strong></td>
    </tr>    
    <tr>
      <td>FERRO (mg)</td>
      <td class="ls-txt-center"><strong><?php echo number_format($ferro,2,',',''); ?></strong></td>
    </tr>    
    <tr>
      <td>MAGNÉSIO (mg)</td>
      <td class="ls-txt-center"><strong><?php echo number_format($magnesio,2,',',''); ?></strong></td>
    </tr>
    <tr>
      <td>ZINCO (mg)</td>
      <td class="ls-txt-center"><strong><?php echo number_format($zinco,2,',',''); ?></strong></td>
    </tr>
    <tr>
      <td>SÓDIO (mg)</td>
      <td class="ls-txt-center"><strong><?php echo number_format($sodio,2,',',''); ?></strong></td>
    </tr>
    <tr>
      <td>VITAMINA C (mg)</td>
      <td class="ls-txt-center"><strong><?php echo number_format($vitamina_c,2,',',''); ?></strong></td>
    </tr>
    <tr>
      <td>VITAMINA A (RE) (mcg)</td>
      <td class="ls-txt-center"><strong><?php echo number_format($vitamina_a,2,',',''); ?></strong></td>
    </tr>
    <tr>
      <td>SATURADOS (g)</td>
      <td class="ls-txt-center"><strong><?php echo number_format($saturados,2,',',''); ?></strong></td>
    </tr>
    <tr>
      <td>MONOINSATURADOS (g)</td>
      <td class="ls-txt-center"><strong><?php echo number_format($monoinsaturados,2,',',''); ?></strong></td>
    </tr>
    <tr>
      <td>POLIINSATURADOS (g)</td>
      <td class="ls-txt-center"><strong><?php echo number_format($poliinsaturados,2,',',''); ?></strong></td>
    </tr>

  </tbody>
</table>

</div>
<?php } ?>

<p>&nbsp;</p>


    <!-- CONTEUDO -->    
  </div>
</main>

    <div class="ls-modal" id="myAwesomeModal">
  <div class="ls-modal-box">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title">INSERIR ÍTEM</h4>
    </div>
    <div class="ls-modal-body" id="myModalBody">
	
	<p>Adicione os ítens que compõem essa preparação.</p>

    
    <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form ls-form-horizontal row">
      
      <label class="ls-label col-md-12">
      <b class="ls-label-text">Ítem</b>
      <div class="ls-custom-select">
      <select name="preparacao_item_id_alimento" class="ls-select" required>
            <option value="" >Escolha</option>
			<?php do {  ?>
            <option value="<?php echo $row_Alimento['TACO_ID']?>" ><?php echo utf8_encode($row_Alimento['DESCRICAO_DOS_ALIMENTOS']); ?></option>
            <?php } while ($row_Alimento = mysql_fetch_assoc($Alimento)); ?>
          </select>
          </div>
          </label>
          
          
     <label class="ls-label col-md-6">
      <b class="ls-label-text">Quantidade</b>
      <input type="text" name="preparacao_item_quantidade" value="" size="32" onchange="this.value = this.value.replace(/,/g, '.')" required>
    </label>
          
          <label class="ls-label col-md-6">
      		<b class="ls-label-text">Unidade</b><br><br>
          
          <label class="ls-label-text">
          <input type="radio" name="preparacao_item_medida" value="g" <?php if (!(strcmp("g","g"))) {echo "checked=\"checked\"";} ?>>
          g</label>
                
          <label class="ls-label-text">
          <input type="radio" name="preparacao_item_medida" value="ml" <?php if (!(strcmp("g","ml"))) {echo "checked=\"checked\"";} ?>>
          ml</label>
               
          <label class="ls-label-text">
          <input type="radio" name="preparacao_item_medida" value="un" <?php if (!(strcmp("g","un"))) {echo "checked=\"checked\"";} ?>>
          un</label>
               
          </label>     
          
          <div class="ls-actions-btn">
            <input type="submit" value="INSERIR" class="ls-btn-primary">
            <a class="ls-btn ls-float-right" data-dismiss="modal">CANCELAR</a>
          </div>
          
      <input type="hidden" name="preparacao_item_id_preparacao" value="<?php echo $row_Preparacao['preparacao_id']; ?>">
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

mysql_free_result($Preparacao);

mysql_free_result($Alimento);

mysql_free_result($Itens);
?>