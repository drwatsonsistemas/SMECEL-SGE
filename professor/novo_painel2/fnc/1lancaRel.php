<?php require_once('../../../Connections/SmecelNovo.php'); ?>
<?php include('anti_injection.php'); ?>


<?php 

$variaves = extract($_POST);

/*
echo $conteudo."<br>";
echo $matricula."<br>";
exit; 

*/

if (empty($matricula)) {
			
			echo "<script>Swal.fire({ icon: 'error', title: 'Oops...', text: 'Informe o texto', showConfirmButton: true, timer: 5000 })</script>";
			
	

} else {
    // Inserimos no banco de dados
	
	$conteudo = mysql_real_escape_string($conteudo);
	$conteudo = anti_injection($conteudo);
	$matricula = anti_injection($matricula);
	
	
	mysql_select_db($database_SmecelNovo, $SmecelNovo);
	$updateSQL = "UPDATE smc_vinculo_aluno SET vinculo_aluno_rel_aval = '$conteudo' WHERE vinculo_aluno_hash = '$matricula'";
	//$updateSQL = "UPDATE smc_conceito_aluno SET conc_avaliacao = null";
	$Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

    // Se inserido com sucesso
    if ($Result1) {
		
				echo "<script>Swal.fire({ position: 'top-end', icon: 'success', title: 'Relatório salvo com sucesso', showConfirmButton: false, timer: 2000 })</script>";
	
	} 
    // Se houver algum erro ao inserir
    else {
		die("<div class=\"card-panel red lighten-4\">Não foi possível inserir as informações. Tente novamente.</div>" . mysql_error());
    }
}
?>