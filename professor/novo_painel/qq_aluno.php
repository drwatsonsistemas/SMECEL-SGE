<?php require_once('../../Connections/SmecelNovoPDO.php'); ?>
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
?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
<?php include "../../sistema/escola/fnc/calculos.php"; ?>
<?php


$ref = "rendimento_alunos";
if (isset($_GET['ref'])) {
  $ref = "rendimento_mapa_qq";
}

$queryPeriodo = "";
$colname_Periodo = "";
if(isset($_GET['periodo'])){
  $colname_Periodo = $_GET['periodo'];
  $queryPeriodo = "AND qq_id_periodo = '".GetSQLValueString($colname_Periodo, "int")."'";
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Periodos = "SELECT per_unid_id, per_unid_id_ano, per_unid_id_sec, per_unid_periodo, per_unid_data_inicio, per_unid_data_fim, per_unid_data_bloqueio, per_unid_hash FROM smc_unidades WHERE per_unid_id_ano = '$row_AnoLetivo[ano_letivo_id]' AND per_unid_id_sec = '$row_Secretaria[sec_id]' ORDER BY per_unid_periodo ASC";
$Periodos = mysql_query($query_Periodos, $SmecelNovo) or die(mysql_error());
$row_Periodos = mysql_fetch_assoc($Periodos);
$totalRows_Periodos = mysql_num_rows($Periodos);

$colname_Matricula = "-1";
if (isset($_GET['cod'])) {
  $colname_Matricula = $_GET['cod'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matricula = sprintf("
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
  WHERE vinculo_aluno_hash = %s", GetSQLValueString($colname_Matricula, "text"));
$Matricula = mysql_query($query_Matricula, $SmecelNovo) or die(mysql_error());
$row_Matricula = mysql_fetch_assoc($Matricula);
$totalRows_Matricula = mysql_num_rows($Matricula);

$colname_Disciplina = "-1";
if (isset($_GET['disciplina'])) {
  $colname_Disciplina = $_GET['disciplina'];
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
$query_Matriz = "SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash, matriz_dias_letivos, matriz_semanas_letivas, matriz_dias_semana, matriz_minutos_aula, matriz_aula_dia, matriz_criterio_avaliativo FROM smc_matriz WHERE matriz_id = '$row_Turma[turma_matriz_id]'";
$Matriz = mysql_query($query_Matriz, $SmecelNovo) or die(mysql_error());
$row_Matriz = mysql_fetch_assoc($Matriz);
$totalRows_Matriz = mysql_num_rows($Matriz);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Criterios = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes, ca_rec_paralela, ca_digitos FROM smc_criterios_avaliativos WHERE ca_id = '$row_Matriz[matriz_criterio_avaliativo]'";
$Criterios = mysql_query($query_Criterios, $SmecelNovo) or die(mysql_error());
$row_Criterios = mysql_fetch_assoc($Criterios);
$totalRows_Criterios = mysql_num_rows($Criterios);


function build_query_without_param($query_string, $param_to_remove) {
  parse_str($query_string, $params);
  unset($params[$param_to_remove]);
  return http_build_query($params);
}


function arredondarNota($nota) {
    $decimal = round($nota - floor($nota), 2); // Arredonda para 2 casas decimais
    
    if ($decimal >= 0.75) {
        return ceil($nota);
    } elseif ($decimal >= 0.5 && $decimal < 0.75) {
        return floor($nota) + 0.5;
    } elseif ($decimal >= 0.3 && $decimal < 0.5) {
        return floor($nota) + 0.5;
    } else {
        return floor($nota);
    }
}
$insertFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $insertFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {

  $hash = md5(date("YmdHis").$_POST['qq_id_matricula'].$row_Disciplina['disciplina_nome']);    
  
  

  if ($_POST['qualitativo'] != 0 && $_POST['quantitativo'] != 0) {
    // Se atividades são selecionadas em ambos os critérios
    $insertGoTo = "qq_aluno.php?erro";
    if (isset($_SERVER['QUERY_STRING'])) {
      $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
      $insertGoTo .= $_SERVER['QUERY_STRING'];
    }
    header(sprintf("Location: %s", $insertGoTo));
    exit;
  } elseif ($_POST['qualitativo'] != 0 && $_POST['quantitativo'] == 0) {
    // Se atividade é selecionada apenas em qualitativo
    $qq = $_POST['qualitativo'];
    if($_POST['periodo'] == '1' || $_POST['periodo'] == '2'){
      if($_POST['qq_nota'] > 16.5){

        $insertGoTo = "qq_aluno.php?erro";
        if (isset($_SERVER['QUERY_STRING'])) {
          $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
          $insertGoTo .= $_SERVER['QUERY_STRING'];
        }
        echo "<script>
        alert('Nota $_POST[qq_nota] digitada é maior que 16,5');
        window.location.href='$insertGoTo';
        </script>";
        exit;

      }
    }elseif($_POST['periodo'] == '3'){
      if($_POST['qq_nota'] > 22){

        $insertGoTo = "qq_aluno.php?erro";
        if (isset($_SERVER['QUERY_STRING'])) {
          $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
          $insertGoTo .= $_SERVER['QUERY_STRING'];
        }
        echo "<script>
        alert('Nota $_POST[qq_nota] digitada é maior que 22');
        window.location.href='$insertGoTo';
        </script>";
        exit;

      }
    }
  } elseif ($_POST['quantitativo'] != 0 && $_POST['qualitativo'] == 0) {
    // Se atividade é selecionada apenas em quantitativo
    $qq = $_POST['quantitativo'];
    if($_POST['periodo'] == '1' || $_POST['periodo'] == '2'){
      if($_POST['qq_nota'] > 13.5){

        $insertGoTo = "qq_aluno.php?erro";
        if (isset($_SERVER['QUERY_STRING'])) {
          $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
          $insertGoTo .= $_SERVER['QUERY_STRING'];
        }
        echo "<script>
        alert('Nota $_POST[qq_nota] digitada é maior que 13,5');
        window.location.href='$insertGoTo';
        </script>";
        exit;

      }
    }elseif($_POST['periodo'] == '3'){
      if($_POST['qq_nota'] > 18){

        $insertGoTo = "qq_aluno.php?erro";
        if (isset($_SERVER['QUERY_STRING'])) {
          $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
          $insertGoTo .= $_SERVER['QUERY_STRING'];
        }
        echo "<script>
        alert('Nota $_POST[qq_nota] digitada é maior que 18');
        window.location.href='$insertGoTo';
        </script>";
        exit;

      }
    }
  } else {
    // Trate qualquer outro caso aqui, se necessário
  }


  if(isset($_POST['qq_nota'])) {
    // Substitui todas as vírgulas por pontos na variável $_POST['qq_nota']
    $_POST['qq_nota'] = str_replace(',', '.', $_POST['qq_nota']);
  }


  $insertSQL = sprintf("INSERT INTO smc_notas_qq (qq_id_matricula, qq_id_componente, qq_id_periodo, qq_tipo_criterio, qq_id_criterio, qq_nota, qq_nota_hash) VALUES (%s, %s, %s,%s,%s,%s, '$hash')",
   GetSQLValueString($_POST['qq_id_matricula'], "int"),
   GetSQLValueString($colname_Disciplina, "int"),
   GetSQLValueString($_POST['periodo'], "int"),
   GetSQLValueString($_POST['criterio'], "int"),
   GetSQLValueString($qq, "int"),
   GetSQLValueString($_POST['qq_nota'], "double")
                       //GetSQLValueString($_POST['per_unid_hash'], "text")
 );
  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

  $insertGoTo = "qq_aluno.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
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
  <link rel="stylesheet" type="text/css" href="css/locastyle.css">  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

   .align-items-center {
    display: flex;
    align-items: center; /* Isso alinha verticalmente os itens */
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
    <?php if(isset($_GET['erro'])){ ?>
      <div class="ls-alert-warning"><strong>Ops:</strong> Ocorreu um erro ao adicionar a pontuação. Tente novamente.</div>
    <?php } ?>
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
    <!--<a href="javascript:void()" class="ls-btn ls-txt-right ls-float-right recarregar">Recalcular</a> -->
    <?php if ($row_Matricula['vinculo_aluno_boletim']=="1") { ?>


      <?php if ( $row_Matricula['vinculo_aluno_situacao']<>"1") { ?>
       <div class="ls-alert-info"><strong>Atenção:</strong> Você não poderá alterar as notas deste(a) aluno(a) pois o status está como <?php echo $row_Matricula['vinculo_aluno_situacao_nome']; ?>.</div>
     <?php } ?>



     <ul class="ls-tabs-nav">
      <?php 
      $periodoPadrao = '1';
      mysql_data_seek($Periodos, 0);
      while ($row_Periodos = mysql_fetch_assoc($Periodos)){ 
    // Verifica se o índice 'periodo' está definido em $_GET
        $periodoAtivo = isset($_GET['periodo']) ? $_GET['periodo'] : '';
        $isActive = ($periodoAtivo == $row_Periodos['per_unid_periodo']) || ($periodoAtivo == '' && $colname_Periodo == $periodoPadrao);
        ?>
        <li class="<?= $isActive ? 'ls-active' : '' ?>" id="track<?= $row_Periodos['per_unid_periodo'] ?>">
          <a data-ls-module="tabs" href="#track<?= $row_Periodos['per_unid_periodo'] ?>" class="tab-link" data-periodo="<?= $row_Periodos['per_unid_periodo'] ?>">
            <?= $row_Periodos['per_unid_periodo'] ?>° PERÍODO
          </a>
        </li>
      <?php } ?>
    </ul>


    <script>
      $(document).ready(function() {
        $('.ls-tabs-nav a.tab-link').on('click', function(event) {
    event.preventDefault(); // Prevent default anchor behavior
    var periodo = $(this).data('periodo'); // Get the value of 'periodo' attribute
    var queryString = window.location.search; // Obter a string de consulta atual

    // Verificar se o parâmetro 'periodo' já está presente na URL
    if (queryString.indexOf('periodo=') === -1) {
      // Se 'periodo' não estiver presente, adicione-o à string de consulta
      queryString += (queryString ? '&' : '?') + 'periodo=' + periodo;
    } else {
      // Se 'periodo' já estiver presente, substitua-o na string de consulta
      queryString = queryString.replace(/([?&])periodo=[^&]*(&|$)/, '$1periodo=' + periodo + '$2');
    }

    // Recarregar a página com a nova string de consulta
    window.location.href = window.location.pathname + queryString;
  });
      });
    </script>

    <div class="ls-tabs-container">
      <?php 
      mysql_data_seek($Periodos, 0);
      $totalQualitativo = 0;
      $totalQuantitativo = 0;
      $totalTrimestre = 0;
      while ($row_Periodos = mysql_fetch_assoc($Periodos)){ 

        $query_qualitativo = sprintf("SELECT qq_id, qq_id_matricula, qq_id_componente, qq_id_periodo, qq_tipo_criterio, qq_id_criterio, qq_nota 
          FROM smc_notas_qq 
          WHERE qq_id_matricula = %s AND qq_id_componente = %s AND qq_id_periodo = %s
          AND qq_tipo_criterio='1'", 
          GetSQLValueString($row_Matricula['vinculo_aluno_id'], "int"), 
          GetSQLValueString($row_Disciplina['disciplina_id'], "int"),
          GetSQLValueString($row_Periodos['per_unid_periodo'], "int"));
        $qualitativo = mysql_query($query_qualitativo, $SmecelNovo) or die(mysql_error());
        $totalRows_qualitativo = mysql_num_rows($qualitativo); 

        //QUANTITATIVO
        $query_quantitativo = sprintf("SELECT qq_id, qq_id_matricula, qq_id_componente, qq_id_periodo, qq_tipo_criterio, qq_id_criterio, qq_nota 
          FROM smc_notas_qq 
          WHERE qq_id_matricula = %s AND qq_id_componente = %s AND qq_id_periodo = %s
          AND qq_tipo_criterio='2'", 
          GetSQLValueString($row_Matricula['vinculo_aluno_id'], "int"), 
          GetSQLValueString($row_Disciplina['disciplina_id'], "int"),
          GetSQLValueString($row_Periodos['per_unid_periodo'], "int"));
        $quantitativo = mysql_query($query_quantitativo, $SmecelNovo) or die(mysql_error());
        $totalRows_quantitativo = mysql_num_rows($quantitativo); 

        $pontosQualitativo = "";
        $pontosQuantitativo = "";
        $somaPontuacaoQualitativo="";
        $somaPontuacaoQuantitativo="";
        switch ($row_Periodos['per_unid_periodo']) {
          case '1':
          $pontosQualitativo = 16.5;
          $pontosQuantitativo = 13.5;
          break;
          case '2':
          $pontosQualitativo = 16.5;
          $pontosQuantitativo = 13.5;
          break;
          case '3':
          $pontosQualitativo = 22;
          $pontosQuantitativo = 18;
          break;
        }

        //PARALELA
        $query_paralela = sprintf("SELECT qq_id, qq_id_matricula, qq_id_componente, qq_id_periodo, qq_tipo_criterio, qq_id_criterio, qq_nota 
          FROM smc_notas_qq 
          WHERE qq_id_matricula = %s AND qq_id_componente = %s AND qq_id_periodo = %s
          AND qq_tipo_criterio='3'", 
          GetSQLValueString($row_Matricula['vinculo_aluno_id'], "int"), 
          GetSQLValueString($row_Disciplina['disciplina_id'], "int"),
          GetSQLValueString($row_Periodos['per_unid_periodo'], "int"));
        $paralela = mysql_query($query_paralela, $SmecelNovo) or die(mysql_error());
        $row_paralela = mysql_fetch_assoc($paralela);
        $totalRows_paralela = mysql_num_rows($paralela); 

        ?>

        <div class="ls-box ls-tab-content <?= (!isset($_GET['periodo']) && $row_Periodos['per_unid_periodo'] == '1') ? 'ls-active' : ($_GET['periodo'] == $row_Periodos['per_unid_periodo'] ? 'ls-active' : '') ?>" id="track<?= $row_Periodos['per_unid_periodo'] ?>">
          <div class="align-items-center">
            <h5 class="ls-title-3"><?= $row_Periodos['per_unid_periodo'] ?>° PERÍODO</h5>
            <a href="#" style="margin-left:3px;margin-bottom:5px" class="ls-ico-help" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="O <?= $row_Periodos['per_unid_periodo'] ?>° período tem um máximo de <?= $pontosQuantitativo + $pontosQualitativo ?> pontos (qualitativo <?= $pontosQualitativo ?> e quantitativo <?= $pontosQuantitativo ?>)"></a>

          </div>
          <br>
          <div class="row">
            <div class="ls-box col-md-6 ">

              <h5 class="ls-title-6" style="display: inline-block;">ASPECTO QUALITATIVO</h5>
              
              <button id="notaQualitativo<?= $row_Periodos['per_unid_periodo']?>" class="ls-ico-plus ls-btn-primary ls-btn-xs ls-float-right" style="display: inline-block;">INSERIR QUALITATIVO</button>

              <div id="boxQualitativo<?= $row_Periodos['per_unid_periodo']?>" style="display: none;" class="ls-box ls-md-margin-top">
                <form method="post" name="form2" action="<?php echo $insertFormAction; ?>" class="ls-form ls-form-horizontal row">
                  <div class="ls-modal-body" id="myModalBody">

                    <div class="ls-label col-md-12" id="aspecto_qualitativo">
                      <label class="ls-label">
                        <b class="ls-label-text">ASPECTOS QUALITATIVOS</b>
                        <div class="ls-custom-select">
                          <select name="qualitativo" class="ls-select">
                            <option value="0">-- SELECIONE --</option>
                            <option value="12">NOTA QUALITATIVA</option>
                            <option value="1">ATIVIDADE DE SALA</option>
                            <option value="2">ATIVIDADES EXTRACLASSE</option>
                            <option value="3">COMPORTAMENTO</option>
                            <option value="4">DESEMPENHO NA LEITURA E ESCRITA</option>
                            <option value="5">INTERAÇÃO</option>
                            <option value="6">ORALIDADE</option>
                            <option value="7">ORGANIZAÇÃO</option>
                            <option value="8">OUTROS</option>
                            <option value="9">PARTICIPAÇÃO</option>
                            <option value="10">TRABALHO EM GRUPO</option>
                            <option value="11">TRABALHO INDIVIDUAL</option>
                          </select>
                        </div>
                      </label>
                    </div>


                    <div class="ls-label col-md-12" id="notaqq">
                      <label class="ls-label">
                        <b class="ls-label-text">PONTUAÇÃO</b>
                        <input type="text" name="qq_nota" placeholder="Pontuação do aluno" class="ls-field" required>
                      </label>
                    </div>

                  </div>
                  <div class="ls-modal-footer">
                    <button class="ls-btn ls-float-right" data-dismiss="modal">FECHAR</button>
                    <button type="submit" class="ls-btn-primary">REGISTRAR PONTUAÇÃO</button>
                    <input type="hidden" name="qq_id_matricula" value="<?= $row_Matricula['vinculo_aluno_id'] ?>">
                    <input type="hidden" name="criterio" value="1" id="qualitativo" class="ls-field-radio">
                    <input type="hidden" name="periodo" value="<?= $row_Periodos['per_unid_periodo']?>" id="periodo" class="ls-field-radio">
                    <input type="hidden" name="MM_insert" value="form2">
                  </div>
                </form>
              </div>

              <script>
                $(document).ready(function() {
                  $('#notaQualitativo<?= $row_Periodos['per_unid_periodo']?>').on('click', function() {
                    $( "#boxQualitativo<?= $row_Periodos['per_unid_periodo']?>" ).show( "slow");
                    $( "#notaQualitativo<?= $row_Periodos['per_unid_periodo']?>" ).hide();
                  });
                });
              </script>

              <?php if($totalRows_qualitativo > 0){ 
                $somaPontuacaoQualitativo = 0; 
                ?>
                <table class="ls-table ls-sm-space">
                  <thead>
                    <tr>
                      <th >CRITÉRIO AVALIATIVO</th>
                      <th width="100">PONTUAÇÃO</th>
                      <th class="ls-txt-center" width="70px"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php while($row_qualitativo = mysql_fetch_assoc($qualitativo)) { 
                     $atividades_qq = "";
                     if($row_qualitativo['qq_tipo_criterio'] == '1'){
                      $atividades_qq = array(
                        0 => "-- SELECIONE --",
                        1 => "ATIVIDADE DE SALA",
                        2 => "ATIVIDADES EXTRACLASSE",
                        3 => "COMPORTAMENTO",
                        4 => "DESEMPENHO NA LEITURA E ESCRITA",
                        5 => "INTERAÇÃO",
                        6 => "ORALIDADE",
                        7 => "ORGANIZAÇÃO",
                        8 => "OUTROS",
                        9 => "PARTICIPAÇÃO",
                        10 => "TRABALHO EM GRUPO",
                        11 => "TRABALHO INDIVIDUAL",
                        12 => "NOTA QUALITATIVA"
                      );
                    }elseif($row_qualitativo['qq_tipo_criterio']=='2'){
                      $atividades_qq = array(
                        0 => "-- SELECIONE --",
                        1 => "1º AVALIAÇÃO ESCRITA",
                        2 => "2º AVALIAÇÃO ESCRITA",
                        3 => "3º AVALIAÇÃO ESCRITA",
                        4 => "4º AVALIAÇÃO (TRABALHOS-PESQUISAS)",
                        5 => "5º AVALIAÇÃO (TRABALHOS-PESQUISAS)",
                        6 => "NOTA QUANTITATIVA"
                      );
                    }
                    $somaPontuacaoQualitativo += $row_qualitativo['qq_nota'];
                    ?>
                    <tr>
                      <td><?= $atividades_qq[$row_qualitativo['qq_id_criterio']] ?></td>
                      <td><?= $row_qualitativo['qq_nota'] ?></td>
                      <td class="ls-txt-center">
                        <a id="excluirQualitativo<?php echo $row_qualitativo['qq_id']; ?>" data-id="<?php echo $row_qualitativo['qq_id']; ?>" data-matricula="<?php echo $row_qualitativo['qq_id_matricula']; ?>" class="ls-ico-cancel-circle ls-divider ls-color-danger"></a>
                      </td>
                    </tr>

                    <script>
                      $(document).ready(function() {
                        $("#excluirQualitativo<?php echo $row_qualitativo['qq_id']; ?>").on("click", function() {
                          var idQualitativo = $(this).data("id");
                          var idMatricula = $(this).data("matricula");
                          var resposta = confirm("Excluir pontuação?");
                          if (resposta == true) {
                            jQuery.ajax({
                              type: "POST",
                              url: "excluirPontuacaoQQ.php",
                              data: {id: idQualitativo, matricula:idMatricula},
                              success: function (data)
                              {
                                location.reload();
                              //$('#status').html(data);
                              }
                            });
                          }
                        });
                      });
                    </script>
                  <?php } ?>
                  <tr>
                    <td><strong>Total:</strong></td>
                    <td><strong><?= $somaPontuacaoQualitativo ?></strong></td>
                    <td></td>
                  </tr>
                </tbody>
              </table>
            <?php } else { ?>
              <p>Nenhuma pontuação cadastrada para esse período</p>
            <?php } ?>

          </div>
          <div class="ls-box col-md-6">
            <h5 class="ls-title-6" style="display: inline-block;">ASPECTO QUANTITATIVO</h5>
            <button id="notaQuantitativo<?= $row_Periodos['per_unid_periodo']?>" class="ls-ico-plus ls-btn-primary ls-btn-xs ls-float-right" style="display: inline-block;">INSERIR QUANTITATIVO</button>

            <div id="boxQuantitativo<?= $row_Periodos['per_unid_periodo']?>" style="display: none;" class="ls-box ls-md-margin-top">
              <form method="post" name="form2" action="<?php echo $insertFormAction; ?>" class="ls-form ls-form-horizontal row">
                <div class="ls-modal-body" id="myModalBody">

                  <div class="ls-label col-md-12" id="aspecto_quantitativo">
                    <label class="ls-label">
                      <b class="ls-label-text">ASPECTOS QUANTITATIVOS</b>
                      <div class="ls-custom-select">
                        <select name="quantitativo" class="ls-select">
                          <option value="0">-- SELECIONE --</option>
                          <option value="6">NOTA QUANTITATIVA</option>
                          <option value="1">1º AVALIAÇÃO ESCRITA</option>
                          <option value="2">2º AVALIAÇÃO ESCRITA</option>
                          <option value="3">3º AVALIAÇÃO ESCRITA</option>
                          <option value="4">4º AVALIAÇÃO (TRABALHOS-PESQUISAS)</option>
                          <option value="5">5º AVALIAÇÃO (TRABALHOS-PESQUISAS</option>
                        </select>
                      </div>
                    </label>
                  </div>

                  <div class="ls-label col-md-12" id="notaqq">
                    <label class="ls-label">
                      <b class="ls-label-text">PONTUAÇÃO</b>
                      <input type="text" name="qq_nota" placeholder="Pontuação do aluno" class="ls-field" required>
                    </label>
                  </div>

                </div>
                <div class="ls-modal-footer">
                  <button class="ls-btn ls-float-right" data-dismiss="modal">FECHAR</button>
                  <button type="submit" class="ls-btn-primary">REGISTRAR PONTUAÇÃO</button>
                  <input type="hidden" name="qq_id_matricula" value="<?= $row_Matricula['vinculo_aluno_id'] ?>">
                  <input type="hidden" name="periodo" value="<?= $row_Periodos['per_unid_periodo']?>" id="periodo" class="ls-field-radio">
                  <input type="hidden" name="criterio" value="2" id="quantitativo" class="ls-field-radio">
                  <input type="hidden" name="MM_insert" value="form2">
                </div>
              </form>
            </div>

            <script>
              $(document).ready(function() {
                $('#notaQuantitativo<?= $row_Periodos['per_unid_periodo']?>').on('click', function() {
                  $( "#boxQuantitativo<?= $row_Periodos['per_unid_periodo']?>" ).show( "slow");
                  $( "#notaQuantitativo<?= $row_Periodos['per_unid_periodo']?>" ).hide();
                });
              });
            </script>


            <?php if($totalRows_quantitativo > 0){ 
              $somaPontuacaoQuantitativo = 0; 
              ?>
              <table class="ls-table ls-sm-space">
                <thead>
                  <tr>
                    <th >CRITÉRIO AVALIATIVO</th>
                    <th width="100">PONTUAÇÃO</th>
                    <th class="ls-txt-center" width="70px"></th>
                  </tr>
                </thead>
                <tbody>
                  <?php while($row_quantitativo = mysql_fetch_assoc($quantitativo)) { 
                   $atividades_qq = "";
                   if($row_quantitativo['qq_tipo_criterio'] == '1'){
                    $atividades_qq = array(
                      0 => "-- SELECIONE --",
                      1 => "ATIVIDADE DE SALA",
                      2 => "ATIVIDADES EXTRACLASSE",
                      3 => "COMPORTAMENTO",
                      4 => "DESEMPENHO NA LEITURA E ESCRITA",
                      5 => "INTERAÇÃO",
                      6 => "ORALIDADE",
                      7 => "ORGANIZAÇÃO",
                      8 => "OUTROS",
                      9 => "PARTICIPAÇÃO",
                      10 => "TRABALHO EM GRUPO",
                      11 => "TRABALHO INDIVIDUAL",
                      12 => "NOTA QUALITATIVA"
                    );
                  }elseif($row_quantitativo['qq_tipo_criterio']=='2'){
                    $atividades_qq = array(
                      0 => "-- SELECIONE --",
                      1 => "1º AVALIAÇÃO ESCRITA",
                      2 => "2º AVALIAÇÃO ESCRITA",
                      3 => "3º AVALIAÇÃO ESCRITA",
                      4 => "4º AVALIAÇÃO (TRABALHOS-PESQUISAS)",
                      5 => "5º AVALIAÇÃO (TRABALHOS-PESQUISAS)",
                      6 => "NOTA QUANTITATIVA"
                    );
                  }

                  $somaPontuacaoQuantitativo += $row_quantitativo['qq_nota'];
                  ?>
                  <tr>
                    <td><?= $atividades_qq[$row_quantitativo['qq_id_criterio']] ?></td>
                    <td><?= $row_quantitativo['qq_nota'] ?></td>
                    <td class="ls-txt-center">
                      <a id="excluirQuantitativo<?php echo $row_quantitativo['qq_id']; ?>" data-id="<?php echo $row_quantitativo['qq_id']; ?>" data-matricula="<?php echo $row_quantitativo['qq_id_matricula']; ?>" class="ls-ico-cancel-circle ls-divider ls-color-danger"></a>
                    </td>
                  </tr>
                  <script>
                    $(document).ready(function() {
                      $("#excluirQuantitativo<?php echo $row_quantitativo['qq_id']; ?>").on("click", function() {
                        var idQuantitativo = $(this).data("id");
                        var idMatricula = $(this).data("matricula");
                        var resposta = confirm("Excluir pontuação?");
                        if (resposta == true) {
                          jQuery.ajax({
                            type: "POST",
                            url: "excluirPontuacaoQQ.php",
                            data: {id: idQuantitativo, matricula:idMatricula},
                            success: function (data)
                            {
                              location.reload();
                              //$('#status').html(data);
                            }
                          });
                        }
                      });
                    });
                  </script>
                <?php } ?>
                <tr>
                  <td><strong>Total:</strong></td>
                  <td><strong><?= $somaPontuacaoQuantitativo ?></strong></td>
                  <td></td>
                </tr>

              </tbody>

            </table>

          <?php } else { ?>
            <p>Nenhuma pontuação cadastrada para esse período</p>
          <?php } ?>
        </div>
        <button data-ls-module="modal" data-target="#modalParalela<?= $row_Periodos['per_unid_periodo']?>" class="ls-btn-primary ls-float-right" style="margin-bottom: 10px;">INSERIR PARALELA</button>

        <div class="ls-modal" id="modalParalela<?= $row_Periodos['per_unid_periodo']?>">
          <div class="ls-modal-box">
            <div class="ls-modal-header">
              <button data-dismiss="modal">&times;</button>
              <h4 class="ls-modal-title">INSERIR PARALELA - <?= $row_Periodos['per_unid_periodo']?>° PERÍODO</h4>
            </div>
            <form method="post" name="form2" action="<?php echo $insertFormAction; ?>" class="ls-form ls-form-horizontal row">
              <div class="ls-modal-body" id="myModalBody">

                <div class="ls-modal-body" id="myModalBody">


                  <div class="ls-label col-md-12" id="notaqq">
                    <label class="ls-label">
                      <b class="ls-label-text">PARALELA</b>
                      <input type="text" name="qq_nota" placeholder="Pontuação de paralela" class="ls-field" required>
                    </label>
                  </div>

                </div>

              </div>
              <div class="ls-modal-footer">
                <button class="ls-btn ls-float-right" data-dismiss="modal">FECHAR</button>
                <button type="submit" class="ls-btn-primary">REGISTRAR PARALELA</button>
                <input type="hidden" name="qq_id_matricula" value="<?= $row_Matricula['vinculo_aluno_id'] ?>">
                <input type="hidden" name="periodo" value="<?= $row_Periodos['per_unid_periodo']?>" id="periodo" class="ls-field-radio">
                <input type="hidden" name="criterio" value="3" id="quantitativo" class="ls-field-radio">
                <input type="hidden" name="MM_insert" value="form2">
              </div>
            </form>
          </div>
          
        </div>

        <?php if($totalRows_qualitativo > 0 AND $totalRows_quantitativo > 0){  ?>
          <div class="ls-box col-md-12">
            <?php  if($totalRows_paralela > 0){ ?>
              <table class="ls-table">
                <tbody>
                  <tr>
                    <td><strong>Paralela:</strong></td>
                    <td><strong><?= $row_paralela['qq_nota'] ?></strong></td>
                    <td>
                      <a id="excluirParalela<?= $row_Periodos['per_unid_periodo'] ?>" data-id="<?php echo $row_paralela['qq_id']; ?>" data-matricula="<?php echo $row_paralela['qq_id_matricula']; ?>" class="ls-ico-cancel-circle ls-divider ls-color-danger"></a>
                    </td>
                  </tr>
                </tbody>
              </table>
            <?php } ?>
            <?php 
            $totalTrimestre = $somaPontuacaoQuantitativo + $somaPontuacaoQualitativo;
            if ($row_Periodos['per_unid_periodo'] == '1' OR $row_Periodos['per_unid_periodo'] == '2') {
              if ($somaPontuacaoQuantitativo >= 8.1 && $totalTrimestre < 18) {
                echo '<div class="ls-alert-warning"><strong>Reprovado.</div>';
              } elseif ($somaPontuacaoQuantitativo < 8.1 && $totalTrimestre < 18) {
                if ($totalRows_paralela > 0) {
                  if ($row_paralela['qq_nota'] > $somaPontuacaoQuantitativo) {
                    $totalTrimestre = $somaPontuacaoQualitativo + $row_paralela['qq_nota'];
                    if ($totalTrimestre >= 18) {
                      echo '<div class="ls-alert-success">Aprovado pela paralela.</div>';
                    } else {
                      echo '<div class="ls-alert-warning"><strong>Atenção:</strong> O aluno não obteve nota para passar pela paralela.</div>';
                    }
                  } else {
                    echo '<div class="ls-alert-warning"><strong>Atenção:</strong> Reprovado pela paralela.</div>';
                  }
                } else {
                  echo '<div class="ls-alert-warning"><strong>Atenção:</strong> O aluno está na paralela.</div>';
                }
              } else {
                echo '<div class="ls-alert-success">Aluno aprovado.</div>';
              }
            }


            if ($row_Periodos['per_unid_periodo'] == '3') {
              if ($somaPontuacaoQuantitativo >= 10.8 && $totalTrimestre < 24) {
                echo '<div class="ls-alert-warning"><strong>Reprovado.</div>';
              } elseif ($somaPontuacaoQuantitativo < 10.8 && $totalTrimestre < 24) {
                if ($totalRows_paralela > 0) {
                  if ($row_paralela['qq_nota'] > $somaPontuacaoQuantitativo) {
                    $totalTrimestre = $somaPontuacaoQualitativo + $row_paralela['qq_nota'];
                    if ($totalTrimestre >= 24) {
                      echo '<div class="ls-alert-success">Aprovado pela paralela.</div>';
                    } else {
                      echo '<div class="ls-alert-warning"><strong>Atenção:</strong> O aluno não obteve nota para passar pela paralela.</div>';
                    }
                  } else {
                    echo '<div class="ls-alert-warning"><strong>Atenção:</strong> Reprovado pela paralela.</div>';
                  }
                } else {
                  echo '<div class="ls-alert-warning"><strong>Atenção:</strong> O aluno está na paralela.</div>';
                }
              } else {
                echo '<div class="ls-alert-success">Aluno aprovado.</div>';
              }
            }



            ?>
            <p>Total trimestre: <?= arredondarNota($totalTrimestre) ?><?= $totalRows_paralela > 0 ? " (paralela: $row_paralela[qq_nota])" : '' ?></p>
          </div>
          <script>
            $(document).ready(function() {
              $("#excluirParalela<?= $row_Periodos['per_unid_periodo'] ?>").on("click", function() {
                var idQuantitativo = $(this).data("id");
                var idMatricula = $(this).data("matricula");
                var resposta = confirm("Excluir pontuação?");
                if (resposta == true) {
                  jQuery.ajax({
                    type: "POST",
                    url: "excluirPontuacaoQQ.php",
                    data: {id: idQuantitativo, matricula:idMatricula},
                    success: function (data)
                    {
                      location.reload();
                              //$('#status').html(data);
                    }
                  });
                }
              });
            });
          </script>
        <?php } ?>
      </div>


    </div>
  <?php } ?>
</div>
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
<script src="js/locastyle.js"></script> 


<script type="text/javascript">

/*
  <?php if ( $row_Matricula['vinculo_aluno_situacao']=="1") { ?>


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

  <?php } ?>		*/		

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
<?php
?>
