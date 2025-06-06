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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_FuncionariosPorFuncao = "
SELECT vinculo_id, vinculo_id_escola, vinculo_id_funcionario, vinculo_id_funcao, vinculo_carga_horaria, 
vinculo_data_inicio, vinculo_obs, funcao_id, funcao_secretaria_id, funcao_nome, COUNT(*) AS total_funcao 
FROM smc_vinculo
INNER JOIN smc_funcao ON funcao_id = vinculo_id_funcao
WHERE funcao_secretaria_id = '$row_UsuarioLogado[usu_sec]'
GROUP BY vinculo_id_funcao
ORDER BY funcao_nome ASC
";
$FuncionariosPorFuncao = mysql_query($query_FuncionariosPorFuncao, $SmecelNovo) or die(mysql_error());
$row_FuncionariosPorFuncao = mysql_fetch_assoc($FuncionariosPorFuncao);
$totalRows_FuncionariosPorFuncao = mysql_num_rows($FuncionariosPorFuncao);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_FuncionariosPorFuncaoG = "
SELECT vinculo_id, vinculo_id_escola, vinculo_id_funcionario, vinculo_id_funcao, vinculo_carga_horaria, 
vinculo_data_inicio, vinculo_obs, funcao_id, funcao_secretaria_id, funcao_nome, COUNT(*) AS total_funcao 
FROM smc_vinculo
INNER JOIN smc_funcao ON funcao_id = vinculo_id_funcao
WHERE funcao_secretaria_id = '$row_UsuarioLogado[usu_sec]'
GROUP BY vinculo_id_funcao
ORDER BY funcao_nome ASC
";
$FuncionariosPorFuncaoG = mysql_query($query_FuncionariosPorFuncaoG, $SmecelNovo) or die(mysql_error());
$row_FuncionariosPorFuncaoG = mysql_fetch_assoc($FuncionariosPorFuncaoG);
$totalRows_FuncionariosPorFuncaoG = mysql_num_rows($FuncionariosPorFuncaoG);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_VinculosPorEscola = "
SELECT vinculo_id, vinculo_id_escola, vinculo_id_funcionario, vinculo_id_funcao, vinculo_carga_horaria, 
vinculo_data_inicio, vinculo_obs vinculo_status, escola_id, escola_id_sec, escola_nome, escola_ue, func_id, func_id_sec, COUNT(*) AS total_funcionarios,
CASE escola_ue
WHEN 1 THEN 'UNIDADE ESCOLAR'
WHEN 0 THEN 'SETOR/DEPARTAMENTO'
END AS escola_ue_nome 
FROM smc_vinculo
INNER JOIN smc_escola ON escola_id = vinculo_id_escola
INNER JOIN smc_func ON func_id = vinculo_id_funcionario
WHERE escola_id_sec = '$row_UsuarioLogado[usu_sec]'
AND vinculo_status = 1
GROUP BY vinculo_id_escola
ORDER BY escola_nome ASC 
";
$VinculosPorEscola = mysql_query($query_VinculosPorEscola, $SmecelNovo) or die(mysql_error());
$row_VinculosPorEscola = mysql_fetch_assoc($VinculosPorEscola);
$totalRows_VinculosPorEscola = mysql_num_rows($VinculosPorEscola);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_FuncionariosPorEscolaG = "
SELECT vinculo_id, vinculo_id_escola, vinculo_id_funcionario, vinculo_id_funcao, vinculo_carga_horaria, 
vinculo_data_inicio, vinculo_obs vinculo_status, escola_id, escola_id_sec, escola_nome, escola_ue, func_id, func_id_sec, COUNT(*) AS total_funcionarios,
CASE escola_ue
WHEN 1 THEN 'UNIDADE ESCOLAR'
WHEN 0 THEN 'SETOR/DEPARTAMENTO'
END AS escola_ue_nome 
FROM smc_vinculo
INNER JOIN smc_escola ON escola_id = vinculo_id_escola
INNER JOIN smc_func ON func_id = vinculo_id_funcionario
WHERE escola_id_sec = '$row_UsuarioLogado[usu_sec]'
AND vinculo_status = 1
GROUP BY vinculo_id_escola
ORDER BY escola_nome ASC 
";
$FuncionariosPorEscolaG = mysql_query($query_FuncionariosPorEscolaG, $SmecelNovo) or die(mysql_error());
$row_FuncionariosPorEscolaG = mysql_fetch_assoc($FuncionariosPorEscolaG);
$totalRows_FuncionariosPorEscolaG = mysql_num_rows($FuncionariosPorEscolaG);
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
      <h1 class="ls-title-intro ls-ico-home">FUNCIONÁRIOS VINCULADOS</h1>

      <a href="index.php" class="ls-btn-primary">VOLTAR</a>


      <div data-ls-module="dropdown" class="ls-dropdown">
        <a href="#" class="ls-btn-primary">RELATÓRIOS</a>

        <ul class="ls-dropdown-nav">
          <li><a href="rel_func_regime.php">Por regime</a></li>
          <li><a href="rel_func_graduacao.php">Por escolaridade</a></li>
          <li><a href="rel_func_idade.php">Por idade</a></li>
          <li><a href="rel_func_vacinacao.php">Por situação vacinal</a></li>
          <li><a href="rel_func_contatos.php">Dados de contato</a></li>
          <li><a href="rel_func_naturalidade.php">Por local de nascimento</a></li>
          <li><a href="licencas_listar.php">Licenças/Afastamentos</a></li>
          <li><a href="professor_por_etapa.php">Professores vinculados por Etapa</a></li>
          <li><a href="rel_func_professores.php" target="_blank">Todos os professores</a></li>
          <li><a href="professor_por_componente.php">Professores vinculados por Componente</a></li>
          <li><a href="rel_func_tipo_sanguineo.php">Servidores por Tipo Sanguíneo</a></li>
        </ul>
      </div>


      <div class="ls-box ls-board-box">

        <header class="ls-info-header">
          <p class="ls-float-right ls-float-none-xs ls-small-info">Dados atualizados em
            <strong><?php echo date("d/m/Y"); ?> às <?php echo date("H\hi"); ?></strong>
          </p>
          <h2 class="ls-title-3">Servidores lotados por local</h2>
        </header>
        <div class="col-sm-12 col-md-6">
          <?php if ($totalRows_VinculosPorEscola > 0) { ?>
            <table class="ls-table ls-sm-space">
              <thead>
                <tr>
                  <th width="50"></th>
                  <th width="200">ESCOLA</th>
                  <th width="150" class="ls-txt-center">TIPO</th>
                  <th class="ls-txt-center">TOTAL</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $n = 1;
                do { ?>
                  <tr>
                    <td><?php echo $n;
                    $n++; ?></td>
                    <td><?php echo $row_VinculosPorEscola['escola_nome']; ?></td>
                    <td class="ls-txt-center"><?php echo $row_VinculosPorEscola['escola_ue_nome']; ?></td>
                    <td class="ls-txt-center"><a
                        href="funcionarios_vinculados_escola.php?escola=<?php echo $row_VinculosPorEscola['escola_id']; ?>"><?php echo $row_VinculosPorEscola['total_funcionarios']; ?></a>
                    </td>
                  </tr>
                <?php } while ($row_VinculosPorEscola = mysql_fetch_assoc($VinculosPorEscola)); ?>
              </tbody>
            </table>
          <?php } else { ?>
            <div class="ls-alert-info"><strong>Atenção:</strong> Sem funcionários vinculados.</div>
          <?php } ?>

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

                  ['<?php echo $row_FuncionariosPorEscolaG['escola_nome']; ?> (<?php echo $row_FuncionariosPorEscolaG['total_funcionarios']; ?>)', <?php echo $row_FuncionariosPorEscolaG['total_funcionarios']; ?>],

                <?php } while ($row_FuncionariosPorEscolaG = mysql_fetch_assoc($FuncionariosPorEscolaG)); ?>


              ]);
              var options = {
                'title': 'FUNCIONÁRIOS POR LOCAL',
                is3D: false,
                pieSliceText: 'percentage',
                fontSize: '10',
                legend: 'right',
                maxLines: 2
                // 'width':400,
                //'height':300
              };
              var chart = new google.visualization.PieChart(document.getElementById('chart_div_local'));
              chart.draw(data, options);
            }
          </script>

          <!-- CHART -->
          </script>
          <div id="chart_div_local" style="width:100%; height:600px;"></div>




        </div>
      </div>



      <div class="ls-box ls-board-box">

        <header class="ls-info-header">
          <h2 class="ls-title-3">Servidores lotados por função</h2>
        </header>

        <div class="col-sm-12 col-md-5">

          <?php if ($totalRows_FuncionariosPorFuncao > 0) { ?>
            <table class="ls-table ls-sm-space">
              <thead>
                <tr>
                  <th width="50"></th>
                  <th>FUNÇÃO</th>
                  <th class="ls-txt-center">TOTAL</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $n = 1;
                $total = 0;
                do { ?>
                  <tr>
                    <td class="ls-txt-center"><?php echo $n;
                    $n++; ?></td>
                    <td><?php echo $row_FuncionariosPorFuncao['funcao_nome']; ?></td>
                    <td class="ls-txt-center"><a target="_blank" href="funcionarios_vinculados_funcao.php?funcao=<?php echo $row_FuncionariosPorFuncao['funcao_id'] ?>"><?php echo $row_FuncionariosPorFuncao['total_funcao']; ?></a></td>
                  </tr>
                  <?php $total = $total + $row_FuncionariosPorFuncao['total_funcao']; ?>
                <?php } while ($row_FuncionariosPorFuncao = mysql_fetch_assoc($FuncionariosPorFuncao)); ?>
              </tbody>
            </table>
            <p>Total de funcionários: <?php echo $total; ?></p>

          <?php } else { ?>
            <div class="ls-alert-info"><strong>Atenção:</strong> Sem funcionários vinculados.</div>
          <?php } ?>

        </div>

        <div class="col-sm-12 col-md-7">


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

                  ['<?php echo $row_FuncionariosPorFuncaoG['funcao_nome']; ?> (<?php echo $row_FuncionariosPorFuncaoG['total_funcao']; ?>)', <?php echo $row_FuncionariosPorFuncaoG['total_funcao']; ?>],

                <?php } while ($row_FuncionariosPorFuncaoG = mysql_fetch_assoc($FuncionariosPorFuncaoG)); ?>


              ]);
              var options = {
                'title': 'FUNCIONÁRIOS POR FUNÇÃO',
                is3D: false,
                pieSliceText: 'percentage',
                fontSize: '10',
                legend: 'right',
                maxLines: 2
                // 'width':400,
                //'height':300
              };
              var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
              chart.draw(data, options);
            }
          </script>

          <!-- CHART -->
          </script>
          <div id="chart_div" style="width:100%; height:600px;"></div>




        </div>

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

mysql_free_result($FuncionariosPorFuncao);

mysql_free_result($VinculosPorEscola);
?>