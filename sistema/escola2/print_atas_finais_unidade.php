<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include('fnc/notas.php'); ?>
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
if (isset($_GET['turma'])) {
	
	if ($_GET['turma'] == "") {
	//echo "TURMA EM BRANCO";	
	header("Location: turmasAlunosVinculados.php?nada"); 
 	exit;
	}
	
  $codTurma = anti_injection($_GET['turma']);
  $codTurma = (int)$codTurma;
  $buscaTurma = " AND turma_id = $codTurma ";
}

$unidade = "";
if (isset($_GET['unidade'])) {
	
	if ($_GET['unidade'] == "") {
	//echo "TURMA EM BRANCO";	
	header("Location: turmasAlunosVinculados.php?nada"); 
 	exit;
	}
	
  $unidade = anti_injection($_GET['unidade']);
  $unidade = (int)$unidade;

}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turmas = "
SELECT 
turma_id, turma_id_escola, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo,
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno_nome 
FROM smc_turma 
WHERE turma_id_escola = $row_EscolaLogada[escola_id] AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' $buscaTurma ORDER BY turma_turno, turma_etapa, turma_nome ASC";
$Turmas = mysql_query($query_Turmas, $SmecelNovo) or die(mysql_error());
$row_Turmas = mysql_fetch_assoc($Turmas);
$totalRows_Turmas = mysql_num_rows($Turmas);

