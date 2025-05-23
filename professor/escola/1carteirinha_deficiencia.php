<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include('fnc/inverteData.php'); ?>
<?php include "fnc/anti_injection.php"; ?>
<?php include('../funcoes/url_base.php'); ?>



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
if (isset($_GET['st'])) {	
	$stCod = anti_injection($_GET['st']);
	$stCod = (int)$stCod;
}

$st = "1";
$stqry = "AND vinculo_aluno_situacao = $st ";
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
$nomeFiltro = "Matriculados";
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
		echo "Matriculados";
	}	
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ExibirTurmas = "
SELECT turma_id, turma_id_escola, turma_nome, turma_etapa, turma_tipo_atendimento,
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno, 
turma_total_alunos, turma_ano_letivo 
FROM smc_turma 
WHERE turma_id_escola = '$row_UsuLogado[usu_escola]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND turma_tipo_atendimento = 1 $buscaTurma
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

	<title>Carteirinha do Aluno | SMECEL - Sistema de Gestão Escolar</title>

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
		html{
			-webkit-print-color-adjust: exact;
		}
		body {font-size:8px;}
		#quebra {
			page-break-before: always;
		}

		.container {
			display: inline-block;
			flex-wrap: wrap;
			max-width: 100%;
			box-sizing: border-box;
			justify-content: flex-start; /* Certifique-se de que os itens comecem à esquerda */
			float: left;

		}

		.background-image {
			position: relative; /* Define o contexto para o posicionamento absoluto dos elementos filhos */
			background-image: url('<?php echo URL_BASE.'img/3.png'; ?>');
			background-size: 100% 100%;
			background-position: center;
			background-repeat: no-repeat;
			width: 54mm; /* Largura fixa */
			height: 86mm; /* Altura fixa */
			box-sizing: border-box;
			float: left;
		}

		.logo {
			position: absolute; /* Posiciona o elemento em relação ao contêiner pai */
			bottom: 0; /* Alinha ao fundo do contêiner */
			left: 50%; /* Centraliza horizontalmente */
			transform: translateX(-50%); /* Ajusta a posição para realmente centralizar */
			width: 18mm; /* Ajuste conforme necessário */
			height: auto; /* Mantém a proporção da logo */
			text-align: center; /* Centraliza o texto ou o conteúdo se necessário */
			float: left;

		}

		.logo img {
			max-width: 100%; /* Ajusta a imagem para caber dentro da logo */
			height: auto; /* Mantém a proporção da imagem */
			float: left;

		}

		.photo {
			position: absolute; /* Posiciona o elemento em relação ao contêiner pai */
			top: 35%; /* Alinha verticalmente mais para o centro */
			left: 18%; /* Posiciona horizontalmente para o lado esquerdo, ajustável conforme necessário */
			transform: translate(-50%, -50%); /* Ajusta a posição para centrar verticalmente e horizontalmente */
			width: 18mm; /* Ajuste conforme necessário */
			height: auto; /* Mantém a proporção da foto */
			float: left;

		}

		.photo img {
			max-width: 100%; /* Ajusta a imagem para caber dentro do contêiner */
			height: auto; /* Mantém a proporção da imagem */
			float: left;

		}

		.qr-code {
			position: absolute; /* Posiciona o elemento em relação ao contêiner pai */
			top: 73%; /* Alinha verticalmente mais para o centro */
			left: 78.5%; /* Posiciona horizontalmente para o lado esquerdo, ajustável conforme necessário */
			transform: translate(-50%, -50%); /* Ajusta a posição para centrar verticalmente e horizontalmente */
			width: 18mm; /* Ajuste conforme necessário */
			height: auto; /* Mantém a proporção da foto */
		}

		.text-container {
			position: absolute; /* Posiciona o elemento em relação ao contêiner pai */
			top: 33%; /* Alinha verticalmente com a foto */
			left: calc(18% + 7mm); /* Posiciona horizontalmente ao lado da foto */
			transform: translateY(-50%); /* Ajusta a posição para centrar verticalmente em relação à foto */
			width: calc(54mm - 14mm - 8mm); /* Ajuste conforme necessário para caber ao lado da foto */
			padding-left: 10px; /* Espaço entre a foto e o texto */
			box-sizing: border-box;
			font-size: 6px !important; /* Força o tamanho do texto */
			line-height: 1.2; /* Ajusta a altura da linha se necessário */
		}

		.text-container p {
			margin: 0; /* Remove margens padrão dos parágrafos */
			padding: 0; /* Remove padding padrão dos parágrafos */
		}

		.text-container-sanguineo {
			position: absolute; /* Posiciona o elemento em relação ao contêiner pai */
			top: 37%; /* Alinha verticalmente com a foto */
			left: calc(18% + 28mm); /* Posiciona horizontalmente ao lado da foto */
			transform: translateY(-50%); /* Ajusta a posição para centrar verticalmente em relação à foto */
			width: calc(54mm - 14mm - 8mm); /* Ajuste conforme necessário para caber ao lado da foto */
			padding-left: 10px; /* Espaço entre a foto e o texto */
			box-sizing: border-box;
			font-size: 5px !important; /* Força o tamanho do texto */
			line-height: 1.2; /* Ajusta a altura da linha se necessário */
		}

		.text-container p {
			margin: 0; /* Remove margens padrão dos parágrafos */
			padding: 0; /* Remove padding padrão dos parágrafos */
		}

		.text-container-filiacao {
			position: absolute; /* Posiciona o elemento em relação ao contêiner pai */
			top: 58%; /* Alinha verticalmente com a foto */
			left: -2%; /* Posiciona horizontalmente ao lado da foto */
			transform: translateY(-50%); /* Ajusta a posição para centrar verticalmente em relação à foto */
			
			padding-left: 10px; /* Espaço entre a foto e o texto */
			box-sizing: border-box;
			font-size: 6px !important; /* Força o tamanho do texto */
			line-height: 1.2; /* Ajusta a altura da linha se necessário */
		}

		.text-container-filiacao p {
			margin: 0; /* Remove margens padrão dos parágrafos */
			padding: 0; /* Remove padding padrão dos parágrafos */
		}

	</style>
	
	
	<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
	<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<body onload="self.print();">


	<?php do { ?>
		
		<?php 
		mysql_select_db($database_SmecelNovo, $SmecelNovo);
		$query_ExibirAlunosVinculados = "
		SELECT 
		vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, 
		vinculo_aluno_hash, vinculo_aluno_boletim, vinculo_aluno_situacao, 
		aluno_id, aluno_nome, aluno_nascimento, aluno_filiacao1, aluno_hash, aluno_foto, aluno_filiacao2, aluno_laudo, aluno_aluno_com_deficiencia,aluno_identidade,aluno_cpf,aluno_def_autista,aluno_cid,aluno_hash,aluno_tel_pai,aluno_tel_mae,aluno_sangue_tipo,aluno_sangue_rh
		FROM smc_vinculo_aluno 
		INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
		WHERE vinculo_aluno_id_turma = '$row_ExibirTurmas[turma_id]' AND aluno_aluno_com_deficiencia = 1 $stqry
		GROUP BY aluno_id
		ORDER BY aluno_nome ASC";
		$ExibirAlunosVinculados = mysql_query($query_ExibirAlunosVinculados, $SmecelNovo) or die(mysql_error());
		$row_ExibirAlunosVinculados = mysql_fetch_assoc($ExibirAlunosVinculados);
		$totalRows_ExibirAlunosVinculados = mysql_num_rows($ExibirAlunosVinculados);

//		WHERE vinculo_aluno_id_turma = '$row_ExibirTurmas[turma_id]' AND aluno_aluno_com_deficiencia = 1 AND aluno_laudo = 1 AND aluno_def_autista = 1 $stqry
		
		?>

		<?php if ($totalRows_ExibirAlunosVinculados > 0) { ?>
			<div class="container">
				<?php 
				$first_iteration = true;
				do { 
					$aux = 'fnc/qr/php/qr_img.php?';
					$aux .= 'd='.URL_BASE.'/publico/aluno_cid.php?h='.$row_ExibirAlunosVinculados['aluno_hash'];
					?>
					<div class="background-image" style="float: left; margin: 10px;">
						<div class="logo">
							<img src="<?php echo URL_BASE.'img/logo_smecel_background.png' ?>" alt="Logo">
						</div>
						<div class="photo">
							<?php if($row_ExibirAlunosVinculados['aluno_foto'] == "") { ?>
								<img src="<?php echo URL_BASE.'aluno/fotos/' ?>semfoto.jpg" style="margin:1mm;width:15mm;">
							<?php } else { ?>
								<img src="<?php echo URL_BASE.'aluno/fotos/' ?><?php echo $row_ExibirAlunosVinculados['aluno_foto']; ?>" style="margin:1mm;width:15mm;">
							<?php } ?>  
						</div>
						<div class="text-container">
							<p style="font-size:8px; color: #0F338B;font-weight: bold;">Aluno(a): <?php echo $row_ExibirAlunosVinculados['aluno_nome']; ?></p>
							<p style="font-size:7px; color: #0F338B;font-weight: bold;">CID: <?php echo $row_ExibirAlunosVinculados['aluno_cid']; ?></p>
							<p style="font-size:7px; color: #0F338B;font-weight: bold;">RG: <?php echo $row_ExibirAlunosVinculados['aluno_identidade']; ?></p>
							<p style="font-size:7px; color: #0F338B;font-weight: bold;">CPF: <?php echo $row_ExibirAlunosVinculados['aluno_cpf']; ?></p>
							<p style="font-size:7px; color: #0F338B;font-weight: bold;">NASCIMENTO:<br><?php echo inverteData($row_ExibirAlunosVinculados['aluno_nascimento']); ?></p>
						</div>
						<div class="text-container-sanguineo" style="font-weight:bold;color: white">
							<p style="font-size:8px"><?php echo $row_ExibirAlunosVinculados['aluno_sangue_tipo'].$row_ExibirAlunosVinculados['aluno_sangue_rh']; ?></p>
						</div>
						<div class="text-container-filiacao">
							<p style="font-size:8px; color: #0F338B;font-weight: bold;">Filiação: <br><?php echo $row_ExibirAlunosVinculados['aluno_filiacao1']; ?><br><?php echo $row_ExibirAlunosVinculados['aluno_filiacao2']; ?></p>
							<br>
							<p style="font-size:8px; color: #0F338B;font-weight: bold;">Contato: <br><?php echo $row_ExibirAlunosVinculados['aluno_tel_mae']; ?><br><?php echo $row_ExibirAlunosVinculados['aluno_tel_pai']; ?></p>
						</div>
						<div class="qr-code">
							<div class="ls-txt-center" style="color:white;font-weight: bold;">
								<img src="<?php echo $aux; ?>" />
								<br>ID <?php echo $row_ExibirAlunosVinculados['vinculo_aluno_id_aluno']; ?>
							</div>
						</div>

					</div>
					<?php 
					$first_iteration = false; 
				} while ($row_ExibirAlunosVinculados = mysql_fetch_assoc($ExibirAlunosVinculados)); 
				?>
			</div>



		<?php } ?>

	<?php } while ($row_ExibirTurmas = mysql_fetch_assoc($ExibirTurmas)); ?>

	<!-- CONTEÚDO -->

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
