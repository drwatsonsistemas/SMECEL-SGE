<?php 
require_once('../../../Connections/SmecelNovoPDO.php'); 

$variaveis = extract($_POST);

// Verificação de campos obrigatórios
if (empty($objeto) || empty($valor) || empty($matricula) || empty($periodo)) {
    echo "<script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });
        Toast.fire({
            icon: 'error',
            title: 'Algo deu errado.'
        });
    </script>";
} else {
    try {
        // Preparar a query de atualização
        $updateSQL = "
            UPDATE smc_conceito_aluno 
            SET conc_avaliacao = :valor 
            WHERE conc_acomp_id = :objeto 
              AND conc_matricula_id = :matricula 
              AND conc_periodo = :periodo";

        $stmt = $SmecelNovo->prepare($updateSQL);

        // Bind dos parâmetros
        $stmt->bindParam(':valor', $valor, PDO::PARAM_INT);
        $stmt->bindParam(':objeto', $objeto, PDO::PARAM_INT);
        $stmt->bindParam(':matricula', $matricula, PDO::PARAM_INT);
        $stmt->bindParam(':periodo', $periodo, PDO::PARAM_INT);

        // Executar a query
        if ($stmt->execute()) {
            echo "<script>
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    }
                });
                Toast.fire({
                    icon: 'success',
                    title: 'Salvo com sucesso'
                });
            </script>";
        } else {
            echo "<div class='card-panel red lighten-4'>Não foi possível inserir as informações. Tente novamente.</div>";
        }
    } catch (PDOException $e) {
        die("<div class='card-panel red lighten-4'>Erro ao atualizar: " . $e->getMessage() . "</div>");
    }
}
?>
