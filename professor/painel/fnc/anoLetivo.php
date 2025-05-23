<?php 
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AnoLetivo = "SELECT ano_letivo_id, ano_letivo_ano, ano_letivo_inicio, ano_letivo_fim, ano_letivo_aberto, ano_letivo_id_sec FROM smc_ano_letivo WHERE ano_letivo_id_sec = '$row_Vinculos[vinculo_id_sec]' AND ano_letivo_aberto = 'S' ORDER BY ano_letivo_ano DESC LIMIT 1";
$AnoLetivo = mysql_query($query_AnoLetivo, $SmecelNovo) or die(mysql_error());
$row_AnoLetivo = mysql_fetch_assoc($AnoLetivo);
$totalRows_AnoLetivo = mysql_num_rows($AnoLetivo);
?>