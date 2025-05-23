<?php require_once('../../Connections/SmecelNovoPDO.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
<?php include('../../sistema/funcoes/inverteData.php'); ?>
<?php include "../../sistema/escola/fnc/calculos.php"; ?>

<?php
try {
  // Verifica se o parâmetro "componente" foi passado
  $colname_Disciplina = isset($_GET['componente']) ? $_GET['componente'] : -1;

  // Consulta Disciplina
  $query_Disciplina = "SELECT disciplina_id, disciplina_area_conhecimento_id, disciplina_codigo_inep, disciplina_ordem, disciplina_nome, disciplina_nome_abrev 
                         FROM smc_disciplina 
                         WHERE disciplina_id = :disciplina_id";
  $stmt = $SmecelNovo->prepare($query_Disciplina);
  $stmt->bindParam(':disciplina_id', $colname_Disciplina, PDO::PARAM_INT);
  $stmt->execute();
  $row_Disciplina = $stmt->fetch(PDO::FETCH_ASSOC);
  $totalRows_Disciplina = $stmt->rowCount();

  // Verifica se o parâmetro "turma" foi passado
  $colname_Turma = isset($_GET['turma']) ? $_GET['turma'] : -1;

  // Consulta Turma
  $query_Turma = "SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo 
                    FROM smc_turma 
                    WHERE turma_id = :turma_id";
  $stmt = $SmecelNovo->prepare($query_Turma);
  $stmt->bindParam(':turma_id', $colname_Turma, PDO::PARAM_INT);
  $stmt->execute();
  $row_Turma = $stmt->fetch(PDO::FETCH_ASSOC);
  $totalRows_Turma = $stmt->rowCount();

  // Consulta Alunos
  $query_Alunos = "SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_situacao, vinculo_aluno_ano_letivo, vinculo_aluno_hash,
                            aluno_id, aluno_nome, aluno_foto, disciplina_id, disciplina_nome, turma_id, turma_nome, turma_id_escola, turma_ano_letivo 
                     FROM smc_vinculo_aluno
                     INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
                     INNER JOIN smc_disciplina ON disciplina_id = :disciplina_id
                     INNER JOIN smc_turma ON turma_id = :turma_id
                     WHERE vinculo_aluno_id_turma = :turma_id 
                     AND turma_ano_letivo = :ano_letivo
                     AND vinculo_aluno_ano_letivo = :ano_letivo
                     ORDER BY aluno_nome";
  $stmt = $SmecelNovo->prepare($query_Alunos);
  $stmt->bindParam(':disciplina_id', $colname_Disciplina, PDO::PARAM_INT);
  $stmt->bindParam(':turma_id', $colname_Turma, PDO::PARAM_INT);
  $stmt->bindParam(':ano_letivo', $row_AnoLetivo['ano_letivo_ano'], PDO::PARAM_INT);
  $stmt->execute();
  $Alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);
  $totalRows_Alunos = $stmt->rowCount();

  // Se não houver alunos, redireciona
  if ($totalRows_Alunos == 0) {
    // header("Location:index.php?erro");
  }

  // Consulta Escola
  $query_Escola = "SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, escola_telefone2, 
                            escola_email, escola_inep, escola_cnpj, escola_logo, escola_ue 
                     FROM smc_escola 
                     WHERE escola_id = :escola_id";
  $stmt = $SmecelNovo->prepare($query_Escola);
  $stmt->bindParam(':escola_id', $row_Alunos[0]['turma_id_escola'], PDO::PARAM_INT);
  $stmt->execute();
  $row_Escola = $stmt->fetch(PDO::FETCH_ASSOC);
  $totalRows_Escola = $stmt->rowCount();

  // Se não houver escola, redireciona
  if ($totalRows_Escola == 0) {
    // header("Location:../index.php?loginErr");
  }

  // Consulta Matriz
  $query_Matriz = "SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash, matriz_dias_letivos, 
                            matriz_semanas_letivas, matriz_dias_semana, matriz_minutos_aula, matriz_aula_dia, matriz_criterio_avaliativo 
                     FROM smc_matriz 
                     WHERE matriz_id = :matriz_id";
  $stmt = $SmecelNovo->prepare($query_Matriz);
  $stmt->bindParam(':matriz_id', $row_Turma['turma_matriz_id'], PDO::PARAM_INT);
  $stmt->execute();
  $row_Matriz = $stmt->fetch(PDO::FETCH_ASSOC);
  $totalRows_Matriz = $stmt->rowCount();

  // Consulta Critérios Avaliativos
  $query_Criterios = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, 
                               ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, 
                               ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes, ca_digitos 
                        FROM smc_criterios_avaliativos 
                        WHERE ca_id = :criterio_id";
  $stmt = $SmecelNovo->prepare($query_Criterios);
  $stmt->bindParam(':criterio_id', $row_Matriz['matriz_criterio_avaliativo'], PDO::PARAM_INT);
  $stmt->execute();
  $row_Criterios = $stmt->fetch(PDO::FETCH_ASSOC);
  $totalRows_Criterios = $stmt->rowCount();

} catch (PDOException $e) {
  die("Erro na consulta: " . $e->getMessage());
}

