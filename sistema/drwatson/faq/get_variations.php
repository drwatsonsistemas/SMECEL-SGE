<?php
require_once('../../../Connections/SmecelNovo.php');

$id_resposta = intval($_GET['id_resposta']);

mysql_select_db($database_SmecelNovo, $SmecelNovo);

$query_Variations = sprintf("SELECT * FROM smc_faq_variacoes_perguntas WHERE id_resposta = %d", $id_resposta);
$result = mysql_query($query_Variations, $SmecelNovo) or die(mysql_error());

$variations = [];

while ($row = mysql_fetch_assoc($result)) {
    $variations[] = $row;
}

header('Content-Type: application/json');
echo json_encode($variations);
?>