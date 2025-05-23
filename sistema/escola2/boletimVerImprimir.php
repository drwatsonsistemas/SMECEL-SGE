<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include('fnc/inverteData.php'); ?>
<?php include "fnc/calculos.php"; ?>
<?php include('../funcoes/url_base.php'); ?>
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

$colname_matricula = "-1";
if (isset($_GET['c'])) {
  $colname_matricula = $_GET['c'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_matricula = sprintf("SELECT 
  vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, 
  vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, 
  vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, 
  vinculo_aluno_vacina_atualizada, aluno_id, aluno_nome, aluno_nome_social, aluno_nascimento, aluno_foto, aluno_filiacao1, aluno_hash, turma_id, turma_nome, turma_turno 
  FROM smc_vinculo_aluno 
  INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
  INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
  WHERE vinculo_aluno_hash = %s", GetSQLValueString($colname_matricula, "text"));
$matricula = mysql_query($query_matricula, $SmecelNovo) or die(mysql_error());
$row_matricula = mysql_fetch_assoc($matricula);
$totalRows_matricula = mysql_num_rows($matricula);

if ($totalRows_matricula == 0) {
  header("Location:turmaListar.php?nada");
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_turma = "SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo FROM smc_turma WHERE turma_id = '$row_matricula[vinculo_aluno_id_turma]'";
$turma = mysql_query($query_turma, $SmecelNovo) or die(mysql_error());
$row_turma = mysql_fetch_assoc($turma);
$totalRows_turma = mysql_num_rows($turma);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_matriz = "SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash, matriz_dias_letivos, matriz_semanas_letivas, matriz_dias_semana, matriz_minutos_aula, matriz_aula_dia, matriz_criterio_avaliativo FROM smc_matriz WHERE matriz_id = '$row_turma[turma_matriz_id]'";
$matriz = mysql_query($query_matriz, $SmecelNovo) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_disciplinasMatriz = "
SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_reprova, matriz_disciplina_id_disciplina, disciplina_id, disciplina_nome 
FROM smc_matriz_disciplinas
INNER JOIN smc_disciplina ON disciplina_id = matriz_disciplina_id_disciplina 
WHERE matriz_disciplina_id_matriz = '$row_turma[turma_matriz_id]'";
$disciplinasMatriz = mysql_query($query_disciplinasMatriz, $SmecelNovo) or die(mysql_error());
$row_disciplinasMatriz = mysql_fetch_assoc($disciplinasMatriz);
$totalRows_disciplinasMatriz = mysql_num_rows($disciplinasMatriz);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_criteriosAvaliativos = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes, ca_rec_paralela, ca_digitos FROM smc_criterios_avaliativos WHERE ca_id = '$row_matriz[matriz_criterio_avaliativo]'";
$criteriosAvaliativos = mysql_query($query_criteriosAvaliativos, $SmecelNovo) or die(mysql_error());
$row_criteriosAvaliativos = mysql_fetch_assoc($criteriosAvaliativos);
$totalRows_criteriosAvaliativos = mysql_num_rows($criteriosAvaliativos);

$rec = 0;
if ($row_criteriosAvaliativos['ca_rec_paralela'] == "S") {
  $rec = 1;
}

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
  <title>
    <?php echo "BOLETIM INDIVIDUAL $row_matricula[vinculo_aluno_ano_letivo] - $row_matricula[aluno_nome] - $row_matricula[turma_nome] - $row_EscolaLogada[escola_nome]" ?>
  </title>
  <meta charset="utf-8">
  <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
  <meta name="description" content="">
  <meta name="keywords" content="">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <link rel="stylesheet" type="text/css" href="css/locastyle.css">
  <link rel="stylesheet" type="text/css" href="css/preloader.css">
  <script src="js/locastyle.js"></script>
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<style>
  table.bordasimples {
    border-collapse: collapse;
    font-size: 11px;
  }

  table.bordasimples tr td {
    border: 1px solid #808080;
    padding: 3px;
    font-size: 11px;
  }

  table.bordasimples tr th {
    border: 1px solid #808080;
    padding: 3px;
    font-size: 11px;
  }
</style>

<body onLoad="print()">

  <!-- CONTEÚDO -->


  <p>


    <span class="ls-float-right" style="margin-left:20px;">
      <?php if ($row_matricula['aluno_foto'] == "") { ?>
        <img src="<?php echo URL_BASE . 'aluno/fotos/' ?>semfoto.jpg" style="margin:1mm;width:15mm;">
      <?php } else { ?>
        <img src="<?php echo URL_BASE . 'aluno/fotos/' ?><?php echo $row_matricula['aluno_foto']; ?>"
          style="margin:1mm;width:15mm;">
      <?php } ?>
    </span> <span class="ls-float-left" style="margin-right:20px;"> <img
        src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt="" width="60px" /></span>
    <?php echo $row_EscolaLogada['escola_nome']; ?><br>
    <small> Aluno(a):
      <strong><?php if ($row_matricula['aluno_nome_social'] == '') {
        echo $row_matricula['aluno_nome'];
      } else {
        echo $row_matricula['aluno_nome_social'];
      } ?></strong><br>
      Nascimento: <strong><?php echo inverteData($row_matricula['aluno_nascimento']); ?></strong><br>
      Filiação: <strong><?php echo $row_matricula['aluno_filiacao1']; ?></strong><br>
      Turma: <strong><?php echo $row_matricula['turma_nome']; ?></strong> </small> </div>
  </p>
  <p class="ls-ico-text ls-txt-center">BOLETIM ESCOLAR <?php echo $row_matricula['vinculo_aluno_ano_letivo']; ?></p>


  <?php if (isset($_GET["boletimcadastrado"])) { ?>
    <p>
    <div class="ls-alert-success ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> Boletim
      gerado com sucesso. </div>
    </p>
  <?php } ?>
  <table class="ls-table1 ls-sm-space bordasimples" width="100%">
    <thead>
      <tr height="30">
        <td width="150"></td>
        <?php for ($p = 1; $p <= $row_criteriosAvaliativos['ca_qtd_periodos']; $p++) { ?>
          <th class="ls-txt-center" style="background-color:#F5F5F5;"
            colspan="<?php echo $row_criteriosAvaliativos['ca_qtd_av_periodos'] + $rec; ?>"><?php echo $p; ?>ª UNIDADE</th>
          <th></th>
        <?php } ?>
        <th colspan="4" class="ls-txt-center" style="background-color:#F5F5F5;">RESULTADO</th>
      </tr>
      <tr height="30">
        <th class="ls-txt-center" width="200">COMPONENTES CURRICULARES</th>
        <?php for ($p = 1; $p <= $row_criteriosAvaliativos['ca_qtd_periodos']; $p++) { ?>
          <?php for ($a = 1; $a <= $row_criteriosAvaliativos['ca_qtd_av_periodos']; $a++) { ?>
            <th class="ls-txt-center" width="40"> AV<?php echo $a; ?> </th>
          <?php } ?>
          <?php if ($row_criteriosAvaliativos['ca_rec_paralela'] == "S") { ?>
            <th class="ls-txt-center" width="40">RP</th><?php } ?>
          <th class="ls-txt-center" width="40">RU</th>
        <?php } ?>
        <th class="ls-txt-center" width="40">TP</th>
        <th class="ls-txt-center" width="40">MC</th>
        <th class="ls-txt-center" width="40">AF</th>
        <th class="ls-txt-center" width="60">RF</th>
      </tr>
    </thead>
    <tbody>
      <?php do { ?>
        <tr>

          <td width="150"><?php echo $row_disciplinasMatriz['disciplina_nome']; ?></td>

          <?php $tmu = 0; ?>
          <?php for ($p = 1; $p <= $row_criteriosAvaliativos['ca_qtd_periodos']; $p++) { ?>
            <?php $ru = 0; ?>
            <?php for ($a = 1; $a <= $row_criteriosAvaliativos['ca_qtd_av_periodos']; $a++) { ?>

              <td class="ls-txt-center" width="50">
                <?php
                mysql_select_db($database_SmecelNovo, $SmecelNovo);
                $query_nota = "SELECT nota_id, nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_max, nota_min, nota_valor, nota_hash FROM smc_nota WHERE nota_id_matricula = '$row_matricula[vinculo_aluno_id]' AND nota_id_disciplina = $row_disciplinasMatriz[disciplina_id] AND nota_periodo = '$p' AND nota_num_avaliacao = '$a'";
                $nota = mysql_query($query_nota, $SmecelNovo) or die(mysql_error());
                $row_nota = mysql_fetch_assoc($nota);
                $totalRows_nota = mysql_num_rows($nota);
                echo exibeTraco($row_nota['nota_valor'], $row_criteriosAvaliativos['ca_nota_min_av'], $row_criteriosAvaliativos['ca_digitos']);
                $ru = $ru + $row_nota['nota_valor'];
                ?>
              </td>
            <?php } ?>

            <?php

            if ($row_criteriosAvaliativos['ca_rec_paralela'] == "S") {

              mysql_select_db($database_SmecelNovo, $SmecelNovo);
              $query_notaRecPar = "SELECT nota_id, nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_max, nota_min, nota_valor, nota_hash FROM smc_nota WHERE nota_id_matricula = '$row_matricula[vinculo_aluno_id]' AND nota_id_disciplina = $row_disciplinasMatriz[disciplina_id] AND nota_periodo = '$p' AND nota_num_avaliacao = '98'";
              $notaRecPar = mysql_query($query_notaRecPar, $SmecelNovo) or die(mysql_error());
              $row_notaRecPar = mysql_fetch_assoc($notaRecPar);
              $totalRows_notaRecPar = mysql_num_rows($notaRecPar);

              ?>
              <th class="ls-txt-center" width="50">
                <?php echo exibeTraco($row_notaRecPar['nota_valor'], $row_criteriosAvaliativos['ca_nota_min_av']); ?></th>
            <?php } ?>

            <td class="ls-txt-center ls-background-info" width="50"><strong>
                <?php $mu = mediaUnidade($ru, $row_criteriosAvaliativos['ca_arredonda_media'], $row_criteriosAvaliativos['ca_aproxima_media'], $row_criteriosAvaliativos['ca_media_min_periodo'], $row_criteriosAvaliativos['ca_calculo_media_periodo'], $row_criteriosAvaliativos['ca_qtd_av_periodos'], $row_criteriosAvaliativos['ca_digitos']); ?>
                <?php $tmu = $tmu + $mu; ?>
              </strong>
            </td>

          <?php } ?>

          <td class="ls-txt-center" width="50"><strong>
              <?php $tp = totalPontos($tmu, $row_criteriosAvaliativos['ca_digitos']); ?>
            </strong>
          </td>

          <td class="ls-txt-center"><strong>

              <?php
              $mc = mediaCurso($tp, $row_criteriosAvaliativos['ca_arredonda_media'], $row_criteriosAvaliativos['ca_aproxima_media'], $row_criteriosAvaliativos['ca_min_media_aprovacao_final'], $row_criteriosAvaliativos['ca_qtd_periodos'], $row_criteriosAvaliativos['ca_digitos']);
              ?>

            </strong>
          </td>

          <td class="ls-txt-center"><strong> <a href="#">
                <?php
                mysql_select_db($database_SmecelNovo, $SmecelNovo);
                $query_notaAf = "SELECT nota_id, nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_max, nota_min, nota_valor, nota_hash FROM smc_nota WHERE nota_id_matricula = '$row_matricula[vinculo_aluno_id]' AND nota_id_disciplina = $row_disciplinasMatriz[disciplina_id] AND nota_periodo = '99' AND nota_num_avaliacao = '99'";
                $notaAf = mysql_query($query_notaAf, $SmecelNovo) or die(mysql_error());
                $row_notaAf = mysql_fetch_assoc($notaAf);
                $totalRows_notaAf = mysql_num_rows($notaAf);
                echo $af = avaliacaoFinal($row_notaAf['nota_valor'], $row_criteriosAvaliativos['ca_nota_min_recuperacao_final'],$row_criteriosAvaliativos["ca_digitos"]);
                ?>
              </a> </strong></td>
          <td class="ls-txt-center">
            <?php
            if (($row_AnoLetivo['ano_letivo_resultado_final'] <= date("Y-m-d")) && $row_AnoLetivo['ano_letivo_resultado_final'] <> "") {
              if ($row_disciplinasMatriz['matriz_disciplina_reprova'] == "S") {
                echo resultadoFinal($mc, $af, $row_criteriosAvaliativos['ca_nota_min_recuperacao_final'], $row_criteriosAvaliativos['ca_min_media_aprovacao_final'], $row_criteriosAvaliativos['ca_digitos']);
              } else {
                echo "**";
              }
            } else {
              echo "-";
            }

            ?>
          </td>
        </tr>
      <?php } while ($row_disciplinasMatriz = mysql_fetch_assoc($disciplinasMatriz)); ?>
    </tbody>

  </table>

  <small class="ls-float-right">
    <?php if (($row_AnoLetivo['ano_letivo_resultado_final'] <= date("Y-m-d")) && $row_AnoLetivo['ano_letivo_resultado_final'] <> "") {
      echo "<strong>Resultado Final disponível.</strong> Verifique sua situação em cada Componente Curricular.";
    } else if ($row_AnoLetivo['ano_letivo_resultado_final'] == "") {
      echo "A data de divulgação do Resultado Final (RF) ainda será definida pela escola.";
    } else {
      echo "Resultado Final (RF) estará disponível à partir do dia " . date("d/m/Y", strtotime(($row_AnoLetivo['ano_letivo_resultado_final'])));
    } ?>
  </small>

  <br>

  <div class="ls-box ls-txt-center">
    <strong>DADOS DE ACESSO AO PAINEL DO ALUNO</strong>
    <table width="100%" class="ls-sm-space bordasimples">
      <tr>
        <td>Data de Nascimento: <strong><?php echo inverteData($row_matricula['aluno_nascimento']); ?></strong></td>
        <td>Código de acesso: <strong><?php echo str_pad($row_matricula['aluno_id'], 5, '0', STR_PAD_LEFT); ?></strong>
        </td>
        <td>Senha de acesso: <strong><?php echo substr($row_matricula['aluno_hash'], 0, 5); ?></strong><br></td>
      </tr>
    </table>
    <small><i>Acesse o site www.smecel.com.br, clique em "Área do Aluno" e informe os dados acima</i></small>

  </div>


  <!-- We recommended use jQuery 1.10 or up -->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="js/locastyle.js"></script>

  <script type="text/javascript">


  </script>
</body>

</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($matricula);

mysql_free_result($turma);

mysql_free_result($disciplinasMatriz);

mysql_free_result($criteriosAvaliativos);

mysql_free_result($nota);

mysql_free_result($matriz);

mysql_free_result($EscolaLogada);
?>