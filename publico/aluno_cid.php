<?php require_once('../Connections/SmecelNovo.php'); ?>
<?php include('../sistema/escola/fnc/inverteData.php'); ?>
<?php include('../sistema/funcoes/anti_injection.php'); ?>
<?php
//initialize the session
if (!isset($_SESSION)) {
	session_start();
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
	$logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
	$_SESSION['MM_Username'] = NULL;
	$_SESSION['MM_UserGroup'] = NULL;
	$_SESSION['PrevUrl'] = NULL;
	unset($_SESSION['MM_Username']);
	unset($_SESSION['MM_UserGroup']);
	unset($_SESSION['PrevUrl']);
	
	$logoutGoTo = "../index.php?saiu=true";
	if ($logoutGoTo) {
		header("Location: $logoutGoTo");
		exit;
	}
}
?>
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




$colname_Hash = "-1";
if (isset($_GET['h'])) {
	$colname_Hash = anti_injection($_GET['h']);
}
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
<!DOCTYPE html>
<html>
<head>
	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());

		gtag('config', 'UA-117872281-1');
	</script>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>SMECEL - Sistema de Gestão Escolar Municipal</title>
	<meta name="description" content="Tenha o controle das informações Educacionais em seu município na palma da mão" />

	<link rel="canonical" href="https://www.smecel.com.br/" />
	<meta property="og:locale" content="pt_BR" />
	<meta property="og:type" content="website" />
	<meta property="og:title" content="SMECEL - Sistema de Gestão Escolar Municipal" />
	<meta property="og:description" content="Tenha o controle das informações Educacionais em seu município na palma da mão" />
	<meta property="og:url" content="https://www.smecel.com.br/" />
	<meta property="og:site_name" content="SMECEL" />
	<meta property="og:image" content="https://www.smecel.com.br/img/quadro1.jpg" />
	<meta property="og:image:width" content="600" />
	<meta property="og:image:height" content="400" />
	<meta property="og:image:type" content="image/jpeg" />
	<meta name="author" content="DR WATSON" />

	<!--Import Google Icon Font-->
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<!--Import materialize.css-->
	<link type="text/css" rel="stylesheet" href="../css/materialize.min.css"  media="screen,projection"/>
	<link type="text/css" rel="stylesheet" href="../css/animate.css"  media="screen,projection"/>
	<link type="text/css" rel="stylesheet" href="../css/app.css"  media="screen,projection"/>
	<style>
		body {
			display: flex;
			min-height: 80vh;
			flex-direction: column;
			margin: 0px;
			background-color:#14376A;
		}
		main {
			flex: 1 0 auto;
		}
		.container {
			width: 100vw;
			height: 80vh;
			display: flex;
			flex-direction: row;
			justify-content: center;
			align-items: center
		}
		.transparent {
			background-color: rgba(0,0,0,0);
			box-shadow: 0px 0px 0px rgba(0,0,0,0)
		}
		.sombra {
			text-shadow: 1px 1px 2px black;
		}

		.img-col {
			width: 180px; /* Ajusta a largura da coluna da imagem */
		}

		.aluno-foto {
			width: 100%; /* Faz a imagem ocupar 100% da largura da coluna */
			height: auto; /* Mantém a proporção da imagem */
			border-radius: 10px; /* Opcional: Cantos arredondados na imagem */
			margin-right: 16px; /* Espaçamento à direita da imagem */
		}

		@media only screen and (max-width: 600px) {
			.img-col {
				width: 80px; /* Reduz a largura da coluna da imagem em telas menores */
			}
		}

		.titulo-com-logo {
			display: flex;
			align-items: center;
			justify-content: center;
			font-weight: bold;
		}

		.logo-img {
			width: 10mm;
			margin-right: 10px; /* Espaçamento entre a logo e o texto */
		}

		@media only screen and (max-width: 600px) {
			.logo-img {
				width: 8mm; /* Ajusta o tamanho da logo em telas menores */
			}
		}

		.info-aluno {
			padding: 16px; /* Aumenta o espaçamento interno */
			vertical-align: top;
			font-family: 'Roboto', sans-serif; /* Muda a fonte para algo mais moderno */
			line-height: 1.5; /* Aumenta o espaçamento entre as linhas para melhor legibilidade */
			color: #14376A; /* Aplica uma cor azul escuro para os textos */
		}

		.info-aluno p {
			margin: 0 0 10px; /* Define um espaçamento consistente entre os parágrafos */
		}

		.info-aluno strong {
			color: #2D7CBF; /* Aplica uma cor azul para os títulos */
			font-weight: bold;
		}


	</style>
	<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
	<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>

