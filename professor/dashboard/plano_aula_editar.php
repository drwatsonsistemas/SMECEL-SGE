<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "../../sistema/funcoes/anoLetivo.php"; ?>
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
<html lang="pt_br">
<head>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-117872281-1');
</script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $row_ProfLogado['func_nome']?> - Painel do professor</title>
	<meta charset="UTF-8">
    <meta name="theme-color" content="#5c6bc0">
    <meta name="msapplication-navbutton-color" content="#5c6bc0">
    <meta name="apple-mobile-web-app-status-bar-style" content="#5c6bc0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="../cssn/materialize.min.css" />
    <link type="text/css" rel="stylesheet" href="../cssn/prism.css" />
    <link type="text/css" rel="stylesheet" href="../cssn/app.css" />
    <link type="text/css" rel="stylesheet" href="../cssn/helper.css" />
    <link type="text/css" rel="stylesheet" href="../cssn/responsive.css" />
    <link type="text/css" rel="stylesheet" href="../cssn/default.css" />
	<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
	<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<body>

    <!-- //////////////////////////////////////////////////////////////////////////// -->
    <!--Efnify body-->
    <div class="ui-app">

        <!-- //////////////////////////////////////////////////////////////////////////// -->
        <!--Efnify body page wrapper -->
        <div class="ui-app__wrapper" id="app-layout-control">

            <!-- ////////////////s//////////////////////////////////////////////////////////// -->
            <!--prepage loader-->
            <div id="prepage-loader">
                <div class="ui-app__prepage-loader spinner">
                    <div class="double-bounce1"></div>
                    <div class="double-bounce2"></div>
                </div>
            </div>
            <!-- End prepage loader-->
            <!-- //////////////////////////////////////////////////////////////////////////// -->

            <!-- /////////////////////////////////////////////////////////////////// -->
            <!--navbar/header-->
			<?php include "assets/nav-bar.php"; ?>
            <!--End navbar/header-->
            <!-- //////////////////////////////////////////////////////////////////// -->


            <!-- //////////////////////////////////////////////////////////////////////////// -->
            <!--Left sidenav/sidebar-->
			<?php include "assets/aside-left.php"; ?>
            <!--End Left sidenav/sidebar-->
            <!-- //////////////////////////////////////////////////////////////////////////// -->

            <!-- //////////////////////////////////////////////////////////////////////////// -->
            <!--Right sidenav/sidebar-->
			<?php //include "assets/options-right.php"; ?>
            <!-- Right sidenav toggle (show and hide right sidenav on click button) 
            <a href="#" data-target="ui-app__right-sidenav-slide-out" class="ui-app__right-sidenav-toggle sidenav-trigger btn-floating waves-effect waves-light" id="right-sidenav-toggle"><i class="material-icons ">settings</i></a>
			-->
            <!--End Right sidenav/sidebar-->
            <!-- //////////////////////////////////////////////////////////////////////////// -->

            <!-- //////////////////////////////////////////////////////////////////////////// -->
            <!--Page Body-->
            <main>
			
			                <!-- Page heading -->
                <div class="row ui-app__row">
                    <div class="col s12 ui-app__header">
                        <!-- title -->
                        <h1 class="ui-app__header__title display-1">Editar plano de aula. CÓDIGO: <strong><?php echo htmlentities($row_Plano['plano_aula_id'], ENT_COMPAT, ''); ?></strong></h1>
                        <!-- bookmark -->
                        <!-- sub heading -->
                        <p class="ui-app__header__body subheading">
						
						    <a href="plano_aula.php?turma=<?php echo $row_Plano['plano_aula_id_turma']; ?>&disciplina=<?php echo $row_Plano['plano_aula_id_disciplina']; ?>" class="waves-effect waves-light btn-small btn"><i class="material-icons left">arrow_back</i> Voltar</a>

						<blockquote>
