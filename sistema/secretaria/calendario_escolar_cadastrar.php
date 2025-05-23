<?php require_once('../../Connections/SmecelNovo.php'); ?>
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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO smc_calendario_escolar (ce_id_sec, ce_ano, ce_data, ce_tipo, ce_descricao) VALUES (%s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['ce_id_sec'], "int"),
                       GetSQLValueString($_POST['ce_ano'], "text"),
                       GetSQLValueString($_POST['ce_data'], "date"),
                       GetSQLValueString($_POST['ce_tipo'], "text"),
                       GetSQLValueString($_POST['ce_descricao'], "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

  $insertGoTo = "calendario_escolar.php?cadastrado";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

require_once('funcoes/usuLogado.php');
require_once('funcoes/anoLetivo.php');

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

$data = "";
if (isset($_GET['data'])) {

$data = $_GET['data'];
  
$array = explode('-', $data);

//garante que o array possue tres elementos (dia, mes e ano)
if(count($array) == 3){
    $dia = (int)$array[2];
    $mes = (int)$array[1];
    $ano = (int)$array[0];

    //testa se a data é válida
    if(checkdate($mes, $dia, $ano)){
        $dataValida = $data;
    }else{
    $dataInvalida = "calendario_escolar.php?erro";
	header(sprintf("Location: %s", $dataInvalida));
    }
}else{
    //$dataInvalida = "calendario_escolar.php?erro";
	//header(sprintf("Location: %s", $dataInvalida));
}

}






?>

<!DOCTYPE html>
<html class="ls-theme-green">
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
<link rel="stylesheet" type="text/css" href="css/locastyle.css">
<script src="js/locastyle.js"></script><link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<body>

      
      
      <div class="ls-modal" data-modal-blocked id="myAwesomeModal">
  <div class="ls-modal-box">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title">CALENDÁRIO ESCOLAR - CADASTRAR</h4>
    </div>
    <div class="ls-modal-body" id="myModalBody">
      <p>
      

        <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form">
        <label class="ls-label col-md-12">
        <b class="ls-label-text">EVENTO DO DIA <?php echo date("d/m/Y", strtotime($data)); ?></b>
        <p class="ls-label-info">Informe o tipo de evento</p>
        <div class="ls-custom-select">
          <select name="ce_tipo" required>
            <option value=""> </option>
            <option value="19" <?php if (!(strcmp(19, ""))) {echo "SELECTED";} ?>>0 - RECESSO</option>
            <option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>1 - DIA LETIVO</option>
            <option value="2" <?php if (!(strcmp(2, ""))) {echo "SELECTED";} ?>>2 - SABADO LETIVO</option>
            <option value="3" <?php if (!(strcmp(3, ""))) {echo "SELECTED";} ?>>3 - FERIADO NACIONAL</option>
            <option value="4" <?php if (!(strcmp(4, ""))) {echo "SELECTED";} ?>>4 - FERIADO MUNICIPAL</option>
            <option value="5" <?php if (!(strcmp(5, ""))) {echo "SELECTED";} ?>>5 - RECESSO JUNINO</option>
            <option value="6" <?php if (!(strcmp(6, ""))) {echo "SELECTED";} ?>>6 - RECESSO DE NATAL</option>
			
            <option value="7" <?php if (!(strcmp(7, ""))) {echo "SELECTED";} ?>>7 - JORNADA PEDAGÓGICA</option>
            <option value="8" <?php if (!(strcmp(8, ""))) {echo "SELECTED";} ?>>8 - ENCONTRO P/ PLANEJAMENTO</option>
            <option value="9" <?php if (!(strcmp(9, ""))) {echo "SELECTED";} ?>>9 - CONSELHO DE CLASSE</option>
            <option value="10" <?php if (!(strcmp(10, ""))) {echo "SELECTED";} ?>>10 - ESTUDO DE RECUPERAÇÃO</option>
            <option value="11" <?php if (!(strcmp(11, ""))) {echo "SELECTED";} ?>>11 - ANO LETIVO 2020</option>
            <option value="12" <?php if (!(strcmp(12, ""))) {echo "SELECTED";} ?>>12 - ANO LETIVO 2021</option>
			
            <option value="13" <?php if (!(strcmp(13, ""))) {echo "SELECTED";} ?>>13 - PLANTÃO PEDAGÓGICO</option>
            <option value="14" <?php if (!(strcmp(14, ""))) {echo "SELECTED";} ?>>14 - CONSELHO DE CLASSE</option>
            <option value="15" <?php if (!(strcmp(15, ""))) {echo "SELECTED";} ?>>15 - RESULTADOS FINAIS</option>
			

            <option value="16" <?php if (!(strcmp(16, ""))) {echo "SELECTED";} ?>>16 - FÉRIAS COLETIVAS</option>
            <option value="17" <?php if (!(strcmp(17, ""))) {echo "SELECTED";} ?>>17 - ATIVIDADE SUSPENSA</option>
            <option value="18" <?php if (!(strcmp(18, ""))) {echo "SELECTED";} ?>>18 - AC COLETIVO</option>


          </select>
        </div>
        </label>
        <label class="ls-label col-md-12">
        <b class="ls-label-text">DESCRIÇÃO</b>
        <p class="ls-label-info">Informe apenas se precisar descrever o feriado. Ex.: PROCLAMAÇÃO DA REPÚBLICA</p>
        <input type="text" name="ce_descricao" value="" size="32">
        </label>
        <label class="ls-label col-md-6">
         
        </label>
        <input type="hidden" name="ce_id_sec" value="<?php echo $row_UsuarioLogado['usu_sec']; ?>">
        <input type="hidden" name="ce_ano" value="<?php echo $row_AnoLetivo['ano_letivo_ano']; ?>">
        <input type="hidden" name="ce_data" value="<?php echo $dataValida; ?>" size="32">
        <input type="hidden" name="MM_insert" value="form1">
      
      
      
      
      </p>
    </div>
    <div class="ls-modal-footer">
       <button type="submit" class="ls-btn-primary">SALVAR</button>    	
        <a href="calendario_escolar.php" class="ls-btn">VOLTAR</a>
    </div>
    </form>
  </div>
</div><!-- /.modal -->
      
      
      

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>
<script>

locastyle.modal.open("#myAwesomeModal");


</script>
</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

//mysql_free_result($ListaCalendario);
?>