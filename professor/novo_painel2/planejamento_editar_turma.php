<?php require_once('../../Connections/SmecelNovoPDO.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
<?php
if (!function_exists("GetSQLValueString")) {
  function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "")
  {
    if (PHP_VERSION < 6) {
      $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
    }

    $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

    switch ($theType) {
      case "text":
        $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
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
?>
<?php

$componente = "-1";
if (isset($_GET['componente'])) {
  $componente = anti_injection($_GET['componente']);
}

$turma = "-1";
if (isset($_GET['turma'])) {
  $turma = anti_injection($_GET['turma']);
}


$etapa = "-1";
if (isset($_GET['etapa'])) {
  $etapa = anti_injection($_GET['etapa']);
}

$colname_Componente = "-1";
if (isset($_GET['componente'])) {
  $colname_Componente = anti_injection($_GET['componente']);
}

$colname_ac_edit = "-1";
if (isset($_GET['ac'])) {
  $colname_ac_edit = $_GET['ac'];
}

$query_ac_edit = "
  SELECT * FROM smc_ac WHERE ac_id = :ac_id";
$stmt = $SmecelNovo->prepare($query_ac_edit);
$stmt->bindParam(':ac_id', $colname_ac_edit, PDO::PARAM_INT);
$stmt->execute();
$row_ac_edit = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_ac_edit = $stmt->rowCount();


$query_Componente = "SELECT * FROM smc_disciplina";
$stmt = $SmecelNovo->prepare($query_Componente);
$stmt->bindParam(':disciplina_id', $colname_Componente, PDO::PARAM_INT);
$stmt->execute();
$row_Componente = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_Componente = $stmt->rowCount();

$colname_Etapa = "-1";
if (isset($_GET['etapa'])) {
  $colname_Etapa = anti_injection($_GET['etapa']);
}

$query_Etapa = "SELECT * FROM smc_etapa WHERE etapa_id = :etapa_id";
$stmt = $SmecelNovo->prepare($query_Etapa);
$stmt->bindParam(':etapa_id', $etapa, PDO::PARAM_INT);
$stmt->execute();
$row_Etapa = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_Etapa = $stmt->rowCount();

$escola = "-1";
if (isset($_GET['escola'])) {
  $escola = anti_injection($_GET['escola']);
}


$query_ac = "
SELECT * FROM smc_ac
WHERE ac_id_professor = " . ID_PROFESSOR . " 
AND ac_id_componente = :componente 
AND ac_id_etapa = :etapa 
AND ac_id_escola = :escola 
AND ac_ano_letivo = " . ANO_LETIVO . "
ORDER BY ac_data_inicial DESC";
$stmt = $SmecelNovo->prepare($query_ac);
$stmt->bindParam(':componente', $colname_Componente, PDO::PARAM_INT);
$stmt->bindParam(':etapa', $etapa, PDO::PARAM_INT);
$stmt->bindParam(':escola', $escola, PDO::PARAM_INT);
$stmt->execute();
$row_ac = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_ac = $stmt->rowCount();


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form3")) {
  $disciplina = $_POST['disciplina'];
  $insertSQL = "INSERT INTO smc_ac_componente (ac_componente_id_planejamento, ac_componente_id_componente) VALUES (:ac_id, :disciplina)";
  $stmt = $SmecelNovo->prepare($insertSQL);
  $stmt->bindParam(':ac_id', $colname_ac_edit, PDO::PARAM_INT);
  $stmt->bindParam(':disciplina', $disciplina, PDO::PARAM_INT);
  $stmt->execute();

  $insertGoTo = "planejamento_editar_turma.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header("Location: $insertGoTo");
  exit();
}



$etapa_ano = $row_Etapa['etapa_ano_ef'];
//$etapa_ano = "4,5";

$consulta = " AND bncc_ef_ano IN ($etapa_ano) ";
//$consulta = " WHERE bncc_ef_ano IN ($etapa_ano) ";
//$consulta = "";

$disciplina = $row_Componente['disciplina_id'];

//mysql_query("SET NAMES 'utf8'");
//mysql_query('SET character_set_connection=utf8');
//mysql_query('SET character_set_client=utf8');
//mysql_query('SET character_set_results=utf8');
//header('Content-Type: text/html; charset=utf-8');


$query_bncc_ef = "
SELECT * FROM smc_bncc_ef
WHERE bncc_ef_comp_id = :disciplina $consulta";
$stmt = $SmecelNovo->prepare($query_bncc_ef);
$stmt->bindParam(':disciplina', $row_Componente['disciplina_id'], PDO::PARAM_INT);
$stmt->execute();
$row_bncc_ef = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_bncc_ef = $stmt->rowCount();


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

  $updateSQL = "UPDATE smc_ac 
      SET ac_data_inicial = :ac_data_inicial,
          ac_data_final = :ac_data_final,
          ac_conteudo = :ac_conteudo,
          ac_objetivo_especifico = :ac_objetivo_especifico,
          ac_objeto_conhecimento = :ac_objeto_conhecimento,
          ac_metodologia = :ac_metodologia,
          ac_recursos = :ac_recursos,
          ac_avaliacao = :ac_avaliacao,
          ac_da_conviver = :ac_da_conviver,
          ac_da_brincar = :ac_da_brincar,
          ac_da_participar = :ac_da_participar,
          ac_da_explorar = :ac_da_explorar,
          ac_da_expressar = :ac_da_expressar,
          ac_da_conhecerse = :ac_da_conhecerse,
          ac_ce_eo = :ac_ce_eo,
          ac_ce_ts = :ac_ce_ts,
          ac_ce_ef = :ac_ce_ef,
          ac_ce_cg = :ac_ce_cg,
          ac_ce_et = :ac_ce_et,
          ac_ce_di = :ac_ce_di,
          ac_correcao = :ac_correcao,
          ac_periodo = :ac_periodo,
          ac_tema = :ac_tema,
          ac_unid_tematica = :ac_unid_tematica
      WHERE ac_id = :ac_id";

  $stmt = $SmecelNovo->prepare($updateSQL);

  // Bind dos valores
  $stmt->bindParam(':ac_data_inicial', $_POST['ac_data_inicial']);
  $stmt->bindParam(':ac_data_final', $_POST['ac_data_final']);
  $stmt->bindParam(':ac_conteudo', $_POST['ac_conteudo']);
  $stmt->bindParam(':ac_objetivo_especifico', $_POST['ac_objetivo_especifico']);
  $stmt->bindParam(':ac_objeto_conhecimento', $_POST['ac_objeto_conhecimento']);
  $stmt->bindParam(':ac_metodologia', $_POST['ac_metodologia']);
  $stmt->bindParam(':ac_recursos', $_POST['ac_recursos']);
  $stmt->bindParam(':ac_avaliacao', $_POST['ac_avaliacao']);

  // Checkbox tratado como 'S' ou 'N'
  $stmt->bindValue(':ac_da_conviver', isset($_POST['ac_da_conviver']) ? 'S' : 'N');
  $stmt->bindValue(':ac_da_brincar', isset($_POST['ac_da_brincar']) ? 'S' : 'N');
  $stmt->bindValue(':ac_da_participar', isset($_POST['ac_da_participar']) ? 'S' : 'N');
  $stmt->bindValue(':ac_da_explorar', isset($_POST['ac_da_explorar']) ? 'S' : 'N');
  $stmt->bindValue(':ac_da_expressar', isset($_POST['ac_da_expressar']) ? 'S' : 'N');
  $stmt->bindValue(':ac_da_conhecerse', isset($_POST['ac_da_conhecerse']) ? 'S' : 'N');

  $stmt->bindParam(':ac_ce_eo', $_POST['ac_ce_eo']);
  $stmt->bindParam(':ac_ce_ts', $_POST['ac_ce_ts']);
  $stmt->bindParam(':ac_ce_ef', $_POST['ac_ce_ef']);
  $stmt->bindParam(':ac_ce_cg', $_POST['ac_ce_cg']);
  $stmt->bindParam(':ac_ce_et', $_POST['ac_ce_et']);
  $stmt->bindParam(':ac_ce_di', $_POST['ac_ce_di']);
  $stmt->bindParam(':ac_correcao', $_POST['ac_correcao']);
  $stmt->bindParam(':ac_periodo', $_POST['ac_periodo'], PDO::PARAM_INT);
  $stmt->bindParam(':ac_tema', $_POST['ac_tema']);
  $stmt->bindParam(':ac_unid_tematica', $_POST['ac_unidade_tematica']);
  $stmt->bindParam(':ac_id', $_POST['ac_id'], PDO::PARAM_INT);

  $stmt->execute();

  // Redirecionamento
  $updateGoTo = "planejamento_ver.php?cadastrado";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header("Location: $updateGoTo");
  exit();
}

//disciplinas 
$query_matriz_disc = "SELECT disciplina_id, disciplina_nome FROM smc_disciplina";
$stmt = $SmecelNovo->prepare($query_matriz_disc);
$stmt->execute();
$rowDisciplinas = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRowsDisciplinas = $stmt->rowCount();

//disciplinas + ac
$query_matriz_disc_ac = "
SELECT * FROM smc_ac_componente
INNER JOIN smc_ac ON ac_componente_id_planejamento = ac_id
INNER JOIN smc_disciplina ON disciplina_id = ac_componente_id_componente
WHERE ac_id = :ac_id";
$stmt = $SmecelNovo->prepare($query_matriz_disc_ac);
$stmt->bindParam(':ac_id', $colname_ac_edit, PDO::PARAM_INT);
$stmt->execute();
$matrizDisciplinasAC = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRowsDisciplinasAC = $stmt->rowCount($matrizDisciplinasAC);

$query_matriz_disc_ac2 = "
SELECT ac_componente_id, ac_componente_id_componente, ac_componente_id_planejamento, ac_id, disciplina_id, disciplina_nome, disciplina_id_campos_exp 
FROM smc_ac_componente
INNER JOIN smc_ac ON ac_componente_id_planejamento = ac_id
INNER JOIN smc_disciplina ON disciplina_id = ac_componente_id_componente
WHERE ac_id = :ac_id";

$stmt = $SmecelNovo->prepare($query_matriz_disc_ac2);
$stmt->bindParam(':ac_id', $colname_ac_edit, PDO::PARAM_INT);
$stmt->execute();

$rowDisciplinasAC2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRowsDisciplinasAC2 = $stmt->rowCount();

$query_periodo = "SELECT * FROM smc_unidades WHERE per_unid_id_ano = '$row_AnoLetivo[ano_letivo_id]' AND per_unid_id_sec = '$row_Secretaria[sec_id]' ORDER BY per_unid_periodo ASC";
$periodos = $SmecelNovo->query($query_periodo)->fetchAll(PDO::FETCH_ASSOC);
$rowPeriodos = $SmecelNovo->query($query_periodo)->rowCount($periodos);

$query_Turmas = "SELECT * FROM smc_turma WHERE turma_id_escola = :escola AND turma_id = :turma";
$stmt = $SmecelNovo->prepare($query_Turmas);
$stmt->bindParam(':escola', $escola, PDO::PARAM_INT);
$stmt->bindParam(':turma', $row_ac_edit['ac_id_turma'], PDO::PARAM_INT);
$stmt->execute();
$rowTurmas = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRowsTurmas = $stmt->rowCount();

$query_ac_label = "SELECT * FROM smc_ac_label WHERE ac_id_ac = :ac_id";
$stmt = $SmecelNovo->prepare($query_ac_label);
$stmt->bindParam(':ac_id', $row_ac_edit['ac_id'], PDO::PARAM_INT);
$stmt->execute();

$ac_labels = $stmt->fetchAll(PDO::FETCH_ASSOC);
$TotalrowAcLabel = $stmt->rowCount();

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
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">

</head>

<body>
  <?php include_once "inc/navebar.php"; ?>
  <?php include_once "inc/sidebar.php"; ?>
  <main class="ls-main">
    <div class="container-fluid">
      <h1 class="ls-title-intro ls-ico-home">Ano letivo <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h1>
      <p><a href="planejamento_ver.php" class="ls-btn ls-ico-chevron-left">Voltar</a></p>
      <hr>


      <?php if ($row_ac_edit['ac_correcao'] == "1") { ?>
        <div class="ls-box ls-box-gray">
          <h5 class="ls-title-3">Retorno da Coordenação Pedagógica</h5>
          <p><?php echo nl2br($row_ac_edit['ac_feedback']); ?></p>
        </div>
      <?php } ?>

      <div class="ls-modal" id="modalDetalhes">
        <div class="ls-modal-box">
          <div class="ls-modal-header">
            <button data-dismiss="modal" class="close">&times;</button>
          </div>
          <div class="ls-modal-body" id="modalBody">
            <!-- Aqui será inserido o conteúdo dinâmico do modal -->
          </div>
          <div class="ls-modal-footer">
            <button class="ls-btn" data-dismiss="modal">Fechar</button>
          </div>
        </div>
      </div>


      <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form ls-form-horizontal row">

        <label class="ls-label col-md-12 ls-flex">
          <b class="ls-label-text ls-text-left">Componente curricular/eixo/campo de experiência:</b>

          <?php if ($totalRowsDisciplinasAC > 0): ?>
            <?php foreach ($matrizDisciplinasAC as $rowDisciplinasAC): ?>
              <?php
              $tipo = ($rowDisciplinasAC['disciplina_id_campos_exp'] != '') ? "EI" : "EF";
              $id = ($tipo === "EI")
                ? '#EI' . $rowDisciplinasAC['disciplina_id_campos_exp']
                : '#EF' . $rowDisciplinasAC['disciplina_id'];
              ?>
              <a href="#" data-ls-module="modal" tipo-etapa="<?= $tipo ?>" etapa="<?= $etapa ?>" data-target="<?= $id ?>"
                data-id="<?= $rowDisciplinasAC['disciplina_id_campos_exp'] ?>" class="ls-tag-success open-modal">
                <?= htmlspecialchars($rowDisciplinasAC['disciplina_nome']); ?>&nbsp;
                <a style="cursor:pointer;" onclick="deletarDisciplina(<?= $rowDisciplinasAC['ac_componente_id'] ?>)"
                  class="ls-ico-remove ls-text-md"></a>
              </a>
            <?php endforeach; ?>
          <?php endif; ?>

          <br>
          <a data-ls-module="modal" data-target="#modalComponente"
            class="ls-tag-info ls-ico-plus ls-sm-margin-top">Adicionar componente</a>
        </label>


        <label class="ls-label col-xs-12">
          <b class="ls-label-text">Período</b>
          <p class="ls-label-info">Informe o período</p>
          <div class="ls-custom-select">
            <select class="ls-select" name="ac_periodo">
              <option value="">SELECIONE O PERÍODO</option>
              <?php foreach ($periodos as $rowPeriodos): ?>
                <option value="<?= htmlspecialchars($rowPeriodos['per_unid_id']) ?>"
                  <?= ($row_ac_edit['ac_periodo'] == $rowPeriodos['per_unid_id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($rowPeriodos['per_unid_periodo']) ?>°
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </label>

        <label class="ls-label col-md-6">
          <b class="ls-label-text">DE</b>
          <p class="ls-label-info">Informe a data inicial</p>
          <input type="date" name="ac_data_inicial"
            value="<?php echo htmlentities($row_ac_edit['ac_data_inicial'], ENT_COMPAT, 'utf-8'); ?>" size="32"
            required>
        </label>
        <label class="ls-label col-md-6">
          <b class="ls-label-text">ATÉ</b>
          <p class="ls-label-info">Informe a data final</p>
          <input type="date" name="ac_data_final"
            value="<?php echo htmlentities($row_ac_edit['ac_data_final'], ENT_COMPAT, 'utf-8'); ?>" size="32" required>
        </label>


        <?php if ($row_Etapa['etapa_id_filtro'] == "1") { ?>
          <hr>
          <fieldset>



            <label class="ls-label col-md-12">
              <b class="ls-label-text">Turma:</b>
              <a href="#" class="ls-tag-info"><?php echo $rowTurmas['turma_nome']; ?></a>
            </label>

            <label class="ls-label col-md-12">
              <b class="ls-label-text">Etapa:</b>
              <a href="#" class="ls-tag-info"><?php echo $row_Etapa['etapa_nome']; ?></a>
            </label>

            <label class="ls-label col-md-6">
              <b class="ls-label-text">Tema do planejamento</b>
              <input type="text" name="ac_tema" placeholder="Ex: Processo diagnóstico"
                value="<?php echo htmlentities($row_ac_edit['ac_tema'], ENT_COMPAT, 'utf-8'); ?>">
            </label>


            <!-- Exemplo com Checkbox -->
            <div class="ls-label col-md-12">
              <p>DIREITOS DE APRENDIZAGEM</p>
              <label class="ls-label-text">
                <input name="ac_da_conviver" type="checkbox" <?php if (!(strcmp(htmlentities($row_ac_edit['ac_da_conviver'], ENT_COMPAT, 'utf-8'), "S"))) {
                  echo "checked=\"checked\"";
                } ?> />
                CONVIVER </label>
              <label class="ls-label-text">
                <input name="ac_da_brincar" type="checkbox" <?php if (!(strcmp(htmlentities($row_ac_edit['ac_da_brincar'], ENT_COMPAT, 'utf-8'), "S"))) {
                  echo "checked=\"checked\"";
                } ?> />
                BRINCAR </label>
              <label class="ls-label-text">
                <input name="ac_da_participar" type="checkbox" <?php if (!(strcmp(htmlentities($row_ac_edit['ac_da_participar'], ENT_COMPAT, 'utf-8'), "S"))) {
                  echo "checked=\"checked\"";
                } ?> />
                PARTICIPAR </label>
              <label class="ls-label-text">
                <input name="ac_da_explorar" type="checkbox" <?php if (!(strcmp(htmlentities($row_ac_edit['ac_da_explorar'], ENT_COMPAT, 'utf-8'), "S"))) {
                  echo "checked=\"checked\"";
                } ?> />
                EXPLORAR </label>
              <label class="ls-label-text">
                <input name="ac_da_expressar" type="checkbox" <?php if (!(strcmp(htmlentities($row_ac_edit['ac_da_expressar'], ENT_COMPAT, 'utf-8'), "S"))) {
                  echo "checked=\"checked\"";
                } ?> />
                EXPRESSAR </label>
              <label class="ls-label-text">
                <input name="ac_da_conhecerse" type="checkbox" <?php if (!(strcmp(htmlentities($row_ac_edit['ac_da_conhecerse'], ENT_COMPAT, 'utf-8'), "S"))) {
                  echo "checked=\"checked\"";
                } ?> />
                CONHECER-SE </label>
            </div>
          </fieldset>
          <?php if ($totalRowsDisciplinasAC > 0) {
            mysql_data_seek($matrizDisciplinasAC2, 0);
            while ($rowDisciplinasAC2 = mysql_fetch_assoc($matrizDisciplinasAC2)) {
              switch ($rowDisciplinasAC2['disciplina_id_campos_exp']) {
                case '1':
                  $name = "ac_ce_ts";
                  break;
                case '2':
                  $name = "ac_ce_eo";
                  break;
                case '3':
                  $name = "ac_ce_cg";
                  break;
                case '4':
                  $name = "ac_ce_ef";
                  break;
                case '5':
                  $name = "ac_ce_et";
                  break;
                default:
                  $name = "";
                  break;
              }
              ?>
              <label class="ls-label col-md-12 <?php if ($rowDisciplinasAC2['disciplina_id_campos_exp'] != "") {
                echo "";
              } else {
                echo " ls-display-none";
              } ?>">
                <b class="ls-label-text"><?php echo $rowDisciplinasAC2['disciplina_nome']; ?> (Parte diversificada) </b>
                <textarea name="<?php echo $name ?>" id="summernote"
                  rows="4"><?php echo htmlentities($row_ac_edit[$name], ENT_COMPAT, 'utf-8'); ?></textarea>
              </label>
              <?php

            }
          } ?>



          <?php if ($TotalrowAcLabel == 0) { ?>


            <?php if ($row_ac_edit['ac_unid_tematica'] != "") { ?>
              <label class="ls-label col-md-12">
                <b class="ls-label-text">Unidade temática</b>
                <p class="ls-label-info"></p>
                <textarea name="ac_unidade_tematica" id=""
                  class="materialize-textarea"><?php echo htmlentities($row_ac_edit['ac_unid_tematica'], ENT_COMPAT, 'utf-8'); ?></textarea>
              </label>
            <?php } ?>

            <?php if ($row_ac_edit['ac_objetivo_especifico'] != "") { ?>
              <label class="ls-label col-md-12">
                <b class="ls-label-text">Objetivos de Aprendizagem e desenvolvimento</b>
                <p class="ls-label-info"></p>
                <textarea name="ac_objetivo_especifico" id=""
                  class="materialize-textarea"><?php echo htmlentities($row_ac_edit['ac_objetivo_especifico'], ENT_COMPAT, 'utf-8'); ?></textarea>
              </label>
            <?php } ?>

            <?php if ($row_ac_edit['ac_objeto_conhecimento'] != "") { ?>
              <label class="ls-label col-md-12">
                <b class="ls-label-text">Objetos de conhecimento/saberes e conhecimento/conteúdo</b>
                <p class="ls-label-info"></p>
                <textarea name="ac_objeto_conhecimento" id=""
                  class="materialize-textarea"><?php echo htmlentities($row_ac_edit['ac_objeto_conhecimento'], ENT_COMPAT, 'utf-8'); ?></textarea>
              </label>
            <?php } ?>

            <?php if ($row_ac_edit['ac_recursos'] != "") { ?>
              <label class="ls-label col-md-12">
                <b class="ls-label-text">Habilidades</b>
                <p class="ls-label-info"></p>
                <textarea name="ac_recursos" id=""
                  class="materialize-textarea"><?php echo htmlentities($row_ac_edit['ac_recursos'], ENT_COMPAT, 'utf-8'); ?></textarea>
              </label>
            <?php } ?>

            <?php if ($row_ac_edit['ac_metodologia'] != "") { ?>
              <label class="ls-label col-md-12">
                <b class="ls-label-text">Metodologia</b>
                <p class="ls-label-info"></p>
                <textarea name="ac_metodologia" id=""
                  class="materialize-textarea"><?php echo htmlentities($row_ac_edit['ac_metodologia'], ENT_COMPAT, 'utf-8'); ?></textarea>
              </label>
            <?php } ?>

            <?php if ($row_ac_edit['ac_avaliacao'] != "") { ?>
              <label class="ls-label col-md-12">
                <b class="ls-label-text">Avaliação</b>
                <p class="ls-label-info"></p>
                <textarea name="ac_avaliacao" id=""
                  class="materialize-textarea"><?php echo htmlentities($row_ac_edit['ac_avaliacao'], ENT_COMPAT, 'utf-8'); ?></textarea>
              </label>
            <?php } ?>

            <?php if ($row_ac_edit['ac_conteudo'] != "") { ?>
              <label class="ls-label col-md-12">
                <b class="ls-label-text">Observação/Recursos</b>
                <p class="ls-label-info"></p>
                <textarea name="ac_conteudo" id=""
                  class="materialize-textarea"><?php echo htmlentities($row_ac_edit['ac_conteudo'], ENT_COMPAT, 'utf-8'); ?></textarea>
              </label>
            <?php } ?>
          <?php } ?>

        <?php } ?>
        <?php if (($row_Etapa['etapa_id_filtro'] == "3") || ($row_Etapa['etapa_id_filtro'] == "7")) { ?>
          <hr>

          <label class="ls-label col-md-12">
            <b class="ls-label-text">Turma:</b>
            <a href="#" class="ls-tag-info"><?php echo $rowTurmas['turma_nome']; ?></a>
          </label>

          <label class="ls-label col-md-12">
            <b class="ls-label-text">Etapa:</b>
            <a href="#" class="ls-tag-info"><?php echo $row_Etapa['etapa_nome']; ?></a>
          </label>


          <label class="ls-label col-md-6">
            <b class="ls-label-text">Tema do planejamento</b>
            <input type="text" name="ac_tema" placeholder="Ex: Processo diagnóstico"
              value="<?php echo htmlentities($row_ac_edit['ac_tema'], ENT_COMPAT, 'utf-8'); ?>">
          </label>

          <?php if ($TotalrowAcLabel == 0) { ?>


            <?php if ($row_ac_edit['ac_unid_tematica'] != "") { ?>
              <label class="ls-label col-md-12">
                <b class="ls-label-text">Unidade temática</b>
                <p class="ls-label-info"></p>
                <textarea name="ac_unidade_tematica" id=""
                  class="materialize-textarea"><?php echo htmlentities($row_ac_edit['ac_unid_tematica'], ENT_COMPAT, 'utf-8'); ?></textarea>
              </label>
            <?php } ?>

            <?php if ($row_ac_edit['ac_objetivo_especifico'] != "") { ?>
              <label class="ls-label col-md-12">
                <b class="ls-label-text">Objetivos de Aprendizagem e desenvolvimento</b>
                <p class="ls-label-info"></p>
                <textarea name="ac_objetivo_especifico" id=""
                  class="materialize-textarea"><?php echo htmlentities($row_ac_edit['ac_objetivo_especifico'], ENT_COMPAT, 'utf-8'); ?></textarea>
              </label>
            <?php } ?>

            <?php if ($row_ac_edit['ac_objeto_conhecimento'] != "") { ?>
              <label class="ls-label col-md-12">
                <b class="ls-label-text">Objetos de conhecimento/saberes e conhecimento/conteúdo</b>
                <p class="ls-label-info"></p>
                <textarea name="ac_objeto_conhecimento" id=""
                  class="materialize-textarea"><?php echo htmlentities($row_ac_edit['ac_objeto_conhecimento'], ENT_COMPAT, 'utf-8'); ?></textarea>
              </label>
            <?php } ?>

            <?php if ($row_ac_edit['ac_recursos'] != "") { ?>
              <label class="ls-label col-md-12">
                <b class="ls-label-text">Habilidades</b>
                <p class="ls-label-info"></p>
                <textarea name="ac_recursos" id=""
                  class="materialize-textarea"><?php echo htmlentities($row_ac_edit['ac_recursos'], ENT_COMPAT, 'utf-8'); ?></textarea>
              </label>
            <?php } ?>

            <?php if ($row_ac_edit['ac_metodologia'] != "") { ?>
              <label class="ls-label col-md-12">
                <b class="ls-label-text">Metodologia</b>
                <p class="ls-label-info"></p>
                <textarea name="ac_metodologia" id=""
                  class="materialize-textarea"><?php echo htmlentities($row_ac_edit['ac_metodologia'], ENT_COMPAT, 'utf-8'); ?></textarea>
              </label>
            <?php } ?>

            <?php if ($row_ac_edit['ac_avaliacao'] != "") { ?>
              <label class="ls-label col-md-12">
                <b class="ls-label-text">Avaliação</b>
                <p class="ls-label-info"></p>
                <textarea name="ac_avaliacao" id=""
                  class="materialize-textarea"><?php echo htmlentities($row_ac_edit['ac_avaliacao'], ENT_COMPAT, 'utf-8'); ?></textarea>
              </label>
            <?php } ?>

            <?php if ($row_ac_edit['ac_conteudo'] != "") { ?>
              <label class="ls-label col-md-12">
                <b class="ls-label-text">Observação/Recursos</b>
                <p class="ls-label-info"></p>
                <textarea name="ac_conteudo" id=""
                  class="materialize-textarea"><?php echo htmlentities($row_ac_edit['ac_conteudo'], ENT_COMPAT, 'utf-8'); ?></textarea>
              </label>
            <?php } ?>
          <?php } ?>
        <?php } ?>


        <?php if ($row_ac_edit['ac_correcao'] == "1") { ?>


          <label class="ls-label col-md-12">
            <div class="ls-box ls-box-gray"><?php echo nl2br($row_ac_edit['ac_feedback']); ?></div>
          </label>



          <label class="ls-label col-md-12">
            <b class="ls-label-text">Realizou a correção solicitada?</b>

            <div class="ls-custom-select">
              <select class="ls-custom" name="ac_correcao">
                <option value="1" <?php if (!(strcmp(1, htmlentities($row_ac_edit['ac_correcao'], ENT_COMPAT, 'utf-8')))) {
                  echo "SELECTED";
                } ?>>Não</option>
                <option value="2" <?php if (!(strcmp(2, htmlentities($row_ac_edit['ac_correcao'], ENT_COMPAT, 'utf-8')))) {
                  echo "SELECTED";
                } ?>>Sim</option>
              </select>
            </div>
          </label>


        <?php } ?>



        <div class="ls-actions-btn">
          <input type="submit" class="ls-btn-primary ls-float-right" value="ATUALIZAR PLANEJAMENTO">
          <a href="novo_bloco_ac_turma.php?ac=<?= $colname_ac_edit ?>&escola=<?= $escola ?>&etapa=<?= $etapa ?>&componente=<?= $componente ?>"
            class="ls-btn-primary ls-ico-plus">ADICIONAR BLOCO</a>

          <input type="hidden" name="MM_update" value="form1" />
          <input type="hidden" name="ac_id" value="<?php echo $row_ac_edit['ac_id']; ?>" />


        </div>
      </form>

      <?php if ($TotalrowAcLabel > 0): ?>
        <?php foreach ($ac_labels as $rowAcLabel): ?>
          <?php
          $titulos = [
            '1' => "Unidade temática",
            '2' => "Objetivos de Aprendizagem e desenvolvimento",
            '3' => "Objetos de conhecimento/saberes e conhecimento/conteúdo",
            '4' => "Habilidades",
            '5' => "Metodologia",
            '6' => "Avaliação",
            '7' => "Observação",
            '8' => "Recursos"
          ];
          $titulo = isset($titulos[$rowAcLabel['ac_id_tipo']]) ? $titulos[$rowAcLabel['ac_id_tipo']] : "";

          ?>

          <div class="ls-box">
            <span class="ls-btn-danger ls-ico-remove ls-float-right" style="cursor:pointer; margin-left: 10px;"
              id="excluirBloco<?= $rowAcLabel['ac_label_id']; ?>" onclick="enviarId(<?= $rowAcLabel['ac_label_id']; ?>)">
            </span>
            <a href="editar_bloco_turma.php?ac=<?= $colname_ac_edit ?>&escola=<?= $escola ?>&etapa=<?= $etapa ?>&componente=<?= $componente ?>&b=<?= $rowAcLabel['ac_id_tipo'] ?>"
              class="ls-btn ls-ico-pencil ls-float-right" style="cursor:pointer;">
            </a>
            <h5 class="ls-title-5"><?= htmlspecialchars($titulo); ?></h5>
            <p><?= $rowAcLabel['ac_conteudo']; ?></p>

          </div>
          <br>
        <?php endforeach; ?>
      <?php endif; ?>

      <hr>
      <p>&nbsp;</p>
    </div>
    <?php //include_once "inc/footer.php"; ?>
  </main>
  <?php include_once "inc/notificacoes.php"; ?>


  <div class="ls-modal" id="modalHabilidades">
    <div class="ls-modal-box ls-modal-large">
      <div class="ls-modal-header">
        <button data-dismiss="modal">&times;</button>
        <h4 class="ls-modal-title">HABILIDADES</h4>
      </div>
      <div class="ls-modal-body" id="myModalBody">

        <table class="ls-table">
          <?php do { ?>
            <tr>

              <td>
                <strong class="">Habilidades</strong>
                <?php echo utf8_decode($row_bncc_ef['bncc_ef_habilidades']); ?><br><br>
                <strong class="">Componente</strong> <?php echo utf8_decode($row_bncc_ef['bncc_ef_componente']); ?> |
                <strong class="">Ano/Faixa</strong> <?php echo utf8_decode($row_bncc_ef['bncc_ef_ano']); ?>º
                ano(s)<br><br>
                <?php if ($row_bncc_ef['bncc_ef_campos_atuacao'] <> "") { ?><strong class="">Campo de atuação</strong>
                  <?php echo utf8_decode($row_bncc_ef['bncc_ef_campos_atuacao']); ?><br><br><?php } ?>
                <?php if ($row_bncc_ef['bncc_ef_eixo'] <> "") { ?><strong class="">Eixo</strong>
                  <?php echo utf8_decode($row_bncc_ef['bncc_ef_eixo']); ?><br><br><?php } ?>
                <?php if ($row_bncc_ef['bncc_ef_un_tematicas'] <> "") { ?><strong class="">Unidades Temáticas</strong>
                  <?php echo utf8_decode($row_bncc_ef['bncc_ef_un_tematicas']); ?><br><br><?php } ?>
                <?php if ($row_bncc_ef['bncc_ef_prat_ling'] <> "") { ?><strong class="">Práticas de Linguagem</strong>
                  <?php echo utf8_decode($row_bncc_ef['bncc_ef_prat_ling']); ?><br><br><?php } ?>
                <strong class="">Objetos de conhecimento</strong>
                <?php echo utf8_decode($row_bncc_ef['bncc_ef_obj_conhec']); ?><br><br>
                <strong class="">Comentários</strong>
                <?php echo utf8_decode($row_bncc_ef['bncc_ef_comentarios']); ?><br><br>
                <strong class="">Possibilidades para o Currículo</strong>
                <?php echo utf8_decode($row_bncc_ef['bncc_ef_poss_curr']); ?><br>

              </td>


            </tr>
          <?php } while ($row_bncc_ef = mysql_fetch_assoc($bncc_ef)); ?>
        </table>

      </div>
      <div class="ls-modal-footer">
        <button class="ls-btn-primary" data-dismiss="modal">FECHAR</button>
      </div>
    </div>
  </div><!-- /.modal -->


  <div class="ls-modal" id="modalComponente">
    <div class="ls-modal-small">
      <div class="ls-modal-header">
        <button data-dismiss="modal">&times;</button>
        <h4 class="ls-modal-title">Adicionar componente</h4>
      </div>

      <div class="ls-modal-body" id="myModalBody">
        <form method="post" id="form3" name="form3" action="<?php echo $editFormAction; ?>"
          onsubmit="return validateForm();">
          <div class="ls-custom-select">
            <select class="ls-select" id="disciplinas" name="disciplina">
              <option>SELECIONE...</option>
              <?php foreach ($rowDisciplinas as $disciplinas): ?>
                <option value="<?= htmlspecialchars($disciplinas['disciplina_id']) ?>">
                  <?= htmlspecialchars($disciplinas['disciplina_nome']) ?>
                </option>
              <?php endforeach; ?>
            </select>

          </div>
          <input type="submit" value="Adicionar" class="ls-btn-primary ls-sm-margin-top" title="Adicionar">
          <input type="hidden" name="MM_insert" value="form3" />
        </form>
      </div>
      <div class="ls-modal-footer">

      </div>

    </div>
  </div><!-- /.modal disciplinas -->







  <div class="ls-modal" id="modalLargeN">
    <div class="ls-modal-large">
      <div class="ls-modal-header">
        <button data-dismiss="modal">&times;</button>
        <h4 class="ls-modal-title">Parte Diversificada</h4>
      </div>
      <div class="ls-modal-body">
        <p>

          Não há descrição dos Objetivos de Aprendizagem e Desenvolvimento definidas pela BNCC na parte diversificada.

        </p>
      </div>
      <div class="ls-modal-footer">
        <button class="ls-btn-primary" data-dismiss="modal">FECHAR</button>

      </div>
    </div>
  </div>




  <!-- We recommended use jQuery 1.10 or up -->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="//assets.locaweb.com.br/locastyle/edge/javascripts/locastyle.js"></script>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="js/sweetalert2.min.js"></script>
  <!--<script src="https://cdn.tiny.cloud/1/iq1gy8qt7s1b9bj92c2fc7whie95augu8r8kz97dgoufkli8/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>-->
  <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
  <script src="langs/pt_BR.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script>
    $(document).ready(function () {
      $('textarea').summernote({
        placeholder: 'Digite aqui...',
        tabsize: 2,
        height: 120,
        toolbar: [
          ['style', ['style']],
          ['font', ['bold', 'underline', 'clear']],
          ['color', ['color']],
          ['para', ['ul', 'ol', 'paragraph']],
          ['table', ['table']],
          ['insert', []],
          ['view', []]
        ]
      });
    });

  </script>
  <script>
    function enviarId(id) {
      // Enviar o ID via AJAX
      $.ajax({
        type: "POST",
        url: "excluirBloco.php", // Substitua "sua_pagina.php" pelo caminho da sua página que vai lidar com a requisição AJAX
        data: { id: id }, // Envia o ID como parâmetro
        success: function (response) {
          // Aqui você pode fazer algo com a resposta da requisição
          location.reload();
        }
      });
    }
  </script>

  <script>
    $(document).ready(function () {
      // Quando um link para abrir o modal é clicado
      $('.open-modal').click(function (e) {
        // Impedir o comportamento padrão do link
        e.preventDefault();
        // Obter o ID do item a partir do atributo data-id
        var itemId = $(this).data('id');
        var etapa = $(this).attr('etapa');
        var tipoEtapa = $(this).attr('tipo-etapa');
        // Aqui você pode usar AJAX para carregar o conteúdo do modal com base no ID do item
        // Por exemplo, você pode ter um arquivo PHP que retorna os detalhes do item com base no ID
        // e então insira esses detalhes no corpo do modal
        $.ajax({
          url: 'detalhes_modal.php?id=' + itemId + "&tipoetapa=" + tipoEtapa + "&etapa=" + etapa,
          type: 'GET',
          success: function (response) {
            $('#modalBody').html(response);
            locastyle.modal.open("#modalDetalhes");

          }
        });
      });
    });
  </script>


  <script>
    function deletarDisciplina(id) {
      jQuery.ajax({
        type: "POST",
        url: "planejamento_deletar_componente.php",
        data: { componente: id },
        cache: true,
        success: function (data) {
          location.reload();
        }
      });
    }

    function validateForm() {
      var selectedOption = document.getElementById("disciplinas").value;
      if (selectedOption == "SELECIONE...") {
        alert("Por favor, selecione uma disciplina.");
        return false; // Impede o envio do formulário
      }
      return true; // Permite o envio do formulário
    }
  </script>
  <script>
    $('#disciplinas').select2({
      width: '100%' // Definindo a largura como 100%
    });


  </script>

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