<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php // include "fnc/anoLetivo.php"; ?>
<?php include('fnc/inverteData.php'); ?>
<?php include 'fnc/anti_injection.php'; ?>
<?php // include('fnc/notas.php'); ?>
<?php include('../funcoes/url_base.php'); ?>
<?php include 'fnc/calculos.php'; ?>
<?php include "fnc/session.php"; ?>
<?php
if (!function_exists('GetSQLValueString')) {
    function GetSQLValueString($theValue, $theType, $theDefinedValue = '', $theNotDefinedValue = '')
    {
        if (PHP_VERSION < 6) {
            $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
        }

        $theValue = function_exists('mysql_real_escape_string') ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

        switch ($theType) {
            case 'text':
                $theValue = ($theValue != '') ? "'" . $theValue . "'" : 'NULL';
                break;
            case 'long':
            case 'int':
                $theValue = ($theValue != '') ? intval($theValue) : 'NULL';
                break;
            case 'double':
                $theValue = ($theValue != '') ? doubleval($theValue) : 'NULL';
                break;
            case 'date':
                $theValue = ($theValue != '') ? "'" . $theValue . "'" : 'NULL';
                break;
            case 'defined':
                $theValue = ($theValue != '') ? $theDefinedValue : $theNotDefinedValue;
                break;
        }
        return $theValue;
    }
}

$codTurma = '';
$buscaTurma = '';
if (isset($_GET['ct'])) {
    if ($_GET['ct'] == '') {
        // echo "TURMA EM BRANCO";
        header('Location: turmasAlunosVinculados.php?nada');
        exit;
    }

    $codTurma = anti_injection($_GET['ct']);
    $codTurma = (int) $codTurma;
    $buscaTurma = "AND turma_id = $codTurma ";
}


include 'usuLogado.php';
include 'fnc/anoLetivo.php';

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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AlunoBoletim = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vincullo_aluno_conselho_parecer,
vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_conselho,
aluno_id, aluno_nome, aluno_nascimento, aluno_filiacao1, aluno_foto, aluno_hash,
turma_id, turma_nome, turma_matriz_id, turma_turno, turma_etapa 
FROM 
smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma 
WHERE vinculo_aluno_situacao = '1' AND vinculo_aluno_boletim = '1' AND vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND vinculo_aluno_id_escola = '$row_UsuLogado[usu_escola]' $buscaTurma
ORDER BY turma_turno ASC, turma_etapa ASC, turma_nome ASC, aluno_nome ASC";
$AlunoBoletim = mysql_query($query_AlunoBoletim, $SmecelNovo) or die(mysql_error());
$row_AlunoBoletim = mysql_fetch_assoc($AlunoBoletim);
$totalRows_AlunoBoletim = mysql_num_rows($AlunoBoletim);

