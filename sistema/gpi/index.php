<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php require_once('funcoes/configuracoes.php'); ?>

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
$MM_authorizedUsers = "99";
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

$colname_UsuarioLogado = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_UsuarioLogado = $_SESSION['MM_Username'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_UsuarioLogado = sprintf("SELECT usu_id, usu_nome, usu_email, usu_senha, usu_tipo, usu_sec, usu_escola, usu_status, usu_cadastro FROM smc_usu WHERE usu_email = %s", GetSQLValueString($colname_UsuarioLogado, "text"));
$UsuarioLogado = mysql_query($query_UsuarioLogado, $SmecelNovo) or die(mysql_error());
$row_UsuarioLogado = mysql_fetch_assoc($UsuarioLogado);
$totalRows_UsuarioLogado = mysql_num_rows($UsuarioLogado);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Entidades = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_ibge_municipio, sec_regra_media FROM smc_sec";
$Entidades = mysql_query($query_Entidades, $SmecelNovo) or die(mysql_error());
$row_Entidades = mysql_fetch_assoc($Entidades);
$totalRows_Entidades = mysql_num_rows($Entidades);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Escolas = "SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_ue, escola_situacao, escola_localizacao, escola_ibge_municipio FROM smc_escola";
$Escolas = mysql_query($query_Escolas, $SmecelNovo) or die(mysql_error());
$row_Escolas = mysql_fetch_assoc($Escolas);
$totalRows_Escolas = mysql_num_rows($Escolas);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turmas = "SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo FROM smc_turma";
$Turmas = mysql_query($query_Turmas, $SmecelNovo) or die(mysql_error());
$row_Turmas = mysql_fetch_assoc($Turmas);
$totalRows_Turmas = mysql_num_rows($Turmas);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Alunos = "SELECT aluno_id, aluno_cod_inep, aluno_cpf, aluno_nome, aluno_nascimento, aluno_filiacao1, aluno_filiacao2, aluno_sexo, aluno_raca, aluno_nacionalidade, aluno_uf_nascimento, aluno_municipio_nascimento, aluno_municipio_nascimento_ibge, aluno_aluno_com_deficiencia, aluno_nis, aluno_identidade, aluno_emissor, aluno_uf_emissor, aluno_data_espedicao, aluno_tipo_certidao, aluno_termo, aluno_folhas, aluno_livro, aluno_emissao_certidao, aluno_uf_cartorio, aluno_mucicipio_cartorio, aluno_nome_cartorio, aluno_num_matricula_modelo_novo, aluno_localizacao, aluno_cep, aluno_endereco, aluno_numero, aluno_complemento, aluno_bairro, aluno_uf, aluno_municipio, aluno_telefone, aluno_celular, aluno_email, aluno_sus, aluno_tipo_deficiencia, aluno_laudo, aluno_alergia, aluno_alergia_qual, aluno_destro, aluno_emergencia_avisar, aluno_emergencia_tel1, aluno_emergencia_tel2, aluno_prof_mae, aluno_tel_mae, aluno_escolaridade_mae, aluno_rg_mae, aluno_cpf_mae, aluno_prof_pai, aluno_tel_pai, aluno_escolaridade_pai, aluno_rg_pai, aluno_cpf_pai, aluno_hash, aluno_recebe_bolsa_familia, aluno_foto, aluno_def_bvisao, aluno_def_cegueira, aluno_def_auditiva, aluno_def_fisica, aluno_def_intelectual, aluno_def_surdez, aluno_def_surdocegueira, aluno_def_autista, aluno_def_superdotacao FROM smc_aluno";
$Alunos = mysql_query($query_Alunos, $SmecelNovo) or die(mysql_error());
$row_Alunos = mysql_fetch_assoc($Alunos);
$totalRows_Alunos = mysql_num_rows($Alunos);

mysql_select_db($database_SmecelNovo, $SmecelNovo);

// Consulta para obter o total de acessos de hoje, ontem e há dois dias
$query_Logins = "
SELECT 
    DATE(log_data_hora) AS access_date,
    COUNT(*) AS total_accesses
FROM smc_log 
WHERE DATE(log_data_hora) >= CURDATE() - INTERVAL 2 DAY 
GROUP BY DATE(log_data_hora)
ORDER BY access_date DESC
";

$Logins = mysql_query($query_Logins, $SmecelNovo) or die(mysql_error());

// Inicia a contagem como zero
$totalHoje = 0;
$totalOntem = 0;
$totalDoisDiasAtras = 0;

// Atribui os valores totais de acessos para cada data
while ($row_Logins = mysql_fetch_assoc($Logins)) {
  if ($row_Logins['access_date'] == date('Y-m-d')) {
    $totalHoje = $row_Logins['total_accesses'];
  } elseif ($row_Logins['access_date'] == date('Y-m-d', strtotime('-1 day'))) {
    $totalOntem = $row_Logins['total_accesses'];
  } elseif ($row_Logins['access_date'] == date('Y-m-d', strtotime('-2 days'))) {
    $totalDoisDiasAtras = $row_Logins['total_accesses'];
  }
}


mysql_select_db($database_SmecelNovo, $SmecelNovo);

// Consulta para obter o total de acessos de hoje, ontem e há dois dias para a tabela smc_login_professor
$query_LoginsProfessor = "
SELECT 
    DATE(login_professor_data_hora) AS access_date,
    COUNT(*) AS total_accesses
FROM smc_login_professor 
WHERE DATE(login_professor_data_hora) >= CURDATE() - INTERVAL 2 DAY 
GROUP BY DATE(login_professor_data_hora)
ORDER BY access_date DESC
";

$LoginsProfessor = mysql_query($query_LoginsProfessor, $SmecelNovo) or die(mysql_error());

// Inicia a contagem como zero
$totalHojeProf = 0;
$totalOntemProf = 0;
$totalDoisDiasAtrasProf = 0;

// Atribui os valores totais de acessos para cada data
while ($row_LoginsProfessor = mysql_fetch_assoc($LoginsProfessor)) {
  if ($row_LoginsProfessor['access_date'] == date('Y-m-d')) {
    $totalHojeProf = $row_LoginsProfessor['total_accesses'];
  } elseif ($row_LoginsProfessor['access_date'] == date('Y-m-d', strtotime('-1 day'))) {
    $totalOntemProf = $row_LoginsProfessor['total_accesses'];
  } elseif ($row_LoginsProfessor['access_date'] == date('Y-m-d', strtotime('-2 days'))) {
    $totalDoisDiasAtrasProf = $row_LoginsProfessor['total_accesses'];
  }
}


mysql_select_db($database_SmecelNovo, $SmecelNovo);

// Consulta para obter o total de acessos de hoje, ontem e há dois dias para a tabela smc_login_aluno
$query_LoginsAluno = "
SELECT 
    DATE(login_aluno_data_hora) AS access_date,
    COUNT(*) AS total_accesses
FROM smc_login_aluno 
WHERE DATE(login_aluno_data_hora) >= CURDATE() - INTERVAL 2 DAY 
GROUP BY DATE(login_aluno_data_hora)
ORDER BY access_date DESC
";

$LoginsAluno = mysql_query($query_LoginsAluno, $SmecelNovo) or die(mysql_error());

// Inicia a contagem como zero
$totalHojeAluno = 0;
$totalOntemAluno = 0;
$totalDoisDiasAtrasAluno = 0;

// Atribui os valores totais de acessos para cada data
while ($row_LoginsAluno = mysql_fetch_assoc($LoginsAluno)) {
  if ($row_LoginsAluno['access_date'] == date('Y-m-d')) {
    $totalHojeAluno = $row_LoginsAluno['total_accesses'];
  } elseif ($row_LoginsAluno['access_date'] == date('Y-m-d', strtotime('-1 day'))) {
    $totalOntemAluno = $row_LoginsAluno['total_accesses'];
  } elseif ($row_LoginsAluno['access_date'] == date('Y-m-d', strtotime('-2 days'))) {
    $totalDoisDiasAtrasAluno = $row_LoginsAluno['total_accesses'];
  }
}

$anoAtual = date('Y');

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_TurmasPorEtapa = "
SELECT 
turma_id, turma_etapa, etapa_id, etapa_nome, etapa_nome_abrev, turma_ano_letivo, turma_id_sec, turma_tipo_atendimento, turma_id_escola, COUNT(*) AS total, escola_id, escola_situacao 
FROM smc_turma
INNER JOIN smc_etapa ON etapa_id = turma_etapa
INNER JOIN smc_escola ON escola_id = turma_id_escola
WHERE turma_tipo_atendimento = '1' AND turma_ano_letivo = '$anoAtual' AND turma_ano_letivo = '$anoAtual' AND escola_situacao = '1'
GROUP BY etapa_id, etapa_nome, etapa_nome_abrev
ORDER BY etapa_id 
";
$TurmasPorEtapa = mysql_query($query_TurmasPorEtapa, $SmecelNovo) or die(mysql_error());
$row_TurmasPorEtapa = mysql_fetch_assoc($TurmasPorEtapa);
$totalRows_TurmasPorEtapa = mysql_num_rows($TurmasPorEtapa);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_MatriculasPorEtapa = "
SELECT 
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, 
vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, 
vinculo_aluno_vacina_atualizada, turma_id, turma_etapa, turma_tipo_atendimento, turma_ano_letivo, etapa_id, etapa_nome, etapa_nome_abrev, COUNT(*) AS total, escola_id, escola_situacao 
FROM smc_vinculo_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
INNER JOIN smc_etapa ON etapa_id = turma_etapa
INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola
WHERE turma_tipo_atendimento = '1' AND turma_ano_letivo = '$anoAtual' AND vinculo_aluno_ano_letivo = '$anoAtual' AND vinculo_aluno_situacao = '1' AND escola_situacao = '1'
GROUP BY etapa_id, etapa_nome, etapa_nome_abrev
ORDER BY etapa_id 
";
$MatriculasPorEtapa = mysql_query($query_MatriculasPorEtapa, $SmecelNovo) or die(mysql_error());
$row_MatriculasPorEtapa = mysql_fetch_assoc($MatriculasPorEtapa);
$totalRows_MatriculasPorEtapa = mysql_num_rows($MatriculasPorEtapa);
?>

<!DOCTYPE html>
<html class="<?php echo COR_TEMA ?>">

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
  <link rel="stylesheet" type="text/css" href="//assets.locaweb.com.br/locastyle/edge/stylesheets/locastyle.css">
  <script src="//assets.locaweb.com.br/locastyle/edge/javascripts/locastyle.js"></script>
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">

  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript">
    google.charts.load('current', { 'packages': ['corechart'] });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
      var data = google.visualization.arrayToDataTable([


        ['Data', 'Matrículas', 'Transferências'],

        <?php
        $date_fim = date("Y-m-d"); //Data final
        $date_ini = date("Y-m-d", strtotime("-10 days", strtotime($date_fim)));
        ; //Data inicial
        $contaMatriculas = 0;
        $contaTransferencias = 0;

        while (strtotime($date_ini) <= strtotime($date_fim)) {

          mysql_select_db($database_SmecelNovo, $SmecelNovo);
          $query_Matriculas = "
					SELECT vinculo_aluno_data, vinculo_aluno_situacao, vinculo_aluno_datatransferencia 
					FROM smc_vinculo_aluno
					WHERE vinculo_aluno_situacao = '1' AND vinculo_aluno_data = '$date_ini'";
          $Matriculas = mysql_query($query_Matriculas, $SmecelNovo) or die(mysql_error());
          $row_Matriculas = mysql_fetch_assoc($Matriculas);
          $totalRows_Matriculas = mysql_num_rows($Matriculas);

          mysql_select_db($database_SmecelNovo, $SmecelNovo);
          $query_MatriculasT = "
					SELECT vinculo_aluno_data, vinculo_aluno_situacao, vinculo_aluno_datatransferencia
					FROM smc_vinculo_aluno
					WHERE vinculo_aluno_situacao = '2' AND vinculo_aluno_datatransferencia = '$date_ini'";
          $MatriculasT = mysql_query($query_MatriculasT, $SmecelNovo) or die(mysql_error());
          $row_MatriculasT = mysql_fetch_assoc($MatriculasT);
          $totalRows_MatriculasT = mysql_num_rows($MatriculasT);


          ?>

          ['<?php echo date("d/m", strtotime($date_ini)); ?>', <?php echo $totalRows_Matriculas; ?>, <?php echo $totalRows_MatriculasT; ?>],

          <?php

          $date_ini = date("Y-m-d", strtotime("+1 day", strtotime($date_ini)));
          $contaMatriculas = $contaMatriculas + $totalRows_Matriculas;
          $contaTransferencias = $contaTransferencias + $totalRows_MatriculasT;

        }

        ?>



      ]);

      var options = {
        vAxis: { minValue: 0 },
        legend: { position: 'bottom', maxLines: 3 },
        animation: {
          startup: true,
          duration: 1000,
          easing: 'linear'
        }
      };

      var chart = new google.visualization.AreaChart(document.getElementById('chart_div_matriculas'));
      chart.draw(data, options);
    }
  </script>

