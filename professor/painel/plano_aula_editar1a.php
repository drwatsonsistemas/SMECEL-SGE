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
$erroUp = "0";
if ((isset($_POST["MM_insert2"])) && ($_POST["MM_insert2"] == "form2")) {
	
	$id_atividade 	= $_POST['plano_aula_anexo_id_atividade'];
	$nome_atividade 	= $_POST['plano_aula_anexo_descricao'];
	$novo_nome 	= md5(time());
	
include('../../sistema/funcoes/class.upload.php');

$handle = new Upload($_FILES['plano_aula_anexo_arquivo']);

if ($handle->uploaded) 
{ 

$handle->file_new_name_body = $novo_nome;
$handle->mime_check = true;
$handle->allowed = array('application/pdf','application/msword', 'application/excel', 'application/vnd.ms-excel', 'application/msexcel', 'application/excel', 'application/powerpoint', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'image/*');
$handle->file_max_size = '10242880'; // 1KB
$handle->Process('../../anexos/'.$id_atividade.'/');

if ($handle->processed) 
{

$nome_do_arquivo = $handle->file_dst_name;

  $insertSQL = sprintf("INSERT INTO smc_plano_aula_anexo (plano_aula_anexo_id_atividade, plano_aula_anexo_arquivo, plano_aula_anexo_descricao) VALUES (%s, '$nome_do_arquivo', '$nome_atividade')",
                       GetSQLValueString($_POST['plano_aula_anexo_id_atividade'], "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());
  
  $insertGoTo = "plano_aula_editar.php?";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
    $insertGoTo .= "&editado";
  }
  header(sprintf("Location: %s", $insertGoTo));
} 
else 
{
	
	$erroUp = "1";
	$erroValor = $handle->error;

}
}
}


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE smc_plano_aula SET plano_aula_data=%s, plano_aula_texto=%s, plano_aula_conteudo=%s, plano_aula_atividade=%s, plano_aula_atividade_resposta_obrigatoria=%s, plano_aula_atividade_resposta_obrigatoria_data_limite=%s, plano_aula_video=%s, plano_aula_google_form=%s, plano_aula_google_form_tempo=%s, plano_aula_publicado=%s WHERE plano_aula_id=%s",
                       GetSQLValueString(inverteData($_POST['plano_aula_data']), "date"),
                       GetSQLValueString($_POST['plano_aula_texto'], "text"),
                       GetSQLValueString($_POST['plano_aula_conteudo'], "text"),
                       GetSQLValueString($_POST['plano_aula_atividade'], "text"),
                       GetSQLValueString(isset($_POST['plano_aula_atividade_resposta_obrigatoria']) ? "true" : "", "defined","'S'","'N'"),
                       GetSQLValueString(inverteData($_POST['plano_aula_atividade_resposta_obrigatoria_data_limite']), "date"),
                       GetSQLValueString($_POST['plano_aula_video'], "text"),
                       GetSQLValueString($_POST['plano_aula_google_form'], "text"),
                       GetSQLValueString($_POST['plano_aula_google_form_tempo'], "text"),
                       GetSQLValueString(isset($_POST['plano_aula_publicado']) ? "true" : "", "defined","'S'","'N'"),
                       GetSQLValueString($_POST['plano_aula_id'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

  $updateGoTo = "plano_aula.php?editado";
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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Vinculos = "SELECT vinculo_id, vinculo_id_escola, vinculo_id_sec, vinculo_id_funcionario FROM smc_vinculo WHERE vinculo_id_funcionario = '$row_ProfLogado[func_id]'";
$Vinculos = mysql_query($query_Vinculos, $SmecelNovo) or die(mysql_error());
$row_Vinculos = mysql_fetch_assoc($Vinculos);
$totalRows_Vinculos = mysql_num_rows($Vinculos);
include "fnc/anoLetivo.php";

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Escolas = "
SELECT 
ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola, 
escola_id, escola_nome, turma_id, turma_nome, turma_ano_letivo, disciplina_id, disciplina_nome 
FROM smc_ch_lotacao_professor
INNER JOIN smc_escola ON escola_id = ch_lotacao_escola
INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
INNER JOIN smc_disciplina ON disciplina_id = ch_lotacao_disciplina_id
WHERE ch_lotacao_professor_id = '$row_ProfLogado[func_id]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'
GROUP BY ch_lotacao_escola";
$Escolas = mysql_query($query_Escolas, $SmecelNovo) or die(mysql_error());
$row_Escolas = mysql_fetch_assoc($Escolas);
$totalRows_Escolas = mysql_num_rows($Escolas);

$colname_Plano = "-1";
if (isset($_GET['hash'])) {
  $colname_Plano = anti_injection($_GET['hash']);
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Plano = sprintf("
SELECT plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, plano_aula_data, plano_aula_texto, 
plano_aula_conteudo, plano_aula_atividade, plano_aula_atividade_resposta_obrigatoria, plano_aula_atividade_resposta_obrigatoria_data_limite, 
plano_aula_video, plano_aula_google_form, plano_aula_google_form_tempo, plano_aula_publicado, plano_aula_hash 
FROM smc_plano_aula 
WHERE plano_aula_hash = %s AND plano_aula_id_professor = '$row_ProfLogado[func_id]'", GetSQLValueString($colname_Plano, "text"));
$Plano = mysql_query($query_Plano, $SmecelNovo) or die(mysql_error());
$row_Plano = mysql_fetch_assoc($Plano);
$totalRows_Plano = mysql_num_rows($Plano);

if($totalRows_Plano=="") {
	header("Location:index.php?erro");
}

$colname_Disciplina = "-1";
if (isset($_GET['disciplina'])) {
$colname_Disciplina = anti_injection($_GET['disciplina']);
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Disciplina = sprintf("SELECT disciplina_id, disciplina_area_conhecimento_id, disciplina_codigo_inep, disciplina_ordem, disciplina_nome, disciplina_nome_abrev FROM smc_disciplina WHERE disciplina_id = %s", GetSQLValueString($colname_Disciplina, "int"));
$Disciplina = mysql_query($query_Disciplina, $SmecelNovo) or die(mysql_error());
$row_Disciplina = mysql_fetch_assoc($Disciplina);
$totalRows_Disciplina = mysql_num_rows($Disciplina);

$colname_Turma = "-1";
if (isset($_GET['turma'])) {
$colname_Turma = anti_injection($_GET['turma']);
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turma = sprintf("SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo FROM smc_turma WHERE turma_id = %s", GetSQLValueString($colname_Turma, "int"));
$Turma = mysql_query($query_Turma, $SmecelNovo) or die(mysql_error());
$row_Turma = mysql_fetch_assoc($Turma);
$totalRows_Turma = mysql_num_rows($Turma);

if ((isset($_GET['deletar'])) && ($_GET['deletar'] != "")) {
  $deleteSQL = sprintf("DELETE FROM smc_plano_aula WHERE plano_aula_hash=%s",
                       GetSQLValueString($_GET['deletar'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());

  $deleteGoTo = "plano_aula.php?deletado";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}



mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Anexos = "SELECT plano_aula_anexo_id, plano_aula_anexo_id_atividade, plano_aula_anexo_arquivo, plano_aula_anexo_descricao FROM smc_plano_aula_anexo WHERE plano_aula_anexo_id_atividade = '$row_Plano[plano_aula_id]'";
$Anexos = mysql_query($query_Anexos, $SmecelNovo) or die(mysql_error());
$row_Anexos = mysql_fetch_assoc($Anexos);
$totalRows_Anexos = mysql_num_rows($Anexos);

if ((isset($_GET['anexo'])) && ($_GET['anexo'] != "")) {
  $deleteSQL = sprintf("DELETE FROM smc_plano_aula_anexo WHERE plano_aula_anexo_id_atividade = '$row_Plano[plano_aula_id]' AND plano_aula_anexo_id=%s",
                       GetSQLValueString($_GET['anexo'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());

  $deleteGoTo = "plano_aula_editar.php?hash=$colname_Plano&anexoDeletado";
  if (isset($_SERVER['QUERY_STRING'])) {
    //$deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    //$deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
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
	
	
	<h5>Editar plano de aula. CÓDIGO: <strong><?php echo htmlentities($row_Plano['plano_aula_id'], ENT_COMPAT, ''); ?></strong></h5>
	  
	  <hr>

    <a href="plano_aula.php?turma=<?php echo $row_Plano['plano_aula_id_turma']; ?>&disciplina=<?php echo $row_Plano['plano_aula_id_disciplina']; ?>" class="waves-effect waves-light btn-small btn-flat"><i class="material-icons left">arrow_back</i> Voltar</a>


<blockquote>
<h6><?php echo $row_Escolas['escola_nome']; ?><br><small><?php echo $row_Disciplina['disciplina_nome']; ?> - <?php echo $row_Turma['turma_nome']; ?></small></h6>
</blockquote>
     

     <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="card-panel1">
       
		  <div class="row">
          <div class="input-field col s12 m3">
		  <i class="material-icons prefix">date_range</i>
          <input type="text" name="plano_aula_data" value="<?php echo htmlentities(inverteData($row_Plano['plano_aula_data']), ENT_COMPAT, ''); ?>" size="32"  class="datepicker" autocomplete="off" required>
       	  <label for="plano_aula_data">DATA DA AULA</label>
          </div>
          
	   <div class="input-field col s12 m9">
       <i class="material-icons prefix">event_note</i>   	
       <input type="text" name="plano_aula_texto" value="<?php echo $row_Plano['plano_aula_texto']; ?>" size="32" autocomplete="off" required>
       <label for="plano_aula_texto">CONTEÚDO MINISTRADO</label>
       </div>		  
       </div>
		
		
		
  <ul class="collapsible expandable">
    <li>
      <div class="collapsible-header"><i class="material-icons purple-text darken-4">import_contacts</i>CONTEÚDO PARA O ALUNO <?php if ($row_Plano['plano_aula_conteudo']<>"") { ?><span class="badge" data-badge-caption=""><i class="material-icons green-text darken-4">check_circle</i></span><?php } ?></div>
      <div class="collapsible-body"><span>
	  
	  	<blockquote>
		<strong>LEIA COM ATENÇÃO</strong>
		
		<p>Preencha este campo, mesmo que a aula seja apenas um vídeo ou um anexo. Você poderá colocar alguma instrução para o aluno, ou apenas uma mensagem de boas-vindas.</p>
		
		<p>Informe abaixo o conteúdo da aula para que o aluno acompanhe e estude. Neste espaço, o professor pode iniciar o assunto de uma maneira mais pessoal, com uma saudação ou uma frase de incentivo. Importante não deixar este espaço em branco, mesmo que a aula seja apenas uma atividade.</p>
		
		<p>Para visualizar este editor em Tela Cheia, vá em: 'Visualizar > Tela Cheia' ou com o atalho Ctrl+Shift+F</p>
		
		</blockquote>
	  
	   <div class="row">
	   <div class="input-field col s12">
	   <textarea name="plano_aula_conteudo" rows="10" id="mytextarea" class="materialize-textarea"><?php echo htmlentities($row_Plano['plano_aula_conteudo'], ENT_COMPAT, ''); ?></textarea>
	   </div>
	   </div>
	  
	  </span></div>
    </li>

    <li>
      <div class="collapsible-header"><i class="material-icons red-text darken-4">place</i>VÍDEO DO CELULAR <?php if ($row_Plano['plano_aula_video']<>"") { ?><span class="badge" data-badge-caption=""><i class="material-icons green-text darken-4">check_circle</i></span><?php } ?></div>
      <div class="collapsible-body"><span>
	  
	
	
	<form id="upload_form" enctype="multipart/form-data" method="post" class="col s12">

	<div class="file-field input-field">
      <div class="btn">
        <span>Escolha o vídeo</span>
        <input type="file" name="video" id="video">
      </div>
      <div class="file-path-wrapper">
        <input class="file-path validate" type="text">
      </div>
    </div>
	
	

  <input type="hidden" name="aula" id="aula" value="<?php echo $row_Plano['plano_aula_id']; ?>">
  <input type="hidden" name="professor" value="<?php echo $row_ProfLogado['func_id']; ?>">
  <input type="hidden" name="componente" value="<?php echo $row_Disciplina['disciplina_id']; ?>">
  <input type="hidden" name="turma" value="<?php echo $row_Turma['turma_id']; ?>">
  
  <input type="button" value="ENVIAR" onclick="uploadFile()" class="btn">
  
  <progress id="progressBar" value="0" max="100" style="width:100%;"></progress>
  
  
  <h5 id="status"></h5>
  
  <small id="loaded_n_total"></small>
  
</form>
		
	  
	  
	  </span></div>
    </li>	
	
    <li>
      <div class="collapsible-header"><i class="material-icons red-text darken-4">place</i>VÍDEO DO YOUTUBE <?php if ($row_Plano['plano_aula_video']<>"") { ?><span class="badge" data-badge-caption=""><i class="material-icons green-text darken-4">check_circle</i></span><?php } ?></div>
      <div class="collapsible-body"><span>
	  
	  <p>Cole abaixo o link de um vídeo no youtube. É necessário que o link seja exatamente o que está na barra de endereços, por exemplo: <strong>https://www.youtube.com/watch?v=PUs7pyp6nYw</strong></p>
	  
       <div class="row">		
	   <div class="input-field col s12">
	   <i class="material-icons prefix">ondemand_video</i>
       <input type="text" id="plano_aula_video" name="plano_aula_video" value="<?php echo htmlentities($row_Plano['plano_aula_video'], ENT_COMPAT, ''); ?>" size="32" autocomplete="off">
       <label for="plano_aula_video">COLE AQUI O LINK PARA VIDEO DO YOUTUBE</label>
       </div>
	   </div>	  
	  
	  </span></div>
    </li>
    <li>
      <div class="collapsible-header"><i class="material-icons orange-text darken-4">description</i>ATIVIDADE PROPOSTA <?php if ($row_Plano['plano_aula_atividade']<>"") { ?><span class="badge" data-badge-caption=""><i class="material-icons green-text darken-4">check_circle</i></span><?php } ?></div>
      <div class="collapsible-body"><span>
	  
	  <p>O espaço abaixo é para disponibilizar uma atividade para os alunos. Você poderá copiar a atividade, ou dar instruções para o aluno copiar do livro. 
	  
	  Fica a seu critério a forma como o aluno vai responder: copiando a atividade no caderno ou responder diretamente no livro, depois, enviar uma foto através do campo de envio de atividades.</p>
	  
	  		<p>Para visualizar este editor em Tela Cheia, vá em: 'Visualizar > Tela Cheia' ou com o atalho Ctrl+Shift+F</p>

	  
	   <div class="row">
	   <div class="input-field col s12">
	   <textarea name="plano_aula_atividade" rows="10" id="mytextareaAtividade" class="materialize-textarea"><?php echo htmlentities($row_Plano['plano_aula_atividade'], ENT_COMPAT, ''); ?></textarea>
	   </div>
	   </div>
	   
	
	   	<p>
		
		Para solicitar a resposta para a atividade, escolha <strong>SIM</strong> no campo abaixo, e escolha uma data limite no campo abaixo à direita. 
		
		</p>

	
	<div class="row">
	
	   <div class="input-field col s12 m6">
	   <h5>Exigir resposta? <i class="material-icons tooltipped" data-position="bottom" data-tooltip="Marcando SIM o aluno deverá enviar uma resposta para a atividade. Informe a data limite no campo à seguir.">help</i></h5>

		    <!-- Switch -->
  <div class="switch">
    <label>
      Não
      <input type="checkbox" name="plano_aula_atividade_resposta_obrigatoria" value=""  <?php if (!(strcmp(htmlentities($row_Plano['plano_aula_atividade_resposta_obrigatoria'], ENT_COMPAT, 'utf-8'),"S"))) {echo "checked=\"checked\"";} ?>>
      <span class="lever"></span>
      Sim
    </label>
  </div>


	   </div>

	   	<div class="input-field col s12 m6">		
		<i class="material-icons prefix">date_range</i>
        <input type="text" name="plano_aula_atividade_resposta_obrigatoria_data_limite" value="<?php echo htmlentities(inverteData($row_Plano['plano_aula_atividade_resposta_obrigatoria_data_limite']), ENT_COMPAT, ''); ?>" size="" class="datepicker1" autocomplete="off">
		</div>
	   
	   
	   </div>
	   

	   
	  
	  </span></div>
    </li>
    <li>
      <div class="collapsible-header"><i class="material-icons green-text darken-4">attach_file</i>ANEXOS <?php if ($totalRows_Anexos > 0) { ?><span class="badge" data-badge-caption=""><i class="material-icons green-text darken-4" title="<?php echo $totalRows_Anexos; ?> anexo(s)">check_circle</i></span><?php } ?></span></div>
      <div class="collapsible-body"><span>
	  
	  <p>
	  
	  Você pode disponibilizar arquivos em PDF, documentos do Word, planilhas do Excel, imagens e outros arquivos como material de apoio aos alunos.
	  
	  </p>
	  
   <a class="waves-effect waves-light btn modal-trigger" href="#modal1">INSERIR ANEXO</a>
   <br>
   
<?php if ($totalRows_Anexos > 0) { ?>
<p>
<table>
<?php do { ?>
<tr>
  <td><strong><?php echo $row_Anexos['plano_aula_anexo_descricao']; ?></strong></td> <td width="150px"><a href="<?php echo URL_BASE.'anexos/'. $row_Plano['plano_aula_id'] ?>/<?php echo $row_Anexos['plano_aula_anexo_arquivo']; ?>" target="_blank"><i class="tiny material-icons">pageview</i> visualizar/baixar</a> </td><td width="150px"> <a  class="red-text" href="plano_aula_editar.php?hash=<?php echo $colname_Plano; ?>&anexo=<?php echo $row_Anexos['plano_aula_anexo_id']; ?>"><i class="tiny material-icons">delete_forever</i> Deletar</a></td>
</tr>  
<?php } while ($row_Anexos = mysql_fetch_assoc($Anexos)); ?>
</table>
</p>
<?php } ?>      
      
      </span></div>
    </li>
	
	
	 <li>
      <div class="collapsible-header"><i class="material-icons brown-text darken-4">class</i>AVALIAÇÃO ONLINE <?php if ($row_Plano['plano_aula_google_form']<>"") { ?><span class="badge" data-badge-caption=""><i class="material-icons green-text darken-4">check_circle</i></span><?php } ?></div>
      <div class="collapsible-body"><span>
	  
	  	<p>
		<strong>AVALIAÇÃO  ONLINE</strong>
		</p>
		<p>
		Para inserir o fomulário do Google Form no painel, cole no campo abaixo o código HTML disponibilizado pela plataforma do Google. 
		Obs.: O código de inforporação começa no seguinte formato: <b><code>&lt;iframe src="http...&gt;</code></b>.
		</p>
	  
	   <div class="row">
	   <div class="input-field col s12">
	   <textarea name="plano_aula_google_form" rows="10" id="mytextarea1" class="materialize-textarea"><?php echo $row_Plano['plano_aula_google_form']; ?></textarea>
	   </div>
	   
	   
	   	   <div class="input-field col s12">
		  
		  
    <select name="plano_aula_google_form_tempo" id="plano_aula_google_form_tempo">
	

      <option value="10" <?php if (!(strcmp(10, htmlentities($row_Plano['plano_aula_google_form_tempo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>10 minutos</option>
      <option value="20" <?php if (!(strcmp(20, htmlentities($row_Plano['plano_aula_google_form_tempo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>20 minutos</option>
      <option value="30" <?php if (!(strcmp(30, htmlentities($row_Plano['plano_aula_google_form_tempo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>30 minutos</option>
      <option value="40" <?php if (!(strcmp(40, htmlentities($row_Plano['plano_aula_google_form_tempo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>40 minutos</option>
      <option value="50" <?php if (!(strcmp(50, htmlentities($row_Plano['plano_aula_google_form_tempo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>50 minutos</option>
      <option value="60" <?php if (!(strcmp(60, htmlentities($row_Plano['plano_aula_google_form_tempo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>1 hora</option>
      <option value="90" <?php if (!(strcmp(90, htmlentities($row_Plano['plano_aula_google_form_tempo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>1 hora e 30 minutos</option>
      <option value="120" <?php if (!(strcmp(120, htmlentities($row_Plano['plano_aula_google_form_tempo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>2 horas</option>
      <option value="150" <?php if (!(strcmp(150, htmlentities($row_Plano['plano_aula_google_form_tempo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>2 horas e 30 minutos</option>


    </select>
    <label>Tempo para responder a prova depois de aberta (em minutos)</label>
		  
	
          </div>
	   
	   </div>
	   

	   
	   
	  
	  </span></div>
    </li>
	
	
  </ul>
		
		
	   

  
  	   <div class="row">
	   <div class="input-field col s12">
	   <h5>Publicar imediatamente? <i class="material-icons tooltipped" data-position="bottom" data-tooltip="Marcando em 'SIM' o conteúdo será publicado imediatamente. Caso queira apenas salvar a aula, mas não exibir o conteúdo para os alunos neste momento, marque a opção 'NÃO'.">help</i></h5>

		    <!-- Switch -->
  <div class="switch">
    <label>
      Não
      <input type="checkbox" name="plano_aula_publicado" value=""  <?php if (!(strcmp(htmlentities($row_Plano['plano_aula_publicado'], ENT_COMPAT, 'utf-8'),"S"))) {echo "checked=\"checked\"";} ?>>
      <span class="lever"></span>
      Sim
    </label>
  </div>


	   </div>
	   </div>
  
  
		  
		
	   <div class="row">		
       <div class="input-field col s12"> 
       <input type="submit" value="SALVAR" class="waves-effect waves-light btn">
       </div>
	   </div>
	   
       <input type="hidden" name="MM_update" value="form1">
       <input type="hidden" name="plano_aula_id" value="<?php echo $row_Plano['plano_aula_id']; ?>">
     </form>
	 
	  
		 
	</div>
		

     
	  </div>
    </div>
  </div>
  
  
  <div id="modal1" class="modal bottom-sheet">
    <div class="modal-content">
	  
	   <form method="post" enctype="multipart/form-data" name="form2" action="<?php  ?>" autocomplete="off">

<div class="row">

<div class="file-field input-field col s12 m4">
      <div class="btn">
        <span>ARQUIVO</span>
        <input type="file" name="plano_aula_anexo_arquivo" class="panel" value="" required>
      </div>
      <div class="file-path-wrapper">
        <input class="file-path validate" type="text">
      </div>
    </div>

        <div class="input-field col s12 m4">
          <input id="descricao" type="text" name="plano_aula_anexo_descricao" class="validate" required>
          <label for="descricao">Informe um nome que descreva este arquivo</label>
        </div>
	


	   <div class="input-field col s12 m4">
<input type="submit" value="ENVIAR" class="waves-effect waves-light btn">
	   </div>
   
   </div>

<input type="hidden" name="MM_insert2" value="form2">
<input type="hidden" name="plano_aula_anexo_id_atividade" value="<?php echo $row_Plano['plano_aula_id']; ?>">
</form>


	   <div class="input-field col s12">
	   
	   <div class="card-panel">
    <span class="blue-text text-darken-2">
	São aceitos arquivos do Word, Excel, Power Point ou imagens.
	O tamanho do arquivo não pode ser maior do que 10 MB.
	
	</span>
  </div>
	   
	   
	   	   </div>

	  
    </div>
    <div class="modal-footer">
      <a href="#!" class="modal-close waves-effect waves-green btn-flat">VOLTAR</a>
    </div>
  </div>
  
  
      <!--JavaScript at end of body for optimized loading-->
   	  <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
      <script type="text/javascript" src="../js/materialize.min.js"></script>
<?php include ("rodape.php"); ?>      <script type="text/javascript" src="../js/app.js"></script>
      	<script type="text/javascript">
		$(document).ready(function(){
			$('select').formSelect();
			$(".dropdown-trigger").dropdown();
			$('.sidenav').sidenav();
			 $('.modal').modal();
			$('.collapsible').collapsible({
					accordion: false
			});
				$('.tooltipped').tooltip();

			
			$('.datepicker, .datepicker1').datepicker({
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
	
	    <script src="//cdn.tinymce.com/4/tinymce.min.js1"></script>
		
		<script src="https://cdn.tiny.cloud/1/iq1gy8qt7s1b9bj92c2fc7whie95augu8r8kz97dgoufkli8/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="langs/pt_BR.js"></script>

	<script>
	
	$('.tooltipped').tooltip();

	tinymce.init({
	  selector: '#mytextarea, #mytextareaAtividade',
	  
	  mobile: {
    menubar: true
  },
	  
	  images_upload_url: 'postAcceptor.php',
	  automatic_uploads: true,
	  imagetools_proxy: 'proxy.php',
	  
	  plugins: 'emoticons',
	  toolbar: 'emoticons',
	  
	  imagetools_toolbar: 'rotateleft rotateright | flipv fliph | editimage imageoptions',
	  	  
	  height: 500,
	  toolbar: ['paste undo redo | formatselect | forecolor | bold italic backcolor | bullist numlist | image | emoticons | alignleft aligncenter alignright alignjustify | link h2 h3 blockquote'],
	  plugins : ['textcolor','advlist autolink link image imagetools lists charmap print preview paste emoticons',
		'advlist autolink lists link image imagetools charmap print preview anchor',
		'searchreplace visualblocks code fullscreen',
		'insertdatetime media table paste code help wordcount'],
	  //force_br_newlines : false,
	  //force_p_newlines : false,
	  //forced_root_block : '',	
	  statusbar: false,
	  language: 'pt_BR',
	  menubar: true,
	  paste_as_text: true,
	  content_css: '//www.tinymce.com/css/codepen.min.css'
	});

</script>
    
    	<script language="Javascript">
	function confirmaExclusao(codigo) {
     var resposta = confirm("Deseja realmente excluir este conteúdo?");
     	if (resposta == true) {
     	     window.location.href = "plano_aula_editar.php?"+codigo;
    	 }
	}
	</script>
	
<?php if (isset($_GET["anexoDeletado"])) { ?>
  <script>
M.toast({html: '<i class=\"material-icons\">check_circle</i>&nbsp;<button class="btn-flat toast-action">ANEXO DELETADO COM SUCESSO</button>'});
</script>
<?php } ?>	
<?php if (isset($_GET["editado"])) { ?>
  <script>
M.toast({html: '<i class=\"material-icons\">check_circle</i>&nbsp;<button class="btn-flat toast-action">ANEXO ENVIADO COM SUCESSO</button>'});
</script>
<?php } ?>

<?php if ($erroUp == "1") { ?>
  <script>
M.toast({html: '<i class=\"material-icons red-text\">block</i>&nbsp;<button class="btn-flat toast-action"><?php echo $erroValor; ?></button>'});
</script>
<?php $erroUp = "0" ?>
<?php } ?>

    </body>
  </html>
  <?php
mysql_free_result($ProfLogado);

mysql_free_result($Plano);

mysql_free_result($Anexos);

mysql_free_result($Escolas);

mysql_free_result($Disciplina);

mysql_free_result($Turma);
?>