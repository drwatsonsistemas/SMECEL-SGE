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


$profQry = "";
if (isset($_GET['professor'])) {
  $colname_Professor = $_GET['professor'];
  $profQry = " AND plano_aula_id_professor = '$colname_Professor'";
}


$currentPage = $_SERVER["PHP_SELF"];

$maxRows_Aulas = 50;
$pageNum_Aulas = 0;
if (isset($_GET['pageNum_Aulas'])) {
  $pageNum_Aulas = $_GET['pageNum_Aulas'];
}
$startRow_Aulas = $pageNum_Aulas * $maxRows_Aulas;

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Aulas = "
SELECT plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, 
plano_aula_data, plano_aula_texto, plano_aula_conteudo, plano_aula_video, plano_aula_atividade, plano_aula_google_form, plano_aula_publicado, plano_aula_hash,
turma_id, turma_nome, turma_id_escola, turma_ano_letivo,
disciplina_id, disciplina_nome, disciplina_cor_fundo,
func_id, func_nome
FROM smc_plano_aula
INNER JOIN smc_turma ON turma_id = plano_aula_id_turma 
INNER JOIN smc_disciplina ON disciplina_id = plano_aula_id_disciplina
INNER JOIN smc_func ON func_id = plano_aula_id_professor
WHERE turma_id_escola = '$row_EscolaLogada[escola_id]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' 
AND (plano_aula_conteudo IS NOT NULL OR plano_aula_video IS NOT NULL OR plano_aula_atividade IS NOT NULL OR plano_aula_google_form IS NOT NULL) 
$profQry
ORDER BY plano_aula_id DESC
";
$query_limit_Aulas = sprintf("%s LIMIT %d, %d", $query_Aulas, $startRow_Aulas, $maxRows_Aulas);
$Aulas = mysql_query($query_limit_Aulas, $SmecelNovo) or die(mysql_error());
$row_Aulas = mysql_fetch_assoc($Aulas);

if (isset($_GET['totalRows_Aulas'])) {
  $totalRows_Aulas = $_GET['totalRows_Aulas'];
} else {
  $all_Aulas = mysql_query($query_Aulas);
  $totalRows_Aulas = mysql_num_rows($all_Aulas);
}
$totalPages_Aulas = ceil($totalRows_Aulas/$maxRows_Aulas)-1;

