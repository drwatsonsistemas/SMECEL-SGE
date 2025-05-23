<?php 

$hostname_SmecelNovo = "localhost";
$database_SmecelNovo = "smecel1";
$username = "root";
$password = "";

// Conexão com o banco de dados
$connection = mysql_connect($hostname_SmecelNovo, $username, $password);
mysql_select_db($database_SmecelNovo, $connection);

$offset = 0;
$limit = 1000; // Quantidade de registros por lote

do {
    $query = "SELECT * FROM smc_turma LIMIT $offset, $limit";
    $result = mysql_query($query, $connection);

    while ($row = mysql_fetch_assoc($result)) {
        // Processar os dados aqui
        echo $row['nome_da_coluna'] . "\n";
    }

    $offset += $limit; // Incrementa o offset para o próximo lote
} while (mysql_num_rows($result) > 0);

mysql_free_result($result);

?>
