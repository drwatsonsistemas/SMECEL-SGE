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
	$codTurma = (int) $codTurma;
	$buscaTurma = "AND turma_id = $codTurma ";
}

if(isset($_GET['ano'])){
	$anoLetivo = anti_injection($_GET['ano']);
	$anoLetivo = (int) $anoLetivo;
} else {
	$anoLetivo = $row_AnoLetivo['ano_letivo_ano'];
}

$stCod = "";
$stqry = "";

if (isset($_GET['st'])) {
	$stCod = anti_injection($_GET['st']);
	$stCod = (int) $stCod;
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
	$st = (int) $st;
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
etapa_id, etapa_idade,
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno_nome, 
turma_total_alunos, turma_ano_letivo, turma_total_alunos, turma_ano_letivo, turma_multisseriada 
FROM smc_turma 
INNER JOIN smc_etapa ON etapa_id = turma_etapa
WHERE turma_id_escola = '$row_UsuLogado[usu_escola]' AND turma_ano_letivo = '$anoLetivo' $buscaTurma
ORDER BY turma_turno, turma_etapa, turma_nome ASC";
$ExibirTurmas = mysql_query($query_ExibirTurmas, $SmecelNovo) or die(mysql_error());
$row_ExibirTurmas = mysql_fetch_assoc($ExibirTurmas);
$totalRows_ExibirTurmas = mysql_num_rows($ExibirTurmas);

if ($totalRows_ExibirTurmas == "") {
	//echo "TURMA EM BRANCO";	
	header("Location: turmasAlunosVinculados.php?nada");
	exit;
}

function idade($idade, $anoLetivo)
{

	if ($idade <> "") {

		// Declara a data! :P
		$data = $idade;

		// Separa em dia, mês e ano
		list($ano, $mes, $dia) = explode('-', $data);

		// Descobre que dia é hoje e retorna a unix timestamp
		//$hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		$hoje = mktime(0, 0, 0, '03', '31', $anoLetivo);
		// Descobre a unix timestamp da data de nascimento do fulano
		$nascimento = mktime(0, 0, 0, $mes, $dia, $ano);

		// Depois apenas fazemos o cálculo já citado :)
		$idade = floor((((($hoje - $nascimento) / 60) / 60) / 24) / 365.25);

	} else {
		$idade = "-";
	}

	return $idade;

}

function calcularIdade($dataNascimento, $idade, $etapa, $anoLetivo)
{
	// Data base para cálculo da idade

	$idadeMesDia = date($anoLetivo . '-m-d', strtotime($dataNascimento));
	$dataBase = $anoLetivo . '-03-31';

	if ($idadeMesDia < $dataBase) {
		$idade + 1;
	} else {
		$idade;
	}

	if ($idade > $etapa) {
		$asterisco = "Acima";
	} else if ($idade < $etapa) {
		$asterisco = "Abaixo";
	} else {
		$asterisco = "-";
	}
	return $asterisco;
	// Converte a data de nascimento para segundos desde o Unix Epoch
	//$dataNascimento = strtotime($dataNascimento);

	// Calcula a diferença em segundos
	// $diferenca = $dataBase - $dataNascimento;

	// Converte a diferença de segundos para anos
	// $idade = floor($diferenca / (365 * 24 * 60 * 60));

}

?>
<!DOCTYPE html>
<html class="<?php echo $row_EscolaLogada['escola_tema']; ?>" lang="pt-br">

<head>
	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag() { dataLayer.push(arguments); }
		gtag('js', new Date());

		gtag('config', 'UA-117872281-1');
	</script>

	<title>Matrícula inicial | SMECEL - Sistema de Gestão Escolar</title>

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
		@media print {
			@page {
				size: landscape;
				/* Define a orientação como paisagem */
			}
		}


		table.bordasimples {
			border-collapse: collapse;
			font-size: 7px;
		}

		table.bordasimples tr td {
			border: 1px dotted #000000;
			padding: 2px;
			font-size: 9px;
		}

		table.bordasimples tr th {
			border: 1px dotted #000000;
			padding: 2px;
			font-size: 9px;
		}
	</style>


	<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
	<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>

<body onload="self.print();">




	<div class="container-fluid">



		<div class="ls-box">
			<span class="ls-float-left"
				style="margin-right:20px;"><?php if ($row_EscolaLogada['escola_logo'] <> "") { ?><img
						src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt=""
						width="100px" /><?php } else { ?><img src="../../img/brasao_republica.png" alt=""
						width="80px" /><?php } ?></span>
			<?php echo $row_EscolaLogada['escola_nome']; ?><br>
			<small>
				<?php echo $row_EscolaLogada['escola_endereco']; ?>,
				<?php echo $row_EscolaLogada['escola_num']; ?> -
				<?php echo $row_EscolaLogada['escola_bairro']; ?> -
				<?php echo $row_EscolaLogada['escola_cep']; ?><br>
				CNPJ:<?php echo $row_EscolaLogada['escola_cnpj']; ?>
				INEP:<?php echo $row_EscolaLogada['escola_inep']; ?><br>
				<?php echo $row_EscolaLogada['escola_telefone1']; ?>
				<?php echo $row_EscolaLogada['escola_telefone2']; ?> <?php echo $row_EscolaLogada['escola_email']; ?>
			</small>
		</div>

		<div class="ls-box ls-txt-center" style="text-transform: uppercase;">
			MATRÍCULA INICIAL - <?php echo $nomeFiltro; ?>
		</div>

		<!-- CONTEÚDO -->

		<?php $totalAlunosEscola = 0; ?>

		<?php do { ?>
			<?php


			mysql_select_db($database_SmecelNovo, $SmecelNovo);
			$query_ExibirAlunosVinculados = "
			SELECT 
			vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, vinculo_aluno_vacina_atualizada,
			vinculo_aluno_hash, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_repetente, vinculo_aluno_multietapa, aluno_nome_social, etapa_id, etapa_nome, etapa_nome_abrev,
			CASE vinculo_aluno_situacao
			WHEN 1 THEN 'MATRICULADO'
			WHEN 2 THEN '<span class=\"ls-color-danger\"><b>TRANSFERIDO</b></span>'
			WHEN 3 THEN '<span class=\"ls-color-danger\"><b>DESISTENTE</b></span>'
			WHEN 4 THEN '<span class=\"ls-color-danger\"><b>FALECIDO</b></span>'
			WHEN 5 THEN '<span class=\"ls-color-danger\"><b>OUTROS</b></span>'
			END AS vinculo_aluno_situacao_nome, 
			aluno_id, aluno_nome, aluno_nascimento, aluno_filiacao1, aluno_hash,aluno_sexo,aluno_municipio_nascimento,aluno_municipio_nascimento_ibge, aluno_cpf,aluno_nis,aluno_identidade,aluno_cpf_mae,aluno_rg_mae,aluno_sus,municipio_nome,municipio_cod_ibge,aluno_sus_mae,aluno_nis_mae,aluno_sus_pai,aluno_nis_pai,aluno_nome_responsavel_legal,aluno_cpf_responsavel_legal,aluno_filiacao2,aluno_cpf_pai,aluno_rg_pai,aluno_prof_mae,aluno_prof_pai
			FROM smc_vinculo_aluno 
			INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
			LEFT JOIN smc_etapa ON etapa_id = vinculo_aluno_multietapa  
			LEFT JOIN smc_municipio ON aluno_municipio_nascimento_ibge = municipio_cod_ibge
			WHERE vinculo_aluno_ano_letivo = '$anoLetivo' AND vinculo_aluno_id_turma = '$row_ExibirTurmas[turma_id]' $stqry
			ORDER BY aluno_nome ASC";
			$ExibirAlunosVinculados = mysql_query($query_ExibirAlunosVinculados, $SmecelNovo) or die(mysql_error());
			$row_ExibirAlunosVinculados = mysql_fetch_assoc($ExibirAlunosVinculados);
			$totalRows_ExibirAlunosVinculados = mysql_num_rows($ExibirAlunosVinculados);


			?>






			<?php if ($totalRows_ExibirAlunosVinculados > 0) { ?>

				<div class="ls-box ls-sm-space">

					<?php $contaAlunos = 1; ?>

					<h5 class="ls-title-5 ls-txt-center">
						<?php echo $row_ExibirTurmas['turma_nome']; ?> - <?php echo $row_ExibirTurmas['turma_turno_nome']; ?>
						(<?php echo $nomeFiltro; ?>)
						<!--<br><small>Idade da etapa: <?php echo $row_ExibirTurmas['etapa_idade']; ?> (até 31/03/<?= $row_AnoLetivo['ano_letivo_ano'] ?>)</small>-->
					</h5>

					<table class="ls-sm-space bordasimples" width="100%" style="font-size:10px;">
						<thead>
							<tr>
								<th width="2%" class="ls-txt-center">Nº</th>
								<th width="15%">ALUNO</th>
								<th width="10%" class="ls-txt-center">NASCIMENTO</th>
								<th width="5%" class="ls-txt-center">IDADE</th>
								<!--<th width="5%" class="ls-txt-center">DIST</th> -->
								<th width="5%" class="ls-txt-center">SEXO</th>
								<th width="5%" class="ls-txt-center">REPETENTE</th>
								<th width="7%" class="ls-txt-center">NATURALIDADE</th>
								<th width="8%" class="ls-txt-center">CARTÃO DE VACINA (S/N)</th>
								<th width="10%" class="ls-txt-center">CPF/RG/NIS - ALUNO</th>
								<th width="15%" class="ls-txt-center">MÃE OU RESPONSÁVEL</th>
								<th width="15%" class="ls-txt-center">PROFISSÃO</th>
								<th width="8%" class="ls-txt-center">CPF/RG/NIS - MÃE OU RESPONSÁVEL</th>
							</tr>
							
						<tbody>
							<?php do { ?>
								<?php
								$exibirNome = "";
								$exibirCPF = "";
								$exibirRG = "";
								$exibirNIS = "";
								$exibirSUS = "";
								$exibirProfissao = "";

								if ($row_ExibirAlunosVinculados['aluno_nome_responsavel_legal'] != '') {
									$exibirNome = $row_ExibirAlunosVinculados['aluno_nome_responsavel_legal'];
									$exibirCPF = $row_ExibirAlunosVinculados['aluno_cpf_responsavel_legal'];
								} else if ($row_ExibirAlunosVinculados['aluno_filiacao1'] != '') {
									$exibirNome = $row_ExibirAlunosVinculados['aluno_filiacao1'];
									$exibirCPF = $row_ExibirAlunosVinculados['aluno_cpf_mae'];
									$exibirRG = $row_ExibirAlunosVinculados['aluno_rg_mae'];
									$exibirNIS = $row_ExibirAlunosVinculados['aluno_nis_mae'];
									$exibirSUS = $row_ExibirAlunosVinculados['aluno_sus_mae'];
									$exibirProfissao = $row_ExibirAlunosVinculados['aluno_prof_mae'];
								} else {
									$exibirNome = $row_ExibirAlunosVinculados['aluno_filiacao2'];
									$exibirCPF = $row_ExibirAlunosVinculados['aluno_cpf_pai'];
									$exibirRG = $row_ExibirAlunosVinculados['aluno_rg_pai'];
									$exibirNIS = $row_ExibirAlunosVinculados['aluno_nis_pai'];
									$exibirSUS = $row_ExibirAlunosVinculados['aluno_sus_pai'];
									$exibirProfissao = $row_ExibirAlunosVinculados['aluno_prof_pai'];
								}
								?>
								<tr style="border-bottom:black solid 1 px;">
									<td class="ls-txt-center"><?php
									echo $contaAlunos;
									$contaAlunos++;
									?></td>
									<td><?php echo $row_ExibirAlunosVinculados['aluno_nome_social'] != "" ? $row_ExibirAlunosVinculados['aluno_nome_social'] : $row_ExibirAlunosVinculados['aluno_nome']; ?></td>
									<td class="ls-txt-center">
										<?php echo inverteData($row_ExibirAlunosVinculados['aluno_nascimento']); ?></td>
									<td class="ls-txt-center"><?php
									$idade = idade($row_ExibirAlunosVinculados['aluno_nascimento'], $row_AnoLetivo['ano_letivo_ano']);
									echo $idade;
									?></td>
									<!--<td class="ls-txt-center"><?php
									//echo calcularIdade($row_ExibirAlunosVinculados['aluno_nascimento'],$idade,$row_ExibirTurmas['etapa_idade'],$row_AnoLetivo['ano_letivo_ano']);
									?></td>-->

									<td class="ls-txt-center">
										<?php echo $row_ExibirAlunosVinculados['aluno_sexo'] == 1 ? 'M' : ($row_ExibirAlunosVinculados['aluno_sexo'] == 2 ? 'F' : ''); ?>
									</td>
									<td class="ls-txt-center"><?php echo $row_ExibirAlunosVinculados['vinculo_aluno_repetente']; ?>
									</td>
									<td class="ls-txt-center"><?php echo $row_ExibirAlunosVinculados['municipio_nome']; ?></td>
									<td class="ls-txt-center">
										<?php echo $row_ExibirAlunosVinculados['vinculo_aluno_vacina_atualizada'] == 'I' ? 'N' : $row_ExibirAlunosVinculados['vinculo_aluno_vacina_atualizada']; ?>
									</td>
									<td class="">
										<?php
										echo "CPF: " . $row_ExibirAlunosVinculados['aluno_cpf'] . "<br>" . "RG: " . $row_ExibirAlunosVinculados['aluno_identidade'] . "<br>" . "NIS: " . $row_ExibirAlunosVinculados['aluno_nis'] . "<br>" . "SUS: " . $row_ExibirAlunosVinculados['aluno_sus'];
										?>
									</td>
									<td class="ls-txt-center"><?php echo $exibirNome; ?></td>
									<td class="ls-txt-center"><?php echo $exibirProfissao; ?></td>
									<td class="">
										<?php
										echo "CPF: " . $exibirCPF . "<br>" . "RG: " . $exibirRG . "<br>" . "NIS: " . $exibirNIS . "<br>" . "SUS: " . $exibirSUS;
										?>
									</td>
								</tr>
							<?php } while ($row_ExibirAlunosVinculados = mysql_fetch_assoc($ExibirAlunosVinculados)); ?>
						</tbody>
					</table>
					<p>Alunos vinculados na turma: <strong><?php echo $totalRows_ExibirAlunosVinculados; ?></strong>

				</div>


			<?php } ?>


			<?php $totalAlunosEscola = $totalAlunosEscola + $totalRows_ExibirAlunosVinculados; ?>
			
		<?php } while ($row_ExibirTurmas = mysql_fetch_assoc($ExibirTurmas)); ?>




		<small>Relatório impresso em <?php echo date('d/m/Y'); ?> às <?php echo date('H:i:s'); ?>. <br>SMECEL - Sistema
			de Gestão Escolar</i></small>



		<!-- CONTEÚDO -->
	</div>


	<!-- We recommended use jQuery 1.10 or up -->
	<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
	<script src="js/locastyle.js"></script>


	<script language="Javascript">
		function confirmaExclusao(id) {
			var resposta = confirm("Deseja realmente excluir o vínculo deste aluno?");
			if (resposta == true) {
				window.location.href = "matriculaExcluir.php?hash=" + id;
			}
		}
	</script>

</body>

</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($ExibirTurmas);

mysql_free_result($ExibirAlunosVinculados);
?>