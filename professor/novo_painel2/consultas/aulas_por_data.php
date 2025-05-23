<?php
require_once('../../../Connections/SmecelNovoPDO.php');

$turma_id = isset($_GET['turma_id']) ? intval($_GET['turma_id']) : null;
$disciplina_id = isset($_GET['disciplina_id']) ? intval($_GET['disciplina_id']) : null;
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;

if ($turma_id === null || $disciplina_id === null) {
    echo json_encode(['error' => 'Parâmetros inválidos.']);
    exit;
}

$query_Aulas = "
    SELECT plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, plano_aula_texto, 
           plano_aula_data, plano_aula_data_cadastro, plano_aula_publicado, plano_aula_hash, COUNT(*) AS aulas_total 
    FROM smc_plano_aula
    WHERE plano_aula_id_turma = :turma_id AND plano_aula_id_disciplina = :disciplina_id
    GROUP BY plano_aula_data
    ORDER BY plano_aula_data DESC
    LIMIT :offset, :limit";

try {
    $stmtAulas = $SmecelNovo->prepare($query_Aulas);
    $stmtAulas->bindValue(':turma_id', $turma_id, PDO::PARAM_INT);
    $stmtAulas->bindValue(':disciplina_id', $disciplina_id, PDO::PARAM_INT);
    $stmtAulas->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmtAulas->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmtAulas->execute();

    $row_Aulas = $stmtAulas->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($row_Aulas);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Erro ao executar a consulta: ' . $e->getMessage()]);
    exit;
}
