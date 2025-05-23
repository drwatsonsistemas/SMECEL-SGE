<?php
if (isset($_POST["id"])) {
    require_once('../../../../Connections/SmecelNovoPDO.php');
    
    extract($_POST);
    
    if (empty($id)) {
        echo "<script>M.toast({html: '<i class=\"material-icons red-text\">block</i>&nbsp;<button class=\"btn-flat toast-action\"> Informe o código </button>'});</script>";
        exit;
    } else {
        try {
            $stmtDelete = $SmecelNovo->prepare("DELETE FROM smc_ac WHERE ac_id = :id AND ac_id_professor = :prof");
            $stmtDelete->execute([
                ':id' => $id,
                ':prof' => $prof
            ]);

            echo "<script>Swal.fire({ position: 'top-end', icon: 'success', title: 'Planejamento excluído',  text: '$id', showConfirmButton: false, timer: 2000 })</script>";
            exit;
        } catch (PDOException $e) {
            die("Erro ao excluir planejamento: " . $e->getMessage());
        }
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