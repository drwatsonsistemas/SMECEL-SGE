<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include('fnc/inverteData.php'); ?>

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

$colname_matricula = "-1";
if (isset($_GET['cmatricula'])) {
  $colname_matricula = $_GET['cmatricula'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_matricula = sprintf("
SELECT 
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia 
FROM 
smc_vinculo_aluno 
WHERE vinculo_aluno_hash = %s", GetSQLValueString($colname_matricula, "text"));
$matricula = mysql_query($query_matricula, $SmecelNovo) or die(mysql_error());
$row_matricula = mysql_fetch_assoc($matricula);
$totalRows_matricula = mysql_num_rows($matricula);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ocorrencia = "
SELECT ocorrencia_id, ocorrencia_id_aluno, ocorrencia_id_turma, ocorrencia_id_escola, 
ocorrencia_ano_letivo, ocorrencia_data, ocorrencia_hora, ocorrencia_tipo,
CASE ocorrencia_tipo
WHEN 1 THEN 'ADVERTÊNCIA'
WHEN 2 THEN 'SUSPENSÃO'
WHEN 3 THEN 'OUTRAS'
END AS ocorrencia_tipo_nome, 
ocorrencia_afastamento_de, ocorrencia_afastamento_ate, ocorrencia_total_dias, ocorrencia_descricao 
FROM smc_ocorrencia
WHERE ocorrencia_id_aluno = '$row_matricula[vinculo_aluno_id_aluno]' AND ocorrencia_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'";
$ocorrencia = mysql_query($query_ocorrencia, $SmecelNovo) or die(mysql_error());
$row_ocorrencia = mysql_fetch_assoc($ocorrencia);
$totalRows_ocorrencia = mysql_num_rows($ocorrencia);
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

      <h1 class="ls-title-intro ls-ico-home">OCORRÊNCIAS</h1>
      <!-- CONTEÚDO -->

      <p>
        <a href="matriculaExibe.php?cmatricula=<?php echo $row_matricula['vinculo_aluno_hash']; ?>"
          class="ls-btn-primary">VOLTAR</a>
        <a href="ocorrenciaCadastrar.php?cmatricula=<?php echo $row_matricula['vinculo_aluno_hash']; ?>"
          class="ls-ico-plus ls-btn-primary">Registrar ocorrência</a>

      </p>

      <hr>

      <?php if (isset($_GET["excluido"])) { ?>
        <div class="ls-alert-success ls-dismissable">
          <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
          OCORRÊNCIA EXCLUÍDA COM SUCESSO.
        </div>
      <?php } ?>

      <?php if ($totalRows_ocorrencia > 0) { ?>

        <?php do { ?>
          <div class="ls-box">
            <h5 class="ls-title-3"><?php echo $row_ocorrencia['ocorrencia_tipo_nome']; ?></h5>


            <p>
              TIPO: <?php echo $row_ocorrencia['ocorrencia_tipo_nome']; ?><br>
              DATA: <?php echo inverteData($row_ocorrencia['ocorrencia_data']); ?><br>
              HORA: <?php echo $row_ocorrencia['ocorrencia_hora']; ?><br>
              <?php if ($row_ocorrencia['ocorrencia_tipo'] == "2") { ?>
                DE: <?php echo inverteData($row_ocorrencia['ocorrencia_afastamento_de']); ?> ATÉ:
                <?php echo inverteData($row_ocorrencia['ocorrencia_afastamento_ate']); ?><br>
                TOTAL DE DIAS EM AFASTAMENTO: <?php echo $row_ocorrencia['ocorrencia_total_dias']; ?><br>
              <?php } ?>
              DESCRIÇÃO: <i><strong><?php echo $row_ocorrencia['ocorrencia_descricao']; ?></strong></i><br>
            </p>

            <a href="imprimir/print_ocorrencia.php?ocorrencia=<?php echo $row_ocorrencia['ocorrencia_id']; ?>&hash=<?php echo $row_matricula['vinculo_aluno_hash']; ?>"
              class="ls-btn-primary ls-ico-paint-format" target="_blank"></a>
            
            <a href="ocorrenciaExcluir.php?ocorrencia=<?php echo $row_ocorrencia['ocorrencia_id']; ?>&cmatricula=<?php echo $row_matricula['vinculo_aluno_hash']; ?>"
              class="ls-btn-primary-danger ls-ico-remove ls-float-right"></a>

              <a href="ocorrenciaEditar.php?ocorrencia=<?php echo $row_ocorrencia['ocorrencia_id']; ?>&cmatricula=<?php echo $row_matricula['vinculo_aluno_hash']; ?>"
              class="ls-btn-primary ls-ico-pencil ls-float-right"></a>


          </div>

        <?php } while ($row_ocorrencia = mysql_fetch_assoc($ocorrencia)); ?>

      <?php } else { ?>
        <div class="ls-alert-success ls-dismissable">
          <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
          ALUNO NÃO POSSUI NENHUMA OCORRÊNCIA REGISTRADA.
        </div>
      <?php } ?>


      <!-- CONTEÚDO -->
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
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($matricula);

mysql_free_result($ocorrencia);
?>