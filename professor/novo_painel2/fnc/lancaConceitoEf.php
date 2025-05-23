<?php require_once('../../../Connections/SmecelNovo.php'); ?>

<?php 

$variaves = extract($_POST);

/*
echo $objeto."<br>";
echo $valor."<br>";
echo $matricula."<br>";
echo $periodo."<br>";
echo $q."<br>";
exit; 
*/

if ($objeto==0) {
	
					
					mysql_select_db($database_SmecelNovo, $SmecelNovo);
					$insere = "INSERT INTO smc_conceito_ef (conc_ef_id_quest, conc_ef_id_matr, conc_ef_periodo, conc_ef_avaliac) VALUES ('$q', '$matricula','$periodo','$valor')";
					$Result2 = mysql_query($insere, $SmecelNovo) or die(mysql_error());
					
					if ($Result2) {
						echo "<script>Swal.fire({ position: 'top-end', icon: 'success', title: 'Feito', text: 'Conceito registrado com sucesso.', showConfirmButton: false, timer: 2000 })</script>";
						echo "<script>window.location.reload(true);</script>";

					} 
					// Se houver algum erro ao inserir
					else {
						die("<script>Swal.fire({ position: 'top-end', icon: 'warning', title: 'Atenção', text: 'Não foi possível inserir as informações.', showConfirmButton: false, timer: 2000 })</script>" . mysql_error());
					}
					
					/*
					echo "<script>Swal.fire({ position: 'top-end', icon: 'warning', title: 'Atenção', text: 'Informe o código.', showConfirmButton: false, timer: 2000 })</script>";
					*/

} else {
    // Inserimos no banco de dados
	
	mysql_select_db($database_SmecelNovo, $SmecelNovo);
	$updateSQL = "UPDATE smc_conceito_ef SET conc_ef_avaliac = $valor WHERE conc_ef_id = $objeto AND conc_ef_id_matr = $matricula AND conc_ef_periodo = $periodo";
	//$updateSQL = "UPDATE smc_conceito_aluno SET conc_avaliacao = null";
	$Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

    // Se inserido com sucesso
    if ($Result1) {
    
		echo "<script>Swal.fire({ position: 'top-end', icon: 'success', title: 'Feito', text: 'Conceito atualizado com sucesso.', showConfirmButton: false, timer: 2000 })</script>";
	
	} 
    // Se houver algum erro ao inserir
    else {
		die("<script>Swal.fire({ position: 'top-end', icon: 'warning', title: 'Atenção', text: 'Não foi possível inserir as informações.', showConfirmButton: false, timer: 2000 })</script>" . mysql_error());
    }
}
?>