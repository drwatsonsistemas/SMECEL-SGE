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
$query_Turma = sprintf("SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo FROM smc_turma WHERE turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND turma_id = %s", GetSQLValueString($colname_Turma, "int"));
$Turma = mysql_query($query_Turma, $SmecelNovo) or die(mysql_error());
$row_Turma = mysql_fetch_assoc($Turma);
$totalRows_Turma = mysql_num_rows($Turma);

if($totalRows_Turma=="") {
	header("Location:index.php?erro");
}

/*
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AulasMinistradas = "
SELECT plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, 
plano_aula_data, plano_aula_texto, plano_aula_conteudo, plano_aula_atividade, plano_aula_video, plano_aula_google_form, plano_aula_publicado, plano_aula_hash 
FROM smc_plano_aula
WHERE plano_aula_id_turma = '$colname_Turma' AND plano_aula_id_disciplina = '$colname_Disciplina' AND plano_aula_id_professor = '$row_ProfLogado[func_id]'
ORDER BY plano_aula_data DESC, plano_aula_id DESC
";
$AulasMinistradas = mysql_query($query_AulasMinistradas, $SmecelNovo) or die(mysql_error());
$row_AulasMinistradas = mysql_fetch_assoc($AulasMinistradas);
$totalRows_AulasMinistradas = mysql_num_rows($AulasMinistradas);
*/

//FILTRO

$maxRows_AulasMinistradas = 10;
$pageNum_AulasMinistradas = 0;
if (isset($_GET['pageNum_AulasMinistradas'])) {
  $pageNum_AulasMinistradas = $_GET['pageNum_AulasMinistradas'];
}
$startRow_AulasMinistradas = $pageNum_AulasMinistradas * $maxRows_AulasMinistradas;

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AulasMinistradas = "
SELECT plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, 
plano_aula_data, plano_aula_texto, plano_aula_conteudo, plano_aula_atividade, plano_aula_video, plano_aula_google_form, plano_aula_publicado, plano_aula_hash 
FROM smc_plano_aula
WHERE plano_aula_id_turma = '$colname_Turma' AND plano_aula_id_disciplina = '$colname_Disciplina' AND plano_aula_id_professor = '$row_ProfLogado[func_id]'
ORDER BY plano_aula_data DESC, plano_aula_id DESC
";
$query_limit_AulasMinistradas = sprintf("%s LIMIT %d, %d", $query_AulasMinistradas, $startRow_AulasMinistradas, $maxRows_AulasMinistradas);
$AulasMinistradas = mysql_query($query_limit_AulasMinistradas, $SmecelNovo) or die(mysql_error());
$row_AulasMinistradas = mysql_fetch_assoc($AulasMinistradas);

if (isset($_GET['totalRows_AulasMinistradas'])) {
  $totalRows_AulasMinistradas = $_GET['totalRows_AulasMinistradas'];
} else {
  $all_AulasMinistradas = mysql_query($query_AulasMinistradas);
  $totalRows_AulasMinistradas = mysql_num_rows($all_AulasMinistradas);
}
$totalPages_AulasMinistradas = ceil($totalRows_AulasMinistradas/$maxRows_AulasMinistradas)-1;


//FILTRO



mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AulasMinistradasTotal = "
SELECT plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, 
plano_aula_data, plano_aula_texto, plano_aula_conteudo, plano_aula_atividade, plano_aula_video, plano_aula_google_form, plano_aula_publicado, plano_aula_hash 
FROM smc_plano_aula
WHERE plano_aula_id_turma = '$colname_Turma' AND plano_aula_id_disciplina = '$colname_Disciplina' AND plano_aula_id_professor = '$row_ProfLogado[func_id]'
ORDER BY plano_aula_data DESC, plano_aula_id DESC
";
$AulasMinistradasTotal = mysql_query($query_AulasMinistradasTotal, $SmecelNovo) or die(mysql_error());
$row_AulasMinistradasTotal = mysql_fetch_assoc($AulasMinistradasTotal);
$totalRows_AulasMinistradasTotal = mysql_num_rows($AulasMinistradasTotal);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Escolas = "
SELECT 
ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola, 
escola_id, escola_nome, turma_id, turma_nome, turma_ano_letivo 
FROM smc_ch_lotacao_professor
INNER JOIN smc_escola ON escola_id = ch_lotacao_escola
INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
WHERE ch_lotacao_professor_id = '$row_ProfLogado[func_id]' AND ch_lotacao_turma_id = '$colname_Turma'
GROUP BY ch_lotacao_escola";
$Escolas = mysql_query($query_Escolas, $SmecelNovo) or die(mysql_error());
$row_Escolas = mysql_fetch_assoc($Escolas);
$totalRows_Escolas = mysql_num_rows($Escolas);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_disciplinaMatriz = "
SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_id_disciplina, matriz_disciplina_ch_ano 
FROM smc_matriz_disciplinas
WHERE matriz_disciplina_id_matriz = '$row_Turma[turma_matriz_id]' AND matriz_disciplina_id_disciplina = '$row_Disciplina[disciplina_id]'
";
$disciplinaMatriz = mysql_query($query_disciplinaMatriz, $SmecelNovo) or die(mysql_error());
$row_disciplinaMatriz = mysql_fetch_assoc($disciplinaMatriz);
$totalRows_disciplinaMatriz = mysql_num_rows($disciplinaMatriz);


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
$editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {


$qtd = $_POST['plano_aula_qtd']; 

for($i=0; $i < $qtd; $i++){

$dataCad = date('Y-m-d H:i:s');

//$hash = md5($totalRows_Turma['turma_id'].$i.$dataCad);

$hash = md5(uniqid(""));

sleep(1);

$insertSQL = sprintf("INSERT INTO smc_plano_aula (plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, plano_aula_data, plano_aula_data_cadastro, plano_aula_texto, plano_aula_hash, plano_aula_publicado) VALUES (%s, %s, %s, %s, '$dataCad', %s, '$hash', 'N')",
GetSQLValueString($_POST['plano_aula_id_turma'], "int"),
GetSQLValueString($_POST['plano_aula_id_disciplina'], "int"),
GetSQLValueString($_POST['plano_aula_id_professor'], "int"),
GetSQLValueString(inverteData($_POST['plano_aula_data']), "date"),
GetSQLValueString($_POST['plano_aula_texto'], "text"));

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

}

$insertGoTo = "plano_aula.php?aulaLancada";
if (isset($_SERVER['QUERY_STRING'])) {
$insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
$insertGoTo .= $_SERVER['QUERY_STRING'];
}
header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_GET['deletar'])) && ($_GET['deletar'] != "")) {
  $deleteSQL = sprintf("DELETE FROM smc_plano_aula WHERE plano_aula_hash=%s",
                       GetSQLValueString($_GET['deletar'], "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());

  $deleteGoTo = "plano_aula.php?disciplina=$row_AulasMinistradas[plano_aula_id_disciplina]&turma=$row_AulasMinistradas[plano_aula_id_turma]&deletado";
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

      <title><?php echo $row_ProfLogado['func_nome']?> - Painel do Professor</title>
    
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

th, td {
	border:0px solid #ccc;
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
	
	<h5>Cadastrar Aula</h5>
	  
	  <hr>

    <a href="turmas.php?disciplina=<?php echo $colname_Disciplina; ?>&cod=<?php echo $row_Escolas['ch_lotacao_escola']; ?>" class="waves-effect waves-light btn-small btn-flat"><i class="material-icons left">arrow_back</i> Voltar</a>


<blockquote>
<h6><?php echo $row_Escolas['escola_nome']; ?><br><strong><?php echo $row_Disciplina['disciplina_nome']; ?> - <?php echo $row_Turma['turma_nome']; ?></strong></h6>
</blockquote>


	
<p>	
<a class="waves-effect waves-light btn modal-trigger" href="#modal1">INCLUIR NOVA AULA</a>
</p>

         <div class="col s12 m4 center">
            <div class="card white-text grey">
               <div class="card-content center">
                     <i class="material-icons background-round mt-5 medium">dvr</i>
                     <h5 class="white-text lighten-4"><?php echo $row_disciplinaMatriz['matriz_disciplina_ch_ano']; ?></h5>
                     <p class="white-text lighten-4">AULAS</p>
                     <p class="white-text lighten-4">NECESSÁRIAS (ANUAL)</p>
                  </div>
            </div>
         </div>
         
         <div class="col s12 m4 center">
            <div class="card white-text grey">
               <div class="card-content center">
                     <i class="material-icons background-round mt-5 medium">equalizer</i>
                     <h5 class="white-text lighten-4"><?php echo $totalRows_AulasMinistradasTotal; ?></h5>
                     <p class="white-text lighten-4">AULAS POSTADAS</p>
                     <p class="white-text lighten-4">PELO PROFESSOR</p>
                  </div>
            </div>
         </div>
         
         <?php $diferenca = $row_disciplinaMatriz['matriz_disciplina_ch_ano'] - $totalRows_AulasMinistradasTotal; ?>
         <?php $resultado = $diferenca; if ($diferenca < 0) { $resultado = $diferenca*(-1); } ?>
         <div class="col s12 m4 center">
            <div class="card white-text <?php if ($diferenca > 0) { echo "red"; } else if ($diferenca < 0) { echo "green"; } else { echo "green"; } ?> lighten-2">
               <div class="card-content center">
                     <i class="material-icons background-round mt-5 medium">developer_board</i>
                     <h5 class="white-text lighten-4"><?php if ($diferenca<>0) { echo $resultado; } else { echo "&nbsp;"; } ?></h5>
                     <p class="white-text lighten-4">AULAS</p>
                     <p class="white-text lighten-4"><?php if ($diferenca > 0) { echo "PENDENTES"; } else if ($diferenca < 0) { echo "EXCEDENTES"; } else { echo "CONCLUÍDAS"; } ?></p>
                  </div>
            </div>
         </div>



	
    <?php if ($totalRows_AulasMinistradas > 0) { // Show if recordset not empty ?>

<table class="striped">	
<tbody>
 <?php do { ?>
 
 <tr>
 

	<td>
	<?php if ($row_AulasMinistradas['plano_aula_publicado']=="S") { ?><i class="material-icons green-text tooltipped" data-position="bottom" data-tooltip="Aula publicada">remove_red_eye</i><?php } else { ?><i class="material-icons grey-text text-lighten-2 tooltipped" data-position="bottom" data-tooltip="Aula aguardando publicação">remove_red_eye</i><?php } ?>	
	<?php if ($row_AulasMinistradas['plano_aula_conteudo']<>"") { ?><i class="material-icons purple-text tooltipped" data-position="bottom" data-tooltip="Conteúdo para estudo">import_contacts</i><?php } else { ?><i class="material-icons grey-text text-lighten-3 ">import_contacts</i><?php } ?>
	<?php if ($row_AulasMinistradas['plano_aula_atividade']<>"") { ?><i class="material-icons orange-text tooltipped" data-position="bottom" data-tooltip="Atividades">description</i><?php } else { ?><i class="material-icons grey-text text-lighten-3 ">description</i><?php } ?>
	<?php if ($row_AulasMinistradas['plano_aula_video']<>"") { ?><i class="material-icons red-text tooltipped" data-position="bottom" data-tooltip="Vídeo de apoio">ondemand_video</i><?php } else { ?><i class="material-icons grey-text text-lighten-3 ">ondemand_video</i><?php } ?>
	
	<?php if ($row_AulasMinistradas['plano_aula_google_form']<>"") { ?><a href="presenca_avaliacao.php?aula=<?php echo $row_AulasMinistradas['plano_aula_hash']; ?>&turma=<?php echo $colname_Turma; ?>&disciplina=<?php echo $colname_Disciplina; ?>"><i class="material-icons brown-text tooltipped" data-position="bottom" data-tooltip="Avaliação / Frequência">class</i></a><?php } else { ?><i class="material-icons brown-text text-lighten-5">class</i><?php } ?>
	
	<br>
	<strong><small><?php echo $row_AulasMinistradas['plano_aula_id']; ?></small></strong><br><small><?php echo inverteData($row_AulasMinistradas['plano_aula_data']); ?><br><?php echo $row_AulasMinistradas['plano_aula_texto']; ?></small>
	</td>


	

	<td>	
	
	<a href="frequencia_aula.php?aula=<?php echo $row_AulasMinistradas['plano_aula_hash']; ?>&turma=<?php echo $colname_Turma; ?>&disciplina=<?php echo $colname_Disciplina; ?>" class="waves-effect waves-light btn-small pink"><i class="material-icons right">rate_review</i><span class="hide-on-small-only">FREQUÊNCIA</span></a>
	<a href="forum.php?hash=<?php echo $row_AulasMinistradas['plano_aula_hash']; ?>&turma=<?php echo $colname_Turma; ?>&disciplina=<?php echo $colname_Disciplina; ?>" class="waves-effect waves-light btn-small green darken-4"><i class="material-icons right">search</i><span class="hide-on-small-only">VISUALIZAR</span></a>
	<a href="plano_aula_editar.php?hash=<?php echo $row_AulasMinistradas['plano_aula_hash']; ?>&turma=<?php echo $colname_Turma; ?>&disciplina=<?php echo $colname_Disciplina; ?>" class="waves-effect waves-light btn-small"><i class="material-icons right">edit</i><span class="hide-on-small-only">EDITAR</span></a>
	
	</td>
	
	
	<td width="20">
	<a href="javascript:func()" onclick="confirmaExclusao('disciplina=<?php echo $colname_Disciplina; ?>&turma=<?php echo $colname_Turma; ?>&deletar=<?php echo $row_AulasMinistradas['plano_aula_hash']; ?>')"><i class="material-icons red-text tooltipped" data-position="bottom" data-tooltip="Deletar aula">delete_forever</i></a>	
	</td>

    
</tr>	

<?php } while ($row_AulasMinistradas = mysql_fetch_assoc($AulasMinistradas)); ?>
</tbody>
</table>

<hr>

<div class="center-align">
        <a class="waves-effect waves-light btn-small green <?php if ($pageNum_AulasMinistradas > 0) { ?><?php } else { ?>disabled<?php } ?>" href="<?php printf("%s?pageNum_AulasMinistradas=%d%s&disciplina=$colname_Disciplina&turma=$colname_Turma", $currentPage, 0, $queryString_AulasMinistradas); ?>"><i class="material-icons left">first_page</i></a>
        <a class="waves-effect waves-light btn-small green <?php if ($pageNum_AulasMinistradas > 0) { ?><?php } else { ?>disabled<?php } ?>" href="<?php printf("%s?pageNum_AulasMinistradas=%d%s&disciplina=$colname_Disciplina&turma=$colname_Turma", $currentPage, max(0, $pageNum_AulasMinistradas - 1), $queryString_AulasMinistradas); ?>"><i class="material-icons left">navigate_before</i></a>
        <a class="waves-effect waves-light btn-small green <?php if ($pageNum_AulasMinistradas < $totalPages_AulasMinistradas) { ?><?php } else { ?>disabled<?php } ?>" href="<?php printf("%s?pageNum_AulasMinistradas=%d%s&disciplina=$colname_Disciplina&turma=$colname_Turma", $currentPage, min($totalPages_AulasMinistradas, $pageNum_AulasMinistradas + 1), $queryString_AulasMinistradas); ?>"><i class="material-icons right">navigate_next</i></a>
        <a class="waves-effect waves-light btn-small green <?php if ($pageNum_AulasMinistradas < $totalPages_AulasMinistradas) { ?><?php } else { ?>disabled<?php } ?>" href="<?php printf("%s?pageNum_AulasMinistradas=%d%s&disciplina=$colname_Disciplina&turma=$colname_Turma", $currentPage, $totalPages_AulasMinistradas, $queryString_AulasMinistradas); ?>"><i class="material-icons right">last_page</i></a>
</div>


	
      <?php } else { ?>
	  

      
  <div class="card-panel">
    <span class="blue-text text-darken-2">Nenhum conteúdo de aula inserido até o momento.</span>
  </div>
      
      <?php } ?>
	  
	  <br>
	  
		 
	</div>
		

     
	  </div>
    </div>
  </div>
  
  
  <div id="modal1" class="modal">
    <div class="modal-content">
      <h4>AULA DE <?php echo $row_Disciplina['disciplina_nome']; ?></h4>
      <p>
	  
<blockquote>
<h6><?php echo $row_Escolas['escola_nome']; ?><br><small><?php echo $row_Disciplina['disciplina_nome']; ?> - <?php echo $row_Turma['turma_nome']; ?></small></h6>
</blockquote>



      <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
		<fieldset>
		
		
		<div class="row">
		
          <div class="input-field col s12 m6">
          <i class="material-icons prefix">date_range</i>
		  <input type="text" name="plano_aula_data" id="plano_aula_data" value="" size="32" class="datepicker validate" autocomplete="off" required>
          <label for="plano_aula_data">DATA DA AULA</label>
          </div>
		  
		  <div class="input-field col s12 m6">
		  <i class="material-icons prefix">format_list_numbered</i>
		  
    <select name="plano_aula_qtd" id="plano_aula_qtd">
      <option value="1" select>1</option>
      <option value="2">2</option>
      <option value="3">3</option>
      <option value="4">4</option>
      <option value="5">5</option>
    </select>
    <label>NÚMERO DE AULAS DA DISCIPLINA</label>
		  
	
          </div>
          
		  <div class="input-field col s12 m12">
		  <i class="material-icons prefix">event_note</i>
		  <input type="text" name="plano_aula_texto" id="plano_aula_texto" value="" class="validate" autocomplete="off" required>
          <label for="plano_aula_texto">ASSUNTO</label>
          </div>

        </div>
		
		
		
		
        <input type="submit" value="INSERIR" class="waves-effect waves-light btn" onclick="return checkSubmission();">
		
        <input type="hidden" name="plano_aula_id_turma" value="<?php echo $row_Turma['turma_id']; ?>">
        <input type="hidden" name="plano_aula_id_disciplina" value="<?php echo $row_Disciplina['disciplina_id']; ?>">
        <input type="hidden" name="plano_aula_id_professor" value="<?php echo $row_ProfLogado['func_id']; ?>">
        <input type="hidden" name="plano_aula_hash" value="">
        <input type="hidden" name="MM_insert" value="form1">
	  
	  </fieldset>
	  </form>
	  
	  </p>
    </div>
    <div class="modal-footer">
      <a href="#!" class="modal-close waves-effect waves-green btn-flat">FECHAR</a>
    </div>
  </div>
  
      <!--JavaScript at end of body for optimized loading-->
   	  <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
      <script type="text/javascript" src="../js/materialize.min.js"></script>
<?php include ("rodape.php"); ?>      <script type="text/javascript" src="../js/app.js"></script>
      	<script type="text/javascript">
$(document).ready(function(){
$(".dropdown-trigger").dropdown();
$('.collapsible').collapsible();
$('.sidenav').sidenav();
$('.modal').modal();
$('.tooltipped').tooltip();
$('select').formSelect();
//$('.datepicker').datepicker();

$('.datepicker').datepicker({
i18n: {
months: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
monthsShort: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
weekdays: ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sabádo'],
weekdaysShort: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'],
weekdaysAbbrev: ['D', 'S', 'T', 'Q', 'Q', 'S', 'S'],
today: 'Hoje',
clear: 'Limpar',
cancel: 'Sair',
done: 'Confirmar',
labelMonthNext: 'Próximo mês',
labelMonthPrev: 'Mês anterior',
labelMonthSelect: 'Selecione um mês',
labelYearSelect: 'Selecione um ano',
selectMonths: true,
selectYears: 15,
},
format: 'dd/mm/yyyy',
container: 'body',
//minDate: new Date(),
});


});


</script>


    	<script language="Javascript">
	function confirmaExclusao(codigo) {
     var resposta = confirm("Deseja realmente excluir essa aula?");
     	if (resposta == true) {
     	     window.location.href = "plano_aula.php?"+codigo;
    	 }
	}
	</script>
	
<script language="Javascript">
var submissionflag = false;
function checkSubmission()
{
   if (!submissionflag) {
       submissionflag= true;
       return true;
   } else {
       return false;
   }
}


</script>


<?php if (isset($_GET["editado"])) { ?>
  <script>
M.toast({html: '<i class=\"material-icons\">check_circle</i>&nbsp;<button class="btn-flat toast-action">DADOS SALVOS COM SUCESSO</button>'});
</script>
  <?php } ?>
<?php if (isset($_GET["aulaLancada"])) { ?>
  <script>
M.toast({html: '<i class=\"material-icons\">check_circle</i>&nbsp;<button class="btn-flat toast-action">AULA LANÇADA COM SUCESSO</button>'});
</script>
  <?php } ?>
<?php if (isset($_GET["deletado"])) { ?>
  <script>
M.toast({html: '<i class=\"material-icons\">check_circle</i>&nbsp;<button class="btn-flat toast-action">CONTEÚDO DE AULA EXCLUÍDO COM SUCESSO</button>'});
</script>
  <?php } ?>
    </body>
  </html>
  <?php
mysql_free_result($ProfLogado);

mysql_free_result($Disciplina);

mysql_free_result($Turma);

mysql_free_result($disciplinaMatriz);

mysql_free_result($AulasMinistradasTotal);

mysql_free_result($AulasMinistradas);

mysql_free_result($Escolas);
?>