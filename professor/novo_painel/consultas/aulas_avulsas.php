<?php
ob_start();
require_once('../../../Connections/SmecelNovoPDO.php');
header('Content-Type: application/json; charset=utf-8');

if (isset($_GET['ano_letivo']) && isset($_GET['escola_id'])) {
    $anoLetivo = $_GET['ano_letivo'];
    $escolaId = $_GET['escola_id'];

    try {
        $stmtTurmas = $SmecelNovo->prepare("
            SELECT turma_id, turma_nome, turma_turno
            FROM smc_turma
            WHERE turma_ano_letivo = :ano_letivo AND turma_id_escola = :escola_id
            ORDER BY turma_turno, turma_nome
        ");
        $stmtTurmas->bindValue(':ano_letivo', $anoLetivo, PDO::PARAM_STR);
        $stmtTurmas->bindValue(':escola_id', $escolaId, PDO::PARAM_INT);
        $stmtTurmas->execute();
        $turmas = $stmtTurmas->fetchAll(PDO::FETCH_ASSOC);

        // Verifica se $turmas tem dados
        if (empty($turmas)) {
            error_log("Nenhuma turma encontrada para ano_letivo=$anoLetivo, escola_id=$escolaId");
            echo json_encode([]);
        } else {
            error_log("Turmas encontradas: " . count($turmas));
            // Força codificação UTF-8 nos dados
            $turmas = array_map(function($turma) {
                return array_map('utf8_encode', $turma);
            }, $turmas);
            $jsonOutput = json_encode($turmas);
            if ($jsonOutput === false) {
                error_log("Erro no json_encode: " . json_last_error_msg());
                echo json_encode(['error' => 'Erro ao gerar JSON: ' . json_last_error_msg()]);
            } else {
                error_log("JSON gerado com sucesso: " . substr($jsonOutput, 0, 100));
                echo $jsonOutput;
            }
        }
    } catch (PDOException $e) {
        error_log("Erro PDO: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Parâmetros ano_letivo e escola_id são obrigatórios']);
}

ob_end_flush();
exit;