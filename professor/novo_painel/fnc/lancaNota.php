<?php require_once('../../../Connections/SmecelNovoPDO.php'); ?>

<?php 

$id = $_POST['id'];
$decimal = $_POST['decimal'];
$notaMax = $_POST['notaMax'];
$notaAnterior = $_POST['notaAnterior'];
$disciplina = $_POST['disciplina'];
$turma = $_POST['turma'];
$escola = $_POST['escola'];
$consolidado = $_POST['consolidado'];
$recParalela = $_POST['recParalela'];



// Verifica se o valor foi enviado ou está vazio
if (isset($_POST['valor']) && $_POST['valor'] !== "") {
    // Mantém como string e apenas formata
    $valor = number_format((float)$_POST['valor'], $decimal, '.', '');
    $vazio = 1;
} else {
    $valor = null;
    $vazio = 0;
}

// Validações iniciais
if (empty($id)) {
    echo "<script>Swal.fire({
        position: 'top-end',
        icon: 'error',
        backdrop: false,
        allowOutsideClick: true,
        toast: true,
        title: 'Informe o código',
        showConfirmButton: false,
        timer: 1500
    });</script>";
    exit;
}

if ($valor !== null && $valor > $notaMax) {
    echo "<script>Swal.fire({
        position: 'top-end',
        icon: 'error',
        backdrop: false,
        allowOutsideClick: true,
        toast: true,
        title: 'Nota <strong>$valor</strong> digitada é maior do que <strong>$notaMax</strong>',
        showConfirmButton: false,
        timer: 1500
    });</script>";
    exit;
}

if ($valor !== null && $valor < 0) {
    echo "<script>Swal.fire({
        position: 'top-end',
        icon: 'error',
        backdrop: false,
        allowOutsideClick: true,
        toast: true,
        title: 'Nota não pode ser menor do que o valor <strong>0 (zero)</strong>',
        showConfirmButton: false,
        timer: 1500
    });</script>";
    exit;
}



// Atualiza no banco de dados
try {
    if ($vazio === 1) {
        $updateSQL = "UPDATE smc_nota SET nota_valor = :valor WHERE nota_hash = :id";
        $stmt = $SmecelNovo->prepare($updateSQL);
        $stmt->bindValue(':valor', $valor, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
    } else {
        $updateSQL = "UPDATE smc_nota SET nota_valor = NULL WHERE nota_hash = :id";
        $stmt = $SmecelNovo->prepare($updateSQL);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
    }
    
    $result = $stmt->execute();

    if ($result) {
        // Atualiza o consolidado se necessário
        if ($consolidado === "S") {
            $updateConsolidadoSQL = "UPDATE smc_turma SET turma_resultado_consolidado = 'N' WHERE turma_id = :turma AND turma_id_escola = :escola";
            $stmt = $SmecelNovo->prepare($updateConsolidadoSQL);
            $stmt->bindValue(':turma', $turma, PDO::PARAM_INT);
            $stmt->bindValue(':escola', $escola, PDO::PARAM_INT);
            $stmt->execute();
        }

        echo "<script>Swal.fire({
            position: 'top-end',
            icon: 'success',
            backdrop: false,
            allowOutsideClick: true,
            toast: true,
            title: 'Nota <strong>$valor</strong> da disciplina <strong>$disciplina</strong> salva com sucesso. Nota anterior: <strong>$notaAnterior</strong>',
            showConfirmButton: false,
            timer: 1500
        });</script>";
    } else {
        echo "<div class=\"card-panel red lighten-4\">Não foi possível inserir as informações. Tente novamente.</div>";
    }
} catch (PDOException $e) {
    echo "<script>Swal.fire({
        position: 'top-end',
        icon: 'error',
        backdrop: false,
        allowOutsideClick: true,
        toast: true,
        title: 'Erro ao processar dados: " . $e->getMessage() . "',
        showConfirmButton: false,
        timer: 1500
    });</script>";
}
?>
