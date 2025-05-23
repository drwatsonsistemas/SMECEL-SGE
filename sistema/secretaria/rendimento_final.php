<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include('../escola/fnc/inverteData.php'); ?>
<?php include "../escola/fnc/anti_injection.php"; ?>
<?php //include('fnc/notas.php'); ?>
<?php include('../funcoes/url_base.php'); ?>
<?php include "../escola/fnc/calculos.php"; ?>
<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}
// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF'] . "?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")) {
  $logoutAction .= "&" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) && ($_GET['doLogout'] == "true")) {
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);

  $logoutGoTo = "../../index.php?exit";
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
$MM_authorizedUsers = "1,99";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup)
{
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

$MM_restrictGoTo = "../../index.php?acessorestrito";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("", $MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?"))
    $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0)
    $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo . $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: " . $MM_restrictGoTo);
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

require_once('funcoes/usuLogado.php');
require_once('funcoes/anoLetivo.php');

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

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
WHERE vinculo_aluno_boletim = '1' AND vinculo_aluno_id_sec = '$row_UsuarioLogado[usu_sec]' AND vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'
ORDER BY vinculo_aluno_id_turma ASC";
$Vinculos = mysql_query($query_Vinculos, $SmecelNovo) or die(mysql_error());
$row_Vinculos = mysql_fetch_assoc($Vinculos);
$totalRows_Vinculos = mysql_num_rows($Vinculos);

//APROVADOS E REPROVADOS
$query_AlunosAprovadosConselho = "
SELECT 
COUNT(vinculo_aluno_id) AS alunos_aprovados_conselho, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_ponto_id, vinculo_aluno_data, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, 
vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, vinculo_aluno_vacina_data_retorno, 
vinculo_aluno_conselho, vinculo_aluno_conselho_parecer,vinculo_aluno_resultado_final,turma_id, turma_matriz_id, turma_resultado_consolidado 
FROM smc_vinculo_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
WHERE vinculo_aluno_boletim = '1' AND vinculo_aluno_id_sec = '$row_UsuarioLogado[usu_sec]' AND vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND
vinculo_aluno_resultado_final = '1' AND vinculo_aluno_conselho='S'
ORDER BY vinculo_aluno_id_turma ASC";
$AprovadosC = mysql_query($query_AlunosAprovadosConselho, $SmecelNovo) or die(mysql_error());
$row_AprovadosC = mysql_fetch_assoc($AprovadosC);
$totalRows_AprovadosC = mysql_num_rows($AprovadosC);


$query_AlunosMatriculados = "
SELECT 
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_ponto_id, vinculo_aluno_data, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim,vinculo_aluno_situacao, COUNT(vinculo_aluno_situacao) AS aluno_situacao, vinculo_aluno_datatransferencia, 
vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, vinculo_aluno_vacina_data_retorno, 
vinculo_aluno_conselho, vinculo_aluno_conselho_parecer,vinculo_aluno_resultado_final,turma_id, turma_matriz_id,
CASE vinculo_aluno_situacao
WHEN 1 THEN 'MATRICULADOS'
WHEN 2 THEN 'TRANSFERIDOS'
WHEN 3 THEN 'DESISTENTES'
WHEN 4 THEN 'FALECIDOS'
WHEN 5 THEN 'OUTROS'
END AS aluno_total_descricao_situacao 
FROM smc_vinculo_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
WHERE vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'
AND vinculo_aluno_id_sec = '$row_UsuarioLogado[usu_sec]'
GROUP BY vinculo_aluno_situacao";
$Matriculados = mysql_query($query_AlunosMatriculados, $SmecelNovo) or die(mysql_error());
$row_Matriculados = mysql_fetch_assoc($Matriculados);
$totalRows_row_Matriculados = mysql_num_rows($Matriculados);

//APROVADOS E REPROVADOS
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AlunosAprovados = "
SELECT 
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_ponto_id, vinculo_aluno_data, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia,
vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, vinculo_aluno_vacina_data_retorno, 
vinculo_aluno_conselho, vinculo_aluno_conselho_parecer,vinculo_aluno_resultado_final,COUNT(vinculo_aluno_resultado_final) AS aluno_total,turma_id, turma_matriz_id,
CASE vinculo_aluno_resultado_final
WHEN 1 THEN 'APROVADO'
WHEN 2 THEN 'REPROVADO'
END AS aluno_total_descricao   
FROM smc_vinculo_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
WHERE vinculo_aluno_boletim = '1' AND vinculo_aluno_id_sec = '$row_UsuarioLogado[usu_sec]' AND vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND
vinculo_aluno_resultado_final != 0
AND vinculo_aluno_situacao = 1
GROUP BY vinculo_aluno_resultado_final";
$Aprovados = mysql_query($query_AlunosAprovados, $SmecelNovo) or die('Erro na consulta: ' . mysql_error());
$row_Aprovados = mysql_fetch_assoc($Aprovados);
$totalRows_Aprovados = mysql_num_rows($Aprovados);


//APROVADOS, REPROVADOS E DESISTENTES
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AlunosAprovadosD = "
SELECT 
vinculo_aluno_id, 
vinculo_aluno_id_aluno, 
vinculo_aluno_id_turma, 
vinculo_aluno_id_escola, 
vinculo_aluno_id_sec, 
vinculo_aluno_ano_letivo, 
vinculo_aluno_transporte, 
vinculo_aluno_ponto_id, 
vinculo_aluno_data, 
vinculo_aluno_hash, 
vinculo_aluno_verificacao, 
vinculo_aluno_boletim, 
vinculo_aluno_situacao, 
vinculo_aluno_datatransferencia,
vinculo_aluno_da_casa, 
vinculo_aluno_historico_transferencia, 
vinculo_aluno_vacina_atualizada, 
vinculo_aluno_vacina_data_retorno, 
vinculo_aluno_conselho, 
vinculo_aluno_conselho_parecer,
vinculo_aluno_resultado_final,
turma_id, 
turma_matriz_id,
CASE vinculo_aluno_resultado_final
WHEN 1 THEN 'APROVADO'
WHEN 2 THEN 'REPROVADO'
END AS aluno_total_descricao,
COUNT(*) AS aluno_total,
CASE vinculo_aluno_situacao
WHEN 1 THEN 'MATRICULADOS'
WHEN 2 THEN 'TRANSFERIDOS'
WHEN 3 THEN 'DESISTENTES'
WHEN 4 THEN 'FALECIDOS'
WHEN 5 THEN 'OUTROS'
END AS aluno_total_descricao_situacao 
FROM smc_vinculo_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
WHERE vinculo_aluno_id_sec = '$row_UsuarioLogado[usu_sec]' 
AND vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' 
AND vinculo_aluno_situacao IN (1, 3)
GROUP BY vinculo_aluno_id";
$AprovadosD = mysql_query($query_AlunosAprovadosD, $SmecelNovo) or die(mysql_error());
$row_AprovadosD = mysql_fetch_assoc($AprovadosD);
$totalRows_AprovadosD = mysql_num_rows($AprovadosD);
?>

<!DOCTYPE html>
<html class="ls-theme-green">

<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
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
  <link rel="stylesheet" type="text/css" href="css/locastyle.css">
  <script src="js/locastyle.js"></script>
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>

<body>
  <?php include_once("menu_top.php"); ?>
  <?php include_once "menu.php"; ?>
  <main class="ls-main">
    <div class="container-fluid">
      <h1 class="ls-title-intro ls-ico-home">RESULTADO FINAL</h1>
      <!-- CONTEUDO -->
      <p>
        <?php
        if ($totalRows_Aprovados > 0 ) {

          ?>
          <?php do { ?>
            <?= $row_Aprovados['aluno_total_descricao'] ?>: <?= $row_Aprovados['aluno_total'] ?> |
          <?php } while ($row_Aprovados = mysql_fetch_assoc($Aprovados)); ?>
       <?php } else { ?>
        <p>Ainda sem dados de aprovados/reprovados</p>
        <?php
        }
        ?>
        </p>
        <p>
          <?php
          do {
            echo $row_Matriculados['aluno_total_descricao_situacao'] . ": " . $row_Matriculados['aluno_situacao'] . " | ";
          } while ($row_Matriculados = mysql_fetch_assoc($Matriculados));
          ?>

       
      </p>

      <p>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript">
          google.charts.load('current', { 'packages': ['corechart'] });
          google.charts.setOnLoadCallback(drawChart);

          function drawChart() {
            var data = google.visualization.arrayToDataTable([
              ['SITUAÇÃO', 'TOTAL'],
              <?php
              // Reinicie o ponteiro do resultado da consulta
              mysql_data_seek($Aprovados, 0);
              while ($row_Aprovados = mysql_fetch_assoc($Aprovados)) {
                echo "['{$row_Aprovados['aluno_total_descricao']} ({$row_Aprovados['aluno_total']})', {$row_Aprovados['aluno_total']}],";
              }
              ?>
            ]);

            var options = {
              title: 'GRÁFICO POR SITUAÇÃO'
            };

            var chart = new google.visualization.PieChart(document.getElementById('piechart'));

            chart.draw(data, options);
          }
        </script>

        <script type="text/javascript">
          google.charts.load('current', { 'packages': ['corechart'] });
          google.charts.setOnLoadCallback(drawChart);

          function drawChart() {
            var data = google.visualization.arrayToDataTable([
              ['SITUAÇÃO', 'TOTAL'],
              <?php
              // Reinicie o ponteiro do resultado da consulta
              mysql_data_seek($Matriculados, 0);
              // Segundo loop para gerar os dados para o gráfico
              while ($row_Matriculados = mysql_fetch_assoc($Matriculados)) {
                echo "['{$row_Matriculados['aluno_total_descricao_situacao']} ({$row_Matriculados['aluno_situacao']})', {$row_Matriculados['aluno_situacao']}],";
              }
              ?>
            ]);

            var options = {
              title: 'GRÁFICO POR SITUAÇÃO'
            };

            var chart = new google.visualization.PieChart(document.getElementById('piechart_matriculados'));

            chart.draw(data, options);
          }
        </script>

        <script type="text/javascript">
          google.charts.load('current', { 'packages': ['corechart'] });
          google.charts.setOnLoadCallback(drawChart);

          function drawChart() {
            var data = google.visualization.arrayToDataTable([
              ['SITUAÇÃO', 'TOTAL'],
              <?php
              // Reinicie o ponteiro do resultado da consulta
              mysql_data_seek($AprovadosD, 0);
              // Inicialize variáveis para armazenar os totais
              $aprovados_total = 0;
              $reprovados_total = 0;
              $desistentes_total = 0;
              // Loop para calcular os totais
              while ($row_AprovadosD = mysql_fetch_assoc($AprovadosD)) {
                if ($row_AprovadosD['aluno_total_descricao'] == 'APROVADO') {
                  $aprovados_total += $row_AprovadosD['aluno_total'];
                } elseif ($row_AprovadosD['aluno_total_descricao'] == 'REPROVADO') {
                  $reprovados_total += $row_AprovadosD['aluno_total'];
                } elseif ($row_AprovadosD['aluno_total_descricao_situacao'] == 'DESISTENTES') {
                  $desistentes_total += $row_AprovadosD['aluno_total'];
                }
              }
              // Imprimir os totais no formato adequado para o gráfico
              echo "['APROVADOS ($aprovados_total)', {$aprovados_total}],";
              echo "['REPROVADOS ($reprovados_total)', {$reprovados_total}],";
              echo "['DESISTENTES ($desistentes_total)', {$desistentes_total}],";
              ?>
            ]);

            var options = {
              title: 'GRÁFICO POR SITUAÇÃO'
            };

            var chart = new google.visualization.PieChart(document.getElementById('piechart_matriculados_desistentes'));

            chart.draw(data, options);
          }
        </script>

      <div class="row">

        <div class="col-md-12 col-sm-12">
          <div id="piechart" style="width: 100%; height: 500px;"></div>
        </div>
        <div class="col-md-12 col-sm-12">
          <div id="piechart_matriculados" style="width: 100%; height: 500px;"></div>
        </div>

        <div class="col-md-12 col-sm-12">
          <div id="piechart_matriculados_desistentes" style="width: 100%; height: 500px;"></div>
        </div>

      </div>
      </p>


      <p>&nbsp;</p>
      <!-- CONTEUDO -->
    </div>
  </main>
  <?php include_once "notificacoes.php"; ?>

  <!-- We recommended use jQuery 1.10 or up -->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="js/locastyle.js"></script>
</body>

</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

mysql_free_result($Vinculos);

if ($totalRows_Vinculos > 0) {
  //mysql_free_result($Matriz);
//mysql_free_result($CriteriosAvaliativos);
//mysql_free_result($disciplinasMatrizCab);
//mysql_free_result($disciplinasMatriz);
//mysql_free_result($nota);
//mysql_free_result($notaAf);
}
?>