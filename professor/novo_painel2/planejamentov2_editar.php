<?php require_once('../../Connections/SmecelNovoPDO.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>

<?php
$turma = isset($_GET['turma']) ? anti_injection($_GET['turma']) : "-1";
$planejamento = isset($_GET['plan']) ? $_GET['plan'] : "-1";
$escola = isset($_GET['escola']) ? anti_injection($_GET['escola']) : "-1";

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
    $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}


try {

    $stmt_plan_edit = $SmecelNovo->prepare(
        "SELECT p.*, 
                GROUP_CONCAT(DISTINCT pm.smc_id_metodologia) AS metodologias_marcadas,
                GROUP_CONCAT(DISTINCT pa.smc_id_avaliacao) AS avaliacoes_marcadas,
                GROUP_CONCAT(DISTINCT pt.smc_id_temas_integradores) AS temas_marcados,
                GROUP_CONCAT(DISTINCT per.smc_id_estrate_metod) AS estrategias_marcadas,
                GROUP_CONCAT(DISTINCT pae.smc_id_avaliacao_edinf) AS avaliacoes_marcadas_edinf
         FROM smc_planejamento p
         LEFT JOIN smc_plan_metod_relacionamento pmr ON p.smc_id_planejamento = pmr.smc_id_planejamento
         LEFT JOIN smc_planejamento_metodologias pm ON pmr.smc_id_metodologia = pm.smc_id_metodologia
         LEFT JOIN smc_plan_ava_relacionamento par ON p.smc_id_planejamento = par.smc_id_planejamento
         LEFT JOIN smc_planejamento_avaliacoes pa ON par.smc_id_avaliacao = pa.smc_id_avaliacao
         LEFT JOIN smc_plan_temas_relacionamento ptr ON p.smc_id_planejamento = ptr.smc_id_planejamento
         LEFT JOIN smc_planejamento_temas_integradores pt ON ptr.smc_id_tema = pt.smc_id_temas_integradores
         LEFT JOIN smc_plan_estrate_relacionamentos per ON p.smc_id_planejamento = per.smc_id_planejamento
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

    // Consulta para obter informações da turma
    $stmt_Turma = $SmecelNovo->prepare(
        "SELECT * FROM smc_turma WHERE turma_id = :turma"
    );
    $stmt_Turma->bindParam(':turma', $turma, PDO::PARAM_INT);
    $stmt_Turma->execute();
    $row_Turma = $stmt_Turma->fetch(PDO::FETCH_ASSOC);
    $etapa = $row_Turma['turma_etapa'];

    // Consulta para obter informações da etapa
    $stmt_Etapa = $SmecelNovo->prepare(
        "SELECT etapa_id, etapa_id_filtro, etapa_nome, etapa_idade, etapa_limite_turma, etapa_nome_abrev, etapa_ano_ef 
        FROM smc_etapa WHERE etapa_id = :etapa_id"
    );
    $stmt_Etapa->bindParam(':etapa_id', $etapa, PDO::PARAM_INT);
    $stmt_Etapa->execute();
    $row_Etapa = $stmt_Etapa->fetch(PDO::FETCH_ASSOC);

    // Consulta para listar os períodos
    $stmt_periodos = $SmecelNovo->prepare(
        "SELECT * FROM smc_unidades WHERE per_unid_id_ano = :ano_letivo_id AND per_unid_id_sec = :sec_id ORDER BY per_unid_periodo ASC"
    );
    $stmt_periodos->execute([
        ':ano_letivo_id' => $row_AnoLetivo['ano_letivo_id'],
        ':sec_id' => $row_Secretaria['sec_id']
    ]);
    $result_periodos = $stmt_periodos->fetchAll(PDO::FETCH_ASSOC);


    // Preparar consulta com a cláusula condicional
    $stmtDisciplinasAC = $SmecelNovo->prepare("SELECT smc_id_planejamento, smc_id_componente AS ac_componente_id, disciplina_nome, disciplina_id, disciplina_bncc 
    FROM smc_planejamento_componente
    INNER JOIN smc_disciplina ON disciplina_id = smc_id_componente
    WHERE smc_id_planejamento = :plan_id");
    $stmtDisciplinasAC->bindValue(':plan_id', $_GET['plan'], PDO::PARAM_INT);
    $stmtDisciplinasAC->execute(); // Executa a query
    $disciplinasAC = $stmtDisciplinasAC->fetchAll(PDO::FETCH_ASSOC);
    $totalRowsDisciplinasAC = $stmtDisciplinasAC->rowCount();



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

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['MM_update']) && $_POST['MM_update'] === 'form1') {
        $stmtUpdate = $SmecelNovo->prepare(
            "UPDATE smc_planejamento SET 
                smc_id_periodo = :periodo,
                smc_planejamento_data_inicial = :data_inicial, 
                smc_planejamento_data_final = :data_final, 
                obj_aprendizagem_desenvolvimento = :obj_aprendizagem, 
                aprendizagens_saberes = :aprendizagens_saberes, 
                metodologia = :metodologia, 
                recursos = :recursos, 
                avaliacao = :avaliacao, 
                ac_da_conviver = :da_conviver, 
                ac_da_brincar = :da_brincar, 
                ac_da_participar = :da_participar, 
                ac_da_explorar = :da_explorar, 
                ac_da_expressar = :da_expressar, 
                ac_da_conhecerse = :da_conhecerse, 
                smc_metodologia_personalilzada = :metodologia_personalizada,
                smc_obj_conhecimento_conteudos = :obj_conhecimento_conteudos,
                smc_habilidades = :habilidades,
                smc_atividades_permanentes = :atv_permanentes,
                smc_atv_dev_tema_integrador = :atv_dev_tema_integrador
            WHERE smc_id_planejamento = :plan_id"
        );

        $stmtUpdate->execute([
            ':periodo' => $_POST['ac_periodo'],
            ':data_inicial' => $_POST['ac_data_inicial'],
            ':data_final' => $_POST['ac_data_final'],
            ':obj_aprendizagem' => $_POST['obj_aprendizagem'],
            ':aprendizagens_saberes' => $_POST['aprendizagens_saberes'],
            ':metodologia' => $_POST['metodologia'],
            ':recursos' => $_POST['recursos'],
            ':avaliacao' => $_POST['avaliacao'],
            ':da_conviver' => isset($_POST['plan_da_conviver']) ? 'S' : 'N',
            ':da_brincar' => isset($_POST['plan_da_brincar']) ? 'S' : 'N',
            ':da_participar' => isset($_POST['plan_da_participar']) ? 'S' : 'N',
            ':da_explorar' => isset($_POST['plan_da_explorar']) ? 'S' : 'N',
            ':da_expressar' => isset($_POST['plan_da_expressar']) ? 'S' : 'N',
            ':da_conhecerse' => isset($_POST['plan_da_conhecerse']) ? 'S' : 'N',
            ':metodologia_personalizada' => $_POST['metodologia_personalilzada'],
            ':obj_conhecimento_conteudos' => $_POST['obj_conhecimento_conteudos'],
            ':habilidades' => $_POST['habilidades'],
            ':atv_permanentes' => $_POST['atv_permanentes'],
            ':atv_dev_tema_integrador' => $_POST['smc_atv_dev_tema_integrador'],
            ':plan_id' => $_POST['plan_id']
        ]);

        // Inserção dos temas integradores selecionados
        if (isset($_POST['temas_integradores']) && is_array($_POST['temas_integradores'])) {
            // Excluir temas existentes para este planejamento antes de inserir os novos
            $stmtDeleteTemas = $SmecelNovo->prepare("DELETE FROM smc_plan_temas_relacionamento WHERE smc_id_planejamento = :plan_id");
            $stmtDeleteTemas->execute([':plan_id' => $_POST['plan_id']]);

            // Inserir os novos temas selecionados
            $stmtInsertTema = $SmecelNovo->prepare("INSERT INTO smc_plan_temas_relacionamento (smc_id_planejamento, smc_id_tema) VALUES (:plan_id, :tema_id)");
            foreach ($_POST['temas_integradores'] as $tema_id) {
                $stmtInsertTema->execute([
                    ':plan_id' => $_POST['plan_id'],
                    ':tema_id' => $tema_id
                ]);
            }
        }

        // Inserção das metodologias selecionadas
        if (isset($_POST['metodologias']) && is_array($_POST['metodologias'])) {
            $stmtDeleteMetodologias = $SmecelNovo->prepare("DELETE FROM smc_plan_metod_relacionamento WHERE smc_id_planejamento = :plan_id");
            $stmtDeleteMetodologias->execute([':plan_id' => $_POST['plan_id']]);

            $stmtInsertMetodologia = $SmecelNovo->prepare("INSERT INTO smc_plan_metod_relacionamento (smc_id_planejamento, smc_id_metodologia) VALUES (:plan_id, :metodologia_id)");
            foreach ($_POST['metodologias'] as $metodologia_id) {
                $stmtInsertMetodologia->execute([
                    ':plan_id' => $_POST['plan_id'],
                    ':metodologia_id' => $metodologia_id
                ]);
            }
        }

        if (isset($_POST['avaliacoesedinf']) && is_array($_POST['avaliacoesedinf'])) {
            // Remover avaliações existentes antes de inserir as novas
            $stmtDeleteAvaliacoes = $SmecelNovo->prepare("DELETE FROM smc_plan_avaliacaoedinf_relacionamento WHERE smc_id_planejamento = :plan_id");
            $stmtDeleteAvaliacoes->execute([':plan_id' => $_POST['plan_id']]);

            // Inserir novas avaliações selecionadas
            $stmtInsertAvaliacao = $SmecelNovo->prepare("INSERT INTO smc_plan_avaliacaoedinf_relacionamento (smc_id_planejamento, smc_id_avaliacaoedinf) VALUES (:plan_id, :avaliacao_id)");
            foreach ($_POST['avaliacoesedinf'] as $avaliacao_id) {
                $stmtInsertAvaliacao->execute([
                    ':plan_id' => $_POST['plan_id'],
                    ':avaliacao_id' => $avaliacao_id
                ]);
            }

            if (!empty($_POST['avaliacao_edinf_outras'])) {
                $stmtUpdateOutraEstrategia = $SmecelNovo->prepare("
                    UPDATE smc_planejamento 
                    SET avaliacao_edinf_outras = :avaliacao_edinf_outras
                    WHERE smc_id_planejamento = :plan_id
                ");
                $stmtUpdateOutraEstrategia->execute([
                    ':plan_id' => $_POST['plan_id'],
                    ':avaliacao_edinf_outras' => $_POST['avaliacao_edinf_outras']
                ]);

            }
        }


        // Inserção das estratégias metodológicas selecionadas
        if (isset($_POST['estrategias_metodologicas']) && is_array($_POST['estrategias_metodologicas'])) {
            try {
                // Remove as estratégias metodológicas anteriores para evitar duplicatas
                $stmtDeleteEstrategias = $SmecelNovo->prepare("DELETE FROM smc_plan_estrate_relacionamentos WHERE smc_id_planejamento = :plan_id");
                $stmtDeleteEstrategias->execute([':plan_id' => $_POST['plan_id']]);

                // Prepara o INSERT para as novas estratégias metodológicas
                $stmtInsertEstrategia = $SmecelNovo->prepare("INSERT INTO smc_plan_estrate_relacionamentos (smc_id_planejamento, smc_id_estrate_metod) VALUES (:plan_id, :estrategia_id)");

                foreach ($_POST['estrategias_metodologicas'] as $estrategia_id) {
                    $stmtInsertEstrategia->execute([
                        ':plan_id' => $_POST['plan_id'],
                        ':estrategia_id' => $estrategia_id
                    ]);
                }

                // Inserção de estratégia metodológica personalizada se o usuário preencher o campo "Outra"
                /*if (!empty($_POST['estrategia_metodologica_personalizada'])) {
                    $stmtInsertOutraEstrategia = $SmecelNovo->prepare("INSERT INTO smc_plane (smc_id_planejamento, estrategia_personalizada) VALUES (:plan_id, :estrategia_personalizada)");
                    $stmtInsertOutraEstrategia->execute([
                        ':plan_id' => $_POST['plan_id'],
                        ':estrategia_personalizada' => $_POST['estrategia_metodologica_personalizada']
                    ]);
                }*/
            } catch (PDOException $e) {
                die("Erro ao salvar estratégias metodológicas: " . $e->getMessage());
            }
        }



        // Inserção das avaliações selecionadas
        if (isset($_POST['avaliacoes']) && is_array($_POST['avaliacoes'])) {
            $stmtDeleteAvaliacoes = $SmecelNovo->prepare("DELETE FROM smc_plan_ava_relacionamento WHERE smc_id_planejamento = :plan_id");
            $stmtDeleteAvaliacoes->execute([':plan_id' => $_POST['plan_id']]);

            $stmtInsertAvaliacao = $SmecelNovo->prepare("INSERT INTO smc_plan_ava_relacionamento (smc_id_planejamento, smc_id_avaliacao) VALUES (:plan_id, :avaliacao_id)");
            foreach ($_POST['avaliacoes'] as $avaliacao_id) {
                $stmtInsertAvaliacao->execute([
                    ':plan_id' => $_POST['plan_id'],
                    ':avaliacao_id' => $avaliacao_id
                ]);
            }
        }

        header("Location: planejamento_ver.php?atualizado");
        exit;
    }



    $etapa_ano = $row_Etapa['etapa_ano_ef'];
    $consulta = " AND bncc_ef_ano IN ($etapa_ano) ";
    //$disciplina = $row_Componente['disciplina_id'];
    $disciplina = '';

    $stmtBnccEf = $SmecelNovo->prepare("
        SELECT 
            bncc_ef_id, 
            bncc_ef_area_conhec_id, 
            bncc_ef_comp_id, 
            bncc_ef_componente, 
            bncc_ef_ano, 
            bncc_ef_campos_atuacao, 
            bncc_ef_eixo, 
            bncc_ef_un_tematicas, 
            bncc_ef_prat_ling, 
            bncc_ef_obj_conhec, 
            bncc_ef_habilidades, 
            bncc_ef_comentarios, 
            bncc_ef_poss_curr
        FROM smc_bncc_ef
        WHERE bncc_ef_comp_id = :disciplina $consulta
    ");
    $stmtBnccEf->bindValue(':disciplina', $disciplina, PDO::PARAM_STR);
    $stmtBnccEf->execute();
    $bnccEf = $stmtBnccEf->fetchAll(PDO::FETCH_ASSOC);

    $queryBncc = "";
    $params = [];

    // Verifica se a etapa requer filtro BNCC
    if ($row_Etapa['etapa_id_filtro'] == "1") {
        $queryBncc = "WHERE disciplina_bncc = :bncc";
        $params[':bncc'] = 'S';  // Adicionando valor do parâmetro dinamicamente
    }

    $query = "SELECT disciplina_id, disciplina_nome, disciplina_bncc FROM smc_disciplina $queryBncc";
    $stmtMatrizDisciplinas = $SmecelNovo->prepare($query);
    $stmtMatrizDisciplinas->execute($params);
    $matrizDisciplinas = $stmtMatrizDisciplinas->fetchAll(PDO::FETCH_ASSOC);
    $totalRowsDisciplinas = $stmtMatrizDisciplinas->rowCount();

    if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form3")) {
        try {
            $disciplina = $_POST['disciplina'];

            $stmtInsert = $SmecelNovo->prepare(
                "INSERT INTO smc_planejamento_componente (smc_id_planejamento, smc_id_componente) 
             VALUES (:plan, :disciplina)"
            );

            $stmtInsert->bindValue(':plan', $planejamento, PDO::PARAM_INT);
            $stmtInsert->bindValue(':disciplina', $disciplina, PDO::PARAM_INT);
            $stmtInsert->execute();

            $insertGoTo = "planejamentov2_editar.php";
            if (isset($_SERVER['QUERY_STRING'])) {
                $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
                $insertGoTo .= $_SERVER['QUERY_STRING'];
            }
            header("Location: $insertGoTo");
            exit;
        } catch (PDOException $e) {
            die("Erro ao inserir o componente: " . $e->getMessage());
        }
    }


} catch (PDOException $e) {
    die("Erro na consulta: " . $e->getMessage());
}

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
    <title>PROFESSOR | <?php echo $row_ProfLogado['func_nome']; ?> | SMECEL - Sistema de Gestão Escolar</title>
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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">

</head>

<body>
    <?php include_once "inc/navebar.php"; ?>
    <?php include_once "inc/sidebar.php"; ?>
    <main class="ls-main">
        <div class="container-fluid">
            <h1 class="ls-title-intro ls-ico-home">Ano letivo <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h1>
            <p><a href="planejamento_ver.php" class="ls-btn ls-ico-chevron-left">Voltar</a></p>
            <hr>

            <form method="post" name="form1" action="<?php echo $editFormAction; ?>"
                class="ls-form ls-form-horizontal row">

                <label class="ls-label col-md-12 ls-flex">
                    <b class="ls-label-text ls-text-left">Componente curricular:</b>
                    <?php if (!empty($disciplinasAC)) {
                        foreach ($disciplinasAC as $disciplinaAC) { ?>
                            <span class="ls-tag-success">
                                <?php echo htmlspecialchars($disciplinaAC['disciplina_nome'], ENT_QUOTES, 'UTF-8'); ?>&nbsp;
                                <a style="cursor:pointer" onclick="deletarDisciplina(<?= $disciplinaAC['ac_componente_id']; ?>)"
                                    class="ls-ico-close"></a>
                            </span>
                        <?php }
                    } ?>
                    <br>
                    <a data-ls-module="modal" data-target="#modalComponente"
                        class="ls-tag-info ls-ico-plus ls-sm-margin-top">Adicionar componente </a>
                </label>

                <label class="ls-label col-xs-12">
                    <b class="ls-label-text">Período</b>
                    <p class="ls-label-info">Informe o período</p>
                    <div class="ls-custom-select">
                        <select class="ls-select" name="ac_periodo">
                            <option value="">SELECIONE O PERÍODO</option>
                            <?php foreach ($result_periodos as $periodo): ?>

                                <option value="<?= $periodo['per_unid_id']; ?>" <?php if ($row_plan_edit['smc_id_periodo'] == $periodo['per_unid_id'])
                                      echo 'selected'; ?>>
                                    <?= $periodo['per_unid_periodo']; ?>° PERÍODO
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </label>

                <label class="ls-label col-md-6">
                    <b class="ls-label-text">DE</b>
                    <p class="ls-label-info">Informe a data inicial</p>
                    <input type="date" name="ac_data_inicial"
                        value="<?php echo htmlspecialchars($row_plan_edit['smc_planejamento_data_inicial'], ENT_COMPAT, 'utf-8'); ?>"
                        size="32" required>
                </label>
                <label class="ls-label col-md-6">
                    <b class="ls-label-text">ATÉ</b>
                    <p class="ls-label-info">Informe a data final</p>
                    <input type="date" name="ac_data_final"
                        value="<?php echo htmlspecialchars($row_plan_edit['smc_planejamento_data_final'], ENT_COMPAT, 'utf-8'); ?>"
                        size="32" required>
                </label>

                <?php if ($row_plan_edit['smc_planejamento_correcao'] == "1") { ?>
                    <label class="ls-label col-md-12">
                        <b class="ls-label-text">Retorno da Coordenação Pedagógica:</b><br>
                        <p class="ls-label-info ls-text-underline ls-tag-warning">
                            <?php echo nl2br($row_plan_edit['smc_feedback']); ?>
                        </p>
                    </label>
                <?php } ?>

                <!-- CASO SEJA EDUCAÇÃO INFANTIL -->
                <?php if ($row_Etapa['etapa_id_filtro'] == "1") { ?>
                    <!-- Botão único para abrir a modal universal -->
                    <a class="ls-btn-primary" onclick="openUniversalModal()">HABILIDADES</a>
                    <hr>
                    <!-- Modal Universal -->
                    <div class="ls-modal" id="modalUniversal">
                        <div class="ls-modal-large">
                            <div class="ls-modal-header">
                                <button data-dismiss="modal">&times;</button>
                                <h4 class="ls-modal-title">Selecione uma opção</h4>
                            </div>
                            <div class="ls-modal-body">
                                <label class="ls-label col-xs-12">
                                    <b class="ls-label-text">Componente</b>
                                    <p class="ls-label-info">Informe o componente</p>
                                    <div class="ls-custom-select">
                                        <select id="modalSelect" class="ls-select" onchange="loadModalContent()">
                                            <option value="" selected disabled>Selecione</option>
                                            <option value="EO">EO – O eu, o outro e o nós</option>
                                            <option value="CG">CG – Corpo, gestos e movimento</option>
                                            <option value="TS">TS – Traços, sons, cores e formas</option>
                                            <option value="EF">EF – Escuta, fala, pensamento e imaginação</option>
                                            <option value="ET">ET – Espaços, tempos, quantidades, relações e transformações
                                            </option>
                                        </select>
                                    </div>
                                </label>
                                <div id="modalContent">
                                    <p>Por favor, selecione um componente para visualizar o conteúdo.</p>
                                </div>
                            </div>
                            <div class="ls-modal-footer">
                                <button class="ls-btn-primary" data-dismiss="modal">FECHAR</button>
                            </div>
                        </div>
                    </div>

                    <?php
                    if ($temasIntegradores) {
                        echo '<div class="ls-label col-md-12">';
                        echo '<p><strong>TEMAS INTEGRADORES</strong></p>';

                        // Exibindo os temas integradores com marcação dos itens previamente selecionados
                        $temasSelecionados = explode(',', $row_plan_edit['temas_marcados']);
                        foreach ($temasIntegradores as $tema) {
                            $nome_tema = mb_convert_encoding($tema['smc_tema_integrador'], 'UTF-8', 'ISO-8859-1');
                            $checked = in_array($tema['smc_id_temas_integradores'], $temasSelecionados) ? 'checked="checked"' : '';

                            echo '<label class="ls-label-text">';
                            echo '<input name="temas_integradores[]" type="checkbox" value="' . htmlspecialchars($tema['smc_id_temas_integradores'], ENT_QUOTES, 'UTF-8') . '" ' . $checked . ' /> ';
                            echo htmlspecialchars($nome_tema, ENT_QUOTES, 'UTF-8');
                            echo '</label><br>';
                        }

                        echo '</div>';
                    } else {
                        echo "<p>Nenhum tema integrador encontrado.</p>";
                    }
                    ?>
                    <label class="ls-label col-md-12">
                        <b class="ls-label-text">ATIVIDADE DESENVOLVIDA PARA O TEMA INTEGRADOR</b>
                        <input type="text" name="smc_atv_dev_tema_integrador" value="<?php echo htmlspecialchars($row_plan_edit['smc_atv_dev_tema_integrador'], ENT_COMPAT, 'utf-8'); ?>">
                    </label>
                    <hr>

                    <!-- Exemplo com Checkbox -->
                    <div class="ls-label col-md-12">
                        <p>DIREITOS DE APRENDIZAGEM</p>
                        <label class="ls-label-text">
                            <input name="plan_da_conviver" type="checkbox" <?php if (!(strcmp(htmlspecialchars($row_plan_edit['ac_da_conviver'], ENT_COMPAT, 'utf-8'), "S"))) {
                                echo "checked=\"checked\"";
                            } ?> />
                            CONVIVER </label>
                        <label class="ls-label-text">
                            <input name="plan_da_brincar" type="checkbox" <?php if (!(strcmp(htmlspecialchars($row_plan_edit['ac_da_brincar'], ENT_COMPAT, 'utf-8'), "S"))) {
                                echo "checked=\"checked\"";
                            } ?> />
                            BRINCAR </label>
                        <label class="ls-label-text">
                            <input name="plan_da_participar" type="checkbox" <?php if (!(strcmp(htmlspecialchars($row_plan_edit['ac_da_participar'], ENT_COMPAT, 'utf-8'), "S"))) {
                                echo "checked=\"checked\"";
                            } ?> />
                            PARTICIPAR </label>
                        <label class="ls-label-text">
                            <input name="plan_da_explorar" type="checkbox" <?php if (!(strcmp(htmlspecialchars($row_plan_edit['ac_da_explorar'], ENT_COMPAT, 'utf-8'), "S"))) {
                                echo "checked=\"checked\"";
                            } ?> />
                            EXPLORAR </label>
                        <label class="ls-label-text">
                            <input name="plan_da_expressar" type="checkbox" <?php if (!(strcmp(htmlspecialchars($row_plan_edit['ac_da_expressar'], ENT_COMPAT, 'utf-8'), "S"))) {
                                echo "checked=\"checked\"";
                            } ?> />
                            EXPRESSAR </label>
                        <label class="ls-label-text">
                            <input name="plan_da_conhecerse" type="checkbox" <?php if (!(strcmp(htmlspecialchars($row_plan_edit['ac_da_conhecerse'], ENT_COMPAT, 'utf-8'), "S"))) {
                                echo "checked=\"checked\"";
                            } ?> />
                            CONHECER-SE </label>
                    </div>

                    <label class="ls-label col-md-12">
                        <b class="ls-label-text">Atividades permanentes</b>
                        <textarea name="atv_permanentes" id="summernote1"
                            rows="4"><?php echo htmlspecialchars($row_plan_edit['smc_atividades_permanentes'], ENT_COMPAT, 'utf-8'); ?></textarea>
                    </label>

                    <label class="ls-label col-md-12">
                        <b class="ls-label-text">Objetivos de aprendizagem e desenvolvimento</b>
                        <textarea name="obj_aprendizagem" id="summernote1"
                            rows="4"><?php echo htmlspecialchars($row_plan_edit['obj_aprendizagem_desenvolvimento'], ENT_COMPAT, 'utf-8'); ?></textarea>
                    </label>

                    <label class="ls-label col-md-12">
                        <b class="ls-label-text">Aprendizagens vivenciadas pelas crianças (Saberes)</b>
                        <textarea name="aprendizagens_saberes" id="summernote1"
                            rows="4"><?php echo htmlspecialchars($row_plan_edit['aprendizagens_saberes'], ENT_COMPAT, 'utf-8'); ?></textarea>
                    </label>

                    <?php
                    if ($EstrategiasMetodologicas) {
                        echo '<div class="ls-label col-md-12">';
                        echo '<p><strong>ESTRATÉGIAS METODOLÓGICAS</strong></p>';

                        // Converte a string de estratégias marcadas para array
                        $estrategias_marcadas = isset($row_plan_edit['estrategias_marcadas']) ? explode(',', $row_plan_edit['estrategias_marcadas']) : [];

                        foreach ($EstrategiasMetodologicas as $estrategias) {
                            $nome_estrategias = mb_convert_encoding($estrategias['smc_estrategias_metodologicas'], 'UTF-8', 'ISO-8859-1');
                            $checked = in_array($estrategias['smc_id_estrategias_metodologicas'], $estrategias_marcadas) ? 'checked="checked"' : '';

                            echo '<label class="ls-label-text">';
                            echo '<input name="estrategias_metodologicas[]" type="checkbox" value="' . htmlspecialchars($estrategias['smc_id_estrategias_metodologicas'], ENT_QUOTES, 'UTF-8') . '" ' . $checked . ' /> ';
                            echo htmlspecialchars($nome_estrategias, ENT_QUOTES, 'UTF-8');
                            echo '</label><br>';
                        }

                        echo '</div>';
                        ?>
                        <label class="ls-label col-md-12">
                            <b class="ls-label-text">OUTRA</b>
                            <textarea name="estrategia_metodologica_personalizada" id="summernote2"
                                rows="1"><?php echo htmlspecialchars($row_plan_edit['estrategia_metodologica_personalizada'], ENT_COMPAT, 'utf-8'); ?></textarea>
                        </label>
                        <?php
                    } else {
                        echo "<p>Nenhuma estratégia metodológica encontrada.</p>";
                    }

                    ?>

                    <label class="ls-label col-md-12">
                        <b class="ls-label-text">Recursos</b>
                        <textarea name="recursos" id="summernote1"
                            rows="4"><?php echo htmlspecialchars($row_plan_edit['recursos'], ENT_COMPAT, 'utf-8'); ?></textarea>
                    </label>

                    <?php

                    if ($AvaliacaoEdinf) {
                        echo '<div class="ls-label col-md-12">';
                        echo '<p><strong>AVALIAÇÕES</strong></p>';

                        foreach ($AvaliacaoEdinf as $avaliacao) {
                            $nome_avaliacao = mb_convert_encoding($avaliacao['smc_avaliacao_edinf'], 'UTF-8', 'ISO-8859-1');
                            $checked = in_array($avaliacao['smc_id_avaliacao_edinf'], $avaliacoes_marcadas_edinf) ? 'checked="checked"' : '';

                            echo '<label class="ls-label-text">';
                            echo '<input name="avaliacoesedinf[]" type="checkbox" value="' . htmlspecialchars($avaliacao['smc_id_avaliacao_edinf'], ENT_QUOTES, 'UTF-8') . '" ' . $checked . ' /> ';
                            echo htmlspecialchars($nome_avaliacao, ENT_QUOTES, 'UTF-8');
                            echo '</label><br>';
                        }

                        echo '</div>';
                        ?>
                        <label class="ls-label col-md-12">
                            <b class="ls-label-text">OUTROS</b>
                            <textarea name="avaliacao_edinf_outras" id="summernote1"
                                rows="1"><?php echo htmlspecialchars(mb_convert_encoding($row_plan_edit['avaliacao_edinf_outras'], 'UTF-8', 'ISO-8859-1'), ENT_COMPAT, 'UTF-8'); ?></textarea>
                        </label>
                        <?php
                    } else {
                        echo 'Nenhuma avaliação encontrada';
                    }

                    ?>

                <?php } else { ?>
                    <?php
                    if ($temasIntegradores) {
                        echo '<div class="ls-label col-md-12">';
                        echo '<p><strong>TEMAS INTEGRADORES</strong></p>';

                        // Exibindo os temas integradores com marcação dos itens previamente selecionados
                        $temasSelecionados = explode(',', $row_plan_edit['temas_marcados']);
                        foreach ($temasIntegradores as $tema) {
                            $nome_tema = mb_convert_encoding($tema['smc_tema_integrador'], 'UTF-8', 'ISO-8859-1');
                            $checked = in_array($tema['smc_id_temas_integradores'], $temasSelecionados) ? 'checked="checked"' : '';

                            echo '<label class="ls-label-text">';
                            echo '<input name="temas_integradores[]" type="checkbox" value="' . htmlspecialchars($tema['smc_id_temas_integradores'], ENT_QUOTES, 'UTF-8') . '" ' . $checked . ' /> ';
                            echo htmlspecialchars($nome_tema, ENT_QUOTES, 'UTF-8');
                            echo '</label><br>';
                        }

                        echo '</div>';
                        ?>
                        <?php
                    } else {
                        echo "<p>Nenhum tema integrador encontrado.</p>";
                    }
                    ?>
                    <!-- FIM DA ABA EDUCAÇÃO INFANTIL -->
                    <label class="ls-label col-md-12">
                        <b class="ls-label-text">OBJETOS DE CONHECIMENTO/CONTEÚDO(S)</b>
                        <textarea name="obj_conhecimento_conteudos" id="summernote1"
                            rows="4"><?php echo htmlspecialchars($row_plan_edit['smc_obj_conhecimento_conteudos'], ENT_COMPAT, 'utf-8'); ?></textarea>
                    </label>

                    <label class="ls-label col-md-12">
                        <b class="ls-label-text">HABILIDADES</b>
                        <textarea name="habilidades" id="summernote1"
                            rows="4"><?php echo htmlspecialchars($row_plan_edit['smc_habilidades'], ENT_COMPAT, 'utf-8'); ?></textarea>
                    </label>

                    <?php
                    if ($metodologias) {
                        echo '<div class="ls-label col-md-12">';
                        echo '<p><strong>METODOLOGIA</strong></p>';

                        // Exibindo as metodologias com marcação dos itens previamente selecionados
                        foreach ($metodologias as $metodologia) {
                            $nome_metodologia = mb_convert_encoding($metodologia['smc_metodologia'], 'UTF-8', 'ISO-8859-1');
                            $checked = in_array($metodologia['smc_id_metodologia'], $metodologias_marcadas) ? 'checked="checked"' : '';

                            echo '<label class="ls-label-text">';
                            echo '<input name="metodologias[]" type="checkbox" value="' . htmlspecialchars($metodologia['smc_id_metodologia'], ENT_QUOTES, 'UTF-8') . '" ' . $checked . ' /> ';
                            echo htmlspecialchars($nome_metodologia, ENT_QUOTES, 'UTF-8');
                            echo '</label><br>';
                        }

                        echo '</div>';
                        ?>
                        <label class="ls-label col-md-12">
                            <b class="ls-label-text">OUTROS</b>
                            <textarea name="metodologia_personalilzada" id="summernote1"
                                rows="1"><?php echo htmlspecialchars($row_plan_edit['smc_metodologia_personalilzada'], ENT_COMPAT, 'utf-8'); ?></textarea>
                        </label>
                        <?php
                    } else {
                        echo "<p>Nenhuma metodologia encontrada.</p>";
                    }
                    ?>

                    <?php
                    if ($avaliacoes) {
                        echo '<div class="ls-label col-md-12">';
                        echo '<br><h3><strong>AVALIAÇÃO</strong></h3><hr>';

                        // Exibindo as avaliações divididas por tipo e marcadas corretamente
                        $tipos = ['Processual', 'Somativa', 'Formativa'];
                        foreach ($tipos as $tipo) {
                            echo "<h4><strong>$tipo</strong></h4>";
                            foreach ($avaliacoes as $avaliacao) {
                                if ($avaliacao['smc_tipo'] === $tipo) {
                                    $descricao = mb_convert_encoding($avaliacao['smc_descricao'], 'UTF-8', 'ISO-8859-1');
                                    $checked = in_array($avaliacao['smc_id_avaliacao'], $avaliacoes_marcadas) ? 'checked="checked"' : '';

                                    echo '<label class="ls-label-text">';
                                    echo '<input name="avaliacoes[]" type="checkbox" value="' . htmlspecialchars($avaliacao['smc_id_avaliacao'], ENT_QUOTES, 'UTF-8') . '" ' . $checked . ' /> ';
                                    echo htmlspecialchars($descricao, ENT_QUOTES, 'UTF-8');
                                    echo '</label><br>';
                                }
                            }
                            echo '<br>';
                        }

                        echo '</div>';
                        ?>
                        <label class="ls-label col-md-12">
                            <b class="ls-label-text">OUTRA</b>
                            <textarea name="avaliacao_personalizada" id="summernote2"
                                rows="1"><?php echo htmlspecialchars($row_plan_edit['smc_avaliacao_personalizada'], ENT_COMPAT, 'utf-8'); ?></textarea>
                        </label>
                        <?php
                    } else {
                        echo "<p>Nenhuma avaliação encontrada.</p>";
                    }
                    ?>


                <?php } ?>

                <div class="ls-actions-btn">
                    <input type="submit" class="ls-btn-primary ls-float-left" value="ATUALIZAR PLANEJAMENTO">
                    <input type="hidden" name="MM_update" value="form1" />
                    <input type="hidden" name="plan_id" value="<?php echo $planejamento; ?>" />


                </div>

            </form>
        </div>
    </main>
    <?php include_once "inc/notificacoes.php"; ?>


    <div class="ls-modal" id="modalHabilidades">
        <div class="ls-modal-box ls-modal-large">
            <div class="ls-modal-header">
                <button data-dismiss="modal">&times;</button>
                <h4 class="ls-modal-title">HABILIDADES</h4>
            </div>
            <div class="ls-modal-body" id="myModalBody">

                <table class="ls-table">
                    <?php foreach ($bnccEf as $rowBnccEf): ?>
                        <tr>
                            <td>
                                <strong class="">Habilidades</strong>
                                <?= htmlspecialchars(utf8_encode($row_bncc['bncc_ef_habilidades']), ENT_QUOTES, 'UTF-8'); ?><br><br>
                                <strong class="">Componente</strong>
                                <?= htmlspecialchars(utf8_encode($row_bncc['bncc_ef_componente']), ENT_QUOTES, 'UTF-8'); ?>
                                |
                                <strong class="">Ano/Faixa</strong>
                                <?= htmlspecialchars(utf8_encode($row_bncc['bncc_ef_ano']), ENT_QUOTES, 'UTF-8'); ?>º
                                ano(s)<br><br>
                                <?php if (!empty($row_bncc['bncc_ef_campos_atuacao'])): ?>
                                    <strong class="">Campo de atuação</strong>
                                    <?= htmlspecialchars(utf8_encode($row_bncc['bncc_ef_campos_atuacao']), ENT_QUOTES, 'UTF-8'); ?><br><br>
                                <?php endif; ?>
                                <?php if (!empty($row_bncc['bncc_ef_eixo'])): ?>
                                    <strong class="">Eixo</strong>
                                    <?= htmlspecialchars(utf8_encode($row_bncc['bncc_ef_eixo']), ENT_QUOTES, 'UTF-8'); ?><br><br>
                                <?php endif; ?>
                                <?php if (!empty($row_bncc['bncc_ef_un_tematicas'])): ?>
                                    <strong class="">Unidades Temáticas</strong>
                                    <?= htmlspecialchars(utf8_encode($row_bncc['bncc_ef_un_tematicas']), ENT_QUOTES, 'UTF-8'); ?><br><br>
                                <?php endif; ?>
                                <?php if (!empty($row_bncc['bncc_ef_prat_ling'])): ?>
                                    <strong class="">Práticas de Linguagem</strong>
                                    <?= htmlspecialchars(utf8_encode($row_bncc['bncc_ef_prat_ling']), ENT_QUOTES, 'UTF-8'); ?><br><br>
                                <?php endif; ?>
                                <strong class="">Objetos de conhecimento</strong>
                                <?= htmlspecialchars(utf8_encode($row_bncc['bncc_ef_obj_conhec']), ENT_QUOTES, 'UTF-8'); ?><br><br>
                                <strong class="">Comentários</strong>
                                <?= htmlspecialchars(utf8_encode($row_bncc['bncc_ef_comentarios']), ENT_QUOTES, 'UTF-8'); ?><br><br>
                                <strong class="">Possibilidades para o Currículo</strong>
                                <?= htmlspecialchars(utf8_encode($row_bncc['bncc_ef_poss_curr']), ENT_QUOTES, 'UTF-8'); ?><br>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>

            </div>
            <div class="ls-modal-footer">
                <button class="ls-btn-primary" data-dismiss="modal">FECHAR</button>
            </div>
        </div>
    </div><!-- /.modal -->


    <div class="ls-modal" id="modalComponente">
        <div class="ls-modal-small">
            <div class="ls-modal-header">
                <button data-dismiss="modal">&times;</button>
                <h4 class="ls-modal-title">Adicionar componente</h4>
            </div>

            <div class="ls-modal-body" id="myModalBody">
                <form method="post" id="form3" name="form3" action="<?php echo $editFormAction; ?>"
                    onsubmit="return validateForm();">
                    <div class="ls-custom-select">
                        <select class="ls-select" id="disciplinas" name="disciplina">
                            <option>SELECIONE...</option>
                            <?php foreach ($matrizDisciplinas as $disciplina): ?>
                                <option value="<?= htmlspecialchars($disciplina['disciplina_id'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <?= htmlspecialchars($disciplina['disciplina_nome'], ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <input type="submit" value="Adicionar" class="ls-btn-primary ls-sm-margin-top" title="Adicionar">
                    <input type="hidden" name="MM_insert" value="form3" />
                </form>
            </div>
            <div class="ls-modal-footer">

            </div>

        </div>
    </div><!-- /.modal disciplinas -->




    <div class="ls-modal" id="modalLargeEO">
        <div class="ls-modal-large">
            <div class="ls-modal-header">
                <button data-dismiss="modal">&times;</button>
                <h4 class="ls-modal-title">EO – O eu, o outro e o nós</h4>
            </div>
            <div class="ls-modal-body">
                <p>
                <table class="ls-table ls-sm-space">
                    <thead>
                        <tr>
                            <th colspan="3"> OBJETIVOS DE APRENDIZAGEM E DESENVOLVIMENTO </th>
                        </tr>
                        <tr>
                            <th> Bebês (zero a 1 ano e 6 meses) </th>
                            <th> Crianças bem pequenas (1 ano
                                e 7 meses a 3 anos e 11 meses) </th>
                            <th> Crianças pequenas (4 anos a
                                5 anos e 11 meses) </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>(EI01EO01)</strong> Perceber que suas ações
                                têm efeitos nas outras
                                crianças e nos adultos. </td>
                            <td><strong>(EI02EO01)</strong> Demonstrar atitudes de
                                cuidado e solidariedade na
                                interação com crianças e
                                adultos. </td>
                            <td><strong>(EI03EO01)</strong> Demonstrar empatia pelos
                                outros, percebendo que
                                as pessoas têm diferentes
                                sentimentos, necessidades e
                                maneiras de pensar e agir. </td>
                        </tr>
                        <tr>
                            <td><strong>(EI01EO02)</strong> Perceber as possibilidades
                                e os limites de seu corpo nas
                                brincadeiras e interações
                                das quais participa. </td>
                            <td><strong>(EI02EO02)</strong> Demonstrar imagem positiva
                                de si e confiança em sua
                                capacidade para enfrentar
                                dificuldades e desafios. </td>
                            <td><strong>(EI03EO02)</strong> Agir de maneira independente,
                                com confiança em suas
                                capacidades, reconhecendo
                                suas conquistas e limitações. </td>
                        </tr>
                        <tr>
                            <td><strong>(EI01EO03)</strong> Interagir com crianças
                                da mesma faixa etária
                                e adultos ao explorar
                                espaços, materiais,
                                objetos, brinquedos. </td>
                            <td><strong> (EI02EO03)</strong> Compartilhar os objetos e
                                os espaços com crianças da
                                mesma faixa etária e adultos. </td>
                            <td><strong>(EI03EO03)</strong> Ampliar as relações
                                interpessoais, desenvolvendo
                                atitudes de participação e
                                cooperação. </td>
                        </tr>
                        <tr>
                            <td><strong>(EI01EO04)</strong> Comunicar necessidades,
                                desejos e emoções,
                                utilizando gestos,
                                balbucios, palavras. </td>
                            <td><strong> (EI02EO04)</strong> Comunicar-se com os colegas
                                e os adultos, buscando
                                compreendê-los e fazendo-se
                                compreender. </td>
                            <td><strong> (EI03EO04)</strong> Comunicar suas ideias e
                                sentimentos a pessoas e
                                grupos diversos. </td>
                        </tr>
                        <tr>
                            <td><strong> (EI01EO05)</strong> Reconhecer seu corpo e
                                expressar suas sensações
                                em momentos de
                                alimentação, higiene,
                                brincadeira e descanso. </td>
                            <td><strong> (EI02EO05)</strong> Perceber que as pessoas
                                têm características físicas
                                diferentes, respeitando essas
                                diferenças. </td>
                            <td><strong> (EI03EO05)</strong> Demonstrar valorização das
                                características de seu corpo
                                e respeitar as características
                                dos outros (crianças e adultos)
                                com os quais convive. </td>
                        </tr>
                        <tr>
                            <td><strong> (EI01EO06)</strong> Interagir com outras crianças
                                da mesma faixa etária e
                                adultos, adaptando-se
                                ao convívio social. </td>
                            <td><strong> (EI02EO06)</strong> Respeitar regras básicas de
                                convívio social nas interações
                                e brincadeiras. </td>
                            <td><strong> (EI03EO06)</strong> Manifestar interesse e
                                respeito por diferentes
                                culturas e modos de vida. </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><strong> (EI02EO07)</strong> Resolver conflitos nas
                                interações e brincadeiras, com
                                a orientação de um adulto. </td>
                            <td><strong> (EI03EO07)</strong> Usar estratégias pautadas
                                no respeito mútuo para lidar
                                com conflitos nas interações
                                com crianças e adultos. </td>
                        </tr>
                    </tbody>
                </table>
                </p>
            </div>
            <div class="ls-modal-footer">
                <button class="ls-btn-primary" data-dismiss="modal">FECHAR</button>
            </div>
        </div>
    </div>

    <div class="ls-modal" id="modalLargeCG">
        <div class="ls-modal-large">
            <div class="ls-modal-header">
                <button data-dismiss="modal">&times;</button>
                <h4 class="ls-modal-title">CG – Corpo, gestos e movimento</h4>
            </div>
            <div class="ls-modal-body">
                <p>
                <table class="ls-table ls-sm-space">
                    <thead>
                        <tr>
                            <th colspan="3"> OBJETIVOS DE APRENDIZAGEM E DESENVOLVIMENTO </th>
                        </tr>
                        <tr>
                            <th> Bebês (zero a 1 ano e 6 meses) </th>
                            <th> Crianças bem pequenas (1 ano
                                e 7 meses a 3 anos e 11 meses) </th>
                            <th> Crianças pequenas (4 anos a
                                5 anos e 11 meses) </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>(EI01CG01)</strong> Movimentar as partes
                                do corpo para exprimir
                                corporalmente emoções,
                                necessidades e desejos. </td>
                            <td><strong>(EI02CG01)</strong> Apropriar-se de gestos e
                                movimentos de sua cultura no
                                cuidado de si e nos jogos e
                                brincadeiras. </td>
                            <td><strong>(EI03CG01)</strong> Criar com o corpo formas
                                diversificadas de expressão
                                de sentimentos, sensações
                                e emoções, tanto nas
                                situações do cotidiano
                                quanto em brincadeiras,
                                dança, teatro, música. </td>
                        </tr>
                        <tr>
                            <td><strong>(EI01CG02)</strong> Experimentar as
                                possibilidades corporais
                                nas brincadeiras e
                                interações em ambientes
                                acolhedores e desafiantes. </td>
                            <td><strong>(EI02CG02)</strong> Deslocar seu corpo no espaço,
                                orientando-se por noções
                                como em frente, atrás, no alto,
                                embaixo, dentro, fora etc., ao
                                se envolver em brincadeiras
                                e atividades de diferentes
                                naturezas. </td>
                            <td><strong>(EI03CG02)</strong> Demonstrar controle e
                                adequação do uso de seu
                                corpo em brincadeiras e
                                jogos, escuta e reconto
                                de histórias, atividades
                                artísticas, entre outras
                                possibilidades. </td>
                        </tr>
                        <tr>
                            <td><strong>(EI01CG03)</strong> Imitar gestos e
                                movimentos de outras
                                crianças, adultos e animais. </td>
                            <td><strong>(EI02CG03)</strong> Explorar formas de
                                deslocamento no espaço
                                (pular, saltar, dançar),
                                combinando movimentos e
                                seguindo orientações. </td>
                            <td><strong>(EI03CG03)</strong> Criar movimentos, gestos,
                                olhares e mímicas em
                                brincadeiras, jogos e
                                atividades artísticas como
                                dança, teatro e música. </td>
                        </tr>
                        <tr>
                            <td><strong>(EI01CG04)</strong> Participar do cuidado do
                                seu corpo e da promoção
                                do seu bem-estar. </td>
                            <td><strong>(EI02CG04)</strong> Demonstrar progressiva
                                independência no cuidado do
                                seu corpo. </td>
                            <td><strong>(EI03CG04)</strong> Adotar hábitos de
                                autocuidado relacionados
                                a higiene, alimentação,
                                conforto e aparência. </td>
                        </tr>
                        <tr>
                            <td><strong>(EI01CG05)</strong> Utilizar os movimentos
                                de preensão, encaixe e
                                lançamento, ampliando
                                suas possibilidades de
                                manuseio de diferentes
                                materiais e objetos. </td>
                            <td><strong>(EI02CG05)</strong> Desenvolver progressivamente
                                as habilidades manuais,
                                adquirindo controle para
                                desenhar, pintar, rasgar,
                                folhear, entre outros. </td>
                            <td><strong>(EI03CG05)</strong> Coordenar suas habilidades
                                manuais no atendimento
                                adequado a seus interesses
                                e necessidades em situações
                                diversas. </td>
                        </tr>
                    </tbody>
                </table>
                </p>
            </div>
            <div class="ls-modal-footer">
                <button class="ls-btn-primary" data-dismiss="modal">FECHAR</button>
            </div>
        </div>
    </div>

    <div class="ls-modal" id="modalLargeTS">
        <div class="ls-modal-large">
            <div class="ls-modal-header">
                <button data-dismiss="modal">&times;</button>
                <h4 class="ls-modal-title">TS – Traços, sons, cores e formas</h4>
            </div>
            <div class="ls-modal-body">
                <p>
                <table class="ls-table ls-sm-space">
                    <thead>
                        <tr>
                            <th colspan="3"> OBJETIVOS DE APRENDIZAGEM E DESENVOLVIMENTO </th>
                        </tr>
                        <tr>
                            <th> Bebês (zero a 1 ano e 6 meses) </th>
                            <th> Crianças bem pequenas (1 ano
                                e 7 meses a 3 anos e 11 meses) </th>
                            <th> Crianças pequenas (4 anos a
                                5 anos e 11 meses) </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong> (EI01TS01)</strong> Explorar sons produzidos
                                com o próprio corpo e
                                com objetos do ambiente. </td>
                            <td><strong> (EI02TS01)</strong> Criar sons com materiais,
                                objetos e instrumentos
                                musicais, para acompanhar
                                diversos ritmos de música. </td>
                            <td><strong> (EI03TS01)</strong> Utilizar sons produzidos
                                por materiais, objetos e
                                instrumentos musicais
                                durante brincadeiras de
                                faz de conta, encenações,
                                criações musicais, festas. </td>
                        </tr>
                        <tr>
                            <td><strong> (EI01TS02)</strong> Traçar marcas gráficas,
                                em diferentes suportes,
                                usando instrumentos
                                riscantes e tintas. </td>
                            <td><strong> (EI02TS02)</strong> Utilizar materiais variados com
                                possibilidades de manipulação
                                (argila, massa de modelar),
                                explorando cores, texturas,
                                superfícies, planos, formas
                                e volumes ao criar objetos
                                tridimensionais. </td>
                            <td><strong> (EI03TS02)</strong> Expressar-se livremente
                                por meio de desenho,
                                pintura, colagem, dobradura
                                e escultura, criando
                                produções bidimensionais e
                                tridimensionais. </td>
                        </tr>
                        <tr>
                            <td><strong> (EI01TS03)</strong> Explorar diferentes fontes
                                sonoras e materiais para
                                acompanhar brincadeiras
                                cantadas, canções,
                                músicas e melodias. </td>
                            <td><strong>(EI02TS03)</strong> Utilizar diferentes fontes
                                sonoras disponíveis no
                                ambiente em brincadeiras
                                cantadas, canções, músicas e
                                melodias. </td>
                            <td><strong>(EI03TS03)</strong> Reconhecer as qualidades do
                                som (intensidade, duração,
                                altura e timbre), utilizando-as
                                em suas produções sonoras
                                e ao ouvir músicas e sons. </td>
                        </tr>
                    </tbody>
                </table>
                </p>
            </div>
            <div class="ls-modal-footer">
                <button class="ls-btn-primary" data-dismiss="modal">FECHAR</button>
            </div>
        </div>
    </div>

    <div class="ls-modal" id="modalLargeEF">
        <div class="ls-modal-large">
            <div class="ls-modal-header">
                <button data-dismiss="modal">&times;</button>
                <h4 class="ls-modal-title">EF – Escuta, fala, pensamento e imaginação</h4>
            </div>
            <div class="ls-modal-body">
                <p>
                <table class="ls-table ls-sm-space">
                    <thead>
                        <tr>
                            <th colspan="3"> OBJETIVOS DE APRENDIZAGEM E DESENVOLVIMENTO </th>
                        </tr>
                        <tr>
                            <th> Bebês (zero a 1 ano e 6 meses) </th>
                            <th> Crianças bem pequenas (1 ano
                                e 7 meses a 3 anos e 11 meses) </th>
                            <th> Crianças pequenas (4 anos a
                                5 anos e 11 meses) </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong> (EI01EF01)</strong> Reconhecer quando é
                                chamado por seu nome
                                e reconhecer os nomes
                                de pessoas com quem
                                convive. </td>
                            <td><strong> (EI02EF01)</strong> Dialogar com crianças e
                                adultos, expressando seus
                                desejos, necessidades,
                                sentimentos e opiniões. </td>
                            <td><strong> (EI03EF01)</strong> Expressar ideias, desejos
                                e sentimentos sobre suas
                                vivências, por meio da
                                linguagem oral e escrita
                                (escrita espontânea), de
                                fotos, desenhos e outras
                                formas de expressão. </td>
                        </tr>
                        <tr>
                            <td><strong> (EI01EF02)</strong> Demonstrar interesse ao
                                ouvir a leitura de poemas
                                e a apresentação de
                                músicas. </td>
                            <td><strong>(EI02EF02)</strong> Identificar e criar diferentes
                                sons e reconhecer rimas e
                                aliterações em cantigas de
                                roda e textos poéticos. </td>
                            <td><strong> (EI03EF02)</strong> Inventar brincadeiras
                                cantadas, poemas e
                                canções, criando rimas,
                                aliterações e ritmos. </td>
                        </tr>
                        <tr>
                            <td><strong> (EI01EF03)</strong> Demonstrar interesse ao
                                ouvir histórias lidas ou
                                contadas, observando
                                ilustrações e os
                                movimentos de leitura do
                                adulto-leitor (modo de
                                segurar o portador e de
                                virar as páginas). </td>
                            <td><strong>(EI02EF03)</strong> Demonstrar interesse e
                                atenção ao ouvir a leitura
                                de histórias e outros textos,
                                diferenciando escrita de
                                ilustrações, e acompanhando,
                                com orientação do adulto-leitor, a direção da leitura (de
                                cima para baixo, da esquerda
                                para a direita). </td>
                            <td><strong>(EI03EF03)</strong> Escolher e folhear livros,
                                procurando orientar-se
                                por temas e ilustrações e
                                tentando identificar palavras
                                conhecidas. </td>
                        </tr>
                        <tr>
                            <td><strong>(EI01EF04)</strong> Reconhecer elementos das
                                ilustrações de histórias,
                                apontando-os, a pedido
                                do adulto-leitor. </td>
                            <td><strong>(EI02EF04)</strong> Formular e responder
                                perguntas sobre fatos da
                                história narrada, identificando
                                cenários, personagens e
                                principais acontecimentos. </td>
                            <td><strong>(EI03EF04)</strong> Recontar histórias ouvidas
                                e planejar coletivamente
                                roteiros de vídeos e de
                                encenações, definindo os
                                contextos, os personagens,
                                a estrutura da história. </td>
                        </tr>
                        <tr>
                            <td><strong>(EI01EF05)</strong> Imitar as variações de
                                entonação e gestos
                                realizados pelos adultos,
                                ao ler histórias e ao cantar. </td>
                            <td><strong>(EI02EF05)</strong> Relatar experiências e fatos
                                acontecidos, histórias ouvidas,
                                filmes ou peças teatrais
                                assistidos etc. </td>
                            <td><strong>(EI03EF05)</strong> Recontar histórias ouvidas
                                para produção de reconto
                                escrito, tendo o professor
                                como escriba. </td>
                        </tr>
                        <tr>
                            <td><strong>(EI01EF06)</strong> Comunicar-se com
                                outras pessoas usando
                                movimentos, gestos,
                                balbucios, fala e outras
                                formas de expressão. </td>
                            <td><strong>(EI02EF06)</strong> Criar e contar histórias
                                oralmente, com base em
                                imagens ou temas sugeridos. </td>
                            <td><strong>(EI03EF06)</strong> Produzir suas próprias
                                histórias orais e escritas
                                (escrita espontânea), em
                                situações com função social
                                significativa. </td>
                        </tr>
                        <tr>
                            <td><strong>(EI01EF07)</strong> Conhecer e manipular
                                materiais impressos e
                                audiovisuais em diferentes
                                portadores (livro, revista,
                                gibi, jornal, cartaz, CD, <em>tablet</em> etc.). </td>
                            <td><strong>(EI02EF07)</strong> Manusear diferentes
                                portadores textuais,
                                demonstrando reconhecer
                                seus usos sociais. </td>
                            <td><strong>(EI03EF07)</strong> Levantar hipóteses sobre
                                gêneros textuais veiculados
                                em portadores conhecidos,
                                recorrendo a estratégias de
                                observação gráfica e/ou de
                                leitura. </td>
                        </tr>
                        <tr>
                            <td><strong> (EI01EF08)</strong> Participar de situações
                                de escuta de textos
                                em diferentes gêneros
                                textuais (poemas,
                                fábulas, contos, receitas,
                                quadrinhos, anúncios etc.). </td>
                            <td><strong>(EI02EF08)</strong> Manipular textos e participar
                                de situações de escuta para
                                ampliar seu contato com
                                diferentes gêneros textuais
                                (parlendas, histórias de
                                aventura, tirinhas, cartazes de
                                sala, cardápios, notícias etc.). </td>
                            <td><strong>(EI03EF08)</strong> Selecionar livros e textos
                                de gêneros conhecidos para
                                a leitura de um adulto e/ou
                                para sua própria leitura
                                (partindo de seu repertório
                                sobre esses textos, como a
                                recuperação pela memória,
                                pela leitura das ilustrações
                                etc.). </td>
                        </tr>
                        <tr>
                            <td><strong>(EI01EF09)</strong> Conhecer e manipular
                                diferentes instrumentos e
                                suportes de escrita. </td>
                            <td><strong>(EI02EF09)</strong> Manusear diferentes
                                instrumentos e suportes de
                                escrita para desenhar, traçar
                                letras e outros sinais gráficos. </td>
                            <td><strong>(EI03EF09)</strong> Levantar hipóteses em
                                relação à linguagem escrita,
                                realizando registros de
                                palavras e textos, por meio
                                de escrita espontânea. </td>
                        </tr>
                    </tbody>
                </table>
                </p>
            </div>
            <div class="ls-modal-footer">
                <button class="ls-btn-primary" data-dismiss="modal">FECHAR</button>
            </div>
        </div>
    </div>

    <div class="ls-modal" id="modalLargeET">
        <div class="ls-modal-large">
            <div class="ls-modal-header">
                <button data-dismiss="modal">&times;</button>
                <h4 class="ls-modal-title">ET – Espaços, tempos, quantidades, relações e transformações</h4>
            </div>
            <div class="ls-modal-body">
                <p>
                <table class="ls-table ls-sm-space">
                    <thead>
                        <tr>
                            <th colspan="3"> OBJETIVOS DE APRENDIZAGEM E DESENVOLVIMENTO </th>
                        </tr>
                        <tr>
                            <th> Bebês (zero a 1 ano e 6 meses) </th>
                            <th> Crianças bem pequenas (1 ano
                                e 7 meses a 3 anos e 11 meses) </th>
                            <th> Crianças pequenas (4 anos a
                                5 anos e 11 meses) </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>(EI01ET01)</strong> Explorar e descobrir as
                                propriedades de objetos e
                                materiais (odor, cor, sabor,
                                temperatura). </td>
                            <td><strong> (EI02ET01)</strong> Explorar e descrever
                                semelhanças e diferenças
                                entre as características e
                                propriedades dos objetos
                                (textura, massa, tamanho). </td>
                            <td><strong>(EI03ET01)</strong> Estabelecer relações
                                de comparação entre
                                objetos, observando suas
                                propriedades. </td>
                        </tr>
                        <tr>
                            <td><strong>(EI01ET02)</strong> Explorar relações
                                de causa e efeito
                                (transbordar, tingir,
                                misturar, mover e remover
                                etc.) na interação com o
                                mundo físico. </td>
                            <td><strong>(EI02ET02)</strong> Observar, relatar e descrever
                                incidentes do cotidiano e
                                fenômenos naturais (luz solar,
                                vento, chuva etc.). </td>
                            <td><strong>(EI03ET02)</strong> Observar e descrever
                                mudanças em diferentes
                                materiais, resultantes
                                de ações sobre eles, em
                                experimentos envolvendo
                                fenômenos naturais e
                                artificiais. </td>
                        </tr>
                        <tr>
                            <td><strong>(EI01ET03)</strong> Explorar o ambiente
                                pela ação e observação,
                                manipulando,
                                experimentando e
                                fazendo descobertas. </td>
                            <td><strong>(EI02ET03)</strong> Compartilhar, com outras
                                crianças, situações de cuidado
                                de plantas e animais nos
                                espaços da instituição e fora
                                dela. </td>
                            <td><strong>(EI03ET03)</strong> Identificar e selecionar
                                fontes de informações, para
                                responder a questões sobre
                                a natureza, seus fenômenos,
                                sua conservação. </td>
                        </tr>
                        <tr> </tr>
                        <tr>
                            <td><strong>(EI01ET04)</strong> Manipular, experimentar,
                                arrumar e explorar
                                o espaço por meio
                                de experiências de
                                deslocamentos de si e dos
                                objetos. </td>
                            <td><strong>(EI02ET04)</strong> Identificar relações espaciais
                                (dentro e fora, em cima,
                                embaixo, acima, abaixo, entre
                                e do lado) e temporais (antes,
                                durante e depois). </td>
                            <td><strong>(EI03ET04)</strong> Registrar observações,
                                manipulações e medidas,
                                usando múltiplas linguagens
                                (desenho, registro por
                                números ou escrita
                                espontânea), em diferentes
                                suportes. </td>
                        </tr>
                        <tr>
                            <td><strong> (EI01ET05)</strong> Manipular materiais
                                diversos e variados para
                                comparar as diferenças e
                                semelhanças entre eles. </td>
                            <td><strong>(EI02ET05)</strong> Classificar objetos,
                                considerando determinado
                                atributo (tamanho, peso, cor,
                                forma etc.). </td>
                            <td><strong> (EI03ET05)</strong> Classificar objetos e figuras
                                de acordo com suas
                                semelhanças e diferenças. </td>
                        </tr>
                        <tr>
                            <td><strong> (EI01ET06)</strong> Vivenciar diferentes ritmos,
                                velocidades e fluxos nas
                                interações e brincadeiras
                                (em danças, balanços,
                                escorregadores etc.). </td>
                            <td><strong> (EI02ET06)</strong> Utilizar conceitos básicos de
                                tempo (agora, antes, durante,
                                depois, ontem, hoje, amanhã,
                                lento, rápido, depressa,
                                devagar). </td>
                            <td><strong> (EI03ET06)</strong> Relatar fatos importantes
                                sobre seu nascimento e
                                desenvolvimento, a história
                                dos seus familiares e da sua
                                comunidade. </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><strong>(EI02ET07)</strong> Contar oralmente objetos,
                                pessoas, livros etc., em
                                contextos diversos. </td>
                            <td><strong>(EI03ET07)</strong> Relacionar números às suas
                                respectivas quantidades
                                e identificar o antes, o
                                depois e o entre em uma
                                sequência. </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><strong>(EI02ET08)</strong> Registrar com números a
                                quantidade de crianças
                                (meninas e meninos, presentes
                                e ausentes) e a quantidade de
                                objetos da mesma natureza
                                (bonecas, bolas, livros etc.). </td>
                            <td><strong>(EI03ET08)</strong> Expressar medidas (peso,
                                altura etc.), construindo
                                gráficos básicos. </td>
                        </tr>
                    </tbody>
                </table>
                </p>
            </div>
            <div class="ls-modal-footer">
                <button class="ls-btn-primary" data-dismiss="modal">FECHAR</button>
            </div>

        </div>
    </div>



    <div class="ls-modal" id="modalLargeN">
        <div class="ls-modal-large">
            <div class="ls-modal-header">
                <button data-dismiss="modal">&times;</button>
                <h4 class="ls-modal-title">Parte Diversificada</h4>
            </div>
            <div class="ls-modal-body">
                <p>

                    Não há descrição dos Objetivos de Aprendizagem e Desenvolvimento definidas pela BNCC na parte
                    diversificada.

                </p>
            </div>
            <div class="ls-modal-footer">
                <button class="ls-btn-primary" data-dismiss="modal">FECHAR</button>

            </div>
        </div>
    </div>




    <!-- We recommended use jQuery 1.10 or up -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="js/locastyle.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/sweetalert2.min.js"></script>
    <!--<script src="https://cdn.tiny.cloud/1/iq1gy8qt7s1b9bj92c2fc7whie95augu8r8kz97dgoufkli8/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>-->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script src="langs/pt_BR.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // Abre a modal universal
        function openUniversalModal() {
            document.getElementById('modalUniversal').classList.add('ls-opened');
        }

        // Fecha a modal ao clicar no botão "FECHAR"
        document.querySelector('[data-dismiss="modal"]').addEventListener('click', function () {
            document.getElementById('modalUniversal').classList.remove('ls-opened');
        });

        // Função para carregar o conteúdo com base na seleção
        function loadModalContent() {
            let selectedValue = document.getElementById('modalSelect').value;
            let modalTitle = document.getElementById('modalTitle');

            switch (selectedValue) {
                case 'EO':
                    modalLarge('EO');
                    break;
                case 'CG':
                    modalLarge('CG');
                    break;
                case 'TS':
                    modalLarge('TS');
                    break;
                case 'EF':
                    modalLarge('EF');
                    break;
                case 'ET':
                    modalLarge('ET');
                    break;
                default:
                    document.getElementById('modalContent').innerHTML = "<p>Por favor, selecione uma categoria para visualizar o conteúdo.</p>";
                    modalTitle.innerText = "Selecione uma opção";
            }
        }

        // Função para abrir a modal específica baseada no valor selecionado
        function modalLarge(category) {
            let modalId = `modalLarge${category}`;
            let modal = document.getElementById(modalId);

            if (modal) {
                modal.classList.add('ls-opened');
            } else {
                alert("Conteúdo não encontrado!");
            }
        }

        // Fechar modais ao clicar no botão de fechar
        document.querySelectorAll('[data-dismiss="modal"]').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.ls-modal').forEach(modal => {
                    modal.classList.remove('ls-opened');
                });
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            $('textarea').summernote({
                placeholder: 'Digite aqui...',
                tabsize: 2,
                height: 120,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', []],
                    ['view', []]
                ]
            });
        });

    </script>
    <script>
        function enviarId(id) {
            // Enviar o ID via AJAX
            $.ajax({
                type: "POST",
                url: "excluirBloco.php", // Substitua "sua_pagina.php" pelo caminho da sua página que vai lidar com a requisição AJAX
                data: { id: id }, // Envia o ID como parâmetro
                success: function (response) {
                    // Aqui você pode fazer algo com a resposta da requisição
                    location.reload();
                }
            });
        }
    </script>

    <script>
        function deletarDisciplina(id) {
            jQuery.ajax({
                type: "POST",
                url: "planejamento_deletar_componente.php",
                data: { componente: id },
                cache: true,
                success: function (data) {
                    location.reload();
                }
            });
        }

        function validateForm() {
            var selectedOption = document.getElementById("disciplinas").value;
            if (selectedOption == "SELECIONE...") {
                alert("Por favor, selecione uma disciplina.");
                return false; // Impede o envio do formulário
            }
            return true; // Permite o envio do formulário
        }
    </script>
    <script>
        $('#disciplinas').select2({
            width: '100%' // Definindo a largura como 100%
        });

        /* tinymce.init({
          selector: 'textarea',
    
          mobile: {
           menubar: false
         },
    
         images_upload_url: 'postAcceptor.php',
         automatic_uploads: true,
         imagetools_proxy: 'proxy.php',
    
       //plugins: 'emoticons',
       //toolbar: 'emoticons',
    
       //imagetools_toolbar: 'rotateleft rotateright | flipv fliph | editimage imageoptions',
    
         height: 200,
         toolbar: ['paste undo redo | formatselect | forecolor | bold italic backcolor | bullist numlist | image | emoticons'],
         plugins : ['textcolor','advlist autolink link image imagetools lists charmap print preview paste emoticons',
           'advlist autolink lists link image imagetools charmap print preview anchor',
           'searchreplace visualblocks code fullscreen',
           'insertdatetime media table paste code help wordcount'],
       //force_br_newlines : false,
       //force_p_newlines : false,
       //forced_root_block : '', 
         statusbar: false,
         language: 'pt_BR',
         menubar: false,
         paste_as_text: true,
         content_css: '//www.tinymce.com/css/codepen.min.css'
       });*/

    </script>

    <script type="application/javascript">

        /*
        Swal.fire({
          //position: 'top-end',
          icon: 'success',
          title: 'Tudo certo por aqui',
          showConfirmButton: false,
          timer: 1500
        })
        */
    </script>
</body>

</html>