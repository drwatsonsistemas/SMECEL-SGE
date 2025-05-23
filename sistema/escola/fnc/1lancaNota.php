<?php require_once('../../../Connections/SmecelNovo.php'); ?>

<?php 

$id 			= $_POST['id'];	
$valor 			= number_format($_POST['valor'], 1, '.', '');
$notaMax 		= $_POST['notaMax'];
$notaAnterior 	= $_POST['notaAnterior'];
$disciplina 	= $_POST['disciplina'];
	

if (empty($valor)) {
    echo "<div class=\"ls-alert-danger\">Informe uma nota</div>";
} 
// Verifica se a mensagem foi digitada 
elseif (empty($id)) {
    echo "<div class=\"ls-alert-danger\">Informe o código</div>";
} 
elseif ($valor > $notaMax) {
    echo "<div class=\"ls-alert-danger\">Nota <strong>$valor</strong> digitada é maior do que <strong>$notaMax</strong></div>";
} 
elseif ($valor < 0) {
    echo "<div class=\"ls-alert-danger\">Nota não pode ser menor do que o valor <strong>0 (zero)</strong></div>";
} 
else {
    // Inserimos no banco de dados

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$updateSQL = "
UPDATE smc_nota SET nota_valor = '$valor' WHERE nota_hash = '$id' 
";
$Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());


    // Se inserido com sucesso
    if ($Result1) {
		echo "<div class=\"ls-alert-success\">Nota <strong>$valor</strong> da disciplina <strong>$disciplina</strong> salva com sucesso. Nota anterior: <strong>$notaAnterior</strong></div>";
    } 
    // Se houver algum erro ao inserir
    else {
		die("<div class=\"ls-alert-danger\">Não foi possível inserir as informações. Tente novamente.</div>" . mysql_error());
    }
}
?>