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


$colname_Data = "-1";
if (isset($_POST['data'])) {
  $colname_Data = $_POST['data'];
}

$colname_Hora = "-1";
if (isset($_POST['hora'])) {
  $colname_Hora = $_POST['hora'];
}

$colname_Alunos = "-1";
if (isset($_POST['ct'])) {
  $colname_Alunos = $_POST['ct'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Alunos = sprintf("
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, 
vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, 
vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, 
vinculo_aluno_vacina_atualizada, aluno_id, aluno_nome 
FROM smc_vinculo_aluno 
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
WHERE vinculo_aluno_id_turma = %s
ORDER BY aluno_nome ASC
", GetSQLValueString($colname_Alunos, "int"));
$Alunos = mysql_query($query_Alunos, $SmecelNovo) or die(mysql_error());
$row_Alunos = mysql_fetch_assoc($Alunos);
$totalRows_Alunos = mysql_num_rows($Alunos);
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
 
        <h1 class="ls-title-intro ls-ico-home">GERAR COMUNICADO</h1>
		<!-- CONTEÚDO -->
		
		
		<a href="comunicados.php" class="ls-btn-primary">VOLTAR</a>
		
		<hr>
        
        
        <form method="post" name="form1" action="print_comunicReuniao.php" target="_blank" class="ls-form row">
		
		<fieldset>
    <div class="ls-label col-md-12">
      <p>Selecione os alunos para gerar o comunicado:</p>
        
	
		
		
        <?php do { ?>
		
	  <label class="ls-label-text ls-xs-space">
        <input type="checkbox" name="aluno[]" value="<?php echo $row_Alunos['aluno_nome']; ?>">
       <?php echo $row_Alunos['aluno_nome']; ?>
      </label>

        <?php } while ($row_Alunos = mysql_fetch_assoc($Alunos)); ?>
		
			<label class="ls-label-text ls-box">
		<input type="checkbox" id="checkTodos" name="checkTodos"> SELECIONAR TUDO
		</label>
		
		
		
		<input type="hidden" name="ct" value="<?php echo $colname_Alunos; ?>">
		<input type="hidden" name="data" value="<?php echo $colname_Data; ?>">
		<input type="hidden" name="hora" value="<?php echo $colname_Hora; ?>">
		
		<br>
		
		<label class="ls-label-text">
		<input type="submit" name="submit" value="GERAR COMUNICADOS" class="ls-btn-primary">
      <input type="hidden" name="MM_insert" value="form1">
		</label>
		
		<hr>
		
		
		
		
		  </div>
  </fieldset>
        
        </form>
        
        
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
 
		<script>

	// Forma 1
$("#checkTodos").click(function(){
    $('input:checkbox').not(this).prop('checked', this.checked);
});

	</script>
  </body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($Alunos);
?>
