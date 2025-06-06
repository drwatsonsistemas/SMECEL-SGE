<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include('fnc/inverteData.php'); ?>
<?php include "fnc/anti_injection.php"; ?>
<?php //include('fnc/notas.php'); ?>
<?php include('../funcoes/url_base.php'); ?>
<?php include "fnc/calculos.php"; ?>
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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AlunoBoletim = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_conselho,
vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_rel_aval,
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
$query_CriteriosAvaliativos = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes, ca_digitos FROM smc_criterios_avaliativos WHERE ca_id = '$row_Matriz[matriz_criterio_avaliativo]'";
$CriteriosAvaliativos = mysql_query($query_CriteriosAvaliativos, $SmecelNovo) or die(mysql_error());
$row_CriteriosAvaliativos = mysql_fetch_assoc($CriteriosAvaliativos);
$totalRows_CriteriosAvaliativos = mysql_num_rows($CriteriosAvaliativos);

$procurar  = array('\n', '\\','\"');
$buscar  = array('<br><br>', '','"');

function arredondarNota($nota) {
    $decimal = round($nota - floor($nota), 2); // Arredonda para 2 casas decimais
    
    if ($decimal >= 0.75) {
      return ceil($nota);
    } elseif ($decimal >= 0.5 && $decimal < 0.75) {
      return floor($nota) + 0.5;
    } elseif ($decimal >= 0.3 && $decimal < 0.5) {
      return floor($nota) + 0.5;
    } else {
      return floor($nota);
    }
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
  <link rel="stylesheet" type="text/css" href="css/locastyle.css">  <script src="js/locastyle.js"></script>
  <style>
   table.bordasimples {
    border-collapse: collapse;
    font-size:7px;
  }
  table.bordasimples tr td {
    border:1px solid #808080;
    padding:2px;
    font-size:12px;
  }
  table.bordasimples tr th {
    border:1px solid #808080;
    padding:2px;
    font-size:9px;
  }

  .nota-paralela {
    color: #B9A016;
  }

  .nota-paralela-apr {
    color: #2881AC;
  }

  .nota-paralela-rep {
    color: #D75553;
  }

  .nota-apr {
    color: #388f39;
  }
</style>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<body onload="self.print();">
  <div class="container-fluid">
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
      <h1>TURMA <?php echo $row_AlunoBoletim['turma_nome']; ?></h1>
      <br>
      <br>
      <br>
      <h3>RENDIMENTO DOS ALUNOS <span class="ls-ico-checkmark ls-ico-right"></span></h3>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
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
        </span> 

        <small> Aluno(a): <strong><?php echo $row_AlunoBoletim['aluno_nome']; ?></strong><br>
          Nascimento: <strong><?php echo inverteData($row_AlunoBoletim['aluno_nascimento']); ?></strong><br>
          Filiação: <strong><?php echo $row_AlunoBoletim['aluno_filiacao1']; ?></strong><br>
          Turma: <strong><?php echo $row_AlunoBoletim['turma_nome']; ?> | Ano Letivo <?php echo $anoLetivo; ?></strong> </small> </div>
        </p>
        <p class="ls-ico-text ls-txt-center">BOLETIM ESCOLAR <?php echo $row_AlunoBoletim['vinculo_aluno_ano_letivo']; ?></p>
        <table class="ls-sm-space bordasimples" width="100%">
          <thead>
            <tr height="30">
              <td width="200"></td>
              <?php for ($p = 1; $p <= $row_CriteriosAvaliativos['ca_qtd_periodos']; $p++) { ?>
                <th colspan="2" class="ls-txt-center" style="background-color:#F5F5F5;" colspan="<?php echo $row_CriteriosAvaliativos['ca_qtd_av_periodos']+1; ?>"><strong><?php echo $p; ?>ª TRIMESTRE</strong></th>
              <?php } ?>
              <th colspan="2" class="ls-txt-center" style="background-color:#F5F5F5;">RESULTADO</th>
            </tr>
            <tr height="30">
              <th class="ls-txt-center" width="200">COMPONENTES CURRICULARES</th>
              <?php for ($p = 1; $p <= $row_CriteriosAvaliativos['ca_qtd_periodos']; $p++) { ?>
                <?php for ($a = 1; $a <= $row_CriteriosAvaliativos['ca_qtd_av_periodos']; $a++) { ?>
                  <th class="ls-txt-center"> AV<?php echo $a; ?> </th>
                <?php } ?>
                <th width="15" style="background-color:#e4e9ec" class="ls-txt-center">TT.</th>
                <th width="15" style="background-color:#e4e9ec" class="ls-txt-center">REC.</th>
              <?php } ?>
              <th width="15" style="background-color:#ccde9e" class="ls-txt-center">TP.</th>
              <th width="15" style="background-color:#ccde9e" class="ls-txt-center">RF</th>
            </tr>
          </thead>
          <tbody>
            <?php

            $alunos_na_paralela = array();
            $alunos_aprovados_paralela = array();
            $alunos_reprovados_paralela = array();
            $alunos_aprovados = array();
            $num = 1;
            $pMaxQualitativa = 0;
            $pMaxQuantitativa = 0;
            $pMaxParalela = 0;

            ?>
            <?php
            do { ?>
              <tr>
                <td width="150"><?php echo $row_disciplinasMatriz['disciplina_nome']; ?></td>
                <?php 
                $totalTrimestre = 0;
                $totalTrimestreSemRecuperacao = 0;
                $pontuacaoTotalAnoLetivo1 = 0;
                $pontuacaoTotalAnoLetivo2 = 0;
                $pontuacaoTotalAnoLetivo3 = 0;

                for ($p = 1; $p <= $row_CriteriosAvaliativos['ca_qtd_periodos']; $p++) { 
    // Consultas ao banco de dados para obter as notas qualitativas, quantitativas, paralela e de recuperação
                  $query_qualitativo = sprintf("SELECT qq_nota FROM smc_notas_qq WHERE qq_id_matricula = %s AND qq_id_componente = %s AND qq_id_periodo = %s AND qq_tipo_criterio='1'", 
                    GetSQLValueString($row_AlunoBoletim['vinculo_aluno_id'], "int"), 
                    GetSQLValueString($row_disciplinasMatriz['disciplina_id'], "int"), 
                    GetSQLValueString($p, "int"));
                  $qualitativo = mysql_query($query_qualitativo, $SmecelNovo) or die(mysql_error());
                  $somaPontuacaoQualitativo = 0;
                  while ($row_qualitativo = mysql_fetch_assoc($qualitativo)) {
                    $somaPontuacaoQualitativo += floatval($row_qualitativo['qq_nota']);
                  }

                  $query_quantitativo = sprintf("SELECT qq_nota FROM smc_notas_qq WHERE qq_id_matricula = %s AND qq_id_componente = %s AND qq_id_periodo = %s AND qq_tipo_criterio='2'", 
                    GetSQLValueString($row_AlunoBoletim['vinculo_aluno_id'], "int"), 
                    GetSQLValueString($row_disciplinasMatriz['disciplina_id'], "int"), 
                    GetSQLValueString($p, "int"));
                  $quantitativo = mysql_query($query_quantitativo, $SmecelNovo) or die(mysql_error());
                  $somaPontuacaoQuantitativo = 0;
                  while ($row_quantitativo = mysql_fetch_assoc($quantitativo)) {
                    $somaPontuacaoQuantitativo += floatval($row_quantitativo['qq_nota']);
                  }

                  $query_paralela = sprintf("SELECT qq_nota FROM smc_notas_qq WHERE qq_id_matricula = %s AND qq_id_componente = %s AND qq_id_periodo = %s AND qq_tipo_criterio='3'", 
                    GetSQLValueString($row_AlunoBoletim['vinculo_aluno_id'], "int"), 
                    GetSQLValueString($row_disciplinasMatriz['disciplina_id'], "int"), 
                    GetSQLValueString($p, "int"));
                  $paralela = mysql_query($query_paralela, $SmecelNovo) or die(mysql_error());
                  $notaParalela = 0;
                  if ($row_paralela = mysql_fetch_assoc($paralela)) {
                    $notaParalela = floatval($row_paralela['qq_nota']);
                  }

                  $query_recuperacao = sprintf("SELECT qq_nota FROM smc_notas_qq WHERE qq_id_matricula = %s AND qq_id_componente = %s AND qq_id_periodo = %s AND qq_tipo_criterio='4'", 
                    GetSQLValueString($row_AlunoBoletim['vinculo_aluno_id'], "int"), 
                    GetSQLValueString($row_disciplinasMatriz['disciplina_id'], "int"), 
                    GetSQLValueString($p, "int"));
                  $recuperacao = mysql_query($query_recuperacao, $SmecelNovo) or die(mysql_error());
                  $notaRecuperacao = 0;
                  if ($row_recuperacao = mysql_fetch_assoc($recuperacao)) {
                    $notaRecuperacao = floatval($row_recuperacao['qq_nota']);
                  }

    // Ajustando valores de acordo com os períodos
                  switch ($p) {
                    case '1':
                    case '2':
            $mediaTrimestre = 18; // Média necessária para os períodos 1 e 2
            break;
            case '3':
            $mediaTrimestre = 24; // Média necessária para o período 3
            break;
          }

    // Soma da nota qualitativa com a quantitativa, ou com a paralela se existir
          $totalTrimestreSemRecuperacao = $somaPontuacaoQualitativo + ($notaParalela > 0 ? $notaParalela : $somaPontuacaoQuantitativo);

    // Definindo a nota total do trimestre
          $totalTrimestre = $notaRecuperacao > 0 ? $notaRecuperacao : $totalTrimestreSemRecuperacao;

          $totalTrimestre = arredondarNota($totalTrimestre);
          $totalTrimestreSemRecuperacao = arredondarNota($totalTrimestreSemRecuperacao);

          switch ($p) {
            case '1':
            $pontuacaoTotalAnoLetivo1 = $totalTrimestre;
            break;
            case '2':
            $pontuacaoTotalAnoLetivo2 = $totalTrimestre;
            break;
            case '3':
            $pontuacaoTotalAnoLetivo3 = $totalTrimestre;
            break;
          }

    // Inicializa a classe da nota como reprovado
          $classeNota = 'nota-paralela-rep';

    // Verifica aprovação com paralela ou recuperação
          if ($totalTrimestre >= $mediaTrimestre) {
            $classeNota = 'nota-apr';
            $alunos_aprovados[] = array('aluno_id' => $row_AlunoBoletim['vinculo_aluno_id'], 'periodo' => $p);
          } else if ($notaParalela > 0 && $notaParalela >= 8.1 && $totalTrimestreSemRecuperacao >= $mediaTrimestre) {
            $classeNota = 'nota-paralela-apr';
            $alunos_aprovados[] = array('aluno_id' => $row_AlunoBoletim['vinculo_aluno_id'], 'periodo' => $p);
          } else {
            $alunos_reprovados_paralela[] = array('aluno_id' => $row_AlunoBoletim['vinculo_aluno_id'], 'periodo' => $p);
          }

          $txtApr = "APR";
          $pontuacaoTotal = $pontuacaoTotalAnoLetivo1 + $pontuacaoTotalAnoLetivo2 + $pontuacaoTotalAnoLetivo3;
          if ($pontuacaoTotal < 60 && $row_AlunoBoletim['vinculo_aluno_conselho'] == "S") {
            $pontuacaoTotal = 60;
            $txtApr = "APR. CONSELHO";
          }

          ?>
          <td class="<?php echo $classeNota; ?> ls-txt-center"><?php echo $totalTrimestreSemRecuperacao; ?></td>
          <td class="ls-txt-center"><?php echo $notaRecuperacao; ?></td>
        <?php } ?>
    
        <td class="ls-txt-center">
          <span
          class="ls-text-md" style="color:<?php if($pontuacaoTotal >= 60){echo 'blue';}else{echo '';} ?>"><?= $pontuacaoTotal ?></span>
        </td>
        <td width="15" inputmode="numeric" disciplina="<?php echo $row_Disciplina['disciplina_nome']; ?>" class="ls-txt-center"><span class="ls-text-md">

          <?php

          if($pontuacaoTotal < 60){
              echo "<span style='color: red;'>REP</span>";
           
          }else{
              echo "<span style='color: green;'>$txtApr</span>";
           
          }
          ?>

        </span></td>
      </tr>
    <?php } while ($row_disciplinasMatriz = mysql_fetch_assoc($disciplinasMatriz)); ?>
  </tbody>
</table>

<br>





<?php 

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_parecer = "
SELECT p_ind_id, p_ind_id_prof, p_ind_mat_aluno, p_ind_texto, p_ind_data_cadastro, p_ind_periodo, func_id, func_nome 
FROM smc_parecer_individual_professor 
INNER JOIN smc_func ON func_id = p_ind_id_prof
WHERE p_ind_mat_aluno = '$row_AlunoBoletim[vinculo_aluno_id]' ORDER BY p_ind_periodo ASC";
$parecer = mysql_query($query_parecer, $SmecelNovo) or die(mysql_error());
$row_parecer = mysql_fetch_assoc($parecer);
$totalRows_parecer = mysql_num_rows($parecer);

?>


<br>



<div class="ls-box">
  <h3 class="ls-txt-center">OBSERVAÇÕES DO ALUNO</h3>
  
  <?php echo str_replace($procurar,$buscar,$row_AlunoBoletim['vinculo_aluno_rel_aval']); ?>
  
  <hr>
  
  <table class="ls-table">
   <?php 
   if ($totalRows_parecer>0) {
     do { ?>
      <tr>
        <td><strong><?php echo $row_parecer['p_ind_periodo']; ?>º período | <?php echo $row_parecer['func_nome']; ?></strong><br><br><?php echo str_replace($procurar,$buscar,$row_parecer['p_ind_texto']); ?></td>
      </tr>
    <?php } while ($row_parecer = mysql_fetch_assoc($parecer)); } ?>
  </table>
  
</div>






</div>



<?php } while ($row_AlunoBoletim = mysql_fetch_assoc($AlunoBoletim)); ?>






<!-- CONTEÚDO --> 
</div>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>
</body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($AlunoBoletim);

mysql_free_result($CriteriosAvaliativos);

mysql_free_result($Matriz);

mysql_free_result($disciplinasMatriz);
?>