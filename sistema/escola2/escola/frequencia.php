<?php require_once('../../Connections/SmecelNovo.php'); ?>
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

$colname_vinculo = "-1";
if (isset($_GET['c'])) {
  $colname_vinculo = $_GET['c'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_vinculo = sprintf("
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, aluno_id, aluno_nome, aluno_nascimento, aluno_municipio_nascimento, aluno_uf_nascimento, aluno_filiacao1, aluno_filiacao2, aluno_endereco, aluno_numero, aluno_bairro, aluno_municipio, aluno_uf, turma_id, turma_nome 
FROM smc_vinculo_aluno INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma WHERE vinculo_aluno_id = %s
", GetSQLValueString($colname_vinculo, "int"));
$vinculo = mysql_query($query_vinculo, $SmecelNovo) or die(mysql_error());
$row_vinculo = mysql_fetch_assoc($vinculo);
$totalRows_vinculo = mysql_num_rows($vinculo);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">
<head>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-117872281-1');
</script>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sem título</title>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>

<body>

<h1 style="text-align:center;">DECLARAÇÃO</h1>

<div style="text-align:justify; margin: 10px 20px;">
Declaro para os devidos fins que <strong><?php echo $row_vinculo['aluno_nome']; ?></strong>, nascido em <strong><?php echo $row_vinculo['aluno_nascimento']; ?></strong>, natural de <strong><?php echo $row_vinculo['aluno_municipio_nascimento']; ?>-<?php echo $row_vinculo['aluno_uf_nascimento']; ?></strong>, filho(a) de <strong><?php echo $row_vinculo['aluno_filiacao1']; ?> e de <?php echo $row_vinculo['aluno_filiacao2']; ?></strong>, residente na <strong><?php echo $row_vinculo['aluno_endereco']; ?>, <?php echo $row_vinculo['aluno_numero']; ?>, <?php echo $row_vinculo['aluno_bairro']; ?>, <?php echo $row_vinculo['aluno_municipio']; ?>-<?php echo $row_vinculo['aluno_uf']; ?></strong>, é aluno(a) devidamente matriculado(a) e frequente nesta Unidade de Ensino, cursando o <strong><?php echo $row_vinculo['turma_nome']; ?></strong> no turno Vespertino do Ensino Fundamental no Ano Letivo de 2018.

<br />
<br />
<br />
<br />
____________________________________________<br />
Assinatura do Diretor

</div>

</body>
</html>
<?php
mysql_free_result($vinculo);
?>
