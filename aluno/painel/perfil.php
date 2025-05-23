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
  $hash = $row_AlunoLogado['aluno_hash'];
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Perfil = sprintf("
  SELECT * FROM smc_aluno WHERE aluno_perfil_ativo = 'S' AND aluno_hash = %s", GetSQLValueString($hash, "text"));
$Perfil = mysql_query($query_Perfil, $SmecelNovo) or die(mysql_error());
$row_Perfil = mysql_fetch_assoc($Perfil);
$totalRows_Perfil = mysql_num_rows($Perfil);
if ($totalRows_Perfil == "") {
  header("Location: perfil.php?erro");
}

if (($row_Perfil['aluno_aceite_termos'] == "N") && ($row_Perfil['aluno_hash'] <> $row_AlunoLogado['aluno_hash'])) {

  $primeiroNome = current(str_word_count($row_Perfil['aluno_nome'], 2));

  header("Location: perfil.php?naceite=" . $primeiroNome);
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

$query_FavoritoConta = sprintf("
  SELECT * FROM smc_aluno_fans WHERE aluno_fan_aluno_de_id = %s", GetSQLValueString($row_Perfil['aluno_id'], "int"));
$FavoritoConta = mysql_query($query_FavoritoConta, $SmecelNovo) or die(mysql_error());
$row_FavoritoConta = mysql_fetch_assoc($FavoritoConta);
$totalRows_FavoritoConta = mysql_num_rows($FavoritoConta);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matricula = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, 
vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, 
vinculo_aluno_vacina_atualizada, turma_id, turma_nome, turma_turno, turma_tipo_atendimento, vinculo_aluno_vacina_data_retorno, escola_id, escola_nome, sec_id, sec_cidade, sec_uf 
FROM smc_vinculo_aluno 
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma 
INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola
INNER JOIN smc_sec ON sec_id = vinculo_aluno_id_sec
WHERE vinculo_aluno_id_aluno = '$row_Perfil[aluno_id]' AND turma_tipo_atendimento = '1' 
ORDER BY vinculo_aluno_id DESC LIMIT 0,1";
$Matricula = mysql_query($query_Matricula, $SmecelNovo) or die(mysql_error());
$row_Matricula = mysql_fetch_assoc($Matricula);
$totalRows_Matricula = mysql_num_rows($Matricula);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Colegas = "
SELECT 
*
FROM smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
WHERE vinculo_aluno_situacao = '1' AND vinculo_aluno_id_turma = '$row_Matricula[vinculo_aluno_id_turma]' AND vinculo_aluno_dependencia = 'N' AND aluno_perfil_ativo = 'S'
ORDER BY aluno_aceite_termos DESC, RAND()
";
$Colegas = mysql_query($query_Colegas, $SmecelNovo) or die(mysql_error());
$row_Colegas = mysql_fetch_assoc($Colegas);
$totalRows_Colegas = mysql_num_rows($Colegas);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ColegasAniversario = "
SELECT 
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola,
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_dependencia,
vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, aluno_id, aluno_nome, aluno_foto, aluno_nascimento, aluno_hash, aluno_aceite_termos,
DATE_FORMAT(aluno_nascimento, '%d/%m') AS aniversario,
CASE
        WHEN DATE_FORMAT(aluno_nascimento, '%m-%d') = DATE_FORMAT(CURDATE(), '%m-%d') THEN 'Hoje<br><i class=\"material-icons tiny purple-text\">cake</i> '
        WHEN DATE_FORMAT(aluno_nascimento, '%m-%d') = DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 1 DAY), '%m-%d') THEN 'Amanhã'
        WHEN DATE_FORMAT(aluno_nascimento, '%m-%d') = DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 2 DAY), '%m-%d') THEN 'Em 2 dias'
        ELSE DATE_FORMAT(aluno_nascimento, '%d/%m')
        END AS quando 
FROM smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
WHERE vinculo_aluno_situacao = '1' AND vinculo_aluno_id_turma = '$row_Matricula[vinculo_aluno_id_turma]' AND vinculo_aluno_dependencia = 'N' AND DATE_FORMAT(aluno_nascimento, '%m-%d') >= DATE_FORMAT(CURDATE(), '%m-%d')  AND aluno_perfil_ativo = 'S'
ORDER BY DATE_FORMAT(aluno_nascimento, '%m-%d')
LIMIT 0,6
";
$ColegasAniversario = mysql_query($query_ColegasAniversario, $SmecelNovo) or die(mysql_error());
$row_ColegasAniversario = mysql_fetch_assoc($ColegasAniversario);
$totalRows_ColegasAniversario = mysql_num_rows($ColegasAniversario);


