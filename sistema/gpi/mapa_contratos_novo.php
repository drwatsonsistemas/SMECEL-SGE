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

$situacao = "N";
$texto = " ATIVOS";

if (isset($_GET['situacao'])) {

  $situacao = $_GET['situacao'];
  
  if ($situacao=="S") {
  
  $texto = " INATIVOS";

  } 

}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Prefeituras = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_ibge_municipio, sec_regra_media, sec_bloqueada, sec_aviso_bloqueio FROM smc_sec WHERE sec_bloqueada = '$situacao' ORDER BY sec_prefeitura ASC";
$Prefeituras = mysql_query($query_Prefeituras, $SmecelNovo) or die(mysql_error());
$row_Prefeituras = mysql_fetch_assoc($Prefeituras);
$totalRows_Prefeituras = mysql_num_rows($Prefeituras);
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" type="text/css" href="//assets.locaweb.com.br/locastyle/edge/stylesheets/locastyle.css">
<script src="//assets.locaweb.com.br/locastyle/edge/javascripts/locastyle.js"></script>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">

  <style>
html, body {
            height: 100%; /* Altura total da página */
            margin: 0;    /* Remove margens */
            padding: 0;   /* Remove preenchimentos */
        }

        /* O mapa ocupa 100% da altura e largura */
        #map {
            height: 100%; /* Altura total disponível */
            width: 100%;  /* Largura total disponível */
        }
    </style>


  

</head>
<body>
<?php include_once("menu_top.php"); ?>
<?php include_once "menu.php"; ?>
<main class="ls-main">
  <div class="container-fluid">
    <h1 class="ls-title-intro ls-ico-home">CONTRATOS <?php echo $texto; ?></h1>
    <div class="ls-box ls-board-box"> 
    <!-- CONTEUDO -->
    
    <div id="container" style="height: 80vh; width: 100%;">
    <div id="map" style="height: 100%; width: 100%;"></div>
</div>



    <!-- Carregamento assíncrono e otimizado -->
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBZmOxO_HqkcybUcGsj_aOIe-zsOlxq9Ak&callback=initMap"></script>
    <script>
        // Lista de cidades
        const cities = [

          <?php do { ?>
                "<?php echo $row_Prefeituras['sec_cidade']; ?>, <?php echo $row_Prefeituras['sec_uf']; ?>",
            <?php } while ($row_Prefeituras = mysql_fetch_assoc($Prefeituras)); ?>

        ];

        // URL do ícone de alfinete
        //const pinIcon = "https://maps.google.com/mapfiles/kml/pushpin/red-pushpin.png";
        const pinIcon = "https://www.smecel.com.br/img/pushpin.png";


        // Função de inicialização do mapa
        function initMap() {
            // Cria o mapa, centralizado no Brasil
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 7,
                center: { lat: -16.088468628452596, lng: -39.614260223784896 }, // Centro do Brasil
                mapTypeId: "roadmap"
            });

            

            // Inicializa o geocoder
            const geocoder = new google.maps.Geocoder();

            // Processa cada cidade
            cities.forEach(city => {
                geocodeCity(geocoder, map, city);
            });
        }

        // Função para buscar coordenadas de uma cidade
        function geocodeCity(geocoder, map, city) {
            geocoder.geocode({ address: city }, (results, status) => {
                if (status === "OK") {
                    const location = results[0].geometry.location;

                    // Adiciona o marcador no mapa
                    const marker = new google.maps.Marker({
                        position: location,
                        map: map,
                        icon: pinIcon,
                        title: city
                    });

                    // Adiciona uma janela de informações ao marcador
                    const infoWindow = new google.maps.InfoWindow({
                        content: `<h3>${city}</h3>`
                    });

                    // Evento para abrir a janela de informações ao clicar no marcador
                    marker.addListener("click", () => {
                        infoWindow.open(map, marker);
                    });
                } else {
                    console.error(`Erro ao buscar coordenadas para ${city}: ${status}`);
                }
            });
        }
    </script>




    


  
    
    
    
    <!-- CONTEUDO -->    
    </div>
  </div>
</main>
<?php include_once "notificacoes.php"; ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
<script src="https://assets.locaweb.com.br/locastyle/3.8.4/javascripts/locastyle.js" type="text/javascript"></script>
</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Prefeituras);
?>