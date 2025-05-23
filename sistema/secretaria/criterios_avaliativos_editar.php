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


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE smc_criterios_avaliativos SET ca_id_secretaria=%s, ca_descricao=%s, ca_qtd_periodos=%s, ca_qtd_av_periodos=%s, ca_nota_min_av=%s, ca_nota_max_av=%s, ca_calculo_media_periodo=%s, ca_media_min_periodo=%s, ca_arredonda_media=%s, ca_aproxima_media=%s, ca_digitos=%s, ca_min_pontos_aprovacao_final=%s, ca_min_media_aprovacao_final=%s, ca_nota_min_recuperacao_final=%s, ca_detalhes=%s, ca_grupo_etario=%s, ca_grupo_conceito=%s, ca_rec_paralela=%s, ca_questionario_conceitos=%s, ca_etapa_id=%s WHERE ca_id=%s",
                       GetSQLValueString($_POST['ca_id_secretaria'], "int"),
                       GetSQLValueString($_POST['ca_descricao'], "text"),
                       GetSQLValueString($_POST['ca_qtd_periodos'], "text"),
                       GetSQLValueString($_POST['ca_qtd_av_periodos'], "text"),
                       GetSQLValueString($_POST['ca_nota_min_av'], "double"),
                       GetSQLValueString($_POST['ca_nota_max_av'], "double"),
                       GetSQLValueString($_POST['ca_calculo_media_periodo'], "text"),
                       GetSQLValueString($_POST['ca_media_min_periodo'], "double"),
                       GetSQLValueString($_POST['ca_arredonda_media'], "text"),
                       GetSQLValueString($_POST['ca_aproxima_media'], "text"),
                       GetSQLValueString($_POST['ca_digitos'], "text"),
                       GetSQLValueString($_POST['ca_min_pontos_aprovacao_final'], "double"),
                       GetSQLValueString($_POST['ca_min_media_aprovacao_final'], "double"),
                       GetSQLValueString($_POST['ca_nota_min_recuperacao_final'], "double"),
                       GetSQLValueString($_POST['ca_detalhes'], "text"),
					   GetSQLValueString($_POST['ca_grupo_etario'], "text"),
					   GetSQLValueString($_POST['ca_grupo_conceito'], "text"),
					   GetSQLValueString($_POST['ca_rec_paralela'], "text"),
					   GetSQLValueString($_POST['ca_questionario_conceitos'], "text"),
					   GetSQLValueString($_POST['ca_etapa_id'], "text"),
                       GetSQLValueString($_POST['ca_id'], "int"));
					   

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

  $updateGoTo = "criterios_avaliativos.php?editado";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

