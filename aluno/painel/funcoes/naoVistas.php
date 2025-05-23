<?php //require_once('../../Connections/SmecelNovo.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

function naoVistas($turma, $aula, $aluno) {

require_once('../../Connections/SmecelNovo.php');
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Conteudo = "
SELECT plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, 
plano_aula_data, plano_aula_texto, plano_aula_conteudo, plano_aula_atividade, plano_aula_video, plano_aula_publicado, plano_aula_hash, func_id, func_nome, disciplina_id, disciplina_nome 
FROM smc_plano_aula
INNER JOIN smc_func ON func_id = plano_aula_id_professor
INNER JOIN smc_disciplina ON disciplina_id = plano_aula_id_disciplina
WHERE plano_aula_id_turma = '$turma' AND plano_aula_publicado = 'S' AND plano_aula_data <= NOW()
ORDER BY plano_aula_data DESC
";
$Conteudo = mysql_query($query_Conteudo, $SmecelNovo) or die(mysql_error());
$row_Conteudo = mysql_fetch_assoc($Conteudo);
$totalRows_Conteudo = mysql_num_rows($Conteudo); 

$novasAulas = 0;

do {

		mysql_select_db($database_SmecelNovo, $SmecelNovo);
		$query_Visualizou = "SELECT visualiza_aula_id, visualiza_aula_id_aula, visualiza_aula_id_matricula, visualiza_aula_data_hora FROM smc_visualiza_aula WHERE visualiza_aula_id_aula = $aula AND visualiza_aula_id_matricula = '$aluno'";
		$Visualizou = mysql_query($query_Visualizou, $SmecelNovo) or die(mysql_error());
		$row_Visualizou = mysql_fetch_assoc($Visualizou);
		$totalRows_Visualizou = mysql_num_rows($Visualizou);

		if ($totalRows_Visualizou > 0) {
			
				$totalRows_Conteudo--;
		
		}

//mysql_free_result($Visualizou);	
} while ($row_Conteudo = mysql_fetch_assoc($Conteudo));

mysql_free_result($naoVistas);

return $totalRows_Conteudo;

}
?>
