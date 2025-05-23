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
if (isset($_POST['user_id'])) {

	$user_id = anti_injection($_POST['user_id']);
	$fan_id = anti_injection($row_AlunoLogado['aluno_id']);

	// Verifica se o relacionamento existe antes de tentar deletar
	$checkSQL = sprintf("SELECT COUNT(*) as total FROM smc_aluno_fans 
		WHERE aluno_fan_aluno_de_id = %s 
		AND aluno_fan_aluno_para_id = %s",
		GetSQLValueString($fan_id, 'int'),
		GetSQLValueString($user_id, 'int'));

	$checkResult = mysql_query($checkSQL, $SmecelNovo) or die(mysql_error());
	$rowCheck = mysql_fetch_assoc($checkResult);

	if ($rowCheck['total'] > 0) {
		// Se existe, executa o DELETE
		$deleteSQL = sprintf("DELETE FROM smc_aluno_fans WHERE aluno_fan_aluno_de_id = %s AND aluno_fan_aluno_para_id = %s",
			GetSQLValueString($fan_id, 'int'),
			GetSQLValueString($user_id, 'int'));
		$Result2 = mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());		
		$response = ['success' => true, 'message' => 'Fã removido com sucesso.'];
	} else {
		// Se o relacionamento não existe
		$response = ['success' => false, 'message' => 'Você não é fã deste usuário.'];
	}
} else {
	$response = ['success' => false, 'message' => 'Dados inválidos.'];
}

echo json_encode($response);
?>
