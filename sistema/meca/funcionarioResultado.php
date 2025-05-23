<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "funcoes/funcoes.php"; ?>
<?php include "funcoes/exibe_vazio.php"; ?>
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
$MM_authorizedUsers = "1";
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

$currentPage = $_SERVER["PHP_SELF"];

$colname_UsuarioLogado = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_UsuarioLogado = $_SESSION['MM_Username'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_UsuarioLogado = sprintf("SELECT usu_id, usu_nome, usu_email, usu_senha, usu_tipo, usu_sec, usu_escola, usu_status, usu_cadastro FROM smc_usu WHERE usu_email = %s", GetSQLValueString($colname_UsuarioLogado, "text"));
$UsuarioLogado = mysql_query($query_UsuarioLogado, $SmecelNovo) or die(mysql_error());
$row_UsuarioLogado = mysql_fetch_assoc($UsuarioLogado);
$totalRows_UsuarioLogado = mysql_num_rows($UsuarioLogado);

$maxRows_ExibeFuncionarios = 20;
$pageNum_ExibeFuncionarios = 0;
if (isset($_GET['pageNum_ExibeFuncionarios'])) {
  $pageNum_ExibeFuncionarios = $_GET['pageNum_ExibeFuncionarios'];
}
$startRow_ExibeFuncionarios = $pageNum_ExibeFuncionarios * $maxRows_ExibeFuncionarios;


$colname_EditaFunc = "-1";
if (isset($_GET['p'])) {
  $colname_EditaFunc = $_GET['p'];
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ExibeFuncionarios = "SELECT func_id, func_id_sec, func_nome, func_mae, func_pai, func_data_nascimento, func_uf_nascimento, func_municipio_nascimento, func_estado_civil, func_sexo, func_escolaridade, func_cpf, func_rg_numero, func_rg_emissor, func_titulo, func_titulo_secao, func_titulo_zona, func_pis, func_cnh_num, func_categoria, func_ctps, func_ctps_serie, func_reservista, func_endereco, func_endereco_numero, func_endereco_bairro, func_endereco_cep, func_endereco_uf, func_endereco_cidade, func_matricula, func_admissao, func_decreto, func_lotacao, func_cargo, func_regime, func_grupo_sanquineo, func_fator_rh, func_email, func_telefone, func_celular1, func_celular2, func_agencia_banco, func_conta_banco, func_nome_banco, func_area_concurso, func_formacao, func_situacao, func_foto FROM smc_func WHERE func_nome = '$colname_EditaFunc' ORDER BY func_nome ASC";
$query_limit_ExibeFuncionarios = sprintf("%s LIMIT %d, %d", $query_ExibeFuncionarios, $startRow_ExibeFuncionarios, $maxRows_ExibeFuncionarios);
$ExibeFuncionarios = mysql_query($query_limit_ExibeFuncionarios, $SmecelNovo) or die(mysql_error());
$row_ExibeFuncionarios = mysql_fetch_assoc($ExibeFuncionarios);

if (isset($_GET['totalRows_ExibeFuncionarios'])) {
  $totalRows_ExibeFuncionarios = $_GET['totalRows_ExibeFuncionarios'];
} else {
  $all_ExibeFuncionarios = mysql_query($query_ExibeFuncionarios);
  $totalRows_ExibeFuncionarios = mysql_num_rows($all_ExibeFuncionarios);
}
$totalPages_ExibeFuncionarios = ceil($totalRows_ExibeFuncionarios/$maxRows_ExibeFuncionarios)-1;

$queryString_ExibeFuncionarios = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_ExibeFuncionarios") == false && 
        stristr($param, "totalRows_ExibeFuncionarios") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_ExibeFuncionarios = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_ExibeFuncionarios = sprintf("&totalRows_ExibeFuncionarios=%d%s", $totalRows_ExibeFuncionarios, $queryString_ExibeFuncionarios);
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Painel Administrativo</title>
<link rel="stylesheet" href="../../css/foundation.css">
<link rel="stylesheet" href="../../css/normalize.css">
  <!-- This is how you would link your custom stylesheet -->
  <link rel="stylesheet" href="../css/app-painel.css">
  <script src="../../js/vendor/modernizr.js"></script>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>

<body>

<?php include "menu.php"; ?>

<div class="row">

<hr>

  <div class="small-12 medium-3 columns">
  	  <a href="funcionarioCadastrar.php" class="button tiny">CADASTRAR FUNCIONÁRIO</a>
  </div>
  <div class="small-12 medium-9 columns">
    
<form autocomplete="off" action="funcionarioResultado.php" method="get">
  <div class="row">
    <div class="large-12 columns">
      <div class="row collapse">
        <div class="small-10 columns">
          <input type="text" name="p" placeholder="Pesquise um funcionário" id="inputString" onKeyUp="lookup(this.value);" onBlur="fill();">
        </div>
        <div class="small-2 columns">
        <input type="submit" value="Buscar" class="button postfix">
        </div>
              	<div class="suggestionsBox panel" id="suggestions" style="display: none;">
                  <b>RESULTADO</b> (Selecione um nome e clique em BUSCAR)
				  <div class="suggestionList" id="autoSuggestionsList">
				  </div>
			    </div>

      </div>
    </div>

  </div>
</form>

  </div>
</div>

<div class="row">

	<div class="small-12 columns">
      
      <p>Total de funcionários cadastrados (<?php echo $totalRows_ExibeFuncionarios ?>)</p>
      
      <?php if ($totalRows_ExibeFuncionarios > 0) { // Show if recordset not empty ?>
      
  <table width="100%" border="0" cellpadding="0" cellspacing="0"  role="grid">
    
    <thead>
      <tr>
        <td>NOME</td>
        <td align="center" class="text-center">MATRÍCULA</td>
        <td align="center" class="text-center">ADMISSÃO</td>
        <td align="center" class="text-center">FORMAÇÃO</td>
        </tr>
    </thead>
    <tbody>
      <?php do { ?>
        <tr>
          <td><a href="funcionarioVer.php?c=<?php echo $row_ExibeFuncionarios['func_id']; ?>"><small><?php echo $row_ExibeFuncionarios['func_nome']; ?></small></a></td>
          <td align="center" class="text-center"><small><?php echo exibeVazio($row_ExibeFuncionarios['func_matricula']); ?></small></td>
          <td align="center" class="text-center"><small><?php echo exibeVazio(inverteData($row_ExibeFuncionarios['func_admissao'])); ?></small></td>
          <td align="center" class="text-center"><small><a href="titulacaoCadastrar.php?c=<?php echo $row_ExibeFuncionarios['func_id']; ?>" class="text-center">INCLUIR</a></small></td>
        </tr>
        <?php } while ($row_ExibeFuncionarios = mysql_fetch_assoc($ExibeFuncionarios)); ?>
    </tbody>
  </table>
        
      <hr>
      <table width="100%" border="0">
        <tr>
          <td width="25%" align="center" valign="middle"><?php if ($pageNum_ExibeFuncionarios > 0) { // Show if not first page ?>
              <a href="<?php printf("%s?pageNum_ExibeFuncionarios=%d%s", $currentPage, 0, $queryString_ExibeFuncionarios); ?>" class="button small text-center expand">Primeira</a>
              <?php } // Show if not first page ?></td>
          <td width="25%" align="center" valign="middle"><?php if ($pageNum_ExibeFuncionarios > 0) { // Show if not first page ?>
              <a href="<?php printf("%s?pageNum_ExibeFuncionarios=%d%s", $currentPage, max(0, $pageNum_ExibeFuncionarios - 1), $queryString_ExibeFuncionarios); ?>" class="button small text-center expand">Anterior</a>
              <?php } // Show if not first page ?></td>
          <td width="25%" align="center" valign="middle"><?php if ($pageNum_ExibeFuncionarios < $totalPages_ExibeFuncionarios) { // Show if not last page ?>
              <a href="<?php printf("%s?pageNum_ExibeFuncionarios=%d%s", $currentPage, min($totalPages_ExibeFuncionarios, $pageNum_ExibeFuncionarios + 1), $queryString_ExibeFuncionarios); ?>" class="button small text-center expand">Próxima</a>
              <?php } // Show if not last page ?></td>
          <td width="25%" align="center" valign="middle">
            <?php if ($pageNum_ExibeFuncionarios < $totalPages_ExibeFuncionarios) { // Show if not last page ?>
                <a href="<?php printf("%s?pageNum_ExibeFuncionarios=%d%s", $currentPage, $totalPages_ExibeFuncionarios, $queryString_ExibeFuncionarios); ?>" class="button small text-center expand">Última</a>
                <?php } // Show if not last page ?>
          </td>
        </tr>
      </table>
        <?php } else { ?>
        
        <div data-alert class="alert-box info radius">
  		Nenhum resultado encontrado.
  		<a href="#" class="close">&times;</a>
		</div>
        
        <?php } // Show if recordset not empty ?>
        
        
        
        
        
    </div>
</div>



<script src="../../js/vendor/jquery.js"></script>
  <script src="../../js/foundation.min.js"></script>
  <script src="js/foundation/foundation.dropdown.js"></script>
<script>
    $(document).foundation();
  </script>
  
<!--
<script type="text/javascript" src="jquery-1.3.js"></script> 
-->
<script type="text/javascript">
	function lookup(inputString) {
		if(inputString.length == 0) {
			// Hide the suggestion box.
			$('#suggestions').hide();
		} else {
			$.post("buscaFuncionario.php", {queryString: ""+inputString+""}, function(data){
				if(data.length >0) {
					$('#suggestions').show();
					$('#autoSuggestionsList').html(data);
				}
			});
		}
	} // lookup
	
	function fill(thisValue) {
		$('#inputString').val(thisValue);
		setTimeout("$('#suggestions').hide();", 5000);
	}
</script>
  
  
</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($ExibeFuncionarios);
?>
