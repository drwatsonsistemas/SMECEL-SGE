<?php
$host = "186.202.152.242"; // ou "localhost"
$port = 3306; // Porta padrão do MySQL
$dbname = "smecel1"; // Substitua pelo nome do banco
$username = "smecel1"; // Substitua pelo usuário do MySQL
$password = "Drw4atson@smec"; // Insira sua senha aqui

// Conexão com o MySQL
$conn = mysql_connect("$host:$port", $username, $password);
if (!$conn) {
    die("<h3>Erro ao conectar ao MySQL: " . htmlspecialchars(mysql_error()) . "</h3>");
}

echo "<h3>Conexão bem-sucedida com o MySQL!</h3>";

// Selecionar o banco de dados
if (!mysql_select_db($dbname, $conn)) {
    die("<h3>Erro ao selecionar o banco de dados: " . htmlspecialchars(mysql_error()) . "</h3>");
}

// Configurar o charset da conexão para utf8mb4
mysql_query("SET NAMES 'utf8mb4'", $conn);
mysql_query("SET CHARACTER SET utf8mb4", $conn);
mysql_query("SET SESSION collation_connection = 'utf8mb4_general_ci'", $conn);

// Testar uma query simples
$query = "SELECT * FROM smc_ac LIMIT 1"; // Substitua "sua_tabela" por uma tabela existente
$result = mysql_query($query, $conn);

if (!$result) {
    die("<h3>Erro ao executar a query: " . htmlspecialchars(mysql_error()) . "</h3>");
}

$data = mysql_fetch_assoc($result);

if ($data) {
    echo "<h4>Dados da tabela:</h4>";
    foreach ($data as $key => $value) {
        echo "<p><strong>$key:</strong> " . htmlspecialchars($value) . "</p>";
    }
} else {
    echo "<p>Nenhum dado encontrado na tabela.</p>";
}

// Verificar o charset da conexão
$charsetQuery = mysql_query("SELECT @@character_set_connection, @@collation_connection", $conn);
if ($charsetQuery) {
    $charsetInfo = mysql_fetch_assoc($charsetQuery);
    echo "<h4>Informações de Charset:</h4>";
    echo "<p><strong>Character Set:</strong> " . htmlspecialchars($charsetInfo['@@character_set_connection']) . "</p>";
    echo "<p><strong>Collation:</strong> " . htmlspecialchars($charsetInfo['@@collation_connection']) . "</p>";
} else {
    echo "<p>Não foi possível obter informações de charset.</p>";
}

// Fechar a conexão
mysql_close($conn);
?>
