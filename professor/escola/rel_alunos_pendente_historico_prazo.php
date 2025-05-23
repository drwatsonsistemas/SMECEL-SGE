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

$hoje = date('Y-m-d');
$dias = 30;

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matriculas = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, 
vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, DATE_ADD(vinculo_aluno_data, INTERVAL 30 DAY) as nova_data,
DATEDIFF(CURDATE(), vinculo_aluno_data) as intervalo,
aluno_id, aluno_nome,
turma_id, turma_nome 
FROM smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma 
WHERE vinculo_aluno_da_casa = 'F' AND 
vinculo_aluno_historico_transferencia = 'D' AND 
vinculo_aluno_id_escola = '$row_EscolaLogada[escola_id]' AND
vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND 
(DATE_ADD(vinculo_aluno_data, INTERVAL 30 DAY) >= CURDATE())
ORDER BY intervalo DESC, aluno_nome ASC
";
$Matriculas = mysql_query($query_Matriculas, $SmecelNovo) or die(mysql_error());
$row_Matriculas = mysql_fetch_assoc($Matriculas);
$totalRows_Matriculas = mysql_num_rows($Matriculas);
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
    <link href="https://assets.locaweb.com.br/locastyle/edge/stylesheets/locastyle.css" rel="stylesheet" type="text/css">
    <link href="css/app.css" rel="stylesheet" type="text/css">
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
 
        <h1 class="ls-title-intro ls-ico-home">ENTREGA DE HISTÓRICO</h1>
		<!-- CONTEÚDO -->
		
		
<div class="ls-group-btn ls-group-active">
  <a href="rel_alunos_pendente_historico.php" type="button" class="ls-btn-primary ls-active">Alunos em atraso</a>
  <a href="rel_alunos_pendente_historico_prazo.php" type="button" class="ls-btn-primary">Alunos no prazo</a>
</div>
		
				<?php if ($totalRows_Matriculas > 0) { ?>

		
        <table class="ls-table ls-sm-space">
		<thead>
          <tr>
            <th class="ls-txt-center" width="45px">Nº</th>
            <th>ALUNO</th>
            <th class="ls-txt-center">TURMA</th>
            <th class="ls-txt-center" width="150px">MATRÍCULA</th>
            <th class="ls-txt-center" width="150px">VENCIMENTO</th>
            <th class="ls-txt-center" width="150px">DIAS RESTANTE</th>
          </tr>
		  </thead>
<?php 
		  $contagem = 1;
		  do { ?>
		  <tr>
		  <td class="ls-txt-center"><?php
					echo $contagem;
					$contagem++;

					?></td>
              <td><a href="receber_historico.php?cod=<?php echo $row_Matriculas['vinculo_aluno_hash']; ?>"><?php echo $row_Matriculas['aluno_nome']; ?></a></td>
              <td class="ls-txt-center"><?php echo $row_Matriculas['turma_nome']; ?></td>
              <td class="ls-txt-center"><?php echo inverteData($row_Matriculas['vinculo_aluno_data']); ?></td>
              <td class="ls-txt-center"><?php echo inverteData($row_Matriculas['nova_data']); ?></td>
              <td class="ls-txt-center"><?php echo 30-$row_Matriculas['intervalo']; ?></td>
            </tr>
            <?php } while ($row_Matriculas = mysql_fetch_assoc($Matriculas)); ?>
        </table>
		
		
				
		<?php } else { ?>
		<hr>
		<div class="ls-alert-warning">
                  Nenhum aluno com Histórico Escolar pendente de entrega.
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

mysql_free_result($Matriculas);
?>
