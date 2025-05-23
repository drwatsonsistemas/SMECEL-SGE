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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
$editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {

$dataCad = date('Y-m-d H:i:s');
$qtd = $_POST['plano_aula_qtd']; 

for($i=0; $i < $qtd; $i++){

$hash = md5($totalRows_Turma['turma_id'].$i.date('YmdHis'));

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
			
    <a href="turmas.php?disciplina=<?php echo $colname_Disciplina; ?>&cod=<?php echo $row_Escolas['ch_lotacao_escola']; ?>" class="waves-effect waves-light btn-small btn"><i class="material-icons left">arrow_back</i> Voltar</a>
					
					<hr>
			
			                <!-- Page heading -->
                <div class="row ui-app__row">
                    <div class="col s12 ui-app__header">
                        <!-- title -->
                        <h1 class="ui-app__header__title display-1"><?php echo $row_Escolas['escola_nome']; ?><br><small><?php echo $row_Disciplina['disciplina_nome']; ?> - <?php echo $row_Turma['turma_nome']; ?></small></h1>
                        <!-- bookmark -->
                        <!-- sub heading -->
                    </div>
                </div>
                <!-- End page heading -->
                <!-- Page content -->
				<div class="row">
				
				<p>	
<a class="waves-effect waves-light btn modal-trigger" href="#modal1">INCLUIR NOVA AULA</a>
</p>
				
<!-- Basic -->
	<div class="col s12">
		<div class="1card ui-app__page-content">
			<div class="1card-content">
				<div class="card-title headline">DIÁRIO DE CLASSE</div>

				<div class="1card-body">

					<table id="aulas" class="display" style="width:100%">
						<thead>
							<tr>
								<th class="center">Data</th>
								<th class="center">Assunto</th>
								<th></th>
								<th class="center">Conteúdo</th>
							</tr>
						</thead>
						<tbody>
						<?php do { ?>
							<tr>
							<td class="center">
							
							<div class="<?php if ($row_AulasMinistradas['plano_aula_publicado']=="S") { ?>green darken-3 <?php } else { ?>red<?php } ?>" style="color:white; font-size: 12px; padding-bottom: 5px;">
							<?php 
							$mes = array('', 'JAN', 'FEV', 'MAR', 'ABR', 'MAI', 'JUN', 'JUL', 'AGO', 'SET', 'OUT', 'NOV', 'DEZ');
							?>
							<strong style="font-size: 20px;"><?php echo date('d', strtotime($row_AulasMinistradas['plano_aula_data'])); ?></strong><br>
							<?php $mee = (date('m', strtotime($row_AulasMinistradas['plano_aula_data'])))*1; 
							echo $mes[$mee];
							?>
							</div>
								</td>
								<td><strong><?php echo $row_AulasMinistradas['plano_aula_id']; ?></strong><br><?php echo $row_AulasMinistradas['plano_aula_texto']; ?></td>
								
								
							<td class="center">
								
								
								<!-- Dropdown Trigger -->
							<a class='dropdown-trigger waves-effect waves-light btn-flat' href='#' data-target='dropdown-default-dropdown-small-btn-<?php echo $row_AulasMinistradas['plano_aula_hash']; ?>'><i class="large material-icons">settings</i></a>

							<!-- Dropdown Structure -->
							<ul id='dropdown-default-dropdown-small-btn-<?php echo $row_AulasMinistradas['plano_aula_hash']; ?>' class='dropdown-content'>
								<li><a href="plano_aula_editar.php?hash=<?php echo $row_AulasMinistradas['plano_aula_hash']; ?>&turma=<?php echo $colname_Turma; ?>&disciplina=<?php echo $colname_Disciplina; ?>"><i class="material-icons tooltipped" data-position="bottom" data-tooltip="Editar aula">edit</i></a></li>
								<li><a href="frequencia_aula.php?aula=<?php echo $row_AulasMinistradas['plano_aula_hash']; ?>&turma=<?php echo $colname_Turma; ?>&disciplina=<?php echo $colname_Disciplina; ?>"><i class="material-icons pink-text tooltipped" data-position="bottom" data-tooltip="Visualizar frequência">rate_review</i></a></li>
								<li><a href="forum.php?hash=<?php echo $row_AulasMinistradas['plano_aula_hash']; ?>&turma=<?php echo $colname_Turma; ?>&disciplina=<?php echo $colname_Disciplina; ?>"><i class="material-icons green-text darken-4 tooltipped" data-position="bottom" data-tooltip="Visualizar aula">search</i></a></li>
								<li class="divider" tabindex="-1"></li>
								<li><a class="" href="javascript:func()" onclick="confirmaExclusao('disciplina=<?php echo $colname_Disciplina; ?>&turma=<?php echo $colname_Turma; ?>&deletar=<?php echo $row_AulasMinistradas['plano_aula_hash']; ?>')"><i class="material-icons red-text tooltipped" data-position="bottom" data-tooltip="Deletar aula">delete_forever</i></a></li>
							</ul>
								
								
								</td>
								
								
								
								
								<td class="center">

								<?php if ($row_AulasMinistradas['plano_aula_conteudo']<>"") { ?><i class="material-icons purple-text tooltipped" data-position="bottom" data-tooltip="Conteúdo para estudo">import_contacts</i><?php } else { ?><i class="material-icons grey-text text-lighten-3 ">import_contacts</i><?php } ?>
								<?php if ($row_AulasMinistradas['plano_aula_atividade']<>"") { ?><i class="material-icons orange-text tooltipped" data-position="bottom" data-tooltip="Atividades">description</i><?php } else { ?><i class="material-icons grey-text text-lighten-3 ">description</i><?php } ?>
								<?php if ($row_AulasMinistradas['plano_aula_video']<>"") { ?><i class="material-icons red-text tooltipped" data-position="bottom" data-tooltip="Vídeo de apoio">ondemand_video</i><?php } else { ?><i class="material-icons grey-text text-lighten-3 ">ondemand_video</i><?php } ?>
								<?php if ($row_AulasMinistradas['plano_aula_google_form']<>"") { ?><a href="presenca_avaliacao.php?aula=<?php echo $row_AulasMinistradas['plano_aula_hash']; ?>&turma=<?php echo $colname_Turma; ?>&disciplina=<?php echo $colname_Disciplina; ?>"><i class="material-icons brown-text tooltipped" data-position="bottom" data-tooltip="Avaliação / Frequência">class</i></a><?php } else { ?><i class="material-icons brown-text text-lighten-5">class</i><?php } ?>

								</td>


							</tr>
						<?php } while ($row_AulasMinistradas = mysql_fetch_assoc($AulasMinistradas)); ?>	
						</tbody>
					</table>

				</div>


			</div>
		</div>
	</div>
	<!-- End Basic -->
				


                    <!-- sales chart -->
                    <div class="col s12">
					<br>
					
<div class="center-align">
        <a class="waves-effect waves-light btn-small green <?php if ($pageNum_AulasMinistradas > 0) { ?><?php } else { ?>disabled<?php } ?>" href="<?php printf("%s?pageNum_AulasMinistradas=%d%s&disciplina=$colname_Disciplina&turma=$colname_Turma", $currentPage, 0, $queryString_AulasMinistradas); ?>"><i class="material-icons left">first_page</i></a>
        <a class="waves-effect waves-light btn-small green <?php if ($pageNum_AulasMinistradas > 0) { ?><?php } else { ?>disabled<?php } ?>" href="<?php printf("%s?pageNum_AulasMinistradas=%d%s&disciplina=$colname_Disciplina&turma=$colname_Turma", $currentPage, max(0, $pageNum_AulasMinistradas - 1), $queryString_AulasMinistradas); ?>"><i class="material-icons left">navigate_before</i></a>
        <a class="waves-effect waves-light btn-small green <?php if ($pageNum_AulasMinistradas < $totalPages_AulasMinistradas) { ?><?php } else { ?>disabled<?php } ?>" href="<?php printf("%s?pageNum_AulasMinistradas=%d%s&disciplina=$colname_Disciplina&turma=$colname_Turma", $currentPage, min($totalPages_AulasMinistradas, $pageNum_AulasMinistradas + 1), $queryString_AulasMinistradas); ?>"><i class="material-icons right">navigate_next</i></a>
        <a class="waves-effect waves-light btn-small green <?php if ($pageNum_AulasMinistradas < $totalPages_AulasMinistradas) { ?><?php } else { ?>disabled<?php } ?>" href="<?php printf("%s?pageNum_AulasMinistradas=%d%s&disciplina=$colname_Disciplina&turma=$colname_Turma", $currentPage, $totalPages_AulasMinistradas, $queryString_AulasMinistradas); ?>"><i class="material-icons right">last_page</i></a>
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

  <div id="modal1" class="modal">
    <div class="modal-content">
      <h4>INSERIR NOVA(S) AULA(S)</h4>
	  <?php echo $row_Disciplina['disciplina_nome']; ?> - <?php echo $row_Turma['turma_nome']; ?>
      <p>
	  

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
    <label>QUANTIDADE DE AULAS</label>
		  
	
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


    <script src="../jsn/jquery.min.js"></script>
    <script src="../jsn/materialize.min.js"></script>
    <script src="../jsn/prism.js"></script>
    <script src="../jsn/Chart.min.js"></script>
    <script src="../jsn/app.js"></script>
    <script src="../jsn/search.js"></script>
    <script src="../jsn/jquery.dataTables.min.js"></script>
	
	
    <script>
        $(function() {
            $('#aulas').DataTable( {
					"paging": false,
					"searching": false,
					"ordering":  false,
					"info":     false
			});
        });
    </script>
	
	

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
<!--End body-->
<!-- //////////////////////////////////////////////////////////////////////////// -->

</html>
<!--End HTML-->
<!-- //////////////////////////////////////////////////////////////////////////// -->
  <?php
mysql_free_result($ProfLogado);

mysql_free_result($Disciplina);

mysql_free_result($Turma);

mysql_free_result($AulasMinistradas);

mysql_free_result($Escolas);
?>