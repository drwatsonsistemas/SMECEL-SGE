<?php
require_once('../../Connections/SmecelNovoPDO.php'); // Include your PDO connection file
include "fnc/anti_injection.php";


if ((isset($_GET['aula'])) && ($_GET['aula'] != "")) {
    $aulaHash = anti_injection($_GET['aula']); // Sanitize the input

    try {

        $sql = "DELETE FROM smc_plano_aula WHERE plano_aula_hash = :aulaHash";
        $stmt = $SmecelNovo->prepare($sql);
        $stmt->bindValue(':aulaHash', $aulaHash, PDO::PARAM_STR);
        $stmt->execute();

        $deleteGoTo = "mapa_aulas_avulsas.php?deletado";
        if (isset($_SERVER['QUERY_STRING'])) {
            $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
            $deleteGoTo .= $_SERVER['QUERY_STRING'];
        }
        header(sprintf("Location: %s", $deleteGoTo));
        exit; // Very important: Add exit after header redirect
    } catch (PDOException $e) {
        // Handle the error appropriately
        echo "Database Error: " . $e->getMessage();
        // Or log the error: error_log($e->getMessage());
        exit; // Important: Stop execution after error handling
    }
}
?>