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
vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_rel_aval,
aluno_id, aluno_nome, aluno_nascimento, aluno_filiacao1, aluno_foto, aluno_hash,
turma_id, turma_nome, turma_matriz_id, turma_turno, turma_etapa 
FROM 
smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma 
WHERE vinculo_aluno_situacao = '1' AND vinculo_aluno_boletim = '1' AND vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND vinculo_aluno_id_escola = '$row_UsuLogado[usu_escola]' $buscaTurma
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
$query_CriteriosAvaliativos = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes, ca_grupo_etario, ca_grupo_conceito FROM smc_criterios_avaliativos WHERE ca_id = '$row_Matriz[matriz_criterio_avaliativo]'";
$CriteriosAvaliativos = mysql_query($query_CriteriosAvaliativos, $SmecelNovo) or die(mysql_error());
$row_CriteriosAvaliativos = mysql_fetch_assoc($CriteriosAvaliativos);
$totalRows_CriteriosAvaliativos = mysql_num_rows($CriteriosAvaliativos);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_GrupoConceitos = "
SELECT conceito_itens_id, conceito_itens_id_conceito, conceito_itens_descricao, conceito_itens_legenda, conceito_itens_peso 
FROM smc_conceito_itens
WHERE conceito_itens_id_conceito = '$row_CriteriosAvaliativos[ca_grupo_conceito]'
ORDER BY conceito_itens_peso DESC
";
$GrupoConceitos = mysql_query($query_GrupoConceitos, $SmecelNovo) or die(mysql_error());
$row_GrupoConceitos = mysql_fetch_assoc($GrupoConceitos);
$totalRows_GrupoConceitos = mysql_num_rows($GrupoConceitos);

$procurar  = array('\n', '\\','\"');
$buscar  = array('<br><br>', '','"');


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
                <link rel="stylesheet" type="text/css" href="css/locastyle.css">            <script src="js/locastyle.js"></script>
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
				</style>
                <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
                <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
                <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
                <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
                <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
                </head>
                <body onload="1alert('Atenção: Configure sua impressora para o formato RETRATO');self.print();">
                <div class="container-fluid">
                
                <br><span class="ls-txt-center"><a href="#" onClick="window.print()" class="ls-txt-center ls-btn-danger">IMPRIMIR</a></span>
                  
