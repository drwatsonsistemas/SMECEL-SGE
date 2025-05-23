<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include('fnc/inverteData.php'); ?>
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

$colname_oficio = "-1";
if (isset($_GET['oficio'])) {
  $colname_oficio = $_GET['oficio'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_oficio = sprintf("
SELECT oficio_id, oficio_id_escola, oficio_numeracao, oficio_ano_letivo, oficio_data, 
oficio_cabecalho, oficio_texto, oficio_diretor, oficio_hash 
FROM smc_oficios 
WHERE oficio_hash = %s", GetSQLValueString($colname_oficio, "text"));
$oficio = mysql_query($query_oficio, $SmecelNovo) or die(mysql_error());
$row_oficio = mysql_fetch_assoc($oficio);
$totalRows_oficio = mysql_num_rows($oficio);


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

<title>OFÍCIO Nº <?php echo $row_oficio['oficio_numeracao']; ?>/<?php echo $row_oficio['oficio_ano_letivo']; ?> - <?php echo $row_EscolaLogada['escola_nome']; ?></title>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link rel="stylesheet" type="text/css" href="css/locastyle.css">    <link rel="stylesheet" type="text/css" href="css/preloader.css">
<script src="js/locastyle.js"></script>
<style>

body {
	font-size: 16px;

	background-image:url(<?php if ($row_EscolaLogada['escola_logo']<>"") { ?>../../img/marcadagua/<?php echo $row_EscolaLogada['escola_logo']; ?><?php } else { ?>../../img/marcadagua/brasao_republica.png<?php } ?>);
	background-repeat:no-repeat;
	background-position:center center;
	z-index:-999;
  
}
p { margin-bottom: 1px; }
page {
  display: block;
  margin: 0 auto;
  margin-bottom: 0.5cm;

  }
page[size="A4"] {
  width: 21cm;
  height: 29.7cm;
  border: dotted 0px gray;
  padding: 5px; 
}
page[size="A4"][layout="portrait"] {
  width: 29.7cm;
  height: 21cm;
}
@media print {
  body,
  page {
    margin: 0;
    box-shadow: 0;
  }
}



</style>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<body onload="self.print();">

<!-- CONTEÚDO -->


<page size="A4" style="padding:30px;">



<table>
	<tr>
		<td>
			<?php if ($row_EscolaLogada['escola_logo']<>"") { ?><img src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt="" width="100px" /><?php } else { ?><img src="../../img/brasao_republica.png" alt="" width="80px" /><?php } ?>
		</td>
		<td>
			<p><strong><?php echo $row_EscolaLogada['escola_nome']; ?></strong></p>
			<p>INEP: <?php echo $row_EscolaLogada['escola_inep']; ?> AUT: - D.O. -</p>
			<p>ENDEREÇO: <?php echo $row_EscolaLogada['escola_endereco']; ?>, <?php echo $row_EscolaLogada['escola_num']; ?>, <?php echo $row_EscolaLogada['escola_bairro']; ?> - 
			 <?php echo $row_EscolaLogada['sec_cidade']; ?> - <?php echo $row_EscolaLogada['sec_uf']; ?> - CEP: <?php echo $row_EscolaLogada['escola_cep']; ?></p>
			<p>CNPJ: <?php echo $row_EscolaLogada['escola_cnpj']; ?> 
			<?php echo $row_EscolaLogada['escola_email']; ?> <?php echo $row_EscolaLogada['escola_telefone1']; ?></p>
		</td>
	</tr>
</table>



<div class="row">
  <div class="col-xs-12">
	<br><br><br><br><p style="line-height: 180%; text-align:justify; font-size:16px;">OFÍCIO Nº <?php echo $row_oficio['oficio_numeracao']; ?>/<?php echo $row_oficio['oficio_ano_letivo']; ?></p>
  </div>
</div>

<div class="row">
  <div class="col-xs-12 ls-txt-right">
	<p style="line-height: 180%; font-size:16px;">
<?php echo $row_EscolaLogada['sec_cidade']; ?>-<?php echo $row_EscolaLogada['sec_uf']; ?>, 
<?php 
setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');
$dia = strftime('%d', strtotime($row_oficio['oficio_data']));
$mes = strftime('%B', strtotime($row_oficio['oficio_data']));
$mes = utf8_encode($mes);
$ano = strftime('%Y', strtotime($row_oficio['oficio_data']));
echo "$dia de $mes de $ano";
?>
</p>
  </div>
</div>
  

<div class="row">
  <div class="col-xs-12">
	<br><br><br><br>
	<p style="line-height: 130%; text-align:justify; font-size:16px;"><?php echo nl2br($row_oficio['oficio_cabecalho']); ?></p>
  </div>
</div>  
  
<div class="row">
  <div class="col-xs-12">
  <br><br><br><br><p style="line-height: 180%; text-align:justify; font-size:16px;">
  
  <?php echo nl2br($row_oficio['oficio_texto']); ?>
  
  
  </p> 
  </div>
</div>

<div class="row"><div class="col-xs-12"><p></p></div></div>


<div class="row"><div class="col-xs-12"><p><br><br><br><br><br></p></div></div>
<p style="line-height: 130%; text-align:center; font-size:14px;">_________________________________________________________<br><strong><?php echo $row_oficio['oficio_diretor']; ?></strong><br>DIREÇÃO ESCOLAR</p>
<div class="row"><div class="col-xs-12"><p><br><br><br><br><br></p></div></div>

</div>
</div>

<hr>
<div style="bottom:0; width:100%; display:block; text-align:center" class="ls-box">

			<?php
			$aux = 'fnc/qr/php/qr_img.php?';
			$aux .= 'd=https://www.smecel.com.br/publico/oficio.php?oficio='.$row_oficio['oficio_hash'].'&';
			$aux .= 'e=M&';
			$aux .= 's=2&';
			$aux .= 't=P';
			?>
<div>
<img src="<?php echo $aux; ?>" align="absmiddle" style="float:left;" />
<small><strong>CERTIFICADO DE VALIDADE</strong></small><br><br>
<small><strong>https://www.smecel.com.br/publico/oficio.php?oficio=<?php echo $row_oficio['oficio_hash']; ?></strong></small><br>
<small>Digite o endereço no navegador ou aponte a câmera com leitor de QR Code para visualizar este ofício</small>
</div>


</div>


</page>



<!-- CONTEÚDO --> 

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
<script src="js/locastyle.js"></script>
 

</body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($oficio);
?>