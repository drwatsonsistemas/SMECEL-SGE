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


$query_Publicacoes = "SELECT * FROM smc_aluno_postagem
INNER JOIN smc_aluno ON aluno_id = id_aluno_postagem_id_aluno
";
$Publicacoes = mysql_query($query_Publicacoes, $SmecelNovo) or die(mysql_error());
$row_Publicacoes = mysql_fetch_assoc($Publicacoes);
$totalRows_Publicacoes = mysql_num_rows($Publicacoes);

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
  <title><?php echo $row_AlunoLogado['aluno_nome']; ?>- SMECEL - Secretaria Municipal de Educação, Cultura, Esporte e
    Lazer</title>
  <!--Import Google Icon Font-->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <!--Import materialize.css-->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">

  <style>
    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 12px;
    }

    th,
    td {
      border: 0px solid #ccc;
    }

    th,
    td {
      padding: 5px;
      height: 15px;
      line-height: 15px;
    }

    .aluno {
      background-color: #ddd;
      object-fit: cover;
      width: 100%;
      aspect-ratio: 1 / 1;
      /* Define a proporção da imagem como 1:1 */
      border-radius: 10%;
      /* Mantém a borda não arredondada, pode ser removido se necessário */
    }



    @media only screen and (max-width: 600px) {
      .profile-pic-mobile {
        width: 100%;
        /* Ajuste conforme necessário */
        padding-top: 18px;
      }

      blockquote.hide-on-small-only h4 {
        margin: 0;
        /* Remove margem para alinhar corretamente */
      }
    }


    .tweet-card {
      border: 1px solid #e0e0e0;
      border-radius: 10px;
      padding: 15px;
      margin: 20px 0;
    }

    .tweet-header {
      display: flex;
      align-items: center;
      margin-bottom: 10px;
    }

    .tweet-header img {
      border-radius: 50%;
      width: 50px;
      height: 50px;
      margin-right: 10px;
    }

    .tweet-header .user-info {
      flex-grow: 1;
    }

    .tweet-header .user-info .user-name {
      font-weight: bold;
      margin: 0;
    }

    .tweet-header .user-info .user-handle {
      color: #757575;
      margin: 0;
    }

    .tweet-header .tweet-time {
      color: #9e9e9e;
    }

    .tweet-content {
      margin-bottom: 15px;
    }

    .tweet-actions {
      display: flex;
      justify-content: space-between;
    }

    .tweet-actions a {
      color: #616161;
      display: flex;
      align-items: center;
      text-decoration: none;
    }

    .tweet-actions a i {
      margin-right: 5px;
    }

    /* Estilo do botão moderno */
    .modern-button {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 100%;
      max-width: 400px;
      padding: 15px;
      font-size: 16px;
      font-weight: 500;
      color: #fff;
      background: linear-gradient(135deg, #6C63FF, #4C4CFF);
      /* Gradiente moderno */
      border: none;
      border-radius: 25px;
      /* Bordas arredondadas */
      text-align: center;
      text-decoration: none;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      /* Sombra leve */
      cursor: pointer;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      /* Para efeitos visuais */
    }

    .modern-button i {
      margin-right: 10px;
      font-size: 20px;
      /* Tamanho do ícone */
    }

    .modern-button:after {
      content: '';
      position: absolute;
      width: 150%;
      height: 150%;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%) scale(0);
      background: rgba(255, 255, 255, 0.3);
      border-radius: 50%;
      transition: transform 0.5s ease;
    }

    .modern-button:active:after {
      transform: translate(-50%, -50%) scale(1);
      /* Efeito de "onda" ao clicar */
    }

    /* Responsividade */
    @media (max-width: 600px) {
      .modern-button {
        max-width: 100%;
        padding: 12px;
        font-size: 13px;
        /* Ajusta o tamanho da fonte em telas menores */
      }
    }



    /* Container dos ícones */
    .icons-container {
      display: flex;
      justify-content: space-around;
      /* Espaçamento entre os ícones */
      margin-top: 10px;
      /* Espaço acima dos ícones */
    }

    /* Ajustar os ícones */
    .icons-container i {
      font-size: 24px;
      cursor: pointer;
      transition: transform 0.3s ease;
    }

    .icons-container i:hover {
      transform: scale(1.2);
      /* Aumenta o tamanho do ícone ao passar o mouse */
    }

    .card-title {
      margin-top: 0;
      font-size: 1.2rem;
    }

    .card-action a {
      color: #039be5;
      margin-right: 10px;
    }

    .grey-text {
      font-size: 0.9rem;
    }


    .date-time {
      display: flex;
      justify-content: flex-end;
      align-items: center;
    }

    .date-time span {
      margin-left: 10px;
    }

    @media screen and (max-width: 600px) {
      .date-time {
        justify-content: flex-start;
        margin-top: 5px;
      }
    }

    .aluno_circulo {
      background-color: #ddd;
      object-fit: cover;
      width: 100%;
      aspect-ratio: 1 / 1;
      /* Define a proporção da imagem como 1:1 */
      border-radius: 50%;
      /* Mantém a borda não arredondada, pode ser removido se necessário */
    }
  </style>


