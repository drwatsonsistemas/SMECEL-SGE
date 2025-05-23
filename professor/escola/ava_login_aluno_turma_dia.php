<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include('../../sistema/funcoes/inverteData.php'); ?>

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
$query_Turmas = "SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo FROM smc_turma WHERE turma_id_escola = $row_EscolaLogada[escola_id] AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' ORDER BY turma_turno, turma_etapa, turma_nome ASC";
$Turmas = mysql_query($query_Turmas, $SmecelNovo) or die(mysql_error());
$row_Turmas = mysql_fetch_assoc($Turmas);
$totalRows_Turmas = mysql_num_rows($Turmas);

$dataHoje = date("Y-m-d");
$dataNome = date("d/m/Y");

if (isset($_POST['dataInicio'])) {
  $dataHoje = $_POST['dataInicio'];
  $dataNome = $_POST['dataInicio'];

}

/*
$data = '30/02/2016';
$d = DateTime::createFromFormat('d/m/Y', $data);
if($d && $d->format('d/m/Y') == $data){
    echo 'data valida';
}else{
    echo 'data invalida';
}
*/
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
 
        <h1 class="ls-title-intro ls-ico-home">REGISTRO DE LOGIN POR DATA</h1>
		<!-- CONTEÚDO -->
		
        <h2>DATA DO REGISTRO: <?php echo $dataNome; ?></h2>
        
				<p>
        
<div class="ls-box-filter">
  <form action="ava_login_aluno_turma_dia.php" method="post" class="ls-form ls-form-inline row" data-ls-module="form">
    
    <label class="ls-label col-md-3 col-sm-4">
      <b class="ls-label-text">DATA</b>
      <input type="date" name="dataInicio" class="" autocomplete="off" required>
    </label>

    <div class="ls-actions-btn">
      <input type="submit" value="FILTRAR" class="ls-btn">
    </div>
  </form>
</div>

        </p>
        
        <?php $contagem = 0; $contagemTotal = 0; ?>

		<?php do { ?>
        
        <?php
			mysql_select_db($database_SmecelNovo, $SmecelNovo);
			$query_Aluno = "
			SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
			vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data,
			vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, 
			vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, 
			vinculo_aluno_vacina_atualizada, vinculo_aluno_vacina_data_retorno, aluno_id, aluno_nome 
			FROM smc_vinculo_aluno
			INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
			WHERE vinculo_aluno_id_turma = '$row_Turmas[turma_id]' AND vinculo_aluno_situacao = '1'
			ORDER BY aluno_nome ASC
			";
			$Aluno = mysql_query($query_Aluno, $SmecelNovo) or die(mysql_error());
			$row_Aluno = mysql_fetch_assoc($Aluno);
			$totalRows_Aluno = mysql_num_rows($Aluno);
		?>
		
            <div class="ls-box">
              <h5 class="ls-title-3"><?php echo $row_Turmas['turma_nome']; ?></h5>
              <p>
              
         <table border="0" class="ls-table">
         <thead>
          <tr>
            <th width="50" class="ls-txt-center"></th>
            <th width="120" class="ls-txt-center">MATRÍCULA</th>
            <th width="100" class="ls-txt-center">PRESENÇA</th>
            <th class="ls-txt-center">ALUNO</th>
            <th width="50" class="ls-txt-center"></th>
          </tr>
          </thead>
          <tbody>
          <?php $num  = 1; $cont = 0; do { ?>
          
          <?php
		  
		  //WHERE login_aluno_id_aluno = '$row_Aluno[vinculo_aluno_id_aluno]' AND DATE_FORMAT(login_aluno_data_hora, '%Y-%m-%d') = '$dataHoje'";
			mysql_select_db($database_SmecelNovo, $SmecelNovo);
			$query_Login = "
			SELECT login_aluno_id, login_aluno_id_aluno, login_aluno_data_hora, login_aluno_ip  
			FROM smc_login_aluno 
			WHERE login_aluno_id_aluno = '$row_Aluno[vinculo_aluno_id_aluno]' AND DATE_FORMAT(login_aluno_data_hora, '%Y-%m-%d') = '$dataHoje'";
			$Login = mysql_query($query_Login, $SmecelNovo) or die(mysql_error());
			$row_Login = mysql_fetch_assoc($Login);
			$totalRows_Login = mysql_num_rows($Login);
		  ?>
          
          
            <tr>
              <td class="ls-txt-center"><?php echo $num; $num++; ?></td>
              <td class="ls-txt-center"><?php echo str_pad($row_Aluno['vinculo_aluno_id'], 5, '0', STR_PAD_LEFT); ?></td>
              <td class="ls-txt-center"><?php if ($totalRows_Login > 0) { $contagem++; ?><span class="ls-ico-checkmark-circle ls-color-success"></span><?php } ?></td>
              <td class="ls-txt-center"><?php echo $row_Aluno['aluno_nome']; ?></td>
              <td class="ls-txt-center"><?php if ($totalRows_Login > 0) { $cont++; ?><?php echo $totalRows_Login; $contagemTotal = $contagemTotal + $totalRows_Login; ?><?php } ?></td>
            </tr>
            <?php } while ($row_Aluno = mysql_fetch_assoc($Aluno)); ?>
        	</tbody>
        </table>
              
              </p>
              
              <h5>Total de logins da turma: <?php echo $cont; ?></h5>
              
            </div>
        
		<?php } while ($row_Turmas = mysql_fetch_assoc($Turmas)); ?>
        
        <p>Logins únicos: <?php echo $contagem; ?></p>
        <p>Total de logins: <?php echo $contagemTotal; ?></p>
        
        <br>
        
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
	locastyle.datepicker.newDatepicker('');
	</script>
 
  </body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($Turmas);

mysql_free_result($Login);

mysql_free_result($Aluno);

mysql_free_result($EscolaLogada);
?>
