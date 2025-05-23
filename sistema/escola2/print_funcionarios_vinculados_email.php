<?php require_once('../../Connections/SmecelNovo.php'); ?>
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

$ordem = " ORDER BY func_nome ASC";
$escreve_ordem = "*Ordenado por nome";

if (isset($_GET['ordem'])) {
  $ordem = $_GET['ordem'];
	
  switch ($ordem) {
	  case "nome":
		  $ordem = " ORDER BY func_nome ASC";
		  $escreve_ordem = "*Ordenado por nome";
		  break;
		  
	  case "funcao":
		  $ordem = " ORDER BY funcao_nome ASC";
		  $escreve_ordem = "*Ordenado por função";
		  break;
		  
	  case "ch":
		  $ordem = " ORDER BY vinculo_carga_horaria ASC";
		  $escreve_ordem = "*Ordenado por carga horária";
		  break;
		  
	  case "data":
		  $ordem = " ORDER BY vinculo_data_inicio ASC";
		  $escreve_ordem = "*Ordenado por data de início";
		  break;
		  
		  default:
		  $ordem = "";
		  $escreve_ordem = "";
		  
  }
}


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_FuncionariosVinculados = "
SELECT 
vinculo_id, vinculo_id_escola, vinculo_id_funcionario, vinculo_id_funcao, vinculo_carga_horaria, 
vinculo_data_inicio, vinculo_obs, func_id, func_nome, func_telefone, func_celular1, func_celular2, func_email, funcao_id, funcao_nome 
FROM smc_vinculo 
INNER JOIN smc_func ON func_id = vinculo_id_funcionario 
INNER JOIN smc_funcao ON funcao_id = vinculo_id_funcao 
WHERE vinculo_id_escola = '$row_UsuLogado[usu_escola]' 
AND vinculo_status != 2
$ordem";
$FuncionariosVinculados = mysql_query($query_FuncionariosVinculados, $SmecelNovo) or die(mysql_error());
$row_FuncionariosVinculados = mysql_fetch_assoc($FuncionariosVinculados);
$totalRows_FuncionariosVinculados = mysql_num_rows($FuncionariosVinculados);
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

<title>RELAÇÃO DE FUNCIONÁRIOS (CONTATOS) | SMECEL - Sistema de Gestão Escolar</title>
<meta charset="utf-8">
<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<meta name="description" content="">
<meta name="keywords" content="">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<link rel="stylesheet" type="text/css" href="css/locastyle.css"> 
<script src="js/locastyle.js"></script>
	
		<style>
	table.bordasimples {border-collapse: collapse; font-size:7px; }
	table.bordasimples tr td {border:1px solid #808080; padding:2px; font-size:12px;}
	table.bordasimples tr th {border:1px solid #808080; padding:2px; font-size:9px;}
	.foo { 

 	writing-mode: vertical-lr;
	 -webkit-writing-mode: vertical-lr;
	 -ms-writing-mode: vertical-lr;

/* 	-webkit-transform:rotate(180deg); //tente 90 no lugar de 270
	-moz-transform:rotate(180deg);
	-o-transform: rotate(180deg); */
	
  }
</style>
	
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
  <body onload="self.print();">

		<div class="ls-txt-center">
		
		<?php if ($row_EscolaLogada['escola_logo']<>"") { ?><img src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt="" width="60px" /><?php } else { ?><img src="../../img/brasao_republica.png" alt="" width="60px" /><?php } ?><br>
		<strong><?php echo $row_EscolaLogada['escola_nome']; ?></strong><br>
		<small>INEP: <?php echo $row_EscolaLogada['escola_inep']; ?> AUT: - D.O. -<br>
		ENDEREÇO: <?php echo $row_EscolaLogada['escola_endereco']; ?>, <?php echo $row_EscolaLogada['escola_num']; ?>, <?php echo $row_EscolaLogada['escola_bairro']; ?> 
		<?php echo $row_EscolaLogada['sec_cidade']; ?>-<?php echo $row_EscolaLogada['sec_uf']; ?> CEP: <?php echo $row_EscolaLogada['escola_cep']; ?><br>
		CNPJ: <?php echo $row_EscolaLogada['escola_cnpj']; ?> <?php echo $row_EscolaLogada['escola_email']; ?> <?php echo $row_EscolaLogada['escola_telefone1']; ?></small>

		<br><br>
		<p><strong>RELAÇÃO DE FUNCIONÁRIOS (CONTATOS)</strong></p>
		
		</div>
        
		<?php if ($totalRows_FuncionariosVinculados > 0) { // Show if recordset not empty ?>
        
		<table width="100%" class="ls-sm-space ls-table-striped bordasimples">
          <thead>
            <tr>
              <th class="ls-data-descending">NOME</th>
              <th class="ls-data-descending ls-txt-center">FUNÇÃO</th>
              <th class="ls-data-descending ls-txt-center">EMAIL</th>
            </tr>
          </thead>
          <tbody>
            <?php do { ?>
              <tr>
                <td><?php echo $row_FuncionariosVinculados['func_nome']; ?></td>
                <td class="ls-txt-center"><?php echo $row_FuncionariosVinculados['funcao_nome']; ?></td>
                <td class="ls-txt-center"><?php echo $row_FuncionariosVinculados['func_email']; ?></td>
	  		 </tr><?php include_once('relatorios_rodape.php') ?>
              <?php } while ($row_FuncionariosVinculados = mysql_fetch_assoc($FuncionariosVinculados)); ?>
            </tbody>
            
        </table>
          <?php } else { ?>

            <?php include_once('relatorios_rodape.php') ?>

			  <div class="ls-alert-info"><strong>Atenção:</strong> Nenhum funcionário vinculado.</div>
          <?php } // Show if recordset not empty ?>
          
              

    <!-- We recommended use jQuery 1.10 or up -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="js/locastyle.js"></script>
 
  </body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($FuncionariosVinculados);
?>
