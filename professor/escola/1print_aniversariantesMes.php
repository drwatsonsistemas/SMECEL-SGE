<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include('fnc/idade.php'); ?>

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


$mes = date("m");

//$mes = "-1";
if (isset($_GET['mes'])) {
  $mes = $_GET['mes'];
}

if ($mes == '01' ) {
	$mesAnterior = 12;
	} else {
		$mesAnterior = $mes-01;
		}
	
if ($mes == '12' ) {
	$mesSeguinte = 01;
	} else {
		$mesSeguinte = $mes+01;
		}

		

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_aniversariantesMes = "
SELECT 
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo,
aluno_id, aluno_nome, aluno_nascimento, DATE_FORMAT(aluno_nascimento, '%m%d') AS aniversario, DATE_FORMAT(aluno_nascimento, '%d/%m') AS data_aniversario, turma_id, turma_nome, DATE_FORMAT(aluno_nascimento, '%d') as dia_aniversario, DATE_FORMAT(aluno_nascimento, '%m') as mes_aniversario
FROM smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma 
WHERE vinculo_aluno_id_escola = '$row_EscolaLogada[escola_id]' AND Month(aluno_nascimento) = '$mes' ORDER BY aniversario, aluno_nome ASC";
$aniversariantesMes = mysql_query($query_aniversariantesMes, $SmecelNovo) or die(mysql_error());
$row_aniversariantesMes = mysql_fetch_assoc($aniversariantesMes);
$totalRows_aniversariantesMes = mysql_num_rows($aniversariantesMes);
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
<link rel="stylesheet" type="text/css" href="css/locastyle.css"> 
<script src="js/locastyle.js"></script>
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
  <body onload="alert('Atenção: Configure sua impressora para o formato RETRATO');self.print();">




      <div class="container-fluid">
 
        <h1 class="ls-title-intro ls-ico-home">ALUNOS ANIVERSARIANTES DO MÊS</h1>
		<!-- CONTEÚDO -->
				
<h1 class="panel">Aniversariantes do mês: <?php echo $mes; ?>/<?php echo date("Y"); ?></h1>
        <table width="100%" class="ls-table ls-no-hover ls-table-striped ls-bg-header">
          <thead>
          <tr>
            <th class="ls-txt-center" width="20px"></th>
            <th class="ls-txt-center" width="120px">DATA</th>
            <th>NOME</th>
            <th class="ls-txt-center">IDADE</th>
            <th>TURMA</th>
          </tr>
          </thead>
          <?php do { ?>
            <tr>
              <td class="ls-txt-center"><?php if ($row_aniversariantesMes['dia_aniversario'] == date("d") AND $row_aniversariantesMes['mes_aniversario'] == date("m")) { ?> <span class="ls-ico-star ls-color-theme"></span> <?php } ?></td>
              <td class="ls-txt-center"><?php echo $row_aniversariantesMes['data_aniversario']; ?></td>
              <td><?php echo $row_aniversariantesMes['aluno_nome']; ?></td>
              <td class="ls-txt-center"><?php echo idade($row_aniversariantesMes['aluno_nascimento']); ?> anos</td>
              <td><?php echo $row_aniversariantesMes['turma_nome']; ?></td>
            </tr>
            <?php } while ($row_aniversariantesMes = mysql_fetch_assoc($aniversariantesMes)); ?>
        </table>
        
       
		<br><br>
		<hr>
        
    </div>
<!-- CONTEÚDO -->
      </div>


    <!-- We recommended use jQuery 1.10 or up -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="js/locastyle.js"></script>
 
  </body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($aniversariantesMes);
?>
