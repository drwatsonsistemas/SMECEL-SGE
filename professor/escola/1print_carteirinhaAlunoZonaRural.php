<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include('fnc/inverteData.php'); ?>
<?php include "fnc/anti_injection.php"; ?>
<?php include "fnc/barcode128.php"; ?>
<?php include('../funcoes/url_base.php'); ?>


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
if (isset($_GET['st'])) {	
  $stCod = anti_injection($_GET['st']);
  $stCod = (int)$stCod;
}

	$st = "1";
	$stqry = "AND vinculo_aluno_situacao = $st ";
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
			  $nomeFiltro = "Matriculados";
			  if (isset($_GET['st'])) {
					switch ($_GET['st']) {
							case 1:
						$nomeFiltro = "Matriculados";
								break;
							case 2:
						$nomeFiltro = "Transferidos";
								break;
							case 3:
						$nomeFiltro = "Desistentes";
								break;
							case 4:
						$nomeFiltro = "Falecidos";
								break;
							case 5:
						$nomeFiltro = "Outros";
								break;
							default:
							   echo "Matriculados";
					}	
			  }
			  
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ExibirTurmas = "
SELECT turma_id, turma_id_escola, turma_nome, turma_etapa, 
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno, 
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
<html>	

  <head>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-117872281-1');
</script>

    <title>CARTEIRINHA DE ESTUDANTE - <?php echo $row_ExibirTurmas['turma_nome']; ?> <?php echo $row_ExibirTurmas['turma_turno']; ?></title>
<meta charset="utf-8">
<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<meta name="description" content="">
<meta name="keywords" content="">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">

  
	<style>
	body {
		font-size:10px;
		font-family:Arial, Helvetica, sans-serif;
		}
	#quebra {
    page-break-before: 1always;
	page-break-inside:auto;
	}
	div.b128{
	 border-left: 1px black solid;
	 height: 60px;
	} 
	.cartao {
		font-size:8px;
		display:block; 
		width:86mm; 
		height:54mm; 
		float:left; 
		padding:1px; 
		margin:0 3px 3px 0; 
		border:dotted 0px #000000;page-break-before: inside; 
		background-image:url('../../img/fundoCarteirinha.jpg');
		background-repeat: no-repeat;
		background-size: cover;
		page-break-inside:avoid;
	}


	</style>  
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>


	
	

  <body onLoad="alert('Atenção: Configure sua impressora para o tamanho A4 e formato RETRATO');self.print();">


			
		
		<!-- CONTEÚDO -->

	

        
        <?php do { ?>
		
        <?php 
		mysql_select_db($database_SmecelNovo, $SmecelNovo);
		$query_ExibirAlunosVinculados = "
		SELECT 
		vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, 
		vinculo_aluno_hash, vinculo_aluno_boletim, vinculo_aluno_situacao
		aluno_id, aluno_nome, aluno_nascimento, aluno_filiacao1, aluno_hash, aluno_foto, aluno_filiacao2, aluno_localizacao, aluno_endereco, aluno_numero, aluno_bairro, aluno_complemento
		FROM smc_vinculo_aluno 
		INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
		WHERE aluno_localizacao = '2' AND vinculo_aluno_id_turma = '$row_ExibirTurmas[turma_id]' $stqry
		ORDER BY aluno_nome ASC";
		$ExibirAlunosVinculados = mysql_query($query_ExibirAlunosVinculados, $SmecelNovo) or die(mysql_error());
		$row_ExibirAlunosVinculados = mysql_fetch_assoc($ExibirAlunosVinculados);
		$totalRows_ExibirAlunosVinculados = mysql_num_rows($ExibirAlunosVinculados);
		?>
        
			
        
        <?php if ($totalRows_ExibirAlunosVinculados > 0) { ?>
			<?php do { ?>

								
			<div class="cartao" style="">
				<h4 style="text-align:center">CARTEIRINHA DO ESTUDANTE - ZONA RURAL</h4>
				<h2 style="text-align:center"><small><?php echo $row_EscolaLogada['escola_nome']; ?></small></h2>
                <h2 style="text-align:center; padding:3px; background-color:#CCCCCC"><?php echo $row_ExibirAlunosVinculados['aluno_nome']; ?></h2>
				
				<table width="100%">
					<tr>
						<td width="65">
						<?php if($row_ExibirAlunosVinculados['aluno_foto']=="") { ?>
							<img src="<?php echo URL_BASE.'aluno/fotos/' ?>semfoto.jpg" style="margin:1mm;width:15mm;">
						<?php } else { ?>
							<img src="<?php echo URL_BASE.'aluno/fotos/' ?><?php echo $row_ExibirAlunosVinculados['aluno_foto']; ?>" style="margin:1mm;width:15mm;">
						<?php } ?>	
						</td>
						<td style="font-size:10px;">
							<strong>Tutma</strong>:<?php echo $row_ExibirTurmas['turma_nome']; ?> <?php echo $row_ExibirTurmas['turma_turno']; ?><br>
							<strong>Nascimento</strong>: <?php echo inverteData($row_ExibirAlunosVinculados['aluno_nascimento']); ?><br>
							<strong>Filiação</strong>: <?php echo $row_ExibirAlunosVinculados['aluno_filiacao1']; ?><br>
							<?php echo $row_ExibirAlunosVinculados['aluno_filiacao2']; ?><br><br>
                            <strong style="background-color:#FDFFD2; padding:3px; display:block"><?php echo $row_ExibirAlunosVinculados['aluno_endereco']; ?> <?php echo $row_ExibirAlunosVinculados['aluno_numero']; ?> <?php echo $row_ExibirAlunosVinculados['aluno_complemento']; ?> <?php echo $row_ExibirAlunosVinculados['aluno_bairro']; ?></strong>	
						</td>
						
					</tr>
				</table>
				<!--
				<img src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt="" width="40px" style="float:right" />
				
				<img src="<?php if ($row_EscolaLogada['escola_logo']<>"") { ?>../../img/<?php echo $row_EscolaLogada['escola_logo']; ?><?php } else { ?>../../img/brasao_republica.png<?php } ?>" alt="" width="40px" style="float:right" />
				-->
					

				<br><i style="text-align:center; float:right; margin-right:5px;">Válido durante o Ano Letivo de <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></i>
				
				
				</div>
				<span id="quebra1"></span>		

			<?php } while ($row_ExibirAlunosVinculados = mysql_fetch_assoc($ExibirAlunosVinculados)); ?>
				
				<span id="quebra1"></span>		
				
		
		<?php } else { ?>
			<p class="ls-txt-center"><small><i>Nenhum aluno vinculado na turma.</i></small></p>
		<?php } ?>
                    
          
          <?php } while ($row_ExibirTurmas = mysql_fetch_assoc($ExibirTurmas)); ?>
          
          
     
		  
		 
          

		<!-- CONTEÚDO -->




  </body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($ExibirTurmas);

mysql_free_result($ExibirAlunosVinculados);
?>
