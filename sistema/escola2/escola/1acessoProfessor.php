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
    if($_POST['func_acesso'] == "on"){
        $acesso = "S";
    }else{
        $acesso = "N";
    }
    $email = $_POST['func_email'];
    $nome = $_POST['func_nome'];
    $codigo = $_POST['func_id'];

    $updateSQL = sprintf('UPDATE smc_func SET func_acesso=%s WHERE func_id=%s',
        GetSQLValueString($acesso, 'text'),
        GetSQLValueString($_POST['func_id'], 'int'));

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

$colname_Funcionario = '-1';
if (isset($_GET['c'])) {
    $colname_Funcionario = $_GET['c'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Funcionario = sprintf('SELECT func_id, func_id_sec, func_nome, func_mae, func_pai, func_data_nascimento, 
func_uf_nascimento, func_municipio_nascimento, func_estado_civil, func_sexo, func_escolaridade, func_cpf, 
func_rg_numero, func_rg_emissor, func_titulo, func_titulo_secao, func_titulo_zona, func_pis, func_cnh_num, 
func_categoria, func_ctps, func_ctps_serie, func_reservista, func_endereco, func_endereco_numero, 
func_endereco_bairro, func_endereco_cep, func_endereco_uf, func_endereco_cidade, func_matricula, func_admissao, 
func_decreto, func_lotacao, func_cargo, func_regime, func_grupo_sanquineo, func_fator_rh, func_email, func_telefone, 
func_celular1, func_celular2, func_agencia_banco, func_conta_banco, func_nome_banco, func_area_concurso, 
func_formacao, func_situacao, func_foto, func_senha, func_senha_ativa, func_acesso
FROM smc_func WHERE func_id = %s', GetSQLValueString($colname_Funcionario, 'int'));
$Funcionario = mysql_query($query_Funcionario, $SmecelNovo) or die (mysql_error());
$row_Funcionario = mysql_fetch_assoc($Funcionario);
$totalRows_Funcionario = mysql_num_rows($Funcionario);
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
	
	
	<?php if ($row_Funcionario['func_acesso'] == 'S') { ?>
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
    <input type="checkbox" name="func_acesso" id="status_acesso" <?php echo $row_Funcionario['func_acesso'] == "S" ? "checked" : "" ?>>
    <label class="ls-switch-label" for="status_acesso" name="label-teste" ls-switch-off="Desativado" ls-switch-on="Ativado"><span></span></label>
  </div>
	  </label>
	  
	  
	  <div class="ls-actions-btn ls-group-btn ls-group-active">
      <input type="submit" value="ATUALIZAR" class="ls-btn-primary ls-btn-lg" >
      
	  <a href="funcListar.php" class="ls-btn-primary-danger ls-btn-lg">CANCELAR</a>
	  </div>
          <input type="hidden" name="MM_update" value="form1">
          <input type="hidden" name="func_id" value="<?php echo $row_Funcionario['func_id']; ?>">
          <input type="hidden" name="func_nome" value="<?php echo $row_Funcionario['func_nome']; ?>">
		  
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
