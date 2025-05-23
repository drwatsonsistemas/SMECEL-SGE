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


  function anti_injection($sql){
   $sql = preg_replace(sql_regcase("/(from|select|insert|delete|where|drop table|show tables|#|\*|--|\\\\)/"), "" ,$sql);
   $sql = trim($sql);
   $sql = strip_tags($sql);
   $sql = (get_magic_quotes_gpc()) ? $sql : addslashes($sql);
   return $sql;
  }


if (empty($_POST['email'])) {
		
		echo "<div class=\"card-panel red darken-4\" style=\"color:white\"><i class=\"material-icons\">error_outline</i> Informe um e-mail.</div>";
		exit;
	
	} else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
  
 
 	echo "<div class=\"card-panel red darken-4\" style=\"color:white\"><i class=\"material-icons\">error_outline</i> Este e-mail não é válido.</div>";
 	exit;
 
	} else {
 
$email	=	anti_injection($_POST['email']);  
  //FALTA TRATAR CONTRA SQL-INJECTION

  
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_verifica = "SELECT usu_nome, usu_email, usu_senha, usu_tipo, usu_status FROM smc_usu WHERE usu_email='$email'";
$verifica = mysql_query($query_verifica, $SmecelNovo) or die(mysql_error());
$row_verifica = mysql_fetch_assoc($verifica);
$totalRows_verifica = mysql_num_rows($verifica);

$nome = $row_verifica['usu_nome'];	
$senha = $row_verifica['usu_senha'];
  
  // Verifica se o nome foi preenchido
if (empty($email)) {
	echo "<div class=\"card-panel red darken-4 right-align\" style=\"color:white\"><i class=\"material-icons right-align\">error_outline</i> O campo E-mail não pode ficar em branco.</div>";
	echo "
	<script> 
	//document.location = 'sistema/index.php'; 
	window.setTimeout(\"document.location='lembrarsenha.php'\",3000)
	</script>
	";
} else if ($totalRows_verifica == 0) {	
	echo "<div class=\"card-panel red darken-4\" style=\"color:white\"><i class=\"material-icons\">error_outline</i> E-mail não encontrado.</div>";
echo "
	<script> 
	//document.location = 'sistema/index.php'; 
	window.setTimeout(\"document.location='lembrarsenha.php'\",3000)
	</script>
	";	
} else {
	
	// Inclui o arquivo class.phpmailer.php localizado na pasta class
require_once("classes/class.phpmailer.php");
 
// Inicia a classe PHPMailer
$mail = new PHPMailer(true);
 
// Define os dados do servidor e tipo de conexão
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
$mail->IsSMTP(); // Define que a mensagem será SMTP
 
try {
     $mail->Host = 'smtp.smecel.com.br'; // Endereço do servidor SMTP (Autenticação, utilize o host smtp.seudomínio.com.br)
     $mail->SMTPAuth   = true;  // Usar autenticação SMTP (obrigatório para smtp.seudomínio.com.br)
     $mail->Port       = 587; //  Usar 587 porta SMTP
     $mail->Username = 'suporte@smecel.com.br'; // Usuário do servidor SMTP (endereço de email)
     $mail->Password = 'Drw4tson@smecel'; // Senha do servidor SMTP (senha do email usado)
	 $mail->IsHTML(true);
	 $mail->CharSet = "UTF-8";
 
     //Define o remetente
     // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=    
     $mail->SetFrom('suporte@smecel.com.br', 'Suporte SMECEL'); //Seu e-mail
     //$mail->AddReplyTo('seu@e-mail.com.br', 'Nome'); //Seu e-mail
     $mail->Subject = "Recuperação da senha de acesso ao sistema [SMECEL]";//Assunto do e-mail
 
 
     //Define os destinatário(s)
     //=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
     $mail->AddAddress($email);
 
     //Campos abaixo são opcionais 
     //=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
     //$mail->AddCC('destinarario@dominio.com.br', 'Destinatario'); // Copia
     //$mail->AddBCC('destinatario_oculto@dominio.com.br', 'Destinatario2`'); // Cópia Oculta
     //$mail->AddAttachment('images/phpmailer.gif');      // Adicionar um anexo
 
 
     //Define o corpo do email
     $mail->MsgHTML("
	 
	 <p>Prezado(a) $nome,</p>
	 <p>Sua senha de acesso ao sistema SMECEL é: <br><br><strong>$senha</strong></p>
	 <p>Faça login utilizando o e-mail <strong>$email</strong> e a senha informada acima.</p>
	 <p>Atenciosamente,<br>Equipe de suporte.<br>www.smecel.com.br</p>
   <img src=\"https://www.smecel.com.br/img/logo_smecel_background_flattened.png\" width=\"150\">
	 "); 
 
     ////Caso queira colocar o conteudo de um arquivo utilize o método abaixo ao invés da mensagem no corpo do e-mail.
     //$mail->MsgHTML(file_get_contents('arquivo.html'));
 
     $mail->Send();
     echo "<div class=\"card-panel green darken-3\"><strong style=\"color:white\">E-mail de recuperação enviado com sucesso.</strong></div>";
 		
	 $mail->ClearAllRecipients();
     $mail->ClearAttachments();
		
    //caso apresente algum erro é apresentado abaixo com essa exceção.
    }catch (phpmailerException $e) {
      echo $e->errorMessage(); //Mensagem de erro costumizada do PHPMailer
}
	
	echo "
	<script> 
	//document.location = 'sistema/index.php'; 
	window.setTimeout(\"document.location='index.php'\",3000)
	</script>
	";
  
    //header("Location: ". $MM_redirectLoginFailed );
  }
}  

?>