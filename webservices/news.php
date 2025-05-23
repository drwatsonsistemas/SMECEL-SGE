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

//Drw4tson@smecel12345 
//info@smecel.com.br


//mysql_set_charset("utf8");

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Atualizacao = "SELECT atualizacoes_id, atualizacoes_painel, atualizacoes_modulo, atualizacoes_texto, atualizacoes_data FROM smc_atualizacoes ORDER BY atualizacoes_id DESC LIMIT 0,1";
$Atualizacao = mysql_query($query_Atualizacao, $SmecelNovo) or die(mysql_error());
$row_Atualizacao = mysql_fetch_assoc($Atualizacao);
$totalRows_Atualizacao = mysql_num_rows($Atualizacao);


$tipoQry = "";
$tipoPainel = "";

switch ($row_Atualizacao['atualizacoes_painel']) {
	
	case 1:
	$tipoQry = " AND usu_tipo IN (1,99)";
	$tipoPainel = "PAINEL SECRETARIA DE EDUCAÇÃO";
	break;
	
	case 2:
	$tipoQry = " AND usu_tipo IN (1,2,99)";
	$tipoPainel = "PAINEL SECRETARIA ESCOLAR";
	break;
	
	case 3:
	$tipoQry = " AND usu_tipo IN (1,2,99)";
	$tipoPainel = "PAINEL DO PROFESSOR";
	break;
	
	case 4:
	$tipoQry = " AND usu_tipo IN (1,2,99)";
	$tipoPainel = "PAINEL DO ALUNO";
	break;
	
	case 5:
	$tipoQry = " AND usu_tipo IN (1,2,99)";
	$tipoPainel = "PAINEL DA PORTARIA";
	break;
	
	case 6:
	$tipoQry = " AND usu_tipo IN (1,2,99)";
	$tipoPainel = "PAINEL DO PSE - PROGRAMA SAÚDE NA ESCOLA";
	break;

	case 99:
	$tipoQry = " AND usu_tipo IN (99)";
	$tipoPainel = "PAINEL GPI";
	break;
	
	default:
	$tipoQry = "";
	$tipoPainel = "";

}







$ultima = $row_Atualizacao['atualizacoes_id'];
//USUARIOS
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Usuarios = "
SELECT usu_id, usu_nome, usu_email, usu_senha, usu_tipo, usu_sec, usu_escola, usu_status, usu_cadastro, usu_insert, usu_update, usu_delete, usu_m_ava, usu_m_administrativo, usu_m_formacao, usu_m_transporte, 
usu_m_merenda, usu_m_patrimonio, usu_m_relatorios, usu_m_graficos, usu_m_configuracoes, usu_foto, usu_aceite_lgpd, usu_aceite_lgpd_data, usu_ultima_atualizacao 
FROM smc_usu 
WHERE usu_status = '1' AND usu_ultima_atualizacao <> '$ultima' $tipoQry ORDER BY RAND()
";
$Usuarios = mysql_query($query_Usuarios, $SmecelNovo) or die(mysql_error());
$row_Usuarios = mysql_fetch_assoc($Usuarios);
$totalRows_Usuarios = mysql_num_rows($Usuarios);

$nome = $row_Usuarios['usu_nome'];
$primeiroNome = explode(" ", $nome);
$email = $row_Usuarios['usu_email'];
$data = date("d/m/Y", strtotime($row_Atualizacao['atualizacoes_data']));
$modulo = $row_Atualizacao['atualizacoes_modulo'];
$at = $row_Atualizacao['atualizacoes_texto'];
	

if ($totalRows_Usuarios > 0) {	
	
	// Inclui o arquivo class.phpmailer.php localizado na pasta class
require_once("../classes/class.phpmailer.php");
 
// Inicia a classe PHPMailer
$mail = new PHPMailer(true);
 
// Define os dados do servidor e tipo de conexão
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
$mail->IsSMTP(); // Define que a mensagem será SMTP
 
try {
     $mail->Host = 'smtp.smecel.com.br'; // Endereço do servidor SMTP (Autenticação, utilize o host smtp.seudomínio.com.br)
     $mail->SMTPAuth   = true;  // Usar autenticação SMTP (obrigatório para smtp.seudomínio.com.br)
     $mail->Port       = 587; //  Usar 587 porta SMTP
     $mail->Username = 'info@smecel.com.br'; // Usuário do servidor SMTP (endereço de email)
     $mail->Password = 'Drw4tson@smecel12345'; // Senha do servidor SMTP (senha do email usado)
	 $mail->IsHTML(true);
	 $mail->CharSet = "UTF-8";
 
     //Define o remetente
     // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=    
     $mail->SetFrom('info@smecel.com.br', 'ATUALIZAÇÕES SMECEL'); //Seu e-mail
     //$mail->AddReplyTo('seu@e-mail.com.br', 'Nome'); //Seu e-mail
     $mail->Subject = $data." | ".$modulo;//Assunto do e-mail
 
 
     //Define os destinatário(s)
     //=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
     $mail->AddAddress($email);
 
     //Campos abaixo são opcionais 
     //=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
     //$mail->AddCC('destinarario@dominio.com.br', 'Destinatario'); // Copia
     //$mail->AddBCC('rcamaral@outlook.com'); // Cópia Oculta
     //$mail->AddAttachment('images/phpmailer.gif');      // Adicionar um anexo
 
 
     //Define o corpo do email
     $mail->MsgHTML("
	 Olá, $primeiroNome[0]!<br><br>
	 Acompanhe a última atualização no sistema SMECEL.<br><br>
	 Data: <strong>$data</strong><br>
	 Módulo: <strong>$modulo</strong><br>
	 Painel: <strong>$tipoPainel</strong><br><br>
	 Descrição: <br>
	 <blockquote><i>$at</i></blockquote>
	 <hr>
	 <p>Atenciosamente,<br>Equipe de suporte.<br>www.smecel.com.br</p>
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
UPDATE smc_usu SET usu_ultima_atualizacao = '$ultima' WHERE usu_id = '$row_Usuarios[usu_id]'
";
$Update = mysql_query($query_update, $SmecelNovo) or die(mysql_error());

}

mysql_free_result($Usuarios);

mysql_free_result($Atualizacao);
?>
