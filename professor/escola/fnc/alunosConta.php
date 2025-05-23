<?php


  function alunosConta($turma, $ano){
  
	require('../../Connections/SmecelNovo.php');
	//require('fnc/anoLetivo.php');
	
	mysql_select_db($database_SmecelNovo, $SmecelNovo);
	$query_ListaVinculos = "
	SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
	vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
	vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia 
	FROM smc_vinculo_aluno
	WHERE vinculo_aluno_id_turma = $turma AND vinculo_aluno_situacao = '1' AND vinculo_aluno_ano_letivo = '$ano'";
	$ListaVinculos = mysql_query($query_ListaVinculos, $SmecelNovo) or die(mysql_error());
	$row_ListaVinculos = mysql_fetch_assoc($ListaVinculos);
	$totalRows_ListaVinculos = mysql_num_rows($ListaVinculos);

	mysql_select_db($database_SmecelNovo, $SmecelNovo);
	$query_Turmas = "SELECT turma_id, turma_id_escola, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo
	FROM smc_turma WHERE turma_id = '$turma'
	";
	$Turmas = mysql_query($query_Turmas, $SmecelNovo) or die(mysql_error());
	$row_Turmas = mysql_fetch_assoc($Turmas);
	$totalRows_Turmas = mysql_num_rows($Turmas);

	$excedeu = "";
	if ($totalRows_ListaVinculos > $row_Turmas['turma_total_alunos']) {
		$excedeu = "(excedeu)";
	}
	
	return (int) $totalRows_ListaVinculos;
  
	mysql_free_result($ListaVinculos);
  }
  
?>