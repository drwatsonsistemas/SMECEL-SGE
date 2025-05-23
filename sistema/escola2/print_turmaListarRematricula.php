<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php 

?>
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
$anoLetivo = $row_AnoLetivo['ano_letivo_ano']+1;

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
$query_TurmasListar = "SELECT turma_id, turma_id_escola, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo, etapa_id, etapa_nome FROM smc_turma INNER JOIN smc_etapa ON etapa_id = turma_etapa WHERE turma_id_escola = $row_EscolaLogada[escola_id] AND turma_ano_letivo = '$anoLetivo' ORDER BY turma_turno, turma_etapa, turma_nome ASC";
$TurmasListar = mysql_query($query_TurmasListar, $SmecelNovo) or die(mysql_error());
$row_TurmasListar = mysql_fetch_assoc($TurmasListar);
$totalRows_TurmasListar = mysql_num_rows($TurmasListar);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ContaAlunos = "SELECT turma_id, turma_id_escola, sum(turma_total_alunos) as totalAlunos, turma_ano_letivo FROM smc_turma WHERE turma_id_escola = $row_EscolaLogada[escola_id] AND turma_ano_letivo = '$anoLetivo'";
$ContaAlunos = mysql_query($query_ContaAlunos, $SmecelNovo) or die(mysql_error());
$row_ContaAlunos = mysql_fetch_assoc($ContaAlunos);
$totalRows_ContaAlunos = mysql_num_rows($ContaAlunos);
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
  
  	<style>
	
	table.bordasimples {border-collapse: collapse; font-size:10px; }
	table.bordasimples tr td {border:1px dotted #000000; padding:2px; font-size:10px;}
	table.bordasimples tr th {border:1px dotted #000000; padding:2px; font-size:10px;}

	</style>
  
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
  <body onload="self.print();">

 

      <div class="container-fluid">
 
					
		<div class="ls-box">
		<span class="ls-float-left" style="margin-right:20px;"><?php if ($row_EscolaLogada['escola_logo']<>"") { ?><img src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt="" width="100px" /><?php } else { ?><img src="../../img/brasao_republica.png" alt="" width="80px" /><?php } ?></span>
		<?php echo $row_EscolaLogada['escola_nome']; ?><br>
		<small>
		<?php echo $row_EscolaLogada['escola_endereco']; ?>, 
		<?php echo $row_EscolaLogada['escola_num']; ?> - 
		<?php echo $row_EscolaLogada['escola_bairro']; ?> - 
		<?php echo $row_EscolaLogada['escola_cep']; ?><br>
		CNPJ:<?php echo $row_EscolaLogada['escola_cnpj']; ?> INEP:<?php echo $row_EscolaLogada['escola_inep']; ?><br>
		<?php echo $row_EscolaLogada['escola_telefone1']; ?> <?php echo $row_EscolaLogada['escola_telefone2']; ?> <?php echo $row_EscolaLogada['escola_email']; ?>
		</small>
		</div>

			<div class="ls-box ls-txt-center" style="text-transform: uppercase;">
			RELAÇÃO DE TURMAS DA ESCOLA - ANO LETIVO <?php echo $anoLetivo; ?>
			</div>
        
			<?php if ($totalRows_TurmasListar > 0) { // Show if recordset not empty ?>
            
			<table class="ls-sm-space bordasimples" width="100%" style="font-size:10px;">
              <thead>
                <tr>
                  <th width="40px" class="ls-txt-center">Nº</th>
                  <th class="ls-txt-center">TURMA</th>
                  <th class="ls-txt-center">TURNO</th>
                  <th class="ls-txt-center">ALUNOS MATRICULADOS</th>
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
                    <td class="ls-txt-center"><?php echo $row_TurmasListar['turma_nome']; ?></td>
                    <td class="ls-txt-center">
                      <?php switch ($row_TurmasListar['turma_turno']) {
                            case 0:
                              echo "INTEGRAL";
                              break;
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
					$alunosTurma = alunosConta($row_TurmasListar['turma_id'], $anoLetivo);					
					echo $alunosTurma;
					$totalAlunos = $totalAlunos + $alunosTurma;
					?></td>
				  
				  </tr>
                  <?php } while ($row_TurmasListar = mysql_fetch_assoc($TurmasListar)); ?>
              </tbody>
            </table>
			<p>
			<div class="ls-box ls-box-gray">
			<small>TURMAS CADASTRADAS: <strong><?php echo $totalRows_TurmasListar; ?></strong></small><br>
			<small>ALUNOS MATRICULADOS: <strong><?php echo $totalAlunos; ?></strong></small>
			</div>
			</p>

			
            
            <?php } else { ?>
            <hr>
			<div class="ls-alert-info"><strong>Atenção:</strong> Nenhuma turma cadastrada.</div>
            <?php } // Show if recordset not empty ?>
      </div>


    <!-- We recommended use jQuery 1.10 or up -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="js/locastyle.js"></script>
 
	
	<script language="Javascript">
	function confirmaExclusao(c,turma) {
     var resposta = confirm("Deseja realmente remover a turma "+turma+"? Se escolher SIM, os vínculos desta turma também serão excluídos.");
     	if (resposta == true) {
     	     window.location.href = "turmaExcluir.php?c="+c;
    	 }
	}
	</script>
	
  </body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($TurmasListar);

mysql_free_result($ContaAlunos);
?>
