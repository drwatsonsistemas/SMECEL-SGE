<?php require_once('../../../Connections/SmecelNovo.php'); ?>
<?php require_once('../funcoes/inverteData.php'); ?>

<?php
//initialize the session
if (!isset($_SESSION)) {
	session_start();
}
// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF'] . "?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")) {
	$logoutAction .= "&" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) && ($_GET['doLogout'] == "true")) {
	//to fully log out a visitor we need to clear the session varialbles
	$_SESSION['MM_Username'] = NULL;
	$_SESSION['MM_UserGroup'] = NULL;
	$_SESSION['PrevUrl'] = NULL;
	unset($_SESSION['MM_Username']);
	unset($_SESSION['MM_UserGroup']);
	unset($_SESSION['PrevUrl']);

	$logoutGoTo = "../../../index.php?exit";
	if ($logoutGoTo) {
		header("Location: $logoutGoTo");
		exit;
	}
}
?>
<?php
if (!isset($_SESSION)) {
	session_start();
}
$MM_authorizedUsers = "1,99";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup)
{
	// For security, start by assuming the visitor is NOT authorized. 
	$isValid = False;

	// When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
	// Therefore, we know that a user is NOT logged in if that Session variable is blank. 
	if (!empty($UserName)) {
		// Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
		// Parse the strings into arrays. 
		$arrUsers = Explode(",", $strUsers);
		$arrGroups = Explode(",", $strGroups);
		if (in_array($UserName, $arrUsers)) {
			$isValid = true;
		}
		// Or, you may restrict access to only certain users based on their username. 
		if (in_array($UserGroup, $arrGroups)) {
			$isValid = true;
		}
		if (($strUsers == "") && false) {
			$isValid = true;
		}
	}
	return $isValid;
}

$MM_restrictGoTo = "../../../index.php?acessorestrito";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("", $MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {
	$MM_qsChar = "?";
	$MM_referrer = $_SERVER['PHP_SELF'];
	if (strpos($MM_restrictGoTo, "?"))
		$MM_qsChar = "&";
	if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0)
		$MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
	$MM_restrictGoTo = $MM_restrictGoTo . $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
	header("Location: " . $MM_restrictGoTo);
	exit;
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

require_once('../funcoes/usuLogado.php');
require_once('../funcoes/anoLetivo.php');


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

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

//$meses = 12;
//$ano = $row_AnoLetivo['ano_letivo_ano'];




$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
	$editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}


