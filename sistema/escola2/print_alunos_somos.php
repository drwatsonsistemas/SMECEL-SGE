<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include('fnc/inverteData.php'); ?>
<?php include "fnc/anti_injection.php"; ?>
<?php include "fnc/dataLocal.php"; ?>


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


$codTurma = "";
$buscaTurma = "";
if (isset($_GET['ct'])) {
	
	if ($_GET['ct'] == "") {
	//echo "TURMA EM BRANCO";	
		header("Location: turmasAlunosVinculados.php?nada"); 
		exit;
	}
	
	$codTurma = anti_injection($_GET['ct']);
	$codTurma = (int)$codTurma;
	$buscaTurma = "AND turma_id = $codTurma ";
}

$stCod = "";
$stqry = "";

if (isset($_GET['st'])) {	
	$stCod = anti_injection($_GET['st']);
	$stCod = (int)$stCod;
}

	//$st = "1";
	//$stqry = "AND vinculo_aluno_situacao = $st ";
if (isset($_GET['st'])) {
	
	if ($_GET['st'] == "") {
	//echo "TURMA EM BRANCO";	
		header("Location: turmasAlunosVinculados.php?nada"); 
		exit;
	}
	
	$st = anti_injection($_GET['st']);
	$st = (int)$st;
	$stqry = "AND vinculo_aluno_situacao = $st ";
}

