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
	
	include "Connections/SmecelNovo.php";
    include "sistema/funcoes/anti_injection.php";

	$q = anti_injection($message);
	

	mysql_select_db($database_SmecelNovo, $SmecelNovo);
	$query_Prof = "SELECT * FROM smc_func WHERE func_id = '$q'";
	$Prof = mysql_query($query_Prof, $SmecelNovo) or die(mysql_error());
	$row_Prof = mysql_fetch_assoc($Prof);
	$totalRows_Prof = mysql_num_rows($Prof);
	

	
	if ($totalRows_Prof > 0) {

	echo json_encode(array("replies" => array(
		array("message" => "Servidor(a) encontrado(a):"),
		array("message" => "*" . $row_Prof['func_nome'] . "*")
	)));

} else {

	echo json_encode(array("replies" => array(
		array("message" => "Nenhum servidor localizado com este código")
	)));

}
}


?>