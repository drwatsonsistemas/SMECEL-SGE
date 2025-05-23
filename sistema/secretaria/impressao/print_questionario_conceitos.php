<?php require_once('../../../Connections/SmecelNovo.php'); ?>
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



require_once('../funcoes/usuLogado.php');
require_once('../funcoes/anoLetivo.php');


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

$colname_Componente = "-1";
if (isset($_GET['comp'])) {
  $colname_Componente = $_GET['comp'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Componente = sprintf("SELECT disciplina_id, disciplina_area_conhecimento_id, disciplina_codigo_inep, disciplina_ordem, disciplina_nome, disciplina_nome_abrev, disciplina_cor_fundo, disciplina_bncc, disciplina_diversificada, disciplina_id_campos_exp, disciplina_ata FROM smc_disciplina WHERE disciplina_id = %s", GetSQLValueString($colname_Componente, "int"));
$Componente = mysql_query($query_Componente, $SmecelNovo) or die(mysql_error());
$row_Componente = mysql_fetch_assoc($Componente);
$totalRows_Componente = mysql_num_rows($Componente);

$colname_comp = "-1";
if (isset($_GET['comp'])) {
  $colname_comp = $_GET['comp'];
}


$colname_Matriz = "-1";
if (isset($_GET['matriz'])) {
  $colname_Matriz = $_GET['matriz'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matriz = sprintf("
SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash, matriz_dias_letivos, matriz_semanas_letivas, matriz_dias_semana, matriz_minutos_aula, 
matriz_aula_dia, matriz_criterio_avaliativo, matriz_ativa, ca_id, ca_descricao, ca_forma_avaliacao, ca_questionario_conceitos, ca_etapa_id, etapa_id, etapa_nome, etapa_nome_abrev 
FROM smc_matriz 
INNER JOIN smc_criterios_avaliativos ON ca_id = matriz_criterio_avaliativo 
INNER JOIN smc_etapa ON etapa_id = ca_etapa_id
WHERE matriz_hash = %s", GetSQLValueString($colname_Matriz, "text"));
$Matriz = mysql_query($query_Matriz, $SmecelNovo) or die(mysql_error());
$row_Matriz = mysql_fetch_assoc($Matriz);
$totalRows_Matriz = mysql_num_rows($Matriz);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_questoes = 
"SELECT quest_conc_id, quest_conc_id_matriz, quest_conc_id_etapa, quest_conc_id_comp, quest_conc_descricao, quest_conc_hash 
FROM smc_questionario_conceitos
WHERE quest_conc_id_matriz = '$row_Matriz[matriz_id]' AND quest_conc_id_comp = '$colname_comp'
";
$questoes = mysql_query($query_questoes, $SmecelNovo) or die(mysql_error());
$row_questoes = mysql_fetch_assoc($questoes);
$totalRows_questoes = mysql_num_rows($questoes);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
  $etapa = $row_Matriz['ca_etapa_id'];
  $hash = md5(date("YmdHis").$row_Matriz['ca_etapa_id']);
  		
  $insertSQL = sprintf("INSERT INTO smc_questionario_conceitos (quest_conc_id_matriz, quest_conc_id_etapa, quest_conc_id_comp, quest_conc_descricao, quest_conc_hash) VALUES (%s, '$etapa', '$colname_comp', %s, '$hash')",
                       GetSQLValueString($_POST['quest_conc_id_matriz'], "int"),
                       //GetSQLValueString($_POST['quest_conc_id_etapa'], "int"),
                       //GetSQLValueString($_POST['quest_conc_id_comp'], "int"),
                       GetSQLValueString($_POST['quest_conc_descricao'], "text"),
                       GetSQLValueString($_POST['quest_conc_hash'], "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

  $insertGoTo = "questionario_conceitos.php?cadastrado";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_GET['quest_hab'])) && ($_GET['quest_hab'] != "")) {
  $deleteSQL = sprintf("DELETE FROM smc_questionario_habilidades_ef WHERE quest_hab_hash=%s",
                       GetSQLValueString($_GET['quest_hab'], "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());

  $deleteGoTo = "questionario_conceitos.php?matriz=$colname_Matriz&comp=$colname_comp&deletado";
  if (isset($_SERVER['QUERY_STRING'])) {
    //$deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    //$deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

if ((isset($_GET['criterio'])) && ($_GET['criterio'] != "")) {
		
  $deleteSQL = sprintf("DELETE FROM smc_questionario_conceitos WHERE quest_conc_hash=%s",
                       GetSQLValueString($_GET['criterio'], "text"));
					   
  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());
					   

  $deleteGoTo = "questionario_conceitos.php?matriz=$colname_Matriz&comp=$colname_comp&delCriterio";
  if (isset($_SERVER['QUERY_STRING'])) {
    //$deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    //$deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
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

<title><?php echo $row_Matriz['etapa_nome_abrev']; ?> | <?php echo $row_Componente['disciplina_nome']; ?></title>
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
  <link rel="stylesheet" type="text/css" href="../css/impressao.css">

</head>
<body onload="self.print();">
<?php //include_once("menu_top.php"); ?>
<?php //include_once "menu.php"; ?>
<main class="ls-main1">
  <div class="container-fluid1">
    <!-- CONTEUDO -->

    <table class="bordasimples1" width="100%">
    	<tr>
        	<td class="ls-txt-center" width="60"></td>
        	<td class="ls-txt-center">		
				<?php if ($row_Secretaria['sec_logo'] <> "") { ?>
				  <img src="../../../img/logo/secretaria/<?php echo $row_Secretaria['sec_logo']; ?>" alt="Logo da <?php echo $row_Secretaria['sec_nome']; ?>" title="Logo da <?php echo $row_EscolaLogada['sec_nome']; ?>"  width="60" />
				<?php } else { ?>
				  <img src="../../../img/brasao_republica.png" width="60">
				<?php } ?>
              <h3><?php echo $row_Secretaria['sec_prefeitura']; ?></h3>
              <?php echo $row_Secretaria['sec_nome']; ?>
            </td>
        	<td class="ls-txt-center" width="60"></td>
        </tr>
    </table>
    <br>


    <div class="ls-box ls-box-gray">
    <p>MATRIZ: <strong><?php echo $row_Matriz['matriz_nome']; ?></strong></p>
    <p>CRITÉRIO AVALIATIVO: <strong><?php echo $row_Matriz['ca_descricao']; ?></strong></p>
    <p>COMPONENTE: <strong><?php echo $row_Componente['disciplina_nome']; ?> (<?php echo $row_Componente['disciplina_nome_abrev']; ?>)</strong></p>
    <p>ETAPA: <strong><?php echo $row_Matriz['etapa_nome_abrev']; ?> (<?php echo $row_Matriz['etapa_nome']; ?>)</strong></p>
    </div>
    




    <?php if ($totalRows_questoes > 0) { ?>
    <table class="bordasimples ls-sm-space" width="100%">
      <tr>
        <td width="50"></td>
        <td width="400">CRITÉRIOS AVALIATIVOS</td>
        <td>HABILIDADES</td>
      </tr>
      <?php $num = 1; do { ?>
        <tr>
          <td class="ls-txt-center"><?php echo $num; $num++; ?></td>
          <td>
		  <p><?php echo $row_questoes['quest_conc_descricao']; ?></p>
          </td>
          <td>
            <?php 
			mysql_select_db($database_SmecelNovo, $SmecelNovo);
			$query_Habilidades = "
			SELECT quest_hab_id, quest_hab_id_quest, quest_hab_id_hab, quest_hab_hash, bncc_ef_id, bncc_ef_habilidades 
			FROM smc_questionario_habilidades_ef
			INNER JOIN smc_bncc_ef ON bncc_ef_id = quest_hab_id_hab
			WHERE quest_hab_id_quest = '$row_questoes[quest_conc_id]'
			";
			$Habilidades = mysql_query($query_Habilidades, $SmecelNovo) or die(mysql_error());
			$row_Habilidades = mysql_fetch_assoc($Habilidades);
			$totalRows_Habilidades = mysql_num_rows($Habilidades);
		  ?>
          
            <?php 
			if ($totalRows_Habilidades > 0) { 
			do { ?>
              <p class=""><?php echo utf8_decode($row_Habilidades['bncc_ef_habilidades']); ?>  </p>
            <?php } while ($row_Habilidades = mysql_fetch_assoc($Habilidades)); 
			}
			?>
            
         
          </td>
        </tr>
        <?php } while ($row_questoes = mysql_fetch_assoc($questoes)); ?>
    </table>
    <?php } else { ?>
    <hr>
    <p>Nenhum Critério Avaliativo cadastrado.</p>
    
    <?php } ?>
<p>&nbsp;</p>
<p>&nbsp;</p>
    <!-- CONTEUDO -->    
  </div>
</main>
<?php include_once "notificacoes.php"; ?>

<div class="ls-modal" id="myAwesomeModal">
  <div class="ls-modal-box">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title">CADASTRAR CRITÉRIO AVALIATIVO</h4>
    </div>
    <div class="ls-modal-body" id="myModalBody">
      <p>
      
     <div class="ls-box ls-box-gray">
    <p>MATRIZ: <strong><?php echo $row_Matriz['matriz_nome']; ?></strong></p>
    <p>CRITÉRIO AVALIATIVO: <strong><?php echo $row_Matriz['ca_descricao']; ?></strong></p>
    <p>COMPONENTE: <strong><?php echo $row_Componente['disciplina_nome']; ?> (<?php echo $row_Componente['disciplina_nome_abrev']; ?>)</strong></p>
    <p>ETAPA: <strong><?php echo $row_Matriz['etapa_nome_abrev']; ?> (<?php echo $row_Matriz['etapa_nome']; ?>)</strong></p>
    </div>

      
      
      <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form">
    
      <label class="ls-label">
    	<b class="ls-label-text">Digite o texto do Critério Avaliativo</b>
    	<textarea name="quest_conc_descricao" class="ls-textarea-autoresize ls-textarea-resize-both" rows="3" required></textarea>
      </label>  
      
      
      <input type="hidden" name="quest_conc_id_matriz" value="<?php echo $row_Matriz['matriz_id']; ?>">
      <input type="hidden" name="quest_conc_id_etapa" value="<?php echo $row_Matriz['matriz_id_etapa']; ?>">
      <input type="hidden" name="quest_conc_id_comp" value="<?php echo $row_Matriz['matriz_id']; ?>">
      <input type="hidden" name="quest_conc_hash" value="<?php echo $row_Matriz['matriz_id']; ?>">
      <input type="hidden" name="MM_insert" value="form1">
    
      
      
      </p>
    </div>
    <div class="ls-modal-footer">
      <button class="ls-btn ls-float-right" data-dismiss="modal">FECHAR</button>
      <button type="submit" class="ls-btn-primary">CADASTRAR</button>
    </div>
    </form>
  </div>
</div><!-- /.modal -->

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="https://assets.locaweb.com.br/locastyle/3.8.4/javascripts/locastyle.js" type="text/javascript"></script>
</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

mysql_free_result($Componente);

mysql_free_result($Habilidades);

mysql_free_result($questoes);

mysql_free_result($Matriz);
?>