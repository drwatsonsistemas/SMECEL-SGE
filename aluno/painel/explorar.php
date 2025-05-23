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
vinculo_aluno_vacina_atualizada, turma_id, turma_nome, turma_turno, turma_id_sec, turma_id_escola, turma_tipo_atendimento, turma_etapa, turma_ano_letivo, 
vinculo_aluno_vacina_data_retorno, escola_id, escola_nome, sec_id, sec_cidade, sec_uf 
FROM smc_vinculo_aluno 
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma 
INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola
INNER JOIN smc_sec ON sec_id = vinculo_aluno_id_sec
WHERE vinculo_aluno_id_aluno = '$row_Perfil[aluno_id]' AND turma_tipo_atendimento = '1' ORDER BY vinculo_aluno_id DESC LIMIT 0,1";
$Matricula = mysql_query($query_Matricula, $SmecelNovo) or die(mysql_error());
$row_Matricula = mysql_fetch_assoc($Matricula);
$totalRows_Matricula = mysql_num_rows($Matricula);



mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Cidades = "
SELECT * 
FROM smc_sec
WHERE sec_bloqueada = 'N' AND sec_logo IS NOT NULL
";
$Cidades = mysql_query($query_Cidades, $SmecelNovo) or die(mysql_error());
$row_Cidades = mysql_fetch_assoc($Cidades);
$totalRows_Cidades = mysql_num_rows($Cidades);
//AND turma_id_sec = '$row_Matricula[vinculo_aluno_id_sec]'



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
                <i class="material-icons left small purple-text">explorer</i> EXPLORAR
              </h5>


            </blockquote>
          </div>

          <!-- Para celular: Exibe a imagem ao lado das informações do aluno, sem blockquote -->
          <div class="hide-on-med-and-up">
            <blockquote>
              <h5 class="truncate">
                <i class="material-icons left small purple-text">explorer</i> EXPLORAR
              </h5>


            </blockquote>
          </div>

          <a href="perfil.php?uid=<?php echo $row_Perfil['aluno_hash']; ?>" class="btn-flat"><i class="material-icons left">arrow_back</i> Voltar</a>




  <ul class="collapsible">


          <?php do { ?>

            <?php
              mysql_select_db($database_SmecelNovo, $SmecelNovo);
              $query_OutrasTurmas = "
              SELECT *, 
              CASE turma_turno
              WHEN 0 THEN 'INTEGRAL'
              WHEN 1 THEN 'MATUTINO'
              WHEN 2 THEN 'VESPERTINO'
              WHEN 3 THEN 'NOTURNO'
              END AS turma_turno_nome 
              FROM smc_turma
              INNER JOIN smc_escola ON escola_id = turma_id_escola 
              INNER JOIN smc_sec ON sec_id = escola_id_sec
              WHERE turma_id_sec = '$row_Cidades[sec_id]' AND turma_ano_letivo = '$row_Matricula[vinculo_aluno_ano_letivo]' AND turma_etapa = '$row_Matricula[turma_etapa]' AND turma_tipo_atendimento = '1'
              ORDER BY turma_id_escola, turma_turno, turma_nome ASC";
              $OutrasTurmas = mysql_query($query_OutrasTurmas, $SmecelNovo) or die(mysql_error());
              $row_OutrasTurmas = mysql_fetch_assoc($OutrasTurmas);
              $totalRows_OutrasTurmas = mysql_num_rows($OutrasTurmas);
              //AND turma_id_sec = '$row_Matricula[vinculo_aluno_id_sec]'
            ?>


            <li>
      <div class="collapsible-header">
      
      <div style="margin-right: 10px;">
      <?php if ($row_Cidades['sec_logo'] <> "") { ?>
				  <img class="circle" src="http://www.smecel.com.br/img/logo/secretaria/<?php echo $row_Cidades['sec_logo']; ?>" alt="Logo da <?php echo $row_Cidades['sec_nome']; ?>" title="Logo da <?php echo $row_Cidades['sec_nome']; ?>"  width="25" />
				<?php } else { ?>
				  <img class="circle" src="http://www.smecel.com.br/img/brasao_republica.png" width="25">
				<?php } ?> 
        </div>

      <?php echo $row_Cidades['sec_cidade']; ?> (<?php echo $totalRows_OutrasTurmas; ?> turmas)</div>
      <div class="collapsible-body"><span>

      <?php if ($totalRows_OutrasTurmas > 0) { ?>
      <ul class="collection">
        <?php do { ?>




     <li class="collection-item avatar">
     <?php if ($row_OutrasTurmas['escola_logo'] <> "") { ?>
            <img src="http://www.smecel.com.br/img/logo/<?php echo $row_OutrasTurmas['escola_logo']; ?>" alt="" width="80px" class="circle" />
            <?php } else { ?>
              <img src="http://www.smecel.com.br/img/brasao_republica.png" alt="" width="80px" class="circle"/>
              <?php } ?>
      <span class="title"><a href="turma.php?t=<?php echo $row_OutrasTurmas['turma_id']; ?>"><?php echo $row_OutrasTurmas['turma_nome']; ?> - <?php echo $row_OutrasTurmas['turma_turno_nome']; ?></a></span>
      <p><?php echo $row_OutrasTurmas['escola_nome']; ?> <br>
      <small><?php echo $row_OutrasTurmas['sec_cidade']; ?> - <?php echo $row_OutrasTurmas['sec_uf']; ?></small>
      </p>
      <a href="turma.php?t=<?php echo $row_OutrasTurmas['turma_id']; ?>" class="secondary-content"><i class="material-icons">school</i></a>
    </li>
        
        



      <?php } while ($row_OutrasTurmas = mysql_fetch_assoc($OutrasTurmas)); ?>
        </ul>
        <?php } else { ?>
          
          Nenhuma turma localizada nesta etapa de ensino.
          
          <?php } ?>
        
    

    
    

          <?php } while ($row_Cidades = mysql_fetch_assoc($Cidades)); ?> 

          </ul>

        
        
        
        </div>
      </div>


    <div class="col s12 m3">

    </div>

  </div>
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
      $('.collapsible').collapsible();
    });
  </script>

<?php if (isset($_GET['erro'])) { ?>
<script type="text/javascript">

Swal.fire({
  icon: "error",
  title: "Ocorreu um erro!",
  text: "Um relatório foi enviado aos administradores do sistema."
});

</script>
<?php } ?>


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