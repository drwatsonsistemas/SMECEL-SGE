<?php
  
  function preencheHorario ($professor, $ano, $dia, $aula, $turno, $escola) {
	
	require('../../Connections/SmecelNovo.php');
	//require('fnc/anoLetivo.php');	  
  	mysql_select_db($database_SmecelNovo, $SmecelNovo);
	$query_CargaHoraria = "
	SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, 
	ch_lotacao_escola, turma_id, turma_nome, turma_ano_letivo, turma_turno, disciplina_id, disciplina_nome
	FROM smc_ch_lotacao_professor
	INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
	INNER JOIN smc_disciplina ON disciplina_id = ch_lotacao_disciplina_id
	WHERE ch_lotacao_professor_id = '$professor' AND turma_ano_letivo = '$ano' AND ch_lotacao_dia = '$dia' AND ch_lotacao_aula = '$aula' AND turma_turno = '$turno' AND ch_lotacao_escola = '$escola'
	ORDER BY ch_lotacao_dia, turma_turno, ch_lotacao_aula ASC
	";
	$CargaHoraria = mysql_query($query_CargaHoraria, $SmecelNovo) or die(mysql_error());
	$row_CargaHoraria = mysql_fetch_assoc($CargaHoraria);
	$totalRows_CargaHoraria = mysql_num_rows($CargaHoraria);
	
	if ($totalRows_CargaHoraria > 0) {
		return "<strong>".$row_CargaHoraria['turma_nome']."</strong><br><small>".$row_CargaHoraria['disciplina_nome']."</small>";
		} else {
			return "-";
			}
	
	
  }

  function getTotalAulas($professor, $ano, $turno) {
    require('../../Connections/SmecelNovo.php');
    mysql_select_db($database_SmecelNovo, $SmecelNovo);
    
    $query = "
      SELECT MAX(ch_lotacao_aula) as total
      FROM smc_ch_lotacao_professor
      INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
      WHERE ch_lotacao_professor_id = '$professor'
        AND turma_ano_letivo = '$ano'
        AND turma_turno = '$turno'
    ";
    $result = mysql_query($query, $SmecelNovo) or die(mysql_error());
    $row = mysql_fetch_assoc($result);
    return (int)$row['total']; // se não houver registros, retornará 0
}

// Função para gerar a tabela de horários para um turno específico
function gerarTabelaHorarios($professor, $ano, $dias, $turno, $turno_nome, $escola) {
    // Obtém o total de aulas para este professor, ano e turno
    $totalAulas = getTotalAulas($professor, $ano, $turno);
    
    // Se não houver aulas, você pode optar por não exibir nada (ou exibir uma mensagem)
    if ($totalAulas <= 0) {
        return;
    }
    
    // Gera um array de aulas de 1 até o total de aulas encontrado
    $aulas = range(1, $totalAulas);
    
    echo "<h4 class='ls-txt-center'>{$turno_nome}</h4>";
    echo "<table class='ls-table ls-sm-space ls-table-striped ls-bg-header'>";
    echo "<thead><tr><th class='ls-txt-center' width='40'></th>";
    // Cabeçalho: dias da semana
    foreach ($dias as $dia_num => $dia_nome) {
        echo "<th class='ls-txt-center'>{$dia_nome}</th>";
    }
    echo "</tr></thead><tbody>";
    
    // Para cada aula, gera uma linha na tabela
    foreach ($aulas as $aula) {
        echo "<tr>";
        echo "<td class='ls-txt-center'><strong>{$aula}ª</strong></td>";
        // Preenche as células para cada dia com a função preencheHorario
        foreach ($dias as $dia_num => $dia_nome) {
            echo "<td class='ls-txt-center'>" . preencheHorario($professor, $ano, $dia_num, $aula, $turno, $escola) . "</td>";
        }
        echo "</tr>";
    }
    echo "</tbody></table>";
}

