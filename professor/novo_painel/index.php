<?php require_once('../../Connections/SmecelNovoPDO.php'); ?>
<?php include "conf/session.php"; ?>
<?php

$query_turmas = "
            SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, 
            ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola, turma_id, turma_nome, turma_ano_letivo, turma_turno, turma_etapa, escola_id, escola_nome,
            CASE turma_turno
                WHEN 0 THEN 'INTEGRAL'
                WHEN 1 THEN 'MATUTINO'
                WHEN 2 THEN 'VESPERTINO'
                WHEN 3 THEN 'NOTURNO'
            END AS turma_turno_nome 
            FROM smc_ch_lotacao_professor
            INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
            INNER JOIN smc_escola ON escola_id = ch_lotacao_escola
            WHERE turma_ano_letivo = :ano_letivo AND ch_lotacao_professor_id = :professor_id
            GROUP BY turma_id
            ORDER BY turma_turno, turma_etapa, turma_nome ASC
        ";
$stmt_turmas = $SmecelNovo->prepare($query_turmas);
$stmt_turmas->bindValue(':ano_letivo', ANO_LETIVO, PDO::PARAM_INT);
$stmt_turmas->bindValue(':professor_id', ID_PROFESSOR, PDO::PARAM_INT);
$stmt_turmas->execute();
$count_turmas = $stmt_turmas->rowCount();

$query_alunos = "
    SELECT COUNT(DISTINCT va.vinculo_aluno_id_aluno) AS total_alunos
    FROM smc_ch_lotacao_professor lp
    INNER JOIN smc_turma t ON t.turma_id = lp.ch_lotacao_turma_id
    INNER JOIN smc_vinculo_aluno va ON va.vinculo_aluno_id_turma = t.turma_id
    WHERE t.turma_ano_letivo = :ano_letivo 
      AND lp.ch_lotacao_professor_id = :professor_id 
      AND va.vinculo_aluno_ano_letivo = :ano_letivo
";
$stmt_alunos = $SmecelNovo->prepare($query_alunos);
$stmt_alunos->bindValue(':ano_letivo', ANO_LETIVO, PDO::PARAM_INT);
$stmt_alunos->bindValue(':professor_id', ID_PROFESSOR, PDO::PARAM_INT);
$stmt_alunos->execute();
$result_alunos = $stmt_alunos->fetch(PDO::FETCH_ASSOC);
$total_alunos = $result_alunos['total_alunos'];


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
  <link rel="stylesheet" type="text/css" href="//assets.locaweb.com.br/locastyle/edge/stylesheets/locastyle.css">
  <link rel="stylesheet" href="css/sweetalert2.min.css">
</head>

