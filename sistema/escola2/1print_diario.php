<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include('fnc/inverteData.php'); ?>
<?php include "fnc/anti_injection.php"; ?>
<?php include('../funcoes/url_base.php'); ?>
<?php include "fnc/calculos.php"; ?>
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
  $buscaTurma = "AND turma_id = $codTurma ";
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
$query_AlunoBoletim = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia,
aluno_id, aluno_nome, aluno_nascimento, aluno_filiacao1, aluno_foto, aluno_hash,
turma_id, turma_nome, turma_matriz_id, turma_turno, turma_etapa 
FROM 
smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma 
WHERE vinculo_aluno_situacao = '1' AND vinculo_aluno_boletim = '1' AND vinculo_aluno_ano_letivo = '$anoLetivo' AND vinculo_aluno_id_escola = '$row_UsuLogado[usu_escola]' $buscaTurma
ORDER BY turma_turno ASC, turma_etapa ASC, turma_nome ASC, aluno_nome ASC";
$AlunoBoletim = mysql_query($query_AlunoBoletim, $SmecelNovo) or die(mysql_error());
$row_AlunoBoletim = mysql_fetch_assoc($AlunoBoletim);
$totalRows_AlunoBoletim = mysql_num_rows($AlunoBoletim);

if ($totalRows_AlunoBoletim == "") {
	//echo "TURMA EM BRANCO";	
	//header("Location: turmasAlunosVinculados.php?nada"); 
 	
	echo "<h3><center>Sem dados.<br><a href=\"javascript:window.close()\">Fechar</a></center></h3>";
	echo "";
	
	exit;
	}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matriz = "SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash, matriz_dias_letivos, matriz_semanas_letivas, matriz_dias_semana, matriz_minutos_aula, matriz_aula_dia, matriz_criterio_avaliativo FROM smc_matriz WHERE matriz_id = '$row_AlunoBoletim[turma_matriz_id]'";
$Matriz = mysql_query($query_Matriz, $SmecelNovo) or die(mysql_error());
$row_Matriz = mysql_fetch_assoc($Matriz);
$totalRows_Matriz = mysql_num_rows($Matriz);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_CriteriosAvaliativos = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes FROM smc_criterios_avaliativos WHERE ca_id = '$row_Matriz[matriz_criterio_avaliativo]'";
$CriteriosAvaliativos = mysql_query($query_CriteriosAvaliativos, $SmecelNovo) or die(mysql_error());
$row_CriteriosAvaliativos = mysql_fetch_assoc($CriteriosAvaliativos);
$totalRows_CriteriosAvaliativos = mysql_num_rows($CriteriosAvaliativos);

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

function diffMonth($from, $to) {

        $fromYear = date("Y", strtotime($from));
        $fromMonth = date("m", strtotime($from));
        $toYear = date("Y", strtotime($to));
        $toMonth = date("m", strtotime($to));
        if ($fromYear == $toYear) {
            return ($toMonth-$fromMonth)+1;
        } else {
            return (12-$fromMonth)+1+$toMonth;
        }

    }


