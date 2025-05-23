<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "fnc/session.php"; ?>
<?php
if (!function_exists("GetSQLValueString")) {
  function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") {
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
SELECT escola_id, escola_nome, escola_logo, escola_tema, sec_cidade, sec_uf 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ListaFuncionarios = "
SELECT vinculo_id, vinculo_id_funcionario, vinculo_id_funcao, vinculo_carga_horaria, func_nome, funcao_nome 
FROM smc_vinculo 
INNER JOIN smc_func ON func_id = vinculo_id_funcionario 
INNER JOIN smc_funcao ON funcao_id = vinculo_id_funcao 
WHERE vinculo_id_escola = '$row_EscolaLogada[escola_id]' 
AND vinculo_status = 1 
ORDER BY func_nome ASC";
$ListaFuncionarios = mysql_query($query_ListaFuncionarios, $SmecelNovo) or die(mysql_error());
$row_ListaFuncionarios = mysql_fetch_assoc($ListaFuncionarios);
$totalRows_ListaFuncionarios = mysql_num_rows($ListaFuncionarios);

$mes = date('m');
$ano = date('Y');
setlocale(LC_TIME, "portuguese");
$nome_mes = strtoupper(strftime('%B'));

if (isset($_GET['mes'])) {
  $mes = $_GET['mes'];
  switch ($mes) {
    case 1: $nome_mes = "JANEIRO"; break;
    case 2: $nome_mes = "FEVEREIRO"; break;
    case 3: $nome_mes = "MARÇO"; break;
    case 4: $nome_mes = "ABRIL"; break;
    case 5: $nome_mes = "MAIO"; break;
    case 6: $nome_mes = "JUNHO"; break;
    case 7: $nome_mes = "JULHO"; break;
    case 8: $nome_mes = "AGOSTO"; break;
    case 9: $nome_mes = "SETEMBRO"; break;
    case 10: $nome_mes = "OUTUBRO"; break;
    case 11: $nome_mes = "NOVEMBRO"; break;
    case 12: $nome_mes = "DEZEMBRO"; break;
    default: header("Location: index.php"); break;
  }
}

$dias = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
$nome_mes = utf8_encode($nome_mes);
?>

<!DOCTYPE html>
<html class="<?php echo $row_EscolaLogada['escola_tema']; ?>" lang="pt-br">
<head>
  <title>FOLHA DE FREQUÊNCIA GERAL | MÊS: <?php echo $nome_mes; ?> | SMECEL - Sistema de Gestão Escolar</title>
  <meta charset="utf-8">
  <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
  <link rel="stylesheet" type="text/css" href="css/locastyle.css">
  <script src="js/locastyle.js"></script>

  <style media="print">
    .no_imp { display: none; }
    .pagebreak { page-break-before: always; }
  </style>
  <style>
    table.bordasimples { border-collapse: collapse; font-size: 7px; }
    table.bordasimples tr td { border: 1px dotted #000000; padding: 4px; font-size: 9px; }
    table.bordasimples tr th { border: 1px dotted #000000; padding: 3px; font-size: 9px; }
  </style>
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>

<body onload="alert('Atenção: Configure sua impressora para o formato PAISAGEM');self.print();">
  <div class="container-fluid">
    <?php if ($totalRows_ListaFuncionarios > 0) { ?>
      <?php $first = true; do { ?>
        <div class="ls-box ls-sm-space <?php if (!$first) echo 'pagebreak'; ?>" style="page-break-after: always;">
          <div class="ls-box">
            <span class="ls-float-right"><img src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt="" width="40px" /></span>
            <strong><?php echo $row_EscolaLogada['escola_nome']; ?></strong> - FOLHA DE FREQUÊNCIA GERAL<br>
            <br>
            <strong>SERVIDOR(A):</strong> <?php echo $row_ListaFuncionarios['func_nome']; ?> | 
            <strong>FUNÇÃO:</strong> <?php echo $row_ListaFuncionarios['funcao_nome']; ?> | 
            <strong>MÊS:</strong> <?php echo $nome_mes; ?><br>
            <div class="no_imp">
              <br><br>
              <div data-ls-module="dropdown" class="ls-dropdown">
                <a href="#" class="ls-btn"><?php echo $nome_mes; ?></a>
                <ul class="ls-dropdown-nav">
                  <li><a href="frequenciaGeralFuncionarios.php?mes=1">JANEIRO</a></li>
                  <li><a href="frequenciaGeralFuncionarios.php?mes=2">FEVEREIRO</a></li>
                  <li><a href="frequenciaGeralFuncionarios.php?mes=3">MARÇO</a></li>
                  <li><a href="frequenciaGeralFuncionarios.php?mes=4">ABRIL</a></li>
                  <li><a href="frequenciaGeralFuncionarios.php?mes=5">MAIO</a></li>
                  <li><a href="frequenciaGeralFuncionarios.php?mes=6">JUNHO</a></li>
                  <li><a href="frequenciaGeralFuncionarios.php?mes=7">JULHO</a></li>
                  <li><a href="frequenciaGeralFuncionarios.php?mes=8">AGOSTO</a></li>
                  <li><a href="frequenciaGeralFuncionarios.php?mes=9">SETEMBRO</a></li>
                  <li><a href="frequenciaGeralFuncionarios.php?mes=10">OUTUBRO</a></li>
                  <li><a href="frequenciaGeralFuncionarios.php?mes=11">NOVEMBRO</a></li>
                  <li><a href="frequenciaGeralFuncionarios.php?mes=12">DEZEMBRO</a></li>
                </ul>
              </div>
              <input type="button" value="IMPRIMIR" onclick="window.print()" class="ls-btn" />
            </div>
          </div>
          <br><br><br>
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
                $dia_nome = ($semana == 0) ? "DOMINGO" : (($semana == 6) ? "SÁBADO" : "");
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
              <?php $contador++; } ?>
            </tbody>
          </table>
        </div>
      <?php $first = false; } while ($row_ListaFuncionarios = mysql_fetch_assoc($ListaFuncionarios)); ?>
    <?php } else { ?>
      <div class="ls-box ls-sm-space">
        <p><div class="ls-alert-info"><strong>Atenção:</strong> Nenhum funcionário vinculado nessa escola.</div></p>
      </div>
    <?php } ?>
  </div>

  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="js/locastyle.js"></script>
</body>
</html>
<?php
mysql_free_result($UsuLogado);
mysql_free_result($EscolaLogada);
mysql_free_result($ListaFuncionarios);
?>