$disciplinaNome = isset($row_Disciplina['disciplina_nome']) ? $row_Disciplina['disciplina_nome'] : 'N/D';
$turmaNome = isset($row_Turma['turma_nome']) ? $row_Turma['turma_nome'] : 'N/D';

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
  <title>PROFESSOR | <?php echo $row_ProfLogado['func_nome']; ?> | SMECEL - Sistema de Gestão Escolar</title>
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
    table {
      width: 100%;
      border-collapse: collapse;
    }

    table a {
      display: block;
      padding: 3px;
    }

    th,
    td {
      padding: 5px;
      border: 1px solid #ccc;
    }

    tr,
    td {
      padding: 2px;
      height: 9px;
      line-height: 9px;
    }

    .aluno {
      background-color: #ddd;
      border-radius: 0%;
      height: 40px;
      object-fit: cover;
      width: 40px;
    }
  </style>
</head>

<body onload="print();alert('Configure a impressora para a orientação paisagem')">




  <h3 class="ls-txt-center">MAPA DE NOTAS</h3>
  <?php if ($totalRows_Alunos == 0) { ?>
    NENHUM ALUNO COM BOLETIM GERADO. <a href="index.php">VOLTAR</a>
  <?php } else { ?>
    <br>
    <h4 class="ls-txt-center"><?php echo $disciplinaNome; ?> - <?php echo $turmaNome; ?> - <?php echo $row_ProfLogado['func_nome']; ?></h4><br>

    <table class="ls-table1 ls-sm-space ls-table-layout-auto ls-full-width ls-height-auto" width="100%">
      <thead>
        <tr class="">
          <th colspan="3" class="ls-display-none-xs"></th>
          <?php $tmu = 0; ?>
          <?php for ($p = 1; $p <= $row_Criterios['ca_qtd_periodos']; $p++) { ?>
            <th colspan="<?php echo $row_Criterios['ca_qtd_av_periodos'] + 1; ?>" class="ls-txt-center ls-display-none-xs"
              width="15"><?php echo $p; ?>º PERÍODO</th>
          <?php } ?>
          <th colspan="4" class="ls-txt-center ls-display-none-xs">RESULTADO</th>
        </tr>
        <tr class="">
          <th class="ls-txt-center"></th>
          <th class="ls-txt-center">MAT</th>
          <th class="ls-txt-center">IDENTIFICAÇÃO</th>
          <?php $tmu = 0; ?>
          <?php for ($p = 1; $p <= $row_Criterios['ca_qtd_periodos']; $p++) { ?>
            <?php for ($c = 1; $c <= $row_Criterios['ca_qtd_av_periodos']; $c++) { ?>
              <th width="15" class="ls-txt-center ls-display-none-xs"><?php echo $c; ?>ª</th>
            <?php } ?>
            <th width="15" class="ls-txt-center ls-display-none-xs">MU</th>
          <?php } ?>
          <th width="15" class="ls-txt-center ls-display-none-xs">TP</th>
          <th width="15" class="ls-txt-center ls-display-none-xs">MC</th>
          <th width="15" class="ls-txt-center ls-display-none-xs">NR</th>
          <th width="15" class="ls-txt-center ls-display-none-xs">RES</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $num = 1;
        foreach ($Alunos as $row_Alunos) { ?>
          <tr>
            <td width="15" class="ls-txt-center"><strong><?php echo $num;
            $num++; ?></strong></td>
            <td width="10" class="ls-txt-center"><?php echo $row_Alunos['vinculo_aluno_id']; ?></td>

            <td width="150" class="" style="padding:0 5px;">
              <?php echo current(str_word_count($row_Alunos['aluno_nome'], 2)); ?>
              <?php $word = explode(" ", trim($row_Alunos['aluno_nome']));
              echo $word[count($word) - 1]; ?>
            </td>

            <?php $tmu = 0; ?>
            <?php for ($p = 1; $p <= $row_Criterios['ca_qtd_periodos']; $p++) { ?>
              <?php $ru = 0; ?>
              <?php for ($a = 1; $a <= $row_Criterios['ca_qtd_av_periodos']; $a++) { ?>
                <td width="15" class="ls-txt-center ls-display-none-xs">
                  <?php
                  // Consulta para buscar notas
                  $query_Notas = "SELECT nota_valor FROM smc_nota 
                                            WHERE nota_id_matricula = :vinculo_aluno_id 
                                            AND nota_id_disciplina = :disciplina_id 
                                            AND nota_periodo = :periodo 
                                            AND nota_num_avaliacao = :avaliacao";

                  $stmt_Notas = $SmecelNovo->prepare($query_Notas);
                  $stmt_Notas->bindParam(':vinculo_aluno_id', $row_Alunos['vinculo_aluno_id'], PDO::PARAM_INT);
                  $stmt_Notas->bindParam(':disciplina_id', $row_Alunos['disciplina_id'], PDO::PARAM_INT);
                  $stmt_Notas->bindParam(':periodo', $p, PDO::PARAM_INT);
                  $stmt_Notas->bindParam(':avaliacao', $a, PDO::PARAM_INT);
                  $stmt_Notas->execute();
                  $row_Notas = $stmt_Notas->fetch(PDO::FETCH_ASSOC);
                  $nota_valor = isset($row_Notas['nota_valor']) ? $row_Notas['nota_valor'] : 0;
                  $ru += $nota_valor;
                  ?>
                  <span
                    style="display:block; width:100%; color:<?php echo ($nota_valor >= $row_Criterios['ca_nota_min_av']) ? 'blue' : 'red'; ?>;">
                    <?php echo number_format($nota_valor, $row_Criterios['ca_digitos'], ",", "."); ?>
                  </span>
                </td>
              <?php } ?>
              <td width="15" class="ls-txt-center ls-display-none-xs">
                <span class="ls-text-md">
                  <?php $mu = mediaUnidade(
                    $ru,
                    $row_Criterios['ca_arredonda_media'],
                    $row_Criterios['ca_aproxima_media'],
                    $row_Criterios['ca_media_min_periodo'],
                    $row_Criterios['ca_calculo_media_periodo'],
                    $row_Criterios['ca_qtd_av_periodos'],
                    $row_Criterios['ca_digitos']
                  ); ?>
                </span>
                <?php $tmu += $mu; ?>
              </td>
            <?php } ?>

            <td width="15" class="ls-txt-center ls-display-none-xs">
              <span class="ls-text-md"><?php $tp = totalPontos($tmu, $row_Criterios['ca_digitos']); ?></span>
            </td>
            <td width="15" class="ls-txt-center ls-display-none-xs">
              <span class="ls-text-md"><?php $mc = mediaCurso(
                $tp,
                $row_Criterios['ca_arredonda_media'],
                $row_Criterios['ca_aproxima_media'],
                $row_Criterios['ca_min_media_aprovacao_final'],
                $row_Criterios['ca_qtd_periodos'],
                $row_Criterios['ca_digitos']
              ); ?>
              </span>
            </td>

            <td width="15" class="ls-txt-center ls-display-none-xs">
              <?php
              // Consulta para buscar nota de recuperação final
              $query_notaAf = "SELECT nota_valor FROM smc_nota 
                                    WHERE nota_id_matricula = :vinculo_aluno_id 
                                    AND nota_id_disciplina = :disciplina_id 
                                    AND nota_periodo = '99' 
                                    AND nota_num_avaliacao = '99'";

              $stmt_notaAf = $SmecelNovo->prepare($query_notaAf);
              $stmt_notaAf->bindParam(':vinculo_aluno_id', $row_Alunos['vinculo_aluno_id'], PDO::PARAM_INT);
              $stmt_notaAf->bindParam(':disciplina_id', $row_Alunos['disciplina_id'], PDO::PARAM_INT);
              $stmt_notaAf->execute();
              $row_notaAf = $stmt_notaAf->fetch(PDO::FETCH_ASSOC);
              $nota_final = isset($row_notaAf['nota_valor']) ? $row_notaAf['nota_valor'] : 0;
              $af = avaliacaoFinal($nota_final, $row_Criterios['ca_nota_min_recuperacao_final'], $row_Criterios['ca_digitos']);
              ?>
              <span class="ls-text-md"><?php echo $nota_final; ?></span>
            </td>

            <td width="15" class="ls-txt-center ls-display-none-xs">
              <?php
              $resultado = resultadoFinal(
                $mc,
                $af,
                $row_Criterios['ca_nota_min_recuperacao_final'],
                $row_Criterios['ca_min_media_aprovacao_final'],
                $row_Criterios['ca_digitos']
              );

              echo ($resultado == "APR") ? "<small class='light-green lighten-2'>APR</small>" : "<small class='pink accent-1'>CON</small>";
              ?>
            </td>
          </tr>
        <?php } ?>
      </tbody>

    </table>
  <?php } ?>
  <br>
  <p> LEGENDA:
    <strong>MU</strong>: MÉDIA DA UNIDADE - <strong>TP</strong>: TOTAL DE PONTOS - <strong>MC</strong>: MÉDIA DO CURSO -
    <strong>NR</strong>: NOTA DE RECUPERAÇÃO - <strong>RES</strong>: RESULTADO FINAL
  </p>
  <small>SMECEL - Sistema de Gestão Escolar | www.smecel.com.br</small>


  <!-- We recommended use jQuery 1.10 or up -->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="js/locastyle.js"></script>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="js/sweetalert2.min.js"></script>
  <script src="../../js/jquery.mask.js"></script>
  <script type="text/javascript" src="../js/app.js"></script>

  <script type="text/javascript">

    $(document).ready(function () {
      $("input").blur(function () {

        var id = $(this).attr('name');
        var valor = $(this).val();
        var notaAnterior = $(this).attr('notaAnterior');
        var notaMax = $(this).attr('max');
        var notaMin = $(this).attr('notaMin');
        var disciplina = $(this).attr('disciplina');

        if (valor < notaMin) {
          $(this).css("color", "red");
        } else {
          $(this).css("color", "blue");
        }


        if ((valor != notaAnterior)) {
          $.ajax({
            type: 'POST',
            url: 'fnc/lancaNota.php',
            data: {
              id: id,
              valor: valor,
              notaMax: notaMax,
              notaAnterior: notaAnterior,
              disciplina: disciplina
            },
            success: function (data) {
              $('#status').html(data);

              setTimeout(function () {
                $("#status").html("");
              }, 15000);

            }
          })
        }

      });
    });

    $(document).ready(function () {
      $('.nota').mask('00.0', { reverse: true });
      $('.money').mask('000.000.000.000.000,00', { reverse: true });
    });

    $(document).ready(function () {
      $('.recarregar').click(function () {
        location.reload();
      });
    });



    $(function () {
      $(document).on('click', 'input[type=text]', function () {
        this.select();
      });
    });

  </script>

  <script type="application/javascript">
    /*
    Swal.fire({
      //position: 'top-end',
      icon: 'success',
      title: 'Tudo certo por aqui',
      showConfirmButton: false,
      timer: 1500
    })
    */
  </script>
</body>

</html>