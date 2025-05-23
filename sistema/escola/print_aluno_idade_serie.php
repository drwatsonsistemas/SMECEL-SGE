<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include('fnc/inverteData.php'); ?>
<?php include('fnc/idade.php'); ?>
<?php include('fnc/idadeSerie.php'); ?>


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
$query_listaTurmas = "
SELECT 
turma_id, turma_id_escola, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo, etapa_id, etapa_idade,
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno 
FROM smc_turma
INNER JOIN smc_etapa ON etapa_id = turma_etapa 
WHERE turma_id_escola = '$row_UsuLogado[usu_escola]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'";
$listaTurmas = mysql_query($query_listaTurmas, $SmecelNovo) or die(mysql_error());
$row_listaTurmas = mysql_fetch_assoc($listaTurmas);
$totalRows_listaTurmas = mysql_num_rows($listaTurmas);


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

<title>Distorção Idade-série / <?php echo $row_EscolaLogada['escola_nome']; ?> / SMECEL / Sistema de Gestão Escolar</title>
<meta charset="utf-8">
<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<meta name="description" content="">
<meta name="keywords" content="">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<link rel="stylesheet" type="text/css" href="css/locastyle.css"><link rel="stylesheet" type="text/css" href="css/preloader.css">
<script src="js/locastyle.js"></script>
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">

  <style>
	
	table.bordasimples {border-collapse: collapse; font-size:10px; }
	table.bordasimples tr td {border:1px dotted #000000; padding:2px; font-size:10px;}
	table.bordasimples tr th {border:1px dotted #000000; padding:2px; font-size:10px;}

	</style>

</head>
  <body onload="self.print();">
   
 <!-- CONTEÚDO -->
        
  <div class="ls-box">
		<span class="ls-float-left" style="margin-right:20px;"><?php if ($row_EscolaLogada['escola_logo']<>"") { ?><img src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt="" width="100px" /><?php } else { ?><img src="../../img/brasao_republica.png" alt="" width="80px" /><?php } ?></span>
		<?php echo $row_EscolaLogada['escola_nome']; ?><br>
		<small>
		<?php echo $row_EscolaLogada['escola_endereco']; ?>, 
		<?php echo $row_EscolaLogada['escola_num']; ?> - 
		<?php echo $row_EscolaLogada['escola_bairro']; ?> - 
		<?php echo $row_EscolaLogada['escola_cep']; ?><br>
		CNPJ:<?php echo $row_EscolaLogada['escola_cnpj']; ?> INEP:<?php echo $row_EscolaLogada['escola_inep']; ?><br>
		<?php echo $row_EscolaLogada['escola_telefone1']; ?> <?php echo $row_EscolaLogada['escola_telefone2']; ?> <?php echo $row_EscolaLogada['escola_email']; ?>
		</small>
		</div>

			<div class="ls-txt-center" style="text-transform: uppercase;">
			<h3>RELATÓRIO DE DISTORÇÃO IDADE/SÉRIE(ANO)</h3>
			</div>


		

          <?php do { ?>
          
          
          
          <hr>
            
            <h4 class="ls-txt-center" ><?php echo $row_listaTurmas['turma_nome']; ?> - <?php echo $row_listaTurmas['turma_turno']; ?></h4><br>
            
            
            
            <?php
			
			mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_listaAlunos = "SELECT 
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, aluno_id, aluno_nome, aluno_nome_social,aluno_nascimento 
FROM smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
WHERE vinculo_aluno_id_turma = '$row_listaTurmas[turma_id]'";
$listaAlunos = mysql_query($query_listaAlunos, $SmecelNovo) or die(mysql_error());
$row_listaAlunos = mysql_fetch_assoc($listaAlunos);
$totalRows_listaAlunos = mysql_num_rows($listaAlunos);

			?>
            
			<?php if ($totalRows_listaAlunos>0) { ?>
			
      <table class="ls-sm-space bordasimples" width="100%" style="font-size:10px;">
			
			<thead>
				<tr>
          <th width="50px" class="ls-txt-center"></th>
					<th>ALUNO</th>
					<th width="100px" class="ls-txt-center">NASCIMENTO</th>
					<th width="70px" class="ls-txt-center">IDADE</th>
					<th width="150px" class="ls-txt-center">RESULTADO</th>
				</tr>
			</thead>
			<tbody>
			<?php $num = 1; ?>
      <?php do { ?>
            	<tr>
                <td class="ls-txt-center"><?php echo $num; $num++?></td>
                <td><?php echo $row_listaAlunos['aluno_nome_social']!= ""? $row_listaAlunos["aluno_nome_social"] : $row_listaAlunos["aluno_nome"]; ?></td>
                <td class="ls-txt-center"><?php echo inverteData($row_listaAlunos['aluno_nascimento']); ?></td>
                <td class="ls-txt-center"><?php echo idade($row_listaAlunos['aluno_nascimento']); ?></td>
                <td class="ls-txt-center">
				<?php if (idade($row_listaAlunos['aluno_nascimento']) <> "-") {
				if (idade($row_listaAlunos['aluno_nascimento']) > ($row_listaTurmas['etapa_idade']+1)) {
					$diferenca = idade($row_listaAlunos['aluno_nascimento']) - $row_listaTurmas['etapa_idade'];
					echo "<span class='ls-ico-chevron-up ls-ico-left ls-color-danger'> Acima ".$diferenca." ano(s)</span>";
				} else if (idade($row_listaAlunos['aluno_nascimento']) < $row_listaTurmas['etapa_idade']) {
					$diferenca = $row_listaTurmas['etapa_idade'] - idade($row_listaAlunos['aluno_nascimento']);
					echo "<span class='ls-ico-chevron-down ls-ico-left ls-color-danger'> Abaixo ".$diferenca." ano(s)</span>";
				} else {
					echo "<span class='ls-ico-checkmark ls-color-success'> </span>";
				}
				} else {
					echo "-";
				}
				?>
				</td>
            	</tr>
			<?php } while ($row_listaAlunos = mysql_fetch_assoc($listaAlunos)); ?>
			</tbody>
			</table>
			
			<?php } else { ?>
					<hr>
		<div class="ls-alert-warning">
            Nenhuma informação encontrada.
        </div>
			<?php } ?>
			
			
			<?php } while ($row_listaTurmas = mysql_fetch_assoc($listaTurmas)); ?>
        
        


    <!-- We recommended use jQuery 1.10 or up -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="js/locastyle.js"></script>
 
  </body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($listaTurmas);

mysql_free_result($listaAlunos);
?>
