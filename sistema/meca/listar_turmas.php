<?php
function turno ($cod) {
		if ($cod == "1"){
			$turno = " / MAT";
		}else if ($cod == "2") {
			$turno = " / VESP";
		}else if ($cod == "3") {
			$turno = " / NOT";
		} else {
			$turno = "";
		}
		return $turno;
}
 
require_once('../../Connections/SmecelNovo.php'); 
require_once "funcoes/anoLetivo.php";

$escola = $_GET['escola'];

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ListaTurmas = "SELECT turma_id, turma_id_escola, turma_nome, turma_turno, turma_total_alunos FROM smc_turma WHERE turma_id_escola = '$escola' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' ORDER BY turma_turno, turma_etapa ASC";
$ListaTurmas = mysql_query($query_ListaTurmas, $SmecelNovo) or die(mysql_error());
$row_ListaTurmas = mysql_fetch_assoc($ListaTurmas);
$totalRows_ListaTurmas = mysql_num_rows($ListaTurmas);

if ($totalRows_ListaTurmas > 0 ) {
echo "<label>Turmas:<select name='smc_ativ_id_turma'>\n";
		echo "<option value=\"\" selected=\"selected\">SELECIONE UMA TURMA...</option>";
        	    do {
				echo "<option value=".$row_ListaTurmas['turma_id'].">".$row_ListaTurmas['turma_nome']."".turno($row_ListaTurmas['turma_turno'])." / Alunos: ".$row_ListaTurmas['turma_total_alunos']."</option>";
        	     } while ($row_ListaTurmas = mysql_fetch_assoc($ListaTurmas));
echo "</select></label>\n";
} else {
	
	echo "<option value=\"\" selected=\"selected\">NENHUMA TURMA ENCONTRADA</option>";
	
}
 
?>