function nomeMes($numero) {
	
switch ($numero) {
	case 1:
		$nomeMes = "JAN";
		break;
	case 2:
		$nomeMes = "FEV";
		break;
	case 3:
		$nomeMes = "MAR";
		break;
	case 4:
		$nomeMes = "ABR";
		break;
	case 5:
		$nomeMes = "MAI";
		break;
	case 6:
		$nomeMes = "JUN";
		break;
	case 7:
		$nomeMes = "JUL";
		break;
	case 8:
		$nomeMes = "AGO";
		break;
	case 9:
		$nomeMes = "SET";
		break;
	case 10:
		$nomeMes = "OUT";
		break;
	case 11:
		$nomeMes = "NOV";
		break;
	case 12:
		$nomeMes = "DEZ";
		break;
}

return $nomeMes;	
	
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
                <title>DIÁRIO | <?php echo $row_EscolaLogada['escola_nome']; ?> | <?php echo $row_Turma['turma_nome']; ?> | <?php echo $row_AnoLetivo['ano_letivo_ano']; ?> | SMECEL - Sistema de Gestão Escolar</title>
                <meta charset="utf-8">
                <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
                <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
                <meta name="description" content="">
                <meta name="keywords" content="">
                <meta name="mobile-web-app-capable" content="yes">
                <meta name="apple-mobile-web-app-capable" content="yes">
                <link rel="stylesheet" type="text/css" href="css/locastyle.css">                <link rel="stylesheet" type="text/css" href="css/preloader.css">
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
                
                <body onload="alert('Atenção: Configure sua impressora para o formato RETRATO');self.print();">
                
                <div style="page-break-inside: avoid;" class="ls-box1 ls-txt-center" align="center"> <br>
                  <br>
                  <?php if ($row_EscolaLogada['escola_logo']<>"") { ?>
                  <img src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt="" width="130px" />
                  <?php } else { ?>
                  <img src="../../img/brasao_republica.png" alt="" width="130px" />
                  <?php } ?>
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
                  <h3>REGISTRO DE AULAS <span class="ls-ico-checkmark ls-ico-right"></span></h3>
                  <br>
                  <h3>RENDIMENTO DOS ALUNOS <span class="ls-ico-checkmark ls-ico-right"></span></h3>
                  <br>
                  <h3>FREQUÊNCIA DOS ALUNOS <span class="ls-ico-checkmark ls-ico-right"></span></h3>
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
                  <h2>ANO LETIVO <?php echo $anoLetivo; ?> <span class="ls-ico-calendar ls-ico-right"></span></h2>
                  <br>
                </div>
                <div style="page-break-inside: avoid;" class="ls-box1 ls-txt-center" align="center"> <br>
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
        <h3><?php echo $row_Turma['turma_nome']; ?></h3>
        
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
			INNER JOIN
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
          
          <?php if ($totalRows_Aulas > 0) { ?>
          
          <div class="ls-box">
          <p>Professor(a): <strong><?php echo $row_Aulas['func_nome']; ?> (<?php echo $row_Aulas['func_id']; ?>)</strong> </p>
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
            <p class="ls-txt-center" align="center">________________________________________________________<br>Assinatura do(a) professor(a)<br><?php echo $professor; ?></p>
            <br>
            <small><i>*Registro diário com assinatura digital através de login e senha</i></small>
            
          
                      
            <hr>    
            
             <?php } else { ?>
             <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
             <h3 class="ls-txt-center">Nenhum registro encontrado para <?php echo $row_Disciplinas['disciplina_nome']; ?></h3>
             <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
              <?php } ?>   
        
        </div>
        <?php } while ($row_Disciplinas = mysql_fetch_assoc($Disciplinas)); ?>
                <br>
       			<h4>Total de aulas da turma: <?php echo $aulasTotal; ?></h4>
 
                
                
