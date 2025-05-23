<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include "fnc/inverteData.php"; ?>
<?php
$data = $_COOKIE['data']; // Fulano
$dataBase = $_COOKIE['dataBase']; // Fulano
$diasemana_numero = $_COOKIE['diasemana_numero']; // Fulano
$dia_semana_nome = $_COOKIE['dia_semana_nome']; // Fulano
//initialize the session
?>
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

$colname_Turma = "-1";
if (isset($_GET['turma'])) {
  $colname_Turma = $_GET['turma'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turma = sprintf("
SELECT turma_id, turma_id_escola, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo, turma_matriz_id 
FROM smc_turma 
WHERE turma_id = %s AND turma_id_escola = '$row_UsuLogado[usu_escola]'", GetSQLValueString($colname_Turma, "int"));
$Turma = mysql_query($query_Turma, $SmecelNovo) or die(mysql_error());
$row_Turma = mysql_fetch_assoc($Turma);
$totalRows_Turma = mysql_num_rows($Turma);

if ($totalRows_Turma < 1) {
	
	$erro = "turmaListar.php?nada";	
  	header(sprintf("Location: %s", $erro));
	
	}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_matriz = "SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash, matriz_dias_letivos, matriz_semanas_letivas, matriz_dias_semana, matriz_minutos_aula, matriz_aula_dia, matriz_criterio_avaliativo FROM smc_matriz WHERE matriz_id = $row_Turma[turma_matriz_id]  ";
$matriz = mysql_query($query_matriz, $SmecelNovo) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Alunos = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, 
vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, aluno_id, aluno_nome, aluno_cod_inep 
FROM smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
WHERE vinculo_aluno_id_turma = '$row_Turma[turma_id]' ORDER BY aluno_nome ASC
";
$Alunos = mysql_query($query_Alunos, $SmecelNovo) or die(mysql_error());
$row_Alunos = mysql_fetch_assoc($Alunos);
$totalRows_Alunos = mysql_num_rows($Alunos);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Aulas = "SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola FROM smc_ch_lotacao_professor WHERE ch_lotacao_turma_id = '$row_Turma[turma_id]'";
$Aulas = mysql_query($query_Aulas, $SmecelNovo) or die(mysql_error());
$row_Aulas = mysql_fetch_assoc($Aulas);
$totalRows_Aulas = mysql_num_rows($Aulas);

/*
if (!isset($_SESSION['data'])) {
  $insertGoTo = "definirData.php";	
  header(sprintf("Location: %s", $insertGoTo));
}
*/



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
 
        <h1 class="ls-title-intro ls-ico-home">REGISTRO DE FALTAS</h1>
		<!-- CONTEÚDO -->
        
        
         <a href="turmasFrequencia.php" class="ls-btn">VOLTAR</a>    
         
         <br><br>
		
		
		<?php if (isset($_GET["faltalancada"])) { ?>
                <div class="ls-alert-success ls-dismissable">
                  <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
                  Registro de faltas realizado com sucesso.
                </div>
              <?php } ?>
		
		<?php if (isset($_GET["faltanaolancada"])) { ?>
                <div class="ls-alert-warning ls-dismissable">
                  <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
                  Não houve lançamento de faltas na ação anterior.
                </div>
              <?php } ?>
		
        
        <div class="ls-box">
			<p>TURMA: <strong><?php echo $row_Turma['turma_nome']; ?></strong></p>
            <p>MATRIZ: <strong><?php echo $row_matriz['matriz_nome']; ?></strong></p></p>
			<p>DATA PARA REGISTRO DAS FALTAS: <strong><?php echo $dia_semana_nome; ?>, <?php echo inverteData($data); ?></strong></p>
		</div>	
	
		
        
        <?php if ($row_Alunos > 0) { ?>
        
        <table width="100%" class="ls-table ls-sm-space">
		<thead>
          <tr>
            <th width="40px" class="ls-txt-center"></th>
            <th>ALUNO</th>
            
            <?php for ($c = 1; $c <= $row_matriz['matriz_aula_dia']; $c++) { ?>
            
            <th class="ls-txt-center" width="34px"><?php echo $c; ?></th>
            
            <?php } ?>
            
          </thead>
		  </tr>
          <?php 
		  $contagem = 1;
		  do { ?>
            <tr>
              <td class="ls-txt-center"><?php
					echo $contagem;
					$contagem++;

					?></td>
              <td><a href="alunosFaltaLancar.php?hash=<?php echo $row_Alunos['vinculo_aluno_hash']; ?>"><?php echo $row_Alunos['aluno_nome']; ?></a></td>
              
              
<?php              


for ($i = 1; $i <= $row_matriz['matriz_aula_dia']; $i++) {
	
	
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Faltas = "
SELECT faltas_alunos_id, faltas_alunos_matricula_id, faltas_alunos_disciplina_id, faltas_alunos_numero_aula, faltas_alunos_data 
FROM smc_faltas_alunos
WHERE faltas_alunos_matricula_id = '$row_Alunos[vinculo_aluno_id]' AND faltas_alunos_data = '$dataBase' AND faltas_alunos_numero_aula = '$i'
";
$Faltas = mysql_query($query_Faltas, $SmecelNovo) or die(mysql_error());
$row_Faltas = mysql_fetch_assoc($Faltas);
$totalRows_Faltas = mysql_num_rows($Faltas);
	
if ($totalRows_Faltas > 0) {
	
	$check = " checked";
	$icone = "ls-ico-close ls-color-danger";
	$celula = " ls-background-danger";

	
	} else {

	$check = "";
	$icone = "ls-ico-checkmark ls-color-success";
	$celula = "";

		
		}




?>

<td class="ls-txt-center <?php echo $celula; ?>">

<span class="<?php echo $icone; ?>"></span>


</td>

<?php
	
	
	
}


?>



            </tr>
            <?php } while ($row_Alunos = mysql_fetch_assoc($Alunos)); ?>
        </table>
        
        
         <?php } else { ?>
         
         <p>Nenhum aluno vinculado nesta turma.</p>
         
          <?php } ?>
	
		
		<hr>
		
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

mysql_free_result($EscolaLogada);

mysql_free_result($Turma);

mysql_free_result($Alunos);

mysql_free_result($Aulas);

mysql_free_result($Faltas);
?>