$query_DepoimentosConta = "SELECT * FROM smc_aluno_depoimentos WHERE aluno_depoimento_status = 'P' AND aluno_depoimento_id_para = $row_Perfil[aluno_id]";
$DepoimentosConta = mysql_query($query_DepoimentosConta, $SmecelNovo) or die(mysql_error());
$row_DepoimentosConta = mysql_fetch_assoc($DepoimentosConta);
$totalRows_DepoimentosConta = mysql_num_rows($DepoimentosConta);

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
  <title>Perfil de <?php echo $row_Perfil['aluno_nome']; ?> - EduConnect</title>
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

<body class="<?= $row_Perfil['aluno_cor_fundo'] ?> lighten-4">

  <?php include "menu_top_social.php" ?>

  <div class="container">

    <?php if (isset($_GET['naceite'])) { ?>

      <div class="col s12">
        <div class="card-panel orange lighten-2 center-align">Para interagir com <?php echo $_GET['naceite']; ?>, é necessário que ele(a) aceite os termos para participar da EduConnect.</div>
      </div>

    <?php } ?>

    <div class="row white" style="margin: 10px 0;">

      <div class="col s12 m2 hide-on-small-only">
        <p>



        <?php 
        if (!empty($row_Perfil['aluno_foto2'])) { ?>
            <img src="<?php echo URL_BASE.'aluno/fotos2/' . $row_Perfil['aluno_foto2']; ?>" width="100%" class="hoverable">
        <?php } elseif (!empty($row_Perfil['aluno_foto'])) { ?>
            <img src="<?php echo URL_BASE.'aluno/fotos/' . $row_Perfil['aluno_foto']; ?>" width="100%" class="hoverable">
        <?php } else { ?>
            <img src="<?php echo URL_BASE.'aluno/fotos2/semfoto.jpg'; ?>" width="100%" class="hoverable">
        <?php } ?>
            <br>
            <small style="font-size:14px;" class="truncate">
              <?php echo current(str_word_count($row_Perfil['aluno_nome'], 2)); ?>
              <?php $word = explode(" ", trim($row_Perfil['aluno_nome']));
              echo $word[count($word) - 1]; ?>,
              <?php echo idade($row_Perfil['aluno_nascimento']); ?>
            </small>



        </p>



        <?php include "menu_esq_social.php"; ?>

      </div>

      <div class="col s12 m7">



        <!-- Para desktop: Exibe as informações do aluno com blockquote -->
        <div class="hide-on-small-only">
          <blockquote>
            <h4 class="truncate">
              <?php if ($row_Perfil['aluno_def_autista'] == "1") { ?>
                <img src="../../img/autismo.png" width="30" class="responsive-img">
              <?php } ?>

              <?php echo current(str_word_count($row_Perfil['aluno_nome'], 2)); ?>
              <?php $word = explode(" ", trim($row_Perfil['aluno_nome']));
              echo $word[count($word) - 1]; ?>
            </h4>

            <p>
              <i class="material-icons tiny red-text">location_on</i> <?php echo $row_Matricula['sec_cidade']; ?> - <?php echo $row_Matricula['sec_uf']; ?><br>
              <i class="material-icons tiny blue-text">location_city</i> <?php echo $row_Matricula['escola_nome']; ?><br>
              <i class="material-icons tiny orange-text">class</i> <?php echo $row_Matricula['turma_nome']; ?><br>
              <i class="material-icons tiny purple-text">cake</i> <?php echo idade($row_Perfil['aluno_nascimento']); ?> anos
            </p>
          </blockquote>
        </div>

        <!-- Para celular: Exibe a imagem ao lado das informações do aluno, sem blockquote -->
        <div class="hide-on-med-and-up">

          <div class="row">
            <div class="col s4">


        <?php if (!empty($row_Perfil['aluno_foto2'])) { ?>
            <img src="<?php echo URL_BASE.'aluno/fotos2/' . $row_Perfil['aluno_foto2']; ?>" width="100%" class="hoverable profile-pic-mobile">
        <?php } elseif (!empty($row_Perfil['aluno_foto'])) { ?>
            <img src="<?php echo URL_BASE.'aluno/fotos/' . $row_Perfil['aluno_foto']; ?>" width="100%" class="hoverable profile-pic-mobile">
        <?php } else { ?>
            <img src="<?php echo URL_BASE.'aluno/fotos2/semfoto.jpg'; ?>" width="100%" class="hoverable profile-pic-mobile">
        <?php } ?>

            </div>
            <div class="col s8">


              <h5 class="truncate">
                

                <?php echo current(str_word_count($row_Perfil['aluno_nome'], 2)); ?>
                <?php $word = explode(" ", trim($row_Perfil['aluno_nome']));
                echo $word[count($word) - 1]; ?>
              </h5>



              <p class="truncate">
                <i class="material-icons tiny red-text">location_on</i> <?php echo $row_Matricula['sec_cidade']; ?> - <?php echo $row_Matricula['sec_uf']; ?><br>
                <i class="material-icons tiny blue-text">location_city</i> <?php echo $row_Matricula['escola_nome']; ?><br>
                <i class="material-icons tiny orange-text">class</i> <?php echo $row_Matricula['turma_nome']; ?><br>
                <i class="material-icons tiny purple-text">cake</i> <?php echo idade($row_Perfil['aluno_nascimento']); ?> anos<br>
              </p>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col s2">
            <?php if ($hash <> $row_AlunoLogado['aluno_hash']) { ?>
              <a href="perfil.php" class="btn-flat btn-small" style="border-radius: 30px;"><i class="material-icons">arrow_back</i></a>
            <?php } ?>
          </div>
          <div class="col s10 right-align">
            <?php if ($hash == $row_AlunoLogado['aluno_hash']) { ?>
              <a href="configuracoes.php" class="btn-small waves-effect waves-light <?= $row_Perfil['aluno_cor_fundo'] ?>" style="border-radius: 18px;"><i class="material-icons left">settings</i>EDITAR PERFIL</a>
            <?php } else { ?>
              <?php if ($totalRows_Fan == 0) { ?>
                <a id="fan-button" class="btn-small waves-effect waves-light btn <?= $row_Perfil['aluno_cor_fundo'] ?>" style="border-radius: 18px;" data-is-fan="false"><i class="material-icons left">stars</i>VIRAR FÃ</a>
              <?php } else { ?>
                <a id="fan-button" class="btn-small grey lighten-5 btn" style="border-radius: 18px; color:#ff8f00" data-is-fan="true"><i class="material-icons left">verified_user</i>SOU FÃ</a>
              <?php } ?>
            <?php } ?>
          </div>
        </div>





        <?php if ($row_Perfil['aluno_bio'] <> "") { ?>
          <div class="row">
            <div class="col s12" style="word-wrap: break-word;">
              <div class="1yellow 1lighten-2"><?php echo nl2br($row_Perfil['aluno_bio']); ?></div>
            </div>
          </div>
        <?php } ?>

        <div class="row">
          <div class="col s12">
            <?php if ($row_Perfil['aluno_face']  != '') { ?>
              <a href="https://www.facebook.com/<?= $row_Perfil['aluno_face'] ?>" target="_blank">
                <svg style="margin-right:10px ;" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="25" height="25" viewBox="0 0 50 50">
                  <path d="M25,3C12.85,3,3,12.85,3,25c0,11.03,8.125,20.137,18.712,21.728V30.831h-5.443v-5.783h5.443v-3.848 c0-6.371,3.104-9.168,8.399-9.168c2.536,0,3.877,0.188,4.512,0.274v5.048h-3.612c-2.248,0-3.033,2.131-3.033,4.533v3.161h6.588 l-0.894,5.783h-5.694v15.944C38.716,45.318,47,36.137,47,25C47,12.85,37.15,3,25,3z"></path>
                </svg>
              </a>
            <?php } ?>

            <?php if ($row_Perfil['aluno_insta']  != '') { ?>
              <a href="https://www.instagram.com/<?= $row_Perfil['aluno_insta'] ?>" target="_blank">
                <svg style="margin-right:10px ;" xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24">
                  <path d="M 8 3 C 5.243 3 3 5.243 3 8 L 3 16 C 3 18.757 5.243 21 8 21 L 16 21 C 18.757 21 21 18.757 21 16 L 21 8 C 21 5.243 18.757 3 16 3 L 8 3 z M 8 5 L 16 5 C 17.654 5 19 6.346 19 8 L 19 16 C 19 17.654 17.654 19 16 19 L 8 19 C 6.346 19 5 17.654 5 16 L 5 8 C 5 6.346 6.346 5 8 5 z M 17 6 A 1 1 0 0 0 16 7 A 1 1 0 0 0 17 8 A 1 1 0 0 0 18 7 A 1 1 0 0 0 17 6 z M 12 7 C 9.243 7 7 9.243 7 12 C 7 14.757 9.243 17 12 17 C 14.757 17 17 14.757 17 12 C 17 9.243 14.757 7 12 7 z M 12 9 C 13.654 9 15 10.346 15 12 C 15 13.654 13.654 15 12 15 C 10.346 15 9 13.654 9 12 C 9 10.346 10.346 9 12 9 z"></path>
                </svg>
              </a>
            <?php } ?>

            <?php if ($row_Perfil['aluno_x']  != '') { ?>
              <a href="https://x.com/<?= $row_Perfil['aluno_x'] ?>" target="_blank">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 512 509.64">
                  <rect width="512" height="509.64" rx="115.61" ry="115.61" />
                  <path fill="#fff" fill-rule="nonzero" d="M323.74 148.35h36.12l-78.91 90.2 92.83 122.73h-72.69l-56.93-74.43-65.15 74.43h-36.14l84.4-96.47-89.05-116.46h74.53l51.46 68.04 59.53-68.04zm-12.68 191.31h20.02l-129.2-170.82H180.4l130.66 170.82z" />
                </svg>
              </a>
            <?php } ?>
          </div>
        </div>

        <div class="row">
          <a href="perfil.php?uid=<?php echo $hash; ?>" class="waves-effect waves-light btn-small btn-flat brown-text">
            <i class="material-icons left">person</i>PERFIL
          </a>
          <a href="mural.php?uid=<?php echo $hash; ?>" class="waves-effect waves-light btn-small btn-flat blue-text">
            <i class="material-icons left">message</i>MURAL (<?php echo $totalRows_DepoimentosConta; ?>)
          </a>
          <a id="fan-count" href="fans.php?uid=<?php echo $row_Perfil['aluno_hash']; ?>" class="waves-effect waves-light btn-small btn-flat orange-text " >
            <i class="material-icons left">star_border</i><span id="fan-count-number"><?php echo $totalRows_FanConta; ?></span> FÃS
          </a>
          <a id="favorito-count" href="favoritos.php?uid=<?php echo $row_Perfil['aluno_hash']; ?>" class="waves-effect waves-light btn-small btn-flat green-text" >
            <i class="material-icons left">favorite_border</i><span id="fan-count-number-fav"><?php echo $totalRows_FavoritoConta; ?></span> FAVORITOS
          </a>
          <a href="explorar.php" class="waves-effect waves-light btn-small btn-flat purple-text">
            <i class="material-icons left">explorer</i> EXPLORAR 
          </a>


          <!--
          <a class="waves-effect waves-light btn-small btn-flat purple-text disabled" style="font-size: 0.6em;">
            <i class="material-icons left">stars</i>CONQUISTAS
          </a>
            -->

        </div>




        <?php if ($hash == $row_AlunoLogado['aluno_hash']) { ?>
          PRÓXIMOS ANIVERSARIANTES
          
          <div class="row">  
          <?php do { ?>
          <div class="col s2 center-align">
            <a href="perfil.php?uid=<?php echo $row_ColegasAniversario['aluno_hash']; ?>">

            <?php 
if (!empty($row_ColegasAniversario['aluno_foto2'])) { ?>
    <img src="<?php echo URL_BASE . 'aluno/foto2/' . $row_ColegasAniversario['aluno_foto2']; ?>" width="100%" class="hoverable aluno" 
         style="<?php if ($row_ColegasAniversario['aluno_aceite_termos'] == "S") { echo "border: solid 2px #23CE6B"; } ?>"><br>
<?php } elseif (!empty($row_ColegasAniversario['aluno_foto'])) { ?>
    <img src="<?php echo URL_BASE . 'aluno/fotos/' . $row_ColegasAniversario['aluno_foto']; ?>" width="100%" class="hoverable aluno" 
         style="<?php if ($row_ColegasAniversario['aluno_aceite_termos'] == "S") { echo "border: solid 2px #23CE6B"; } ?>"><br>
<?php } else { ?>
    <img src="<?php echo URL_BASE . 'aluno/foto2/semfoto.jpg'; ?>" width="100%" class="hoverable aluno" 
         style="<?php if ($row_ColegasAniversario['aluno_aceite_termos'] == "S") { echo "border: solid 2px #23CE6B"; } ?>"><br>
<?php } ?>

              <div style="font-size:10px;" class="truncate">
                
                <?php echo current(str_word_count($row_ColegasAniversario['aluno_nome'], 2)); ?>
                <?php $word = explode(" ", trim($row_ColegasAniversario['aluno_nome'])); ?><br>
                <?php echo $row_ColegasAniversario['quando']; ?>
              </div><br>
            </a>
          </div>
        <?php } while ($row_ColegasAniversario = mysql_fetch_assoc($ColegasAniversario)); ?>
              </div>
        

      <?php } ?>



      <?php if ($hash == $row_AlunoLogado['aluno_hash']) { ?>
      <small>Os dados abaixo estão visíveis apenas para mim.</small>            
      <table class="striped">
      <thead>
      	<tr>
        	<th width="120"></th>
        	<th></th>
        </tr>
      
      </thead>

        <tbody>
        
        	<tr>
            	<td class="right grey-text text-darken-1">nome completo</td>
            	<td class="black-text"><strong><?php echo $row_AlunoLogado['aluno_nome']; ?></strong></td>
            </tr>
			
        	<tr>
            	<td class="right grey-text text-darken-1">aniversário</td>
            	<td class="black-text"><strong><?php echo inverteData($row_AlunoLogado['aluno_nascimento']); ?></strong></td>
            </tr>

        	<tr>
            	<td class="right grey-text text-darken-1">idade</td>
            	<td class="black-text"><strong><?php echo idade($row_AlunoLogado['aluno_nascimento']); ?></strong></td>
            </tr>

        	<tr>
            	<td class="right grey-text text-darken-1">gênero</td>
            	<td class="black-text"><strong><?php echo $row_AlunoLogado['aluno_sexo_nome']; ?></strong></td>
            </tr>
						
        	<tr>
            	<td class="right grey-text text-darken-1">filiação</td>
            	<td class="black-text"><strong><?php echo $row_AlunoLogado['aluno_filiacao1']; ?></strong></td>
            </tr>
			
            <?php if ($row_AlunoLogado['aluno_filiacao2']<>"") { ?>
        	<tr>
            	<td class="right grey-text text-darken-1">filiação</td>
            	<td class="black-text"><strong><?php echo $row_AlunoLogado['aluno_filiacao2']; ?></strong></td>
            </tr>
            <?php } ?>
            
        	<tr>
            	<td class="right grey-text text-darken-1">naturalidade</td>
            	<td class="black-text"><strong><?php echo $row_AlunoLogado['aluno_municipio_nascimento']; ?> (<?php echo $row_AlunoLogado['aluno_uf_nascimento']; ?>)</strong></td>
            </tr>
			
        	<tr>
            	<td class="right grey-text text-darken-1">CEP</td>
            	<td class="black-text"><strong><?php echo $row_AlunoLogado['aluno_cep']; ?></strong></td>
            </tr>
			
        	<tr>
            	<td class="right grey-text text-darken-1">endereço</td>
            	<td class="black-text"><strong><?php echo $row_AlunoLogado['aluno_endereco']; ?>, <?php echo $row_AlunoLogado['aluno_numero']; ?></strong></td>
            </tr>
			
        	<tr>
            	<td class="right grey-text text-darken-1">bairro</td>
            	<td class="black-text"><strong><?php echo $row_AlunoLogado['aluno_bairro']; ?></strong></td>
            </tr>
			
        	<tr>
            	<td class="right grey-text text-darken-1">cidade</td>
            	<td class="black-text"><strong><?php echo $row_AlunoLogado['aluno_municipio']; ?> (<?php echo $row_AlunoLogado['aluno_uf']; ?>)</strong></td>
            </tr>
			
        	<tr>
            	<td class="right grey-text text-darken-1">telefone</td>
            	<td class="black-text"><strong><?php echo $row_AlunoLogado['aluno_telefone']; ?></strong>&nbsp</td>
            </tr>
			
        	<tr>
            	<td class="right grey-text text-darken-1">celular</td>
            	<td class="black-text"><strong><?php echo $row_AlunoLogado['aluno_celular']; ?></strong>&nbsp</td>
            </tr>

            <tr>
            	<td class="right grey-text text-darken-1">tel emergência 1</td>
            	<td class="black-text"><strong><?php echo $row_AlunoLogado['aluno_emergencia_tel1']; ?></strong>&nbsp</td>
            </tr>

            <tr>
            	<td class="right grey-text text-darken-1">tel emergência 2</td>
            	<td class="black-text"><strong><?php echo $row_AlunoLogado['aluno_emergencia_tel2']; ?></strong>&nbsp</td>
            </tr>

            <tr>
            	<td class="right grey-text text-darken-1">telefone/mãe</td>
            	<td class="black-text"><strong><?php echo $row_AlunoLogado['aluno_tel_mae']; ?></strong>&nbsp</td>
            </tr>

            <tr>
            	<td class="right grey-text text-darken-1">telefone/pai</td>
            	<td class="black-text"><strong><?php echo $row_AlunoLogado['aluno_tel_pai']; ?></strong>&nbsp</td>
            </tr>


        	<?php if ($row_AlunoLogado['aluno_email']<>"") { ?>
            <tr>
            	<td class="right grey-text text-darken-1">e-mail</td>
            	<td class="black-text"><strong><?php echo $row_AlunoLogado['aluno_email']; ?></strong>&nbsp</td>
            </tr>
			<?php } ?>		
        </tbody>
      </table>
      <i class="right"><small>Se algum dos dados de contato estiver desatualizado, <a href="editar_dados.php">clique aqui</a> para modificar</small></i>
      <?php } ?>



      <!-- FIM CONTEUDO CENTRAL-->          
      </div>
      <div class="col s12 m3">


        <h5><strong>Participantes da turma (<?php echo $totalRows_Colegas; ?>)</strong></h5>
        <hr>

      <p>
       <?php do { ?>
        <div class="col s4 center-align truncate">
          <a href="perfil.php?uid=<?php echo $row_Colegas['aluno_hash']; ?>">

            <?php 
if (!empty($row_Colegas['aluno_foto2'])) { ?>
    <img src="<?php echo URL_BASE . 'aluno/fotos2/' . $row_Colegas['aluno_foto2']; ?>" width="90%" class="hoverable aluno 1responsive-img" 
         style="<?php if ($row_Colegas['aluno_aceite_termos'] == "S") { echo "border: solid 3px #23CE6B"; } ?>"><br>
<?php } elseif (!empty($row_Colegas['aluno_foto'])) { ?>
    <img src="<?php echo URL_BASE . 'aluno/fotos/' . $row_Colegas['aluno_foto']; ?>" width="90%" class="hoverable aluno 1responsive-img" 
         style="<?php if ($row_Colegas['aluno_aceite_termos'] == "S") { echo "border: solid 3px #23CE6B"; } ?>"><br>
<?php } else { ?>
    <img src="<?php echo URL_BASE . 'aluno/fotos2/semfoto.jpg'; ?>" width="90%" class="hoverable aluno 1responsive-img" 
         style="<?php if ($row_Colegas['aluno_aceite_termos'] == "S") { echo "border: solid 3px #23CE6B"; } ?>"><br>
<?php } ?>

            
            
            <div style="font-size:10px;" class="">
              <?php echo current(str_word_count($row_Colegas['aluno_nome'], 2)); ?>
            </div><br>
          </a>
        </div>
      <?php } while ($row_Colegas = mysql_fetch_assoc($Colegas)); ?>
      </p>

      <br>
      <div class="divider"></div>
      <strong class="right-align right"></strong>


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
    });
  </script>


