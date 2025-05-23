<?php
try {
    // Consulta Ano Letivo
    $stmtAnoLetivo = $SmecelNovo->prepare("
        SELECT ano_letivo_id, ano_letivo_ano, ano_letivo_inicio, ano_letivo_fim, ano_letivo_aberto, ano_letivo_id_sec, 
        ano_letivo_data_rematricula, ano_letivo_resultado_final, ano_letivo_mat_inicial, ano_letivo_mat_final 
        FROM smc_ano_letivo 
        WHERE ano_letivo_aberto = :aberto AND ano_letivo_id_sec = :sec_id 
        ORDER BY ano_letivo_ano DESC 
        LIMIT 1
    ");
    $stmtAnoLetivo->execute([
        ':aberto' => 'S',
        ':sec_id' => $row_UsuLogado['usu_sec']
    ]);
    $row_AnoLetivo = $stmtAnoLetivo->fetch(PDO::FETCH_ASSOC);
    $totalRows_AnoLetivo = $stmtAnoLetivo->rowCount();
} catch (PDOException $e) {
    die("Erro ao consultar o Ano Letivo: " . $e->getMessage());
}
?>
