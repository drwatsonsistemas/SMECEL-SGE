<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>

<?php include "fnc/session.php"; ?>
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
include "usuLogado.php";
include "fnc/anoLetivo.php";

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

  if ($row_UsuLogado['usu_update'] == "N") {
    header(sprintf("Location: turmaListar.php?permissao"));
    die();
  }

  switch($_POST['turma_tipo_atendimento']) {
    case 1:
      $link = "turmaListar.php";
      break;
    case 2:
      $link = "turmaListarAee.php";
      break;    
    case 3:
      $link = "turmaListarComplementar.php";
      break;
    default:
      $link = "turmaListar.php";
      break;
  }


  $updateSQL = sprintf(
    "UPDATE smc_turma SET turma_nome=%s, turma_etapa=%s, turma_matriz_id=%s, turma_turno=%s, turma_total_alunos=%s, turma_multisseriada=%s, turma_tipo_atendimento=%s, turma_pai_id=%s WHERE turma_id=%s",
    GetSQLValueString($_POST['turma_nome'], "text"),
    GetSQLValueString($_POST['turma_etapa'], "int"),
    GetSQLValueString($_POST['turma_matriz_id'], "int"),
    GetSQLValueString($_POST['turma_turno'], "int"),
    GetSQLValueString($_POST['turma_total_alunos'], "text"),
    GetSQLValueString(isset($_POST['turma_multisseriada']) ? "true" : "", "defined", "'1'", "'0'"),
    GetSQLValueString($_POST['turma_tipo_atendimento'], "text"),
    GetSQLValueString($_POST['turma_pai_id'] == "" ? NULL : $_POST['turma_pai_id'], "int"),
    GetSQLValueString($_POST['turma_id'], "int")
  );

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

  // ** REGISTRO DE LOG DE USUÁRIO **
  $usu = $_POST['usu_id'];
  $esc = $_POST['usu_escola'];
  $detalhes = $_POST['detalhes'];
  date_default_timezone_set('America/Bahia');
  $dat = date('Y-m-d H:i:s');

  $sql = "
INSERT INTO smc_registros (
registros_id_escola, 
registros_id_usuario, 
registros_tipo, 
registros_complemento, 
registros_data_hora
) VALUES (
'$esc', 
'$usu', 
'21', 
'($detalhes)', 
'$dat')
";
  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($sql, $SmecelNovo) or die(mysql_error());
  // ** REGISTRO DE LOG DE USUÁRIO **



  $updateGoTo = "$link?editada";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}



mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EscolaLogada = "
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, 
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_tema,  sec_id, sec_cidade, sec_uf 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_listarEtapa = "SELECT etapa_id, etapa_id_filtro, etapa_nome FROM smc_etapa ORDER BY etapa_id ASC";
$listarEtapa = mysql_query($query_listarEtapa, $SmecelNovo) or die(mysql_error());
$row_listarEtapa = mysql_fetch_assoc($listarEtapa);
$totalRows_listarEtapa = mysql_num_rows($listarEtapa);