$queryString_Aulas = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_Aulas") == false && 
        stristr($param, "totalRows_Aulas") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_Aulas = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_Aulas = sprintf("&totalRows_Aulas=%d%s", $totalRows_Aulas, $queryString_Aulas);



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
</head>
  <body>
    <?php include_once ("menu-top.php"); ?>
          <?php include_once ("menu-esc.php"); ?>


    <main class="ls-main ">
      <div class="container-fluid">
 
        <h1 class="ls-title-intro ls-ico-home">Aulas virtuais</h1>
		<!-- CONTEÚDO -->
        <?php if ($totalRows_Aulas > 0) { // Show if recordset not empty ?>
		

		
  <p class="ls-box ls-txt-center ls-v-align-middle">
  <small>
  <i class="material-icons">import_contacts</i> CONTEÚDO PARA LER | 
  <i class="material-icons ls-color-warning">description</i> ATIVIDADE PROPOSTA | 
  <i class="material-icons ls-color-danger">ondemand_video</i> VÍDEO-AULA | 
  <i class="material-icons ls-color-success">attach_file</i> MATERIAL EM ANEXO | 
  <i class="material-icons ls-color-success">remove_red_eye</i> VERDE: AULA ESTÁ VISÍVEL AOS ALUNOS
  </small>
  
  </p>

<p><a class="ls-btn-primary" href="aulas_virtuais.php">VER TODAS AS AULAS</a></p>
  
  <ul class="ls-pager">
  <li class="<?php if ($pageNum_Aulas > 0) { } else { ?>ls-disabled<?php } ?>"><a href="<?php printf("%s?pageNum_Aulas=%d%s", $currentPage, 0, $queryString_Aulas); ?>">Primeira</a></li>
  <li class="<?php if ($pageNum_Aulas > 0) { } else { ?>ls-disabled<?php } ?>"><a href="<?php printf("%s?pageNum_Aulas=%d%s", $currentPage, max(0, $pageNum_Aulas - 1), $queryString_Aulas); ?>">Anterior</a></li>
  <li class="<?php if ($pageNum_Aulas < $totalPages_Aulas) { } else { ?>ls-disabled<?php } ?>"><a href="<?php printf("%s?pageNum_Aulas=%d%s", $currentPage, min($totalPages_Aulas, $pageNum_Aulas + 1), $queryString_Aulas); ?>">Próximo</a></li>
  <li class="<?php if ($pageNum_Aulas < $totalPages_Aulas) { } else { ?>ls-disabled<?php } ?>"><a href="<?php printf("%s?pageNum_Aulas=%d%s", $currentPage, $totalPages_Aulas, $queryString_Aulas); ?>">Última</a></li>
</ul>		
		
  <h4>Lista ordenada por data de cadastro</h4>
  <table class="ls-table ls-sm-space ls-bg-header ls-table-striped">
    <thead>
      <tr>
        <th class="ls-txt-center">IDENTIFICAÇÃO</th>
        <th class="ls-txt-center">AULAS CADASTRADAS</th>
        <th class="ls-txt-center">CONTEÚDO</th>
        <th width="120" class="ls-txt-center">OPÇÕES</th>
        </tr>
    </thead>
    <tbody>
      <?php do { ?>
	  
	    <?php 
   
		mysql_select_db($database_SmecelNovo, $SmecelNovo);
		$query_Anexos = "SELECT plano_aula_anexo_id, plano_aula_anexo_id_atividade, plano_aula_anexo_arquivo FROM smc_plano_aula_anexo WHERE plano_aula_anexo_id_atividade = '$row_Aulas[plano_aula_id]'";
		$Anexos = mysql_query($query_Anexos, $SmecelNovo) or die(mysql_error());
		$row_Anexos = mysql_fetch_assoc($Anexos);
		$totalRows_Anexos = mysql_num_rows($Anexos);
   
        ?>

	  
        <tr>
		
		<td>
		<div style="border-left:5px solid <?php echo $row_Aulas['disciplina_cor_fundo']; ?>; padding-left: 5px;">
		<?php echo inverteData($row_Aulas['plano_aula_data']); ?><br>
		<b style="color:<?php echo $row_Aulas['disciplina_cor_fundo']; ?>"><?php echo $row_Aulas['disciplina_nome']; ?></b><br>
		<a href="<?php echo $currentPage; ?>?professor=<?php echo $row_Aulas['func_id']; ?>"><?php echo $row_Aulas['func_nome']; ?></a>
		</div>
		</td>
		
		<td>
		<?php echo $row_Aulas['turma_nome']; ?><br>
		<?php echo $row_Aulas['plano_aula_texto']; ?>
		</td>
		
		<td class="ls-txt-center">
       <?php if ($row_Aulas['plano_aula_publicado']=="S") { ?><a href="aulas_virtuais_ver.php?aula=<?php echo $row_Aulas['plano_aula_hash']; ?>" class="ls-tooltip-left" aria-label="Aula publicada"><i class="material-icons ls-color-success">remove_red_eye</i></a><?php } else { ?><a href="aulas_virtuais_ver.php?aula=<?php echo $row_Aulas['plano_aula_hash']; ?>" class="ls-tooltip-left" aria-label="Aula aguardando publicação"><i class="material-icons ls-color-danger">remove_red_eye</i></a><?php } ?>
	   <?php if ($row_Aulas['plano_aula_conteudo']<>"") { ?><a class="ls-tooltip-left" aria-label="Conteúdo para ler" href="aulas_virtuais_ver.php?aula=<?php echo $row_Aulas['plano_aula_hash']; ?>"><i class="material-icons">import_contacts</i></a><?php } else { ?><i class="material-icons ls-transparent-25">import_contacts</i><?php } ?>
	   <?php if ($row_Aulas['plano_aula_atividade']<>"") { ?><a class="ls-tooltip-left" aria-label="Atividade proposta" href="aulas_virtuais_ver.php?aula=<?php echo $row_Aulas['plano_aula_hash']; ?>"><i class="material-icons ls-color-warning">description</i></a><?php } else { ?><i class="material-icons ls-transparent-25">description</i><?php } ?>
	   <?php if ($row_Aulas['plano_aula_video']<>"") { ?><a class="ls-tooltip-left" aria-label="Vídeo de apoio" href="aulas_virtuais_ver.php?aula=<?php echo $row_Aulas['plano_aula_hash']; ?>"><i class="material-icons ls-color-danger">ondemand_video</i></a><?php } else { ?><i class="material-icons ls-transparent-25">ondemand_video</i><?php } ?>
	   <?php if ($row_Aulas['plano_aula_google_form']<>"") { ?><a class="ls-tooltip-left" aria-label="Avaliação" href="aulas_virtuais_ver.php?aula=<?php echo $row_Aulas['plano_aula_hash']; ?>"><i class="material-icons ls-color-black">book</i></a><?php } else { ?><i class="material-icons ls-transparent-25">book</i><?php } ?>
	   <?php if ($totalRows_Anexos > 0) { ?><a class="ls-tooltip-left" aria-label="<?php echo $totalRows_Anexos; ?> anexo(s)" href="aulas_virtuais_ver.php?aula=<?php echo $row_Aulas['plano_aula_hash']; ?>"><i class="material-icons ls-color-info">attach_file</i></a><?php } else { ?><i class="material-icons ls-transparent-25">attach_file</i><?php } ?>
		</td>
		
	<td class="ls-txt-center">
       <a title="Visualizar" href="aulas_virtuais_ver.php?aula=<?php echo $row_Aulas['plano_aula_hash']; ?>" target="_blank" class="waves-effect waves-light btn-small"><i class="tiny material-icons">pageview</i></a>
       <a title="Imprimir" href="aulas_virtuais_ver_imprimir.php?aula=<?php echo $row_Aulas['plano_aula_hash']; ?>" target="_blank" class="waves-effect waves-light btn-small"><i class="tiny material-icons">local_printshop</i></a>
       <a title="Frequência" href="aulas_virtuais_frequencia.php?aula=<?php echo $row_Aulas['plano_aula_hash']; ?>" target="_blank" class="waves-effect waves-light btn-small"><i class="tiny material-icons">view_list</i></a>
	</td>
		
        </tr>
        <?php } while ($row_Aulas = mysql_fetch_assoc($Aulas)); ?>
    </tbody>
  </table>
  



<ul class="ls-pager">
  <li class="<?php if ($pageNum_Aulas > 0) { } else { ?>ls-disabled<?php } ?>"><a href="<?php printf("%s?pageNum_Aulas=%d%s", $currentPage, 0, $queryString_Aulas); ?>">Primeira</a></li>
  <li class="<?php if ($pageNum_Aulas > 0) { } else { ?>ls-disabled<?php } ?>"><a href="<?php printf("%s?pageNum_Aulas=%d%s", $currentPage, max(0, $pageNum_Aulas - 1), $queryString_Aulas); ?>">Anterior</a></li>
  <li class="<?php if ($pageNum_Aulas < $totalPages_Aulas) { } else { ?>ls-disabled<?php } ?>"><a href="<?php printf("%s?pageNum_Aulas=%d%s", $currentPage, min($totalPages_Aulas, $pageNum_Aulas + 1), $queryString_Aulas); ?>">Próximo</a></li>
  <li class="<?php if ($pageNum_Aulas < $totalPages_Aulas) { } else { ?>ls-disabled<?php } ?>"><a href="<?php printf("%s?pageNum_Aulas=%d%s", $currentPage, $totalPages_Aulas, $queryString_Aulas); ?>">Última</a></li>
</ul>

<hr>
  
  <?php } else { ?>
  
  <p>Nenhuma aula cadastrada.</p>
  
  <?php } // Show if recordset not empty ?>
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
mysql_free_result($Anexos);
mysql_free_result($Aulas);
mysql_free_result($EscolaLogada);
?>
