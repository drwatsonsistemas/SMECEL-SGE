<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "conf/session.php"; ?>
<?php
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turmas = "
SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola, 
turma_id, turma_ano_letivo 
FROM smc_ch_lotacao_professor
INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
WHERE turma_ano_letivo = ".ANO_LETIVO." AND ch_lotacao_professor_id = ".ID_PROFESSOR."
GROUP BY ch_lotacao_turma_id
";
$Turmas = mysql_query($query_Turmas, $SmecelNovo) or die(mysql_error());
$row_Turmas = mysql_fetch_assoc($Turmas);
$totalRows_Turmas = mysql_num_rows($Turmas);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Logins = "SELECT login_professor_id, login_professor_id_professor, login_professor_data_hora FROM smc_login_professor WHERE login_professor_id_professor = ".ID_PROFESSOR."";
$Logins = mysql_query($query_Logins, $SmecelNovo) or die(mysql_error());
$row_Logins = mysql_fetch_assoc($Logins);
$totalRows_Logins = mysql_num_rows($Logins);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Avisos = "
SELECT vinculo_id, vinculo_id_escola, vinculo_id_sec, vinculo_id_funcionario, vinculo_id_funcao, vinculo_carga_horaria, vinculo_data_inicio, vinculo_obs,
aviso_prof_id_escola, aviso_prof_texto, aviso_prof_exibir_ate, aviso_prof_data_cadastro, escola_id, escola_nome 
FROM smc_vinculo
INNER JOIN smc_aviso_prof ON aviso_prof_id_escola = vinculo_id_escola
INNER JOIN smc_escola ON escola_id = vinculo_id_escola
WHERE vinculo_id_funcionario = ".ID_PROFESSOR."
ORDER BY aviso_prof_data_cadastro DESC";
$Avisos = mysql_query($query_Avisos, $SmecelNovo) or die(mysql_error());
$row_Avisos = mysql_fetch_assoc($Avisos);
$totalRows_Avisos = mysql_num_rows($Avisos);



mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Material1 = "
SELECT material_id, material_id_sec, material_tipo, material_painel_escola, material_painel_professor, material_link, material_titulo, material_descricao, material_etapa, material_componente, material_hash,
etapa_id, etapa_nome, etapa_nome_abrev, disciplina_id, disciplina_nome, disciplina_nome_abrev 
FROM smc_material_apoio
LEFT JOIN smc_etapa ON etapa_id = material_etapa 
LEFT JOIN smc_disciplina ON disciplina_id = material_componente
WHERE material_id_sec = ".SEC_ID." AND material_tipo = 1 AND material_painel_professor = 'S'
";
$Material1 = mysql_query($query_Material1, $SmecelNovo) or die(mysql_error());
$row_Material1 = mysql_fetch_assoc($Material1);
$totalRows_Material1 = mysql_num_rows($Material1);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Material2 = "
SELECT material_id, material_id_sec, material_tipo, material_painel_escola, material_painel_professor, material_link, material_titulo, material_descricao, material_etapa, material_componente, material_hash,
etapa_id, etapa_nome, etapa_nome_abrev, disciplina_id, disciplina_nome, disciplina_nome_abrev 
FROM smc_material_apoio
LEFT JOIN smc_etapa ON etapa_id = material_etapa 
LEFT JOIN smc_disciplina ON disciplina_id = material_componente
WHERE material_id_sec = ".SEC_ID." AND material_tipo = 2 AND material_painel_professor = 'S'
";
$Material2 = mysql_query($query_Material2, $SmecelNovo) or die(mysql_error());
$row_Material2 = mysql_fetch_assoc($Material2);
$totalRows_Material2 = mysql_num_rows($Material2);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Material3 = "
SELECT material_id, material_id_sec, material_tipo, material_painel_escola, material_painel_professor, material_link, material_titulo, material_descricao, material_etapa, material_componente, material_hash,
etapa_id, etapa_nome, etapa_nome_abrev, disciplina_id, disciplina_nome, disciplina_nome_abrev 
FROM smc_material_apoio
LEFT JOIN smc_etapa ON etapa_id = material_etapa 
LEFT JOIN smc_disciplina ON disciplina_id = material_componente
WHERE material_id_sec = ".SEC_ID." AND material_tipo = 3 AND material_painel_professor = 'S'
";
$Material3 = mysql_query($query_Material3, $SmecelNovo) or die(mysql_error());
$row_Material3 = mysql_fetch_assoc($Material3);
$totalRows_Material3 = mysql_num_rows($Material3);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Material4 = "
SELECT material_id, material_id_sec, material_tipo, material_painel_escola, material_painel_professor, material_link, material_titulo, material_descricao, material_etapa, material_componente, material_hash,
etapa_id, etapa_nome, etapa_nome_abrev, disciplina_id, disciplina_nome, disciplina_nome_abrev 
FROM smc_material_apoio
LEFT JOIN smc_etapa ON etapa_id = material_etapa 
LEFT JOIN smc_disciplina ON disciplina_id = material_componente
WHERE material_id_sec = ".SEC_ID." AND material_tipo = 4 AND material_painel_professor = 'S'
";
$Material4 = mysql_query($query_Material4, $SmecelNovo) or die(mysql_error());
$row_Material4 = mysql_fetch_assoc($Material4);
$totalRows_Material4 = mysql_num_rows($Material4);



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
<title>PROFESSOR | <?php echo $row_ProfLogado['func_nome']; ?> | SMECEL - Sistema de Gestão Escolar</title>
<meta charset="utf-8">
<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
<link rel="stylesheet" type="text/css" href="css/locastyle.css"><link rel="stylesheet" href="css/sweetalert2.min.css">
</head>
<body>
<?php include_once "inc/navebar.php"; ?>
<?php include_once "inc/sidebar.php"; ?>
<main class="ls-main">
  <div class="container-fluid">
    <h1 class="ls-title-intro ls-ico-home">Ano letivo <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h1>

    <div class="ls-box">
    &#128515; Seja bem-vind<?php if ($row_ProfLogado['func_sexo']==2) { echo "a"; } else { echo "o"; } ?>, <strong class="ls-text-capitalize"><?php $nomeProf = explode(" ", $row_ProfLogado['func_nome']); echo ucfirst(strtolower($nomeProf[0])); ?></strong>! 
    
    <?php if (date("m-d", strtotime($row_ProfLogado['func_data_nascimento'])) == (date("m-d"))) {  ?>
          &nbsp;&nbsp;&nbsp;&nbsp;<img src="../../img/bolo.png" width="15" > Que seu dia seja repleto de felicidades. <strong>Parabéns!</strong> 
        <?php } ?>
    
    </div>
    
    
    <div class="ls-txt-center">	
      <a href="aulas_calendario.php?target=aulas"><div class="col-md-3 col-xs-6 ls-background-success1 ls-md-space" style="background-color:#990000; color:#FFFFFF"><p><span class="ls-display-none-xs">REGISTRO DE </span>AULAS</p><h1 class="ls-ico-book"></h1><p></p><span class="ls-btn ls-btn-primary ls-btn-xs">ACESSAR</span></div></a>
      <a href="selecionar.php?target=frequencia&data=<?php echo date("Y-m-d"); ?>"><div class="col-md-3 col-xs-6 ls-background-info ls-md-space" style="background-color:#063; color:#FFFFFF"><p><span class="ls-display-none-xs">REGISTRAR </span>FREQUÊNCIA</p><h1 class="ls-ico-checkbox-checked"></h1><p></p><span class="ls-btn ls-btn-primary ls-btn-xs">ACESSAR</span></div></a>
      <a href="rendimento.php"><div class="col-md-3 col-xs-6 ls-background-primary ls-md-space" style="background-color:#C66; color:#FFFFFF"><p>RENDIMENTO</p><h1 class="ls-ico-bars"></h1><p></p><span class="ls-btn ls-btn-primary ls-btn-xs">ACESSAR</span></div></a>
      <a href="planejamento_mapa.php"><div class="col-md-3 col-xs-6 ls-background-warning ls-md-space" style="background-color:#F60; color:#FFFFFF"><p>PLANEJAMENTO</p><h1 class="ls-ico-numbered-list"></h1><p></p><span class="ls-btn ls-btn-primary ls-btn-xs">ACESSAR</span></div></a>
    
    
    </div>
    
    
    <p>&nbsp;</p>    

