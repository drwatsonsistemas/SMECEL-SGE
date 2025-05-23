<?php require_once('../../Connections/SmecelNovoPDO.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
<?php include('../../sistema/funcoes/inverteData.php'); ?>

<?php

$turma = isset($_GET['turma']) ? anti_injection($_GET['turma']) : "-1";
$planejamento = isset($_GET['plan']) ? $_GET['plan'] : "-1";
$escola = isset($_GET['escola']) ? anti_injection($_GET['escola']) : "-1";

$stmtEscolaLogada = $SmecelNovo->prepare(
    "SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, 
  escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_tema, sec_id, sec_cidade, sec_uf, 
  sec_termo_matricula, escola_assinatura 
  FROM smc_escola 
  INNER JOIN smc_sec ON sec_id = escola_id_sec 
  WHERE escola_id = :escola"
);
$stmtEscolaLogada->execute([':escola' => $escola]);
$row_EscolaLogada = $stmtEscolaLogada->fetch(PDO::FETCH_ASSOC);
$totalRows_EscolaLogada = $stmtEscolaLogada->rowCount();


// Consultar AC Edit
$stmt_plan_edit = $SmecelNovo->prepare(
    "SELECT p.*, 
            GROUP_CONCAT(DISTINCT pm.smc_id_metodologia) AS metodologias_marcadas,
            GROUP_CONCAT(DISTINCT pa.smc_id_avaliacao) AS avaliacoes_marcadas,
            GROUP_CONCAT(DISTINCT pt.smc_id_temas_integradores) AS temas_marcados,
            GROUP_CONCAT(DISTINCT sem.smc_id_estrategias_metodologicas) AS estrategias_metodologicas_marcadas,
            GROUP_CONCAT(DISTINCT pae.smc_id_avaliacao_edinf) AS avaliacoes_marcadas_edinf
     FROM smc_planejamento p
     LEFT JOIN smc_plan_metod_relacionamento pmr ON p.smc_id_planejamento = pmr.smc_id_planejamento
     LEFT JOIN smc_planejamento_metodologias pm ON pmr.smc_id_metodologia = pm.smc_id_metodologia
     LEFT JOIN smc_plan_ava_relacionamento par ON p.smc_id_planejamento = par.smc_id_planejamento
     LEFT JOIN smc_planejamento_avaliacoes pa ON par.smc_id_avaliacao = pa.smc_id_avaliacao
     LEFT JOIN smc_plan_temas_relacionamento ptr ON p.smc_id_planejamento = ptr.smc_id_planejamento
     LEFT JOIN smc_planejamento_temas_integradores pt ON ptr.smc_id_tema = pt.smc_id_temas_integradores
     LEFT JOIN smc_plan_estrate_relacionamentos per ON p.smc_id_planejamento = per.smc_id_planejamento
     LEFT JOIN smc_planejamento_estrategias_metodologicas sem ON per.smc_id_estrate_metod = sem.smc_id_estrategias_metodologicas
     LEFT JOIN smc_plan_avaliacaoedinf_relacionamento pae_rel ON p.smc_id_planejamento = pae_rel.smc_id_planejamento
     LEFT JOIN smc_planejamento_avaliacaoedinf pae ON pae_rel.smc_id_avaliacaoedinf = pae.smc_id_avaliacao_edinf
     WHERE p.smc_id_planejamento = :plan_id
     GROUP BY p.smc_id_planejamento"
);

$stmt_plan_edit->bindParam(':plan_id', $planejamento, PDO::PARAM_INT);
$stmt_plan_edit->execute();
$row_plan_edit = $stmt_plan_edit->fetch(PDO::FETCH_ASSOC);

 // Converter metodologias e avaliações marcadas em arrays para facilitar a comparação no HTML
 $metodologias_marcadas = explode(',', $row_plan_edit['metodologias_marcadas']);
 $avaliacoes_marcadas = explode(',', $row_plan_edit['avaliacoes_marcadas']);
 $avaliacoes_marcadas_edinf = explode(',', $row_plan_edit['avaliacoes_marcadas_edinf']);

$stmtDisciplinasAC = $SmecelNovo->prepare("SELECT DISTINCT smc_id_planejamento, smc_id_componente, disciplina_nome, disciplina_id 
    FROM smc_planejamento_componente
    INNER JOIN smc_disciplina ON disciplina_id = smc_id_componente
    WHERE smc_id_planejamento = :plan_id");
$stmtDisciplinasAC->bindValue(':plan_id', $_GET['plan'], PDO::PARAM_INT);
$stmtDisciplinasAC->execute();
$disciplinasAC = $stmtDisciplinasAC->fetchAll(PDO::FETCH_ASSOC);
$totalRowsDisciplinasAC = $stmtDisciplinasAC->rowCount();


// Consulta para obter informações da turma
$stmt_Turma = $SmecelNovo->prepare(
    "SELECT * FROM smc_turma WHERE turma_id = :turma"
);
$stmt_Turma->bindParam(':turma', $turma, PDO::PARAM_INT);
$stmt_Turma->execute();
$row_Turma = $stmt_Turma->fetch(PDO::FETCH_ASSOC);
$etapa = $row_Turma['turma_etapa'];

$stmt_periodos = $SmecelNovo->prepare(
    "SELECT * FROM smc_unidades WHERE per_unid_id = :id_unidade"
);
$stmt_periodos->execute([
    ':id_unidade' => $row_plan_edit['smc_id_periodo']
]);
$result_periodos = $stmt_periodos->fetch(PDO::FETCH_ASSOC);

// Consulta para obter informações da etapa
$stmt_Etapa = $SmecelNovo->prepare(
    "SELECT etapa_id, etapa_id_filtro, etapa_nome, etapa_idade, etapa_limite_turma, etapa_nome_abrev, etapa_ano_ef 
        FROM smc_etapa WHERE etapa_id = :etapa_id"
);
$stmt_Etapa->bindParam(':etapa_id', $etapa, PDO::PARAM_INT);
$stmt_Etapa->execute();
$row_Etapa = $stmt_Etapa->fetch(PDO::FETCH_ASSOC);

$queryMetodologias = "SELECT smc_id_metodologia, smc_metodologia FROM smc_planejamento_metodologias";
$stmtMetodologias = $SmecelNovo->query($queryMetodologias);
$metodologias = $stmtMetodologias->fetchAll(PDO::FETCH_ASSOC);

$queryAvaliacoes = "SELECT smc_id_avaliacao, smc_tipo, smc_descricao FROM smc_planejamento_avaliacoes";
$stmtAvaliacoes = $SmecelNovo->query($queryAvaliacoes);
$avaliacoes = $stmtAvaliacoes->fetchAll(PDO::FETCH_ASSOC);

$queryTemasIntegradores = "SELECT smc_id_temas_integradores, smc_tema_integrador FROM smc_planejamento_temas_integradores";
$stmtTemasIntegradores = $SmecelNovo->query($queryTemasIntegradores);
$temasIntegradores = $stmtTemasIntegradores->fetchAll(PDO::FETCH_ASSOC);

$queryEstrategiasMetodologicas = "SELECT smc_id_estrategias_metodologicas, smc_estrategias_metodologicas FROM smc_planejamento_estrategias_metodologicas";
$stmtEstrategiasMetodologicas = $SmecelNovo->query($queryEstrategiasMetodologicas);
$EstrategiasMetodologicas = $stmtEstrategiasMetodologicas->fetchAll(PDO::FETCH_ASSOC);

$queryAvaliacaoEdinf = "SELECT smc_id_avaliacao_edinf, smc_avaliacao_edinf FROM smc_planejamento_avaliacaoedinf";
$stmtAvaliacaoEdinf = $SmecelNovo->query($queryAvaliacaoEdinf);
$AvaliacaoEdinf = $stmtAvaliacaoEdinf->fetchAll(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html class="<?php echo TEMA; ?>" lang="pt-br">

<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());

        gtag('config', 'UA-117872281-1');
    </script>
    <title>PLANEJAMENTO | <?php echo $row_Etapa['etapa_nome']; ?> | SMECEL - Sistema de Gestão Escolar</title>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
    <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
    <link rel="stylesheet" type="text/css" href="css/locastyle.css">
    <link rel="stylesheet" href="css/sweetalert2.min.css">

    <style>
        body {
            font-size: 12px;
            background-image: url(<?php if ($row_EscolaLogada['escola_logo'] <> "") { ?>../../img/marcadagua/<?php echo $row_EscolaLogada['escola_logo']; ?><?php } else { ?>../../img/marcadagua/brasao_republica.png<?php } ?>);
            background-repeat: no-repeat;
            background-position: center center;
            z-index: -999;
        }

        p {
            margin-bottom: 1px;
        }

        page {
            display: block;
            margin: 0 auto;
            margin-bottom: 0.5cm;
        }

        page[size="A4"] {
            width: 21cm;
            height: 29.7cm;
            padding: 5px;

        }

        page[size="A4"][layout="portrait"] {
            width: 29.7cm;
            height: 21cm;
        }

        @media print {

            body,
            page {
                margin: 0;
                box-shadow: 0;
            }

            #div_impressao {
                display: none;
            }
        }

        table.bordasimples {
            border-collapse: collapse;
            font-size: 10px;
        }

        table.bordasimples tr td {
            border: 1px dotted #000000;
            padding: 2px;
            font-size: 14px;
            vertical-align: top;
            height: 30px;
        }

        table.bordasimples tr th {
            border: 1px dotted #000000;
            padding: 2px;
            font-size: 14px;
            vertical-align: top;
            height: 30px;
        }
    </style>
</head>

<body onload="self.print()">
    <page size="A4">


        <div class="ls-txt-center1">

            <table>

                <tr>
                    <td width="150px" class="ls-txt-center">
                        <span><?php if ($row_EscolaLogada['escola_logo'] <> "") { ?><img
                                    src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt=""
                                    width="100px" /><?php } else { ?><img src="../../img/brasao_republica.png" alt=""
                                    width="80px" /><?php } ?></span>
                    </td>

                    <td width="350px">
                        <h2><strong><?php echo $row_EscolaLogada['escola_nome']; ?></strong></h2>
                        <small>
                            <?php echo $row_EscolaLogada['escola_endereco']; ?>,
                            <?php echo $row_EscolaLogada['escola_num']; ?> -
                            <?php echo $row_EscolaLogada['escola_bairro']; ?> -
                            <?php echo $row_EscolaLogada['escola_cep']; ?><br>
                            CNPJ:<?php echo $row_EscolaLogada['escola_cnpj']; ?>
                            INEP:<?php echo $row_EscolaLogada['escola_inep']; ?><br>
                            <?php echo $row_EscolaLogada['escola_telefone1']; ?>
                            <?php echo $row_EscolaLogada['escola_telefone2']; ?>
                            <?php echo $row_EscolaLogada['escola_email']; ?>
                        </small>
                    </td>

                    <td class="ls-txt-right" width="270px">

                        <h2 class="ls-txt-right">PLANO DE AULA</h2>

                    </td>
                </tr>

            </table>

        </div>
        <br>
        <table class="ls-sm-space bordasimples" width="100%" style="font-size:10px;">
            <tr>
                <td><small><strong>Professor(a):</strong></small><br><?php echo $row_ProfLogado['func_nome'] ?>&nbsp;
                </td>
                <td colspan="2">
                    <small><strong>Ano/Série:</strong></small><br><?php echo $row_Etapa['etapa_nome'] ?>&nbsp;
                </td>
            </tr>
            <tr>
                <td><small><strong>Componentes curriculares:</strong></small><br>
                    <?php if (!empty($disciplinasAC)) {
                        foreach ($disciplinasAC as $disciplinaAC) { ?>

                            <span class="">
                                <?php echo htmlspecialchars($disciplinaAC['disciplina_nome'], ENT_QUOTES, 'UTF-8'); ?>&nbsp;|
                                &nbsp;

                            </span>
                        <?php }
                    } ?>
                </td>
                <td>
                    <small><strong>Período:</strong></small><br>
                    <?php
                    // Converte as datas para o formato desejado
                    $data_inicial_formatada = date('d/m/Y', strtotime($row_plan_edit['smc_planejamento_data_inicial']));
                    $data_final_formatada = date('d/m/Y', strtotime($row_plan_edit['smc_planejamento_data_final']));

                    // Exibe as datas formatadas
                    echo $data_inicial_formatada . " à " . $data_final_formatada;
                    ?>
                </td>
                <td style="padding-top:10px">
                    <?php echo $result_periodos['per_unid_periodo'] ?>º PERÍODO/UNIDADE
                </td>


            </tr>


        </table>
        <table class="ls-sm-space bordasimples" width="100%" style="font-size:10px;">
            <tr>
                <td><small><strong>Turma:</strong></small><br><?php echo $row_Turma['turma_nome'] ?></td>
            </tr>

        </table>
        <br><br>

        <table class="ls-sm-space bordasimples" width="100%" style="font-size:10px;">
            <tr>
                <td><small><strong>TEMAS INTEGRADORES:</strong></small><br>
                    <?php
                    if (!empty($temasIntegradores)) {
                        $temasSelecionados = explode(',', $row_plan_edit['temas_marcados']);
                        $temasExibidos = [];

                        foreach ($temasIntegradores as $tema) {
                            if (in_array($tema['smc_id_temas_integradores'], $temasSelecionados)) {
                                $temasExibidos[] = mb_convert_encoding($tema['smc_tema_integrador'], 'UTF-8', 'ISO-8859-1');
                            }
                        }

                        // Exibir os temas selecionados formatados corretamente
                        if (!empty($temasExibidos)) {
                            echo implode(" | ", $temasExibidos);
                        } else {
                            echo "Nenhum tema integrador selecionado.";
                        }
                    } else {
                        echo "Nenhum tema integrador encontrado.";
                    }
                    ?>
                    <?php if($row_plan_edit['smc_atv_dev_tema_integrador'] <> '') { ?>
                        <br><br><small><strong>ATIVIDADE DESENVOLVIDA PARA O TEMA INTEGRADOR:</strong></small><br>
                        <?php echo $row_plan_edit['smc_atv_dev_tema_integrador']; ?>
                    <?php } ?>
                </td>
            </tr>
        </table>


        <?php if ($row_Etapa['etapa_id_filtro'] == "1") { ?>
            <table class="ls-sm-space bordasimples" width="100%" style="font-size:10px;">

                <tr>
                    <td><small><strong>DIREITOS DE APRENDIZAGEM:</strong></small><br>
                        <?php
                        $direitosAprendizagem = [];

                        if ($row_plan_edit['ac_da_conviver'] === "S") {
                            $direitosAprendizagem[] = "CONVIVER";
                        }
                        if ($row_plan_edit['ac_da_brincar'] === "S") {
                            $direitosAprendizagem[] = "BRINCAR";
                        }
                        if ($row_plan_edit['ac_da_participar'] === "S") {
                            $direitosAprendizagem[] = "PARTICIPAR";
                        }
                        if ($row_plan_edit['ac_da_explorar'] === "S") {
                            $direitosAprendizagem[] = "EXPLORAR";
                        }
                        if ($row_plan_edit['ac_da_expressar'] === "S") {
                            $direitosAprendizagem[] = "EXPRESSAR";
                        }
                        if ($row_plan_edit['ac_da_conhecerse'] === "S") {
                            $direitosAprendizagem[] = "CONHECER-SE";
                        }

                        // Exibir os direitos marcados
                        if (!empty($direitosAprendizagem)) {
                            echo implode(" | ", $direitosAprendizagem);
                        } else {
                            echo "Nenhum direito de aprendizagem marcado.";
                        }
                        ?>
                    </td>
                </tr>
            </table>
            <br><br>

            <?php
            // Função para exibir apenas os campos preenchidos
            function exibirCampo($titulo, $conteudo)
            {
                if (!empty($conteudo)) {
                    echo '<table class="ls-sm-space bordasimples" width="100%" style="font-size:10px;">';
                    echo '<tr>';
                    echo '<td><small><strong>' . $titulo . ':</strong></small><br>' . $conteudo . '</td>';
                    echo '</tr>';
                    echo '</table><br>';
                }
            }

            // Exibir os campos preenchidos
            exibirCampo('Atividades permanentes', $row_plan_edit['smc_atividades_permanentes']);
            exibirCampo('Objetivos de aprendizagem e desenvolvimento', $row_plan_edit['obj_aprendizagem_desenvolvimento']);
            exibirCampo('Aprendizagens vivenciadas pelas crianças (Saberes)', $row_plan_edit['aprendizagens_saberes']);
            exibirCampo('Metodologia', $row_plan_edit['metodologia']);
            exibirCampo('Recursos', $row_plan_edit['recursos']);
            exibirCampo('Avaliação', $row_plan_edit['avaliacao']);
            ?>

            <table class="ls-sm-space bordasimples" width="100%" style="font-size:10px;">
                <tr>
                    <td><small><strong>ESTRATÉGIAS METODOLÓGICAS:</strong></small><br>
                        <?php
                        if (!empty($EstrategiasMetodologicas)) {
                            $EstrategiasSelecionadas = explode(',', $row_plan_edit['estrategias_metodologicas_marcadas']);
                            $EstrategiasExibidas = [];

                            foreach ($EstrategiasMetodologicas as $estrategias) {
                                if (in_array($estrategias['smc_id_estrategias_metodologicas'], $EstrategiasSelecionadas)) {
                                    $EstrategiasExibidas[] = mb_convert_encoding($estrategias['smc_estrategias_metodologicas'], 'UTF-8', 'ISO-8859-1');
                                }
                            }

                            // Exibir os temas selecionados formatados corretamente
                            if (!empty($EstrategiasExibidas)) {
                                echo implode(" | ", $EstrategiasExibidas);
                            } else {
                                echo "Nenhuma estratégia metodológica selecionada.";
                            }
                            
                            // Exibir estratégia metodológica personalizada, se existir
                            if (!empty($row_plan_edit['estrategia_metodologica_personalizada'])) {
                                echo "<br><strong>Outra:</strong> " . $row_plan_edit['estrategia_metodologica_personalizada'];
                            }
                        } else {
                            echo "Nenhuma estratégia metodológica encontrada.";
                        }
                        ?>
                    </td>
                </tr>
            </table>

            <table class="ls-sm-space bordasimples" width="100%" style="font-size:10px;">
                <tr>
                    <td><small><strong>AVALIAÇÕES:</strong></small><br>
                        <?php
                        if (!empty($AvaliacaoEdinf)) {
                            $AvaliacoesInfSelecionadas = explode(',', $row_plan_edit['avaliacoes_marcadas_edinf']);
                            $AvaliacoesInfExibidas = [];

                            foreach ($AvaliacaoEdinf as $avainf) {
                                if (in_array($avainf['smc_id_avaliacao_edinf'], $AvaliacoesInfSelecionadas)) {
                                    $AvaliacoesInfExibidas[] = mb_convert_encoding($avainf['smc_avaliacao_edinf'], 'UTF-8', 'ISO-8859-1');
                                }
                            }

                            // Exibir os temas selecionados formatados corretamente
                            if (!empty($AvaliacoesInfExibidas)) {
                                echo implode(" | ", $AvaliacoesInfExibidas);
                            } else {
                                echo "Nenhuma avaliação selecionada.";
                            }
                        } else {
                            echo "Nenhuma avaliação encontrada.";
                        }
                        ?>
                    </td>
                </tr>
            </table>
        <?php } else { ?>

            <table class="ls-sm-space bordasimples" width="100%" style="font-size:10px;">
                <?php
                // Função para exibir campos preenchidos
                function exibirCampo($titulo, $conteudo)
                {
                    if (!empty($conteudo)) {
                        echo '<tr>';
                        echo '<td><small><strong>' . $titulo . ':</strong></small><br>' . nl2br($conteudo) . '</td>';
                        echo '</tr>';
                    }
                }

                // Exibir os campos preenchidos
                exibirCampo('OBJETOS DE CONHECIMENTO/CONTEÚDO(S)', $row_plan_edit['smc_obj_conhecimento_conteudos']);
                exibirCampo('HABILIDADES', $row_plan_edit['smc_habilidades']);
                ?>

                <tr>
                    <td><small><strong>METODOLOGIA:</strong></small><br>
                        <?php
                        if (!empty($metodologias)) {
                            $metodologiasSelecionadas = explode(',', $row_plan_edit['metodologias_marcadas']);
                            foreach ($metodologias as $metodologia) {
                                $nome_metodologia = mb_convert_encoding($metodologia['smc_metodologia'], 'UTF-8', 'ISO-8859-1');
                                if (in_array($metodologia['smc_id_metodologia'], $metodologiasSelecionadas)) {
                                    echo $nome_metodologia . '<br>';
                                }
                            }
                            if (!empty($row_plan_edit['smc_metodologia_personalilzada'])) {
                                echo '<strong>Outros:</strong> ' . $row_plan_edit['smc_metodologia_personalilzada'];
                            }
                        } else {
                            echo "Nenhuma metodologia encontrada.";
                        }
                        ?>
                    </td>
                </tr>

                <tr>
                    <td><small><strong>AVALIAÇÃO:</strong></small><br>
                        <?php
                        if (!empty($avaliacoes)) {
                            $avaliacoesSelecionadas = explode(',', $row_plan_edit['avaliacoes_marcadas']);
                            $tipos = ['Processual', 'Somativa', 'Formativa'];
                            foreach ($tipos as $tipo) {
                                echo "<strong>$tipo</strong><br>";
                                foreach ($avaliacoes as $avaliacao) {

                                    if ($avaliacao['smc_tipo'] === $tipo && in_array($avaliacao['smc_id_avaliacao'], $avaliacoesSelecionadas)) {
                                        $descricao = mb_convert_encoding($avaliacao['smc_descricao'], 'UTF-8', 'ISO-8859-1');
                                        echo $descricao . '<br>';
                                    }
                                }
                                echo '<br>';
                            }
                            if (!empty($row_plan_edit['smc_avaliacao_personalizada'])) {
                                echo '<strong>Outra:</strong> ' . $row_plan_edit['smc_avaliacao_personalizada'];
                            }
                        } else {
                            echo "Nenhuma avaliação encontrada.";
                        }
                        ?>
                    </td>
                </tr>
            </table>

        <?php } ?>


        <table class="ls-sm-space bordasimples" width="100%">
            <tr>
                <td class="ls-v-align-middle">
                    <br><br><br>
                    <p style="text-align:center">
                        COORDENAÇÃO PEDAGÓGICA: VISTO
                        EM.................................................................................
                    </p>
                </td>
            </tr>
        </table>
        <br><br>
        <p style="text-align:center">
            <?php echo $row_EscolaLogada['sec_cidade']; ?>-<?php echo $row_EscolaLogada['sec_uf']; ?>,
            <?php
            setlocale(LC_TIME, 'pt_BR', 'utf-8', 'utf-8', 'portuguese');
            date_default_timezone_set('America/Sao_Paulo');
            echo utf8_encode(strftime('%d de %B de %Y', strtotime('today')));
            ?>
        </p>
    </page>
</body>

</html>