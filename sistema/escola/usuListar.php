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
usu_id, usu_nome, usu_email, usu_contato, usu_escola, usu_status, usu_tipo, DATE_FORMAT(usu_cadastro, '%d/%m/%Y') AS usu_cadastro
FROM smc_usu
WHERE usu_escola = '$row_EscolaLogada[escola_id]' AND usu_status = '1' AND usu_tipo <> '99' 
ORDER BY usu_nome ASC
";
$ListaVinculos = mysql_query($query_ListaVinculos, $SmecelNovo) or die(mysql_error());
$row_ListaVinculos = mysql_fetch_assoc($ListaVinculos);
$totalRows_ListaVinculos = mysql_num_rows($ListaVinculos);

/*
echo "<pre>";
do{
	var_dump($row_ListaVinculos);
}while ($row_ListaVinculos = mysql_fetch_assoc($ListaVinculos));
echo "</pre>";
*/
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

	<title>Listar Funcionários</title>

	<meta charset="utf-8">
	<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<meta name="description" content="">
	<meta name="keywords" content="">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<link rel="stylesheet" type="text/css" href="css/locastyle.css">	<link rel="stylesheet" type="text/css" href="css/preloader.css">
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

			<h1 class="ls-title-intro ls-ico-home">Usuários</h1>

			<div class="ls-box ls-sm-space">

				<?php if ( $totalRows_ListaVinculos > 0 ) { ?>
					<table class="ls-table ls-table-striped ls-sm-space fonte-tabela" role="grid">
						<thead>
							<tr>
								<th width="40px" class="ls-txt-center hidden-xs">Nº</th>
								<th width="20px" class="hidden-xs"></th>
								<th>USUÁRIO</th>
								<th class="ls-txt-center">TELEFONE</th>
								<th class="ls-txt-center">EMAIL</th>
								<th class="ls-txt-center hidden-xs">CADASTRO</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$contagem = 1;
							do { 
								?>
								<tr>
									<td class="ls-txt-center hidden-xs">
										<?php 
										echo $contagem;
										$contagem++;
									?></td>
									<td></td>
									<td><?php echo $row_ListaVinculos['usu_nome']; ?></a></td>
									<td class="ls-txt-center"><?php echo $row_ListaVinculos['usu_contato'] ?></td>
									<td class="ls-txt-center"><?php echo $row_ListaVinculos['usu_email'] ?></td>
									<td class="ls-txt-center hidden-xs"><?php echo $row_ListaVinculos['usu_cadastro'] ?></td>
	
								</tr>
							<?php } while ($row_ListaVinculos = mysql_fetch_assoc($ListaVinculos)); ?>
						</tbody>
					</table>

				</div>
				
				<p class="ls-txt-right">Total: <?php echo $totalRows_ListaVinculos; ?></p>

			<?php } else { ?>
				<br>
				<p><div class="ls-alert-info"><strong>Atenção:</strong> Nenhum usuário cadastrado nessa escola.</div></p>
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

mysql_free_result($EscolaLogada);

mysql_free_result($ListaVinculos);
?>