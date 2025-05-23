<?php
// Evite espaços ou linhas antes do <?php
require_once('../../../Connections/SmecelNovoPDO.php');

// Ative a exibição de erros para depuração (remova em produção)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Capture os parâmetros GET
$turma_id = isset($_GET['turma_id']) ? $_GET['turma_id'] : null;
$professor_id = isset($_GET['professor_id']) ? $_GET['professor_id'] : null;
$ano_letivo = isset($_GET['ano_letivo']) ? $_GET['ano_letivo'] : null;

// Verifique se os parâmetros estão presentes
if (!$turma_id || !$professor_id || !$ano_letivo) {
    header('Content-Type: application/json');
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Parâmetros obrigatórios ausentes']);
    exit;
}

try {
    $query_componente = "
        SELECT disciplina_id, disciplina_nome, turma_id, turma_ano_letivo
        FROM smc_ch_lotacao_professor
        INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
        INNER JOIN smc_disciplina ON disciplina_id = ch_lotacao_disciplina_id
        WHERE turma_ano_letivo = :ano_letivo AND ch_lotacao_turma_id = :turma AND ch_lotacao_professor_id = :professor_id
        GROUP BY disciplina_id
        ORDER BY disciplina_nome ASC";

    $stmt = $SmecelNovo->prepare($query_componente);
    $stmt->execute([
        ':ano_letivo' => $ano_letivo,
        ':turma' => $turma_id,
        ':professor_id' => $professor_id
    ]);
    $componentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Defina o cabeçalho e retorne o JSON
    header('Content-Type: application/json');
    echo json_encode($componentes);
} catch (PDOException $e) {
    // Em caso de erro no banco, retorne uma resposta JSON com o erro
    header('Content-Type: application/json');
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Erro no banco de dados: ' . $e->getMessage()]);
    exit;
}