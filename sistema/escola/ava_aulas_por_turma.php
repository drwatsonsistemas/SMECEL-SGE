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
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, sec_id, sec_cidade, sec_uf, escola_tema 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

$colname_Turma = "-1";
if (isset($_GET['turma'])) {
  $colname_Turma = $_GET['turma'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turma = sprintf("SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo FROM smc_turma WHERE turma_id = %s", GetSQLValueString($colname_Turma, "int"));
$Turma = mysql_query($query_Turma, $SmecelNovo) or die(mysql_error());
$row_Turma = mysql_fetch_assoc($Turma);
$totalRows_Turma = mysql_num_rows($Turma);

if ($totalRows_Turma  == "") {
	//echo "TURMA EM BRANCO";	
	header("Location: turmasAlunosVinculados.php?nada"); 
 	exit;
	}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AulasTurma = "
SELECT plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, plano_aula_data, 
plano_aula_data_cadastro, plano_aula_texto, plano_aula_conteudo, plano_aula_atividade, plano_aula_atividade_resposta_obrigatoria, 
plano_aula_atividade_resposta_obrigatoria_data_limite, plano_aula_video, plano_aula_publicado, plano_aula_hash,
disciplina_id, disciplina_nome, func_id, func_nome 
FROM smc_plano_aula
INNER JOIN smc_disciplina ON disciplina_id = plano_aula_id_disciplina
INNER JOIN smc_func ON FUNC_ID = plano_aula_id_professor 
WHERE plano_aula_id_turma = '$row_Turma[turma_id]' 
AND (plano_aula_conteudo IS NOT NULL OR plano_aula_video IS NOT NULL OR plano_aula_atividade IS NOT NULL) 
ORDER BY plano_aula_data DESC";
$AulasTurma = mysql_query($query_AulasTurma, $SmecelNovo) or die(mysql_error());
$row_AulasTurma = mysql_fetch_assoc($AulasTurma);
$totalRows_AulasTurma = mysql_num_rows($AulasTurma);

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
 
        <h1 class="ls-title-intro ls-ico-home">AULAS DO <?php echo $row_Turma['turma_nome']; ?></h1>
		<!-- CONTEÚDO -->
        
		<p>
        
<div class="ls-box-filter">
  <form action="ava_aulas_por_turma_print.php" method="get" class="ls-form ls-form-inline row" data-ls-module="form" target="_blank">
    <input type="hidden" name="turma" value="<?php echo $row_Turma['turma_id']; ?>">
    <label class="ls-label col-md-3 col-sm-4">
      <b class="ls-label-text">Período</b>
      <input type="date" name="dataInicio" class="" id="" data-ls-daterange="" autocomplete="off" required>
    </label>

    <label class="ls-label col-md-3 col-sm-4">
      <b class="ls-label-text">a</b>
      <input type="date" name="dataFinal" class="" id="" autocomplete="off" required>
    </label>
    <div class="ls-actions-btn">
      <input type="submit" value="Imprimir" class="ls-btn">
    </div>
  </form>
</div>

        </p>


        
        <a href="ava_aulas_turmas.php" class="ls-btn">Voltar</a>
        
        <table class="ls-table ls-sm-space">
          <thead>
          <tr>
            <th width="100">DATA/AULA</th>
            <th width="300">DISCIPLINA</th>
            <th width="200">PROFESSOR(A)</th>
            <th>ASSUNTO</th>
          </tr>
          </thead>
          <tbody>
          <?php do { ?>
            <tr>
              <td><?php echo date("d/m/y", strtotime($row_AulasTurma['plano_aula_data'])); ?></td>
              <td><?php echo $row_AulasTurma['disciplina_nome']; ?></td>
              <td><?php echo current( str_word_count($row_AulasTurma['func_nome'],2)); ?> <?php $word = explode(" ", trim($row_AulasTurma['func_nome'])); echo $word[count($word)-1]; ?></td>
              <td><a href="aulas_virtuais_ver.php?aula=<?php echo $row_AulasTurma['plano_aula_hash']; ?>" target="_blank"><?php echo $row_AulasTurma['plano_aula_id']; ?></a> - <?php echo substr(mb_strtoupper($row_AulasTurma['plano_aula_texto']),0,50); ?>...</td>
            </tr>
            <?php } while ($row_AulasTurma = mysql_fetch_assoc($AulasTurma)); ?>
            </tbody>
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
    
	<script src="js/pikaday.js"></script> 
	<script>
	//locastyle.modal.open("#myAwesomeModal");
	locastyle.datepicker.newDatepicker('#dataInicio, #dataFinal');
	</script>
 
  </body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($Turma);

mysql_free_result($AulasTurma);

mysql_free_result($EscolaLogada);
?>
