<?php require_once('../../../Connections/SmecelNovo.php'); ?>

<?php 

$matricula 		= $_POST['matricula'];
$aula_numero 	= $_POST['aula_numero'];
$data 			= $_POST['data'];
$aluno 			= $_POST['aluno'];
$disciplina 	= $_POST['disciplina'];


	  mysql_select_db($database_SmecelNovo, $SmecelNovo);
	  $query_Verifica = "
	  SELECT faltas_alunos_id, faltas_alunos_matricula_id, faltas_alunos_disciplina_id, faltas_alunos_numero_aula, faltas_alunos_data 
	  FROM smc_faltas_alunos 
	  WHERE faltas_alunos_matricula_id = '$matricula' AND faltas_alunos_data = '$data' AND faltas_alunos_numero_aula = '$aula_numero'";
	  $Verifica = mysql_query($query_Verifica, $SmecelNovo) or die(mysql_error());
	  $row_Verifica = mysql_fetch_assoc($Verifica);
	  $totalRows_Verifica = mysql_num_rows($Verifica);


if (empty($matricula)) {
			echo "<script>M.toast({html: '<i class=\"material-icons red-text\">block</i>&nbsp;<button class=\"btn-flat toast-action\"> Informe o código $totalRows_Verifica </button>'});</script>";
			exit;

} elseif ($totalRows_Verifica > 0) {
	
	
			  $deleteSQL = sprintf("DELETE FROM smc_faltas_alunos WHERE faltas_alunos_id = '$row_Verifica[faltas_alunos_id]'");
			  mysql_select_db($database_SmecelNovo, $SmecelNovo);
			  $Result2 = mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());

	
			  echo "<script>M.toast({html: '<i class=\"material-icons\">check_circle</i>&nbsp;<button class=\"btn-flat toast-action\">Falta excluída com sucesso.</button>'});</script>";
			exit;

} else {
			
			
			  $insertSQL = sprintf("INSERT INTO smc_faltas_alunos (faltas_alunos_matricula_id, faltas_alunos_disciplina_id, faltas_alunos_numero_aula, faltas_alunos_data) VALUES ('$matricula', '$disciplina', '$aula_numero', '$data')");
			  mysql_select_db($database_SmecelNovo, $SmecelNovo);
			  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

			  echo "<script>M.toast({html: '<i class=\"material-icons\">check_circle</i>&nbsp;<button class=\"btn-flat toast-action\">Falta realizada com sucesso. </button>'});</script>";
			  
			  exit;
			
			
/*
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
	
*/

	
}
?>