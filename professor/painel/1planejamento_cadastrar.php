<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "../../sistema/funcoes/anoLetivo.php"; ?>
<?php include('../../sistema/funcoes/url_base.php'); ?>
<?php include('../../sistema/funcoes/inverteData.php'); ?>
<?php include('../../sistema/funcoes/anti_injection.php'); ?>


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

$colname_Componente = "-1";
if (isset($_GET['componente'])) {
  $colname_Componente = anti_injection($_GET['componente']);
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Componente = sprintf("SELECT disciplina_id, disciplina_area_conhecimento_id, disciplina_codigo_inep, disciplina_ordem, disciplina_nome, disciplina_nome_abrev, disciplina_cor_fundo, disciplina_bncc FROM smc_disciplina WHERE disciplina_id = %s", GetSQLValueString($colname_Componente, "int"));
$Componente = mysql_query($query_Componente, $SmecelNovo) or die(mysql_error());
$row_Componente = mysql_fetch_assoc($Componente);
$totalRows_Componente = mysql_num_rows($Componente);

$colname_Etapa = "-1";
if (isset($_GET['etapa'])) {
  $colname_Etapa = anti_injection($_GET['etapa']);
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Etapa = sprintf("SELECT etapa_id, etapa_id_filtro, etapa_nome, etapa_idade, etapa_limite_turma, etapa_nome_abrev FROM smc_etapa WHERE etapa_id = %s", GetSQLValueString($colname_Etapa, "int"));
$Etapa = mysql_query($query_Etapa, $SmecelNovo) or die(mysql_error());
$row_Etapa = mysql_fetch_assoc($Etapa);
$totalRows_Etapa = mysql_num_rows($Etapa);






$escola = "-1";
if (isset($_GET['cod'])) {
  $escola = anti_injection($_GET['cod']);
}

$componente = "-1";
if (isset($_GET['componente'])) {
  $componente = anti_injection($_GET['componente']);
}

$etapa = "-1";
if (isset($_GET['etapa'])) {
  $etapa = anti_injection($_GET['etapa']);
}



$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO smc_ac (
  
  ac_id_professor, 
  ac_id_escola, 
  ac_id_componente, 
  ac_id_etapa, 
  ac_ano_letivo, 
  ac_data_inicial, 
  ac_data_final, 
  ac_conteudo, 
  ac_objetivo_especifico, 
  ac_objeto_conhecimento, 
  ac_metodologia,
  ac_recursos,  
  ac_avaliacao
	  ) VALUES (
  '$row_ProfLogado[func_id]', 
  '$escola', 
  '$componente', 
  '$etapa', 
  '$row_AnoLetivo[ano_letivo_ano]', 
  %s, 
  %s, 
  %s, 
  %s, 
  %s, 
  %s,  
  %s, 
  %s)",
                       
					   //GetSQLValueString($_POST['ac_id_professor'], "int"),
                       //GetSQLValueString($_POST['ac_id_escola'], "int"),
                       //GetSQLValueString($_POST['ac_ano_letivo'], "text"),
                       GetSQLValueString($_POST['ac_data_inicial'], "date"),
                       GetSQLValueString($_POST['ac_data_final'], "date"),
                       GetSQLValueString($_POST['ac_conteudo'], "text"),
                       GetSQLValueString($_POST['ac_objetivo_especifico'], "text"),
                       GetSQLValueString($_POST['ac_objeto_conhecimento'], "text"),
                       GetSQLValueString($_POST['ac_metodologia'], "text"),
                       GetSQLValueString($_POST['ac_recursos'], "text"),
                       GetSQLValueString($_POST['ac_avaliacao'], "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

  $insertGoTo = "ac.php?cadastrado";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
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

      <title><?php echo $row_ProfLogado['func_nome']?> - </title>
    
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
<small><a href="foto.php"><i class="tiny material-icons">photo_camera</i></a></small>		
<small style="font-size:14px;">
                  <?php echo current( str_word_count($row_ProfLogado['func_nome'],2)); ?>
                  <?php $word = explode(" ", trim($row_ProfLogado['func_nome'])); echo $word[count($word)-1]; ?>
        </small>
	 
	 </p>
	 
	 <?php include "menu_esq.php"; ?>
	 
	 
	 </div>
     
    <div class="col s12 m9">
	
	  <h5 class="amber" style="padding:5px;">REGISTRO DE PLANEJAMENTO DE AULAS</h5>
	  
	  <hr>
	  
	  
    

 <blockquote>
 
<h5 class="light-green" style="padding:5px;"><small>Componente curricular:<br></small><?php echo $row_Componente['disciplina_nome']; ?></h5>
<h5 class="lime" style="padding:5px;"><small>Etapa:</small><br><?php echo $row_Etapa['etapa_nome']; ?></h5>
 
 
 </blockquote>
 
<a href="index.php" class="waves-effect waves-light btn-small btn-flat"><i class="material-icons left">arrow_back</i> Voltar</a>    
    
<hr>
  
			<p>
            
            <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
            
            <div class="row">
              <div class="input-field col s6">
              <h4 for="textarea1">Data inicial</h4>
                <input type="date" name="ac_data_inicial" value="" size="32" required>
                <label for="first_name">Data inicial</label>
              </div>
              <div class="input-field col s6">
              <h4 for="textarea1">Data final</h4>
                <input type="date" name="ac_data_final" value="" size="32" required>
                <label for="last_name">Data final</label>
              </div>
            </div>
            
            <div class="row">
              <div class="input-field col s12">
			  <h4 for="textarea1">Direito de aprendizagem (EI) / Habilidades (EF)</h4>
                <textarea name="ac_objetivo_especifico" id="" class="materialize-textarea"></textarea>
              </div>
              <div class="input-field col s12">
                <h4 for="textarea1">Objetivo de aprendizagem (EI) / Objetos de Conhecimento (EF)</h4>
                <textarea name="ac_objeto_conhecimento" id="" class="materialize-textarea"></textarea>
              </div>
              <div class="input-field col s12">
                <h4 for="textarea1">Metodologia</h4>
                <textarea name="ac_metodologia" id="" class="materialize-textarea"></textarea>
              </div>
              <div class="input-field col s12">
                <h4 for="textarea1">Recursos</h4>
                <textarea name="ac_recursos" id="" class="materialize-textarea"></textarea>
              </div>
              <div class="input-field col s12">
                <h4 for="textarea1">Avaliação</h4>
                <textarea name="ac_avaliacao" id="" class="materialize-textarea"></textarea>
              </div>
              <div class="input-field col s12">
                <h4 for="textarea1">Observação</h4>
                <textarea name="ac_conteudo" id="" class="materialize-textarea"></textarea>
              </div>
              <div class="input-field col s12">
              <input type="submit" class="btn" value="REGISTRAR AC">
			  <input type="hidden" name="MM_insert" value="form1">
            </div>
            </div>
            
          
            </div>
            
          </form> 
   
	  		</p>
	  
	  
	  
		 
	</div>

	  </div>
    </div>
  </div>
  
      <!--JavaScript at end of body for optimized loading-->
   	  <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
      <script type="text/javascript" src="../js/materialize.min.js"></script>
<?php include ("rodape.php"); ?>      <script type="text/javascript" src="../js/app.js"></script>
      	<script type="text/javascript">
		$(document).ready(function(){
			$(".dropdown-trigger").dropdown();
			$('.sidenav').sidenav();
		});
	</script>
    
    <script src="https://cdn.tiny.cloud/1/iq1gy8qt7s1b9bj92c2fc7whie95augu8r8kz97dgoufkli8/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="langs/pt_BR.js"></script>

	<script>
	

	tinymce.init({
	  selector: 'textarea',
	  
	  mobile: {
      menubar: false
  },
	  
	  images_upload_url: 'postAcceptor.php',
	  automatic_uploads: true,
	  imagetools_proxy: 'proxy.php',
	  
	  //plugins: 'emoticons',
	  //toolbar: 'emoticons',
	  
	  //imagetools_toolbar: 'rotateleft rotateright | flipv fliph | editimage imageoptions',
	  	  
	  height: 200,
	  toolbar: ['paste undo redo | formatselect | forecolor | bold italic backcolor | bullist numlist | image | emoticons'],
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
    
    </body>
  </html>
  <?php
mysql_free_result($ProfLogado);

mysql_free_result($Componente);

mysql_free_result($Etapa);
?>