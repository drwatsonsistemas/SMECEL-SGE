<?php 
$colname_UsuLogado = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_UsuLogado = $_SESSION['MM_Username'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_UsuLogado = sprintf("SELECT * FROM smc_usu WHERE usu_email = %s", GetSQLValueString($colname_UsuLogado, "text"));
$UsuLogado = mysql_query($query_UsuLogado, $SmecelNovo) or die(mysql_error());
$row_UsuLogado = mysql_fetch_assoc($UsuLogado);
$totalRows_UsuLogado = mysql_num_rows($UsuLogado);

if ($row_UsuLogado['usu_aceite_lgpd']=="N") {
	
	header("Location: aceite.php"); 
 	exit;
	
	}

if(empty($row_UsuLogado['usu_contato']) OR empty($row_UsuLogado['usu_cargo'])){
		//header("Location: dados.php?preencher"); 
 		//exit;
	if(!strpos($_SERVER['PHP_SELF'], 'dados.php')){
		header("Location: dados.php?preencher"); 
		exit;
	}
}


?>