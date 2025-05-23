<?php 
if (isset($_FILES['video'])) {

require_once('../../Connections/SmecelNovo.php'); 

include "../../sistema/funcoes/class.upload.php"; 
 
$foo = new upload($_FILES['video']);

$aula = $_GET['aula']; 
$turma = $_GET['turma']; 
$componente = $_GET['componente']; 
$professor = $_GET['professor']; 

if ($foo->uploaded) {
   // save uploaded image with no changes
   
$dataCod = date("YmdHis");
   
$foo->file_new_name_body = $aula."_".$turma."_".$componente."_".$professor;
$foo->file_name_body_add = "_".$dataCod;
$foo->allowed = array ( 'video/*' );
$foo->mime_check = true;
$foo->file_max_size = '1553674800';

$foo->process('../../videoaula/'.$turma.'/'.$aula.'/'.$professor.'/'.$componente.'/');
$nome_do_arquivo = $foo->file_dst_name;
   
   if ($foo->processed) {
	   	   
	 
	$insertSQL = sprintf("
	INSERT INTO smc_videoaula (videoaula_id_aula, videoaula_id_professor, videoaula_id_turma, videoaula_id_componente, videoaula_nome) 
	VALUES ('$aula', '$professor', '$turma', '$componente', '$nome_do_arquivo')
	");

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());   
	   	    
	   
     echo 'Vídeo aula enviada com sucesso!';
	 
	 
   } else {
     echo 'error : ' . $foo->error;
   }

}  

} else {
	echo "Algo errado não está certo. Você não pode chegar aqui desta forma.";
}
?>