$colname_CriteriosAvaliativos = "-1";
if (isset($_GET['codigo'])) {
  $colname_CriteriosAvaliativos = $_GET['codigo'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_CriteriosAvaliativos = sprintf("SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes, ca_forma_avaliacao, ca_grupo_etario, ca_rec_paralela, ca_grupo_conceito, ca_questionario_conceitos, ca_etapa_id, ca_digitos FROM smc_criterios_avaliativos WHERE ca_id_secretaria = '$row_UsuarioLogado[usu_sec]' AND ca_id = %s", GetSQLValueString($colname_CriteriosAvaliativos, "int"));
$CriteriosAvaliativos = mysql_query($query_CriteriosAvaliativos, $SmecelNovo) or die(mysql_error());
$row_CriteriosAvaliativos = mysql_fetch_assoc($CriteriosAvaliativos);
$totalRows_CriteriosAvaliativos = mysql_num_rows($CriteriosAvaliativos);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Etapa = "SELECT etapa_id, etapa_id_filtro, etapa_nome, etapa_idade, etapa_limite_turma, etapa_nome_abrev, etapa_ano_ef FROM smc_etapa WHERE etapa_id_filtro = '3'";
$Etapa = mysql_query($query_Etapa, $SmecelNovo) or die(mysql_error());
$row_Etapa = mysql_fetch_assoc($Etapa);
$totalRows_Etapa = mysql_num_rows($Etapa);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Conceitos = "SELECT conceito_id, conceito_id_sec, conceito_descricao, conceito_observacao, conceito_hash FROM smc_conceito WHERE conceito_id_sec = '$row_Secretaria[sec_id]'";
$Conceitos = mysql_query($query_Conceitos, $SmecelNovo) or die(mysql_error());
$row_Conceitos = mysql_fetch_assoc($Conceitos);
$totalRows_Conceitos = mysql_num_rows($Conceitos);

if ($totalRows_CriteriosAvaliativos < 1) {
	$semEscolas = "escolas.php?erro";
	header(sprintf("Location: %s", $semEscolas));
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
    <h1 class="ls-title-intro ls-ico-home">CRITÉRIO AVALIATIVO</h1>
    <div class="ls-box ls-board-box"> 
    <!-- CONTEUDO -->
    <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form">
    
    
          <label class="ls-label col-md-12 col-sm-12">
          <b class="ls-label-text">FORMA DE AVALIAÇÃO</b>
          <a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Descreva um nome que irá identificar o critério avaliativo. Ex.: CRITÉRIO AVALIATIVO ANUAL PADRÃO" data-title="AJUDA"></a>
          <input type="text" name="ca_forma_avaliacao" value="<?php if ($row_CriteriosAvaliativos['ca_forma_avaliacao']=="N") { echo "NOTA"; } elseif ($row_CriteriosAvaliativos['ca_forma_avaliacao']=="C") { echo "CONCEITO"; }elseif ($row_CriteriosAvaliativos['ca_forma_avaliacao']=="Q") { echo "QUALITATIVA E QUANTITATIVA"; } ?>" size="32" disabled>
          </label>
          
          <label class="ls-label col-md-12 col-sm-12">
          <b class="ls-label-text">DESCRIÇÃO</b>
          <a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Descreva um nome que irá identificar o critério avaliativo. Ex.: CRITÉRIO AVALIATIVO ANUAL PADRÃO" data-title="AJUDA"></a>
          <input type="text" name="ca_descricao" value="<?php echo htmlentities($row_CriteriosAvaliativos['ca_descricao'], ENT_COMPAT, 'utf-8'); ?>" size="32" required>
          </label>
          
          <label class="ls-label col-md-12 col-sm-12">
          <b class="ls-label-text">PERÍODOS POR ANO</b>
          <a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Quantidade de períodos (Unidades, Bimestres, Trimestres, etc) por ano." data-title="AJUDA"></a>
          <div class="ls-custom-select">
            <select name="ca_qtd_periodos" class="ls-select" required>
            <option value="" selected>Escolha...</option>
            <option value="1" <?php if (!(strcmp(1, htmlentities($row_CriteriosAvaliativos['ca_qtd_periodos'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>1</option>
            <option value="2" <?php if (!(strcmp(2, htmlentities($row_CriteriosAvaliativos['ca_qtd_periodos'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>2</option>
            <option value="3" <?php if (!(strcmp(3, htmlentities($row_CriteriosAvaliativos['ca_qtd_periodos'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>3</option>
            <option value="4" <?php if (!(strcmp(4, htmlentities($row_CriteriosAvaliativos['ca_qtd_periodos'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>4</option>
            <option value="5" <?php if (!(strcmp(5, htmlentities($row_CriteriosAvaliativos['ca_qtd_periodos'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>5</option>
            <option value="6" <?php if (!(strcmp(6, htmlentities($row_CriteriosAvaliativos['ca_qtd_periodos'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>6</option>
            </select>
          </div>
          </label>
          
          <div style="display:<?php if ($row_CriteriosAvaliativos['ca_forma_avaliacao']=="N") { echo "block"; } else { echo "none"; } ?>">
          
          <label class="ls-label col-md-12 col-sm-12">
          <b class="ls-label-text">AVALIAÇÕES POR PERÍODO</b>
          <a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Informe a quantidade de avaliações por período." data-title="AJUDA"></a>
          <div class="ls-custom-select">
            <select name="ca_qtd_av_periodos" class="ls-select">
            <option value="" selected>Escolha...</option>
            <option value="1" <?php if (!(strcmp(1, htmlentities($row_CriteriosAvaliativos['ca_qtd_av_periodos'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>1</option>
            <option value="2" <?php if (!(strcmp(2, htmlentities($row_CriteriosAvaliativos['ca_qtd_av_periodos'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>2</option>
            <option value="3" <?php if (!(strcmp(3, htmlentities($row_CriteriosAvaliativos['ca_qtd_av_periodos'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>3</option>
            <option value="4" <?php if (!(strcmp(4, htmlentities($row_CriteriosAvaliativos['ca_qtd_av_periodos'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>4</option>
            <option value="5" <?php if (!(strcmp(5, htmlentities($row_CriteriosAvaliativos['ca_qtd_av_periodos'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>5</option>
            <option value="6" <?php if (!(strcmp(6, htmlentities($row_CriteriosAvaliativos['ca_qtd_av_periodos'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>6</option>
            </select>
          </div>
          </label>
                    
          <label class="ls-label col-md-12 col-sm-12">
          <b class="ls-label-text">MÍNIMO DE AVALIAÇÃO P/ APROVAÇÃO</b>
          <a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Informe o valor mínimo de nota por avaliação necessária para que o aluno seja aprovado. (Ex.: 5,00)" data-title="AJUDA"></a>
          <input type="number" name="ca_nota_min_av" size="5" max="10" step="0.01" class="nota" value="<?php echo htmlentities($row_CriteriosAvaliativos['ca_nota_min_av'], ENT_COMPAT, 'utf-8'); ?>">
          </label>
          
          <label class="ls-label col-md-12 col-sm-12">
          <b class="ls-label-text">NOTA MÁXIMA</b>
          <a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Informe o valor máximo de nota por avaliação. (Ex.: 10,00)" data-title="AJUDA"></a>
          <input type="number" name="ca_nota_max_av" size="5" max="10" step="0.01" class="nota" value="<?php echo htmlentities($row_CriteriosAvaliativos['ca_nota_max_av'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          
          <label class="ls-label col-md-12 col-sm-12">
          <b class="ls-label-text">TIPO DE CÁLCULO</b>
          <a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Cálculo para a média do período." data-title="AJUDA"></a>
          <div class="ls-custom-select">
            <select name="ca_calculo_media_periodo" class="ls-select">
            <option value="" selected>Escolha...</option>            
            <option value="MEDIA" <?php if (!(strcmp("MEDIA", htmlentities($row_CriteriosAvaliativos['ca_calculo_media_periodo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>MEDIA</option>
            <option value="SOMA" <?php if (!(strcmp("SOMA", htmlentities($row_CriteriosAvaliativos['ca_calculo_media_periodo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>SOMA</option>
            </select>
          </div>
          </label>
          
          <label class="ls-label col-md-12 col-sm-12">
          <b class="ls-label-text">RECUPERAÇÃO PARALELA</b>
          <a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Permite lançar nota de recupeação paralela por unidade." data-title="AJUDA"></a>
          <div class="ls-custom-select">
            <select name="ca_rec_paralela" class="ls-select">
            <option value="" selected>Escolha...</option>            
            <option value="S" <?php if (!(strcmp("S", htmlentities($row_CriteriosAvaliativos['ca_rec_paralela'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>SIM</option>
            <option value="N" <?php if (!(strcmp("N", htmlentities($row_CriteriosAvaliativos['ca_rec_paralela'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
          </div>
          </label>
          
          <label class="ls-label col-md-12 col-sm-12">
          <b class="ls-label-text">MÉDIA MÍNIMA POR PERÍODO</b>
          <a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Valor mínimo para aprovação na média por período. (Ex.: 6,00)" data-title="AJUDA"></a>
          <input type="number" name="ca_media_min_periodo" size="5" max="100" step="0.01" class="nota" value="<?php echo htmlentities($row_CriteriosAvaliativos['ca_media_min_periodo'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>

          <label class="ls-label col-md-12 col-sm-12">
          <b class="ls-label-text">ARREDONDA MÉDIA?</b>
          <a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Se o campo Tipo de Cálculo for MÉDIA, o valor será arredondado." data-title="AJUDA"></a>
          <div class="ls-custom-select">
            <select name="ca_arredonda_media" class="ls-select">
            <option value="" selected>Escolha...</option>
            <option value="S" <?php if (!(strcmp("S", htmlentities($row_CriteriosAvaliativos['ca_arredonda_media'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>SIM</option>
            <option value="N" <?php if (!(strcmp("N", htmlentities($row_CriteriosAvaliativos['ca_arredonda_media'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
          </div>
          </label>
          
          <label class="ls-label col-md-12 col-sm-12">
          <b class="ls-label-text">APROXIMA MÉDIA?</b>
          <a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Se o campo Tipo de Cálculo for MÉDIA, o valor será aproximado para cima." data-title="AJUDA"></a>
          <div class="ls-custom-select">
            <select name="ca_aproxima_media" class="ls-select">
            <option value="" selected>Escolha...</option>
            <option value="S" <?php if (!(strcmp("S", htmlentities($row_CriteriosAvaliativos['ca_aproxima_media'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>SIM</option>
            <option value="N" <?php if (!(strcmp("N", htmlentities($row_CriteriosAvaliativos['ca_aproxima_media'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
          </div>
          </label>
          
          <label class="ls-label col-md-12 col-sm-12">
          <b class="ls-label-text">CASAS DECIMAIS APÓS A VÍRGULA</b>
          <a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Quantidade de casas decimais após a vírgula para exibição na média. Ex.: 7.6 ou 7.63" data-title="AJUDA"></a>
          <div class="ls-custom-select">
            <select name="ca_digitos" class="ls-select">
            <option value="" selected>Escolha...</option>
            <option value="1" <?php if (!(strcmp(1, htmlentities($row_CriteriosAvaliativos['ca_digitos'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>1</option>
            <option value="2" <?php if (!(strcmp(2, htmlentities($row_CriteriosAvaliativos['ca_digitos'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>2</option>
            </select>
          </div>
          </label>
          
          <label class="ls-label col-md-12 col-sm-12">
          <b class="ls-label-text">TOTAL DE PONTOS (ANUAL)</b>
          <a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Mínimo de pontos necessários para aprovação. (Ex.: 24,00)" data-title="AJUDA"></a>
          <input type="number" name="ca_min_pontos_aprovacao_final" size="5" step="0.01" class="nota" value="<?php echo htmlentities($row_CriteriosAvaliativos['ca_min_pontos_aprovacao_final'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          
          <label class="ls-label col-md-12 col-sm-12">
          <b class="ls-label-text">MÉDIA FINAL</b>
          <a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Média mínima para aprovação final. (Ex.: 6,00)" data-title="AJUDA"></a>
          <input type="number" name="ca_min_media_aprovacao_final" size="5" max="10" step="0.01" class="nota" value="<?php echo htmlentities($row_CriteriosAvaliativos['ca_min_media_aprovacao_final'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          
          <label class="ls-label col-md-12 col-sm-12">
          <b class="ls-label-text">NOTA DE AVALIAÇÃO FINAL</b>
          <a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Nota mínima para aprovação na avaliação final. (Ex.: 5,00)" data-title="AJUDA"></a>
          <input type="number" name="ca_nota_min_recuperacao_final" size="5" max="10" step="0.01" class="nota" value="<?php echo htmlentities($row_CriteriosAvaliativos['ca_nota_min_recuperacao_final'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          
          </div>
          
          <div style="display:<?php if ($row_CriteriosAvaliativos['ca_forma_avaliacao']=="C") { echo "block"; } else { echo "none"; } ?>">
          
          <label class="ls-label col-md-12 col-sm-12">
          <b class="ls-label-text">GRUPO ETÁRIO</b>
          <a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Quantidade de períodos (Unidades, Bimestres, Trimestres, etc) por ano." data-title="AJUDA"></a>
          <div class="ls-custom-select">
            <select name="ca_grupo_etario" class="ls-select">
            <option value="" selected>Escolha...</option>
            <option value="1" <?php if (!(strcmp(1, htmlentities($row_CriteriosAvaliativos['ca_grupo_etario'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Bebês (zero a 1 ano e 6 meses)</option>
            <option value="2" <?php if (!(strcmp(2, htmlentities($row_CriteriosAvaliativos['ca_grupo_etario'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Crianças bem pequenas (1 ano e 7 meses a 3 anos e 11 meses)</option>
            <option value="3" <?php if (!(strcmp(3, htmlentities($row_CriteriosAvaliativos['ca_grupo_etario'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Crianças pequenas (4 anos a 5 anos e 11 meses)</option>
            <option value="4" <?php if (!(strcmp(4, htmlentities($row_CriteriosAvaliativos['ca_grupo_etario'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Fundamental Anos Iniciais</option>
            <option value="5" <?php if (!(strcmp(5, htmlentities($row_CriteriosAvaliativos['ca_grupo_etario'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Fundamental Anos Finais</option>
            <option value="6" <?php if (!(strcmp(6, htmlentities($row_CriteriosAvaliativos['ca_grupo_etario'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>EPJAI</option>

            </select>
          </div>
          </label>
          
          
          <label class="ls-label col-md-12 col-sm-12">
          <b class="ls-label-text">GRUPO DE CONCEITOS</b>
          <a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Informe o grupo de conceitos." data-title="AJUDA"></a>
          <div class="ls-custom-select">
            <select name="ca_grupo_conceito" class="ls-select">
            <option value="" selected>ESCOLHA...</option>
            <?php do { ?>
            <option value="<?php echo $row_Conceitos['conceito_id']?>" <?php if (!(strcmp($row_Conceitos['conceito_id'], htmlentities($row_CriteriosAvaliativos['ca_grupo_conceito'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>><?php echo $row_Conceitos['conceito_descricao']?></option>
            <?php } while ($row_Conceitos = mysql_fetch_assoc($Conceitos)); ?>
            </select>
          </div>
          </label>
          
          
          <label class="ls-label col-md-12 col-sm-12">
          <b class="ls-label-text">CADASTRAR QUESTIONÁRIO DE CONCEITOS?</b>
          <a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Informe a etapa que se aplica ao questionário de conceitos." data-title="AJUDA"></a>
          <div class="ls-custom-select">
			<select name="ca_questionario_conceitos" class="ls-select">
            <option value="S" <?php if (!(strcmp("S", htmlentities($row_CriteriosAvaliativos['ca_questionario_conceitos'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>SIM</option>
            <option value="N" <?php if (!(strcmp("N", htmlentities($row_CriteriosAvaliativos['ca_questionario_conceitos'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
          </div>
          </label>


         <label class="ls-label col-md-12 col-sm-12">
          <b class="ls-label-text">ETAPA</b>
          <a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Informe a etapa que se aplica ao questionário de conceitos." data-title="AJUDA"></a>
          <div class="ls-custom-select">
            <select name="ca_etapa_id" class="ls-select">
            <option value="" selected>ESCOLHA...</option>
            <?php do { ?>
            <option value="<?php echo $row_Etapa['etapa_id']; ?>"  <?php if (!(strcmp($row_Etapa['etapa_id'], htmlentities($row_CriteriosAvaliativos['ca_etapa_id'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?> ><?php echo $row_Etapa['etapa_nome']; ?></option>
            
            <?php } while ($row_Etapa = mysql_fetch_assoc($Etapa)); ?>
            </select>
          </div>
          </label>
          
          
          </div>
          
          <label class="ls-label col-md-12"> <b class="ls-label-text">DETALHES DO CRITÉRIO DE AVALIAÇÃO</b>
          <a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Descreva os detalhes do critério avaliativo" data-title="AJUDA"></a>
            <textarea name="ca_detalhes" rows="5" class="ls-textarea-autoresize"><?php echo htmlentities($row_CriteriosAvaliativos['ca_detalhes'], ENT_COMPAT, 'utf-8'); ?></textarea>
          </label>

          <div class="ls-actions-btn">
            <input type="submit" value="SALVAR" class="ls-btn-primary">
            <a class="ls-btn-danger" href="criterios_avaliativos.php">CANCELAR</a>
          </div>

      <input type="hidden" name="ca_id" value="<?php echo $row_CriteriosAvaliativos['ca_id']; ?>">
      <input type="hidden" name="ca_id_secretaria" value="<?php echo htmlentities($row_CriteriosAvaliativos['ca_id_secretaria'], ENT_COMPAT, 'utf-8'); ?>">
      <input type="hidden" name="MM_update" value="form1">
    </form>
    <p>&nbsp;</p>
    <!-- CONTEUDO -->    
    </div>
  </div>
</main>
<?php include_once "notificacoes.php"; ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>
</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

mysql_free_result($CriteriosAvaliativos);
?>