<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include('fnc/inverteData.php'); ?>
<?php include "fnc/calculos.php"; ?>
<?php include "fnc/anti_injection.php"; ?>

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

$codTurma = "";
$buscaTurma = "";
if (isset($_GET['turma'])) {
	
	if ($_GET['turma'] == "") {
	//echo "TURMA EM BRANCO";	
	header("Location: turmasAlunosVinculados.php?nada"); 
 	exit;
	}
	
  $codTurma = anti_injection($_GET['turma']);
  $codTurma = (int)$codTurma;
  $buscaTurma = " AND turma_id = $codTurma ";
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turmas = "SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo FROM smc_turma WHERE turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND turma_id_escola = '$row_EscolaLogada[escola_id]' $buscaTurma ORDER BY turma_turno, turma_etapa, turma_nome ASC";
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
                <link rel="stylesheet" type="text/css" href="css/locastyle.css">                <link rel="stylesheet" type="text/css" href="css/preloader.css">
            <script src="js/locastyle.js"></script>
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
.foo {
	
	-moz-transform: rotate(-90deg);  /* FF3.5/3.6 */
	-o-transform: rotate(-90deg);  /* Opera 10.5 */
	-webkit-transform: rotate(-90deg);  /* Saf3.1+ */
	transform: rotate(-90deg);  /* Newer browsers (incl IE9) */
	
	
	
	/*
	writing-mode: vertical-rl;
	-webkit-writing-mode: vertical-rl;
	-ms-writing-mode: vertical-rl;
	vertical-align:middle;
	
	
	 	
	-webkit-transform:rotate(180deg); //tente 90 no lugar de 270
	-moz-transform:rotate(180deg);
	-o-transform: rotate(180deg); */
	
  }
</style>
                </head>
                <body>
                <?php include_once ("menu-top.php"); ?>
                <?php include_once ("menu-esc.php"); ?>
                <main class="ls-main ">
                  <div class="container-fluid">
                    <div id="preload" class="1preload"></div>
                    <h1 class="ls-title-intro ls-ico-home">ATAS</h1>
                    <!-- CONTEÚDO -->
                    
                    <?php do { // TURMAS?>
                    <?php
		
		mysql_select_db($database_SmecelNovo, $SmecelNovo);
		$query_Matriz = "SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash, matriz_dias_letivos, matriz_semanas_letivas, matriz_dias_semana, matriz_minutos_aula, matriz_aula_dia, matriz_criterio_avaliativo FROM smc_matriz WHERE matriz_id = '$row_Turmas[turma_matriz_id]'";
		$Matriz = mysql_query($query_Matriz, $SmecelNovo) or die(mysql_error());
		$row_Matriz = mysql_fetch_assoc($Matriz);
		$totalRows_Matriz = mysql_num_rows($Matriz);


		mysql_select_db($database_SmecelNovo, $SmecelNovo);
		$query_criteriosAvaliativos = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes FROM smc_criterios_avaliativos WHERE ca_id = '$row_Matriz[matriz_criterio_avaliativo]'";
		$criteriosAvaliativos = mysql_query($query_criteriosAvaliativos, $SmecelNovo) or die(mysql_error());
		$row_criteriosAvaliativos = mysql_fetch_assoc($criteriosAvaliativos);
		$totalRows_criteriosAvaliativos = mysql_num_rows($criteriosAvaliativos);

		?>
                    <div class="ls-box ls-box ls-txt-center">
                      <?php if ($row_EscolaLogada['escola_logo']<>"") { ?>
                      <img src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt="" width="60px" />
                      <?php } else { ?>
                      <img src="../../img/brasao_republica.png" alt="" width="60px" />
                      <?php } ?>
                      <br>
                      <strong><?php echo $row_EscolaLogada['escola_nome']; ?></strong><br>
                      <small>INEP: <?php echo $row_EscolaLogada['escola_inep']; ?> AUT: - D.O. -<br>
                      ENDEREÇO: <?php echo $row_EscolaLogada['escola_endereco']; ?>, <?php echo $row_EscolaLogada['escola_num']; ?>, <?php echo $row_EscolaLogada['escola_bairro']; ?> <?php echo $row_EscolaLogada['sec_cidade']; ?>-<?php echo $row_EscolaLogada['sec_uf']; ?> CEP: <?php echo $row_EscolaLogada['escola_cep']; ?><br>
                      CNPJ: <?php echo $row_EscolaLogada['escola_cnpj']; ?> <?php echo $row_EscolaLogada['escola_email']; ?> <?php echo $row_EscolaLogada['escola_telefone1']; ?></small>
                      <p>
                      <h2 class="ls-txt-center">ATA DE RESULTADOS FINAIS</h2>
                      </p>

                      <p>
                      
                      
                      
                      <div style="text-align:justify; line-height:150%;">
                        <?php
						  setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
						  date_default_timezone_set('America/Sao_Paulo');
						  echo strftime('Aos %d dias do mês de %B do ano de %Y', strtotime($row_AnoLetivo['ano_letivo_fim']));
						 ?>
                        terminou-se o processo de apuração das notas finais e nota global do Ano Letivo de <strong><?php echo $row_Turmas['turma_ano_letivo']; ?></strong> dos alunos da turma <strong><?php echo $row_Turmas['turma_nome']; ?></strong>, turno <strong><?php echo $row_Turmas['turma_turno_nome']; ?></strong>, deste estabelecimento de ensino, com os seguintes resultados: <br>
                      </div>
                      </p>
                      <?php
			mysql_select_db($database_SmecelNovo, $SmecelNovo);
			$query_Matricula = "
			SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, 
			vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, 
			vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, 
			vinculo_aluno_vacina_atualizada, aluno_id, aluno_nome, aluno_nascimento 
			FROM smc_vinculo_aluno
			INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
			WHERE vinculo_aluno_id_turma = '$row_Turmas[turma_id]'
			ORDER BY aluno_nome ASC
			";
			$Matricula = mysql_query($query_Matricula, $SmecelNovo) or die(mysql_error());
			$row_Matricula = mysql_fetch_assoc($Matricula);
			$totalRows_Matricula = mysql_num_rows($Matricula);

		?>
                      <table class="bordasimples" width="100%">
                        <thead>
                          <tr>
                            <th>NOME</th>
                            <th width="70">NASCIMENTO</th>
                            <?php 
								mysql_select_db($database_SmecelNovo, $SmecelNovo);
								$query_DisciplinasCabecalho = "
								SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_id_disciplina,
								disciplina_id, disciplina_nome 
								FROM smc_matriz_disciplinas
								INNER JOIN smc_disciplina ON disciplina_id = matriz_disciplina_id_disciplina 
								WHERE matriz_disciplina_id_matriz = '$row_Turmas[turma_matriz_id]'";
								$DisciplinasCabecalho = mysql_query($query_DisciplinasCabecalho, $SmecelNovo) or die(mysql_error());
								$row_DisciplinasCabecalho = mysql_fetch_assoc($DisciplinasCabecalho);
								$totalRows_DisciplinasCabecalho = mysql_num_rows($DisciplinasCabecalho);
							?>
                            <?php do { ?>
                          <th width="40" height="150"><div class="foo"><?php echo $row_DisciplinasCabecalho['disciplina_nome']?></div></th>
                              <?php } while ($row_DisciplinasCabecalho = mysql_fetch_assoc($DisciplinasCabecalho)); ?>
                          <th width="40" height="150"><div class="foo">RESULTADO</div></th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php do { ?>
                            <tr>
                            <td><?php echo $row_Matricula['aluno_nome']; ?></td>
                            <td><?php echo $row_Matricula['aluno_nascimento']; ?></td>
								<?php 
                                mysql_select_db($database_SmecelNovo, $SmecelNovo);
                                $query_Disciplinas = "SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_id_disciplina FROM smc_matriz_disciplinas WHERE matriz_disciplina_id_matriz = '$row_Turmas[turma_matriz_id]'";
                                $Disciplinas = mysql_query($query_Disciplinas, $SmecelNovo) or die(mysql_error());
                                $row_Disciplinas = mysql_fetch_assoc($Disciplinas);
                                $totalRows_Disciplinas = mysql_num_rows($Disciplinas);
                                ?>
                   <?php 
				   $contaSituacao = 0;
				   do { 
				   ?>
                              <td>
                   <div style="display:none">
                   <?php 
				  $tmu = 0;
				  
				  for ($p = 1; $p <= $row_criteriosAvaliativos['ca_qtd_periodos']; $p++) {
                  
				  	$ru = 0;
					

					
				  	for ($a = 1; $a <= $row_criteriosAvaliativos['ca_qtd_av_periodos']; $a++) { 
				  		
					
					
					//mysql_select_db($database_SmecelNovo, $SmecelNovo);
					$query_Nota = "SELECT nota_id, nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_max, nota_min, nota_valor, nota_hash FROM smc_nota WHERE nota_id_matricula = '$row_Matricula[vinculo_aluno_id]' AND nota_id_disciplina = '$row_Disciplinas[matriz_disciplina_id_disciplina]' AND nota_periodo = '$p' AND nota_num_avaliacao = '$a'";
					$Nota = mysql_query($query_Nota, $SmecelNovo) or die(mysql_error());
					$row_Nota = mysql_fetch_assoc($Nota);
					$totalRows_Nota = mysql_num_rows($Nota);
					//echo exibeTraco($row_Nota['nota_valor'],$row_criteriosAvaliativos['ca_nota_min_av']);
					$ru = $ru + $row_Nota['nota_valor'];			
				  	
					 
					
					}
				  	
					$mu = mediaUnidade($ru,$row_criteriosAvaliativos['ca_arredonda_media'],$row_criteriosAvaliativos['ca_aproxima_media'],$row_criteriosAvaliativos['ca_media_min_periodo'],$row_criteriosAvaliativos['ca_calculo_media_periodo'],$row_criteriosAvaliativos['ca_qtd_av_periodos']);
                    $tmu = $tmu + $mu;
				  
                  } 
				  
				  $tp = totalPontos($tmu);
				  $mc = mediaCurso($tp,$row_criteriosAvaliativos['ca_arredonda_media'],$row_criteriosAvaliativos['ca_aproxima_media'],$row_criteriosAvaliativos['ca_min_media_aprovacao_final'],$row_criteriosAvaliativos['ca_qtd_periodos']);
				  
				  
				  		//mysql_select_db($database_SmecelNovo, $SmecelNovo);
                        $query_notaAf = "SELECT nota_id, nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_max, nota_min, nota_valor, nota_hash FROM smc_nota WHERE nota_id_matricula = '$row_Matricula[vinculo_aluno_id]' AND nota_id_disciplina = '$row_Disciplinas[matriz_disciplina_id_disciplina]' AND nota_periodo = '99' AND nota_num_avaliacao = '99'";
                        $notaAf = mysql_query($query_notaAf, $SmecelNovo) or die(mysql_error());
                        $row_notaAf = mysql_fetch_assoc($notaAf);
                        $totalRows_notaAf = mysql_num_rows($notaAf);
						$af = avaliacaoFinal($row_notaAf['nota_valor'],$row_criteriosAvaliativos['ca_nota_min_recuperacao_final']);
						
						$rf = resultadoFinal($mc, $af, $row_criteriosAvaliativos['ca_nota_min_recuperacao_final'], $row_criteriosAvaliativos['ca_min_media_aprovacao_final']);
						
				  ?>
                                </div>
                  <?php 
/*				  if ($mc >= (float)$row_criteriosAvaliativos['ca_min_media_aprovacao_final']) {
				  echo $mc; 
				  } else {
					  if ($af <> "-") {
					  echo $af;
					  } else {
						  echo $mc;
						  }					  
					  }
*/


 if ($af<>"-") {
					  echo "(".$af.")";
					  
					  if ($af < (float)$row_criteriosAvaliativos['ca_nota_min_recuperacao_final']) {
						  $contaSituacao++;
						  }
					  
					  } else {
						  echo $mc;
						  //echo $row_CriteriosAvaliativos['ca_min_media_aprovacao_final'];
						  //echo 6.0;
						  
						  if ($mc < (float)$row_criteriosAvaliativos['ca_min_media_aprovacao_final']) {
							  $contaSituacao++;
							  }
						  
						  }
				  
				  ?>
                  
                  </td>
                  
                              <?php } while ($row_Disciplinas = mysql_fetch_assoc($Disciplinas)); ?>
                              <td><?php 
							  
							  if ($contaSituacao > 0) { 
					echo "<span style='color:red;'>CON</span>";
					$contaReprovados++;		
				} else { 
					echo "APR"; 
					$contaAprovados++;
				} 
							  
							   ?></td>
                          </tr>
                            <?php } while ($row_Matricula = mysql_fetch_assoc($Matricula)); ?>
                        </tbody>
                      </table>
                    </div>
                    <?php } while ($row_Turmas = mysql_fetch_assoc($Turmas)); // TURMAS ?>
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
                      <li class="ls-txt-center hidden-xs"> <a href="tutoriais_video.php" class="ls-btn-dark ls-btn-tour">Tutorial (Vídeos)</a> </li>
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

mysql_free_result($Turmas);

mysql_free_result($Matriz);

mysql_free_result($criteriosAvaliativos);

mysql_free_result($DisciplinasCabecalho);

mysql_free_result($Matricula);

mysql_free_result($EscolaLogada);

mysql_free_result($Nota);

mysql_free_result($notaAf);
?>
				