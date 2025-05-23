<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "fnc/inverteData.php"; ?>

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

$ordem = "";
$escreve_ordem = "";

if (isset($_GET['ordem'])) {
  $ordem = $_GET['ordem'];
	
  switch ($ordem) {
	  case "nome":
		  $ordem = " ORDER BY func_nome ASC";
		  $escreve_ordem = "*Ordenado por nome";
		  break;
		  
	  case "funcao":
		  $ordem = " ORDER BY funcao_nome ASC";
		  $escreve_ordem = "*Ordenado por função";
		  break;
		  
	  case "ch":
		  $ordem = " ORDER BY vinculo_carga_horaria ASC";
		  $escreve_ordem = "*Ordenado por carga horária";
		  break;
		  
	  case "data":
		  $ordem = " ORDER BY vinculo_data_inicio ASC";
		  $escreve_ordem = "*Ordenado por data de início";
		  break;
		  
		  default:
		  $ordem = "";
		  $escreve_ordem = "";
  }
}


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_FuncionariosVinculados = "
SELECT vinculo_id, vinculo_id_escola, vinculo_id_funcionario, vinculo_id_funcao, vinculo_carga_horaria, vinculo_data_inicio, 
vinculo_obs, func_id, func_nome, funcao_id, funcao_nome 
FROM smc_vinculo INNER JOIN smc_func ON func_id = vinculo_id_funcionario INNER JOIN smc_funcao ON funcao_id = vinculo_id_funcao 
WHERE vinculo_id_escola = '$row_UsuLogado[usu_escola]' AND vinculo_status != 2 $ordem";
$FuncionariosVinculados = mysql_query($query_FuncionariosVinculados, $SmecelNovo) or die(mysql_error());
$row_FuncionariosVinculados = mysql_fetch_assoc($FuncionariosVinculados);
$totalRows_FuncionariosVinculados = mysql_num_rows($FuncionariosVinculados);
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
 
        <h1 class="ls-title-intro ls-ico-home">Relação de funcionários</h1>
        <?php if ($totalRows_FuncionariosVinculados > 0) { // Show if recordset not empty ?>
        <table class="ls-table">
          <thead>
            <tr>
              <th class="ls-data-descending"><a href="?ordem=nome">NOME</a></th>
              <th class="ls-data-descending ls-txt-center"><a href="?ordem=funcao">FUNÇÃO</a></th>
              <th class="ls-data-descending ls-txt-center"><a href="?ordem=ch">CARGA HORÁRIA</a></th>
              <th class="ls-data-descending ls-txt-center"><a href="?ordem=data">DATA DE INÍCIO</a></th>
              <th class="ls-txt-center">OBS</th>
            </tr>
          </thead>
          <tbody>
            <?php do { ?>
              <tr>
                <td><?php echo $row_FuncionariosVinculados['func_nome']; ?></td>
                <td class="ls-txt-center"><?php echo $row_FuncionariosVinculados['funcao_nome']; ?></td>
                <td class="ls-txt-center"><?php echo $row_FuncionariosVinculados['vinculo_carga_horaria']; ?></td>
                <td class="ls-txt-center"><?php echo inverteData($row_FuncionariosVinculados['vinculo_data_inicio']); ?></td>
                <td class="ls-txt-center"><?php echo $row_FuncionariosVinculados['vinculo_obs']; ?></td>
              </tr>
              <?php } while ($row_FuncionariosVinculados = mysql_fetch_assoc($FuncionariosVinculados)); ?>
            </tbody>
        </table>
		
		<div class="ls-txt-center">
			<a class="ls-btn ls-ico-windows" href="print_funcionarios_vinculados.php" target="_blank"> Imprimir</a>
		</div>
		
		<hr>
		
          <?php } else { ?>
			  <div class="ls-alert-info"><strong>Atenção:</strong> Nenhum funcionário vinculado.</div>
          <?php } // Show if recordset not empty ?>
          
          
          
<small><?php echo $escreve_ordem; ?></small>
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
 
  </body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($FuncionariosVinculados);
?>
