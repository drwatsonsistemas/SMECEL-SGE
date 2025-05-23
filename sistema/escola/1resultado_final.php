<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include('fnc/inverteData.php'); ?>
<?php include "fnc/anti_injection.php"; ?>
<?php //include('fnc/notas.php'); ?>
<?php include('../funcoes/url_base.php'); ?>
<?php include "fnc/calculos.php"; ?>
				<?php //include "fnc/anoLetivo.php"; ?>
				<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}
//set_time_limit(0);
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
	
  $logoutGoTo = "../../index.php?saiu=true";
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
$MM_authorizedUsers = "1,2,99";
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

$MM_restrictGoTo = "../../index.php?err=true";
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

include "usuLogado.php";
include "fnc/anoLetivo.php";

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EscolaLogada = "
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, 
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, sec_id, sec_cidade, sec_uf, escola_tema 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Vinculos = "
SELECT 
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_ponto_id, vinculo_aluno_data, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, 
vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, vinculo_aluno_vacina_data_retorno, 
vinculo_aluno_conselho, vinculo_aluno_conselho_parecer, turma_id, turma_matriz_id 
FROM smc_vinculo_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
WHERE vinculo_aluno_boletim = '1' AND vinculo_aluno_id_escola = '$row_UsuLogado[usu_escola]' AND vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'
ORDER BY vinculo_aluno_id_turma ASC";
$Vinculos = mysql_query($query_Vinculos, $SmecelNovo) or die(mysql_error());
$row_Vinculos = mysql_fetch_assoc($Vinculos);
$totalRows_Vinculos = mysql_num_rows($Vinculos);

?>
				<!DOCTYPE html>
				<html class="<?php echo $row_EscolaLogada['escola_tema']; ?>" lang="pt-br">
                <head>
                <!-- Global site tag (gtag.js) - Google Analytics -->
                <script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
                <script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-117872281-1');
