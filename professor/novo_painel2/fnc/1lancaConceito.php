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
					echo "<script>Swal.fire({ position: 'top-end', icon: 'warning', title: 'Atenção', text: 'Informe o código.', showConfirmButton: false, timer: 2000 })</script>";


} else {
    // Inserimos no banco de dados
	
	mysql_select_db($database_SmecelNovo, $SmecelNovo);
	$updateSQL = "UPDATE smc_conceito_aluno SET conc_avaliacao = $valor WHERE conc_acomp_id = $objeto AND conc_matricula_id = $matricula AND conc_periodo = $periodo";
	//$updateSQL = "UPDATE smc_conceito_aluno SET conc_avaliacao = null";
	$Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

    // Se inserido com sucesso
    if ($Result1) {
    
		echo "<script>Swal.fire({ position: 'top-end', icon: 'success', title: 'Feito', text: 'Conceito registrado com sucesso.', showConfirmButton: false, timer: 2000 })</script>";
	
	} 
    // Se houver algum erro ao inserir
    else {
		die("<div class=\"card-panel red lighten-4\">Não foi possível inserir as informações. Tente novamente.</div>" . mysql_error());
    }
}
?>