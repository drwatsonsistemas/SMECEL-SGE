<?php require_once('../../Connections/SmecelNovoPDO.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
<?php

$colname_Matricula = "-1";
if (isset($_GET['cod'])) {
  $colname_Matricula = $_GET['cod'];
}

try {
  // PDO query for Matricula
  $query_Matricula = "SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, 
        vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, 
        vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, 
        vinculo_aluno_vacina_atualizada, vinculo_aluno_rel_aval, aluno_id, aluno_nome, aluno_foto
        FROM smc_vinculo_aluno 
        INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
        WHERE vinculo_aluno_hash = :colname_Matricula";
  $stmt_Matricula = $SmecelNovo->prepare($query_Matricula);
  $stmt_Matricula->bindValue(':colname_Matricula', $colname_Matricula, PDO::PARAM_STR);
  $stmt_Matricula->execute();
  $row_Matricula = $stmt_Matricula->fetch(PDO::FETCH_ASSOC);
  $totalRows_Matricula = $stmt_Matricula->rowCount();

  // PDO query for Disciplina
  $colname_Disciplina = "-1";
  if (isset($_GET['disciplina'])) {
    $colname_Disciplina = $_GET['disciplina'];
  }
  $query_Disciplina = "SELECT disciplina_id, disciplina_area_conhecimento_id, disciplina_codigo_inep, disciplina_ordem, disciplina_nome, disciplina_nome_abrev, disciplina_id_campos_exp FROM smc_disciplina WHERE disciplina_id = :colname_Disciplina";
  $stmt_Disciplina = $SmecelNovo->prepare($query_Disciplina);
  $stmt_Disciplina->bindValue(':colname_Disciplina', $colname_Disciplina, PDO::PARAM_INT);
  $stmt_Disciplina->execute();
  $row_Disciplina = $stmt_Disciplina->fetch(PDO::FETCH_ASSOC);
  $totalRows_Disciplina = $stmt_Disciplina->rowCount();


  // PDO query for Turma
  $colname_Turma = "-1";
  if (isset($_GET['turma'])) {
    $colname_Turma = $_GET['turma'];
  }
  $query_Turma = "SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo FROM smc_turma WHERE turma_id = :colname_Turma";
  $stmt_Turma = $SmecelNovo->prepare($query_Turma);
  $stmt_Turma->bindValue(':colname_Turma', $colname_Turma, PDO::PARAM_INT);
  $stmt_Turma->execute();
  $row_Turma = $stmt_Turma->fetch(PDO::FETCH_ASSOC);
  $totalRows_Turma = $stmt_Turma->rowCount();


  //consulta antiga
  $query_componente = "
  SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, 
         ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola, 
         turma_id, turma_nome, turma_ano_letivo, turma_turno, turma_etapa, turma_matriz_id, 
         disciplina_id, disciplina_nome
  FROM smc_ch_lotacao_professor
  INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
  INNER JOIN smc_disciplina ON disciplina_id = ch_lotacao_disciplina_id
  WHERE turma_ano_letivo = :ano_letivo 
    AND ch_lotacao_turma_id = :turma_id 
    AND ch_lotacao_professor_id = :professor_id
  GROUP BY disciplina_id
  ORDER BY disciplina_nome ASC
";

  $stmt_componente = $SmecelNovo->prepare($query_componente);
  $stmt_componente->bindValue(':ano_letivo', ANO_LETIVO, PDO::PARAM_INT);
  $stmt_componente->bindValue(':turma_id', $colname_Turma, PDO::PARAM_INT);
  $stmt_componente->bindValue(':professor_id', ID_PROFESSOR, PDO::PARAM_INT);
  $stmt_componente->execute();
  $componentes = $stmt_componente->fetchAll(PDO::FETCH_ASSOC);


  // PDO query for Matriz
  $query_Matriz = "SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash, matriz_dias_letivos, matriz_semanas_letivas, matriz_dias_semana, matriz_minutos_aula, matriz_aula_dia, matriz_criterio_avaliativo FROM smc_matriz WHERE matriz_id = :matriz_id";
  $stmt_Matriz = $SmecelNovo->prepare($query_Matriz);
  $stmt_Matriz->bindValue(':matriz_id', $row_Turma['turma_matriz_id'], PDO::PARAM_INT);
  $stmt_Matriz->execute();
  $row_Matriz = $stmt_Matriz->fetch(PDO::FETCH_ASSOC);
  $totalRows_Matriz = $stmt_Matriz->rowCount();

  // PDO query for Criterios
  $query_Criterios = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes, ca_grupo_etario, ca_grupo_conceito FROM smc_criterios_avaliativos WHERE ca_id = :ca_id";
  $stmt_Criterios = $SmecelNovo->prepare($query_Criterios);
  $stmt_Criterios->bindValue(':ca_id', $row_Matriz['matriz_criterio_avaliativo'], PDO::PARAM_INT);
  $stmt_Criterios->execute();
  $row_Criterios = $stmt_Criterios->fetch(PDO::FETCH_ASSOC);
  $totalRows_Criterios = $stmt_Criterios->rowCount();

  // PDO query for Campos
  $query_Campos = "SELECT campos_exp_id, campos_exp_nome, campos_exp_mais, campos_exp_orientacoes, campos_exp_direitos FROM smc_campos_exp WHERE campos_exp_id = :campos_exp_id";
  $stmt_Campos = $SmecelNovo->prepare($query_Campos);
  $stmt_Campos->bindValue(':campos_exp_id', $row_Disciplina['disciplina_id_campos_exp'], PDO::PARAM_INT);
  $stmt_Campos->execute();
  $row_Campos = $stmt_Campos->fetch(PDO::FETCH_ASSOC);
  $totalRows_Campos = $stmt_Campos->rowCount();

  // PDO query for GrupoConceitos
  $query_GrupoConceitos = "SELECT conceito_itens_id, conceito_itens_id_conceito, conceito_itens_descricao, conceito_itens_legenda, conceito_itens_peso FROM smc_conceito_itens WHERE conceito_itens_id_conceito = :ca_grupo_conceito";
  $stmt_GrupoConceitos = $SmecelNovo->prepare($query_GrupoConceitos);
  $stmt_GrupoConceitos->bindValue(':ca_grupo_conceito', $row_Criterios['ca_grupo_conceito'], PDO::PARAM_INT);
  $stmt_GrupoConceitos->execute();
  $row_GrupoConceitos = $stmt_GrupoConceitos->fetch(PDO::FETCH_ASSOC);
  $totalRows_GrupoConceitos = $stmt_GrupoConceitos->rowCount();

  // Collecting all concepts
  $conceito = [];
  do {
    $conceito[] = $row_GrupoConceitos['conceito_itens_peso'] . "|" . $row_GrupoConceitos['conceito_itens_legenda'] . "|" . $row_GrupoConceitos['conceito_itens_descricao'];
  } while ($row_GrupoConceitos = $stmt_GrupoConceitos->fetch(PDO::FETCH_ASSOC));

  $colname_Periodo = "1";
  if (isset($_GET['periodo'])) {
    $colname_Periodo = $_GET['periodo'];
  } else {
    $colname_Periodo = "1";
  }

} catch (PDOException $e) {
  echo "Error: " . $e->getMessage();
}

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
  <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/sweetalert2.min.css">
</head>

<body>
  <?php include_once "inc/navebar.php"; ?>
  <?php include_once "inc/sidebar.php"; ?>
  <main class="ls-main">
    <div class="container-fluid">
      <h1 class="ls-title-intro ls-ico-home">Ano letivo <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h1>
      <p><a
          href="rendimento_alunos.php?escola=<?php echo $row_Turma['turma_id_escola']; ?>&etapa=<?php echo $row_Turma['turma_etapa']; ?>&componente=<?php echo $colname_Disciplina; ?>&turma=<?php echo $colname_Turma; ?>"
          class="ls-btn ls-ico-chevron-left">Voltar</a></p>


      <blockquote>
        <span style="margin-right:10px; text-align:center; float:left;">
          <?php if ($row_Matricula['aluno_foto'] == "") { ?>
            <img src="<?php echo '../../aluno/fotos/' ?>semfoto.jpg" class="" border="0" width="50">
          <?php } else { ?>
            <img src="<?php echo '../../aluno/fotos/' ?><?php echo $row_Matricula['aluno_foto']; ?>" class="" border="0"
              width="50">
          <?php } ?>
          <?php //echo $row_Alunos['aluno_nome']; ?>
        </span> Turma: <strong><?php echo $row_Turma['turma_nome']; ?></strong><br>
        Disciplina: <strong><?php echo $row_Disciplina['disciplina_nome']; ?></strong><br>
        Aluno(a): <strong><?php echo $row_Matricula['aluno_nome']; ?></strong>
      </blockquote>

      <hr>

      <div class="ls-box">
        <span class="">
          Preencha o campo abaixo separando os períodos (bimestre/unidade) por parágrafo.
        </span>
      </div>


      <div id="status"></div>

      <button data-ls-module="modal" data-target="#myAwesomeModal" class="ls-btn-primary">Ative a modal</button>

      <div class="ls-modal" id="myAwesomeModal">
        <div class="ls-modal-box">
          <div class="ls-modal-header">
            <button data-dismiss="modal">&times;</button>
            <h4 class="ls-modal-title">Modal title</h4>

          </div>
          <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
            <div class="ls-modal-body" id="myModalBody">
              <label class="ls-label">
                <b class="ls-label-text">Mensagem</b>
                <textarea rows="4" name="p_col_parecer"></textarea>
              </label>

              <div class="ls-custom-select">
                <select name="p_col_periodo" class="ls-select">
                  <?php for ($i = 1; $i < $row_Criterios['ca_qtd_periodos'] + 1; $i++) { ?>
                    <option value="<?php echo $i; ?>" <?php if (!(strcmp($i, ""))) {
                         echo "SELECTED";
                       } ?>><?php echo $i; ?>º
                      PERÍODO/UNIDADE</option>
                  <?php } ?>
                </select>
              </div>

              <input type="hidden" name="MM_insert" value="form1">
            </div>
            <div class="ls-modal-footer">
              <input type="submit" value="SALVAR" class="ls-btn ls-float-right" data-dismiss="modal" value="SALVAR">
              <button type="submit" class="ls-btn-primary">Save changes</button>
            </div>
          </form>
        </div>
      </div><!-- /.modal -->




    </div>
    <?php //include_once "inc/footer.php"; ?>
  </main>
  <?php include_once "inc/notificacoes.php"; ?>
  <!-- We recommended use jQuery 1.10 or up -->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="js/locastyle.js"></script>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="js/sweetalert2.min.js"></script>
  <!--<script src="//cdn.tinymce.com/4/tinymce.min.js1"></script>-->
  <!--<script src="https://cdn.tiny.cloud/1/iq1gy8qt7s1b9bj92c2fc7whie95augu8r8kz97dgoufkli8/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>-->
  <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
  <script src="langs/pt_BR.js"></script>

  <script type="text/javascript">

    $(document).ready(function () {
      $("#salvar").click(function () {

        var conteudo = $("#rel_avaliativo").val();
        var matricula = $("#matricula").val();
        var componentes = [];

        // Percorre os checkboxes marcados e adiciona ao array
        $("input[name='componentes[]']:checked").each(function () {
          componentes.push($(this).val());
        });

        $.ajax({
          type: 'POST',
          url: 'fnc/lancaRel.php',
          data: {
            conteudo: conteudo,
            matricula: matricula,
            componentes: componentes // Enviamos um array com os componentes selecionados
          },
          success: function (data) {
            $('#status').html(data);
            setTimeout(function () {
              $("#status").html("");
            }, 15000);
          }
        });

      });
    });




    $(function () {
      $(document).on('click', 'input[type=text]', function () {
        this.select();
      });
    });

  </script>

  <script>
    $('#rel_avaliativo').summernote({
      placeholder: 'Digite aqui...',
      tabsize: 2,
      height: 120,
      toolbar: [
        ['style', ['style']],
        ['font', ['bold', 'underline', 'clear']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['table', ['table']],
        ['insert', []],
        ['view', []]
      ]
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