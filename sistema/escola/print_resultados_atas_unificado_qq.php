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
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_multietapa, vinculo_aluno_datatransferencia, vinculo_aluno_conselho, vinculo_aluno_reprovado_faltas,
etapa_id, etapa_nome, etapa_nome_abrev,
CASE vinculo_aluno_situacao
WHEN 1 THEN 'MATRICULADO'
WHEN 2 THEN 'TRANSFERIDO'
WHEN 3 THEN 'DESISTENTE'
WHEN 4 THEN 'FALECIDO'
WHEN 5 THEN 'OUTROS'
END AS vinculo_aluno_situacao_nome,
aluno_id, aluno_nome, aluno_nascimento, aluno_filiacao1, aluno_foto, aluno_hash,
turma_id, turma_nome, turma_matriz_id, turma_turno, turma_etapa, turma_ano_letivo, turma_multisseriada,
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno_nome  
FROM 
smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma 
LEFT JOIN smc_etapa ON etapa_id = vinculo_aluno_multietapa 
WHERE vinculo_aluno_boletim = '1' AND vinculo_aluno_ano_letivo = '$anoLetivo' AND vinculo_aluno_id_escola = '$row_UsuLogado[usu_escola]' $buscaTurma
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
$query_CriteriosAvaliativos = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes, ca_digitos, ca_grupo_conceito, ca_questionario_conceitos FROM smc_criterios_avaliativos WHERE ca_id = '$row_Matriz[matriz_criterio_avaliativo]'";
$CriteriosAvaliativos = mysql_query($query_CriteriosAvaliativos, $SmecelNovo) or die(mysql_error());
$row_CriteriosAvaliativos = mysql_fetch_assoc($CriteriosAvaliativos);
$totalRows_CriteriosAvaliativos = mysql_num_rows($CriteriosAvaliativos);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_GrupoConceitos = "
SELECT conceito_itens_id, conceito_itens_id_conceito, conceito_itens_descricao, conceito_itens_legenda, conceito_itens_peso 
FROM smc_conceito_itens
WHERE conceito_itens_id_conceito = '$row_CriteriosAvaliativos[ca_grupo_conceito]'
ORDER BY conceito_itens_peso ASC
";
$GrupoConceitos = mysql_query($query_GrupoConceitos, $SmecelNovo) or die(mysql_error());
$row_GrupoConceitos = mysql_fetch_assoc($GrupoConceitos);
$totalRows_GrupoConceitos = mysql_num_rows($GrupoConceitos);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AnoLetivoDesc = "SELECT ano_letivo_id, ano_letivo_ano, ano_letivo_inicio, ano_letivo_fim, ano_letivo_aberto, ano_letivo_id_sec, ano_letivo_data_rematricula FROM smc_ano_letivo WHERE ano_letivo_ano = '$anoLetivo' AND ano_letivo_id_sec = '$row_UsuLogado[usu_sec]' ORDER BY ano_letivo_ano DESC LIMIT 1";
$AnoLetivoDesc = mysql_query($query_AnoLetivoDesc, $SmecelNovo) or die(mysql_error());
$row_AnoLetivoDesc = mysql_fetch_assoc($AnoLetivoDesc);
$totalRows_AnoLetivoDesc = mysql_num_rows($AnoLetivoDesc);

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
    <title>ATA DE RESULTADOS FINAIS POR PERÍODO - <?php echo $row_AlunoBoletim['turma_nome'] ." - ".$row_EscolaLogada['escola_nome'] ?></title>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link rel="stylesheet" type="text/css" href="css/locastyle.css"><script src="js/locastyle.js"></script>
    <style>
      html{
        -webkit-print-color-adjust: exact;
      }
      @media print {
        /* Define a orientação como paisagem para impressão */
        @page {
          size: landscape;
        }
      }
      
      table.bordasimples {
        border-collapse: collapse;
        font-size:5px;
      }
      table.bordasimples tr td {
        border:1px solid #808080;
        padding:2px;
        font-size:10px;
      }
      table.bordasimples tr th {
        border:1px solid #808080;
        padding:2px;
        font-size:8px;
      }
      .foo {
        writing-mode: vertical-lr;
        -webkit-writing-mode: vertical-lr;
  -ms-writing-mode: vertical-lr;/*  -webkit-transform:rotate(180deg); //tente 90 no lugar de 270
-moz-transform:rotate(180deg);
-o-transform: rotate(180deg); */
}
</style>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">

