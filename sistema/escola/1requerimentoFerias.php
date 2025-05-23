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
vinculo_data_inicio, vinculo_obs, func_id, func_nome, func_cpf, func_rg_numero, funcao_id, funcao_nome 
FROM smc_vinculo
INNER JOIN smc_func ON vinculo_id_funcionario = func_id 
INNER JOIN smc_funcao ON vinculo_id_funcao = funcao_id 
WHERE vinculo_id_escola = '$row_EscolaLogada[escola_id]' AND vinculo_id = %s", GetSQLValueString($colname_FrequenciaFuncionario, "int"));
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
    <title>Requerimento de férias - <?php echo $row_FrequenciaFuncionario['func_nome']; ?> | SMECEL - Sistema de Gestão Escolar</title>
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
	font-size:7px;
}
table.bordasimples tr td {
	border:1px dotted #000000;
	padding:4px;
	font-size:9px;
}
table.bordasimples tr th {
	border:1px dotted #000000;
	padding:3px;
	font-size:9px;
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
      
      <div class="ls-box1 ls-sm-space" style="page-break-after: 1always;">
      <table width="100%">
	<tr>
		<td width="100px" class="ls-txt-center">
		<span><?php if ($row_EscolaLogada['escola_logo']<>"") { ?><img src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt="" width="80px" /><?php } else { ?><img src="../../img/brasao_republica.png" alt="" width="80px" /><?php } ?></span>
		</td>
		<td width="" class="ls-txt-center">
		<h1><strong><?php echo $row_EscolaLogada['sec_prefeitura']; ?></strong></h1>
		<h1><strong><?php echo $row_EscolaLogada['sec_nome']; ?></strong></h1>
		<?php echo $row_EscolaLogada['sec_endereco']; ?>, 
		<?php echo $row_EscolaLogada['sec_num']; ?> - 
		<?php echo $row_EscolaLogada['sec_bairro']; ?> - 
		<?php echo $row_EscolaLogada['escola_cep']; ?><br>
		<?php echo $row_EscolaLogada['sec_telefone1']; ?> <?php echo $row_EscolaLogada['sec_email']; ?> 
		</td>
		<td class="ls-txt-right" width="100px">
        <?php if ($row_EscolaLogada['sec_logo'] <> "") { ?>
				  <img src="../../img/logo/secretaria/<?php echo $row_EscolaLogada['sec_logo']; ?>" alt="Logo da <?php echo $row_EscolaLogada['sec_nome']; ?>" title="Logo da <?php echo $row_EscolaLogada['sec_nome']; ?>"  width="80" />
				<?php } else { ?>
				  <img src="../../img/brasao_republica.png" width="80">
				<?php } ?>
		</td>	
	</tr>
	</table>
      
    <br><br>
    <div class="ls-box"> 
    
    <h2 class="ls-txt-center"><?php echo $row_EscolaLogada['escola_nome']; ?><br><small>UNIDADE ESCOLAR</small></h2>
    
        </div>
        
        
        <div class="row">
  <div class="col-xs-12 ls-txt-center">
  
	<br><br><br><br><p><h1>SOLICITAÇÃO DE FÉRIAS</h1></p><br><br><br><br><br>
	
  </div>
</div>
        
        
  <p style="line-height: 180%; text-align:justify; font-size:16px;">

    Em conformidade com a Consolidação das Leis trabalhistas – CLT Art. 36 e 129, a Direção da(o) <?php echo $row_EscolaLogada['escola_nome']; ?>, 
    vem requerer de V.S.ª a concessão de férias ao funcionário(a) <strong><?php echo $row_FrequenciaFuncionario['func_nome']; ?></strong>, CPF <?php echo $row_FrequenciaFuncionario['func_cpf']; ?>, RG <?php echo $row_FrequenciaFuncionario['func_rg_numero']; ?>, lotado(a) neste estabelecimento de ensino no cargo/função <?php echo $row_FrequenciaFuncionario['funcao_nome']; ?>.
    <br><br>
    Período: de_______/_______/________ até_______/_______/________. 
    <br><br>


    Nestes termos, peço deferimento.
    
  </p>



<p style="line-height: 180%; text-align:right; font-size:16px;t">
<?php echo $row_EscolaLogada['sec_cidade']; ?>-<?php echo $row_EscolaLogada['sec_uf']; ?>, 
<?php 
setlocale(LC_TIME, 'pt_BR', 'utf-8', 'utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');
echo strftime('%d de %B de %Y', strtotime('today'));
?>
</p>
<br><br><br><br>

 <p style="line-height: 180%; text-align:center; font-size:16px;">_________________________________________________________<br>Servidor(a)</p>  
 <p style="line-height: 180%; text-align:center; font-size:16px;">_________________________________________________________<br>Diretor(a) ou Secretário(a) Escolar</p> <br><br> 

   
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
