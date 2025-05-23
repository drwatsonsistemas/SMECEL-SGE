<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include('../../sistema/funcoes/inverteData.php'); ?>
<?php include('../../sistema/funcoes/url_base.php'); ?>
<?php include('../../sistema/funcoes/idade.php'); ?>
<?php include('../../sistema/funcoes/anti_injection.php'); ?>
<?php include('funcoes/session.php'); ?>

<?php
if (isset($_GET['uid'])) {
  $hash = anti_injection($_GET['uid']);
} else {
  $hash = anti_injection($row_AlunoLogado['aluno_hash']);
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Perfil = sprintf("
  SELECT * FROM smc_aluno WHERE aluno_hash = %s", GetSQLValueString($hash, "text"));
$Perfil = mysql_query($query_Perfil, $SmecelNovo) or die(mysql_error());
$row_Perfil = mysql_fetch_assoc($Perfil);
$totalRows_Perfil = mysql_num_rows($Perfil);
if ($totalRows_Perfil == "") {
  header("Location: index.php?loginErr");
}

$query_Fan = sprintf("
  SELECT * FROM smc_aluno_fans WHERE aluno_fan_aluno_de_id = %s AND aluno_fan_aluno_para_id = %s", GetSQLValueString($row_AlunoLogado['aluno_id'], "int"), GetSQLValueString($row_Perfil['aluno_id'], "int"));
$Fan = mysql_query($query_Fan, $SmecelNovo) or die(mysql_error());
$row_Fan = mysql_fetch_assoc($Fan);
$totalRows_Fan = mysql_num_rows($Fan);

$query_FanConta = sprintf("
  SELECT * FROM smc_aluno_fans WHERE aluno_fan_aluno_para_id = %s", GetSQLValueString($row_Perfil['aluno_id'], "int"));
$FanConta = mysql_query($query_FanConta, $SmecelNovo) or die(mysql_error());
$row_FanConta = mysql_fetch_assoc($FanConta);
$totalRows_FanConta = mysql_num_rows($FanConta);



mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matricula = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, 
vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, 
vinculo_aluno_vacina_atualizada, turma_id, turma_nome, turma_turno, turma_tipo_atendimento, vinculo_aluno_vacina_data_retorno, escola_id, escola_nome, sec_id, sec_cidade, sec_uf 
FROM smc_vinculo_aluno 
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma 
INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola
INNER JOIN smc_sec ON sec_id = vinculo_aluno_id_sec
WHERE vinculo_aluno_id_aluno = '$row_Perfil[aluno_id]' AND turma_tipo_atendimento = '1' ORDER BY vinculo_aluno_id DESC LIMIT 0,1";
$Matricula = mysql_query($query_Matricula, $SmecelNovo) or die(mysql_error());
$row_Matricula = mysql_fetch_assoc($Matricula);
$totalRows_Matricula = mysql_num_rows($Matricula);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Colegas = "
SELECT 
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola,
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_dependencia,
vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, aluno_id, aluno_nome, aluno_foto, aluno_hash 
FROM smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
WHERE vinculo_aluno_id_turma = '$row_Matricula[vinculo_aluno_id_turma]' AND vinculo_aluno_dependencia = 'N'
ORDER BY RAND()
";
$Colegas = mysql_query($query_Colegas, $SmecelNovo) or die(mysql_error());
$row_Colegas = mysql_fetch_assoc($Colegas);
$totalRows_Colegas = mysql_num_rows($Colegas);



$query_Depoimentos_Outros = "
SELECT 
smc_aluno_depoimentos.aluno_depoimento_id,
smc_aluno_depoimentos.aluno_depoimento_id_de,
smc_aluno_depoimentos.aluno_depoimento_id_para,
smc_aluno_depoimentos.aluno_depoimento_texto,
smc_aluno_depoimentos.aluno_depoimento_data,
smc_aluno_depoimentos.aluno_depoimento_status,
smc_aluno.aluno_id,
smc_aluno.aluno_nome,
smc_aluno.aluno_foto,
smc_aluno.aluno_hash
FROM 
smc_aluno_depoimentos
INNER JOIN 
smc_aluno ON smc_aluno.aluno_id = aluno_depoimento_id_de
WHERE 
aluno_depoimento_status = 'P'
AND
aluno_depoimento_id_para = $row_Perfil[aluno_id]
ORDER BY 
aluno_depoimento_data DESC
";

$query_Depoimentos_Logado = "
SELECT 
smc_aluno_depoimentos.aluno_depoimento_id,
smc_aluno_depoimentos.aluno_depoimento_id_de,
smc_aluno_depoimentos.aluno_depoimento_id_para,
smc_aluno_depoimentos.aluno_depoimento_texto,
smc_aluno_depoimentos.aluno_depoimento_data,
smc_aluno_depoimentos.aluno_depoimento_status,
smc_aluno.aluno_id,
smc_aluno.aluno_nome,
smc_aluno.aluno_foto,
smc_aluno.aluno_hash
FROM 
smc_aluno_depoimentos
INNER JOIN 
smc_aluno ON smc_aluno.aluno_id = aluno_depoimento_id_de
WHERE 
aluno_depoimento_id_para = '$row_AlunoLogado[aluno_id]'
ORDER BY 
aluno_depoimento_data DESC
";

// Supondo que você tenha a variável $alunoLogado que contém o ID do aluno logado ou NULL se não estiver logado
if ($row_Perfil['aluno_id'] == $row_AlunoLogado['aluno_id']) {
  // Consulta para o aluno logado
  $query_Depoimentos = $query_Depoimentos_Logado;
} else {
  // Consulta para outros alunos
  $query_Depoimentos = $query_Depoimentos_Outros;
}

// Executar a consulta
$Depoimentos = mysql_query($query_Depoimentos, $SmecelNovo) or die(mysql_error());
$row_Depoimentos = mysql_fetch_assoc($Depoimentos);
$totalRows_Depoimentos = mysql_num_rows($Depoimentos);


if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

  $aluno_bio = anti_injection($_POST['aluno_bio']);
  $cor_fundo = anti_injection($_POST['color']);

  $instagram = anti_injection($_POST['instagram']);
  $twitter = anti_injection($_POST['twitter']);
  $facebook = anti_injection($_POST['facebook']);

  $updateSQL = sprintf(
    "UPDATE smc_aluno 
    SET 
    aluno_bio = '$aluno_bio',
    aluno_cor_fundo = '$cor_fundo',
    aluno_insta = '$instagram',
    aluno_face = '$facebook',
    aluno_x = '$twitter'
    WHERE aluno_hash=%s",
    GetSQLValueString($_POST['aluno_hash'], "text")
  );


  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());


  $updateGoTo = "perfil.php";
  //if (isset($_SERVER['QUERY_STRING'])) {
  //$updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
  //$updateGoTo .= $_SERVER['QUERY_STRING'];
  //}
  header(sprintf("Location: %s", $updateGoTo));


  //$updateGoTo = "celular.php?aluno=".$_POST['aluno_hash'];

}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    gtag('js', new Date());

    gtag('config', 'UA-117872281-1');
  </script>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Mural de <?php echo $row_Perfil['aluno_nome']; ?> - EduConnect</title>
  <!--Import Google Icon Font-->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <!--Import materialize.css-->
  <link type="text/css" rel="stylesheet" href="../css/materialize.min.css" media="screen,projection" />
  <link type="text/css" rel="stylesheet" href="../css/app.css" media="screen,projection" />
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
  <link type="text/css" rel="stylesheet" href="css/geral.css" media="screen,projection" />
  <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>

</head>

<body class="<?= $row_Perfil['aluno_cor_fundo'] ?> lighten-4">


  <?php include "menu_top_social.php" ?>

  <div class="container">


    <div class="row white" style="margin: 10px 0;">

      <div class="col s12 m2 hide-on-small-only">
        <p>

          <a href="perfil.php?uid=<?php echo $row_Perfil['aluno_hash']; ?>">

          <?php 
        if (!empty($row_AlunoLogado['aluno_foto2'])) { ?>
            <img src="<?php echo URL_BASE.'aluno/fotos2/' . $row_AlunoLogado['aluno_foto2']; ?>" width="100%" class="hoverable">
        <?php } elseif (!empty($row_AlunoLogado['aluno_foto'])) { ?>
            <img src="<?php echo URL_BASE.'aluno/fotos/' . $row_AlunoLogado['aluno_foto']; ?>" width="100%" class="hoverable">
        <?php } else { ?>
            <img src="<?php echo URL_BASE.'aluno/fotos2/semfoto.jpg'; ?>" width="100%" class="hoverable">
        <?php } ?>
            <br>
            <small style="font-size:14px;">
              <?php echo current(str_word_count($row_Perfil['aluno_nome'], 2)); ?>
              <?php $word = explode(" ", trim($row_Perfil['aluno_nome']));
              echo $word[count($word) - 1]; ?>
            </small>
          </a>

        </p>


        <?php include "menu_esq_social.php"; ?>

      </div>

      <div class="col s12 m7">

        <!-- Para desktop: Exibe as informações do aluno com blockquote -->
        <div class="hide-on-small-only">
          <blockquote>
            <h5 class="truncate">
              <i class="material-icons left small orange-text">message</i> Depoimentos para
              <?php echo current(str_word_count($row_Perfil['aluno_nome'], 2)); ?>
              <?php $word = explode(" ", trim($row_Perfil['aluno_nome'])); ?>
            </h5>


          </blockquote>
        </div>

        <!-- Para celular: Exibe a imagem ao lado das informações do aluno, sem blockquote -->
        <div class="hide-on-med-and-up">

          <blockquote style="word-wrap: break-word;">
            <h5 class="truncate1">
              <i class="material-icons left medium orange-text">message</i> Depoimentos para
              <strong><?php echo current(str_word_count($row_Perfil['aluno_nome'], 2)); ?></strong>
              <?php $word = explode(" ", trim($row_Perfil['aluno_nome'])); ?>
            </h5>


          </blockquote>
        </div>



        <!-- CONTEÚDO-->


        <a href="perfil.php?uid=<?php echo $row_Perfil['aluno_hash']; ?>" class="btn-flat btn-small"
          style="border-radius: 30px;"><i class="material-icons">arrow_back</i></a>


        <?php if ($hash != $row_AlunoLogado['aluno_hash']) { ?>

          <div class="row light-blue lighten-5">
            <form class="col s12" method="post" id="depoimento">

              <div class="row">
                <div class="input-field col s12">
                  <i class="material-icons prefix">mode_edit</i>
                  <textarea id="depoimento" name="aluno_depoimento" class="materialize-textarea" data-length="120"
                    required></textarea>
                  <label for="depoimento">Escreva algo legal para
                    <?php echo current(str_word_count($row_Perfil['aluno_nome'], 2)); ?></label>
                  <span class="helper-text" data-error="wrong" data-success="right">Lembre-se de seguir os critérios
                    estabelecidos nos termos de uso da rede.</span>
                </div>

                <div class="input-field col s12">
                  <button type="submit"
                    class="waves-effect waves-orange btn-small right <?= $row_Perfil['aluno_cor_fundo'] ?>">ENVIAR
                    MENSAGEM<i class="material-icons right">send</i></button>
                </div>
              </div>

            </form>
          </div>

          <hr>

        <?php } ?>







        <?php if ($totalRows_Depoimentos > 0): ?>





          <?php do { ?>


            <h5>Últimos depoimentos</h5>

            <div class="card-panel <?= $row_Perfil['aluno_cor_fundo'] ?> lighten-5">





              <div class="row">

                <div class="col s3 m1">
                  <a href="perfil.php?uid=<?php echo $row_Depoimentos['aluno_hash']; ?>">
                    <?php if ($row_Depoimentos['aluno_foto'] == "") { ?>
                      <img src="<?php echo URL_BASE . 'aluno/fotos/' ?>semfoto.jpg" class="aluno">
                    <?php } else { ?>
                      <img src="<?php echo URL_BASE . 'aluno/fotos/' ?><?php echo $row_Depoimentos['aluno_foto']; ?>"
                        class="aluno">
                    <?php } ?>
                  </a>
                </div>

                <div class="col s9 m11">
                  <strong>
                    <a href="perfil.php?uid=<?php echo $row_Depoimentos['aluno_hash']; ?>">
                      <?php echo current(str_word_count($row_Depoimentos['aluno_nome'], 2)); ?>
                      <?php $wordD = explode(" ", trim($row_Depoimentos['aluno_nome']));
                      echo $wordD[count($wordD) - 1]; ?>
                    </a>
                    <br>
                  </strong>
                  <p class="" style="word-wrap: break-word;">
                    <?php echo nl2br($row_Depoimentos['aluno_depoimento_texto']); ?>
                  </p>
                  <small
                    class="right grey-text"><?= date("d/m - H\hi", strtotime($row_Depoimentos['aluno_depoimento_data'])) ?></small>
                </div>

              </div>





              <?php if ($hash == $row_AlunoLogado['aluno_hash']): ?>

                <div class="row">

                  <div class="col s12">
                    <hr>
                    <a style="cursor: pointer;" data-depoimento-id="<?php echo $row_Depoimentos['aluno_depoimento_id']; ?>"
                      id="btn-denuncia<?= $row_Depoimentos['aluno_depoimento_id']; ?>">
                      <i class="material-icons right" style="color: red;">warning</i>
                    </a>
                    <div class="switch">
                      <label>
                        Esconder
                        <input type="checkbox" class="status-switch"
                          data-depoimento-id="<?php echo $row_Depoimentos['aluno_depoimento_id']; ?>" <?php echo $row_Depoimentos['aluno_depoimento_status'] == 'P' ? 'checked' : ''; ?>>
                        <span class="lever"></span>
                        Exibir
                      </label>
                    </div>
                  </div>
                </div>
              <?php endif; ?>
            </div>

            <script>
              $("#btn-denuncia<?= $row_Depoimentos['aluno_depoimento_id']; ?>").click(function () {
                Swal.fire({
                  title: "Deseja denunciar esse depoimento?",
                  showDenyButton: true,
                  confirmButtonText: "Sim, denunciar",
                  denyButtonText: `Não, cancelar`
                }).then((result) => {
                  /* Read more about isConfirmed, isDenied below */
                  if (result.isConfirmed) {
                    var depoimentoId = $(this).data('depoimento-id');
                    var status = 'D';
                    $.ajax({
                      url: 'funcoes/atualizar_status_depoimento.php', // O arquivo PHP que vai processar o AJAX
                      method: 'POST',
                      data: {
                        aluno_depoimento_id: depoimentoId,
                        aluno_depoimento_status: status
                      },
                      success: function (response) {
                        // Código a ser executado em caso de sucesso
                        console.log(response); // Apenas para depuração
                        M.toast({ html: 'Depoimento denunciado!' }); // Mostra uma mensagem de sucesso
                      },
                      error: function (xhr, status, error) {
                        // Código a ser executado em caso de erro
                        console.log(error); // Apenas para depuração
                        M.toast({ html: 'Ocorreu um erro ao denunciar o depoimento, tente novamente mais tarde.' }); // Mostra uma mensagem de erro
                      }
                    });
                  }
                });
              })
            </script>
          <?php } while ($row_Depoimentos = mysql_fetch_assoc($Depoimentos)); ?>
        <?php else: ?>
          <p class="center">Nenhum depoimento disponível.</p>
        <?php endif; ?>

      </div>


      <div class="col s12 m3">
      </div>
    </div>



    <!--JavaScript at end of body for optimized loading-->
    <script type="text/javascript" src="../js/materialize.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript">
      $(document).ready(function () {
        $('.sidenav').sidenav();
        $('.tabs').tabs();
        $('.dropdown-trigger').dropdown();
        $('.materialboxed').materialbox();
        $('.modal').modal();
        $('textarea#bio').characterCounter();
        $('.fixed-action-btn').floatingActionButton();
        $('.tooltipped').tooltip();
      });
    </script>

    <script type="text/javascript">

      document.addEventListener('DOMContentLoaded', function () {
        var bioTextarea = document.getElementById('bio');
        var maxLength = bioTextarea.getAttribute('data-length');

        bioTextarea.addEventListener('input', function () {
          if (bioTextarea.value.length >= maxLength) {
            bioTextarea.value = bioTextarea.value.substring(0, maxLength); // Corta o texto para o máximo permitido
          }
        });
      });

    </script>

    <script type="text/javascript">
      $(document).ready(function () {
        $('.status-switch').change(function () {
          var isChecked = $(this).is(':checked');
          var depoimentoId = $(this).data('depoimento-id');
          var status = isChecked ? 'P' : 'N'; // Define o status com base no estado do switch

          $.ajax({
            url: 'funcoes/atualizar_status_depoimento.php', // O arquivo PHP que vai processar o AJAX
            method: 'POST',
            data: {
              aluno_depoimento_id: depoimentoId,
              aluno_depoimento_status: status
            },
            success: function (response) {
              // Código a ser executado em caso de sucesso
              console.log(response); // Apenas para depuração
              M.toast({ html: 'Status atualizado com sucesso!' }); // Mostra uma mensagem de sucesso
            },
            error: function (xhr, status, error) {
              // Código a ser executado em caso de erro
              console.log(error); // Apenas para depuração
              M.toast({ html: 'Ocorreu um erro ao atualizar o status.' }); // Mostra uma mensagem de erro
            }
          });
        });
      });
    </script>

    <script type="text/javascript">
      $(document).ready(function () {
        $('#depoimento').off('submit').on('submit', function (event) {
          event.preventDefault(); // Evita o envio padrão do formulário

          var depoimento = $('textarea[name="aluno_depoimento"]').val(); // Captura o texto do depoimento

          $.ajax({
            url: 'funcoes/depoimentos.php', // O arquivo PHP que vai processar o AJAX
            method: 'POST',
            data: {
              aluno_depoimento: depoimento,
              aluno_id: '<?php echo $row_Perfil['aluno_id']; ?>'
            },
            success: function (response) {
              // Verifica se houve sucesso ou erro com base na resposta do servidor
              if (response.success) {
                // Se a mensagem for enviada com sucesso
                M.toast({ html: response.message }); // Mostra uma mensagem de sucesso
                $('textarea[name="aluno_depoimento"]').val(''); // Limpa o campo de texto
              } else {
                // Se a mensagem contiver palavras proibidas ou houve outro erro
                //M.toast({html: response.message}); // Mostra a mensagem de erro

                Swal.fire({
                  title: "Verifique sua mensagem!",
                  html: response.message,
                  icon: "warning"
                });

              }
            },
            error: function (xhr, status, error) {
              // Código a ser executado em caso de erro
              console.log(error); // Apenas para depuração
              M.toast({ html: 'Ocorreu um erro ao enviar o depoimento.' }); // Mostra uma mensagem de erro
            }
          });
        });
      });
    </script>

</body>

</html>
<?php
mysql_free_result($Matricula);

mysql_free_result($AlunoLogado);
?>