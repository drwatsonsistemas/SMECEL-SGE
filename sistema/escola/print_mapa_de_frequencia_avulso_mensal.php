<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php // include "fnc/anoLetivo.php"; ?>
<?php include('fnc/inverteData.php'); ?>
<?php include 'fnc/anti_injection.php'; ?>
<?php // include('fnc/notas.php'); ?>
<?php include('../funcoes/url_base.php'); ?>
<?php include 'fnc/calculos.php'; ?>
<?php include "fnc/session.php"; ?>
<?php
if (!function_exists('GetSQLValueString')) {
  function GetSQLValueString($theValue, $theType, $theDefinedValue = '', $theNotDefinedValue = '')
  {
    if (PHP_VERSION < 6) {
      $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
    }

    $theValue = function_exists('mysql_real_escape_string') ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

    switch ($theType) {
      case 'text':
        $theValue = ($theValue != '') ? "'" . $theValue . "'" : 'NULL';
        break;
      case 'long':
      case 'int':
        $theValue = ($theValue != '') ? intval($theValue) : 'NULL';
        break;
      case 'double':
        $theValue = ($theValue != '') ? doubleval($theValue) : 'NULL';
        break;
      case 'date':
        $theValue = ($theValue != '') ? "'" . $theValue . "'" : 'NULL';
        break;
      case 'defined':
        $theValue = ($theValue != '') ? $theDefinedValue : $theNotDefinedValue;
        break;
    }
    return $theValue;
  }
}

$codTurma = '';
$buscaTurma = '';
if (isset($_GET['ct'])) {
  if ($_GET['ct'] == '') {
    // echo "TURMA EM BRANCO";
    header('Location: turmasAlunosVinculados.php?nada');
    exit;
  }

  if ($_GET['mes'] >= '13' or $_GET['mes'] <= 0) {
    header('Location: turmasAlunosVinculados.php?nada');
    exit;
  }

  if ($_GET['ano'] <= 1999) {
    header('Location: turmasAlunosVinculados.php?nada');
    exit;
  }

  $codTurma = anti_injection($_GET['ct']);
  $codTurma = (int) $codTurma;
  $buscaTurma = "AND turma_id = $codTurma ";
}

include 'usuLogado.php';
include 'fnc/anoLetivo.php';

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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AlunoBoletim = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia,
aluno_id, aluno_nome, aluno_nascimento, aluno_filiacao1, aluno_foto, aluno_hash,
turma_id, turma_nome, turma_matriz_id, turma_turno, turma_etapa, 
CASE vinculo_aluno_situacao
WHEN 1 THEN 'MATRICULADO'
WHEN 2 THEN 'TRANSFERIDO'
WHEN 3 THEN 'DESISTENTE'
WHEN 4 THEN 'FALECIDO'
WHEN 5 THEN 'OUTROS'
END AS vinculo_aluno_situacao_nome
FROM 
smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma 
WHERE vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND vinculo_aluno_id_escola = '$row_UsuLogado[usu_escola]' $buscaTurma
ORDER BY turma_turno ASC, turma_etapa ASC, turma_nome ASC, aluno_nome ASC";
$AlunoBoletim = mysql_query($query_AlunoBoletim, $SmecelNovo) or die(mysql_error());
$row_AlunoBoletim = mysql_fetch_assoc($AlunoBoletim);
$totalRows_AlunoBoletim = mysql_num_rows($AlunoBoletim);