</script>
                <title>SMECEL - Sistema de Gestão Escolar</title>
                <meta charset="utf-8">
                <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
                <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
                <meta name="description" content="">
                <meta name="keywords" content="">
                <meta name="mobile-web-app-capable" content="yes">
                <meta name="apple-mobile-web-app-capable" content="yes">
                <link rel="stylesheet" type="text/css" href="css/locastyle.css">                <link rel="stylesheet" type="text/css" href="css/preloader.css">
            <script src="js/locastyle.js"></script>
                <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
                <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
                <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
                <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
                </head>
                <body>
                <?php include_once ("menu-top.php"); ?>
                <?php include_once ("menu-esc.php"); ?>
                <main class="ls-main ">
                  <div class="container-fluid">
                    <h1 class="ls-title-intro ls-ico-home">GRÁFICO DE RENDIMENTOS</h1>
                    <!-- CONTEÚDO -->
                    
              
                    
                    <?php
            $matriculado = 0;
            $transferido = 0;
            $desistente = 0;
            $falecido = 0;
            $outros = 0;

            $aprovados_turma = 0; 
            $reprovados_turma = 0; 
            $aprovados_escola = 0; 
            $reprovados_escola = 0; 
          ?>
          
                    <?php do { ?>
                      <?php 
		  
			mysql_select_db($database_SmecelNovo, $SmecelNovo);
			$query_Matriz = "SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash, matriz_dias_letivos, matriz_semanas_letivas, matriz_dias_semana, matriz_minutos_aula, matriz_aula_dia, matriz_criterio_avaliativo FROM smc_matriz WHERE matriz_id = '$row_Vinculos[turma_matriz_id]'";
			$Matriz = mysql_query($query_Matriz, $SmecelNovo) or die(mysql_error());
			$row_Matriz = mysql_fetch_assoc($Matriz);
			$totalRows_Matriz = mysql_num_rows($Matriz);
			
			
			mysql_select_db($database_SmecelNovo, $SmecelNovo);
			$query_CriteriosAvaliativos = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes FROM smc_criterios_avaliativos WHERE ca_id = '$row_Matriz[matriz_criterio_avaliativo]'";
			$CriteriosAvaliativos = mysql_query($query_CriteriosAvaliativos, $SmecelNovo) or die(mysql_error());
			$row_CriteriosAvaliativos = mysql_fetch_assoc($CriteriosAvaliativos);
			$totalRows_CriteriosAvaliativos = mysql_num_rows($CriteriosAvaliativos);
		  
		  ?>
                      <?php
				   
				    mysql_select_db($database_SmecelNovo, $SmecelNovo);
					$query_disciplinasMatrizCab = "
					SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_id_disciplina, disciplina_id, disciplina_nome 
					FROM smc_matriz_disciplinas
					INNER JOIN smc_disciplina ON disciplina_id = matriz_disciplina_id_disciplina 
					WHERE matriz_disciplina_id_matriz = '$row_Vinculos[turma_matriz_id]'";
					$disciplinasMatrizCab = mysql_query($query_disciplinasMatrizCab, $SmecelNovo) or die(mysql_error());
					$row_disciplinasMatrizCab = mysql_fetch_assoc($disciplinasMatrizCab);
					$totalRows_disciplinasMatrizCab = mysql_num_rows($disciplinasMatrizCab);
				   
				   ?>
                      <?php
				   
				    mysql_select_db($database_SmecelNovo, $SmecelNovo);
					$query_disciplinasMatriz = "
					SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_id_disciplina, disciplina_id, disciplina_nome 
					FROM smc_matriz_disciplinas
					INNER JOIN smc_disciplina ON disciplina_id = matriz_disciplina_id_disciplina 
					WHERE matriz_disciplina_id_matriz = '$row_Vinculos[turma_matriz_id]'";
					$disciplinasMatriz = mysql_query($query_disciplinasMatriz, $SmecelNovo) or die(mysql_error());
					$row_disciplinasMatriz = mysql_fetch_assoc($disciplinasMatriz);
					$totalRows_disciplinasMatriz = mysql_num_rows($disciplinasMatriz);
				   
				   ?>
                      <?php 
					$res = 0;
				  ?>
                     
                      <?php do { ?>
                        <div style="display:none;">
                        <?php $tmu = 0; ?>
                        <?php for ($p = 1; $p <= $row_CriteriosAvaliativos['ca_qtd_periodos']; $p++) { ?>
                        <?php $ru = 0; ?>
                        <?php for ($a = 1; $a <= $row_CriteriosAvaliativos['ca_qtd_av_periodos']; $a++) { ?>
                        <?php 
                        //mysql_select_db($database_SmecelNovo, $SmecelNovo);
                        $query_nota = "SELECT nota_id, nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_max, nota_min, nota_valor, nota_hash FROM smc_nota WHERE nota_id_matricula = '$row_Vinculos[vinculo_aluno_id]' AND nota_id_disciplina = '$row_disciplinasMatriz[disciplina_id]' AND nota_periodo = '$p' AND nota_num_avaliacao = '$a'";
                        $nota = mysql_query($query_nota, $SmecelNovo) or die(mysql_error());
                        $row_nota = mysql_fetch_assoc($nota);
                        $totalRows_nota = mysql_num_rows($nota);
                        exibeTraco($row_nota['nota_valor'],$row_CriteriosAvaliativos['ca_nota_min_av']);
						$ru = $ru + $row_nota['nota_valor'];
                        ?>
                        <?php } ?>
                        <?php $mu = mediaUnidade($ru,$row_CriteriosAvaliativos['ca_arredonda_media'],$row_CriteriosAvaliativos['ca_aproxima_media'],$row_CriteriosAvaliativos['ca_media_min_periodo'],$row_CriteriosAvaliativos['ca_calculo_media_periodo'],$row_CriteriosAvaliativos['ca_qtd_av_periodos']); ?>
                        <?php $tmu = $tmu + $mu; ?>
                        <?php } ?>
                        <?php $tp = totalPontos($tmu); ?>
                        <?php $mc = mediaCurso($tp,$row_CriteriosAvaliativos['ca_arredonda_media'],$row_CriteriosAvaliativos['ca_aproxima_media'],$row_CriteriosAvaliativos['ca_min_media_aprovacao_final'],$row_CriteriosAvaliativos['ca_qtd_periodos']); ?>
                        <?php 
                        //mysql_select_db($database_SmecelNovo, $SmecelNovo);
                        $query_notaAf = "SELECT nota_id, nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_max, nota_min, nota_valor, nota_hash FROM smc_nota WHERE nota_id_matricula = '$row_Vinculos[vinculo_aluno_id]' AND nota_id_disciplina = '$row_disciplinasMatriz[disciplina_id]' AND nota_periodo = '99' AND nota_num_avaliacao = '99'";
                        $notaAf = mysql_query($query_notaAf, $SmecelNovo) or die(mysql_error());
                        $row_notaAf = mysql_fetch_assoc($notaAf);
                        $totalRows_notaAf = mysql_num_rows($notaAf);
						$af = avaliacaoFinal($row_notaAf['nota_valor'],$row_CriteriosAvaliativos['ca_nota_min_recuperacao_final']);
                        ?>
                      </div>
                        <?php 
							
							$resultado = resultadoFinal($mc, $af, $row_CriteriosAvaliativos['ca_nota_min_recuperacao_final'], $row_CriteriosAvaliativos['ca_min_media_aprovacao_final']);
							//echo $resultado;
							
							if ($resultado <> "APR") {
								$res++;
              }


							
						 ?>
                        <?php } while ($row_disciplinasMatriz = mysql_fetch_assoc($disciplinasMatriz)); ?>
                      <?php 
              if ($res == 0) { 
               // echo "APROVADO(A)"; 
                $aprovados_turma++;
                } else { 
                  //echo "<span style='color:red;'>CONSERVADO(A)</span>"; 
                  if ($row_Vinculos['vinculo_aluno_situacao']==1) {
                    $reprovados_turma++;
                  }
                  
                  } 
                  ?>
                      <?php

switch($row_Vinculos['vinculo_aluno_situacao']) {
  case 1:
    $matriculado++;
    break;
  case 2:
    $transferido++;
    break;
  case 3:
    $desistente++;
    break;
  case 4:
    $falecido++;
    break;
  case 5:
    $outros++;
    break;
}


?>
                      
                      <?php } while ($row_Vinculos = mysql_fetch_assoc($Vinculos)); ?>
                    <p>APROVADOS: <?php echo $aprovados_turma; ?> | CONSERVADOS: <?php echo $reprovados_turma; ?></p>
                    <p>MATRICULADO: <?php echo $matriculado; ?> | 
                      TRANSFERIDO: <?php echo $transferido; ?> |
                      DESISTENTE: <?php echo $desistente; ?> | 
                      FALECIDO: <?php echo $falecido; ?> | 
                      OUTROS: <?php echo $outros; ?> | </p>
                    <p> 
                      <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script> 
                      <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['SITUAÇÃO', 'TOTAL'],
          ['APROVADOS (<?php echo $aprovados_turma; ?>)',     <?php echo $aprovados_turma; ?>],
          ['CONSERVADOS (<?php echo $reprovados_turma; ?>)',      <?php echo $reprovados_turma; ?>]
        ]);

        var options = {
          title: 'APROVADOS/CONSERVADOS'
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart_resultado'));

        chart.draw(data, options);
      }
    </script> 
                      <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['SITUAÇÃO', 'TOTAL'],
          ['MATRICULADOS (<?php echo $matriculado; ?>)', <?php echo $matriculado; ?>],
          ['TRANSFERIDOS (<?php echo $transferido; ?>)', <?php echo $transferido; ?>],
          ['DESISTENTES (<?php echo $desistente; ?>)', <?php echo $desistente; ?>],
          ['FALECIDOS (<?php echo $falecido; ?>)', <?php echo $falecido; ?>],
          ['OUTROS (<?php echo $outros; ?>)', <?php echo $outros; ?>]
        ]);

        var options = {
          title: 'GRÁFICO POR SITUAÇÃO'
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);
      }
    </script>
                    <div class="row">
                      <div class="col-md-12 col-sm-12">
                        <div id="piechart_resultado" style="width: 100%; height: 500px;"></div>
                      </div>
                      <div class="col-md-12 col-sm-12">
                        <div id="piechart" style="width: 100%; height: 500px;"></div>
                      </div>
                    </div>
                    </p>
                    
                    <!-- CONTEÚDO --> 
                  </div>
                </main>
                <aside class="ls-notification">
                  <nav class="ls-notification-list" id="ls-notification-curtain" style="left: 1716px;">
                    <h3 class="ls-title-2">Notificações</h3>
                    <ul>
                      <?php include "notificacoes.php"; ?>
                    </ul>
                  </nav>
                  <nav class="ls-notification-list" id="ls-help-curtain" style="left: 1756px;">
                    <h3 class="ls-title-2">Feedback</h3>
                    <ul>
                      <li><a href="https://webmail.smecel.com.br" target="_blank">&gt; Acesse o webmail de sua escola</a></li>
                    </ul>
                  </nav>
                  <nav class="ls-notification-list" id="ls-feedback-curtain" style="left: 1796px;">
                    <h3 class="ls-title-2">Ajuda</h3>
                    <ul>
                      <li class="ls-txt-center hidden-xs"> <a href="tutoriais_video.php" class="ls-btn-dark ls-btn-tour">Tutorial (Vídeos)</a> </li>
                      <li><a href="#">&gt; Guia</a></li>
                      <li><a href="#">&gt; Wiki</a></li>
                    </ul>
                  </nav>
                </aside>
                
                <!-- We recommended use jQuery 1.10 or up --> 
                <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
                <script src="js/locastyle.js"></script>
                </body>
                </html>
				<?php
mysql_free_result($Vinculos);

mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);
?>
				