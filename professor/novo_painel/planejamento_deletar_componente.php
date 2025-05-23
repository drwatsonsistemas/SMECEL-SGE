<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once('../../Connections/SmecelNovoPDO.php');
    include "fnc/anti_injection.php";

    try {
        // Extrair e validar o componente
        $componente = isset($_POST['componente']) ? anti_injection($_POST['componente']) : null;

        if (!$componente || !is_numeric($componente)) {
            echo "ID do componente inválido.";
            exit;
        }

        // Realizar a exclusão do componente
        $stmtDelete = $SmecelNovo->prepare("DELETE FROM smc_ac_componente WHERE ac_componente_id = :componente");
        $stmtDelete->bindValue(':componente', $componente, PDO::PARAM_INT);
        $stmtDelete->execute();

        if ($stmtDelete->rowCount() > 0) {
            echo "Componente excluído com sucesso.";
        } else {
            echo "Nenhum componente encontrado para exclusão.";
        }
    } catch (PDOException $e) {
        die("Erro ao excluir o componente: " . $e->getMessage());
    }
} else {
    echo "Método inválido.";
    exit;
}
?>
