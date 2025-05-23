<?php require_once('../../Connections/SmecelNovoPDO.php'); ?>
<?php include('../funcoes/inverteData.php'); ?>
<?php include "fnc/sessionPDO.php"; ?>
<?php include "fnc/anti_injection.php"; ?>

<?php
if (!function_exists("GetSQLValueString")) {
  function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "")
  {
    if (PHP_VERSION < 6) {
      $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
    }

    switch ($theType) {
      case "text":
        $theValue = ($theValue != "") ? "'" . addslashes($theValue) . "'" : "NULL";
        break;
      case "long":
      case "int":
        $theValue = ($theValue != "") ? intval($theValue) : "NULL";
        break;
      case "double":
        $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
        break;
      case "date":
        $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
        break;
      case "defined":
        $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
        break;
    }
    return $theValue;
  }
}

include "usuLogadoPDO.php";
include "fnc/anoLetivoPDO.php";

// Consulta para obter informações da escola logada
$query_EscolaLogada = "
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, 
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, sec_id, sec_cidade, sec_uf, escola_tema 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = :escola_id";

$stmt = $SmecelNovo->prepare($query_EscolaLogada);
$stmt->bindParam(':escola_id', $row_UsuLogado['usu_escola'], PDO::PARAM_INT);
$stmt->execute();
$row_EscolaLogada = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_EscolaLogada = $stmt->rowCount();


$query_Ano = "SELECT ano_letivo_id, ano_letivo_ano, ano_letivo_aberto, ano_letivo_id_sec 
                  FROM smc_ano_letivo 
                  WHERE ano_letivo_aberto = 'N' 
                  AND ano_letivo_id_sec = :sec_id 
                  ORDER BY ano_letivo_ano DESC";

$stmt = $SmecelNovo->prepare($query_Ano);
$stmt->bindParam(':sec_id', $row_UsuLogado['usu_sec'], PDO::PARAM_INT);
$stmt->execute();

$rows_Ano = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRows_Ano = count($rows_Ano);

$anoLetivo = $row_AnoLetivo['ano_letivo_ano'];
if (isset($_GET['ano'])) {

  if ($_GET['ano'] == "") {
    //echo "TURMA EM BRANCO";	
    header("Location: turmasAlunosVinculados.php?nada");
    exit;
  }

  $anoLetivo = $_GET['ano'];
  $anoLetivo = (int) $anoLetivo;
}


// Verifica se há código de funcionário na URL
$colname_Funcionario = "-1";
if (isset($_GET['codigo'])) {
  $colname_Funcionario = (int) $_GET['codigo'];
}

// Consulta para obter informações do professor (Plano de Aula)
$query_Ac = "
SELECT ac_id, ac_id_professor, ac_id_etapa, ac_id_componente, ac_id_escola, ac_ano_letivo, ac_data_inicial, ac_data_final, ac_id_turma,
ac_conteudo, ac_objetivo_especifico, ac_objeto_conhecimento, ac_metodologia, ac_recursos, ac_avaliacao, ac_criacao, ac_status, ac_correcao, ac_feedback, 
func_id, func_nome, turma_id, turma_nome,
CASE ac_status
  WHEN 0 THEN '<span class=\"ls-tag-warning\">NOVO</span>'
  WHEN 1 THEN '<span class=\"ls-tag-primary\">VERIFICADO</span>'
END AS ac_status,
CASE ac_correcao
  WHEN 0 THEN '<span class=\"ls-tag-success\">TUDO CERTO</span>'
  WHEN 1 THEN '<span class=\"ls-tag-warning\">AGUARDANDO CORREÇÃO</span>'
  WHEN 2 THEN '<span class=\"ls-tag-success\">CORRIGIDO</span>'
END AS ac_correcao 
FROM smc_ac
LEFT JOIN smc_func ON func_id = ac_id_professor
LEFT JOIN smc_turma ON turma_id = ac_id_turma
WHERE ac_id_professor = :funcionario 
  AND ac_id_escola = :escola_id 
  AND ac_ano_letivo = :ano_letivo
ORDER BY ac_data_inicial DESC";

$stmt = $SmecelNovo->prepare($query_Ac);
$stmt->bindParam(':funcionario', $colname_Funcionario, PDO::PARAM_INT);
$stmt->bindParam(':escola_id', $row_EscolaLogada['escola_id'], PDO::PARAM_INT);
$stmt->bindParam(':ano_letivo', $anoLetivo, PDO::PARAM_INT);
$stmt->execute();
$row_Ac = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_Ac = $stmt->rowCount();

