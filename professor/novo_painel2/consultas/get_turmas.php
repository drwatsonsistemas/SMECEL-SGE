<?php
require_once('../../../Connections/SmecelNovoPDO.php');

$escola_id = $_GET['escola_id'];
$professor_id = $_GET['professor_id'];
$ano_letivo = $_GET['ano_letivo'];

$query_turmas = "
    SELECT turma_id, turma_nome, turma_turno, turma_ano_letivo,
        CASE turma_turno
            WHEN 0 THEN 'INTEGRAL'
            WHEN 1 THEN 'MATUTINO'
            WHEN 2 THEN 'VESPERTINO'
            WHEN 3 THEN 'NOTURNO'
        END AS turma_turno_nome
    FROM smc_ch_lotacao_professor
    INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
    WHERE turma_ano_letivo = :ano_letivo AND ch_lotacao_escola = :escola AND ch_lotacao_professor_id = :professor_id
    GROUP BY turma_id
    ORDER BY turma_turno, turma_nome ASC";

$stmt = $SmecelNovo->prepare($query_turmas);
$stmt->execute([
    ':ano_letivo' => $ano_letivo,
    ':escola' => $escola_id,
    ':professor_id' => $professor_id
]);
$turmas = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($turmas);
?>