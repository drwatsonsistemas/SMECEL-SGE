<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include "fnc/session.php"; ?>
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
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_tema,  sec_id, sec_cidade, sec_uf 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

$colname_FrequenciaFuncionario = "-1";
if (isset($_GET['c'])) {
  $colname_FrequenciaFuncionario = $_GET['c'];
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_FrequenciaFuncionario = sprintf("
SELECT vinculo_id, vinculo_id_escola, vinculo_id_funcionario, vinculo_id_funcao, vinculo_carga_horaria, 
vinculo_data_inicio, vinculo_obs, vinculo_status, func_id, func_nome, funcao_id, funcao_nome 
FROM smc_vinculo
INNER JOIN smc_func ON vinculo_id_funcionario = func_id 
INNER JOIN smc_funcao ON vinculo_id_funcao = funcao_id 
WHERE vinculo_id_escola = '$row_EscolaLogada[escola_id]' 
AND vinculo_status = '1' AND vinculo_id = %s", GetSQLValueString($colname_FrequenciaFuncionario, "int"));
$FrequenciaFuncionario = mysql_query($query_FrequenciaFuncionario, $SmecelNovo) or die(mysql_error());
$row_FrequenciaFuncionario = mysql_fetch_assoc($FrequenciaFuncionario);
$totalRows_FrequenciaFuncionario = mysql_num_rows($FrequenciaFuncionario);

$mes = date('m');
$ano = date('Y');
setlocale(LC_TIME, "portuguese");
$nome_mes = strtoupper(strftime('%B'));

if (isset($_GET['mes'])) {

  $mes = $_GET['mes'];

  switch ($mes) {

    case 1:
      $nome_mes = "JANEIRO";
      break;

    case 2:
      $nome_mes = "FEVEREIRO";
      break;

    case 3:
      $nome_mes = "MARÇO";
      break;

    case 4:
      $nome_mes = "ABRIL";
      break;

    case 5:
      $nome_mes = "MAIO";
      break;

    case 6:
      $nome_mes = "JUNHO";
      break;

    case 7:
      $nome_mes = "JULHO";
      break;

    case 8:
      $nome_mes = "AGOSTO";
      break;

    case 9:
      $nome_mes = "SETEMBRO";
      break;

    case 10:
      $nome_mes = "OUTUBRO";
      break;

    case 11:
      $nome_mes = "NOVEMBRO";
      break;

    case 12:
      $nome_mes = "DEZEMBRO";
      break;

    default:
      header("Location: index.php");
      break;

  }

}


$dias = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

$nome_mes = utf8_encode($nome_mes);

?>
<!DOCTYPE html>
<html class="<?php echo $row_EscolaLogada['escola_tema']; ?>" lang="pt-br">

<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    gtag('js', new Date());

    gtag('config', 'UA-117872281-1');
  </script>
  <title>FOLHA DE FREQUENCIA | SERVIDOR(A): <?php echo $row_FrequenciaFuncionario['func_nome']; ?> | MÊS:
    <?php echo $nome_mes; ?> | SMECEL - Sistema de Gestão Escolar</title>
  <meta charset="utf-8">
  <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
  <meta name="description" content="">
  <meta name="keywords" content="">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <link rel="stylesheet" type="text/css" href="css/locastyle.css">
  <script src="js/locastyle.js"></script>

  <style media="print">
    .no_imp {
      display: none;
    }

    .pagebreak {
      page-break-before: always;
    }
  </style>


  <style>
    table.bordasimples {
      border-collapse: collapse;
      font-size: 7px;
    }

    table.bordasimples tr td {
      border: 1px dotted #000000;
      padding: 4px;
      font-size: 9px;
    }

    table.bordasimples tr th {
      border: 1px dotted #000000;
      padding: 3px;
      font-size: 9px;
    }
  </style>
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>

<body onload="alert('Atenção: Configure sua impressora para o formato PAISAGEM');1self.print();">
  <div class="container-fluid">
    <!-- CONTEÚDO -->

    <div class="ls-box1 ls-sm-space" style="page-break-after: always;">
      <div class="ls-box1"> <span class="ls-float-right"><img
            src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt="" width="40px" /></span>
        <strong><strong><?php echo $row_EscolaLogada['escola_nome']; ?></strong> - FOLHA DE FREQUENCIA</strong><br>
        <br>
        <strong> SERVIDOR(A):</strong> <?php echo $row_FrequenciaFuncionario['func_nome']; ?> | <strong> FUNÇÃO:
        </strong> <?php echo $row_FrequenciaFuncionario['funcao_nome']; ?> | <strong>MES: </strong>
        <?php
        echo $nome_mes;
        ?>
        <br>
        <div class="no_imp">
          <br><br>
          <div data-ls-module="dropdown" class="ls-dropdown"> <a href="#" class="ls-btn"><?php echo $nome_mes; ?></a>
            <ul class="ls-dropdown-nav">
              <li><a
                  href="frequenciaFuncionarios.php?c=<?php echo $row_FrequenciaFuncionario['vinculo_id']; ?>&mes=1">JANEIRO</a>
              </li>
              <li><a
                  href="frequenciaFuncionarios.php?c=<?php echo $row_FrequenciaFuncionario['vinculo_id']; ?>&mes=2">FEVEREIRO</a>
              </li>
              <li><a
                  href="frequenciaFuncionarios.php?c=<?php echo $row_FrequenciaFuncionario['vinculo_id']; ?>&mes=3">MARÇO</a>
              </li>
              <li><a
                  href="frequenciaFuncionarios.php?c=<?php echo $row_FrequenciaFuncionario['vinculo_id']; ?>&mes=4">ABRIL</a>
              </li>
              <li><a
                  href="frequenciaFuncionarios.php?c=<?php echo $row_FrequenciaFuncionario['vinculo_id']; ?>&mes=5">MAIO</a>
              </li>
              <li><a
                  href="frequenciaFuncionarios.php?c=<?php echo $row_FrequenciaFuncionario['vinculo_id']; ?>&mes=6">JUNHO</a>
              </li>
              <li><a
                  href="frequenciaFuncionarios.php?c=<?php echo $row_FrequenciaFuncionario['vinculo_id']; ?>&mes=7">JULHO</a>
              </li>
              <li><a
                  href="frequenciaFuncionarios.php?c=<?php echo $row_FrequenciaFuncionario['vinculo_id']; ?>&mes=8">AGOSTO</a>
              </li>
              <li><a
                  href="frequenciaFuncionarios.php?c=<?php echo $row_FrequenciaFuncionario['vinculo_id']; ?>&mes=9">SETEMBRO</a>
              </li>
              <li><a
                  href="frequenciaFuncionarios.php?c=<?php echo $row_FrequenciaFuncionario['vinculo_id']; ?>&mes=10">OUTUBRO</a>
              </li>
              <li><a
                  href="frequenciaFuncionarios.php?c=<?php echo $row_FrequenciaFuncionario['vinculo_id']; ?>&mes=11">NOVEMBRO</a>
              </li>
              <li><a
                  href="frequenciaFuncionarios.php?c=<?php echo $row_FrequenciaFuncionario['vinculo_id']; ?>&mes=12">DEZEMBRO</a>
              </li>
            </ul>
          </div>
          <input type="button" value="IMPRIMIR" onclick="window.print()" class="ls-btn" />

        </div>
      </div>
      <br>
      <br>
      <br>
      <table class="ls-sm-space bordasimples" width="100%" style="font-size:9px;">
        <thead>
          <tr>
            <th colspan="3"></th>
            <th colspan="2">REFEIÇÃO/DESCANSO</th>
            <th colspan="3"></th>
            <th colspan="2">PRORROGAÇÃO</th>
            <th colspan="2"></th>
          </tr>
          <tr>
            <th width="3%" class="ls-txt-center">DIA</th>
            <th width="4%">ENTRADA</th>
            <th>ASSINATURA</th>
            <th width="4%">SAÍDA</th>
            <th width="4%">ENTRADA</th>
            <th>ASSINATURA</th>
            <th width="4%">SAÍDA</th>
            <th>ASSINATURA</th>
            <th width="4%">ENTRADA</th>
            <th width="4%">SAÍDA</th>
            <th>ASSINATURA</th>
            <th width="4%">TOTAL</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $contador = 1;
          while ($contador <= $dias) {
            $dataaa = $ano . "-" . $mes . "-" . str_pad($contador, 2, "0", STR_PAD_LEFT);
            $semana = date('w', strtotime($dataaa));
            $dia_nome = "";

            if ($semana == 0) {
              $dia_nome = "DOMINGO";
            } elseif ($semana == 6) {
              $dia_nome = "SÁBADO";
            }
            ?>
            <tr>
              <td class="ls-txt-center"><?php echo $contador; ?></td>
              <td class="ls-txt-center">:</td>
              <td class="ls-txt-center"><?php echo $dia_nome; ?></td>
              <td class="ls-txt-center">:</td>
              <td class="ls-txt-center">:</td>
              <td class="ls-txt-center"><?php echo $dia_nome; ?></td>
              <td class="ls-txt-center">:</td>
              <td class="ls-txt-center"><?php echo $dia_nome; ?></td>
              <td class="ls-txt-center">:</td>
              <td class="ls-txt-center">:</td>
              <td class="ls-txt-center"><?php echo $dia_nome; ?></td>
              <td class="ls-txt-center"></td>
            </tr>
            <?php
            $contador++;
          }
          ?>
        </tbody>
      </table>

    </div>

    <!-- CONTEÚDO -->
  </div>
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
        <li class="ls-txt-center hidden-xs"> <a href="tutoriais_video.php" class="ls-btn-dark ls-btn-tour">Tutorial
            (Vídeos)</a> </li>
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
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($FrequenciaFuncionario);
?>