if ($totalRows_AlunoBoletim == '') {
    // echo "TURMA EM BRANCO";
    // header("Location: turmasAlunosVinculados.php?nada");

    echo '<h3><center>Sem dados.<br><a href="javascript:window.close()">Fechar</a></center></h3>';
    echo '';

    exit;
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matriz = "SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash, matriz_dias_letivos, matriz_semanas_letivas, matriz_dias_semana, matriz_minutos_aula, matriz_aula_dia, matriz_criterio_avaliativo FROM smc_matriz WHERE matriz_id = '$row_AlunoBoletim[turma_matriz_id]'";
$Matriz = mysql_query($query_Matriz, $SmecelNovo) or die(mysql_error());
$row_Matriz = mysql_fetch_assoc($Matriz);
$totalRows_Matriz = mysql_num_rows($Matriz);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_criteriosAvaliativos = "
SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, 
ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, 
ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes, ca_grupo_conceito, ca_grupo_etario, ca_questionario_conceitos, ca_digitos   
FROM smc_criterios_avaliativos 
WHERE ca_id = '$row_Matriz[matriz_criterio_avaliativo]'";
$criteriosAvaliativos = mysql_query($query_criteriosAvaliativos, $SmecelNovo) or die(mysql_error());
$row_CriteriosAvaliativos = mysql_fetch_assoc($criteriosAvaliativos);
$totalRows_criteriosAvaliativos = mysql_num_rows($criteriosAvaliativos);
// echo $row_CriteriosAvaliativos['ca_questionario_conceitos'];
// exit;

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
// exit(var_dump($row_CriteriosAvaliativos))


function arredondarNota($nota)
{
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
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());

        gtag('config', 'UA-117872281-1');
    </script>
    <title>FICHA INDIVIDUAL | SMECEL - FICHA INDIVIDUAL POR TURMA</title>
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
        table.bordasimples {
            border-collapse: collapse;
            font-size: 7px;
        }

        table.bordasimples tr td {
            border: 1px solid #808080;
            padding: 2px;
            font-size: 12px;
        }

        table.bordasimples tr th {
            border: 1px solid #808080;
            padding: 2px;
            font-size: 9px;
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

<body onload="self.print()">
    <div class="container-fluid">
        <?php do { ?>

            <?php

            // mysql_select_db($database_SmecelNovo, $SmecelNovo);
            $query_disciplinasMatriz = "
         SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_reprova, matriz_disciplina_id_disciplina, matriz_disciplina_ch_ano, disciplina_id, disciplina_nome 
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
                        <?php if ($row_AlunoBoletim['aluno_foto'] == '') { ?>
                            <img src="<?php echo URL_BASE . 'aluno/fotos/' ?>semfoto.jpg" style="margin:1mm;width:15mm;">
                        <?php } else { ?>
                            <img src="<?php echo URL_BASE . 'aluno/fotos/' ?><?php echo $row_AlunoBoletim['aluno_foto']; ?>"
                                style="margin:1mm;width:15mm;">
                        <?php } ?>
                    </span> <span class="ls-float-left" style="margin-right:20px;"> <img
                            src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt=""
                            width="60px" /></span> <?php echo $row_EscolaLogada['escola_nome']; ?><br>
                    <small> Aluno(a): <strong><?php echo $row_AlunoBoletim['aluno_nome']; ?></strong><br>
                        Nascimento: <strong><?php echo inverteData($row_AlunoBoletim['aluno_nascimento']); ?></strong><br>
                        Filiação: <strong><?php echo $row_AlunoBoletim['aluno_filiacao1']; ?></strong><br>
                        Turma: <strong><?php echo $row_AlunoBoletim['turma_nome']; ?></strong> </small>
                </div>
                </p>
                <br><br><br><br>
                <h4 class="ls-txt-center">FICHA INDIVIDUAL DO ALUNO</h4><br>
                <?php
                if ($row_CriteriosAvaliativos['ca_questionario_conceitos'] == 'N') {
                    ?>
                    <table class="ls-sm-space bordasimples" width="100%">
                        <thead>
                            <!-- Cabeçalho com os trimestres -->
                            <tr height="30">
                                <th class="ls-txt-center" width="200"></th>
                                <th class="ls-txt-center" width="50"></th>
                                <?php for ($p = 1; $p <= $row_CriteriosAvaliativos['ca_qtd_periodos']; $p++) { ?>
                                    <th colspan="2" class="ls-txt-center" style="background-color:#F5F5F5;">
                                        <strong><?php echo $p; ?>ª TRIMESTRE</strong>
                                    </th>
                                <?php } ?>
                                <th colspan="2" class="ls-txt-center" style="background-color:#F5F5F5;">RESULTADO</th>
                            </tr>
                            <tr height="30">
                                <th class="ls-txt-center">COMPONENTES CURRICULARES</th>
                                <th class="ls-txt-center">CH</th>
                                <?php for ($p = 1; $p <= $row_CriteriosAvaliativos['ca_qtd_periodos']; $p++) { ?>
                                    <th class="ls-txt-center" width="30">TT.</th>
                                    <th class="ls-txt-center" width="30">REC.</th>
                                <?php } ?>
                                <th class="ls-txt-center" width="30">TP.</th>
                                <th class="ls-txt-center" width="30">RF</th>
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
                                    <td class="ls-txt-center"><?php echo $row_disciplinasMatriz['matriz_disciplina_ch_ano']; ?></td>
                                    <?php
                                    $totalTrimestre = 0;
                                    $totalTrimestreSemRecuperacao = 0;
                                    $pontuacaoTotalAnoLetivo1 = 0;
                                    $pontuacaoTotalAnoLetivo2 = 0;
                                    $pontuacaoTotalAnoLetivo3 = 0;

                                    for ($p = 1; $p <= $row_CriteriosAvaliativos['ca_qtd_periodos']; $p++) {
                                        // Consultas ao banco de dados para obter as notas qualitativas, quantitativas, paralela e de recuperação
                                        $query_qualitativo = sprintf(
                                            "SELECT qq_nota FROM smc_notas_qq WHERE qq_id_matricula = %s AND qq_id_componente = %s AND qq_id_periodo = %s AND qq_tipo_criterio='1'",
                                            GetSQLValueString($row_AlunoBoletim['vinculo_aluno_id'], "int"),
                                            GetSQLValueString($row_disciplinasMatriz['disciplina_id'], "int"),
                                            GetSQLValueString($p, "int")
                                        );
                                        $qualitativo = mysql_query($query_qualitativo, $SmecelNovo) or die(mysql_error());
                                        $somaPontuacaoQualitativo = 0;
                                        while ($row_qualitativo = mysql_fetch_assoc($qualitativo)) {
                                            $somaPontuacaoQualitativo += floatval($row_qualitativo['qq_nota']);
                                        }

                                        $query_quantitativo = sprintf(
                                            "SELECT qq_nota FROM smc_notas_qq WHERE qq_id_matricula = %s AND qq_id_componente = %s AND qq_id_periodo = %s AND qq_tipo_criterio='2'",
                                            GetSQLValueString($row_AlunoBoletim['vinculo_aluno_id'], "int"),
                                            GetSQLValueString($row_disciplinasMatriz['disciplina_id'], "int"),
                                            GetSQLValueString($p, "int")
                                        );
                                        $quantitativo = mysql_query($query_quantitativo, $SmecelNovo) or die(mysql_error());
                                        $somaPontuacaoQuantitativo = 0;
                                        while ($row_quantitativo = mysql_fetch_assoc($quantitativo)) {
                                            $somaPontuacaoQuantitativo += floatval($row_quantitativo['qq_nota']);
                                        }

                                        $query_paralela = sprintf(
                                            "SELECT qq_nota FROM smc_notas_qq WHERE qq_id_matricula = %s AND qq_id_componente = %s AND qq_id_periodo = %s AND qq_tipo_criterio='3'",
                                            GetSQLValueString($row_AlunoBoletim['vinculo_aluno_id'], "int"),
                                            GetSQLValueString($row_disciplinasMatriz['disciplina_id'], "int"),
                                            GetSQLValueString($p, "int")
                                        );
                                        $paralela = mysql_query($query_paralela, $SmecelNovo) or die(mysql_error());
                                        $notaParalela = 0;
                                        if ($row_paralela = mysql_fetch_assoc($paralela)) {
                                            $notaParalela = floatval($row_paralela['qq_nota']);
                                        }

                                        $query_recuperacao = sprintf(
                                            "SELECT qq_nota FROM smc_notas_qq WHERE qq_id_matricula = %s AND qq_id_componente = %s AND qq_id_periodo = %s AND qq_tipo_criterio='4'",
                                            GetSQLValueString($row_AlunoBoletim['vinculo_aluno_id'], "int"),
                                            GetSQLValueString($row_disciplinasMatriz['disciplina_id'], "int"),
                                            GetSQLValueString($p, "int")
                                        );
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
                                        $totalTrimestreSemRecuperacao = $somaPontuacaoQualitativo + ($notaParalela > 0 && $notaParalela > $somaPontuacaoQuantitativo ? $notaParalela : $somaPontuacaoQuantitativo);

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

                                        ?>
                                        <td class="<?php echo $classeNota; ?> ls-txt-center">
                                            <?php echo $totalTrimestreSemRecuperacao; ?>
                                        </td>
                                        <td class="ls-txt-center"><?php echo $notaRecuperacao; ?></td>
                                    <?php } ?>
                                    <?php
                                    $txtApr = "APR";
                                    $pontuacaoTotal = $pontuacaoTotalAnoLetivo1 + $pontuacaoTotalAnoLetivo2 + $pontuacaoTotalAnoLetivo3;
                                    if ($pontuacaoTotal < 60 && $row_AlunoBoletim['vinculo_aluno_conselho'] == "S") {
                                        $pontuacaoTotal = 60;
                                        $txtApr = "APR. CONSELHO";
                                    } ?>
                                    <td class="ls-txt-center">
                                        <span class="ls-text-md"><?= $pontuacaoTotal ?></span>
                                    </td>
                                    <td width="15" inputmode="numeric"
                                        disciplina="<?php echo $row_Disciplina['disciplina_nome']; ?>" class="ls-txt-center"><span
                                            class="ls-text-md">

                                            <?php

                                            if (($row_AnoLetivo['ano_letivo_resultado_final'] <= date("Y-m-d")) && $row_AnoLetivo['ano_letivo_resultado_final'] <> "") {

                                                if ($pontuacaoTotal < 60) {
                                                    echo "<span style='color: red;'>REP</span>";

                                                } else {
                                                    echo "<span style='color: green;'>$txtApr</span>";
                                                }
                                            } else {
                                                echo '-';
                                            }
                                            ?>

                                        </span></td>
                                </tr>
                            <?php } while ($row_disciplinasMatriz = mysql_fetch_assoc($disciplinasMatriz)); ?>
                        </tbody>
                    </table>
                    <?php
                } else if ($row_CriteriosAvaliativos['ca_questionario_conceitos'] == 'S') {
                    echo '<script type="text/javascript">';
                    echo 'window.location.href = "print_fichaIndividualTurmaConceito.php?ct=' . $_GET['ct'] . '";';
                    echo '</script>';
                }
                ?>

                <small class="ls-float-right">
                    <?php if (($row_AnoLetivo['ano_letivo_resultado_final'] <= date('Y-m-d')) && $row_AnoLetivo['ano_letivo_resultado_final'] <> '') {
                        echo '<strong>Resultado Final disponível.</strong> Verifique sua situação em cada Componente Curricular.';
                    } else if ($row_AnoLetivo['ano_letivo_resultado_final'] == '') {
                        echo 'A data de divulgação do Resultado Final (RF) ainda será definida pela escola.';
                    } else {
                        echo 'Resultado Final (RF) estará disponível à partir do dia ' . date('d/m/Y', strtotime(($row_AnoLetivo['ano_letivo_resultado_final'])));
                    } ?>
                </small>

                <br>
                <br><br><br><br><br>
                <h4 class="ls-txt-center">ANOTAÇÕES DA UNIDADE DE ENSINO</h4><br>

                <table class="ls-table1 ls-sm-space bordasimples" width="100%">
                    <tr>
                        <td><br><br>Resultado final: <br><br></td>
                        <td><br><br>Visto em: ______/_______/_____________<br><br></td>
                    </tr>

                    <tr>
                        <td colspan="2">Observações: <br><br><br><br></td>
                    </tr>
                    <?php if($row_matricula['vinculo_aluno_conselho_parecer'] != ''){ ?>
                          <tr>
                        	<td colspan="2">Parecer do conselho de classe: <br><br><?php echo $row_matricula['vinculo_aluno_conselho_parecer']; ?><br><br></td>
                        </tr>
                        <?php } ?>
                    <tr>
                        <td colspan="2"><br><br>Transferência entregue em: ______/_______/_____________<br><br></td>
                    </tr>
                    <tr>
                        <td colspan="2"><br><br>Assinatura do responsável:
                            ____________________________________________________________________<br><br></td>
                    </tr>

                </table>
                <small>Qualquer emenda ou rasura invalida este documento</small>


                <hr>
                <br><br><br><br><br><br><br>
                <p style="text-align:center">
                    <?php echo $row_EscolaLogada['sec_cidade']; ?>-<?php echo $row_EscolaLogada['sec_uf']; ?>,
                    <?php
                    setlocale(LC_TIME, 'pt_BR', 'utf-8', 'utf-8', 'portuguese');
                    date_default_timezone_set('America/Sao_Paulo');
                    echo strftime('%d de %B de %Y', strtotime('today'));
                    ?>
                    <br><br> SMECEL - Sistema de Gestão Escolar | www.smecel.com.br | <small>Código de certificação:
                        <strong><?php echo $row_AlunoBoletim['vinculo_aluno_verificacao']; ?></strong></small>
                </p>
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

mysql_free_result($Matriz);

mysql_free_result($disciplinasMatriz);
?>