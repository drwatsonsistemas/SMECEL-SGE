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
vinculo_aluno_rel_aval, vinculo_aluno_dependencia, turma_id, turma_nome, turma_etapa, turma_turno, turma_id_escola,
aluno_id, aluno_nome, aluno_sexo,

COUNT(IF(aluno_sexo = '1', aluno_sexo, NULL)) masculino,
COUNT(IF(aluno_sexo = '2', aluno_sexo, NULL)) feminino
 
FROM smc_vinculo_aluno

INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
WHERE vinculo_aluno_id_escola = '$row_UsuLogado[usu_escola]' AND turma_id_escola = '$row_UsuLogado[usu_escola]' AND vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'
GROUP BY vinculo_aluno_id_turma
ORDER BY turma_turno, turma_etapa, turma_nome ASC
";
$Matriculas = mysql_query($query_Matriculas, $SmecelNovo) or die(mysql_error());
$row_Matriculas = mysql_fetch_assoc($Matriculas);
$totalRows_Matriculas = mysql_num_rows($Matriculas);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Etapas = "
SELECT etapa_id, etapa_id_filtro, etapa_nome, etapa_idade, etapa_limite_turma, etapa_nome_abrev, etapa_ano_ef 
FROM smc_etapa
WHERE etapa_id_filtro IN(1,3,7)";
$Etapas = mysql_query($query_Etapas, $SmecelNovo) or die(mysql_error());
$row_Etapas = mysql_fetch_assoc($Etapas);
$totalRows_Etapas = mysql_num_rows($Etapas);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_TurmasEtapas = "
SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo, turma_parecer, turma_multisseriada 
FROM smc_turma
WHERE turma_id_escola = '$row_UsuLogado[usu_escola]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'
ORDER BY turma_turno, turma_etapa
";
$TurmasEtapas = mysql_query($query_TurmasEtapas, $SmecelNovo) or die(mysql_error());
$row_TurmasEtapas = mysql_fetch_assoc($TurmasEtapas);
$totalRows_TurmasEtapas = mysql_num_rows($TurmasEtapas);


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
 
        <h1 class="ls-title-intro ls-ico-home">DOCENTES POR NÍVEL DE FORMAÇÃO NAS TURMAS</h1>
		<!-- CONTEÚDO -->
        
        <p><a href="rel.php" class="ls-btn">VOLTAR</a></p>
        
        <table class="ls-table ls-sm-space bordasimples">
          <tr>
            <th width="300">TURMA</td>
            <th class="ls-txt-center" width="100">MAGISTERIO</th>
            <th class="ls-txt-center" width="100">GRADUAÇÃO</th>
            <th class="ls-txt-center" width="100">PÓS</th>
            <th class="ls-txt-center" width="100">MESTRADO</th>
            <th class="ls-txt-center" width="100">DOUTORADO</th>
          </tr>
          <?php 
		  
		 	  $magisterioT = 0;
			  $graduadoT = 0;
			  $posT = 0;
			  $mestradoT = 0;
			  $doutoradoT = 0;
		  
		  do { ?>
          <?php 
		  
		    mysql_select_db($database_SmecelNovo, $SmecelNovo);
			$query_Lotacao = "
			SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola, func_id, func_nome, func_escolaridade,
			
			COUNT(IF(func_escolaridade = '2', func_escolaridade, NULL)) magisterio,
			COUNT(IF(func_escolaridade = '3', func_escolaridade, NULL)) graduacao,
			COUNT(IF(func_escolaridade = '4', func_escolaridade, NULL)) pos,
			COUNT(IF(func_escolaridade = '5', func_escolaridade, NULL)) mestrado,
			COUNT(IF(func_escolaridade = '6', func_escolaridade, NULL)) doutorado

			 
			FROM smc_ch_lotacao_professor
			INNER JOIN smc_func ON func_id = ch_lotacao_professor_id
			WHERE ch_lotacao_turma_id = '$row_TurmasEtapas[turma_id]'
			GROUP BY ch_lotacao_professor_id
			";
			$Lotacao = mysql_query($query_Lotacao, $SmecelNovo) or die(mysql_error());
			$row_Lotacao = mysql_fetch_assoc($Lotacao);
			$totalRows_Lotacao = mysql_num_rows($Lotacao);
		  
		  ?>
          
          
          <tr>
            <td><?php echo $row_TurmasEtapas['turma_nome']; ?></td>
            
			
            
            <?php 
			
			  $magisterio = 0;
			  $graduado = 0;
			  $pos = 0;
			  $mestrado = 0;
			  $doutorado = 0;
		  
			do { 
			
			
			switch ($row_Lotacao['func_escolaridade']) {
				case 2:
					 $magisterio++;
					 $magisterioT++;
					break;
				case 3:
					$graduado++;
					$graduadoT++;
					break;
				case 4:
					$pos++;
					$posT++;
					break;
				case 5:
					$mestrado++;
					$mestradoT++;
					break;
				case 6:
					$doutorado++;
					$doutoradoT++;
					break;
			}
			
			
			?>
            
            
            
          	<?php } while ($row_Lotacao = mysql_fetch_assoc($Lotacao)); ?>
            

            
            <td class="ls-txt-center"><?php echo ($magisterio > 0) ? $magisterio : '-'; ?></td>
            <td class="ls-txt-center"><?php echo ($graduado > 0) ? $graduado : '-'; ?></td>
            <td class="ls-txt-center"><?php echo ($pos > 0) ? $pos : '-'; ?></td>
            <td class="ls-txt-center"><?php echo ($mestrado > 0) ? $mestrado : '-'; ?></td>
            <td class="ls-txt-center"><?php echo ($doutorado > 0) ? $doutorado : '-'; ?></td>
            
           
          </tr>
          <?php } while ($row_TurmasEtapas = mysql_fetch_assoc($TurmasEtapas)); ?>
          
          <tr>
            <td>TOTAL</td>
            <td class="ls-txt-center"><?php echo ($magisterioT > 0) ? $magisterioT : '-'; ?></td>
            <td class="ls-txt-center"><?php echo ($graduadoT > 0) ? $graduadoT : '-'; ?></td>
            <td class="ls-txt-center"><?php echo ($posT > 0) ? $posT : '-'; ?></td>
            <td class="ls-txt-center"><?php echo ($mestradoT > 0) ? $mestradoT : '-'; ?></td>
            <td class="ls-txt-center"><?php echo ($doutoradoT > 0) ? $doutoradoT : '-'; ?></td>
          </tr>
          
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
mysql_free_result($Lotacao);

mysql_free_result($TurmasEtapas);

mysql_free_result($Etapas);

mysql_free_result($Matriculas);

mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);
?>