<h6><?php echo $row_Escolas['escola_nome']; ?><br><small><?php echo $row_Disciplina['disciplina_nome']; ?> - <?php echo $row_Turma['turma_nome']; ?></small></h6>
</blockquote>

						</p>

                    </div>
                </div>
                <!-- End page heading -->
				
				
	  


                <!-- Page content -->
                <div class="row">

                    <!-- sales chart -->
                    <div class="col s12">

                        <div class="1card ui-app__page-content">
                            <div class="card-content">
                                <!-- title -->
                                <div class="card-title headline">Conteúdo</div>

                                <div class="card-body">
                                    
									
									
									
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
		
		<p>Preencha este campo, mesmo que a aula seja apenas um vídeo ou um anexo. Você poderá colocar alguma instrução para o aluno, ou apenas uma mensagem de boas-vindas à aula.</p>
		
		<p>Informe abaixo o conteúdo da aula para que o aluno acompanhe e estude. Neste espaço, o professor pode iniciar o assunto de uma maneira mais pessoal, com uma saudação ou uma frase de incentivo. Importante não deixar este espaço em branco, mesmo que a aula seja apenas uma atividade.</p>
		
		</blockquote>
	  
	   <div class="row">
	   <div class="input-field col s12">
	   <textarea name="plano_aula_conteudo" rows="10" id="mytextarea" class="materialize-textarea"><?php echo htmlentities($row_Plano['plano_aula_conteudo'], ENT_COMPAT, ''); ?></textarea>
	   </div>
	   </div>
	  
	  </span></div>
    </li>
    <li>
      <div class="collapsible-header"><i class="material-icons red-text darken-4">place</i>VÍDEO DO YOUTUBE <?php if ($row_Plano['plano_aula_video']<>"") { ?><span class="badge" data-badge-caption=""><i class="material-icons green-text darken-4">check_circle</i></span><?php } ?></div>
      <div class="collapsible-body"><span>
	  
	  <blockquote>
	  <p>Cole abaixo o link de um vídeo no youtube. É necessário que o link seja exatamente o que está na barra de endereços, por exemplo: <strong>https://www.youtube.com/watch?v=PUs7pyp6nYw</strong></p>
	  </blockquote>
	  
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
	  
	  <blockquote>
	  <p>O espaço abaixo é para disponibilizar uma atividade para os alunos. Você poderá copiar a atividade, ou dar instruções para o aluno copiar do livro. 
	  
	  Fica a seu critério a forma como o aluno vai responder: copiando a atividade no caderno ou responder diretamente no livro, depois, enviar uma foto através do campo de envio de atividades.</p>
	  </blockquote>
	  
	   <div class="row">
	   <div class="input-field col s12">
	   <textarea name="plano_aula_atividade" rows="10" id="mytextareaAtividade" class="materialize-textarea"><?php echo htmlentities($row_Plano['plano_aula_atividade'], ENT_COMPAT, ''); ?></textarea>
	   </div>
	   </div>
	   
	
	   	<blockquote>
		<p>
		
		Para solicitar a resposta para a atividade, escolha <strong>SIM</strong> no campo abaixo, e escolha uma data limite no campo abaixo à direita. 
		
		</p>
		</blockquote>

	
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
	  
	  <blockquote>
	  <p>
	  
	  Você pode disponibilizar arquivos em PDF, documentos do Word, planilhas do Excel, imagens e outros arquivos como material de apoio aos alunos.
	  
	  </p>
	  </blockquote>
	  
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
	  
	  	<blockquote>
		<p>
		<strong>AVALIAÇÃO  ONLINE</strong>
		</p>
		<p>
		Para inserir o fomulário do Google Form no painel, cole no campo abaixo o código HTML disponibilizado pela plataforma do Google. 
		Obs.: O código de inforporação começa no seguinte formato: <b><code>&lt;iframe src="http...&gt;</code></b>.
		</p>
		</blockquote>
	  
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
                    <!-- sales chart -->

                    


                </div>
                <!--End Page content -->

            </main>
            <!--End page body-->
            <!-- //////////////////////////////////////////////////////////////////////////// -->


            <!-- //////////////////////////////////////////////////////////////////////////// -->
            <!--Footer-->
			<?php include "assets/foot.php"; ?>
            <!--End footer-->
            <!-- //////////////////////////////////////////////////////////////////////////// -->

        </div>
        <!-- End Efnify body page wrapper -->
        <!-- //////////////////////////////////////////////////////////////////////////// -->
    </div>

    <!-- End Efnify body -->
    <!-- //////////////////////////////////////////////////////////////////////////// -->


    <!-- //////////////////////////////////////////////////////////////////////////// -->
    <!--  Scripts-->

    <script src="../jsn/jquery.min.js"></script>
    <script src="../jsn/materialize.min.js"></script>
    <script src="../jsn/prism.js"></script>
    <script src="../jsn/Chart.min.js"></script>
    <script src="../jsn/app.js"></script>
    <script src="../jsn/search.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			

			
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
	
	    <script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
    <script src="langs/pt_BR.js"></script>

	<script>
	
	$('.tooltipped').tooltip();

	tinymce.init({
	  selector: '#mytextarea, #mytextareaAtividade',
	  
	  images_upload_url: 'postAcceptor.php',
	  automatic_uploads: true,
	  imagetools_proxy: 'proxy.php',
	  
	  plugins: 'emoticons',
  toolbar: 'emoticons',
	  
	  imagetools_toolbar: 'rotateleft rotateright | flipv fliph | editimage imageoptions',
	  	  
	  height: 500,
	  toolbar: ['undo redo | formatselect | forecolor | bold italic backcolor | bullist numlist | image | emoticons | alignleft aligncenter alignright alignjustify | link h2 h3 blockquote'],
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
<!--End body-->
<!-- //////////////////////////////////////////////////////////////////////////// -->

</html>
<!--End HTML-->
<!-- //////////////////////////////////////////////////////////////////////////// -->
  <?php
mysql_free_result($ProfLogado);

mysql_free_result($Plano);

mysql_free_result($Anexos);

mysql_free_result($Escolas);

mysql_free_result($Disciplina);

mysql_free_result($Turma);
?>