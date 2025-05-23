<?php 



function exibeHorario($turma, $dia, $aula){

$cTurma = $turma;
$cDia = $dia;
$cAula = $aula;

//require_once('../../Connections/SmecelNovo.php');
require('../../Connections/SmecelNovo.php'); 

//$hostname_SmecelNovo = "localhost";
//$database_SmecelNovo = "smecel1";
//$username_SmecelNovo = "root";
//$password_SmecelNovo = "";
//$SmecelNovo = mysql_pconnect($hostname_SmecelNovo, $username_SmecelNovo, $password_SmecelNovo) or trigger_error(mysql_error(),E_USER_ERROR); 



mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Aulas = "
SELECT 
ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, 
ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola,
disciplina_id, disciplina_nome, disciplina_nome_abrev 
FROM smc_ch_lotacao_professor INNER JOIN smc_disciplina ON disciplina_id = ch_lotacao_disciplina_id
WHERE ch_lotacao_turma_id = $cTurma AND ch_lotacao_dia = $cDia AND ch_lotacao_aula = $cAula
";
$Aulas = mysql_query($query_Aulas, $SmecelNovo) or die(mysql_error());
$row_Aulas = mysql_fetch_assoc($Aulas);
$totalRows_Aulas = mysql_num_rows($Aulas);

if ($totalRows_Aulas <> 0) {

$codHorario = $row_Aulas['ch_lotacao_id'];	
$disciplinaNome = $row_Aulas['disciplina_nome'];	
$disciplinaNomeAbrev = $row_Aulas['disciplina_nome_abrev'];	
$codProfessor = $row_Aulas['ch_lotacao_professor_id'];
	
//return $row_Aulas['disciplina_nome']." <small>(<b>".$row_Aulas['ch_lotacao_professor_id']."</b>)</small>";

return "<a href='gradeEditar.php?horario=$codHorario'><small><span class=\"ls-display-none-xs\">$disciplinaNome</span><span class=\"ls-display-none-sm ls-display-none-md ls-display-none-lg\">$disciplinaNomeAbrev</span> (<b>$codProfessor</b>)</small></a>";

} else {
	return "<a href='gradeCadastrar.php?c=$cTurma&dia=$cDia&aula=$cAula'><small class='ls-color-warning'>VAGA</small></a>";
	}

}

?>