<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include "fnc/print_exibeHorario.php"; ?>
<?php include "fnc/dataLocal.php"; ?>

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
$query_TurmasListar = "SELECT turma_id, turma_id_escola, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo, etapa_id, etapa_nome FROM smc_turma INNER JOIN smc_etapa ON etapa_id = turma_etapa WHERE turma_id_escola = $row_EscolaLogada[escola_id] AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' ORDER BY turma_turno, turma_etapa, turma_nome ASC";
$TurmasListar = mysql_query($query_TurmasListar, $SmecelNovo) or die(mysql_error());
$row_TurmasListar = mysql_fetch_assoc($TurmasListar);
$totalRows_TurmasListar = mysql_num_rows($TurmasListar);

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

    <title>QUADRO DE HORÁRIOS - <?php echo $row_EscolaLogada['escola_nome']; ?> | SMECEL</title>

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
	table.bordasimples {border-collapse: collapse;}
	table.bordasimples tr td {border-bottom:1px dotted #000000; padding:12px;}
	

	
	</style>
	
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
  <body onload="alert('Atenção: Configure sua impressora para o formato PAISAGEM');self.print();">




      <div class="container-fluid">
 
 
		<!-- CONTEÚDO -->
		
	
		
		
		<?php if ($totalRows_TurmasListar > 0) { // Show if recordset not empty ?>

        <?php do { ?>                
		<div style="page-break-inside: avoid;">	

<div class="ls-box ls-board-box">
  <header class="ls-info-header">
    <h2 class="ls-title-3"><?php echo $row_TurmasListar['turma_nome']; ?></h2>
    <p class="ls-float-right ls-float-none-xs ls-small-info">
	
	</p>
  </header>			
			
<table class="ls-sm-space bordasimples" width="100%" style="font-size:10px;">
  <thead>
    <tr>
      <th class="ls-txt-center" width="5%"></th>
      <th class="ls-txt-center" width="19%">SEGUNDA</th>
      <th class="ls-txt-center" width="19%">TERÇA</th>
      <th class="ls-txt-center" width="19%">QUARTA</th>
      <th class="ls-txt-center" width="19%">QUINTA</th>
      <th class="ls-txt-center" width="19%">SEXTA</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td class="ls-txt-center">1ª</td>
      <td class="ls-txt-center"><?php echo print_exibeHorario($row_TurmasListar['turma_id'],1,1); ?></td>
      <td class="ls-txt-center"><?php echo print_exibeHorario($row_TurmasListar['turma_id'],2,1); ?></td>
      <td class="ls-txt-center"><?php echo print_exibeHorario($row_TurmasListar['turma_id'],3,1); ?></td>
      <td class="ls-txt-center"><?php echo print_exibeHorario($row_TurmasListar['turma_id'],4,1); ?></td>
      <td class="ls-txt-center"><?php echo print_exibeHorario($row_TurmasListar['turma_id'],5,1); ?></td>
    </tr>
    <tr>
      <td class="ls-txt-center">2ª</td>
      <td class="ls-txt-center"><?php echo print_exibeHorario($row_TurmasListar['turma_id'],1,2); ?></td>
      <td class="ls-txt-center"><?php echo print_exibeHorario($row_TurmasListar['turma_id'],2,2); ?></td>
      <td class="ls-txt-center"><?php echo print_exibeHorario($row_TurmasListar['turma_id'],3,2); ?></td>
      <td class="ls-txt-center"><?php echo print_exibeHorario($row_TurmasListar['turma_id'],4,2); ?></td>
      <td class="ls-txt-center"><?php echo print_exibeHorario($row_TurmasListar['turma_id'],5,2); ?></td>
    </tr>
    <tr>
      <td class="ls-txt-center">3ª</td>
      <td class="ls-txt-center"><?php echo print_exibeHorario($row_TurmasListar['turma_id'],1,3); ?></td>
      <td class="ls-txt-center"><?php echo print_exibeHorario($row_TurmasListar['turma_id'],2,3); ?></td>
      <td class="ls-txt-center"><?php echo print_exibeHorario($row_TurmasListar['turma_id'],3,3); ?></td>
      <td class="ls-txt-center"><?php echo print_exibeHorario($row_TurmasListar['turma_id'],4,3); ?></td>
      <td class="ls-txt-center"><?php echo print_exibeHorario($row_TurmasListar['turma_id'],5,3); ?></td>
    </tr>
    <tr>
      <td class="ls-txt-center">4ª</td>
      <td class="ls-txt-center"><?php echo print_exibeHorario($row_TurmasListar['turma_id'],1,4); ?></td>
      <td class="ls-txt-center"><?php echo print_exibeHorario($row_TurmasListar['turma_id'],2,4); ?></td>
      <td class="ls-txt-center"><?php echo print_exibeHorario($row_TurmasListar['turma_id'],3,4); ?></td>
      <td class="ls-txt-center"><?php echo print_exibeHorario($row_TurmasListar['turma_id'],4,4); ?></td>
      <td class="ls-txt-center"><?php echo print_exibeHorario($row_TurmasListar['turma_id'],5,4); ?></td>
    </tr>
    <tr>
      <td class="ls-txt-center">5ª</td>
      <td class="ls-txt-center"><?php echo print_exibeHorario($row_TurmasListar['turma_id'],1,5); ?></td>
      <td class="ls-txt-center"><?php echo print_exibeHorario($row_TurmasListar['turma_id'],2,5); ?></td>
      <td class="ls-txt-center"><?php echo print_exibeHorario($row_TurmasListar['turma_id'],3,5); ?></td>
      <td class="ls-txt-center"><?php echo print_exibeHorario($row_TurmasListar['turma_id'],4,5); ?></td>
      <td class="ls-txt-center"><?php echo print_exibeHorario($row_TurmasListar['turma_id'],5,5); ?></td>
    </tr>
	</tbody>
</table>
			
<?php
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Funcionarios = "
SELECT 
ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_turma_id, func_id, func_nome 
FROM smc_ch_lotacao_professor
INNER JOIN smc_func ON func_id = ch_lotacao_professor_id
WHERE ch_lotacao_turma_id = $row_TurmasListar[turma_id]
GROUP BY ch_lotacao_professor_id ASC";
$Funcionarios = mysql_query($query_Funcionarios, $SmecelNovo) or die(mysql_error());
$row_Funcionarios = mysql_fetch_assoc($Funcionarios);
$totalRows_Funcionarios = mysql_num_rows($Funcionarios);
?>			


<?php if ($totalRows_Funcionarios > 0) { ?>

<small>
<?php do { ?>
  (<b><?php echo $row_Funcionarios['ch_lotacao_professor_id'] ?></b>) <?php echo $row_Funcionarios['func_nome'] ?>; 
<?php } while ($row_Funcionarios = mysql_fetch_assoc($Funcionarios)); ?>
</small>

</div>

<?php } ?>


		</div>
        <?php } while ($row_TurmasListar = mysql_fetch_assoc($TurmasListar)); ?>
			
        <?php } ?>
		
				  		<small></i>Relatório impresso em <?php echo date('d/m/Y'); ?> às <?php echo date('H:i:s'); ?>. <br>SMECEL - Sistema de Gestão Escolar</i></small>

		
		
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

mysql_free_result($TurmasListar);

mysql_free_result($Funcionarios);
?>