<?php 
require_once '../Connections/SmecelNovo.php';
require_once("../classes/class.phpmailer.php");


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_verifica = "SELECT * FROM divulgacao2 WHERE divulgacao_enviado = 'N' ORDER BY RAND() LIMIT 0,1";
$verifica = mysql_query($query_verifica, $SmecelNovo) or die(mysql_error());
$row_verifica = mysql_fetch_assoc($verifica);
$totalRows_verifica = mysql_num_rows($verifica);

do {

  $time = time();

  $email = strtolower($row_verifica['divulgacao_email']);

  $mail = new PHPMailer(true);
  $mail->IsSMTP(); // Define que a mensagem será SMTP
   
  try {



       //$mail->Host = 'smtplw.com.br'; // Endereço do servidor SMTP (Autenticação, utilize o host smtp.seudomínio.com.br)
       $mail->Host = 'email-ssl.com.br'; // Endereço do servidor SMTP (Autenticação, utilize o host smtp.seudomínio.com.br)
       $mail->SMTPAuth   = true;  // Usar autenticação SMTP (obrigatório para smtp.seudomínio.com.br)
       $mail->Port       = 587; //  Usar 587 porta SMTP
       $mail->Username = 'comercial@smecel.com.br'; // Usuário do servidor SMTP (endereço de email)
       $mail->Password = 'Drw4tson@smecel'; // Senha do servidor SMTP (senha do email usado)
       //$mail->SMTPSecure = 'tls';
       $mail->IsHTML(true);
       $mail->CharSet = "UTF-8";
       //$mail->CharSet = "iso-8859-1";
       $mail->SetFrom('comercial@smecel.com.br', 'SMECEL'); //Seu e-mail
       $mail->AddReplyTo('comercial@smecel.com.br', 'SMECEL'); //Seu e-mail
       $mail->Subject = "Sobre implantação do SMECEL - Sistema de Gestão Escolar";//Assunto do e-mail

       //$mail->SMTPDebug = 3; 
   
       $mail->AddAddress($email);
      // $mail->AddAddress('rcamaral@outlook.com');
   
       $mail->AddBCC('rcamaral@outlook.com'); // Cópia Oculta
       //$mail->AddBCC('fua.rafael@gmail.com'); // Cópia Oculta
       
       //Define o corpo do email
       $mail->MsgHTML("

       <p>Prezados(as),</p>

       <p>Sou o Rafael Amaral, servidor efetivo há quase 20 anos na Secretaria de Educação de Itagimirim-BA, onde dediquei grande parte desse tempo ao desenvolvimento do SMECEL – Sistema de Gestão Escolar.</p>

       <p>Estou entrando em contato para saber se vocês já utilizam algum sistema de gestão escolar aí em seu município? Se ainda não possuem, gostaria de apresentar meu sistema para vocês.</p> 

       <p>Que tal uma demonstração personalizada? Entre em contato comigo pelo WhatsApp <a href='https://wa.me/5573981489157'>https://wa.me/5573981489157</a> ou para mais informações sobre o sistema, acesse <a href='https://www.smecel.com.br/sobre'>https://www.smecel.com.br/sobre</a></p>
       
       <p>Abaixo o exemplo de algumas das principais telas do sistema:</p>
       
       <p>Painel Escolar<br><img src=\"https://www.smecel.com.br/sobre/images/sec-escola.png\" width=\"100%\"></p>

       <p>Painel do Professor<br><img src=\"https://www.smecel.com.br/sobre/images/sec-professor.png\" width=\"100%\"></p>
       
       <p>Painel do Aluno<br><img src=\"https://www.smecel.com.br/sobre/images/sec-aluno.png\" width=\"100%\"></p>

       <p>Painel do PSE (Programa Saúde na Escola)<br><img src=\"https://www.smecel.com.br/sobre/images/sec-pse.png\" width=\"100%\"></p>

       <p>Painel do Conselho Tutelar<br><img src=\"https://www.smecel.com.br/sobre/images/sec-conselho.png\" width=\"100%\"></p>

       <p>Caso vocês ainda não possuem um sistema de gestão escolar e têm interesse em saber um pouco mais sobre o SMECEL, retorne este e-mail informando o número de telefone e em breve entrarei em contato com vocês.</p>

       <p>Atenciosamente,</p>
       
       <hr>
       
       <small>
       <p><strong>Rafael Amaral</strong><br>Sócio-administrador<br>Departamento Comercial <br>(73) 3289-2704<br>(73) 98148-9157<br>(73) 98118-6670<br>SMECEL - Sistema de Gestão Escolar</p>
       </small> 
       <img src=\"https://www.smecel.com.br/img/logo_smecel_background_flattened.png\" width=\"150\">
       
       <hr>

       $time
       
       "); 
   
       ////Caso queira colocar o conteudo de um arquivo utilize o método abaixo ao invés da mensagem no corpo do e-mail.
       //$mail->MsgHTML(file_get_contents('arquivo.html'));
   
       $mail->Send();
       //echo "<div class=\"card-panel green darken-3\"><strong>E-mail de recuperação enviado com sucesso.</strong></div>";
       
       $mail->ClearAllRecipients();
       $mail->ClearAttachments();


       mysql_select_db($database_SmecelNovo, $SmecelNovo);
       $edit = "UPDATE divulgacao2 SET divulgacao_enviado = 'S' WHERE divulgacao_email = '$email'";
       $stmt_edit = mysql_query($edit, $SmecelNovo) or die(mysql_error());
      
      //caso apresente algum erro é apresentado abaixo com essa exceção.
      } catch (phpmailerException $e) {
        echo $e->errorMessage(); //Mensagem de erro costumizada do PHPMailer
  } 




} while ($row_verifica = mysql_fetch_assoc($verifica));

/*
$mail = new PHPMailer();
$mail->IsSMTP();
$mail->setLanguage('br');                             
$mail->CharSet='UTF-8';                               
$mail->Host = 'email-ssl.com.br';                     
$mail->SMTPAuth = true;                               
$mail->Username = 'comunicacao@masteroficios.com.br'; 
$mail->Password = 'Drw4tson@';                        
$mail->SMTPSecure = 'tls';                            
$mail->Port = 587;                                    
$mail->SetFrom('comunicacao@masteroficios.com.br', 'Masteroficios');
$mail->addReplyTo('comunicacao@masteroficios.com.br', 'Masteroficios');
//$mail->AddAttachment('anexos/OFICIO-2023-123.pdf');
$mail->isHTML(true);	
	
	//echo $linha["nome_fantasia"]."<br>";

//$id = $linha["id"];
//$email_empresa = $linha["email"];
//$hash = md5($linha["id"]);
$data1 = date("d/m/Y H:i:s");
//$nome_empresa = $linha["nome_fantasia"];

$mail->Subject = "05 emails enviados pelo Masterofícios em ".$data1;
//$mail->AddAddress('$email_empresa');
$mail->addBCC('rcamaral@outlook.com');
$mail->addBCC('ppaulo.developer@gmail.com');
$mail->msgHTML('

<p>05 emails enviados em '.$data1.'</p>
<p>'.json_encode($emails).'</p>

');

$mail->send();

$mail->ClearAllRecipients();
$mail->ClearAddresses();
$mail->ClearAttachments();

https://masteroficios.com.br/descadastrar.php?c='.$hash.'

*/

?>