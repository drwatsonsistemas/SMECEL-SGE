<?php require_once('../../../Connections/SmecelNovoPDO.php'); ?>

<?php 

$variaves = extract($_POST);

/*
echo $objeto."<br>";
echo $valor."<br>";
echo $matricula."<br>";
echo $periodo."<br>";

exit; 
*/

if (empty($objeto) || empty($valor) || empty($matricula) || empty($periodo)) {
    echo "<script>M.toast({html: '<i class=\"material-icons red-text\">block</i>&nbsp;<button class=\"btn-flat toast-action\"> Informe o código </button>'});</script>";
} else {
    try {
        // Atualizamos no banco de dados
        $updateSQL = "UPDATE smc_conceito_aluno 
                      SET conc_avaliacao = :valor 
                      WHERE conc_acomp_id = :objeto 
                      AND conc_matricula_id = :matricula 
                      AND conc_periodo = :periodo";
        
        $stmt = $SmecelNovo->prepare($updateSQL);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_INT);
        $stmt->bindParam(':objeto', $objeto, PDO::PARAM_INT);
        $stmt->bindParam(':matricula', $matricula, PDO::PARAM_INT);
        $stmt->bindParam(':periodo', $periodo, PDO::PARAM_INT);
        $stmt->execute();

        // Verificamos se a consulta foi executada com sucesso
        if ($stmt->rowCount() > 0) {
            echo "<script>M.toast({html: '<i class=\"material-icons green-text\">check_circle</i>&nbsp;<button class=\"btn-flat toast-action\"> Conceito lançado com sucesso</button>'});</script>";
        } else {
            echo "<script>M.toast({html: '<i class=\"material-icons red-text\">error</i>&nbsp;<button class=\"btn-flat toast-action\"> Nenhuma linha foi atualizada </button>'});</script>";
        }
    } catch (PDOException $e) {
        // Se houver algum erro ao atualizar
        die("<div class=\"card-panel red lighten-4\">Não foi possível atualizar as informações. Tente novamente.</div>" . $e->getMessage());
    }
}
?>
