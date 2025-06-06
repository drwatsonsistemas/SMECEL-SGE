<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
<?php

$componente = "-1";
if (isset($_GET['componente'])) {
  $componente = anti_injection($_GET['componente']);
}

$etapa = "-1";
if (isset($_GET['etapa'])) {
  $etapa = anti_injection($_GET['etapa']);
}

$colname_Componente = "-1";
if (isset($_GET['componente'])) {
  $colname_Componente = anti_injection($_GET['componente']);
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Componente = sprintf("SELECT disciplina_id, disciplina_area_conhecimento_id, disciplina_codigo_inep, disciplina_ordem, disciplina_nome, disciplina_nome_abrev, disciplina_cor_fundo, disciplina_bncc, disciplina_diversificada, disciplina_id_campos_exp FROM smc_disciplina WHERE disciplina_id = %s", GetSQLValueString($colname_Componente, "int"));
$Componente = mysql_query($query_Componente, $SmecelNovo) or die(mysql_error());
$row_Componente = mysql_fetch_assoc($Componente);
$totalRows_Componente = mysql_num_rows($Componente);

$colname_Etapa = "-1";
if (isset($_GET['etapa'])) {
  $colname_Etapa = anti_injection($_GET['etapa']);
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Etapa = sprintf("SELECT etapa_id, etapa_id_filtro, etapa_nome, etapa_idade, etapa_limite_turma, etapa_nome_abrev, etapa_ano_ef FROM smc_etapa WHERE etapa_id = %s", GetSQLValueString($colname_Etapa, "int"));
$Etapa = mysql_query($query_Etapa, $SmecelNovo) or die(mysql_error());
$row_Etapa = mysql_fetch_assoc($Etapa);
$totalRows_Etapa = mysql_num_rows($Etapa);

$escola = "-1";
if (isset($_GET['escola'])) {
  $escola = anti_injection($_GET['escola']);
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ac = "
SELECT ac_id, ac_id_professor, ac_id_escola, ac_id_componente, ac_id_etapa, ac_ano_letivo, ac_data_inicial, ac_data_final, ac_conteudo, ac_objetivo_especifico, ac_objeto_conhecimento, ac_metodologia, ac_recursos, 
ac_avaliacao, ac_criacao, ac_da_conviver, ac_da_brincar, ac_da_participar, ac_da_explorar, ac_da_expressar, ac_da_conhecerse, ac_ce_eo, ac_ce_ts, ac_ce_ef, ac_ce_cg, ac_ce_et, ac_ce_di 
FROM smc_ac
WHERE ac_id_professor = ".ID_PROFESSOR." AND ac_id_componente = '$colname_Componente' AND ac_id_etapa = '$colname_Etapa' AND ac_id_escola = '$escola' AND ac_ano_letivo = ".ANO_LETIVO."
ORDER BY ac_data_inicial DESC
";
$ac = mysql_query($query_ac, $SmecelNovo) or die(mysql_error());
$row_ac = mysql_fetch_assoc($ac);
$totalRows_ac = mysql_num_rows($ac);


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {
	
  $hash = md5(uniqid(rand(), true));	 
  
   	$data_inicio = $_POST['ac_data_inicial'];
   	$data_final = $_POST['ac_data_final'];

//   	$data_inicio = date("Y-m-d", strtotime($_POST['ac_data_inicial']));
//   	$data_final = date("Y-m-d", strtotime($_POST['ac_data_final']));
	
  $insertSQL = sprintf("INSERT INTO smc_ac (ac_data_inicial, ac_data_final, ac_id_professor, ac_id_escola, ac_id_componente, ac_id_etapa, ac_ano_letivo, ac_hash) VALUES ('$data_inicio', '$data_final', %s, %s, %s, %s, %s, '$hash')",
                       GetSQLValueString(ID_PROFESSOR, "int"),
                       GetSQLValueString($escola, "int"),
                       GetSQLValueString($colname_Componente, "int"),
                       GetSQLValueString($colname_Etapa, "int"),
                       GetSQLValueString(ANO_LETIVO, "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());
  

  $insertGoTo = "planejamento_lancar.php?hash=$hash";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO smc_ac (
  
  ac_id_professor, 
  ac_id_escola, 
  ac_id_componente, 
  ac_id_etapa, 
  ac_ano_letivo, 
  ac_data_inicial, 
  ac_data_final, 
  ac_conteudo, 
  
  ac_objetivo_especifico, 
  ac_objeto_conhecimento, 
  ac_metodologia,
  ac_recursos,  
  ac_avaliacao,
  
  ac_da_conviver,
  ac_da_brincar,
  ac_da_participar,
  ac_da_explorar,
  ac_da_expressar,
  ac_da_conhecerse,
  
  ac_ce_eo,
  ac_ce_ts,
  ac_ce_ef,
  ac_ce_cg,
  ac_ce_et,
  ac_ce_di,
  
  ac_status
  
	  ) VALUES (
	  
  '$row_ProfLogado[func_id]', 
  '$escola', 
  '$componente', 
  '$etapa', 
  '$row_AnoLetivo[ano_letivo_ano]', 
  %s, 
  %s, 
  %s, 
  %s, 
  %s, 
  %s, 
  %s, 
  %s, 
  %s, 
  %s, 
  %s, 
  %s,  
  %s, 
  %s, 
  %s, 
  %s, 
  %s,
  %s, 
  %s, 
  %s,
  '0'
  )",
                       
					   //GetSQLValueString($_POST['ac_id_professor'], "int"),
                       //GetSQLValueString($_POST['ac_id_escola'], "int"),
                       //GetSQLValueString($_POST['ac_ano_letivo'], "text"),
                       GetSQLValueString($_POST['ac_data_inicial'], "date"),
                       GetSQLValueString($_POST['ac_data_final'], "date"),
                       GetSQLValueString($_POST['ac_conteudo'], "text"),
                       GetSQLValueString($_POST['ac_objetivo_especifico'], "text"),
                       GetSQLValueString($_POST['ac_objeto_conhecimento'], "text"),
                       GetSQLValueString($_POST['ac_metodologia'], "text"),
                       GetSQLValueString($_POST['ac_recursos'], "text"),
                       GetSQLValueString($_POST['ac_avaliacao'], "text"),
					   
					   
					   GetSQLValueString(isset($_POST['ac_da_conviver']) ? "true" : "", "defined","'S'","'N'"),
					   GetSQLValueString(isset($_POST['ac_da_brincar']) ? "true" : "", "defined","'S'","'N'"),
					   GetSQLValueString(isset($_POST['ac_da_participar']) ? "true" : "", "defined","'S'","'N'"),
					   GetSQLValueString(isset($_POST['ac_da_explorar']) ? "true" : "", "defined","'S'","'N'"),
					   GetSQLValueString(isset($_POST['ac_da_expressar']) ? "true" : "", "defined","'S'","'N'"),
					   GetSQLValueString(isset($_POST['ac_da_conhecerse']) ? "true" : "", "defined","'S'","'N'"),
					   
					   
                       GetSQLValueString($_POST['ac_ce_eo'], "text"),
                       GetSQLValueString($_POST['ac_ce_ts'], "text"),
                       GetSQLValueString($_POST['ac_ce_ef'], "text"),
                       GetSQLValueString($_POST['ac_ce_cg'], "text"),
                       GetSQLValueString($_POST['ac_ce_et'], "text"),
                       GetSQLValueString($_POST['ac_ce_di'], "text")
					   
					   
					   
					   
					   );

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

  $insertGoTo = "planejamento_ver.php?cadastrado";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}


$etapa_ano = $row_Etapa['etapa_ano_ef'];
//$etapa_ano = "4,5";

$consulta = " AND bncc_ef_ano IN ($etapa_ano) ";
//$consulta = " WHERE bncc_ef_ano IN ($etapa_ano) ";
//$consulta = "";

$disciplina = $row_Componente['disciplina_id'];

//mysql_query("SET NAMES 'utf8'");
//mysql_query('SET character_set_connection=utf8');
//mysql_query('SET character_set_client=utf8');
//mysql_query('SET character_set_results=utf8');
//header('Content-Type: text/html; charset=utf-8');


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_bncc_ef = "
SELECT 
bncc_ef_id, bncc_ef_area_conhec_id, bncc_ef_comp_id, bncc_ef_componente, bncc_ef_ano, bncc_ef_campos_atuacao, bncc_ef_eixo, bncc_ef_un_tematicas, bncc_ef_prat_ling, 
bncc_ef_obj_conhec, bncc_ef_habilidades, bncc_ef_comentarios, bncc_ef_poss_curr 
FROM smc_bncc_ef
WHERE bncc_ef_comp_id = '$disciplina' $consulta";
$bncc_ef = mysql_query($query_bncc_ef, $SmecelNovo) or die(mysql_error());
$row_bncc_ef = mysql_fetch_assoc($bncc_ef);
$totalRows_bncc_ef = mysql_num_rows($bncc_ef);


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
    <p><a href="planejamento.php" class="ls-btn ls-ico-chevron-left">Voltar</a></p>
    <hr>
    <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form ls-form-horizontal row">
      
        <label class="ls-label col-md-6">
        <b class="ls-label-text">DE</b>
        <p class="ls-label-info">Informe a data inicial</p>
        <input type="date" name="ac_data_inicial" value="" size="32" required>
        </label>
        <label class="ls-label col-md-6">
        <b class="ls-label-text">ATÉ</b>
        <p class="ls-label-info">Informe a data final</p>
        <input type="date" name="ac_data_final" value="" size="32" required>
        </label>
        
      
      <?php if ($row_Etapa['etapa_id_filtro']=="1") { ?>
      <hr>
      <fieldset>
      
      <label class="ls-label col-md-12">
        <b class="ls-label-text">Campo de Experiência:</b>
        <a href="#" class="ls-tag-success"><?php echo $row_Componente['disciplina_nome']; ?></a>
        </label>
        <label class="ls-label col-md-12">
        <b class="ls-label-text">Etapa:</b>
        <a href="#" class="ls-tag-info"><?php echo $row_Etapa['etapa_nome']; ?></a>
        </label>
        
        <!-- Exemplo com Checkbox -->
        <div class="ls-label col-md-12">
          <p>DIREITOS DE APRENDIZAGEM</p>
          <label class="ls-label-text">
            <input name="ac_da_conviver" type="checkbox" />
            CONVIVER </label>
          <label class="ls-label-text">
            <input  name="ac_da_brincar" type="checkbox" />
            BRINCAR </label>
          <label class="ls-label-text">
            <input name="ac_da_participar" type="checkbox" />
            PARTICIPAR </label>
          <label class="ls-label-text">
            <input  name="ac_da_explorar" type="checkbox" />
            EXPLORAR </label>
          <label class="ls-label-text">
            <input name="ac_da_expressar" type="checkbox" />
            EXPRESSAR </label>
          <label class="ls-label-text">
            <input name="ac_da_conhecerse" type="checkbox" />
            CONHECER-SE </label>
        </div>
      </fieldset>
      <label class="ls-label col-md-12 <?php if ($row_Componente['disciplina_id_campos_exp']=="2") { echo ""; } else { echo " ls-display-none";}?>"> <b class="ls-label-text">EO – O eu, o outro e o nós &nbsp;<a href="#" data-ls-module="modal" data-target="#modalLargeEO" class="ls-ico-help ls-float-right"></a> </b>
        <textarea name="ac_ce_eo" id=""  rows="4"></textarea>
      </label>
      <label class="ls-label col-md-12 <?php if ($row_Componente['disciplina_id_campos_exp']=="1") { echo ""; } else { echo " ls-display-none";}?>"> <b class="ls-label-text">TS – Traços, sons, cores e formas &nbsp;<a href="#" data-ls-module="modal" data-target="#modalLargeTS" class="ls-ico-help ls-float-right"></a> </b>
        <textarea name="ac_ce_ts" id=""  rows="4"></textarea>
      </label>
      <label class="ls-label col-md-12 <?php if ($row_Componente['disciplina_id_campos_exp']=="4") { echo ""; } else { echo " ls-display-none";}?>"> <b class="ls-label-text">EF – Escuta, fala, pensamento e imaginação &nbsp;<a href="#" data-ls-module="modal" data-target="#modalLargeEF" class="ls-ico-help ls-float-right"></a> </b>
        <textarea name="ac_ce_ef" id=""  rows="4"></textarea>
      </label>
      <label class="ls-label col-md-12 <?php if ($row_Componente['disciplina_id_campos_exp']=="3") { echo ""; } else { echo " ls-display-none";}?>"> <b class="ls-label-text">CG – Corpo, gestos e movimento &nbsp; <a href="#" data-ls-module="modal" data-target="#modalLargeCG" class="ls-ico-help ls-float-right"></a> </b>
        <textarea name="ac_ce_cg" id=""  rows="4"></textarea>
      </label>
      <label class="ls-label col-md-12 <?php if ($row_Componente['disciplina_id_campos_exp']=="5") { echo ""; } else { echo " ls-display-none";}?>"> <b class="ls-label-text">ET – Espaços, tempos, quantidades, relações e transformações &nbsp;<a href="#" data-ls-module="modal" data-target="#modalLargeET" class="ls-ico-help ls-float-right"></a> </b>
        <textarea name="ac_ce_et" id=""  rows="4"></textarea>
      </label>
      <label class="ls-label col-md-12 <?php if ($row_Componente['disciplina_id_campos_exp']=="") { echo ""; } else { echo " ls-display-none";}?>"> <b class="ls-label-text"><?php echo $row_Componente['disciplina_nome']; ?> (Parte diversificada) <a href="#" data-ls-module="modal" data-target="#modalLargeN" class="ls-ico-help ls-float-right"></a> </b>
        <textarea name="ac_ce_di" id=""  rows="4"></textarea>
      </label>
      
      <label class="ls-label col-md-12">
      <b class="ls-label-text">Metodologia</b>
      <p class="ls-label-info"></p>
      <textarea name="ac_metodologia" id="" class="materialize-textarea"></textarea>
      </label>
      <label class="ls-label col-md-12">
      <b class="ls-label-text">Avaliação</b>
      <p class="ls-label-info"></p>
      <textarea name="ac_avaliacao" id="" class="materialize-textarea"></textarea>
      </label>
      <label class="ls-label col-md-12">
      <b class="ls-label-text">Observação</b>
      <p class="ls-label-info"></p>
      <textarea name="ac_conteudo" id="" class="materialize-textarea"></textarea>
      </label>
      
      <?php } ?>
      <?php if (($row_Etapa['etapa_id_filtro']=="3") || ($row_Etapa['etapa_id_filtro']=="7")) { ?>
      <hr>
        <label class="ls-label col-md-12">
        <b class="ls-label-text">Componente curricular:</b>
        <a href="#" class="ls-tag-success"><?php echo $row_Componente['disciplina_nome']; ?></a>
        </label>
        <label class="ls-label col-md-12">
        <b class="ls-label-text">Etapa:</b>
        <a href="#" class="ls-tag-info"><?php echo $row_Etapa['etapa_nome']; ?></a>
        </label>
        
        <label class="ls-label col-md-12">
        <a href="#" data-ls-module="modal" data-target="#modalHabilidades" class="ls-btn-primary<?php if ($totalRows_bncc_ef == 0) { ?> ls-display-none<?php } ?>">HABILIDADES&nbsp;<i class="ls-ico-help"></i></a>
        </label>
        
        
      <label class="ls-label col-md-12">
      <b class="ls-label-text">Unidades Temáticas/Eixo/Práticas de linguagem</b>
      <p class="ls-label-info"></p>
      <textarea name="ac_recursos" id="" class="materialize-textarea"></textarea>
      </label>
      <label class="ls-label col-md-12">
      <b class="ls-label-text">Objetos de Conhecimento (EF) / Conteúdo</b>
      <p class="ls-label-info"></p>
      <textarea name="ac_objeto_conhecimento" id="" class="materialize-textarea"></textarea>
      </label>
      <label class="ls-label col-md-12">
      <b class="ls-label-text">Habilidades (EF)</b>
      <p class="ls-label-info"></p>
      <textarea name="ac_objetivo_especifico" id="" class="materialize-textarea"></textarea>
      </label>
      <label class="ls-label col-md-12">
      <b class="ls-label-text">Metodologia</b>
      <p class="ls-label-info"></p>
      <textarea name="ac_metodologia" id="" class="materialize-textarea"></textarea>
      </label>
      <label class="ls-label col-md-12">
      <b class="ls-label-text">Avaliação</b>
      <p class="ls-label-info"></p>
      <textarea name="ac_avaliacao" id="" class="materialize-textarea"></textarea>
      </label>
      <label class="ls-label col-md-12">
      <b class="ls-label-text">Observação</b>
      <p class="ls-label-info"></p>
      <textarea name="ac_conteudo" id="" class="materialize-textarea"></textarea>
      </label>
      <?php } ?>
      <div class="ls-actions-btn">
        <input type="submit" class="ls-btn-primary" value="REGISTRAR PLANEJAMENTO">
        <input type="hidden" name="MM_insert" value="form1">
      </div>
    </form>
    <hr>
    <p>&nbsp;</p>
  </div>
  <?php //include_once "inc/footer.php"; ?>
</main>
<?php include_once "inc/notificacoes.php"; ?>


<div class="ls-modal" id="modalHabilidades">
  <div class="ls-modal-box ls-modal-large">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title">HABILIDADES</h4>
    </div>
    <div class="ls-modal-body" id="myModalBody">
     
      <table class="ls-table">
        <?php do { ?>
          <tr>

            <td>
            <strong class="">Habilidades</strong> <?php echo utf8_decode($row_bncc_ef['bncc_ef_habilidades']); ?><br><br>
			<strong class="">Componente</strong> <?php echo utf8_decode($row_bncc_ef['bncc_ef_componente']); ?> | 
            <strong class="">Ano/Faixa</strong> <?php echo utf8_decode($row_bncc_ef['bncc_ef_ano']); ?>º ano(s)<br><br>
            <?php if ($row_bncc_ef['bncc_ef_campos_atuacao']<>"") { ?><strong class="">Campo de atuação</strong> <?php echo utf8_decode($row_bncc_ef['bncc_ef_campos_atuacao']); ?><br><br><?php } ?>
            <?php if ($row_bncc_ef['bncc_ef_eixo']<>"") { ?><strong class="">Eixo</strong> <?php echo utf8_decode($row_bncc_ef['bncc_ef_eixo']); ?><br><br><?php } ?>
            <?php if ($row_bncc_ef['bncc_ef_un_tematicas']<>"") { ?><strong class="">Unidades Temáticas</strong> <?php echo utf8_decode($row_bncc_ef['bncc_ef_un_tematicas']); ?><br><br><?php } ?>
            <?php if ($row_bncc_ef['bncc_ef_prat_ling']<>"") { ?><strong class="">Práticas de Linguagem</strong> <?php echo utf8_decode($row_bncc_ef['bncc_ef_prat_ling']); ?><br><br><?php } ?>
            <strong class="">Objetos de conhecimento</strong> <?php echo utf8_decode($row_bncc_ef['bncc_ef_obj_conhec']); ?><br><br>
            <strong class="">Comentários</strong> <?php echo utf8_decode($row_bncc_ef['bncc_ef_comentarios']); ?><br><br>
            <strong class="">Possibilidades para o Currículo</strong> <?php echo utf8_decode($row_bncc_ef['bncc_ef_poss_curr']); ?><br>

            </td>


          </tr>
          <?php } while ($row_bncc_ef = mysql_fetch_assoc($bncc_ef)); ?>
      </table>

    </div>
    <div class="ls-modal-footer">
      <button class="ls-btn-primary" data-dismiss="modal">FECHAR</button>
    </div>
  </div>
</div><!-- /.modal -->

<div class="ls-modal" id="modalLargeEO">
  <div class="ls-modal-large">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title">EO – O eu, o outro e o nós</h4>
    </div>
    <div class="ls-modal-body">
                    <p>
                    <table class="ls-table ls-sm-space">
                      <thead>
                        <tr>
                          <th colspan="3"> OBJETIVOS DE APRENDIZAGEM E DESENVOLVIMENTO </th>
                        </tr>
                        <tr>
                          <th> Bebês (zero a 1 ano e 6 meses) </th>
                          <th> Crianças bem pequenas (1 ano
                            e 7 meses a 3 anos e 11 meses) </th>
                          <th> Crianças pequenas (4 anos a
                            5 anos e 11 meses) </th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td><strong>(EI01EO01)</strong> Perceber que suas ações
                            têm efeitos nas outras
                            crianças e nos adultos. </td>
                          <td><strong>(EI02EO01)</strong> Demonstrar atitudes de
                            cuidado e solidariedade na
                            interação com crianças e
                            adultos. </td>
                          <td><strong>(EI03EO01)</strong> Demonstrar empatia pelos
                            outros, percebendo que
                            as pessoas têm diferentes
                            sentimentos, necessidades e
                            maneiras de pensar e agir. </td>
                        </tr>
                        <tr>
                          <td><strong>(EI01EO02)</strong> Perceber as possibilidades
                            e os limites de seu corpo nas
                            brincadeiras e interações
                            das quais participa. </td>
                          <td><strong>(EI02EO02)</strong> Demonstrar imagem positiva
                            de si e confiança em sua
                            capacidade para enfrentar
                            dificuldades e desafios. </td>
                          <td><strong>(EI03EO02)</strong> Agir de maneira independente,
                            com confiança em suas
                            capacidades, reconhecendo
                            suas conquistas e limitações. </td>
                        </tr>
                        <tr>
                          <td><strong>(EI01EO03)</strong> Interagir com crianças
                            da mesma faixa etária
                            e adultos ao explorar
                            espaços, materiais,
                            objetos, brinquedos. </td>
                          <td><strong> (EI02EO03)</strong> Compartilhar os objetos e
                            os espaços com crianças da
                            mesma faixa etária e adultos. </td>
                          <td><strong>(EI03EO03)</strong> Ampliar as relações
                            interpessoais, desenvolvendo
                            atitudes de participação e
                            cooperação. </td>
                        </tr>
                        <tr>
                          <td><strong>(EI01EO04)</strong> Comunicar necessidades,
                            desejos e emoções,
                            utilizando gestos,
                            balbucios, palavras. </td>
                          <td><strong> (EI02EO04)</strong> Comunicar-se com os colegas
                            e os adultos, buscando
                            compreendê-los e fazendo-se
                            compreender. </td>
                          <td><strong> (EI03EO04)</strong> Comunicar suas ideias e
                            sentimentos a pessoas e
                            grupos diversos. </td>
                        </tr>
                        <tr>
                          <td><strong> (EI01EO05)</strong> Reconhecer seu corpo e
                            expressar suas sensações
                            em momentos de
                            alimentação, higiene,
                            brincadeira e descanso. </td>
                          <td><strong> (EI02EO05)</strong> Perceber que as pessoas
                            têm características físicas
                            diferentes, respeitando essas
                            diferenças. </td>
                          <td><strong> (EI03EO05)</strong> Demonstrar valorização das
                            características de seu corpo
                            e respeitar as características
                            dos outros (crianças e adultos)
                            com os quais convive. </td>
                        </tr>
                        <tr>
                          <td><strong> (EI01EO06)</strong> Interagir com outras crianças
                            da mesma faixa etária e
                            adultos, adaptando-se
                            ao convívio social. </td>
                          <td><strong> (EI02EO06)</strong> Respeitar regras básicas de
                            convívio social nas interações
                            e brincadeiras. </td>
                          <td><strong> (EI03EO06)</strong> Manifestar interesse e
                            respeito por diferentes
                            culturas e modos de vida. </td>
                        </tr>
                        <tr>
                          <td></td>
                          <td><strong> (EI02EO07)</strong> Resolver conflitos nas
                            interações e brincadeiras, com
                            a orientação de um adulto. </td>
                          <td><strong> (EI03EO07)</strong> Usar estratégias pautadas
                            no respeito mútuo para lidar
                            com conflitos nas interações
                            com crianças e adultos. </td>
                        </tr>
                      </tbody>
                    </table>
                    </p>
    </div>
    <div class="ls-modal-footer">
      <button class="ls-btn-primary" data-dismiss="modal">FECHAR</button>
    </div>
  </div>
</div>

<div class="ls-modal" id="modalLargeCG">
  <div class="ls-modal-large">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title">CG – Corpo, gestos e movimento</h4>
    </div>
    <div class="ls-modal-body">
                    <p>
                    <table class="ls-table ls-sm-space">
                      <thead>
                        <tr>
                          <th colspan="3"> OBJETIVOS DE APRENDIZAGEM E DESENVOLVIMENTO </th>
                        </tr>
                        <tr>
                          <th> Bebês (zero a 1 ano e 6 meses) </th>
                          <th> Crianças bem pequenas (1 ano
                            e 7 meses a 3 anos e 11 meses) </th>
                          <th> Crianças pequenas (4 anos a
                            5 anos e 11 meses) </th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td><strong>(EI01CG01)</strong> Movimentar as partes
                            do corpo para exprimir
                            corporalmente emoções,
                            necessidades e desejos. </td>
                          <td><strong>(EI02CG01)</strong> Apropriar-se de gestos e
                            movimentos de sua cultura no
                            cuidado de si e nos jogos e
                            brincadeiras. </td>
                          <td><strong>(EI03CG01)</strong> Criar com o corpo formas
                            diversificadas de expressão
                            de sentimentos, sensações
                            e emoções, tanto nas
                            situações do cotidiano
                            quanto em brincadeiras,
                            dança, teatro, música. </td>
                        </tr>
                        <tr>
                          <td><strong>(EI01CG02)</strong> Experimentar as
                            possibilidades corporais
                            nas brincadeiras e
                            interações em ambientes
                            acolhedores e desafiantes. </td>
                          <td><strong>(EI02CG02)</strong> Deslocar seu corpo no espaço,
                            orientando-se por noções
                            como em frente, atrás, no alto,
                            embaixo, dentro, fora etc., ao
                            se envolver em brincadeiras
                            e atividades de diferentes
                            naturezas. </td>
                          <td><strong>(EI03CG02)</strong> Demonstrar controle e
                            adequação do uso de seu
                            corpo em brincadeiras e
                            jogos, escuta e reconto
                            de histórias, atividades
                            artísticas, entre outras
                            possibilidades. </td>
                        </tr>
                        <tr>
                          <td><strong>(EI01CG03)</strong> Imitar gestos e
                            movimentos de outras
                            crianças, adultos e animais. </td>
                          <td><strong>(EI02CG03)</strong> Explorar formas de
                            deslocamento no espaço
                            (pular, saltar, dançar),
                            combinando movimentos e
                            seguindo orientações. </td>
                          <td><strong>(EI03CG03)</strong> Criar movimentos, gestos,
                            olhares e mímicas em
                            brincadeiras, jogos e
                            atividades artísticas como
                            dança, teatro e música. </td>
                        </tr>
                        <tr>
                          <td><strong>(EI01CG04)</strong> Participar do cuidado do
                            seu corpo e da promoção
                            do seu bem-estar. </td>
                          <td><strong>(EI02CG04)</strong> Demonstrar progressiva
                            independência no cuidado do
                            seu corpo. </td>
                          <td><strong>(EI03CG04)</strong> Adotar hábitos de
                            autocuidado relacionados
                            a higiene, alimentação,
                            conforto e aparência. </td>
                        </tr>
                        <tr>
                          <td><strong>(EI01CG05)</strong> Utilizar os movimentos
                            de preensão, encaixe e
                            lançamento, ampliando
                            suas possibilidades de
                            manuseio de diferentes
                            materiais e objetos. </td>
                          <td><strong>(EI02CG05)</strong> Desenvolver progressivamente
                            as habilidades manuais,
                            adquirindo controle para
                            desenhar, pintar, rasgar,
                            folhear, entre outros. </td>
                          <td><strong>(EI03CG05)</strong> Coordenar suas habilidades
                            manuais no atendimento
                            adequado a seus interesses
                            e necessidades em situações
                            diversas. </td>
                        </tr>
                      </tbody>
                    </table>
                    </p>
    </div>
    <div class="ls-modal-footer">
      <button class="ls-btn-primary" data-dismiss="modal">FECHAR</button>
    </div>
  </div>
</div>

<div class="ls-modal" id="modalLargeTS">
  <div class="ls-modal-large">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title">TS – Traços, sons, cores e formas</h4>
    </div>
    <div class="ls-modal-body">
                    <p>
                    <table class="ls-table ls-sm-space">
                      <thead>
                        <tr>
                          <th colspan="3"> OBJETIVOS DE APRENDIZAGEM E DESENVOLVIMENTO </th>
                        </tr>
                        <tr>
                          <th> Bebês (zero a 1 ano e 6 meses) </th>
                          <th> Crianças bem pequenas (1 ano
                            e 7 meses a 3 anos e 11 meses) </th>
                          <th> Crianças pequenas (4 anos a
                            5 anos e 11 meses) </th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td><strong> (EI01TS01)</strong> Explorar sons produzidos
                            com o próprio corpo e
                            com objetos do ambiente. </td>
                          <td><strong> (EI02TS01)</strong> Criar sons com materiais,
                            objetos e instrumentos
                            musicais, para acompanhar
                            diversos ritmos de música. </td>
                          <td><strong> (EI03TS01)</strong> Utilizar sons produzidos
                            por materiais, objetos e
                            instrumentos musicais
                            durante brincadeiras de
                            faz de conta, encenações,
                            criações musicais, festas. </td>
                        </tr>
                        <tr>
                          <td><strong> (EI01TS02)</strong> Traçar marcas gráficas,
                            em diferentes suportes,
                            usando instrumentos
                            riscantes e tintas. </td>
                          <td><strong> (EI02TS02)</strong> Utilizar materiais variados com
                            possibilidades de manipulação
                            (argila, massa de modelar),
                            explorando cores, texturas,
                            superfícies, planos, formas
                            e volumes ao criar objetos
                            tridimensionais. </td>
                          <td><strong> (EI03TS02)</strong> Expressar-se livremente
                            por meio de desenho,
                            pintura, colagem, dobradura
                            e escultura, criando
                            produções bidimensionais e
                            tridimensionais. </td>
                        </tr>
                        <tr>
                          <td><strong> (EI01TS03)</strong> Explorar diferentes fontes
                            sonoras e materiais para
                            acompanhar brincadeiras
                            cantadas, canções,
                            músicas e melodias. </td>
                          <td><strong>(EI02TS03)</strong> Utilizar diferentes fontes
                            sonoras disponíveis no
                            ambiente em brincadeiras
                            cantadas, canções, músicas e
                            melodias. </td>
                          <td><strong>(EI03TS03)</strong> Reconhecer as qualidades do
                            som (intensidade, duração,
                            altura e timbre), utilizando-as
                            em suas produções sonoras
                            e ao ouvir músicas e sons. </td>
                        </tr>
                      </tbody>
                    </table>
                    </p>
    </div>
    <div class="ls-modal-footer">
      <button class="ls-btn-primary" data-dismiss="modal">FECHAR</button>
    </div>
  </div>
</div>

<div class="ls-modal" id="modalLargeEF">
  <div class="ls-modal-large">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title">EF – Escuta, fala, pensamento e imaginação</h4>
    </div>
    <div class="ls-modal-body">
                    <p>
                    <table class="ls-table ls-sm-space">
                      <thead>
                        <tr>
                          <th colspan="3"> OBJETIVOS DE APRENDIZAGEM E DESENVOLVIMENTO </th>
                        </tr>
                        <tr>
                          <th> Bebês (zero a 1 ano e 6 meses) </th>
                          <th> Crianças bem pequenas (1 ano
                            e 7 meses a 3 anos e 11 meses) </th>
                          <th> Crianças pequenas (4 anos a
                            5 anos e 11 meses) </th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td><strong> (EI01EF01)</strong> Reconhecer quando é
                            chamado por seu nome
                            e reconhecer os nomes
                            de pessoas com quem
                            convive. </td>
                          <td><strong> (EI02EF01)</strong> Dialogar com crianças e
                            adultos, expressando seus
                            desejos, necessidades,
                            sentimentos e opiniões. </td>
                          <td><strong> (EI03EF01)</strong> Expressar ideias, desejos
                            e sentimentos sobre suas
                            vivências, por meio da
                            linguagem oral e escrita
                            (escrita espontânea), de
                            fotos, desenhos e outras
                            formas de expressão. </td>
                        </tr>
                        <tr>
                          <td><strong> (EI01EF02)</strong> Demonstrar interesse ao
                            ouvir a leitura de poemas
                            e a apresentação de
                            músicas. </td>
                          <td><strong>(EI02EF02)</strong> Identificar e criar diferentes
                            sons e reconhecer rimas e
                            aliterações em cantigas de
                            roda e textos poéticos. </td>
                          <td><strong> (EI03EF02)</strong> Inventar brincadeiras
                            cantadas, poemas e
                            canções, criando rimas,
                            aliterações e ritmos. </td>
                        </tr>
                        <tr>
                          <td><strong> (EI01EF03)</strong> Demonstrar interesse ao
                            ouvir histórias lidas ou
                            contadas, observando
                            ilustrações e os
                            movimentos de leitura do
                            adulto-leitor (modo de
                            segurar o portador e de
                            virar as páginas). </td>
                          <td><strong>(EI02EF03)</strong> Demonstrar interesse e
                            atenção ao ouvir a leitura
                            de histórias e outros textos,
                            diferenciando escrita de
                            ilustrações, e acompanhando,
                            com orientação do adulto-leitor, a direção da leitura (de
                            cima para baixo, da esquerda
                            para a direita). </td>
                          <td><strong>(EI03EF03)</strong> Escolher e folhear livros,
                            procurando orientar-se
                            por temas e ilustrações e
                            tentando identificar palavras
                            conhecidas. </td>
                        </tr>
                        <tr>
                          <td><strong>(EI01EF04)</strong> Reconhecer elementos das
                            ilustrações de histórias,
                            apontando-os, a pedido
                            do adulto-leitor. </td>
                          <td><strong>(EI02EF04)</strong> Formular e responder
                            perguntas sobre fatos da
                            história narrada, identificando
                            cenários, personagens e
                            principais acontecimentos. </td>
                          <td><strong>(EI03EF04)</strong> Recontar histórias ouvidas
                            e planejar coletivamente
                            roteiros de vídeos e de
                            encenações, definindo os
                            contextos, os personagens,
                            a estrutura da história. </td>
                        </tr>
                        <tr>
                          <td><strong>(EI01EF05)</strong> Imitar as variações de
                            entonação e gestos
                            realizados pelos adultos,
                            ao ler histórias e ao cantar. </td>
                          <td><strong>(EI02EF05)</strong> Relatar experiências e fatos
                            acontecidos, histórias ouvidas,
                            filmes ou peças teatrais
                            assistidos etc. </td>
                          <td><strong>(EI03EF05)</strong> Recontar histórias ouvidas
                            para produção de reconto
                            escrito, tendo o professor
                            como escriba. </td>
                        </tr>
                        <tr>
                          <td><strong>(EI01EF06)</strong> Comunicar-se com
                            outras pessoas usando
                            movimentos, gestos,
                            balbucios, fala e outras
                            formas de expressão. </td>
                          <td><strong>(EI02EF06)</strong> Criar e contar histórias
                            oralmente, com base em
                            imagens ou temas sugeridos. </td>
                          <td><strong>(EI03EF06)</strong> Produzir suas próprias
                            histórias orais e escritas
                            (escrita espontânea), em
                            situações com função social
                            significativa. </td>
                        </tr>
                        <tr>
                          <td><strong>(EI01EF07)</strong> Conhecer e manipular
                            materiais impressos e
                            audiovisuais em diferentes
                            portadores (livro, revista,
                            gibi, jornal, cartaz, CD, <em>tablet</em> etc.). </td>
                          <td><strong>(EI02EF07)</strong> Manusear diferentes
                            portadores textuais,
                            demonstrando reconhecer
                            seus usos sociais. </td>
                          <td><strong>(EI03EF07)</strong> Levantar hipóteses sobre
                            gêneros textuais veiculados
                            em portadores conhecidos,
                            recorrendo a estratégias de
                            observação gráfica e/ou de
                            leitura. </td>
                        </tr>
                        <tr>
                          <td><strong> (EI01EF08)</strong> Participar de situações
                            de escuta de textos
                            em diferentes gêneros
                            textuais (poemas,
                            fábulas, contos, receitas,
                            quadrinhos, anúncios etc.). </td>
                          <td><strong>(EI02EF08)</strong> Manipular textos e participar
                            de situações de escuta para
                            ampliar seu contato com
                            diferentes gêneros textuais
                            (parlendas, histórias de
                            aventura, tirinhas, cartazes de
                            sala, cardápios, notícias etc.). </td>
                          <td><strong>(EI03EF08)</strong> Selecionar livros e textos
                            de gêneros conhecidos para
                            a leitura de um adulto e/ou
                            para sua própria leitura
                            (partindo de seu repertório
                            sobre esses textos, como a
                            recuperação pela memória,
                            pela leitura das ilustrações
                            etc.). </td>
                        </tr>
                        <tr>
                          <td><strong>(EI01EF09)</strong> Conhecer e manipular
                            diferentes instrumentos e
                            suportes de escrita. </td>
                          <td><strong>(EI02EF09)</strong> Manusear diferentes
                            instrumentos e suportes de
                            escrita para desenhar, traçar
                            letras e outros sinais gráficos. </td>
                          <td><strong>(EI03EF09)</strong> Levantar hipóteses em
                            relação à linguagem escrita,
                            realizando registros de
                            palavras e textos, por meio
                            de escrita espontânea. </td>
                        </tr>
                      </tbody>
                    </table>
                    </p>
    </div>
    <div class="ls-modal-footer">
      <button class="ls-btn-primary" data-dismiss="modal">FECHAR</button>
    </div>
  </div>
</div>

<div class="ls-modal" id="modalLargeET">
  <div class="ls-modal-large">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title">ET – Espaços, tempos, quantidades, relações e transformações</h4>
    </div>
    <div class="ls-modal-body">
                    <p>
                    <table class="ls-table ls-sm-space">
                      <thead>
                        <tr>
                          <th colspan="3"> OBJETIVOS DE APRENDIZAGEM E DESENVOLVIMENTO </th>
                        </tr>
                        <tr>
                          <th> Bebês (zero a 1 ano e 6 meses) </th>
                          <th> Crianças bem pequenas (1 ano
                            e 7 meses a 3 anos e 11 meses) </th>
                          <th> Crianças pequenas (4 anos a
                            5 anos e 11 meses) </th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td><strong>(EI01ET01)</strong> Explorar e descobrir as
                            propriedades de objetos e
                            materiais (odor, cor, sabor,
                            temperatura). </td>
                          <td><strong> (EI02ET01)</strong> Explorar e descrever
                            semelhanças e diferenças
                            entre as características e
                            propriedades dos objetos
                            (textura, massa, tamanho). </td>
                          <td><strong>(EI03ET01)</strong> Estabelecer relações
                            de comparação entre
                            objetos, observando suas
                            propriedades. </td>
                        </tr>
                        <tr>
                          <td><strong>(EI01ET02)</strong> Explorar relações
                            de causa e efeito
                            (transbordar, tingir,
                            misturar, mover e remover
                            etc.) na interação com o
                            mundo físico. </td>
                          <td><strong>(EI02ET02)</strong> Observar, relatar e descrever
                            incidentes do cotidiano e
                            fenômenos naturais (luz solar,
                            vento, chuva etc.). </td>
                          <td><strong>(EI03ET02)</strong> Observar e descrever
                            mudanças em diferentes
                            materiais, resultantes
                            de ações sobre eles, em
                            experimentos envolvendo
                            fenômenos naturais e
                            artificiais. </td>
                        </tr>
                        <tr>
                          <td><strong>(EI01ET03)</strong> Explorar o ambiente
                            pela ação e observação,
                            manipulando,
                            experimentando e
                            fazendo descobertas. </td>
                          <td><strong>(EI02ET03)</strong> Compartilhar, com outras
                            crianças, situações de cuidado
                            de plantas e animais nos
                            espaços da instituição e fora
                            dela. </td>
                          <td><strong>(EI03ET03)</strong> Identificar e selecionar
                            fontes de informações, para
                            responder a questões sobre
                            a natureza, seus fenômenos,
                            sua conservação. </td>
                        </tr>
                        <tr> </tr>
                        <tr>
                          <td><strong>(EI01ET04)</strong> Manipular, experimentar,
                            arrumar e explorar
                            o espaço por meio
                            de experiências de
                            deslocamentos de si e dos
                            objetos. </td>
                          <td><strong>(EI02ET04)</strong> Identificar relações espaciais
                            (dentro e fora, em cima,
                            embaixo, acima, abaixo, entre
                            e do lado) e temporais (antes,
                            durante e depois). </td>
                          <td><strong>(EI03ET04)</strong> Registrar observações,
                            manipulações e medidas,
                            usando múltiplas linguagens
                            (desenho, registro por
                            números ou escrita
                            espontânea), em diferentes
                            suportes. </td>
                        </tr>
                        <tr>
                          <td><strong> (EI01ET05)</strong> Manipular materiais
                            diversos e variados para
                            comparar as diferenças e
                            semelhanças entre eles. </td>
                          <td><strong>(EI02ET05)</strong> Classificar objetos,
                            considerando determinado
                            atributo (tamanho, peso, cor,
                            forma etc.). </td>
                          <td><strong> (EI03ET05)</strong> Classificar objetos e figuras
                            de acordo com suas
                            semelhanças e diferenças. </td>
                        </tr>
                        <tr>
                          <td><strong> (EI01ET06)</strong> Vivenciar diferentes ritmos,
                            velocidades e fluxos nas
                            interações e brincadeiras
                            (em danças, balanços,
                            escorregadores etc.). </td>
                          <td><strong> (EI02ET06)</strong> Utilizar conceitos básicos de
                            tempo (agora, antes, durante,
                            depois, ontem, hoje, amanhã,
                            lento, rápido, depressa,
                            devagar). </td>
                          <td><strong> (EI03ET06)</strong> Relatar fatos importantes
                            sobre seu nascimento e
                            desenvolvimento, a história
                            dos seus familiares e da sua
                            comunidade. </td>
                        </tr>
                        <tr>
                          <td></td>
                          <td><strong>(EI02ET07)</strong> Contar oralmente objetos,
                            pessoas, livros etc., em
                            contextos diversos. </td>
                          <td><strong>(EI03ET07)</strong> Relacionar números às suas
                            respectivas quantidades
                            e identificar o antes, o
                            depois e o entre em uma
                            sequência. </td>
                        </tr>
                        <tr>
                          <td></td>
                          <td><strong>(EI02ET08)</strong> Registrar com números a
                            quantidade de crianças
                            (meninas e meninos, presentes
                            e ausentes) e a quantidade de
                            objetos da mesma natureza
                            (bonecas, bolas, livros etc.). </td>
                          <td><strong>(EI03ET08)</strong> Expressar medidas (peso,
                            altura etc.), construindo
                            gráficos básicos. </td>
                        </tr>
                      </tbody>
                    </table>
                    </p>
    </div>
    <div class="ls-modal-footer">
      <button class="ls-btn-primary" data-dismiss="modal">FECHAR</button>
    </div>
  </div>
</div>

<div class="ls-modal" id="modalLargeN">
  <div class="ls-modal-large">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title">Parte Diversificada</h4>
    </div>
    <div class="ls-modal-body">
                    <p>
                    
                    Não há descrição dos Objetivos de Aprendizagem e Desenvolvimento definidas pela BNCC na parte diversificada.

                    </p>
    </div>
    <div class="ls-modal-footer">
      <button class="ls-btn-primary" data-dismiss="modal">FECHAR</button>
    </div>
  </div>
</div>







<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script> 
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
<script src="js/sweetalert2.min.js"></script> 

<script src="https://cdn.tiny.cloud/1/iq1gy8qt7s1b9bj92c2fc7whie95augu8r8kz97dgoufkli8/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script> 
<script src="langs/pt_BR.js"></script> 
<script>
	

	tinymce.init({
	  selector: 'textarea',
	  resize: true,
	  theme_advanced_source_editor_width : '100%',
	  
	  mobile: {
      menubar: false
  },
	  
	  images_upload_url: 'postAcceptor.php',
	  automatic_uploads: true,
	  imagetools_proxy: 'proxy.php',
	  
	  //plugins: 'emoticons',
	  //toolbar: 'emoticons',
	  
	  //imagetools_toolbar: 'rotateleft rotateright | flipv fliph | editimage imageoptions',
	  	  
	  height: 200,
	  toolbar: ['paste undo redo | formatselect | forecolor | bold italic backcolor | bullist numlist | image | emoticons'],
	  plugins : ['textcolor','advlist autolink link image imagetools lists charmap print preview paste emoticons',
		'advlist autolink lists link image imagetools charmap print preview anchor',
		'searchreplace visualblocks code fullscreen',
		'insertdatetime media table paste code help wordcount'],
	  //force_br_newlines : false,
	  //force_p_newlines : false,
	  //forced_root_block : '',	
	  statusbar: false,
	  language: 'pt_BR',
	  menubar: false,
	  paste_as_text: true,
	  content_css: '//www.tinymce.com/css/codepen.min.css'
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
mysql_free_result($ac);
?>
