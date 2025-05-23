<?php require_once('../../Connections/SmecelNovo.php'); 

function contarVinculos($id){

$idfuncao = $id;	

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Vinculo = "SELECT vinculo_id, vinculo_id_escola, vinculo_id_funcionario, vinculo_id_funcao FROM smc_vinculo WHERE vinculo_id_funcao = '$idfuncao' GROUP BY vinculo_id_funcao";
$Vinculo = mysql_query($query_Vinculo, $SmecelNovo) or die(mysql_error());
$row_Vinculo = mysql_fetch_assoc($Vinculo);
$totalRows_Vinculo = mysql_num_rows($Vinculo);

}

?>