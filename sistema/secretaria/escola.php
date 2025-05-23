<?php require_once('../../Connections/SmecelNovo.php'); ?>
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
include "../escola/fnc/alunosConta.php";

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

$colname_Escola = "-1";
if (isset($_GET['escola'])) {
  $colname_Escola = $_GET['escola'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Escola = sprintf("SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_ue, escola_situacao FROM smc_escola WHERE escola_id_sec = '$row_Secretaria[sec_id]' AND escola_id = %s", GetSQLValueString($colname_Escola, "int"));
$Escola = mysql_query($query_Escola, $SmecelNovo) or die(mysql_error());
$row_Escola = mysql_fetch_assoc($Escola);
$totalRows_Escola = mysql_num_rows($Escola);

if ($totalRows_Escola < 1) {
  $redireciona = "index.php?erro";
  header(sprintf("Location: %s", $redireciona));
}

$colname_Turmas = "-1";
if (isset($_GET['escola'])) {
  $colname_Turmas = $_GET['escola'];
}
$query_Turmas = sprintf("
    SELECT 
        COUNT(CASE WHEN turma_tipo_atendimento = 1 THEN 1 END) AS total_turmas_ativas,
        COUNT(CASE WHEN turma_tipo_atendimento <> 1 THEN 1 END) AS total_turmas_extras
    FROM smc_turma 
    WHERE turma_ano_letivo = '%s' AND turma_id_escola = %s
", $row_AnoLetivo['ano_letivo_ano'], GetSQLValueString($colname_Turmas, "int"));

$Turmas = mysql_query($query_Turmas, $SmecelNovo) or die(mysql_error());
$row_Turmas = mysql_fetch_assoc($Turmas);


$colname_Matriculas = "-1";
if (isset($_GET['escola'])) {
  $colname_Matriculas = $_GET['escola'];
}
$query_Matriculas = sprintf("
    SELECT 
        COUNT(CASE WHEN t.turma_tipo_atendimento = 1 THEN 1 END) AS total_matriculas_ativas,
        COUNT(CASE WHEN t.turma_tipo_atendimento <> 1 THEN 1 END) AS total_matriculas_extras
    FROM smc_vinculo_aluno v
    INNER JOIN smc_turma t ON v.vinculo_aluno_id_turma = t.turma_id
    WHERE v.vinculo_aluno_ano_letivo = '%s' 
    AND v.vinculo_aluno_situacao = '1' 
    AND v.vinculo_aluno_id_escola = %s
", $row_AnoLetivo['ano_letivo_ano'], GetSQLValueString($colname_Matriculas, "int"));

$Matriculas = mysql_query($query_Matriculas, $SmecelNovo) or die(mysql_error());
$row_Matriculas = mysql_fetch_assoc($Matriculas);


$colname_Funcionarios = "-1";
if (isset($_GET['escola'])) {
  $colname_Funcionarios = $_GET['escola'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Funcionarios = sprintf("
SELECT vinculo_id, vinculo_id_escola, vinculo_id_funcionario, vinculo_id_funcao, 
vinculo_carga_horaria, vinculo_data_inicio, vinculo_obs 
FROM smc_vinculo WHERE vinculo_id_escola = %s", GetSQLValueString($colname_Funcionarios, "int"));
$Funcionarios = mysql_query($query_Funcionarios, $SmecelNovo) or die(mysql_error());
$row_Funcionarios = mysql_fetch_assoc($Funcionarios);
$totalRows_Funcionarios = mysql_num_rows($Funcionarios);

$colname_TrumasPorTurno = "-1";
if (isset($_GET['escola'])) {
  $colname_TrumasPorTurno = $_GET['escola'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_TrumasPorTurno = sprintf("
SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, 
turma_turno, turma_total_alunos, turma_ano_letivo, COUNT(*) AS turmasTurnoTotal,
CASE turma_turno
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno_nome 
FROM smc_turma 
WHERE turma_tipo_atendimento = '1' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND turma_id_escola = %s
GROUP BY turma_turno ASC
", GetSQLValueString($colname_TrumasPorTurno, "int"));
$TrumasPorTurno = mysql_query($query_TrumasPorTurno, $SmecelNovo) or die(mysql_error());
$row_TrumasPorTurno = mysql_fetch_assoc($TrumasPorTurno);
$totalRows_TrumasPorTurno = mysql_num_rows($TrumasPorTurno);

$colname_MatriculasPorTurno = "-1";
if (isset($_GET['escola'])) {
  $colname_MatriculasPorTurno = $_GET['escola'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_MatriculasPorTurno = sprintf("
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data,
vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, 
vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, 
vinculo_aluno_vacina_atualizada, turma_id, turma_tipo_atendimento, turma_turno,  COUNT(*) AS totalAlunosTurno, 
CASE turma_turno
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno_nome 
FROM smc_vinculo_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma 
WHERE turma_tipo_atendimento = '1' AND vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND vinculo_aluno_situacao = '1' AND vinculo_aluno_id_escola = %s
GROUP BY turma_turno", GetSQLValueString($colname_MatriculasPorTurno, "int"));
$MatriculasPorTurno = mysql_query($query_MatriculasPorTurno, $SmecelNovo) or die(mysql_error());
$row_MatriculasPorTurno = mysql_fetch_assoc($MatriculasPorTurno);
$totalRows_MatriculasPorTurno = mysql_num_rows($MatriculasPorTurno);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_TurmasPorEtapa = "
SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, 
turma_turno, turma_total_alunos, turma_ano_letivo, etapa_id, etapa_id_filtro, etapa_nome, COUNT(*) AS total, escola_id, escola_situacao 
FROM smc_turma 
INNER JOIN smc_etapa ON etapa_id = turma_etapa
INNER JOIN smc_escola ON escola_id = turma_id_escola
WHERE turma_tipo_atendimento = '1' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND  turma_id_sec = '$row_Secretaria[sec_id]' AND escola_situacao = '1' AND turma_id_escola = '$row_Escola[escola_id]'
GROUP BY turma_etapa
";
$TurmasPorEtapa = mysql_query($query_TurmasPorEtapa, $SmecelNovo) or die(mysql_error());
$row_TurmasPorEtapa = mysql_fetch_assoc($TurmasPorEtapa);
$totalRows_TurmasPorEtapa = mysql_num_rows($TurmasPorEtapa);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_TurmasPorEtapaFiltro = "
SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, 
turma_turno, turma_total_alunos, turma_ano_letivo, etapa_id, etapa_id_filtro, etapa_nome, 
etapa_filtro_id, etapa_filtro_nome, COUNT(*) AS total, escola_id, escola_situacao 
FROM smc_turma 
INNER JOIN smc_etapa ON etapa_id = turma_etapa
INNER JOIN smc_etapa_filtro ON etapa_filtro_id = etapa_id_filtro
INNER JOIN smc_escola ON escola_id = turma_id_escola
WHERE turma_tipo_atendimento = '1' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND  turma_id_sec = '$row_Secretaria[sec_id]' AND escola_situacao = '1' AND turma_id_escola = '$row_Escola[escola_id]'
GROUP BY etapa_filtro_id
";
$TurmasPorEtapaFiltro = mysql_query($query_TurmasPorEtapaFiltro, $SmecelNovo) or die(mysql_error());
$row_TurmasPorEtapaFiltro = mysql_fetch_assoc($TurmasPorEtapaFiltro);
$totalRows_TurmasPorEtapaFiltro = mysql_num_rows($TurmasPorEtapaFiltro);

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
      <h1 class="ls-title-intro ls-ico-home">DADOS DA ESCOLA</h1>

      <a href="escolas.php" class="ls-btn-primary">VOLTAR</a>


      <div class="ls-box ls-board-box">
        <header class="ls-info-header">
          <p class="ls-float-right ls-float-none-xs ls-small-info">Dados atualizados em
            <strong><?php echo date("d/m/Y"); ?> às <?php echo date("H\hi"); ?></strong>
          </p>
          <h2 class="ls-title-3"><?php echo $row_Escola['escola_nome']; ?></h2>
        </header>
        <div id="sending-stats" class="row ls-clearfix">
          <div class="col-sm-12 col-md-4">
            <div class="ls-box">
              <div class="ls-box-head">
                <h6 class="ls-title-4">TURMAS</h6>
              </div>
              <div class="ls-box-body">
                <span class="ls-board-data">
                  <strong class="ls-color-danger">
                    <?php echo $row_Turmas['total_turmas_ativas']; ?>
                    <small>CADASTRADAS</small>
                    <?php if ($row_Turmas['total_turmas_extras'] > 0): ?>
                      <small style="font-size: 12px;">(+<?php echo $row_Turmas['total_turmas_extras']; ?> extras)</small>
                    <?php endif; ?>
                  </strong>
                </span>
              </div>
              <div class="ls-box-footer">
                <a href="turmas.php" class="ls-btn ls-btn-xs">Ver Turmas</a>
              </div>
            </div>
          </div>

          <div class="col-sm-12 col-md-4">
            <div class="ls-box">
              <div class="ls-box-head">
                <h6 class="ls-title-4">MATRÍCULAS</h6>
              </div>
              <div class="ls-box-body">
                <span class="ls-board-data">
                  <strong class="ls-color-warning">
                    <?php echo $row_Matriculas['total_matriculas_ativas']; ?>
                    <small>ATIVAS</small>
                    <?php if ($row_Matriculas['total_matriculas_extras'] > 0): ?>
                      <small style="font-size: 12px;">(+<?php echo $row_Matriculas['total_matriculas_extras']; ?>
                        extras)</small>
                    <?php endif; ?>
                  </strong>
                </span>

              </div>
              <div class="ls-box-footer">
                <a href="matriculas.php" class="ls-btn ls-btn-xs">Ver Matrículas</a>
              </div>
            </div>
          </div>

          <div class="col-sm-12 col-md-4">
            <div class="ls-box">
              <div class="ls-box-head">
                <h6 class="ls-title-4">FUNCIONÁRIOS</h6>
              </div>
              <div class="ls-box-body"> <span class="ls-board-data"> <strong><?php echo $totalRows_Funcionarios; ?>
                    <small>VINCULADOS</small></strong> </span> </div>
              <div class="ls-box-footer"> <a href="funcionarios_vinculados.php" class="ls-btn ls-btn-xs">Ver
                  Funcionários</a> </div>
            </div>
          </div>
        </div>
        <br>
        <small><i class="ls-color-info">Os números *extras referem-se a matrículas e/ou turmas de outros tipos de
            atendimento, como AEE, Complementar, entre outros.</i></small>
      </div>
      <div class="ls-box ls-board-box">
        <div class="row">
          <div class="col-sm-12 col-md-6">
            <!-- CHART -->
            <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
            <script type="text/javascript">
              google.charts.load('current', { 'packages': ['corechart'] });
              google.charts.setOnLoadCallback(drawChart);
              function drawChart() {
                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Topping');
                data.addColumn('number', 'Slices');
                data.addRows([
                  <?php do { ?>
                    ['<?php echo $row_TrumasPorTurno['turma_turno_nome']; ?> (<?php echo $row_TrumasPorTurno['turmasTurnoTotal']; ?>)', <?php echo $row_TrumasPorTurno['turmasTurnoTotal']; ?>],
                  <?php } while ($row_TrumasPorTurno = mysql_fetch_assoc($TrumasPorTurno)); ?>
                ]);
                var options = {
                  'title': 'TURMAS POR TURNO',
                  is3D: false,
                  pieSliceText: 'percentage',
                  fontSize: '10',
                  legend: 'bottom',
                  // 'width':400,
                  //'height':300
                };
                var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
                chart.draw(data, options);
              }
            </script>

            <!-- CHART -->
            </script>
            <div id="chart_div" style="width:100%; height:500px; padding-right:15px;"></div>
          </div>
          <div class="col-sm-12 col-md-6">

            <!-- CHART -->
            <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
            <script type="text/javascript">
              google.charts.load('current', { 'packages': ['corechart'] });
              google.charts.setOnLoadCallback(drawChart);
              function drawChart() {
                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Topping');
                data.addColumn('number', 'Slices');
                data.addRows([
                  <?php do { ?>
                    [' <?php echo $row_MatriculasPorTurno['turma_turno_nome']; ?> (<?php echo $row_MatriculasPorTurno['totalAlunosTurno']; ?>)', <?php echo $row_MatriculasPorTurno['totalAlunosTurno']; ?>],
                  <?php } while ($row_MatriculasPorTurno = mysql_fetch_assoc($MatriculasPorTurno)); ?>
                ]);
                var options = {
                  'title': 'ALUNOS POR TURNO (MATRICULADOS)',
                  is3D: false,
                  pieSliceText: 'percentage',
                  fontSize: '10',
                  legend: 'bottom',
                  // 'width':400,
                  //'height':300
                };
                var chart = new google.visualization.PieChart(document.getElementById('chart_div_1'));
                chart.draw(data, options);
              }
            </script>

            <!-- CHART -->
            </script>
            <div id="chart_div_1" style="width:100%; height:500px; padding-right:15px;"></div>
          </div>
        </div>





      </div>

      <div class="ls-box ls-board-box">
        <header class="ls-info-header">
          <p class="ls-float-right ls-float-none-xs ls-small-info">Dados atualizados em
            <strong><?php echo date("d/m/Y"); ?> às <?php echo date("H\hi"); ?></strong>
          </p>
          <h2 class="ls-title-3">Turmas por Etapa de Ensino</h2>
        </header>

        <?php if ($totalRows_TurmasPorEtapa > 0) { ?>
          <table class="ls-table ls-sm-space" width="100%">
            <thead>
              <tr>
                <th width="50"></th>
                <th>ETAPA DE ENSINO</th>
                <th class="ls-txt-center">TOTAL DE TURMAS</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $n = 1;
              $total = 0;
              do { ?>
                <tr>
                  <td class="ls-txt-center"><strong><?php echo $n;
                  $n++; ?></strong></td>
                  <td><?php echo $row_TurmasPorEtapa['etapa_nome']; ?></td>
                  <td class="ls-txt-center"><?php echo $row_TurmasPorEtapa['total']; ?></td>
                </tr>
                <?php $total = $total + $row_TurmasPorEtapa['total']; ?>
              <?php } while ($row_TurmasPorEtapa = mysql_fetch_assoc($TurmasPorEtapa)); ?>
              <tr>
                <td></td>
                <td class=""><strong>TOTAL</strong></td>
                <td class="ls-txt-center"><strong><?php echo $total; ?></strong></td>
              </tr>

            </tbody>
          </table>
        <?php } else { ?>
          <div class="ls-alert-info"><strong>Atenção:</strong> Sem turmas cadastradas.</div>
        <?php } ?>


      </div>


      <div class="ls-box ls-board-box">

        <header class="ls-info-header">
          <p class="ls-float-right ls-float-none-xs ls-small-info">Dados atualizados em
            <strong><?php echo date("d/m/Y"); ?> às <?php echo date("H\hi"); ?></strong>
          </p>
          <h2 class="ls-title-3">Turmas por filtro de Etapa</h2>
        </header>

        <?php if ($totalRows_TurmasPorEtapaFiltro > 0) { ?>
          <table class="ls-table ls-sm-space" width="100%">
            <thead>
              <tr>
                <th width="50"></th>
                <th>FILTRO DE ETAPA</th>
                <th class="ls-txt-center">TOTAL DE TURMAS</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $n = 1;
              $total = 0;
              do { ?>
                <tr>
                  <td class="ls-txt-center"><strong><?php echo $n;
                  $n++; ?></strong></td>
                  <td><?php echo $row_TurmasPorEtapaFiltro['etapa_filtro_nome']; ?></td>
                  <td class="ls-txt-center"><?php echo $row_TurmasPorEtapaFiltro['total']; ?></td>
                </tr>
                <?php $total = $total + $row_TurmasPorEtapaFiltro['total']; ?>
              <?php } while ($row_TurmasPorEtapaFiltro = mysql_fetch_assoc($TurmasPorEtapaFiltro)); ?>
              <tr>
                <td></td>
                <td class=""><strong>TOTAL</strong></td>
                <td class="ls-txt-center"><strong><?php echo $total; ?></strong></td>
              </tr>

            </tbody>
          </table>
        <?php } else { ?>
          <div class="ls-alert-info"><strong>Atenção:</strong> Sem turmas cadastradas.</div>
        <?php } ?>



      </div>


      <p>&nbsp;</p>
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

mysql_free_result($Escola);

mysql_free_result($Turmas);

mysql_free_result($Matriculas);

mysql_free_result($Funcionarios);

mysql_free_result($MatriculasPorTurno);

mysql_free_result($TrumasPorTurno);
?>