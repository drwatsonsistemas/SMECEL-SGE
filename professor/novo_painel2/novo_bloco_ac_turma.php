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

$etapa = "-1";
if (isset($_GET['etapa'])) {
  $etapa = anti_injection($_GET['etapa']);
}

$componente = "-1";
if (isset($_GET['componente'])) {
  $componente = anti_injection($_GET['componente']);
}

$escola = "-1";
if (isset($_GET['escola'])) {
  $escola = anti_injection($_GET['escola']);
}

$colname_ac = "-1";
if (isset($_GET['ac'])) {
  $colname_ac = anti_injection($_GET['ac']);
}
$query_ac = "
    SELECT ac_id, ac_id_professor, ac_id_escola, ac_id_componente, ac_id_etapa, ac_ano_letivo, ac_data_inicial, ac_data_final, 
           ac_status, ac_correcao, ac_feedback, ac_conteudo, ac_objetivo_especifico, ac_objeto_conhecimento, ac_metodologia, 
           ac_recursos, ac_avaliacao, ac_criacao, ac_da_conviver, ac_da_brincar, ac_da_participar, ac_da_explorar, 
           ac_da_expressar, ac_da_conhecerse, ac_ce_eo, ac_ce_ts, ac_ce_ef, ac_ce_cg, ac_ce_et, ac_ce_di, ac_periodo, 
           ac_tema, ac_unid_tematica 
    FROM smc_ac 
    WHERE ac_id_professor = :professor_id 
      AND ac_id = :ac_id 
      AND ac_id_escola = :escola";

$stmt = $SmecelNovo->prepare($query_ac);
$stmt->bindParam(':professor_id', $row_ProfLogado['func_id'], PDO::PARAM_INT);
$stmt->bindParam(':ac_id', $colname_ac, PDO::PARAM_INT);
$stmt->bindParam(':escola', $escola, PDO::PARAM_INT);
$stmt->execute();

$row_ac = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_ac = $stmt->rowCount();


