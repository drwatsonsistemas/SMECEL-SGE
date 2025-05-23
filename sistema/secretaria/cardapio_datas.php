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

$colname_Cardapio = "-1";
if (isset($_GET['cardapio'])) {
  $colname_Cardapio = $_GET['cardapio'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Cardapio = sprintf("
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
WHERE cardapio_id_sec = '$row_Secretaria[sec_id]' AND cardapio_id = %s", GetSQLValueString($colname_Cardapio, "int"));
$Cardapio = mysql_query($query_Cardapio, $SmecelNovo) or die(mysql_error());
$row_Cardapio = mysql_fetch_assoc($Cardapio);
$totalRows_Cardapio = mysql_num_rows($Cardapio);

if ($totalRows_Cardapio  < 1) {
	$red = "index.php?erro&cod=cardapio_datas.php";
	header(sprintf("Location: %s", $red));
	}

$colname_Dias = "-1";
if (isset($_GET['cardapio'])) {
  $colname_Dias = $_GET['cardapio'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Dias = sprintf("
SELECT cardapio_dia_id, cardapio_dia_id_card, cardapio_dia_data 
FROM smc_me_cardapio_dia 
WHERE cardapio_dia_id_card = %s
ORDER BY cardapio_dia_data ASC
", GetSQLValueString($colname_Dias, "int"));
$Dias = mysql_query($query_Dias, $SmecelNovo) or die(mysql_error());
$row_Dias = mysql_fetch_assoc($Dias);
$totalRows_Dias = mysql_num_rows($Dias);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Preparacao = "SELECT preparacao_id, preparacao_id_sec, preparacao_nome_preparacao, preparacao_modo_preparo, preparacao_hash FROM smc_me_preparacao WHERE preparacao_id_sec = '$row_Secretaria[sec_id]' ORDER BY preparacao_nome_preparacao ASC";
$Preparacao = mysql_query($query_Preparacao, $SmecelNovo) or die(mysql_error());
$row_Preparacao = mysql_fetch_assoc($Preparacao);
$totalRows_Preparacao = mysql_num_rows($Preparacao);

$dia = "";
if (isset($_GET['dia'])) {
  $dia = $_GET['dia'];
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO smc_me_cardapio_dia (cardapio_dia_id_card, cardapio_dia_data) VALUES (%s, %s)",
                       GetSQLValueString($_POST['cardapio_dia_id_card'], "int"),
                       GetSQLValueString($_POST['cardapio_dia_data'], "date"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());
  
  $ultimoId = mysql_insert_id($SmecelNovo);
  
  $insertGoTo = "cardapio_datas.php?cardapio=$row_Cardapio[cardapio_id]&dia=$ultimoId";
		 //if (isset($_SERVER['QUERY_STRING'])) {
		  //$insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
		  //$insertGoTo .= $_SERVER['QUERY_STRING'];
		  //}
  header(sprintf("Location: %s", $insertGoTo));
  
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {
  $insertSQL = sprintf("INSERT INTO smc_me_cardapio_dia_item (cardapio_dia_item_id_cardapio, cardapio_dia_item_id_dia, cardapio_dia_item_id_preparacao, cardapio_dia_item_periodo) VALUES (%s, %s, %s, %s)",
                       GetSQLValueString($_POST['cardapio_dia_item_id_cardapio'], "int"),
                       GetSQLValueString($_POST['cardapio_dia_item_id_dia'], "int"),
                       GetSQLValueString($_POST['cardapio_dia_item_id_preparacao'], "int"),
                       GetSQLValueString($_POST['cardapio_dia_item_periodo'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());
  
  $insertGoTo = "cardapio_datas.php?cardapio=$_POST[cardapio_dia_item_id_cardapio]&preparacaoInserida";
  //if (isset($_SERVER['QUERY_STRING'])) {
    //$insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    //$insertGoTo .= $_SERVER['QUERY_STRING'];
  //}
  header(sprintf("Location: %s", $insertGoTo));
}


if ((isset($_GET['diaDel'])) && ($_GET['diaDel'] != "")) {
  $deleteSQL = sprintf("DELETE FROM smc_me_cardapio_dia WHERE cardapio_dia_id=%s",
                       GetSQLValueString($_GET['diaDel'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());

  $deleteGoTo = "cardapio_datas.php?cardapio=$colname_Cardapio?diaDeletado";
  //if (isset($_SERVER['QUERY_STRING'])) {
    //$deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    //$deleteGoTo .= $_SERVER['QUERY_STRING'];
  //}
  header(sprintf("Location: %s", $deleteGoTo));
}



if ((isset($_GET['itemDel'])) && ($_GET['itemDel'] != "")) {
  $deleteSQL = sprintf("DELETE FROM smc_me_cardapio_dia_item WHERE cardapio_dia_item_id=%s",
                       GetSQLValueString($_GET['itemDel'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());

  $deleteGoTo = "cardapio_datas.php?cardapio=$colname_Cardapio?itemDeletado";
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
    <h1 class="ls-title-intro ls-ico-home">CARDÁPIO DA SEMANA</h1>
    <!-- CONTEUDO -->
    
    
	<?php if (isset($_GET["diaInserido"])) { ?>
      <div class="ls-alert-success ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> Dia da semana inserida com sucesso. </div>
      <?php } ?>
    <?php if (isset($_GET["preparacaoInserida"])) { ?>
      <div class="ls-alert-success ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> Preparação inserida com sucesso. </div>
      <?php } ?>
    <a href="cardapio.php" class="ls-btn">VOLTAR</a> 
    <button data-ls-module="modal" data-target="#myAwesomeModalDia" class="ls-btn-primary ls-ico-plus">INSERIR DIA</button>
    <a href="impressao/rel_cardapio_datas.php?cardapio=<?php echo $colname_Cardapio; ?>" class="ls-btn" target="_blank">IMPRIMIR</a>
    <p>&nbsp;</p>
    
    <blockquote class="ls-box">
    
    <p>ETAPA (FAIXA-ETÁRIA):<strong> <?php echo $row_Cardapio['cardapio_etapa']; ?></strong></p>
	<p>SEMANA (1º DIA): <strong><?php echo date('d/m/Y', strtotime($row_Cardapio['cardapio_data_inicial'])); ?></strong></p>
    
    </blockquote>
    
	<?php if ($totalRows_Dias > 0) {  ?>
    <div class="ls-box ls-board-box">
      <header class="ls-info-header">
        <h2 class="ls-title-3">CARDÁPIO SEMANAL</h2>
        <p class="ls-float-right ls-float-none-xs ls-small-info"><strong></strong></p>
      </header>
      <div id="sending-stats" class="row">
        <?php $diasemana = array('Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'); ?>
        
        <?php do { ?>
          <?php $diasemana_numero = date('w', strtotime($row_Dias['cardapio_dia_data'])); ?>
          <div class="col-sm-6 col-md-3 ls-xs-space">
            <div class="ls-box">
              <div class="ls-box-head">
                <h6 class="ls-title-4"><b><?php echo $diasemana[$diasemana_numero]; ?> <br><?php echo date('d/m', strtotime($row_Dias['cardapio_dia_data'])); ?></b></h6>
              </div>
              <div class="ls-box-body">
                <?php          
                mysql_select_db($database_SmecelNovo, $SmecelNovo);
                $query_PreparacaoDia = "SELECT cardapio_dia_item_id, cardapio_dia_item_id_cardapio, cardapio_dia_item_id_dia, cardapio_dia_item_id_preparacao, cardapio_dia_item_periodo, preparacao_id, preparacao_nome_preparacao
                FROM smc_me_cardapio_dia_item 
                INNER JOIN smc_me_preparacao ON preparacao_id = cardapio_dia_item_id_preparacao
                WHERE cardapio_dia_item_id_dia = '$row_Dias[cardapio_dia_id]'
				ORDER BY cardapio_dia_item_periodo ASC
				";
                $PreparacaoDia = mysql_query($query_PreparacaoDia, $SmecelNovo) or die(mysql_error());
                $row_PreparacaoDia = mysql_fetch_assoc($PreparacaoDia);
                $totalRows_PreparacaoDia = mysql_num_rows($PreparacaoDia);
                ?>
				
				
                <small>
				
				<?php if ($totalRows_PreparacaoDia > 0) { ?>
                <?php do { ?>
                  <?php echo $row_PreparacaoDia['preparacao_nome_preparacao']; ?> <a href="cardapio_datas.php?cardapio=<?php echo $row_Cardapio['cardapio_id']; ?>&itemDel=<?php echo $row_PreparacaoDia['cardapio_dia_item_id']; ?>" class="ls-ico-remove"></a> <br>
                  <?php } while ($row_PreparacaoDia = mysql_fetch_assoc($PreparacaoDia)); ?>
                <?php } else { ?>
                
				<i>Nenhuma preparação</i>
				
				<?php } ?>
				
				
				</small> 
				
				
				<br>
                <small><a href="cardapio_datas.php?cardapio=<?php echo $row_Cardapio['cardapio_id']; ?>&dia=<?php echo $row_Dias['cardapio_dia_id']; ?>" class="ls-ico-plus ls-tag"> ADD. PREPARAÇÃO</a></small> </div>
            	<a href="cardapio_datas.php?cardapio=<?php echo $row_Cardapio['cardapio_id']; ?>&diaDel=<?php echo $row_Dias['cardapio_dia_id']; ?>" class="ls-ico-remove"></a>
			</div>
          </div>
          <?php } while ($row_Dias = mysql_fetch_assoc($Dias)); ?>
       
      </div>
    </div>
	<?php } else { ?>
    
    <div class="ls-alert-info"><strong>Atenção:</strong> Clique no botão "INSERIR DIA" e cadastre todos os dias da semana em que este cardápio será aplicado, começando do dia <?php echo date('d/m/Y', strtotime($row_Cardapio['cardapio_data_inicial'])); ?>.</div>
    
	<?php } ?>
    
    
    
    <p>&nbsp;</p>
    <!-- CONTEUDO --> 
  </div>
</main>
<div class="ls-modal" id="myAwesomeModalDia">
  <div class="ls-modal-box">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title">CARDÁPIO SEMANAL</h4>
    </div>
    <div class="ls-modal-body" id="myModalBody">
      <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form">
        <label class="ls-label col-md-12"> <b class="ls-label-text">DIA DA SEMANA</b>
          
		  <input type="date" name="cardapio_dia_data" value="<?php if ($totalRows_Dias == 0) { ?><?php echo date('d/m/Y', strtotime($row_Cardapio['cardapio_data_inicial'])); ?><?php } ?>" size="32" placeholder="dd/mm/aaaa" autocomplete="off" required>
        
		</label>
        <label class="ls-label col-md-12">
          <input type="submit" value="INSERIR" class="ls-btn-primary">
        </label>
        <input type="hidden" name="cardapio_dia_id_card" value="<?php echo $row_Cardapio['cardapio_id']; ?>">
        <input type="hidden" name="MM_insert" value="form1">
      </form>
    </div>
  </div>
</div>
<!-- /.modal -->

<div class="ls-modal" data-modal-blocked1 id="myAwesomeModal">
  <div class="ls-modal-box">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title">INSERIR PRATO DO DIA</h4>
    </div>
    <div class="ls-modal-body" id="myModalBody">
    
    <?php if ($totalRows_Preparacao > 0) { ?>
      <form method="post" name="form2" action="<?php echo $editFormAction; ?>"  class="ls-form">
        <label class="ls-label col-md-12">
        <b class="ls-label-text">PREPARAÇÃO</b>
        <div class="ls-custom-select">
          <select name="cardapio_dia_item_id_preparacao" class="ls-select" required>
            <option value="" >ESCOLHA</option>
            <?php do { ?>
            <option value="<?php echo $row_Preparacao['preparacao_id']?>" ><?php echo $row_Preparacao['preparacao_nome_preparacao']?></option>
            <?php } while ($row_Preparacao = mysql_fetch_assoc($Preparacao)); ?>
          </select>
        </div>
        </label>
        <label class="ls-label col-md-12">
        <b class="ls-label-text">PERÍODO</b>
        <div class="ls-custom-select">
          <select name="cardapio_dia_item_periodo" class="ls-select" required>
            <option value="" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>ESCOLHA</option>
            <option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>Desjejum/Café da manhã</option>
            <option value="2" <?php if (!(strcmp(2, ""))) {echo "SELECTED";} ?>>Lanche da manhã</option>
            <option value="3" <?php if (!(strcmp(3, ""))) {echo "SELECTED";} ?>>Almoço</option>
            <option value="4" <?php if (!(strcmp(4, ""))) {echo "SELECTED";} ?>>Lanche da tarde</option>
            <option value="5" <?php if (!(strcmp(5, ""))) {echo "SELECTED";} ?>>Jantar</option>
            <option value="6" <?php if (!(strcmp(6, ""))) {echo "SELECTED";} ?>>Lanche da noite</option>
            <option value="7" <?php if (!(strcmp(7, ""))) {echo "SELECTED";} ?>>Kit Lanche (para passeio)</option>
            <option value="8" <?php if (!(strcmp(8, ""))) {echo "SELECTED";} ?>>Lanche EJA</option>
          </select>
        </div>
        </label>
        <label class="ls-label col-md-12">
          <input type="submit" value="INSERIR" class="ls-btn">
        </label>
        <input type="hidden" name="cardapio_dia_item_id_cardapio" value="<?php echo $row_Cardapio['cardapio_id']; ?>">
        <input type="hidden" name="cardapio_dia_item_id_dia" value="<?php echo $dia; ?>">
        <input type="hidden" name="MM_insert" value="form2">
      </form>
      <?php  } else { ?>
      
      <div class="ls-alert-warning"><strong>Atenção:</strong> É necessário cadastrar as preparações que serão inseridas nos cardápios. <a href="preparacao.php">Clique aqui</a> para cadastrar as preparações.</div>
      
      <?php } ?>
      
      
    </div>
  </div>
</div>
<!-- /.modal -->

<?php include_once "notificacoes.php"; ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>
<?php if (isset($_GET["dia"])) { ?>
  <script>
		locastyle.modal.open("#myAwesomeModal");
    </script>
  <?php } ?>
</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

mysql_free_result($Cardapio);

mysql_free_result($Dias);

mysql_free_result($Preparacao);

mysql_free_result($PreparacaoDia);
?>