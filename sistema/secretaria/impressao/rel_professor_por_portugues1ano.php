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
	
  $logoutGoTo = "../../../index.php?exit";
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

$MM_restrictGoTo = "../../../index.php?acessorestrito";
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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Etapas = "SELECT etapa_id, etapa_id_filtro, etapa_nome, etapa_idade, etapa_limite_turma, etapa_nome_abrev, etapa_ano_ef FROM smc_etapa WHERE etapa_id_filtro IN (1,3,7)";
$Etapas = mysql_query($query_Etapas, $SmecelNovo) or die(mysql_error());
$row_Etapas = mysql_fetch_assoc($Etapas);
$totalRows_Etapas = mysql_num_rows($Etapas);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Componente = "SELECT disciplina_id, disciplina_area_conhecimento_id, disciplina_codigo_inep, disciplina_ordem, disciplina_nome, disciplina_nome_abrev, disciplina_cor_fundo, disciplina_bncc, disciplina_diversificada, disciplina_id_campos_exp FROM smc_disciplina WHERE disciplina_id = 15";
$Componente = mysql_query($query_Componente, $SmecelNovo) or die(mysql_error());
$row_Componente = mysql_fetch_assoc($Componente);
$totalRows_Componente = mysql_num_rows($Componente);


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
<link rel="stylesheet" type="text/css" href="//assets.locaweb.com.br/locastyle/edge/stylesheets/locastyle.css">
<link rel="stylesheet" type="text/css" href="../css/impressao.css">

<script src="//assets.locaweb.com.br/locastyle/edge/javascripts/locastyle.js"></script>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<body onload="self.print();alert('Configure a impressora para o formato PAISAGEM')">

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
    
    <h2 class="ls-txt-center">RELATÓRIO GERAL</h2>
    <br>
    <h3 class="ls-txt-center">Professores - 1º ano</h3>
    <br>  

	<?php do { ?>
    
    <table class="ls-table1 ls-sm-space bordasimples" width="100%">
	  <thead>
       <tr>
        <th>
		  <h2><?php echo $row_Componente['disciplina_nome']; ?></h2>
        </th>
       </tr>
      </thead>
      
	  <?php 
		mysql_select_db($database_SmecelNovo, $SmecelNovo);
		$query_Vinculo = "
		SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola,
		turma_id, turma_nome, turma_etapa, turma_id_sec, turma_ano_letivo, func_id, func_nome 
		FROM smc_ch_lotacao_professor
		INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
		INNER JOIN smc_func ON func_id = ch_lotacao_professor_id
		WHERE ch_lotacao_disciplina_id = '$row_Componente[disciplina_id]' AND turma_id_sec = '$row_UsuarioLogado[usu_sec]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND turma_etapa = 14
		GROUP BY func_id
		ORDER BY func_nome ASC
		";
		$Vinculo = mysql_query($query_Vinculo, $SmecelNovo) or die(mysql_error());
		$row_Vinculo = mysql_fetch_assoc($Vinculo);
		$totalRows_Vinculo = mysql_num_rows($Vinculo);
	  ?>
      
      <tbody>
      
      <?php if ($totalRows_Vinculo>0) { ?>
      <?php do { ?>
      
      <tr>
	  	<td>- <?php echo $row_Vinculo['func_nome']; ?></td>
      </tr>
      
	  <?php } while ($row_Vinculo = mysql_fetch_assoc($Vinculo)); ?>
      
      <tr>
	  	<td><i class="ls-float-right">Total de profissionais vinculados neste componente: <strong><?php echo $totalRows_Vinculo; ?></strong></i></td>
      </tr>


      <?php } else { ?>
      <tr>
	  	<td>- Nenhum professor vinculado nesta etapa</td>
      </tr>
      <?php } ?>
      </tbody>
      
      
      
    </table>  
    
    <hr>
    
	<?php } while ($row_Componente = mysql_fetch_assoc($Componente)); ?>
      
      

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="https://assets.locaweb.com.br/locastyle/3.8.4/javascripts/locastyle.js" type="text/javascript"></script>
</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

mysql_free_result($Etapas);

mysql_free_result($Vinculo);
?>