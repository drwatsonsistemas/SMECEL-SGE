<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$myheader = $_SERVER['HTTP_XXXXXX_XXXX'];
  
$data = json_decode(file_get_contents("php://input"));
  
if(
	!empty($data->query) &&
	!empty($data->appPackageName) &&
	!empty($data->messengerPackageName) &&
	!empty($data->query->sender) &&
	!empty($data->query->message)
){
	
	$appPackageName = $data->appPackageName;
	$messengerPackageName = $data->messengerPackageName;
	$sender = $data->query->sender;
	$message = $data->query->message;
	$isGroup = $data->query->isGroup;
	$groupParticipant = $data->query->groupParticipant;
	$ruleId = $data->query->ruleId;
	$isTestMessage = $data->query->isTestMessage;
	
	include "../Connections/SmecelNovo.php";
    include "../sistema/funcoes/anti_injection.php";

	$q = anti_injection($message);
	

	mysql_select_db($database_SmecelNovo, $SmecelNovo);
	$query_Escola = "SELECT * FROM smc_escola INNER JOIN smc_sec ON sec_id = escola_id_sec WHERE escola_id = '$q'";
	$Escola = mysql_query($query_Escola, $SmecelNovo) or die(mysql_error());
	$row_Escola = mysql_fetch_assoc($Escola);
	$totalRows_Escola = mysql_num_rows($Escola);
	
	if ($totalRows_Escola > 0) {

	echo json_encode(array("replies" => array(
		array("message" => "ESCOLA:"),
		array("message" => "*".$row_Escola['escola_inep']." - ".$row_Escola['escola_nome']."*\n".$row_Escola['escola_endereco']." ".$row_Escola['escola_numero']." ".$row_Escola['escola_bairro']."\n".$row_Escola['sec_cidade']."-".$row_Escola['sec_uf']."\n".$row_Escola['sec_cep']."\n".$row_Escola['escola_telefone1']." ".$row_Escola['escola_telefone2']."\n".$row_Escola['escola_email'])
	)));

} else {

	echo json_encode(array("replies" => array(
		array("message" => "🚫 *Nenhum escola localizada com este código.*")
	)));

}
}

mysql_free_result($Escola);
?>