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

$anoAnterior = $row_AnoLetivo['ano_letivo_ano']-1; 
//$anoAnterior = "2020"; 

$colname_Turma = "-1";
if (isset($_GET['turma'])) {
$colname_Turma = anti_injection($_GET['turma']);
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turma = sprintf("
SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo, etapa_id, etapa_nome_abrev
FROM smc_turma
INNER JOIN smc_etapa ON etapa_id = turma_etapa 
WHERE turma_id = %s", GetSQLValueString($colname_Turma, "int"));
$Turma = mysql_query($query_Turma, $SmecelNovo) or die(mysql_error());
$row_Turma = mysql_fetch_assoc($Turma);
$totalRows_Turma = mysql_num_rows($Turma);

$colname_Disciplina = "-1";
if (isset($_GET['disciplina'])) {
$colname_Disciplina = anti_injection($_GET['disciplina']);
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Disciplina = sprintf("SELECT disciplina_id, disciplina_area_conhecimento_id, disciplina_codigo_inep, disciplina_ordem, disciplina_nome, disciplina_nome_abrev FROM smc_disciplina WHERE disciplina_id = %s", GetSQLValueString($colname_Disciplina, "int"));
$Disciplina = mysql_query($query_Disciplina, $SmecelNovo) or die(mysql_error());
$row_Disciplina = mysql_fetch_assoc($Disciplina);
$totalRows_Disciplina = mysql_num_rows($Disciplina);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Aula = "
SELECT plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, plano_aula_data, plano_aula_data_cadastro, 
plano_aula_texto, plano_aula_conteudo, plano_aula_atividade, plano_aula_atividade_resposta_obrigatoria, plano_aula_atividade_resposta_obrigatoria_data_limite, 
plano_aula_video, plano_aula_google_form, plano_aula_google_form_tempo, plano_aula_publicado, plano_aula_hash, turma_id, turma_etapa, turma_nome, turma_ano_letivo
FROM smc_plano_aula
INNER JOIN smc_turma ON turma_id = plano_aula_id_turma
WHERE plano_aula_id_disciplina = '$colname_Disciplina' AND plano_aula_publicado = 'S' AND plano_aula_id_professor = '$row_ProfLogado[func_id]' AND YEAR(plano_aula_data) < '$row_AnoLetivo[ano_letivo_ano]' AND turma_etapa = '$row_Turma[turma_etapa]'
ORDER BY plano_aula_id DESC
";
$Aula = mysql_query($query_Aula, $SmecelNovo) or die(mysql_error());
$row_Aula = mysql_fetch_assoc($Aula);
$totalRows_Aula = mysql_num_rows($Aula);



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
	
	  <h5>Acervo de aulas:</h5>
	  
	  <hr>
	  
		<a href="index.php" class="waves-effect waves-light btn-small btn-flat"><i class="material-icons left">arrow_back</i> Voltar</a> 
        
        <blockquote>
		
        Componente: <?php echo $row_Disciplina['disciplina_nome']; ?><br>
        Etapa: <?php echo $row_Turma['etapa_nome_abrev']; ?><br>
        
        </blockquote>
		
		<div class="card-panel"><strong>Atenção:</strong> Suas aulas registradas neste mesmo componente e etapa de ensino.</div>
        
        <?php if ($totalRows_Aula > 0) { ?>

      <table>
      <thead>
        <tr>
          <th class="center">TURMA</th>
          <th class="center">ANO</th>
          <th class="center">DATA</th>
          <th class="center">AULA</th>
          <th class="center"></th>
        </tr>
        </thead>
        <tbody>
        <?php do { ?>
          <tr>
            <td class="center"><?php echo $row_Aula['turma_nome']; ?></td>
            <td class="center"><?php echo $row_Aula['turma_ano_letivo']; ?></td>
            <td class="center"><?php echo date("d/m/Y", strtotime($row_Aula['plano_aula_data'])); ?></td>
            <td class=""><?php echo $row_Aula['plano_aula_texto']; ?></td>
            <td class="center"><a href="forum.php?hash=<?php echo $row_Aula['plano_aula_hash']; ?>" class="col s12 waves-effect waves-light btn-small" target="_blank">VER</a></td>
          </tr>
          <?php } while ($row_Aula = mysql_fetch_assoc($Aula)); ?>
      	</tbody>
      </table>
      
      <?php } else { ?>
      
	  <p>Nenhuma aula anterior</p>
	  
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
    </body>
  </html>
  <?php
mysql_free_result($ProfLogado);

mysql_free_result($Aula);
?>