<?php


function logins ($id) {

$cod = $id;

//require_once('../../Connections/SmecelNovo.php');

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Logins = "SELECT login_aluno_id, login_aluno_id_aluno, login_aluno_data_hora, login_aluno_ip FROM smc_login_aluno WHERE login_aluno_id_aluno = $cod";
$Logins = mysql_query($query_Logins, $SmecelNovo) or die(mysql_error());
$row_Logins = mysql_fetch_assoc($Logins);
$totalRows_Logins = mysql_num_rows($Logins);

return $totalRows_Logins;
mysql_free_result($Logins);

}

?>