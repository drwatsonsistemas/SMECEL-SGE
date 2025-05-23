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

$colname_Matriz = "-1";
if (isset($_GET['matriz'])) {
  $colname_Matriz = $_GET['matriz'];
}


$colname_Componente = "-1";
if (isset($_GET['comp'])) {
  $colname_Componente = $_GET['comp'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Componente = sprintf("SELECT disciplina_id, disciplina_area_conhecimento_id, disciplina_codigo_inep, disciplina_ordem, disciplina_nome, disciplina_nome_abrev, disciplina_cor_fundo, disciplina_bncc, disciplina_diversificada, disciplina_id_campos_exp, disciplina_ata FROM smc_disciplina WHERE disciplina_id = %s", GetSQLValueString($colname_Componente, "int"));
$Componente = mysql_query($query_Componente, $SmecelNovo) or die(mysql_error());
$row_Componente = mysql_fetch_assoc($Componente);
$totalRows_Componente = mysql_num_rows($Componente);


$colname_Habilidade = "-1";
if (isset($_GET['habilidade'])) {
  $colname_Habilidade = $_GET['habilidade'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Habilidade = sprintf("
SELECT quest_conc_id, quest_conc_id_matriz, quest_conc_id_etapa, quest_conc_id_comp, quest_conc_descricao, quest_conc_hash, etapa_id, etapa_nome, etapa_ano_ef 
FROM smc_questionario_conceitos
INNER JOIN smc_etapa ON etapa_id = quest_conc_id_etapa
WHERE quest_conc_hash = %s", GetSQLValueString($colname_Habilidade, "text"));
$Habilidade = mysql_query($query_Habilidade, $SmecelNovo) or die(mysql_error());
// Após a consulta $Habilidade
$row_Habilidade = mysql_fetch_assoc($Habilidade);
$totalRows_Habilidade = mysql_num_rows($Habilidade);

// Validar o valor de etapa_ano_ef
$etapa_ano_ef = isset($row_Habilidade['etapa_ano_ef']) ? intval($row_Habilidade['etapa_ano_ef']) : 1; // Default para 1 se inválido
if ($etapa_ano_ef < 1 || $etapa_ano_ef > 9) {
    $etapa_ano_ef = 1; // Define um valor padrão (1º ano) se fora do intervalo
}

$ano = "bncc_ef_ano_" . $etapa_ano_ef;
$comp = $row_Habilidade['quest_conc_id_comp'];

// Construir a query com o ano validado
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_habilidades = "
SELECT bncc_ef_id, bncc_ef_area_conhec_id, bncc_ef_comp_id, bncc_ef_componente, bncc_ef_ano, bncc_ef_campos_atuacao, bncc_ef_eixo, bncc_ef_un_tematicas, bncc_ef_prat_ling, 
bncc_ef_obj_conhec, bncc_ef_habilidades, bncc_ef_comentarios, bncc_ef_poss_curr 
FROM smc_bncc_ef
WHERE $ano = 'S' AND bncc_ef_comp_id = $comp
";
$habilidades = mysql_query($query_habilidades, $SmecelNovo) or die(mysql_error());
$row_habilidades = mysql_fetch_assoc($habilidades);
$totalRows_habilidades = mysql_num_rows($habilidades);
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
	$habilidade_cod = $row_Habilidade['quest_conc_id'];
	$hash = md5(date("YmdHis").$row_Habilidade['quest_conc_id']);
	
  $insertSQL = sprintf("INSERT INTO smc_questionario_habilidades_ef (quest_hab_id_quest, quest_hab_id_hab, quest_hab_hash) VALUES ('$habilidade_cod', %s, '$hash')",
                       //GetSQLValueString($_POST['quest_hab_id_quest'], "int"),
                       GetSQLValueString($_POST['quest_hab_id_hab'], "int")
                      //GetSQLValueString($_POST['quest_hab_hash'], "text")
					   );

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

  $insertGoTo = "questionario_conceitos.php";
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
    <h1 class="ls-title-intro ls-ico-home">CADASTRAR HABILIDADE</h1>
    <!-- CONTEUDO -->
    
    
    <div class="ls-box">
	<h3><?php echo $row_Habilidade['quest_conc_descricao']; ?></h3>
	<h4>Etapa: <?php echo $row_Habilidade['etapa_nome']; ?></h4>
	<h4>Componente: <?php echo $row_Componente['disciplina_nome']; ?></h4>
    </div>
    
  
    <hr>
    
    <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form">
    
    
    
    <fieldset>
    <!-- Exemplo com Radio button -->
    <div class="ls-label col-md-12">
      <p>Escolha uma das plataformas:</p>
      
      
	  <?php 
	  if ($totalRows_habilidades>0) {
	  do {  ?>
      <label class="ls-label ls-box1">
        <input type="radio" name="quest_hab_id_hab" value="<?php echo $row_habilidades['bncc_ef_id']?>" required>
        <?php echo utf8_decode($row_habilidades['bncc_ef_habilidades']); ?>
      </label>
     <?php } while ($row_habilidades = mysql_fetch_assoc($habilidades)); 
	  }
	 ?>
            
      
      
      
      
    </div>
  </fieldset>
    

          
        
        
        

  <div class="ls-actions-btn">
        <?php if ($totalRows_habilidades>0) { ?> <input type="submit" value="CADASTRAR" class="ls-btn-primary"><?php } ?>
    <a href="questionario_conceitos.php?matriz=<?php echo $colname_Matriz; ?>&comp=<?php echo $colname_Componente; ?>" class="ls-btn-danger">VOLTAR</a>
  </div>
        
        
      <input type="hidden" name="quest_hab_id_quest" value="">
      <input type="hidden" name="quest_hab_hash" value="">
      <input type="hidden" name="MM_insert" value="form1">
    </form>
    
    <p>&nbsp;</p>
<p>&nbsp;</p>
    <!-- CONTEUDO -->    
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

mysql_free_result($habilidades);

mysql_free_result($Habilidade);
?>