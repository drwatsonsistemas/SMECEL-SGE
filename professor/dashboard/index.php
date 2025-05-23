<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "../../sistema/funcoes/anoLetivo.php"; ?>
<?php include('../../sistema/funcoes/url_base.php'); ?>
<?php include('../../sistema/funcoes/inverteData.php'); ?>


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
$query_Escolas = "
SELECT 
ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola, 
escola_id, escola_nome, turma_id, turma_nome, turma_ano_letivo 
FROM smc_ch_lotacao_professor
INNER JOIN smc_escola ON escola_id = ch_lotacao_escola
INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
WHERE ch_lotacao_professor_id = '$row_ProfLogado[func_id]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'
GROUP BY ch_lotacao_escola";
$Escolas = mysql_query($query_Escolas, $SmecelNovo) or die(mysql_error());
$row_Escolas = mysql_fetch_assoc($Escolas);
$totalRows_Escolas = mysql_num_rows($Escolas);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Atividades = "
SELECT plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, plano_aula_data, 
plano_aula_texto, plano_aula_conteudo, plano_aula_video, plano_aula_hash 
FROM smc_plano_aula 
WHERE plano_aula_id_professor = '$row_ProfLogado[func_id]'
ORDER BY plano_aula_data DESC";
$Atividades = mysql_query($query_Atividades, $SmecelNovo) or die(mysql_error());
$row_Atividades = mysql_fetch_assoc($Atividades);
$totalRows_Atividades = mysql_num_rows($Atividades);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_UltimasAulas = "
SELECT plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, plano_aula_data, 
plano_aula_texto, plano_aula_conteudo, plano_aula_video, plano_aula_hash, disciplina_id, disciplina_nome, turma_id, turma_nome 
FROM smc_plano_aula
INNER JOIN smc_disciplina ON disciplina_id = plano_aula_id_disciplina
INNER JOIN smc_turma ON turma_id = plano_aula_id_turma 
WHERE plano_aula_id_professor = '$row_ProfLogado[func_id]'
ORDER BY plano_aula_data DESC LIMIT 0, 5";
$UltimasAulas = mysql_query($query_UltimasAulas, $SmecelNovo) or die(mysql_error());
$row_UltimasAulas = mysql_fetch_assoc($UltimasAulas);
$totalRows_UltimasAulas = mysql_num_rows($UltimasAulas);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Comentarios = "
SELECT com_at_aluno_id, com_at_aluno_id_atividade, com_at_aluno_id_matricula, com_at_aluno_data_hora, com_at_aluno_comentario, com_at_aluno_comentario_professor,
plano_aula_id, plano_aula_texto, plano_aula_id_professor, plano_aula_id_disciplina, plano_aula_hash
FROM smc_coment_ativ_aluno
INNER JOIN smc_plano_aula ON plano_aula_id = com_at_aluno_id_atividade
WHERE plano_aula_id_professor = '$row_ProfLogado[func_id]' AND com_at_aluno_comentario_professor IS NULL
ORDER BY com_at_aluno_id DESC";
$Comentarios = mysql_query($query_Comentarios, $SmecelNovo) or die(mysql_error());
$row_Comentarios = mysql_fetch_assoc($Comentarios);
$totalRows_Comentarios = mysql_num_rows($Comentarios);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ComentariosFull = "
SELECT com_at_aluno_id, com_at_aluno_id_atividade, com_at_aluno_id_matricula, com_at_aluno_data_hora, com_at_aluno_comentario, com_at_aluno_comentario_professor,
plano_aula_id, plano_aula_texto, plano_aula_id_professor, plano_aula_id_disciplina, plano_aula_hash
FROM smc_coment_ativ_aluno
INNER JOIN smc_plano_aula ON plano_aula_id = com_at_aluno_id_atividade
WHERE plano_aula_id_professor = '$row_ProfLogado[func_id]'
ORDER BY com_at_aluno_id DESC";
$ComentariosFull = mysql_query($query_ComentariosFull, $SmecelNovo) or die(mysql_error());
$row_ComentariosFull = mysql_fetch_assoc($ComentariosFull);
$totalRows_ComentariosFull = mysql_num_rows($ComentariosFull);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Aulas = "
SELECT plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, 
plano_aula_data, plano_aula_data_cadastro, plano_aula_texto, plano_aula_conteudo, plano_aula_atividade, 
plano_aula_atividade_resposta_obrigatoria, plano_aula_atividade_resposta_obrigatoria_data_limite, 
plano_aula_video, plano_aula_publicado, plano_aula_hash, disciplina_id, disciplina_nome 
FROM smc_plano_aula
INNER JOIN smc_disciplina ON disciplina_id = plano_aula_id_disciplina 
WHERE plano_aula_id_professor = '$row_ProfLogado[func_id]'";
$Aulas = mysql_query($query_Aulas, $SmecelNovo) or die(mysql_error());
$row_Aulas = mysql_fetch_assoc($Aulas);
$totalRows_Aulas = mysql_num_rows($Aulas);
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
	
	<link href='../../sistema/calendar/core/main.css' rel='stylesheet' />
<link href='../../sistema/calendar/daygrid/main.css' rel='stylesheet' />
<script src='../../sistema/calendar/core/main.js'></script>
<script src='../../sistema/calendar/interaction/main.js'></script>
<script src='../../sistema/calendar/daygrid/main.js'></script>
<script src='../../sistema/calendar/core/main.js'></script>
<script src='../../sistema/calendar/core/locales/pt-br.js'></script>