<body>
	<div class="navbar-fixed">
		<nav class="transparent" role="navigation">
			<div class="nav-wrapper"> 
				<a href="../" class="brand-logo"><img style="padding:20px" src="../img/logo_smecel_background_flattened.png" width="250" class="responsive-img jello wow" data-wow-delay="0s" data-wow-duration="0.6s"></a></a>
				
			</div>
		</nav>
	</div>

	<div class="container">
		<div class="row">
			<div class="col s12">
				<div class="card-panel box-with-bg" style="border-radius: 20px;">
					<!-- Conteúdo da Box -->
					<h6 class="center-align titulo-com-logo">
						<img src="../img/autismo2.png" class="logo-img">
						Identificação da Pessoa com Transtorno de Aspecto Autista
					</h6>

					<table width="100%">
						<tr>
							<!-- Coluna para a imagem -->
							<td class="img-col" style="vertical-align: top;">
								<?php if($row_Matricula['aluno_foto'] == "") { ?>
									<img src="<?php echo '../aluno/fotos/' ?>semfoto.jpg" class="responsive-img aluno-foto">
								<?php } else { ?>
									<img src="<?php echo '../aluno/fotos/' ?><?php echo $row_Matricula['aluno_foto']; ?>" class="responsive-img aluno-foto">
								<?php } ?>  
							</td>
							<!-- Coluna para os textos -->
							<td colspan="2" class="info-aluno">
								<p><strong>Aluno(a):</strong> <?php echo $row_Matricula['aluno_nome']; ?></p>
								<p><strong>CID:</strong> <?php echo $row_Matricula['aluno_cid']; ?></p>
								<p><strong>RG:</strong> <?php echo $row_Matricula['aluno_identidade']; ?></p>
								<p><strong>CPF:</strong> <?php echo $row_Matricula['aluno_cpf']; ?></p>
								<p><strong>Nascimento:</strong> <?php echo inverteData($row_Matricula['aluno_nascimento']); ?></p>
								<p><strong>Tipo sanguíneo:</strong> <?php echo $row_Matricula['aluno_sangue_tipo'].$row_Matricula['aluno_sangue_rh']; ?></p>
								<p><strong>Filiação: </strong><?php echo $row_Matricula['aluno_filiacao1']; ?><br><?php echo $row_Matricula['aluno_filiacao2']; ?></p>
								<p><strong>Contatos: </strong><?php echo $row_Matricula['aluno_tel_mae']; ?><br><?php echo $row_Matricula['aluno_tel_pai']; ?></p>
							</td>
						</tr>
					</table>
					<small style="font-size: 10px;">
						Este documento comprova que o aluno mencionado possui um laudo médico que identifica a condição descrita, validado e verificado pela escola em que o aluno está matriculado.
					</small>
				</div>


			</div>
		</div>
	</div>



	<!--JavaScript at end of body for optimized loading--> 
	<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script> 
	<script type="text/javascript" src="../js/materialize.min.js"></script> 
	<script type="text/javascript" src="../js/wow.min.js"></script> 
	<script type="text/javascript" src="../js/pace.min.js"></script> 
	<script>
		new WOW().init();
	</script> 
</body>
</html>