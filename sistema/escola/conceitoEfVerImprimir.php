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
  vinculo_aluno_vacina_atualizada, aluno_id, aluno_nome, aluno_nascimento, aluno_foto, aluno_hash, aluno_filiacao1, turma_id, turma_nome 
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
SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_id_disciplina, disciplina_id, disciplina_nome 
FROM smc_matriz_disciplinas
INNER JOIN smc_disciplina ON disciplina_id = matriz_disciplina_id_disciplina 
WHERE matriz_disciplina_id_matriz = '$row_turma[turma_matriz_id]'";
$disciplinasMatriz = mysql_query($query_disciplinasMatriz, $SmecelNovo) or die(mysql_error());
$row_disciplinasMatriz = mysql_fetch_assoc($disciplinasMatriz);
$totalRows_disciplinasMatriz = mysql_num_rows($disciplinasMatriz);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_criteriosAvaliativos = "
SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, 
ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, 
ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes, ca_grupo_conceito, ca_grupo_etario  
FROM smc_criterios_avaliativos 
WHERE ca_id = '$row_matriz[matriz_criterio_avaliativo]'";
$criteriosAvaliativos = mysql_query($query_criteriosAvaliativos, $SmecelNovo) or die(mysql_error());
$row_criteriosAvaliativos = mysql_fetch_assoc($criteriosAvaliativos);
$totalRows_criteriosAvaliativos = mysql_num_rows($criteriosAvaliativos);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_GrupoConceitos = "
SELECT conceito_itens_id, conceito_itens_id_conceito, conceito_itens_descricao, conceito_itens_legenda, conceito_itens_peso 
FROM smc_conceito_itens
WHERE conceito_itens_id_conceito = '$row_criteriosAvaliativos[ca_grupo_conceito]'
ORDER BY conceito_itens_peso DESC
";
$GrupoConceitos = mysql_query($query_GrupoConceitos, $SmecelNovo) or die(mysql_error());
$row_GrupoConceitos = mysql_fetch_assoc($GrupoConceitos);
$totalRows_GrupoConceitos = mysql_num_rows($GrupoConceitos);

$pesoMax = $row_GrupoConceitos['conceito_itens_peso'];

