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

include "usuLogado.php";
include "fnc/anoLetivo.php";

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	

	if ($row_UsuLogado['usu_update']=="N") {
		header(sprintf("Location: oficios.php?permissao"));
		break;
	}
	
  $updateSQL = sprintf("UPDATE smc_oficios SET oficio_numeracao=%s, oficio_ano_letivo=%s, oficio_cabecalho=%s, oficio_texto=%s, oficio_diretor=%s WHERE oficio_id=%s",
   GetSQLValueString($_POST['oficio_numeracao'], "text"),
   GetSQLValueString($_POST['oficio_ano_letivo'], "text"),
   GetSQLValueString($_POST['oficio_cabecalho'], "text"),
   GetSQLValueString($_POST['oficio_texto'], "text"),
   GetSQLValueString($_POST['oficio_diretor'], "text"),
   GetSQLValueString($_POST['oficio_id'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());
  
  
  $usu = $_POST['usu_id'];
  $esc = $_POST['escola_id'];
  $oficio1 = $_POST['oficio_numeracao1'];
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
    '4', 
    'Nº $oficio1', 
    '$dat')
    ";
    mysql_select_db($database_SmecelNovo, $SmecelNovo);
    $Result1 = mysql_query($sql, $SmecelNovo) or die(mysql_error());  



    $updateGoTo = "oficios.php?editado";
    if (isset($_SERVER['QUERY_STRING'])) {
      $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
      $updateGoTo .= $_SERVER['QUERY_STRING'];
    }
    header(sprintf("Location: %s", $updateGoTo));
  }

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

  $colname_oficiosEditar = "-1";
  if (isset($_GET['oficio'])) {
    $colname_oficiosEditar = $_GET['oficio'];
  }
  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $query_oficiosEditar = sprintf("SELECT oficio_id, oficio_id_escola, oficio_numeracao, oficio_ano_letivo, oficio_data, oficio_cabecalho, oficio_texto, oficio_diretor, oficio_hash FROM smc_oficios WHERE oficio_hash = %s", GetSQLValueString($colname_oficiosEditar, "text"));
  $oficiosEditar = mysql_query($query_oficiosEditar, $SmecelNovo) or die(mysql_error());
  $row_oficiosEditar = mysql_fetch_assoc($oficiosEditar);
  $totalRows_oficiosEditar = mysql_num_rows($oficiosEditar);
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
    <link rel="stylesheet" type="text/css" href="css/locastyle.css">    <link rel="stylesheet" type="text/css" href="css/preloader.css">
<script src="js/locastyle.js"></script>
    <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
  </head>
  <body>
    <?php include_once ("menu-top.php"); ?>
    <?php include_once ("menu-esc.php"); ?>


    <main class="ls-main ">
      <div class="container-fluid">

        <h1 class="ls-title-intro ls-ico-home">EDITAR OFÍCIO</h1>
        <!-- CONTEÚDO -->
        <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form ls-form-horizontal row">


          <label class="ls-label col-md-2">
            <input type="text" name="oficio_numeracao" value="<?php echo htmlentities($row_oficiosEditar['oficio_numeracao'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>         

          <label class="ls-label col-md-2">
            <input type="text" name="oficio_ano_letivo" value="<?php echo htmlentities($row_oficiosEditar['oficio_ano_letivo'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>         

          <label class="ls-label col-md-12">
            <b class="ls-label-text">CABEÇALHO</b>
            <p class="ls-label-info">Informe os dados do destinatário e endereçamento</p>
            <textarea name="oficio_cabecalho" cols="50" rows="5"><?php echo htmlentities($row_oficiosEditar['oficio_cabecalho'], ENT_COMPAT, 'utf-8'); ?></textarea>    
          </label>            

          <label class="ls-label col-md-12">
            <b class="ls-label-text">TEXTO</b>
            <p class="ls-label-info">Informe o texto do ofício</p>
            <textarea name="oficio_texto" cols="10" rows="10" id="mytextarea"><?php echo htmlentities($row_oficiosEditar['oficio_texto'], ENT_COMPAT, 'utf-8'); ?></textarea>
          </label>	

          <label class="ls-label col-md-12">
            <b class="ls-label-text">DIRETOR(A) ESCOLAR</b>      
            <input type="text" name="oficio_diretor" value="<?php echo htmlentities($row_oficiosEditar['oficio_diretor'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>

          <label class="ls-label col-md-12">
            <input type="submit" value="SALVAR" class="ls-btn-primary">
          </label>            

          <input type="hidden" name="oficio_id" value="<?php echo $row_oficiosEditar['oficio_id']; ?>">
          <input type="hidden" name="MM_update" value="form1">

          <input type="hidden" name="usu_id" value="<?php echo $row_UsuLogado['usu_id']; ?>">
          <input type="hidden" name="escola_id" value="<?php echo $row_EscolaLogada['escola_id']; ?>">
          <input type="hidden" name="oficio_numeracao1" value="<?php echo htmlentities($row_oficiosEditar['oficio_numeracao'], ENT_COMPAT, 'utf-8'); ?>/<?php echo htmlentities($row_oficiosEditar['oficio_ano_letivo'], ENT_COMPAT, 'utf-8'); ?>" size="32">



        </form>
        <p>&nbsp;</p>
        <!-- CONTEÚDO -->
      </div>
    </main>

    <aside class="ls-notification">
      <nav class="ls-notification-list" id="ls-notification-curtain" style="left: 1716px;">
        <h3 class="ls-title-2">Notificações</h3>
        <ul>
          <?php include "notificacoes.php"; ?>
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

    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <!--<script src="https://cdn.tiny.cloud/1/iq1gy8qt7s1b9bj92c2fc7whie95augu8r8kz97dgoufkli8/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>-->
    <script src="langs/pt_BR.js"></script>

    <script>

     $('#mytextarea').summernote({
      placeholder: 'Digite aqui...',
      tabsize: 2,
      height: 250,
      toolbar: [
        ['style', ['style']],
        ['font', ['bold', 'underline', 'clear']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['table', ['table']],
        ['insert', []],
        ['view', []]
        ]
    });
  </script>
</body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($oficiosEditar);
?>
