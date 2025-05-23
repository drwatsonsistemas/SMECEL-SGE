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
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, sec_id, sec_cidade, sec_uf, sec_logo, escola_tema 
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

<title><?php echo $row_Aulas['turma_nome']; ?> | <?php echo $row_Aulas['disciplina_nome']; ?> | <?php echo $row_Aulas['func_nome']; ?> | <?php echo $row_Aulas['plano_aula_texto']; ?> - SMECEL</title>
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
	border:1px solid #ccc;
}
th, td {
	padding:10px;
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
  <body onload="self.print();">

    <main class="1ls-main">
      <div class="1container-fluid">
 
		<!-- CONTEÚDO -->
        



<table class="ls-sm-space bordasimples" width="100%" style="font-size:12px;">
<tbody>
<tr>
	<td width="100">
		<?php if ($row_EscolaLogada['escola_logo']<>"") { ?><img src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt="" width="100px" /><?php } else { ?><img src="../../img/brasao_republica.png" alt="" width="80px" /><?php } ?>
	</td>
	<td class="ls-txt-left">	
		<h2 class="ls-txt-center"><?php echo $row_EscolaLogada['escola_nome']; ?></h2>
		<br>
		<strong>DATA:</strong> <?php echo inverteData($row_Aulas['plano_aula_data']); ?> | <strong>UNIDADE:</strong>_______<br>
		<strong>PROFESSOR(A):</strong> <?php echo $row_Aulas['func_nome']; ?> <br>
		<strong>TURMA:</strong> <?php echo $row_Aulas['turma_nome']; ?>	
	</td>
	<td width="100">
	
              	<?php if ($row_EscolaLogada['sec_logo'] <> "") { ?>
				  <img src="../../img/logo/secretaria/<?php echo $row_EscolaLogada['sec_logo']; ?>" alt="" title=""  width="80px" />
				<?php } else { ?>
				  <img src="../../img/brasao_republica.png" width="80px">
				<?php } ?>	

	</td>
</tr>
<tr>
	<th colspan="3" style="background-color:#FFFFF0">
		<h2>ATIVIDADE DE <?php echo $row_Aulas['disciplina_nome']; ?></h2>
	</th>
</tr>


<tr>
	<td colspan="3">
	<h2 class="ls-txt-center"><?php echo $row_Aulas['plano_aula_texto']; ?></h2>
	
			<div class="leitura">
			<p>
			<?php echo $row_Aulas['plano_aula_conteudo']; ?>
            </p>
            
			</div>
	
	</td>
</tr>

<?php if ($row_Aulas['plano_aula_atividade']<>"") { ?>

<tr>
	<th colspan="3"  style="background-color:#FFFFF0">
		<h2>ATIVIDADE PROPOSTA</h2>
	</th>
</tr>

<tr>
	<td colspan="3">
	
	
	<div class="leitura">

	  <p class="flow-text"><?php echo $row_Aulas['plano_aula_atividade']; ?></p>
	  
            
	</div>
	
	</td>
</tr>
 <?php } ?>

	



</tbody>
</table>

	  <p class="ls-txt-center">
	  <small>Relatório impresso em <?php echo date('d/m/Y'); ?> às <?php echo date('H:i:s'); ?>. <br>SMECEL - Sistema de Gestão Escolar</i></small>
	  </p>	 
        
		

      </div>
    </main>

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