if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {


	$inicio = $_POST['ce_data_de'];
	$final = $_POST['ce_data_ate'];
	$ano = $row_AnoLetivo['ano_letivo_ano'];
	$tipo = $_POST['ce_tipo'];
	$sec = $row_Secretaria['sec_id'];
	$data = $inicio;


	if (strtotime($inicio) > strtotime($final)) {
		$dataInvalida1 = "calendario_escolar.php?maior";
		header(sprintf("Location: %s", $dataInvalida1));
		die();
	}



	while ($inicio <= $final) {

		$util = date('N', strtotime($inicio));


		if ($util < 6) {

			$insertSQL = sprintf("INSERT INTO smc_calendario_escolar (ce_ano, ce_id_sec, ce_data, ce_tipo) VALUES ('$ano', '$sec', '$inicio', '$tipo')");

			mysql_select_db($database_SmecelNovo, $SmecelNovo);
			$Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

		}

		$inicio = date('Y-m-d', strtotime($inicio . ' + 1 days'));

	}

	$insertGoTo = "calendario_escolar.php?lancado";
	if (isset($_SERVER['QUERY_STRING'])) {
		$insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
		$insertGoTo .= $_SERVER['QUERY_STRING'];
	}
	header(sprintf("Location: %s", $insertGoTo));
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

<?php

function cor($codigo)
{

	switch ($codigo) {
		case 19:
			echo "#3A7DCE";
			break;
		case 1:
			echo "yellow";
			break;
		case 2:
			echo "orange";
			break;
		case 3:
			echo "#2E8B57";
			break;
		case 4:
			echo "#4169E1";
			break;
		case 5:
			echo "#9400D3";
			break;
		case 6:
			echo "pink";
			break;

		case 7:
			echo "#e57373";
			break;
		case 8:
			echo "#ba68c8";
			break;
		case 9:
			echo "#9575cd";
			break;
		case 10:
			echo "#7986cb";
			break;
		case 11:
			echo "#80cbc4";
			break;
		case 12:
			echo "#81c784";
			break;
		case 13:
			echo "#ef9a9a";
			break;

		case 14:
			echo "#b39ddb";
			break;

		case 15:
			echo "#81d4fa";
			break;




		default:
			echo "";
			break;
	}

}

?>

<!DOCTYPE html>
<html class="ls-theme-green">

<head>
	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag() { dataLayer.push(arguments); }
		gtag('js', new Date());

		gtag('config', 'UA-117872281-1');
	</script>
	<title>smecel</title>
	<meta charset="utf-8">
	<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<meta name="title" content="Calendário Escolar 2024">
	<meta name="description" content="">
	<meta name="keywords" content="">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<link rel="stylesheet" type="text/css" href="//assets.locaweb.com.br/locastyle/edge/stylesheets/locastyle.css">
	<script src="//assets.locaweb.com.br/locastyle/edge/javascripts/locastyle.js"></script>
	<style>
		@media print {
			* {
				-webkit-print-color-adjust: exact !important;
				/* Para navegadores baseados no WebKit */
				print-color-adjust: exact !important;
				/* Para outros navegadores */
			}
		}

		html {
			-webkit-print-color-adjust: exact;
		}

		.mes {
			display: block;
			min-height: 190px;
			height: auto;
			width: 33%;
		}

		.dia {
			display: block;
			float: left;
			margin: 1px 1px 0 0;
			padding: 1px;
			width: 13%;
			height: 18px;
			color: black;
			//background-color: yellow;
			text-align: left;
			border: #000 1px solid;
			font-size: 9px;
		}

		.semana {
			float: left;
			display: block;
			margin: 1px 1px 0 0;
			padding: 1px;
			width: 13%;
			height: 15px;
			color: black;
			background-color: #CCC;
			text-align: center;
			border: #000 1px solid;
			font-size: 10px;
		}

		.nome-mes {
			margin: 10px 0;
			float: left;
			display: block;
			font-size: 12px;
			width: 100%;
			font-weight: bolder;

		}

		.lista {
			display: block;
			float: inherit;
			font-size: 9px;
			margin: 4px 0;
			font-style: italic;
			font-size: 9px;
		}

		.limpa {
			clear: left;
		}
	</style>
</head>

<body onload="setPrintTitle()">

	<div class="ls-box1">


		<table class="" width="100%">
			<tr>
				<td class="ls-txt-center" width="60"></td>
				<td class="ls-txt-center">
					<?php if ($row_Secretaria['sec_logo'] <> "") { ?>
						<img src="../../../img/logo/secretaria/<?php echo $row_Secretaria['sec_logo']; ?>"
							alt="Logo da <?php echo $row_Secretaria['sec_nome']; ?>"
							title="Logo da <?php echo $row_EscolaLogada['sec_nome']; ?>" width="60" />
					<?php } else { ?>
						<img src="../../../img/brasao_republica.png" width="60">
					<?php } ?>
					<h3><?php echo $row_Secretaria['sec_prefeitura']; ?></h3>
					<?php echo $row_Secretaria['sec_nome']; ?>
				</td>
				<td class="ls-txt-center" width="60"></td>
			</tr>
		</table>
		<br>
		<h3 class="ls-txt-center">CALENDÁRIO ESCOLAR <?php echo $ano; ?></h3>
		<br>

		<!-- CONTEUDO -->


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

			<div class="mes" style="float:left">

				<div class="nome-mes ls-txt-center"> <?php echo nomeMes($mesInicio); ?>/<?php echo $anoInicio; ?> </div>
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
					$query_ListaCalendario = "SELECT ce_id, ce_id_sec, ce_ano, ce_data, ce_tipo, ce_descricao FROM smc_calendario_escolar WHERE ce_id_sec = '$row_UsuarioLogado[usu_sec]' AND ce_data = '$buscaData'";
					$ListaCalendario = mysql_query($query_ListaCalendario, $SmecelNovo) or die(mysql_error());
					$row_ListaCalendario = mysql_fetch_assoc($ListaCalendario);
					$totalRows_ListaCalendario = mysql_num_rows($ListaCalendario);

					switch ($row_ListaCalendario['ce_tipo']) {
						case 19:
							$corDia = "#3A7DCE";
							$corTexto = "black";
							$titulo = "RECESSO";
							$contar['recesso']++;
							$diaLetivoMes++;
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
						<a href="../calendario_escolar_cadastrar.php?data=<?php echo $anoInicio . "-" . $mesInicio . "-" . $dia; ?>"
							title="<?php echo $titulo . " - " . $row_ListaCalendario['ce_descricao']; ?>">
						<?php } else { ?>
							<a href="calendario_escolar_editar.php?codigo=<?php echo $row_ListaCalendario['ce_id']; ?>">
							<?php } ?>

							<div class="dia" <?php if ($domingo == 0) {
								echo " style=background-color:#F5F5F5;";
							} ?><?php if ($totalRows_ListaCalendario > 0) {
								 echo "style=\"color:" . $corTexto . "; background-color:" . $corDia . "\";";
							 } ?>> <?php echo $dia; ?></div>

						</a>

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
		WHERE ce_id_sec = '$row_UsuarioLogado[usu_sec]' AND ce_tipo <> '1' AND MONTH(STR_TO_DATE(ce_data, '%Y-%m-%d')) = '$mesInicio' AND YEAR(STR_TO_DATE(ce_data, '%Y-%m-%d')) = '$anoInicio'
		
		GROUP BY ce_tipo
		
		";
					$ListaCalendarioMes = mysql_query($query_ListaCalendarioMes, $SmecelNovo) or die(mysql_error());
					$row_ListaCalendarioMes = mysql_fetch_assoc($ListaCalendarioMes);
					$totalRows_ListaCalendarioMes = mysql_num_rows($ListaCalendarioMes);


					?>

					<div class="limpa"></div>

					<p style="display:none">
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
							<div
								style="width:20px; height:8px; margin-right:5px; float:left; background-color:<?php echo cor($row_ListaCalendarioMes['ce_tipo']); ?>;">
							</div> <?php echo $row_ListaCalendarioMes['ce_tipo_nome']; ?>
							<strong><?php echo $row_ListaCalendarioMes['ce_descricao']; ?></strong><br>
						<?php } while ($row_ListaCalendarioMes = mysql_fetch_assoc($ListaCalendarioMes)); ?>


					</div>
			</div>


			<?php

			if ($mesInicio == 12) {
				$mesInicio = 1;
				$anoInicio++;
			} else {
				$mesInicio++;
			}

		} ?>


		<!-- CONTEUDO -->
	</div>

	<div class="ls-box1">
		<h6 class="ls-title-5">RESUMO</h6>
		<small>
			RECESSOS <strong><?php echo $contar['recesso']; ?></strong> |
			DIAS LETIVOS <strong><?php echo $contar['dialetivo']; ?></strong> |
			SÁBADOS LETIVOS <strong><?php echo $contar['sabadoletivo']; ?></strong>
			TOTAL DE DIAS LETIVOS <strong><?php echo $contar['dialetivo'] + $contar['sabadoletivo']; ?></strong> |
			FERIADOS NACIONAIS <strong><?php echo $contar['feriadonacional']; ?></strong> |
			FERIADOS MUNICIPAIS <strong><?php echo $contar['feriadomunicipal']; ?></strong> |
			DIAS DE RECESSO JUNINO <strong><?php echo $contar['recessojunino']; ?></strong> |
			DIAS DE RECESSO DE NATAL <strong><?php echo $contar['recessonatal']; ?></strong> |

			JORNADA PEDAGÓGICA <strong><?php echo $contar['jornadapedagogica']; ?></strong> |
			ENCONTRO P/ PLANEJAMENTO <strong><?php echo $contar['encontroplanejamento']; ?></strong> |
			CONSELHO DE CLASSE <strong><?php echo $contar['conselhoclasse']; ?></strong> |
			ESTUDO DE RECUPERAÇÃO <strong><?php echo $contar['estudorecuperacao']; ?></strong>


		</small>
	</div>





	<!-- We recommended use jQuery 1.10 or up -->
	<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
	<script src="https://assets.locaweb.com.br/locastyle/3.8.4/javascripts/locastyle.js"
		type="text/javascript"></script>

		<script>
function setPrintTitle() {
    document.title = "Calendario_Escolar_2025"; // Define um nome para o PDF
    window.print();
    setTimeout(() => {
        document.title = "SMECEL - Sistema de Gestão Escolar"; // Restaura o título original
    }, 1000);
}
</script>
</body>

</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

mysql_free_result($ListaCalendario);

mysql_free_result($ListaCalendarioMes);
?>