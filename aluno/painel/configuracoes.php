<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include('../../sistema/funcoes/inverteData.php'); ?>
<?php include('../../sistema/funcoes/url_base.php'); ?>
<?php include('../../sistema/funcoes/idade.php'); ?>
<?php include('../../sistema/funcoes/anti_injection.php'); ?>
<?php include('funcoes/session.php'); ?>

<?php

$hash = anti_injection($row_AlunoLogado['aluno_hash']);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Perfil = sprintf("
  SELECT * FROM smc_aluno WHERE aluno_hash = %s", GetSQLValueString($hash, "text"));
$Perfil = mysql_query($query_Perfil, $SmecelNovo) or die(mysql_error());
$row_Perfil = mysql_fetch_assoc($Perfil);
$totalRows_Perfil = mysql_num_rows($Perfil);
if ($totalRows_Perfil == "") {
  header("Location: index.php?loginErr");
}



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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

  $aluno_bio = anti_injection($_POST['aluno_bio']);
  $cor_fundo = anti_injection($_POST['color']); 

  $instagram = anti_injection($_POST['instagram']);
  $twitter = anti_injection($_POST['twitter']);
  $facebook = anti_injection($_POST['facebook']);

  $updateSQL = sprintf("UPDATE smc_aluno 
    SET 
    aluno_bio = '$aluno_bio',
    aluno_cor_fundo = '$cor_fundo',
    aluno_insta = '$instagram',
    aluno_face = '$facebook',
    aluno_x = '$twitter'
    WHERE aluno_hash=%s",
    GetSQLValueString($_POST['aluno_hash'], "text"));


  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());


  $updateGoTo = "configuracoes.php?salvo";
  //if (isset($_SERVER['QUERY_STRING'])) {
    //$updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    //$updateGoTo .= $_SERVER['QUERY_STRING'];
  //}
  header(sprintf("Location: %s", $updateGoTo));
}

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
      dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'UA-117872281-1');
  </script>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo $row_AlunoLogado['aluno_nome']; ?> - EduConnect</title>
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

</head>

