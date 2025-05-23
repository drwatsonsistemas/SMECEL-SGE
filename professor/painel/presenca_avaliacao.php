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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Vinculos = "SELECT vinculo_id, vinculo_id_escola, vinculo_id_sec, vinculo_id_funcionario FROM smc_vinculo WHERE vinculo_id_funcionario = '$row_ProfLogado[func_id]'";
$Vinculos = mysql_query($query_Vinculos, $SmecelNovo) or die(mysql_error());
$row_Vinculos = mysql_fetch_assoc($Vinculos);
$totalRows_Vinculos = mysql_num_rows($Vinculos);
include "fnc/anoLetivo.php";

if($totalRows_ProfLogado=="") {
	header("Location:../index.php?loginErr");
}

$colname_Aula = "-1";
if (isset($_GET['aula'])) {
$colname_Aula = anti_injection($_GET['aula']);
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AulasMinistradas = "
SELECT plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, 
plano_aula_data, plano_aula_texto, plano_aula_conteudo, plano_aula_atividade, plano_aula_video, plano_aula_publicado, plano_aula_hash 
FROM smc_plano_aula
WHERE plano_aula_hash = '$colname_Aula' AND plano_aula_id_professor = '$row_ProfLogado[func_id]'
";
$AulasMinistradas = mysql_query($query_AulasMinistradas, $SmecelNovo) or die(mysql_error());
$row_AulasMinistradas = mysql_fetch_assoc($AulasMinistradas);
$totalRows_AulasMinistradas = mysql_num_rows($AulasMinistradas);

if($totalRows_AulasMinistradas=="") {
	header("Location:index.php?erro");
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Alunos = "SELECT 
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, 
vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, 
vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia,
vinculo_aluno_vacina_atualizada, vinculo_aluno_vacina_data_retorno, aluno_id, aluno_nome, aluno_foto  
FROM smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
WHERE vinculo_aluno_id_turma = '$row_AulasMinistradas[plano_aula_id_turma]' AND vinculo_aluno_situacao = '1'
ORDER BY aluno_nome ASC
";
$Alunos = mysql_query($query_Alunos, $SmecelNovo) or die(mysql_error());
$row_Alunos = mysql_fetch_assoc($Alunos);
$totalRows_Alunos = mysql_num_rows($Alunos);


if ((isset($_GET['acesso'])) && ($_GET['acesso'] != "")) {
  $deleteSQL = sprintf("DELETE FROM smc_plano_aula_google_form WHERE plano_aula_google_form_id_atividade = '$row_AulasMinistradas[plano_aula_id]' AND plano_aula_google_form_id=%s",
                       GetSQLValueString($_GET['acesso'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());
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
table1 {
	width:100%;
	border-collapse: collapse;
	font-size:12px;
}

th1, td1 {
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
	
	  <h5>RESULTADO DAS AVALIAÇÕES</h5>
	  
	  <hr>
	  
	  
<a href="plano_aula.php?turma=<?php echo $row_AulasMinistradas['plano_aula_id_turma']; ?>&disciplina=<?php echo $row_AulasMinistradas['plano_aula_id_disciplina']; ?>" class="waves-effect waves-light btn-small btn-flat"><i class="material-icons left">arrow_back</i> Voltar</a>


<h5>Relação de alunos que acessaram a avaliação</h5>
<hr>

<?php $visualizaram = 0; ?>

<div class="row">

<table>

<?php do { ?>

<?php

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Visualizou = "
SELECT plano_aula_google_form_id, plano_aula_google_form_id_aluno, plano_aula_google_form_id_atividade, plano_aula_google_form_data_hora
FROM smc_plano_aula_google_form
WHERE plano_aula_google_form_id_atividade = $row_AulasMinistradas[plano_aula_id] AND plano_aula_google_form_id_aluno = '$row_Alunos[vinculo_aluno_id_aluno]'
";
$Visualizou = mysql_query($query_Visualizou, $SmecelNovo) or die(mysql_error());
$row_Visualizou = mysql_fetch_assoc($Visualizou);
$totalRows_Visualizou = mysql_num_rows($Visualizou);


?>

<tr class="<?php if ($totalRows_Visualizou > 0) { ?> green lighten-4<?php } ?>">

<td width="50">

			<?php if ($row_Alunos['aluno_foto']=="") { ?>
            <img src="<?php echo URL_BASE.'aluno/fotos/' ?>semfoto.jpg" width="100%" class="hoverable">
            <?php } else { ?>
            <img src="<?php echo URL_BASE.'aluno/fotos/' ?><?php echo $row_Alunos['aluno_foto']; ?>" width="100%" class="hoverable">
            <?php } ?>

</td>

<td>
            <?php echo $row_Alunos['aluno_nome']; ?>
             
</td>

<td>
<?php if ($row_Visualizou['plano_aula_google_form_data_hora']<>"") { ?><?php echo date('d/m/Y à\s H\hi', strtotime($row_Visualizou['plano_aula_google_form_data_hora'])); ?> <a href="javascript:func()" onclick="confirmaExclusao('presenca_avaliacao.php?aula=<?php echo $row_AulasMinistradas['plano_aula_hash']; ?>&turma=<?php echo $row_AulasMinistradas['plano_aula_id_turma']; ?>&disciplina=<?php echo $row_AulasMinistradas['plano_aula_id_disciplina']; ?>&acesso=<?php echo $row_Visualizou['plano_aula_google_form_id']; ?>')" class="waves-effect waves-light"><i class="material-icons left">delete_forever</i></a><?php } ?>
</td>



<td>
			<?php if ($totalRows_Visualizou > 0) { $visualizaram++; ?><i class="material-icons green-text tiny right">check_circle</i><?php } else { ?><i class="material-icons red-text tiny right">not_interested</i><?php } ?>

</td>


</tr>
  
  
<?php } while ($row_Alunos = mysql_fetch_assoc($Alunos)); ?>

</table>

</div>

<hr>

  
<h5><?php echo $visualizaram; ?> aluno(s) acessaram a avaliação (<?php $percAcessaram = ($visualizaram * 100) / $totalRows_Alunos; ?><?php echo number_format($percAcessaram, 1, ',', ' '); ?>%)</h5>  

<br>
<br>

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
    
    <script language="Javascript">
	function confirmaExclusao(codigo) {
     var resposta = confirm("Excluindo este registro, o aluno terá outra chance de responder a avaliação. Confirma?");
     	if (resposta == true) {
     	     window.location.href = codigo;
    	 }
	}
	</script>
    
    </body>
  </html>
  <?php
mysql_free_result($ProfLogado);

mysql_free_result($Alunos);

mysql_free_result($Visualizou);
?>