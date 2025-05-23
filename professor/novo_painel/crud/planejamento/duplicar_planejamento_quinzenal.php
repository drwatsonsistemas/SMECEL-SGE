<?php
require_once('../../../../Connections/SmecelNovoPDO.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plan_id = $_POST['plan_id'];
    $professor_id = $_POST['professor_id'];
    $escola_id = $_POST['escola_id'];
    $turma_id = $_POST['turma_id'];
    $componente_id = $_POST['componente_id'];
    $observacao = $_POST['observacao'];
    $data_inicial = $_POST['data_inicial'];
    $data_final = $_POST['data_final'];

    // Log temporário para depuração
    error_log("Dados recebidos - plan_id: $plan_id, professor_id: $professor_id, escola_id: $escola_id, turma_id: $turma_id, componente_id: $componente_id, observacao: $observacao, data_inicial: $data_inicial, data_final: $data_final");

    // Verificar se já existe um planejamento para essa turma e componente no mesmo período
    $stmtCheck = $SmecelNovo->prepare("
        SELECT COUNT(*) 
        FROM smc_planejamento 
        WHERE smc_id_turma = :turma_id 
        AND smc_id_componente = :componente_id 
        AND smc_id_planejamento != :plan_id
        AND (
            (smc_planejamento_data_inicial BETWEEN :data_inicial AND :data_final)
            OR (smc_planejamento_data_final BETWEEN :data_inicial AND :data_final)
            OR (:data_inicial BETWEEN smc_planejamento_data_inicial AND smc_planejamento_data_final)
        )
    ");
    $stmtCheck->execute([
        ':turma_id' => $turma_id,
        ':componente_id' => $componente_id,
        ':plan_id' => $plan_id,
        ':data_inicial' => $data_inicial,
        ':data_final' => $data_final
    ]);

    if ($stmtCheck->fetchColumn() > 0) {
        die("Erro: Já existe um planejamento para essa turma e componente no mesmo período de datas.");
    }

    // Pegar os dados do planejamento original
    $stmtOriginal = $SmecelNovo->prepare("SELECT * FROM smc_planejamento WHERE smc_id_planejamento = :plan_id");
    $stmtOriginal->execute([':plan_id' => $plan_id]);
    $original = $stmtOriginal->fetch(PDO::FETCH_ASSOC);

    // Adicionar campo de observação ao planejamento original, se preenchido
    if (!empty($observacao)) {
        $stmtUpdateObs = $SmecelNovo->prepare("
            UPDATE smc_planejamento 
            SET observacao = :observacao 
            WHERE smc_id_planejamento = :plan_id
        ");
        $stmtUpdateObs->execute([
            ':observacao' => $observacao,
            ':plan_id' => $plan_id
        ]);
    }

    // Inserir o novo planejamento com todos os campos relevantes
    $stmtInsert = $SmecelNovo->prepare("
        INSERT INTO smc_planejamento (
            smc_id_professor, smc_id_escola, smc_id_turma, smc_id_periodo, smc_id_componente, smc_id_tema_integrador,
            smc_obj_conhecimento_conteudos, smc_habilidades, smc_avaliacao_personalizada, smc_ano_letivo,
            smc_planejamento_data_inicial, smc_planejamento_data_final, 
            ac_da_conviver, ac_da_brincar, ac_da_participar, ac_da_explorar, ac_da_expressar, ac_da_conhecerse,
            obj_aprendizagem_desenvolvimento, aprendizagens_saberes, metodologia, recursos, avaliacao,
            smc_metodologia_personalilzada, smc_atividades_permanentes, planejamento_status, smc_planejamento_correcao,
            smc_feedback, estrategia_metodologica_personalizada, avaliacao_edinf_outras, smc_atv_dev_tema_integrador,
            observacao
        ) VALUES (
            :professor_id, :escola_id, :turma_id, :periodo, :componente_id, :tema_integrador,
            :obj_conhecimento_conteudos, :habilidades, :avaliacao_personalizada, :ano_letivo,
            :data_inicial, :data_final, 
            :da_conviver, :da_brincar, :da_participar, :da_explorar, :da_expressar, :da_conhecerse,
            :obj_aprendizagem, :aprendizagens_saberes, :metodologia, :recursos, :avaliacao,
            :metodologia_personalizada, :atv_permanentes, :status, :correcao,
            :feedback, :estrategia_metodologica, :avaliacao_edinf_outras, :atv_dev_tema_integrador,
            :observacao
        )
    ");
    $stmtInsert->execute([
        ':professor_id' => $professor_id,
        ':escola_id' => $escola_id,
        ':turma_id' => $turma_id,
        ':periodo' => $original['smc_id_periodo'],
        ':componente_id' => $componente_id,
        ':tema_integrador' => $original['smc_id_tema_integrador'],
        ':obj_conhecimento_conteudos' => $original['smc_obj_conhecimento_conteudos'],
        ':habilidades' => $original['smc_habilidades'],
        ':avaliacao_personalizada' => $original['smc_avaliacao_personalizada'],
        ':ano_letivo' => $original['smc_ano_letivo'],
        ':data_inicial' => $data_inicial,
        ':data_final' => $data_final,
        ':da_conviver' => $original['ac_da_conviver'],
        ':da_brincar' => $original['ac_da_brincar'],
        ':da_participar' => $original['ac_da_participar'],
        ':da_explorar' => $original['ac_da_explorar'],
        ':da_expressar' => $original['ac_da_expressar'],
        ':da_conhecerse' => $original['ac_da_conhecerse'],
        ':obj_aprendizagem' => $original['obj_aprendizagem_desenvolvimento'],
        ':aprendizagens_saberes' => $original['aprendizagens_saberes'],
        ':metodologia' => $original['metodologia'],
        ':recursos' => $original['recursos'],
        ':avaliacao' => $original['avaliacao'],
        ':metodologia_personalizada' => $original['smc_metodologia_personalilzada'],
        ':atv_permanentes' => $original['smc_atividades_permanentes'],
        ':status' => $original['planejamento_status'],
        ':correcao' => $original['smc_planejamento_correcao'],
        ':feedback' => $original['smc_feedback'],
        ':estrategia_metodologica' => $original['estrategia_metodologica_personalizada'],
        ':avaliacao_edinf_outras' => $original['avaliacao_edinf_outras'],
        ':atv_dev_tema_integrador' => $original['smc_atv_dev_tema_integrador'],
        ':observacao' => $observacao
    ]);

    $novo_plan_id = $SmecelNovo->lastInsertId();

    // Duplicar relacionamentos (temas, metodologias, etc.)
    $tabelas_relacionamento = [
        'smc_plan_temas_relacionamento' => 'smc_id_tema',
        'smc_plan_metod_relacionamento' => 'smc_id_metodologia',
        'smc_plan_avaliacaoedinf_relacionamento' => 'smc_id_avaliacaoedinf',
        'smc_plan_estrate_relacionamentos' => 'smc_id_estrate_metod',
        'smc_plan_ava_relacionamento' => 'smc_id_avaliacao'
    ];

    foreach ($tabelas_relacionamento as $tabela => $coluna) {
        $stmtSelectRel = $SmecelNovo->prepare("SELECT $coluna FROM $tabela WHERE smc_id_planejamento = :plan_id");
        $stmtSelectRel->execute([':plan_id' => $plan_id]);
        $relacionamentos = $stmtSelectRel->fetchAll(PDO::FETCH_ASSOC);

        $stmtInsertRel = $SmecelNovo->prepare("INSERT INTO $tabela (smc_id_planejamento, $coluna) VALUES (:novo_plan_id, :valor)");
        foreach ($relacionamentos as $rel) {
            $stmtInsertRel->execute([
                ':novo_plan_id' => $novo_plan_id,
                ':valor' => $rel[$coluna]
            ]);
        }
    }

    header("Location: ../../planejamento_ver.php?duplicado=" . time());
    exit;
}
?>