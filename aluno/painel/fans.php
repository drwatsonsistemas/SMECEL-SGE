<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include('../../sistema/funcoes/inverteData.php'); ?>
<?php include('../../sistema/funcoes/url_base.php'); ?>
<?php include('../../sistema/funcoes/idade.php'); ?>
<?php include('../../sistema/funcoes/anti_injection.php'); ?>
<?php include('funcoes/session.php'); ?>

<?php
if (isset($_GET['uid'])) {
  $hash = anti_injection($_GET['uid']);
}else{
  $hash = anti_injection($row_AlunoLogado['aluno_hash']);
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Perfil = sprintf("
  SELECT * FROM smc_aluno WHERE aluno_perfil_ativo = 'S' AND aluno_hash = %s", GetSQLValueString($hash, "text"));
$Perfil = mysql_query($query_Perfil, $SmecelNovo) or die(mysql_error());
$row_Perfil = mysql_fetch_assoc($Perfil);
$totalRows_Perfil = mysql_num_rows($Perfil);
if($totalRows_Perfil=="") {
  header("Location: index.php?loginErr");
}

$query_FansAlunoLogado = sprintf("
  SELECT * FROM smc_aluno_fans 
  INNER JOIN smc_aluno ON aluno_id = aluno_fan_aluno_de_id
  WHERE
  aluno_perfil_ativo = 'S' AND aluno_fan_aluno_para_id = %s
  ORDER BY aluno_fan_id DESC
  ", GetSQLValueString($row_Perfil['aluno_id'], "int"));
$FanAlunoLogado = mysql_query($query_FansAlunoLogado, $SmecelNovo) or die(mysql_error());
$row_FanAlunoLogado = mysql_fetch_assoc($FanAlunoLogado);
$totalRows_FanAlunoLogado = mysql_num_rows($FanAlunoLogado);


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

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-117872281-1');
  </script>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Fãs de <?php echo $row_Perfil['aluno_nome']; ?> - EduConnect</title>
  <!--Import Google Icon Font-->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <!--Import materialize.css-->
  <link type="text/css" rel="stylesheet" href="../css/materialize.min.css"  media="screen,projection"/>
  <link type="text/css" rel="stylesheet" href="../css/app.css"  media="screen,projection"/>
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
  <link type="text/css" rel="stylesheet" href="css/geral.css" media="screen,projection" />

</head>
<body class="indigo lighten-5">

  <?php include "menu_top_social.php"?>

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
              <?php echo current( str_word_count($row_Perfil['aluno_nome'],2)); ?>
              <?php $word = explode(" ", trim($row_Perfil['aluno_nome'])); echo $word[count($word)-1]; ?>
            </small>
          </a>

        </p>

        

        <?php include "menu_esq_social.php"; ?>

      </div>
      <div>
        <div class="col s12 m7">
        

          <!-- Para desktop: Exibe as informações do aluno com blockquote -->
          <div class="hide-on-small-only">
          
            <blockquote>
              <h5 class="truncate">
                <i class="material-icons left small orange-text">star_border</i> Fãs de <strong><?php echo current( str_word_count($row_Perfil['aluno_nome'],2)); ?></strong>
              <?php $word = explode(" ", trim($row_Perfil['aluno_nome'])); ?>
              </h5>


            </blockquote>
          </div>

          <!-- Para celular: Exibe a imagem ao lado das informações do aluno, sem blockquote -->
          <div class="hide-on-med-and-up">
            <blockquote>
              <h5 class="truncate">
                <i class="material-icons left small orange-text">star_border</i> Fãs de <?php echo current( str_word_count($row_Perfil['aluno_nome'],2)); ?>
              <?php $word = explode(" ", trim($row_Perfil['aluno_nome'])); ?>
              </h5>


            </blockquote>
          </div>

          
                  <a href="perfil.php?uid=<?php echo $row_Perfil['aluno_hash']; ?>" class="btn-flat"><i class="material-icons left">arrow_back</i> Voltar</a>


          <div class="">
            <?php if($totalRows_FanAlunoLogado != 0){ ?>
              <div class="">
                <ul class="collection">
                  <?php 
                  do {
                    ?>
                    <li class="collection-item avatar">
                      <a href="perfil.php?uid=<?php echo $row_FanAlunoLogado['aluno_hash']; ?>">
                        <?php if ($row_FanAlunoLogado['aluno_foto'] == "") { ?>
                          <img src="<?php echo URL_BASE . 'aluno/fotos/' ?>semfoto.jpg" alt="Sem Foto" class="aluno_circulo circle">
                        <?php } else { ?>
                          <img src="<?php echo URL_BASE . 'aluno/fotos/' ?><?php echo $row_FanAlunoLogado['aluno_foto']; ?>" alt="Foto do Aluno" class="aluno_circulo circle">
                        <?php } ?>
                        <span class="title">
                          <small><?php echo current(str_word_count($row_FanAlunoLogado['aluno_nome'], 2)); ?>
                        <?php $word = explode(" ", trim($row_FanAlunoLogado['aluno_nome'])); echo $word[count($word)-1]; ?></small>
                        </span>
                        <!-- Caso queira adicionar alguma ação, como um botão "Seguir de volta" -->
                        <!-- <a href="#!" class="secondary-content"><i class="material-icons">grade</i></a> -->
                        <p class="grey-text truncate"><small><?php echo $row_FanAlunoLogado['aluno_bio']; ?></small></p>
                      </a>
                    </li>

                    <?php 
                  } while ($row_FanAlunoLogado = mysql_fetch_assoc($FanAlunoLogado));
                  ?>
                </div>
              </div>
            <?php }else{ ?>
              <div class="">
                <div class="card-panel grey center-align lighten-5 z-depth-1">
                  <span class="black-text ">
                    Ainda não tem fãs :(
                  </span>
                </div>
              </div>
            <?php } ?>
          </div>
        </div>


        <div class="col s12 m3">


          

        </div>





      </div>
    </div>

    <!--JavaScript at end of body for optimized loading--> 
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script> 
    <script type="text/javascript" src="../js/materialize.min.js"></script> 
    <script type="text/javascript">
      $(document).ready(function(){
       $('.sidenav').sidenav();
       $('.tabs').tabs();
       $('.dropdown-trigger').dropdown();
       $('.materialboxed').materialbox();
       $('.modal').modal();
       $('textarea#bio').characterCounter();
     });
   </script>


 </body>
 </html>
 <?php
 mysql_free_result($Matricula);

 mysql_free_result($AlunoLogado);
 ?>
