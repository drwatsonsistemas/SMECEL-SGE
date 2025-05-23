<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include "fnc/alunosConta.php"; ?>


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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turmas = "SELECT turma_id, turma_id_escola, turma_nome, turma_etapa, turma_turno, turma_total_alunos, etapa_id, etapa_nome FROM smc_turma INNER JOIN smc_etapa ON etapa_id = turma_etapa WHERE turma_id_escola = $row_EscolaLogada[escola_id] AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' ORDER BY turma_turno, turma_etapa ASC";
$Turmas = mysql_query($query_Turmas, $SmecelNovo) or die(mysql_error());
$row_Turmas = mysql_fetch_assoc($Turmas);
$totalRows_Turmas = mysql_num_rows($Turmas);
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
	  
	  
 
        <h1 class="ls-title-intro ls-ico-home">Turmas cadastradas</h1>
        <?php if ($totalRows_Turmas > 0) { // Show if recordset not empty ?>
		
	  <a class="ls-btn-primary ls-ico-paint-format" href="print_turmaListar.php" target="_blank"> Imprimir</a>
		
  <table class="ls-table">
    <thead>
      <tr>
        <th class="ls-txt-center" width="50px">Nº</th>
        <th>TURMA</th>
        <th class="ls-txt-center" width="400px">ETAPA</th>
        <th class="ls-txt-center">TURNO</th>
        <th class="ls-txt-center">ALUNOS</th>
        </tr>
    </thead>
    <tbody>
      <?php 
		$contagem = 1;
		$totalAlunos = 0;
	  
	  do { ?>
        <tr>
          <td class="ls-txt-center"><?php 
		  echo $contagem; 
		  $contagem++;
		  ?></td>
          <td><?php echo $row_Turmas['turma_nome']; ?></td>
          <td class="ls-txt-center"><?php echo $row_Turmas['etapa_nome']; ?></td>
          <td class="ls-txt-center">
            <?php switch ($row_Turmas['turma_turno']) {
                      case 1:
                        echo "MATUTINO";
                        break;
                      case 2:
                        echo "VESPERTINO";
                        break;
                      case 3:
                        echo "NOTURNO";
                        break;			  
                  }  ?>
          </td>
          <td class="ls-txt-center">
		  <?php  
		  
					$alunosTurma = alunosConta($row_Turmas['turma_id'], $row_AnoLetivo['ano_letivo_ano']);					
					echo $alunosTurma;
					$totalAlunos = $totalAlunos + $alunosTurma;
		  
		  ?></td>
        </tr>
        <?php } while ($row_Turmas = mysql_fetch_assoc($Turmas)); ?>
    </tbody>
  </table>
  
  			<p>
			<div class="ls-box ls-box-gray">
			<small>TURMAS CADASTRADAS: <strong><?php echo $totalRows_Turmas; ?></strong></small><br>
			<small>ALUNOS MATRICULADOS: <strong><?php echo $totalAlunos; ?></strong></small>
			</div>
			</p>
  
  <?php } else { ?>
  <div class="ls-alert-info"><strong>Atenção:</strong> Nenhuma turma cadastrada.</div>
  <?php } // Show if recordset not empty ?>
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

mysql_free_result($Turmas);
?>
