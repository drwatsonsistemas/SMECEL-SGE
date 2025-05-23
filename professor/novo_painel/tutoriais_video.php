<?php
require_once('../../Connections/SmecelNovoPDO.php');
include "conf/session.php";
include "fnc/anti_injection.php";

// Consultando tutoriais com PDO
$query_Tutoriais = "
SELECT 
    tutoriais_video_id, 
    tutoriais_video_titulo, 
    tutoriais_video_url, 
    tutoriais_video_painel,
    CASE tutoriais_video_painel
        WHEN 1 THEN '<span class=ls-tag-primary>SECRETARIA</span>'
        WHEN 2 THEN '<span class=ls-tag-success>ESCOLA</span>'
        WHEN 3 THEN '<span class=ls-tag-info>PROFESSOR</span>'
        WHEN 4 THEN '<span class=ls-tag-warning>ALUNO</span>'
        WHEN 5 THEN '<span class=ls-tag-danger>PORTARIA</span>'
        WHEN 6 THEN '<span class=ls-tag>PSE</span>'
    END AS tutoriais_video_painel_descricao 
FROM smc_tutoriais_video
WHERE tutoriais_video_painel IN (3, 4)
ORDER BY tutoriais_video_painel, tutoriais_video_titulo ASC
";

// Preparar e executar a consulta
$stmt = $SmecelNovo->prepare($query_Tutoriais);
$stmt->execute();

// Buscar todos os resultados
$tutoriais = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRows_Tutoriais = count($tutoriais);
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
      <h1 class="ls-title-intro ls-ico-home">TUTORIAIS</h1>
      <p><a href="index.php" class="ls-btn ls-ico-chevron-left">Voltar</a></p>

      <table class="ls-table">
        <tr>
          <td>CLIQUE SOBRE O TÍTULO PARA ASSISTIR O VÍDEO TUTORIAL</td>
        </tr>
        <?php
        // Aqui estamos assumindo que você já tem a conexão com o banco de dados utilizando PDO
// Vamos usar o resultado da consulta PDO para exibir os vídeos.
        
        foreach ($tutoriais as $row_Tutoriais) {
          // Extraindo o link do vídeo do YouTube da URL
          $link = explode("=", $row_Tutoriais['tutoriais_video_url']);
          ?>
          <tr>
            <td>
              <!-- Link do vídeo, que abre em um modal -->
              <a class="ls-ico-screen" style="cursor:pointer" data-ls-module="modal" data-action="" data-content='<iframe width="100%" height="320" src="https://www.youtube.com/embed/<?php echo $link[1]; ?>" 
                            title="<?php echo htmlspecialchars($row_Tutoriais['tutoriais_video_titulo']); ?>" 
                            frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen></iframe>'
                data-title="<?php echo htmlspecialchars($row_Tutoriais['tutoriais_video_titulo']); ?>"
                data-class="ls-btn-danger" data-save="FECHAR" data-close="CANCELAR">
                &nbsp;<?php echo htmlspecialchars($row_Tutoriais['tutoriais_video_titulo']); ?>
              </a>
            </td>
          </tr>
        <?php } ?>

      </table>


    </div>
    <?php //include_once "inc/footer.php"; ?>
  </main>
  <?php include_once "inc/notificacoes.php"; ?>
  <!-- We recommended use jQuery 1.10 or up -->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="js/locastyle.js"></script>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="js/sweetalert2.min.js"></script>
  <script src="https://www.gstatic.com/dialogflow-console/fast/messenger/bootstrap.js?v=1"></script>
  <df-messenger
    chat-icon="https:&#x2F;&#x2F;storage.googleapis.com&#x2F;cloudprod-apiai&#x2F;d6fbe379-bf37-4b42-b738-5ec0762d62da_x.png"
    intent="WELCOME" chat-title="Smecel-FAQ" agent-id="553bbcb9-1afc-4a31-9d38-44ae05426572"
    language-code="pt-br"></df-messenger>

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