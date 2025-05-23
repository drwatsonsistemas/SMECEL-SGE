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
	
	$hash = md5(date('YmdHis'));
	
  $insertSQL = sprintf("INSERT INTO smc_acomp_proc_aprend (acomp_id_matriz, acomp_id_crit, acomp_id_obj_aprend, acomp_descricao, acomp_hash) VALUES (%s, %s, %s, %s, '$hash')",
                       GetSQLValueString($_POST['acomp_id_matriz'], "int"),
                       GetSQLValueString($_POST['acomp_id_crit'], "int"),
                       GetSQLValueString($_POST['acomp_id_obj_aprend'], "int"),
                       GetSQLValueString($_POST['acomp_descricao'], "text")
                       );

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

$colname_Obetivo = "-1";
if (isset($_GET['campo'])) {
  $colname_Obetivo = $_GET['campo'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Obetivo = sprintf("SELECT campos_exp_obj_id, campos_exp_obj_id_campos_exp, campos_exp_obj_nome, campos_exp_obj_faixa_et_cod, campos_exp_obj_faixa_et_nome, campos_exp_obj_campos_exp, campos_exp_obj_abordagem, campos_exp_obj_sugestoes FROM smc_campos_exp_objetivos WHERE campos_exp_obj_id = %s", GetSQLValueString($colname_Obetivo, "int"));
$Obetivo = mysql_query($query_Obetivo, $SmecelNovo) or die(mysql_error());
$row_Obetivo = mysql_fetch_assoc($Obetivo);
$totalRows_Obetivo = mysql_num_rows($Obetivo);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ObjetivosEspecificos = "
SELECT acomp_id, acomp_id_matriz, acomp_id_crit, acomp_id_obj_aprend, acomp_descricao, acomp_hash 
FROM smc_acomp_proc_aprend
WHERE acomp_id_matriz = '$row_Matriz[matriz_id]'
AND acomp_id_crit = '$row_Criterios[ca_id]'
AND acomp_id_obj_aprend = '$row_Obetivo[campos_exp_obj_id]'
";
$ObjetivosEspecificos = mysql_query($query_ObjetivosEspecificos, $SmecelNovo) or die(mysql_error());
$row_ObjetivosEspecificos = mysql_fetch_assoc($ObjetivosEspecificos);
$totalRows_ObjetivosEspecificos = mysql_num_rows($ObjetivosEspecificos);

if ((isset($_GET['deletar'])) && ($_GET['deletar'] != "")) {
  $deleteSQL = sprintf("DELETE FROM smc_acomp_proc_aprend WHERE acomp_hash=%s",
                       GetSQLValueString($_GET['deletar'], "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());

  $deleteGoTo = "bncc_acomp_proc_aprendizagem.php?hash=$colname_Matriz&campo=$colname_Obetivo&deletado";
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
    <h1 class="ls-title-intro ls-ico-home">CADASTRAR</h1>
    <!-- CONTEUDO -->
    
    <p><a href="bncc_camp_exp_cad.php?hash=<?php echo $colname_Matriz; ?>" class="ls-btn-primary">VOLTAR</a></p>    

    
        
    <table class="ls-table">
    <thead>
    	<tr>
        	<th>CAMPO DE EXPERIÊNCIAS</th>
        	<th>FAIXA ETÁRIA</th>
        	<th>OBJETIVOS DE APRENDIZAGEM</th>
        	<th width="50"></th>
        	<th width="50"></th>
        </tr>
    </thead>
    <tbody>
    	<tr>
        	<td><?php echo utf8_encode($row_Obetivo['campos_exp_obj_nome']); ?></td>
        	<td><?php echo utf8_encode($row_Obetivo['campos_exp_obj_faixa_et_nome']); ?></td>
        	<td><?php echo utf8_encode($row_Obetivo['campos_exp_obj_campos_exp']); ?></td>
        	<td><button data-ls-module="modal" data-action="" data-content="<?php echo utf8_encode($row_Obetivo['campos_exp_obj_abordagem']); ?>" data-title="ABORDAGEM" data-class="ls-btn-danger" data-save="" data-close="Fechar" class="ls-btn"> A </button></td>
        	<td><button data-ls-module="modal" data-action="" data-content="<?php echo utf8_encode($row_Obetivo['campos_exp_obj_sugestoes']); ?>" data-title="SUGESTÕES" data-class="ls-btn-danger" data-save="" data-close="Fechar" class="ls-btn"> S </button></td>
        </tr>
    
    </tbody>
    
    </table>    
    

<div class="ls-modal" id="myAwesomeModal">
  <div class="ls-modal-box">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title">CADASTRAR</h4>
    </div>
    <div class="ls-modal-body" id="myModalBody">
      <p>

    
    <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form row"> 
  
  <fieldset>
  
      <label class="ls-label col-md-12">
    	<b class="ls-label-text">Descreva o acompanhamento do processo de aprendizagem:</b>
      <textarea name="acomp_descricao" cols="50" rows="5"></textarea>
      </label>
      

      
  </fieldset>    
      
      <input type="hidden" name="acomp_id_matriz" value="<?php echo $row_Matriz['matriz_id']; ?>">
      <input type="hidden" name="acomp_id_crit" value="<?php echo $row_Criterios['ca_id']; ?>">
      <input type="hidden" name="acomp_id_obj_aprend" value="<?php echo $row_Obetivo['campos_exp_obj_id']; ?>">
      <input type="hidden" name="acomp_hash" value="">
      <input type="hidden" name="MM_insert" value="form1">
    
    


      
      </p>
    </div>
    <div class="ls-modal-footer">
      <div class="ls-btn ls-float-right" data-dismiss="modal">SAIR</div>
      <input type="submit" value="SALVAR" class="ls-btn">
    </div>
    </form>
  </div>
</div><!-- /.modal -->


    <?php if ($totalRows_ObjetivosEspecificos > 0) { ?>
    <table class="ls-table">
    <thead>
      <tr>
        <th>Objetivos de aprendizagem e desenvolvimento específicos cadastrados</th>
        <th width="50"></th>
      </tr>
      </thead>
      <tbody>
      <?php 
	  $num = 1; 
	  do { ?>
        <tr>
          <td><span class="ls-tag-info"><?php echo $num; $num++;?></span> <?php echo $row_ObjetivosEspecificos['acomp_descricao']; ?></td>
          <td>
          <a href="javascript:func()" onclick="confirmaExclusao('<?php echo $colname_Matriz; ?>','<?php echo $colname_Obetivo; ?>','<?php echo $row_ObjetivosEspecificos['acomp_hash']; ?>')" class="ls-btn-primary-danger ls-btn-xs ls-ico-remove"></a>
          </td>
        </tr>
        <?php } while ($row_ObjetivosEspecificos = mysql_fetch_assoc($ObjetivosEspecificos)); ?>
    	</tbody>
    </table>
    
    <?php } ?>
    
    <button data-ls-module="modal" data-target="#myAwesomeModal" class="ls-btn-primary">INSERIR NOVO OBJTIVO ESPECÍFICO</button>

    
<p>&nbsp;</p>
    <!-- CONTEUDO -->    
  </div>
</main>
<?php include_once "notificacoes.php"; ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>

	<script language="Javascript">
	function confirmaExclusao(hash,campo,deletar) {
     var resposta = confirm("Deseja realmente remover esse ítem?");
     	if (resposta == true) {
     	     window.location.href = "bncc_acomp_proc_aprendizagem.php?hash="+hash+"&campo="+campo+"&deletar="+deletar;
    	 }
	}
	</script>

</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

mysql_free_result($Matriz);

mysql_free_result($Criterios);

mysql_free_result($Obetivo);

mysql_free_result($ObjetivosEspecificos);
?>