<script>

  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
      plugins: [ 'interaction', 'dayGrid', 'timeGrid', 'list' ],
      header: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,dayGridWeek,dayGridDay,listWeek'
      },
	  defaultView: 'dayGridMonth',
	  locale: 'pt-BR',
      defaultDate: '<?php echo date('Y-m-d'); ?>',
      navLinks: true, // can click day/week names to navigate views
      editable: false,
      eventLimit: true, // allow "more" link when too many events
      events: [
	  
	  
	  <?php do { ?>
	  
	  
	  {
          title: '<?php echo $row_Aulas['disciplina_nome']; ?> - <?php echo $row_Aulas['plano_aula_texto']; ?>',
          start: '<?php echo $row_Aulas['plano_aula_data']; ?>',
		  url: 'forum.php?hash=<?php echo $row_Aulas['plano_aula_hash']; ?>',
		  

        },
	  
	  <?php } while ($row_Aulas = mysql_fetch_assoc($Aulas)); ?>
	  
	  
      ]
    });

    calendar.render();
  });

</script>
<style>

  #calendar {
    max-width: 900px;
    margin: 0 auto;
	font-size:12px;
  }

</style>
	
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
                        <h1 class="ui-app__header__title display-1">Página principal</h1>
                        <!-- bookmark -->
                        <!-- sub heading -->

                    </div>
                </div>
                <!-- End page heading -->

                <!-- Page content -->
                <div class="row">
				
				<!-- Analytics Header -->

                    <div class="col s12 m6 l3">
                        <div class="card ui-app__page-content white-text indigo lighten-2">
                            <div class="card-content ui-app__page-content__analytics">

                                <div class="card-body">
                                    <div class="ui-app__page-content__analytics--data">
                                        <div class="right"><i class="material-icons">featured_play_list</i>
                                        </div>
                                        <h3 class="headline"><?php echo $totalRows_Atividades; ?></h3>
                                        <div class="text-muted">Aulas cadastradas</div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col s12 m6 l3">
                        <div class="card ui-app__page-content white-text red lighten-2">
                            <div class="card-content ui-app__page-content__analytics">

                                <div class="card-body">
                                    <div class="ui-app__page-content__analytics--data">
                                        <div class="right"><i class="material-icons ">insert_comment</i>
                                        </div>
                                        <h3 class="headline"><?php echo $totalRows_ComentariosFull; ?></h3>
                                        <div class="text-muted">Comentários (<?php echo $totalRows_Comentarios; ?> sem respostas)</div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col s12 m6 l3">
                        <div class="card ui-app__page-content white-text amber lighten-1">
                            <div class="card-content ui-app__page-content__analytics ">

                                <div class="card-body">
                                    <div class="ui-app__page-content__analytics--data">
                                        <div class="right"><i class="material-icons ">notifications</i>
                                        </div>
                                        <h3 class="headline">0</h3>
                                        <div class="text-muted">Avisos</div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col s12 m6 l3">
                        <div class="card ui-app__page-content white-text green lighten-1">
                            <div class="card-content ui-app__page-content__analytics">

                                <div class="card-body">
                                    <div class="ui-app__page-content__analytics--data">
                                        <div class="right"><i class="material-icons ">local_library</i>
                                        </div>
                                        <h3 class="headline"><?php echo $totalRows_Escolas; ?></h3>
                                        <div class="text-muted">Escolas</div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- End Analytics Header -->
					
					
					<div class="col s12">
                        <div class="card ui-app__page-content">

                            <div class="card-content">
                                <div class="card-title headline">Minha(s) escola(s)</div>

                                <div class="card-body">
                                    <ul class="collection">
									
									 <?php if ($totalRows_Escolas <> 0) { ?>
									
									 <?php do { ?>
									 										
                                        <li class="collection-item avatar">
                                            <i class="material-icons circle">folder</i>
                                            <span class="title"><?php echo $row_Escolas['escola_nome']; ?></span>
                                            <p><a href="disciplinas.php?cod=<?php echo $row_Escolas['ch_lotacao_escola']; ?>">VER DISCIPLINAS</a></p>
                                            <a href="disciplinas.php?cod=<?php echo $row_Escolas['ch_lotacao_escola']; ?>" class="secondary-content"><i class="material-icons">grade</i></a>
                                        </li>
									 
									 <?php } while ($row_Escolas = mysql_fetch_assoc($Escolas)); ?>
									
									 <?php } else { ?>
									 <p>Nenhuma escola encontrada com turma ativa para este professor.</p>
									 <?php } ?>
																		
                                    </ul>
                                </div>

                            </div>
                        </div>
                    </div>
					


                    <!-- Calendário -->
                    <div class="col s12 m12 l12">

                        <div class="card ui-app__page-content">
                            <div class="card-content">
                                <!-- title -->
                                <div class="card-title headline">Calendário de aulas</div>

                                <div class="card-body">
                                    
									<div id='calendar'></div>
									
                                </div>

                            </div>
                        </div>

                    </div>
                    <!-- Calendário -->

                    

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

mysql_free_result($Aulas);

mysql_free_result($Atividades);

mysql_free_result($UltimasAulas);

mysql_free_result($Comentarios);

mysql_free_result($Escolas);
?>