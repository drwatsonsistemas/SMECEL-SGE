<?php
require_once('../../../Connections/SmecelNovoPDO.php');

// Define o tipo de conteúdo como JSON
header('Content-Type: application/json; charset=utf-8');

try {
    // Obtém e valida os parâmetros
    $escola_id = isset($_GET['escola_id']) ? $_GET['escola_id'] : null;
    $professor_id = isset($_GET['professor_id']) ? $_GET['professor_id'] : null;
    $ano_letivo = isset($_GET['ano_letivo']) ? $_GET['ano_letivo'] : null;
    // Verifica se todos os parâmetros necessários foram fornecidos
    if (!$escola_id || !$professor_id || !$ano_letivo) {
        throw new Exception('Parâmetros obrigatórios não fornecidos');
    }

    // Query para buscar as turmas
    $query_turmas = "
        SELECT 
            turma_id, 
            turma_nome, 
            turma_turno, 
            turma_ano_letivo,
            CASE turma_turno
                WHEN 0 THEN 'INTEGRAL'
                WHEN 1 THEN 'MATUTINO'
                WHEN 2 THEN 'VESPERTINO'
                WHEN 3 THEN 'NOTURNO'
            END AS turma_turno_nome
        FROM smc_ch_lotacao_professor
        INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
        WHERE turma_ano_letivo = :ano_letivo 
        AND ch_lotacao_escola = :escola 
        AND ch_lotacao_professor_id = :professor_id
        GROUP BY turma_id
        ORDER BY turma_turno, turma_nome ASC";

    // Prepara e executa a query
    $stmt = $SmecelNovo->prepare($query_turmas);
    $stmt->execute([
        ':ano_letivo' => $ano_letivo,
        ':escola' => $escola_id,
        ':professor_id' => $professor_id
    ]);

    // Obtém os resultados
    $turmas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    function limparCaracteres($str)
    {
        return mb_convert_encoding($str, 'UTF-8', 'UTF-8');
    }
    array_walk_recursive($turmas, function (&$item) {
        if (is_string($item)) {
            $item = limparCaracteres($item);
        }
    });
    echo json_encode($turmas, JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    // Erro de banco de dados
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => 'Erro ao consultar o banco de dados: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    // Outros erros
    http_response_code(400);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
?>