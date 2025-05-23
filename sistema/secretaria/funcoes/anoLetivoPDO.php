<?php
try {
	// Consulta para buscar o ano letivo
	$stmtAnoLetivo = $SmecelNovo->prepare("
		SELECT 
			ano_letivo_id, 
			ano_letivo_ano, 
			ano_letivo_inicio, 
			ano_letivo_fim, 
			ano_letivo_aberto, 
			ano_letivo_id_sec, 
			ano_letivo_data_rematricula 
		FROM smc_ano_letivo 
		WHERE ano_letivo_id_sec = :sec AND ano_letivo_aberto = 'S' 
		ORDER BY ano_letivo_ano DESC 
		LIMIT 1
	");
	$stmtAnoLetivo->bindParam(':sec', $row_UsuarioLogado['usu_sec'], PDO::PARAM_INT);
	$stmtAnoLetivo->execute();
	$row_AnoLetivo = $stmtAnoLetivo->fetch(PDO::FETCH_ASSOC);
	$totalRows_AnoLetivo = $stmtAnoLetivo->rowCount();

	// Verificar se nenhum ano letivo foi encontrado
	if ($totalRows_AnoLetivo < 1) {
		$redireciona = "anoletivonovo.php";
		header(sprintf("Location: %s", $redireciona));
		exit;
	}
} catch (PDOException $e) {
	// Manipular erros de consulta
	die("Erro ao buscar ano letivo: " . $e->getMessage());
}
?>