// Consulta planejamentos novos
$query_NewPlan = "
SELECT p.smc_id_planejamento, 
        p.smc_planejamento_data_inicial, 
        p.smc_planejamento_data_final, 
        p.smc_ano_letivo, 
        p.smc_id_professor, 
        p.planejamento_status,
        p.smc_id_escola,  
        p.smc_id_turma,   
        e.escola_nome,
        t.turma_nome,
        CASE p.planejamento_status 
            WHEN 0 THEN '<span class=\"ls-tag-primary\">NOVO</span>' 
            WHEN 1 THEN '<span class=\"ls-tag-primary\">VISUALIZADO</span>' 
        END AS status_formatado
FROM smc_planejamento p
INNER JOIN smc_escola e ON p.smc_id_escola = e.escola_id
INNER JOIN smc_turma t ON p.smc_id_turma = t.turma_id
WHERE p.smc_id_professor = :professor_id 
  AND p.smc_ano_letivo = :ano_letivo 
ORDER BY p.smc_planejamento_data_inicial DESC";

$stmtNewPlan = $SmecelNovo->prepare($query_NewPlan);
$stmtNewPlan->bindParam(':professor_id', $colname_Funcionario, PDO::PARAM_INT);
$stmtNewPlan->bindParam(':ano_letivo', $anoLetivo, PDO::PARAM_INT);
$stmtNewPlan->execute();
$resultNewPlan = $stmtNewPlan->fetchAll(PDO::FETCH_ASSOC);
$totalRows_NewPlan = count($resultNewPlan);
?>

<!DOCTYPE html>
<html class="<?php echo $row_EscolaLogada['escola_tema']; ?>" lang="pt-br">

<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    gtag('js', new Date());

    gtag('config', 'UA-117872281-1');
  </script>

  <title>SMECEL - Sistema de Gestão Escolar</title>
  <meta charset="utf-8">
  <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
  <meta name="description" content="">
  <meta name="keywords" content="">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <link rel="stylesheet" type="text/css" href="css/locastyle.css">
  <link rel="stylesheet" type="text/css" href="css/preloader.css">
  <script src="js/locastyle.js"></script>
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>