$colname_Periodo = "";
$periodo = $row_criteriosAvaliativos['ca_qtd_periodos'];
if (isset($_GET['periodo'])) {
  $colname_Periodo = $_GET['periodo'];
  $periodo = $colname_Periodo;
} else {
  $colname_Periodo = "";
  $periodo = $row_criteriosAvaliativos['ca_qtd_periodos'];
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_GrupoConceitosLegenda = "
SELECT conceito_itens_id, conceito_itens_id_conceito, conceito_itens_descricao, conceito_itens_legenda, conceito_itens_peso 
FROM smc_conceito_itens
WHERE conceito_itens_id_conceito = '$row_criteriosAvaliativos[ca_grupo_conceito]'
ORDER BY conceito_itens_peso ASC
";
$GrupoConceitosLegenda = mysql_query($query_GrupoConceitosLegenda, $SmecelNovo) or die(mysql_error());
$row_GrupoConceitosLegenda = mysql_fetch_assoc($GrupoConceitosLegenda);
$totalRows_GrupoConceitosLegenda = mysql_num_rows($GrupoConceitosLegenda);
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
  <link rel="stylesheet" type="text/css" href="css/preloader.css">
  <script src="js/locastyle.js"></script>
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <style>
    html {
      -webkit-print-color-adjust: exact;
    }

    table.bordasimples {
      border-collapse: collapse;
      font-size: 10px;
    }

    table.bordasimples tr td {
      border: 1px dotted #000000;
      padding: 4px;
      font-size: 14px;
    }

    table.bordasimples tr th {
      border: 1px dotted #000000;
      padding: 4px;
      font-size: 14px;
      font-weight: bold;
      height: 30px;
    }
  </style>
</head>

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
    </span>
    <span class="ls-float-left" style="margin-right:20px;"> <img
        src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt="" width="60px" /></span>
    <?php echo $row_EscolaLogada['escola_nome']; ?><br>
    <small> Aluno(a): <strong><?php echo $row_matricula['aluno_nome']; ?></strong><br>
      Nascimento: <strong><?php echo inverteData($row_matricula['aluno_nascimento']); ?></strong><br>
      Filiação: <strong><?php echo $row_matricula['aluno_filiacao1']; ?></strong><br>
      Turma: <strong><?php echo $row_matricula['turma_nome']; ?></strong> </small> </div>
  </p>

  <h3 class="ls-ico-text ls-txt-center">BOLETIM ESCOLAR <?php echo $row_matricula['vinculo_aluno_ano_letivo']; ?></h3>

  <hr>


  <?php if (isset($_GET["boletimcadastrado"])) { ?>
    <p>

    <div class="ls-alert-success ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
      Relatório gerado com sucesso. </div>
    </p>
  <?php } ?>

  <!--

<?php for ($i = 1; $i <= $row_criteriosAvaliativos['ca_qtd_periodos']; $i++) { ?>
<a class="ls-btn-primary <?php if ($colname_Periodo == $i) {
      echo "ls-active";
    } ?>" href="conceitoEfVer.php?c=<?php echo $colname_matricula; ?>&periodo=<?php echo $i; ?>"><?php echo $i; ?>º período</a>
<?php } ?>
<a class="ls-btn-primary <?php if ($colname_Periodo == $i) {
  echo "ls-active";
} ?>" href="conceitoEfVer.php?c=<?php echo $colname_matricula; ?>">Anual</a>    
-->


  <?php
  $perc = number_format(100 / $totalRows_GrupoConceitos, 0);
  $inicio = 0;
  $parc = $perc;
  $cont = 1;
  $ver = 0;
  ?>

  <table>
    <?php do { ?>
      <tr>


        <td class="ls-txt-right">
          <span class="ls-tag-warning">
            De <?php echo $inicio; ?>% até <?php echo min($parc, 100); ?>%
            <?php $cont++; ?>
            <?php if ($cont == $totalRows_GrupoConceitosLegenda) {
              $ver = 1;
            } ?>
            <?php $inicio = min($parc + 1, 100);
            $parc = min($parc + $perc + $ver, 100); ?>
          </span>
        <td>
          <span class="ls-tag-info"><?php echo $row_GrupoConceitosLegenda['conceito_itens_legenda']; ?>:
            <?php echo $row_GrupoConceitosLegenda['conceito_itens_descricao']; ?></span>
        </td>
      </tr>
    <?php } while ($row_GrupoConceitosLegenda = mysql_fetch_assoc($GrupoConceitosLegenda)); ?>
  </table>


  <?php $nn = 1; ?>
  <?php do { ?>
    <table class="ls-table bordasimples ls-bg-header" width="100%">
      <thead>
        <tr>
          <td width="40" class="ls-txt-center"></td>
          <th class="ls-txt-center">COMPONENTES</th>

          <?php for ($i = 1; $i <= $row_criteriosAvaliativos['ca_qtd_periodos']; $i++) { ?>
            <th width="50" class="ls-txt-center"><?php echo $i; ?>ª</th>
          <?php } ?>
          <th width="50" class="ls-txt-center">RF</th>
          <?php do { ?>
            <?php
            mysql_select_db($database_SmecelNovo, $SmecelNovo);
            $query_Acompanhamento = "
             SELECT quest_conc_id, quest_conc_id_matriz, quest_conc_id_etapa, quest_conc_id_comp, quest_conc_descricao, quest_conc_hash 
    FROM smc_questionario_conceitos
    WHERE quest_conc_id_matriz = '$row_turma[turma_matriz_id]' AND quest_conc_id_comp = '$row_disciplinasMatriz[disciplina_id]'
    ORDER BY quest_conc_descricao ASC
            ";
            $Acompanhamento = mysql_query($query_Acompanhamento, $SmecelNovo) or die(mysql_error());
            $row_Acompanhamento = mysql_fetch_assoc($Acompanhamento);
            $totalRows_Acompanhamento = mysql_num_rows($Acompanhamento);
            ?>


            <?php for ($i = 1; $i <= $row_criteriosAvaliativos['ca_qtd_periodos']; $i++) { ?>
              <?php

              $tot[$i] = 0;
              $countZero[$i] = 0;
              ?>
            <?php } ?>

          </tr>

        </thead>

        <tbody>
          <?php $n = 1;
          do { ?>
            <!--
                              <tr>
                                <td class="ls-txt-center"><?php echo $n;
                                $n++; ?></td>
                                <td><?php echo $row_Acompanhamento['quest_conc_descricao']; ?></td>
                                <?php for ($i = 1; $i <= $row_criteriosAvaliativos['ca_qtd_periodos']; $i++) { ?>
                                <?php

                                mysql_select_db($database_SmecelNovo, $SmecelNovo);
                                $query_Avaliacao = "
                                   SELECT conc_ef_id, conc_ef_id_quest, conc_ef_id_matr, conc_ef_periodo, conc_ef_avaliac, conceito_itens_id_conceito, conceito_itens_peso, conceito_itens_legenda 
                    FROM smc_conceito_ef
                    LEFT JOIN smc_conceito_itens ON conceito_itens_peso = conc_ef_avaliac
                    WHERE conc_ef_id_quest = '$row_Acompanhamento[quest_conc_id]' AND conc_ef_id_matr = '$row_matricula[vinculo_aluno_id]' AND conc_ef_periodo = '$i' AND conceito_itens_id_conceito = '$row_criteriosAvaliativos[ca_grupo_conceito]'
                    ";
                                $Avaliacao = mysql_query($query_Avaliacao, $SmecelNovo) or die(mysql_error());
                                $row_Avaliacao = mysql_fetch_assoc($Avaliacao);
                                $totalRows_Avaliacao = mysql_num_rows($Avaliacao);


                                $tot[$i] = $tot[$i] + $row_Avaliacao['conceito_itens_peso'];

                                if ($row_Avaliacao['conc_ef_avaliac'] == 0) {

                                  $countZero[$i]++;

                                }

                                ?>
                                  
                                  <td width="60" class="ls-txt-center"><?php if ($row_Avaliacao['conceito_itens_legenda'] == "") { ?>
                                  -
                                  <?php } else { ?>
                                  <span class="" style="font-weight:bolder"><?php echo $row_Avaliacao['conceito_itens_legenda']; ?>
                                  <?php } ?>
                                  </span></td>
                                 
                                  
                                  
                                  
                                <?php } ?>
                              </tr>
                            -->
          <?php } while ($row_Acompanhamento = mysql_fetch_assoc($Acompanhamento)); ?>
          <tr>
            <td class="ls-txt-center"><?php echo $nn;
            $nn++; ?></td>
            <td class="ls-txt-left"><?php echo $row_disciplinasMatriz['disciplina_nome']; ?></td>
            <?php
            $rf = 0;
            ?>
            <?php for ($i = 1; $i <= $row_criteriosAvaliativos['ca_qtd_periodos']; $i++) { ?>
              <td width="70" class="ls-txt-center">
                <?php
                
                 $deducao = $pesoMax * $countZero[$i];
                 $totalPeso = ($pesoMax * $totalRows_Acompanhamento) - $deducao;
                 //echo "deducao: " . $deducao . "<br>";
                 //echo "totalPeso: " . $totalPeso . "<br>";
                // Verifica se o totalPeso é maior que zero antes de fazer a divisão
                if ($totalPeso > 0) {
                  $res = number_format((($tot[$i] / $totalPeso) * 100), 1);
                } else {
                  $res = 0; // Ou outro valor apropriado
                }
                ?>

                <small>
                  <strong><?php echo ($res == 0) ? "-" : $res . "%"; ?></strong>
                </small>
              </td>
              <?php
               $rf = $rf + $res;
              
            }
            ?>

            <td class="ls-txt-center" style="background-color: #EEEEEE;">
              <?php $rf = number_format($rf / $row_criteriosAvaliativos['ca_qtd_periodos'], 1) ?>
              <strong><small><?php if ($res == 0) {
                echo "-";
              } else {
                echo $rf . "%";
              } ?></small></strong>
            </td>

          </tr>

        <?php } while ($row_disciplinasMatriz = mysql_fetch_assoc($disciplinasMatriz)); ?>





      </tbody>
    </table>


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





  <?php } while ($row_matricula = mysql_fetch_assoc($matricula)); ?>



  </div>
  <!-- CONTEÚDO -->



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

mysql_free_result($matriz);

mysql_free_result($Avaliacao);

mysql_free_result($Acompanhamento);

mysql_free_result($EscolaLogada);
?>