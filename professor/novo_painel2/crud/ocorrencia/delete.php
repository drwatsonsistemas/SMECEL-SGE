<?php

if(isset($_POST["id"])) {

    // Incluindo a conexão PDO
    require_once('../../../../Connections/SmecelNovoPDO.php');
    
    // Extraindo os valores de $_POST
    extract($_POST);

    // Verifica se o ID está vazio
    if (empty($id)) {
        echo "<script>M.toast({html: '<i class=\"material-icons red-text\">block</i>&nbsp;<button class=\"btn-flat toast-action\"> Informe o código $totalRows_Verifica </button>'});</script>";
        exit;
    } else {
        try {
            // Prepara a consulta de exclusão utilizando PDO
            $deleteSQL = "DELETE FROM smc_ocorrencia_turma WHERE ocorrencia_id = :id AND ocorrencia_id_professor = :prof";
            $stmt = $SmecelNovo->prepare($deleteSQL);

            // Bind dos parâmetros
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':prof', $prof, PDO::PARAM_INT);

            // Executa a consulta
            $stmt->execute();

            // Se a exclusão for bem-sucedida, exibe a notificação com o SweetAlert
            echo "<script>Swal.fire({ position: 'top-end', icon: 'success', title: 'Ocorrência excluída', text: '$id', showConfirmButton: false, timer: 2000 })</script>";

        } catch (PDOException $e) {
            // Caso ocorra um erro durante a execução da consulta
            echo "<script>M.toast({html: '<i class=\"material-icons red-text\">block</i>&nbsp;<button class=\"btn-flat toast-action\">Erro ao excluir a ocorrência. Tente novamente.</button>'});</script>";
            error_log($e->getMessage());
        }

        exit;
    }

} else {
    echo "Como é que você veio parar aqui?<br>";

    // Função para pegar o IP do cliente
    function get_client_ip() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    // Exibe o IP do cliente
    echo get_client_ip();

    // Redireciona para a página inicial com erro
    header("Location:../../index.php?err");
}

?>
