<?php 
if (isset($_FILES['atividade'])) {

require_once('../../Connections/SmecelNovo.php'); 

include "../../sistema/funcoes/class.upload.php"; 
 
$foo = new upload($_FILES['atividade']);

	$id_resposta 	= $_GET['aula'];
	$id_aluno 		= $_GET['aluno'];
	$novo_nome 		= md5(time())."_".$id_resposta."_".$id_aluno;
	$nova_data		= date('Y-m-d H:i:s');

$handle = new Upload($_FILES['atividade']);

if ($handle->uploaded) 
{ 

$handle->file_new_name_body = $novo_nome;
$handle->mime_check = true;
$handle->allowed = array('application/pdf','application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/*');
$handle->file_max_size = '20242880'; // 1KB
$handle->Process('../../anexos_respostas/'.$id_resposta.'/');

if ($handle->processed) 
{

$nome_do_arquivo = $handle->file_dst_name;

  $insertSQL = sprintf("INSERT INTO smc_plano_aula_anexo_atividade (plano_aula_anexo_atividade_id_aluno, plano_aula_anexo_atividade_id_atividade, plano_aula_anexo_atividade_caminho, plano_aula_anexo_atividade_data_hora, plano_aula_anexo_atividade_visualizada_professor) VALUES ('$id_aluno', '$id_resposta', '$nome_do_arquivo', '$nova_data', 'N')");

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error()); 
	   	    
	   
     echo 'Atividade enviada com sucesso! Aguarde a página atualizar.';
	 
	 
   } else {
     echo 'error : ' . $foo->error;
   }

}  

} else {
	echo "Ops! Ocorreu um erro. Aguarde alguns instantes e tente novamente.";
}
?>