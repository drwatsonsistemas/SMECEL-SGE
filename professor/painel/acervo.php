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

$currentPage = $_SERVER["PHP_SELF"];

$colname_ProfLogado = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_ProfLogado = $_SESSION['MM_Username'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ProfLogado = sprintf("SELECT func_id, func_nome, func_email, func_foto FROM smc_func WHERE func_id = %s", GetSQLValueString($colname_ProfLogado, "text"));
$ProfLogado = mysql_query($query_ProfLogado, $SmecelNovo) or die(mysql_error());
$row_ProfLogado = mysql_fetch_assoc($ProfLogado);
$totalRows_ProfLogado = mysql_num_rows($ProfLogado);

$colname_turma = "-1";
if (isset($_GET['turma'])) {
  $colname_turma = $_GET['turma'];
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Vinculos = "SELECT vinculo_id, vinculo_id_escola, vinculo_id_sec, vinculo_id_funcionario FROM smc_vinculo WHERE vinculo_id_funcionario = '$row_ProfLogado[func_id]'";
$Vinculos = mysql_query($query_Vinculos, $SmecelNovo) or die(mysql_error());
$row_Vinculos = mysql_fetch_assoc($Vinculos);
$totalRows_Vinculos = mysql_num_rows($Vinculos);
include "fnc/anoLetivo.php";

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_turma = sprintf("SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo FROM smc_turma WHERE turma_id = %s", GetSQLValueString($colname_turma, "int"));
$turma = mysql_query($query_turma, $SmecelNovo) or die(mysql_error());
$row_turma = mysql_fetch_assoc($turma);
$totalRows_turma = mysql_num_rows($turma);

$maxRows_atividades = 2000;
$pageNum_atividades = 0;
if (isset($_GET['pageNum_atividades'])) {
  $pageNum_atividades = $_GET['pageNum_atividades'];
}
$startRow_atividades = $pageNum_atividades * $maxRows_atividades;

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_atividades = "
SELECT turma_id, turma_nome, turma_id_escola, turma_id_sec, turma_etapa, smc_ativ_data, smc_ativ_id_escola, smc_ativ_id_turma,
smc_ativ_caminho, smc_ativ_obs 
FROM smc_turma
INNER JOIN smc_atividade ON smc_ativ_id_turma = turma_id
WHERE turma_etapa = $row_turma[turma_etapa] AND turma_id_sec = $row_turma[turma_id_sec]
ORDER BY smc_ativ_data DESC
";
$query_limit_atividades = sprintf("%s LIMIT %d, %d", $query_atividades, $startRow_atividades, $maxRows_atividades);
$atividades = mysql_query($query_limit_atividades, $SmecelNovo) or die(mysql_error());
$row_atividades = mysql_fetch_assoc($atividades);

if (isset($_GET['totalRows_atividades'])) {
  $totalRows_atividades = $_GET['totalRows_atividades'];
} else {
  $all_atividades = mysql_query($query_atividades);
  $totalRows_atividades = mysql_num_rows($all_atividades);
}
$totalPages_atividades = ceil($totalRows_atividades/$maxRows_atividades)-1;

$queryString_atividades = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_atividades") == false && 
        stristr($param, "totalRows_atividades") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_atividades = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_atividades = sprintf("&totalRows_atividades=%d%s", $totalRows_atividades, $queryString_atividades);

if($totalRows_ProfLogado=="") {
	header("Location:../index.php?loginErr");
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
     
    <div class="col s12 m10">
	
	  <h5>SUGESTÃO DE ATIVIDADES PARA O <?php echo $row_turma['turma_nome']; ?></h5>
	  
	  <hr>
	  
	  
<p><a href="index.php" class="waves-effect waves-light btn-small btn-flat"><i class="material-icons left">arrow_back</i> Voltar</a></p>


<p>
<input type="text" class="buscar-atividade" alt="fonte-tabela" placeholder="Digite um texto para buscar uma atividade" autofocus="autofocus"/>
</p>

<i class="right">*atividades de todas as disciplinas produzidas para este curso</i>

<?php if ($totalRows_atividades > 0) { ?>

<table class="fonte-tabela" role="grid">
<thead>
  <tr>
    <th class="center">ATIVIDADE</th>
    <th class="center">DATA</th>
    <th class="center">TURMA</th>
	<th class="center" width="80">VISUALIZAR</th>
    <th class="center" width="80">BAIXAR</th>
  </tr>
  </thead>
  <tbody>
  <?php do { ?>
    <tr>
	  <td><?php echo $row_atividades['smc_ativ_obs']; ?></td>
      <td class="center"><?php echo inverteData($row_atividades['smc_ativ_data']); ?></td>
      <td class="center"><?php echo $row_atividades['turma_nome']; ?></td>
      <td class="center"><a href="https://docs.google.com/gview?url=https://www.smecel.com.br/atividades/<?php echo $row_atividades['turma_id_escola']; ?>/<?php echo $row_atividades['turma_id']; ?>/<?php echo $row_atividades['smc_ativ_caminho']; ?>&amp;embedded=true" target="_blank"><i class="material-icons">remove_red_eye</i></a></td>
      <td class="center"><a href="https://www.smecel.com.br/atividades/<?php echo $row_atividades['turma_id_escola']; ?>/<?php echo $row_atividades['turma_id']; ?>/<?php echo $row_atividades['smc_ativ_caminho']; ?>" target="_blank"><i class="material-icons">cloud_download</i></a></td>
    </tr>
    <?php } while ($row_atividades = mysql_fetch_assoc($atividades)); ?>
	</tbody>
</table>

<hr>

<table border="0">
  <tr>
    <td width="25%"><?php if ($pageNum_atividades > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_atividades=%d%s", $currentPage, 0, $queryString_atividades); ?>">PRIMEIRA</a>
        <?php } // Show if not first page ?></td>
    <td width="25%"><?php if ($pageNum_atividades > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_atividades=%d%s", $currentPage, max(0, $pageNum_atividades - 1), $queryString_atividades); ?>">ANTERIOR</a>
        <?php } // Show if not first page ?></td>
    <td width="25%"><?php if ($pageNum_atividades < $totalPages_atividades) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_atividades=%d%s", $currentPage, min($totalPages_atividades, $pageNum_atividades + 1), $queryString_atividades); ?>">PRÓXIMA</a>
        <?php } // Show if not last page ?></td>
    <td width="25%"><?php if ($pageNum_atividades < $totalPages_atividades) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_atividades=%d%s", $currentPage, $totalPages_atividades, $queryString_atividades); ?>">ÚLTIMA</a>
        <?php } // Show if not last page ?></td>
  </tr>
</table>

<?php } else { ?>

Nenhuma atividade cadastrada

<?php } ?>


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
	
	<script type="text/javascript">
$(function(){
    $(".buscar-atividade").keyup(function(){
        //pega o css da tabela 
        var tabela = $(this).attr('alt');
        if( $(this).val() != ""){
            $("."+tabela+" tbody>tr").hide();
            $("."+tabela+" td:contains-ci('" + $(this).val() + "')").parent("tr").show();
        } else{
            $("."+tabela+" tbody>tr").show();
        }
    }); 
});
$.extend($.expr[":"], {
    "contains-ci": function(elem, i, match, array) {
        return (elem.textContent || elem.innerText || $(elem).text() || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
    }
});
</script>
	
    </body>
  </html>
  <?php
mysql_free_result($ProfLogado);

mysql_free_result($turma);

mysql_free_result($atividades);
?>