</head>

<body class="<?= $row_Perfil['aluno_cor_fundo'] ?> lighten-4">


  <?php include "menu_top_social.php" ?>

  <div id="containerId" class="container">



    <div class="row white" style="margin: 10px 0;">

      <div class="col s12 m2 hide-on-small-only">
        <p>

          <a href="perfil.php?uid=<?php echo $row_Perfil['aluno_hash']; ?>">

            <?php if ($row_Perfil['aluno_foto'] == "") { ?>
              <img src="<?php echo URL_BASE . 'aluno/fotos/' ?>semfoto.jpg" width="100%" class="hoverable">
            <?php } else { ?>
              <img src="<?php echo URL_BASE . 'aluno/fotos/' ?><?php echo $row_Perfil['aluno_foto']; ?>" width="100%"
                class="hoverable">
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
        <a href="perfil.php?uid=<?php echo $row_Perfil['aluno_hash']; ?>" class="btn-flat btn-small"
          style="border-radius: 30px;"><i class="material-icons">arrow_back</i></a>

        <div class="row1">
          <div class="row">
            <div class="input-field col s12">
              <a class="modern-button modal-trigger" href="#modal1">
                <i class="material-icons left">cloud</i> No que você está pensando,
                <?= ucwords(strtolower($word[0])) ?>?
              </a>
            </div>
          </div>
        </div>

        <hr>

        <div id="postagensContainer"></div>


        <div id="modal1" class="modal">
          <form id="formPublicacao" class="col s12" enctype="multipart/form-data">
            <div class="modal-content">
              <h5>Nova publicação</h5>
              <div class="row1">

                <div class="row">
                  <div class="input-field col s12">
                    <textarea id="icon_prefix2" name="publicacao_aluno" class="materialize-textarea"></textarea>
                    <label for="icon_prefix2"><i class="material-icons">mode_edit</i> Publique algo legal</label>
                    <!-- Input file escondido -->
                    <input type="file" id="imageInput" name="imagem" accept="image/*" style="display:none;">
                    <!-- Campo de pré-visualização da imagem -->
                    <div id="imagePreview" class="center" style="margin-top: 10px;"></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <!-- Ícone de seleção de imagem -->
              <i class="material-icons blue-text left select-image" style="cursor:pointer;">photo_camera</i>
              <!-- Ícone de vídeo -->
              <i class="material-icons red-text left">ondemand_video</i>

              <!-- Botão de Publicar -->
              <button type="submit" id="btnPublicar" class="waves-effect waves-light btn-small right">
                <i class="material-icons right">cloud</i>PUBLICAR
              </button>
            </div>
          </form>
        </div>






      </div>








    </div>





    <!--JavaScript at end of body for optimized loading-->
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
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



    <script>
      function adjustContainerClass() {
        var container = document.getElementById('containerId');
        if (window.innerWidth <= 600) {
          // Remover a classe 'container' em telas pequenas (celulares)
          container.classList.remove('container');
        } else {
          // Adicionar a classe 'container' de volta em telas maiores
          container.classList.add('container');
        }
      }

      // Executar a função quando a página carregar
      window.onload = adjustContainerClass;

      // Executar a função sempre que a janela for redimensionada
      window.onresize = adjustContainerClass;
    </script>

    <script>
      // Função para carregar as postagens
      function carregarPostagens() {
        $.ajax({
          url: 'funcoes/carregar_postagens.php', // URL para o arquivo PHP que retorna as postagens
          type: 'GET',
          dataType: 'json',
          success: function (response) {
            var postagensContainer = $('#postagensContainer'); // Container onde as postagens serão inseridas
            postagensContainer.html(''); // Limpa o container

            // Percorre o array de postagens
            response.forEach(function (postagem) {
              console.log(postagem);
              var postagemHtml = `
            <div class="row">
              <div class="col s12">
                <div class="card">
                  <div class="card-content">
                    <div class="row">
                      <div class="col s4 m2">
                        <img src="${postagem.aluno_foto ? 'https://www.smecel.com.br/aluno/fotos/' + postagem.aluno_foto : 'https://www.smecel.com.br/aluno/fotos/semfoto.jpg'}" alt="avatar" class="circle aluno_circulo">
                      </div>
                      <div class="col s8 m10">
                        <span class="card-title"><strong>${postagem.aluno_nome}</strong></span>
                      </div>
                    </div>
                    <p>${postagem.texto ? postagem.texto : ''}</p>
                    ${postagem.imagem ? `<div class="card-image"><img src="../publicacoes/${postagem.imagem}" alt="Imagem do Depoimento" style="border-radius: 8px;padding-top: 5px;"></div>` : ''}
                  </div>
                </div>
              </div>
            </div>
          `;
              postagensContainer.append(postagemHtml); // Insere a postagem no container
            });
          },
          error: function (jqXHR, textStatus, errorThrown) {
            console.error('Erro ao carregar postagens:', textStatus);
          }
        });
      }

      $(document).ready(function () {
        // Inicialmente desabilitar o botão
        $('#btnPublicar').prop('disabled', true);

        // Função que verifica se há conteúdo para habilitar o botão
        function verificarCampos() {
          if ($('#icon_prefix2').val().trim() !== '' && $('#imageInput').val() !== '') {
            $('#btnPublicar').prop('disabled', true);
          } else {
            $('#btnPublicar').prop('disabled', false);
          }
        }

        // Verificar quando o campo de texto mudar
        $('#icon_prefix2').on('input', verificarCampos);

        // Verificar quando uma imagem for selecionada
        $('#imageInput').on('change', verificarCampos);

        // Quando clicar no ícone de foto, abrir o input file
        $('.select-image').on('click', function () {
          $('#imageInput').click();
        });

        // Quando uma imagem é selecionada, exibir o preview
        $('#imageInput').on('change', function () {
          var file = this.files[0];
          if (file) {
            var reader = new FileReader();
            reader.onload = function (e) {
              // Exibe a imagem no div de preview
              $('#imagePreview').html('<img src="' + e.target.result + '" alt="Pré-visualização" style="max-width: 100%; max-height: 100%; border-radius: 8px;">');
            }
            reader.readAsDataURL(file);
          }
        });

        // Submissão do formulário via AJAX
        $('#formPublicacao').on('submit', function (e) {
          e.preventDefault();

          if (!$('#imageInput').val() && !$('#icon_prefix2').val()) {
            M.toast({ html: 'Por favor, insira uma imagem ou um conteúdo.' });
            return;
          }

          // Criar um objeto FormData para enviar os dados e a imagem
          var formData = new FormData(this);

          $.ajax({
            url: 'funcoes/processa_publicacao.php', // URL do script PHP que processa a publicação
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json', // Especifica que esperamos uma resposta JSON
            success: function (response) {
              if (response.success) {
                M.toast({ html: response.message });
                $('#icon_prefix2').val('');
                $('#imagePreview').html('');
                var instance = M.Modal.getInstance(document.querySelector('.modal'));
                instance.close();

                carregarPostagens(); // Chamar a função carregarPostagens aqui
              } else {
                M.toast({ html: response.message });
              }
            },
            error: function (jqXHR, textStatus, errorThrown) {
              console.error('Resposta do servidor:', jqXHR.responseText);
              alert('Erro ao fazer a publicação: ' + textStatus);
            }
          });
        });

        // Chama a função para carregar as postagens quando a página carrega
        carregarPostagens();
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
</body>

</html>
<?php
mysql_free_result($Matricula);

mysql_free_result($AlunoLogado);
?>