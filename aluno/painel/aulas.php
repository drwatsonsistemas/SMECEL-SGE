<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include('../../sistema/funcoes/inverteData.php'); ?>
<?php include('../../sistema/funcoes/url_base.php'); ?>
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
$MM_authorizedUsers = "6";
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


$colname_AlunoLogado = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_AlunoLogado = $_SESSION['MM_Username'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AlunoLogado = sprintf("
SELECT *
FROM smc_aluno
WHERE aluno_id = %s", GetSQLValueString($colname_AlunoLogado, "int"));
$AlunoLogado = mysql_query($query_AlunoLogado, $SmecelNovo) or die(mysql_error());
$row_AlunoLogado = mysql_fetch_assoc($AlunoLogado);
$totalRows_AlunoLogado = mysql_num_rows($AlunoLogado);
if($totalRows_AlunoLogado=="") {
	header("Location:../index.php?loginErr");
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matricula = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, 
vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, 
vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, vinculo_aluno_vacina_data_retorno, escola_id, escola_nome,
turma_id, turma_nome, turma_turno, turma_etapa, 
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno_nome 
FROM smc_vinculo_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma 
INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola 
WHERE vinculo_aluno_id_aluno = '$row_AlunoLogado[aluno_id]' ORDER BY vinculo_aluno_id DESC LIMIT 0,1";
$Matricula = mysql_query($query_Matricula, $SmecelNovo) or die(mysql_error());
$row_Matricula = mysql_fetch_assoc($Matricula);
$totalRows_Matricula = mysql_num_rows($Matricula);


$disciplinaQry = "";
if (isset($_GET['disciplina'])) {
  $colname_disciplina = anti_injection($_GET['disciplina']);
  $disciplinaQry = " AND plano_aula_id_disciplina = '$colname_disciplina' ";
}

$dataa = date('Y-m-d');

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Conteudo = "
SELECT 
plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, plano_aula_data, plano_aula_texto, plano_aula_conteudo, plano_aula_atividade, plano_aula_video, 
plano_aula_google_form, plano_aula_publicado, plano_aula_hash, plano_aula_meet,
func_id, func_nome, disciplina_id, disciplina_nome, disciplina_cor_fundo 
FROM smc_plano_aula
INNER JOIN smc_func ON func_id = plano_aula_id_professor
INNER JOIN smc_disciplina ON disciplina_id = plano_aula_id_disciplina
WHERE plano_aula_id_turma = '$row_Matricula[vinculo_aluno_id_turma]' AND plano_aula_publicado = 'S' AND plano_aula_data = '$dataa' AND (plano_aula_conteudo IS NOT NULL OR plano_aula_video IS NOT NULL OR plano_aula_atividade IS NOT NULL OR plano_aula_google_form IS NOT NULL) $disciplinaQry
ORDER BY plano_aula_data DESC
";
$Conteudo = mysql_query($query_Conteudo, $SmecelNovo) or die(mysql_error());
$row_Conteudo = mysql_fetch_assoc($Conteudo);
$totalRows_Conteudo = mysql_num_rows($Conteudo);  

if ($row_Matricula['vinculo_aluno_situacao']<>"1") { 
  header("Location:index.php");
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
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><?php echo $row_AlunoLogado['aluno_nome']; ?> - <?php echo $row_Matricula['turma_nome']; ?> - <?php echo $row_Matricula['turma_turno_nome']; ?> - <?php echo $row_Matricula['escola_nome']; ?></title>
<!--Import Google Icon Font-->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<!--Import materialize.css-->
<link type="text/css" rel="stylesheet" href="../css/materialize.min.css"  media="screen,projection"/>
<link type="text/css" rel="stylesheet" href="../css/app.css"  media="screen,projection"/>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
<link type="text/css" rel="stylesheet" href="css/geral.css" media="screen,projection" />




<style>
table {
	width:100%;
	border-collapse: collapse;
	font-size:12px;
}
th, td {
	border:0px solid #ccc;
}
th, td {
	padding:5px;
	height:15px;
	line-height:15px;
}
</style>


</head>
<body class="indigo lighten-5">

<?php include "menu_top.php"?>

<div class="container">
  <div class="row white" style="margin: 10px 0;">
  
    <div class="col s12 m2 hide-on-small-only">
      <p>
      <?php 
        if (!empty($row_AlunoLogado['aluno_foto2'])) { ?>
            <img src="<?php echo URL_BASE.'aluno/fotos2/' . $row_AlunoLogado['aluno_foto2']; ?>" width="100%" class="hoverable">
        <?php } elseif (!empty($row_AlunoLogado['aluno_foto'])) { ?>
            <img src="<?php echo URL_BASE.'aluno/fotos/' . $row_AlunoLogado['aluno_foto']; ?>" width="100%" class="hoverable">
        <?php } else { ?>
            <img src="<?php echo URL_BASE.'aluno/fotos2/semfoto.jpg'; ?>" width="100%" class="hoverable">
        <?php } ?>
				<br>
		<small style="font-size:14px;">
                  <?php echo current( str_word_count($row_AlunoLogado['aluno_nome'],2)); ?>
                  <?php $word = explode(" ", trim($row_AlunoLogado['aluno_nome'])); echo $word[count($word)-1]; ?>
        </small>
      </p>

	<?php include "menu_esq.php"; ?>

    </div>
        
    <div class="col s12 m10">


 <h5><strong>AULAS DE HOJE (<?php echo $totalRows_Conteudo; ?>)</strong></h5>
 <hr>
 <a href="index.php" class="waves-effect waves-light btn-small btn-flat"><i class="material-icons left">arrow_back</i>VOLTAR</a>
 <a href="aulasNovas.php" class="waves-effect waves-light purple btn-small btn"><i class="material-icons left">new_releases</i>NOVAS</a>
 <a href="aulas.php" class="waves-effect waves-light btn-small btn disabled"><i class="material-icons left">alarm_on</i>HOJE</a>
 <a href="aulasTodas.php" class="waves-effect waves-light orange btn-small btn"><i class="material-icons left">all_inclusive</i>TODAS</a>
 <a href="disciplinas.php" class="waves-effect waves-light btn-small btn green lighten-1"><i class="material-icons left">apps</i>DISCIPLINAS</a>

  
 <?php if ($totalRows_Conteudo > 0) { ?>
 
<hr>
 
<h5 class="center">Aulas de hoje (<?php echo date('d/m/Y'); ?>)</h5>
<p class="center"><i>Clique no link TODAS para visualizar as aulas anteriores</i></p>
 
 <table class="striped1">

   <tbody>
   <?php do { ?>
   
   <?php 
   
		mysql_select_db($database_SmecelNovo, $SmecelNovo);
		$query_Visualizou = "SELECT visualiza_aula_id, visualiza_aula_id_aula, visualiza_aula_id_matricula, visualiza_aula_data_hora FROM smc_visualiza_aula WHERE visualiza_aula_id_aula = $row_Conteudo[plano_aula_id] AND visualiza_aula_id_matricula = '$row_Matricula[vinculo_aluno_id]'";
		$Visualizou = mysql_query($query_Visualizou, $SmecelNovo) or die(mysql_error());
		$row_Visualizou = mysql_fetch_assoc($Visualizou);
		$totalRows_Visualizou = mysql_num_rows($Visualizou);
		
		mysql_select_db($database_SmecelNovo, $SmecelNovo);
		$query_Anexos = "SELECT plano_aula_anexo_id, plano_aula_anexo_id_atividade, plano_aula_anexo_arquivo FROM smc_plano_aula_anexo WHERE plano_aula_anexo_id_atividade = '$row_Conteudo[plano_aula_id]'";
		$Anexos = mysql_query($query_Anexos, $SmecelNovo) or die(mysql_error());
		$row_Anexos = mysql_fetch_assoc($Anexos);
		$totalRows_Anexos = mysql_num_rows($Anexos);
   
   ?>
     <tr>
	 
	 
      <td>
	  
	  <div style="padding:10px; border-left: 6px solid <?php echo $row_Conteudo['disciplina_cor_fundo']; ?>; color:<?php echo $row_Conteudo['disciplina_cor_fundo']; ?>">
	  
	  <a href="aulas_conteudo.php?aula=<?php echo $row_Conteudo['plano_aula_hash']; ?>">
	  
	  <span style="background-color:<?php echo $row_Conteudo['disciplina_cor_fundo']; ?>; padding:3px; margin: 5px 0; color: white" >
	  <?php echo $row_Conteudo['plano_aula_id']; ?> - <?php echo $row_Conteudo['disciplina_nome']; ?>
	  </span>
	  
	  <br><br> 	
	  
	  
	  <strong class="black-text"><?php echo inverteData($row_Conteudo['plano_aula_data']); ?> | <?php echo $row_Conteudo['plano_aula_texto']; ?></strong><br>
	  <i><small><?php echo $row_Conteudo['func_nome']; ?></small></i>
	  
	  <br>
	   <?php if ($row_Conteudo['plano_aula_conteudo']<>"") { ?><a class="tooltipped" data-position="bottom" data-tooltip="Conteúdo para ler" href="aulas_conteudo.php?aula=<?php echo $row_Conteudo['plano_aula_hash']; ?>"><i class="material-icons purple-text">import_contacts</i></a><?php } ?>
	   <?php if ($row_Conteudo['plano_aula_meet']=="S") { ?><a class="tooltipped" data-position="bottom" data-tooltip="Conteúdo para ler" href="aulas_conteudo.php?aula=<?php echo $row_Conteudo['plano_aula_hash']; ?>"><i class="material-icons red-text">videocam</i></a><?php } ?>
	   <?php if ($row_Conteudo['plano_aula_atividade']<>"") { ?><a class="tooltipped" data-position="bottom" data-tooltip="Atividade proposta" href="aulas_conteudo.php?aula=<?php echo $row_Conteudo['plano_aula_hash']; ?>"><i class="material-icons orange-text">description</i></a><?php } ?>
	   <?php if ($row_Conteudo['plano_aula_video']<>"") { ?><a class="tooltipped" data-position="bottom" data-tooltip="Vídeo de apoio" href="aulas_conteudo.php?aula=<?php echo $row_Conteudo['plano_aula_hash']; ?>"><i class="material-icons red-text">ondemand_video</i></a><?php } ?>
	   <?php if ($totalRows_Anexos > 0) { ?><a class="tooltipped" data-position="bottom" data-tooltip="<?php echo $totalRows_Anexos; ?> anexo(s)" href="aulas_conteudo.php?aula=<?php echo $row_Conteudo['plano_aula_hash']; ?>"><i class="material-icons green-text">attach_file</i></a><?php } ?>
	   <?php if ($row_Conteudo['plano_aula_google_form']<>"") { ?><a class="tooltipped" data-position="bottom" data-tooltip="Avaliação" href="aulas_conteudo.php?aula=<?php echo $row_Conteudo['plano_aula_hash']; ?>"><i class="material-icons brown-text">class</i></a><?php } ?>
	  	  
	  </a>
	  </div>
	  
	  </td>	 

	  	   
	   
      <td width="30">

		   <a class="right" href="aulas_conteudo.php?aula=<?php echo $row_Conteudo['plano_aula_hash']; ?>">
		   <?php if ($totalRows_Visualizou > 0) { ?>
		   <i class="material-icons green-text">assignment_turned_in</i>
		   <?php } else { ?>
		   <i class="material-icons red-text">new_releases</i>
		   <?php } ?>
		   </a>
	  
	  </td>

     </tr>
	 <?php mysql_free_result($Visualizou); ?>
     <?php } while ($row_Conteudo = mysql_fetch_assoc($Conteudo)); ?>
	 </tbody>
 </table>
 
 <p class="center">Para visualizar as aulas anteriores, clique no link <a href="aulasTodas.php">aulas anteriores</a></p>
 
 
 <?php } else { ?>
 
 <hr>
 <br>
 <div class="card-panel grey lighten-5">
	
 <p class="left"><i class="medium material-icons">info_outline</i></p>
 <p class="center">Nenhuma aula lançada para a data de hoje.</p>
 <p class="center">Para visualizar as aulas anteriores, clique no link <a href="aulasTodas.php">aulas anteriores</a></p>

	
  </div>
 
 <?php } ?>
 
    </div>
    

    
    
    
  </div>
</div>

<!--JavaScript at end of body for optimized loading--> 
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script> 
<script type="text/javascript" src="../js/materialize.min.js"></script>
 
<script type="text/javascript">
		$(document).ready(function(){
			$('.sidenav').sidenav();
			$('.tabs').tabs();
			$('.dropdown-trigger').dropdown();
			$('.materialboxed').materialbox();
			$('.tooltipped').tooltip();
		});
	</script>
</body>
</html>
<?php
mysql_free_result($Matricula);

mysql_free_result($Conteudo);

mysql_free_result($AlunoLogado);
?>
