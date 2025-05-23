<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include ("../funcoes/inverteData.php"); ?>

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
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, sec_id, sec_cidade, sec_uf, escola_tema 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

$colname_Aula = "-1";
if (isset($_GET['aula'])) {
  $colname_Aula = $_GET['aula'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Aula = sprintf("
SELECT plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, plano_aula_data, 
plano_aula_data_cadastro, plano_aula_texto, plano_aula_conteudo, plano_aula_atividade, plano_aula_atividade_resposta_obrigatoria, 
plano_aula_atividade_resposta_obrigatoria_data_limite, plano_aula_video, plano_aula_google_form, plano_aula_publicado, 
plano_aula_hash, func_id, func_nome, disciplina_id, disciplina_nome, turma_id, turma_nome 
FROM smc_plano_aula 
INNER JOIN smc_func ON func_id = plano_aula_id_professor
INNER JOIN smc_disciplina ON disciplina_id = plano_aula_id_disciplina 
INNER JOIN smc_turma ON turma_id = plano_aula_id_turma 
WHERE plano_aula_hash = %s", GetSQLValueString($colname_Aula, "text"));
$Aula = mysql_query($query_Aula, $SmecelNovo) or die(mysql_error());
$row_Aula = mysql_fetch_assoc($Aula);
$totalRows_Aula = mysql_num_rows($Aula);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matricula = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, 
vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, vinculo_aluno_vacina_data_retorno,
aluno_id, aluno_nome, aluno_foto 
FROM smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
WHERE vinculo_aluno_id_turma = '$row_Aula[plano_aula_id_turma]'
";
$Matricula = mysql_query($query_Matricula, $SmecelNovo) or die(mysql_error());
$row_Matricula = mysql_fetch_assoc($Matricula);
$totalRows_Matricula = mysql_num_rows($Matricula);
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
<meta name="description" content="">
<meta name="keywords" content="">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<link rel="stylesheet" type="text/css" href="css/locastyle.css"><link rel="stylesheet" type="text/css" href="css/preloader.css">
<script src="js/locastyle.js"></script>
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
 
        <h1 class="ls-title-intro ls-ico-home">FREQUÊNCIA NA AULA</h1>
		<!-- CONTEÚDO -->
		

 <div class="col-md-12 ls-box">
 
 <p><strong>PROFESSOR(A) </strong><?php echo $row_Aula['func_nome']; ?></p>
 <p><strong>TURMA</strong> <?php echo $row_Aula['disciplina_nome']; ?> - <?php echo $row_Aula['turma_nome']; ?></p>
 <p><strong>AULA </strong><?php echo $row_Aula['plano_aula_texto']; ?></p>
 <p><strong>DATA </strong><?php echo inverteData($row_Aula['plano_aula_data']); ?></p>
 
 </div>
 
 <div class="row">
 
  <?php $cont = 0; do { ?>
  
  <?php  
  
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Visualizacao = "
SELECT visualiza_aula_id, visualiza_aula_id_aula, visualiza_aula_id_matricula, visualiza_aula_data_hora 
FROM smc_visualiza_aula
WHERE visualiza_aula_id_matricula = '$row_Matricula[vinculo_aluno_id]' AND visualiza_aula_id_aula = '$row_Aula[plano_aula_id]'
";
$Visualizacao = mysql_query($query_Visualizacao, $SmecelNovo) or die(mysql_error());
$row_Visualizacao = mysql_fetch_assoc($Visualizacao);
$totalRows_Visualizacao = mysql_num_rows($Visualizacao);
  
  ?>
    
    	<div class="col-md-2 col-xs-6">
        <div <?php if ($totalRows_Visualizacao > 0) { $cont++; ?>class="ls-alert-success ls-ico-checkmark"<?php } else { ?>class="ls-alert-danger ls-ico-close"<?php } ?>>
         <?php if ($row_Matricula['aluno_foto']=="") { ?>
			<img src="<?php echo '../../aluno/fotos/' ?>semfoto.jpg" border="0" width="100%">
		 <?php } else { ?>
			<img src="<?php echo '../../aluno/fotos/' ?><?php echo $row_Matricula['aluno_foto']; ?>" border="0" width="100%">
		 <?php } ?>
			<br><small style="font-size:9px;"><?php echo current( str_word_count($row_Matricula['aluno_nome'],2)); ?> <?php $words = explode(" ", trim($row_Matricula['aluno_nome'])); echo $words[count($words)-1]; ?> </small>
         </div>
         </div>
    
    
  <?php } while ($row_Matricula = mysql_fetch_assoc($Matricula)); ?>
  
  </div>
  
  
  <div class="row">
  <div class="col-md-12">
  <h3><?php echo $totalRows_Matricula; ?> matrículas / <?php echo $cont; ?>  presenças virtuais</h3>

  <hr>


  </div>
  </div>


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

mysql_free_result($Aula);

mysql_free_result($Matricula);

mysql_free_result($Visualizacao);

mysql_free_result($EscolaLogada);
?>