<?php if (isset($_GET['bemvindo'])) { ?>
<script type="text/javascript">
Swal.fire({
  title: "Bem-vindo(a) à EduConnect!",
  html: "<p>Estamos felizes em ter você aqui!</p><p>Agora que você faz parte da nossa rede social escolar, aproveite para explorar e interagir com seus colegas. Na EduConnect, você pode compartilhar suas ideias, aprender coisas novas, e construir conexões significativas com outros estudantes.</p><p>Lembre-se de que este é um espaço para colaboração, respeito e crescimento mútuo. Participe ativamente, contribua com suas experiências e faça deste ambiente um lugar positivo para todos!</p><p>Se precisar de ajuda ou tiver alguma dúvida, nossa equipe de suporte está aqui para ajudar.</p><p>Divirta-se e boas interações!</p>",
  icon: "success"
});
</script>
<?php } ?>


<?php if (isset($_GET['erro'])) { ?>
<script type="text/javascript">

Swal.fire({
  icon: "error",
  title: "Ocorreu um erro!",
  text: "Um relatório foi enviado aos administradores do sistema."
});

</script>
<?php } ?>



  <script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
      var bioTextarea = document.getElementById('bio');
      var maxLength = bioTextarea.getAttribute('data-length');

      bioTextarea.addEventListener('input', function() {
        if (bioTextarea.value.length >= maxLength) {
          bioTextarea.value = bioTextarea.value.substring(0, maxLength); // Corta o texto para o máximo permitido
        }
      });
    });
  </script>

