<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
<?php

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Vinculos = "
SELECT *, ano_letivo_id_sec, ano_letivo_aberto, escola_id, escola_nome
FROM smc_vinculo 
INNER JOIN smc_ano_letivo ON ano_letivo_id_sec = vinculo_id_sec
INNER JOIN smc_escola ON escola_id = vinculo_id_escola
WHERE vinculo_id_funcionario = '$row_ProfLogado[func_id]' AND ano_letivo_aberto = 'S'
";
$Vinculos = mysql_query($query_Vinculos, $SmecelNovo) or die(mysql_error());
$row_Vinculos = mysql_fetch_assoc($Vinculos);
$totalRows_Vinculos = mysql_num_rows($Vinculos);




?>
<!DOCTYPE html>
<html class="<?php echo TEMA; ?>" lang="pt-br">
<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
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
  <link rel="stylesheet" type="text/css" href="css/locastyle.css">  <link rel="stylesheet" href="css/sweetalert2.min.css">
</head>
<body>
  <?php include_once "inc/navebar.php"; ?>
  <?php include_once "inc/sidebar.php"; ?>
  <main class="ls-main">
    <div class="container-fluid">
      <h1 class="ls-title-intro ls-ico-home">AULAS AVULSAS |   Ano letivo <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h1>

      <p><a href="index.php" class="ls-btn ls-ico-chevron-left">Voltar</a></p>    
        <hr>

        <?php 
        do {
          mysql_select_db($database_SmecelNovo, $SmecelNovo);
          $query_Turmas = "
          SELECT turma_id, turma_nome, turma_ano_letivo, turma_turno, turma_id_escola
          FROM smc_turma
          WHERE turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND turma_id_escola = '$row_Vinculos[vinculo_id_escola]'
          ORDER BY turma_turno, turma_nome
          ";
          $Turmas = mysql_query($query_Turmas, $SmecelNovo) or die(mysql_error());
          $row_Turmas = mysql_fetch_assoc($Turmas);
          $totalRows_Turmas = mysql_num_rows($Turmas);

          ?>
          <h3><?php echo $row_Vinculos['escola_nome'] ?></h3>
          <table class="ls-table ls-sm-space">

            <tr>
              <th class="ls-txt-left" width="4%">N°</th>
              <th class="ls-txt-left">TURMA</th>
              <th class="ls-txt-left" width="50"></th>
              <th class="ls-txt-left"width="50"></th>

            </tr>

            <?php 
            $num_turmas = 0;

            do { 

              $num_turmas++;
              ?>
              <tr>
                <td class="ls-txt-left "><b><?php echo $num_turmas ?></b></td>
                <td class="ls-txt-left"><?php echo $row_Turmas['turma_nome']; ?></td>
                <td class="ls-txt-right"><a href="aulas_avulsa_cadastrar.php?turma=<?php echo $row_Turmas['turma_id'] ?>" class="ls-sm-margin-top ls-btn-primary ls-btn-xs ls-ico-plus"></a></td>
                <td class="ls-txt-right"><a href="mapa_aulas_avulsas.php?turma=<?php echo $row_Turmas['turma_id'] ?>" class="ls-sm-margin-top ls-btn-primary ls-btn-xs ls-ico-search"></a></td>
              </tr>

            <?php } while ($row_Turmas = mysql_fetch_assoc($Turmas)); ?>
          </table>

          <?php 
        } while ($row_Vinculos = mysql_fetch_assoc($Vinculos));
        ?>




      </div>
      <?php //include_once "inc/footer.php"; ?>
    </main>
    <?php include_once "inc/notificacoes.php"; ?>
    <!-- We recommended use jQuery 1.10 or up --> 
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
    <script src="js/locastyle.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/sweetalert2.min.js"></script>
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