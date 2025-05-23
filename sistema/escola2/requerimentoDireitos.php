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
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_tema, sec_id, sec_cidade, sec_prefeitura, sec_uf, sec_logo, sec_nome, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_email 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

$colname_FrequenciaFuncionario = "-1";
if (isset($_GET['c'])) {
  $colname_FrequenciaFuncionario = $_GET['c'];
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_FrequenciaFuncionario = sprintf("
SELECT vinculo_id, vinculo_id_escola, vinculo_id_funcionario, vinculo_id_funcao, vinculo_carga_horaria, 
vinculo_data_inicio, vinculo_obs, func_id, func_nome, func_cpf, func_rg_numero, func_data_nascimento, func_admissao, func_matricula, func_telefone, func_email, func_endereco, func_endereco_numero, func_endereco_bairro, funcao_id, funcao_nome 
FROM smc_vinculo
INNER JOIN smc_func ON vinculo_id_funcionario = func_id 
INNER JOIN smc_funcao ON vinculo_id_funcao = funcao_id 
WHERE vinculo_id_escola = '$row_EscolaLogada[escola_id]' AND func_id = %s", GetSQLValueString($colname_FrequenciaFuncionario, "int"));
$FrequenciaFuncionario = mysql_query($query_FrequenciaFuncionario, $SmecelNovo) or die(mysql_error());
$row_FrequenciaFuncionario = mysql_fetch_assoc($FrequenciaFuncionario);
$totalRows_FrequenciaFuncionario = mysql_num_rows($FrequenciaFuncionario);

$mes = date('m');
$ano = date('Y');
setlocale(LC_TIME,"portuguese"); 
$nome_mes = strtoupper(strftime('%B'));

if (isset($_GET['mes'])) {
	
  $mes = $_GET['mes'];
  
switch ($mes) {
	
	case 1:
		$nome_mes = "JANEIRO";
		break;
	
	case 2:
		$nome_mes = "FEVEREIRO";
		break;
	
	case 3:
		$nome_mes = "MARÇO";
		break;
	
	case 4:
		$nome_mes = "ABRIL";
		break;
	
	case 5:
		$nome_mes = "MAIO";
		break;
	
	case 6:
		$nome_mes = "JUNHO";
		break;
	
	case 7:
		$nome_mes = "JULHO";
		break;
	
	case 8:
		$nome_mes = "AGOSTO";
		break;
	
	case 9:
		$nome_mes = "SETEMBRO";
		break;
	
	case 10:
		$nome_mes = "OUTUBRO";
		break;
	
	case 11:
		$nome_mes = "NOVEMBRO";
		break;
	
	case 12:
		$nome_mes = "DEZEMBRO";
		break;
		
	default:
	header("Location: index.php");
	break;	
	
	}  

}


$dias = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);



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
<title>Requerimento de férias -<?php echo $row_FrequenciaFuncionario['func_nome']; ?>| SMECEL - Sistema de Gestão Escolar</title>
<meta charset="utf-8">
<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<meta name="description" content="">
<meta name="keywords" content="">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<link rel="stylesheet" type="text/css" href="css/locastyle.css"><script src="js/locastyle.js"></script>
<style media="print">
.no_imp {
	display: none;
}
.pagebreak {
	page-break-before: always;
}
</style>
<style>
table.bordasimples {
	border-collapse: collapse;
	font-size:10px;
}
table.bordasimples tr td {
	border:1px dotted #000000;
	padding:4px;
	font-size:10px;
}
table.bordasimples tr th {
	border:1px dotted #000000;
	padding:3px;
	font-size:10px;
}
</style>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<body onload="self.print();">
<div class="container-fluid"> 
  <!-- CONTEÚDO -->
  
  <table width="100%">
    <tr>
      <td width="100px" class="ls-txt-center"><span>
        <?php if ($row_EscolaLogada['escola_logo']<>"") { ?>
        <img src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt="" width="80px" />
        <?php } else { ?>
        <img src="../../img/brasao_republica.png" alt="" width="80px" />
        <?php } ?>
        </span></td>
      <td width="" class="ls-txt-center"><h3><strong><?php echo $row_EscolaLogada['sec_prefeitura']; ?></strong></h3>
        <h3><strong><?php echo $row_EscolaLogada['sec_nome']; ?></strong></h3>
        <?php echo $row_EscolaLogada['sec_endereco']; ?>, <?php echo $row_EscolaLogada['sec_num']; ?> - <?php echo $row_EscolaLogada['sec_bairro']; ?> - <?php echo $row_EscolaLogada['escola_cep']; ?><br>
        <?php echo $row_EscolaLogada['sec_telefone1']; ?> <?php echo $row_EscolaLogada['sec_email']; ?></td>
      <td class="ls-txt-right" width="100px"><?php if ($row_EscolaLogada['sec_logo'] <> "") { ?>
        <img src="../../img/logo/secretaria/<?php echo $row_EscolaLogada['sec_logo']; ?>" alt="Logo da <?php echo $row_EscolaLogada['sec_nome']; ?>" title="Logo da <?php echo $row_EscolaLogada['sec_nome']; ?>"  width="80" />
        <?php } else { ?>
        <img src="../../img/brasao_republica.png" width="80">
        <?php } ?></td>
    </tr>
  </table>
  <div class="row">
    <div class="col-xs-12 ls-txt-center">
      <br><p>
      <h3>FICHA DE REQUERIMENTO<br>
        <small>DADOS PESSOAIS</small></h3>
      </p>
    </div>
  </div>
  
  <div class="ls-box1 row">
  <table class="ls-table1 bordasimples" width="100%" style="">
    <tr>
      <td><strong>MATRÍCULA</strong>
        <?php echo $row_FrequenciaFuncionario['func_matricula']; ?></td>
      <td><strong>ADMISSÃO</strong>
        <?php echo date("d/m/Y", strtotime($row_FrequenciaFuncionario['func_admissao'])); ?></td>
    </tr>
    <tr>
      <td><strong>NOME</strong>
        <?php echo $row_FrequenciaFuncionario['func_nome']; ?></td>
      <td><strong>CARGO/FUNÇÃO</strong>
        <?php echo $row_FrequenciaFuncionario['funcao_nome']; ?></td>
    </tr>
    <tr>
      <td><strong>CPF</strong>
        <?php echo $row_FrequenciaFuncionario['func_cpf']; ?></td>
      <td><strong>RG</strong>
        <?php echo $row_FrequenciaFuncionario['func_rg_numero']; ?></td>
    </tr>
    <tr>
      <td><strong>LOCAL DE TRABALHO</strong>
        <?php echo $row_EscolaLogada['escola_nome']; ?></td>
      <td><strong>ENDEREÇO</strong>
        <?php echo $row_FrequenciaFuncionario['func_endereco']; ?>, <?php echo $row_FrequenciaFuncionario['func_endereco_numero']; ?>, <?php echo $row_FrequenciaFuncionario['func_endereco_bairro']; ?></td>
    </tr>
    <tr>
      <td><strong>TELEFONE</strong>
        <?php echo $row_FrequenciaFuncionario['func_telefone']; ?></td>
      <td><strong>EMAIL</strong>
        <?php echo $row_FrequenciaFuncionario['func_email']; ?></td>
    </tr>
  </table>
  </div>
  
  <div class="row ls-box1">
  <br><br>
    <div class="col-xs-12 ls-txt-center">
      <h3>REQUERIMENTOS</h3>
      <br>
    </div>
 
  
  <table width="100%" class="bordasimples" style="">
    <tr>
      <td>&#9744; ABONO DE FÉRIAS</td>
      <td>&#9744; ABONO/ESTORNO DE FALTAS </td>
      <td>&#9744; ABONO PECUNIÁRIO</td>
    </tr>
    <tr>
      <td>&#9744; AVERBAÇÃO DE TEMPO DE SERVIÇO</td>
      <td>&#9744; ADITAMENTO DO 13º SALÁRIO (6/12 AVOS) </td>
      <td>&#9744; APOSENTADORIA</td>
    </tr>
    <tr>
      <td>&#9744; CERTIDÃO DE TEMPO DE SERVIÇO</td>
      <td>&#9744; CONTAGEM DE TEMPO DE SERVIÇO</td>
      <td>&#9744; DECLARAÇÃO</td>
    </tr>
    <tr>
      <td>&#9744; DISPENSA DE FUNÇÃO</td>
      <td>&#9744; FÉRIAS (1/3)</td>
      <td>&#9744; HORAS EXTRAS</td>
    </tr>
    <tr>
      <td>&#9744; INC. DE DEPEND. IRRF</td>
      <td>&#9744; INC. DE DEPEND. SALÁRIO FAMÍLIA</td>
      <td>&#9744; LIC. SEM REMUNERAÇÃO</td>
    </tr>
    <tr>
      <td>&#9744; LIC. PRÊMIO POR ASSIDUIDADE</td>
      <td>&#9744; LIC. POR INTERESSE PARTICULAR</td>
      <td>&#9744; LIC. PATERNIDADE</td>
    </tr>
    <tr>
      <td>&#9744; LIC. PARA ATIVIDADES POLÍTICAS</td>
      <td>&#9744; LIC. PARA TRATAMENTO DE SAÚDE</td>
      <td>&#9744; LIC. MATERNIDADE</td>
    </tr>
    <tr>
      <td>&#9744; MUDANÇA DE CONTA CORRENTE</td>
      <td>&#9744; MUDANÇA DE ENDEREÇO </td>
      <td>&#9744; PENSÃO</td>
    </tr>
    <tr>
      <td>&#9744; PROGR. FUNCIONAL POR TITULAÇÃO - ADM.</td>
      <td>&#9744; PROGR. FUNCIONAL POR TITULAÇÃO - DOCENTE</td>
      <td>&#9744; PROGR. DE FAIXA</td>
    </tr>
    <tr>
      <td>&#9744; PEDIDO DE DESLIG. POR MOTIVO DE APOSENT.</td>
      <td>&#9744; REINTEGRAÇÃO</td>
      <td>&#9744; RESCISÃO DE CONTRATO</td>
    </tr>
    <tr>
      <td>&#9744; RETIF. DE TEMPO DE SERVIÇO</td>
      <td>&#9744; OUTROS</td>
      <td></td>
    </tr>
    </table>
    
    <br><br>
    
    <table width="100%" class="bordasimples" style="">
    <tr>
    	<td colspan="3">
        <strong>Justificativa/Observação</strong><br><br><br><br><br>
        </td>
    </tr>
    </table>
    
    <br><br>
    <table width="100%" class="bordasimples" style="">
    
    <tr>
    	<td colspan="3">
        <h3 class="ls-txt-center">DECLARAÇÃO</h3>
      Nestes termos<br>
      P. deferimento <br><br>
<?php echo $row_EscolaLogada['sec_cidade']; ?>-<?php echo $row_EscolaLogada['sec_uf']; ?>, 
<?php 
setlocale(LC_TIME, 'pt_BR', 'utf-8', 'utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');
echo strftime('%d de %B de %Y', strtotime('today'));
?>
      
      <p class=" ls-txt-center"> _________________________________________<br>
        Assinatura </p>
        </td>
    </tr>
    
    <tr>
    	<td colspan="3">
		<h3 class="ls-txt-center">PARECER</h3>
      		&#9744; Providenciado  <br><br><br>
      		<p class=" ls-txt-center"> _________________________________________<br>
        	Assinatura </p>        
        </td>
    </tr>
    
    <tr>
    	<td colspan="3">
      <h3 class="ls-txt-center">DEFERIMENTO</h3>
      &#9744; Deferido  <br>
      &#9744; Indeferido  <br><br>
      <p class=" ls-txt-center"> _________________________________________<br>
        Assinatura </p>        
        </td>
    </tr>
    

    
    
  </table>
  
  </div>
  
<div style="position:fixed; bottom:0" class="ls-txt-center">
	<p class="ls-txt-center" style="text-align:center"><small>Impresso em <?php echo date("d/m/Y à\s H:i:s"); ?> | SMECEL - Sistema de Gestão Escolar | www.smecel.com.br</small></p>
</div>

  
  
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

mysql_free_result($FrequenciaFuncionario);
?>
