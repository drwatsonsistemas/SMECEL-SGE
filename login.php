<?php require_once('Connections/SmecelNovo.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}
?>
<?php
// *** Validate request to login to this site.
if (!isset($_SESSION)) {
  session_start();
}

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

if (isset($_POST['usuario'])) {
  
  
  //FALTA TRATAR CONTRA SQL-INJECTION
  
  function anti_injection($sql){
   $sql = preg_replace(sql_regcase("/(from|select|insert|delete|where|drop table|show tables|#|\*|--|\\\\)/"), "" ,$sql);
   $sql = trim($sql);
   $sql = strip_tags($sql);
   $sql = (get_magic_quotes_gpc()) ? $sql : addslashes($sql);
   return $sql;
}
  
  
  $loginUsername	=	anti_injection($_POST['usuario']);
  $password			=	anti_injection($_POST['senha']);
  
  
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_verifica = "SELECT usu_email, usu_senha, usu_tipo, usu_status FROM smc_usu WHERE usu_email='$loginUsername' AND usu_senha='$password'";
$verifica = mysql_query($query_verifica, $SmecelNovo) or die(mysql_error());
$row_verifica = mysql_fetch_assoc($verifica);
$totalRows_verifica = mysql_num_rows($verifica);

  
  // Verifica se o nome foi preenchido
if (empty($loginUsername)) {
	//echo "<blockquote>O campo E-mail não pode ficar em branco.</blockquote>";
	echo "<script>
			  M.toast({
				html: '<i class=\"material-icons\">warning</i>&nbsp;Campo E-mail deve ser preenchido.',
				classes: 'orange darken-3'
			});
              </script>";
} 
else if (empty($password)) {
	
	//echo "<blockquote>O campo Senha não pode ficar em branco.<blockquote>";
	echo "<script>
			  M.toast({
				html: '<i class=\"material-icons\">warning</i>&nbsp;Campo Senha deve ser preenchido.',
				classes: 'orange darken-3'
			});
              </script>";
	}
  
else if ($totalRows_verifica == 0) {
	
	//echo "<blockquote>Login ou senha não conferem.</blockquote>";
	echo "<script>
			  M.toast({
				html: '<i class=\"material-icons\">warning</i>&nbsp;Usuário e/ou senha não conferem.',
				classes: 'orange darken-3'
			});
              </script>";
	}

else if ($row_verifica['usu_status'] == "2") {
	
	//echo "<blockquote>Usuário não autorizado.</blockquote>";
	echo "<script>
			  M.toast({
				html: '<i class=\"material-icons\">warning</i>&nbsp;Acesso não autorizado. Procure a Secretaria de Educação.',
				classes: 'orange darken-3'
			});
              </script>";
	}
	
	
else { 
  
  $MM_fldUserAuthorization = "usu_tipo";
  $MM_redirectLoginSuccess = "sistema/index.php";
  $MM_redirectLoginFailed = "index.php?err";
  $MM_redirecttoReferrer = false;
  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  	
  $LoginRS__query=sprintf("SELECT usu_email, usu_senha, usu_tipo FROM smc_usu WHERE usu_email=%s AND usu_senha=%s",
  GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text")); 
   
  $LoginRS = mysql_query($LoginRS__query, $SmecelNovo) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);
  if ($loginFoundUser) {
    
    $loginStrGroup  = mysql_result($LoginRS,0,'usu_tipo');
    
	if (PHP_VERSION >= 5.1) {session_regenerate_id(true);} else {session_regenerate_id();}
    //declare two session variables and assign them
    $_SESSION['MM_Username'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;	      

    if (isset($_SESSION['PrevUrl']) && false) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
    }
	//echo "<div class=\"card-panel\">Usuário autorizado. <strong>Entrando...</strong></div>";
	echo "<script>
			  M.toast({
				html: '<i class=\"material-icons\">check_circle</i>&nbsp;Usuário autorizado. Entrando...',
				classes: 'green darken-1'
			});
              </script>";
	echo "
	<script> 
	//document.location = 'sistema/index.php'; 
	window.setTimeout(\"document.location='sistema/index.php'\",2000)
	</script>
	";
	//header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
	echo "<script> document.location = 'index.php?err' </script>";  
    //header("Location: ". $MM_redirectLoginFailed );
  }
}

}

?>