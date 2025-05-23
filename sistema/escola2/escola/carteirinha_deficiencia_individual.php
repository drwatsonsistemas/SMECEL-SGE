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

$colname_Hash = "-1";
if (isset($_GET['hash'])) {
	$colname_Hash = anti_injection($_GET['hash']);
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


	</style>
	
	
	<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
	<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<body onload="self.print();">

	<!-- CONTEÚDO -->

	<?php 
	mysql_select_db($database_SmecelNovo, $SmecelNovo);
	$query_Matricula = sprintf("
		SELECT 
		aluno_id,
		aluno_cod_inep,
		aluno_cpf,
		aluno_nome,
		aluno_nascimento,
		aluno_filiacao1,
		aluno_filiacao2,
		aluno_sexo,
		aluno_sangue_tipo,
		aluno_sangue_rh,
		aluno_raca,
		aluno_nacionalidade,
		aluno_uf_nascimento,
		aluno_municipio_nascimento,
		aluno_municipio_nascimento_ibge,
		aluno_aluno_com_deficiencia,
		aluno_nis,
		aluno_identidade,
		aluno_emissor,
		aluno_uf_emissor,
		aluno_data_espedicao,
		aluno_tipo_certidao,
		aluno_termo,
		aluno_folhas,
		aluno_livro,
		aluno_emissao_certidao,
		aluno_uf_cartorio,
		aluno_mucicipio_cartorio,
		aluno_nome_cartorio,
		aluno_num_matricula_modelo_novo,
		aluno_localizacao,
		aluno_cep,
		aluno_endereco,
		aluno_numero,
		aluno_complemento,
		aluno_bairro,
		aluno_uf,
		aluno_municipio,
		aluno_telefone,
		aluno_celular,
		aluno_email,
		aluno_sus,
		aluno_tipo_deficiencia,
		aluno_laudo,
		aluno_alergia,
		aluno_alergia_qual,
		aluno_emergencia_avisar,
		aluno_emergencia_tel1,
		aluno_emergencia_tel2,
		aluno_prof_mae,
		aluno_tel_mae,
		aluno_escolaridade_mae,
		aluno_rg_mae,
		aluno_cpf_mae,
		aluno_prof_pai,
		aluno_tel_pai,
		aluno_escolaridade_pai,
		aluno_rg_pai,
		aluno_foto,
		aluno_cpf_pai,
		aluno_hash,
		aluno_cid,
		municipio_id,
		municipio_cod_ibge,
		municipio_nome,
		municipio_sigla_uf
		FROM 
		smc_aluno 
		INNER JOIN smc_municipio ON municipio_cod_ibge = aluno_municipio_nascimento_ibge
		WHERE aluno_hash = %s", GetSQLValueString($colname_Hash, "text"));
	$Matricula = mysql_query($query_Matricula, $SmecelNovo) or die(mysql_error());
	$row_Matricula = mysql_fetch_assoc($Matricula);
	$totalRows_Matricula = mysql_num_rows($Matricula);
	?>


	<?php 

	$aux = 'fnc/qr/php/qr_img.php?';
	$aux .= 'd='.URL_BASE.'/publico/aluno_cid.php?h='.$row_Matricula['aluno_hash'];

	?>

	<div style="color: white; font-display: 800; background-color: #1b325f; display: block; width: 54mm; height: 86mm; float: left; margin: 0 1mm 1mm 0; border: dotted 0px #000000; position: relative; <?php if (!$first_iteration) echo 'page-break-after: always;'; ?>">
		<br><h3 style="text-align: center; background-color: orange;">DIREITO A ATENDIMENTO PRIORITÁRIO</h3>
		<p style="text-align: center; font-size: 10px;">Carteira de identificação da Pessoa com Transtorno de Aspecto Autista</p>

		<table width="100%">
			<tr>
				<!-- Seção superior com foto e QR code -->
				<td width="85">
					<?php if($row_Matricula['aluno_foto'] == "") { ?>
						<img src="<?php echo URL_BASE.'aluno/fotos/' ?>semfoto.jpg" style="margin: 1mm; width: 20mm;">
					<?php } else { ?>
						<img src="<?php echo URL_BASE.'aluno/fotos/' ?><?php echo $row_Matricula['aluno_foto']; ?>" style="margin: 1mm; width: 20mm;">
					<?php } ?>  
				</td>

				<td width="90">
					<div class="ls-txt-center">
						<img src="<?php echo $aux; ?>" />
						<br>ID <?php echo $row_Matricula['aluno_id']; ?>
					</div>
				</td>
			</tr>

			<!-- Seção inferior com as informações do aluno -->
			<tr>
				<td colspan="2" style="padding: 8px;">
					<strong>Aluno(a)</strong><br><?php echo $row_Matricula['aluno_nome']; ?><br><br>
					<strong>CID</strong><br><?php echo $row_Matricula['aluno_cid']; ?><br><br>
					<strong>RG:</strong> <?php echo $row_Matricula['aluno_identidade']; ?> &nbsp;&nbsp;
					<strong>CPF:</strong> <?php echo $row_Matricula['aluno_cpf']; ?><br><br>
					<strong>Nascimento:</strong><br><?php echo inverteData($row_Matricula['aluno_nascimento']); ?><br><br>
					<strong>Filiação</strong><br><?php echo $row_Matricula['aluno_filiacao1']; ?><br><?php echo $row_Matricula['aluno_filiacao2']; ?>
				</td>
			</tr>
		</table>

		<!-- Imagem no canto inferior esquerdo -->
		<img src="<?php echo URL_BASE.'img/logo_smecel_background.png' ?>" style="padding-top:20px;position: absolute; bottom: 10px; left: 10px; width: 20mm;">
		<img src="<?php echo URL_BASE.'img/autismo.png' ?>" style="position: absolute; bottom: 10px; left: 155px; width: 10mm;">
	</div>






<!-- CONTEÚDO -->

<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
<script src="js/locastyle.js"></script>

</body>

</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

?>