if ($totalRows_AlunoBoletim == '') {
  // echo "TURMA EM BRANCO";
  // header("Location: turmasAlunosVinculados.php?nada");

  echo '<h3><center>Sem dados.<br><a href="javascript:window.close()">Fechar</a></center></h3>';
  echo '';

  exit;
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matriz = "SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash, matriz_dias_letivos, matriz_semanas_letivas, matriz_dias_semana, matriz_minutos_aula, matriz_aula_dia, matriz_criterio_avaliativo FROM smc_matriz WHERE matriz_id = '$row_AlunoBoletim[turma_matriz_id]'";
$Matriz = mysql_query($query_Matriz, $SmecelNovo) or die(mysql_error());
$row_Matriz = mysql_fetch_assoc($Matriz);
$totalRows_Matriz = mysql_num_rows($Matriz);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_CriteriosAvaliativos = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes FROM smc_criterios_avaliativos WHERE ca_id = '$row_Matriz[matriz_criterio_avaliativo]'";
$CriteriosAvaliativos = mysql_query($query_CriteriosAvaliativos, $SmecelNovo) or die(mysql_error());
$row_CriteriosAvaliativos = mysql_fetch_assoc($CriteriosAvaliativos);
$totalRows_CriteriosAvaliativos = mysql_num_rows($CriteriosAvaliativos);

function diffMonth($from, $to)
{
  $fromYear = date('Y', strtotime($from));
  $fromMonth = date('m', strtotime($from));
  $toYear = date('Y', strtotime($to));
  $toMonth = date('m', strtotime($to));
  if ($fromYear == $toYear) {
    return ($toMonth - $fromMonth) + 1;
  } else {
    return (12 - $fromMonth) + 1 + $toMonth;
  }
}

function nomeMes($numero)
{
  switch ($numero) {
    case 1:
      $nomeMes = 'JANEIRO';
      break;
    case 2:
      $nomeMes = 'FEVEREIRO';
      break;
    case 3:
      $nomeMes = 'MARÇO';
      break;
    case 4:
      $nomeMes = 'ABRIL';
      break;
    case 5:
      $nomeMes = 'MAIO';
      break;
    case 6:
      $nomeMes = 'JUNHO';
      break;
    case 7:
      $nomeMes = 'JULHO';
      break;
    case 8:
      $nomeMes = 'AGOSTO';
      break;
    case 9:
      $nomeMes = 'SETEMBRO';
      break;
    case 10:
      $nomeMes = 'OUTUBRO';
      break;
    case 11:
      $nomeMes = 'NOVEMBRO';
      break;
    case 12:
      $nomeMes = 'DEZEMBRO';
      break;
  }

  return $nomeMes;
}

$mes = $_GET['mes'];
$ano = $_GET['ano'];
$numero_de_dias = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

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
  <style>
    html {
      -webkit-print-color-adjust: exact;
    }

    table.bordasimples {
      border-collapse: collapse;
      font-size: 7px;
    }

    table.bordasimples tr td {
      border: 1px solid #808080;
      padding: 2px;
      font-size: 12px;
    }

    table.bordasimples tr th {
      border: 1px solid #808080;
      padding: 2px;
      font-size: 9px;
    }

    .aluno {
      background-color: #ddd;
      border-radius: 0%;
      height: 70px;
      object-fit: cover;
      width: 70px;
    }
  </style>
</head>

<body onload="alert('Atenção: Configure sua impressora para o formato HORIZONTAL');self.print();">

  <div class="container-fluid1">

    <div style="page-break-inside: avoid;">
      <table>
        <tr>
          <td width="150" class="ls-txt-center"><?php if ($row_EscolaLogada['escola_logo'] <> '') { ?>
              <img src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt="" width="70%" />
            <?php } else { ?>
              <img src="../../img/brasao_republica.png" alt="" width="70%" />
            <?php } ?>
            <br>
          </td>
          <td class="ls-txt-left"><strong><?php echo $row_EscolaLogada['escola_nome']; ?></strong><br>
            <small>INEP: <?php echo $row_EscolaLogada['escola_inep']; ?> AUT: - D.O. -<br>
              ENDEREÇO: <?php echo $row_EscolaLogada['escola_endereco']; ?>,
              <?php echo $row_EscolaLogada['escola_num']; ?>, <?php echo $row_EscolaLogada['escola_bairro']; ?>
              <?php echo $row_EscolaLogada['sec_cidade']; ?>-<?php echo $row_EscolaLogada['sec_uf']; ?> CEP:
              <?php echo $row_EscolaLogada['escola_cep']; ?><br>
              CNPJ: <?php echo $row_EscolaLogada['escola_cnpj']; ?> <?php echo $row_EscolaLogada['escola_email']; ?>
              <?php echo $row_EscolaLogada['escola_telefone1']; ?></small>
          </td>
        </tr>
      </table>



      <h3 class="ls-txt-center">MAPA DE FREQUÊNCIA MENSAL |
        <?php echo nomeMes($mes) . "/" . $row_AlunoBoletim['vinculo_aluno_ano_letivo']; ?>
      </h3>
      <h2 class="ls-txt-center"><?php echo $row_AlunoBoletim['turma_nome']; ?></h2>

      <br>



      <table width="100%" class="ls-sm-space bordasimples" cellpadding="0" cellspacing="0" border="0">
        <br>
        <tr class="ls-txt-center">
          <td>N°</td>
          <th width="20%">Nome</th>
          <?php
          for ($i = 1; $i <= $numero_de_dias; $i++) {
            if ($i < 10) {
              $i = '0' . $i;
            }
            ?>
            <th><?php echo $i ?></th>
          <?php } ?>
        </tr>

        <?php
        $count = 0;
        do {
          $count++;
          ?>
          <tr>
            <td class="ls-txt-center"><?php echo $count ?></td>
            <td class="ls-txt-left"><?= $row_AlunoBoletim['aluno_nome'] ?></td>
            <?php
            if ($row_AlunoBoletim['vinculo_aluno_situacao'] <> "1") {
              // Aluno não ativo: exibe o status com colspan cobrindo todos os dias
              echo '<td align="center" style="border:1px solid #808080; border-right:1px solid #808080; letter-spacing:15px; font-size:9px;" colspan="' . $numero_de_dias . '">' . $row_AlunoBoletim['vinculo_aluno_situacao_nome'] . '</td>';
            } else {
              // Aluno ativo: exibe uma célula para cada dia
              for ($i = 1; $i <= $numero_de_dias; $i++) {
                $data = "$ano-$mes-$i";
                $dia_da_semana = date('w', strtotime($data));
                $e_fim_de_semana = ($dia_da_semana == 0 || $dia_da_semana == 6); // Domingo (0) ou Sábado (6)
                if ($e_fim_de_semana) {
                  echo '<td class="ls-txt-center" style="background:#ffecff;font-size:10px;padding:0px">' . ($dia_da_semana == 0 ? 'D' : 'S') . '</td>';
                } else {
                  echo '<td></td>';
                }
              }
            }
            ?>
          </tr>
        <?php } while ($row_AlunoBoletim = mysql_fetch_assoc($AlunoBoletim)); ?>
      </table>



    </div>




    <!-- CONTEÚDO -->
  </div>

  <!-- We recommended use jQuery 1.10 or up -->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="js/locastyle.js"></script>
</body>

</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($AlunoBoletim);

mysql_free_result($CriteriosAvaliativos);

mysql_free_result($Matriz);
?>