<div class="ls-box ls-board-box">
  <header class="ls-info-header">
    <p class="ls-float-right ls-float-none-xs ls-small-info">Atualizado em <strong><?php echo date("d/m/Y"); ?></strong></p>
    <h2 class="ls-title-3">Dashboard</h2>
  </header>

  <div id="sending-stats" class="row ls-clearfix">
    <div class="col-sm-6 col-md-3">
      <div class="ls-box">
        <div class="ls-box-head">
          <h6 class="ls-title-4">TURMAS</h6>
        </div>
        <div class="ls-box-body">
          <span class="ls-board-data">
            <strong><?php echo $totalRows_Turmas ?> <small>vínculos</small></strong>
          </span>
        </div>
        <div class="ls-box-footer">
          <a href="grade_analitica.php" class="ls-btn ls-btn-xs">Ver horários</a>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-md-3">
      <div class="ls-box">
        <div class="ls-box-head">
          <h6 class="ls-title-4">Logins</h6>
        </div>
        <div class="ls-box-body">
          <span class="ls-board-data">
            <strong><?php echo $totalRows_Logins ?> <small>acessos</small></strong>
          </span>
        </div>
        <div class="ls-box-footer">
          <a href="logins.php" class="ls-btn ls-btn-xs">Ver logins</a>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-md-3">
      <div class="ls-box">
        <div class="ls-box-head">
          <h6 class="ls-title-4">CURSOS</h6>
        </div>
        <div class="ls-box-body">
          <span class="ls-board-data">
            <strong>0 <small>cursos</small></strong>
          </span>
        </div>
        <div class="ls-box-footer">
          <a href="#" class="ls-btn ls-btn-xs">Ver cursos</a>
        </div>
      </div>
    </div>

	<div class="col-sm-6 col-md-3">
      <div class="ls-box">
        <div class="ls-box-head">
          <h6 class="ls-title-4">Avisos</h6>
        </div>
        <div class="ls-box-body">
          <span class="ls-board-data">
            <strong><?php echo $totalRows_Avisos; ?> <small>avisos</small></strong>
          </span>
        </div>
        <div class="ls-box-footer">
          <a href="avisos.php" class="ls-btn ls-btn-xs">Ver avisos	 </a>
        </div>
      </div>
    </div>


  </div>
  <br>
  <p class="ls-txt-right">Status: <strong class="ls-color-success">Ativo</strong> 
  <span style="clear: both;" class="ls-ico-help anti-fraud-pop" data-trigger="hover" data-ls-module="popover" data-placement="left" data-custom-class="ls-width-300" data-title="Sua conta está ativa!" data-content="Você tem vínculo com escolas e turmas."></span></p>
</div>        

<h5 class="ls-title-3">MATERIAL DE APOIO</h5>

