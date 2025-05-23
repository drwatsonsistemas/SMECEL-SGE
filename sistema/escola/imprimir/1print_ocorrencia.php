<?php require_once('../../../Connections/SmecelNovo.php'); ?>
<?php include('../fnc/inverteData.php'); ?>
<?php //include "../fnc/anoLetivo.php"; ?>

<?php include "../fnc/session.php"; ?>
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

include "../usuLogado.php";
include "../fnc/anoLetivo.php";

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


$colname_Matricula = "-1";
if (isset($_GET['hash'])) {
  $colname_Matricula = $_GET['hash'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matricula = sprintf("
SELECT 
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao,
aluno_id,
aluno_cod_inep,
aluno_cpf,
aluno_nome,
aluno_nascimento,
aluno_filiacao1,
aluno_filiacao2,
aluno_sexo,
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
aluno_cpf_pai,
aluno_hash,
turma_id,
turma_nome,
turma_etapa,
turma_turno,
turma_ano_letivo,
etapa_id,
etapa_id_filtro,
etapa_nome,
municipio_id,
municipio_cod_ibge,
municipio_nome,
municipio_sigla_uf
FROM 
smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
INNER JOIN smc_etapa ON etapa_id = turma_etapa
INNER JOIN smc_municipio ON municipio_cod_ibge = aluno_municipio_nascimento_ibge
WHERE vinculo_aluno_hash = %s", GetSQLValueString($colname_Matricula, "text"));
$Matricula = mysql_query($query_Matricula, $SmecelNovo) or die(mysql_error());
$row_Matricula = mysql_fetch_assoc($Matricula);
$totalRows_Matricula = mysql_num_rows($Matricula);

$colname_Ocorrencia = "-1";
if (isset($_GET['ocorrencia'])) {
  $colname_Ocorrencia = $_GET['ocorrencia'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Ocorrencia = sprintf("
SELECT ocorrencia_id, ocorrencia_id_aluno, ocorrencia_id_turma, ocorrencia_id_escola, ocorrencia_ano_letivo, ocorrencia_data, 
ocorrencia_hora, ocorrencia_tipo, ocorrencia_afastamento_de, ocorrencia_afastamento_ate, ocorrencia_total_dias, ocorrencia_descricao,
CASE ocorrencia_tipo
WHEN 1 THEN 'ADVERTÊNCIA'
WHEN 2 THEN 'SUSPENSÃO'
END AS ocorrencia_tipo_nome 
FROM smc_ocorrencia WHERE ocorrencia_id = %s", GetSQLValueString($colname_Ocorrencia, "int"));
$Ocorrencia = mysql_query($query_Ocorrencia, $SmecelNovo) or die(mysql_error());
$row_Ocorrencia = mysql_fetch_assoc($Ocorrencia);
$totalRows_Ocorrencia = mysql_num_rows($Ocorrencia);


?>
<!DOCTYPE html>
<html class="<?php echo $row_EscolaLogada['escola_tema']; ?>">
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
<link rel="stylesheet" type="text/css" href="../css/locastyle.css">
 
<script src="../js/locastyle.js"></script>
<style>

body {
  font-size: 12px;

	background-image:url(<?php if ($row_EscolaLogada['escola_logo']<>"") { ?>../../../img/marcadagua/<?php echo $row_EscolaLogada['escola_logo']; ?><?php } else { ?>../../../img/marcadagua/brasao_republica.png<?php } ?>);
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
  border: dotted 1px gray;
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
	<body onload="alert('Atenção: Configure sua impressora para o formato RETRATO');self.print();">

<!-- CONTEÚDO -->
 



<page size="A4" style="padding:30px;">



<table>
	<tr>
		<td width="20%"><?php if ($row_EscolaLogada['escola_logo']<>"") { ?><img src="../../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt="" width="100px" /><?php } else { ?><img src="../../../img/brasao_republica.png" alt="" width="80px" /><?php } ?></td>
		<td width="80%">
			<p><strong><?php echo $row_EscolaLogada['escola_nome']; ?></strong></p>
			<p>INEP: <?php echo $row_EscolaLogada['escola_inep']; ?> AUT: - D.O. -</p>
			<p>ENDEREÇO: <?php echo $row_EscolaLogada['escola_endereco']; ?>, <?php echo $row_EscolaLogada['escola_num']; ?>, <?php echo $row_EscolaLogada['escola_bairro']; ?></p>
			<p><?php echo $row_EscolaLogada['sec_cidade']; ?>-<?php echo $row_EscolaLogada['sec_uf']; ?> CEP: <?php echo $row_EscolaLogada['escola_cep']; ?></p>
			<p>CNPJ: <?php echo $row_EscolaLogada['escola_cnpj']; ?></p>
			<p><?php echo $row_EscolaLogada['escola_email']; ?> <?php echo $row_EscolaLogada['escola_telefone1']; ?></p>
		</td>
	</tr>
</table>



<div class="row"><div class="col-xs-12"><p></p></div></div>

<div class="row">
  <div class="col-xs-12 ls-txt-center">
  
	<br><br><br><br><br><br><br><br><p><h1>OCORRÊNCIA DE <?php echo $row_Ocorrencia['ocorrencia_tipo_nome']; ?></h1></p><br><br><br><br><br><br><br>
	
  </div>
</div>

  

<div class="row">
  <div class="col-xs-12">
  
  <p style="line-height: 180%; text-align:justify; font-size:16px;">
  Foi registrada a ocorrência de <?php echo $row_Ocorrencia['ocorrencia_tipo_nome']; ?> do(a) aluno(a) <strong><?php echo $row_Matricula['aluno_nome']; ?></strong>, nascido(a) em <strong><?php echo inverteData($row_Matricula['aluno_nascimento']); ?></strong>, 
  natural de <strong><?php echo $row_Matricula['municipio_nome']; ?>-<?php echo $row_Matricula['municipio_sigla_uf']; ?></strong>, filho(a) de <strong><?php echo $row_Matricula['aluno_filiacao1']; ?></strong><?php if ($row_Matricula['aluno_filiacao2']<>"") { ?> e <strong><?php echo $row_Matricula['aluno_filiacao2']; ?></strong><?php } ?>,
  residente na <strong><?php echo $row_Matricula['aluno_endereco']; ?>, <?php echo $row_Matricula['aluno_numero']; ?> - <?php echo $row_Matricula['aluno_bairro']; ?>, <?php echo $row_Matricula['aluno_municipio']; ?>-<?php echo $row_Matricula['aluno_uf']; ?></strong>,
  matriculado(a) e frequente nesta Unidade de Ensino, cursando o <strong><?php echo $row_Matricula['turma_nome']; ?> (<?php echo $row_Matricula['etapa_nome']; ?>)</strong>, no turno <strong><?php 
  switch ($row_Matricula['turma_turno']) {
	  
	  case 1:
	  echo "MATUTINO";
	  break;
	  case 2:
	  echo "VESPERTINO";
	  break;
	  case 3:
	  echo "NOTURNO";
	  break;
	  default:
	  echo "-";
	  break;
	  
  } 
  ?></strong>, neste ano letivo de <strong><?php echo $row_Matricula['turma_ano_letivo']; ?></strong>, com os seguintes detalhes: </p>
  
  <br>
  <hr>
 <p style="line-height: 180%; text-align:justify; font-size:16px;">
  
  TIPO: <strong><?php echo $row_Ocorrencia['ocorrencia_tipo_nome']; ?></strong><br>
  DATA: <strong><?php echo inverteData($row_Ocorrencia['ocorrencia_data']); ?></strong><br> 
  HORA: <strong><?php echo $row_Ocorrencia['ocorrencia_hora']; ?></strong><br> 
  <?php if ($row_Ocorrencia['ocorrencia_tipo']=="2") { ?>
  INÍCIO DO AFASTAMENTO: <strong><?php echo inverteData($row_Ocorrencia['ocorrencia_afastamento_de']); ?></strong><br>
  TÉRMINO DO AFASTAMENTO: <strong><?php echo inverteData($row_Ocorrencia['ocorrencia_afastamento_ate']); ?></strong><br>
  TOTAL DE DIAS EM AFASTAMENTO: <strong><?php echo $row_Ocorrencia['ocorrencia_total_dias']; ?></strong><br>
  <?php } ?> 
  MOTIVO: <strong><?php echo $row_Ocorrencia['ocorrencia_descricao']; ?></strong><br> 

  </p>
  <hr>
  
  
  
  <div class="row"><div class="col-xs-12"><p><br><br><br></p></div></div>
<p style="text-align:center">_________________________________________________________<br>Responsável pelo(a) aluno(a)</p>
<div class="row"><div class="col-xs-12"><p><br><br><br><br></p></div></div>
<p style="text-align:right">
<?php echo $row_EscolaLogada['sec_cidade']; ?>-<?php echo $row_EscolaLogada['sec_uf']; ?>, 
<?php 
setlocale(LC_TIME, 'pt_BR', 'utf-8', 'utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');
echo utf8_encode(strftime('%d de %B de %Y', strtotime('today')));
?>
</p>
</div>
</div>

  
  
  
  </div>
</div>

<div class="row"><div class="col-xs-12"><p></p></div></div>




</page>

    <!-- We recommended use jQuery 1.10 or up -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="../js/locastyle.js"></script>
 
  </body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($Ocorrencia);

mysql_free_result($EscolaLogada);
?>
