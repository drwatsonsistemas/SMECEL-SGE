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

$turma = "-1";
if (isset($_GET['turma'])) {
  $turma = $_GET['turma'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turma = sprintf("SELECT turma_id, turma_id_escola, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo FROM smc_turma WHERE turma_id = %s", GetSQLValueString($turma, "int"));
$Turma = mysql_query($query_Turma, $SmecelNovo) or die(mysql_error());
$row_Turma = mysql_fetch_assoc($Turma);
$totalRows_Turma = mysql_num_rows($Turma);

$disciplina = "-1";
if (isset($_GET['disciplina'])) {
  $disciplina = $_GET['disciplina'];
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Notas = "
SELECT 
boletim_id, boletim_id_vinculo_aluno, boletim_id_disciplina, 
boletim_1v1, boletim_2v1, boletim_3v1, vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_situacao, turma_id, turma_nome, disciplina_id, disciplina_nome
FROM smc_boletim_disciplinas 
INNER JOIN smc_vinculo_aluno ON vinculo_aluno_id = boletim_id_vinculo_aluno 
INNER JOIN smc_turma ON turma_id = '$turma'
INNER JOIN smc_disciplina ON disciplina_id = $disciplina
WHERE boletim_id_disciplina = '$disciplina' AND vinculo_aluno_id_turma = '$turma' AND vinculo_aluno_situacao = '1'
";
$Notas = mysql_query($query_Notas, $SmecelNovo) or die(mysql_error());
$row_Notas = mysql_fetch_assoc($Notas);
$totalRows_Notas = mysql_num_rows($Notas);
?>
<!DOCTYPE html>
<html class="<?php echo $row_EscolaLogada['escola_tema']; ?>" lang="pt-br">
  <head>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-117872281-1');
</script>

    <title>SMECEL - Sistema de Gestão Escolar</title>

    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="description" content="Sistema de Gestão Escolar.">
    <link href="https://assets.locaweb.com.br/locastyle/edge/stylesheets/locastyle.css1" rel="stylesheet" type="text/css">
    <link rel="icon" sizes="192x192" href="img/icone.png">
    <link rel="apple-touch-icon" href="img/icone.png">
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
  <body>
    <?php include_once ("menu-top.php"); ?>
          <?php include_once ("menu-esc.php"); ?>


    <main class="ls-main ">
      <div class="container-fluid">
 
        <h1 class="ls-title-intro ls-ico-home"><?php echo $row_Turma['turma_nome']; ?></h1>
		<!-- CONTEÚDO -->
        <table width="100%">
          <tr>
            <td>VINCULO ID</td>
            <td>DISCIPLINA ID</td>
            <td>TURMA</td>
            <td>V1</td>
            <td>V2</td>
            <td>V3</td>
            <td>MEDIA</td>
          </tr>
          <?php do { ?>
            <tr>
              <td><?php echo $row_Notas['boletim_id_vinculo_aluno']; ?></td>
              <td><?php echo $row_Notas['boletim_id_disciplina']; ?></td>
              <td><?php echo $row_Notas['turma_nome']; ?></td>
              <td><?php echo $row_Notas['boletim_1v1']; ?></td>
              <td><?php echo $row_Notas['boletim_2v1']; ?></td>
              <td><?php echo $row_Notas['boletim_3v1']; ?></td>
              <td>
			  <?php 
			  $media = ($row_Notas['boletim_1v1'] + $row_Notas['boletim_2v1'] + $row_Notas['boletim_3v1'])/3;
			  echo $media; 
			  ?></td>
            </tr>
            <?php } while ($row_Notas = mysql_fetch_assoc($Notas)); ?>
        </table>
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

mysql_free_result($Turma);

mysql_free_result($Notas);
?>
