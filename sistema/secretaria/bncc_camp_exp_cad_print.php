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
if (isset($_GET['hash'])) {
  $colname_Matriz = $_GET['hash'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matriz = sprintf("SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash, matriz_dias_letivos, matriz_semanas_letivas, matriz_dias_semana, matriz_minutos_aula, matriz_aula_dia, matriz_criterio_avaliativo FROM smc_matriz WHERE matriz_hash = %s", GetSQLValueString($colname_Matriz, "text"));
$Matriz = mysql_query($query_Matriz, $SmecelNovo) or die(mysql_error());
$row_Matriz = mysql_fetch_assoc($Matriz);
$totalRows_Matriz = mysql_num_rows($Matriz);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Criterios = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes, ca_forma_avaliacao, ca_grupo_etario FROM smc_criterios_avaliativos WHERE ca_id = '$row_Matriz[matriz_criterio_avaliativo]'";
$Criterios = mysql_query($query_Criterios, $SmecelNovo) or die(mysql_error());
$row_Criterios = mysql_fetch_assoc($Criterios);
$totalRows_Criterios = mysql_num_rows($Criterios);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Campos = "SELECT campos_exp_id, campos_exp_nome, campos_exp_mais, campos_exp_orientacoes, campos_exp_direitos FROM smc_campos_exp ORDER BY campos_exp_id ASC";
$Campos = mysql_query($query_Campos, $SmecelNovo) or die(mysql_error());
$row_Campos = mysql_fetch_assoc($Campos);
$totalRows_Campos = mysql_num_rows($Campos);
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
<title><?php echo $row_Matriz['matriz_nome']; ?> | <?php echo $row_Secretaria['sec_prefeitura']; ?> | SMECEL - Sistema de Gestão Escolar</title>
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
  <link rel="stylesheet" type="text/css" href="css/impressao.css">

</head>
<body onload="self.print();">

<main class="ls-main1">
  <div class="container-fluid1">
    
    <!-- CONTEUDO --> 

    <table class="bordasimples1" width="100%">
    	<tr>
        	<td class="ls-txt-center" width="60"></td>
        	<td class="ls-txt-center">		
				<?php if ($row_Secretaria['sec_logo'] <> "") { ?>
				  <img src="../../img/logo/secretaria/<?php echo $row_Secretaria['sec_logo']; ?>" alt="Logo da <?php echo $row_Secretaria['sec_nome']; ?>" title="Logo da <?php echo $row_EscolaLogada['sec_nome']; ?>"  width="60" />
				<?php } else { ?>
				  <img src="../../img/brasao_republica.png" width="60">
				<?php } ?>
              <h3><?php echo $row_Secretaria['sec_prefeitura']; ?></h3>
              <?php echo $row_Secretaria['sec_nome']; ?>
            </td>
        	<td class="ls-txt-center" width="60"></td>
        </tr>
    </table>
    
    <br>      
    
    <h3 class="ls-txt-center">MATRIZ: <?php echo $row_Matriz['matriz_nome']; ?></h3>

          <br><br>

    <?php do { ?>
      <div class="ls-box1 ls-board-box1">
        <header class="ls-info-header">
        <br><br>
          <h2 class="ls-title-3 ls-color-danger"><?php echo utf8_encode($row_Campos['campos_exp_nome']); ?></h2>
        </header>
        <?php 
		mysql_select_db($database_SmecelNovo, $SmecelNovo);
		$query_Objetivos = "SELECT campos_exp_obj_id, campos_exp_obj_id_campos_exp, campos_exp_obj_nome, campos_exp_obj_faixa_et_cod, campos_exp_obj_faixa_et_nome, campos_exp_obj_campos_exp, campos_exp_obj_abordagem, campos_exp_obj_sugestoes FROM smc_campos_exp_objetivos WHERE campos_exp_obj_id_campos_exp = '$row_Campos[campos_exp_id]' AND campos_exp_obj_faixa_et_cod = '$row_Criterios[ca_grupo_etario]' ORDER BY campos_exp_obj_campos_exp ASC";
		$Objetivos = mysql_query($query_Objetivos, $SmecelNovo) or die(mysql_error());
		$row_Objetivos = mysql_fetch_assoc($Objetivos);
		$totalRows_Objetivos = mysql_num_rows($Objetivos);
	 ?>
        <table class="ls-table ls-lg-space1 bordasimples1">
          <thead>
            <tr>
              <th width="30%">Objetivos de aprendizagem e desenvolvimento</th>
              <th>Acompanhamento do processo de aprendizagem</th>
            </tr>
          </thead>
          <?php do { ?>
            <?php 
	   
		mysql_select_db($database_SmecelNovo, $SmecelNovo);
		$query_ObjetivosEspecificos = "
		SELECT acomp_id, acomp_id_matriz, acomp_id_crit, acomp_id_obj_aprend, acomp_descricao, acomp_hash 
		FROM smc_acomp_proc_aprend
		WHERE acomp_id_matriz = '$row_Matriz[matriz_id]'
		AND acomp_id_crit = '$row_Criterios[ca_id]'
		AND acomp_id_obj_aprend = '$row_Objetivos[campos_exp_obj_id]'
		";
		$ObjetivosEspecificos = mysql_query($query_ObjetivosEspecificos, $SmecelNovo) or die(mysql_error());
		$row_ObjetivosEspecificos = mysql_fetch_assoc($ObjetivosEspecificos);
		$totalRows_ObjetivosEspecificos = mysql_num_rows($ObjetivosEspecificos);
	   
	   ?>
       
       

       
            <tr>
              <td><?php echo utf8_encode($row_Objetivos['campos_exp_obj_campos_exp']); ?></td>
              <td><?php if ($totalRows_ObjetivosEspecificos > 0) { ?>
                <?php 
	  $num = 1; 
	  do { ?>
                  <p><span class="ls-tag-info"><?php echo $num; $num++; ?></span> <?php echo $row_ObjetivosEspecificos['acomp_descricao']; ?></p>
                  <?php } while ($row_ObjetivosEspecificos = mysql_fetch_assoc($ObjetivosEspecificos)); ?>
                <?php } ?></td>
            </tr>
            
 
            
            <?php } while ($row_Objetivos = mysql_fetch_assoc($Objetivos)); ?>
        </table>
      </div>
      <?php } while ($row_Campos = mysql_fetch_assoc($Campos)); ?>

      <p class="ls-txt-right">SMECEL | Sistema de Gestão Escolar - www.smecel.com.br <br>Impresso em <?php echo date("d/m/Y à\s H\hi"); ?></p>  


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

mysql_free_result($Matriz);

mysql_free_result($Criterios);

mysql_free_result($Campos);

mysql_free_result($Objetivos);

mysql_free_result($ObjetivosEspecificos);
?>