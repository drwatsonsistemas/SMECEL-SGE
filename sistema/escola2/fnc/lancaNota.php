<?php
require_once('../../../Connections/SmecelNovo.php');

$id = $_POST['id'];
$decimal = $_POST['decimal'];
$notaMax = $_POST['notaMax'];
$notaAnterior = $_POST['notaAnterior'];
$disciplina = $_POST['disciplina'];
$turma = $_POST['turma'];
$escola = $_POST['escola'];
$consolidado = $_POST['consolidado'];

// Verifica se o valor foi enviado ou está vazio
if (isset($_POST['valor']) && $_POST['valor'] !== "") {
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
mysql_select_db($database_SmecelNovo, $SmecelNovo);

if ($vazio === 1) {
    $updateSQL = "UPDATE smc_nota SET nota_valor = '$valor' WHERE nota_hash = '$id'";
} else {
    $updateSQL = "UPDATE smc_nota SET nota_valor = NULL WHERE nota_hash = '$id'";
}

$result = mysql_query($updateSQL, $SmecelNovo);

if ($result) {
    // Atualiza o consolidado se necessário
    if ($consolidado === "S") {
        $updateConsolidadoSQL = "UPDATE smc_turma SET turma_resultado_consolidado = 'N' WHERE turma_id = '$turma' AND turma_id_escola = '$escola'";
        mysql_query($updateConsolidadoSQL, $SmecelNovo);
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
    echo mysql_error();
}