</head>
<body onload="window.print()">
  <div class="container-fluid">
    <div style="page-break-inside: avoid;" class="ls-box1 ls-txt-center" align="center"> <br>
      <table>
        <tr>
          <td width="150" class="ls-txt-center"><?php if ($row_EscolaLogada['escola_logo']<>"") { ?>
            <img src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt="" width="70%" />
          <?php } else { ?>
            <img src="../../img/brasao_republica.png" alt="" width="70%" />
          <?php } ?>
          <br></td>
          <td class="ls-txt-left"><strong><?php echo $row_EscolaLogada['escola_nome']; ?></strong><br>
            <small>INEP: <?php echo $row_EscolaLogada['escola_inep']; ?> AUT: - D.O. -<br>
              ENDEREÇO: <?php echo $row_EscolaLogada['escola_endereco']; ?>, <?php echo $row_EscolaLogada['escola_num']; ?>, <?php echo $row_EscolaLogada['escola_bairro']; ?> <?php echo $row_EscolaLogada['sec_cidade']; ?>-<?php echo $row_EscolaLogada['sec_uf']; ?> CEP: <?php echo $row_EscolaLogada['escola_cep']; ?><br>
              CNPJ: <?php echo $row_EscolaLogada['escola_cnpj']; ?> <?php echo $row_EscolaLogada['escola_email']; ?> <?php echo $row_EscolaLogada['escola_telefone1']; ?></small></td>
            </tr>
          </table>
          <p>
            <h2 class="ls-txt-center">ATA DE RESULTADOS FINAIS POR PERÍODO - <strong><?php echo $row_AlunoBoletim['turma_nome'] ?></strong></h2>
          </p>

          
          <?php 
          $contaAprovadosEscola = 0;
          $contaReprovadosEscola = 0;
          $contaTransferidosEscola = 0;
          $contaDesistentesEscola = 0;
          $contaFalecidosEscola = 0;
          $contaOutrosEscola = 0;
          ?>
          <?php 
          $contaSituacao = 0;
          ?>
          <?php

       //mysql_select_db($database_SmecelNovo, $SmecelNovo);
          $query_disciplinasMatrizCab = "
          SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_id_disciplina, matriz_disciplina_reprova, disciplina_id, disciplina_nome 
          FROM smc_matriz_disciplinas
          INNER JOIN smc_disciplina ON disciplina_id = matriz_disciplina_id_disciplina 
          WHERE matriz_disciplina_id_matriz = '$row_AlunoBoletim[turma_matriz_id]'";
          $disciplinasMatrizCab = mysql_query($query_disciplinasMatrizCab, $SmecelNovo) or die(mysql_error());
          $row_disciplinasMatrizCab = mysql_fetch_assoc($disciplinasMatrizCab);
          $totalRows_disciplinasMatrizCab = mysql_num_rows($disciplinasMatrizCab);


          $disciplinas = array();
          do {
            $disciplinas[] = $row_disciplinasMatrizCab; // Armazene as disciplinas no array
          }while ($row_disciplinasMatrizCab = mysql_fetch_assoc($disciplinasMatrizCab));

          
          ?>
          <?php if($row_CriteriosAvaliativos['ca_questionario_conceitos']=="S"){?>
            <?php 
            $perc = number_format(100/$totalRows_GrupoConceitos, 0);
            $inicio = 0;
            $parc = $perc;
            $cont = 1;
            $ver = 0;
            ?>

            <table>
              <?php do { ?>
                <tr>
                  <td class="ls-txt-left">
                    <span class="ls-tag-warning">
                      De <?php echo $inicio; ?>% até <?php echo min($parc, 100); ?>%
                      <?php $cont++; ?>
                      <?php if ($cont == $totalRows_GrupoConceitos) { 
                        $ver = 1; 
                      }?>
                      <?php $inicio = min($parc + 1, 100); $parc = min($parc + $perc + $ver, 100); ?>
                    </span>
                    <span class="ls-tag-info">
                      <?php echo $row_GrupoConceitos['conceito_itens_legenda']; ?>: <?php echo $row_GrupoConceitos['conceito_itens_descricao']; ?>
                    </span>
                  </td>
                </tr>
              <?php } while ($row_GrupoConceitos = mysql_fetch_assoc($GrupoConceitos)); ?>
            </table>


          <?php } ?>


          <table width="100%" class="ls-sm-space ls-table-striped bordasimples">
            <tr>
              <th></th>
              <th></th>


              <?php for ($i=1; $i <= $row_CriteriosAvaliativos['ca_qtd_periodos'] ; $i++) { ?>
                <th  style="background-color: #90ee90" colspan="<?php echo $totalRows_disciplinasMatrizCab + 1 ?>"><?php echo $i . "° Período" ?></th>
              <?php } ?>
            </tr>
            <tr>
              <th>Nº</th>
              <th width="">ALUNO(A)</th>

              <?php for ($i = 1; $i <= $row_CriteriosAvaliativos['ca_qtd_periodos']; $i++) : ?>
                <th class="foo" style="background-color: #90ee90"></th>
                <?php foreach ($disciplinas as $disciplina) : ?>

                  <th class="foo"><?php echo $disciplina['disciplina_nome']; ?></th>
                <?php endforeach; ?>
              <?php endfor; ?>

              <!-- <th align="center" style="border:1px solid #808080; border-right:1px solid #808080; font-size:10px;"><div class="foo">RESULTADO</div></th>-->
            </tr>
            <?php 
            $contaAlunos = 1; 
            $contaAprovados = 0;
            $contaReprovados = 0;
            $contaTransferidos = 0;
            $contaDesistentes = 0;
            $contaFalecidos = 0;      
            $contaOutros = 0;
            $break = 0;
            ?>
            <?php do { ?>
              <?php
              //$break++;

       //mysql_select_db($database_SmecelNovo, $SmecelNovo);
              $query_disciplinasMatriz = "
              SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_id_disciplina, matriz_disciplina_reprova, disciplina_id, disciplina_nome 
              FROM smc_matriz_disciplinas
              INNER JOIN smc_disciplina ON disciplina_id = matriz_disciplina_id_disciplina 
              WHERE matriz_disciplina_id_matriz = '$row_AlunoBoletim[turma_matriz_id]'";
              $disciplinasMatriz = mysql_query($query_disciplinasMatriz, $SmecelNovo) or die(mysql_error());
              $row_disciplinasMatriz = mysql_fetch_assoc($disciplinasMatriz);
              $totalRows_disciplinasMatriz = mysql_num_rows($disciplinasMatriz);

              ?>
              <tr>
                <td align="center" width="25px"><?php 
                echo $contaAlunos;
                $contaAlunos++;   
              ?></td>


              <td width="25%" align="left" ><?php echo $row_AlunoBoletim['aluno_nome']; ?>
              <?php if ($row_AlunoBoletim['vinculo_aluno_conselho']=="S") { ?>
                *
              <?php } ?>
              <?php if($row_AlunoBoletim['vinculo_aluno_situacao_nome'] != "MATRICULADO"){ ?>
                <span style="font-size: 10px;color:#ff6961 ">(<?php echo $row_AlunoBoletim['vinculo_aluno_situacao_nome']?>)</span>
              <?php } ?>
              <?php if (($row_AlunoBoletim['turma_multisseriada']==1) && ($row_AlunoBoletim['vinculo_aluno_multietapa']==0)) { ?>
                <b class="ls-txt-right">* informe a etapa do aluno na turma multi</b>
              <?php } else { ?>
                <b style="float:right"><?php echo $row_AlunoBoletim['etapa_nome_abrev']; ?>&nbsp;</b>
                <?php } ?></td>
                <?php for ($i = 1; $i <= $row_CriteriosAvaliativos['ca_qtd_periodos']; $i++) : ?>
                  <td style="background-color: #90ee90"></td>
                  <?php foreach ($disciplinas as $disciplina) : ?>
                    <?php 
                    $totalTrimestre = 0;
                    $totalTrimestreSemRecuperacao = 0;
                    $pontuacaoTotalAnoLetivo1 = 0;
                    $pontuacaoTotalAnoLetivo2 = 0;
                    $pontuacaoTotalAnoLetivo3 = 0;


    // Consultas ao banco de dados para obter as notas qualitativas, quantitativas, paralela e de recuperação
                    $query_qualitativo = sprintf("SELECT qq_nota FROM smc_notas_qq WHERE qq_id_matricula = %s AND qq_id_componente = %s AND qq_id_periodo = %s AND qq_tipo_criterio='1'", 
                      GetSQLValueString($row_AlunoBoletim['vinculo_aluno_id'], "int"), 
                      GetSQLValueString($disciplina['disciplina_id'], "int"), 
                      GetSQLValueString($i, "int"));
                    $qualitativo = mysql_query($query_qualitativo, $SmecelNovo) or die(mysql_error());
                    $somaPontuacaoQualitativo = 0;
                    while ($row_qualitativo = mysql_fetch_assoc($qualitativo)) {
                      $somaPontuacaoQualitativo += floatval($row_qualitativo['qq_nota']);
                    }

                    $query_quantitativo = sprintf("SELECT qq_nota FROM smc_notas_qq WHERE qq_id_matricula = %s AND qq_id_componente = %s AND qq_id_periodo = %s AND qq_tipo_criterio='2'", 
                      GetSQLValueString($row_AlunoBoletim['vinculo_aluno_id'], "int"), 
                      GetSQLValueString($disciplina['disciplina_id'], "int"), 
                      GetSQLValueString($i, "int"));
                    $quantitativo = mysql_query($query_quantitativo, $SmecelNovo) or die(mysql_error());
                    $somaPontuacaoQuantitativo = 0;
                    while ($row_quantitativo = mysql_fetch_assoc($quantitativo)) {
                      $somaPontuacaoQuantitativo += floatval($row_quantitativo['qq_nota']);
                    }

                    $query_paralela = sprintf("SELECT qq_nota FROM smc_notas_qq WHERE qq_id_matricula = %s AND qq_id_componente = %s AND qq_id_periodo = %s AND qq_tipo_criterio='3'", 
                      GetSQLValueString($row_AlunoBoletim['vinculo_aluno_id'], "int"), 
                      GetSQLValueString($disciplina['disciplina_id'], "int"), 
                      GetSQLValueString($i, "int"));
                    $paralela = mysql_query($query_paralela, $SmecelNovo) or die(mysql_error());
                    $notaParalela = 0;
                    if ($row_paralela = mysql_fetch_assoc($paralela)) {
                      $notaParalela = floatval($row_paralela['qq_nota']);
                    }

                    $query_recuperacao = sprintf("SELECT qq_nota FROM smc_notas_qq WHERE qq_id_matricula = %s AND qq_id_componente = %s AND qq_id_periodo = %s AND qq_tipo_criterio='4'", 
                      GetSQLValueString($row_AlunoBoletim['vinculo_aluno_id'], "int"), 
                      GetSQLValueString($disciplina['disciplina_id'], "int"), 
                      GetSQLValueString($i, "int"));
                    $recuperacao = mysql_query($query_recuperacao, $SmecelNovo) or die(mysql_error());
                    $notaRecuperacao = 0;
                    if ($row_recuperacao = mysql_fetch_assoc($recuperacao)) {
                      $notaRecuperacao = floatval($row_recuperacao['qq_nota']);
                    }

    // Ajustando valores de acordo com os períodos
                    switch ($i) {
                      case '1':
                      case '2':
                  $mediaTrimestre = 18; // Média necessária para os períodos 1 e 2
                  break;
                  case '3':
                  $mediaTrimestre = 24; // Média necessária para o período 3
                  break;
                }

    // Soma da nota qualitativa com a quantitativa, ou com a paralela se existir
                $totalTrimestreSemRecuperacao = $somaPontuacaoQualitativo + ($notaParalela > 0 && $notaParalela > $somaPontuacaoQuantitativo ? $notaParalela : $somaPontuacaoQuantitativo);

    // Definindo a nota total do trimestre
                $totalTrimestre = $notaRecuperacao > 0 ? $notaRecuperacao : $totalTrimestreSemRecuperacao;

                $totalTrimestre = arredondarNota($totalTrimestre);
                $totalTrimestreSemRecuperacao = arredondarNota($totalTrimestreSemRecuperacao);

                switch ($i) {
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
                  $alunos_aprovados[] = array('aluno_id' => $row_AlunoBoletim['vinculo_aluno_id'], 'periodo' => $i);
                } else if ($notaParalela > 0 && $notaParalela >= 8.1 && $totalTrimestreSemRecuperacao >= $mediaTrimestre) {
                  $classeNota = 'nota-paralela-apr';
                  $alunos_aprovados[] = array('aluno_id' => $row_AlunoBoletim['vinculo_aluno_id'], 'periodo' => $i);
                } else {
                  $alunos_reprovados_paralela[] = array('aluno_id' => $row_AlunoBoletim['vinculo_aluno_id'], 'periodo' => $i);
                }
                $cor = "";
                if($totalTrimestre < $mediaTrimestre){
                  $cor = "red"; 
                }
                ?>
                <td style="color:<?= $cor ?>">
                  <?= $totalTrimestre ?>
                </td>
              <?php endforeach; ?>

            <?php endfor; ?>

          </tr>

        <?php } while ($row_AlunoBoletim = mysql_fetch_assoc($AlunoBoletim)); ?>
      </table>







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

//mysql_free_result($disciplinasMatriz);
?>