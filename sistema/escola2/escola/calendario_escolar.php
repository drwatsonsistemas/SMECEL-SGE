<?php require_once('../../Connections/SmecelNovo.php'); ?>
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



function diffMonth($from, $to)
{

	$fromYear = date("Y", strtotime($from));
	$fromMonth = date("m", strtotime($from));
	$toYear = date("Y", strtotime($to));
	$toMonth = date("m", strtotime($to));
	if ($fromYear == $toYear) {
		return ($toMonth - $fromMonth) + 1;
	} else {
		return (12 - $fromMonth) + 1 + $toMonth;
	}

}


function nomeMes($numero)
{

	switch ($numero) {
		case 1:
			$nomeMes = "JANEIRO";
			break;
		case 2:
			$nomeMes = "FEVEREIRO";
			break;
		case 3:
			$nomeMes = "MARÇO";
			break;
		case 4:
			$nomeMes = "ABRIL";
			break;
		case 5:
			$nomeMes = "MAIO";
			break;
		case 6:
			$nomeMes = "JUNHO";
			break;
		case 7:
			$nomeMes = "JULHO";
			break;
		case 8:
			$nomeMes = "AGOSTO";
			break;
		case 9:
			$nomeMes = "SETEMBRO";
			break;
		case 10:
			$nomeMes = "OUTUBRO";
			break;
		case 11:
			$nomeMes = "NOVEMBRO";
			break;
		case 12:
			$nomeMes = "DEZEMBRO";
			break;
	}

	return $nomeMes;

}


$totalMeses = diffMonth($row_AnoLetivo['ano_letivo_inicio'], $row_AnoLetivo['ano_letivo_fim']);

//$meses = 12;

$ano = $row_AnoLetivo['ano_letivo_ano'];

$mesInicio = date("m", strtotime($row_AnoLetivo['ano_letivo_inicio']));
$anoInicio = date("Y", strtotime($row_AnoLetivo['ano_letivo_inicio']));

$mesFim = date("m", strtotime($row_AnoLetivo['ano_letivo_fim']));
$anoFim = date("Y", strtotime($row_AnoLetivo['ano_letivo_fim']));

date("m", strtotime($row_AnoLetivo['ano_letivo_inicio']));

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

	<title>SMECEL - Sistema de Gestão Escolar</title>
	<meta charset="utf-8">
	<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<meta name="description" content="">
	<meta name="keywords" content="">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<link rel="stylesheet" type="text/css" href="css/locastyle.css">
	<link rel="stylesheet" type="text/css" href="css/preloader.css">
	<script src="js/locastyle.js"></script>
	<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
	<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">

	<style>
		.mes {
			display: block;
			min-height: 420px;
			height: auto;
		}

		.dia {
			display: block;
			float: left;
			margin: 1px 1px 0 0;
			padding: 2px;
			width: 13%;
			height: 30px;
			color: black;
			//background-color: yellow;
			text-align: left;
			border: #000 1px solid;
			font-size: 10px;
		}

		.semana {
			float: left;
			display: block;
			margin: 1px 1px 0 0;
			padding: 2px;
			width: 13%;
			height: 24px;
			color: black;
			background-color: #CCC;
			text-align: center;
			border: #000 1px solid;
		}

		.nome-mes {
			margin: 10px 0;
			float: left;
			display: block;
			font-size: 16px;
			width: 100%;
			font-weight: bolder;
		}

		.lista {
			display: block;
			float: inherit;
			font-size: 9px;
			margin: 5px 0;
			font-style: italic;
		}

		.limpa {
			clear: left;
		}
	</style>

</head>

