<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include('../funcoes/inverteData.php'); ?>
<?php include('../funcoes/anti_injection.php'); ?>


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
$query_Ac = "
SELECT ac_id, ac_id_professor, ac_id_etapa, ac_id_componente, ac_id_escola, ac_ano_letivo, ac_data_inicial, ac_data_final, ac_conteudo, ac_objetivo_especifico, ac_objeto_conhecimento, ac_metodologia, ac_recursos, ac_avaliacao, ac_criacao,
func_id, func_nome, disciplina_id, disciplina_nome, etapa_id, etapa_nome 
FROM smc_ac
LEFT JOIN smc_func ON func_id = ac_id_professor
LEFT JOIN smc_disciplina ON disciplina_id = ac_id_componente 
LEFT JOIN smc_etapa ON etapa_id = ac_id_etapa
WHERE ac_id_escola = '$row_EscolaLogada[escola_id]' AND ac_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'
ORDER BY ac_id DESC
";
$Ac = mysql_query($query_Ac, $SmecelNovo) or die(mysql_error());
$row_Ac = mysql_fetch_assoc($Ac);
$totalRows_Ac = mysql_num_rows($Ac);

//


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
 
        <h1 class="ls-title-intro ls-ico-home">PLANEJAMENTO</h1>
		<!-- CONTEÚDO -->
        
        <a href="ava_aulas_professores.php" class="ls-btn">VOLTAR</a>
        
        <hr>
		
	
		
		
		
		
		

        
        <?php if ($totalRows_Ac > 0) { // Show if recordset not empty ?>
  <table class="ls-table ls-sm-space">
    <thead>
      <tr>
        <th>PROFESSOR</th>
        <th class="ls-txt-center">COMPONENTE</th>
        <th class="ls-txt-center">ETAPA</th>
        <th class="ls-txt-center" width="100">ANO</th>
        <th class="ls-txt-center" width="120">DATA INICIAL</th>
        <th class="ls-txt-center" width="120">DATA FINAL</th>
        <th class="ls-txt-center" width="120">DIAS</th>
        <th class="ls-txt-center">CADASTRO</th>
        <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
      <?php 
	  
	  $trocar = array("\"", "\'","'");
	  
	  do { ?>
        <tr>
          <td><?php echo $row_Ac['func_nome']; ?></td>
          <td class="ls-txt-center"><?php echo $row_Ac['disciplina_nome']; ?></td>
          <td class="ls-txt-center"><?php echo $row_Ac['etapa_nome']; ?></td>
          <td class="ls-txt-center"><?php echo $row_Ac['ac_ano_letivo']; ?></td>
          <td class="ls-txt-center"><?php echo inverteData($row_Ac['ac_data_inicial']); ?></td>
          <td class="ls-txt-center"><?php echo inverteData($row_Ac['ac_data_final']); ?></td>
          <td class="ls-txt-center"><?php $diferenca = strtotime($row_Ac['ac_data_final']) - strtotime($row_Ac['ac_data_inicial']); echo $dias = floor($diferenca / (60 * 60 * 24))+1; ?></td>
          <td class="ls-txt-center"><?php echo date("d/m/Y - H:i", strtotime($row_Ac['ac_criacao'])); ?></td>
		  <td class="ls-txt-center">
		  <button data-ls-module="modal" data-action="" data-content="
		  
		  <div class='ls-box'>
		  <h4>CONTEUDO</h4>
		  <p><?php echo str_replace($trocar, "", $row_Ac['ac_conteudo']); ?></p>
		  </div>
		  
		  <div class='ls-box'>
		  <h4>DIREITO DE APRENDIZAGEM</h4>
		  <p><?php echo str_replace($trocar, "", $row_Ac['ac_objetivo_especifico']); ?></p>
		  </div>
		  
		  <div class='ls-box'>
		  <h4>OBJETIVO DE APRENDIZAGEM</h4>
		  <p><?php echo str_replace($trocar, "", $row_Ac['ac_objeto_conhecimento']); ?></p>
		  </div>
		  
		  <div class='ls-box'>
		  <h4>METODOLOGIA</h4>
		  <p><?php echo str_replace($trocar, "", $row_Ac['ac_metodologia']); ?></p>
		  </div>
		  
		  <div class='ls-box'>
		  <h4>AVALIAÇÃO</h4>
		  <p><?php echo str_replace($trocar, "", $row_Ac['ac_avaliacao']); ?></p>
		  </div>
		  
		  
		  
		  " data-title="<?php echo $row_Ac['func_nome']; ?> - Componente: <?php echo $row_Ac['disciplina_nome']; ?> - Etapa: <?php echo $row_Ac['etapa_nome']; ?>" data-class="ls-btn-danger" data-save="" data-close="Fechar" class="ls-btn-primary"> Ver planejamento </button>
		  
		  </td>
        </tr>
        <?php } while ($row_Ac = mysql_fetch_assoc($Ac)); ?>
    </tbody>
  </table>
  <?php } else { ?>
  
  <p>Nenhum AC cadastrado</p>
  
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
 
  </body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($Ac);

mysql_free_result($EscolaLogada);
?>
