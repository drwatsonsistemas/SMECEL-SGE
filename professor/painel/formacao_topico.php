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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO smc_curso_formacao_comentarios (curso_form_com_id_func, curso_form_com_id_topico, curso_form_com_texto) VALUES (%s, %s, %s)",
                       GetSQLValueString($_POST['curso_form_com_id_func'], "int"),
                       GetSQLValueString($_POST['curso_form_com_id_topico'], "int"),
                       GetSQLValueString($_POST['curso_form_com_texto'], "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

  $insertGoTo = "formacao_topico.php?comentou";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING']."#coment";
  }
  header(sprintf("Location: %s", $insertGoTo));
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
include "fnc/anoLetivo.php";

$colname_Topico = "-1";
if (isset($_GET['codigo'])) {
  $colname_Topico = $_GET['codigo'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Topico = sprintf("
SELECT curso_form_topic_id, curso_form_topic_id_form, curso_form_topic_titulo, curso_form_topic_texto, curso_form_topic_aberto, curso_form_data , curso_form_id, curso_form_id_sec, curso_form_nome, curso_form_hash
FROM smc_curso_formacao_topicos
INNER JOIN smc_curso_formacao
ON curso_form_id = curso_form_topic_id_form 
WHERE curso_form_id_sec = '$row_Vinculos[vinculo_id_sec]' AND curso_form_topic_id = %s", GetSQLValueString($colname_Topico, "int"));
$Topico = mysql_query($query_Topico, $SmecelNovo) or die(mysql_error());
$row_Topico = mysql_fetch_assoc($Topico);
$totalRows_Topico = mysql_num_rows($Topico);

if($totalRows_Topico=="") {
	header("Location:../index.php?loginErr");
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Anexos = "SELECT curso_form_anexo_id, curso_form_anexo_id_topico, curso_form_anexo_descricao, curso_form_anexo_arquivo FROM smc_curso_formacao_anexo WHERE curso_form_anexo_id_topico = '$row_Topico[curso_form_topic_id]'";
$Anexos = mysql_query($query_Anexos, $SmecelNovo) or die(mysql_error());
$row_Anexos = mysql_fetch_assoc($Anexos);
$totalRows_Anexos = mysql_num_rows($Anexos);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Comentarios = "

SELECT curso_form_com_id, curso_form_com_id_func, curso_form_com_id_topico, curso_form_com_texto, curso_form_com_data, func_id, func_nome, func_foto 
FROM smc_curso_formacao_comentarios
INNER JOIN smc_func  
ON func_id = curso_form_com_id_func
WHERE curso_form_com_id_topico = '$row_Topico[curso_form_topic_id]'
ORDER BY curso_form_com_id ASC";
$Comentarios = mysql_query($query_Comentarios, $SmecelNovo) or die(mysql_error());
$row_Comentarios = mysql_fetch_assoc($Comentarios);
$totalRows_Comentarios = mysql_num_rows($Comentarios);

if ((isset($_GET['deletar'])) && ($_GET['deletar'] != "")) {
	
	
  $deleteSQL = sprintf("DELETE FROM smc_curso_formacao_comentarios WHERE curso_form_com_id_func = '$row_ProfLogado[func_id]' AND curso_form_com_id=%s",
                       GetSQLValueString($_GET['deletar'], "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());

  $deleteGoTo = "formacao_topico.php?codigo=$row_Topico[curso_form_topic_id]&deletado";
//  if (isset($_SERVER['QUERY_STRING'])) {
  //  $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    //$deleteGoTo .= $_SERVER['QUERY_STRING'];
  //}
  header(sprintf("Location: %s", $deleteGoTo));
}

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

.prof {
	background-color: #ddd;
	border-radius: 100%;
	height: 80px;
	object-fit: cover;
	width: 80px;
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
      <div class="col s12 m10"> <a href="formacao_curso.php?codigo=<?php echo $row_Topico['curso_form_hash']; ?>" class="waves-effect waves-light btn-small btn-flat"><i class="material-icons left">arrow_back</i> Voltar</a>
        <hr>
        <h6><?php echo $row_Topico['curso_form_nome']; ?></h6>
        <h5><blockquote><?php echo $row_Topico['curso_form_topic_titulo']; ?></blockquote></h5>
        <hr>
        <?php echo $row_Topico['curso_form_topic_texto']; ?>
        <hr>
        <?php if ($totalRows_Anexos > 0) { // Show if recordset not empty ?>
          <?php do { ?>
            <a class="btn btn-small btn-flat blue-text" target="_blank" href="../../anexos/formacao/professores/<?php echo $row_Topico['curso_form_hash']; ?>/<?php echo $row_Anexos['curso_form_anexo_arquivo']; ?>"><i class="material-icons left">attach_file</i> <?php echo $row_Anexos['curso_form_anexo_descricao']; ?></a>
            <?php } while ($row_Anexos = mysql_fetch_assoc($Anexos)); ?>
          <hr>
          
          <?php } // Show if recordset not empty ?>
          
          <?php if ($totalRows_Comentarios > 0) { // Show if recordset not empty ?>
  
  <h5>DEBATE</h5>
  <table>
    <?php do { ?>
      
      <tr>
        <td width="80">
          <?php if ($row_Comentarios['func_foto']=="") { ?>
          <img src="<?php echo URL_BASE.'professor/fotos/' ?>semfoto.jpg" width="100%" class="hoverable prof">
          <?php } else { ?>
          <img src="<?php echo URL_BASE.'professor/fotos/' ?><?php echo $row_Comentarios['func_foto']; ?>" width="100%" class="hoverable prof">
          <?php } ?>
        </td>
        <td><strong><?php echo $row_Comentarios['func_nome']; ?></strong> <p><?php echo $row_Comentarios['curso_form_com_texto']; ?></p></td>
        <td>
        <small class="right"><?php echo date("d/m/Y - H:i", strtotime($row_Comentarios['curso_form_com_data'])); ?></small>
        </td>
        <td width="50">
        <?php if ($row_Comentarios['curso_form_com_id_func']==$row_ProfLogado["func_id"]) { ?>
        
        <a href="javascript:func()" class="text-red right" onclick="confirmaExclusao('codigo=<?php echo $colname_Topico; ?>&deletar=<?php echo $row_Comentarios['curso_form_com_id']; ?>')"><i class="material-icons right red-text">delete_forever</i></a>
        
         <?php } // Show if recordset not empty ?>
        </td>
      </tr>
      <?php } while ($row_Comentarios = mysql_fetch_assoc($Comentarios)); ?>
  </table>
  <?php } else { ?>
  
  <p>Seja o primeiro a comentar.</p>
  
  <?php } // Show if recordset not empty ?>
  
  
  
<form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="col s12" id="coment">
          <div class="row">
            <div class="input-field col s12">
              <textarea id="mycomentario" name="curso_form_com_texto" cols="50" rows="5"></textarea>
            </div>
          </div>
          <input type="submit" class="btn" value="COMENTAR">
          <input type="hidden" name="curso_form_com_id_func" value="<?php echo $row_ProfLogado['func_id']; ?>">
          <input type="hidden" name="curso_form_com_id_topico" value="<?php echo $row_Topico['curso_form_topic_id']; ?>">
          <input type="hidden" name="MM_insert" value="form1">
        </form>
        <p>&nbsp;</p>
      </div>
    </div>
  </div>
</div>
<?php include ("rodape.php"); ?>
<!--JavaScript at end of body for optimized loading--> 
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script> 
<script type="text/javascript" src="../js/materialize.min.js"></script> 
<script src="https://cdn.tiny.cloud/1/iq1gy8qt7s1b9bj92c2fc7whie95augu8r8kz97dgoufkli8/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script> 
<script src="langs/pt_BR.js"></script> 
<script>
	$('.tooltipped').tooltip();
	tinymce.init({
	  selector: '#mycomentario',
	  mobile: {
    menubar: false
  },
	  plugins: 'emoticons',
	  toolbar: 'emoticons',
	  height: 200,
	  toolbar: ['paste undo redo | bold italic | bullist numlist | emoticons | alignleft aligncenter alignright alignjustify | link blockquote'],
	  plugins : ['textcolor','advlist autolink link image imagetools lists charmap print preview paste emoticons',
		'advlist autolink lists link image imagetools charmap print preview anchor',
		'searchreplace visualblocks code fullscreen',
		'insertdatetime media table paste code help wordcount'],
	  //force_br_newlines : false,
	  //force_p_newlines : false,
	  //forced_root_block : '',	
	  statusbar: false,
	  language: 'pt_BR',
	  menubar: false,
	  paste_as_text: true,
	  content_css: '//www.tinymce.com/css/codepen.min.css'
	});
</script> 
<script type="text/javascript" src="../js/app.js"></script> 
<script type="text/javascript">
		$(document).ready(function(){
			$(".dropdown-trigger").dropdown();
			$('.sidenav').sidenav();
		});
	</script>

    <script language="Javascript">
	function confirmaExclusao(codigo) {
     var resposta = confirm("Deseja realmente excluir seu comentario?");
     	if (resposta == true) {
     	     window.location.href = "formacao_topico.php?"+codigo;
    	 }
	}
	</script>

</body>
</html>
<?php
mysql_free_result($ProfLogado);
mysql_free_result($Topico);
mysql_free_result($Anexos);
mysql_free_result($Comentarios);
?>