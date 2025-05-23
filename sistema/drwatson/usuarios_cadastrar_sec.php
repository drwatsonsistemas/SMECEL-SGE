<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php require_once('funcoes/configuracoes.php'); ?>
<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}
// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "../../index.php?exit";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "1,99";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && false) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "../../index.php?acessorestrito";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}



$colname_UsuarioLogado = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_UsuarioLogado = $_SESSION['MM_Username'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_UsuarioLogado = sprintf("SELECT usu_id, usu_nome, usu_email, usu_senha, usu_tipo, usu_sec, usu_escola, usu_status, usu_cadastro FROM smc_usu WHERE usu_email = %s", GetSQLValueString($colname_UsuarioLogado, "text"));
$UsuarioLogado = mysql_query($query_UsuarioLogado, $SmecelNovo) or die(mysql_error());
$row_UsuarioLogado = mysql_fetch_assoc($UsuarioLogado);
$totalRows_UsuarioLogado = mysql_num_rows($UsuarioLogado);

$colname_Sec = "-1";
if (isset($_GET['sec'])) {
  $colname_Sec = $_GET['sec'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Sec = sprintf("SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_ibge_municipio, sec_regra_media FROM smc_sec WHERE sec_id = %s", GetSQLValueString($colname_Sec, "int"));
$Sec = mysql_query($query_Sec, $SmecelNovo) or die(mysql_error());
$row_Sec = mysql_fetch_assoc($Sec);
$totalRows_Sec = mysql_num_rows($Sec);


/**
* Função para gerar senhas aleatórias
*/
function geraSenha($tamanho = 8, $maiusculas = false, $numeros = true, $simbolos = false)
{
$lmin = 'abcdefghjkmnpqrstuvwxyz';
$lmai = 'ABCDEFGHJKMNPQRSTUVWXYZ';
$num = '123456789';
$simb = '!@#$%*-';
$retorno = '';
$caracteres = '';
$caracteres .= $lmin;
if ($maiusculas) $caracteres .= $lmai;
if ($numeros) $caracteres .= $num;
if ($simbolos) $caracteres .= $simb;
$len = strlen($caracteres);
for ($n = 1; $n <= $tamanho; $n++) {
$rand = mt_rand(1, $len);
$retorno .= $caracteres[$rand-1];
}
return $retorno;
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
	$verificaEmail = $_POST['usu_email'];	
	
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_VerificaEmail = "SELECT usu_id, usu_nome, usu_email, usu_senha, usu_tipo, usu_sec, usu_escola, usu_status, usu_cadastro FROM smc_usu WHERE usu_email = '$verificaEmail'";
$VerificaEmail = mysql_query($query_VerificaEmail, $SmecelNovo) or die(mysql_error());
$row_VerificaEmail = mysql_fetch_assoc($VerificaEmail);
$totalRows_VerificaEmail = mysql_num_rows($VerificaEmail);


	if ($totalRows_VerificaEmail > 0) {
		
	echo "
	<script> 
	alert('Um usuário já está cadastrado com este e-mail');
	//document.location = 'sistema/index.php'; 
	window.setTimeout(\"document.location='usuarios_cadastrar.php'\")
	</script>
	";
		
		exit;
		}
	
	
	if ($_POST['usu_senha']=="") {
		
		$senha = geraSenha(6);
		
		} else {
			
			$senha = $_POST['usu_senha'];
			
			}
	
	
  $insertSQL = sprintf("INSERT INTO smc_usu (usu_nome, usu_email, usu_senha, usu_tipo, usu_sec, usu_escola, usu_status, usu_cadastro) VALUES (%s, %s, '$senha', %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['usu_nome'], "text"),
                       GetSQLValueString($_POST['usu_email'], "text"),
                       //GetSQLValueString($_POST['usu_senha'], "text"),
                       GetSQLValueString($_POST['usu_tipo'], "int"),
                       GetSQLValueString($_POST['usu_sec'], "int"),
                       GetSQLValueString($_POST['usu_escola'], "int"),
                       GetSQLValueString($_POST['usu_status'], "int"),
                       GetSQLValueString($_POST['usu_cadastro'], "date"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());
  
  

  //--------------------------------------- ENVIO DE SENHA POR EMAIL ---- INÍCIO
  
//$senha = $_POST['usu_senha'];
$email = $_POST['usu_email'];
$nome = $_POST['usu_nome'];
$prefeitura = $row_Sec['sec_prefeitura'];
$secEduc = $row_Sec['sec_nome'];
//$codigo = $_POST['func_id'];
  
  
  
  // Inclui o arquivo class.phpmailer.php localizado na pasta class
require_once("../../classes/class.phpmailer.php");
 
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
     $mail->Subject = "Informações de acesso ao painel do Sistema de Gestão Escolar na $prefeitura";//Assunto do e-mail
 
 
     //Define os destinatário(s)
     //=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
     $mail->AddAddress($email);
 
     //Campos abaixo são opcionais 
     //=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
     //$mail->AddCC('destinarario@dominio.com.br', 'Destinatario'); // Copia
     $mail->AddBCC('rafael_fua@hotmail.com'); // Cópia Oculta
     //$mail->AddAttachment('images/phpmailer.gif');      // Adicionar um anexo
 
 
     //Define o corpo do email
     $mail->MsgHTML("
	 
	 <p>Prezado(a) $nome,</p>
	 <p>Você foi cadastrado no Sistema de Gestão Escolar [SMECEL] da $prefeitura.</p>
	 <p>
	 E-mail de acesso: <strong>$email</strong><br>
	 Senha de acesso: <strong>$senha</strong>
	 </p>
	 <p>Acesse o endereço www.smecel.com.br, clique em LOGIN e informe os dados acima.</p>
	 <p>Atenciosamente,<br><br>
	 
	 Equipe de suporte da $secEduc.
	 <br>www.smecel.com.br</p>
	 "); 
 
     ////Caso queira colocar o conteudo de um arquivo utilize o método abaixo ao invés da mensagem no corpo do e-mail.
     //$mail->MsgHTML(file_get_contents('arquivo.html'));
 
     $mail->Send();
     //echo "<div class=\"card-panel green darken-3\"><strong>E-mail de recuperação enviado com sucesso.</strong></div>";
 		
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
  
  
  //--------------------------------------- ENVIO DE SENHA POR EMAIL ---- FIM
  
  

  $insertGoTo = "contratos_ver.php?cadastrado";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}


?>

<!DOCTYPE html>
<html class="<?php echo COR_TEMA ?>">
<head>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-117872281-1');
</script>

<title>SMECEL - Sistema de Gestão Escolar</title>
<meta charset="utf-8">
<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<meta name="description" content="">
<meta name="keywords" content="">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<link rel="stylesheet" type="text/css" href="//assets.locaweb.com.br/locastyle/edge/stylesheets/locastyle.css">
<script src="//assets.locaweb.com.br/locastyle/edge/javascripts/locastyle.js"></script>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<body>
<?php include_once("menu_top.php"); ?>
<?php include_once "menu.php"; ?>
<main class="ls-main">
  <div class="container-fluid">
    <h1 class="ls-title-intro ls-ico-home">CADASTRAR USUÁRIO PRINCIPAL</h1>
    
    <div class="ls-alert-info"><strong>Atenção:</strong> Você está cadastrando o usuário principal para acessar a <?php echo $row_Sec['sec_nome']; ?>. Após o cadastro, os dados de acesso serão enviados ao e-mail informado abaixo. Solicite ao usuário que faça uma verificação na caixa de spam.</div>
    
    
    <div class="ls-box ls-board-box">
    
    
      <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form-horizontal">
        
        <label class="ls-label col-md-6">
        <b class="ls-label-text">PREFEITURA</b>
        <p class="ls-label-info">Nome da prefeitura</p>
        <input type="text" name="" value="<?php echo $row_Sec['sec_prefeitura']; ?>" size="32" disabled>
        </label>

        <label class="ls-label col-md-6">
        <b class="ls-label-text">SECRETARIA</b>
        <p class="ls-label-info">Nome/sigla da Secretaria de Educação</p>
        <input type="text" name="" value="<?php echo $row_Sec['sec_nome']; ?>" size="32" disabled>
        </label>

        <label class="ls-label col-md-12">
        <b class="ls-label-text">NOME DO USUÁRIO</b>
        <p class="ls-label-info">Informe o nome completo</p>
        <input type="text" name="usu_nome" value="" size="32" required>
        </label>
        
        <label class="ls-label col-md-6">
        <b class="ls-label-text">E-MAIL</b>
        <p class="ls-label-info">Informe o e-mail que será utilizado como login</p>
        <input type="text" name="usu_email" value="" size="32" required>
        </label>
        <label class="ls-label col-md-6">
        <b class="ls-label-text">SENHA</b>
        <p class="ls-label-info">Obs.: Deixe em branco para gerar uma senha automática</p>
        <div class="ls-prefix-group">
          <input type="password" id="password_field" name="usu_senha" value="" size="32">
          <a class="ls-label-text-prefix ls-toggle-pass ls-ico-eye" data-toggle-class="ls-ico-eye, ls-ico-eye-blocked" data-target="#password_field" href="#"> </a> </div>
        </label>
    
        <label class="ls-label col-md-12">
          <input type="submit" value="CADASTRAR" class="ls-btn-primary">
          <a href="usuarios.php" class="ls-btn">CANCELAR</a> </label>
        <input type="hidden" name="usu_sec" value="<?php echo $row_Sec['sec_id']; ?>">
        <input type="hidden" name="usu_status" value="1">
        <input type="hidden" name="usu_tipo" value="1">
        <input type="hidden" name="usu_escola" value="0">
        <input type="hidden" name="usu_cadastro" value="<?php echo date('Y-m-d'); ?>">
        <input type="hidden" name="MM_insert" value="form1">
      </form>
      <p>&nbsp;</p>
    </div>
  </div>
</main>
<?php include_once "notificacoes.php"; ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="https://assets.locaweb.com.br/locastyle/3.8.4/javascripts/locastyle.js" type="text/javascript"></script> 
<script src="../js/jquery.mask.js"></script> 
<script src="js/mascara.js"></script> 
<script src="js/maiuscula.js"></script> 
<script src="js/semAcentos.js"></script>





</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Sec);
?>