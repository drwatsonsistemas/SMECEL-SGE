<?php require_once('../../../Connections/SmecelNovo.php'); ?>

<?php 

$id 			= $_POST['id'];	
if (empty($_POST['valor'])) {
	$valor = "";
	$vazio = 0;
} else {
	$valor = number_format($_POST['valor'], 1, '.', '');
	$vazio = 1;
}
$notaMax 		= $_POST['notaMax'];
$notaAnterior 	= $_POST['notaAnterior'];
$disciplina 	= $_POST['disciplina'];
	

/* if (empty($valor)) {
    //echo "<div class=\"card-panel red lighten-4\">Informe uma nota</div>";
		echo "<script>M.toast({html: '<i class=\"material-icons\">block</i>&nbsp;<button class=\"btn-flat toast-action\"> Informe um valor para a nota </button>'});</script>";

}  */
// Verifica se a mensagem foi digitada 
if (empty($id)) {
			echo "<script>M.toast({html: '<i class=\"material-icons red-text\">block</i>&nbsp;<button class=\"btn-flat toast-action\"> Informe o código </button>'});</script>";

} 
elseif ($valor > $notaMax) {
			echo "<script>M.toast({html: '<i class=\"material-icons red-text\">block</i>&nbsp;<button class=\"btn-flat toast-action\"> Nota <strong>$valor</strong> digitada é maior do que <strong>$notaMax</strong>  </button>'});</script>";

} 
elseif ($valor < 0) {
			echo "<script>M.toast({html: '<i class=\"material-icons red-text\">block</i>&nbsp;<button class=\"btn-flat toast-action\">  Nota não pode ser menor do que o valor <strong>0 (zero)</strong> </button>'});</script>";

} 
else {
    // Inserimos no banco de dados


if ($vazio == 1) {

	mysql_select_db($database_SmecelNovo, $SmecelNovo);
	$updateSQL = "
	UPDATE smc_nota SET nota_valor = '$valor' WHERE nota_hash = '$id' 
	";
	$Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

} else {

	mysql_select_db($database_SmecelNovo, $SmecelNovo);
	$updateSQL = "
	UPDATE smc_nota SET nota_valor = NULL WHERE nota_hash = '$id' 
	";
	$Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());
	
}

    // Se inserido com sucesso
    if ($Result1) {
    
	echo "<script>M.toast({html: '<i class=\"material-icons green\">check_circle</i>&nbsp;<button class=\"btn-flat toast-action\">Nota <strong>$valor</strong> da disciplina <strong>$disciplina</strong> salva com sucesso. Nota anterior: <strong>$notaAnterior</strong></button>'});</script>";
	
	} 
    // Se houver algum erro ao inserir
    else {
		die("<div class=\"card-panel red lighten-4\">Não foi possível inserir as informações. Tente novamente.</div>" . mysql_error());
    }
}
?>