<?php require_once('../../Connections/SmecelNovoPDO.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
<?php include "../../sistema/escola/fnc/calculos.php"; ?>

<?php
$ref = "rendimento_alunos";
if (isset($_GET['ref'])) {
    $ref = "rendimento_mapa";
}

$colname_Matricula = "-1";
if (isset($_GET['cod'])) {
    $colname_Matricula = $_GET['cod'];
}

$query_Matricula = "
    SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, 
           vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, 
           vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, 
           vinculo_aluno_vacina_atualizada, aluno_id, aluno_nome, aluno_foto,
           CASE vinculo_aluno_situacao
               WHEN 1 THEN 'MATRICULADO'
               WHEN 2 THEN 'TRANSFERIDO(A)'
               WHEN 3 THEN 'DESISTENTE'
               WHEN 4 THEN 'FALECIDO(A)'
               WHEN 5 THEN 'OUTROS'
           END AS vinculo_aluno_situacao_nome 
    FROM smc_vinculo_aluno 
    INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
    WHERE vinculo_aluno_hash = :colname_Matricula
";
$stmt_Matricula = $SmecelNovo->prepare($query_Matricula);
$stmt_Matricula->bindValue(':colname_Matricula', $colname_Matricula, PDO::PARAM_STR);
$stmt_Matricula->execute();
$row_Matricula = $stmt_Matricula->fetch(PDO::FETCH_ASSOC);

$colname_Disciplina = "-1";
if (isset($_GET['disciplina'])) {
    $colname_Disciplina = $_GET['disciplina'];
}

$query_Disciplina = "
    SELECT disciplina_id, disciplina_area_conhecimento_id, disciplina_codigo_inep, disciplina_ordem, disciplina_nome, disciplina_nome_abrev 
    FROM smc_disciplina 
    WHERE disciplina_id = :colname_Disciplina
";
$stmt_Disciplina = $SmecelNovo->prepare($query_Disciplina);
$stmt_Disciplina->bindValue(':colname_Disciplina', $colname_Disciplina, PDO::PARAM_INT);
$stmt_Disciplina->execute();
$row_Disciplina = $stmt_Disciplina->fetch(PDO::FETCH_ASSOC);

$colname_Turma = "-1";
if (isset($_GET['turma'])) {
    $colname_Turma = $_GET['turma'];
}

$query_Turma = "
    SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo 
    FROM smc_turma 
    WHERE turma_id = :colname_Turma
";
$stmt_Turma = $SmecelNovo->prepare($query_Turma);
$stmt_Turma->bindValue(':colname_Turma', $colname_Turma, PDO::PARAM_INT);
$stmt_Turma->execute();
$row_Turma = $stmt_Turma->fetch(PDO::FETCH_ASSOC);

$query_Matriz = "
    SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash, matriz_dias_letivos, matriz_semanas_letivas, matriz_dias_semana, matriz_minutos_aula, matriz_aula_dia, matriz_criterio_avaliativo 
    FROM smc_matriz 
    WHERE matriz_id = :matriz_id
";
$stmt_Matriz = $SmecelNovo->prepare($query_Matriz);
$stmt_Matriz->bindValue(':matriz_id', $row_Turma['turma_matriz_id'], PDO::PARAM_INT);
$stmt_Matriz->execute();
$row_Matriz = $stmt_Matriz->fetch(PDO::FETCH_ASSOC);

$query_Criterios = "
    SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes, ca_rec_paralela, ca_digitos 
    FROM smc_criterios_avaliativos 
    WHERE ca_id = :ca_id
";
$stmt_Criterios = $SmecelNovo->prepare($query_Criterios);
$stmt_Criterios->bindValue(':ca_id', $row_Matriz['matriz_criterio_avaliativo'], PDO::PARAM_INT);
$stmt_Criterios->execute();
$row_Criterios = $stmt_Criterios->fetch(PDO::FETCH_ASSOC);

// Após recuperar os dados iniciais (matrícula, disciplina, turma)
try {
    // Query para validar acesso do professor
    $query_validate_access = "
        SELECT 
            e.escola_id,
            t.turma_id,
            d.disciplina_id,
            (SELECT COUNT(*) 
             FROM smc_vinculo v 
             WHERE v.vinculo_id_escola = e.escola_id 
             AND v.vinculo_id_funcionario = :professor_id 
             AND v.vinculo_status = '1') AS has_vinculo,
            (SELECT COUNT(*) 
             FROM smc_ch_lotacao_professor l 
             INNER JOIN smc_turma t2 ON t2.turma_id = l.ch_lotacao_turma_id
             WHERE l.ch_lotacao_escola = e.escola_id 
             AND l.ch_lotacao_turma_id = :turma_id
             AND l.ch_lotacao_disciplina_id = :disciplina_id
             AND l.ch_lotacao_professor_id = :professor_id
             AND t2.turma_ano_letivo = :ano_letivo) AS has_lotacao
        FROM smc_escola e
        INNER JOIN smc_turma t ON t.turma_id_escola = e.escola_id
        INNER JOIN smc_disciplina d ON d.disciplina_id = :disciplina_id
        WHERE e.escola_id = :escola_id
        AND t.turma_id = :turma_id
        AND t.turma_ano_letivo = :ano_letivo";

    $stmt_validate = $SmecelNovo->prepare($query_validate_access);
    $stmt_validate->execute([
        ':professor_id' => $row_ProfLogado['func_id'],
        ':escola_id' => $row_Matricula['vinculo_aluno_id_escola'],
        ':turma_id' => $colname_Turma,
        ':disciplina_id' => $colname_Disciplina,
        ':ano_letivo' => $row_AnoLetivo['ano_letivo_ano']
    ]);
    $validation_result = $stmt_validate->fetch(PDO::FETCH_ASSOC);
   
    // Verificar se o professor tem permissão
    if (!$validation_result || 
        $validation_result['has_vinculo'] == 0 || 
        $validation_result['has_lotacao'] == 0) {
        header("Location: index.php?permissao");
        exit;
    }
} catch (PDOException $e) {
    echo $e;
    exit;
}
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
table.bordasimples {
	width:100%;
	border-collapse: collapse;
	font-size:12px;
	alignment-adjust:central;
	text-align:center;
}
table.bordasimples tr td {
	border:1px dotted #000000;
	padding:2px;
	font-size:12px;
	alignment-adjust:central;
	text-align:center;
}
table.bordasimples tr th {
	border:1px dotted #000000;
	padding:2px;
	font-size:12px;
	alignment-adjust:central;
	text-align:center;
}
</style>
                </head>
                <body>
                <?php include_once "inc/navebar.php"; ?>
                <?php include_once "inc/sidebar.php"; ?>
                <main class="ls-main">
                  <div class="container-fluid">
                    <h1 class="ls-title-intro ls-ico-home">Ano letivo <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h1>
                    <p><a href="<?php echo $ref; ?>.php?escola=<?php echo $row_Turma['turma_id_escola']; ?>&etapa=<?php echo $row_Turma['turma_etapa']; ?>&componente=<?php echo $colname_Disciplina; ?>&turma=<?php echo $colname_Turma; ?>" class="ls-btn">Voltar</a></p>
                    <hr>
                    <blockquote class="ls-box"> 
                    <span style="margin-right:10px; text-align:center; float:left;">
                      <?php if ($row_Matricula['aluno_foto']=="") { ?>
                      <img src="<?php echo '../../aluno/fotos/' ?>semfoto.jpg" class="" border="0" width="50">
                      <?php } else { ?>
                      <img src="<?php echo '../../aluno/fotos/' ?><?php echo $row_Matricula['aluno_foto']; ?>" class="" border="0" width="50">
                      <?php } ?>
                      <?php //echo $row_Alunos['aluno_nome']; ?>
                      </span> 
                      Turma: <strong><?php echo $row_Turma['turma_nome']; ?></strong><br>
                      Disciplina: <strong><?php echo $row_Disciplina['disciplina_nome']; ?></strong><br>
                      Aluno(a): <strong><?php echo $row_Matricula['aluno_nome']; ?></strong> 
                      <?php if ( $row_Matricula['vinculo_aluno_situacao']<>"1") { ?>
                      <br>
                      <span class="ls-color-danger"><?php echo $row_Matricula['vinculo_aluno_situacao_nome']; ?></span>
                      <?php } ?>
                      
                      </blockquote>
                      <?php if ($row_Criterios['ca_rec_paralela']=="S") { ?>
                    <br>
                    <div class="ls-alert-warning">
                      <strong>Atenção!</strong> A nota da recuperação paralela
                      deverá ser substituída na nota de uma das avaliações do período correspondente.
                    </div>
                    <?php } ?>
                    <a href="javascript:void()" class="ls-btn ls-txt-right ls-float-right recarregar">Recalcular</a> <br>
                    <hr>
                    <?php if ($row_Matricula['vinculo_aluno_boletim']=="1") { ?>
                    
                    
                    <?php if ( $row_Matricula['vinculo_aluno_situacao']<>"1") { ?>
					<div class="ls-alert-info"><strong>Atenção:</strong> Você não poderá alterar as notas deste(a) aluno(a) pois o status está como <?php echo $row_Matricula['vinculo_aluno_situacao_nome']; ?>.</div>
					<?php } ?>
                    
                    
                    <table class="ls-table ls-sm-space ls-table-layout-auto ls-full-width ls-height-auto centered bordasimples" width="100%">
                      <thead>
                        <tr class="purple blue lighten-1 white-text">
                          <th width="30">UNI</th>
                          <th width="30">LIMITE</th>
                          <?php for ($c = 1; $c <= $row_Criterios['ca_qtd_av_periodos']; $c++) { ?>
                          <th width="20">AV<?php echo $c; ?></th>
                          <?php } ?>
                          <?php  if ($row_Criterios['ca_rec_paralela']=="S") { ?>
                          <th class="ls-txt-center">RP</th>
                          <?php } ?>
                          <th width="30">MÉDIA</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $tmu = 0; ?>
                        <?php for ($p = 1; $p <= $row_Criterios['ca_qtd_periodos']; $p++) { ?>
                        
                        <?php
						
						$query_PeriodosBloqueio = "
        SELECT per_unid_id, per_unid_id_ano, per_unid_id_sec, per_unid_periodo, per_unid_data_inicio, 
               per_unid_data_fim, per_unid_data_bloqueio, per_unid_hash 
        FROM smc_unidades
        WHERE per_unid_id_sec = :sec_id AND per_unid_periodo = :periodo
        AND per_unid_id_ano = :ano_letivo";
        $stmt_PeriodosBloqueio = $SmecelNovo->prepare($query_PeriodosBloqueio);
        $stmt_PeriodosBloqueio->bindValue(':sec_id', $row_Secretaria['sec_id'], PDO::PARAM_INT);
        $stmt_PeriodosBloqueio->bindValue(':periodo', $p, PDO::PARAM_INT);
        $stmt_PeriodosBloqueio->bindValue(':ano_letivo', $row_AnoLetivo['ano_letivo_id'], PDO::PARAM_INT);
        $stmt_PeriodosBloqueio->execute();
        $row_PeriodosBloqueio = $stmt_PeriodosBloqueio->fetch(PDO::FETCH_ASSOC);
        $totalRows_PeriodosBloqueio = $stmt_PeriodosBloqueio->rowCount();
						
						?>
                        
                        <tr>
                          <td width="30"><strong><?php echo $p; ?>º</strong></td>
                          <td width="30"><?php if ($totalRows_PeriodosBloqueio > 0) { echo date("d/m/y", strtotime($row_PeriodosBloqueio['per_unid_data_bloqueio'])); } else { echo "-"; } ?> <?php if (($totalRows_PeriodosBloqueio > 0) && ($row_PeriodosBloqueio['per_unid_data_bloqueio'] < date("Y-m-d"))) { ?> <br><small>Período bloqueado</small> <?php } ?></td>
                          <?php $ru = 0; ?>
                          <?php for ($a = 1; $a <= $row_Criterios['ca_qtd_av_periodos']; $a++) { ?>
                          <td width="30"><?php
								$query_Notas = "
                SELECT nota_id, nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, 
                       nota_max, nota_min, nota_valor, nota_hash 
                FROM smc_nota 
                WHERE nota_id_matricula = :matricula_id 
                AND nota_id_disciplina = :disciplina_id 
                AND nota_periodo = :periodo 
                AND nota_num_avaliacao = :avaliacao";
                $stmt_Notas = $SmecelNovo->prepare($query_Notas);
                $stmt_Notas->bindValue(':matricula_id', $row_Matricula['vinculo_aluno_id'], PDO::PARAM_INT);
                $stmt_Notas->bindValue(':disciplina_id', $row_Disciplina['disciplina_id'], PDO::PARAM_INT);
                $stmt_Notas->bindValue(':periodo', $p, PDO::PARAM_INT);
                $stmt_Notas->bindValue(':avaliacao', $a, PDO::PARAM_INT);
                $stmt_Notas->execute();
                $row_Notas = $stmt_Notas->fetch(PDO::FETCH_ASSOC);
                $totalRows_Notas = $stmt_Notas->rowCount();
                $ru = $ru + $row_Notas['nota_valor'];
							  ?>
                            <input type="text" inputmode="numeric" max="<?php echo $row_Criterios['ca_nota_max_av']; ?>"
                          notaMin="<?php echo $row_Criterios['ca_nota_min_av']; ?>" step="0.1"
                          name="<?php echo $row_Notas['nota_hash']; ?>" value="<?php if ($row_Notas['nota_valor'] != "") {
                            echo number_format($row_Notas['nota_valor'], $row_Criterios['ca_digitos'], '.', '');
                          } ?>" notaAnterior="<?php if ($row_Notas['nota_valor'] != "") {
                            echo number_format($row_Notas['nota_valor'], $row_Criterios['ca_digitos']);
                          } ?>" disciplina="<?php echo $row_Disciplina['disciplina_nome']; ?>"
                          decimal="<?php echo $row_Criterios['ca_digitos']; ?>"
                          class="ls-no-style-input ls-txt-center ls-text-md nota" style="display:block; width:100%; <?php if ($row_Notas['nota_valor'] >= $row_Criterios['ca_nota_min_av']) {
                            echo "color:blue;";
                          } else {
                            echo "color:red;";
                          } ?>" <?php if ($totalRows_Notas == 0) {
                            echo "disabled";
                          } ?> <?php if (($totalRows_PeriodosBloqueio > 0) && ($row_PeriodosBloqueio['per_unid_data_bloqueio'] < date("Y-m-d"))) { ?> disabled="" readonly <?php } ?>>
                          </td>
                          <?php } ?>
                          <?php  
						
						if ($row_Criterios['ca_rec_paralela']=="S") { 
						
              $query_notaRecPar = "
              SELECT nota_id, nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, 
                     nota_max, nota_min, nota_valor, nota_hash 
              FROM smc_nota 
              WHERE nota_id_matricula = :matricula_id 
              AND nota_id_disciplina = :disciplina_id 
              AND nota_periodo = :periodo 
              AND nota_num_avaliacao = 98";
              $stmt_notaRecPar = $SmecelNovo->prepare($query_notaRecPar);
              $stmt_notaRecPar->bindValue(':matricula_id', $row_Matricula['vinculo_aluno_id'], PDO::PARAM_INT);
              $stmt_notaRecPar->bindValue(':disciplina_id', $row_Disciplina['disciplina_id'], PDO::PARAM_INT);
              $stmt_notaRecPar->bindValue(':periodo', $p, PDO::PARAM_INT);
              $stmt_notaRecPar->execute();
              $row_notaRecPar = $stmt_notaRecPar->fetch(PDO::FETCH_ASSOC);
              $totalRows_notaRecPar = $stmt_notaRecPar->rowCount();
						
						?>
                          <td width="30"><input 
                              type="text" 
                              inputmode="numeric"
                              max="<?php echo $row_Criterios['ca_nota_max_av']; ?>" 
                              notaMin="<?php echo $row_Criterios['ca_nota_min_av']; ?>" 
                              step="0.1" 
                              name="<?php echo $row_notaRecPar['nota_hash']; ?>" 
                              value="<?php if ($row_notaRecPar['nota_valor']<>"") { echo number_format($row_notaRecPar['nota_valor'],$row_Criterios['ca_digitos']); } ?>" 
                              notaAnterior="<?php if ($row_notaRecPar['nota_valor']<>"") { echo number_format($row_notaRecPar['nota_valor'],$row_Criterios['ca_digitos']); } ?>"
                              decimal = "<?php echo $row_Criterios['ca_digitos']; ?>"
                              disciplina="<?php echo $row_Disciplina['disciplina_nome']; ?>" 
                              class="ls-no-style-input ls-txt-center ls-text-md nota nota"
							  style=" width:100%; <?php if ($row_notaRecPar['nota_valor'] >= $row_Criterios['ca_nota_min_recuperacao_final']) { echo "; color:blue;"; } else { echo "; color:red;"; }; ?>" 
                              <?php if ( $row_Matricula['vinculo_aluno_situacao']<>"1") { ?> disabled="" readonly <?php } ?>
                              
                          >
                            <?php //echo exibeTraco($row_notaRecPar['nota_valor'],$row_criteriosAvaliativos['ca_nota_min_av']); ?></td>
                          <?php } ?>
                          <td width="30"><?php 
                          // Calcula a média normal
                          $mu = mediaUnidadeSemExibicao($ru,$row_Criterios['ca_arredonda_media'],$row_Criterios['ca_aproxima_media'],$row_Criterios['ca_media_min_periodo'],$row_Criterios['ca_calculo_media_periodo'],$row_Criterios['ca_qtd_av_periodos'],$row_Criterios['ca_digitos']); 
                          
                          // Verifica se tem recuperação paralela e se é maior que a média
                          if($row_Criterios['ca_rec_paralela'] == "S" && $row_notaRecPar && $row_notaRecPar['nota_valor'] !== null) {
                            $notaRecPar = $row_notaRecPar['nota_valor'];
                            if ($notaRecPar > $mu) {
                              $mu = $notaRecPar;
                            }
                          }

                          // Exibe a média formatada com a cor correta
                          if ($mu == 0) {
                            echo "-";
                          } else {
                            if ($mu < $row_Criterios['ca_media_min_periodo']) {
                              echo "<span style='color:red;'>".number_format($mu, $row_Criterios['ca_digitos'], '.', '')."</span>";
                            } else {
                              echo "<span style='color:blue;'>".number_format($mu, $row_Criterios['ca_digitos'], '.', '')."</span>";
                            }
                          }
                          ?>
                            <?php $tmu = $tmu + $mu; ?></td>
                        </tr>
                        <?php } ?>
                      </tbody>
                    </table>
                    <br>
                    <table class="ls-table ls-sm-space ls-table-layout-auto ls-full-width ls-height-auto centered bordasimples">
                      <tbody>
                        <tr>
                          <td>TOTAL DE PONTOS</td>
                          <td><?php $tp = totalPontos($tmu,$row_Criterios['ca_digitos']); ?></td>
                        </tr>
                        <tr>
                          <td>MÉDIA TOTAL</td>
                          <td><?php $mc = mediaCurso($tp,$row_Criterios['ca_arredonda_media'],$row_Criterios['ca_aproxima_media'],$row_Criterios['ca_min_media_aprovacao_final'],$row_Criterios['ca_qtd_periodos'],$row_Criterios['ca_digitos']); ?></td>
                        </tr>
                        <tr>
                          <td>NOTA DE RECUPERAÇÃO</td>
                          <td><?php 
			$query_notaAf = "
      SELECT nota_id, nota_id_matricula, nota_id_disciplina, nota_periodo, 
             nota_num_avaliacao, nota_max, nota_min, nota_valor, nota_hash 
      FROM smc_nota 
      WHERE nota_id_matricula = :matricula_id 
      AND nota_id_disciplina = :disciplina_id 
      AND nota_periodo = 99 
      AND nota_num_avaliacao = 99";
  $stmt_notaAf = $SmecelNovo->prepare($query_notaAf);
  $stmt_notaAf->bindValue(':matricula_id', $row_Matricula['vinculo_aluno_id'], PDO::PARAM_INT);
  $stmt_notaAf->bindValue(':disciplina_id', $row_Disciplina['disciplina_id'], PDO::PARAM_INT);
  $stmt_notaAf->execute();
  $row_notaAf = $stmt_notaAf->fetch(PDO::FETCH_ASSOC);
  $totalRows_notaAf = $stmt_notaAf->rowCount();

  $af = avaliacaoFinal($row_notaAf['nota_valor'], $row_Criterios['ca_nota_min_recuperacao_final'], $row_Criterios['ca_digitos']);
  ?>
                            <input 
                              type="text" 
                              inputmode="numeric"
                              max="<?php echo $row_Criterios['ca_nota_max_av']; ?>" 
                              notaMin="<?php echo $row_Criterios['ca_nota_min_av']; ?>" 
                              step="0.1" 
                              name="<?php echo $row_notaAf['nota_hash']; ?>" 
                              value="<?php if ($row_notaAf['nota_valor']<>"") { echo number_format($row_notaAf['nota_valor'],$row_Criterios['ca_digitos']); } ?>" 
                              notaAnterior="<?php if ($row_notaAf['nota_valor']<>"") { echo number_format($row_notaAf['nota_valor'],$row_Criterios['ca_digitos']); } ?>"
                              decimal = "<?php echo $row_Criterios['ca_digitos']; ?>"
                              disciplina="<?php echo $row_Disciplina['disciplina_nome']; ?>" 
                              class="ls-no-style-input ls-txt-center ls-text-md nota nota av_final_input"
							  style=" <?php if ($row_notaAf['nota_valor'] >= $row_Criterios['ca_nota_min_recuperacao_final']) { echo "; color:blue;"; } else { echo "; color:red;"; }; ?>" 
                              <?php if ($totalRows_notaAf==0) { echo "disabled"; } ?>
                              <?php if ( $row_Matricula['vinculo_aluno_situacao']<>"1") { ?> disabled="" readonly <?php } ?>
                          ></td>
                        </tr>
                      </tbody>
                    </table>
                    <table class="ls-table ls-sm-space ls-table-layout-auto ls-full-width ls-height-auto centered bordasimples">
                      <thead>
                        <tr class="purple blue lighten-1 white-text">
                          <th>RESULTADO</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr height="50">
                          <td><?php 
                                    $resultado = resultadoFinal($mc, $af, $row_Criterios['ca_nota_min_recuperacao_final'], $row_Criterios['ca_min_media_aprovacao_final'],$row_Criterios['ca_digitos']);				
                                    if ($resultado == "APR") { echo "<div class=\"ls-tag-success\">APROVADO</div>"; } else { echo "<div class=\"ls-tag-danger\">CONSERVADO</div>"; }
                                ?></td>
                        </tr>
                      </tbody>
                    </table>
                    <?php //mysql_free_result($Notas); ?>
                    <?php } else { ?>
                    <div class="card-panel yellow lighten-5">Boletim não gerado.</div>
                    <?php } ?>
                    <p>
                    <div id="status"></div>
                    </p>
                    <hr>
                  </div>
                  <?php //include_once "inc/footer.php"; ?>
                </main>
                <?php include_once "inc/notificacoes.php"; ?>
                <!-- We recommended use jQuery 1.10 or up --> 
                <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
                <script src="js/locastyle.js"></script> 
                <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
                <script src="js/sweetalert2.min.js"></script> 
                
				
				
				<script type="text/javascript">
				

<?php if ( $row_Matricula['vinculo_aluno_situacao']=="1") { ?>
                      
			
$(document).ready(function(){
$("input").blur(function(){
	

  

  var id 				    = $(this).attr('name');
	var valor 			  = $(this).val();
	var notaAnterior 	= $(this).attr('notaAnterior');
	var notaMax 		  = $(this).attr('max');
	var notaMin 		  = $(this).attr('notaMin');
	var disciplina 		= $(this).attr('disciplina');
  var turma         = $(this).attr('turma');
  var escola        = $(this).attr('escola');
  var decimal       = $(this).attr('decimal');
  var consolidado   = $(this).attr('consolidado');
  var recParalela   = $(this).attr('recParalela');

  var valor1 = parseFloat(valor);
          var notaMin1 = parseFloat(notaMin);
	
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
              disciplina		:disciplina,
              turma:turma,
              escola:escola,
              decimal:decimal,
              consolidado:consolidado,
              recParalela:recParalela
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

 <?php } ?>				

 $(document).ready(function(){
  <?php if ($row_Criterios['ca_digitos']=="1") { ?>
  $('.nota').mask('00.0', {reverse: true});
  <?php } else { ?>
    $('.nota').mask('00.00', {reverse: true});
  <?php } ?> 
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
