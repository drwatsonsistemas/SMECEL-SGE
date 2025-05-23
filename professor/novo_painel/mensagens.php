<?php
require_once('../../Connections/SmecelNovoPDO.php');
include "conf/session.php";
include "fnc/anti_injection.php";

// Garantir que a conexão PDO foi estabelecida corretamente
try {
  // Preparando a consulta com PDO
  $query_comunicados = "
    SELECT com_topico_id, com_topico_id_escola, com_topico_id_prof, com_topico_data, com_topico_texto, 
           com_topico_hash, com_topico_atualizacao, com_topico_visualizado, com_topico_quem, 
           func_id, func_nome, escola_id, escola_nome  
    FROM comun_esc_prof_topico
    INNER JOIN smc_func ON func_id = com_topico_id_prof
    INNER JOIN smc_escola ON escola_id = com_topico_id_escola
    WHERE com_topico_id_prof = :prof_id
    ORDER BY com_topico_atualizacao DESC
    ";

  // Preparando a execução da consulta no PDO
  $stmt = $SmecelNovo->prepare($query_comunicados);

  // Bind do parâmetro para evitar SQL Injection
  $stmt->bindParam(':prof_id', $row_ProfLogado['func_id'], PDO::PARAM_INT);

  // Executa a consulta
  $stmt->execute();

  // Busca todos os dados
  $comunicados = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Contando o número de resultados
  $totalRows_comunicados = count($comunicados);

} catch (PDOException $e) {
  echo "Erro: " . $e->getMessage();
  exit;
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
      <h1 class="ls-title-intro ls-ico-home">MENSAGENS</h1>
      <p><a href="index.php" class="ls-btn ls-ico-chevron-left">Voltar</a></p>


      <?php if ($totalRows_comunicados > 0) { ?>
        <table class="ls-table ls-sm-space">
          <thead>
            <tr>
              <th width="50"></th>
              <th>UNIDADE</th>
              <th width="400">NOME</th>
              <th width="110" class="ls-txt-center">DATA</th>
              <th width="80" class="ls-txt-center">HORA</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($totalRows_comunicados > 0): ?>
              <?php foreach ($comunicados as $row_comunicados): ?>
                <?php
                // Definindo o estilo de fonte com base no campo "com_topico_visualizado"
                $netrito = ($row_comunicados['com_topico_visualizado'] == "N") ? "bolder" : "normal";
                ?>
                <tr style="font-weight:<?php echo $netrito; ?>;">
                  <td class="ls-txt-center">
                    <?php if (($row_comunicados['com_topico_visualizado'] == "N") && ($row_comunicados['com_topico_quem'] == "C")): ?>
                      <strong><span class="ls-ico-envelope ls-ico-right"></span></strong>
                    <?php else: ?>
                      <span class=""></span>
                    <?php endif; ?>
                  </td>
                  <td><a
                      href="mensagem.php?msg=<?php echo htmlspecialchars($row_comunicados['com_topico_hash']); ?>"><?php echo htmlspecialchars($row_comunicados['escola_nome']); ?></a>
                  </td>
                  <td><a
                      href="mensagem.php?msg=<?php echo htmlspecialchars($row_comunicados['com_topico_hash']); ?>"><?php echo htmlspecialchars(substr($row_comunicados['com_topico_texto'], 0, 50)); ?>...</a>
                  </td>
                  <td class="ls-txt-center">
                    <?php
                    $emailData = date("d/m/Y", strtotime($row_comunicados['com_topico_atualizacao']));
                    echo ($emailData == date("d/m/Y")) ? "Hoje" : $emailData;
                    ?>
                  </td>
                  <td class="ls-txt-center">
                    <?php echo date("H:i", strtotime($row_comunicados['com_topico_atualizacao'])); ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="5">Nenhum comunicado encontrado.</td>
              </tr>
            <?php endif; ?>
            <tr>
              <td colspan="5">
                <p><small><strong><?php echo $totalRows_comunicados; ?></strong> comunicado(s) encontrado(s).</small></p>
              </td>
            </tr>
          </tbody>

        </table>
      <?php } else { ?>

        <hr>
        <p>Nenhuma mensagem.</p>

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