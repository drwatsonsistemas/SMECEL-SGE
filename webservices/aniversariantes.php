<?php require_once('../Connections/SmecelNovo.php'); ?>
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


$mes = date("m");
$dia = date("d");
$ano = date("Y");

		

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_aniversariantesMes = "
SELECT 
func_id, func_nome, func_data_nascimento, func_email, func_lembrete_aniversario, func_envia_email,
DATE_FORMAT(func_data_nascimento, '%m%d') AS aniversario, DATE_FORMAT(func_data_nascimento, '%d/%m') AS data_aniversario, 
DATE_FORMAT(func_data_nascimento, '%d') as dia_aniversario, DATE_FORMAT(func_data_nascimento, '%m') as mes_aniversario
FROM smc_func
WHERE func_envia_email = 'S' AND func_email IS NOT NULL AND Month(func_data_nascimento) = '$mes' AND Day(func_data_nascimento) = '$dia' AND func_lembrete_aniversario <> '$ano' ORDER BY aniversario, func_nome ASC LIMIT 0,1";
$aniversariantesMes = mysql_query($query_aniversariantesMes, $SmecelNovo) or die(mysql_error());
$row_aniversariantesMes = mysql_fetch_assoc($aniversariantesMes);
$totalRows_aniversariantesMes = mysql_num_rows($aniversariantesMes);

$nome = trim($row_aniversariantesMes['func_nome']);
$primeiroNome = explode(" ", $nome);
$email = $row_aniversariantesMes['func_email'];
$aniversario = $row_aniversariantesMes['data_aniversario'];
	

if ($totalRows_aniversariantesMes > 0) {	
	
	// Inclui o arquivo class.phpmailer.php localizado na pasta class
require_once("../classes/class.phpmailer.php");
 
// Inicia a classe PHPMailer
$mail = new PHPMailer(true);
 
// Define os dados do servidor e tipo de conexão
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
$mail->IsSMTP(); // Define que a mensagem será SMTP
 
try {
     $mail->Host 		= 'smtp.smecel.com.br'; // Endereço do servidor SMTP (Autenticação, utilize o host smtp.seudomínio.com.br)
     $mail->SMTPAuth   	= true;  // Usar autenticação SMTP (obrigatório para smtp.seudomínio.com.br)
     $mail->Port       	= 587; //  Usar 587 porta SMTP
     $mail->Username 	= 'info@smecel.com.br'; // Usuário do servidor SMTP (endereço de email)
     $mail->Password 	= 'Drw4tson@smecel12345'; // Senha do servidor SMTP (senha do email usado)
	 $mail->CharSet 	= "UTF-8";
	 $mail->IsHTML(true);
 
     //Define o remetente
     // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=    
     $mail->SetFrom('info@smecel.com.br', 'SMECEL - Sistema de Gestão Escolar'); //Seu e-mail
     //$mail->AddReplyTo('seu@e-mail.com.br', 'Nome'); //Seu e-mail
     $mail->Subject = "Feliz Aniversário, " .$primeiroNome[0];//Assunto do e-mail
 
 
     //Define os destinatário(s)
     //=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
     //$mail->AddAddress('rcamaral@outlook.com');
     $mail->AddAddress($email);
 
     //Campos abaixo são opcionais 
     //=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
     //$mail->AddCC('destinarario@dominio.com.br', 'Destinatario'); // Copia
     //$mail->AddBCC('rcamaral@outlook.com'); // Cópia Oculta
     //$mail->AddAttachment('images/phpmailer.gif');      // Adicionar um anexo
 
 
     //Define o corpo do email
     $mail->MsgHTML("
	 Feliz Aniversário, <b>$primeiroNome[0]</b>!<br><br>
	 
		Hoje, dia $aniversario, mais do que comemorar apenas o seu aniversário, é um momento para celebrar você e a incrível contribuição que oferece ao ambiente educacional. Parabéns pelo seu dia!<br><br>
	
		Neste aniversário, queremos expressar nossa gratidão por sua dedicação e comprometimento em tornar nossa instituição de ensino um lugar verdadeiramente inspirador. Seu trabalho incansável é fundamental para o sucesso de nossos alunos e para a construção de um ambiente acolhedor e educativo.<br><br>
		
		Seu esforço diário, sua paixão pelo que faz e seu papel crucial na formação das mentes e na construção de um futuro melhor para as gerações vindouras não passam despercebidos. Cada ação sua faz diferença e é valorizada por todos nós.<br><br>
		
		Que este aniversário seja um momento de celebração não apenas da passagem do tempo, mas também de reconhecimento do impacto positivo que você tem em nossa comunidade educacional. Que continue inspirando, moldando vidas e fazendo a diferença com seu trabalho dedicado e apaixonado.<br><br>
		
		Desejamos a você um novo ciclo repleto de oportunidades de crescimento pessoal e profissional, momentos de alegria, realização e sucesso em todos os seus empreendimentos.<br><br>
		
		Feliz aniversário! Que esta data especial seja o início de um ano incrível e cheio de conquistas para você.<br><br>
		
		Com os mais sinceros votos de felicidades,<br><br>
	 
	 	<b>Equipe SMECEL</b><br><br>
	 <hr>
	 <img src=\"https://www.smecel.com.br/img/logo_smecel_background_flattened.png\" width=\"150\">
	 "); 
 
     ////Caso queira colocar o conteudo de um arquivo utilize o método abaixo ao invés da mensagem no corpo do e-mail.
     //$mail->MsgHTML(file_get_contents('arquivo.html'));
 
     $mail->Send();
 		
	 $mail->ClearAllRecipients();
     $mail->ClearAttachments();
		
    //caso apresente algum erro é apresentado abaixo com essa exceção.
    }catch (phpmailerException $e) {
      echo $e->errorMessage(); //Mensagem de erro costumizada do PHPMailer
}
	
  
    //header("Location: ". $MM_redirectLoginFailed );

 


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_update = "
UPDATE smc_func SET func_lembrete_aniversario = '$ano' WHERE func_id = '$row_aniversariantesMes[func_id]'
";
$Update = mysql_query($query_update, $SmecelNovo) or die(mysql_error());

}


mysql_free_result($aniversariantesMes);
?>