<body>
  <?php include_once("menu-top.php"); ?>
  <?php include_once("menu-esc.php"); ?>


  <main class="ls-main ">
    <div class="container-fluid">

      <h1 class="ls-title-intro ls-ico-home">PLANEJAMENTOS</h1>
      <!-- CONTEÚDO -->

      <a href="ac-geral.php" class="ls-btn">VOLTAR</a>

      <div data-ls-module="dropdown" class="ls-dropdown ls-float-right1">
        <a href="#" class="ls-btn">ANO LETIVO: <?php echo $anoLetivo; ?></a>
        <ul class="ls-dropdown-nav">
          <li>
            <a href="ac-professor.php?codigo=<?php echo $_GET['codigo']; ?>&ano=<?php echo $row_AnoLetivo['ano_letivo_ano']; ?>"
              title="ANO LETIVO">
              ANO LETIVO <?php echo $row_AnoLetivo['ano_letivo_ano']; ?>
            </a>
          </li>

          <?php foreach ($rows_Ano as $row_Ano) { ?>
            <li>
              <a href="ac-professor.php?codigo=<?php echo $_GET['codigo']; ?>&ano=<?php echo $row_Ano['ano_letivo_ano']; ?>"
                title="ANO LETIVO">
                ANO LETIVO <?php echo $row_Ano['ano_letivo_ano']; ?>
              </a>
            </li>
          <?php } ?>
        </ul>
      </div>


      <hr>

      <h3>Professor(a): <?php echo $row_Ac['func_nome']; ?> </h3>
      <br>
      <div class="ls-alert-info">
        <strong>Atenção:</strong> A turma será exibida apenas quando o planejamento for feito por turma.
      </div>

      <hr>

      <div class="ls-tabs-btn">
        <ul class="ls-tabs-btn-nav">
          <?php if ($totalRows_NewPlan > 0) { ?>
            <li class="col-md-3 col-sm-6 col-xs-6 ls-active">
              <label class="ls-btn" data-ls-module="button" data-target="#tabNovos">
                Planejamentos (Nova versão) <input type="radio" name="btn" checked>
              </label>
            </li>
          <?php } ?>
          <li class="col-md-3 col-sm-6 col-xs-6 <?php echo ($totalRows_NewPlan == 0) ? 'ls-active' : ''; ?>">
            <label class="ls-btn" data-ls-module="button" data-target="#tabAntigos">
              Planejamentos <input type="radio" name="btn" <?php echo ($totalRows_NewPlan == 0) ? 'checked' : ''; ?>>
            </label>
          </li>
        </ul>

        <div class="ls-tabs-container">
          <?php if ($totalRows_NewPlan > 0) { ?>
            <div id="tabNovos" class="ls-tab-content ls-active">
              <h3>Planejamentos - Nova versão</h3>
              <table class="ls-table">
                <thead>
                  <tr>
                    <th class="ls-txt-center" width="130">INTERVALO</th>
                    <th class="ls-txt-center" width="350">ESCOLA</th>
                    <th class="ls-txt-center" width="200">TURMA</th>
                    <th class="ls-txt-center">STATUS</th>
                    <th class="ls-txt-center" width="100"></th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($resultNewPlan as $plan) { ?>
                    <tr>
                      <td class="ls-txt-center">
                        <?php echo date('d/m/Y', strtotime($plan['smc_planejamento_data_inicial'])); ?>
                        até
                        <?php echo date('d/m/Y', strtotime($plan['smc_planejamento_data_final'])); ?>
                      </td>
                      <td class="ls-txt-center">
                        <?php echo htmlspecialchars($plan['escola_nome'], ENT_QUOTES, 'UTF-8'); ?>
                      </td>
                      <td class="ls-txt-center">
                        <?php echo htmlspecialchars($plan['turma_nome'], ENT_QUOTES, 'UTF-8'); ?>
                      </td>
                      <td class="ls-txt-center">
                        <?php echo $plan['status_formatado']; ?>
                      </td>
                      <td class="ls-txt-center">
                        <a href="plan-feedback.php?plan=<?php echo $plan['smc_id_planejamento']; ?>"
                          class="ls-btn-primary">VISUALIZAR</a>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          <?php } ?>

          <div id="tabAntigos" class="ls-tab-content <?php echo ($totalRows_NewPlan == 0) ? 'ls-active' : ''; ?>">
            <h3>Planejamentos</h3>
            <?php if ($totalRows_Ac > 0) { // Show if recordset not empty ?>
              <table class="ls-table ls-sm-space">
                <thead>
                  <tr>
                    <th width="200">PROFESSOR</th>
                    <th class="ls-txt-center" width="200">COMPONENTE</th>
                    <th class="ls-txt-center" width="200">ETAPA</th>
                    <th class="ls-txt-center" width="200">TURMA</th>
                    <th class="ls-txt-center" width="100">ANO</th>
                    <th class="ls-txt-center" width="120">DATA INICIAL</th>
                    <th class="ls-txt-center" width="120">DATA FINAL</th>
                    <th class="ls-txt-center" width="120">DIAS</th>
                    <th class="ls-txt-center" width="150">STATUS</th>
                    <th class="ls-txt-center" width="150">CORREÇÃO</th>
                    <th width="150">&nbsp;</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $trocar = array("\"", "\'", "'");

                  do {
                    // Consulta PDO para ac_edit2
                    $query_ac_edit2 = "
                    SELECT ac_id, ac_id_professor, ac_id_escola, ac_id_componente, ac_id_etapa, ac_ano_letivo, ac_data_inicial, ac_data_final, ac_status, 
                    ac_correcao, ac_feedback, ac_conteudo, ac_objetivo_especifico, ac_objeto_conhecimento, ac_metodologia, ac_recursos, ac_avaliacao, 
                    ac_criacao, ac_tema, disciplina_id, disciplina_nome
                    FROM smc_ac
                    INNER JOIN smc_disciplina ON disciplina_id = ac_id_componente 
                    WHERE ac_id = :ac_id";
                    $stmt_ac_edit2 = $SmecelNovo->prepare($query_ac_edit2);
                    $stmt_ac_edit2->bindParam(':ac_id', $row_Ac['ac_id'], PDO::PARAM_INT);
                    $stmt_ac_edit2->execute();
                    $row_ac_edit2 = $stmt_ac_edit2->fetch(PDO::FETCH_ASSOC);

                    // Consulta PDO para matrizDisciplinasAC
                    $query_matriz_disc_ac = "
                    SELECT ac_componente_id, ac_componente_id_componente, ac_componente_id_planejamento, ac_id, disciplina_id, disciplina_nome 
                    FROM smc_ac_componente
                    INNER JOIN smc_ac ON ac_componente_id_planejamento = ac_id
                    INNER JOIN smc_disciplina ON disciplina_id = ac_componente_id_componente
                    WHERE ac_id = :ac_id";
                    $stmt_matrizDisc = $SmecelNovo->prepare($query_matriz_disc_ac);
                    $stmt_matrizDisc->bindParam(':ac_id', $row_Ac['ac_id'], PDO::PARAM_INT);
                    $stmt_matrizDisc->execute();
                    $rowDisciplinasAC = $stmt_matrizDisc->fetchAll(PDO::FETCH_ASSOC);

                    // Consulta PDO para etapa
                    $query_Etapa = "
                    SELECT etapa_id, etapa_nome 
                    FROM smc_etapa 
                    WHERE etapa_id = :etapa_id";
                    $stmt_Etapa = $SmecelNovo->prepare($query_Etapa);
                    $stmt_Etapa->bindParam(':etapa_id', $row_Ac['ac_id_etapa'], PDO::PARAM_INT);
                    $stmt_Etapa->execute();
                    $row_Etapa = $stmt_Etapa->fetch(PDO::FETCH_ASSOC);
                    $etapa_nome = $row_Etapa ? $row_Etapa['etapa_nome'] : "NAO SE APLICA";
                    ?>
                    <tr>
                      <td><?php echo $row_Ac['func_nome']; ?></td>
                      <td class="ls-txt-center">
                        <?php
                        if (!empty($row_ac_edit2['disciplina_nome'])) {
                          echo $row_ac_edit2['disciplina_nome'] . " ";
                        }
                        if ($rowDisciplinasAC) {
                          foreach ($rowDisciplinasAC as $disciplina) {
                            echo $disciplina['disciplina_nome'] . " | ";
                          }
                        }
                        ?>
                      </td>
                      <td class="ls-txt-center"><?php echo $etapa_nome; ?></td>
                      <td class="ls-txt-center"><?php echo $row_Ac['turma_nome']; ?></td>
                      <td class="ls-txt-center"><?php echo $row_Ac['ac_ano_letivo']; ?></td>
                      <td class="ls-txt-center"><?php echo inverteData($row_Ac['ac_data_inicial']); ?></td>
                      <td class="ls-txt-center"><?php echo inverteData($row_Ac['ac_data_final']); ?></td>
                      <td class="ls-txt-center">
                        <?php
                        $diferenca = strtotime($row_Ac['ac_data_final']) - strtotime($row_Ac['ac_data_inicial']);
                        echo $dias = floor($diferenca / (60 * 60 * 24)) + 1;
                        ?>
                      </td>
                      <td class="ls-txt-center"><?php echo $row_Ac['ac_status']; ?></td>
                      <td class="ls-txt-center"><?php echo $row_Ac['ac_correcao']; ?></td>
                      <td class="ls-txt-center">
                        <a href="ac-feedback.php?ac=<?php echo $row_Ac['ac_id']; ?>" class="ls-btn-primary">VISUALIZAR</a>
                      </td>
                    </tr>
                  <?php } while ($row_Ac = $stmt->fetch(PDO::FETCH_ASSOC)); ?>
                </tbody>
              </table>
            <?php } else { ?>
              <p>Nenhum planejamento cadastrado para este professor</p>
            <?php } // Show if recordset not empty ?>
          </div>
        </div>
      </div>
    </div>
  </main>

  <aside class="ls-notification">
    <nav class="ls-notification-list" id="ls-notification-curtain" style="left: 1716px;">
      <h3 class="ls-title-2">Notificações</h3>
      <ul>
        <?php include "notificacoes.php"; ?>
      </ul>
    </nav>

    <nav class="ls-notification-list" id="ls-help-curtain" style="left: 1756px;">
      <h3 class="ls-title-2">Feedback</h3>
      <ul>
        <li><a href="https://webmail.smecel.com.br" target="_blank">&gt; Acesse o webmail de sua escola</a></li>
      </ul>
    </nav>

    <nav class="ls-notification-list" id="ls-feedback-curtain" style="left: 1796px;">
      <h3 class="ls-title-2">Ajuda</h3>
      <ul>
        <li class="ls-txt-center hidden-xs">
          <a href="tutoriais_video.php" class="ls-btn-dark ls-btn-tour">Tutorial (Vídeos)</a>
        </li>
        <li><a href="#">&gt; Guia</a></li>
        <li><a href="#">&gt; Wiki</a></li>
      </ul>
    </nav>
  </aside>

  <!-- We recommended use jQuery 1.10 or up -->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="js/locastyle.js"></script>

</body>

</html>