<body class="<?= $row_Perfil['aluno_cor_fundo'] ?> lighten-5">

  <?php include "menu_top_social.php" ?>

  <div class="container">

    <div class="row white" style="margin: 10px 0;">

      <div class="col s12 m2 hide-on-small-only">
        <p>

          <a href="perfil.php?uid=<?php echo $row_Perfil['aluno_hash']; ?>">

            <?php if ($row_Perfil['aluno_foto'] == "") { ?>
              <img src="<?php echo URL_BASE . 'aluno/fotos/' ?>semfoto.jpg" width="100%" class="hoverable">
            <?php } else { ?>
              <img src="<?php echo URL_BASE . 'aluno/fotos/' ?><?php echo $row_Perfil['aluno_foto']; ?>" width="100%" class="hoverable">
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
                <i class="material-icons left small">settings</i> CONFIGURAÇÕES
              </h5>


            </blockquote>
          </div>

          <!-- Para celular: Exibe a imagem ao lado das informações do aluno, sem blockquote -->
          <div class="hide-on-med-and-up">
            <blockquote>
              <h5 class="truncate">
                <i class="material-icons left small">settings</i> CONFIGURAÇÕES
              </h5>


            </blockquote>
          </div>

          <a href="perfil.php?uid=<?php echo $row_Perfil['aluno_hash']; ?>" class="btn-flat"><i class="material-icons left">arrow_back</i> Voltar</a>


          <form class="col s12" method="post" action="configuracoes.php" name="form1" id="form1">

            <input type="hidden" name="aluno_hash" value="<?php echo $row_AlunoLogado['aluno_hash']; ?>" />
            <input type="hidden" name="aluno_id" value="<?php echo $row_AlunoLogado['aluno_id']; ?>" />
            <input type="hidden" name="MM_update" value="form1" />


                <p class="<?= $row_Perfil['aluno_cor_fundo'] ?> lighten-4"><i class="material-icons left">create</i> Sua bio:</p>

                <div class="row">
                <div class="input-field col s12">
                  <textarea id="bio" name="aluno_bio" class="materialize-textarea" data-length="120"><?= $row_Perfil['aluno_bio'] ?></textarea>
                  <label for="bio">Escreva algo sobre você</label>
                </div>
                </div>

                <p class="<?= $row_Perfil['aluno_cor_fundo'] ?> lighten-4"><i class="material-icons left">create</i> Redes sociais: (Os links serão exibidos em seu perfil, caso sejam preenchidos abaixo)</p>
                
                <div class="row">
                <div class="input-field col s12">
                  <input value="<?= $row_Perfil['aluno_insta'] ?>" name="instagram" type="text" class="validate">
                  <label for="instagram">Nome de seu usuário do Instagram</label>
                </div>
                <div class="input-field col s12">
                  <input value="<?= $row_Perfil['aluno_face'] ?>" name="facebook" id="facebook" type="text" class="validate">
                  <label for="facebook">Nome de usuário do Facebook (ou ID)</label>
                </div>
                <div class="input-field col s12">
                  <input value="<?= $row_Perfil['aluno_x'] ?>" name="twitter" id="twitter" type="text" class="validate">
                  <label for="twitter">Nome de usuário do X (Twitter)</label>
                </div>
                </div>


                <div class="row">
                <div class="input-field col s12">
                  <p class="<?= $row_Perfil['aluno_cor_fundo'] ?> lighten-4"><i class="material-icons left">create</i> Cor (tema) do seu perfil:</p>

                  <?php
                  // Defina o valor da cor do perfil
                  $corPerfil = $row_Perfil['aluno_cor_fundo'];

                  // Lista de cores com nomes em português e valores em inglês
                  $cores = [
                    'red' => ['nome' => 'Vermelho', 'cor' => '#f44336'],
                    'pink' => ['nome' => 'Rosa', 'cor' => '#e91e63'],
                    'purple' => ['nome' => 'Roxo', 'cor' => '#9c27b0'],
                    'deep-purple' => ['nome' => 'Roxo escuro', 'cor' => '#673ab7'],
                    'indigo' => ['nome' => 'Indigo', 'cor' => '#3f51b5'],
                    'blue' => ['nome' => 'Azul', 'cor' => '#3f51b5'],
                    'cyan' => ['nome' => 'Ciano', 'cor' => '#00bcd4'],
                    'teal' => ['nome' => 'Verde azulado', 'cor' => '#009688'],
                    'green' => ['nome' => 'Verde', 'cor' => '#4caf50'],
                    'lime' => ['nome' => 'Lima', 'cor' => '#cddc39'],
                    'yellow' => ['nome' => 'Amarelo', 'cor' => '#ffeb3b'],
                    'amber' => ['nome' => 'Âmbar', 'cor' => '#ffc107'],
                    'orange' => ['nome' => 'Laranja', 'cor' => '#ff9800'],
                    'brown' => ['nome' => 'Marrom', 'cor' => '#795548'],
                    'grey' => ['nome' => 'Cinza', 'cor' => '#9e9e9e'],
                    'blue-grey' => ['nome' => 'Cinza azulado', 'cor' => '#607d8b']
                  ];

                  // Gera os inputs radio
                  foreach ($cores as $valor => $dados) {
                    $checked = ($corPerfil === $valor) ? 'checked' : '';
                    echo '<p style="display: block;">
                    <label>
                    <input type="radio" name="color" value="' . $valor . '" class="color-radio" ' . $checked . '>
                    <span style="color: ' . $dados['cor'] . ';">' . $dados['nome'] . '</span>
                    </label>
                    </p>';
                  }
                  ?>
                </div>
                </div>

                <div class="row">

              <div class="input-field col s12">
                <a href="perfil.php" class="modal-close waves-effect waves-green btn-flat">VOLTAR</a>
                <button type="submit" class="waves-effect waves-green btn">SALVAR</button>
              </div>
                </div>


            </form>
        </div>

      


    </div>


    <div class="col s12 m3">

    </div>

  </div>
  </div>

  <div id="modal1" class="modal">
    <div class="modal-content">
      <h4>Editar biografia</h4>
      <form class="col s12">
        <div class="row">
          <div class="row">
            <div class="input-field col s12">
              <textarea id="bio" class="materialize-textarea" data-length="120"></textarea>
              <label for="bio">Bio</label>
            </div>
          </div>
        </div>
    </div>
    <div class="modal-footer">
      <a id="atualizar_bio" class="modal-close waves-effect waves-green btn-flat">ATUALIZAR</a>
    </div>
    </form>
  </div>
  <!--JavaScript at end of body for optimized loading-->
  <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
  <script type="text/javascript" src="../js/materialize.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script type="text/javascript">
    $(document).ready(function() {
      $('.sidenav').sidenav();
      $('.tabs').tabs();
      $('.dropdown-trigger').dropdown();
      $('.materialboxed').materialbox();
      $('.modal').modal();
      $('textarea#bio').characterCounter();
    });
  </script>


<?php if (isset($_GET['salvo'])) { ?>
<script type="text/javascript">
Swal.fire({
  position: "top-end",
  icon: "success",
  title: "Dados salvos com sucesso!",
  showConfirmButton: false,
  timer: 1500
});
</script>
<?php } ?>


</body>

</html>
<?php
mysql_free_result($Matricula);

mysql_free_result($AlunoLogado);
?>