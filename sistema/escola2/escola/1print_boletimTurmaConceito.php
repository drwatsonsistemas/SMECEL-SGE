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
$query_criteriosAvaliativos = "
SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, 
ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, 
ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes, ca_grupo_conceito, ca_grupo_etario, ca_questionario_conceitos   
FROM smc_criterios_avaliativos 
WHERE ca_id = '$row_Matriz[matriz_criterio_avaliativo]'";
$criteriosAvaliativos = mysql_query($query_criteriosAvaliativos, $SmecelNovo) or die(mysql_error());
$row_CriteriosAvaliativos  = mysql_fetch_assoc($criteriosAvaliativos);
$totalRows_criteriosAvaliativos = mysql_num_rows($criteriosAvaliativos);







// while ($row_GrupoConceitos = mysql_fetch_assoc($GrupoConceitos)) {
//   echo "<pre>";
//   var_dump($row_GrupoConceitos);
//   echo "</pre>";
// }

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
  <link rel="stylesheet" type="text/css" href="css/locastyle.css">  <script src="js/locastyle.js"></script>
  <style>
    html{
      -webkit-print-color-adjust: exact;
    }
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
</head>
<body onload="self.print()">
  <div class="container-fluid">
    <?php do { 


      $query_disciplinasMatriz = "
      SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_reprova, matriz_disciplina_id_disciplina, disciplina_id, disciplina_nome 
      FROM smc_matriz_disciplinas
      INNER JOIN smc_disciplina ON disciplina_id = matriz_disciplina_id_disciplina 
      WHERE matriz_disciplina_id_matriz = '$row_AlunoBoletim[turma_matriz_id]'";
      $disciplinasMatriz = mysql_query($query_disciplinasMatriz, $SmecelNovo) or die(mysql_error());
      $row_disciplinasMatriz = mysql_fetch_assoc($disciplinasMatriz);
      $totalRows_disciplinasMatriz = mysql_num_rows($disciplinasMatriz);

      mysql_select_db($database_SmecelNovo, $SmecelNovo);
      $query_GrupoConceitos = "
      SELECT conceito_itens_id, conceito_itens_id_conceito, conceito_itens_descricao, conceito_itens_legenda, conceito_itens_peso 
      FROM smc_conceito_itens
      WHERE conceito_itens_id_conceito = '$row_CriteriosAvaliativos[ca_grupo_conceito]'
      ORDER BY conceito_itens_peso ASC
      ";
      $GrupoConceitos = mysql_query($query_GrupoConceitos, $SmecelNovo) or die(mysql_error());
      $row_GrupoConceitos = mysql_fetch_assoc($GrupoConceitos);
      $totalRows_GrupoConceitos = mysql_num_rows($GrupoConceitos);	  

      ?>



      <div style="page-break-inside: avoid;"> <br>
        <p>

          <div class="ls-box1"> <span class="ls-float-right" style="margin-left:20px;">
            <?php if($row_AlunoBoletim['aluno_foto']=="") { ?>
              <img src="<?php echo URL_BASE.'aluno/fotos/' ?>semfoto.jpg" style="margin:1mm;width:15mm;">
            <?php } else { ?>
              <img src="<?php echo URL_BASE.'aluno/fotos/' ?><?php echo $row_AlunoBoletim['aluno_foto']; ?>" style="margin:1mm;width:15mm;">
            <?php } ?>
          </span> <span class="ls-float-left" style="margin-right:20px;"> <img src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt="" width="60px" /></span> <?php echo $row_EscolaLogada['escola_nome']; ?><br>
          <small> Aluno(a): <strong><?php echo $row_AlunoBoletim['aluno_nome']; ?></strong><br>
            Nascimento: <strong><?php echo inverteData($row_AlunoBoletim['aluno_nascimento']); ?></strong><br>
            Filiação: <strong><?php echo $row_AlunoBoletim['aluno_filiacao1']; ?></strong><br>
            Turma: <strong><?php echo $row_AlunoBoletim['turma_nome']; ?></strong> </small> </div>
          </p>
          <p class="ls-ico-text ls-txt-center">BOLETIM ESCOLAR <?php echo $row_AlunoBoletim['vinculo_aluno_ano_letivo']; ?></p>


          <?php 
          $perc = number_format(100/$totalRows_GrupoConceitos,0); 
          $inicio = 0;
          $parc = $perc;
          $cont = 1;
          $ver = 0;
          ?>

          <table>
            <?php do { ?>
              <tr>


                <td class="ls-txt-right">
                  <span class="ls-tag-warning">
                    De <?php echo $inicio; ?>% até <?php echo $parc; ?>%
                    <?php $cont++; ?>
                    <?php if ($cont == $totalRows_GrupoConceitos) { $ver = 1; }?>     
                    <?php $inicio = $parc+1; $parc = $parc + $perc + $ver; ?>
                  </span>
                  <td>
                    <span class="ls-tag-info"><?php echo $row_GrupoConceitos['conceito_itens_legenda']; ?>: <?php echo $row_GrupoConceitos['conceito_itens_descricao']; ?></span>
                  </td>
                </tr>
              <?php } while ($row_GrupoConceitos = mysql_fetch_assoc($GrupoConceitos)); ?>
            </table>	
            <?php $nn = 1; ?>
            <table class="ls-table bordasimples ls-bg-header" width="100%">
              <thead>
                <tr>
                  <td width="40" class="ls-txt-center"></td>
                  <th class="ls-txt-center">COMPONENTES</th>
                  <?php for ($i = 1; $i <= $row_CriteriosAvaliativos['ca_qtd_periodos']; $i++) { ?>
                    <th width="50" class="ls-txt-center"><?php echo $i; ?>ª</th>
                  <?php } ?>
                  <th width="50" class="ls-txt-center">RF</th>  
                  <?php do { ?>
                    <?php 
                    mysql_select_db($database_SmecelNovo, $SmecelNovo);
                    $query_Acompanhamento = "
                    SELECT quest_conc_id, quest_conc_id_matriz, quest_conc_id_etapa, quest_conc_id_comp, quest_conc_descricao, quest_conc_hash 
                    FROM smc_questionario_conceitos
                    WHERE quest_conc_id_matriz = '$row_AlunoBoletim[turma_matriz_id]' AND quest_conc_id_comp = '$row_disciplinasMatriz[disciplina_id]'
                    ORDER BY quest_conc_descricao ASC
                    ";
                    $Acompanhamento = mysql_query($query_Acompanhamento, $SmecelNovo) or die(mysql_error());
                    $row_Acompanhamento = mysql_fetch_assoc($Acompanhamento);
                    $totalRows_Acompanhamento = mysql_num_rows($Acompanhamento);
                    ?>


                    <?php for ($i = 1; $i <= $row_CriteriosAvaliativos['ca_qtd_periodos']; $i++) { ?>
                      <?php 

                      $tot[$i] = 0;

                      ?>
                    <?php } ?>
                  </tr>

                </thead>

                <tbody>
                  <?php $n = 1; do { ?>
                            <!--
                              <tr>
                                <td class="ls-txt-center"><?php echo $n; $n++; ?></td>
                                <td><?php echo $row_Acompanhamento['quest_conc_descricao']; ?></td>
                                <?php for ($i = 1; $i <= $row_CriteriosAvaliativos['ca_qtd_periodos']; $i++) { ?>
                                <?php 
		
                                mysql_select_db($database_SmecelNovo, $SmecelNovo);
                                $query_Avaliacao = "
                                SELECT conc_ef_id, conc_ef_id_quest, conc_ef_id_matr, conc_ef_periodo, conc_ef_avaliac, conceito_itens_id_conceito, conceito_itens_peso, conceito_itens_legenda 
                                FROM smc_conceito_ef
                                LEFT JOIN smc_conceito_itens ON conceito_itens_peso = conc_ef_avaliac
                                WHERE conc_ef_id_quest = '$row_Acompanhamento[quest_conc_id]' AND conc_ef_id_matr = '$row_AlunoBoletim[vinculo_aluno_id]' AND conc_ef_periodo = '$i' AND conceito_itens_id_conceito = '$row_CriteriosAvaliativos[ca_grupo_conceito]'
                                ";
                                $Avaliacao = mysql_query($query_Avaliacao, $SmecelNovo) or die(mysql_error());
                                $row_Avaliacao = mysql_fetch_assoc($Avaliacao);
                                $totalRows_Avaliacao = mysql_num_rows($Avaliacao);
                                
                                
                                $tot[$i] = $tot[$i]+$row_Avaliacao['conceito_itens_peso'];
                                var_dump($tot); 
				
		?>
                                  
                                  <td width="60" class="ls-txt-center"><?php if ($row_Avaliacao['conceito_itens_legenda']=="") { ?>
                                  -
                                  <?php } else { ?>
                                  <span class="" style="font-weight:bolder"><?php echo $row_Avaliacao['conceito_itens_legenda']; ?>
                                  <?php } ?>
                                  </span></td>
                                 
                                  
                                  
                                  
                                <?php } ?>
                              </tr>
                            -->
                          <?php } while ($row_Acompanhamento = mysql_fetch_assoc($Acompanhamento)); ?>
                          <tr>
                            <td class="ls-txt-center"><?php echo $nn; $nn++; ?></td>
                            <td class="ls-txt-left"><?php echo $row_disciplinasMatriz['disciplina_nome']; ?></td>
                            <?php
                            $rf = 0;
                            ?>
                            <?php for ($i = 1; $i <= $row_CriteriosAvaliativos['ca_qtd_periodos']; $i++) { ?>

                              <td width="70" class="ls-txt-center"><small><?php $res = number_format((($tot[$i]/($row_CriteriosAvaliativos['ca_qtd_periodos']*$totalRows_Acompanhamento))*100),1); ?><strong><?php if ($res==0) { echo "-"; } else { echo $res."%"; } ?></strong></small></td>
                            <?php 
                            $rf = $rf+$res;
                            } 
                            ?>
                            <td class="ls-txt-center" style="background-color: #EEEEEE;">
                              <?php $rf = number_format($rf/$row_CriteriosAvaliativos['ca_qtd_periodos'] ,1) ?>
                              <strong><small><?php if ($res==0) { echo "-"; } else { echo $rf."%"; } ?></small></strong>
                            </td>
                          </tr>

                        <?php } while ($row_disciplinasMatriz = mysql_fetch_assoc($disciplinasMatriz)); ?>
                        




                      </tbody>
                    </table>


                    <small class="ls-float-right">
                      <?php if (($row_AnoLetivo['ano_letivo_resultado_final'] <= date("Y-m-d")) && $row_AnoLetivo['ano_letivo_resultado_final'] <> "") { echo "<strong>Resultado Final disponível.</strong> Verifique sua situação em cada Componente Curricular."; } else if ($row_AnoLetivo['ano_letivo_resultado_final'] == "") { echo "A data de divulgação do Resultado Final (RF) ainda será definida pela escola."; } else { echo "Resultado Final (RF) estará disponível à partir do dia ".date("d/m/Y", strtotime(($row_AnoLetivo['ano_letivo_resultado_final']))); }?>
                    </small>

                    <br>

                    <div class="ls-box ls-txt-center">
                     <strong>DADOS DE ACESSO AO PAINEL DO ALUNO</strong>
                     <table width="100%" class="ls-sm-space bordasimples">
                       <tr>
                        <td>Data de Nascimento: <strong><?php echo inverteData($row_AlunoBoletim['aluno_nascimento']); ?></strong></td>
                        <td>Código de acesso: <strong><?php echo str_pad($row_AlunoBoletim['aluno_id'], 5, '0', STR_PAD_LEFT); ?></strong></td>
                        <td>Senha de acesso: <strong><?php echo substr($row_AlunoBoletim['aluno_hash'],0,5); ?></strong><br></td>
                      </tr>
                    </table>
                    <small><i>Acesse o site www.smecel.com.br, clique em "Área do Aluno" e informe os dados acima</i></small>

                  </div>





                  <img src="img/corte.jpg">



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

          mysql_free_result($criteriosAvaliativos);

          mysql_free_result($Matriz);

          mysql_free_result($disciplinasMatriz);
        ?>