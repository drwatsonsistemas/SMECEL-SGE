<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include('fnc/inverteData.php'); ?>
<?php include "fnc/anti_injection.php"; ?>


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

    <title>Alunos por turma | SMECEL - Sistema de Gestão Escolar</title>

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
	table.bordasimples {border-collapse: collapse; font-size:12px; }
	table.bordasimples tr td {border:1px dotted #000000; padding:22px; font-size:12px;}
	table.bordasimples tr th {border:1px dotted #000000; padding:22px; font-size:12px;}


	</style>
	
	
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
  <body onload="alert('Atenção: Configure sua impressora para o tamanho A4 e formato RETRATO');self.print();">




      <div class="container-fluid">
 
	  
	  		
			
		
		<!-- CONTEÚDO -->
        
		<?php $totalAlunosEscola = 0; ?>
        
        <?php do { ?>
        <?php 
		mysql_select_db($database_SmecelNovo, $SmecelNovo);
		$query_ExibirAlunosVinculados = "
		SELECT 
		vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, 
		vinculo_aluno_hash, vinculo_aluno_boletim, vinculo_aluno_situacao, 
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
		aluno_municipio,
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
		aluno_hash
		FROM smc_vinculo_aluno 
		INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
		WHERE vinculo_aluno_id_turma = '$row_ExibirTurmas[turma_id]' $stqry
		ORDER BY aluno_nome ASC";
		$ExibirAlunosVinculados = mysql_query($query_ExibirAlunosVinculados, $SmecelNovo) or die(mysql_error());
		$row_ExibirAlunosVinculados = mysql_fetch_assoc($ExibirAlunosVinculados);
		$totalRows_ExibirAlunosVinculados = mysql_num_rows($ExibirAlunosVinculados);
		?>
        
        <?php if ($totalRows_ExibirAlunosVinculados > 0) { ?>
		
		
		
        <?php $contaAlunos = 1; ?>
				

			<?php do { ?>
				
				
				<div class="ls-box1" style="page-break-inside: avoid; padding: 20px 0; display:block;">
				
					
					
					<div class="col-sm-12">
					
					<h2 class="ls-txt-center">RECIBO</h2>
					
					<p>
					<strong><?php echo $row_EscolaLogada['escola_nome']; ?></strong><br>
					<strong>TURMA:</strong> <?php echo $row_ExibirTurmas['turma_nome']; ?> <?php echo $row_ExibirTurmas['turma_turno']; ?> - <strong>ANO LETIVO:</strong> <?php echo $row_AnoLetivo['ano_letivo_ano']; ?>
					</p>
					
					<p class="ls-txt-justify">
					Declaro, para os devidos fins, que o(a) aluno(a) <strong><?php echo $row_ExibirAlunosVinculados['aluno_nome']; ?></strong>,
					nascido(a) em <strong><?php echo inverteData($row_ExibirAlunosVinculados['aluno_nascimento']); ?></strong>,
					filho(a) de <strong><?php echo $row_ExibirAlunosVinculados['aluno_filiacao1']; ?></strong><?php if ($row_ExibirAlunosVinculados['aluno_filiacao2']<>"") { ?> e <strong><?php echo $row_ExibirAlunosVinculados['aluno_filiacao2']; ?></strong><?php } ?>,
					residente na <strong><?php echo $row_ExibirAlunosVinculados['aluno_endereco']; ?>, <?php echo $row_ExibirAlunosVinculados['aluno_numero']; ?> - <?php echo $row_ExibirAlunosVinculados['aluno_bairro']; ?>, <?php echo $row_ExibirAlunosVinculados['aluno_municipio']; ?>-<?php echo $row_ExibirAlunosVinculados['aluno_uf']; ?></strong>,
					recebeu da Secretaria Municipal de Educação de <?php echo $row_EscolaLogada['sec_cidade']; ?>, o "KIT ESCOLAR".
					</p>
					
					<p class="ls-txt-center">
					___________________________________________________________<br>Assinatura dos pais ou responsável
					
					</p>


					</div>
					
					
					
				<img src="img/corte.jpg">
					
					
				</div>
				
				
				
				<?php } while ($row_ExibirAlunosVinculados = mysql_fetch_assoc($ExibirAlunosVinculados)); ?>

				
		
		
		
		<?php } else { ?>
		<div class="ls-box ls-sm-space" style="page-break-after: always;">
			<p class="ls-txt-center"><small><i>Nenhum aluno vinculado na turma.</i></small></p>
		</div> 
		<?php } ?>
          
         
          
          
         <?php $totalAlunosEscola = $totalAlunosEscola + $totalRows_ExibirAlunosVinculados; ?> 
          
          
          <?php } while ($row_ExibirTurmas = mysql_fetch_assoc($ExibirTurmas)); ?>
          
          
          
          <?php if ($codTurma == "") { ?>
          <div class="ls-box ls-box-gray">
		  <p>Total de alunos vinculados na escola: <strong><?php echo $totalAlunosEscola; ?></strong></p>
		  </div>
		  <?php } ?>
		  
		 
          

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
