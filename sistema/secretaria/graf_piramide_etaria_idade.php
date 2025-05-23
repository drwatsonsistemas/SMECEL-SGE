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

require_once('funcoes/usuLogado.php');
require_once('funcoes/anoLetivo.php');

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_VinculosIdade = "

 SELECT 
      CASE
        WHEN YEAR(CURRENT_DATE) - YEAR(aluno_nascimento) - (DATE_FORMAT(CURRENT_DATE, '%m%d') < DATE_FORMAT(aluno_nascimento, '%m%d')) BETWEEN 0 AND 4 THEN 'A = 00 a 04 anos'
        WHEN YEAR(CURRENT_DATE) - YEAR(aluno_nascimento) - (DATE_FORMAT(CURRENT_DATE, '%m%d') < DATE_FORMAT(aluno_nascimento, '%m%d')) BETWEEN 5 AND 9 THEN 'B = 05 a 09 anos'
        WHEN YEAR(CURRENT_DATE) - YEAR(aluno_nascimento) - (DATE_FORMAT(CURRENT_DATE, '%m%d') < DATE_FORMAT(aluno_nascimento, '%m%d')) BETWEEN 10 AND 14 THEN 'C = 10 a 14 anos'
        WHEN YEAR(CURRENT_DATE) - YEAR(aluno_nascimento) - (DATE_FORMAT(CURRENT_DATE, '%m%d') < DATE_FORMAT(aluno_nascimento, '%m%d')) BETWEEN 15 AND 19 THEN 'D = 15 a 19 anos'
        WHEN YEAR(CURRENT_DATE) - YEAR(aluno_nascimento) - (DATE_FORMAT(CURRENT_DATE, '%m%d') < DATE_FORMAT(aluno_nascimento, '%m%d')) BETWEEN 20 AND 24 THEN 'E = 20 a 24 anos'
        WHEN YEAR(CURRENT_DATE) - YEAR(aluno_nascimento) - (DATE_FORMAT(CURRENT_DATE, '%m%d') < DATE_FORMAT(aluno_nascimento, '%m%d')) BETWEEN 25 AND 29 THEN 'F = 25 a 29 anos'
        WHEN YEAR(CURRENT_DATE) - YEAR(aluno_nascimento) - (DATE_FORMAT(CURRENT_DATE, '%m%d') < DATE_FORMAT(aluno_nascimento, '%m%d')) BETWEEN 30 AND 34 THEN 'G = 30 a 34 anos'
        WHEN YEAR(CURRENT_DATE) - YEAR(aluno_nascimento) - (DATE_FORMAT(CURRENT_DATE, '%m%d') < DATE_FORMAT(aluno_nascimento, '%m%d')) BETWEEN 35 AND 39 THEN 'H = 35 a 39 anos'
        WHEN YEAR(CURRENT_DATE) - YEAR(aluno_nascimento) - (DATE_FORMAT(CURRENT_DATE, '%m%d') < DATE_FORMAT(aluno_nascimento, '%m%d')) BETWEEN 40 AND 44 THEN 'I = 40 a 44 anos'
        WHEN YEAR(CURRENT_DATE) - YEAR(aluno_nascimento) - (DATE_FORMAT(CURRENT_DATE, '%m%d') < DATE_FORMAT(aluno_nascimento, '%m%d')) BETWEEN 45 AND 49 THEN 'J = 45 a 49 anos'
        WHEN YEAR(CURRENT_DATE) - YEAR(aluno_nascimento) - (DATE_FORMAT(CURRENT_DATE, '%m%d') < DATE_FORMAT(aluno_nascimento, '%m%d')) BETWEEN 50 AND 54 THEN 'K = 50 a 54 anos'
        WHEN YEAR(CURRENT_DATE) - YEAR(aluno_nascimento) - (DATE_FORMAT(CURRENT_DATE, '%m%d') < DATE_FORMAT(aluno_nascimento, '%m%d')) BETWEEN 55 AND 59 THEN 'L = 55 a 59 anos'
        WHEN YEAR(CURRENT_DATE) - YEAR(aluno_nascimento) - (DATE_FORMAT(CURRENT_DATE, '%m%d') < DATE_FORMAT(aluno_nascimento, '%m%d')) BETWEEN 60 AND 64 THEN 'M = 60 a 64 anos'
        WHEN YEAR(CURRENT_DATE) - YEAR(aluno_nascimento) - (DATE_FORMAT(CURRENT_DATE, '%m%d') < DATE_FORMAT(aluno_nascimento, '%m%d')) BETWEEN 65 AND 69 THEN 'N = 65 a 69 anos'
        WHEN YEAR(CURRENT_DATE) - YEAR(aluno_nascimento) - (DATE_FORMAT(CURRENT_DATE, '%m%d') < DATE_FORMAT(aluno_nascimento, '%m%d')) BETWEEN 70 AND 74 THEN 'O = 70 a 74 anos'
        WHEN YEAR(CURRENT_DATE) - YEAR(aluno_nascimento) - (DATE_FORMAT(CURRENT_DATE, '%m%d') < DATE_FORMAT(aluno_nascimento, '%m%d')) BETWEEN 75 AND 79 THEN 'P = 75 a 79 anos'
        WHEN YEAR(CURRENT_DATE) - YEAR(aluno_nascimento) - (DATE_FORMAT(CURRENT_DATE, '%m%d') < DATE_FORMAT(aluno_nascimento, '%m%d')) BETWEEN 80 AND 84 THEN 'Q = 80 a 84 anos'
        WHEN YEAR(CURRENT_DATE) - YEAR(aluno_nascimento) - (DATE_FORMAT(CURRENT_DATE, '%m%d') < DATE_FORMAT(aluno_nascimento, '%m%d')) BETWEEN 85 AND 89 THEN 'R = 85 a 89 anos'
        WHEN YEAR(CURRENT_DATE) - YEAR(aluno_nascimento) - (DATE_FORMAT(CURRENT_DATE, '%m%d') < DATE_FORMAT(aluno_nascimento, '%m%d')) BETWEEN 90 AND 94 THEN 'S = 90 a 94 anos'
        WHEN YEAR(CURRENT_DATE) - YEAR(aluno_nascimento) - (DATE_FORMAT(CURRENT_DATE, '%m%d') < DATE_FORMAT(aluno_nascimento, '%m%d')) BETWEEN 95 AND 99 THEN 'T = 95 a 99 anos'
        WHEN YEAR(CURRENT_DATE) - YEAR(aluno_nascimento) - (DATE_FORMAT(CURRENT_DATE, '%m%d') < DATE_FORMAT(aluno_nascimento, '%m%d')) >= 100 THEN 'U = 100 anos ou mais'
        ELSE 'Outros'
      END AS faixa_etaria,
      COUNT(CASE WHEN smc_aluno.aluno_sexo = '1' THEN 1 END) AS homens,
      COUNT(CASE WHEN smc_aluno.aluno_sexo = '2' THEN 1 END) AS mulheres,
      COUNT(CASE WHEN smc_aluno.aluno_sexo IS NULL THEN 1 END) AS seminformacao
    FROM smc_vinculo_aluno
    INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola
    INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
    INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
    WHERE turma_tipo_atendimento = '1'
      AND vinculo_aluno_id_sec = '$row_UsuarioLogado[usu_sec]'
      AND vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'
      AND vinculo_aluno_situacao = '1'
      AND escola_situacao = '1'
    GROUP BY faixa_etaria
    ORDER BY faixa_etaria

