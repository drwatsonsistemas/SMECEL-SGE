<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "../../sistema/funcoes/anoLetivo.php"; ?>
<?php include('../../sistema/funcoes/url_base.php'); ?>
<?php include('../../sistema/funcoes/inverteData.php'); ?>
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
	
  $logoutGoTo = "../index.php?saiu";
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
$MM_authorizedUsers = "7";
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

$MM_restrictGoTo = "../index.php?err";
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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	
  $updateSQL = sprintf("UPDATE smc_plano_aula_anexo_atividade SET plano_aula_anexo_atividade_resposta_professor=%s, plano_aula_anexo_atividade_visualizada_professor=%s WHERE plano_aula_anexo_atividade_id_aluno=%s AND plano_aula_anexo_atividade_id_atividade=%s",
                       GetSQLValueString($_POST['plano_aula_anexo_atividade_resposta_professor'], "text"),
                       GetSQLValueString($_POST['plano_aula_anexo_atividade_visualizada_professor'], "text"),
                       GetSQLValueString($_POST['plano_aula_anexo_atividade_id_aluno'], "int"),
					   GetSQLValueString($_POST['plano_aula_anexo_atividade_id_atividade'], "int"));
					   
					   
					   
					   

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

  //$updateGoTo = "forum.php";
  $updateGoTo = "resposta_atividade.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_ProfLogado = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_ProfLogado = $_SESSION['MM_Username'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ProfLogado = sprintf("SELECT func_id, func_nome, func_email, func_foto FROM smc_func WHERE func_id = %s", GetSQLValueString($colname_ProfLogado, "text"));
$ProfLogado = mysql_query($query_ProfLogado, $SmecelNovo) or die(mysql_error());
$row_ProfLogado = mysql_fetch_assoc($ProfLogado);
$totalRows_ProfLogado = mysql_num_rows($ProfLogado);

