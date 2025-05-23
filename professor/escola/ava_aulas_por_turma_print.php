
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
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, sec_id, sec_cidade, sec_uf, sec_logo, escola_tema 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

$colname_Turma = "-1";
if (isset($_GET['turma'])) {
  $colname_Turma = $_GET['turma'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turma = sprintf("SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo FROM smc_turma WHERE turma_id = %s", GetSQLValueString($colname_Turma, "int"));
$Turma = mysql_query($query_Turma, $SmecelNovo) or die(mysql_error());
$row_Turma = mysql_fetch_assoc($Turma);
$totalRows_Turma = mysql_num_rows($Turma);

if ($totalRows_Turma  == "") {
	//echo "TURMA EM BRANCO";	
	header("Location: turmasAlunosVinculados.php?nada"); 
 	exit;
	}

$dataInicio = date('Y-m-d');
if (isset($_GET['dataInicio'])) {
  $dataInicio = $_GET['dataInicio'];
}

$dataFinal = date('Y-m-d');
if (isset($_GET['dataFinal'])) {
  $dataFinal = $_GET['dataFinal'];
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AulasTurma = "
SELECT plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, plano_aula_data, 
plano_aula_data_cadastro, plano_aula_texto, plano_aula_conteudo, plano_aula_atividade, plano_aula_atividade_resposta_obrigatoria, 
plano_aula_atividade_resposta_obrigatoria_data_limite, plano_aula_video, plano_aula_publicado, plano_aula_hash,
disciplina_id, disciplina_nome, func_id, func_nome 
FROM smc_plano_aula
INNER JOIN smc_disciplina ON disciplina_id = plano_aula_id_disciplina
INNER JOIN smc_func ON FUNC_ID = plano_aula_id_professor 
WHERE plano_aula_id_turma = '$row_Turma[turma_id]' AND ( plano_aula_data BETWEEN '$dataInicio' AND '$dataFinal')
AND (plano_aula_conteudo IS NOT NULL OR plano_aula_video IS NOT NULL OR plano_aula_atividade IS NOT NULL)  
ORDER BY plano_aula_data ASC";
$AulasTurma = mysql_query($query_AulasTurma, $SmecelNovo) or die(mysql_error());
$row_AulasTurma = mysql_fetch_assoc($AulasTurma);
$totalRows_AulasTurma = mysql_num_rows($AulasTurma);
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
<style>
table {
	width:100%;
	border-collapse: collapse;
	font-size:12px;
}
th, td {
	border:1px solid #ccc;
}
th, td {
	padding:10px;
	height:15px;
	line-height:15px;
}

.leitura img {
	max-width:100%;
	height:auto;
	margin:10px 0;
}
</style>
</head>
  <body onload="self.print();">
<main class="1ls-main ">
  <div class="1container-fluid"> 
    
    <!-- CONTEÚDO -->
    
    <?php $sequencia = 1; do { ?>
      <div class="ls-box1 ls-sm-space" style="page-break-after: always;">
        <table class="ls-sm-space bordasimples" width="100%" style="font-size:12px;">
          <tbody>
            <tr>
              <td width="100"><?php if ($row_EscolaLogada['escola_logo']<>"") { ?>
                <img src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt="" width="100px" />
                <?php } else { ?>
                <img src="../../img/brasao_republica.png" alt="" width="80px" />
                <?php } ?></td>
              <td class="ls-txt-left"><h2 class="ls-txt-center"><?php echo $row_EscolaLogada['escola_nome']; ?></h2>
                <br>
                <strong>DATA:</strong> <?php echo inverteData($row_AulasTurma['plano_aula_data']); ?> | <strong>UNIDADE:</strong>_______<br>
                <strong>PROFESSOR(A):</strong> <?php echo $row_AulasTurma['func_nome']; ?> <br>
                <strong>TURMA:</strong> <?php echo $row_Turma['turma_nome']; ?></td>
              <td width="100">
			  
              	<?php if ($row_EscolaLogada['sec_logo'] <> "") { ?>
				  <img src="../../img/logo/secretaria/<?php echo $row_EscolaLogada['sec_logo']; ?>" alt="" title=""  width="80px" />
				<?php } else { ?>
				  <img src="../../img/brasao_republica.png" width="80px">
				<?php } ?>	
			  
			  </td>
            </tr>
            <tr>
              <th colspan="3" style="background-color:#FFFFF0"> 
			  <p>COMPONENTE: <?php echo $row_AulasTurma['disciplina_nome']; ?></p>
			  <p>CÓDIGO DA AULA: <?php echo $row_AulasTurma['plano_aula_id']; ?></p>
			  <p>SEQUÊNCIA: <?php echo $sequencia; $sequencia++; ?></p>
              </th>
            </tr>
            <tr>
              <td colspan="3"><h2 class="ls-txt-center"><?php echo $row_AulasTurma['plano_aula_texto']; ?></h2>
                <div class="leitura">
                  <p> <?php echo $row_AulasTurma['plano_aula_conteudo']; ?> </p>
                </div></td>
            </tr>
            <?php if ($row_AulasTurma['plano_aula_atividade']<>"") { ?>
            <tr>
              <th colspan="3"  style="background-color:#FFFFF0"> <h2>ATIVIDADE PROPOSTA</h2>
              </th>
            </tr>
            <tr>
              <td colspan="3"><div class="leitura">
                  <p class="flow-text"><?php echo $row_AulasTurma['plano_aula_atividade']; ?></p>
                </div></td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
        <p class="ls-txt-center"> <small>Atividade impressa em <?php echo date('d/m/Y'); ?> às <?php echo date('H:i:s'); ?>. <br>
          SMECEL - Sistema de Gestão Escolar</i></small> </p>
      </div>
      <?php } while ($row_AulasTurma = mysql_fetch_assoc($AulasTurma)); ?>
    
    <!-- CONTEÚDO --> 
  </div>
</main>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>
</body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($Turma);

mysql_free_result($AulasTurma);

mysql_free_result($EscolaLogada);
?>
