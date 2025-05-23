<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php require_once('funcoes/inverteData.php'); ?>

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
	
  $logoutGoTo = "../../index.php?exit";
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
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
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

$MM_restrictGoTo = "../../index.php?acessorestrito";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
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

require_once('funcoes/usuLogado.php');
require_once('funcoes/anoLetivo.php');

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);



function diffMonth($from, $to) {

        $fromYear = date("Y", strtotime($from));
        $fromMonth = date("m", strtotime($from));
        $toYear = date("Y", strtotime($to));
        $toMonth = date("m", strtotime($to));
        if ($fromYear == $toYear) {
            return ($toMonth-$fromMonth)+1;
        } else {
            return (12-$fromMonth)+1+$toMonth;
        }

    }


function nomeMes($numero) {
	
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
	break;
	}



while ($inicio <= $final) { 

$util = date('N', strtotime($inicio));


if  ($util < 6) {
	
  $insertSQL = sprintf("INSERT INTO smc_calendario_escolar (ce_ano, ce_id_sec, ce_data, ce_tipo) VALUES ('$ano', '$sec', '$inicio', '$tipo')");

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());
  
}
  
  $inicio = date('Y-m-d', strtotime($inicio. ' + 1 days'));

}

  $insertGoTo = "calendario_escolar.php?lancado";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}




//$data_inicio = new DateTime($row_AnoLetivo['ano_letivo_inicio']);
//$data_fim = new DateTime($row_AnoLetivo['ano_letivo_fim']);

//$dateInterval = $data_inicio->diff($data_fim);

//$totalMeses = $dateInterval->m + ($dateInterval->y * 12);



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
<html class="ls-theme-green">
<head>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
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
<script src="js/locastyle.js"></script><style>
.mes {
	display:block;
	min-height:420px;
	height:auto;	
}
.dia {
	display:block;
	float:left;
	margin: 1px 1px 0 0;
	padding: 2px;
	width: 13%;
	height:30px;
	color:black;
	//background-color: yellow;
	text-align: left;
	border:#000 1px solid;
	font-size:10px;
}
.semana {
	float:left;
	display:block;
	margin: 1px 1px 0 0;
	padding: 2px;
	width: 13%;
	height:24px;
	color:black;
	background-color:#CCC;
	text-align:center;
	border:#000 1px solid;
}
.nome-mes {
	margin:10px 0;
	float:left;
	display:block;
	font-size:16px;
	width:100%;
	font-weight:bolder;
}

.lista {
	display:block;
	float:inherit;
	font-size:9px;
	margin: 5px 0;
	font-style:italic;
	}

.limpa {
	clear:left;
}

