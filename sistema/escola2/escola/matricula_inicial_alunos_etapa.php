<?php require_once('../../Connections/SmecelNovo.php'); ?>
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

$inicio = ($row_AnoLetivo['ano_letivo_mat_inicial']<>'') ? $row_AnoLetivo['ano_letivo_mat_inicial'] : $row_AnoLetivo['ano_letivo_inicio'];
$fim = ($row_AnoLetivo['ano_letivo_mat_final']<>'') ? $row_AnoLetivo['ano_letivo_mat_final'] : $row_AnoLetivo['ano_letivo_fim'];

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matriculas = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, 
vinculo_aluno_ponto_id, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, 
vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, vinculo_aluno_vacina_data_retorno, vinculo_aluno_conselho, vinculo_aluno_conselho_parecer, vinculo_aluno_internet, vinculo_aluno_multietapa, 
vinculo_aluno_rel_aval, vinculo_aluno_dependencia, turma_id, turma_nome, turma_etapa, turma_turno, turma_id_escola, etapa_id, etapa_nome,
aluno_id, aluno_nome, aluno_sexo
 
FROM smc_vinculo_aluno

INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
INNER JOIN smc_etapa ON etapa_id = turma_etapa
WHERE vinculo_aluno_id_escola = '$row_UsuLogado[usu_escola]' AND turma_id_escola = '$row_UsuLogado[usu_escola]' AND vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'
AND vinculo_aluno_data BETWEEN '$inicio' AND '$fim'
GROUP BY etapa_id
ORDER BY etapa_id ASC
";
$Matriculas = mysql_query($query_Matriculas, $SmecelNovo) or die(mysql_error());
$row_Matriculas = mysql_fetch_assoc($Matriculas);
$totalRows_Matriculas = mysql_num_rows($Matriculas);

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
<link rel="stylesheet" type="text/css" href="css/locastyle.css"><link rel="stylesheet" type="text/css" href="css/preloader.css">
<script src="js/locastyle.js"></script>
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
<style>
					table.bordasimples {
						border-collapse: collapse;
						font-size:12px;
					}
					table.bordasimples tr td {
						border:1px solid #808080;
						padding:3px;
						font-size:15px;
					}
					table.bordasimples tr th {
						border:1px solid #808080;
						padding:3px;
						font-size:15px;
					}
				</style>
