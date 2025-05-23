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
  $insertSQL = sprintf("INSERT INTO smc_te_rota (te_rota_id_sec, te_rota_descricao, te_rota_id_veiculo, te_rota_id_motorista, te_rota_km_total, te_rota_tipo, te_rota_ativa, te_rota_observacoes) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['te_rota_id_sec'], "int"),
                       GetSQLValueString($_POST['te_rota_descricao'], "text"),
                       GetSQLValueString($_POST['te_rota_id_veiculo'], "int"),
                       GetSQLValueString($_POST['te_rota_id_motorista'], "int"),
                       GetSQLValueString($_POST['te_rota_km_total'], "text"),
                       GetSQLValueString($_POST['te_rota_tipo'], "text"),
                       GetSQLValueString($_POST['te_rota_ativa'], "text"),
                       GetSQLValueString($_POST['te_rota_observacoes'], "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

  $insertGoTo = "rotas.php?cadastrado";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form2")) {
  $updateSQL = sprintf("UPDATE smc_te_rota SET te_rota_descricao=%s, te_rota_id_veiculo=%s, te_rota_id_motorista=%s, te_rota_km_total=%s, te_rota_observacoes=%s, te_rota_tipo=%s, te_rota_ativa=%s WHERE te_rota_id=%s",
                       GetSQLValueString($_POST['te_rota_descricao'], "text"),
                       GetSQLValueString($_POST['te_rota_id_veiculo'], "int"),
                       GetSQLValueString($_POST['te_rota_id_motorista'], "int"),
                       GetSQLValueString($_POST['te_rota_km_total'], "text"),
                       GetSQLValueString($_POST['te_rota_observacoes'], "text"),
                       GetSQLValueString($_POST['te_rota_tipo'], "text"),
                       GetSQLValueString($_POST['te_rota_ativa'], "text"),
                       GetSQLValueString($_POST['te_rota_id'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

  $updateGoTo = "rotas.php?editado";
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
$query_Veiculo = "
SELECT te_cad_veiculo_id, te_cad_veiculo_id_sec, 
CASE te_cad_veiculo_tipo
WHEN 1 THEN 'ONIBUS'
WHEN 2 THEN 'MICRO-ONIBUS'
WHEN 3 THEN 'VANS/KOMBI'
WHEN 4 THEN 'BICICLETA'
WHEN 5 THEN 'TRAÇÃO ANIMAL'
WHEN 6 THEN 'BARCO/LANCHA'
WHEN 99 THEN 'OUTROS'
END AS te_cad_veiculo_tipo, te_cad_veiculo_marca_id, te_cad_veiculo_modelo_id, te_cad_veiculo_placa, 
te_cad_veiculo_renavan, te_cad_veiculo_ano_fab, te_cad_veiculo_ano_modelo, te_cad_veiculo_chassi, te_cad_veiculo_tipo_frota, te_cad_veiculo_situacao, 
te_cad_veiculo_limite_passageiros, te_cad_veiculo_adaptado_pne, te_cad_veiculo_obs, te_veiculos_modelo_id, te_veiculos_modelo_nome
FROM smc_te_veiculo
INNER JOIN smc_te_veiculos_modelo ON te_veiculos_modelo_id = te_cad_veiculo_modelo_id
WHERE te_cad_veiculo_id_sec = '$row_Secretaria[sec_id]'
";
$Veiculo = mysql_query($query_Veiculo, $SmecelNovo) or die(mysql_error());
$row_Veiculo = mysql_fetch_assoc($Veiculo);
$totalRows_Veiculo = mysql_num_rows($Veiculo);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_VeiculoEdit = "
SELECT te_cad_veiculo_id, te_cad_veiculo_id_sec, 
CASE te_cad_veiculo_tipo
WHEN 1 THEN 'ONIBUS'
WHEN 2 THEN 'MICRO-ONIBUS'
WHEN 3 THEN 'VANS/KOMBI'
WHEN 4 THEN 'BICICLETA'
WHEN 5 THEN 'TRAÇÃO ANIMAL'
WHEN 6 THEN 'BARCO/LANCHA'
WHEN 99 THEN 'OUTROS'
END AS te_cad_veiculo_tipo, te_cad_veiculo_marca_id, te_cad_veiculo_modelo_id, te_cad_veiculo_placa, 
te_cad_veiculo_renavan, te_cad_veiculo_ano_fab, te_cad_veiculo_ano_modelo, te_cad_veiculo_chassi, te_cad_veiculo_tipo_frota, te_cad_veiculo_situacao, 
te_cad_veiculo_limite_passageiros, te_cad_veiculo_adaptado_pne, te_cad_veiculo_obs, te_veiculos_modelo_id, te_veiculos_modelo_nome
FROM smc_te_veiculo
INNER JOIN smc_te_veiculos_modelo ON te_veiculos_modelo_id = te_cad_veiculo_modelo_id
WHERE te_cad_veiculo_id_sec = '$row_Secretaria[sec_id]'
";
$VeiculoEdit = mysql_query($query_VeiculoEdit, $SmecelNovo) or die(mysql_error());
$row_VeiculoEdit = mysql_fetch_assoc($VeiculoEdit);
$totalRows_VeiculoEdit = mysql_num_rows($VeiculoEdit);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Motorista = "
SELECT func_id, func_usu_tipo, func_id_sec, func_nome, func_cargo, func_situacao, funcao_id, funcao_nome 
FROM smc_func
INNER JOIN smc_funcao ON funcao_id = func_cargo
WHERE func_id_sec = '$row_Secretaria[sec_id]' AND func_situacao = '1' AND funcao_nome LIKE  '%motor%'
ORDER BY func_nome ASC
";
$Motorista = mysql_query($query_Motorista, $SmecelNovo) or die(mysql_error());
$row_Motorista = mysql_fetch_assoc($Motorista);
$totalRows_Motorista = mysql_num_rows($Motorista);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_MotoristaEdit = "
SELECT func_id, func_usu_tipo, func_id_sec, func_nome, func_cargo, func_situacao, funcao_id, funcao_nome 
FROM smc_func
INNER JOIN smc_funcao ON funcao_id = func_cargo
WHERE func_id_sec = '$row_Secretaria[sec_id]' AND func_situacao = '1' AND funcao_nome LIKE  '%motor%'
ORDER BY func_nome ASC
";
$MotoristaEdit = mysql_query($query_MotoristaEdit, $SmecelNovo) or die(mysql_error());
$row_MotoristaEdit = mysql_fetch_assoc($MotoristaEdit);
$totalRows_MotoristaEdit = mysql_num_rows($MotoristaEdit);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Rotas = "
SELECT te_rota_id, te_rota_id_sec, te_rota_descricao, te_rota_id_veiculo, te_rota_id_motorista, te_rota_km_total, te_rota_observacoes, 
te_rota_tipo, te_rota_ativa, func_id, func_nome, te_cad_veiculo_id, 
CASE te_cad_veiculo_tipo
WHEN 1 THEN 'ONIBUS'
WHEN 2 THEN 'MICRO-ONIBUS'
WHEN 3 THEN 'VANS/KOMBI'
WHEN 4 THEN 'BICICLETA'
WHEN 5 THEN 'TRAÇÃO ANIMAL'
WHEN 6 THEN 'BARCO/LANCHA'
WHEN 99 THEN 'OUTROS'
END AS te_cad_veiculo_tipo, te_cad_veiculo_placa
FROM smc_te_rota
INNER JOIN smc_func ON func_id = te_rota_id_motorista 
INNER JOIN smc_te_veiculo ON te_cad_veiculo_id = te_rota_id_veiculo
WHERE te_rota_id_sec = '$row_Secretaria[sec_id]' 
ORDER BY te_rota_descricao ASC";
$Rotas = mysql_query($query_Rotas, $SmecelNovo) or die(mysql_error());
$row_Rotas = mysql_fetch_assoc($Rotas);
$totalRows_Rotas = mysql_num_rows($Rotas);

$colname_RotaEdit = "-1";
if (isset($_GET['rota'])) {
  $colname_RotaEdit = $_GET['rota'];

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_RotaEdit = sprintf("SELECT te_rota_id, te_rota_id_sec, te_rota_descricao, te_rota_id_veiculo, te_rota_id_motorista, te_rota_km_total, te_rota_observacoes, te_rota_tipo, te_rota_ativa FROM smc_te_rota WHERE te_rota_id = %s", GetSQLValueString($colname_RotaEdit, "int"));
$RotaEdit = mysql_query($query_RotaEdit, $SmecelNovo) or die(mysql_error());
$row_RotaEdit = mysql_fetch_assoc($RotaEdit);
$totalRows_RotaEdit = mysql_num_rows($RotaEdit);

if ($totalRows_RotaEdit  < 1) {
	$red = "index.php?erro&cod=rotaqyr43ai";
	header(sprintf("Location: %s", $red));
	}
mysql_free_result($RotaEdit);

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
    <h1 class="ls-title-intro ls-ico-flag">ROTAS</h1>
    <!-- CONTEUDO -->
    
    <button data-ls-module="modal" data-target="#myAwesomeModal" class="ls-btn-primary ls-ico-plus">CADASTRAR ITINERÁRIO</button>
    
    <hr>
    
<?php if ($totalRows_Rotas > 0) { ?>    
    <table class="ls-table">
      <thead>
      <tr>
        <th>ROTA</th>
        <th>PLACA/VEÍCULO</th>
        <th>MOTORISTA</th>
        <th width="50"></th>
      </tr>
      </thead>
      <tbody>
      <?php do { ?>
      
      <?php 
  
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Itinerario = "
SELECT te_itinerario_id, te_itinerario_rota_id, te_itinerario_sec_id, te_itinerario_pontoa_id, te_itinerario_pontob_id, te_itinerario_metros_ab, 
te_itinerario_minutos_ab, te_ponto_id, te_ponto_descricao
FROM smc_te_itinerario
INNER JOIN smc_te_ponto ON te_ponto_id = te_itinerario_pontoa_id
WHERE te_itinerario_rota_id = $row_Rotas[te_rota_id]
";
$Itinerario = mysql_query($query_Itinerario, $SmecelNovo) or die(mysql_error());
$row_Itinerario = mysql_fetch_assoc($Itinerario);
$totalRows_Itinerario = mysql_num_rows($Itinerario);

	 
	  ?>
      
        <tr>
          <td><?php echo $row_Rotas['te_rota_descricao']; ?> - <small><a href="itinerario.php?rota=<?php echo $row_Rotas['te_rota_id']; ?>">Adicionar itinerário</a></small><br>
          <?php if ($totalRows_Itinerario > 0) { ?>
		  <?php do { ?>
          <small><span class="" style="margin:2px; display:block; float:left; padding:2px; background-color:#ECECFF;"><?php echo $row_Itinerario['te_ponto_descricao']; ?></span></small> 
           <?php } while ($row_Itinerario = mysql_fetch_assoc($Itinerario)); ?>
           <?php } ?>
          </td>
          <td><?php echo $row_Rotas['te_cad_veiculo_placa']; ?> - <?php echo $row_Rotas['te_cad_veiculo_tipo']; ?></td>
          <td><?php echo $row_Rotas['func_nome']; ?></td>
          <td><a href="rotas.php?rota=<?php echo $row_Rotas['te_rota_id']; ?>"><span class="ls-ico-edit-admin ls-ico-right"></span></a></td>
        </tr>
        <?php } while ($row_Rotas = mysql_fetch_assoc($Rotas)); ?>
    	</tbody>
    </table>
    
    <?php } else { ?>
    <div class="ls-alert-info">Nenhuma rota cadastrada</div>
    <?php } ?>
    
<p>&nbsp;</p>


<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
  <!-- CONTEUDO -->
  </div>
</main>
<?php include_once "notificacoes.php"; ?>

<div class="ls-modal" id="myAwesomeModalAtualizar">
  <div class="ls-modal-large">
    <div class="ls-modal-header">
      <a href="pontos.php" data-dismiss="modal">&times;</a>
      <h4 class="ls-modal-title">ATUALIZAR ITINERÁRIO</h4>
    </div>
    <div class="ls-modal-body" id="myAwesomeModalAtualizar">
    


<form method="post" name="form2" action="<?php echo $editFormAction; ?>" class="ls-form ls-form-horizontal row">
  
  <label class="ls-label col-md-12 col-sm-12"> 
          <b class="ls-label-text">DESCRIÇÃO</b>
          <p class="ls-label-info">Informe um nome que identifique a rota</p>
          <input type="text" name="te_rota_descricao" value="<?php echo htmlentities($row_RotaEdit['te_rota_descricao'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          
  
  	  <label class="ls-label col-md-6 col-sm-12">
          <b class="ls-label-text">PLACA/VEÍCULO</b>
          <div class="ls-custom-select">
            <select name="te_rota_id_veiculo" class="ls-select" required>
        <?php do { ?>
        <option value="<?php echo $row_VeiculoEdit['te_cad_veiculo_id']?>" <?php if (!(strcmp($row_VeiculoEdit['te_cad_veiculo_id'], htmlentities($row_RotaEdit['te_rota_id_veiculo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>><?php echo $row_VeiculoEdit['te_cad_veiculo_placa']?> - <?php echo $row_VeiculoEdit['te_cad_veiculo_tipo']?> - <?php echo $row_VeiculoEdit['te_veiculos_modelo_nome']?></option>
        <?php } while ($row_VeiculoEdit = mysql_fetch_assoc($VeiculoEdit)); ?>
      </select>
      </div>
      </label>
      
   	 <label class="ls-label col-md-6 col-sm-12">
          <b class="ls-label-text">MOTORISTA</b>
          <div class="ls-custom-select">
            <select name="te_rota_id_motorista" class="ls-select">
        <?php do { ?>
        <option value="<?php echo $row_MotoristaEdit['func_id']?>" <?php if (!(strcmp($row_MotoristaEdit['func_id'], htmlentities($row_RotaEdit['te_rota_id_motorista'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>><?php echo $row_MotoristaEdit['func_nome']?></option>
        <?php } while ($row_MotoristaEdit = mysql_fetch_assoc($MotoristaEdit)); ?>
      </select>
      </div>
      </label>
      
      <label class="ls-label col-md-6 col-sm-12"> 
          <b class="ls-label-text">KM TOTAL</b>
          <p class="ls-label-info">Total percorrido por dia</p>
          <input type="text" name="te_rota_km_total" value="<?php echo htmlentities($row_RotaEdit['te_rota_km_total'], ENT_COMPAT, 'utf-8'); ?>" size="32">
      </label>
      
     <label class="ls-label col-md-6 col-sm-12">
          <b class="ls-label-text">TIPO</b>
          <div class="ls-custom-select">
            <select name="te_rota_tipo" class="ls-select">
        <option value="R" <?php if (!(strcmp("R", htmlentities($row_RotaEdit['te_rota_tipo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>RURAL</option>
        <option value="U" <?php if (!(strcmp("U", htmlentities($row_RotaEdit['te_rota_tipo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>URBANA</option>
      </select>
      </div>
      </label>
      
      <label class="ls-label col-sm-12">
            <p><b class="ls-label-text">SITUAÇÃO</b></p>
            <label class="ls-label-text">
              <input type="radio" name="te_rota_ativa" value="1" <?php if (!(strcmp(htmlentities($row_RotaEdit['te_rota_ativa'], ENT_COMPAT, 'utf-8'),1))) {echo "checked=\"checked\"";} ?>>
              1 - ATIVA </label>
            <label class="ls-label-text">
              <input type="radio" name="te_rota_ativa" value="2" <?php if (!(strcmp(htmlentities($row_RotaEdit['te_rota_ativa'], ENT_COMPAT, 'utf-8'),2))) {echo "checked=\"checked\"";} ?>>
              2 - INATIVA </label>
          </label>
      
      <label class="ls-label col-sm-12"> 
          <b class="ls-label-text">OBSERVAÇÕES</b>
          <textarea name="te_rota_observacoes" cols="50" rows="2"><?php echo htmlentities($row_RotaEdit['te_rota_observacoes'], ENT_COMPAT, 'utf-8'); ?></textarea>
      </label>
      
  
      
  <input type="hidden" name="MM_update" value="form2">
  <input type="hidden" name="te_rota_id" value="<?php echo $row_RotaEdit['te_rota_id']; ?>">
    </div>
    

    <div class="ls-modal-footer">
      <input type="submit" value="ATUALIZAR PONTO" class="ls-btn-primary">
      <a href="rotas.php" class="ls-btn ls-float-right" data-dismiss="modal">FECHAR</a>
      
<?php if (isset($_GET["editado"])) { ?>
<br><br>
<div class="ls-alert-success">Dados atualizados com sucesso!</div>
<?php } ?>
      
    </div> 
    
    </form>
  </div>
</div><!-- /.modal -->


    <div class="ls-modal" id="myAwesomeModal">
      <div class="ls-modal-large">
        <div class="ls-modal-header">
          <button data-dismiss="modal">&times;</button>
          <h4 class="ls-modal-title">CADASTRAR ROTA</h4>
        </div>
        <div class="ls-modal-body" id="myModalBody">
        
        <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form ls-form-horizontal row">
          <label class="ls-label col-md-12 col-sm-12"> 
          <b class="ls-label-text">DESCRIÇÃO</b>
          <p class="ls-label-info">Informe um nome que identifique a rota</p>
            <input type="text" name="te_rota_descricao" value="" size="32" required>
          </label>
          <label class="ls-label col-md-6 col-sm-12">
          <b class="ls-label-text">PLACA/VEÍCULO <?php if ($totalRows_Veiculo==0) { ?> <a href="veiculos.php"> (Incluir veículo)</a><?php } ?></b>
          <div class="ls-custom-select">
            <select name="te_rota_id_veiculo" class="ls-select" required>
              <option value="">ESCOLHA...</option>
              <?php do { ?>
              <option value="<?php echo $row_Veiculo['te_cad_veiculo_id']?>" ><?php echo $row_Veiculo['te_cad_veiculo_placa']?> - <?php echo $row_Veiculo['te_cad_veiculo_tipo']?> - <?php echo $row_Veiculo['te_veiculos_modelo_nome']?></option>
              <?php } while ($row_Veiculo = mysql_fetch_assoc($Veiculo)); ?>
            </select>
          </div>
          </label>
          <label class="ls-label col-md-6 col-sm-12">
          <b class="ls-label-text">MOTORISTA</b>
          <div class="ls-custom-select">
            <select name="te_rota_id_motorista" class="ls-select" required>
             <option value="">ESCOLHA...</option>
              <?php do { ?>
              <option value="<?php echo $row_Motorista['func_id']?>" ><?php echo $row_Motorista['func_nome']?></option>
              <?php } while ($row_Motorista = mysql_fetch_assoc($Motorista)); ?>
            </select>
          </div>
          </label>
          <label class="ls-label col-md-6 col-sm-12"> 
          <b class="ls-label-text">KM TOTAL</b>
          <p class="ls-label-info">Total percorrido por dia</p>
            <input type="text" name="te_rota_km_total" value="" size="32">
          </label>
          
          <label class="ls-label col-md-6 col-sm-12">
          <b class="ls-label-text">TIPO</b>
          <div class="ls-custom-select">
            <select name="te_rota_tipo" class="ls-select">
              <option value="R" <?php if (!(strcmp("R", ""))) {echo "SELECTED";} ?>>RURAL</option>
              <option value="U" <?php if (!(strcmp("U", ""))) {echo "SELECTED";} ?>>URBANA</option>
            </select>
          </div>
          </label>
          
          <label class="ls-label col-sm-12">
            <p><b class="ls-label-text">SITUAÇÃO</b></p>
            <label class="ls-label-text">
              <input type="radio" name="te_rota_ativa" value="1" <?php if (!(strcmp(1,1))) {echo "checked=\"checked\"";} ?>>
              1 - A TIVA </label>
            <label class="ls-label-text">
              <input type="radio" name="te_rota_ativa" value="2" <?php if (!(strcmp(1,2))) {echo "checked=\"checked\"";} ?>>
              2 - INATIVA </label>
          </label>
          
          <label class="ls-label col-sm-12"> 
          <b class="ls-label-text">OBSERVAÇÕES</b>
            <textarea name="te_rota_observacoes" cols="50" rows="2" class="ls-textarea-autoresize"></textarea>
          </label>
		  
		  
          <input type="hidden" name="te_rota_id_sec" value="<?php echo $row_Secretaria['sec_id']; ?>">
          <input type="hidden" name="MM_insert" value="form1">
          
          </div>
          
          <div class="ls-modal-footer">
          <a class="ls-btn ls-float-right" data-dismiss="modal">FECHAR</a>
          <input type="submit" value="SALVAR"  class="ls-btn-primary">
          </div>
	  </form>
    </div>
  </div>
  <!-- /.modal -->


<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>
<?php if (isset($_GET["rota"])) { ?>
	<script>
		locastyle.modal.open("#myAwesomeModalAtualizar");
    </script>
<?php } ?>
</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

mysql_free_result($Itinerario);

mysql_free_result($Rotas);

mysql_free_result($Veiculo);

mysql_free_result($Motorista);

mysql_free_result($VeiculoEdit);

mysql_free_result($MotoristaEdit);
?>