<?php require_once('../../../Connections/SmecelNovo.php'); ?>
<?php require_once('anti_injection.php'); ?>

<?php 

$variaves = extract($_POST);

$variaves = anti_injection($variaves);
/*
echo $conteudo."<br>";
echo $matricula."<br>";
exit; 

*/

if (empty($parecer) || empty($aluno) || empty($professor)) {
			echo "<script>M.toast({html: '<i class=\"material-icons red-text\">block</i>&nbsp;<button class=\"btn-flat toast-action\"> Erro </button>'});</script>";

} else {
    // Inserimos no banco de dados
	
			  $deleteSQL = sprintf("DELETE FROM smc_parecer_individual_professor WHERE p_ind_id = '$parecer' AND p_ind_id_prof = '$professor' AND p_ind_mat_aluno = '$aluno'");
			  mysql_select_db($database_SmecelNovo, $SmecelNovo);
			  $Result2 = mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());
	

    // Se inserido com sucesso
    if ($Result2) {
    
		echo "<script>M.toast({html: '<i class=\"material-icons green-text\">check_circle</i>&nbsp;<button class=\"btn-flat toast-action\"> Parecer deletado com sucesso</button>'});</script>";
		echo "<script>setTimeout(function() { window.location.reload(1); }, 3000); </script>";
	
	} 
    // Se houver algum erro ao inserir
    else {
		die("<div class=\"card-panel red lighten-4\">Não foi possível inserir as informações. Tente novamente.</div>" . mysql_error());
    }
}
?>