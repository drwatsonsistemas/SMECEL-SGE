<?php
$colname_UsuarioLogado = "-1";
if (isset($_SESSION['MM_Username'])) {
	$colname_UsuarioLogado = $_SESSION['MM_Username'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_UsuarioLogado = sprintf("SELECT * FROM smc_usu WHERE usu_email = %s", GetSQLValueString($colname_UsuarioLogado, "text"));
$UsuarioLogado = mysql_query($query_UsuarioLogado, $SmecelNovo) or die(mysql_error());
$row_UsuarioLogado = mysql_fetch_assoc($UsuarioLogado);
$totalRows_UsuarioLogado = mysql_num_rows($UsuarioLogado);

if ($totalRows_UsuarioLogado < 1) {
	$redireciona = "anoletivonovo.php";
	header(sprintf("Location: %s", $redireciona));
}

if (empty($row_UsuarioLogado['usu_contato']) or empty($row_UsuarioLogado['usu_cargo'])) {
	//header("Location: dados.php?preencher"); 
	//exit;
	if (!strpos($_SERVER['PHP_SELF'], 'dados.php')) {
		header("Location: meus_dados.php?preencher");
		exit;
	}
}
?>