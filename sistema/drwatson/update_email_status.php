<?php
// Conexão com o banco de dados
require_once('../../Connections/SmecelNovo.php');

if (isset($_POST['id']) && isset($_POST['envia_email'])) {
    $id = intval($_POST['id']);
    $envia_email = $_POST['envia_email'];

    // Atualiza o banco de dados
    mysql_select_db($database_SmecelNovo, $SmecelNovo);
    $query = "UPDATE smc_func SET func_envia_email = '$envia_email' WHERE func_id = $id";
    $result = mysql_query($query);

    if ($result) {
        echo 'success';
    } else {
        echo 'error';
    }
}
?>
