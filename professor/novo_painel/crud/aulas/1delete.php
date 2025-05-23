<?php
if(isset($_POST)) {

require_once('../../../../Connections/SmecelNovo.php');	
include "../../fnc/anti_injection.php";
		
extract($_POST);

$aula = anti_injection($aula);

$deleteSQL = sprintf("DELETE FROM smc_plano_aula WHERE plano_aula_hash = '$aula'");
			  mysql_select_db($database_SmecelNovo, $SmecelNovo);
			  $Result2 = mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());

echo "<script>
		Swal.fire({ 
			position: 'top-end', 
			icon: 'success', 
			title: 'Aula excluída',  
			text: '', 
			showConfirmButton: false, 
			timer: 2000 
		});
	</script>";
exit;		
}	
?>