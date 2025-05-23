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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Conceitos = "SELECT conceito_id, conceito_id_sec, conceito_descricao, conceito_observacao, conceito_hash FROM smc_conceito WHERE conceito_id_sec = '$row_Secretaria[sec_id]'";
$Conceitos = mysql_query($query_Conceitos, $SmecelNovo) or die(mysql_error());
$row_Conceitos = mysql_fetch_assoc($Conceitos);
$totalRows_Conceitos = mysql_num_rows($Conceitos);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Etapa = "SELECT etapa_id, etapa_id_filtro, etapa_nome, etapa_idade, etapa_limite_turma, etapa_nome_abrev, etapa_ano_ef FROM smc_etapa WHERE etapa_id_filtro = '3'";
$Etapa = mysql_query($query_Etapa, $SmecelNovo) or die(mysql_error());
$row_Etapa = mysql_fetch_assoc($Etapa);
$totalRows_Etapa = mysql_num_rows($Etapa);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO smc_criterios_avaliativos (ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_digitos, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_forma_avaliacao, ca_grupo_etario, ca_grupo_conceito, ca_rec_paralela, ca_questionario_conceitos, ca_etapa_id, ca_detalhes) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
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
                       GetSQLValueString($_POST['ca_forma_avaliacao'], "text"),
                       GetSQLValueString($_POST['ca_grupo_etario'], "text"),
                       GetSQLValueString($_POST['ca_grupo_conceito'], "text"),
                       GetSQLValueString($_POST['ca_rec_paralela'], "text"),
                       GetSQLValueString($_POST['ca_questionario_conceitos'], "text"),
                       GetSQLValueString($_POST['ca_etapa_id'], "text"),
					   GetSQLValueString($_POST['ca_detalhes'], "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

  $insertGoTo = "criterios_avaliativos.php?cadastrado";
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
    <h1 class="ls-title-intro ls-ico-home">CRITÉRIOS AVALIATIVOS</h1>
    <div class=""> 
      <!-- CONTEUDO -->
      
      
           <div id="conceitoDica" style="display:none">
           
           <div class="<?php if ($totalRows_Conceitos==0) { echo "ls-alert-warning"; } else { echo "ls-alert-info"; }?> ls-dismissable">
              <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
              <strong>Atenção!</strong> Para cadastrar a forma de avaliação do tipo <strong>Conceito</strong>, é necessário ter cadastrado previamente o Grupo de Conceitos. <?php if ($totalRows_Conceitos==0) { echo "<a href=\"conceito_cad.php\">Clique aqui para cadastrar.</a>"; } ?>
            </div>
           
           </div>
      
      
      <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form">
         
          
          
          <label class="ls-label col-md-12 col-sm-12">
          <b class="ls-label-text">FORMA DE AVALIAÇÃO</b>
          <a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Forma de avaliação do aluno." data-title="AJUDA"></a>
          <div class="ls-custom-select">
            <select name="ca_forma_avaliacao" id="ca_forma_avaliacao" class="ls-select" required>
              <option value="">ESCOLHA...</option>
              <option value="C">CONCEITO</option>
              <option value="N">NOTA</option>
              <option value="R">RELATÓRIO AVALIATIVO</option>
              <option value="P">PARECER AVALIATIVO</option>
              <option value="Q">QUANTITATIVO E QUALITATIVO</option>
            </select>
          </div>
          </label>
          
          
          <label class="ls-label col-md-12 col-sm-12">
          <b class="ls-label-text">DESCRIÇÃO</b>
          <a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Descreva um nome que irá identificar o critério avaliativo. Ex.: CRITÉRIO AVALIATIVO ANUAL PADRÃO" data-title="AJUDA"></a>
          <input type="text" name="ca_descricao" value="" size="32" required>
          </label>
          
          <label class="ls-label col-md-12 col-sm-12">
          <b class="ls-label-text">PERÍODOS POR ANO</b>
          <a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Quantidade de períodos (Unidades, Bimestres, Trimestres, etc) por ano." data-title="AJUDA"></a>
          <div class="ls-custom-select">
            <select name="ca_qtd_periodos" class="ls-select" required>
            <option value="" selected>Escolha...</option>
            <option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>1 PERÍODO</option>
            <option value="2" <?php if (!(strcmp(2, ""))) {echo "SELECTED";} ?>>2 PERÍODOS</option>
            <option value="3" <?php if (!(strcmp(3, ""))) {echo "SELECTED";} ?>>3 PERÍODOS</option>
            <option value="4" <?php if (!(strcmp(4, ""))) {echo "SELECTED";} ?>>4 PERÍODOS</option>
            <option value="5" <?php if (!(strcmp(5, ""))) {echo "SELECTED";} ?>>5 PERÍODOS</option>
            <option value="6" <?php if (!(strcmp(6, ""))) {echo "SELECTED";} ?>>6 PERÍODOS</option>
            </select>
          </div>
          </label>
          
          
          <div id="nota" style="display:none">
          
          <label class="ls-label col-md-12 col-sm-12">
          <b class="ls-label-text">AVALIAÇÕES POR PERÍODO</b>
          <a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Informe a quantidade de avaliações por período." data-title="AJUDA"></a>
          <div class="ls-custom-select">
            <select name="ca_qtd_av_periodos" class="ls-select">
            <option value="" selected>Escolha...</option>
            <option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>1 AVALIAÇÃO</option>
            <option value="2" <?php if (!(strcmp(2, ""))) {echo "SELECTED";} ?>>2 AVALIAÇÕES</option>
            <option value="3" <?php if (!(strcmp(3, ""))) {echo "SELECTED";} ?>>3 AVALIAÇÕES</option>
            <option value="4" <?php if (!(strcmp(4, ""))) {echo "SELECTED";} ?>>4 AVALIAÇÕES</option>
            <option value="5" <?php if (!(strcmp(5, ""))) {echo "SELECTED";} ?>>5 AVALIAÇÕES</option>
            <option value="6" <?php if (!(strcmp(6, ""))) {echo "SELECTED";} ?>>6 AVALIAÇÕES</option>
            </select>
          </div>
          </label>
                    
          <label class="ls-label col-md-12 col-sm-12">
          <b class="ls-label-text">MÍNIMO DE AVALIAÇÃO P/ APROVAÇÃO</b>
          <a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Informe o valor mínimo de nota por avaliação necessária para que o aluno seja aprovado. (Ex.: 5,00)" data-title="AJUDA"></a>
          <input type="number" name="ca_nota_min_av" size="5" max="10" step="0.01" class="nota" value="">
          </label>
          
          <label class="ls-label col-md-12 col-sm-12">
          <b class="ls-label-text">NOTA MÁXIMA</b>
          <a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Informe o valor máximo de nota por avaliação. (Ex.: 10,00)" data-title="AJUDA"></a>
          <input type="number" name="ca_nota_max_av" size="5" max="10" step="0.01" class="nota" value="" size="32">
          </label>
          
          <label class="ls-label col-md-12 col-sm-12">
          <b class="ls-label-text">TIPO DE CÁLCULO</b>
          <a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Cálculo para a média do período." data-title="AJUDA"></a>
          <div class="ls-custom-select">
            <select name="ca_calculo_media_periodo" class="ls-select">
              <option value="" selected>Escolha...</option>
              <option value="MEDIA" <?php if (!(strcmp("MEDIA", ""))) {echo "SELECTED";} ?>>MEDIA</option>
              <option value="SOMA" <?php if (!(strcmp("SOMA", ""))) {echo "SELECTED";} ?>>SOMA</option>
            </select>
          </div>
          </label>
          
          <label class="ls-label col-md-12 col-sm-12">
          <b class="ls-label-text">RECUPERAÇÃO PARALELA POR UNIDADE</b>
          <a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Permite inserir nota de recuperação paralela por unidade." data-title="AJUDA"></a>
          <div class="ls-custom-select">
            <select name="ca_rec_paralela" class="ls-select">
              <option value="" selected>Escolha...</option>
              <option value="S" <?php if (!(strcmp("S", ""))) {echo "SELECTED";} ?>>SIM</option>
              <option value="N" <?php if (!(strcmp("N", ""))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
          </div>
          </label>
          
          
          
          <label class="ls-label col-md-12 col-sm-12">
          <b class="ls-label-text">MÉDIA MÍNIMA POR PERÍODO</b>
          <a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Valor mínimo para aprovação na média por período. (Ex.: 6,00)" data-title="AJUDA"></a>
          <input type="number" name="ca_media_min_periodo" size="5" max="100" step="0.01" class="nota" value="" size="32">
          </label>

          <label class="ls-label col-md-12 col-sm-12">
          <b class="ls-label-text">ARREDONDA MÉDIA?</b>
          <a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Se o campo Tipo de Cálculo for MÉDIA, o valor será arredondado." data-title="AJUDA"></a>
          <div class="ls-custom-select">
            <select name="ca_arredonda_media" class="ls-select">
              <option value="" selected>Escolha...</option>            
              <option value="S" <?php if (!(strcmp("S", ""))) {echo "SELECTED";} ?>>SIM</option>
              <option value="N" <?php if (!(strcmp("N", ""))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
          </div>
          </label>
          
          <label class="ls-label col-md-12 col-sm-12">
          <b class="ls-label-text">APROXIMA MÉDIA?</b>
          <a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Se o campo Tipo de Cálculo for MÉDIA, o valor será aproximado para cima." data-title="AJUDA"></a>
          <div class="ls-custom-select">
            <select name="ca_aproxima_media" class="ls-select">
              <option value="" selected>Escolha...</option>
              <option value="S" <?php if (!(strcmp("S", ""))) {echo "SELECTED";} ?>>SIM</option>
              <option value="N" <?php if (!(strcmp("N", ""))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
          </div>
          </label>
          
          <label class="ls-label col-md-12 col-sm-12">
          <b class="ls-label-text">CASAS DECIMAIS APÓS A VÍRGULA?</b>
          <a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Quantidade de casas decimais após a vírgula para exibição na média. Ex.: 7.6 ou 7.63" data-title="AJUDA"></a>
          <div class="ls-custom-select">
            <select name="ca_digitos" class="ls-select">
              <option value="1" selected <?php if (!(strcmp("1", ""))) {echo "SELECTED";} ?>>1</option>
              <option value="2" <?php if (!(strcmp("2", ""))) {echo "SELECTED";} ?>>2</option>
            </select>
          </div>
          </label>
          
          <label class="ls-label col-md-12 col-sm-12">
          <b class="ls-label-text">TOTAL DE PONTOS (ANUAL)</b>
          <a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Mínimo de pontos necessários para aprovação. (Ex.: 24,00)" data-title="AJUDA"></a>
          <input type="number" name="ca_min_pontos_aprovacao_final" size="5" step="0.01" class="nota" value="" size="32">
          </label>
          
          <label class="ls-label col-md-12 col-sm-12">
          <b class="ls-label-text">MÉDIA FINAL</b>
          <a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Média mínima para aprovação final. (Ex.: 6,00)" data-title="AJUDA"></a>
          <input type="number" name="ca_min_media_aprovacao_final" size="5" max="10" step="0.01" class="nota" value="" size="32">
          </label>
          
          <label class="ls-label col-md-12 col-sm-12">
          <b class="ls-label-text">NOTA DE AVALIAÇÃO FINAL</b>
          <a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Nota mínima para aprovação na avaliação final. (Ex.: 5,00)" data-title="AJUDA"></a>
          <input type="number" name="ca_nota_min_recuperacao_final" size="5" max="10" step="0.01" class="nota" value="" size="32">
          </label>
          
          </div>
          
           <div id="conceito" style="display:none">
           
          <label class="ls-label col-md-12 col-sm-12">
          <b class="ls-label-text">GRUPO ETÁRIO</b>
          <a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Informe a faixa-etária." data-title="AJUDA"></a>
          <div class="ls-custom-select">
            <select name="ca_grupo_etario" class="ls-select">
            <option value="" selected>ESCOLHA...</option>
            <option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>Bebês (zero a 1 ano e 6 meses)</option>
            <option value="2" <?php if (!(strcmp(2, ""))) {echo "SELECTED";} ?>>Crianças bem pequenas (1 ano e 7 meses a 3 anos e 11 meses)</option>
            <option value="3" <?php if (!(strcmp(3, ""))) {echo "SELECTED";} ?>>Crianças pequenas (4 anos a 5 anos e 11 meses)</option>
            <option value="4" <?php if (!(strcmp(4, ""))) {echo "SELECTED";} ?>>Fundamental Anos Iniciais</option>
            <option value="5" <?php if (!(strcmp(5, ""))) {echo "SELECTED";} ?>>Fundamental Anos Finais</option>
            <option value="6" <?php if (!(strcmp(6, ""))) {echo "SELECTED";} ?>>EPJAI</option>
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
            <option value="<?php echo $row_Conceitos['conceito_id']; ?>"><?php echo $row_Conceitos['conceito_descricao']; ?></option>
            <?php } while ($row_Conceitos = mysql_fetch_assoc($Conceitos)); ?>
            </select>
          </div>
          </label>
          
          <label class="ls-label col-md-12 col-sm-12">
          <b class="ls-label-text">CADASTRAR QUESTIONÁRIO DE CONCEITOS?</b>
          <a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Informe a etapa que se aplica ao questionário de conceitos." data-title="AJUDA"></a>
          <div class="ls-custom-select">
			<select name="ca_questionario_conceitos" class="ls-select">
              <option value="S" <?php if (!(strcmp("S", ""))) {echo "SELECTED";} ?>>SIM</option>
              <option value="N" <?php if (!(strcmp("N", ""))) {echo "SELECTED";} ?> selected>NÃO</option>
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
            <option value="<?php echo $row_Etapa['etapa_id']; ?>"><?php echo $row_Etapa['etapa_nome']; ?></option>
            <?php } while ($row_Etapa = mysql_fetch_assoc($Etapa)); ?>
            </select>
          </div>
          </label>
          

          
          
           
           </div>
          
          
          
          <label class="ls-label col-md-12"> <b class="ls-label-text">DETALHES DO CRITÉRIO DE AVALIAÇÃO</b>
          <a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Descreva os detalhes do critério avaliativo" data-title="AJUDA"></a>
            <textarea name="ca_detalhes" rows="5" class="ls-textarea-autoresize"></textarea>
          </label>
          
          <div class="ls-actions-btn">
            <input type="submit" value="CADASTRAR" class="ls-btn-primary">
            <a class="ls-btn-danger" href="criterios_avaliativos.php">CANCELAR</a>
          </div>
          <input type="hidden" name="ca_id_secretaria" value="<?php echo $row_UsuarioLogado['usu_sec']; ?>">
          <input type="hidden" name="MM_insert" value="form1">
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
<script src="../js/jquery.mask.js"></script> 


<script type="text/javascript">

    $(document).ready(function(){
        $('#ca_forma_avaliacao').change(function(){
			var nacionalidade = event.currentTarget.value;	
			
 switch (nacionalidade) { 

	case "N": 
		$("#conceito").css("display", "none");
		$("#nota").css("display", "block");
		break;

	case "C": 
		$("#conceito").css("display", "block");
		$("#conceitoDica").css("display", "block");
		$("#nota").css("display", "none");
		break;
		
	case "R": 
		$("#conceito").css("display", "none");
		$("#conceitoDica").css("display", "none");
		$("#nota").css("display", "none");
		break;
		
	case "P": 
		$("#conceito").css("display", "none");
		$("#conceitoDica").css("display", "none");
		$("#nota").css("display", "none");
		break;
		
		
	
}
			
			
			
			//$("#painel_sec").css("display", "none");
        });
    });
    </script>



<script type="text/javascript">

$(document).ready(function(){
	
	
  $('.nota').mask('00.00', {reverse: true});

  $('.money').mask('000.000.000.000.000,00', {reverse: true});

});

</script>
</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

mysql_free_result($Conceitos);

mysql_free_result($Etapa);
?>