<?php
require_once('../../Connections/SmecelNovoPDO.php');

// Parâmetros
$disciplina_id = 6; // ID da disciplina de computação
$vinculo_hash = $_GET['vinculo_hash']; // Hash do vínculo do aluno

try {
    // Primeiro, vamos buscar as informações do vínculo
    $query_vinculo = "
        SELECT va.vinculo_aluno_id, va.vinculo_aluno_id_turma, t.turma_matriz_id, m.matriz_criterio_avaliativo
        FROM smc_vinculo_aluno va
        INNER JOIN smc_turma t ON t.turma_id = va.vinculo_aluno_id_turma
        INNER JOIN smc_matriz m ON m.matriz_id = t.turma_matriz_id
        WHERE va.vinculo_aluno_hash = :vinculo_hash";
    
    $stmt_vinculo = $SmecelNovo->prepare($query_vinculo);
    $stmt_vinculo->execute([':vinculo_hash' => $vinculo_hash]);
    $vinculo = $stmt_vinculo->fetch(PDO::FETCH_ASSOC);

    if (!$vinculo) {
        echo "Vínculo não encontrado.";
        exit;
    }

    // Buscar critérios avaliativos
    $query_criterios = "
        SELECT ca_id, ca_qtd_periodos, ca_grupo_etario 
        FROM smc_criterios_avaliativos 
        WHERE ca_id = :ca_id";
    
    $stmt_criterios = $SmecelNovo->prepare($query_criterios);
    $stmt_criterios->execute([':ca_id' => $vinculo['matriz_criterio_avaliativo']]);
    $criterios = $stmt_criterios->fetch(PDO::FETCH_ASSOC);

    // Buscar campos de experiência
    $query_campos = "
        SELECT * 
        FROM smc_disciplina 
        WHERE disciplina_id = :disciplina_id";
    
    $stmt_campos = $SmecelNovo->prepare($query_campos);
    $stmt_campos->execute([':disciplina_id' => $disciplina_id]);
    $campos_exp_id = $stmt_campos->fetchColumn();

    // Buscar objetivos de aprendizagem
    $query_objetivos = "
        SELECT campos_exp_obj_id 
        FROM smc_campos_exp_objetivos 
        WHERE campos_exp_obj_id_campos_exp = :campos_exp_id 
        AND campos_exp_obj_faixa_et_cod = :grupo_etario";
    
    $stmt_objetivos = $SmecelNovo->prepare($query_objetivos);
    $stmt_objetivos->execute([
        ':campos_exp_id' => $campos_exp_id,
        ':grupo_etario' => $criterios['ca_grupo_etario']
    ]);
    $objetivos = $stmt_objetivos->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($objetivos)) {
        // Buscar acompanhamentos
        $query_acompanhamentos = "
            SELECT acomp_id 
            FROM smc_acomp_proc_aprend 
            WHERE acomp_id_obj_aprend IN (" . implode(',', $objetivos) . ")
            AND acomp_id_matriz = :matriz_id";
        
        $stmt_acompanhamentos = $SmecelNovo->prepare($query_acompanhamentos);
        $stmt_acompanhamentos->execute([':matriz_id' => $vinculo['turma_matriz_id']]);
        $acompanhamentos = $stmt_acompanhamentos->fetchAll(PDO::FETCH_COLUMN);

        if (!empty($acompanhamentos)) {
            // Primeiro deletamos os conceitos existentes
            $query_delete = "
                DELETE FROM smc_conceito_aluno 
                WHERE conc_matricula_id = :aluno_id 
                AND conc_acomp_id IN (" . implode(',', $acompanhamentos) . ")";
            
            $stmt_delete = $SmecelNovo->prepare($query_delete);
            $stmt_delete->execute([':aluno_id' => $vinculo['vinculo_aluno_id']]);

            // Inserimos os novos registros para cada período
            $query_insert = "
                INSERT INTO smc_conceito_aluno (conc_acomp_id, conc_matricula_id, conc_periodo) 
                VALUES (:acomp_id, :aluno_id, :periodo)";
            
            $stmt_insert = $SmecelNovo->prepare($query_insert);

            foreach ($acompanhamentos as $acomp_id) {
                for ($periodo = 1; $periodo <= $criterios['ca_qtd_periodos']; $periodo++) {
                    $stmt_insert->execute([
                        ':acomp_id' => $acomp_id,
                        ':aluno_id' => $vinculo['vinculo_aluno_id'],
                        ':periodo' => $periodo
                    ]);
                }
            }
            
            echo "Componente resetado e conceitos gerados com sucesso!";
        } else {
            echo "Nenhum acompanhamento encontrado para este componente.";
        }
    } else {
        echo "Nenhum objetivo de aprendizagem encontrado para este componente.";
    }

} catch (PDOException $e) {
    echo "Erro ao resetar componente: " . $e->getMessage();
}
?>