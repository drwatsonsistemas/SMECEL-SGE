<?php
function arredondarNota($nota) {
    $decimal = round($nota - floor($nota), 2); // Arredonda para 2 casas decimais

    if ($decimal >= 0.75) {
        return ceil($nota);
    } elseif ($decimal >= 0.5 && $decimal < 0.75) {
        return floor($nota) + 0.5;
    } elseif ($decimal >= 0.3 && $decimal < 0.5) {
        return floor($nota) + 0.5;
    } else {
        return floor($nota);
    }
}


function obterNota($conexao, $alunoId, $componenteId, $periodo, $tipoCriterio) {
    $query = "
        SELECT qq_nota 
        FROM smc_notas_qq 
        WHERE qq_id_matricula = :alunoId 
          AND qq_id_componente = :componenteId 
          AND qq_id_periodo = :periodo 
          AND qq_tipo_criterio = :tipoCriterio
    ";
  
    $stmt = $conexao->prepare($query);
    $stmt->bindParam(':alunoId', $alunoId, PDO::PARAM_INT);
    $stmt->bindParam(':componenteId', $componenteId, PDO::PARAM_INT);
    $stmt->bindParam(':periodo', $periodo, PDO::PARAM_INT);
    $stmt->bindParam(':tipoCriterio', $tipoCriterio, PDO::PARAM_INT);
    $stmt->execute();
    
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalRows = $stmt->rowCount();
  
    return [
        'nota' => isset($row['qq_nota']) ? floatval($row['qq_nota']) : 0,
        'totalRows' => $totalRows
    ];
  }
  
  
  function definirLimitesPeriodo($periodo) {
    $limites = [
        1 => ['quantitativa' => 13.5, 'qualitativa' => 16.5, 'paralela' => 13.5],
        2 => ['quantitativa' => 13.5, 'qualitativa' => 16.5, 'paralela' => 13.5],
        3 => ['quantitativa' => 18.0, 'qualitativa' => 22.0, 'paralela' => 18.0],
    ];
    
    if (!isset($limites[$periodo])) {
        return ['quantitativa' => 0, 'qualitativa' => 0, 'paralela' => 0];
    }
    
    $limite = $limites[$periodo];
    $limite['recuperacao'] = $limite['quantitativa'] + $limite['qualitativa'];
    
    return $limite;
  }
  
  function avaliarDesempenhoAluno($somaQuantitativo, $somaQualitativo, $paralela, $recuperacao, $totalRowsRecuperacao, $totalRowsParalela, $periodo) {
    // Soma total do trimestre
    $totalTrimestre = $somaQuantitativo + $somaQualitativo;

    // Definir os limites por trimestre
    if ($periodo == 3) {
        $limiteQuantitativo = 10.8;
        $limiteTotalTrimestre = 24;
    } else {
        $limiteQuantitativo = 8.1;
        $limiteTotalTrimestre = 18;
    }

    // Verifica se o aluno deve fazer paralela
    $precisaParalela = ($somaQuantitativo < $limiteQuantitativo && $totalTrimestre < $limiteTotalTrimestre);

    // Se já existe recuperação, usa essa nota
    if ($totalRowsRecuperacao > 0) {
        $totalTrimestre = $recuperacao;
    }

    // Se tem direito à paralela e ela foi lançada, verifica se a nota melhora
    if ($totalRowsParalela > 0 && $precisaParalela) {
        $totalTrimestre = max($totalTrimestre, $somaQualitativo + $paralela);
    }

    // Arredondamento da nota final
    $totalTrimestre = arredondarNota($totalTrimestre);

    // Definição de status do aluno
    return [
        'aprovado' => ($totalTrimestre >= $limiteTotalTrimestre),
        'paralela' => $precisaParalela, // Só será true se o aluno precisar
        'reprovado' => (!$precisaParalela && $totalTrimestre < $limiteTotalTrimestre), // Se não está na paralela e não atingiu a nota mínima, reprovado
        'total' => $totalTrimestre
    ];
}

?>