<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include('fnc/inverteData.php'); ?>
<?php include "fnc/anti_injection.php"; ?>
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

$colname_UsuLogado = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_UsuLogado = $_SESSION['MM_Username'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_UsuLogado = sprintf("SELECT usu_id, usu_nome, usu_email, usu_senha, usu_tipo, usu_sec, usu_escola, usu_status, usu_cadastro FROM smc_usu WHERE usu_email = %s", GetSQLValueString($colname_UsuLogado, "text"));
$UsuLogado = mysql_query($query_UsuLogado, $SmecelNovo) or die(mysql_error());
$row_UsuLogado = mysql_fetch_assoc($UsuLogado);
$totalRows_UsuLogado = mysql_num_rows($UsuLogado);
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


$codTurma = "";
$buscaTurma = "";
if (isset($_GET['ct'])) {
	
	if ($_GET['ct'] == "") {
	//echo "TURMA EM BRANCO";	
	header("Location: turmasAlunosVinculados.php?nada"); 
 	exit;
	}
	
  $codTurma = anti_injection($_GET['ct']);
  $codTurma = (int)$codTurma;
  $buscaTurma = "AND turma_id = $codTurma ";
}

$stCod = "";
$stqry = "";

if (isset($_GET['st'])) {	
  $stCod = anti_injection($_GET['st']);
  $stCod = (int)$stCod;
}

if (isset($_GET['st'])) {	
    $stCod = anti_injection($_GET['st']);
    $stCod = (int)$stCod;
  }

	//$st = "1";
	//$stqry = "AND vinculo_aluno_situacao = $st ";
	if (isset($_GET['st'])) {
	
	if ($_GET['st'] == "") {
	//echo "TURMA EM BRANCO";	
	header("Location: turmasAlunosVinculados.php?nada"); 
 	exit;
	}
	
	$st = anti_injection($_GET['st']);
	$st = (int)$st;
	$stqry = "AND vinculo_aluno_situacao = $st ";
	}

$row_AnoLetivo['ano_letivo_ano'] = $row_AnoLetivo['ano_letivo_ano']-1;
	
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ExibirTurmas = "
SELECT turma_id, turma_id_escola, turma_nome, turma_etapa, turma_turno,
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno_nome, 
turma_total_alunos, turma_ano_letivo 
FROM smc_turma 
WHERE turma_id_escola = '$row_UsuLogado[usu_escola]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' $buscaTurma
ORDER BY turma_turno, turma_etapa, turma_nome ASC";
$ExibirTurmas = mysql_query($query_ExibirTurmas, $SmecelNovo) or die(mysql_error());
$row_ExibirTurmas = mysql_fetch_assoc($ExibirTurmas);
$totalRows_ExibirTurmas = mysql_num_rows($ExibirTurmas);

if ($totalRows_ExibirTurmas == "") {
	//echo "TURMA EM BRANCO";	
	header("Location: turmasAlunosVinculados.php?nada"); 
 	exit;
	}


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

    <title>Alunos deistentes | SMECEL - Sistema de Gestão Escolar</title>

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
	table.bordasimples tr td {border:1px dotted #000000; padding:2px; font-size:9px;}
	table.bordasimples tr th {border:1px dotted #000000; padding:2px; font-size:9px;}

	</style>
	
	
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
  <body onload="self.print();">




      <div class="container-fluid">
 
	  
	  		
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

			<div class="ls-box ls-txt-center" style="text-transform: uppercase;">
			RELATÓRIO DE ALUNOS DESISTENTES | <?php echo $row_AnoLetivo['ano_letivo_ano']; ?>
			</div>
		
		<!-- CONTEÚDO -->
        
		<?php $totalAlunosEscola = 0; ?>
        
        <?php do { ?>
        <?php
        
		mysql_select_db($database_SmecelNovo, $SmecelNovo);
		$query_ExibirAlunosVinculados = "
		SELECT 
		vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, 
		vinculo_aluno_hash, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_conselho,
		aluno_id, aluno_nome, aluno_nome_social,aluno_nascimento, aluno_filiacao1, aluno_hash,aluno_endereco,aluno_cep,aluno_numero,aluno_bairro,aluno_municipio,aluno_uf,aluno_telefone,aluno_celular
		FROM smc_vinculo_aluno 
		INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
		WHERE vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' 
        AND vinculo_aluno_id_turma = '$row_ExibirTurmas[turma_id]'
        AND vinculo_aluno_situacao = '3' 
		ORDER BY aluno_nome ASC";
		$ExibirAlunosVinculados = mysql_query($query_ExibirAlunosVinculados, $SmecelNovo) or die(mysql_error());
		$row_ExibirAlunosVinculados = mysql_fetch_assoc($ExibirAlunosVinculados);
		$totalRows_ExibirAlunosVinculados = mysql_num_rows($ExibirAlunosVinculados);
		?>
        
		
			
			
	        
        
        <?php if ($totalRows_ExibirAlunosVinculados > 0) { ?>
		
		<div class="ls-box ls-sm-space">
        
		<?php $contaAlunos = 1; ?>
		
		<h5 class="ls-title-5 ls-txt-center"><?php echo $row_ExibirTurmas['turma_nome']; ?> - <?php echo $row_ExibirTurmas['turma_turno_nome']; ?> </h5>

		<table class="ls-sm-space bordasimples" width="100%" style="font-size:10px;">
		<thead>
			<tr>
				<th width="5%" class="ls-txt-center" width="35px">Nº</th>
				<th width="40%">ALUNO</th>
				<th width="25%" class="ls-txt-center">ENDEREÇO</th>
                <th width="15%" class="ls-txt-center">TELEFONE</th>
				<th width="40%" class="ls-txt-center">FILIAÇÃO</th>

			</tr>
			<tbody>
			<?php do { ?>
				<tr style="border-bottom:black solid 1 px;">
					<td class="ls-txt-center"><?php 
					echo $contaAlunos;
					$contaAlunos++;		
					?></td>
					<td><?php echo $row_ExibirAlunosVinculados['aluno_nome_social'] != "" ? $row_ExibirAlunosVinculados["aluno_nome_social"] : $row_ExibirAlunosVinculados["aluno_nome"]; ?></td>
					<td class="ls-txt-center"><?php echo "$row_ExibirAlunosVinculados[aluno_endereco], $row_ExibirAlunosVinculados[aluno_numero] - $row_ExibirAlunosVinculados[aluno_bairro], $row_ExibirAlunosVinculados[aluno_municipio] - $row_ExibirAlunosVinculados[aluno_uf], CEP $row_ExibirAlunosVinculados[aluno_cep]" ?></td>
                    <td class="ls-txt-center"><?php echo empty($row_ExibirAlunosVinculados['aluno_celular']) ? (isset($row_ExibirAlunosVinculados['aluno_telefone']) ? $row_ExibirAlunosVinculados['aluno_telefone'] : '') : ($row_ExibirAlunosVinculados['aluno_celular'] . ' / ' . (isset($row_ExibirAlunosVinculados['aluno_telefone']) ? $row_ExibirAlunosVinculados['aluno_telefone'] : '')); ?></td>
					<td class="ls-txt-center"><?php echo $row_ExibirAlunosVinculados['aluno_filiacao1']; ?></td>
				</tr>
				<?php } while ($row_ExibirAlunosVinculados = mysql_fetch_assoc($ExibirAlunosVinculados)); ?>
		</tbody>
		</table>
		 
		<p>Alunos desistentes na turma: <strong><?php echo $totalRows_ExibirAlunosVinculados; ?></strong></p>
		
		</div>
		
		
		<?php } ?>
          
          
         <?php $totalAlunosEscola = $totalAlunosEscola + $totalRows_ExibirAlunosVinculados; ?> 
          
          
          <?php } while ($row_ExibirTurmas = mysql_fetch_assoc($ExibirTurmas)); ?>
          
          
          
          <?php if ($codTurma == "") { ?>
          <div class="ls-box ls-box-gray">
		  <p>Total de alunos vinculados na escola: <strong><?php echo $totalAlunosEscola; ?></strong></p>
		  </div>
		  <?php } ?>
		  
		<small>Relatório impresso em <?php echo date('d/m/Y'); ?> às <?php echo date('H:i:s'); ?>. <br>SMECEL - Sistema de Gestão Escolar</i></small>

          

		<!-- CONTEÚDO -->
      </div>


    <!-- We recommended use jQuery 1.10 or up -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="js/locastyle.js"></script>
 

	<script language="Javascript">
	function confirmaExclusao(id) {
     var resposta = confirm("Deseja realmente excluir o vínculo deste aluno?");
     	if (resposta == true) {
     	     window.location.href = "matriculaExcluir.php?hash="+id;
    	 }
	}
	</script>

  </body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($ExibirTurmas);

mysql_free_result($ExibirAlunosVinculados);
?>
