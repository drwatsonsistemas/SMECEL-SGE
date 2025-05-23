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



$colname_Disciplinas = "-1";
if (isset($_GET['cod'])) {
  $colname_Disciplinas = anti_injection($_GET['cod']);
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Disciplinas = sprintf("
SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola,
disciplina_id, disciplina_nome, turma_id, turma_nome, turma_ano_letivo  
FROM smc_ch_lotacao_professor
INNER JOIN smc_disciplina ON disciplina_id = ch_lotacao_disciplina_id 
INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
WHERE ch_lotacao_professor_id = '$row_ProfLogado[func_id]' AND ch_lotacao_escola = %s AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'
GROUP BY ch_lotacao_disciplina_id
", GetSQLValueString($colname_Disciplinas, "int"));
$Disciplinas = mysql_query($query_Disciplinas, $SmecelNovo) or die(mysql_error());
$row_Disciplinas = mysql_fetch_assoc($Disciplinas);
$totalRows_Disciplinas = mysql_num_rows($Disciplinas);

if ($totalRows_Disciplinas == 0) {
	header("Location:index.php?erro");
}


$colname_Escola = "-1";
if (isset($_GET['cod'])) {
  $colname_Escola = anti_injection($_GET['cod']);
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Escola = sprintf("SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_ue FROM smc_escola WHERE escola_id = %s", GetSQLValueString($colname_Escola, "int"));
$Escola = mysql_query($query_Escola, $SmecelNovo) or die(mysql_error());
$row_Escola = mysql_fetch_assoc($Escola);
$totalRows_Escola = mysql_num_rows($Escola);

if($totalRows_Escola=="") {
	header("Location:index.php?erro");
}


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Avisos = "
SELECT aviso_prof_id, aviso_prof_id_escola, aviso_prof_data_cadastro, aviso_prof_texto, aviso_prof_exibir_ate 
FROM smc_aviso_prof 
WHERE aviso_prof_id_escola = '$colname_Escola' AND DATE_FORMAT(aviso_prof_exibir_ate,'%Y-%m-%d') >= CURDATE()";
$Avisos = mysql_query($query_Avisos, $SmecelNovo) or die(mysql_error());
$row_Avisos = mysql_fetch_assoc($Avisos);
$totalRows_Avisos = mysql_num_rows($Avisos);


$colname_Disciplina = "-1";
if (isset($_GET['disciplina'])) {
  $colname_Disciplina = anti_injection($_GET['disciplina']);
}

$colname_Turmas = "-1";
if (isset($_GET['cod'])) {
  $colname_Turmas = anti_injection($_GET['cod']);
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turmas = sprintf("
SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola,
turma_id, turma_nome, turma_turno, turma_ano_letivo, disciplina_id, disciplina_nome,
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno_nome  
FROM smc_ch_lotacao_professor 
INNER JOIN smc_disciplina ON disciplina_id = ch_lotacao_disciplina_id
INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
WHERE 
ch_lotacao_professor_id = '$row_ProfLogado[func_id]' AND ch_lotacao_escola = %s AND ch_lotacao_disciplina_id = %s AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'
GROUP BY ch_lotacao_turma_id
ORDER BY turma_turno, turma_nome
", GetSQLValueString($colname_Turmas, "int"), GetSQLValueString($colname_Disciplina, "int"));
$Turmas = mysql_query($query_Turmas, $SmecelNovo) or die(mysql_error());
$row_Turmas = mysql_fetch_assoc($Turmas);
$totalRows_Turmas = mysql_num_rows($Turmas);

if ($totalRows_Turmas == 0) {
	header("Location:index.php?erro");
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
			
			<a href="disciplinas.php?cod=<?php echo $row_Escola['escola_id']; ?>" class="waves-effect waves-light btn-small btn"><i class="material-icons left">arrow_back</i> Voltar</a>
					
					<hr>
			
			                <!-- Page heading -->
                <div class="row ui-app__row">
                    <div class="col s12 ui-app__header">
                        <!-- title -->
                        <h1 class="ui-app__header__title display-1"><?php echo $row_Escola['escola_nome']; ?><br><small>DISCIPLINA: <?php echo $row_Turmas['disciplina_nome']; ?></small></h1>
                        <!-- bookmark -->
                        <!-- sub heading -->
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
                                <div class="card-title headline">Selecione uma turma:</div>

                                <div class="card-body">
                                    
									
									
<ul class="collapsible">

<?php do { ?>
    <li>
      <div class="collapsible-header">
	  <strong><?php echo $row_Turmas['turma_nome']; ?> <?php echo $row_Turmas['turma_turno_nome']; ?></strong>
      </div>
      <div class="collapsible-body">
      
      
      
      
      <span>
      <a href="alunos.php?disciplina=<?php echo $row_Turmas['ch_lotacao_disciplina_id']; ?>&turma=<?php echo $row_Turmas['turma_id']; ?>" class="waves-effect waves-light red btn-small">NOTAS</a>
      <a href="plano_aula.php?disciplina=<?php echo $row_Turmas['ch_lotacao_disciplina_id']; ?>&turma=<?php echo $row_Turmas['turma_id']; ?>" class="waves-effect waves-light btn-small">AULAS</a>
      <a href="frequencia_por_aula.php?disciplina=<?php echo $row_Turmas['ch_lotacao_disciplina_id']; ?>&turma=<?php echo $row_Turmas['turma_id']; ?>" class="waves-effect waves-light green btn-small">FREQUÊNCIA</a>
      <a href="acervo.php?turma=<?php echo $row_Turmas['turma_id']; ?>" class="waves-effect waves-light purple btn-small">ACERVO</a>
      </span>
      
      </div>
    </li>         
         
         
         <?php } while ($row_Turmas = mysql_fetch_assoc($Turmas)); ?>




  </ul> 



  
									
									
									
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

  <?php if (isset($_GET["erro"])) { ?>
	<script>
		M.toast({html: '<i class=\"material-icons red-text\">block</i>&nbsp;<button class="btn-flat toast-action">ALGO DE ERRADO ACONTECEU POR AQUI</button>'});
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

mysql_free_result($Avisos);

mysql_free_result($Disciplinas);

mysql_free_result($Escola);

mysql_free_result($Turmas);
?>