<ul class="ls-tabs-nav">
      <li class="ls-active"><a data-ls-module="tabs" href="#dcrm">DCRM (<?php echo $totalRows_Material1; ?>)</a></li>
      <li><a data-ls-module="tabs" href="#livros">LIVROS (<?php echo $totalRows_Material2; ?>)</a></li>
      <li><a data-ls-module="tabs" href="#plan">PLANEJAMENTO ANUAL (<?php echo $totalRows_Material3; ?>)</a></li>
      <li><a data-ls-module="tabs" href="#outros">DIVERSOS (<?php echo $totalRows_Material4; ?>)</a></li>
    </ul>
    <div class="ls-tabs-container">
      <div id="dcrm" class="ls-tab-content ls-active">
      <p>
        <?php if ($totalRows_Material1 > 0) { ?>
      <table class="ls-table ls-sm-space">
          <thead)
          
        <tr>
          <th width="50"></th>
          <th>TÍTULO</th>
          <th width="220">ETAPA</th>
          <th>COMP/CAMPO EXP.</th>
        </tr>
          </thead>
        
        <tbody>
          <?php do { ?>
          <tr>
            <td><a href="../../material_apoio/<?php echo $row_Material1['material_link']; ?>" target="_blank"><span class="ls-ico-cloud-download"></span></a></td>
            <td><strong><?php echo $row_Material1['material_titulo']; ?></strong> <br>
              <i><?php echo $row_Material1['material_descricao']; ?></i></td>
            <td><?php if ($row_Material1['etapa_nome_abrev']=="") { ?>
              SEM CRITÉRIOS
              <?php } else { ?>
              <?php echo $row_Material1['etapa_nome_abrev']; ?>
              <?php } ?></td>
            <td><?php if ($row_Material1['disciplina_nome']=="") { ?>
              SEM CRITÉRIOS
              <?php } else { ?>
              <?php echo $row_Material1['disciplina_nome']; ?>
              <?php } ?></td>

        
          </tr>
        
        <?php } while ($row_Material1 = mysql_fetch_assoc($Material1)); ?>
        <tr>
          <td colspan="6"><p><small><strong><?php echo $totalRows_Material1; ?></strong> arquivo(s) enviado(s).</small></p></td>
        </tr>
          </tbody>
        
      </table>
      <?php } else { ?>
      Nenhum arquivo adicionado
      <?php } ?>
      </p>
    </div>
    <div id="livros" class="ls-tab-content">
    <p>
      <?php if ($totalRows_Material2 > 0) { ?>
    <table class="ls-table ls-sm-space">
        <thead)
          
      <tr>
        <th width="50"></th>
        <th>TÍTULO</th>
        <th width="220">ETAPA</th>
        <th>COMP/CAMPO EXP.</th>
      </tr>
        </thead>
      
      <tbody>
        <?php do { ?>
        <tr>
          <td class="ls-txt-center"><a href="../../material_apoio/<?php echo $row_Material2['material_link']; ?>" target="_blank"><span class="ls-ico-cloud-download"></span></a></td>
          <td><strong><?php echo $row_Material2['material_titulo']; ?></strong> <br>
            <i><?php echo $row_Material2['material_descricao']; ?></i></td>
          <td><?php if ($row_Material2['etapa_nome_abrev']=="") { ?>
            SEM CRITÉRIOS
            <?php } else { ?>
            <?php echo $row_Material2['etapa_nome_abrev']; ?>
            <?php } ?></td>
          <td><?php if ($row_Material2['disciplina_nome']=="") { ?>
            SEM CRITÉRIOS
            <?php } else { ?>
            <?php echo $row_Material2['disciplina_nome']; ?>
            <?php } ?></td>
      
        </tr>
      
      <?php } while ($row_Material2 = mysql_fetch_assoc($Material2)); ?>
      <tr>
        <td colspan="6"><p><small><strong><?php echo $totalRows_Material2; ?></strong> arquivo(s) enviado(s).</small></p></td>
      </tr>
        </tbody>
      
    </table>
    <?php } else { ?>
    Nenhum arquivo adicionado
    <?php } ?>
    </p>
  </div>
  <div id="plan" class="ls-tab-content">
  <p>
    <?php if ($totalRows_Material3 > 0) { ?>
  <table class="ls-table ls-sm-space">
      <thead)
          
    <tr>
      <th width="50"></th>
      <th>TÍTULO</th>
      <th width="220">ETAPA</th>
      <th>COMP/CAMPO EXP.</th>
    </tr>
      </thead>
    
    <tbody>
      <?php do { ?>
      <tr>
        <td class="ls-txt-center"><a href="../../material_apoio/<?php echo $row_Material3['material_link']; ?>" target="_blank"><span class="ls-ico-cloud-download"></span></a></td>
        <td><strong><?php echo $row_Material3['material_titulo']; ?></strong> <br>
          <i><?php echo $row_Material3['material_descricao']; ?></i></td>
        <td><?php if ($row_Material3['etapa_nome_abrev']=="") { ?>
          SEM CRITÉRIOS
          <?php } else { ?>
          <?php echo $row_Material3['etapa_nome_abrev']; ?>
          <?php } ?></td>
        <td><?php if ($row_Material3['disciplina_nome']=="") { ?>
          SEM CRITÉRIOS
          <?php } else { ?>
          <?php echo $row_Material3['disciplina_nome']; ?>
          <?php } ?></td>
    
      </tr>
    
    <?php } while ($row_Material3 = mysql_fetch_assoc($Material3)); ?>
    <tr>
      <td colspan="6"><p><small><strong><?php echo $totalRows_Material3; ?></strong> arquivo(s) enviado(s).</small></p></td>
    </tr>
      </tbody>
    
  </table>
  <?php } else { ?>
  Nenhum arquivo adicionado
  <?php } ?>
  </p>
  </div>
  <div id="outros" class="ls-tab-content">
  <p>
    <?php if ($totalRows_Material4 > 0) { ?>
  <table class="ls-table ls-sm-space">
      <thead)
          
    <tr>
      <th width="50"></th>
      <th>TÍTULO</th>
      <th width="220">ETAPA</th>
      <th>COMP/CAMPO EXP.</th>
    </tr>
      </thead>
    
    <tbody>
      <?php do { ?>
      <tr>
        <td class="ls-txt-center"><a href="../../material_apoio/<?php echo $row_Material4['material_link']; ?>" target="_blank"><span class="ls-ico-cloud-download"></span></a></td>
        <td><strong><?php echo $row_Material4['material_titulo']; ?></strong> <br>
          <i><?php echo $row_Material4['material_descricao']; ?></i></td>
        <td><?php if ($row_Material4['etapa_nome_abrev']=="") { ?>
          SEM CRITÉRIOS
          <?php } else { ?>
          <?php echo $row_Material4['etapa_nome_abrev']; ?>
          <?php } ?></td>
        <td><?php if ($row_Material4['disciplina_nome']=="") { ?>
          SEM CRITÉRIOS
          <?php } else { ?>
          <?php echo $row_Material4['disciplina_nome']; ?>
          <?php } ?></td>
    
      </tr>
    
    <?php } while ($row_Material4 = mysql_fetch_assoc($Material4)); ?>
    <tr>
      <td colspan="6"><p><small><strong><?php echo $totalRows_Material4; ?></strong> arquivo(s) enviado(s).</small></p></td>
    </tr>
      </tbody>
    
  </table>
  <?php } else { ?>
  Nenhum arquivo adicionado
  <?php } ?>
  </p>
  </div>
  </div>

    
  </div>
<?php //include_once "inc/footer.php"; ?>
</main>
<?php include_once "inc/notificacoes.php"; ?>
<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="js/sweetalert2.min.js"></script>

<script>


            jQuery('#form_cadastro').submit(function () {
                event.preventDefault();
                var dados = jQuery(this).serialize();
				
			
				$(".preload").css('display', 'block');
				//document.getElementByClass(".preload").style.display = "block";

                jQuery.ajax({
                    type: "POST",
                    url: "crud/model/insert.php",
                    data: dados,
                    success: function (data)
                    {
                        $("input").prop('disabled', true);
                        $("select").prop('disabled', true);
                        $("textarea").prop('disabled', true);
						 

                        $(".preload").css('display', 'none');
						$("#linkResultado").html(data);
						

                        setTimeout(function () {
                            $("#gerar_link").each(function () {
                                this.reset();
                            });

                            $("input").prop('disabled', false);
                            $("select").prop('disabled', false);
                            $("textarea").prop('disabled', false);
             
                        }, 2000);
                    }
                });

                return false;
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
<?php
mysql_free_result($Logins);

mysql_free_result($Turmas);
?>