<?php do { ?>
                   
   <?php
   
    mysql_select_db($database_SmecelNovo, $SmecelNovo);
    $query_Campos = "SELECT campos_exp_id, campos_exp_nome, campos_exp_mais, campos_exp_orientacoes, campos_exp_direitos FROM smc_campos_exp";
    $Campos = mysql_query($query_Campos, $SmecelNovo) or die(mysql_error());
    $row_Campos = mysql_fetch_assoc($Campos);
    $totalRows_Campos = mysql_num_rows($Campos);

   ?>
   
    <div style="page-break-inside: avoid;"> <br>
      <p>
      
    <div class="ls-box1"> <span class="ls-float-right" style="margin-left:20px;">
      <?php if($row_AlunoBoletim['aluno_foto']=="") { ?>
      <img src="<?php echo URL_BASE.'aluno/fotos/' ?>semfoto.jpg" style="margin:1mm;width:15mm;">
      <?php } else { ?>
      <img src="<?php echo URL_BASE.'aluno/fotos/' ?><?php echo $row_AlunoBoletim['aluno_foto']; ?>" style="margin:1mm;width:15mm;">
      <?php } ?>
      </span> 

      <small> Aluno(a): <strong><?php echo $row_AlunoBoletim['aluno_nome']; ?></strong><br>
      Nascimento: <strong><?php echo inverteData($row_AlunoBoletim['aluno_nascimento']); ?></strong><br>
      Filiação: <strong><?php echo $row_AlunoBoletim['aluno_filiacao1']; ?></strong><br>
      Turma: <strong><?php echo $row_AlunoBoletim['turma_nome']; ?></strong> </small> </div>
    </p>
    
    <p class="ls-ico-text ls-txt-center">RENDIMENTO ESCOLAR <?php echo $row_AlunoBoletim['vinculo_aluno_ano_letivo']; ?></p>
                    
	<?php do { ?>

    <?php
        mysql_select_db($database_SmecelNovo, $SmecelNovo);
        $query_Objetos = "SELECT campos_exp_obj_id, campos_exp_obj_id_campos_exp, campos_exp_obj_nome, campos_exp_obj_faixa_et_cod, campos_exp_obj_faixa_et_nome, campos_exp_obj_campos_exp, campos_exp_obj_abordagem, campos_exp_obj_sugestoes FROM smc_campos_exp_objetivos WHERE campos_exp_obj_id_campos_exp = '$row_Campos[campos_exp_id]' AND campos_exp_obj_faixa_et_cod = '$row_CriteriosAvaliativos[ca_grupo_etario]'";
        $Objetos = mysql_query($query_Objetos, $SmecelNovo) or die(mysql_error());
        $row_Objetos = mysql_fetch_assoc($Objetos);
        $totalRows_Objetos = mysql_num_rows($Objetos);
      
	  
        /*do { 
            $rendimento[] = $row_Rendimento['conc_acomp_id']."-".$row_Rendimento['conc_matricula_id']."-".$row_Rendimento['conc_periodo']."-".$row_Rendimento['conc_avaliacao'];
        } while ($row_Rendimento = mysql_fetch_assoc($Rendimento));*/
        
    ?>
                            
      <br><h2><?php echo utf8_encode($row_Campos['campos_exp_nome']); ?></h2>
      <table class="ls-sm-space bordasimples" width="100%">
      <?php do { ?>
      
      <?php 
        mysql_select_db($database_SmecelNovo, $SmecelNovo);
        $query_Acompanhamento = "
		SELECT acomp_id, acomp_id_matriz, acomp_id_crit, acomp_id_obj_aprend, acomp_descricao, acomp_hash 
		FROM smc_acomp_proc_aprend
		WHERE acomp_id_matriz = '$row_Matriz[matriz_id]'
		AND acomp_id_crit = '$row_CriteriosAvaliativos[ca_id]'
		AND acomp_id_obj_aprend = '$row_Objetos[campos_exp_obj_id]'
		";
        $Acompanhamento = mysql_query($query_Acompanhamento, $SmecelNovo) or die(mysql_error());
        $row_Acompanhamento = mysql_fetch_assoc($Acompanhamento);
        $totalRows_Acompanhamento = mysql_num_rows($Acompanhamento);
      ?>
      
      <tr>
      <td width="20%"><?php echo utf8_encode($row_Objetos['campos_exp_obj_campos_exp']); ?></td>
      
      <td>
      
            <?php 
			
			$total = 0;
			$pontos = 0;
			
			?>
            
            <?php do { ?>
        
        	
            
            
            -<?php echo $row_Acompanhamento['acomp_descricao']; ?><br>
            
            <?php for ($p = 1; $p <= $row_CriteriosAvaliativos['ca_qtd_periodos']; $p++) { ?>
            
            
			  <?php 
              
                  mysql_select_db($database_SmecelNovo, $SmecelNovo);
                  $query_ConceitoAluno = "
                  SELECT conc_id, conc_acomp_id, conc_matricula_id, conc_periodo, conc_avaliacao FROM smc_conceito_aluno
                  WHERE conc_acomp_id = '$row_Acompanhamento[acomp_id]' AND conc_matricula_id = '$row_AlunoBoletim[vinculo_aluno_id]' AND conc_periodo = '$p'
                  ";
                  $ConceitoAluno = mysql_query($query_ConceitoAluno, $SmecelNovo) or die(mysql_error());
                  $row_ConceitoAluno = mysql_fetch_assoc($ConceitoAluno);
                  $totalRows_ConceitoAluno = mysql_num_rows($ConceitoAluno);
				  
				  $pontos = $pontos + $row_ConceitoAluno['conc_avaliacao'];
				  $total = $total + $row_GrupoConceitos['conceito_itens_peso'];
      
              ?>
            
            
                
			<?php } ?>
            
           
            
        <?php } while ($row_Acompanhamento = mysql_fetch_assoc($Acompanhamento)); ?>  
      
      </td>
      
     
      
      <td width="100px" class="ls-txt-center">
      <h2><?php if ( $pontos > 0) { echo number_format((($pontos/$total)*100),0)."%"; } else { echo "-"; } ?></h2><h3><?php echo $pontos."/".$total; ?></h3>
      </td>
      
      
      </tr>
      
      <?php } while ($row_Objetos = mysql_fetch_assoc($Objetos)); ?>   
      </table>
    <?php } while ($row_Campos = mysql_fetch_assoc($Campos)); ?>
    
    
    
    <?php 
	
		mysql_select_db($database_SmecelNovo, $SmecelNovo);
		$query_parecer = "
		SELECT p_ind_id, p_ind_id_prof, p_ind_mat_aluno, p_ind_texto, p_ind_data_cadastro, p_ind_periodo, func_id, func_nome 
		FROM smc_parecer_individual_professor 
		INNER JOIN smc_func ON func_id = p_ind_id_prof
		WHERE p_ind_mat_aluno = '$row_AlunoBoletim[vinculo_aluno_id]' ORDER BY p_ind_periodo ASC";
		$parecer = mysql_query($query_parecer, $SmecelNovo) or die(mysql_error());
		$row_parecer = mysql_fetch_assoc($parecer);
		$totalRows_parecer = mysql_num_rows($parecer);
	
	?>
                    
                      
                    <br>

					<h3 class="ls-txt-center">OBSERVAÇÕES DO ALUNO</h3><br>
                    
                    <div class="ls-box">
                    
                    <h5 class="ls-title-3">Parecer</h5>
                    <?php echo str_replace($procurar,$buscar,$row_AlunoBoletim['vinculo_aluno_rel_aval']); ?>
                    
                    <hr>
                    
                      <table class="ls-table">
					  <?php 
					  if ($totalRows_parecer>0) {
					  do { ?>
                          <tr>
                            <td><strong><?php echo $row_parecer['p_ind_periodo']; ?>º período | <?php echo $row_parecer['func_nome']; ?></strong><br><br><?php echo str_replace($procurar,$buscar,$row_parecer['p_ind_texto']); ?></td>
                          </tr>
				  	  <?php } while ($row_parecer = mysql_fetch_assoc($parecer)); } ?>
                      </table>
                      
                      </div>
                    
                    
                    
                                        
					
	
                      
                      
                      
                  </div>
                  

                  
                  <?php } while ($row_AlunoBoletim = mysql_fetch_assoc($AlunoBoletim)); ?>
                  
                  
                  
                  
                  
                  
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


mysql_free_result($Acompanhamento);

mysql_free_result($Campos);

mysql_free_result($Objetos);

mysql_free_result($GrupoConceitos);

mysql_free_result($ConceitoAluno);

mysql_free_result($Matriz);

?>