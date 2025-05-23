<?php
date_default_timezone_set("America/Bahia");
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"

/*
$hostname_SmecelNovo = "177.153.63.56";
$database_SmecelNovo = "smecel_dev";
$username_SmecelNovo = "smecel_dev";
$password_SmecelNovo = "Smecel@dev1";
*/

$hostname_SmecelNovo = "localhost";
$database_SmecelNovo = "smecel1";
$username_SmecelNovo = "root";
$password_SmecelNovo = "";

//error_reporting(0);

/* try {
	$SmecelNovo = @mysql_pconnect($hostname_SmecelNovo, $username_SmecelNovo, $password_SmecelNovo);
} catch (Exception $e) {
    return "Não houve conexão";
} */

$SmecelNovo = @mysql_pconnect($hostname_SmecelNovo, $username_SmecelNovo, $password_SmecelNovo) or trigger_error(mysql_error(),E_USER_ERROR); 

if (!$SmecelNovo) die ("<h3><a href='https://www.smecel.com.br/index.php'>Voltar</a></h3>");

?>