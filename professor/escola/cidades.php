<?php
// Inclua a conexão com o banco de dados
require('../../Connections/SmecelNovo.php');

if (!isset($_GET['estado']) || empty($_GET['estado'])) {
    echo '<option value="">Estado inválido</option>';
    exit;
}


$estado = mysql_real_escape_string($_GET['estado']); // Escape para evitar SQL Injection

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ListaCidades = "SELECT municipio_id, municipio_cod_ibge, municipio_nome, municipio_sigla_uf FROM smc_municipio WHERE municipio_sigla_uf = '$estado' ORDER BY municipio_nome ASC";
$result = mysql_query($query_ListaCidades);



if (!$result) {
    echo '<option value="">Erro na consulta</option>';
    exit;
}

if (mysql_num_rows($result) > 0) {
	echo "<option value=''></option>";
    while ($row = mysql_fetch_assoc($result)) {
        echo "<option value='" . htmlspecialchars($row['municipio_cod_ibge']) . "'>" . htmlspecialchars($row['municipio_nome']) . "</option>";
    }
} else {
    echo '<option value="">Nenhuma cidade encontrada</option>';
}
?>