</style>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<body>
<?php include_once("menu_top.php"); ?>
<?php include_once "menu.php"; ?>
<main class="ls-main">
  <div class="container-fluid">
    <h1 class="ls-title-intro ls-ico-home">CALENDÁRIO ESCOLAR <?php echo $ano; ?></h1>
    <div class="ls-box"> 
    
 
      <!-- CONTEUDO -->
      
      
      <div class="ls-alert-info"><strong>Atenção:</strong>
      Clique sobre o dia no calendário para informar o evento (dia letivo, sábado letivo, feridado etc.) ou clique no botão abaixo para informar um intervalo de datas.
      </div>
      
      
      <button data-ls-module="modal" data-target="#myAwesomeModal" class="ls-btn-primary">PERÍODO</button>
	  <a class="ls-btn" href="impressao/calendario_escolar.php" target="_blank">IMPRIMIR</a>

      
      <p>&nbsp;</p>

      <p>INÍCIO: <?php echo date("d/m/Y", strtotime($row_AnoLetivo['ano_letivo_inicio'])); ?></p>
	  <p>TÉRMINO: <?php echo date("d/m/Y", strtotime($row_AnoLetivo['ano_letivo_fim'])); ?></p>
	  <p>MESES LETIVOS: <?php echo $totalMeses; ?></p>
	  


      
		<?php if (isset($_GET["lancado"])) { ?>
        <div class="ls-alert-success ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> Evento lançado com sucesso no calendário letivo! </div>
        <?php } ?>

		<?php if (isset($_GET["erro"])) { ?>
        <div class="ls-alert-danger ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> <strong>Ops! </strong> Isso não deveria ter acontecido. Um e-mail foi enviado ao administrador do sistema! </div>
        <?php } ?>

		<?php if (isset($_GET["maior"])) { ?>
        <div class="ls-alert-danger ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> <strong>Ops! </strong> A data inicial não pode ser maior do que a data final. </div>
        <?php } ?>

      
      <?php 
	  
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
      
        <div class="nome-mes ls-txt-center"> <?php echo nomeMes($mesInicio); ?>/<?php echo $anoInicio; ?></div>
        <?php
        $primeiroDia = $anoInicio.'-'.$mesInicio.'-1';
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
		
		
        <?php $domingo = (date('w', strtotime($anoInicio.'-'.$mesInicio.'-'.$dia))); ?>
		
		
        
        <?php
		
		$buscaData = $anoInicio."-".$mesInicio."-".$dia; 
		 
		mysql_select_db($database_SmecelNovo, $SmecelNovo);
		$query_ListaCalendario = "SELECT ce_id, ce_id_sec, ce_ano, ce_data, ce_tipo, ce_descricao FROM smc_calendario_escolar WHERE ce_id_sec = '$row_UsuarioLogado[usu_sec]' AND ce_data = '$buscaData'";
		$ListaCalendario = mysql_query($query_ListaCalendario, $SmecelNovo) or die(mysql_error());
		$row_ListaCalendario = mysql_fetch_assoc($ListaCalendario);
		$totalRows_ListaCalendario = mysql_num_rows($ListaCalendario);
				
		switch ($row_ListaCalendario['ce_tipo']) {
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
        <a href="calendario_escolar_cadastrar.php?data=<?php echo $anoInicio."-".$mesInicio."-".$dia; ?>" title="<?php echo $titulo." - ".$row_ListaCalendario['ce_descricao']; ?>">
        <?php } else { ?>
        <a href="calendario_escolar_editar.php?codigo=<?php echo $row_ListaCalendario['ce_id']; ?>">
		<?php } ?>
        
        <div class="dia"<?php if ($domingo == 0) { echo " style=background-color:#F5F5F5;"; }?><?php if ($totalRows_ListaCalendario > 0) { echo "style=\"color:".$corTexto."; background-color:".$corDia."\";"; } ?>> <?php echo $dia; ?></div>
        
        </a>
        
		<?php } ?>
        
        <?php
		 
		mysql_select_db($database_SmecelNovo, $SmecelNovo);
		$query_ListaCalendarioMes = "
		SELECT ce_id, ce_id_sec, ce_ano, ce_data, ce_tipo, ce_descricao, DAY(STR_TO_DATE(ce_data, '%Y-%m-%d')) AS dia,
		CASE ce_tipo
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
		WHERE ce_id_sec = '$row_UsuarioLogado[usu_sec]' AND ce_tipo <> '1' AND MONTH(STR_TO_DATE(ce_data, '%Y-%m-%d')) = '$mesInicio' AND YEAR(STR_TO_DATE(ce_data, '%Y-%m-%d')) = '$anoInicio'";
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
		  <?php echo $row_ListaCalendarioMes['dia']; ?> - <?php echo $row_ListaCalendarioMes['ce_tipo_nome']; ?> <strong><?php echo $row_ListaCalendarioMes['ce_descricao']; ?></strong><br>
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
      
            
      <!-- CONTEUDO --> 
    </div>
    
<div class="ls-box">
  <h5 class="ls-title-3">RESUMO</h5>
  <p>
  
  	  - DIAS LETIVOS <strong><?php echo $contar['dialetivo']; ?></strong><br>
	  - SÁBADOS LETIVOS <strong><?php echo $contar['sabadoletivo']; ?></strong><br>
	  - TOTAL DE DIAS LETIVOS <strong><?php echo $contar['dialetivo'] + $contar['sabadoletivo']; ?></strong><br>
	  - FERIADOS NACIONAIS <strong><?php echo $contar['feriadonacional']; ?></strong><br>
	  - FERIADOS MUNICIPAIS <strong><?php echo $contar['feriadomunicipal']; ?></strong><br>
	  - DIAS DE RECESSO JUNINO <strong><?php echo $contar['recessojunino']; ?></strong><br>
	  - DIAS DE RECESSO DE NATAL <strong><?php echo $contar['recessonatal']; ?></strong><br>

	  - JORNADA PEDAGÓGICA <strong><?php echo $contar['jornadapedagogica']; ?></strong><br>
	  - ENCONTRO P/ PLANEJAMENTO <strong><?php echo $contar['encontroplanejamento']; ?></strong><br>
	  - CONSELHO DE CLASSE <strong><?php echo $contar['conselhoclasse']; ?></strong><br>
	  - ESTUDO DE RECUPERAÇÃO <strong><?php echo $contar['estudorecuperacao']; ?></strong><br>

  
  </p>
