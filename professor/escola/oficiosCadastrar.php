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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
	if ($row_UsuLogado['usu_insert']=="N") {
		header(sprintf("Location: oficios.php?permissao"));
		break;
	}
	
  $insertSQL = sprintf("INSERT INTO smc_oficios (oficio_id_escola, oficio_numeracao, oficio_ano_letivo, oficio_data, oficio_cabecalho, oficio_texto, oficio_diretor, oficio_hash) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
   GetSQLValueString($_POST['oficio_id_escola'], "int"),
   GetSQLValueString($_POST['oficio_numeracao'], "text"),
   GetSQLValueString($_POST['oficio_ano_letivo'], "text"),
   GetSQLValueString($_POST['oficio_data'], "date"),
   GetSQLValueString($_POST['oficio_cabecalho'], "text"),
   GetSQLValueString($_POST['oficio_texto'], "text"),
   GetSQLValueString($_POST['oficio_diretor'], "text"),
   GetSQLValueString(md5($_POST['oficio_hash']), "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());
  
  
  
  $usu = $_POST['usu_id'];
  $esc = $_POST['escola_id'];
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
    '3', 
    '$_POST[oficio_numeracao]/$_POST[oficio_ano_letivo] - $_POST[oficio_cabecalho]', 
    '$dat')
    ";
    mysql_select_db($database_SmecelNovo, $SmecelNovo);
    $Result1 = mysql_query($sql, $SmecelNovo) or die(mysql_error());  

    

    $insertGoTo = "oficios.php?cadastrado";
    if (isset($_SERVER['QUERY_STRING'])) {
      $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
      $insertGoTo .= $_SERVER['QUERY_STRING'];
    }
    header(sprintf("Location: %s", $insertGoTo));
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

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $query_ultimoOficio = "
  SELECT oficio_id, oficio_id_escola, oficio_numeracao, oficio_ano_letivo, oficio_data, oficio_cabecalho, oficio_texto, oficio_diretor, oficio_hash 
  FROM smc_oficios 
  WHERE oficio_id_escola = $row_EscolaLogada[escola_id]
  ORDER BY oficio_id DESC LIMIT 0,1";
  $ultimoOficio = mysql_query($query_ultimoOficio, $SmecelNovo) or die(mysql_error());
  $row_ultimoOficio = mysql_fetch_assoc($ultimoOficio);
  $totalRows_ultimoOficio = mysql_num_rows($ultimoOficio);

  $numeracao = $row_ultimoOficio['oficio_numeracao'] + 1 ;

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $query_listaDiretora = "
  SELECT vinculo_id, vinculo_id_escola, vinculo_id_funcionario, vinculo_id_funcao, func_id, func_nome, funcao_id, funcao_nome, funcao_gestor_escolar 
  FROM smc_vinculo
  INNER JOIN smc_func ON func_id = vinculo_id_funcionario
  INNER JOIN smc_funcao ON funcao_id = vinculo_id_funcao
  WHERE funcao_gestor_escolar = 'S' AND vinculo_status = '1' AND vinculo_id_escola = $row_EscolaLogada[escola_id]";
  $listaDiretora = mysql_query($query_listaDiretora, $SmecelNovo) or die(mysql_error());
  $row_listaDiretora = mysql_fetch_assoc($listaDiretora);
  $totalRows_listaDiretora = mysql_num_rows($listaDiretora);
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
       
        <h1 class="ls-title-intro ls-ico-home">OFÍCIOS</h1>
        <a href="oficios.php" class="ls-btn-primary">Voltar</a>
        <hr>
        <!-- CONTEÚDO -->
        <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form ls-form-horizontal row">
          
         <label class="ls-label col-md-12">
           <p class="ls-label-info">Número do último ofício: <?php echo $row_ultimoOficio['oficio_numeracao']; ?>/<?php echo $row_ultimoOficio['oficio_ano_letivo']; ?></p>
         </label>

         
         <label class="ls-label col-md-2">
           <input type="text" name="oficio_numeracao" value="<?php echo str_pad($numeracao, 3, '0', STR_PAD_LEFT); ?>" size="32"> 
         </label>
         
         <label class="ls-label col-md-2">
           <input type="text" name="oficio_ano_letivo" value="<?php echo date("Y"); ?>" size="32">
         </label>  
         
         
         
         <label class="ls-label col-md-12">
          <b class="ls-label-text">CABEÇALHO</b>
          <p class="ls-label-info">Informe os dados do destinatário e endereçamento</p>
          <textarea name="oficio_cabecalho" cols="50" rows="5"></textarea>  
        </label>			
        
        
        <label class="ls-label col-md-12">
          <b class="ls-label-text">TEXTO</b>
          <p class="ls-label-info">Informe o texto do ofício</p>
          <textarea name="oficio_texto" cols="50" rows="10" id="mytextarea"></textarea>
        </label>	
        
        <label class="ls-label col-md-12">
          <b class="ls-label-text">DIRETOR(A) ESCOLAR</b>      
          <input type="text" name="oficio_diretor" value="<?php echo $row_listaDiretora['func_nome']; ?>" size="32">	
        </label>	
        
        <label class="ls-label col-md-12">
          <input type="submit" value="SALVAR" class="ls-btn-primary">
          <a href="oficios.php" class="ls-btn">Cancelar</a>
          
          
          
        </label>	
        
        
        
        
        
        
        
        
        
        
        <input type="hidden" name="oficio_id_escola" value="<?php echo $row_EscolaLogada['escola_id']; ?>">
        <input type="hidden" name="oficio_data" value="<?php echo date('Y-m-d'); ?>">
        <input type="hidden" name="oficio_hash" value="<?php echo date('YmdHis'); ?>">
        <input type="hidden" name="MM_insert" value="form1">
        
        <input type="hidden" name="usu_id" value="<?php echo $row_UsuLogado['usu_id']; ?>">
        <input type="hidden" name="escola_id" value="<?php echo $row_EscolaLogada['escola_id']; ?>">

        
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

mysql_free_result($ultimoOficio);

mysql_free_result($listaDiretora);
?>