</head>

<body>
  <?php include_once("menu_top.php"); ?>
  <?php include_once "menu.php"; ?>
  <main class="ls-main">
    <div class="container-fluid">
      <h1 class="ls-title-intro ls-ico-home">INÍCIO</h1>
      <!-- CONTEUDO -->

      <div class="ls-box">

        <header class="ls-info-header">
          <h2 class="ls-title-3">Resumo de logins <small><a href="login_hora.php" class="">Ver acessos por
                hora</a></small></h2>
          <p class="ls-float-right ls-float-none-xs ls-small-info">Atualizado em
            <strong><?php echo date("d/m/Y à\s H\hi"); ?></strong>
          </p>
        </header>

        <div class="row">
          <div class="col-md-4">
            <p>
              LOGIN PAINEL ADMINISTRATIVO
            <div class="ls-group-btn ls-group-active">
              <button href="login_hora.php" type="button" class="ls-btn">Hoje: <?php echo $totalHoje; ?></button>
              <button href="login_hora.php" type="button" class="ls-btn">Ontem: <?php echo $totalOntem; ?></button>
              <button href="login_hora.php" type="button" class="ls-btn">Há dois dias:
                <?php echo $totalDoisDiasAtras; ?></button>
            </div>
            </p>
          </div>
          <div class="col-md-4">
            <p>
              LOGIN PAINEL PROFESSOR
            <div class="ls-group-btn ls-group-active">
              <button href="login_hora.php" type="button" class="ls-btn">Hoje: <?php echo $totalHojeProf; ?></button>
              <button href="login_hora.php" type="button" class="ls-btn">Ontem: <?php echo $totalOntemProf; ?></button>
              <button href="login_hora.php" type="button" class="ls-btn">Há dois dias:
                <?php echo $totalDoisDiasAtrasProf; ?></button>
            </div>
            </p>
          </div>
          <div class="col-md-4">
            <p>
              LOGIN PAINEL ALUNO
            <div class="ls-group-btn ls-group-active">
              <button href="login_hora.php" type="button" class="ls-btn">Hoje: <?php echo $totalHojeAluno; ?></button>
              <button href="login_hora.php" type="button" class="ls-btn">Ontem: <?php echo $totalOntemAluno; ?></button>
              <button href="login_hora.php" type="button" class="ls-btn">Há dois dias:
                <?php echo $totalDoisDiasAtrasAluno; ?></button>
            </div>
            </p>
          </div>
        </div>
      </div>





      <div class="ls-box ls-board-box">

        <header class="ls-info-header">
          <h2 class="ls-title-3">Dashboard</h2>
          <p class="ls-float-right ls-float-none-xs ls-small-info">Atualizado em
            <strong><?php echo date("d/m/Y à\s H\hi"); ?></strong>
          </p>
        </header>

        <div id="sending-stats" class="row">

          <div class="col-sm-6 col-md-3">
            <div class="ls-box">
              <div class="ls-box-head">
                <h6 class="ls-title-4">ENTIDADES</h6>
              </div>
              <div class="ls-box-body">
                <strong><span class="count"><?php echo $totalRows_Entidades ?></span></strong>
                <small></small>
              </div>
              <div class="ls-box-footer">
                <a href="contratos.php" aria-label="Ver prefeituras" class="ls-btn ls-btn-sm"
                  title="Ver Prefeituras">Visualizar</a>
              </div>
            </div>
          </div>

          <div class="col-sm-6 col-md-3">
            <div class="ls-box">
              <div class="ls-box-head">
                <h6 class="ls-title-4">ESCOLAS</h6>
              </div>
              <div class="ls-box-body">
                <strong><span class="count"><?php echo $totalRows_Escolas ?></span></strong>
                <small></small>
              </div>
              <div class="ls-box-footer">
              </div>
            </div>
          </div>

          <div class="col-sm-6 col-md-3">
            <div class="ls-box">
              <div class="ls-box-head">
                <h6 class="ls-title-4">TURMAS</h6>
              </div>
              <div class="ls-box-body">
                <strong><span class="count"><?php echo $totalRows_Turmas ?></span></strong>
                <small></small>
              </div>
              <div class="ls-box-footer">
              </div>
            </div>
          </div>

          <div class="col-sm-6 col-md-3">
            <div class="ls-box">
              <div class="ls-box-head">
                <h6 class="ls-title-4">ALUNOS</h6>
              </div>
              <div class="ls-box-body">
                <strong><span class="count"><?php echo $totalRows_Alunos ?></span></strong>
                <small></small>
              </div>
              <div class="ls-box-footer">
              </div>
            </div>
          </div>


        </div>

      </div>

      <div class="ls-box ls-board-box">
        <h5 class="ls-title-3 ls-txt-center"><?php echo $contaMatriculas; ?> matrícula(s) e
          <?php echo $contaTransferencias; ?> transferência(s) realizadas nos últimos 10 dias
        </h5>
        <div id="chart_div_matriculas" style="width: 100%; height: 400px;"></div>
      </div>


      <?php if ($totalRows_MatriculasPorEtapa > 0) { ?>
        <div class="ls-box ls-board-box">
          <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
          <script type="text/javascript">
            google.charts.load("current", { packages: ['corechart'] });
            google.charts.setOnLoadCallback(drawChart);
            function drawChart() {
              var data = google.visualization.arrayToDataTable([


                ["ETAPA", "TOTAL", { role: "style" }],
                <?php do { ?>
                  ["<?php echo $row_MatriculasPorEtapa['etapa_nome_abrev']; ?>", <?php echo $row_MatriculasPorEtapa['total']; ?>, "#b87333"],
                <?php } while ($row_MatriculasPorEtapa = mysql_fetch_assoc($MatriculasPorEtapa)); ?>


              ]);

              var view = new google.visualization.DataView(data);
              view.setColumns([0, 1,
                {
                  calc: "stringify",
                  sourceColumn: 1,
                  type: "string",
                  role: "annotation"
                },
                2]);

              var options = {
                title: 'MATRÍCULAS POR ETAPA DE ENSINO',
                subtitle: 'SOMENTE MATRÍCULAS ATIVAS',
                width: '100%',
                orientation: "horizontal",
                height: 600,
                fontSize: 10,
                histogram: { lastBucketPercentile: 5 },
                vAxis: { scaleType: 'mirrorLog' },
                bar: { groupWidth: "50%" },
                legend: { position: "none" },
              };
              var chart = new google.visualization.ColumnChart(document.getElementById("columnchart_values"));
              chart.draw(view, options);
            }
          </script>
          <div id="columnchart_values" style="width: 100%; height: 600px;"></div>
        </div>
      <?php } else { ?>
        <div class="ls-box ls-board-box">GRÁFICO MATRÍCULAS POR ETAPA DE ENSINO<br><small>(Disponível após preenchimento
            de dados feito pelas Unidades Escolares)</small></div>
      <?php } ?>

      <?php if ($totalRows_TurmasPorEtapa > 0) { ?>
        <div class="ls-box ls-board-box">
          <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
          <script type="text/javascript">
            google.charts.load("current", { packages: ['corechart'] });
            google.charts.setOnLoadCallback(drawChart);
            function drawChart() {
              var data = google.visualization.arrayToDataTable([


                ["ETAPA", "TOTAL", { role: "style" }],
                <?php do { ?>
                  ["<?php echo $row_TurmasPorEtapa['etapa_nome_abrev']; ?>", <?php echo $row_TurmasPorEtapa['total']; ?>, "#a09555"],
                <?php } while ($row_TurmasPorEtapa = mysql_fetch_assoc($TurmasPorEtapa)); ?>


              ]);

              var view = new google.visualization.DataView(data);
              view.setColumns([0, 1,
                {
                  calc: "stringify",
                  sourceColumn: 1,
                  type: "string",
                  role: "annotation"
                },
                2]);

              var options = {
                title: 'TURMAS POR ETAPA DE ENSINO',
                width: '100%',
                orientation: "horizontal",
                height: 600,
                fontSize: 10,
                histogram: { lastBucketPercentile: 5 },
                vAxis: { scaleType: 'mirrorLog' },
                bar: { groupWidth: "50%" },
                legend: { position: "none" },
              };
              var chart = new google.visualization.ColumnChart(document.getElementById("columnchart_values_1"));
              chart.draw(view, options);
            }
          </script>
          <div id="columnchart_values_1" style="width: 100%; height: 600px;"></div>
        </div>
      <?php } else { ?>
        <div class="ls-box ls-board-box">GRÁFICO TURMAS POR ETAPA DE ENSINO<br><small>(Disponível após preenchimento de
            dados feito pelas Unidades Escolares)</small></div>
      <?php } ?>
      <!-- CONTEUDO -->
    </div>
  </main>
  <?php include_once "notificacoes.php"; ?>

  <!-- We recommended use jQuery 1.10 or up -->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="https://assets.locaweb.com.br/locastyle/3.8.4/javascripts/locastyle.js" type="text/javascript"></script>
  <script type="text/javascript">
    $('.count').each(function () {
      $(this).prop('Counter', 0).animate({
        Counter: $(this).text()
      }, {
        duration: 3000,
        easing: 'swing',
        step: function (now) {
          $(this).text(Math.ceil(now));
        }
      });
    });
  </script>
</body>

</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Entidades);

mysql_free_result($Escolas);

mysql_free_result($Turmas);

mysql_free_result($Alunos);
?>