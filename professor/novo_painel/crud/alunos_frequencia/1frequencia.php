<?php


if(isset($_POST["matricula"])) {
	
require_once('../../../../Connections/SmecelNovo.php');

extract($_POST);

	  mysql_select_db($database_SmecelNovo, $SmecelNovo);
	  $query_Verifica = "
	  SELECT faltas_alunos_id, faltas_alunos_matricula_id, faltas_alunos_disciplina_id, faltas_alunos_numero_aula, faltas_alunos_data 
	  FROM smc_faltas_alunos 
	  WHERE faltas_alunos_matricula_id = '$matricula' AND faltas_alunos_data = '$data' AND faltas_alunos_numero_aula = '$aula_numero'";
	  $Verifica = mysql_query($query_Verifica, $SmecelNovo) or die(mysql_error());
	  $row_Verifica = mysql_fetch_assoc($Verifica);
	  $totalRows_Verifica = mysql_num_rows($Verifica);


if (empty($matricula)) {
			echo "<script>Swal.fire({ position: 'top-end', icon: 'error', title: 'Ops...',  text: 'Isso não deveria ter ocorrido', showConfirmButton: false, timer: 1000 })</script>";
			exit;

} elseif ($totalRows_Verifica > 0) {
	
	
			  $deleteSQL = sprintf("DELETE FROM smc_faltas_alunos WHERE faltas_alunos_id = '$row_Verifica[faltas_alunos_id]'");
			  mysql_select_db($database_SmecelNovo, $SmecelNovo);
			  $Result2 = mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());

			echo "<script>Swal.fire({ position: 'top-end', icon: 'success', title: 'Falta excluída',  text: '$aluno', showConfirmButton: false, timer: 1000 })</script>";
			exit;

} else {
	
	
			if ($multi == "s") {
				
				
				
				mysql_select_db($database_SmecelNovo, $SmecelNovo);
				$query_Outras = "
				SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, 
				ch_lotacao_escola, disciplina_id, disciplina_nome, disciplina_nome_abrev, turma_id, turma_ano_letivo, turma_turno
				FROM smc_ch_lotacao_professor
				INNER JOIN smc_disciplina ON disciplina_id = ch_lotacao_disciplina_id
				INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
				WHERE turma_ano_letivo = '$ano' 
				AND ch_lotacao_dia = '$dia' 
				AND ch_lotacao_professor_id = '$prof'
				AND ch_lotacao_turma_id = '$turma'
				ORDER BY turma_turno, ch_lotacao_aula ASC";
				$Outras = mysql_query($query_Outras, $SmecelNovo) or die(mysql_error());
				$row_Outras = mysql_fetch_assoc($Outras);
				$totalRows_Outras = mysql_num_rows($Outras);
				
				
				do {
					
					
			  $insertSQL = sprintf("INSERT INTO smc_faltas_alunos (faltas_alunos_matricula_id, faltas_alunos_disciplina_id, faltas_alunos_numero_aula, faltas_alunos_data) VALUES ('$matricula', '$disciplina', '$row_Outras[ch_lotacao_aula]', '$data')");
			  mysql_select_db($database_SmecelNovo, $SmecelNovo);
			  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());
					
					} while ($row_Outras = mysql_fetch_assoc($Outras));
					
					echo "<script>Swal.fire({ position: 'top-end', icon: 'success', title: '$totalRows_Outras falta(s) registrada(s)', text: '$aluno', showConfirmButton: false, timer: 1000 })</script>";
			  exit;
				
				
				
				} else {
			
			
			  $insertSQL = sprintf("INSERT INTO smc_faltas_alunos (faltas_alunos_matricula_id, faltas_alunos_disciplina_id, faltas_alunos_numero_aula, faltas_alunos_data) VALUES ('$matricula', '$disciplina', '$aula_numero', '$data')");
			  mysql_select_db($database_SmecelNovo, $SmecelNovo);
			  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());
			  
			   echo "<script>Swal.fire({ position: 'top-end', icon: 'success', title: 'Falta registrada', text: '$aluno', showConfirmButton: false, timer: 1000 })</script>";
			  exit;
			  
				}
			  
			  echo "<script>Swal.fire({ position: 'top-end', icon: 'success', title: 'Falta registrada', text: '$aluno', showConfirmButton: false, timer: 1000 })</script>";
			  exit;

}
} else {
	
	echo "Como é que você veio parar aqui?<br>";
	
	function get_client_ip() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;

	
} echo get_client_ip();

header("Location:../../index.php?err");
	
	}
?>