";
$VinculosIdade = mysql_query($query_VinculosIdade, $SmecelNovo) or die(mysql_error());
$row_VinculosIdade = mysql_fetch_assoc($VinculosIdade);
$totalRows_VinculosIdade = mysql_num_rows($VinculosIdade);

// Loop through the result set from MySQL and output the data for the chart

// Criando arrays para armazenar os dados
$faixas_etarias = [];
$homens = [];
$mulheres = [];

/*
while ($row_VinculosIdade = mysql_fetch_assoc($VinculosIdade)) {
  $faixas_etarias[] = $row_VinculosIdade['faixa_etaria'];
  $homens[] = $row_VinculosIdade['homens'] * -1; // Usando valores negativos para homens
  $mulheres[] = $row_VinculosIdade['mulheres'];
}
*/

$faixas_etarias_json = json_encode($faixas_etarias);
$homens_json = json_encode($homens);
$mulheres_json = json_encode($mulheres);

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



  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['bar']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Faixa-etária', 'Alunos', 'Alunas', 'Sem informação'],


          <?php
          
          do {
              echo "['" . $row_VinculosIdade['faixa_etaria'] . "', " . $row_VinculosIdade['homens'] . ", " . $row_VinculosIdade['mulheres'] . ", " . $row_VinculosIdade['seminformacao']. "], ";
            } while ($row_VinculosIdade = mysql_fetch_assoc($VinculosIdade));
          
          ?>  


        ]);

        var options = {
          chart: {
            title: 'Pirâmide-etária',
            subtitle: 'Alunos separados por faixa-etária e gênero ',
          },
          bars: 'horizontal',
          bar: { groupWidth: "90%" }, // Required for Material Bar Charts.
          hAxis: {
            format: 'decimal'
          },
          annotations: {
            alwaysOutside: true, // Garantir que o rótulo esteja sempre fora da barra
            textStyle: {
              fontSize: 12,
              auraColor: 'none'
            }
          }
        };

        var chart = new google.charts.Bar(document.getElementById('barchart_material'));

        chart.draw(data, google.charts.Bar.convertOptions(options));
      }
    </script>


</head>
<body>
<?php include_once("menu_top.php"); ?>
<?php include_once "menu.php"; ?>
<main class="ls-main">
  <div class="container-fluid">
    <h1 class="ls-title-intro ls-ico-home">Quantidade de alunos por faixa-etária</h1>
    <!-- CONTEUDO -->
    
    <a href="relatorios_alunos.php" class="ls-btn-primary ls-ico-chevron-left ">Voltar</a>

    <hr>


    <div id="barchart_material" style="width: 100%; height: 80vh;"></div>
    
    
    <p>&nbsp;</p>
    <!-- CONTEUDO -->    
  </div>
</main>
<?php include_once "notificacoes.php"; ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>
</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);
?>