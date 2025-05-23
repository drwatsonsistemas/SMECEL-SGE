<?php require_once('../../Connections/SmecelNovoPDO.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
<?php include('../../sistema/funcoes/inverteData.php'); ?>

<?php

$colname_ac_edit = isset($_GET['ac']) ? $_GET['ac'] : "-1";
$escola = isset($_GET['escola']) ? anti_injection($_GET['escola']) : "-1";
$colname_Etapa = isset($_GET['etapa']) ? anti_injection($_GET['etapa']) : "-1";
$turma = isset($_GET['turma']) ? anti_injection($_GET['turma']) : "-1";


$stmtEscolaLogada = $SmecelNovo->prepare(
  "SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, 
  escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_tema, sec_id, sec_cidade, sec_uf, 
  sec_termo_matricula, escola_assinatura 
  FROM smc_escola 
  INNER JOIN smc_sec ON sec_id = escola_id_sec 
  WHERE escola_id = :escola"
);
$stmtEscolaLogada->execute([':escola' => $escola]);
$row_EscolaLogada = $stmtEscolaLogada->fetch(PDO::FETCH_ASSOC);
$totalRows_EscolaLogada = $stmtEscolaLogada->rowCount();


// Consultar AC Edit
$stmtAcEdit2 = $SmecelNovo->prepare(
  "SELECT ac_id, ac_id_professor, ac_id_escola, ac_id_componente, ac_id_etapa, ac_ano_letivo, ac_data_inicial, ac_data_final, 
      ac_status, ac_correcao, ac_feedback, ac_conteudo, ac_objetivo_especifico, ac_objeto_conhecimento, ac_metodologia, 
      ac_recursos, ac_avaliacao, ac_criacao, ac_da_conviver, ac_da_brincar, ac_da_participar, ac_da_explorar, ac_da_expressar, 
      ac_da_conhecerse, ac_ce_eo, ac_ce_ts, ac_ce_ef, ac_ce_cg, ac_ce_et, ac_ce_di, ac_tema, disciplina_id, disciplina_nome 
      FROM smc_ac 
      LEFT JOIN smc_disciplina ON disciplina_id = ac_id_componente 
      WHERE ac_id = :ac_id"
);
$stmtAcEdit2->execute([':ac_id' => $colname_ac_edit]);
$row_ac_edit2 = $stmtAcEdit2->fetch(PDO::FETCH_ASSOC);
$totalRows_ac_edit2 = $stmtAcEdit2->rowCount();

$stmtMatrizDisciplinasAC = $SmecelNovo->prepare(
  "SELECT ac_componente_id, ac_componente_id_componente, ac_componente_id_planejamento, ac_id, disciplina_id, disciplina_nome 
  FROM smc_ac_componente 
  INNER JOIN smc_ac ON ac_componente_id_planejamento = ac_id 
  INNER JOIN smc_disciplina ON disciplina_id = ac_componente_id_componente 
  WHERE ac_id = :ac_id"
);
$stmtMatrizDisciplinasAC->execute([':ac_id' => $colname_ac_edit]);
$rowDisciplinasAC = $stmtMatrizDisciplinasAC->fetchAll(PDO::FETCH_ASSOC);
$totalRowsDisciplinasAC = $stmtMatrizDisciplinasAC->rowCount();


$stmtEtapa = $SmecelNovo->prepare(
  "SELECT etapa_id, etapa_id_filtro, etapa_nome, etapa_idade, etapa_limite_turma, etapa_nome_abrev, etapa_ano_ef 
  FROM smc_etapa WHERE etapa_id = :etapa_id"
);
$stmtEtapa->execute([':etapa_id' => $colname_Etapa]);
$row_Etapa = $stmtEtapa->fetch(PDO::FETCH_ASSOC);
$totalRows_Etapa = $stmtEtapa->rowCount();

$stmtTurmas = $SmecelNovo->prepare(
  "SELECT * FROM smc_turma WHERE turma_id_escola = :escola AND turma_id = :turma"
);
$stmtTurmas->execute([':escola' => $escola, ':turma' => $turma]);
$rowTurmas = $stmtTurmas->fetch(PDO::FETCH_ASSOC);


$stmtAcEdit = $SmecelNovo->prepare("
        SELECT 
            ac_id, ac_id_professor, ac_id_escola, ac_id_componente, ac_id_etapa, ac_ano_letivo, ac_data_inicial, ac_data_final, 
            ac_status, ac_correcao, ac_feedback, ac_conteudo, ac_objetivo_especifico, ac_objeto_conhecimento, 
            ac_metodologia, ac_recursos, ac_avaliacao, ac_criacao, ac_da_conviver, ac_da_brincar, ac_da_participar, 
            ac_da_explorar, ac_da_expressar, ac_da_conhecerse, ac_ce_eo, ac_ce_ts, ac_ce_ef, ac_ce_cg, ac_ce_et, ac_ce_di, 
            ac_tema, ac_unid_tematica 
        FROM smc_ac 
        WHERE ac_id = :ac_id
    ");
$stmtAcEdit->execute([':ac_id' => $colname_ac_edit]);
$row_ac_edit = $stmtAcEdit->fetch(PDO::FETCH_ASSOC);
$totalRows_ac_edit = $stmtAcEdit->rowCount();


$stmtAcLabel = $SmecelNovo->prepare(
  "SELECT * FROM smc_ac_label WHERE ac_id_ac = :ac_id"
);
$stmtAcLabel->execute([':ac_id' => $row_ac_edit2['ac_id']]);
$rowAcLabel = $stmtAcLabel->fetchAll(PDO::FETCH_ASSOC);
$TotalrowAcLabel = $stmtAcLabel->rowCount();
?>


<!DOCTYPE html>
<html class="<?php echo TEMA; ?>" lang="pt-br">

<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    gtag('js', new Date());

    gtag('config', 'UA-117872281-1');
  </script>
  <title>PLANEJAMENTO | <?php echo $row_Etapa['etapa_nome']; ?> | SMECEL - Sistema de Gestão Escolar</title>
  <meta charset="utf-8">
  <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
  <link rel="stylesheet" type="text/css" href="css/locastyle.css">
  <link rel="stylesheet" href="css/sweetalert2.min.css">

  <style>
    body {
      font-size: 12px;
      background-image: url(<?php if ($row_EscolaLogada['escola_logo'] <> "") { ?>../../img/marcadagua/<?php echo $row_EscolaLogada['escola_logo']; ?><?php } else { ?>../../img/marcadagua/brasao_republica.png<?php } ?>);
      background-repeat: no-repeat;
      background-position: center center;
      z-index: -999;
    }

    p {
      margin-bottom: 1px;
    }

    page {
      display: block;
      margin: 0 auto;
      margin-bottom: 0.5cm;
    }

    page[size="A4"] {
      width: 21cm;
      height: 29.7cm;
      padding: 5px;

    }

    page[size="A4"][layout="portrait"] {
      width: 29.7cm;
      height: 21cm;
    }

    @media print {

      body,
      page {
        margin: 0;
        box-shadow: 0;
      }

      #div_impressao {
        display: none;
      }
    }

    table.bordasimples {
      border-collapse: collapse;
      font-size: 10px;
    }

    table.bordasimples tr td {
      border: 1px dotted #000000;
      padding: 2px;
      font-size: 14px;
      vertical-align: top;
      height: 30px;
    }

    table.bordasimples tr th {
      border: 1px dotted #000000;
      padding: 2px;
      font-size: 14px;
      vertical-align: top;
      height: 30px;
    }
  </style>
</head>

<body onload="self.print()">
  <page size="A4">


    <div class="ls-txt-center1">

      <table>

        <tr>
          <td width="150px" class="ls-txt-center">
            <span><?php if ($row_EscolaLogada['escola_logo'] <> "") { ?><img
                  src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt=""
                  width="100px" /><?php } else { ?><img src="../../img/brasao_republica.png" alt=""
                  width="80px" /><?php } ?></span>
          </td>

          <td width="350px">
            <h2><strong><?php echo $row_EscolaLogada['escola_nome']; ?></strong></h2>
            <small>
              <?php echo $row_EscolaLogada['escola_endereco']; ?>,
              <?php echo $row_EscolaLogada['escola_num']; ?> -
              <?php echo $row_EscolaLogada['escola_bairro']; ?> -
              <?php echo $row_EscolaLogada['escola_cep']; ?><br>
              CNPJ:<?php echo $row_EscolaLogada['escola_cnpj']; ?>
              INEP:<?php echo $row_EscolaLogada['escola_inep']; ?><br>
              <?php echo $row_EscolaLogada['escola_telefone1']; ?> <?php echo $row_EscolaLogada['escola_telefone2']; ?>
              <?php echo $row_EscolaLogada['escola_email']; ?>
            </small>
          </td>

          <td class="ls-txt-right" width="270px">

            <h2 class="ls-txt-right">SEQUÊNCIA DIDÁTICA</h2>

          </td>
        </tr>

      </table>

    </div>
    <br>
    <table class="ls-sm-space bordasimples" width="100%" style="font-size:10px;">
      <tr>
        <td><small><strong>Professor(a):</strong></small><br><?php echo $row_ProfLogado['func_nome'] ?>&nbsp;</td>
        <td><small><strong>Ano/Série:</strong></small><br><?php echo $row_Etapa['etapa_nome'] ?>&nbsp;</td>
      </tr>
      <tr>
        <td><small><strong>Componentes curriculares:</strong></small><br>
          <?php
          if ($row_ac_edit2['disciplina_nome'] != '') {
            echo $row_ac_edit2['disciplina_nome'] . " |";
          }
          if ($rowDisciplinasAC) {

            foreach ($rowDisciplinasAC as $disciplinas ) {
              echo $disciplinas['disciplina_nome'] . " | ";
            }
          }
          ?>
        </td>
        <td>
          <small><strong>Período:</strong></small><br>
          <?php
          // Converte as datas para o formato desejado
          $data_inicial_formatada = date('d/m/Y', strtotime($row_ac_edit['ac_data_inicial']));
          $data_final_formatada = date('d/m/Y', strtotime($row_ac_edit['ac_data_final']));

          // Exibe as datas formatadas
          echo $data_inicial_formatada . " à " . $data_final_formatada;
          ?>
        </td>

      </tr>


    </table>
    <table class="ls-sm-space bordasimples" width="100%" style="font-size:10px;">
  <tr>
    <td colspan="6" style="text-align: center;"><strong>DIREITOS DE APRENDIZAGEM</strong></td>
  </tr>
  <tr>
    <td><small><strong>Conviver:</strong></small> <?php echo ($row_ac_edit['ac_da_conviver'] == "S") ? "✔️" : ""; ?></td>
    <td><small><strong>Brincar:</strong></small> <?php echo ($row_ac_edit['ac_da_brincar'] == "S") ? "✔️" : ""; ?></td>
    <td><small><strong>Participar:</strong></small> <?php echo ($row_ac_edit['ac_da_participar'] == "S") ? "✔️" : ""; ?></td>
    <td><small><strong>Explorar:</strong></small> <?php echo ($row_ac_edit['ac_da_explorar'] == "S") ? "✔️" : ""; ?></td>
    <td><small><strong>Expressar:</strong></small> <?php echo ($row_ac_edit['ac_da_expressar'] == "S") ? "✔️" : ""; ?></td>
    <td><small><strong>Conhecer-se:</strong></small> <?php echo ($row_ac_edit['ac_da_conhecerse'] == "S") ? "✔️" : ""; ?></td>
  </tr>
</table>
    <table class="ls-sm-space bordasimples" width="100%" style="font-size:10px;">
      <tr>
        <td width="61.3%"><small><strong>Tema:</strong></small><br><?php echo $row_ac_edit['ac_tema'] ?></td>
        <td><small><strong>Turma:</strong></small><br><?php echo $rowTurmas['turma_nome'] ?></td>
      </tr>

    </table>
    <br><br>

    <table class="ls-sm-space bordasimples" width="100%">
      <?php if ($TotalrowAcLabel == 0) { ?>
        <?php if ($row_ac_edit['ac_ce_eo'] != '') { ?>
          <tr>
            <td><br><small><strong>O EU, O OUTRO E O NOS:</strong></small><br><?php echo $row_ac_edit['ac_ce_eo']; ?></td>
          </tr>
        <?php } ?>
        <?php if ($row_ac_edit['ac_ce_ts'] != '') { ?>
          <tr>
            <td><br><small><strong>TRACOS, SONS, CORES E FORMAS:</strong></small><br><?php echo $row_ac_edit['ac_ce_ts']; ?>
            </td>
          </tr>
        <?php } ?>
        <?php if ($row_ac_edit['ac_ce_ef'] != '') { ?>
          <tr>
            <td><br><small><strong>ESCUTA, FALA, PENSAMENTOS E
                  IMAGINACAO:</strong></small><br><?php echo $row_ac_edit['ac_ce_ef']; ?></td>
          </tr>
        <?php } ?>
        <?php if ($row_ac_edit['ac_ce_cg'] != '') { ?>
          <tr>
            <td><br><small><strong>CORPOS, GESTOS E MOVIMENTO:</strong></small><br><?php echo $row_ac_edit['ac_ce_cg']; ?>
            </td>
          </tr>
        <?php } ?>
        <?php if ($row_ac_edit['ac_ce_et'] != '') { ?>
          <tr>
            <td><br><small><strong>ESPACOS, TEMPOS, QUANTIDADES:</strong></small><br><?php echo $row_ac_edit['ac_ce_et']; ?>
            </td>
          </tr>
        <?php } ?>

        <?php if ($row_ac_edit['ac_unid_tematica'] != '') { ?>
          <tr>
            <td><br><small><strong>Unidade temática:</strong></small><br><?php echo $row_ac_edit['ac_unid_tematica']; ?>
            </td>
          </tr>
        <?php } ?>

        <?php if ($row_ac_edit['ac_objetivo_especifico'] != '') { ?>
          <tr>
            <td><br><small><strong>Objetivos de aprendizagem e
                  desenvolvimento:</strong></small><br><?php echo $row_ac_edit['ac_objetivo_especifico']; ?></td>
          </tr>
        <?php } ?>

        <?php if ($row_ac_edit['ac_objeto_conhecimento'] != '') { ?>
          <tr>
            <td><br><small><strong>Objetos de conhecimento/saberes e
                  conhecimento:</strong></small><br><?php echo $row_ac_edit['ac_objeto_conhecimento']; ?></td>
          </tr>
        <?php } ?>

        <?php if ($row_ac_edit['ac_recursos'] != '') { ?>
          <tr>
            <td><br><small><strong>Habilidades:</strong></small><br><?php echo $row_ac_edit['ac_recursos']; ?></td>
          </tr>
        <?php } ?>

        <?php if ($row_ac_edit['ac_metodologia'] != '') { ?>
          <tr>
            <td><br><small><strong>Metodologia:</strong></small><br><?php echo $row_ac_edit['ac_metodologia']; ?></td>
          </tr>
        <?php } ?>

        <?php if ($row_ac_edit['ac_avaliacao'] != '') { ?>
          <tr>
            <td><br><small><strong>Avaliação:</strong></small><br><?php echo $row_ac_edit['ac_avaliacao']; ?></td>
          </tr>
        <?php } ?>

        <?php if ($row_ac_edit['ac_conteudo'] != '') { ?>
          <tr>
            <td><br><small><strong>Observação/Recursos:</strong></small><br><?php echo $row_ac_edit['ac_conteudo']; ?></td>
          </tr>
        <?php } ?>
      <?php } else { ?>

        <?php foreach ($rowAcLabel as $label): ?>
          <?php
          $titulo = '';
          switch ($label['ac_id_tipo']) {
            case '1':
              $titulo = "Unidade temática";
              break;
            case '2':
              $titulo = "Objetivos de Aprendizagem e desenvolvimento";
              break;
            case '3':
              $titulo = "Objetos de conhecimento/saberes e conhecimento/conteúdo";
              break;
            case '4':
              $titulo = "Habilidades";
              break;
            case '5':
              $titulo = "Metodologia";
              break;
            case '6':
              $titulo = "Avaliação";
              break;
            case '7':
              $titulo = "Observação";
              break;
            case '8':
              $titulo = "Recursos";
              break;
            default:
              $titulo = "";
              break;
          }
          ?>
          <tr>
            <td>
              <br><small><strong><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></strong></small><br><?= $label['ac_conteudo'] ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php } ?>
    </table>
    <br><br>
    <table class="ls-sm-space bordasimples" width="100%">
      <tr>
        <td class="ls-v-align-middle">
          <br><br><br>
          <p style="text-align:center">
            .................................................................................<br>PROFESSOR(A)</p>
        </td>
        <td class="ls-v-align-middle">
          <br><br><br>
          <p style="text-align:center">
            .................................................................................<br>COORDENADOR(A)</p>
        </td>

      </tr>
    </table>
    <br><br>
    <p style="text-align:center">
      <?php echo $row_EscolaLogada['sec_cidade']; ?>-<?php echo $row_EscolaLogada['sec_uf']; ?>,
      <?php
      setlocale(LC_TIME, 'pt_BR', 'utf-8', 'utf-8', 'portuguese');
      date_default_timezone_set('America/Sao_Paulo');
      echo strftime('%d de %B de %Y', strtotime('today'));
      ?>
    </p>
  </page>
</body>

</html>