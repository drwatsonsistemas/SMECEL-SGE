<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include('../funcoes/url_base.php'); ?>
<?php include('../funcoes/inverteData.php'); ?>

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

$colname_Aulas = "-1";
if (isset($_GET['aula'])) {
  $colname_Aulas = $_GET['aula'];
}

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
$query_Aulas = sprintf("
SELECT plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, 
plano_aula_data, plano_aula_texto, plano_aula_conteudo, plano_aula_atividade, plano_aula_video, plano_aula_hash,
turma_id, turma_nome, turma_id_escola, turma_ano_letivo,
disciplina_id, disciplina_nome,
func_id, func_nome 
FROM smc_plano_aula
INNER JOIN smc_turma ON turma_id = plano_aula_id_turma 
INNER JOIN smc_disciplina ON disciplina_id = plano_aula_id_disciplina
INNER JOIN smc_func ON func_id = plano_aula_id_professor
WHERE plano_aula_hash = %s", GetSQLValueString($colname_Aulas, "text"));
$Aulas = mysql_query($query_Aulas, $SmecelNovo) or die(mysql_error());
$row_Aulas = mysql_fetch_assoc($Aulas);
$totalRows_Aulas = mysql_num_rows($Aulas);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Anexos = "SELECT plano_aula_anexo_id, plano_aula_anexo_id_atividade, plano_aula_anexo_arquivo FROM smc_plano_aula_anexo WHERE plano_aula_anexo_id_atividade = '$row_Aulas[plano_aula_id]'";
$Anexos = mysql_query($query_Anexos, $SmecelNovo) or die(mysql_error());
$row_Anexos = mysql_fetch_assoc($Anexos);
$totalRows_Anexos = mysql_num_rows($Anexos);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Comentarios = "
SELECT com_at_aluno_id, com_at_aluno_id_atividade, com_at_aluno_id_matricula, com_at_aluno_data_hora, 
com_at_aluno_comentario, com_at_aluno_comentario_professor, com_at_aluno_comentario_professor_data,
vinculo_aluno_id, vinculo_aluno_id_aluno, aluno_id, aluno_nome, aluno_foto  
FROM smc_coment_ativ_aluno
INNER JOIN smc_vinculo_aluno ON vinculo_aluno_id = com_at_aluno_id_matricula
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
WHERE com_at_aluno_id_atividade = '$row_Aulas[plano_aula_id]'  
ORDER BY com_at_aluno_id ASC";
$Comentarios = mysql_query($query_Comentarios, $SmecelNovo) or die(mysql_error());
$row_Comentarios = mysql_fetch_assoc($Comentarios);
$totalRows_Comentarios = mysql_num_rows($Comentarios);



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
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<style>
table {
	width:100%;
	border-collapse: collapse;
	font-size:12px;
}
th, td {
	border:0px solid #ccc;
}
th, td {
	padding:5px;
	height:15px;
	line-height:15px;
}
.leitura img {
	max-width:100%;
	height:auto;
	margin:10px 0;
}
</style>
</head>
  <body>
    <?php include_once ("menu-top.php"); ?>
          <?php include_once ("menu-esc.php"); ?>


    <main class="ls-main ">
      <div class="container-fluid">
 
        <h1 class="ls-title-intro ls-ico-home">AULA VIRTUAL</h1>
		<!-- CONTEÚDO -->
        
        <p>
		<a href="aulas_virtuais.php" class="ls-btn">Voltar</a> 
		<a href="aulas_virtuais_calendario.php" class="ls-btn-primary">Calendário</a>
		<a href="aulas_virtuais_ver_imprimir.php?aula=<?php echo $colname_Aulas; ?>" target="_blank" class="ls-btn-primary">Imprimir</a>
		<a href="aulas_virtuais_frequencia.php?aula=<?php echo $colname_Aulas; ?>" target="_blank" class="ls-btn-primary">Frequência</a>
		</p>
		
		<br>
        
        
		
  <div class="row">
    <div class="col-md-8 col-sm-12 ls-box">
        
            
			<div class="ls-box ls-xs-space">
			<h6><?php echo inverteData($row_Aulas['plano_aula_data']); ?></h6>
			<h2><?php echo $row_Aulas['plano_aula_texto']; ?></h2>
            <small><?php echo $row_Aulas['func_nome']; ?> - <?php echo $row_Aulas['disciplina_nome']; ?> - <?php echo $row_Aulas['turma_nome']; ?></small>
            </div>
			
			<hr>
            
			<div class="leitura">
			<p>
			<?php echo $row_Aulas['plano_aula_conteudo']; ?>
            </p>
            
			
            
	  <?php if ($row_Aulas['plano_aula_video']<>"") { ?>
      <div class="card-panel1">
	  <h5 class="center">Vídeo de apoio</h5>
      <p>
<?php
	  
function youtube_id_from_url($url) {
    $pattern = 
        '%^# Match any YouTube URL
        (?:https?://)?  # Optional scheme. Either http or https
        (?:www\.)?      # Optional www subdomain
        (?:             # Group host alternatives
          youtu\.be/    # Either youtu.be,
        |youtube(?:-nocookie)?\.com  # or youtube.com and youtube-nocookie
          (?:           # Group path alternatives
            /embed/     # Either /embed/
          | /v/         # or /v/
          | /watch\?v=  # or /watch\?v=
          )             # End path alternatives.
        )               # End host alternatives.
        ([\w-]{10,12})  # Allow 10-12 for 11 char YouTube id.
        %x'
        ;
    $result = preg_match($pattern, $url, $matches);
    if (false !== $result) {
        return $matches[1];
    }
    return false;
}
	 
$id = youtube_id_from_url($row_Aulas['plano_aula_video']);
$width = '100%';
$height = '400';

?>
		<div class="ls-height-auto">
		<iframe id="ytplayer" type="text/html" width="<?php echo $width ?>" height="<?php echo $height ?>"
			src="https://www.youtube.com/embed/<?php echo $id ?>?rel=0&showinfo=0&color=white&iv_load_policy=3"
			frameborder="0" allowfullscreen></iframe> 
		</div>
		</p>
		</div>
		<hr>
      <?php } ?>
			


	  <?php if ($row_Aulas['plano_aula_atividade']<>"") { ?>
	  <div class="card-panel1"><h5 class="center">Atividade proposta</h5>
	  <p class="flow-text"><?php echo $row_Aulas['plano_aula_atividade']; ?></p></div>
      <?php } ?>
	  
	  
	  
	  <hr>
	  
	  
	  <h4>Comentários (<?php echo $totalRows_Comentarios ?>):</h4>
            <?php if ($totalRows_Comentarios > 0) { // Show if recordset not empty ?>
  <table class="ls-table">
    
    <?php do { ?>
      <tr>
        <td class="top" valign="top" style="vertical-align: top;" width="80">
          
          <?php if ($row_Comentarios['aluno_foto']=="") { ?>
          <img src="<?php echo URL_BASE.'aluno/fotos/' ?>semfoto.jpg" width="100%" class="hoverable">
          <?php } else { ?>
          <img src="<?php echo URL_BASE.'aluno/fotos/' ?><?php echo $row_Comentarios['aluno_foto']; ?>" width="100%" class="hoverable">
          <?php } ?>
          
          
        </td>
        <td valign="top" style="vertical-align: top;">
          <small>
            <a href="#"><?php echo current( str_word_count($row_Comentarios['aluno_nome'],2)); ?>
            <?php $word = explode(" ", trim($row_Comentarios['aluno_nome'])); echo $word[count($word)-1]; ?></a> - 
            <?php echo date('H\hi - d/m/Y', strtotime($row_Comentarios['com_at_aluno_data_hora'])); ?></small>
          
          <br><br><p><?php echo nl2br($row_Comentarios['com_at_aluno_comentario']); ?></p>
          
          
          <p>
            <?php if ($row_Comentarios['com_at_aluno_comentario_professor']<>"") { ?>
          <div style="padding: 10px 10px; border-left:1px solid #066; margin-left:10px; background-color:#F9F9F9;" class="1blue lighten-5">
            <?php echo $row_Comentarios['com_at_aluno_comentario_professor']; ?><br>
            <?php echo date('H\hi - d/m/Y', strtotime($row_Comentarios['com_at_aluno_comentario_professor_data'])); ?>
          </div>
          <?php } ?> 
          </p>
          
          
        </td>
      </tr>
      <?php } while ($row_Comentarios = mysql_fetch_assoc($Comentarios)); ?>
  </table>
  <?php } else { ?>
  
  <p>Nenhum comentário até agora.</p>
  
  <?php } // Show if recordset not empty ?>
	  


            
			</div>
			
            </div>
            
			<div class="col-md-4 col-sm-12">
			
				  <?php if ($totalRows_Anexos > 0) { ?>
	  
	  <div class="ls-box"><h2 class="center">Anexos</h2>
	  <hr>
	  
      
      <?php do { ?>
        <p><a class="ls-btn" href="<?php echo URL_BASE.'anexos/'.$row_Aulas['plano_aula_id'] ?>/<?php echo $row_Anexos['plano_aula_anexo_arquivo']; ?>" target="_blank"><i class="material-icons">attach_file</i> (baixar)</a></p>
       <?php } while ($row_Anexos = mysql_fetch_assoc($Anexos)); ?>
      </div>
      <?php } else { ?>
	  <p>Nenhum anexo</p>
      <?php } ?>
            
            </div>

		
		
		
	<!-- CONTEÚDO -->
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
<?php
mysql_free_result($UsuLogado);

mysql_free_result($Comentarios);

mysql_free_result($Aulas);

mysql_free_result($EscolaLogada);
?>
