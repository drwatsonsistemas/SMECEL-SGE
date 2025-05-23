<?php require_once ('../../Connections/SmecelNovo.php'); ?>
<?php // include "fnc/anoLetivo.php"; ?>

<?php include "fnc/session.php"; ?>
<?php
if (!function_exists('GetSQLValueString')) {
    function GetSQLValueString($theValue, $theType, $theDefinedValue = '', $theNotDefinedValue = '')
    {
        if (PHP_VERSION < 6) {
            $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
        }

        $theValue = function_exists('mysql_real_escape_string') ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

        switch ($theType) {
            case 'text':
                $theValue = ($theValue != '') ? "'" . $theValue . "'" : 'NULL';
                break;
            case 'long':
            case 'int':
                $theValue = ($theValue != '') ? intval($theValue) : 'NULL';
                break;
            case 'double':
                $theValue = ($theValue != '') ? doubleval($theValue) : 'NULL';
                break;
            case 'date':
                $theValue = ($theValue != '') ? "'" . $theValue . "'" : 'NULL';
                break;
            case 'defined':
                $theValue = ($theValue != '') ? $theDefinedValue : $theNotDefinedValue;
                break;
        }
        return $theValue;
    }
}

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
    if ($maiusculas)
        $caracteres .= $lmai;
    if ($numeros)
        $caracteres .= $num;
    if ($simbolos)
        $caracteres .= $simb;
    $len = strlen($caracteres);
    for ($n = 1; $n <= $tamanho; $n++) {
        $rand = mt_rand(1, $len);
        $retorno .= $caracteres[$rand - 1];
    }
    return $retorno;
}

include 'usuLogado.php';
include 'fnc/anoLetivo.php';

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EscolaLogada = "
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, 
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_tema,  sec_id, sec_cidade, sec_uf 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die (mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

$colname_Funcionario = '-1';
if (isset($_GET['c'])) {
    $colname_Funcionario = $_GET['c'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Funcionario = sprintf("SELECT vinculo_id, vinculo_id_funcionario, vinculo_id_escola, vinculo_acesso, func_id, func_id_sec, func_nome
FROM smc_vinculo 
INNER JOIN smc_func ON func_id = vinculo_id_funcionario
WHERE vinculo_id = %s AND vinculo_id_escola = '$row_EscolaLogada[escola_id]'", GetSQLValueString($colname_Funcionario, 'int'));
$Funcionario = mysql_query($query_Funcionario, $SmecelNovo) or die (mysql_error());
$row_Funcionario = mysql_fetch_assoc($Funcionario);
$totalRows_Funcionario = mysql_num_rows($Funcionario);

if($totalRows_Funcionario == 0){
    header(sprintf('Location: funcListar.php?permissao'));
    exit ();
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
    $editFormAction .= '?' . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST['MM_update'])) && ($_POST['MM_update'] == 'form1')) {
    if ($row_UsuLogado['usu_update'] == 'N') {
        header(sprintf('Location: funcListar.php?permissao'));
        exit ();
    }

    $acesso;
    if ($_POST['func_acesso'] == 'on') {
        $acesso = 'S';
    } else {
        $acesso = 'N';
    }
    $codigo = $_POST['func_id'];

    $updateSQL = sprintf('UPDATE smc_vinculo SET vinculo_acesso=%s WHERE vinculo_id=%s AND vinculo_id_escola = %s',
        GetSQLValueString($acesso, 'text'),
        GetSQLValueString($_POST['func_id'], 'int'),
        GetSQLValueString($row_EscolaLogada['escola_id'], 'int'));

    mysql_select_db($database_SmecelNovo, $SmecelNovo);
    $Result1 = mysql_query($updateSQL, $SmecelNovo) or die (mysql_error());
    
    echo "
\t<script> 
\t//document.location = 'sistema/index.php'; 
\twindow.setTimeout(\"document.location='index.php'\",3000)
\t</script>
\t";

    $updateGoTo = 'funcListar.php?acesso';
    if (isset($_SERVER['QUERY_STRING'])) {
        $updateGoTo .= (strpos($updateGoTo, '?')) ? '&' : '?';
        $updateGoTo .= $_SERVER['QUERY_STRING'];
    }
    header(sprintf('Location: %s', $updateGoTo));
}
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






    <main class="ls-main ">
      <div class="container-fluid">
 
       
		<!-- CONTEÚDO -->
		
		
<div class="ls-modal" data-modal-blocked id="myAwesomeModal" style="top:-100px;">
  <div class="ls-modal-box ls-modal-small">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title">PAINEL DO PROFESSOR - GERENCIAR ACESSO</h4><br>
	 
	<hr>
	
	<h3 class="ls-txt-center"><?php echo $row_Funcionario['func_nome']; ?></h3>
	
	
	<?php if ($row_Funcionario['vinculo_acesso'] == 'S') { ?>
	    <hr>
	    <div class="ls-alert-success"><strong>Atenção: </strong>O servidor(a) possui acesso ao Painel do Professor.</div>
	<?php } else { ?>
        <hr>
        <div class="ls-alert-danger"><strong>Atenção: </strong>O servidor(a) NÃO possui acesso ao Painel do Professor.</div>
    <?php } ?>
	</div>
    <div class="ls-modal-body" id="myModalBody">
		
	  

		
        <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form ls-form-inline">
		<fieldset>
		
		
	  <label class="ls-label col-md-12">
      <b class="ls-label-text">ACESSO:</b>
	  <div data-ls-module="switchButton" class="ls-switch-btn ls-float-left">
    <input type="checkbox" name="func_acesso" id="status_acesso" <?php echo $row_Funcionario['vinculo_acesso'] == 'S' ? 'checked' : '' ?>>
    <label class="ls-switch-label" for="status_acesso" name="label-teste" ls-switch-off="Desativado" ls-switch-on="Ativado"><span></span></label>
  </div>
	  </label>
	  
	  
	  <div class="ls-actions-btn ls-group-btn ls-group-active">
      <input type="submit" value="ATUALIZAR" class="ls-btn-primary ls-btn-lg" >
      
	  <a href="funcListar.php" class="ls-btn-primary-danger ls-btn-lg">CANCELAR</a>
	  </div>
          <input type="hidden" name="MM_update" value="form1">
          <input type="hidden" name="func_id" value="<?php echo $_GET['c']; ?>">
		  
        </fieldset>
		</form>
		
		
		
			</div>
  </div>
</div><!-- /.modal -->
		
		
		
        <!-- CONTEÚDO -->
      </div>
    </main>


    <!-- We recommended use jQuery 1.10 or up -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="js/locastyle.js"></script>
 
	<script>
  locastyle.modal.open("#myAwesomeModal");
</script>	


  </body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($Funcionario);
?>
