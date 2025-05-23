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
	$query_Usu = "SELECT *, escola_id, escola_nome, sec_id, sec_cidade FROM smc_usu LEFT JOIN smc_escola ON escola_id = usu_escola LEFT JOIN smc_sec ON sec_id = escola_id_sec WHERE usu_id = '$q'";
	$Usu = mysql_query($query_Usu, $SmecelNovo) or die(mysql_error());
	$row_Usu = mysql_fetch_assoc($Usu);
	$totalRows_Usu = mysql_num_rows($Usu);
	
	$primeiroNome = explode(" ", $row_Usu['usu_nome']);

	function ofuscaEmail($email, $inicio, $qtd){
		$asc = str_repeat('*', $qtd);
		return substr_replace($email, $asc, $inicio, $qtd);
	  }
	  
	  $email = ofuscaEmail($row_Usu['usu_email'], 2, 8);
	
	if ($totalRows_Usu > 0) {

	echo json_encode(array("replies" => array(
		array("message" => "Dados localizados:\n\nNome: " . $row_Usu['usu_nome'] . "\nEmail: " . $email . "\nEscola: ".$row_Usu['escola_nome']."\nCidade: ".$row_Usu['sec_cidade']."-".$row_Usu['sec_uf']),
		array("message" => "Certo, *".$primeiroNome[0]."* 👍🏼\n\nAgora, descreva qual o problema ou dificuldades que você está enfrentando e aguarde o atendimento de um analista.\n\n_(tente descrever o problema com o máximo de detalhes)_")
	)));

} else {

	echo json_encode(array("replies" => array(
		array("message" => "🚫 *Nenhum usuário localizado com este código.*"),
		array("message" => "Descreva agora qual o problema ou dificuldades que você está enfrentando e aguarde o atendimento de um analista.\n\n_(tente descrever o problema com o máximo de detalhes)_")

	)));

}
}

mysql_free_result($Usu);
?>