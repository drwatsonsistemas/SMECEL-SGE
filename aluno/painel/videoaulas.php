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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matricula = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, 
vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, 
vinculo_aluno_vacina_atualizada, turma_id, turma_nome, turma_turno, turma_tipo_atendimento, turma_etapa, vinculo_aluno_vacina_data_retorno, escola_id, escola_nome, sec_id, sec_cidade, sec_uf 
FROM smc_vinculo_aluno 
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma 
INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola
INNER JOIN smc_sec ON sec_id = vinculo_aluno_id_sec
WHERE vinculo_aluno_id_aluno = '$row_Perfil[aluno_id]' AND turma_tipo_atendimento = '1' ORDER BY vinculo_aluno_id DESC LIMIT 0,1";
$Matricula = mysql_query($query_Matricula, $SmecelNovo) or die(mysql_error());
$row_Matricula = mysql_fetch_assoc($Matricula);
$totalRows_Matricula = mysql_num_rows($Matricula);

$query_Video = "
SELECT * FROM smc_videoaula 
INNER JOIN smc_turma ON turma_id = videoaula_id_turma
INNER JOIN smc_func ON func_id = videoaula_id_professor
INNER JOIN smc_disciplina ON disciplina_id = videoaula_id_componente
WHERE turma_etapa = '$row_Matricula[turma_etapa]'
ORDER BY rand() LIMIT 0,1
";
$Video = mysql_query($query_Video, $SmecelNovo) or die(mysql_error());
$row_Video = mysql_fetch_assoc($Video);
$totalRows_Video = mysql_num_rows($Video);


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
  <link type="text/css" rel="stylesheet" href="css/geral.css" media="screen,projection" />

  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">


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

      
          <!-- Para desktop: Exibe as informações do aluno com blockquote -->
          <div class="hide-on-small-only">
          
            <blockquote>
              <h5 class="truncate">
                <i class="material-icons left small red-text">ondemand_video</i> Videoaulas
              </h5>


            </blockquote>
          </div>

          <!-- Para celular: Exibe a imagem ao lado das informações do aluno, sem blockquote -->
          <div class="hide-on-med-and-up">
            <blockquote>
              <h5 class="truncate">
                <i class="material-icons left small red-text">ondemand_video</i> Videoaulas
              </h5>


            </blockquote>
          </div>
        <a href="perfil.php?uid=<?php echo $row_Perfil['aluno_hash']; ?>" class="btn-flat btn-small" style="border-radius: 30px;"><i class="material-icons">arrow_back</i></a>

        	  
        <hr>

       <div class="row"> 
          <div class="col s3">

         
        <?php if ($row_Video['func_foto']=="") { ?>
        <img src="<?php echo URL_BASE.'professor/fotos/' ?>semfoto.jpg" width="100%" class="hoverable prof1">
        <?php } else { ?>
        <img src="<?php echo URL_BASE.'professor/fotos/' ?><?php echo $row_Video['func_foto']; ?>" width="100%" class="hoverable prof1">
        <?php } ?>
		 
          </div>
          <div class="col s7 truncate">
        <strong>Professor(a):</strong> <br><?php echo $row_Video['func_nome']; ?><br>
        <strong>Componente:</strong> <br><?php echo $row_Video['disciplina_nome']; ?>
        </div>
        
       </div>        
       
          <video width="100%" controls>
            <source src="https://www.smecel.com.br/videoaula/<?php echo $row_Video['videoaula_id_turma']; ?>/<?php echo $row_Video['videoaula_id_aula']; ?>/<?php echo $row_Video['videoaula_id_professor']; ?>/<?php echo $row_Video['videoaula_id_componente']; ?>/<?php echo $row_Video['videoaula_nome']; ?>" type="video/mp4">
            <source src="https://www.smecel.com.br/videoaula/<?php echo $row_Video['videoaula_id_turma']; ?>/<?php echo $row_Video['videoaula_id_aula']; ?>/<?php echo $row_Video['videoaula_id_professor']; ?>/<?php echo $row_Video['videoaula_id_componente']; ?>/<?php echo $row_Video['videoaula_nome']; ?>" type="video/ogg">
          Your browser does not support the video tag.
          </video>

        <br><br>  

      
        
        
      </div>

 



      </div>


    </div>


    <!--JavaScript at end of body for optimized loading-->
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js" async></script>
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


    </body>

</html>
<?php
mysql_free_result($Matricula);

mysql_free_result($AlunoLogado);
?>