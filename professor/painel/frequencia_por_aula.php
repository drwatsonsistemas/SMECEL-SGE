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

$colname_Disciplina = "-1";
if (isset($_GET['disciplina'])) {
$colname_Disciplina = anti_injection($_GET['disciplina']);
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Disciplina = sprintf("SELECT disciplina_id, disciplina_area_conhecimento_id, disciplina_codigo_inep, disciplina_ordem, disciplina_nome, disciplina_nome_abrev FROM smc_disciplina WHERE disciplina_id = %s", GetSQLValueString($colname_Disciplina, "int"));
$Disciplina = mysql_query($query_Disciplina, $SmecelNovo) or die(mysql_error());
$row_Disciplina = mysql_fetch_assoc($Disciplina);
$totalRows_Disciplina = mysql_num_rows($Disciplina);

if($totalRows_Disciplina=="") {
	header("Location:index.php?erro");
}

$colname_Turma = "-1";
if (isset($_GET['turma'])) {
$colname_Turma = anti_injection($_GET['turma']);
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turma = sprintf("SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo FROM smc_turma WHERE turma_id = %s", GetSQLValueString($colname_Turma, "int"));
$Turma = mysql_query($query_Turma, $SmecelNovo) or die(mysql_error());
$row_Turma = mysql_fetch_assoc($Turma);
$totalRows_Turma = mysql_num_rows($Turma);

if($totalRows_Turma=="") {
	header("Location:index.php?erro");
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Escolas = "
SELECT 
ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola, 
escola_id, escola_nome, turma_id, turma_nome, turma_ano_letivo 
FROM smc_ch_lotacao_professor
INNER JOIN smc_escola ON escola_id = ch_lotacao_escola
INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
WHERE ch_lotacao_professor_id = '$row_ProfLogado[func_id]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND ch_lotacao_turma_id = '$colname_Turma'
GROUP BY ch_lotacao_escola";
$Escolas = mysql_query($query_Escolas, $SmecelNovo) or die(mysql_error());
$row_Escolas = mysql_fetch_assoc($Escolas);
$totalRows_Escolas = mysql_num_rows($Escolas);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Contagem = "
SELECT visualiza_aula_id, visualiza_aula_id_aula, visualiza_aula_id_matricula, count(*) as total,
plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, plano_aula_data, plano_aula_texto, plano_aula_publicado, plano_aula_hash, 
disciplina_id, disciplina_nome
FROM smc_visualiza_aula
INNER JOIN smc_plano_aula ON plano_aula_id = visualiza_aula_id_aula
INNER JOIN smc_disciplina ON disciplina_id = plano_aula_id_disciplina
WHERE plano_aula_publicado = 'S' AND plano_aula_id_turma = '$colname_Turma' AND plano_aula_id_disciplina = '$colname_Disciplina' AND plano_aula_id_professor = '$row_ProfLogado[func_id]' 
GROUP BY visualiza_aula_id_aula 
ORDER BY visualiza_aula_id_aula DESC";
$Contagem = mysql_query($query_Contagem, $SmecelNovo) or die(mysql_error());
$row_Contagem = mysql_fetch_assoc($Contagem);
$totalRows_Contagem = mysql_num_rows($Contagem);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Alunos = "SELECT vinculo_aluno_id_turma, vinculo_aluno_situacao FROM smc_vinculo_aluno WHERE vinculo_aluno_situacao = '1' AND vinculo_aluno_id_turma = '$colname_Turma'";
$Alunos = mysql_query($query_Alunos, $SmecelNovo) or die(mysql_error());
$row_Alunos = mysql_fetch_assoc($Alunos);
$totalRows_Alunos = mysql_num_rows($Alunos);

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
	
	  <h5>PERCENTUAL DE FREQUÊNCIA POR AULA</h5>
	  
	  <hr>
	  
	  
    <a href="turmas.php?disciplina=<?php echo $colname_Disciplina; ?>&cod=<?php echo $row_Escolas['ch_lotacao_escola']; ?>" class="waves-effect waves-light btn-small btn-flat"><i class="material-icons left">arrow_back</i> Voltar</a>
    
<blockquote>
<h6><?php echo $row_Escolas['escola_nome']; ?><br><small><?php echo $row_Disciplina['disciplina_nome']; ?> - <?php echo $row_Turma['turma_nome']; ?></small></h6>
</blockquote>
    
 <h5><?php echo $totalRows_Alunos; ?> alunos matriculados</h5> 
 
          
		  <?php if ($totalRows_Contagem > 0) { ?>
		  
		  <table>
		  <thead>
            <tr>
              <th width="50" class="center">AULA</th>
              <th width="70" class="center">DATA</th>
              <th class="center">DISCIPLINA</th>
              <th class="center">CONTEÚDO</th>
              <th width="40" class="center">ACESSOS</th>
              <th width="40" class="center">%</th>
            </tr>
			</thead>
			<tbody>
            <?php do { ?>

			<?php 
			
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Vis = "SELECT visualiza_aula_id, visualiza_aula_id_aula, visualiza_aula_id_matricula FROM smc_visualiza_aula WHERE visualiza_aula_id_aula = '$row_Contagem[plano_aula_id]' GROUP BY visualiza_aula_id_matricula";
$Vis = mysql_query($query_Vis, $SmecelNovo) or die(mysql_error());
$row_Vis = mysql_fetch_assoc($Vis);
$totalRows_Vis = mysql_num_rows($Vis);
			
			
			?>
			
              <tr>
                <td class="center"><?php echo $row_Contagem['plano_aula_id']; ?></td>
                <td class="center"><?php echo inverteData($row_Contagem['plano_aula_data']); ?></td>
                <td class="center"><?php echo $row_Contagem['disciplina_nome']; ?></td>
                <td><?php echo $row_Contagem['plano_aula_texto']; ?></td>
                <td class="center"><a href="frequencia_aula.php?aula=<?php echo $row_Contagem['plano_aula_hash']; ?>&turma=<?php echo $row_Contagem['plano_aula_id_turma']; ?>&disciplina=<?php echo $row_Contagem['disciplina_id']; ?>" target="_blank"><?php echo $totalRows_Vis; ?></a></td>
                <td class="center"><?php $percentual = number_format((($totalRows_Vis/$totalRows_Alunos)*100),1); ?><?php if ($percentual > 100) { echo "100"; } else { echo $percentual; } ?>%</td>
              </tr>
			  
			  
			  <?php mysql_free_result($Vis); ?>
			  
              <?php } while ($row_Contagem = mysql_fetch_assoc($Contagem)); ?>
			</tbody>
		  </table>
		  
		  <?php } ?>
   
	  <br><br>
	  
	  
	  
		 
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

mysql_free_result($Disciplina);

mysql_free_result($Turma);

mysql_free_result($Escolas);

mysql_free_result($Contagem);

mysql_free_result($Alunos);
?>