<body>
	<?php include_once("menu-top.php"); ?>
	<?php include_once("menu-esc.php"); ?>


	<main class="ls-main ">
		<div class="container-fluid">

			<h1 class="ls-title-intro ls-ico-home">CALENDÁRIO ESCOLAR <?php echo $row_AnoLetivo['ano_letivo_ano']; ?>
			</h1>
			<!-- CONTEÚDO -->


			<div class="ls-box">

				<?php
				$contar['recesso'] = 0;
				$contar['dialetivo'] = 0;
				$contar['sabadoletivo'] = 0;
				$contar['feriadonacional'] = 0;
				$contar['feriadomunicipal'] = 0;
				$contar['recessojunino'] = 0;
				$contar['recessonatal'] = 0;

				$contar['jornadapedagogica'] = 0;
				$contar['encontroplanejamento'] = 0;
				$contar['conselhoclasse'] = 0;
				$contar['estudorecuperacao'] = 0;
				$contar['anoletivo2020'] = 0;
				$contar['anoletivo2021'] = 0;

				$contar['plantaopedagogico'] = 0;
				$contar['conselhodeclasse'] = 0;
				$contar['resultadosfinais'] = 0;
				?>


				<?php for ($mesCont = 1; $mesCont <= $totalMeses; $mesCont++) { ?>

					<div class="col-lg-3 col-md-3 col-sm-1">
						<div class="mes">

							<div class="nome-mes ls-txt-center">
								<?php echo nomeMes($mesInicio); ?>/<?php echo $anoInicio; ?>
							</div>
							<?php
							$primeiroDia = $anoInicio . '-' . $mesInicio . '-1';
							$diasemanaNumero = (date('w', strtotime($primeiroDia)));
							$dias = cal_days_in_month(CAL_GREGORIAN, $mesInicio, $anoInicio);
							?>
							<div class="semana">D</div>
							<div class="semana">S</div>
							<div class="semana">T</div>
							<div class="semana">Q</div>
							<div class="semana">Q</div>
							<div class="semana">S</div>
							<div class="semana">S</div>
							<?php for ($fds = 1; $fds <= $diasemanaNumero; $fds++) { ?>
								<div class='dia'>&nbsp;</div>

								<?php

								$diaLetivoMes = 0;
								$sabadoLetivoMes = 0;

							}

							for ($dia = 1; $dia <= $dias; $dia++) { ?>


								<?php $domingo = (date('w', strtotime($anoInicio . '-' . $mesInicio . '-' . $dia))); ?>



								<?php

								$buscaData = $anoInicio . "-" . $mesInicio . "-" . $dia;

								mysql_select_db($database_SmecelNovo, $SmecelNovo);
								$query_ListaCalendario = "SELECT ce_id, ce_id_sec, ce_ano, ce_data, ce_tipo, ce_descricao FROM smc_calendario_escolar WHERE ce_id_sec = '$row_UsuLogado[usu_sec]' AND ce_data = '$buscaData'";
								$ListaCalendario = mysql_query($query_ListaCalendario, $SmecelNovo) or die(mysql_error());
								$row_ListaCalendario = mysql_fetch_assoc($ListaCalendario);
								$totalRows_ListaCalendario = mysql_num_rows($ListaCalendario);

								switch ($row_ListaCalendario['ce_tipo']) {
									case 19:
										$corDia = "#3A7DCE";
										$corTexto = "black";
										$titulo = "RECESSO";
										$contar['recesso']++;
										break;
									case 1:
										$corDia = "yellow";
										$corTexto = "black";
										$titulo = "DIA LETIVO";
										$contar['dialetivo']++;
										$diaLetivoMes++;
										break;
									case 2:
										$corDia = "orange";
										$corTexto = "black";
										$titulo = "SÁBADO LETIVO";
										$contar['sabadoletivo']++;
										$sabadoLetivoMes++;
										break;
									case 3:
										$corDia = "#2E8B57";
										$corTexto = "white";
										$titulo = "FERIADO NACIONAL";
										$contar['feriadonacional']++;
										break;
									case 4:
										$corDia = "#4169E1";
										$corTexto = "white";
										$titulo = "FERIADO MUNICIPAL";
										$contar['feriadomunicipal']++;
										break;
									case 5:
										$corDia = "#9400D3";
										$corTexto = "white";
										$titulo = "RECESSO JUNINO";
										$contar['recessojunino']++;
										break;
									case 6:
										$corDia = "pink";
										$corTexto = "black";
										$titulo = "RECESSO DE NATAL";
										$contar['recessonatal']++;
										break;

									case 7:
										$corDia = "#e57373";
										$corTexto = "black";
										$titulo = "JORNADA PEDAGÓGICA";
										$contar['jornadapedagogica']++;
										break;
									case 8:
										$corDia = "#ba68c8";
										$corTexto = "black";
										$titulo = "ENCONTRO P/ PLANEJAMENTO";
										$contar['encontroplanejamento']++;
										break;
									case 9:
										$corDia = "#9575cd";
										$corTexto = "black";
										$titulo = "CONSELHO DE CLASSE";
										$contar['conselhoclasse']++;
										break;
									case 10:
										$corDia = "#7986cb";
										$corTexto = "black";
										$titulo = "ESTUDO DE RECUPERAÇÃO";
										$contar['estudorecuperacao']++;
										break;
									case 11:
										$corDia = "#80cbc4";
										$corTexto = "black";
										$titulo = "ANO LETIVO 2020";
										$contar['anoletivo2020']++;
										break;
									case 12:
										$corDia = "#81c784";
										$corTexto = "black";
										$titulo = "ANO LETIVO 2021";
										$contar['anoletivo2021']++;
										break;

									case 13:
										$corDia = "#ef9a9a";
										$corTexto = "black";
										$titulo = "PLANTÃO PEDAGÓGICO";
										$contar['plantaopedagogico']++;
										break;

									case 14:
										$corDia = "#b39ddb";
										$corTexto = "black";
										$titulo = "CONSELHO DE CLASSE";
										$contar['conselhodeclasse']++;
										break;

									case 15:
										$corDia = "#81d4fa";
										$corTexto = "black";
										$titulo = "RESULTADOS FINAIS";
										$contar['resultadosfinais']++;
										break;




									default:
										$corDia = "";
										$corTexto = "";
										$titulo = "";
										break;
								}

								?>
								<?php if ($totalRows_ListaCalendario == 0) { ?>

								<?php } else { ?>

								<?php } ?>

								<div class="dia" <?php if ($domingo == 0) {
									echo " style=background-color:#F5F5F5;";
								} ?><?php if ($totalRows_ListaCalendario > 0) {
									 echo "style=\"color:" . $corTexto . "; background-color:" . $corDia . "\";";
								 } ?>> <?php echo $dia; ?></div>



							<?php } ?>

							<?php

							mysql_select_db($database_SmecelNovo, $SmecelNovo);
							$query_ListaCalendarioMes = "
		SELECT ce_id, ce_id_sec, ce_ano, ce_data, ce_tipo, ce_descricao, DAY(STR_TO_DATE(ce_data, '%Y-%m-%d')) AS dia,
		CASE ce_tipo
		WHEN 19 THEN 'RECESSO'
		WHEN 1 THEN 'DIA LETIVO'
		WHEN 2 THEN 'SÁBADO LETIVO'
		WHEN 3 THEN 'FERIADO NACIONAL'
		WHEN 4 THEN 'FERIADO MUNICIPAL'
		WHEN 5 THEN 'RECESSO JUNINO'
		WHEN 6 THEN 'RECESSO DE NATAL'

		WHEN 7 THEN 'JORNADA PEDAGÓGICA'
		WHEN 8 THEN 'ENCONTRO P/ PLANEJAMENTO'
		WHEN 9 THEN 'CONSELHO DE CLASSE'
		WHEN 10 THEN 'ESTUDO DE RECUPERAÇÃO'
		WHEN 11 THEN 'ANO LETIVO 2020'
		WHEN 12 THEN 'ANO LETIVO 2021'

		WHEN 13 THEN 'PLANTÃO PEDAGÓGICO'
		WHEN 14 THEN 'CONSELHO DE CLASSE'
		WHEN 15 THEN 'RESULTADOS FINAIS'

		END AS ce_tipo_nome 
		FROM smc_calendario_escolar 
		WHERE ce_id_sec = '$row_UsuLogado[usu_sec]' AND ce_tipo <> '1' AND MONTH(STR_TO_DATE(ce_data, '%Y-%m-%d')) = '$mesInicio' AND YEAR(STR_TO_DATE(ce_data, '%Y-%m-%d')) = '$anoInicio'";
							$ListaCalendarioMes = mysql_query($query_ListaCalendarioMes, $SmecelNovo) or die(mysql_error());
							$row_ListaCalendarioMes = mysql_fetch_assoc($ListaCalendarioMes);
							$totalRows_ListaCalendarioMes = mysql_num_rows($ListaCalendarioMes);


							?>

							<div class="limpa"></div>

							<p>
								<small>
									Dias letivos (<?php echo $diaLetivoMes; ?>)
									Sábados letivos (<?php echo $sabadoLetivoMes; ?>)
									<strong>Total (<?php echo $diaLetivoMes + $sabadoLetivoMes; ?>)</strong>
								</small>
							</p>

							<?php

							$diaLetivoMes = 0;
							$sabadoLetivoMes = 0;

							?>

							<div class="lista">


								<?php do { ?>
									<?php echo $row_ListaCalendarioMes['dia']; ?> -
									<?php echo $row_ListaCalendarioMes['ce_tipo_nome']; ?>
									<strong><?php echo $row_ListaCalendarioMes['ce_descricao']; ?></strong><br>
								<?php } while ($row_ListaCalendarioMes = mysql_fetch_assoc($ListaCalendarioMes)); ?>


							</div>
						</div>
					</div>

					<?php


					if ($mesInicio == 12) {
						$mesInicio = 1;
						$anoInicio++;
					} else {
						$mesInicio++;
					}



				}


				?>


			</div>

			<div class="ls-box">
				<h5 class="ls-title-3">RESUMO</h5>

				<p>

					- RECESSOS <strong><?php echo $contar['recesso']; ?></strong><br>
					- DIAS LETIVOS <strong><?php echo $contar['dialetivo']; ?></strong><br>
					- SÁBADOS LETIVOS <strong><?php echo $contar['sabadoletivo']; ?></strong><br>
					- TOTAL DE DIAS LETIVOS
					<strong><?php echo $contar['dialetivo'] + $contar['sabadoletivo']; ?></strong><br>
					- FERIADOS NACIONAIS <strong><?php echo $contar['feriadonacional']; ?></strong><br>
					- FERIADOS MUNICIPAIS <strong><?php echo $contar['feriadomunicipal']; ?></strong><br>
					- DIAS DE RECESSO JUNINO <strong><?php echo $contar['recessojunino']; ?></strong><br>
					- DIAS DE RECESSO DE NATAL <strong><?php echo $contar['recessonatal']; ?></strong><br>

					- JORNADA PEDAGÓGICA <strong><?php echo $contar['jornadapedagogica']; ?></strong><br>
					- ENCONTRO P/ PLANEJAMENTO <strong><?php echo $contar['encontroplanejamento']; ?></strong><br>
					- CONSELHO DE CLASSE <strong><?php echo $contar['conselhoclasse']; ?></strong><br>
					- ESTUDO DE RECUPERAÇÃO <strong><?php echo $contar['estudorecuperacao']; ?></strong><br>





					<!-- CONTEÚDO -->
				</p>
			</div>

			<hr>

			<!-- CONTEUDO -->
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
?>