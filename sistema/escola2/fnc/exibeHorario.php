<?php 



function exibeHorario($turma, $dia, $aula, $id_matriz){

	$cTurma = $turma;
	$cDia = $dia;
	$cAula = $aula;

	require('../../Connections/SmecelNovo.php');

	mysql_select_db($database_SmecelNovo, $SmecelNovo);
	$query_Aulas = "
	SELECT 
	ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, 
	ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola,
	disciplina_id, disciplina_nome, disciplina_nome_abrev 
	FROM smc_ch_lotacao_professor 
	INNER JOIN smc_disciplina ON disciplina_id = ch_lotacao_disciplina_id
	WHERE ch_lotacao_turma_id = $cTurma AND ch_lotacao_dia = $cDia AND ch_lotacao_aula = $cAula
	";
	$Aulas = mysql_query($query_Aulas, $SmecelNovo) or die(mysql_error());
	$row_Aulas = mysql_fetch_assoc($Aulas);
	$totalRows_Aulas = mysql_num_rows($Aulas);

	if ($totalRows_Aulas != 0) {

		$query_MatrizDisciplinas = "
		SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_eixo, matriz_disciplina_id_disciplina, matriz_disciplina_ch_ano, disciplina_id, disciplina_nome, disciplina_cor_fundo, disciplina_eixo_id, disciplina_eixo_nome
		FROM smc_matriz_disciplinas
		INNER JOIN smc_disciplina ON disciplina_id = matriz_disciplina_id_disciplina
		LEFT JOIN smc_disciplina_eixos ON matriz_disciplina_eixo = disciplina_eixo_id
		WHERE matriz_disciplina_id_matriz = '$id_matriz' AND disciplina_id = '{$row_Aulas['disciplina_id']}'
		";
		$MatrizDisciplinas = mysql_query($query_MatrizDisciplinas, $SmecelNovo) or die(mysql_error());
		$row_MatrizDisciplinas = mysql_fetch_assoc($MatrizDisciplinas);
		$totalRows_MatrizDisciplinas = mysql_num_rows($MatrizDisciplinas);

		$codHorario = $row_Aulas['ch_lotacao_id'];
		$disciplinaNome = $row_Aulas['disciplina_nome'];
		$disciplinaNomeAbrev = $row_Aulas['disciplina_nome_abrev'];
		$codProfessor = $row_Aulas['ch_lotacao_professor_id'];

		if ($totalRows_MatrizDisciplinas != 0 && $row_MatrizDisciplinas['disciplina_eixo_nome']) {
			$disciplinaNome .= " - ({$row_MatrizDisciplinas['disciplina_eixo_nome']})";
		}

		return "<a href='gradeEditar.php?horario=$codHorario'><small><span class=\"ls-display-none-xs\">$disciplinaNome</span><span class=\"ls-display-none-sm ls-display-none-md ls-display-none-lg\">$disciplinaNomeAbrev</span> (<b>$codProfessor</b>)</small></a>";

	} else {
		return "<a href='gradeCadastrar.php?c=$cTurma&dia=$cDia&aula=$cAula'><small class='ls-color-warning'>VAGA</small></a>";
	}
}
