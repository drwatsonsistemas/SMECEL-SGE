<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "fnc/inverteData.php"; ?>

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
		header(sprintf("Location: funcListar.php?permissao"));
		break;
	}

  $insertSQL = sprintf("INSERT INTO smc_aviso_prof (aviso_prof_id_escola, aviso_prof_data_cadastro, aviso_prof_texto, aviso_prof_exibir_ate) VALUES (%s, %s, %s, %s)",
                       GetSQLValueString($_POST['aviso_prof_id_escola'], "int"),
                       GetSQLValueString($_POST['aviso_prof_data_cadastro'], "date"),
                       GetSQLValueString($_POST['aviso_prof_texto'], "text"),
                       GetSQLValueString($_POST['aviso_prof_exibir_ate']), "date");

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

  $insertGoTo = "aviso_prof.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}



mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EscolaLogada = "
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, 
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, sec_id, sec_cidade, sec_uf, escola_tema 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Avisos = "SELECT aviso_prof_id, aviso_prof_id_escola, aviso_prof_data_cadastro, aviso_prof_texto, aviso_prof_exibir_ate FROM smc_aviso_prof WHERE aviso_prof_id_escola = '$row_UsuLogado[usu_escola]' ORDER BY aviso_prof_exibir_ate DESC";
$Avisos = mysql_query($query_Avisos, $SmecelNovo) or die(mysql_error());
$row_Avisos = mysql_fetch_assoc($Avisos);
$totalRows_Avisos = mysql_num_rows($Avisos);
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
    <?php include_once ("menu-top.php"); ?>
          <?php include_once ("menu-esc.php"); ?>


    <main class="ls-main ">
      <div class="container-fluid">
 
        <h1 class="ls-title-intro ls-ico-home">AVISO / PROFESSORES</h1>
		<!-- CONTEÚDO -->
        
        <button data-ls-module="modal" data-target="#myAwesomeModal" class="ls-btn-primary">GERAR NOVO AVISO</button>
        
        
              <?php if (isset($_GET["editado"])) { ?>
                <div class="ls-alert-success ls-dismissable">
                  <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
                  <span class="ls-ico-checkmark ls-color-success"></span> AVISO EDITADO COM SUCESSO.
                </div>
              <?php } ?>
        
<div class="ls-modal" id="myAwesomeModal">
  <div class="ls-modal-box">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title">NOVO AVISO</h4>
    </div>
    <div class="ls-modal-body" id="myModalBody">
      <p>
      
      
  <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form row">
          
          <label class="ls-label col-md-12">
      <b class="ls-label-text">AVISO</b>
      <p class="ls-label-info">Digite o texto do aviso que será exibido no painel do professor</p>
          <textarea name="aviso_prof_texto" cols="50" rows="5"  required></textarea>
            </label>
            
            
   <label class="ls-label col-md-6 col-sm-12">
      <b class="ls-label-text">DATA LIMITE</b>
      <p class="ls-label-info">Digite a data limite de exibição do aviso</p>
      <input type="date" class="datepicker1" name="aviso_prof_exibir_ate" autocomplete="off" required>
  </label>
            
            

            
          <input type="hidden" name="aviso_prof_id_escola" value="<?php echo $row_UsuLogado['usu_escola']; ?>">
          <input type="hidden" name="aviso_prof_data_cadastro" value="<?php echo date('Y-m-d'); ?>">
          <input type="hidden" name="MM_insert" value="form1">

      
      
      </p>
    </div>
    <div class="ls-modal-footer">
      <a href="#" class="ls-btn ls-float-right" data-dismiss="modal">FECHAR</a>
      <input type="submit" value="SALVAR" class="ls-btn">
    </div>
    
        </form>
  </div>
</div><!-- /.modal -->
        
        
        
        <p>&nbsp;</p>
        <?php if ($totalRows_Avisos > 0) { // Show if recordset not empty ?>
<table class="ls-table">
          <thead>
          <tr>
            <th width="100" class="ls-txt-center">STATUS</th>
            <th width="120" class="ls-txt-center">CADASTRO</th>
            <th width="120" class="ls-txt-center">EXPIRA EM</th>
            <th class="ls-txt-center">AVISO</th>
            <th width="50" class="ls-txt-center"> </th>
          </tr>
          </thead>
          <tbody>
          <?php do { ?>
            <tr>
              <td class="ls-txt-center"><?php  if ($row_Avisos['aviso_prof_exibir_ate'] >= date('Y-m-d')) { ?><span class="ls-ico-eye ls-color-success"></span><?php } else { ?><span class="ls-ico-eye-blocked"></span>
          <?php } ?>
                  </td>
                <td class="ls-txt-center"><?php echo inverteData($row_Avisos['aviso_prof_data_cadastro']); ?></td>
              <td class="ls-txt-center"><?php echo inverteData($row_Avisos['aviso_prof_exibir_ate']); ?></td>
              <td><?php echo $row_Avisos['aviso_prof_texto']; ?></td>
              <td class="ls-txt-center"><a href="aviso_prof_editar.php?cod=<?php echo $row_Avisos['aviso_prof_id']; ?>"><span class="ls-ico-edit-admin"></span></a></td>
            </tr>
            <?php } while ($row_Avisos = mysql_fetch_assoc($Avisos)); ?>
        	</tbody>
        </table>
<?php } // Show if recordset not empty ?>
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
    	<script src="js/pikaday.js"></script> 
<script>
	//locastyle.modal.open("#myAwesomeModal");
	locastyle.datepicker.newDatepicker('#aviso_prof_exibir_ate');
	</script>
 
  </body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($Avisos);

mysql_free_result($EscolaLogada);
?>