<body>
  <?php include_once "inc/navebar.php"; ?>
  <?php include_once "inc/sidebar.php"; ?>
  <main class="ls-main">
    <div class="container-fluid">
      <h1 class="ls-title-intro ls-ico-home">Ano letivo <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h1>
      <div class="ls-box">
        😃 Seja bem-vind<?php if ($row_ProfLogado['func_sexo'] == 2) {
          echo "a";
        } else {
          echo "o";
        } ?>, <strong class="ls-text-capitalize"><?php $nomeProf = explode(" ", $row_ProfLogado['func_nome']);
         echo ucfirst(strtolower($nomeProf[0])); ?></strong>!

        <?php if (date("m-d", strtotime($row_ProfLogado['func_data_nascimento'])) == (date("m-d"))) { ?>
          <img src="../../img/bolo.png" width="15"> Que seu dia seja repleto de felicidades.
          <strong>Parabéns!</strong>
        <?php } ?>
      </div>

      <?php if (isset($_GET['permissao'])) { ?>
        <div class="ls-alert-warning"><strong>Atenção:</strong> Você não tem permissão para acessar aquela página. Um email foi enviado para o administrador do sistema.</div>
      <?php } ?>

      <div class="ls-txt-center">
        <div class="row">
          <a href="aulas_calendario.php?target=aulas">
            <div class="col-md-3 col-xs-6 ls-background-success1 ls-md-space"
              style="background-color:#990000; color:#FFFFFF">
              <p><span class="ls-display-none-xs">REGISTRO DE </span>AULAS</p>
              <h1 class="ls-ico-book"></h1>
              <p><span class="ls-btn ls-btn-primary ls-btn-xs">ACESSAR</span></p>
            </div>
          </a>
          <a href="selecionar.php?target=frequencia&data=<?php echo date('Y-m-d'); ?>">
            <div class="col-md-3 col-xs-6 ls-background-info ls-md-space" style="background-color:#063; color:#FFFFFF">
              <p><span class="ls-display-none-xs">REGISTRAR </span>FREQUÊNCIA</p>
              <h1 class="ls-ico-checkbox-checked"></h1>
              <p><span class="ls-btn ls-btn-primary ls-btn-xs">ACESSAR</span></p>
            </div>
          </a>
          <a href="rendimento.php">
            <div class="col-md-3 col-xs-6 ls-background-primary ls-md-space"
              style="background-color:#C66; color:#FFFFFF">
              <p>RENDIMENTO</p>
              <h1 class="ls-ico-bars"></h1>
              <p><span class="ls-btn ls-btn-primary ls-btn-xs">ACESSAR</span></p>
            </div>
          </a>
          <a href="planejamento_mapa.php">
            <div class="col-md-3 col-xs-6 ls-background-warning ls-md-space"
              style="background-color:#F60; color:#FFFFFF">
              <p>PLANEJAMENTO</p>
              <h1 class="ls-ico-numbered-list"></h1>
              <p><span class="ls-btn ls-btn-primary ls-btn-xs">ACESSAR</span></p>
            </div>
          </a>
        </div>
      </div>

      <p>&nbsp;</p>

      <div class="ls-box ls-board-box">
        <header class="ls-info-header">
          <p class="ls-float-right ls-float-none-xs ls-small-info">Atualizado em
            <strong><?php echo date("d/m/Y"); ?></strong>
          </p>
          <h2 class="ls-title-3">Recursos do Professor</h2>
        </header>

        <div id="sending-stats" class="row ls-clearfix">

          <div class="col-sm-6 col-md-6">
            <div class="ls-box">
              <div class="ls-box-head">
                <h6 class="ls-title-4">TURMAS</h6>
              </div>
              <div class="ls-box-body">
                <strong><?php echo $count_turmas; ?></strong>
                <small>*de todas as escolas vinculadas</small>
              </div>
              <div class="ls-box-footer">
                <a href="grade_analitica.php" class="ls-btn ls-btn-xs">Ver horários</a>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-md-6">
            <div class="ls-box">
              <div class="ls-box-head">
                <h6 class="ls-title-4">ALUNOS</h6>
              </div>
              <div class="ls-box-body">
                <strong><?php echo $total_alunos; ?></strong>
                <small>*de todas as escolas vinculadas</small>
              </div>
              <div class="ls-box-footer">
                <a href="seleciona_turma.php" class="ls-btn ls-btn-xs">Gerenciar alunos</a>
              </div>
            </div>
          </div>
        </div>
        <br>
        <p class="ls-txt-right">Status: <strong class="ls-color-success">Ativo</strong>
          <span style="clear: both;" class="ls-ico-help anti-fraud-pop" data-trigger="hover" data-ls-module="popover"
            data-placement="left" data-custom-class="ls-width-300" data-title="Sua conta está ativa!"
            data-content="Você tem vínculo com escolas e turmas."></span>
        </p>
      </div>

      <!-- <div class="ls-box ls-board-box">
        <header class="ls-info-header">
          <p class="ls-float-right ls-float-none-xs ls-small-info">Atualizado em
            <strong><?php echo date("d/m/Y"); ?></strong>
          </p>
          <h2 class="ls-title-3">Dashboard</h2>
        </header>

        <div id="sending-stats" class="row ls-clearfix">
          <div class="col-sm-6 col-md-3">
            <div class="ls-box">
              <div class="ls-box-head">
                <h6 class="ls-title-4">TURMAS</h6>
              </div>
              <div class="ls-box-footer">
                <a href="grade_analitica.php" class="ls-btn ls-btn-xs">Ver horários</a>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-md-3">
            <div class="ls-box">
              <div class="ls-box-head">
                <h6 class="ls-title-4">LOGINS</h6>
              </div>
              <div class="ls-box-footer">
                <a href="logins.php" class="ls-btn ls-btn-xs">Ver logins</a>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-md-3">
            <div class="ls-box">
              <div class="ls-box-head">
                <h6 class="ls-title-4">CURSOS</h6>
              </div>
              <div class="ls-box-footer">
                <a href="#" class="ls-btn ls-btn-xs">Ver cursos</a>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-md-3">
            <div class="ls-box">
              <div class="ls-box-head">
                <h6 class="ls-title-4">AVISOS</h6>
              </div>
              <div class="ls-box-footer">
                <a href="avisos.php" class="ls-btn ls-btn-xs">Ver avisos</a>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-md-3">
            <div class="ls-box">
              <div class="ls-box-head">
                <h6 class="ls-title-4">ALUNOS</h6>
              </div>
              <div class="ls-box-footer">
                <a href="alunos.php" class="ls-btn ls-btn-xs">Gerenciar alunos</a>
              </div>
            </div>
          </div>
        </div>
        <br>
        <p class="ls-txt-right">Status: <strong class="ls-color-success">Ativo</strong>
          <span style="clear: both;" class="ls-ico-help anti-fraud-pop" data-trigger="hover" data-ls-module="popover"
            data-placement="left" data-custom-class="ls-width-300" data-title="Sua conta está ativa!"
            data-content="Você tem vínculo com escolas e turmas."></span>
        </p>
      </div> -->
    </div>
    <?php //include_once "inc/footer.php"; ?>
  </main>
  <?php include_once "inc/notificacoes.php"; ?>
  <!-- We recommended use jQuery 1.10 or up -->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="//assets.locaweb.com.br/locastyle/edge/javascripts/locastyle.js"></script>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
      const agora = new Date();
      const hora = agora.getHours();
      const dataAtual = agora.toISOString().split('T')[0];

      let saudacao, periodo;
      if (hora >= 5 && hora < 12) {
        saudacao = 'Bom dia';
        periodo = 'manha';
      } else if (hora >= 12 && hora < 18) {
        saudacao = 'Boa tarde';
        periodo = 'tarde';
      } else {
        saudacao = 'Boa noite';
        periodo = 'noite';
      }

      // Função para limpar entradas antigas
      function limparLocalStorageAntigo() {
        for (let i = 0; i < localStorage.length; i++) {
          const chave = localStorage.key(i);
          if (chave.startsWith('popup_')) {
            const dataChave = chave.split('_')[1]; // Extrai YYYY-MM-DD
            if (dataChave !== dataAtual) {
              localStorage.removeItem(chave); // Remove se não for do dia atual
            }
          }
        }
      }

      // Executar limpeza antes de verificar o popup
      limparLocalStorageAntigo();

      const chavePopup = `popup_${dataAtual}_${periodo}`;

      if (!localStorage.getItem(chavePopup)) {
        Swal.fire({
          title: `${saudacao}, Professor(a)!`,
          text: 'Lembre-se de manter a frequência e as aulas em dia.',
          icon: 'info',
          position: 'top-end',
          showConfirmButton: false,
          timer: 5000,
          toast: true
        });
        localStorage.setItem(chavePopup, 'exibido');
      }
    });
  </script>
  <script>


    jQuery('#form_cadastro').submit(function () {
      event.preventDefault();
      var dados = jQuery(this).serialize();


      $(".preload").css('display', 'block');
      //document.getElementByClass(".preload").style.display = "block";

      jQuery.ajax({
        type: "POST",
        url: "crud/model/insert.php",
        data: dados,
        success: function (data) {
          $("input").prop('disabled', true);
          $("select").prop('disabled', true);
          $("textarea").prop('disabled', true);


          $(".preload").css('display', 'none');
          $("#linkResultado").html(data);


          setTimeout(function () {
            $("#gerar_link").each(function () {
              this.reset();
            });

            $("input").prop('disabled', false);
            $("select").prop('disabled', false);
            $("textarea").prop('disabled', false);

          }, 2000);
        }
      });

      return false;
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