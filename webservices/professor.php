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
	$query_Prof = "SELECT *, sec_id, sec_cidade, sec_uf FROM smc_func LEFT JOIN smc_sec ON sec_id = func_id_sec WHERE func_id = '$q'";
	$Prof = mysql_query($query_Prof, $SmecelNovo) or die(mysql_error());
	$row_Prof = mysql_fetch_assoc($Prof);
	$totalRows_Prof = mysql_num_rows($Prof);
	
	$primeiroNome = explode(" ", $row_Prof['func_nome']);
	
	if ($totalRows_Prof > 0) {

	echo json_encode(array("replies" => array(
		array("message" => "Dados localizados: \n\nNome: " . $row_Prof['func_nome'] . "\nMunicípio: ".$row_Prof['sec_cidade']."-".$row_Prof['sec_uf']),
		array("message" => "Certo, *".$primeiroNome[0]."* 👍🏼\n\nDescreva agora qual o problema ou dificuldades que você está enfrentando e aguarde o atendimento de um analista.\n\n_(tente descrever o problema com o máximo de detalhes)_")
	)));

} else {

	echo json_encode(array("replies" => array(
		array("message" => "🚫 *Nenhum servidor localizado com este código.*"),
		array("message" => "Descreva agora qual o problema ou dificuldades que você está enfrentando e aguarde o atendimento de um analista.\n\n_(tente descrever o problema com o máximo de detalhes)_")
	)));

}
}


?>