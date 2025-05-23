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

$colname_UsuarioLogado = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_UsuarioLogado = $_SESSION['MM_Username'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_UsuarioLogado = sprintf("SELECT usu_id, usu_nome, usu_email, usu_senha, usu_tipo, usu_sec, usu_escola, usu_status, usu_cadastro FROM smc_usu WHERE usu_email = %s", GetSQLValueString($colname_UsuarioLogado, "text"));
$UsuarioLogado = mysql_query($query_UsuarioLogado, $SmecelNovo) or die(mysql_error());
$row_UsuarioLogado = mysql_fetch_assoc($UsuarioLogado);
$totalRows_UsuarioLogado = mysql_num_rows($UsuarioLogado);

$dataEscolhida = isset($_GET['data']) ? $_GET['data'] : date('Y-m-d'); // Data fornecida na URL ou data atual se não fornecida

// Validar a data (opcional, dependendo da sua necessidade)
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dataEscolhida)) {
    die("Data inválida. Por favor, use o formato YYYY-MM-DD.");
}



mysql_select_db($database_SmecelNovo, $SmecelNovo);
// Consulta para obter o total de logins por hora no dia anterior
$query_LoginsAluno = "
SELECT 
    HOUR(login_aluno_data_hora) AS hour,
    COUNT(*) AS total_accesses
FROM smc_login_aluno 
WHERE DATE(login_aluno_data_hora) = '$dataEscolhida'
GROUP BY HOUR(login_aluno_data_hora)
ORDER BY hour ASC
";
$LoginsAluno = mysql_query($query_LoginsAluno, $SmecelNovo) or die(mysql_error());
// Inicializa um array para armazenar o total de acessos por hora
$totalAcessosPorHora = array_fill(0, 24, 0);
// Atribui os valores totais de acessos para cada hora
while ($row_LoginsAluno = mysql_fetch_assoc($LoginsAluno)) {
    $totalAcessosPorHora[$row_LoginsAluno['hour']] = $row_LoginsAluno['total_accesses'];
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
// Consulta para obter o total de logins por hora no dia anterior
$query_LoginsProf = "
SELECT 
    HOUR(login_professor_data_hora) AS hour,
    COUNT(*) AS total_accesses
FROM smc_login_professor 
WHERE DATE(login_professor_data_hora) = '$dataEscolhida'
GROUP BY HOUR(login_professor_data_hora)
ORDER BY hour ASC
";
$LoginsProf = mysql_query($query_LoginsProf, $SmecelNovo) or die(mysql_error());
// Inicializa um array para armazenar o total de acessos por hora
$totalAcessosPorHoraProf = array_fill(0, 24, 0);
// Atribui os valores totais de acessos para cada hora
while ($row_LoginsProf = mysql_fetch_assoc($LoginsProf)) {
    $totalAcessosPorHoraProf[$row_LoginsProf['hour']] = $row_LoginsProf['total_accesses'];
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
// Consulta para obter o total de logins por hora no dia anterior
$query_LoginsPainel = "
SELECT 
    HOUR(log_data_hora) AS hour,
    COUNT(*) AS total_accesses
FROM smc_log 
WHERE DATE(log_data_hora) = '$dataEscolhida'
GROUP BY HOUR(log_data_hora)
ORDER BY hour ASC
";
$LoginsPainel = mysql_query($query_LoginsPainel, $SmecelNovo) or die(mysql_error());
// Inicializa um array para armazenar o total de acessos por hora
$totalAcessosPorHoraPainel = array_fill(0, 24, 0);
// Atribui os valores totais de acessos para cada hora
while ($row_LoginsPainel = mysql_fetch_assoc($LoginsPainel)) {
    $totalAcessosPorHoraPainel[$row_LoginsPainel['hour']] = $row_LoginsPainel['total_accesses'];
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

  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">

      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
		
        ['Hora', 'Alunos','Professores','Painel Administrativo'],

        <?php for ($i = 0; $i < 24; $i++) { ?>
          ['<?php echo $i ?>h', <?php echo $totalAcessosPorHora[$i]; ?>,  <?php echo $totalAcessosPorHoraProf[$i]; ?>,  <?php echo $totalAcessosPorHoraPainel[$i]; ?>],
        <?php } ?>
		  
        ]);
		
     var options = {
			vAxis: {minValue: 0},
			legend: {position: 'bottom', maxLines: 3},
   		    animation:{
				startup: true,	
				duration: 1000,
				easing: 'linear'
      		}			
        };

        //var chart = new google.visualization.ColumnChart(document.getElementById("chart_div_acessos"));
        //chart.draw(view, options);

        var chart = new google.visualization.AreaChart(document.getElementById('chart_div_acessos'));
        chart.draw(data, options);
      }
    </script>










</head>
<body>
<?php include_once("menu_top.php"); ?>
<?php include_once "menu.php"; ?>
<main class="ls-main">
  <div class="container-fluid">
    <h1 class="ls-title-intro ls-ico-home">ACESSOS POR HORA / ALUNOS</h1>

    <div class="ls-box-filter">
  <form action="login_hora.php" class="ls-form ls-form-inline">
    <label class="ls-label col-md-3 col-sm-4">
      <b class="ls-label-text">Data</b>
      <input type="date" name="data" class="" value="<?php echo $dataEscolhida; ?>">
    </label>

    <div class="ls-actions-btn">
      <button type="submit" class="ls-btn">Filtrar</button>
      <a href="login_hora.php" class="ls-btn">Hoje</a>
    </div>
  </form>
</div>

    <div class="ls-box ls-board-box"> 
    <!-- CONTEUDO -->


    
    
    <div id="chart_div_acessos" style="width: 100%; height: 400px;"></div>
    
    
    <!-- CONTEUDO -->    
    </div>
  </div>
</main>
<?php include_once "notificacoes.php"; ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="https://assets.locaweb.com.br/locastyle/3.8.4/javascripts/locastyle.js" type="text/javascript"></script>
</body>
</html>
<?php
mysql_free_result($UsuarioLogado);
?>