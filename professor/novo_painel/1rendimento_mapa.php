<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
<?php include('../../sistema/funcoes/inverteData.php'); ?>
<?php include "../../sistema/escola/fnc/calculos.php"; ?>
				<?php
$colname_Disciplina = "-1";
if (isset($_GET['componente'])) {
  $colname_Disciplina = $_GET['componente'];
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Disciplina = sprintf("SELECT disciplina_id, disciplina_area_conhecimento_id, disciplina_codigo_inep, disciplina_ordem, disciplina_nome, disciplina_nome_abrev FROM smc_disciplina WHERE disciplina_id = %s", GetSQLValueString($colname_Disciplina, "int"));
$Disciplina = mysql_query($query_Disciplina, $SmecelNovo) or die(mysql_error());
$row_Disciplina = mysql_fetch_assoc($Disciplina);
$totalRows_Disciplina = mysql_num_rows($Disciplina);

$colname_Turma = "-1";
if (isset($_GET['turma'])) {
  $colname_Turma = $_GET['turma'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turma = sprintf("SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo FROM smc_turma WHERE turma_id = %s", GetSQLValueString($colname_Turma, "int"));
$Turma = mysql_query($query_Turma, $SmecelNovo) or die(mysql_error());
$row_Turma = mysql_fetch_assoc($Turma);
$totalRows_Turma = mysql_num_rows($Turma);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Alunos = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_situacao, vinculo_aluno_ano_letivo, vinculo_aluno_hash,
aluno_id, aluno_nome, aluno_foto, disciplina_id, disciplina_nome, turma_id, turma_nome, turma_id_escola, turma_ano_letivo,
CASE vinculo_aluno_situacao
WHEN 1 THEN 'MATRICULADO'
WHEN 2 THEN 'TRANSFERIDO(A)'
WHEN 3 THEN 'DESISTENTE'
WHEN 4 THEN 'FALECIDO(A)'
WHEN 5 THEN 'OUTROS'
END AS vinculo_aluno_situacao_nome  
FROM smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
INNER JOIN smc_disciplina ON disciplina_id = $colname_Disciplina
INNER JOIN smc_turma ON turma_id = '$colname_Turma'
WHERE vinculo_aluno_id_turma = '$colname_Turma' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'
ORDER BY aluno_nome";
$Alunos = mysql_query($query_Alunos, $SmecelNovo) or die(mysql_error());
$row_Alunos = mysql_fetch_assoc($Alunos);
$totalRows_Alunos = mysql_num_rows($Alunos);

if ($totalRows_Alunos == 0) {
	//header("Location:index.php?erro");
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Escola = sprintf("SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_ue FROM smc_escola WHERE escola_id = '$row_Alunos[turma_id_escola]'");
$Escola = mysql_query($query_Escola, $SmecelNovo) or die(mysql_error());
$row_Escola = mysql_fetch_assoc($Escola);
$totalRows_Escola = mysql_num_rows($Escola);

if($totalRows_Escola=="") {
	//header("Location:../index.php?loginErr");
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matriz = "SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash, matriz_dias_letivos, matriz_semanas_letivas, matriz_dias_semana, matriz_minutos_aula, matriz_aula_dia, matriz_criterio_avaliativo FROM smc_matriz WHERE matriz_id = '$row_Turma[turma_matriz_id]'";
$Matriz = mysql_query($query_Matriz, $SmecelNovo) or die(mysql_error());
$row_Matriz = mysql_fetch_assoc($Matriz);
$totalRows_Matriz = mysql_num_rows($Matriz);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Criterios = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes, ca_digitos FROM smc_criterios_avaliativos WHERE ca_id = '$row_Matriz[matriz_criterio_avaliativo]'";
$Criterios = mysql_query($query_Criterios, $SmecelNovo) or die(mysql_error());
$row_Criterios = mysql_fetch_assoc($Criterios);
$totalRows_Criterios = mysql_num_rows($Criterios);
?>
				<!DOCTYPE html>
				<html class="<?php echo TEMA; ?>" lang="pt-br">
                <head>
                <!-- Global site tag (gtag.js) - Google Analytics -->
                <script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
                <script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-117872281-1');
</script>
                <title>PROFESSOR |<?php echo $row_ProfLogado['func_nome']; ?>| SMECEL - Sistema de Gestão Escolar</title>
                <meta charset="utf-8">
                <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
                <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
                <meta name="mobile-web-app-capable" content="yes">
                <meta name="apple-mobile-web-app-capable" content="yes">
                <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
                <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
                <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
                <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
                <link rel="stylesheet" type="text/css" href="css/locastyle.css">                <link rel="stylesheet" href="css/sweetalert2.min.css">
                <style>
table {
	width:100%;
	border-collapse: collapse;
}
table a {
	display:block;
	padding:4px;
}
th, td {
	border:1px solid #ccc;
}
tr, td {
	padding:0;
	height:10px;
	line-height:10px;
}

.aluno {
	background-color: #ddd;
	border-radius: 0%;
	height: 40px;
	object-fit: cover;
	width: 40px;
}
</style>
                </head>
                <body>
                <?php include_once "inc/navebar.php"; ?>
                <?php include_once "inc/sidebar.php"; ?>
                
                <main class="ls-main">
                  <div class="container-fluid">
                    <h1 class="ls-title-intro ls-ico-home">Ano letivo <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h1>
                    
                    <p>
                    <a href="index.php" class="ls-btn ls-ico-chevron-left">Voltar</a>
                    <a href="rendimento_mapa_print.php?componente=<?php echo $row_Alunos['disciplina_id']; ?>&turma=<?php echo $row_Alunos['turma_id']; ?>" target="_blank" class="ls-btn">IMPRIMIR</a>
                    </p>
                    
                    <div class="ls-box">
                    
                    <p>COMPONENTE: <?php echo $row_Disciplina['disciplina_nome']; ?></p>
                    <p>TURMA: <?php echo $row_Turma['turma_nome']; ?></p>
                    <p><small>-Clique sobre a célula para lançar as notas dos alunos.<br>-Para lançar individualmente, clique sobre o nome do aluno.</small></p>
                    </div>
                    <hr>
                    <?php if ($totalRows_Alunos==0) { ?>
                    NENHUM ALUNO COM BOLETIM GERADO. <a href="index.php">VOLTAR</a>
                    <?php } else { ?>
                    <br>
                    <h5><?php echo $row_Alunos['disciplina_nome']; ?> - <?php echo $row_Alunos['turma_nome']; ?></h5>
                    
                    
                    
                    <table class="ls-table ls-sm-space ls-table-layout-auto ls-full-width ls-height-auto" width="100%">
                      <thead>
                        <tr class="">
                          <th colspan="3" class="ls-display-none-xs"></th>
                          <?php $tmu = 0; ?>
                          <?php for ($p = 1; $p <= $row_Criterios['ca_qtd_periodos']; $p++) { ?>
                          <?php
						
						mysql_select_db($database_SmecelNovo, $SmecelNovo);
						$query_PeriodosBloqueio1 = "SELECT per_unid_id, per_unid_id_ano, per_unid_id_sec, per_unid_periodo, per_unid_data_inicio, per_unid_data_fim, per_unid_data_bloqueio, per_unid_hash FROM smc_unidades
						WHERE per_unid_id_sec = '$row_Secretaria[sec_id]' AND per_unid_periodo = '$p'";
						$PeriodosBloqueio1 = mysql_query($query_PeriodosBloqueio1, $SmecelNovo) or die(mysql_error());
						$row_PeriodosBloqueio1 = mysql_fetch_assoc($PeriodosBloqueio1);
						$totalRows_PeriodosBloqueio1 = mysql_num_rows($PeriodosBloqueio1);
						
						?>
                          <th colspan="<?php echo $row_Criterios['ca_qtd_av_periodos']+1; ?>" class="ls-txt-center ls-display-none-xs" width="15"><?php echo $p; ?>º PERÍODO <br><small>LIMITE: <?php if ($totalRows_PeriodosBloqueio1 > 0) { echo date("d/m/y", strtotime($row_PeriodosBloqueio1['per_unid_data_bloqueio'])); } else { echo "-"; } ?> <?php if (($totalRows_PeriodosBloqueio1 > 0) && ($row_PeriodosBloqueio1['per_unid_data_bloqueio'] < date("Y-m-d"))) { ?> <br><span class="">Período atingido</span> <?php } ?></small></th>
                          <?php } ?>
                          <th colspan="4" class="ls-txt-center ls-display-none-xs">RESULTADO</th>
                        </tr>
                        <tr class="">
                          <th colspan="3" class="ls-txt-center">IDENTIFICAÇÃO</th>
                          <?php $tmu = 0; ?>
                          <?php for ($p = 1; $p <= $row_Criterios['ca_qtd_periodos']; $p++) { ?>
                           
                          <?php for ($c = 1; $c <= $row_Criterios['ca_qtd_av_periodos']; $c++) { ?>
                          <th width="15" class="ls-txt-center ls-display-none-xs"><?php echo $c; ?>ª</th>
                          <?php } ?>
                          <th width="15" class="ls-txt-center ls-display-none-xs">MU</th>
                          <?php } ?>
                          <th width="15" class="ls-txt-center ls-display-none-xs">TP</th>
                          <th width="15" class="ls-txt-center ls-display-none-xs">MC</th>
                          <th width="15" class="ls-txt-center ls-display-none-xs">NR</th>
                          <th width="15" class="ls-txt-center ls-display-none-xs">RES</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $num = 1; do { ?>
                          <tr>
                          <td width="15" class="ls-txt-center"><strong><?php echo $num; $num++; ?></strong></td>
                          <td width="40" class="" style="padding:0 5px;">
                          <?php if ($row_Alunos['aluno_foto']=="") { ?>
                          <img src="<?php echo URL_BASE.'/aluno/fotos/' ?>semfoto.jpg"  class="aluno ls-txt-left ls-float-left" border="0" width="100%">
                          <?php } else { ?>
                          <img src="<?php echo URL_BASE.'/aluno/fotos/' ?><?php echo $row_Alunos['aluno_foto']; ?>"  class="hoverable aluno ls-float-left" border="0" width="100%">
                          <?php } ?>
                          </td>
                          
                          <td width="150" class="" style="padding:0 5px;">
                          
                          <a href="rendimento_aluno.php?cod=<?php echo $row_Alunos['vinculo_aluno_hash']; ?>&disciplina=<?php echo $row_Disciplina['disciplina_id']; ?>&turma=<?php echo $row_Alunos['turma_id']; ?>&ref=mapa" class="ls-ico-export">
						  
						  <?php echo $row_Alunos['aluno_nome']; ?>
                              </a>
                              
                              <?php if ( $row_Alunos['vinculo_aluno_situacao']<>"1") { ?>
                              <br>
                              <span class="ls-color-danger"><?php echo $row_Alunos['vinculo_aluno_situacao_nome']; ?></span>
                              <?php } ?>
                              
                              </td>
                          <?php $tmu = 0; ?>
                          <?php for ($p = 1; $p <= $row_Criterios['ca_qtd_periodos']; $p++) { ?>
                          
                          <?php
						
						mysql_select_db($database_SmecelNovo, $SmecelNovo);
						$query_PeriodosBloqueio = "SELECT per_unid_id, per_unid_id_ano, per_unid_id_sec, per_unid_periodo, per_unid_data_inicio, per_unid_data_fim, per_unid_data_bloqueio, per_unid_hash FROM smc_unidades
						WHERE per_unid_id_sec = '$row_Secretaria[sec_id]' AND per_unid_periodo = '$p'";
						$PeriodosBloqueio = mysql_query($query_PeriodosBloqueio, $SmecelNovo) or die(mysql_error());
						$row_PeriodosBloqueio = mysql_fetch_assoc($PeriodosBloqueio);
						$totalRows_PeriodosBloqueio = mysql_num_rows($PeriodosBloqueio);
						
						?>
                          
                          <?php $ru = 0; ?>
                          <?php for ($a = 1; $a <= $row_Criterios['ca_qtd_av_periodos']; $a++) { ?>
                          <td width="15" class="ls-txt-center ls-display-none-xs"><?php
								mysql_select_db($database_SmecelNovo, $SmecelNovo);
								$query_Notas = "SELECT nota_id, nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_max, nota_min, nota_valor, nota_hash FROM smc_nota WHERE nota_id_matricula = '$row_Alunos[vinculo_aluno_id]' AND nota_id_disciplina = '$row_Disciplina[disciplina_id]' AND nota_periodo = '$p' AND nota_num_avaliacao = '$a'";
								$Notas = mysql_query($query_Notas, $SmecelNovo) or die(mysql_error());
								$row_Notas = mysql_fetch_assoc($Notas);
								$totalRows_Notas = mysql_num_rows($Notas);
								$ru = $ru + $row_Notas['nota_valor'];
							  ?>
                              
                              <?php if ( $row_Alunos['vinculo_aluno_situacao']=="1") { ?>
                             <input 
                              type="text" 
                              inputmode="numeric"
                              max="<?php echo $row_Criterios['ca_nota_max_av']; ?>" 
                              notaMin="<?php echo $row_Criterios['ca_nota_min_av']; ?>" 
                              step="0.1" 
                              name="<?php echo $row_Notas['nota_hash']; ?>" 
                              value="<?php echo $row_Notas['nota_valor']; ?>" 
                              notaAnterior="<?php echo $row_Notas['nota_valor']; ?>" 
                              disciplina="<?php echo $row_Disciplina['disciplina_nome']; ?>" 
                              class="ls-no-style-input ls-txt-center ls-text-md nota"
							  style=" display:block; width:100%; <?php if ($row_Notas['nota_valor'] >= $row_Criterios['ca_nota_min_av']) { echo "; color:blue;"; } else { echo "; color:red;"; }; ?>" 
                              <?php if ($totalRows_Notas==0) { echo "disabled"; } ?>
                              
                              <?php if (($totalRows_PeriodosBloqueio > 0) && ($row_PeriodosBloqueio['per_unid_data_bloqueio'] < date("Y-m-d"))) { ?> disabled="" readonly <?php } ?>
                          >
                              <?php } else { ?>
                              <span style=" display:block; width:100%; <?php if ($row_Notas['nota_valor'] >= $row_Criterios['ca_nota_min_av']) { echo "; color:blue;"; } else { echo "; color:red;"; }; ?>"><?php echo $row_Notas['nota_valor']; ?></span>
                              <?php } ?>
                              
                              </td>
                          <?php } ?>
                          <td width="15" class="ls-txt-center ls-display-none-xs"><span class="ls-text-md"><?php $mu = mediaUnidade($ru,$row_Criterios['ca_arredonda_media'],$row_Criterios['ca_aproxima_media'],$row_Criterios['ca_media_min_periodo'],$row_Criterios['ca_calculo_media_periodo'],$row_Criterios['ca_qtd_av_periodos'],$row_Criterios['ca_digitos']); ?></span>
                              <?php $tmu = $tmu + $mu; ?></td>
                          <?php } ?>
                            </td>
                          <td width="15" class="ls-txt-center ls-display-none-xs"><span class="ls-text-md"><?php $tp = totalPontos($tmu,$row_Criterios['ca_digitos']); ?></span></td>
                          <td width="15" class="ls-txt-center ls-display-none-xs"><span class="ls-text-md"><?php $mc = mediaCurso($tp,$row_Criterios['ca_arredonda_media'],$row_Criterios['ca_aproxima_media'],$row_Criterios['ca_min_media_aprovacao_final'],$row_Criterios['ca_qtd_periodos'],$row_Criterios['ca_digitos']); ?></span></td>
                          <td width="15" class="ls-txt-center ls-display-none-xs"><?php 
				mysql_select_db($database_SmecelNovo, $SmecelNovo);
				$query_notaAf = "SELECT nota_id, nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_max, nota_min, nota_valor, nota_hash FROM smc_nota WHERE nota_id_matricula = '$row_Alunos[vinculo_aluno_id]' AND nota_id_disciplina = '$row_Disciplina[disciplina_id]' AND nota_periodo = '99' AND nota_num_avaliacao = '99'";
				$notaAf = mysql_query($query_notaAf, $SmecelNovo) or die(mysql_error());
				$row_notaAf = mysql_fetch_assoc($notaAf);
				$totalRows_notaAf = mysql_num_rows($notaAf);
				$af = avaliacaoFinal($row_notaAf['nota_valor'],$row_Criterios['ca_nota_min_recuperacao_final']);
			?>
                             <span class="ls-text-md"><?php echo $row_notaAf['nota_valor']; ?></span></td>
                             
                             
                             
                          <td width="15" class="ls-txt-center ls-display-none-xs"><?php 
				
					$resultado = resultadoFinal($mc, $af, $row_Criterios['ca_nota_min_recuperacao_final'], $row_Criterios['ca_min_media_aprovacao_final'],$row_Criterios['ca_digitos']);				
				
					if ($resultado == "APR") { echo "<small class='light-green lighten-2'>APR</small>"; } else { echo "<small class='pink accent-1'>CON</small>"; }
				
				?></td>
                        </tr>
                          <?php } while ($row_Alunos = mysql_fetch_assoc($Alunos)); ?>
                        <?php } ?>
                      </tbody>
                    </table>
                    <br>
                    <p> LEGENDA: </p>
                    <p> <strong>MU</strong>: MÉDIA DA UNIDADE - <strong>TP</strong>: TOTAL DE PONTOS - <strong>MC</strong>: MÉDIA DO CURSO - <strong>NR</strong>: NOTA DE RECUPERAÇÃO - <strong>RES</strong>: RESULTADO FINAL </p>
                  </div>
                  <div id="status"></div>
                  <hr>
                  
                  <?php //include_once "inc/footer.php"; ?>
                </main>
                <?php include_once "inc/notificacoes.php"; ?>
                <!-- We recommended use jQuery 1.10 or up --> 
                <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
                <script src="js/locastyle.js"></script> 
                <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
                <script src="js/sweetalert2.min.js"></script>
	   <script src="../../js/jquery.mask.js"></script> 
	  <script type="text/javascript" src="../js/app.js"></script>
                
                <script type="text/javascript">
				
$(document).ready(function(){
$("input").blur(function(){
	
	var id 				= $(this).attr('name');
	var valor 			= $(this).val();
	var notaAnterior 	= $(this).attr('notaAnterior');
	var notaMax 		= $(this).attr('max');
	var notaMin 		= $(this).attr('notaMin');
	var disciplina 		= $(this).attr('disciplina');
	
	if (valor < notaMin) {
		$(this).css("color", "red");
		} else {
			$(this).css("color", "blue");
			}
	
	
	if( (valor != notaAnterior) ) {
	$.ajax({
		type : 'POST',
        url  : 'fnc/lancaNota.php',
        data : {
			id				:id,
			valor			:valor,
			notaMax			:notaMax,
			notaAnterior	:notaAnterior,
			disciplina		:disciplina
			},
			success:function(data){
				$('#status').html(data);
				
				setTimeout(function(){
					  $("#status").html("");					
					},15000);
				
				}
		})
	}
	
	  });
});
				
$(document).ready(function(){
  $('.nota').mask('00.0', {reverse: true});
  $('.money').mask('000.000.000.000.000,00', {reverse: true});
});				

$(document).ready(function() {
            $('.recarregar').click(function() {
                location.reload();
            });
      });  		  
		   


$(function() {
   $(document).on('click', 'input[type=text]', function() {
     this.select();
   });
 });	 

</script>
                 
                <script type="application/javascript">
/*
Swal.fire({
  //position: 'top-end',
  icon: 'success',
  title: 'Tudo certo por aqui',
  showConfirmButton: false,
  timer: 1500
})
*/
</script>
                </body>
                </html>