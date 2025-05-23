<?php 
$colname_UsuLogado = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_UsuLogado = $_SESSION['MM_Username'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_UsuLogado = sprintf("SELECT usu_id, usu_nome, usu_email, usu_senha, usu_tipo, usu_sec, usu_escola, usu_status, usu_cadastro, usu_insert, usu_update, usu_delete FROM smc_usu WHERE usu_email = %s", GetSQLValueString($colname_UsuLogado, "text"));
$UsuLogado = mysql_query($query_UsuLogado, $SmecelNovo) or die(mysql_error());
$row_UsuLogado = mysql_fetch_assoc($UsuLogado);
$totalRows_UsuLogado = mysql_num_rows($UsuLogado);
?>