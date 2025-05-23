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

require_once('funcoes/usuLogado.php');
require_once('funcoes/anoLetivo.php');

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

$colname_Rota = "-1";
if (isset($_GET['rota'])) {
  $colname_Rota = $_GET['rota'];

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Rota = sprintf("
SELECT te_rota_id, te_rota_id_sec, te_rota_descricao, te_rota_id_veiculo, te_rota_id_motorista, te_rota_km_total, te_rota_observacoes, 
te_rota_tipo, te_rota_ativa, func_id, func_nome, te_cad_veiculo_id, te_cad_veiculo_placa, 
CASE te_cad_veiculo_tipo
WHEN 1 THEN 'ONIBUS'
WHEN 2 THEN 'MICRO-ONIBUS'
WHEN 3 THEN 'VANS/KOMBI'
WHEN 4 THEN 'BICICLETA'
WHEN 5 THEN 'TRAÇÃO ANIMAL'
WHEN 99 THEN 'OUTROS'
END AS te_cad_veiculo_tipo 
FROM smc_te_rota
INNER JOIN smc_func ON func_id = te_rota_id_motorista 
INNER JOIN smc_te_veiculo ON te_cad_veiculo_id = te_rota_id_veiculo
WHERE te_rota_id_sec  = '$row_UsuarioLogado[usu_sec]' AND te_rota_id = %s", GetSQLValueString($colname_Rota, "int"));
$Rota = mysql_query($query_Rota, $SmecelNovo) or die(mysql_error());
$row_Rota = mysql_fetch_assoc($Rota);
$totalRows_Rota = mysql_num_rows($Rota);

if ($totalRows_Rota < 1) {
	$red = "index.php?erro&cod=rota23kju29";
	header(sprintf("Location: %s", $red));
	}
mysql_free_result($Rota);

}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_pontoA = "SELECT te_ponto_id, te_ponto_id_sec, te_ponto_descricao, te_ponto_endereco, te_ponto_num, te_ponto_bairro, te_ponto_latitude, te_ponto_longitude, te_ponto_obs FROM smc_te_ponto WHERE te_ponto_id_sec = '$row_Secretaria[sec_id]' ORDER BY te_ponto_descricao ASC";
$pontoA = mysql_query($query_pontoA, $SmecelNovo) or die(mysql_error());
$row_pontoA = mysql_fetch_assoc($pontoA);
$totalRows_pontoA = mysql_num_rows($pontoA);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_pontoAEdit = "SELECT te_ponto_id, te_ponto_id_sec, te_ponto_descricao, te_ponto_endereco, te_ponto_num, te_ponto_bairro, te_ponto_latitude, te_ponto_longitude, te_ponto_obs FROM smc_te_ponto WHERE te_ponto_id_sec = '$row_Secretaria[sec_id]' ORDER BY te_ponto_descricao ASC";
$pontoAEdit = mysql_query($query_pontoAEdit, $SmecelNovo) or die(mysql_error());
$row_pontoAEdit = mysql_fetch_assoc($pontoAEdit);
$totalRows_pontoAEdit = mysql_num_rows($pontoAEdit);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_pontoB = "SELECT te_ponto_id, te_ponto_id_sec, te_ponto_descricao, te_ponto_endereco, te_ponto_num, te_ponto_bairro, te_ponto_latitude, te_ponto_longitude, te_ponto_obs FROM smc_te_ponto WHERE te_ponto_id_sec = '$row_Secretaria[sec_id]' ORDER BY te_ponto_descricao ASC";
$pontoB = mysql_query($query_pontoB, $SmecelNovo) or die(mysql_error());
$row_pontoB = mysql_fetch_assoc($pontoB);
$totalRows_pontoB = mysql_num_rows($pontoB);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_pontoBEdit = "SELECT te_ponto_id, te_ponto_id_sec, te_ponto_descricao, te_ponto_endereco, te_ponto_num, te_ponto_bairro, te_ponto_latitude, te_ponto_longitude, te_ponto_obs FROM smc_te_ponto WHERE te_ponto_id_sec = '$row_Secretaria[sec_id]' ORDER BY te_ponto_descricao ASC";
$pontoBEdit = mysql_query($query_pontoBEdit, $SmecelNovo) or die(mysql_error());
$row_pontoBEdit = mysql_fetch_assoc($pontoBEdit);
$totalRows_pontoBEdit = mysql_num_rows($pontoBEdit);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Pontos = "
SELECT te_itinerario_id, te_itinerario_rota_id, te_itinerario_sec_id, te_itinerario_pontoa_id, te_itinerario_pontob_id, 
te_itinerario_metros_ab, te_itinerario_minutos_ab, 
pontoa.te_ponto_id, pontoa.te_ponto_descricao as descricao_a, pontob.te_ponto_id, pontob.te_ponto_descricao as descricao_b 
FROM smc_te_itinerario 
INNER JOIN smc_te_ponto as pontoa ON pontoa.te_ponto_id = te_itinerario_pontoa_id 
INNER JOIN smc_te_ponto as pontob ON pontob.te_ponto_id = te_itinerario_pontob_id
WHERE te_itinerario_sec_id = $row_Secretaria[sec_id] AND te_itinerario_rota_id = '$colname_Rota' 
ORDER BY te_itinerario_id ASC";
$Pontos = mysql_query($query_Pontos, $SmecelNovo) or die(mysql_error());
$row_Pontos = mysql_fetch_assoc($Pontos);
$totalRows_Pontos = mysql_num_rows($Pontos);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO smc_te_itinerario (te_itinerario_rota_id, te_itinerario_sec_id, te_itinerario_pontoa_id, te_itinerario_pontob_id, te_itinerario_metros_ab, te_itinerario_minutos_ab) VALUES (%s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['te_itinerario_rota_id'], "int"),
                       GetSQLValueString($_POST['te_itinerario_sec_id'], "int"),
                       GetSQLValueString($_POST['te_itinerario_pontoa_id'], "int"),
                       GetSQLValueString($_POST['te_itinerario_pontob_id'], "int"),
                       GetSQLValueString($_POST['te_itinerario_metros_ab'], "text"),
                       GetSQLValueString($_POST['te_itinerario_minutos_ab'], "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

  $insertGoTo = "itinerario.php?cadastrado";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form2")) {
  $updateSQL = sprintf("UPDATE smc_te_itinerario SET te_itinerario_pontoa_id=%s, te_itinerario_pontob_id=%s, te_itinerario_metros_ab=%s, te_itinerario_minutos_ab=%s WHERE te_itinerario_id=%s",
                       GetSQLValueString($_POST['te_itinerario_pontoa_id'], "int"),
                       GetSQLValueString($_POST['te_itinerario_pontob_id'], "int"),
                       GetSQLValueString($_POST['te_itinerario_metros_ab'], "text"),
                       GetSQLValueString($_POST['te_itinerario_minutos_ab'], "text"),
                       GetSQLValueString($_POST['te_itinerario_id'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

  $updateGoTo = "itinerario.php?editado";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_Ponto = "-1";
if (isset($_GET['ponto'])) {
  $colname_Ponto = $_GET['ponto'];

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Ponto = sprintf("SELECT te_itinerario_id, te_itinerario_rota_id, te_itinerario_sec_id, te_itinerario_pontoa_id, te_itinerario_pontob_id, te_itinerario_metros_ab, te_itinerario_minutos_ab FROM smc_te_itinerario WHERE te_itinerario_sec_id = '$row_Secretaria[sec_id]' AND te_itinerario_id = %s", GetSQLValueString($colname_Ponto, "int"));
$Ponto = mysql_query($query_Ponto, $SmecelNovo) or die(mysql_error());
$row_Ponto = mysql_fetch_assoc($Ponto);
$totalRows_Ponto = mysql_num_rows($Ponto);

if ($totalRows_Ponto < 1) {
	$red = "index.php?erro&cod=pontos6agd85";
	header(sprintf("Location: %s", $red));
	}
mysql_free_result($Ponto);
	
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
    <h1 class="ls-title-intro ls-ico-flag">ITINERÁRIO</h1>
    <!-- CONTEUDO -->
    
    <div class="ls-box">
    ROTA: <strong><?php echo $row_Rota['te_rota_descricao']; ?></strong>  VEÍCULO: <strong><?php echo $row_Rota['te_cad_veiculo_tipo']; ?> - <?php echo $row_Rota['te_cad_veiculo_placa']; ?></strong> MOTORISTA: <strong><?php echo $row_Rota['func_nome']; ?></strong>
    </div>
    
    
    <button data-ls-module="modal" data-target="#myAwesomeModal" class="ls-btn-primary">INSERIR ITINERÁRIO</button>
	<a href="impressao/rel_itinerario.php?rota=<?php echo $colname_Rota; ?>" class="ls-btn" target="_blank">IMPRIMIR</a>
    
    
    <?php if ($totalRows_Pontos > 0) { ?>
    <table class="ls-table ls-txt-center ls-sm-space">
      <thead>
      <tr>
        <th>PONTO A</th>
        <th width="30"></th>
        <th>PONTO B</th>
        <th width="120">DISTÂNCIA (METROS)</th>
        <th width="120">TEMPO (MINUTOS)</th>
        <th width="50"></th>
      </tr>
      </thead>
      <tbody>
      <?php 
	  $distMetros = 0;
	  $tempoMinutos = 0;
	  
	  ?>
      <?php do { ?>
        <tr>
          <td><?php echo $row_Pontos['descricao_a']; ?></td>
          <td><span class="ls-ico-shaft-right"></span></td>
          <td><?php echo $row_Pontos['descricao_b']; ?></td>
          <td><?php echo $row_Pontos['te_itinerario_metros_ab']; ?></td>
          <td><?php echo $row_Pontos['te_itinerario_minutos_ab']; ?></td>
          <td><a href="itinerario.php?rota=<?php echo $colname_Rota; ?>&ponto=<?php echo $row_Pontos['te_itinerario_id']; ?>"><span class="ls-ico-edit-admin ls-ico-right"></span></a></td>
        </tr>
        <?php 
		  $distMetros = $distMetros + $row_Pontos['te_itinerario_metros_ab'];
		  $tempoMinutos = $tempoMinutos + $row_Pontos['te_itinerario_minutos_ab'];
		?>
        <?php } while ($row_Pontos = mysql_fetch_assoc($Pontos)); ?>
   	  </tbody>
    </table>
    
    <?php
	function mintohora($minutos)
	{
	$hora = floor($minutos/60);
	$resto = $minutos%60;
	return $hora.'h'.$resto;
	}
	?>
    
    <div class="ls-box">
	<p>Distância (metros): <?php echo $distMetros; ?> (<?php echo $distMetros/1000; ?> km)</p>
	<p>Tempo (minutos): <?php echo $tempoMinutos; ?> (<?php echo mintohora($tempoMinutos); ?>)</p>
    </div>
    
    <?php } else { ?>
    <hr>
    Nenhum itinerário inserido até o momento.
	<?php } ?>
    
    
<div class="ls-modal" id="myAwesomeModalAtualizar">
  <div class="ls-modal-box">
    <div class="ls-modal-header">
      <a href="pontos.php" data-dismiss="modal">&times;</a>
      <h4 class="ls-modal-title">ATUALIZAR PONTO</h4>
    </div>
    <div class="ls-modal-body" id="myAwesomeModalAtualizar">
    
        <?php if (isset($_GET["editado"])) { ?>
<div class="ls-alert-success">Dados atualizados com sucesso!</div>
<?php } ?>

    <form method="post" name="form2" action="<?php echo $editFormAction; ?>"  class="ls-form ls-form-horizontal row ls-box ls-box-gray1">
      
      <label class="ls-label col-md-12 col-sm-12">
      <b class="ls-label-text">PONTO A</b>
      <div class="ls-custom-select">
      <select name="te_itinerario_pontoa_id" class="ls-select">
            <?php do {  ?>
            <option value="<?php echo $row_pontoAEdit['te_ponto_id']?>" <?php if (!(strcmp($row_pontoAEdit['te_ponto_id'], htmlentities($row_Ponto['te_itinerario_pontoa_id'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>><?php echo $row_pontoAEdit['te_ponto_descricao']?></option>
            <?php } while ($row_pontoAEdit = mysql_fetch_assoc($pontoAEdit)); ?>
          </select>
          </div>
          </label>
          
      <label class="ls-label col-md-12 col-sm-12">
      <b class="ls-label-text">PONTO B</b>
      <div class="ls-custom-select">
      <select name="te_itinerario_pontob_id" class="ls-select">
            <?php do { ?>
            <option value="<?php echo $row_pontoBEdit['te_ponto_id']?>" <?php if (!(strcmp($row_pontoBEdit['te_ponto_id'], htmlentities($row_Ponto['te_itinerario_pontob_id'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>><?php echo $row_pontoBEdit['te_ponto_descricao']?></option>
            <?php } while ($row_pontoBEdit = mysql_fetch_assoc($pontoBEdit)); ?>
          </select>
          </div>
         </label>
          
    <label class="ls-label col-md-6 col-sm-12">
      <b class="ls-label-text">METROS</b>
      <p class="ls-label-info">Distância em metros entre os pontos A e B</p>
          <input type="text" name="te_itinerario_metros_ab" value="<?php echo htmlentities($row_Ponto['te_itinerario_metros_ab'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          
    <label class="ls-label col-md-6 col-sm-12">
      <b class="ls-label-text">TEMPO</b>
      <p class="ls-label-info">Tempo em minutos entre os pontos A e B</p>
          <input type="text" name="te_itinerario_minutos_ab" value="<?php echo htmlentities($row_Ponto['te_itinerario_minutos_ab'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          
                   
      <input type="hidden" name="MM_update" value="form2">
      <input type="hidden" name="te_itinerario_id" value="<?php echo $row_Ponto['te_itinerario_id']; ?>">
    </div>
    <div class="ls-modal-footer">
      <a class="ls-btn ls-float-right" data-dismiss="modal">FECHAR</a>
      <input type="submit" value="SALVAR" class="ls-btn-primary">
       </form>
    </div>
  </div>
</div><!-- /.modal -->   
    
    
    <p>&nbsp;</p>
<div class="ls-modal" id="myAwesomeModal">
  <div class="ls-modal-box">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title">INSERIR ITINERÁRIO</h4>
    </div>
    <div class="ls-modal-body" id="myModalBody">
      
<form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form ls-form-horizontal row ls-box ls-box-gray1">
      
      <label class="ls-label col-md-12 col-sm-12">
      <b class="ls-label-text">PONTO A</b>
      <div class="ls-custom-select">
      <select name="te_itinerario_pontoa_id" class="ls-custom" required>
            <option value="">Escolha...</option>
			<?php do { ?>
            <option value="<?php echo $row_pontoA['te_ponto_id']?>" ><?php echo $row_pontoA['te_ponto_descricao']?></option>
            <?php } while ($row_pontoA = mysql_fetch_assoc($pontoA)); ?>
          </select>
      </div>
    </label>
      
      <label class="ls-label col-md-12 col-sm-12">
      <b class="ls-label-text">PONTO B</b>
      <div class="ls-custom-select">
          <select name="te_itinerario_pontob_id" required>
            <option value="">Escolha...</option>
            <?php do {  ?>
            <option value="<?php echo $row_pontoB['te_ponto_id']?>" ><?php echo $row_pontoB['te_ponto_descricao']?></option>
            <?php } while ($row_pontoB = mysql_fetch_assoc($pontoB)); ?>
          </select>
      </div>
    </label>
      
          
          
    <label class="ls-label col-md-6 col-sm-12">
      <b class="ls-label-text">METROS</b>
      <p class="ls-label-info">Distância em metros entre os pontos A e B</p>
    <input type="text" name="te_itinerario_metros_ab" value="" size="32">
    </label>
    
    <label class="ls-label col-md-6 col-sm-12">
      <b class="ls-label-text">TEMPO</b>
      <p class="ls-label-info">Tempo em minutos entre os pontos A e B</p>
      <input type="text" name="te_itinerario_minutos_ab" value="" size="32">
    </label>
    
         
      <input type="hidden" name="te_itinerario_rota_id" value="<?php echo $row_Rota['te_rota_id']; ?>">
      <input type="hidden" name="te_itinerario_sec_id" value="<?php echo $row_Rota['te_rota_id_sec']; ?>">
      <input type="hidden" name="MM_insert" value="form1">
         
    </div>
    <div class="ls-modal-footer">
      <a class="ls-btn ls-float-right" data-dismiss="modal">FECHAR</a>
      <input type="submit" value="SALVAR" class="ls-btn-primary">
       </form>
    </div>
  </div>
</div><!-- /.modal -->    
    

<p>&nbsp;</p>
<p>&nbsp;</p>
    <!-- CONTEUDO -->    
  </div>
</main>
<?php include_once "notificacoes.php"; ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>
<?php if (isset($_GET["ponto"])) { ?>
	<script>
		locastyle.modal.open("#myAwesomeModalAtualizar");
    </script>
<?php } ?>
</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

mysql_free_result($pontoA);

mysql_free_result($pontoAEdit);

mysql_free_result($pontoB);

mysql_free_result($pontoBEdit);

mysql_free_result($Pontos);
?>