</head>
  <body>
    <?php include_once ("menu-top.php"); ?>
          <?php include_once ("menu-esc.php"); ?>


    <main class="ls-main ">
      <div class="container-fluid">
 
        <h1 class="ls-title-intro ls-ico-home">ALUNOS POR TURNO/ETAPA</h1>
		<!-- CONTEÚDO -->
        

        
        <p><a href="rel.php" class="ls-btn">VOLTAR</a></p>
        
        <p class="ls-box">Matrículas realizadas entre <strong><?php echo date("d/m/Y", strtotime($row_AnoLetivo['ano_letivo_mat_inicial'])); ?></strong> e <strong><?php echo date("d/m/Y", strtotime($row_AnoLetivo['ano_letivo_mat_final'])); ?></strong></p>
	
		<table class="ls-table bordasimples">
        
        <tr>
        	<th>ETAPA</th>
        	<th colspan="2" class="ls-txt-center" width="220">INTEGRAL</th>
        	<th colspan="2" class="ls-txt-center" width="220">MATUTINO</th>
        	<th colspan="2" class="ls-txt-center" width="220">VESPERTINO</th>
        	<th colspan="2" class="ls-txt-center" width="220">NOTURNO</th>
        </tr>

        <tr>
        	<th></th>
        	<th class="ls-txt-center">Nº TURMAS</th>
        	<th class="ls-txt-center">Nº ALUNOS</th>
        	<th class="ls-txt-center">Nº TURMAS</th>
        	<th class="ls-txt-center">Nº ALUNOS</th>
        	<th class="ls-txt-center">Nº TURMAS</th>
        	<th class="ls-txt-center">Nº ALUNOS</th>
        	<th class="ls-txt-center">Nº TURMAS</th>
        	<th class="ls-txt-center">Nº ALUNOS</th>
        </tr>



		<?php do { ?>
        
        
        
        <tr>
        
		  <td><?php echo $row_Matriculas['etapa_nome']; ?></td>
          
          <?php for ($i = 0; $i < 4; $i++) { ?>
          
          
          <?php
 		    mysql_select_db($database_SmecelNovo, $SmecelNovo);
			$query_Turmas = "
			SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo, turma_parecer, turma_multisseriada, COUNT(*) AS total_turmas 
			FROM smc_turma 
			WHERE turma_etapa = '$row_Matriculas[etapa_id]' AND turma_id_escola = '$row_UsuLogado[usu_escola]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND turma_turno = '$i'
			";
			$Turmas = mysql_query($query_Turmas, $SmecelNovo) or die(mysql_error());
			$row_Turmas = mysql_fetch_assoc($Turmas);
			$totalRows_Turmas = mysql_num_rows($Turmas);
		  ?>
          
          
          
		<?php do { ?>
		<td class="ls-txt-center"><?php echo ($row_Turmas['total_turmas'] > 0) ? $row_Turmas['total_turmas'] : '-'; ?></td>
        
        
        <?php
		mysql_select_db($database_SmecelNovo, $SmecelNovo);
		$query_Mat = "
		SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, 
		vinculo_aluno_ponto_id, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, 
		vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, vinculo_aluno_vacina_data_retorno, vinculo_aluno_conselho, vinculo_aluno_conselho_parecer, 
		vinculo_aluno_internet, vinculo_aluno_multietapa, vinculo_aluno_rel_aval, vinculo_aluno_dependencia, vinculo_aluno_reprovado_faltas, turma_id, turma_etapa, turma_turno,
		CASE turma_turno
		WHEN 0 THEN 'INT'
		WHEN 1 THEN 'MAT'
		WHEN 2 THEN 'VES'
		WHEN 3 THEN 'NOT'
		END AS turma_turno_nome 
		  
		FROM smc_vinculo_aluno
		INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
		WHERE vinculo_aluno_id_escola = '$row_UsuLogado[usu_escola]' AND vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND turma_etapa = '$row_Turmas[turma_etapa]' AND turma_turno = '$row_Turmas[turma_turno]'
		";
		$Mat = mysql_query($query_Mat, $SmecelNovo) or die(mysql_error());
		$row_Mat = mysql_fetch_assoc($Mat);
		$totalRows_Mat = mysql_num_rows($Mat);
		?>
        
        <td class="ls-txt-center"><?php echo ($totalRows_Mat>0) ? $totalRows_Mat : '-'; ?></td>
        
        
        
        
		<?php } while ($row_Turmas = mysql_fetch_assoc($Turmas)); ?>
        
        <?php } ?>

        </tr>  
        
        
		<?php } while ($row_Matriculas = mysql_fetch_assoc($Matriculas)); ?>
        
        
        
        </table>
        
        <p class="ls-txt-right">&nbsp;</p>
        <p class="ls-txt-right">&nbsp;</p>
        <p class="ls-txt-right">&nbsp;</p>
        
<!-- CONTEÚDO -->
      </div>
    </main>

    <aside class="ls-notification">
      <nav class="ls-notification-list" id="ls-notification-curtain" style="left: 1716px;">
        <h3 class="ls-title-2">Notificações</h3>
    <ul>
      <?php include "notificacoes.php"; ?>
    </ul>
      </nav>

      <nav class="ls-notification-list" id="ls-help-curtain" style="left: 1756px;">
        <h3 class="ls-title-2">Feedback</h3>
    <ul>
      <li><a href="https://webmail.smecel.com.br" target="_blank">&gt; Acesse o webmail de sua escola</a></li>
    </ul>
      </nav>

      <nav class="ls-notification-list" id="ls-feedback-curtain" style="left: 1796px;">
        <h3 class="ls-title-2">Ajuda</h3>
        <ul>
          <li class="ls-txt-center hidden-xs">
            <a href="tutoriais_video.php" class="ls-btn-dark ls-btn-tour">Tutorial (Vídeos)</a>
          </li>
          <li><a href="#">&gt; Guia</a></li>
          <li><a href="#">&gt; Wiki</a></li>
        </ul>
      </nav>
    </aside>

    <!-- We recommended use jQuery 1.10 or up -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="js/locastyle.js"></script>
 
  </body>
</html>
<?php
mysql_free_result($Mat);

mysql_free_result($Turmas);

mysql_free_result($Matriculas);

mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);
?>