if($totalRows_ProfLogado=="") {
	header("Location:../index.php?loginErr");
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Vinculos = "SELECT vinculo_id, vinculo_id_escola, vinculo_id_sec, vinculo_id_funcionario FROM smc_vinculo WHERE vinculo_id_funcionario = '$row_ProfLogado[func_id]'";
$Vinculos = mysql_query($query_Vinculos, $SmecelNovo) or die(mysql_error());
$row_Vinculos = mysql_fetch_assoc($Vinculos);
$totalRows_Vinculos = mysql_num_rows($Vinculos);

$colname_hash = "-1";
if (isset($_GET['hash'])) {
  $colname_hash = $_GET['hash'];
}
$colname_RespostaAtividade = "-1";
if (isset($_GET['atividade'])) {
  $colname_RespostaAtividade = $_GET['atividade'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_RespostaAtividade = sprintf("SELECT plano_aula_anexo_atividade_id, plano_aula_anexo_atividade_id_aluno, plano_aula_anexo_atividade_id_atividade, plano_aula_anexo_atividade_caminho, plano_aula_anexo_atividade_data_hora, plano_aula_anexo_atividade_resposta_professor, plano_aula_anexo_atividade_visualizada_professor, plano_aula_anexo_atividade_visualizada_aluno FROM smc_plano_aula_anexo_atividade WHERE plano_aula_anexo_atividade_id = %s", GetSQLValueString($colname_RespostaAtividade, "int"));
$RespostaAtividade = mysql_query($query_RespostaAtividade, $SmecelNovo) or die(mysql_error());
$row_RespostaAtividade = mysql_fetch_assoc($RespostaAtividade);
$totalRows_RespostaAtividade = mysql_num_rows($RespostaAtividade);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_RespostaAtividadeOutras = sprintf("SELECT plano_aula_anexo_atividade_id, plano_aula_anexo_atividade_id_aluno, plano_aula_anexo_atividade_id_atividade, plano_aula_anexo_atividade_caminho, plano_aula_anexo_atividade_data_hora, plano_aula_anexo_atividade_resposta_professor, plano_aula_anexo_atividade_visualizada_professor, plano_aula_anexo_atividade_visualizada_aluno FROM smc_plano_aula_anexo_atividade WHERE plano_aula_anexo_atividade_id_aluno = '$row_RespostaAtividade[plano_aula_anexo_atividade_id_aluno]' AND plano_aula_anexo_atividade_id_atividade = %s", GetSQLValueString($row_RespostaAtividade['plano_aula_anexo_atividade_id_atividade'], "int"));
$RespostaAtividadeOutras = mysql_query($query_RespostaAtividadeOutras, $SmecelNovo) or die(mysql_error());
$row_RespostaAtividadeOutras = mysql_fetch_assoc($RespostaAtividadeOutras);
$totalRows_RespostaAtividadeOutras = mysql_num_rows($RespostaAtividadeOutras);


include "fnc/anoLetivo.php";

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-117872281-1');
</script>
<title><?php echo $row_ProfLogado['func_nome']?>-</title>

<!--Import Google Icon Font-->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<!--Import materialize.css-->
<link type="text/css" rel="stylesheet" href="../css/materialize.min.css"  media="screen,projection"/>
<link type="text/css" rel="stylesheet" href="../css/app.css"  media="screen,projection"/>

<!--Let browser know website is optimized for mobile-->
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
<style>
table {
	width:100%;
	border-collapse: collapse;
	font-size:12px;
}
th, td {
	border:1px solid #ccc;
	padding:5px;
	height:15px;
	line-height:15px;
}
</style>
</head>

<body class="indigo lighten-5">
<?php include ("menu_top.php"); ?>
<div class="section no-pad-bot" id="index-banner">
  <div class="container">
    <div class="row white" style="margin: 10px 0;">
      <div class="col s12 m2 hide-on-small-only">
        <p>
          <?php if ($row_ProfLogado['func_foto']=="") { ?>
          <img src="<?php echo URL_BASE.'professor/fotos/' ?>semfoto.jpg" width="100%" class="hoverable">
          <?php } else { ?>
          <img src="<?php echo URL_BASE.'professor/fotos/' ?><?php echo $row_ProfLogado['func_foto']; ?>" width="100%" class="hoverable">
          <?php } ?>
          <br>
          <small><a href="foto.php"><i class="tiny material-icons">photo_camera</i></a></small> <small style="font-size:14px;"> <?php echo current( str_word_count($row_ProfLogado['func_nome'],2)); ?>
          <?php $word = explode(" ", trim($row_ProfLogado['func_nome'])); echo $word[count($word)-1]; ?>
          </small> </p>
        <?php include "menu_esq.php"; ?>
      </div>
      <div class="col s12 m10">
        <h5>ATIVIDADE <?php echo $row_RespostaAtividade['plano_aula_anexo_atividade_id']; ?></h5>
        <hr>
        <a href="forum.php?atividade=<?php echo $row_RespostaAtividade['plano_aula_anexo_atividade_id']; ?>&hash=<?php echo $colname_hash; ?>" class="waves-effect waves-light btn-small btn-flat"><i class="material-icons left">arrow_back</i> Voltar</a> 
		<hr>

		<p>PÁGINA: 
		<?php 
		$num = 1;
		do { ?>
			<a class="<?php if ($row_RespostaAtividadeOutras['plano_aula_anexo_atividade_id'] == $row_RespostaAtividade['plano_aula_anexo_atividade_id']) { ?> waves-effect waves-light btn-small disabled <?php } else { ?> waves-effect waves-light btn-small <?php } ?> <?php if ($row_RespostaAtividadeOutras['plano_aula_anexo_atividade_visualizada_professor']=='N') { ?>orange<?php } else { ?><?php } ?>" href="resposta_atividade.php?atividade=<?php echo $row_RespostaAtividadeOutras['plano_aula_anexo_atividade_id']; ?>&hash=<?php echo $colname_hash; ?>"><?php if ($row_RespostaAtividadeOutras['plano_aula_anexo_atividade_visualizada_professor']=="N") { ?><?php echo $num; ?><?php } else { ?><?php echo $num; ?><?php } ?></a>
			<?php $num++; ?>
		<?php } while ($row_RespostaAtividadeOutras = mysql_fetch_assoc($RespostaAtividadeOutras)); ?>
		</p>

        
		<?php $ext = ltrim( substr( $row_RespostaAtividade['plano_aula_anexo_atividade_caminho'], strrpos( $row_RespostaAtividade['plano_aula_anexo_atividade_caminho'], '.' ) ), '.' ); ?>
		
		<?php if ($ext == "jpg") { ?>
		<p><img src="<?php echo URL_BASE.'anexos_respostas/'.$row_RespostaAtividade['plano_aula_anexo_atividade_id_atividade'] ?>/<?php echo $row_RespostaAtividade['plano_aula_anexo_atividade_caminho']; ?>" width="100%"</p>
        <?php } ?>
		
		<?php if ($ext == "pdf") { ?>
		<p><iframe src="<?php echo URL_BASE.'anexos_respostas/'.$row_RespostaAtividade['plano_aula_anexo_atividade_id_atividade'] ?>/<?php echo $row_RespostaAtividade['plano_aula_anexo_atividade_caminho']; ?>" width="100%" height="1000" style="border: none;"></iframe></p>
        <?php } ?>
		
		<p><a href="<?php echo URL_BASE.'anexos_respostas/'.$row_RespostaAtividade['plano_aula_anexo_atividade_id_atividade'] ?>/<?php echo $row_RespostaAtividade['plano_aula_anexo_atividade_caminho']; ?>" target="_blank">Visualizar/Baixar</a></p>
		
		
		<p>Data e hora do envio: <?php echo date('H\hi - d/m/Y', strtotime($row_RespostaAtividade['plano_aula_anexo_atividade_data_hora'])); ?></p>
        
        <div class="row">
        <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="col s12">
          
		<div class="row">
            <div class="input-field col s12">
              <textarea id="textarea1" class="materialize-textarea" name="plano_aula_anexo_atividade_resposta_professor" cols="50" rows="5"><?php echo htmlentities($row_RespostaAtividade['plano_aula_anexo_atividade_resposta_professor'], ENT_COMPAT, ''); ?></textarea>
              <label for="textarea1">Resposta</label>
            </div>
      	</div>            
            
        <div class="row">
            <div class="input-field col s12">
            <input type="submit" value="RESPONDER" class="btn">
            <a href="forum.php?atividade=<?php echo $row_RespostaAtividade['plano_aula_anexo_atividade_id_atividade']; ?>&hash=<?php echo $colname_hash; ?>" class="waves-effect waves-light btn-small btn-flat">CANCELAR</a> 
            </div>
        </div>

          <input type="hidden" name="plano_aula_anexo_atividade_visualizada_professor" value="S">
          <input type="hidden" name="MM_update" value="form1">
          
          
          <input type="hidden" name="plano_aula_anexo_atividade_id_aluno" value="<?php echo $row_RespostaAtividade['plano_aula_anexo_atividade_id_aluno']; ?>">
          <input type="hidden" name="plano_aula_anexo_atividade_id_atividade" value="<?php echo $row_RespostaAtividade['plano_aula_anexo_atividade_id_atividade']; ?>">
          <input type="hidden" name="plano_aula_anexo_atividade_id" value="<?php echo $row_RespostaAtividade['plano_aula_anexo_atividade_id']; ?>">
        </form>
        </div>
     
      </div>
    </div>
  </div>
</div>

<!--JavaScript at end of body for optimized loading--> 
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script> 
<script type="text/javascript" src="../js/materialize.min.js"></script>
<?php include ("rodape.php"); ?>
<script type="text/javascript" src="../js/app.js"></script> 
<script type="text/javascript">
		$(document).ready(function(){
			$(".dropdown-trigger").dropdown();
			$('.sidenav').sidenav();
		});
	</script>
</body>
</html>
<?php
mysql_free_result($ProfLogado);

mysql_free_result($RespostaAtividade);
?>