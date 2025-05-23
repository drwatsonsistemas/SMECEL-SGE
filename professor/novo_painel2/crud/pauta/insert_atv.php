<?php
if(isset($_POST)) {

require_once('../../../../Connections/SmecelNovo.php');	
include "../../fnc/anti_injection.php";
//include "../../conf/session.php";
		
extract($_POST);

$id_pauta = anti_injection($id_pauta);
$id_atividade = anti_injection($id_atividade);

$deleteSQL = sprintf("INSERT INTO smc_pauta_adiciona_atv (pauta_atv, pauta_id_pauta) VALUES ($id_atividade, $id_pauta) ");
			  mysql_select_db($database_SmecelNovo, $SmecelNovo);
			  $Result2 = mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());
exit;		
}	
?>