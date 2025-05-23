<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include('fnc/inverteData.php'); ?>
<?php include "fnc/anti_injection.php"; ?>


<?php //include "fnc/anoLetivo.php"; ?>

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
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, sec_id, sec_cidade, sec_uf, escola_tema 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

$anoLetivo = $row_AnoLetivo['ano_letivo_ano'];
	if (isset($_GET['ano'])) {
	
		if ($_GET['ano'] == "") {
		//echo "TURMA EM BRANCO";	
		header("Location: turmasAlunosVinculados.php?nada"); 
		exit;
		}
	
	$anoLetivo = anti_injection($_GET['ano']);
	$anoLetivo = (int)$anoLetivo;
}

$colname_Turma = "-1";
if (isset($_GET['turma'])) {
  $colname_Turma = $_GET['turma'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turma = sprintf("SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo, turma_parecer FROM smc_turma WHERE turma_id = %s", GetSQLValueString($colname_Turma, "int"));
$Turma = mysql_query($query_Turma, $SmecelNovo) or die(mysql_error());
$row_Turma = mysql_fetch_assoc($Turma);
$totalRows_Turma = mysql_num_rows($Turma);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Lotacao = "
SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola, func_id, func_nome, disciplina_id, disciplina_nome 
FROM smc_ch_lotacao_professor
INNER JOIN smc_func ON func_id = ch_lotacao_professor_id
INNER JOIN smc_disciplina ON disciplina_id = ch_lotacao_disciplina_id
WHERE ch_lotacao_escola = '$row_EscolaLogada[escola_id]' AND ch_lotacao_turma_id = '$row_Turma[turma_id]'
GROUP BY ch_lotacao_professor_id
";
$Lotacao = mysql_query($query_Lotacao, $SmecelNovo) or die(mysql_error());
$row_Lotacao = mysql_fetch_assoc($Lotacao);
$totalRows_Lotacao = mysql_num_rows($Lotacao);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Disciplinas = "
SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_id_disciplina, matriz_disciplina_ch_ano, 
disciplina_id, disciplina_nome 
FROM smc_matriz_disciplinas
INNER JOIN smc_disciplina ON disciplina_id = matriz_disciplina_id_disciplina
WHERE matriz_disciplina_id_matriz = '$row_Turma[turma_matriz_id]'";
$Disciplinas = mysql_query($query_Disciplinas, $SmecelNovo) or die(mysql_error());
$row_Disciplinas = mysql_fetch_assoc($Disciplinas);
$totalRows_Disciplinas = mysql_num_rows($Disciplinas);
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

<title><?php echo "DIÁRIO DE CLASSE - $row_Turma[turma_nome] - $anoLetivo" ." - ". $row_EscolaLogada['escola_nome']  ?></title>
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
table.bordasimples {
	border-collapse: collapse;
	font-size:10px;
}
table.bordasimples tr td {
	border:1px solid #808080;
	padding:5px;
	font-size:10px;
}
table.bordasimples tr th {
	border:1px solid #808080;
	padding:5px;
	font-size:15px;
}
</style>


</head>


  <body onload="self.print();">

		
		
<div style="page-break-inside: avoid;" class="ls-box1 ls-txt-center" align="center">

<br>
<br>
<?php if ($row_EscolaLogada['escola_logo']<>"") { ?><img src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt="" width="130px" /><?php } else { ?><img src="../../img/brasao_republica.png" alt="" width="130px" /><?php } ?>
<br>
<br>
<br>
<h2><?php echo $row_EscolaLogada['escola_nome']; ?></h2>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<h1 style="font-size:56px; margin-bottom: 20px;"><span style="border-bottom: solid 5px black">DIÁRIO DE CLASSE</span></h1>
<br>
<h1>TURMA <?php echo $row_Turma['turma_nome']; ?></h1>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<h2>ANO LETIVO <?php echo $anoLetivo; ?> <span class="ls-ico-calendar ls-ico-right"></span></h2><br>

</div>


<div style="page-break-inside: avoid;" class="ls-box1 ls-txt-center" align="center">

<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
</div>


 
 
 		<?php 
		$aulasTotal = 0; 
		do { ?>
        
        <div style="page-break-inside: avoid;">
        
        <div class="ls-box1 ls-txt-center" align="center">
        	  
	  	<?php if ($row_EscolaLogada['escola_logo']<>"") { ?><img src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt="" width="60px" /><?php } else { ?><img src="../../img/brasao_republica.png" alt="" width="60px" /><?php } ?><br>
		<strong><?php echo $row_EscolaLogada['escola_nome']; ?></strong><br>
		<small>INEP: <?php echo $row_EscolaLogada['escola_inep']; ?> AUT: - D.O. -<br>
		ENDEREÇO: <?php echo $row_EscolaLogada['escola_endereco']; ?>, <?php echo $row_EscolaLogada['escola_num']; ?>, <?php echo $row_EscolaLogada['escola_bairro']; ?> 
		<?php echo $row_EscolaLogada['sec_cidade']; ?>-<?php echo $row_EscolaLogada['sec_uf']; ?> CEP: <?php echo $row_EscolaLogada['escola_cep']; ?><br>
		CNPJ: <?php echo $row_EscolaLogada['escola_cnpj']; ?> <?php echo $row_EscolaLogada['escola_email']; ?> <?php echo $row_EscolaLogada['escola_telefone1']; ?></small>
        <br><br>
        <h2>DIÁRIO DE CLASSE - <?php echo $anoLetivo; ?></h2><br>
        
        </div>
        
        
          <?php 
			mysql_select_db($database_SmecelNovo, $SmecelNovo);
			$query_Aulas = "
			SELECT 
				plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, plano_aula_data, plano_aula_data_cadastro, plano_aula_texto, plano_aula_conteudo, plano_aula_atividade, 
				plano_aula_atividade_resposta_obrigatoria, plano_aula_atividade_resposta_obrigatoria_data_limite, plano_aula_video, plano_aula_google_form, plano_aula_google_form_tempo, plano_aula_publicado, 
				plano_aula_hash, func_id, func_nome 
			FROM 
				smc_plano_aula
			LEFT JOIN
				smc_func
			ON
				func_id = plano_aula_id_professor
			WHERE
				plano_aula_id_disciplina = '$row_Disciplinas[disciplina_id]' AND plano_aula_id_turma = '$row_Turma[turma_id]'
			
			ORDER BY 
				plano_aula_data
					";
			$Aulas = mysql_query($query_Aulas, $SmecelNovo) or die(mysql_error());
			$row_Aulas = mysql_fetch_assoc($Aulas);
			$totalRows_Aulas = mysql_num_rows($Aulas);
			
			$aulasTotal = $totalRows_Aulas + $aulasTotal;
		  ?>
<?php 
			mysql_select_db($database_SmecelNovo, $SmecelNovo);
			$query_AulasCab = "
			SELECT 
				plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, plano_aula_data, plano_aula_data_cadastro, plano_aula_texto, plano_aula_conteudo, plano_aula_atividade, 
				plano_aula_atividade_resposta_obrigatoria, plano_aula_atividade_resposta_obrigatoria_data_limite, plano_aula_video, plano_aula_google_form, plano_aula_google_form_tempo, plano_aula_publicado, 
				plano_aula_hash, func_id, func_nome 
			FROM 
				smc_plano_aula
			LEFT JOIN
				smc_func
			ON
				func_id = plano_aula_id_professor
			WHERE
				plano_aula_id_disciplina = '$row_Disciplinas[disciplina_id]' AND plano_aula_id_turma = '$row_Turma[turma_id]'
			
			GROUP BY 
				plano_aula_id_professor
					";
			$AulasCab = mysql_query($query_AulasCab, $SmecelNovo) or die(mysql_error());
			$row_AulasCab = mysql_fetch_assoc($AulasCab);
			$totalRows_AulasCab = mysql_num_rows($AulasCab);
		  ?>
          
          <?php 
			mysql_select_db($database_SmecelNovo, $SmecelNovo);
			$query_AulasAss = "
			SELECT 
				plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, plano_aula_data, plano_aula_data_cadastro, plano_aula_texto, plano_aula_conteudo, plano_aula_atividade, 
				plano_aula_atividade_resposta_obrigatoria, plano_aula_atividade_resposta_obrigatoria_data_limite, plano_aula_video, plano_aula_google_form, plano_aula_google_form_tempo, plano_aula_publicado, 
				plano_aula_hash, func_id, func_nome 
			FROM 
				smc_plano_aula
			LEFT JOIN
				smc_func
			ON
				func_id = plano_aula_id_professor
			WHERE
				plano_aula_id_disciplina = '$row_Disciplinas[disciplina_id]' AND plano_aula_id_turma = '$row_Turma[turma_id]'
			
			GROUP BY 
				plano_aula_id_professor
					";
			$AulasAss = mysql_query($query_AulasAss, $SmecelNovo) or die(mysql_error());
			$row_AulasAss = mysql_fetch_assoc($AulasAss);
			$totalRows_AulasAss = mysql_num_rows($AulasAss);
		  ?>          
          <?php if ($totalRows_Aulas > 0) { ?>
          
          <div class="ls-box">
          <p>Professores(as): <br><strong><?php do { ?>-<?php echo $row_AulasCab['func_nome']; ?> (<?php echo $row_AulasCab['func_id']; ?>)<br><?php } while ($row_AulasCab = mysql_fetch_assoc($AulasCab)); ?></strong></p>
          <p>Componente Curricular: <strong><?php echo $row_Disciplinas['disciplina_nome']; ?></strong></p>
          <p>Turma: <strong><?php echo $row_Turma['turma_nome']; ?></strong></p>
          </div>
          
          
          
           <table class="ls-table1 ls-sm-space bordasimples" width="100%">
            
            <tr>
            	<th width="70" align="center">DATA</th>
            	<th width="40" align="center">CÓD</th>
            	<th class="ls-txt-center" align="center">CONTEÚDO</th>
            </tr>
          
          <?php do { ?>
          
                <tr>
				
				<td width="70" align="center"><?php echo inverteData($row_Aulas['plano_aula_data']); ?></td>
                <td class="ls-txt-center" align="center"><?php echo $row_Aulas['plano_aula_id']; ?></td>
                <td><?php echo $row_Aulas['plano_aula_texto']; ?></td>
                 <?php $professor = $row_Aulas['func_nome']; ?>
                
                </tr>
          
          
          <?php } while ($row_Aulas = mysql_fetch_assoc($Aulas)); ?>  
          
            <tr>
            	<td colspan="3"><strong>Total de aulas: (<?php echo $totalRows_Aulas ?>)</strong></td>
            </tr>
            
            </table>
            

<br>
<br>

            <?php do { ?>
			
			 
            <br><br><p class="ls-txt-center">________________________________________________________<br>Assinatura do(a) professor(a)<br><?php echo $row_AulasAss['func_nome']; ?> (<?php echo $row_AulasAss['func_id']; ?>)</p>
            
            
			<?php } while ($row_AulasAss = mysql_fetch_assoc($AulasAss)); ?>

         
            
          
                   
            
             <?php } else { ?>
             <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
             <h3 class="ls-txt-center">Nenhum registro encontrado para <?php echo $row_Disciplinas['disciplina_nome']; ?></h3>
             <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
              <?php } ?>   
        
        </div>
        <?php } while ($row_Disciplinas = mysql_fetch_assoc($Disciplinas)); ?>
        
                <br>
       			<h4>Total de aulas da turma: <?php echo $aulasTotal; ?></h4>
                <hr>
 
 
 <p class="ls-txt-right"><small></i>Relatório impresso em <?php echo date('d/m/Y'); ?> às <?php echo date('H:i:s'); ?>. | SMECEL - Sistema de Gestão Escolar | www.smecel.com.br</i></small></p>
 
      
          
    <!-- We recommended use jQuery 1.10 or up -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="js/locastyle.js"></script>
 
  </body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($Turma);

mysql_free_result($Aulas);

mysql_free_result($Lotacao);

mysql_free_result($EscolaLogada);
?>
