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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AulasTotalGeral = "
SELECT plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, plano_aula_data, 
plano_aula_data_cadastro, plano_aula_texto, plano_aula_conteudo, plano_aula_atividade, 
plano_aula_atividade_resposta_obrigatoria, plano_aula_atividade_resposta_obrigatoria_data_limite, 
plano_aula_video, plano_aula_publicado, plano_aula_hash, turma_id, turma_nome, turma_id_escola, turma_ano_letivo 
FROM smc_plano_aula
INNER JOIN smc_turma ON turma_id = plano_aula_id_turma 
WHERE turma_id_escola = '$row_EscolaLogada[escola_id]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'";
$AulasTotalGeral = mysql_query($query_AulasTotalGeral, $SmecelNovo) or die(mysql_error());
$row_AulasTotalGeral = mysql_fetch_assoc($AulasTotalGeral);
$totalRows_AulasTotalGeral = mysql_num_rows($AulasTotalGeral);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AulasTotal = "
SELECT plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, plano_aula_data, 
plano_aula_data_cadastro, plano_aula_texto, plano_aula_conteudo, plano_aula_atividade, plano_aula_google_form, 
plano_aula_atividade_resposta_obrigatoria, plano_aula_atividade_resposta_obrigatoria_data_limite, 
plano_aula_video, plano_aula_publicado, plano_aula_hash, turma_id, turma_nome, turma_id_escola, turma_ano_letivo 
FROM smc_plano_aula
INNER JOIN smc_turma ON turma_id = plano_aula_id_turma 
WHERE turma_id_escola = '$row_EscolaLogada[escola_id]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' 
AND (plano_aula_conteudo IS NOT NULL OR plano_aula_video IS NOT NULL OR plano_aula_atividade IS NOT NULL OR plano_aula_google_form IS NOT NULL)";
$AulasTotal = mysql_query($query_AulasTotal, $SmecelNovo) or die(mysql_error());
$row_AulasTotal = mysql_fetch_assoc($AulasTotal);
$totalRows_AulasTotal = mysql_num_rows($AulasTotal);

$hojee = date('Y-m-d', strtotime('+0 days'));

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AulasTotalHoje = "
SELECT plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, plano_aula_data, 
plano_aula_data_cadastro, plano_aula_texto, plano_aula_conteudo, plano_aula_atividade, 
plano_aula_atividade_resposta_obrigatoria, plano_aula_atividade_resposta_obrigatoria_data_limite, 
plano_aula_video, plano_aula_publicado, plano_aula_hash, turma_id, turma_nome, turma_id_escola, turma_ano_letivo 
FROM smc_plano_aula
INNER JOIN smc_turma ON turma_id = plano_aula_id_turma 
WHERE turma_id_escola = '$row_EscolaLogada[escola_id]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' 
AND (plano_aula_conteudo IS NOT NULL OR plano_aula_video IS NOT NULL OR plano_aula_atividade IS NOT NULL) AND plano_aula_data = '$hojee'";
$AulasTotalHoje = mysql_query($query_AulasTotalHoje, $SmecelNovo) or die(mysql_error());
$row_AulasTotalHoje = mysql_fetch_assoc($AulasTotalHoje);
$totalRows_AulasTotalHoje = mysql_num_rows($AulasTotalHoje);

