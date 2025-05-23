<?php
// Inclui o arquivo de conexão PDO
require_once('../../Connections/SmecelNovoPDO.php');
include "conf/session.php";

try {
  $stmt = $SmecelNovo->prepare("
  SELECT v.*, al.ano_letivo_ano, e.escola_nome, f.func_senha_ativa, f.func_id, fun.funcao_id, fun.funcao_docencia
  FROM smc_vinculo v
  INNER JOIN smc_ano_letivo al ON al.ano_letivo_id_sec = v.vinculo_id_sec
  INNER JOIN smc_escola e ON e.escola_id = v.vinculo_id_escola
  INNER JOIN smc_func f ON v.vinculo_id_funcionario = f.func_id
  INNER JOIN smc_funcao fun ON v.vinculo_id_funcao = fun.funcao_id
  WHERE v.vinculo_id_funcionario = :func_id 
  AND al.ano_letivo_aberto = 'S' 
  AND v.vinculo_acesso = 'S'
  AND v.vinculo_status = 1 
  AND f.func_senha_ativa = 1 
  AND funcao_docencia = 'S'
  AND al.ano_letivo_ano = :ano_letivo_ano
");
  $stmt->bindValue(':func_id', $row_ProfLogado['func_id'], PDO::PARAM_INT);
  $stmt->bindValue(':ano_letivo_ano', $row_AnoLetivo['ano_letivo_ano'], PDO::PARAM_STR);
  $stmt->execute();
  $vinculos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  echo "Erro na consulta: " . $e->getMessage();
  error_log("Erro PDO: " . $e->getMessage() . " - " . date("Y-m-d H:i:s"));
  die();
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

  <style>
    .loading-spinner {
      display: fllex;
      /* Oculto por padrão */
      text-align: center;
      /* Centraliza o spinner */
      padding: 20px;
    }

    .loading-spinner img {
      max-width: 100px;
      /* Ajuste o tamanho conforme necessário */
      height: auto;
    }
  </style>
</head>

<body>
  <?php include_once "inc/navebar.php"; ?>
  <?php include_once "inc/sidebar.php"; ?>
  <main class="ls-main">
    <div class="container-fluid">
      <h1 class="ls-title-intro ls-ico-home">AULAS AVULSAS | Ano letivo
        <?php echo $row_AnoLetivo['ano_letivo_ano']; ?>
      </h1>

      <p><a href="aulas_calendario.php" class="ls-btn ls-ico-chevron-left">Voltar</a></p>

      <hr>
      <?php foreach ($vinculos as $vinculo): ?>
        <div id="turmas-container-<?php echo $vinculo['vinculo_id_escola']; ?>">
          <h3><?php echo $vinculo['escola_nome']; ?></h3>

          <table class="ls-table ls-sm-space">
            <thead>
              <tr>
                <th class="ls-txt-left" width="4%">N°</th>
                <th class="ls-txt-left">TURMA</th>
                <th class="ls-txt-left" width="50"></th>
                <th class="ls-txt-left" width="50"></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td colspan="4">
                  <div class="loading-spinner" id="spinner-<?php echo $vinculo['vinculo_id_escola']; ?>">
                    <img src="images/spinner.gif" alt="Carregando...">
                  </div>
                  <center>Carregando aulas avulsas...</center>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      <?php endforeach; ?>

      <script>
        function loadTurmas(containerId, anoLetivo, escolaId) {
          fetch(`consultas/aulas_avulsas.php?ano_letivo=${anoLetivo}&escola_id=${escolaId}`)
            .then(response => {
              if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
              }
              return response.json();
            })
            .then(turmas => {
              const tableBody = document.querySelector(`${containerId} tbody`);
              tableBody.innerHTML = '';
              let numTurmas = 0;
              if (turmas && turmas.length > 0) {
                function utf8ToLatin1Fix(str) {
                  try {
                    return decodeURIComponent(escape(str));
                  } catch (e) {
                    return str; // Retorna o original se houver erro
                  }
                }
                turmas.forEach(turma => {
                  numTurmas++;
                  const row = tableBody.insertRow();
                  row.innerHTML = `
                    <td class="ls-txt-left"><b>${numTurmas}</b></td>
                    <td class="ls-txt-left">${utf8ToLatin1Fix(turma.turma_nome)}</td>
                    <td class="ls-txt-right">
                      <a href="aulas_avulsa_cadastrar.php?turma=${turma.turma_id}" class="ls-sm-margin-top ls-btn-primary ls-btn-xs ls-ico-plus"></a>
                    </td>
                    <td class="ls-txt-right">
                      <a href="mapa_aulas_avulsas.php?turma=${turma.turma_id}" class="ls-sm-margin-top ls-btn-primary ls-btn-xs ls-ico-search"></a>
                    </td>
                  `;
                });
              } else {
                const row = tableBody.insertRow();
                row.innerHTML = `<td colspan="4">Nenhuma turma encontrada.</td>`;
              }
            })
            .catch(error => {
              console.error("Erro ao carregar turmas:", error);
              const tableBody = document.querySelector(`${containerId} tbody`);
              tableBody.innerHTML = '';
              const row = tableBody.insertRow();
              row.innerHTML = `<td colspan="4">Erro ao carregar as turmas.</td>`;
            });
        }

        // Chama a função para cada vínculo
        <?php foreach ($vinculos as $vinculo): ?>
          loadTurmas(
            '#turmas-container-<?php echo $vinculo['vinculo_id_escola']; ?>',
            '<?php echo $vinculo['ano_letivo_ano']; ?>',
            '<?php echo $vinculo['vinculo_id_escola']; ?>'
          );
        <?php endforeach; ?>
      </script>
    </div>
    <?php //include_once "inc/footer.php"; ?>
  </main>
  <?php include_once "inc/notificacoes.php"; ?>
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="js/locastyle.js"></script>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="js/sweetalert2.min.js"></script>

</body>

</html>