$nomeFiltro = "Todos";
if (isset($_GET['st'])) {
	switch ($_GET['st']) {
		case 1:
		$nomeFiltro = "Matriculados";
		break;
		case 2:
		$nomeFiltro = "Transferidos";
		break;
		case 3:
		$nomeFiltro = "Desistentes";
		break;
		case 4:
		$nomeFiltro = "Falecidos";
		break;
		case 5:
		$nomeFiltro = "Outros";
		break;
		default:
		echo "Todos";
	}	
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ExibirTurmas = "
SELECT turma_id, turma_id_escola, turma_nome, turma_etapa, turma_turno,
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno_nome, 
turma_total_alunos, turma_ano_letivo, turma_total_alunos, turma_ano_letivo, turma_multisseriada 
FROM smc_turma 
WHERE turma_id_escola = '$row_UsuLogado[usu_escola]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' $buscaTurma
ORDER BY turma_turno, turma_etapa, turma_nome ASC";
$ExibirTurmas = mysql_query($query_ExibirTurmas, $SmecelNovo) or die(mysql_error());
$row_ExibirTurmas = mysql_fetch_assoc($ExibirTurmas);
$totalRows_ExibirTurmas = mysql_num_rows($ExibirTurmas);

if ($totalRows_ExibirTurmas == "") {
	//echo "TURMA EM BRANCO";	
	header("Location: turmasAlunosVinculados.php?nada"); 
	exit;
}


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

	<title>Todos os alunos | SMECEL - Sistema de Gestão Escolar</title>

	<meta charset="utf-8">
	<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<meta name="description" content="">
	<meta name="keywords" content="">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<link rel="stylesheet" type="text/css" href="css/locastyle.css">
	<script src="js/locastyle.js"></script>
	
	<style>

		table.bordasimples {border-collapse: collapse; font-size:7px; }
		table.bordasimples tr td {border:1px dotted #000000; padding:2px; font-size:9px;}
		table.bordasimples tr th {border:1px dotted #000000; padding:2px; font-size:9px;}

	</style>
	
	
	<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
	<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<body onload="1self.print();">




	<div class="container-fluid">



		<div class="ls-box">
			<span class="ls-float-left" style="margin-right:20px;"><?php if ($row_EscolaLogada['escola_logo']<>"") { ?><img src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt="" width="100px" /><?php } else { ?><img src="../../img/brasao_republica.png" alt="" width="80px" /><?php } ?></span>
			<?php echo $row_EscolaLogada['escola_nome']; ?><br>
			<small>
				<?php echo $row_EscolaLogada['escola_endereco']; ?>, 
				<?php echo $row_EscolaLogada['escola_num']; ?> - 
				<?php echo $row_EscolaLogada['escola_bairro']; ?> - 
				<?php echo $row_EscolaLogada['escola_cep']; ?><br>
				CNPJ:<?php echo $row_EscolaLogada['escola_cnpj']; ?> INEP:<?php echo $row_EscolaLogada['escola_inep']; ?><br>
				<?php echo $row_EscolaLogada['escola_telefone1']; ?> <?php echo $row_EscolaLogada['escola_telefone2']; ?> <?php echo $row_EscolaLogada['escola_email']; ?>
			</small>
		</div>

		<div class="ls-box ls-txt-center" style="text-transform: uppercase;">
			RELATÓRIO DE EXPORTAÇÃO DE ALUNOS PARA A SOMUS
		</div>
		
		<!-- CONTEÚDO -->

		<?php $totalAlunosEscola = 0; ?>


		<?php


		mysql_select_db($database_SmecelNovo, $SmecelNovo);
		$query_ExibirAlunosVinculados = "
		SELECT 
		vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, 
		vinculo_aluno_hash, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_multietapa, etapa_id, etapa_nome, etapa_nome_abrev, etapa_ano_ef, vinculo_aluno_situacao
		aluno_id, aluno_nome, aluno_nascimento, aluno_email, aluno_filiacao1, aluno_filiacao2, aluno_hash, aluno_nis, turma_id, turma_nome, turma_etapa, turma_tipo_atendimento,
		CASE etapa_ano_ef 
		WHEN 1 THEN 'Ensino Fundamental 1'
		WHEN 2 THEN 'Ensino Fundamental 1'
		WHEN 3 THEN 'Ensino Fundamental 1'
		WHEN 4 THEN 'Ensino Fundamental 1'
		WHEN 5 THEN 'Ensino Fundamental 1'
		WHEN 6 THEN 'Ensino Fundamental 2'
		WHEN 7 THEN 'Ensino Fundamental 2'
		WHEN 8 THEN 'Ensino Fundamental 2'
		WHEN 9 THEN 'Ensino Fundamental 2'
		END AS segmento
		FROM smc_vinculo_aluno 
		INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
		INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma  
		INNER JOIN smc_etapa ON etapa_id = turma_etapa
		WHERE etapa_ano_ef IN (3,4,5,6,7,8,9) AND vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND vinculo_aluno_id_escola = '$row_UsuLogado[usu_escola]' AND vinculo_aluno_situacao = '1' AND turma_tipo_atendimento = '1'
		ORDER BY etapa_ano_ef ASC";
		$ExibirAlunosVinculados = mysql_query($query_ExibirAlunosVinculados, $SmecelNovo) or die(mysql_error());
		$row_ExibirAlunosVinculados = mysql_fetch_assoc($ExibirAlunosVinculados);
		$totalRows_ExibirAlunosVinculados = mysql_num_rows($ExibirAlunosVinculados);
		?>

		




		<?php if ($totalRows_ExibirAlunosVinculados > 0) { ?>

			<div class="ls-box ls-sm-space">

				<?php $contaAlunos = 1; ?>



				<table class="ls-sm-space bordasimples" width="100%" style="font-size:10px;">
					<thead>
						<tr>
							<th width="" class="ls-txt-center">SEGMENTO</th>
							<th width="" class="ls-txt-center">ANO/SERIE</th>
							<th width="" class="ls-txt-center">TURMA</th>
							<th width="" class="ls-txt-center">NOME DO ALUNO</th>
							<th width="" class="ls-txt-center">MATRÍCULA</th>
							<th width="" class="ls-txt-center">EMAIL ALUNO</th>
							<th width="" class="ls-txt-center">RESPONSAVEL 1</th>
							<th width="" class="ls-txt-center">EMAIL RESPONSAVEL 1</th>
							<th width="" class="ls-txt-center">RESPONSAVEL 2</th>
							<th width="" class="ls-txt-center">EMAIL RESPONSAVEL 2</th>


						</tr>
						<tbody>
							<?php do { ?>
							<tr style="border-bottom:black solid 1 px;">

								<td class="ls-txt-center"><?php echo $row_ExibirAlunosVinculados['segmento']; ?></td>
								<td><?php echo $row_ExibirAlunosVinculados['etapa_ano_ef']; ?>º Ano</td> 
								<td class="ls-txt-center"><?php echo $row_ExibirAlunosVinculados['turma_nome']; ?></td>
								<td class="ls-txt-left"><?php echo $row_ExibirAlunosVinculados['aluno_nome']; ?></td>
								<td class="ls-txt-center"><?php echo $row_ExibirAlunosVinculados['vinculo_aluno_id']; ?></td>
								<td class="ls-txt-center"><?php echo $row_ExibirAlunosVinculados['aluno_email']; ?></td>
								<td class="ls-txt-left"></td>
								<td class="ls-txt-center"></td>
								<td class="ls-txt-left"></td>
								<td class="ls-txt-center"></td>

							</tr>
						<?php } while ($row_ExibirAlunosVinculados = mysql_fetch_assoc($ExibirAlunosVinculados)); ?>
					</tbody>
				</table>

			</div>


		<?php } ?>


		<?php $totalAlunosEscola = $totalAlunosEscola + $totalRows_ExibirAlunosVinculados; ?> 





		<?php if ($codTurma == "") { ?>
			<div class="ls-box ls-box-gray">
				<p>Total de alunos vinculados na escola: <strong><?php echo $totalAlunosEscola; ?></strong></p>
			</div>
		<?php } ?>

		<small>Relatório impresso em <?php echo date('d/m/Y'); ?> às <?php echo date('H:i:s'); ?>. <br>SMECEL - Sistema de Gestão Escolar</i></small>



		<!-- CONTEÚDO -->
	</div>


	<!-- We recommended use jQuery 1.10 or up -->
	<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
	<script src="js/locastyle.js"></script>


</body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($ExibirTurmas);

mysql_free_result($ExibirAlunosVinculados);
?>
