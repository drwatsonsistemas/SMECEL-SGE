<?php require_once('../../Connections/SmecelNovoPDO.php'); ?>
<?php require_once "conf/session.php"; ?>
<?php require_once "fnc/anti_injection.php"; ?>

<?php

$ID_PROFESSOR = ID_PROFESSOR;
try {
  // Preparar a consulta PDO
  $query_Avisos = "
        SELECT vinculo_id, vinculo_id_escola, vinculo_id_sec, vinculo_id_funcionario, vinculo_id_funcao, vinculo_carga_horaria, vinculo_data_inicio, vinculo_obs,
               aviso_prof_id_escola, aviso_prof_texto, aviso_prof_exibir_ate, aviso_prof_data_cadastro, escola_id, escola_nome 
        FROM smc_vinculo
        INNER JOIN smc_aviso_prof ON aviso_prof_id_escola = vinculo_id_escola
        INNER JOIN smc_escola ON escola_id = vinculo_id_escola
        WHERE vinculo_id_funcionario = :id_professor
        ORDER BY aviso_prof_data_cadastro DESC
    ";

  // Preparar a conexão e execução da consulta
  $stmt = $SmecelNovo->prepare($query_Avisos);
  $stmt->bindParam(':id_professor', $ID_PROFESSOR, PDO::PARAM_INT);
  $stmt->execute();

  // Obter os resultados
  $avisos = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Verificar se há registros
  $totalRows_Avisos = count($avisos);

} catch (PDOException $e) {
  // Em caso de erro na consulta ou conexão
  echo "Erro: " . $e->getMessage();
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
  <link rel="stylesheet" href="css/sweetalert2.min.css">
</head>

<body>
  <?php include_once "inc/navebar.php"; ?>
  <?php include_once "inc/sidebar.php"; ?>
  <main class="ls-main">
    <div class="container-fluid">
      <h1 class="ls-title-intro ls-ico-home">Ano letivo <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h1>
      <p><a href="index.php" class="ls-btn ls-ico-chevron-left">Voltar</a></p>


      <div class="ls-box">
        &#128515; Seja bem-vind<?php if ($row_ProfLogado['func_sexo'] == 2) {
          echo "a";
        } else {
          echo "o";
        } ?>,
        <strong><?php $nomeProf = explode(" ", $row_ProfLogado['func_nome']);
        echo ucfirst(strtolower($nomeProf[0])); ?></strong>!
      </div>


      <?php if ($avisos) {
        foreach ($avisos as $aviso) {
          // Exibir cada aviso
          echo '<div class="ls-alert-info">';
          echo '<strong>' . htmlspecialchars($aviso['escola_nome']) . '</strong> em ' . date("d/m/Y", strtotime($aviso['aviso_prof_data_cadastro'])) . ' ';
          echo '<p>' . nl2br(htmlspecialchars($aviso['aviso_prof_texto'])) . '</p>';
          echo '</div>';
        }
      } else { ?>
        <div class="ls-alert-info"><strong>Atenção:</strong> Nenhum aviso cadastrado por sua Unidade Escolar.</div>
      <?php } ?>


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
