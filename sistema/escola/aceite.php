<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>

<?php include "fnc/session.php"; ?>
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



//include "usuLogado.php";

$colname_UsuLogado = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_UsuLogado = $_SESSION['MM_Username'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_UsuLogado = sprintf("SELECT * FROM smc_usu WHERE usu_email = %s", GetSQLValueString($colname_UsuLogado, "text"));
$UsuLogado = mysql_query($query_UsuLogado, $SmecelNovo) or die(mysql_error());
$row_UsuLogado = mysql_fetch_assoc($UsuLogado);
$totalRows_UsuLogado = mysql_num_rows($UsuLogado);

$usuario = $row_UsuLogado['usu_id'];


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE smc_usu SET usu_aceite_lgpd=%s, usu_aceite_lgpd_data=%s WHERE usu_id=$usuario",
                       GetSQLValueString(isset($_POST['usu_aceite_lgpd']) ? "true" : "", "defined","'S'","'N'"),
                       GetSQLValueString($_POST['usu_aceite_lgpd_data'], "date"),
                       GetSQLValueString($_POST['usu_id'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());
  
  
	  header("Location: index.php?termoAceito"); 
 	  exit;
  
  
}

if ($row_UsuLogado['usu_aceite_lgpd']=="S") {
	
	header("Location: index.php?termoAceito"); 
 	exit;
	
	}
	

include "fnc/anoLetivo.php";

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EscolaLogada = "
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, 
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, sec_id, sec_cidade, sec_uf, escola_tema 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);
?>
<!DOCTYPE html>
<html class="<?php echo $row_EscolaLogada['escola_tema']; ?>" lang="pt-br">
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
<link rel="stylesheet" type="text/css" href="css/locastyle.css"><link rel="stylesheet" type="text/css" href="css/preloader.css">
<script src="js/locastyle.js"></script>
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
  <body>
    <?php include_once ("menu-top.php"); ?>


    <main class="ls-main">
      <div class="container-fluid">
 
		<!-- CONTEÚDO -->
        
        

		

		
		
		
		<!-- CONTEÚDO -->
      </div>
    </main>
    
    
    <div class="ls-modal" data-modal-blocked id="aceite">
  <div class="ls-modal-large">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title">Declaração de Conscientização sobre Utilização de Dados Pessoais para Fins Educacionais</h4>
    </div>
    <div class="ls-modal-body">
      
<div class="ls-alert-danger"><strong>IMPORTANTE!</strong> Leitura necessária.</div>      
      
<p>Eu, <strong><?php echo $row_UsuLogado['usu_nome']; ?> (<?php echo $row_UsuLogado['usu_email']; ?>)</strong>, usuário do sistema de gestão escolar SMECEL [www.smecel.com.br], reconheço a importância da proteção de dados pessoais e manifesto meu compromisso em utilizar essas informações exclusivamente para fins educacionais, em conformidade com os termos estabelecidos pela Lei Geral de Proteção de Dados (LGPD).</p>

<p>O sistema de gestão escolar contém informações e dados pessoais dos alunos e funcionários da rede pública de ensino, tais como nome completo, número de matrícula, dados de contato, informações acadêmicas, registros de frequência, notas, resultados de avaliações e outros dados pertinentes ao contexto educacional.</p>

<p>Declaro estar ciente de que é estritamente proibido utilizar essas informações para fins que não sejam educacionais ou administrativos relacionados ao exercício de minhas funções. Comprometo-me a respeitar a privacidade e a confidencialidade dos dados dos alunos e funcionários, evitando qualquer forma de acesso, uso ou compartilhamento inadequado ou não autorizado.</p>

<p>Ao utilizar o sistema de gestão escolar, concordo em aderir às políticas e diretrizes estabelecidas pela instituição de ensino quanto ao tratamento dos dados pessoais. Compreendo que a violação dessas políticas pode resultar em medidas disciplinares e até mesmo em ações legais, conforme permitido pela legislação vigente.</p>

<p>Além disso, reconheço que os dados pessoais dos alunos e funcionários são confidenciais e não devem ser divulgados a terceiros sem a devida autorização. Comprometo-me a manter as informações protegidas e a tomar todas as precauções necessárias para evitar vazamentos, perdas ou acessos não autorizados.</p>

<p>Por meio desta declaração, reitero meu compromisso em utilizar os dados pessoais disponíveis no sistema de gestão escolar exclusivamente para fins educacionais e administrativos, em estrito cumprimento às disposições da LGPD. Estou ciente de que meu acesso ao sistema está condicionado à minha conformidade com essas diretrizes e políticas.</p>
      
      
     
    </div>
    <div class="ls-modal-footer">
      <button class="ls-btn ls-float-right" data-dismiss="modal">Close</button>
      <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
      <label class="ls-label-text ls-float-right ls-txt-right">
      <input class="" type="checkbox" name="usu_aceite_lgpd" value=""  <?php if (!(strcmp(htmlentities($row_UsuLogado['usu_aceite_lgpd'], ENT_COMPAT, 'utf-8'),""))) {echo "checked=\"checked\"";} ?> required>
      Declaro que li e estou de acordo com a declaração descrita acima.
      </label>
      <input type="submit" class="ls-btn-primary" value="ACEITAR E FECHAR" class="ls-float-left">
          <input type="hidden" name="usu_aceite_lgpd_data" value="<?php echo date("Y-m-d H:i:s"); ?>">
          <input type="hidden" name="MM_update" value="form1">
          <input type="hidden" name="usu_id" value="<?php echo $row_UsuLogado['usu_id']; ?>">
</form>
      
      
      
    </div>
  </div>
</div>
		



    <!-- We recommended use jQuery 1.10 or up -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
    <script src="js/locastyle.js"></script>
    <script>
    
    locastyle.modal.open("#aceite");

	
    </script>
 
  </body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);
?>