$amanhaa = date('Y-m-d', strtotime('+1 days'));

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AulasTotalAmanha = "
SELECT plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, plano_aula_data, 
plano_aula_data_cadastro, plano_aula_texto, plano_aula_conteudo, plano_aula_atividade, 
plano_aula_atividade_resposta_obrigatoria, plano_aula_atividade_resposta_obrigatoria_data_limite, 
plano_aula_video, plano_aula_publicado, plano_aula_hash, turma_id, turma_nome, turma_id_escola, turma_ano_letivo 
FROM smc_plano_aula
INNER JOIN smc_turma ON turma_id = plano_aula_id_turma 
WHERE turma_id_escola = '$row_EscolaLogada[escola_id]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' 
AND (plano_aula_conteudo IS NOT NULL OR plano_aula_video IS NOT NULL OR plano_aula_atividade IS NOT NULL) AND plano_aula_data = '$amanhaa'";
$AulasTotalAmanha = mysql_query($query_AulasTotalAmanha, $SmecelNovo) or die(mysql_error());
$row_AulasTotalAmanha = mysql_fetch_assoc($AulasTotalAmanha);
$totalRows_AulasTotalAmanha = mysql_num_rows($AulasTotalAmanha);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Comentarios = "
SELECT com_at_aluno_id, com_at_aluno_id_atividade, com_at_aluno_id_matricula, com_at_aluno_data_hora, com_at_aluno_comentario, 
com_at_aluno_comentario_professor, com_at_aluno_comentario_professor_data, plano_aula_id, plano_aula_id_turma, turma_id, turma_id_escola, turma_ano_letivo  
FROM smc_coment_ativ_aluno
INNER JOIN smc_plano_aula ON plano_aula_id = com_at_aluno_id_atividade 
INNER JOIN smc_turma ON turma_id = plano_aula_id_turma
WHERE turma_id_escola = '$row_EscolaLogada[escola_id]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'  
";
$Comentarios = mysql_query($query_Comentarios, $SmecelNovo) or die(mysql_error());
$row_Comentarios = mysql_fetch_assoc($Comentarios);
$totalRows_Comentarios = mysql_num_rows($Comentarios);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ComentariosResp = "
SELECT com_at_aluno_id, com_at_aluno_id_atividade, com_at_aluno_id_matricula, com_at_aluno_data_hora, com_at_aluno_comentario, 
com_at_aluno_comentario_professor, com_at_aluno_comentario_professor_data, plano_aula_id, plano_aula_id_turma, turma_id, turma_id_escola, turma_ano_letivo
FROM smc_coment_ativ_aluno
INNER JOIN smc_plano_aula ON plano_aula_id = com_at_aluno_id_atividade 
INNER JOIN smc_turma ON turma_id = plano_aula_id_turma
WHERE turma_id_escola = '$row_EscolaLogada[escola_id]' AND com_at_aluno_comentario_professor IS NOT NULL  AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' 
";
$ComentariosResp = mysql_query($query_ComentariosResp, $SmecelNovo) or die(mysql_error());
$row_ComentariosResp = mysql_fetch_assoc($ComentariosResp);
$totalRows_ComentariosResp = mysql_num_rows($ComentariosResp);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Anexos = "
SELECT plano_aula_anexo_id, plano_aula_anexo_id_atividade, plano_aula_id, plano_aula_id_turma,
turma_id, turma_nome, turma_id_escola, turma_ano_letivo 
FROM smc_plano_aula_anexo
INNER JOIN smc_plano_aula ON plano_aula_id = plano_aula_anexo_id_atividade
INNER JOIN smc_turma ON turma_id = plano_aula_id_turma 
WHERE turma_id_escola = '$row_EscolaLogada[escola_id]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' 
";
$Anexos = mysql_query($query_Anexos, $SmecelNovo) or die(mysql_error());
$row_Anexos = mysql_fetch_assoc($Anexos);
$totalRows_Anexos = mysql_num_rows($Anexos);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AulasTotalVideos = "
SELECT plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, plano_aula_data, 
plano_aula_data_cadastro, plano_aula_texto, plano_aula_conteudo, plano_aula_atividade, 
plano_aula_atividade_resposta_obrigatoria, plano_aula_atividade_resposta_obrigatoria_data_limite, 
plano_aula_video, plano_aula_publicado, plano_aula_hash, turma_id, turma_nome, turma_id_escola, turma_ano_letivo 
FROM smc_plano_aula
INNER JOIN smc_turma ON turma_id = plano_aula_id_turma 
WHERE turma_id_escola = '$row_EscolaLogada[escola_id]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' 
AND plano_aula_video IS NOT NULL";
$AulasTotalVideos = mysql_query($query_AulasTotalVideos, $SmecelNovo) or die(mysql_error());
$row_AulasTotalVideos = mysql_fetch_assoc($AulasTotalVideos);
$totalRows_AulasTotalVideos = mysql_num_rows($AulasTotalVideos);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AulasTotalAvaliacoes = "
SELECT plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, plano_aula_data, 
plano_aula_data_cadastro, plano_aula_texto, plano_aula_conteudo, plano_aula_atividade, 
plano_aula_atividade_resposta_obrigatoria, plano_aula_atividade_resposta_obrigatoria_data_limite, plano_aula_google_form,
plano_aula_video, plano_aula_publicado, plano_aula_hash, turma_id, turma_nome, turma_id_escola, turma_ano_letivo 
FROM smc_plano_aula
INNER JOIN smc_turma ON turma_id = plano_aula_id_turma 
WHERE turma_id_escola = '$row_EscolaLogada[escola_id]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' 
AND plano_aula_google_form IS NOT NULL";
$AulasTotalAvaliacoes = mysql_query($query_AulasTotalAvaliacoes, $SmecelNovo) or die(mysql_error());
$row_AulasTotalAvaliacoes = mysql_fetch_assoc($AulasTotalAvaliacoes);
$totalRows_AulasTotalAvaliacoes = mysql_num_rows($AulasTotalAvaliacoes);
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
 
        <h1 class="ls-title-intro ls-ico-home">Ambiente Virtual</h1>
		<!-- CONTEÚDO -->
		

	
        
        
  <div class="ls-box ls-board-box">

  <div id="sending-stats" class="row">
  

	<div class="col-sm-12 col-md-6">
      <div class="ls-box">
        <div class="ls-box-head">
          <h6 class="ls-title-4">TOTAL DE AULAS</h6>
        </div>
        <div class="ls-box-body">
          <div class="col-xs-6">
            <strong style="color:#b71c1c" class="count"><?php echo $totalRows_AulasTotalGeral ?></strong>
            <small>todas</small>
          </div>
          <div class="col-xs-6">
            <strong style="color:#880e4f" class="count"><?php echo $totalRows_AulasTotal ?></strong>
            <small>c/ conteúdo</small>
          </div>
        </div>
		</div>
	</div>
  

	<div class="col-sm-12 col-md-6">
      <div class="ls-box">
        <div class="ls-box-head">
          <h6 class="ls-title-4">AULAS</h6>
        </div>
        <div class="ls-box-body">
          <div class="col-xs-6">
            <strong style="color:#4a148c" class="count"><?php echo $totalRows_AulasTotalHoje ?></strong>
            <small>aulas de hoje</small>
          </div>
          <div class="col-xs-6">
            <strong style="color:#7c4dff" class="count"><?php echo $totalRows_AulasTotalAmanha ?></strong>
            <small>aulas de amanhã</small>
          </div>
        </div>
		</div>
	</div>
	

  
  </div>