if ($totalRows_Turmas == "") {
	//echo "TURMA EM BRANCO";	
	header("Location: turmaListar.php?nada"); 
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

<title>SMECEL - Sistema de Gestão Escolar</title>
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

 	writing-mode: vertical-rl;
	 -webkit-writing-mode: vertical-rl;
	 -ms-writing-mode: vertical-rl;

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




    <!-- CONTEÚDO -->
	
	

    



    <?php do { ?>
	
	<div style="page-break-inside: avoid;">
	
	<div class="ls-box ls-box ls-txt-center">
	
	<?php if ($row_EscolaLogada['escola_logo']<>"") { ?><img src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt="" width="60px" /><?php } else { ?><img src="../../img/brasao_republica.png" alt="" width="60px" /><?php } ?><br>
		<strong><?php echo $row_EscolaLogada['escola_nome']; ?></strong><br>
		<small>INEP: <?php echo $row_EscolaLogada['escola_inep']; ?> AUT: - D.O. -<br>
		ENDEREÇO: <?php echo $row_EscolaLogada['escola_endereco']; ?>, <?php echo $row_EscolaLogada['escola_num']; ?>, <?php echo $row_EscolaLogada['escola_bairro']; ?> 
		<?php echo $row_EscolaLogada['sec_cidade']; ?>-<?php echo $row_EscolaLogada['sec_uf']; ?> CEP: <?php echo $row_EscolaLogada['escola_cep']; ?><br>
		CNPJ: <?php echo $row_EscolaLogada['escola_cnpj']; ?> <?php echo $row_EscolaLogada['escola_email']; ?> <?php echo $row_EscolaLogada['escola_telefone1']; ?></small>
	
	<p><h2 class="ls-txt-center">ATA DE RESULTADOS DA <?php echo $unidade; ?>ª UNIDADE DO ANO LETIVO DE <?php echo $row_Turmas['turma_ano_letivo']; ?></h2></p>
	<p>
	<div style="text-align:justify; line-height:150%;">
	Aos_____ dias do mês de_________________ do ano de________ terminou-se o processo de apuração das notas finais da <?php echo $unidade; ?>ª Unidade do Ano Letivo de <?php echo $row_Turmas['turma_ano_letivo']; ?> dos alunos da turma <?php echo $row_Turmas['turma_nome']; ?>, turno <?php echo $row_Turmas['turma_turno_nome']; ?>, deste estabelecimento de ensino, com os seguintes resultados:
	<br>
	</div>	
	</p>  
	  
    <?php 
	mysql_select_db($database_SmecelNovo, $SmecelNovo);
	$query_Matriculas = "
	SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, 
	vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao,
	CASE vinculo_aluno_situacao
	WHEN 1 THEN 'MATRICULADO'
	WHEN 2 THEN 'TRANSFERIDO'
	WHEN 3 THEN 'DESISTENTE'
	WHEN 4 THEN 'FALECIDO'
	WHEN 5 THEN 'OUTROS'
	END AS vinculo_aluno_situacao_nome,
	vinculo_aluno_datatransferencia, aluno_id, aluno_nome, aluno_nascimento 
	FROM smc_vinculo_aluno 
	INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
	WHERE vinculo_aluno_id_escola = $row_EscolaLogada[escola_id] AND vinculo_aluno_id_turma = $row_Turmas[turma_id] ORDER BY aluno_nome ASC";
	$Matriculas = mysql_query($query_Matriculas, $SmecelNovo) or die(mysql_error());
	$row_Matriculas = mysql_fetch_assoc($Matriculas);
	$totalRows_Matriculas = mysql_num_rows($Matriculas);
	?>
	
      <table width="100%" class="ls-sm-space ls-table-striped bordasimples">	
	    <thead>
          <tr>
            <th>Nº</th>
			<th width="40%">NOME DO(A) ALUNO(A)</th>
            <th width="10%" align="center">NASCIMENTO</th>
            <th>
			<table width="100%">
			<tr>
			<?php
				mysql_select_db($database_SmecelNovo, $SmecelNovo);
				$query_CabecalhoDisciplinas = "
				SELECT 
				matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_id_disciplina, disciplina_id, disciplina_ordem, disciplina_nome, disciplina_nome_abrev 
				FROM smc_matriz_disciplinas
				INNER JOIN smc_disciplina ON disciplina_id = matriz_disciplina_id_disciplina
				WHERE matriz_disciplina_id_matriz = $row_Turmas[turma_matriz_id]	
				ORDER BY disciplina_ordem ASC";
				$CabecalhoDisciplinas = mysql_query($query_CabecalhoDisciplinas, $SmecelNovo) or die(mysql_error());
				$row_CabecalhoDisciplinas = mysql_fetch_assoc($CabecalhoDisciplinas);
				$totalRows_CabecalhoDisciplinas = mysql_num_rows($CabecalhoDisciplinas);			
			?>
            <?php do { ?>
            <td width="11%" height="180px" align="center" style="border:0px solid #808080; border-right:1px solid #808080; font-size:12px;"><div class="foo"><?php echo $row_CabecalhoDisciplinas['disciplina_nome']; ?></div></td>
            <?php } while ($row_CabecalhoDisciplinas = mysql_fetch_assoc($CabecalhoDisciplinas)); ?>
			</tr>
            </table>
            </th>
          </tr>
        </thead>
        <tbody>
		  <?php $contaAlunos = 1; ?>
          <?php do { ?>
            <tr>
			  <td align="center" width="25px"><?php 
					echo $contaAlunos;
					$contaAlunos++;		
					?>
			  </td>	
              <td width="40%" class="ls-txt-left"><?php echo $row_Matriculas['aluno_nome']; ?></td>
              <td width="10%" align="center"><?php echo inverteData($row_Matriculas['aluno_nascimento']); ?></td>
              <td>
			  
			  <?php
					  mysql_select_db($database_SmecelNovo, $SmecelNovo);
					  $query_Notas = "SELECT boletim_id, boletim_id_vinculo_aluno, boletim_id_disciplina, boletim_1v1, boletim_2v1, boletim_3v1, boletim_1v2, boletim_2v2, boletim_3v2, boletim_1v3, boletim_2v3, boletim_3v3, boletim_1v4, boletim_2v4, boletim_3v4, boletim_af, disciplina_id, disciplina_nome, disciplina_ordem FROM smc_boletim_disciplinas INNER JOIN smc_disciplina ON disciplina_id = boletim_id_disciplina WHERE boletim_id_vinculo_aluno = $row_Matriculas[vinculo_aluno_id] ORDER BY disciplina_ordem ASC";
					  $Notas = mysql_query($query_Notas, $SmecelNovo) or die(mysql_error());
					  $row_Notas = mysql_fetch_assoc($Notas);
					  $totalRows_Notas = mysql_num_rows($Notas);
					?>
                
				
				<table width="100%">
				
				<?php if ($row_Matriculas['vinculo_aluno_boletim'] == "0") { ?>
				
				<tr><td align="center" style="border:0px solid #808080; border-right:1px solid #808080; letter-spacing:8px;"><small>SEM LANÇAMENTO</small></td></tr>
				
				<?php } else { ?>
				
				
				<?php if ($row_Matriculas['vinculo_aluno_situacao'] == "1") { ?>
				
				<tr>
				<?php do { ?>
				<td align="center" width="11%" style="border:0px solid #808080; border-right:1px solid #808080;">
				<strong>
				  
				  
				  <div style="display:none">
				  <?php $row_Notas['disciplina_nome']; ?>
				  <?php $mv1 = mediaUnidade($row_Notas['boletim_1v'.$unidade.''],$row_Notas['boletim_2v'.$unidade.''],$row_Notas['boletim_3v'.$unidade.'']); ?>
				  <?php $mv2 = mediaUnidade($row_Notas['boletim_1v2'],$row_Notas['boletim_2v2'],$row_Notas['boletim_3v2']); ?>
				  <?php $mv3 = mediaUnidade($row_Notas['boletim_1v3'],$row_Notas['boletim_2v3'],$row_Notas['boletim_3v3']); ?>
				  <?php $mv4 = mediaUnidade($row_Notas['boletim_1v4'],$row_Notas['boletim_2v4'],$row_Notas['boletim_3v4']); ?>
				  <?php $tp = totalPontos($mv1,$mv2,$mv3,$mv4); ?>
				  <?php echo $af = avaliacaoFinal($row_Notas['boletim_af']); ?>
				  </div>
				  
				  <?php if($mv1 < 6) {
					  echo "<span style='color:red;'>".$mv1."</span>";
				  } else {
					  echo $mv1;
				  }; ?>
				  <?php 
				  /*
				  $mc = mediaCurso($tp); 
				  
				  if ($mc >= 6) {
					  echo $mc;
				  } else {
					  echo $af = avaliacaoFinal($row_Notas['boletim_af']);
				  }
				  */
				  ?>
				  </strong>
				  
				</td>
                  <?php } while ($row_Notas = mysql_fetch_assoc($Notas)); ?>
				</tr>
				
				<?php } else { ?>
				
				<tr><td align="center" style="border:0px solid #808080; border-right:1px solid #808080; letter-spacing:8px;"><small><?php echo $row_Matriculas['vinculo_aluno_situacao_nome']; ?></small></td></tr>
				
				<?php } ?>

				<?php } ?>
				
				
				</table>
				  
				  
				  </td>
            </tr>
            <?php } while ($row_Matriculas = mysql_fetch_assoc($Matriculas)); ?>
        </tbody>
      </table>
	
	<br>
	<p>
	<div style="text-align:justify; line-height:150%;">
	E, para constar, eu_____________________________________________________________, secretário(a) escolar autorizado(a), lavrarei a presente Ata que vai assinada por mim e pelo(a) diretor(a) do estabelecimento de ensino.
	<hr>
	<table width="100%" class="ls-txt-center">
	<tr>
		<td width="50%"><p>________________________________________________<br>Secretário(a) Escolar</p></td>
		<td width="50%"><p>________________________________________________<br>Diretor(a) Escolar</p></td>	
	</tr>
	</table>
	
	<br>
	</div>
	</p>
	</div>

	</div>
	
	<?php } while ($row_Turmas = mysql_fetch_assoc($Turmas)); ?>
	  
	  
    
    
    <!-- CONTEÚDO --> 



<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>
 
</body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($Turmas);

mysql_free_result($Matriculas);

mysql_free_result($Notas);

mysql_free_result($CabecalhoDisciplinas);
?>
