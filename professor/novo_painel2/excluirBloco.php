<?php
require_once('../../Connections/SmecelNovoPDO.php');
include "conf/session.php";
include "fnc/anti_injection.php";

if (isset($_POST["id"])) {
    try {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            echo "<script>alert('Algo de errado aconteceu :(');</script>";
            exit;
        }

        // Exclusão do registro
        $stmtDelete = $SmecelNovo->prepare("DELETE FROM smc_ac_label WHERE ac_label_id = :id");
        $stmtDelete->bindValue(':id', $id, PDO::PARAM_INT);
        $stmtDelete->execute();

        if ($stmtDelete->rowCount() > 0) {
            echo "Registro excluído com sucesso.";
        } else {
            echo "Nenhum registro encontrado para exclusão.";
        }

    } catch (PDOException $e) {
        die("Erro ao excluir o registro: " . $e->getMessage());
    }
} else {
    echo "Como é que você veio parar aqui?<br>";

    function get_client_ip() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if (isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    echo get_client_ip();
    header("Location:../../index.php?err");
}
?>
