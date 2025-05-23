<?php
ob_start();
require_once('../../../Connections/SmecelNovoPDO.php');
header('Content-Type: application/json');

ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

// Função para recalcular o total
function recalcularTotal($db, $alunoId, $componente) {
    $notasQuery = "
        SELECT qq_id_periodo,
            SUM(CASE WHEN qq_tipo_criterio = 1 THEN qq_nota ELSE 0 END) as qualitativo,
            SUM(CASE WHEN qq_tipo_criterio = 2 THEN qq_nota ELSE 0 END) as quantitativo,
            SUM(CASE WHEN qq_tipo_criterio = 3 THEN qq_nota ELSE 0 END) as paralela,
            SUM(CASE WHEN qq_tipo_criterio = 4 THEN qq_nota ELSE 0 END) as recuperacao
        FROM smc_notas_qq 
        WHERE qq_id_matricula = :id AND qq_id_componente = :componente
        GROUP BY qq_id_periodo";
    $stmtNotas = $db->prepare($notasQuery);
    $stmtNotas->execute([':id' => $alunoId, ':componente' => $componente]);
    $notas = $stmtNotas->fetchAll(PDO::FETCH_ASSOC);

    $totaisPorPeriodo = [1 => 0, 2 => 0, 3 => 0];
    foreach ($notas as $nota) {
        $p = $nota['qq_id_periodo'];
        $qualitativo = floatval($nota['qualitativo']);
        $quantitativo = floatval($nota['quantitativo']);
        $paralela = floatval($nota['paralela']);
        $recuperacao = floatval($nota['recuperacao']);

        $totalTrimestre = $qualitativo + $quantitativo;
        if ($recuperacao > 0) {
            $totalTrimestre = $recuperacao;
        } elseif ($paralela > 0 && $paralela > $quantitativo) {
            $totalTrimestre = $qualitativo + $paralela;
        }
        $totaisPorPeriodo[$p] = round($totalTrimestre, 2);
    }
    return array_sum($totaisPorPeriodo);
}

try {
    if (empty($_POST)) {
        throw new Exception('Nenhum dado enviado.');
    }

    $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : '';
    $periodo = isset($_POST['periodo']) ? $_POST['periodo'] : '';
    $valor = isset($_POST['valor']) ? trim($_POST['valor']) : '';
    $alunoId = isset($_POST['alunoId']) ? $_POST['alunoId'] : '';
    $componente = isset($_POST['componente']) ? $_POST['componente'] : '';
    $pontuacaoMax = isset($_POST['pontuacaoMax']) ? $_POST['pontuacaoMax'] : '';

    $tipoMap = [
        'qualitativo' => ['tipo' => 1, 'criterio' => 12],
        'quantitativo' => ['tipo' => 2, 'criterio' => 6],
        'paralela' => ['tipo' => 3, 'criterio' => 99],
        'recuperacaoFinal' => ['tipo' => 4, 'criterio' => 98]
    ];

    $tipoData = isset($tipoMap[$tipo]) ? $tipoMap[$tipo] : null;
    if (!$tipoData) {
        throw new Exception('Tipo inválido.');
    }

    // Tratamento de exclusão: apenas se o valor for '' ou null
    if ($valor === '' || $valor === null) {
        $deleteStmt = $SmecelNovo->prepare("
            DELETE FROM smc_notas_qq
            WHERE qq_id_matricula = :id
            AND qq_id_componente = :componente
            AND qq_id_periodo = :periodo
            AND qq_tipo_criterio = :tipo
        ");
        $deleteStmt->execute([
            ':id' => $alunoId,
            ':componente' => $componente,
            ':periodo' => $periodo,
            ':tipo' => $tipoData['tipo']
        ]);
    
        echo json_encode([
            'success' => true,
            'message' => 'Nota removida com sucesso'
        ]);
        exit;
    }
    

    // Validação da nota: aceita 0 como válido
    if (!is_numeric($valor) || floatval($valor) < 0 || floatval($valor) > floatval($pontuacaoMax)) {
        ob_end_clean();
        echo json_encode([
            'success' => false,
            'message' => 'Nota inválida ou fora do intervalo permitido (0 a ' . $pontuacaoMax . ').'
        ]);
        exit;
    }

    // Excluir nota existente para o mesmo tipo
    $deleteStmt = $SmecelNovo->prepare(
        "DELETE FROM smc_notas_qq 
         WHERE qq_id_matricula = :id 
         AND qq_id_componente = :componente 
         AND qq_id_periodo = :periodo 
         AND qq_tipo_criterio = :tipo"
    );
    $deleteStmt->execute([
        ':id' => $alunoId,
        ':componente' => $componente,
        ':periodo' => $periodo,
        ':tipo' => $tipoData['tipo']
    ]);

    // Inserir a nova nota, incluindo 0
    $insertStmt = $SmecelNovo->prepare(
        "INSERT INTO smc_notas_qq (qq_id_matricula, qq_id_componente, qq_id_periodo, qq_tipo_criterio, qq_id_criterio, qq_nota)
         VALUES (:id, :componente, :periodo, :tipo, :criterio, :valor)"
    );
    $insertStmt->execute([
        ':id' => $alunoId,
        ':componente' => $componente,
        ':periodo' => $periodo,
        ':tipo' => $tipoData['tipo'],
        ':criterio' => $tipoData['criterio'],
        ':valor' => number_format(floatval($valor), 2, '.', '')
    ]);

    $total = recalcularTotal($SmecelNovo, $alunoId, $componente);
    $updateQuery = "UPDATE smc_vinculo_aluno SET vinculo_aluno_total_pontos_qq = :total WHERE vinculo_aluno_id = :id";
    $SmecelNovo->prepare($updateQuery)->execute([':total' => $total, ':id' => $alunoId]);

    ob_end_clean();
    echo json_encode([
        'success' => true,
        'total_pontos' => number_format($total, 2, '.', ''),
        'resultado_final' => ($total < 60) ? "<span style='color: red;'>REP</span>" : "<span style='color: green;'>APR</span>",
        'message' => 'Nota registrada com sucesso'
    ]);
} catch (Exception $e) {
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
exit;
?>