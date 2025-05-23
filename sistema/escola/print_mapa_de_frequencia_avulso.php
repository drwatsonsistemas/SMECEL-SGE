<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include('fnc/inverteData.php'); ?>
<?php include "fnc/anti_injection.php"; ?>
<?php //include('fnc/notas.php'); ?>
<?php include('../funcoes/url_base.php'); ?>
<?php include "fnc/calculos.php"; ?>
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

$codTurma = "";
$buscaTurma = "";
if (isset($_GET['ct'])) {
	
	if ($_GET['ct'] == "") {
	//echo "TURMA EM BRANCO";	
	header("Location: turmasAlunosVinculados.php?nada"); 
 	exit;
	}
	
  $codTurma = anti_injection($_GET['ct']);
  $codTurma = (int)$codTurma;
  $buscaTurma = "AND turma_id = $codTurma ";
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
$query_AlunoBoletim = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia,
aluno_id, aluno_nome, aluno_nascimento, aluno_filiacao1, aluno_foto, aluno_hash,
turma_id, turma_nome, turma_matriz_id, turma_turno, turma_etapa 
FROM 
smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma 
WHERE vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND vinculo_aluno_id_escola = '$row_UsuLogado[usu_escola]' $buscaTurma
ORDER BY turma_turno ASC, turma_etapa ASC, turma_nome ASC, aluno_nome ASC";
$AlunoBoletim = mysql_query($query_AlunoBoletim, $SmecelNovo) or die(mysql_error());
$row_AlunoBoletim = mysql_fetch_assoc($AlunoBoletim);
$totalRows_AlunoBoletim = mysql_num_rows($AlunoBoletim);

if ($totalRows_AlunoBoletim == "") {
	//echo "TURMA EM BRANCO";	
	//header("Location: turmasAlunosVinculados.php?nada"); 
 	
	echo "<h3><center>Sem dados.<br><a href=\"javascript:window.close()\">Fechar</a></center></h3>";
	echo "";
	
	exit;
	}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matriz = "SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash, matriz_dias_letivos, matriz_semanas_letivas, matriz_dias_semana, matriz_minutos_aula, matriz_aula_dia, matriz_criterio_avaliativo FROM smc_matriz WHERE matriz_id = '$row_AlunoBoletim[turma_matriz_id]'";
$Matriz = mysql_query($query_Matriz, $SmecelNovo) or die(mysql_error());
$row_Matriz = mysql_fetch_assoc($Matriz);
$totalRows_Matriz = mysql_num_rows($Matriz);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_CriteriosAvaliativos = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes FROM smc_criterios_avaliativos WHERE ca_id = '$row_Matriz[matriz_criterio_avaliativo]'";
$CriteriosAvaliativos = mysql_query($query_CriteriosAvaliativos, $SmecelNovo) or die(mysql_error());
$row_CriteriosAvaliativos = mysql_fetch_assoc($CriteriosAvaliativos);
$totalRows_CriteriosAvaliativos = mysql_num_rows($CriteriosAvaliativos);

function diffMonth($from, $to) {

        $fromYear = date("Y", strtotime($from));
        $fromMonth = date("m", strtotime($from));
        $toYear = date("Y", strtotime($to));
        $toMonth = date("m", strtotime($to));
        if ($fromYear == $toYear) {
            return ($toMonth-$fromMonth)+1;
        } else {
            return (12-$fromMonth)+1+$toMonth;
        }

    }


function nomeMes($numero) {
	
switch ($numero) {
	case 1:
		$nomeMes = "JAN";
		break;
	case 2:
		$nomeMes = "FEV";
		break;
	case 3:
		$nomeMes = "MAR";
		break;
	case 4:
		$nomeMes = "ABR";
		break;
	case 5:
		$nomeMes = "MAI";
		break;
	case 6:
		$nomeMes = "JUN";
		break;
	case 7:
		$nomeMes = "JUL";
		break;
	case 8:
		$nomeMes = "AGO";
		break;
	case 9:
		$nomeMes = "SET";
		break;
	case 10:
		$nomeMes = "OUT";
		break;
	case 11:
		$nomeMes = "NOV";
		break;
	case 12:
		$nomeMes = "DEZ";
		break;
}

return $nomeMes;	
	
}





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
<link rel="stylesheet" type="text/css" href="css/locastyle.css"><script src="js/locastyle.js"></script>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
<style>
table.bordasimples {
	border-collapse: collapse;
	font-size:7px;
}
table.bordasimples tr td {
	border:1px solid #808080;
	padding:2px;
	font-size:12px;
}
table.bordasimples tr th {
	border:1px solid #808080;
	padding:2px;
	font-size:9px;
}
.aluno {
	background-color: #ddd;
	border-radius: 0%;
	height: 70px;
	object-fit: cover;
	width: 70px;
}
</style>
</head>
<body onload="alert('Atenção: Configure sua impressora para o formato HORIZONTAL');self.print();">
                
                <div class="container-fluid1">
                 					
                    <div style="page-break-inside: avoid;">
                
                
					<?php
										
					$totalMeses = diffMonth($row_AnoLetivo['ano_letivo_inicio'], $row_AnoLetivo['ano_letivo_fim']);

					//$meses = 12;
					
					$ano = $row_AnoLetivo['ano_letivo_ano'];
					
					$mesInicio = date("m", strtotime($row_AnoLetivo['ano_letivo_inicio']));
					$anoInicio = date("y", strtotime($row_AnoLetivo['ano_letivo_inicio']));
					
					$mesFim = date("m", strtotime($row_AnoLetivo['ano_letivo_fim']));
					$anoFim = date("y", strtotime($row_AnoLetivo['ano_letivo_fim']));
										
					//date("m", strtotime($row_AnoLetivo['ano_letivo_inicio']));
				  
				  ?>
                    
                    
                    
                         <div class="ls-box">
                      
                    <span class="ls-float-right" style="margin-left:20px;">
                      
                      <img src="<?php echo URL_BASE.'aluno/fotos/' ?>semfoto.jpg" width="100%" class="aluno">
                      
                    </span> 
                      
                     <strong>FREQUENCIA ESCOLAR <?php echo $row_AlunoBoletim['vinculo_aluno_ano_letivo']; ?></strong><br><br>
                      Aluno(a): <strong>________________________________________________</strong>&nbsp; 
                      Nascimento: <strong>___/___/______</strong>&nbsp; 
                      Filiação: <strong>________________________________________________</strong><br><br>
                      Escola: <strong><?php echo $row_EscolaLogada['escola_nome']; ?></strong>&nbsp;
                      Turma: <strong><?php echo $row_AlunoBoletim['turma_nome']; ?></strong>
                      
                      </div>                 
                    
                    
                    
                    
                    
                    
                    <table width="100%" class="ls-sm-space bordasimples" cellpadding="0" cellspacing="0" border="0">
                    
                    <tr class="ls-txt-center">
                    
                    <!-- LINHA MESES -->
					<td>Meses</td>
                    <?php for ($mesCont = 1; $mesCont <= $totalMeses; $mesCont++) { ?>
                    <td width="150"><?php echo nomeMes($mesInicio); ?>/<?php echo $anoInicio; ?></td>
                    <?php 
					  if ($mesInicio == 12) {
						  $mesInicio = 1;
						  $anoInicio++;
					  } else {
						  $mesInicio++;
					  } 
	  				?>
                    <?php } ?>
                    <?php $mesInicio = date("m", strtotime($row_AnoLetivo['ano_letivo_inicio'])); ?>
                    </tr>

                    <!-- LINHA AULAS -->
                    <tr class="ls-txt-center">
                    <td>Aulas</td>
					<?php for ($mesCont = 1; $mesCont <= $totalMeses; $mesCont++) { ?>
                    <td width="">
                        <div style="width:100%; padding:0; margin:0">
                        <?php $perc = 100/$row_Matriz['matriz_aula_dia']; ?>
						<?php for ($aulaCont = 1; $aulaCont <= $row_Matriz['matriz_aula_dia']; $aulaCont++) { ?>
							<div class="" style="width:<?php echo $perc; ?>%; float:left; border-left:#999 solid 1px; border-right:#999 solid 1px; padding:0; margin:0; background-color:#CCCCCC;"><?php echo $aulaCont; ?></div>
                        <?php } ?>
                        </div>
                    </td>
                    <?php 
					  if ($mesInicio == 12) {
						  $mesInicio = 1;
						  $anoInicio++;
					  } else {
						  $mesInicio++;
					  } 
	  				?>
                    <?php } ?>
                    <?php $mesInicio = date("m", strtotime($row_AnoLetivo['ano_letivo_inicio'])); ?>
                    
                    </tr>
                    

					<!-- DIAS -->
                    <?php $anoInicio = date("Y", strtotime($row_AnoLetivo['ano_letivo_inicio'])); ?>
                    <?php for ($diaCont = 1; $diaCont <= 31; $diaCont++) { ?>
                    
                    <tr class="ls-txt-center">
					<td><?php echo $diaCont; ?></td>
					<?php for ($mesCont = 1; $mesCont <= $totalMeses; $mesCont++) { ?>
                    <td width="">
                    
                    
                        
                    
                    	<?php for ($aulaCont = 1; $aulaCont <= $row_Matriz['matriz_aula_dia']; $aulaCont++) { ?>
                                            
                        
                        	<div class="" style="width:<?php echo $perc; ?>%; float:left; border-left:#999 dotted 1px; border-right:#999 dotted 1px;">
                            
                            &nbsp;
                            
                            </div>
                            
                        <?php } ?>
                    </td>
                    <?php 
					  if ($mesInicio == 12) {
						  $mesInicio = 1;
						  $anoInicio++;
					  } else {
						  $mesInicio++;
					  } 
	  				?>
                    <?php } ?>
                    <?php $mesInicio = date("m", strtotime($row_AnoLetivo['ano_letivo_inicio'])); ?>
                    <?php $anoInicio = date("Y", strtotime($row_AnoLetivo['ano_letivo_inicio'])); ?>
                    </tr>
                    <?php } ?>                    	
                    </table>
                    
                 
                    
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

mysql_free_result($AlunoBoletim);

mysql_free_result($CriteriosAvaliativos);

mysql_free_result($Matriz);
?>