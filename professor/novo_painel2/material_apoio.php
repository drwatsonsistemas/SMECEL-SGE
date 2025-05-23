<?php
require_once('../../Connections/SmecelNovoPDO.php');
include "conf/session.php";
include "fnc/anti_injection.php";

try {

  $SEC_ID = SEC_ID;
  // Consulta para os materiais tipo 1
  $query_Material1 = "
    SELECT material_id, material_id_sec, material_tipo, material_painel_escola, material_painel_professor, 
           material_link, material_titulo, material_descricao, material_etapa, material_componente, material_hash,
           etapa_id, etapa_nome, etapa_nome_abrev, disciplina_id, disciplina_nome, disciplina_nome_abrev 
    FROM smc_material_apoio
    LEFT JOIN smc_etapa ON etapa_id = material_etapa 
    LEFT JOIN smc_disciplina ON disciplina_id = material_componente
    WHERE material_id_sec = :sec_id AND material_tipo = 1 AND material_painel_professor = 'S'";

  $stmt = $SmecelNovo->prepare($query_Material1);
  $stmt->bindParam(':sec_id', $SEC_ID, PDO::PARAM_INT);
  $stmt->execute();
  $Material1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
  $totalRows_Material1 = count($Material1);

  // Consulta para os materiais tipo 2
  $query_Material2 = "
    SELECT material_id, material_id_sec, material_tipo, material_painel_escola, material_painel_professor, 
           material_link, material_titulo, material_descricao, material_etapa, material_componente, material_hash,
           etapa_id, etapa_nome, etapa_nome_abrev, disciplina_id, disciplina_nome, disciplina_nome_abrev 
    FROM smc_material_apoio
    LEFT JOIN smc_etapa ON etapa_id = material_etapa 
    LEFT JOIN smc_disciplina ON disciplina_id = material_componente
    WHERE material_id_sec = :sec_id AND material_tipo = 2 AND material_painel_professor = 'S'";

  $stmt = $SmecelNovo->prepare($query_Material2);
  $stmt->bindParam(':sec_id', $SEC_ID, PDO::PARAM_INT);
  $stmt->execute();
  $Material2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
  $totalRows_Material2 = count($Material2);

  // Consulta para os materiais tipo 3
  $query_Material3 = "
    SELECT material_id, material_id_sec, material_tipo, material_painel_escola, material_painel_professor, 
           material_link, material_titulo, material_descricao, material_etapa, material_componente, material_hash,
           etapa_id, etapa_nome, etapa_nome_abrev, disciplina_id, disciplina_nome, disciplina_nome_abrev 
    FROM smc_material_apoio
    LEFT JOIN smc_etapa ON etapa_id = material_etapa 
    LEFT JOIN smc_disciplina ON disciplina_id = material_componente
    WHERE material_id_sec = :sec_id AND material_tipo = 3 AND material_painel_professor = 'S'";

  $stmt = $SmecelNovo->prepare($query_Material3);
  $stmt->bindParam(':sec_id', $SEC_ID, PDO::PARAM_INT);
  $stmt->execute();
  $Material3 = $stmt->fetchAll(PDO::FETCH_ASSOC);
  $totalRows_Material3 = count($Material3);

  // Consulta para os materiais tipo 4
  $query_Material4 = "
    SELECT material_id, material_id_sec, material_tipo, material_painel_escola, material_painel_professor, 
           material_link, material_titulo, material_descricao, material_etapa, material_componente, material_hash,
           etapa_id, etapa_nome, etapa_nome_abrev, disciplina_id, disciplina_nome, disciplina_nome_abrev 
    FROM smc_material_apoio
    LEFT JOIN smc_etapa ON etapa_id = material_etapa 
    LEFT JOIN smc_disciplina ON disciplina_id = material_componente
    WHERE material_id_sec = :sec_id AND material_tipo = 4 AND material_painel_professor = 'S'";

  $stmt = $SmecelNovo->prepare($query_Material4);
  $stmt->bindParam(':sec_id', $SEC_ID, PDO::PARAM_INT);
  $stmt->execute();
  $Material4 = $stmt->fetchAll(PDO::FETCH_ASSOC);
  $totalRows_Material4 = count($Material4);

  $query_Material5 = "
  SELECT material_id, material_id_sec, material_tipo, material_painel_escola, material_painel_professor, 
         material_link, material_titulo, material_descricao, material_etapa, material_componente, material_hash,
         etapa_id, etapa_nome, etapa_nome_abrev, disciplina_id, disciplina_nome, disciplina_nome_abrev 
  FROM smc_material_apoio
  LEFT JOIN smc_etapa ON etapa_id = material_etapa 
  LEFT JOIN smc_disciplina ON disciplina_id = material_componente
  WHERE material_id_sec = :sec_id AND material_tipo = 5 AND material_painel_professor = 'S'";

$stmt = $SmecelNovo->prepare($query_Material5);
$stmt->bindParam(':sec_id', $SEC_ID, PDO::PARAM_INT);
$stmt->execute();
$Material5 = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRows_Material5 = count($Material5);

$query_Material6 = "
SELECT material_id, material_id_sec, material_tipo, material_painel_escola, material_painel_professor, 
       material_link, material_titulo, material_descricao, material_etapa, material_componente, material_hash,
       etapa_id, etapa_nome, etapa_nome_abrev, disciplina_id, disciplina_nome, disciplina_nome_abrev 
FROM smc_material_apoio
LEFT JOIN smc_etapa ON etapa_id = material_etapa 
LEFT JOIN smc_disciplina ON disciplina_id = material_componente
WHERE material_id_sec = :sec_id AND material_tipo = 6 AND material_painel_professor = 'S'";

$stmt = $SmecelNovo->prepare($query_Material6);
$stmt->bindParam(':sec_id', $SEC_ID, PDO::PARAM_INT);
$stmt->execute();
$Material6 = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRows_Material6 = count($Material6);

$query_Material7 = "
SELECT material_id, material_id_sec, material_tipo, material_painel_escola, material_painel_professor, 
       material_link, material_titulo, material_descricao, material_etapa, material_componente, material_hash,
       etapa_id, etapa_nome, etapa_nome_abrev, disciplina_id, disciplina_nome, disciplina_nome_abrev 
FROM smc_material_apoio
LEFT JOIN smc_etapa ON etapa_id = material_etapa 
LEFT JOIN smc_disciplina ON disciplina_id = material_componente
WHERE material_id_sec = :sec_id AND material_tipo = 7 AND material_painel_professor = 'S'";

$stmt = $SmecelNovo->prepare($query_Material7);
$stmt->bindParam(':sec_id', $SEC_ID, PDO::PARAM_INT);
$stmt->execute();
$Material7 = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRows_Material7 = count($Material7);

$query_Material8 = "
SELECT material_id, material_id_sec, material_tipo, material_painel_escola, material_painel_professor, 
       material_link, material_titulo, material_descricao, material_etapa, material_componente, material_hash,
       etapa_id, etapa_nome, etapa_nome_abrev, disciplina_id, disciplina_nome, disciplina_nome_abrev 
FROM smc_material_apoio
LEFT JOIN smc_etapa ON etapa_id = material_etapa 
LEFT JOIN smc_disciplina ON disciplina_id = material_componente
WHERE material_id_sec = :sec_id AND material_tipo = 8 AND material_painel_professor = 'S'";

$stmt = $SmecelNovo->prepare($query_Material8);
$stmt->bindParam(':sec_id', $SEC_ID, PDO::PARAM_INT);
$stmt->execute();
$Material8 = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRows_Material8 = count($Material8);

$query_Material9 = "
SELECT material_id, material_id_sec, material_tipo, material_painel_escola, material_painel_professor, 
       material_link, material_titulo, material_descricao, material_etapa, material_componente, material_hash,
       etapa_id, etapa_nome, etapa_nome_abrev, disciplina_id, disciplina_nome, disciplina_nome_abrev 
FROM smc_material_apoio
LEFT JOIN smc_etapa ON etapa_id = material_etapa 
LEFT JOIN smc_disciplina ON disciplina_id = material_componente
WHERE material_id_sec = :sec_id AND material_tipo = 9 AND material_painel_professor = 'S'";

$stmt = $SmecelNovo->prepare($query_Material9);
$stmt->bindParam(':sec_id', $SEC_ID, PDO::PARAM_INT);
$stmt->execute();
$Material9 = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRows_Material9 = count($Material9);

} catch (PDOException $e) {
  // Se houver erro de execução ou conexão, captura e exibe
  echo 'Erro: ' . $e->getMessage();
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
  <link rel="stylesheet" type="text/css" href="//assets.locaweb.com.br/locastyle/edge/stylesheets/locastyle.css">
  <link rel="stylesheet" href="css/sweetalert2.min.css">
</head>

<body>
  <?php include_once "inc/navebar.php"; ?>
  <?php include_once "inc/sidebar.php"; ?>
  <main class="ls-main">
    <div class="container-fluid">
      <h1 class="ls-title-intro ls-ico-home">Ano letivo <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h1>


      <p><a href="index.php" class="ls-btn ls-ico-chevron-left">Voltar</a></p>

      <hr>

      <h5 class="ls-title-3">MATERIAL DE APOIO</h5>

      <ul class="ls-tabs-nav">
        <li class="ls-active"><a data-ls-module="tabs" href="#dcrm">DCRM (<?php echo $totalRows_Material1; ?>)</a></li>
        <li><a data-ls-module="tabs" href="#livros">LIVROS (<?php echo $totalRows_Material2; ?>)</a></li>
        <li><a data-ls-module="tabs" href="#plan">PLANEJAMENTO ANUAL (<?php echo $totalRows_Material3; ?>)</a></li>
        <li><a data-ls-module="tabs" href="#edinf">EDUCAÇÃO INFANTIL (<?php echo $totalRows_Material5; ?>)</a></li>
        <li><a data-ls-module="tabs" href="#anosini">ANOS INICIAIS (<?php echo $totalRows_Material6; ?>)</a></li>
        <li><a data-ls-module="tabs" href="#anosfim">ANOS FINAIS (<?php echo $totalRows_Material7; ?>)</a></li>
        <li><a data-ls-module="tabs" href="#eja">EJA (<?php echo $totalRows_Material8; ?>)</a></li>
        <li><a data-ls-module="tabs" href="#campo">EDUCAÇÃO DO CAMPO (<?php echo $totalRows_Material9; ?>)</a></li>
        <li><a data-ls-module="tabs" href="#outros">DIVERSOS (<?php echo $totalRows_Material4; ?>)</a></li>
      </ul>
      <div class="ls-tabs-container">
        <div id="dcrm" class="ls-tab-content ls-active">
          <p>
            <?php if ($totalRows_Material1 > 0) { ?>
            <table class="ls-table ls-sm-space">
              <thead>

                <tr>
                  <th width="50"></th>
                  <th>TÍTULO</th>
                  <th width="220">ETAPA</th>
                  <th>COMP/CAMPO EXP.</th>
                </tr>
              </thead>

              <tbody>
                <?php
                // Verificando se temos materiais para exibir
                if ($totalRows_Material1 > 0) {
                  foreach ($Material1 as $row_Material1) {
                    ?>
                    <tr>
                      <td>
                        <a href="../../material_apoio/<?php echo htmlspecialchars($row_Material1['material_link']); ?>"
                          target="_blank">
                          <span class="ls-ico-cloud-download"></span>
                        </a>
                      </td>
                      <td>
                        <strong><?php echo htmlspecialchars($row_Material1['material_titulo']); ?></strong> <br>
                        <i><?php echo htmlspecialchars($row_Material1['material_descricao']); ?></i>
                      </td>
                      <td>
                        <?php echo !empty($row_Material1['etapa_nome_abrev']) ? htmlspecialchars($row_Material1['etapa_nome_abrev']) : 'SEM CRITÉRIOS'; ?>
                      </td>
                      <td>
                        <?php echo !empty($row_Material1['disciplina_nome']) ? htmlspecialchars($row_Material1['disciplina_nome']) : 'SEM CRITÉRIOS'; ?>
                      </td>
                    </tr>
                    <?php
                  }
                }
                ?>
                <tr>
                  <td colspan="6">
                    <p><small><strong><?php echo $totalRows_Material1; ?></strong> arquivo(s) enviado(s).</small></p>
                  </td>
                </tr>
              </tbody>
            </table>
          <?php } else { ?>
            Nenhum arquivo adicionado
          <?php } ?>
          </p>
        </div>
        <div id="livros" class="ls-tab-content">
          <p>
            <?php if ($totalRows_Material2 > 0) { ?>
            <table class="ls-table ls-sm-space">
              <thead>

                <tr>
                  <th width="50"></th>
                  <th>TÍTULO</th>
                  <th width="220">ETAPA</th>
                  <th>COMP/CAMPO EXP.</th>
                </tr>
              </thead>

              <tbody>
                <?php foreach ($Material2 as $row_Material2) { ?>
                  <tr>
            <td class="ls-txt-center">
                <a href="../../material_apoio/<?php echo $row_Material2['material_link']; ?>" target="_blank">
                    <span class="ls-ico-cloud-download"></span>
                </a>
            </td>
            <td>
                <strong><?php echo $row_Material2['material_titulo']; ?></strong> <br>
                <i><?php echo $row_Material2['material_descricao']; ?></i>
            </td>
            <td>
                <?php if (empty($row_Material2['etapa_nome_abrev'])) { ?>
                    SEM CRITÉRIOS
                <?php } else { ?>
                    <?php echo $row_Material2['etapa_nome_abrev']; ?>
                <?php } ?>
            </td>
            <td>
                <?php if (empty($row_Material2['disciplina_nome'])) { ?>
                    SEM CRITÉRIOS
                <?php } else { ?>
                    <?php echo $row_Material2['disciplina_nome']; ?>
                <?php } ?>
            </td>
        </tr>

                <?php }; ?>
                <tr>
                  <td colspan="6">
                    <p><small><strong><?php echo $totalRows_Material2; ?></strong> arquivo(s) enviado(s).</small></p>
                  </td>
                </tr>
              </tbody>

            </table>
          <?php } else { ?>
            Nenhum arquivo adicionado
          <?php } ?>
          </p>
        </div>
        <div id="plan" class="ls-tab-content">
          <p>
            <?php if ($totalRows_Material3 > 0) { ?>
            <table class="ls-table ls-sm-space">
              <thead>

                <tr>
                  <th width="50"></th>
                  <th>TÍTULO</th>
                  <th width="220">ETAPA</th>
                  <th>COMP/CAMPO EXP.</th>
                </tr>
              </thead>

              <tbody>
                <?php foreach ($Material3 as $row_Material3) { ?>
                  <tr>
                    <td class="ls-txt-center"><a href="../../material_apoio/<?php echo $row_Material3['material_link']; ?>"
                        target="_blank"><span class="ls-ico-cloud-download"></span></a></td>
                    <td><strong><?php echo $row_Material3['material_titulo']; ?></strong> <br>
                      <i><?php echo $row_Material3['material_descricao']; ?></i>
                    </td>
                    <td><?php if ($row_Material3['etapa_nome_abrev'] == "") { ?>
                        SEM CRITÉRIOS
                      <?php } else { ?>
                        <?php echo $row_Material3['etapa_nome_abrev']; ?>
                      <?php } ?>
                    </td>
                    <td><?php if ($row_Material3['disciplina_nome'] == "") { ?>
                        SEM CRITÉRIOS
                      <?php } else { ?>
                        <?php echo $row_Material3['disciplina_nome']; ?>
                      <?php } ?>
                    </td>

                  </tr>

                <?php }  ?>
                <tr>
                  <td colspan="6">
                    <p><small><strong><?php echo $totalRows_Material3; ?></strong> arquivo(s) enviado(s).</small></p>
                  </td>
                </tr>
              </tbody>

            </table>
          <?php } else { ?>
            Nenhum arquivo adicionado
          <?php } ?>
          </p>
        </div>
        <div id="outros" class="ls-tab-content">
          <p>
            <?php if ($totalRows_Material4 > 0) { ?>
            <table class="ls-table ls-sm-space">
              <thead>

                <tr>
                  <th width="50"></th>
                  <th>TÍTULO</th>
                  <th width="220">ETAPA</th>
                  <th>COMP/CAMPO EXP.</th>
                </tr>
              </thead>

              <tbody>
                <?php foreach ($Material4 as $row_Material4) { ?>
                  <tr>
                    <td class="ls-txt-center"><a href="../../material_apoio/<?php echo $row_Material4['material_link']; ?>"
                        target="_blank"><span class="ls-ico-cloud-download"></span></a></td>
                    <td><strong><?php echo $row_Material4['material_titulo']; ?></strong> <br>
                      <i><?php echo $row_Material4['material_descricao']; ?></i>
                    </td>
                    <td><?php if ($row_Material4['etapa_nome_abrev'] == "") { ?>
                        SEM CRITÉRIOS
                      <?php } else { ?>
                        <?php echo $row_Material4['etapa_nome_abrev']; ?>
                      <?php } ?>
                    </td>
                    <td><?php if ($row_Material4['disciplina_nome'] == "") { ?>
                        SEM CRITÉRIOS
                      <?php } else { ?>
                        <?php echo $row_Material4['disciplina_nome']; ?>
                      <?php } ?>
                    </td>

                  </tr>

                <?php } ?>
                <tr>
                  <td colspan="6">
                    <p><small><strong><?php echo $totalRows_Material4; ?></strong> arquivo(s) enviado(s).</small></p>
                  </td>
                </tr>
              </tbody>

            </table>
          <?php } else { ?>
            Nenhum arquivo adicionado
          <?php } ?>
          </p>
        </div>

        <div id="edinf" class="ls-tab-content">
          <p>
            <?php if ($totalRows_Material5 > 0) { ?>
            <table class="ls-table ls-sm-space">
              <thead>

                <tr>
                  <th width="50"></th>
                  <th>TÍTULO</th>
                  <th width="220">ETAPA</th>
                  <th>COMP/CAMPO EXP.</th>
                </tr>
              </thead>

              <tbody>
                <?php foreach ($Material5 as $row_Material5) { ?>
                  <tr>
                    <td class="ls-txt-center"><a href="../../material_apoio/<?php echo $row_Material5['material_link']; ?>"
                        target="_blank"><span class="ls-ico-cloud-download"></span></a></td>
                    <td><strong><?php echo $row_Material5['material_titulo']; ?></strong> <br>
                      <i><?php echo $row_Material5['material_descricao']; ?></i>
                    </td>
                    <td><?php if ($row_Material5['etapa_nome_abrev'] == "") { ?>
                        SEM CRITÉRIOS
                      <?php } else { ?>
                        <?php echo $row_Material5['etapa_nome_abrev']; ?>
                      <?php } ?>
                    </td>
                    <td><?php if ($row_Material5['disciplina_nome'] == "") { ?>
                        SEM CRITÉRIOS
                      <?php } else { ?>
                        <?php echo $row_Material5['disciplina_nome']; ?>
                      <?php } ?>
                    </td>

                  </tr>

                <?php } ?>
                <tr>
                  <td colspan="6">
                    <p><small><strong><?php echo $totalRows_Material5; ?></strong> arquivo(s) enviado(s).</small></p>
                  </td>
                </tr>
              </tbody>

            </table>
          <?php } else { ?>
            Nenhum arquivo adicionado
          <?php } ?>
          </p>
        </div>

        <div id="anosini" class="ls-tab-content">
          <p>
            <?php if ($totalRows_Material6 > 0) { ?>
            <table class="ls-table ls-sm-space">
              <thead>

                <tr>
                  <th width="50"></th>
                  <th>TÍTULO</th>
                  <th width="220">ETAPA</th>
                  <th>COMP/CAMPO EXP.</th>
                </tr>
              </thead>

              <tbody>
                <?php foreach ($Material6 as $row_Material6) { ?>
                  <tr>
                    <td class="ls-txt-center"><a href="../../material_apoio/<?php echo $row_Material6['material_link']; ?>"
                        target="_blank"><span class="ls-ico-cloud-download"></span></a></td>
                    <td><strong><?php echo $row_Material6['material_titulo']; ?></strong> <br>
                      <i><?php echo $row_Material6['material_descricao']; ?></i>
                    </td>
                    <td><?php if ($row_Material6['etapa_nome_abrev'] == "") { ?>
                        SEM CRITÉRIOS
                      <?php } else { ?>
                        <?php echo $row_Material6['etapa_nome_abrev']; ?>
                      <?php } ?>
                    </td>
                    <td><?php if ($row_Material6['disciplina_nome'] == "") { ?>
                        SEM CRITÉRIOS
                      <?php } else { ?>
                        <?php echo $row_Material6['disciplina_nome']; ?>
                      <?php } ?>
                    </td>

                  </tr>

                <?php } ?>
                <tr>
                  <td colspan="6">
                    <p><small><strong><?php echo $totalRows_Material6; ?></strong> arquivo(s) enviado(s).</small></p>
                  </td>
                </tr>
              </tbody>

            </table>
          <?php } else { ?>
            Nenhum arquivo adicionado
          <?php } ?>
          </p>
        </div>

        <div id="anosfim" class="ls-tab-content">
          <p>
            <?php if ($totalRows_Material7 > 0) { ?>
            <table class="ls-table ls-sm-space">
              <thead>

                <tr>
                  <th width="50"></th>
                  <th>TÍTULO</th>
                  <th width="220">ETAPA</th>
                  <th>COMP/CAMPO EXP.</th>
                </tr>
              </thead>

              <tbody>
                <?php foreach ($Material7 as $row_Material7) { ?>
                  <tr>
                    <td class="ls-txt-center"><a href="../../material_apoio/<?php echo $row_Material7['material_link']; ?>"
                        target="_blank"><span class="ls-ico-cloud-download"></span></a></td>
                    <td><strong><?php echo $row_Material7['material_titulo']; ?></strong> <br>
                      <i><?php echo $row_Material7['material_descricao']; ?></i>
                    </td>
                    <td><?php if ($row_Material7['etapa_nome_abrev'] == "") { ?>
                        SEM CRITÉRIOS
                      <?php } else { ?>
                        <?php echo $row_Material7['etapa_nome_abrev']; ?>
                      <?php } ?>
                    </td>
                    <td><?php if ($row_Material7['disciplina_nome'] == "") { ?>
                        SEM CRITÉRIOS
                      <?php } else { ?>
                        <?php echo $row_Material7['disciplina_nome']; ?>
                      <?php } ?>
                    </td>

                  </tr>

                <?php } ?>
                <tr>
                  <td colspan="6">
                    <p><small><strong><?php echo $totalRows_Material7; ?></strong> arquivo(s) enviado(s).</small></p>
                  </td>
                </tr>
              </tbody>

            </table>
          <?php } else { ?>
            Nenhum arquivo adicionado
          <?php } ?>
          </p>
        </div>

        <div id="eja" class="ls-tab-content">
          <p>
            <?php if ($totalRows_Material8 > 0) { ?>
            <table class="ls-table ls-sm-space">
              <thead>

                <tr>
                  <th width="50"></th>
                  <th>TÍTULO</th>
                  <th width="220">ETAPA</th>
                  <th>COMP/CAMPO EXP.</th>
                </tr>
              </thead>

              <tbody>
                <?php foreach ($Material8 as $row_Material8) { ?>
                  <tr>
                    <td class="ls-txt-center"><a href="../../material_apoio/<?php echo $row_Material8['material_link']; ?>"
                        target="_blank"><span class="ls-ico-cloud-download"></span></a></td>
                    <td><strong><?php echo $row_Material8['material_titulo']; ?></strong> <br>
                      <i><?php echo $row_Material8['material_descricao']; ?></i>
                    </td>
                    <td><?php if ($row_Material8['etapa_nome_abrev'] == "") { ?>
                        SEM CRITÉRIOS
                      <?php } else { ?>
                        <?php echo $row_Material8['etapa_nome_abrev']; ?>
                      <?php } ?>
                    </td>
                    <td><?php if ($row_Material8['disciplina_nome'] == "") { ?>
                        SEM CRITÉRIOS
                      <?php } else { ?>
                        <?php echo $row_Material8['disciplina_nome']; ?>
                      <?php } ?>
                    </td>

                  </tr>

                <?php } ?>
                <tr>
                  <td colspan="6">
                    <p><small><strong><?php echo $totalRows_Material8; ?></strong> arquivo(s) enviado(s).</small></p>
                  </td>
                </tr>
              </tbody>

            </table>
          <?php } else { ?>
            Nenhum arquivo adicionado
          <?php } ?>
          </p>
        </div>
      </div>

      <div id="campo" class="ls-tab-content">
          <p>
            <?php if ($totalRows_Material9 > 0) { ?>
            <table class="ls-table ls-sm-space">
              <thead>

                <tr>
                  <th width="50"></th>
                  <th>TÍTULO</th>
                  <th width="220">ETAPA</th>
                  <th>COMP/CAMPO EXP.</th>
                </tr>
              </thead>

              <tbody>
                <?php foreach ($Material9 as $row_Material9) { ?>
                  <tr>
                    <td class="ls-txt-center"><a href="../../material_apoio/<?php echo $row_Material9['material_link']; ?>"
                        target="_blank"><span class="ls-ico-cloud-download"></span></a></td>
                    <td><strong><?php echo $row_Material9['material_titulo']; ?></strong> <br>
                      <i><?php echo $row_Material9['material_descricao']; ?></i>
                    </td>
                    <td><?php if ($row_Material9['etapa_nome_abrev'] == "") { ?>
                        SEM CRITÉRIOS
                      <?php } else { ?>
                        <?php echo $row_Material9['etapa_nome_abrev']; ?>
                      <?php } ?>
                    </td>
                    <td><?php if ($row_Material9['disciplina_nome'] == "") { ?>
                        SEM CRITÉRIOS
                      <?php } else { ?>
                        <?php echo $row_Material9['disciplina_nome']; ?>
                      <?php } ?>
                    </td>

                  </tr>

                <?php } ?>
                <tr>
                  <td colspan="6">
                    <p><small><strong><?php echo $totalRows_Material9; ?></strong> arquivo(s) enviado(s).</small></p>
                  </td>
                </tr>
              </tbody>

            </table>
          <?php } else { ?>
            Nenhum arquivo adicionado
          <?php } ?>
          </p>
        </div>

    </div>
    <?php //include_once "inc/footer.php"; ?>
  </main>
  <?php include_once "inc/notificacoes.php"; ?>
  <!-- We recommended use jQuery 1.10 or up -->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="//assets.locaweb.com.br/locastyle/edge/javascripts/locastyle.js"></script>
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