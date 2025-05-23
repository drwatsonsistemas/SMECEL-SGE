<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php require_once('funcoes/anti_injection.php'); ?>
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
$query_Ano = "SELECT ano_letivo_id, ano_letivo_ano, ano_letivo_aberto, ano_letivo_id_sec FROM smc_ano_letivo WHERE ano_letivo_aberto = 'N' AND ano_letivo_id_sec = '$row_UsuarioLogado[usu_sec]' ORDER BY ano_letivo_ano DESC";
$Ano = mysql_query($query_Ano, $SmecelNovo) or die(mysql_error());
$row_Ano = mysql_fetch_assoc($Ano);
$totalRows_Ano = mysql_num_rows($Ano);

$anoLetivo = $row_AnoLetivo['ano_letivo_ano'];

if (isset($_GET['ano'])) {

  if ($_GET['ano'] == "") {
    //echo "TURMA EM BRANCO";	
    $anoLetivo = $row_AnoLetivo['ano_letivo_ano'];
  }

  $anoLetivo = anti_injection($_GET['ano']);
  $anoLetivo = (int) $anoLetivo;
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_matriculasTurno = "
SELECT 
  vinculo_aluno_situacao,
  COUNT(DISTINCT vinculo_aluno_id_aluno) as matriculas_total,
  CASE vinculo_aluno_situacao
    WHEN 1 THEN 'MATRÍCULA ATIVA' 
    WHEN 2 THEN 'TRANSFERIDO' 
    WHEN 3 THEN 'DESISTENTE' 
    WHEN 4 THEN 'FALECIDO' 
    WHEN 5 THEN 'OUTROS' 
    ELSE 'SEM DADOS'
  END AS matricula
FROM smc_vinculo_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
WHERE vinculo_aluno_id_sec = '$row_Secretaria[sec_id]' 
AND vinculo_aluno_ano_letivo = '$anoLetivo'
AND turma_tipo_atendimento = '1'
GROUP BY vinculo_aluno_situacao 
ORDER BY vinculo_aluno_situacao ASC";
$matriculasTurno = mysql_query($query_matriculasTurno, $SmecelNovo) or die(mysql_error());
$row_matriculasTurno = mysql_fetch_assoc($matriculasTurno);
$totalRows_matriculasTurno = mysql_num_rows($matriculasTurno);
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


  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript">
    google.charts.load('current', { 'packages': ['corechart'] });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {

      var data = google.visualization.arrayToDataTable([
        ['SITUAÇÃO DE MATRÍCULA', 'MATRÍCULAS'],


        <?php $contar = 0; ?>
  
  <?php do { ?>

          ['<?php echo $row_matriculasTurno['matricula']; ?> (<?php echo $row_matriculasTurno['matriculas_total']; ?>)', <?php echo $row_matriculasTurno['matriculas_total']; ?>],

          <?php $contar = $contar + $row_matriculasTurno['matriculas_total']; ?>
    
  <?php } while ($row_matriculasTurno = mysql_fetch_assoc($matriculasTurno)); ?>

      ]);

      var options = {
        title: 'TOTAL DE MATRÍCULAS POR SITUAÇÃO NO ANO LETIVO <?php echo $row_AnoLetivo['ano_letivo_ano']; ?>',
        pieHole: 0.4,
      };

      var chart = new google.visualization.PieChart(document.getElementById('piechart'));

      chart.draw(data, options);
    }
  </script>


</head>

<body>
  <?php include_once("menu_top.php"); ?>
  <?php include_once "menu.php"; ?>
  <main class="ls-main">
    <div class="container-fluid">
      <h1 class="ls-title-intro ls-ico-home">MATRÍCULAS POR SITUAÇÃO</h1>
      <!-- CONTEUDO -->

      <div class="ls-box-filter1">
        <div data-ls-module="dropdown" class="ls-dropdown ls-float-right1 ">
          <a href="#" class="ls-btn">ANO LETIVO: <?php echo $anoLetivo; ?></a>
          <ul class="ls-dropdown-nav">

            <li>
              <a href="graf_matriculas_por_situacao_matricula.php?ano=<?php echo $row_AnoLetivo['ano_letivo_ano'] ?><?php

                ?>" target="" title="Diários">
                ANO LETIVO <?php echo $row_AnoLetivo['ano_letivo_ano']; ?>
              </a>
            </li>

            <?php do { ?>
              <li>
                <a href="graf_matriculas_por_situacao_matricula.php?ano=<?php echo $row_Ano['ano_letivo_ano'] ?><?php

                  ?>" target="" title="Diários">
                  ANO LETIVO <?php echo $row_Ano['ano_letivo_ano']; ?>
                </a>
              </li>
            <?php } while ($row_Ano = mysql_fetch_assoc($Ano)); ?>

          </ul>
        </div>
      </div>





      <div id="piechart" style="width: 100%; height: 500px;"></div>
      <h3>Total de matrículas: <?php echo $contar; ?></h3>
      <p>&nbsp;</p>

      <div class="ls-txt-center">
        <a class="ls-btn ls-ico-windows" onclick="imprimirGrafico()" target="_blank">Imprimir</a>
      </div>

      <script>
        function imprimirGrafico() {
          // Cria uma nova janela para impressão
          var printWindow = window.open('', '_blank');

          // Obtém o conteúdo da div do gráfico
          var conteudo = document.getElementById('piechart').innerHTML;

          // Obtém o conteúdo do cabeçalho
          var cabecalho = `
        <table class="bordasimples1" width="100%">
            <tr>
                <td class="ls-txt-center" width="60"></td>
                <td class="ls-txt-center">
                    <?php if ($row_Secretaria['sec_logo'] <> "") { ?>
                        <img src="../../img/logo/secretaria/<?php echo $row_Secretaria['sec_logo']; ?>"
                            alt="Logo da <?php echo $row_Secretaria['sec_nome']; ?>"
                            title="Logo da <?php echo $row_Secretaria['sec_nome']; ?>" width="60" />
                    <?php } else { ?>
                        <img src="../../img/brasao_republica.png" width="60">
                    <?php } ?>
                    <h3><?php echo $row_Secretaria['sec_prefeitura']; ?></h3>
                    <?php echo $row_Secretaria['sec_nome']; ?>
                </td>
                <td class="ls-txt-center" width="60"></td>
            </tr>
        </table>
        <br>`;

          // Adiciona o conteúdo à nova janela
          printWindow.document.write(`
        <html>
        <head>
            <title>Gráfico de Matrículas</title>
            <link rel="stylesheet" type="text/css" href="css/locastyle.css">
            <link rel="stylesheet" type="text/css" href="../css/impressao.css">
            <style>
                body { margin: 20px; }
                @media print {
                    body { margin: 0; }
                }
            </style>
        </head>
        <body>
            ${cabecalho}
            ${conteudo}
            <script type="text/javascript">
                window.onload = function() {
                    window.print();
                    window.onafterprint = function() {
                        window.close();
                    }
                }
            <\/script>
        </body>
        </html>
    `);

          printWindow.document.close();
        }
      </script>


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

mysql_free_result($matriculasTurno);
?>