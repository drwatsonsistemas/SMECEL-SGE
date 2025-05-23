<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include('fnc/inverteData.php'); ?>

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
</head>
  <body>
    <?php include_once ("menu-top.php"); ?>
          <?php include_once ("menu-esc.php"); ?>


    <main class="ls-main ">
      <div class="container-fluid">
 
        <h1 class="ls-title-intro ls-ico-home">DIÁRIO DE CLASSE</h1>
		<!-- CONTEÚDO -->
		
		

        <div class="ls-box1 ls-txt-center">
        	  
	  	<a href="print_diario_turma.php?turma=<?php echo $colname_Turma; ?>" target="_blank" class="ls-btn"><span class="ls-ico-paint-format ls-ico-right"></span> IMPRIMIR</a>
        
        </div>
        
        <br>

		<?php do { ?>
        
       
		  <div class="ls-box">
          <p>Professor(a): <strong><?php echo $row_Lotacao['func_nome']; ?> (<?php echo $row_Lotacao['ch_lotacao_professor_id']; ?>)</strong> </p>
          <p>Componente Curricular: <strong><?php echo $row_Lotacao['disciplina_nome']; ?></strong></p>
          <p>Turma: <strong><?php echo $row_Turma['turma_nome']; ?></strong></p>
          </div>
          
          <?php 
			mysql_select_db($database_SmecelNovo, $SmecelNovo);
			$query_Aulas = "
			SELECT 
				plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, plano_aula_data, plano_aula_data_cadastro, plano_aula_texto, plano_aula_conteudo, plano_aula_atividade, 
				plano_aula_atividade_resposta_obrigatoria, plano_aula_atividade_resposta_obrigatoria_data_limite, plano_aula_video, plano_aula_google_form, plano_aula_google_form_tempo, plano_aula_publicado, 
				plano_aula_hash 
			FROM 
				smc_plano_aula
			WHERE
				plano_aula_id_professor = '$row_Lotacao[ch_lotacao_professor_id]' AND plano_aula_id_turma = '$row_Turma[turma_id]'
			ORDER BY 
				plano_aula_data	
				";
			$Aulas = mysql_query($query_Aulas, $SmecelNovo) or die(mysql_error());
			$row_Aulas = mysql_fetch_assoc($Aulas);
			$totalRows_Aulas = mysql_num_rows($Aulas);
		  ?>
          
          
    	    <table class="ls-table ls-sm-space">
            
            <tr>
            	<th width="110">DATA</th>
            	<th class="ls-txt-center">CONTEÚDO</th>
            	<th class="ls-txt-center">PROFESSOR(A)</th>
            </tr>
            
			
			<?php do { ?>
            
            	<tr>
				
				<td width="110"><?php echo inverteData($row_Aulas['plano_aula_data']); ?></td>
                <td><?php echo $row_Aulas['plano_aula_texto']; ?></td>
                <td class="ls-txt-center"><?php echo $row_Lotacao['func_nome']; ?></td>
                
                </tr>
			
			<?php } while ($row_Aulas = mysql_fetch_assoc($Aulas)); ?>
            
            <tr>
            	<td><strong>Total de aulas</strong></td>
            	<td><strong><?php echo $totalRows_Aulas ?></strong></td>
                <td></td>
            </tr>
            
            </table>
            
            <p class="ls-txt-center">________________________________________________________<br><?php echo $row_Lotacao['func_nome']; ?></p>
            
            <hr>
          
		<?php } while ($row_Lotacao = mysql_fetch_assoc($Lotacao)); ?>
          
          
          
          
          
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
mysql_free_result($UsuLogado);

mysql_free_result($Turma);

mysql_free_result($Aulas);

mysql_free_result($Lotacao);

mysql_free_result($EscolaLogada);
?>