$colname_turmaEditar = "-1";
if (isset($_GET['c'])) {
  $colname_turmaEditar = $_GET['c'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_turmaEditar = sprintf("SELECT turma_id, turma_id_escola, turma_pai_id, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_multisseriada, turma_tipo_atendimento FROM smc_turma WHERE turma_id_escola = $row_EscolaLogada[escola_id] AND turma_id = %s", GetSQLValueString($colname_turmaEditar, "int"));
$turmaEditar = mysql_query($query_turmaEditar, $SmecelNovo) or die(mysql_error());
$row_turmaEditar = mysql_fetch_assoc($turmaEditar);
$totalRows_turmaEditar = mysql_num_rows($turmaEditar);

if ($totalRows_turmaEditar == "") {
  //echo "TURMA EM BRANCO";	
  header("Location: turmaListar.php?nada");
  exit;
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matriz = "SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash, matriz_ativa FROM smc_matriz WHERE matriz_ativa = 'S' AND matriz_id_secretaria = $row_EscolaLogada[escola_id_sec] ORDER BY matriz_id_etapa, matriz_nome ASC";
$Matriz = mysql_query($query_Matriz, $SmecelNovo) or die(mysql_error());
$row_Matriz = mysql_fetch_assoc($Matriz);
$totalRows_Matriz = mysql_num_rows($Matriz);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_TurmasPai = "SELECT turma_id, turma_nome FROM smc_turma WHERE turma_pai_id IS NULL AND turma_id_escola = '$row_EscolaLogada[escola_id]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND turma_id != '$row_turmaEditar[turma_id]' ORDER BY turma_nome ASC";
$TurmasPai = mysql_query($query_TurmasPai, $SmecelNovo) or die(mysql_error());
$row_TurmasPai = mysql_fetch_assoc($TurmasPai);
$totalRows_TurmasPai = mysql_num_rows($TurmasPai);
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

      <h1 class="ls-title-intro ls-ico-home">Editar Turma</h1>
      <form method="post" class="ls-form-horizontal" name="form1" action="<?php echo $editFormAction; ?>"
        data-ls-module="form">

        <label class="ls-label col-md-12">
          <b class="ls-label-text ls-sm-margin-top">NOME DA TURMA</b><br>
          <p class="ls-label-info">Ex.: 5ª SÉRIE A - MATUTINO</p>
          <input type="text" name="turma_nome"
            value="<?php echo htmlentities($row_turmaEditar['turma_nome'], ENT_COMPAT, 'utf-8'); ?>" size="32">
        </label>

        <label class="ls-label col-md-6 col-xs-12">
          <b class="ls-label-text">ETAPA</b><br>
          <p class="ls-label-info">Modalidade/Etapa</p>
          <div class="ls-custom-select">
            <select name="turma_etapa" class="ls-select">
              <option value="0" <?php if (!(strcmp("0", htmlentities($row_turmaEditar['turma_etapa'], ENT_COMPAT, 'utf-8')))) {
                echo "SELECTED";
              } ?>>NÃO SE APLICA</option>
              <?php do { ?>
                <option value="<?php echo $row_listarEtapa['etapa_id'] ?>" <?php if (!(strcmp($row_listarEtapa['etapa_id'], htmlentities($row_turmaEditar['turma_etapa'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>
                  <?php echo $row_listarEtapa['etapa_nome'] ?></option>
              <?php } while ($row_listarEtapa = mysql_fetch_assoc($listarEtapa)); ?>
            </select>
          </div>
        </label>

        <label class="ls-label col-md-6 col-xs-12">
          <b class="ls-label-text">MATRIZ CURRICULAR</b><br>
          <p class="ls-label-info">Escolha a Matriz da turma</p>
          <div class="ls-custom-select">
            <select name="turma_matriz_id" class="ls-select" required>
              <option value="">Escolha...</option>
              <option value="0" <?php if (!(strcmp("0", htmlentities($row_turmaEditar['turma_matriz_id'], ENT_COMPAT, 'utf-8')))) {
                echo "SELECTED";
              } ?>>NÃO SE APLICA</option>
              <?php do { ?>
                <option value="<?php echo $row_Matriz['matriz_id'] ?>" <?php if (!(strcmp($row_Matriz['matriz_id'], htmlentities($row_turmaEditar['turma_matriz_id'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>
                  <?php echo $row_Matriz['matriz_nome'] ?></option>
              <?php } while ($row_Matriz = mysql_fetch_assoc($Matriz)); ?>
            </select>
          </div>
        </label>


        <label class="ls-label col-md-7 col-xs-12">
          <b class="ls-label-text">TURNO</b><br>
          <p class="ls-label-info">Escolha o turno que a turma funciona</p>
          <div class="ls-custom-select">
            <select name="turma_turno" class="ls-select">
              <option value="0" <?php if (!(strcmp(0, htmlentities($row_turmaEditar['turma_turno'], ENT_COMPAT, 'utf-8')))) {
                echo "SELECTED";
              } ?>>Integral</option>
              <option value="1" <?php if (!(strcmp(1, htmlentities($row_turmaEditar['turma_turno'], ENT_COMPAT, 'utf-8')))) {
                echo "SELECTED";
              } ?>>Matutino</option>
              <option value="2" <?php if (!(strcmp(2, htmlentities($row_turmaEditar['turma_turno'], ENT_COMPAT, 'utf-8')))) {
                echo "SELECTED";
              } ?>>Vespertino</option>
              <option value="3" <?php if (!(strcmp(3, htmlentities($row_turmaEditar['turma_turno'], ENT_COMPAT, 'utf-8')))) {
                echo "SELECTED";
              } ?>>Noturno</option>
            </select>
          </div>
        </label>

        <label class="ls-label col-md-5 col-xs-12">
          <b class="ls-label-text">LIMITE DE ALUNOS NA TURMA</b><br>
          <p class="ls-label-info">Informe o limite de alunos que a turma/sala comporta</p>
          <input type="text" name="turma_total_alunos"
            value="<?php echo htmlentities($row_turmaEditar['turma_total_alunos'], ENT_COMPAT, 'utf-8'); ?>" size="32">
        </label>


        <label class="ls-label col-md-12 col-xs-12">
          <b class="ls-label-text">TIPO DE ATENDIMENTO</b><br>
          <p class="ls-label-info">Escolha o tipo de atendimento da turma</p>
          <div class="ls-custom-select">
            <select name="turma_tipo_atendimento" class="ls-select">
              <option value="1" <?php if (!(strcmp(1, htmlentities($row_turmaEditar['turma_tipo_atendimento'], ENT_COMPAT, 'utf-8')))) {
                echo "SELECTED";
              } ?>>1 - ESCOLARIZAÇÃO</option>
              <option value="2" <?php if (!(strcmp(2, htmlentities($row_turmaEditar['turma_tipo_atendimento'], ENT_COMPAT, 'utf-8')))) {
                echo "SELECTED";
              } ?>>2 - ATENDIMENTO EDUCACIONAL ESPECIALIZADO (AEE)</option>
              <option value="3" <?php if (!(strcmp(3, htmlentities($row_turmaEditar['turma_tipo_atendimento'], ENT_COMPAT, 'utf-8')))) {
                echo "SELECTED";
              } ?>>3 - ATIVIDADE COMPLEMENTAR</option>
            </select>
          </div>
        </label>


        <label class="ls-label col-md-4 col-sm-12">
          <b class="ls-label-text">Turma Multisseriada</b>
          <br>
          <p class="ls-label-info">
          <div data-ls-module="switchButton" class="ls-switch-btn">
            <input type="checkbox" name="turma_multisseriada" id="turma_multisseriada" value="" <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_multisseriada'], ENT_COMPAT, 'utf-8'), "1"))) {
              echo "checked=\"checked\"";
            } ?>>
            <label class="ls-switch-label" for="turma_multisseriada" name="label-teste" ls-switch-off="Não"
              ls-switch-on="Sim"><span></span></label>
          </div>
          </p>
        </label>


        <hr>

        <div class="col-md-12">
          <input type="submit" value="EDITAR TURMA" class="ls-btn-primary">
          <a class="ls-btn-danger" href="turmaListar.php">CANCELAR</a>

          <a class="ls-btn-dark"
            href="turmaEditar_caracteristicas.php?c=<?php echo $row_turmaEditar['turma_id']; ?>">EDITAR
            CARACTERÍSTICAS</a>

        </div>

        <input type="hidden" name="turma_id" value="<?php echo $row_turmaEditar['turma_id']; ?>">
        <input type="hidden" name="MM_update" value="form1">

        <input type="hidden" name="usu_id" value="<?php echo $row_UsuLogado['usu_id']; ?>">
        <input type="hidden" name="usu_escola" value="<?php echo $row_UsuLogado['usu_escola']; ?>">
        <input type="hidden" name="detalhes"
          value="<?php echo htmlentities($row_turmaEditar['turma_nome'], ENT_COMPAT, 'utf-8'); ?>">


      </form>
      <p>&nbsp;</p>
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

  <script>
    jQuery(document).ready(function ($) {
      // Monitora o input do nome da turma para palavras relacionadas ao AEE
      $('input[name="turma_nome"]').on('input', function() {
        var texto = $(this).val().toLowerCase();
        var palavrasAEE = ['aee', 'atendimento', 'atendimento educacional', 'atendimento educacional especializado'];
        
        if (palavrasAEE.some(palavra => texto.includes(palavra))) {
          // Cria o toast usando o LocaStyle
          var toast = $('<div class="ls-alert-info ls-dismissable">' +
            '<span data-ls-module="dismiss" class="ls-dismiss">&times;</span>' +
            'Lembre-se de selecionar o tipo de atendimento como "ATENDIMENTO EDUCACIONAL ESPECIALIZADO (AEE)" no campo abaixo.' +
            '</div>');
          
          // Insere o toast após o input do nome da turma
          $(this).closest('.ls-label').after(toast);
          
          // Remove o toast após 5 segundos
          setTimeout(function() {
            toast.fadeOut(function() {
              $(this).remove();
            });
          }, 5000);
        }
      });

      // Controle do select de etapas baseado no checkbox de turma multisseriada
      $('#turma_multisseriada').change(function() {
        var etapaSelect = $('select[name="turma_etapa"]');
        var options = etapaSelect.find('option');
        var currentValue = etapaSelect.val(); // Guarda o valor atual
        
        if ($(this).is(':checked')) {
          // Se multisseriada estiver ativa, só permite etapas 23 e 25
          options.each(function() {
            var value = $(this).val();
            if (value === '' || value === '0' || value === '23' || value === '25') {
              $(this).show();
            } else {
              $(this).hide();
            }
          });
          
          // Se o valor atual não for válido para multisseriada, reseta para vazio
          if (currentValue !== '' && currentValue !== '0' && currentValue !== '23' && currentValue !== '25') {
            etapaSelect.val('');
          }
        } else {
          // Se multisseriada estiver inativa, mostra todas as opções
          options.show();
        }
      });

      // Executa o controle de etapas ao carregar a página se o checkbox estiver marcado
      if ($('#turma_multisseriada').is(':checked')) {
        $('#turma_multisseriada').trigger('change');
      }
    });
  </script>

</body>

</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($listarEtapa);

mysql_free_result($turmaEditar);

mysql_free_result($TurmasPai);
?>