if ($totalRows_ac == "") {
  header("Location: index.php?err");
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

  $insertSQL = "INSERT INTO smc_ac_label (ac_id_ac, ac_id_tipo, ac_conteudo) 
                VALUES (:ac_id_ac, :ac_id_tipo, :ac_conteudo)";

  $stmt = $SmecelNovo->prepare($insertSQL);
  $stmt->bindParam(':ac_id_ac', $colname_ac, PDO::PARAM_INT);
  $stmt->bindParam(':ac_id_tipo', $_POST['ac_tipo'], PDO::PARAM_INT);
  $stmt->bindParam(':ac_conteudo', $_POST['ac_conteudo'], PDO::PARAM_STR);
  $stmt->execute();

  $insertGoTo = "planejamento_editar_turma.php";
  if (isset($_SERVER['QUERY_STRING'])) {
      $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
      $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  
  header("Location: $insertGoTo");
  exit();
}

$query_Etapa = "SELECT etapa_id, etapa_id_filtro, etapa_nome, etapa_idade, etapa_limite_turma, etapa_nome_abrev, etapa_ano_ef 
                FROM smc_etapa 
                WHERE etapa_id = :etapa_id";

$stmt = $SmecelNovo->prepare($query_Etapa);
$stmt->bindParam(':etapa_id', $etapa, PDO::PARAM_INT);
$stmt->execute();

$row_Etapa = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_Etapa = $stmt->rowCount();


$query_Componente = "SELECT disciplina_id, disciplina_area_conhecimento_id, disciplina_codigo_inep, disciplina_ordem, 
                            disciplina_nome, disciplina_nome_abrev, disciplina_cor_fundo, disciplina_bncc, 
                            disciplina_diversificada, disciplina_id_campos_exp 
                     FROM smc_disciplina 
                     WHERE disciplina_id = :componente_id";

$stmt = $SmecelNovo->prepare($query_Componente);
$stmt->bindParam(':componente_id', $componente, PDO::PARAM_INT);
$stmt->execute();

$row_Componente = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_Componente = $stmt->rowCount();


$etapa_ano = $row_Etapa['etapa_ano_ef'];

$consulta = " AND bncc_ef_ano IN ($etapa_ano) ";
//$consulta = " WHERE bncc_ef_ano IN ($etapa_ano) ";
//$consulta = "";

$disciplina = $row_Componente['disciplina_id'];

$query_bncc_ef = "
SELECT bncc_ef_id, bncc_ef_area_conhec_id, bncc_ef_comp_id, bncc_ef_componente, bncc_ef_ano, bncc_ef_campos_atuacao, 
       bncc_ef_eixo, bncc_ef_un_tematicas, bncc_ef_prat_ling, bncc_ef_obj_conhec, bncc_ef_habilidades, 
       bncc_ef_comentarios, bncc_ef_poss_curr 
FROM smc_bncc_ef
WHERE bncc_ef_comp_id = :disciplina $consulta";

$stmt = $SmecelNovo->prepare($query_bncc_ef);
$stmt->bindParam(':disciplina', $disciplina, PDO::PARAM_INT);
$stmt->execute();

$row_bncc_ef = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRows_bncc_ef = $stmt->rowCount();


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
      <p><a
          href="planejamento_editar_turma.php?ac=<?= $colname_ac ?>&escola=<?= $escola ?>&etapa=<?= $etapa ?>&componente=<?= $componente ?>"
          class="ls-btn ls-ico-chevron-left">Voltar</a></p>
      <hr>
      <label class="ls-label">
        <a href="#" data-ls-module="modal" data-target="#modalHabilidades"
          class="ls-btn-primary<?php if ($totalRows_bncc_ef == 0) { ?> ls-display-none<?php } ?>">HABILIDADES&nbsp;<i
            class="ls-ico-help"></i></a>
      </label>
      <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form ls-form-horizontal row">

        <fieldset>
          <label class="ls-label col-md-6">
            <b class="ls-label-text">Selecione</b>
            <div class="ls-custom-select">
              <select class="ls-custom" name="ac_tipo">
                <option selected="selected">SELECIONAR</option>
                <option value="99">Planejamento completo</option>
                <option value="1">Unidade temática</option>
                <option value="2">Objetivos de Aprendizagem e desenvolvimento</option>
                <option value="3">Objetos de conhecimento/saberes e conhecimento/conteúdo</option>
                <option value="4">Habilidades</option>
                <option value="5">Metodologia</option>
                <option value="6">Avaliação</option>
                <option value="7">Observação</option>
                <option value="8">Recursos</option>
              </select>
            </div>
          </label>
        </fieldset>

        <fieldset>
          <label class="ls-label col-md-12 ">
            <b class="ls-label-text">Conteúdo</b>
            <textarea name="ac_conteudo" id="summernote" rows="4"></textarea>
        </fieldset>

        <div class="ls-actions-btn">
          <input type="submit" class="ls-btn-primary" value="ADICIONAR">
          <input type="hidden" name="MM_update" value="form1" />
          <input type="hidden" name="ac_id" value="<?php echo $colname_ac; ?>" />
        </div>

      </form>








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
          <?php if ($totalRows_bncc_ef > 0): ?>
            <?php foreach ($bncc_ef as $row_bncc_ef): ?>
              <tr>
                <td>
                  <strong>Habilidades:</strong> <?= htmlspecialchars($row_bncc_ef['bncc_ef_habilidades']); ?><br><br>
                  <strong>Componente:</strong> <?= htmlspecialchars($row_bncc_ef['bncc_ef_componente']); ?> |
                  <strong>Ano/Faixa:</strong> <?= htmlspecialchars($row_bncc_ef['bncc_ef_ano']); ?>º ano(s)<br><br>

                  <?php if (!empty($row_bncc_ef['bncc_ef_campos_atuacao'])): ?>
                    <strong>Campo de atuação:</strong>
                    <?= htmlspecialchars($row_bncc_ef['bncc_ef_campos_atuacao']); ?><br><br>
                  <?php endif; ?>

                  <?php if (!empty($row_bncc_ef['bncc_ef_eixo'])): ?>
                    <strong>Eixo:</strong> <?= htmlspecialchars($row_bncc_ef['bncc_ef_eixo']); ?><br><br>
                  <?php endif; ?>

                  <?php if (!empty($row_bncc_ef['bncc_ef_un_tematicas'])): ?>
                    <strong>Unidades Temáticas:</strong>
                    <?= htmlspecialchars($row_bncc_ef['bncc_ef_un_tematicas']); ?><br><br>
                  <?php endif; ?>

                  <?php if (!empty($row_bncc_ef['bncc_ef_prat_ling'])): ?>
                    <strong>Práticas de Linguagem:</strong>
                    <?= htmlspecialchars($row_bncc_ef['bncc_ef_prat_ling']); ?><br><br>
                  <?php endif; ?>

                  <strong>Objetos de conhecimento:</strong>
                  <?= htmlspecialchars($row_bncc_ef['bncc_ef_obj_conhec']); ?><br><br>
                  <strong>Comentários:</strong> <?= htmlspecialchars($row_bncc_ef['bncc_ef_comentarios']); ?><br><br>
                  <strong>Possibilidades para o Currículo:</strong>
                  <?= htmlspecialchars($row_bncc_ef['bncc_ef_poss_curr']); ?><br>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td>Nenhum registro encontrado.</td>
            </tr>
          <?php endif; ?>
        </table>

      </div>
      <div class="ls-modal-footer">
        <button class="ls-btn-primary" data-dismiss="modal">FECHAR</button>
      </div>
    </div>
  </div><!-- /.modal -->


  <!-- We recommended use jQuery 1.10 or up -->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="//assets.locaweb.com.br/locastyle/edge/javascripts/locastyle.js"></script>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="js/sweetalert2.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

  <!--<script src="https://cdn.tiny.cloud/1/iq1gy8qt7s1b9bj92c2fc7whie95augu8r8kz97dgoufkli8/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script> 
      <script src="langs/pt_BR.js"></script>-->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <script>
    $('#summernote').summernote({
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
      ],
      callbacks: {
        onPaste: function (e) {
          // Previne o comportamento padrão de colagem
          e.preventDefault();

          // Obtém o texto simples do que foi copiado
          var clipboardData = (e.originalEvent || e).clipboardData || window.clipboardData;
          var plainText = clipboardData.getData('Text');

          // Insere o texto limpo no editor
          document.execCommand('insertText', false, plainText);
        }
      }
    });

  </script>
  <script>
    $('#disciplinas').select2({
      width: '100%' // Definindo a largura como 100%
    });

    /*tinymce.init({
     selector: 'textarea',

     mobile: {
      menubar: false
    },

    images_upload_url: 'postAcceptor.php',
    automatic_uploads: true,
    imagetools_proxy: 'proxy.php',

//plugins: 'emoticons',
//toolbar: 'emoticons',

//imagetools_toolbar: 'rotateleft rotateright | flipv fliph | editimage imageoptions',

    height: 200,
    toolbar: ['paste undo redo | formatselect | forecolor | bold italic backcolor | bullist numlist | image | emoticons'],
    plugins : ['textcolor','advlist autolink link image imagetools lists charmap print preview paste emoticons',
      'advlist autolink lists link image imagetools charmap print preview anchor',
      'searchreplace visualblocks code fullscreen',
      'insertdatetime media table paste code help wordcount'],
//force_br_newlines : false,
//force_p_newlines : false,
//forced_root_block : '',	
    statusbar: false,
    language: 'pt_BR',
    menubar: false,
    paste_as_text: true,
    content_css: '//www.tinymce.com/css/codepen.min.css'
  });*/

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