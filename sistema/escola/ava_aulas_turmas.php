<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "../funcoes/funcoes.php"; ?>
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


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ListaVinculos = "
SELECT 
vinculo_id, vinculo_id_escola, vinculo_id_funcionario, vinculo_id_funcao, vinculo_carga_horaria, DATE_FORMAT(vinculo_data_inicio, '%d/%m/%Y') AS vinculo_data_inicio, vinculo_obs, 
func_id, func_nome, funcao_id, funcao_nome, funcao_docencia, func_regime, func_senha_ativa 
FROM smc_vinculo 
INNER JOIN smc_func 
ON func_id = vinculo_id_funcionario 
INNER JOIN smc_funcao
ON funcao_id = vinculo_id_funcao 
WHERE vinculo_id_escola = '$row_EscolaLogada[escola_id]' AND funcao_docencia = 'S'
ORDER BY func_nome ASC
";
$ListaVinculos = mysql_query($query_ListaVinculos, $SmecelNovo) or die(mysql_error());
$row_ListaVinculos = mysql_fetch_assoc($ListaVinculos);
$totalRows_ListaVinculos = mysql_num_rows($ListaVinculos);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turmas = "
SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, 
turma_turno,
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MAT'
WHEN 2 THEN 'VESP'
WHEN 3 THEN 'NOT'
END AS turma_turno_nome,
turma_total_alunos, turma_ano_letivo 
FROM smc_turma 
WHERE turma_id_escola = '$row_EscolaLogada[escola_id]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' 
ORDER BY turma_turno, turma_etapa, turma_nome ASC
";
$Turmas = mysql_query($query_Turmas, $SmecelNovo) or die(mysql_error());
$row_Turmas = mysql_fetch_assoc($Turmas);
$totalRows_Turmas = mysql_num_rows($Turmas);



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

<title>Aulas por turma</title>

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

	<h1 class="ls-title-intro ls-ico-home">Total de aulas por turma</h1>
	


	
	


	<div class="ls-box ls-sm-space">
	
	<?php if ( $totalRows_Turmas > 0 ) { ?>
	<table class="ls-table ls-table-striped ls-sm-space fonte-tabela" role="grid">
	  <thead>
	  <tr>
		<th width="40px" class="ls-txt-center">Nº</th>
		<th width="20px" class=""></th>
		<th>TURMA</th>
		<th>TURNO</th>
		<th width="120px" class="ls-txt-center">TOTAL</th>
		<th width="120px" class="ls-txt-center">VISUALIZAR</th>
		<th width="120px" class="ls-txt-center">CONTEÚDO</th>
		<th width="120px" class="ls-txt-center">RENDIMENTO</th>
		<th width="120px" class="ls-txt-center">FREQUÊNCIA</th>
		<th width="120px" class="ls-txt-center">DIÁRIO</th>
	  </tr>
	  </thead>
	   <tbody>
	   <?php
		$semAulas = 0;
		$totalAulas = 0;
		$contagem = 1;
	   do { 
	   ?>
	   
	   <?php 
	   
	   
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AulasCadastradas = "
SELECT 
plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, plano_aula_data,
plano_aula_data_cadastro, plano_aula_texto, plano_aula_conteudo, plano_aula_atividade, 
plano_aula_atividade_resposta_obrigatoria, plano_aula_atividade_resposta_obrigatoria_data_limite, 
plano_aula_video, plano_aula_publicado, plano_aula_hash, 
turma_id, turma_id_escola 
FROM smc_plano_aula
INNER JOIN smc_turma ON turma_id = plano_aula_id_turma
WHERE turma_id_escola = '$row_EscolaLogada[escola_id]' AND plano_aula_id_turma = '$row_Turmas[turma_id]' 
";
$AulasCadastradas = mysql_query($query_AulasCadastradas, $SmecelNovo) or die(mysql_error());
$row_AulasCadastradas = mysql_fetch_assoc($AulasCadastradas);
$totalRows_AulasCadastradas = mysql_num_rows($AulasCadastradas);
	   
	   ?>
	   
		<tr>
		  <td class="ls-txt-center">
		  <?php 
		  echo $contagem;
		  $contagem++;
		  ?></td>
		  <td class=""></td>
		  <td><?php echo $row_Turmas['turma_nome']; ?></td>
		  <td><?php echo $row_Turmas['turma_turno_nome']; ?></td>
		  <td class="ls-txt-center">
		  <?php if ($totalRows_AulasCadastradas > 0) { ?><?php echo $totalRows_AulasCadastradas; ?><?php $totalAulas = $totalAulas + $totalRows_AulasCadastradas; ?></span><?php } else { ?><span class="ls-ico-cancel-circle ls-color-danger"><?php $semAulas++; ?></span><?php } ?>
		  </td>
		  <td class="ls-txt-center"><a href="ava_aulas_por_turma.php?turma=<?php echo $row_Turmas['turma_id']; ?>"><span class="ls-ico-eye ls-ico-right"></span></a></td>
		  <td class="ls-txt-center"><a href="diario_turma.php?turma=<?php echo $row_Turmas['turma_id']; ?>"><span class="ls-ico-paint-format ls-ico-right"></span></a></td>
		  <td class="ls-txt-center"><a target="_blank" href="diario_rendimento.php?ct=<?php echo $row_Turmas['turma_id']; ?>"><span class="ls-ico-paint-format ls-ico-right"></span></a></td>
		  <td class="ls-txt-center"><a target="_blank" href="diario_frequencia.php?ct=<?php echo $row_Turmas['turma_id']; ?>"><span class="ls-ico-paint-format ls-ico-right"></span></a></td>
		  <td class="ls-txt-center"><a target="_blank" href="print_diario.php?turma=<?php echo $row_Turmas['turma_id']; ?>"><span class="ls-ico-paint-format ls-ico-right"></span></a></td>
		</tr>
		<?php } while ($row_Turmas = mysql_fetch_assoc($Turmas)); ?>
		</tbody>
	</table>
	
	</div>
			
	<p class="ls-txt-right">Total de turmas: <strong><?php echo $totalRows_Turmas; ?></strong></p>
	<p class="ls-txt-right">Total de aulas postadas:<strong> <?php echo $totalAulas; ?></strong></p>
	
	<?php } else { ?>
	<br>
	<p><div class="ls-alert-info"><strong>Atenção:</strong> Nenhum funcionário vinculado nessa escola.</div></p>
	<?php } ?>	
	 
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

mysql_free_result($Turmas);

mysql_free_result($AulasCadastradas);

mysql_free_result($EscolaLogada);

mysql_free_result($ListaVinculos);
?>