</div>
        

		

  <div class="ls-box ls-board-box">

  <div id="sending-stats" class="row">
    
	<div class="col-sm-12 col-md-6">
      <div class="ls-box">
        <div class="ls-box-head">
          <h6 class="ls-title-4">COMENTÁRIOS</h6>
        </div>
        <div class="ls-box-body">
          <div class="col-xs-6">
            <strong style="color:#00acc1" class="count"><?php echo $totalRows_Comentarios ?></strong>
            <small>todos</small>
          </div>
          <div class="col-xs-6">
            <strong style="color:#00796b" class="count"><?php echo $totalRows_ComentariosResp ?></strong>
            <small>respondidos</small>
          </div>
        </div>
		</div>
	</div>
	
	

	
	
	<div class="col-sm-12 col-md-6">
      <div class="ls-box">
        <div class="ls-box-head">
          <h6 class="ls-title-4">CONTEÚDO</h6>
        </div>
        <div class="ls-box-body">
          <div class="col-xs-4">
            <strong style="color:#388e3c" class="count"><?php echo $totalRows_Anexos ?></strong>
            <small>ANEXOS</small>
          </div>
          <div class="col-xs-4">
            <strong style="color:#afb42b" class="count"><?php echo $totalRows_AulasTotalVideos ?></strong>
            <small>VÍDEOS</small>
          </div>
          <div class="col-xs-4">
            <strong style="color:#a1887f" class="count"><?php echo $totalRows_AulasTotalAvaliacoes ?></strong>
            <small>AVALIAÇÕES</small>
          </div>
        </div>
		</div>
	</div>
	
	
  
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
	<script type="text/javascript">
$('.count').each(function () {
    $(this).prop('Counter',0).animate({
        Counter: $(this).text()
    }, {
        duration: 6000,
        easing: 'swing',
        step: function (now) {
            $(this).text(Math.ceil(now));
        }
    });
});
</script>
 
  </body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($Anexos);

mysql_free_result($Comentarios);

mysql_free_result($AulasTotal);

mysql_free_result($EscolaLogada);

mysql_free_result($AulasTotalHoje);
mysql_free_result($AulasTotalGeral);
mysql_free_result($AulasTotalAmanha);
mysql_free_result($ComentariosResp);
mysql_free_result($AulasTotalVideos);
mysql_free_result($AulasTotalAvaliacoes);
?>