</div>
    
    <p>&nbsp;</p>
  </div>
</main>

<?php include_once "notificacoes.php"; ?>

  <div class="ls-modal" data-modal-blocked id="myAwesomeModal">
  <div class="ls-modal-box">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title">CALENDÁRIO ESCOLAR - CADASTRAR</h4>
    </div>
    <div class="ls-modal-body" id="myModalBody">
      <p>
      <div class="ls-alert-info"><strong>Atenção:</strong>
      Informe o intervado de datas e escolha o tipo de evento. (serão registrados apenas os dias úteis)
      </div>
      </p>
      
      <p>
      
      <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form">
        
        <label class="ls-label col-md-06">
        <b class="ls-label-text">DATA INICIAL</b>
        <input type="date" name="ce_data_de" value="<?php echo $row_AnoLetivo['ano_letivo_inicio']; ?>" size="32" required>
        </label>

        <label class="ls-label col-md-06">
        <b class="ls-label-text">DATA FINAL</b>
        <input type="date" name="ce_data_ate" value="<?php echo $row_AnoLetivo['ano_letivo_inicio']; ?>" size="32" required>
        </label>
        
        
        <label class="ls-label col-md-12">
        <b class="ls-label-text">TIPO DE EVENTO</b>
        <div class="ls-custom-select">
          <select name="ce_tipo" required>
            <option value=""> </option>
            <option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>1 - DIA LETIVO</option>
            <option value="2" <?php if (!(strcmp(2, ""))) {echo "SELECTED";} ?>>2 - SABADO LETIVO</option>
            <option value="3" <?php if (!(strcmp(3, ""))) {echo "SELECTED";} ?>>3 - FERIADO NACIONAL</option>
            <option value="4" <?php if (!(strcmp(4, ""))) {echo "SELECTED";} ?>>4 - FERIADO MUNICIPAL</option>
            <option value="5" <?php if (!(strcmp(5, ""))) {echo "SELECTED";} ?>>5 - RECESSO JUNINO</option>
            <option value="6" <?php if (!(strcmp(6, ""))) {echo "SELECTED";} ?>>6 - RECESSO DE NATAL</option>
			
            <option value="7" <?php if (!(strcmp(7, ""))) {echo "SELECTED";} ?>>7 - JORNADA PEDAGÓGICA</option>
            <option value="8" <?php if (!(strcmp(8, ""))) {echo "SELECTED";} ?>>8 - ENCONTRO P/ PLANEJAMENTO</option>
            <option value="9" <?php if (!(strcmp(9, ""))) {echo "SELECTED";} ?>>9 - CONSELHO DE CLASSE</option>
            <option value="10" <?php if (!(strcmp(10, ""))) {echo "SELECTED";} ?>>10 - ESTUDO DE RECUPERAÇÃO</option>
            <option value="11" <?php if (!(strcmp(11, ""))) {echo "SELECTED";} ?>>11 - ANO LETIVO 2020</option>
            <option value="12" <?php if (!(strcmp(12, ""))) {echo "SELECTED";} ?>>12 - ANO LETIVO 2021</option>
			
            <option value="13" <?php if (!(strcmp(13, ""))) {echo "SELECTED";} ?>>13 - PLANTÃO PEDAGÓGICO</option>
            <option value="14" <?php if (!(strcmp(14, ""))) {echo "SELECTED";} ?>>14 - CONSELHO DE CLASSE</option>
            <option value="15" <?php if (!(strcmp(15, ""))) {echo "SELECTED";} ?>>15 - RESULTADOS FINAIS</option>
          </select>
        </div>
        </label>
        
        
   
        
        <input type="hidden" name="MM_insert" value="form1">

      
      
      
            </p>
    </div>
    <div class="ls-modal-footer">
       <button type="submit" class="ls-btn-primary">SALVAR</button>    	
        <a href="calendario_escolar.php" class="ls-btn right">VOLTAR</a>
    </div>
    </form>
  </div>
</div><!-- /.modal -->

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>
</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

mysql_free_result($ListaCalendario);

mysql_free_result($ListaCalendarioMes);
?>