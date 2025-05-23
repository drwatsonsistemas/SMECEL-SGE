<?php require_once('../../Connections/SmecelNovo.php'); ?>
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


include "usuLogado.php";
include "fnc/anoLetivo.php";

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EscolaLogada = "
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, 
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_tema,  sec_id, sec_cidade, sec_uf 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

$colname_VerificaVinculo = "-1";
if (isset($_GET['cod'])) {
  $colname_VerificaVinculo = $_GET['cod'];
}
$colname_nome = "-1";
if (isset($_GET['nome'])) {
  $colname_nome = $_GET['nome'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_VerificaVinculo = sprintf("SELECT vinculo_id, vinculo_id_escola, vinculo_id_funcionario, vinculo_id_funcao, vinculo_data_inicio, vinculo_obs FROM smc_vinculo WHERE vinculo_id_escola = '$row_UsuLogado[usu_escola]' AND vinculo_id = %s", GetSQLValueString($colname_VerificaVinculo, "int"));
$VerificaVinculo = mysql_query($query_VerificaVinculo, $SmecelNovo) or die(mysql_error());
$row_VerificaVinculo = mysql_fetch_assoc($VerificaVinculo);
$totalRows_VerificaVinculo = mysql_num_rows($VerificaVinculo);

$query_Folhas = "SELECT * FROM smc_folha WHERE folha_hash = '$_GET[folha]'";
$Folhas = mysql_query($query_Folhas, $SmecelNovo) or die(mysql_error());
$row_Folhas = mysql_fetch_assoc($Folhas);
$totalRows_Folhas = mysql_num_rows($Folhas);

if ($totalRows_VerificaVinculo == 0) {
  header("Location: funcListar.php?erro");
  die;
}

if ($totalRows_Folhas == 0) {
  header("Location: funcListar.php?erro");
  die;
}

if ((isset($_GET['cod'])) && ($_GET['cod'] != "")) {

  if ($row_UsuLogado['usu_delete'] == "N") {
    header(sprintf("Location: funcListar.php?permissao"));
    die;
  }

  // Certifique-se de sanitizar adequadamente todas as variáveis antes de usá-las nas queries.
  $escola_id = GetSQLValueString($row_EscolaLogada['escola_id'], "int");
  $folha_id = GetSQLValueString($row_Folhas['folha_id'], "int");
  $vinculo_id = GetSQLValueString($_GET['cod'], "int");

  // Primeira query de exclusão
  $deleteSQL = sprintf(
    "DELETE FROM smc_vinculo WHERE vinculo_id_escola = %s AND vinculo_id = %s",
    $escola_id,
    $vinculo_id
  );

  // Segunda query de exclusão
  $deleteSQL2 = sprintf(
    "DELETE FROM smc_folha_lancamento WHERE folha_lanc_id_folha = %s AND folha_lanc_id_vinculo = %s",
    $folha_id,
    $vinculo_id
  );

  // Seleciona o banco de dados e executa as queries
  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());
  $Result2 = mysql_query($deleteSQL2, $SmecelNovo) or die(mysql_error());


  // ** REGISTRO DE LOG DE USUÁRIO **
  $usu = $row_UsuLogado['usu_id'];
  $esc = $row_UsuLogado['usu_escola'];

  date_default_timezone_set('America/Bahia');
  $dat = date('Y-m-d H:i:s');

  $sql = "
	INSERT INTO smc_registros (
	registros_id_escola, 
	registros_id_usuario, 
	registros_tipo, 
	registros_complemento, 
	registros_data_hora
	) VALUES (
	'$esc', 
	'$usu', 
	'25', 
	'$colname_nome', 
	'$dat')
	";
  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($sql, $SmecelNovo) or die(mysql_error());
  // ** REGISTRO DE LOG DE USUÁRIO **



  $deleteGoTo = "folha_pagamento_visualizar.php?excluido";

  if (isset($_GET['folha'])) {
    $folha = $_GET['folha'];
    $deleteGoTo .= "&folha=" . $folha;
  }

  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }

  header(sprintf("Location: %s", $deleteGoTo));
}

?>
<!DOCTYPE html>
<html class="<?php echo $row_EscolaLogada['escola_tema']; ?>" lang="pt-br">

<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
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
  <link rel="stylesheet" type="text/css" href="css/preloader.css">
  <script src="js/locastyle.js"></script>
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>

<body>
  <?php include_once("menu-top.php"); ?>
  <?php include_once("menu-esc.php"); ?>


  <main class="ls-main ">
    <div class="container-fluid">

      <h1 class="ls-title-intro ls-ico-home">Página inicial</h1>



    </div>
  </main>

  <aside class="ls-notification">
    <nav class="ls-notification-list" id="ls-notification-curtain" style="left: 1716px;">
      <h3 class="ls-title-2">Notificações</h3>
      <ul>
        <li class="ls-dismissable">
          <a href="#">Blanditiis est est dolorem iure voluptatem eos deleniti repellat et laborum consequatur</a>
          <a href="#" data-ls-module="dismiss" class="ls-ico-close ls-close-notification"></a>
        </li>
        <li class="ls-dismissable">
          <a href="#">Similique eos rerum perferendis voluptatibus</a>
          <a href="#" data-ls-module="dismiss" class="ls-ico-close ls-close-notification"></a>
        </li>
        <li class="ls-dismissable">
          <a href="#">Qui numquam iusto suscipit nisi qui unde</a>
          <a href="#" data-ls-module="dismiss" class="ls-ico-close ls-close-notification"></a>
        </li>
        <li class="ls-dismissable">
          <a href="#">Nisi aut assumenda dignissimos qui ea in deserunt quo deleniti dolorum quo et consequatur</a>
          <a href="#" data-ls-module="dismiss" class="ls-ico-close ls-close-notification"></a>
        </li>
        <li class="ls-dismissable">
          <a href="#">Sunt consequuntur aut aut a molestiae veritatis assumenda voluptas nam placeat eius ad</a>
          <a href="#" data-ls-module="dismiss" class="ls-ico-close ls-close-notification"></a>
        </li>
      </ul>
    </nav>

    <nav class="ls-notification-list" id="ls-help-curtain" style="left: 1756px;">
      <h3 class="ls-title-2">Feedback</h3>
      <ul>
        <li><a href="https://webmail.smecel.com.br" target="_blank">&gt; Acesse o webmail de sua escola</a></li>
      </ul>
    </nav>

    <nav class="ls-notification-list" id="ls-feedback-curtain" style="left: 1796px;">
      <h3 class="ls-title-2">Ajuda</h3>
      <ul>
        <li class="ls-txt-center hidden-xs">
          <a href="tutoriais_video.php" class="ls-btn-dark ls-btn-tour">Tutorial (Vídeos)</a>
        </li>
        <li><a href="#">&gt; Guia</a></li>
        <li><a href="#">&gt; Wiki</a></li>
      </ul>
    </nav>
  </aside>

  <!-- We recommended use jQuery 1.10 or up -->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="js/locastyle.js"></script>

</body>

</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($VerificaVinculo);
?>