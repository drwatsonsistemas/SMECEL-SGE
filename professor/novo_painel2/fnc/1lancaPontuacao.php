<?php require_once('../../../Connections/SmecelNovo.php'); ?>

<?php 




$id = $_POST['alunoId'];	
$periodo = $_POST['periodo'];
$componente = $_POST['componente'];
$pontuacaoMax = $_POST['pontuacaoMax'];

if (empty($_POST['valor'])) {
	$valor = "";
	$vazio = 0;
} else {
	$valor = number_format($_POST['valor'], 2, '.', '');
	$vazio = 1;
	
}

/* if (empty($valor)) {
    //echo "<div class=\"card-panel red lighten-4\">Informe uma nota</div>";
		echo "<script>M.toast({html: '<i class=\"material-icons\">block</i>&nbsp;<button class=\"btn-flat toast-action\"> Informe um valor para a nota </button>'});</script>";

	}  */
// Verifica se a mensagem foi digitada 
	if (empty($id)) {

		echo "<script>Swal.fire({ position: 'top-end', icon: 'warning', title: 'Atenção', text: 'Informe o código.', showConfirmButton: false, timer: 2000 })</script>";


	} 

	elseif ($valor < 0) {
		echo "<script>Swal.fire({ position: 'top-end', icon: 'warning', title: 'Atenção', text: 'Nota não pode ser menor do que zero.', showConfirmButton: false, timer: 2000 })</script>";

	} 
	else {

		$tipo = 0;
		$criterio = 0;
		$texto = '';
		if($_POST['tipo'] == 'qualitativo'){
			if($valor > $pontuacaoMax){
				echo "<script>Swal.fire({ position: 'top-end', icon: 'warning', title: 'Atenção', text: 'Nota $valor digitada é maior do que $pontuacaoMax.', showConfirmButton: false, timer: 2000 })</script>";
				exit;
			}
			$tipo = 1;
			$criterio = 12;
			$texto = 'Qualitativo';
		}elseif($_POST['tipo'] == 'quantitativo'){
			if($valor > $pontuacaoMax){
				echo "<script>Swal.fire({ position: 'top-end', icon: 'warning', title: 'Atenção', text: 'Nota $valor digitada é maior do que $pontuacaoMax.', showConfirmButton: false, timer: 2000 })</script>";
				exit;
			}
			$tipo = 2;
			$criterio = 6;
			$texto = 'Quantitativo';
		}elseif($_POST['tipo'] == 'paralela'){
			if($valor > $pontuacaoMax){
				echo "<script>Swal.fire({ position: 'top-end', icon: 'warning', title: 'Atenção', text: 'Nota $valor digitada é maior do que $pontuacaoMax.', showConfirmButton: false, timer: 2000 })</script>";
				exit;
			}
			$tipo = 3;
			$criterio = 99;
			$texto = 'Paralela';
		}
		
		$deleteSQL = "DELETE FROM smc_notas_qq WHERE qq_id_matricula = '$id' AND qq_id_periodo='$periodo' AND qq_tipo_criterio = '$tipo' AND qq_id_componente = '$componente'";
		mysql_select_db($database_SmecelNovo, $SmecelNovo);
		$Result1 = mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());

		$insertSQL = "INSERT INTO smc_notas_qq (qq_id_matricula, qq_id_componente, qq_id_periodo, qq_tipo_criterio, qq_id_criterio, qq_nota) VALUES ('$id', '$componente', '$periodo','$tipo',$criterio,'$valor')";
		mysql_select_db($database_SmecelNovo, $SmecelNovo);
		$Result2 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());


    // Se inserido com sucesso
		if ($Result2) {

			mysql_select_db($database_SmecelNovo, $SmecelNovo);
			$query_Disciplina = "SELECT disciplina_id, disciplina_area_conhecimento_id, disciplina_codigo_inep, disciplina_ordem, disciplina_nome, disciplina_nome_abrev FROM smc_disciplina WHERE disciplina_id = '$componente'";
			$Disciplina = mysql_query($query_Disciplina, $SmecelNovo) or die(mysql_error());
			$row_Disciplina = mysql_fetch_assoc($Disciplina);
			$totalRows_Disciplina = mysql_num_rows($Disciplina);
			$disciplina = $row_Disciplina['disciplina_nome'];
			echo "<script>Swal.fire({ position: 'top-end', icon: 'success', title: '$texto: $valor registrada', text: 'Componente $disciplina.', showConfirmButton: false, timer: 2000 })</script>";
    /*
	echo "<script>
	Swal.fire({
	  //position: 'top-end',
	  icon: 'success',
	  title: 'Salvo',
	  text: 'Nota <strong>$valor</strong> da disciplina <strong>$disciplina</strong> salva com sucesso. Nota anterior: <strong>$notaAnterior</strong></button>'
	  showConfirmButton: false,
	  timer: 1500
	});
	</script>";
	*/
	
} 
    // Se houver algum erro ao inserir
else {
	die("<div class=\"card-panel red lighten-4\">Não foi possível inserir as informações. Tente novamente.</div>" . mysql_error());
}
}
?>