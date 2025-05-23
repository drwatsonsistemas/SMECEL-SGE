<?php require_once('../../../Connections/SmecelNovo.php'); ?>

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
    // Inserimos no banco de dados
	
	mysql_select_db($database_SmecelNovo, $SmecelNovo);
	$updateSQL = "UPDATE smc_conceito_aluno SET conc_avaliacao = $valor WHERE conc_acomp_id = $objeto AND conc_matricula_id = $matricula AND conc_periodo = $periodo";
	//$updateSQL = "UPDATE smc_conceito_aluno SET conc_avaliacao = null";
	$Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

    // Se inserido com sucesso
    if ($Result1) {
    
		echo "<script>M.toast({html: '<i class=\"material-icons green-text\">check_circle</i>&nbsp;<button class=\"btn-flat toast-action\"> Conceito lançado com sucesso</button>'});</script>";
	
	} 
    // Se houver algum erro ao inserir
    else {
		die("<div class=\"card-panel red lighten-4\">Não foi possível inserir as informações. Tente novamente.</div>" . mysql_error());
    }
}
?>