<?php require_once ('../../Connections/SmecelNovo.php'); ?>
<?php // include "fnc/anoLetivo.php"; ?>

<?php include "fnc/session.php"; ?>
<?php
if (!function_exists('GetSQLValueString')) {
  function GetSQLValueString($theValue, $theType, $theDefinedValue = '', $theNotDefinedValue = '')
  {
    if (PHP_VERSION < 6) {
      $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
    }

    $theValue = function_exists('mysql_real_escape_string') ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

    switch ($theType) {
      case 'text':
        $theValue = ($theValue != '') ? "'" . $theValue . "'" : 'NULL';
        break;
      case 'long':
      case 'int':
        $theValue = ($theValue != '') ? intval($theValue) : 'NULL';
        break;
      case 'double':
        $theValue = ($theValue != '') ? doubleval($theValue) : 'NULL';
        break;
      case 'date':
        $theValue = ($theValue != '') ? "'" . $theValue . "'" : 'NULL';
        break;
      case 'defined':
        $theValue = ($theValue != '') ? $theDefinedValue : $theNotDefinedValue;
        break;
    }
    return $theValue;
  }
}

include 'usuLogado.php';
include 'fnc/anoLetivo.php';

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EscolaLogada = "
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, 
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, sec_id, sec_cidade, sec_uf, escola_tema 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die (mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Chamados = "SELECT chamado_id, chamado_id_sec, chamado_id_escola, chamado_id_usuario, chamado_id_telefone, chamado_data_abertura, chamado_categoria, chamado_situacao, chamado_titulo, chamado_texto, chamado_imagem, chamado_visualizado, chamado_numero FROM smc_chamados WHERE chamado_id_escola = '$row_UsuLogado[usu_escola]' ORDER BY chamado_id DESC";
$Chamados = mysql_query($query_Chamados, $SmecelNovo) or die (mysql_error());
$row_Chamados = mysql_fetch_assoc($Chamados);
$totalRows_Chamados = mysql_num_rows($Chamados);

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
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">

  <style>
    .float{
	position:fixed;
	width:60px;
	height:60px;
	bottom:40px;
	right:40px;
	background-color:#25d366;
	color:#FFF;
	border-radius:50px;
	text-align:center;
  font-size:30px;
	box-shadow: 2px 2px 3px #999;
  z-index:100;
}

.my-float{
	margin-top:16px;
}
  </style>
</head>
  <body>
    <?php include_once ('menu-top.php'); ?>
          <?php include_once ('menu-esc.php'); ?>


    <main class="ls-main ">
    <a href="https://api.whatsapp.com/send?phone=557398685288" class="float" target="_blank">
<i class="fa fa-whatsapp my-float"></i>
</a>
      <div class="container-fluid">
 
        <h1 class="ls-title-intro ls-ico-home">CHAMADOS</h1>
		<!-- CONTEÚDO -->
                
        <?php if (isset($_GET['cadastrado'])) { ?>
        <div class="ls-alert-success ls-dismissable"> 
        <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> 
        <strong>Atenção:</strong> Seu chamado foi criado e responderemos o mais rápido possível. Você receberá um e-mail assim que ocorrer alguma interação no chamado.</div>
        <?php } ?>
        
              <?php if (isset($_GET['permissao'])) { ?>
                <div class="ls-alert-danger ls-dismissable">
                  <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
                  VOCÊ NÃO TEM PERMISSÃO PARA REALIZAR ESTA AÇÃO.
                </div>
              <?php } ?>
        
        
        <a href="chamados_cadastrar.php" class="ls-btn-primary">ABRIR CHAMADO</a>
        
        <?php if ($totalRows_Chamados > 0) { // Show if recordset not empty ?>
  <table class="ls-table ls-sm-space">
    <thead>
      <tr>
        <th class="ls-txt-center" width="120">PROTOCOLO</th>
        <th class="ls-txt-center" width="120">SITUAÇÃO</th>
        <th class="ls-txt-center" width="120">DATA</th>
        <th class="ls-txt-center" width="120">CATEGORIA</th>
        <th class="ls-txt-center">TÍTULO</th>
        <th width="60"></th>
        </tr>
    </thead>
    <tbody>
      <?php do { ?>
        <tr>
          <td class="ls-txt-center"><a href="chamados_ver.php?chamado=<?php echo $row_Chamados['chamado_numero']; ?>"><?php echo $row_Chamados['chamado_numero']; ?></a></td>
          <td class="ls-txt-center"><?php if ($row_Chamados['chamado_situacao'] == 'A') { echo '<a href="#" class="ls-tag-info">ABERTO</a>'; } else { echo '<a href="#" class="ls-tag-warning">ENCERRADO</a>'; } ?></td>
          <td class="ls-txt-center"><?php echo date('d/m/Y H\hi', $row_Chamados['chamado_numero']); ?></td>
          <td class="ls-txt-center"><span class="ls-tag"><?php echo $row_Chamados['chamado_categoria']; ?></span></td>
          <td class="ls-txt-center"><?php echo $row_Chamados['chamado_titulo']; ?><br><small><?php if ($row_Chamados['chamado_visualizado'] == 'N') { ?>(Aguardando interação do suporte)<?php } ?></small></td>
          <td class="ls-txt-center"><a href="chamados_ver.php?chamado=<?php echo $row_Chamados['chamado_numero']; ?>" class="ls-ico-search"></a></td>
        </tr>
        <?php } while ($row_Chamados = mysql_fetch_assoc($Chamados)); ?>
    </tbody>
  </table>
  <?php } else { ?>
  <hr>
  <div class="ls-alert-info">Nenhum chamado cadastrado.</div>
  <?php } ?>
<!-- CONTEÚDO -->
      </div>
    </main>

    <aside class="ls-notification">
      <nav class="ls-notification-list" id="ls-notification-curtain" style="left: 1716px;">
        <h3 class="ls-title-2">Notificações</h3>
    <ul>
      <?php include 'notificacoes.php'; ?>
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

mysql_free_result($Chamados);

mysql_free_result($EscolaLogada);
?>
