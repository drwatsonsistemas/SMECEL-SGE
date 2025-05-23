<?php
ob_start();
require_once('../../../Connections/SmecelNovoPDO.php');
header('Content-Type: application/json');

ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

try {
    if (empty($_POST)) {
        throw new Exception('Nenhum dado enviado.');
    }

    $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : '';
    $periodo = isset($_POST['periodo']) ? $_POST['periodo'] : '';
    $valor = isset($_POST['valor']) ? $_POST['valor'] : '';
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

    if (!is_numeric($valor) || $valor > $pontuacaoMax) {
        echo json_encode([
            'success' => false,
            'message' => 'Nota inválida ou acima do máximo permitido (' . $pontuacaoMax . ').'
        ]);
        ob_end_clean();
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

    // Inserir a nova nota
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
        ':valor' => $valor ? number_format($valor, 2, '.', '') : null
    ]);

    // Consultar todas as notas do aluno para todos os períodos
    $notasQuery = "
        SELECT qq_id_periodo,
            SUM(CASE WHEN qq_tipo_criterio = 1 THEN qq_nota ELSE 0 END) as qualitativo,
            SUM(CASE WHEN qq_tipo_criterio = 2 THEN qq_nota ELSE 0 END) as quantitativo,
            SUM(CASE WHEN qq_tipo_criterio = 3 THEN qq_nota ELSE 0 END) as paralela,
            SUM(CASE WHEN qq_tipo_criterio = 4 THEN qq_nota ELSE 0 END) as recuperacao
        FROM smc_notas_qq 
        WHERE qq_id_matricula = :id AND qq_id_componente = :componente
        GROUP BY qq_id_periodo";
    $stmtNotas = $SmecelNovo->prepare($notasQuery);
    $stmtNotas->execute([':id' => $alunoId, ':componente' => $componente]);
    $notas = $stmtNotas->fetchAll(PDO::FETCH_ASSOC);

    // Inicializar totais por período
    $totaisPorPeriodo = [1 => 0, 2 => 0, 3 => 0];
    $reprovadoEmAlgumPeriodo = false;

    foreach ($notas as $nota) {
        $p = $nota['qq_id_periodo'];
        $qualitativo = floatval($nota['qualitativo']);
        $quantitativo = floatval($nota['quantitativo']);
        $paralela = floatval($nota['paralela']);
        $recuperacao = floatval($nota['recuperacao']);

        // Definir limites por período
        $minQuantitativo = ($p == 3) ? 10.8 : 8.1;
        $minTrimestre = ($p == 3) ? 24 : 18;

        // Calcular total do trimestre
        $totalTrimestre = $qualitativo + $quantitativo;
        if ($recuperacao > 0) {
            $totalTrimestre = $recuperacao;
        } elseif ($paralela > 0 && $paralela > $quantitativo) {
            $totalTrimestre = $qualitativo + $paralela;
        }
        $totalTrimestre = round($totalTrimestre, 2); // Simula arredondarNota

        $totaisPorPeriodo[$p] = $totalTrimestre;

        // Verificar reprovação no período
        if ($totalTrimestre < $minTrimestre) {
            $reprovadoEmAlgumPeriodo = true;
        }
    }

    // Calcular total anual
    $total = array_sum($totaisPorPeriodo);

    // Atualizar total no banco
    $updateQuery = "UPDATE smc_vinculo_aluno SET vinculo_aluno_total_pontos_qq = :total WHERE vinculo_aluno_id = :id";
    $SmecelNovo->prepare($updateQuery)->execute([':total' => $total, ':id' => $alunoId]);

    // Lógica para o período atual
	$indice = array_search($periodo, array_column($notas, 'qq_id_periodo'));
	$qualitativo = ($indice !== false && isset($notas[$indice]['qualitativo'])) ? floatval($notas[$indice]['qualitativo']) : 0;
	$quantitativo = ($indice !== false && isset($notas[$indice]['quantitativo'])) ? floatval($notas[$indice]['quantitativo']) : 0;
	$paralela = ($indice !== false && isset($notas[$indice]['paralela'])) ? floatval($notas[$indice]['paralela']) : 0;
	$recuperacao = ($indice !== false && isset($notas[$indice]['recuperacao'])) ? floatval($notas[$indice]['recuperacao']) : 0;

    $minQuantitativo = ($periodo == 3) ? 10.8 : 8.1;
    $minTrimestre = ($periodo == 3) ? 24 : 18;

    $totalTrimestre = $qualitativo + $quantitativo;
    $status = '';
    $liberarParalela = false;
    $liberarRecuperacao = false;

    if ($recuperacao > 0) {
        $totalTrimestre = $recuperacao;
        $status = ($totalTrimestre < $minTrimestre) ? 'nota-paralela-rep' : 'nota-apr';
    } else {
        if ($paralela > 0 && $paralela > $quantitativo) {
            $totalTrimestre = $qualitativo + $paralela;
            $status = ($totalTrimestre >= $minTrimestre) ? 'nota-paralela-apr' : 'nota-paralela-rep';
            if ($totalTrimestre < $minTrimestre && $total < 60) {
                $liberarRecuperacao = true;
            }
        } else {
            if ($quantitativo < $minQuantitativo && $totalTrimestre < $minTrimestre) {
                $status = 'nota-paralela';
                $liberarParalela = true;
            } elseif ($quantitativo >= $minQuantitativo && $totalTrimestre >= $minTrimestre) {
                $status = 'nota-apr';
            } else {
                $status = 'nota-paralela-rep';
                $liberarRecuperacao = ($total < 60); // Só liberar recuperação se o total anual for insuficiente
            }
        }
    }

    // Resposta JSON
    ob_end_clean();
    echo json_encode([
        'success' => true,
        'total_pontos' => number_format($total, 2, '.', ''),
        'resultado_final' => ($total < 60) ? "<span style='color: red;'>REP</span>" : "<span style='color: green;'>APR</span>",
        'status' => $status,
        'liberarParalela' => $liberarParalela,
        'liberarRecuperacao' => $liberarRecuperacao && $reprovadoEmAlgumPeriodo
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