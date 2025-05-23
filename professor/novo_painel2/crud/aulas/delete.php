<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once('../../../../Connections/SmecelNovoPDO.php'); 
    include "../../fnc/anti_injection.php";

    try {
        // Sanitização e validação da entrada
        if (!isset($_POST['aula']) || empty($_POST['aula'])) {
            throw new Exception("Aula inválida.");
        }

        $aula = anti_injection($_POST['aula']);

        // Excluir a aula do banco de dados
        $deleteSQL = "DELETE FROM smc_plano_aula WHERE plano_aula_hash = :aula_hash";
        $stmt = $SmecelNovo->prepare($deleteSQL);
        $stmt->execute([':aula_hash' => $aula]);

        if ($stmt->rowCount() > 0) {
            echo "<script>
                    Swal.fire({ 
                        position: 'top-end', 
                        icon: 'success', 
                        title: 'Aula excluída',  
                        showConfirmButton: false, 
                        timer: 1000 
                    });
                    setTimeout(function(){ location.reload(); }, 1000);
                  </script>";
        } else {
            throw new Exception("Aula não encontrada ou já excluída.");
        }
    } catch (Exception $e) {
        echo "<script>
                Swal.fire({ 
                    position: 'top-end', 
                    icon: 'error', 
                    title: 'Erro',  
                    text: '{$e->getMessage()}', 
                    showConfirmButton: true 
                });
              </script>";
    }
}
?>
