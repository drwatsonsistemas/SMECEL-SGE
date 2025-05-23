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
$MM_authorizedUsers = "99";
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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE smc_sec SET sec_nome=%s, sec_prefeitura=%s, sec_cep=%s, sec_uf=%s, sec_cidade=%s, sec_endereco=%s, sec_num=%s, sec_bairro=%s, sec_telefone1=%s, sec_telefone2=%s, sec_email=%s, sec_nome_secretario=%s, sec_ibge_municipio=%s, sec_nre=%s, sec_bloqueada=%s, sec_aviso_bloqueio=%s WHERE sec_id=%s",
                       GetSQLValueString($_POST['sec_nome'], "text"),
                       GetSQLValueString($_POST['sec_prefeitura'], "text"),
                       GetSQLValueString($_POST['sec_cep'], "text"),
                       GetSQLValueString($_POST['sec_uf'], "text"),
                       GetSQLValueString($_POST['sec_cidade'], "text"),
                       GetSQLValueString($_POST['sec_endereco'], "text"),
                       GetSQLValueString($_POST['sec_num'], "text"),
                       GetSQLValueString($_POST['sec_bairro'], "text"),
                       GetSQLValueString($_POST['sec_telefone1'], "text"),
                       GetSQLValueString($_POST['sec_telefone2'], "text"),
                       GetSQLValueString($_POST['sec_email'], "text"),
                       GetSQLValueString($_POST['sec_nome_secretario'], "text"),
                       GetSQLValueString($_POST['sec_ibge_municipio'], "text"),
                       GetSQLValueString($_POST['sec_nre'], "text"),
                       GetSQLValueString(isset($_POST['sec_bloqueada']) ? "true" : "", "defined","'S'","'N'"),
                       GetSQLValueString(isset($_POST['sec_aviso_bloqueio']) ? "true" : "", "defined","'S'","'N'"),
                       GetSQLValueString($_POST['sec_id'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

  $updateGoTo = "contratos.php?editado";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
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

$colname_Contrato = "-1";
if (isset($_GET['sec'])) {
  $colname_Contrato = $_GET['sec'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Contrato = sprintf("SELECT * FROM smc_sec WHERE sec_id = %s", GetSQLValueString($colname_Contrato, "int"));
$Contrato = mysql_query($query_Contrato, $SmecelNovo) or die(mysql_error());
$row_Contrato = mysql_fetch_assoc($Contrato);
$totalRows_Contrato = mysql_num_rows($Contrato);
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
    <h1 class="ls-title-intro ls-ico-home">EDITAR</h1>
    <div class="ls-box ls-board-box"> 
      <!-- CONTEUDO -->
      
      <form method="post" name="form1" id="form1" action="<?php echo $editFormAction; ?>" class="ls-form ls-form-horizontal">
        <fieldset>
          <label class="ls-label col-md-6">
          <b class="ls-label-text">Nome ou Sigla da secretaria</b>
          <p class="ls-label-info">Ex.: Secretaria de Educação, SEC, SME etc. </p>
          <input type="text" name="sec_nome" value="<?php echo htmlentities($row_Contrato['sec_nome'], ENT_COMPAT, 'utf-8'); ?>" size="32" required>
          </label>
          <label class="ls-label col-md-6">
          <b class="ls-label-text">Prefeitura</b>
          <p class="ls-label-info">Ex.: Prefeitura de Salvador</p>
          <input type="text" name="sec_prefeitura" value="<?php echo htmlentities($row_Contrato['sec_prefeitura'], ENT_COMPAT, 'utf-8'); ?>" size="32" required>
          </label>
          <label class="ls-label col-md-4">
          <b class="ls-label-text">CEP</b>
          <p class="ls-label-info">Informe o CEP</p>
          <input type="text" name="sec_cep" id="sec_cep" value="<?php echo htmlentities($row_Contrato['sec_cep'], ENT_COMPAT, 'utf-8'); ?>" size="32" class="cep">
          </label>
          
          <label class="ls-label col-md-4 col-sm-12">
          <b class="ls-label-text">UF</b>
          <p class="ls-label-info">Informe a sigla do Estado</p>
          <div class="ls-custom-select">
            <select name="sec_uf" class="ls-custom" required>
              <option value="" <?php if (!(strcmp(-1, ""))) {echo "SELECTED";} ?>>ESCOLHA...</option>
              <option value="AC" <?php if (!(strcmp("BA", htmlentities($row_Contrato['sec_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>AC</option>
              <option value="AL" <?php if (!(strcmp("AL", htmlentities($row_Contrato['sec_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>AL</option>
              <option value="AP" <?php if (!(strcmp("AP", htmlentities($row_Contrato['sec_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>AP</option>
              <option value="AM" <?php if (!(strcmp("AM", htmlentities($row_Contrato['sec_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>AM</option>
              <option value="BA" <?php if (!(strcmp("BA", htmlentities($row_Contrato['sec_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>BA</option>
              <option value="CE" <?php if (!(strcmp("CE", htmlentities($row_Contrato['sec_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>CE</option>
              <option value="DF" <?php if (!(strcmp("DF", htmlentities($row_Contrato['sec_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>DF</option>
              <option value="ES" <?php if (!(strcmp("ES", htmlentities($row_Contrato['sec_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>ES</option>
              <option value="GO" <?php if (!(strcmp("GO", htmlentities($row_Contrato['sec_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>GO</option>
              <option value="MA" <?php if (!(strcmp("MA", htmlentities($row_Contrato['sec_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>MA</option>
              <option value="MT" <?php if (!(strcmp("MT", htmlentities($row_Contrato['sec_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>MT</option>
              <option value="MS" <?php if (!(strcmp("MS", htmlentities($row_Contrato['sec_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>MS</option>
              <option value="MG" <?php if (!(strcmp("MG", htmlentities($row_Contrato['sec_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>MG</option>
              <option value="PA" <?php if (!(strcmp("PA", htmlentities($row_Contrato['sec_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>PA</option>
              <option value="PB" <?php if (!(strcmp("PB", htmlentities($row_Contrato['sec_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>PB</option>
              <option value="PR" <?php if (!(strcmp("PR", htmlentities($row_Contrato['sec_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>PR</option>
              <option value="PE" <?php if (!(strcmp("PE", htmlentities($row_Contrato['sec_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>PE</option>
              <option value="PI" <?php if (!(strcmp("PI", htmlentities($row_Contrato['sec_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>PI</option>
              <option value="RJ" <?php if (!(strcmp("RJ", htmlentities($row_Contrato['sec_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>RJ</option>
              <option value="RN" <?php if (!(strcmp("RN", htmlentities($row_Contrato['sec_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>RN</option>
              <option value="RS" <?php if (!(strcmp("RS", htmlentities($row_Contrato['sec_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>RS</option>
              <option value="RO" <?php if (!(strcmp("RO", htmlentities($row_Contrato['sec_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>RO</option>
              <option value="RR" <?php if (!(strcmp("RR", htmlentities($row_Contrato['sec_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>RR</option>
              <option value="SC" <?php if (!(strcmp("SC", htmlentities($row_Contrato['sec_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>SC</option>
              <option value="SP" <?php if (!(strcmp("SP", htmlentities($row_Contrato['sec_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>SP</option>
              <option value="TO" <?php if (!(strcmp("TO", htmlentities($row_Contrato['sec_uf'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>TO</option>
            </select>
          </div>
          </label>
         
          
          <label class="ls-label col-md-4">
          <b class="ls-label-text">Cidade</b>
          <p class="ls-label-info">Informe o nome da cidade</p>
          <input type="text" name="sec_cidade" id="sec_cidade" value="<?php echo htmlentities($row_Contrato['sec_cidade'], ENT_COMPAT, 'utf-8'); ?>" size="32" required>
          </label>
          <label class="ls-label col-md-6">
          <b class="ls-label-text">Endereço</b>
          <p class="ls-label-info">Informe o endereço completo da Secretaria de Educação</p>
          <input type="text" name="sec_endereco" id="sec_endereco" value="<?php echo htmlentities($row_Contrato['sec_endereco'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label class="ls-label col-md-2">
          <b class="ls-label-text">Número</b>
          <p class="ls-label-info">&nbsp;</p>
          <input type="text" name="sec_num" id="sec_num" value="<?php echo htmlentities($row_Contrato['sec_num'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label class="ls-label col-md-4">
          <b class="ls-label-text">Bairro</b>
          <p class="ls-label-info">Informe o Bairro</p>
          <input type="text" name="sec_bairro" id="sec_bairro" value="<?php echo htmlentities($row_Contrato['sec_bairro'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label class="ls-label col-md-3">
          <b class="ls-label-text">Telefone 1</b>
          <p class="ls-label-info">Contato principal</p>
          <input type="text" name="sec_telefone1" value="<?php echo htmlentities($row_Contrato['sec_telefone1'], ENT_COMPAT, 'utf-8'); ?>" size="32" class="phone_with_ddd" required>
          </label>
          <label class="ls-label col-md-3">
          <b class="ls-label-text">Telefone 2</b>
          <p class="ls-label-info">Contato secundário</p>
          <input type="text" name="sec_telefone2" value="<?php echo htmlentities($row_Contrato['sec_telefone2'], ENT_COMPAT, 'utf-8'); ?>" size="32" class="phone_with_ddd">
          </label>
          <label class="ls-label col-md-6">
          <b class="ls-label-text">E-mail</b>
          <p class="ls-label-info">Informe um e-mail de contato da secretaria</p>
          <input type="text" name="sec_email" value="<?php echo htmlentities($row_Contrato['sec_email'], ENT_COMPAT, 'utf-8'); ?>" size="32" required>
          </label>
          <label class="ls-label col-md-4">
          <b class="ls-label-text">Dirigente de Educação</b>
          <p class="ls-label-info">Informe o nome do(a) Secretário(a) de Educação</p>
          <input type="text" name="sec_nome_secretario" value="<?php echo htmlentities($row_Contrato['sec_nome_secretario'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label class="ls-label col-md-4">
          <b class="ls-label-text">CÓDIGO IBGE</b>
          <p class="ls-label-info">Informe o código IBGE do município</p>
          <input type="text" name="sec_ibge_municipio" id="sec_ibge_municipio" value="<?php echo htmlentities($row_Contrato['sec_ibge_municipio'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label class="ls-label col-md-4">
          <b class="ls-label-text">CÓDIGO NRE</b>
          <p class="ls-label-info">Código do Núcleo Regional  de Educação no formato 00000</p>
          <input type="text" name="sec_nre" id="sec_nre" value="<?php echo htmlentities($row_Contrato['sec_nre'], ENT_COMPAT, 'utf-8'); ?>" size="5" maxlength="5">
          </label>
          
          
          
         
          <label class="ls-label col-md-6">
          <b class="ls-label-text">AVISO DE BLOQUEIO</b>
          <br><p class="ls-label-info">Emite aviso no painel
          <div data-ls-module="switchButton" class="ls-switch-btn">
          <input type="checkbox" name="sec_aviso_bloqueio" id="sec_aviso_bloqueio" value=""  <?php if (!(strcmp(htmlentities($row_Contrato['sec_aviso_bloqueio'], ENT_COMPAT, 'utf-8'),"S"))) {echo "checked=\"checked\"";} ?>>
          <label class="ls-switch-label" for="sec_aviso_bloqueio" name="label-teste" ls-switch-off="Desativado" ls-switch-on="Ativado"><span></span></label>
          </div>
          </p></label>
          

          <label class="ls-label col-md-6">
          <b class="ls-label-text">BLOQUEIO DE ACESSO</b>
          <br><p class="ls-label-info">Bloqueia acesso ao painel
          <div data-ls-module="switchButton" class="ls-switch-btn">
          <input type="checkbox" name="sec_bloqueada" value=""  <?php if (!(strcmp(htmlentities($row_Contrato['sec_bloqueada'], ENT_COMPAT, 'utf-8'),"S"))) {echo "checked=\"checked\"";} ?>>
          <label class="ls-switch-label" for="sec_bloqueada" name="label-teste" ls-switch-off="Desativado" ls-switch-on="Ativado"><span></span></label>
          </div>
          </p></label>

          
          <div class="ls-actions-btn">
            <input class="ls-btn" type="submit" value="SALVAR">
            <a href="contratos.php" class="ls-btn-danger">VOLTAR</a> </div>
        </fieldset>
        <input type="hidden" name="MM_update" value="form1">
        <input type="hidden" name="sec_id" value="<?php echo $row_Contrato['sec_id']; ?>" />
      </form>
      <p>&nbsp;</p>
      
      <!-- CONTEUDO --> 
    </div>
  </div>
</main>
<?php include_once "notificacoes.php"; ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="https://assets.locaweb.com.br/locastyle/3.8.4/javascripts/locastyle.js" type="text/javascript"></script>
<script src="../../js/jquery.mask.js"></script> 
<script src="js/mascara.js"></script> 

<script type="text/javascript">
	$("#sec_cep").focusout(function(){
	//Aqui vai o código	
	
	$.ajax({
			//O campo URL diz o caminho de onde virá os dados
			//É importante concatenar o valor digitado no CEP
			url: 'https://viacep.com.br/ws/'+$(this).val()+'/json/unicode/',
			//Aqui você deve preencher o tipo de dados que será lido,
			//no caso, estamos lendo JSON.
			dataType: 'json',
			//SUCESS é referente a função que será executada caso
			//ele consiga ler a fonte de dados com sucesso.
			//O parâmetro dentro da função se refere ao nome da variável
			//que você vai dar para ler esse objeto.
			success: function(resposta){
				//Agora basta definir os valores que você deseja preencher
				//automaticamente nos campos acima.
				$("#sec_endereco").val(resposta.logradouro);
				$("#sec_complemento").val(resposta.complemento);
				$("#sec_bairro").val(resposta.bairro);
				$("#sec_cidade").val(resposta.localidade);
				$("#sec_uf").val(resposta.uf);
				$("#sec_ibge_municipio").val(resposta.ibge);
				$("#sec_num").val(resposta.numero);
				//Vamos incluir para que o Número seja focado automaticamente
				//melhorando a experiência do usuário
				$("#numero").focus();
			}
		});
		
	});
</script>


</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Contrato);
?>