<div style="page-break-inside: avoid;" class="ls-box1 ls-txt-center" align="center"> <br>
                  <br>
                  <?php if ($row_EscolaLogada['escola_logo']<>"") { ?>
                  <img src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt="" width="130px" />
                  <?php } else { ?>
                  <img src="../../img/brasao_republica.png" alt="" width="130px" />
                  <?php } ?>
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
                  <h3 style="font-size:30px; margin-bottom: 20px;"><span style="border-bottom: solid 5px black">RENDIMENTO E FREQUÊNCIA</span></h3>
                  <br>
                  <h1>TURMA <?php echo $row_Turma['turma_nome']; ?></h1>
                  <br>
                  <br>
                  <br>
                  <br>
                  <h3>REGISTRO DE AULAS <span class="ls-ico-checkmark ls-ico-right"></span></h3>
                  <br>
                  <h3>RENDIMENTO DOS ALUNOS <span class="ls-ico-checkmark ls-ico-right"></span></h3>
                  <br>
                  <h3>FREQUÊNCIA DOS ALUNOS <span class="ls-ico-checkmark ls-ico-right"></span></h3>
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
                  <h2>ANO LETIVO <?php echo $anoLetivo; ?> <span class="ls-ico-calendar ls-ico-right"></span></h2>
                  <br>
                </div>
                <div style="page-break-inside: avoid;" class="ls-box1 ls-txt-center" align="center"> <br>
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
                
                
                <?php do { ?>
                  <?php
				   
				   //mysql_select_db($database_SmecelNovo, $SmecelNovo);
						$query_disciplinasMatriz = "
						SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_id_disciplina, disciplina_id, disciplina_nome 
						FROM smc_matriz_disciplinas
						INNER JOIN smc_disciplina ON disciplina_id = matriz_disciplina_id_disciplina 
						WHERE matriz_disciplina_id_matriz = '$row_AlunoBoletim[turma_matriz_id]'";
						$disciplinasMatriz = mysql_query($query_disciplinasMatriz, $SmecelNovo) or die(mysql_error());
						$row_disciplinasMatriz = mysql_fetch_assoc($disciplinasMatriz);
						$totalRows_disciplinasMatriz = mysql_num_rows($disciplinasMatriz);
				   
				   ?>
                   
                   
                  <div style="page-break-inside: avoid;"> <br>
                    <p>
                    
                  <div class="ls-box1"> <span class="ls-float-right" style="margin-left:20px;">
                    <?php if($row_AlunoBoletim['aluno_foto']=="") { ?>
                    <img src="<?php echo URL_BASE.'aluno/fotos/' ?>semfoto.jpg" style="margin:1mm;width:15mm;">
                    <?php } else { ?>
                    <img src="<?php echo URL_BASE.'aluno/fotos/' ?><?php echo $row_AlunoBoletim['aluno_foto']; ?>" style="margin:1mm;width:15mm;">
                    <?php } ?>
                    </span> <small> Aluno(a): <strong><?php echo $row_AlunoBoletim['aluno_nome']; ?></strong><br>
                    Nascimento: <strong><?php echo inverteData($row_AlunoBoletim['aluno_nascimento']); ?></strong><br>
                    Filiação: <strong><?php echo $row_AlunoBoletim['aluno_filiacao1']; ?></strong><br>
                    Turma: <strong><?php echo $row_AlunoBoletim['turma_nome']; ?></strong> </small> </div>
                  </p>
                  <p class="ls-ico-text ls-txt-center">BOLETIM ESCOLAR <?php echo $row_AlunoBoletim['vinculo_aluno_ano_letivo']; ?></p>
                  <table class="ls-sm-space bordasimples" width="100%">
                      <thead>
                      <tr height="30">
                          <td width="200"></td>
                          <?php for ($p = 1; $p <= $row_CriteriosAvaliativos['ca_qtd_periodos']; $p++) { ?>
                          <th class="ls-txt-center" style="background-color:#F5F5F5;" colspan="<?php echo $row_CriteriosAvaliativos['ca_qtd_av_periodos']+1; ?>"><strong><?php echo $p; ?>ª UNIDADE</strong></th>
                          <?php } ?>
                          <th colspan="4" class="ls-txt-center" style="background-color:#F5F5F5;">RESULTADO</th>
                        </tr>
                      <tr height="30">
                          <th class="ls-txt-center" width="200">COMPONENTES CURRICULARES</th>
                          <?php for ($p = 1; $p <= $row_CriteriosAvaliativos['ca_qtd_periodos']; $p++) { ?>
                          <?php for ($a = 1; $a <= $row_CriteriosAvaliativos['ca_qtd_av_periodos']; $a++) { ?>
                          <th class="ls-txt-center"> AV<?php echo $a; ?> </th>
                          <?php } ?>
                          <th class="ls-txt-center" width="40">RU</th>
                          <?php } ?>
                          <th class="ls-txt-center" width="40">TP</th>
                          <th class="ls-txt-center" width="40">MC</th>
                          <th class="ls-txt-center" width="40">AF</th>
                          <th class="ls-txt-center" width="60">RF</th>
                        </tr>
                    </thead>
                      <tbody>
                      <?php do { ?>
                        <tr>
                        <td width="200"><?php echo $row_disciplinasMatriz['disciplina_nome']; ?></td>
                        <?php $tmu = 0; ?>
                        <?php for ($p = 1; $p <= $row_CriteriosAvaliativos['ca_qtd_periodos']; $p++) { ?>
                        <?php $ru = 0; ?>
                        <?php for ($a = 1; $a <= $row_CriteriosAvaliativos['ca_qtd_av_periodos']; $a++) { ?>
                        <td class="ls-txt-center"><?php 
                        //mysql_select_db($database_SmecelNovo, $SmecelNovo);
                        $query_nota = "SELECT nota_id, nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_max, nota_min, nota_valor, nota_hash FROM smc_nota WHERE nota_id_matricula = '$row_AlunoBoletim[vinculo_aluno_id]' AND nota_id_disciplina = '$row_disciplinasMatriz[disciplina_id]' AND nota_periodo = '$p' AND nota_num_avaliacao = '$a'";
                        $nota = mysql_query($query_nota, $SmecelNovo) or die(mysql_error());
                        $row_nota = mysql_fetch_assoc($nota);
                        $totalRows_nota = mysql_num_rows($nota);
                        echo exibeTraco($row_nota['nota_valor'],$row_CriteriosAvaliativos['ca_nota_min_av']);
						$ru = $ru + $row_nota['nota_valor'];
                        ?></td>
                        <?php } ?>
                        <td class="ls-txt-center ls-background-info"><strong>
                          <?php $mu = mediaUnidade($ru,$row_CriteriosAvaliativos['ca_arredonda_media'],$row_CriteriosAvaliativos['ca_aproxima_media'],$row_CriteriosAvaliativos['ca_media_min_periodo'],$row_CriteriosAvaliativos['ca_calculo_media_periodo'],$row_CriteriosAvaliativos['ca_qtd_av_periodos']); ?>
                          <?php $tmu = $tmu + $mu; ?>
                          </strong></td>
                        <?php } ?>
                        <td class="ls-txt-center"><strong>
                          <?php $tp = totalPontos($tmu); ?>
                          </strong></td>
                        <td class="ls-txt-center"><strong>
                          <?php $mc = mediaCurso($tp,$row_CriteriosAvaliativos['ca_arredonda_media'],$row_CriteriosAvaliativos['ca_aproxima_media'],$row_CriteriosAvaliativos['ca_min_media_aprovacao_final'],$row_CriteriosAvaliativos['ca_qtd_periodos']); ?>
                          </strong></td>
                        <td class="ls-txt-center"><strong> <a href="#">
                        <?php 
                        //mysql_select_db($database_SmecelNovo, $SmecelNovo);
                        $query_notaAf = "SELECT nota_id, nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_max, nota_min, nota_valor, nota_hash FROM smc_nota WHERE nota_id_matricula = '$row_AlunoBoletim[vinculo_aluno_id]' AND nota_id_disciplina = '$row_disciplinasMatriz[disciplina_id]' AND nota_periodo = '99' AND nota_num_avaliacao = '99'";
                        $notaAf = mysql_query($query_notaAf, $SmecelNovo) or die(mysql_error());
                        $row_notaAf = mysql_fetch_assoc($notaAf);
                        $totalRows_notaAf = mysql_num_rows($notaAf);
						echo $af = avaliacaoFinal($row_notaAf['nota_valor'],$row_CriteriosAvaliativos['ca_nota_min_recuperacao_final']);
                        ?>
                        </a> </strong></td>
                        <td class="ls-txt-center"><?php 
					echo resultadoFinal($mc, $af, $row_CriteriosAvaliativos['ca_nota_min_recuperacao_final'], $row_CriteriosAvaliativos['ca_min_media_aprovacao_final']);
				?></td>
                      </tr>
                        <?php } while ($row_disciplinasMatriz = mysql_fetch_assoc($disciplinasMatriz)); ?>
                    </tbody>
                    </table>
                  <br>
                  <h3 class="ls-txt-center">OBSERVAÇÕES DO ALUNO</h3>
                  <br>
                  <?php for ($p = 1; $p <= $row_CriteriosAvaliativos['ca_qtd_periodos']; $p++) { ?>
                  <table class="ls-sm-space bordasimples" width="100%">
                      <thead>
                      <tr>
                          <th width="50%"> <?php echo $p; ?>ª UNIDADE</th>
                        </tr>
                    </thead>
                      <tbody>
                      <tr>
                          <td>&nbsp;</td>
                        </tr>
                      <tr>
                          <td>&nbsp;</td>
                        </tr>
                      <tr>
                          <td>&nbsp;</td>
                        </tr>
                      <tr>
                          <td>&nbsp;</td>
                        </tr>
                    </tbody>
                    </table>
                  <?php } ?>
                  <table class="ls-sm-space bordasimples" width="100%">
                      <thead>
                      <tr>
                          <th>Período de Recuperação e Avaliação Final</th>
                        </tr>
                    </thead>
                      <tbody>
                      <tr>
                          <td>&nbsp;</td>
                        </tr>
                      <tr>
                          <td>&nbsp;</td>
                        </tr>
                      <tr>
                          <td>&nbsp;</td>
                        </tr>
                      <tr>
                          <td>&nbsp;</td>
                        </tr>
                    </tbody>
                    </table>
                </div>
                
                
                
                
                
                
                  <div style="page-break-inside: avoid;">
                  <?php 
				mysql_select_db($database_SmecelNovo, $SmecelNovo);
				$query_Faltas = "
				SELECT faltas_alunos_id, faltas_alunos_matricula_id, faltas_alunos_disciplina_id, faltas_alunos_numero_aula, faltas_alunos_data, faltas_alunos_justificada, faltas_alunos_justificativa 
				FROM smc_faltas_alunos
				WHERE faltas_alunos_data BETWEEN '$row_AnoLetivo[ano_letivo_inicio]' AND '$row_AnoLetivo[ano_letivo_fim]'
				AND faltas_alunos_matricula_id = '$row_AlunoBoletim[vinculo_aluno_id]';
				";
				$Faltas = mysql_query($query_Faltas, $SmecelNovo) or die(mysql_error());
				$row_Faltas = mysql_fetch_assoc($Faltas);
				$totalRows_Faltas = mysql_num_rows($Faltas);
				
				mysql_select_db($database_SmecelNovo, $SmecelNovo);
				$query_FaltasA = "
				SELECT faltas_alunos_id, faltas_alunos_matricula_id, faltas_alunos_disciplina_id, faltas_alunos_numero_aula, faltas_alunos_data, faltas_alunos_justificada, faltas_alunos_justificativa,
				disciplina_id, disciplina_nome, COUNT(*) AS total 
				FROM smc_faltas_alunos
				INNER JOIN smc_disciplina ON disciplina_id = faltas_alunos_disciplina_id
				WHERE faltas_alunos_data BETWEEN '$row_AnoLetivo[ano_letivo_inicio]' AND '$row_AnoLetivo[ano_letivo_fim]'
				AND faltas_alunos_matricula_id = '$row_AlunoBoletim[vinculo_aluno_id]'
				GROUP BY faltas_alunos_disciplina_id
				";
				$FaltasA = mysql_query($query_FaltasA, $SmecelNovo) or die(mysql_error());
				$row_FaltasA = mysql_fetch_assoc($FaltasA);
				$totalRows_FaltasA = mysql_num_rows($FaltasA);				
				
				
				//$array = [];
				
				do { 
					
					$datas[] = $row_Faltas['faltas_alunos_data']."-".$row_Faltas['faltas_alunos_numero_aula'];
					
				} while ($row_Faltas = mysql_fetch_assoc($Faltas));
				
				

				
				?>
                <?php
										
					$totalMeses = diffMonth($row_AnoLetivo['ano_letivo_inicio'], $row_AnoLetivo['ano_letivo_fim']);

					//$meses = 12;
					
					$ano = $row_AnoLetivo['ano_letivo_ano'];
					
					$mesInicio = date("m", strtotime($row_AnoLetivo['ano_letivo_inicio']));
					$anoInicio = date("y", strtotime($row_AnoLetivo['ano_letivo_inicio']));
					
					$mesFim = date("m", strtotime($row_AnoLetivo['ano_letivo_fim']));
					$anoFim = date("y", strtotime($row_AnoLetivo['ano_letivo_fim']));
										
					//date("m", strtotime($row_AnoLetivo['ano_letivo_inicio']));
				  
				  ?>
                <p>
                <div class="ls-box1"> <span class="ls-float-right" style="margin-left:20px;">
                  <?php if($row_AlunoBoletim['aluno_foto']=="") { ?>
                  <img src="<?php echo URL_BASE.'aluno/fotos/' ?>semfoto.jpg" style="margin:1mm;width:15mm;">
                  <?php } else { ?>
                  <img src="<?php echo URL_BASE.'aluno/fotos/' ?><?php echo $row_AlunoBoletim['aluno_foto']; ?>" style="margin:1mm;width:15mm;">
                  <?php } ?>
                  </span> <small> Aluno(a): <strong><?php echo $row_AlunoBoletim['aluno_nome']; ?></strong><br>
                  Nascimento: <strong><?php echo inverteData($row_AlunoBoletim['aluno_nascimento']); ?></strong><br>
                  Filiação: <strong><?php echo $row_AlunoBoletim['aluno_filiacao1']; ?></strong><br>
                  Turma: <strong><?php echo $row_AlunoBoletim['turma_nome']; ?></strong><br> 
                  Data da matrícula: <strong><?php echo date("d/m/Y", strtotime($row_AlunoBoletim['vinculo_aluno_data'])); ?></strong>
                  <?php if ($row_AlunoBoletim['vinculo_aluno_situacao']<>"1") { ?>Matrícula encerrada em <?php echo date("d/m/Y", strtotime($row_AlunoBoletim['vinculo_aluno_datatransferencia'])); ?><?php } ?>
                  </small> 
                  
                  </div>
                </p>
                <p class="ls-ico-text ls-txt-center">FREQUENCIA ESCOLAR <?php echo $anoLetivo; ?></p>
                 
                 <?php if ($row_AlunoBoletim['vinculo_aluno_data'] > $row_AnoLetivo['ano_letivo_inicio']) { ?><br><strong><small>*Atenção: Esta matrícula foi realizada após o início do ano letivo<br><br></small></strong><?php } ?>
                 <table width="100%" class="ls-sm-space bordasimples" cellpadding="0" cellspacing="0" border="0">
                    
                    <tr class="ls-txt-center">
                    
                    <!-- LINHA MESES -->
					<td>Meses</td>
                    <?php for ($mesCont = 1; $mesCont <= $totalMeses; $mesCont++) { ?>
                    <td width="150"><?php echo nomeMes($mesInicio); ?>/<?php echo $anoInicio; ?></td>
                    <?php $mesFalta[$mesInicio] = 0; ?>
                    <?php 
					  if ($mesInicio == 12) {
						  $mesInicio = 1;
						  $anoInicio++;
					  } else {
						  $mesInicio++;
					  } 
	  				?>
                    <?php } ?>
                    <?php $mesInicio = date("m", strtotime($row_AnoLetivo['ano_letivo_inicio'])); ?>
                    </tr>

                    <!-- LINHA AULAS -->
                    <tr class="ls-txt-center">
                    <td>Aulas</td>
					<?php for ($mesCont = 1; $mesCont <= $totalMeses; $mesCont++) { ?>
                    <td width="">
                        <div style="width:100%; padding:0; margin:0">
                        <?php $perc = 100/$row_Matriz['matriz_aula_dia']; ?>
						<?php for ($aulaCont = 1; $aulaCont <= $row_Matriz['matriz_aula_dia']; $aulaCont++) { ?>
							<div class="" style="width:<?php echo $perc; ?>%; float:left; border-left:#999 solid 1px; border-right:#999 solid 1px; padding:0; margin:0; background-color:#CCCCCC;"><?php echo $aulaCont; ?></div>
                        <?php } ?>
                        </div>
                    </td>
                    <?php 
					  if ($mesInicio == 12) {
						  $mesInicio = 1;
						  $anoInicio++;
					  } else {
						  $mesInicio++;
					  } 
	  				?>
                    <?php } ?>
                    <?php $mesInicio = date("m", strtotime($row_AnoLetivo['ano_letivo_inicio'])); ?>
                    
                    </tr>
                    

					<!-- DIAS -->
                    <?php $anoInicio = date("Y", strtotime($row_AnoLetivo['ano_letivo_inicio'])); ?>
                    <?php for ($diaCont = 1; $diaCont <= 31; $diaCont++) { ?>
                    
                    <tr class="ls-txt-center">
					<td><?php echo $diaCont; ?></td>
					<?php for ($mesCont = 1; $mesCont <= $totalMeses; $mesCont++) { ?>
                    <td width="">
                    
                    
					<?php $dataAgora = $anoInicio."-".str_pad($mesInicio, 2, "0", STR_PAD_LEFT)."-".str_pad($diaCont, 2, "0", STR_PAD_LEFT); ?>      
                    	<?php for ($aulaCont = 1; $aulaCont <= $row_Matriz['matriz_aula_dia']; $aulaCont++) { ?>
                        	<div class="" style="width:<?php echo $perc; ?>%; float:left; border-left:#999 solid 1px; border-right:#999 solid 1px;">
							<?php 
											if ($row_AlunoBoletim['vinculo_aluno_data'] > $dataAgora) { 
												echo "<span class=\"ls-ico-minus\"></span>";
											} else {
											if (in_array($dataAgora."-".$aulaCont, $datas)) { 
    												//echo "<span class=\"ls-ico-close ls-color-danger\"></span>";
													echo "<strong><span class=\"ls-color-danger\">X</span></strong>";
													$mesFalta[$mesInicio]++;
													
													
											} else {
												echo "&#8226;";
											}
											}
							?>
                            </div>
                        <?php } ?>
                    </td>
                    <?php 
					  if ($mesInicio == 12) {
						  $mesInicio = 1;
						  $anoInicio++;
					  } else {
						  $mesInicio++;
					  } 
	  				?>
                    <?php } ?>
                    <?php $mesInicio = date("m", strtotime($row_AnoLetivo['ano_letivo_inicio'])); ?>
                    <?php $anoInicio = date("Y", strtotime($row_AnoLetivo['ano_letivo_inicio'])); ?>
                    </tr>
                    <?php } ?> 
                    
                    
                    <!-- LINHA MESES RODAPÉ-->
                    <tr class="ls-txt-center">
                    
					<td>FALTAS</td>
                    <?php for ($mesCont = 1; $mesCont <= $totalMeses; $mesCont++) { ?>
                    <td width="150">Faltas: <strong><?php echo $mesFalta[$mesInicio]; ?></strong></td>
                    <?php 
					  if ($mesInicio == 12) {
						  $mesInicio = 1;
						  $anoInicio++;
					  } else {
						  $mesInicio++;
					  } 
	  				?>
                    <?php } ?>
                    <?php $mesInicio = date("m", strtotime($row_AnoLetivo['ano_letivo_inicio'])); ?>
                    </tr>
                                       	
                    </table>
                    
                    
                    
                    
					 <br>
                     
                     <?php $totalFaltas = 0; ?>
                     <?php if ($totalRows_FaltasA > 0) { ?>
					 <table width="100%" class="ls-sm-space bordasimples" cellpadding="0" cellspacing="0" border="0">
                     <tr>
                     	<th>COMPONENTE</th>
                        <th>TOTAL DE FALTAS</th>
                     </tr>
					 <?php do { ?>
					  <tr>
						 <td><?php echo $row_FaltasA['disciplina_nome']; ?></td>
                         <td class="ls-txt-center"><?php echo $row_FaltasA['total']; ?></td>
					  </tr>
                     <?php $totalFaltas = $totalFaltas + $row_FaltasA['total']; ?> 	  
					 <?php } while ($row_FaltasA = mysql_fetch_assoc($FaltasA)); ?>
                     <tr>
						 <td class="ls-txt-center">TOTAL DE FALTAS NO ANO</td>
                         <td class="ls-txt-center"><strong><?php echo $totalFaltas; ?></strong></td>
					  </tr>	 
                     </table>
                     <?php  } ?>
                <?php 
					unset($datas); //exit; 
					//exit; 
					?>
                </div>
                <?php } while ($row_AlunoBoletim = mysql_fetch_assoc($AlunoBoletim)); ?>
                
                
                <div style="page-break-inside: avoid;" class="ls-box1 ls-txt-center" align="center"> <br>
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
                  <h2>Prefeitura de <?php echo $row_EscolaLogada['sec_cidade']; ?></h2>
                  <h2>ANO LETIVO <?php echo $anoLetivo; ?> <span class="ls-ico-calendar ls-ico-right"></span></h2>
                  <br>
                </div>
                

                
                
                
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

mysql_free_result($AlunoBoletim);

mysql_free_result($CriteriosAvaliativos);

mysql_free_result($Matriz);

mysql_free_result($disciplinasMatriz);

mysql_free_result($Faltas);
?>
				