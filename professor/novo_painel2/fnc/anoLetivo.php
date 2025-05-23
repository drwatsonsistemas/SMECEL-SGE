<?php 
try {
    // Query para buscar informações do ano letivo
    $query_AnoLetivo = "SELECT ano_letivo_id, ano_letivo_ano, ano_letivo_inicio, ano_letivo_fim, 
                        ano_letivo_aberto, ano_letivo_id_sec, ano_letivo_resultado_final 
                        FROM smc_ano_letivo 
                        WHERE ano_letivo_id_sec = :id_sec AND ano_letivo_aberto = 'S' 
                        ORDER BY ano_letivo_ano DESC 
                        LIMIT 1";

    $stmt = $SmecelNovo->prepare($query_AnoLetivo);
    $stmt->bindParam(':id_sec', $row_Vinculos['vinculo_id_sec']);
    $stmt->execute();

    $row_AnoLetivo = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar se alguma linha foi retornada
    $totalRows_AnoLetivo = $stmt->rowCount();

} catch (PDOException $e) {
    die("Erro ao buscar o ano letivo: " . $e->getMessage());
}
?>
