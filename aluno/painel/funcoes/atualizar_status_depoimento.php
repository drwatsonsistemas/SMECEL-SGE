<?php require_once('../../../Connections/SmecelNovo.php'); ?>
<?php include('../../../sistema/funcoes/inverteData.php'); ?>
<?php include('../../../sistema/funcoes/url_base.php'); ?>
<?php include('../../../sistema/funcoes/idade.php'); ?>
<?php include('../../../sistema/funcoes/anti_injection.php'); ?>
<?php include('session.php'); ?>


<?php 

mysql_select_db($database_SmecelNovo, $SmecelNovo);
header('Content-Type: application/json');

// Verifique se os dados foram recebidos corretamente
if (isset($_POST['aluno_depoimento_id'])) {
	
	$aluno_depoimento_id = anti_injection($_POST['aluno_depoimento_id']);
	$user = anti_injection($row_AlunoLogado['aluno_id']);
	$aluno_depoimento_status = anti_injection($_POST['aluno_depoimento_status']);

	$insertSQL = sprintf("UPDATE smc_aluno_depoimentos SET aluno_depoimento_status = '$aluno_depoimento_status' WHERE aluno_depoimento_id_para = %s AND aluno_depoimento_id=%s",
		GetSQLValueString($user, 'int'),
		GetSQLValueString($aluno_depoimento_id, 'int')
	);

	$Result1 = mysql_query($insertSQL, $SmecelNovo);

	if ($Result1) {
		$response = ['success' => true, 'message' => 'Status atualizado!'];
	} else {
		$response = ['success' => false, 'message' => 'Erro ao atualizar o status: ' . mysql_error()];
	}
} else {
	$response = ['success' => false, 'message' => 'Dados não recebidos.'];
}

echo json_encode($response);
exit; // Garante que nada mais será enviado após o JSON