<script type="text/javascript">
    $(document).ready(function() {
      $('#fan-button').click(function() {
        var isFan = $(this).data('is-fan');
        var button = $(this);

        // Desativa o botão para evitar múltiplos cliques
        button.prop('disabled', true);

        if (isFan) {
          // Se já é fã, mostra a mensagem de confirmação antes de "deixar de ser fã"
          Swal.fire({
            title: 'Você tem certeza?',
            text: "Você deixará de ser fã deste usuário.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim, deixar de ser fã',
            cancelButtonText: 'Cancelar'
          }).then((result) => {
            if (result.isConfirmed) {
              // Lógica para deixar de ser fã
              toggleFanStatus(button, isFan);
            } else {
              // Reativa o botão se a ação for cancelada
              button.prop('disabled', false);
            }
          });
        } else {
          // Se ainda não é fã, segue o fluxo normal
          toggleFanStatus(button, isFan);
        }
      });

      function toggleFanStatus(button, isFan) {
        // Define URL e dados com base no estado atual
        var url = isFan ? 'funcoes/unfan.php' : 'funcoes/fan.php';
        var data = {
          user_id: <?php echo $row_Perfil['aluno_id']; ?>, // ID do usuário logado
        };

        // Envia a requisição AJAX
        $.ajax({
          type: 'POST',
          url: url,
          data: data,
          success: function(response) {
            if (response.success) {
              var fanCountElement = $('#fan-count-number');
              var fanCount = parseInt(fanCountElement.text());

              if (isFan) {
                button.removeClass('grey lighten-5').addClass('waves-effect waves-light');
                button.css('color', ''); // Remove a cor customizada
                button.html('<i class="material-icons left">stars</i>VIRAR FÃ');
                button.data('is-fan', false);

                // Diminui o contador de fãs
                fanCountElement.text(fanCount - 1);

              } else {
                button.removeClass('waves-effect waves-light').addClass('grey lighten-5');
                button.css('color', '#ff8f00'); // Adiciona a cor customizada
                button.html('<i class="material-icons left">done</i>SOU FÃ');
                button.data('is-fan', true);

                // Aumenta o contador de fãs
                fanCountElement.text(fanCount + 1);
              }
            } else {
              Swal.fire(
                'Erro!',
                'Erro ao processar a solicitação.',
                'error'
              );
            }
          },
          error: function(xhr, status, error) {
            Swal.fire(
              'Erro!',
              'Ocorreu um erro. Informe ao administrador.',
              'error'
            );
            console.log('Erro na comunicação com o servidor: ', error);
            console.log(xhr.responseText);
          },
          complete: function() {
            // Reativa o botão após a conclusão da requisição AJAX
            button.prop('disabled', false);
          }
        });
      }
    });
</script>

</body>

</html>
<?php
mysql_free_result($Matricula);

mysql_free_result($AlunoLogado);
?>