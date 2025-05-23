<?php require_once('../../../Connections/SmecelNovo.php'); ?>
<?php include "../../funcoes/anoLetivo.php"; ?>
<?php include "../../funcoes/inverteData.php"; ?>

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
	
  $logoutGoTo = "../../../index.php";
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
$MM_authorizedUsers = "1,4,99";
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

$MM_restrictGoTo = "../../../index.php?err";
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

$colname_Logado = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_Logado = $_SESSION['MM_Username'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Logado = sprintf("SELECT usu_id, usu_nome, usu_email, usu_senha, usu_tipo, usu_sec, usu_escola, usu_status, usu_cadastro FROM smc_usu WHERE usu_email = %s", GetSQLValueString($colname_Logado, "text"));
$Logado = mysql_query($query_Logado, $SmecelNovo) or die(mysql_error());
$row_Logado = mysql_fetch_assoc($Logado);
$totalRows_Logado = mysql_num_rows($Logado);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Escolas = "SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_ue, escola_situacao FROM smc_escola WHERE escola_ue = '1' AND escola_situacao = '1'";
$Escolas = mysql_query($query_Escolas, $SmecelNovo) or die(mysql_error());
$row_Escolas = mysql_fetch_assoc($Escolas);
$totalRows_Escolas = mysql_num_rows($Escolas);
?>
<!DOCTYPE html>
<html>
<head>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-117872281-1');
</script>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>SMECEL - Sistema de Gestão Escolar Municipal</title>
<!--Import Google Icon Font-->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<!--Import materialize.css-->
<link type="text/css" rel="stylesheet" href="../css/materialize.min.css"  media="screen,projection"/>
<link type="text/css" rel="stylesheet" href="../css/app.css"  media="screen,projection"/>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<body>

<nav class="blue darken-6" role="navigation">
  <div class="nav-wrapper container"> <a id="logo-container" href="../index.php" class="brand-logo"><img src="../../../img/logo_pse.png" width="50">PSE-SMECEL</a>
    <ul class="right hide-on-med-and-down">
      <li><?php echo $row_Logado['usu_nome']; ?></li>
      <li><a class="waves-effect waves-light btn-flat white-text modal-trigger" href="../dados.php">MEUS DADOS</a></li>
      <li><a class="waves-effect waves-light btn-flat white-text modal-trigger" href="<?php echo $logoutAction ?>"><i class="material-icons left">exit_to_app</i>SAIR</a></li>
    </ul>
    
    
    <ul id="nav-mobile" class="sidenav">
      <li><a class="waves-effect waves-light btn-flat modal-trigger" href="<?php echo $logoutAction ?>"><i class="material-icons left">exit_to_app</i>SAIR</a></li>
    </ul>
    <a href="#" data-target="nav-mobile" class="sidenav-trigger"><i class="material-icons">menu</i></a> </div>
</nav>
<div class="container">


<h3>Situação vacinal por Unidade Escolar</h3>

  <?php do { ?>
  <?php 
	mysql_select_db($database_SmecelNovo, $SmecelNovo);
	$query_Matriculas = "
	SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, 
	vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, 
	vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, 
	vinculo_aluno_vacina_atualizada, COUNT(*) AS total_atualizacoes,
	CASE vinculo_aluno_vacina_atualizada
	WHEN 'S' THEN 'Atualiz.'
	WHEN 'N' THEN 'Desat.'
	WHEN 'I' THEN 'S/Info.'
	END AS vinculo_aluno_vacina_atualizada_nome  
	FROM smc_vinculo_aluno
	WHERE vinculo_aluno_id_escola = $row_Escolas[escola_id] AND vinculo_aluno_id_sec = $row_Logado[usu_sec] AND vinculo_aluno_ano_letivo = $row_AnoLetivo[ano_letivo_ano]
	GROUP BY vinculo_aluno_vacina_atualizada
	ORDER BY vinculo_aluno_vacina_atualizada_nome ASC";
	$Matriculas = mysql_query($query_Matriculas, $SmecelNovo) or die(mysql_error());
	$row_Matriculas = mysql_fetch_assoc($Matriculas);
	$totalRows_Matriculas = mysql_num_rows($Matriculas);
  ?>
   
  
  <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['VACINAÇÃO', 'SITUAÇÃO'],
		  
		  
		  <?php do { ?>
				['<?php echo $row_Matriculas['vinculo_aluno_vacina_atualizada_nome']; ?> (<?php echo $row_Matriculas['total_atualizacoes']; ?>)', <?php echo $row_Matriculas['total_atualizacoes']; ?>],
	      <?php } while ($row_Matriculas = mysql_fetch_assoc($Matriculas)); ?>
		  
        ]);

        var options = {
          title: ' <?php echo $row_Escolas['escola_nome']; ?>',
		  is3D: false,
		  legend: 'bottom',
		  slices: {
            0: { color: 'blue' },
            1: { color: 'red' }
          }
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart_<?php echo $row_Escolas['escola_id']; ?>'));

        chart.draw(data, options);
      }
    </script>
	<div id="piechart_<?php echo $row_Escolas['escola_id']; ?>" style="width: 47%; height: 400px; float:left; border:#000000 solid 1px; margin:2px;"></div>
  
  
  
  <?php } while ($row_Escolas = mysql_fetch_assoc($Escolas)); ?>
  
  
 
<!-- FIM CONTAINER -->
</div>
<!--JavaScript at end of body for optimized loading--> 
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script> 
<script type="text/javascript" src="../js/materialize.min.js"></script> 
<script type="text/javascript" src="../../js/jquery.mask.min.js"></script> 
<script type="text/javascript" src="../../js/mascara.js"></script> 
<script type="text/javascript">
		$(document).ready(function(){
			$('.sidenav').sidenav();
			$(".dropdown-trigger").dropdown();
		});
	</script>
</body>
</html>
<?php
mysql_free_result($Logado);

mysql_free_result($Escolas);

mysql_free_result($Matriculas);
?>
