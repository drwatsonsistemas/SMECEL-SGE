<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include('../../sistema/funcoes/inverteData.php'); ?>
<?php include('../../sistema/funcoes/url_base.php'); ?>
<?php include('../../sistema/funcoes/idade.php'); ?>
<?php include('../../sistema/funcoes/anti_injection.php'); ?>
<?php include "../../sistema/escola/fnc/calculos.php"; ?>

<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);

  $logoutGoTo = "../index.php?saiu";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "6";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && false) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "../index.php?err";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
    $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
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


$colname_AlunoLogado = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_AlunoLogado = $_SESSION['MM_Username'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AlunoLogado = sprintf("
  SELECT *
  FROM smc_aluno
  WHERE aluno_id = %s", GetSQLValueString($colname_AlunoLogado, "int"));
$AlunoLogado = mysql_query($query_AlunoLogado, $SmecelNovo) or die(mysql_error());
$row_AlunoLogado = mysql_fetch_assoc($AlunoLogado);
$totalRows_AlunoLogado = mysql_num_rows($AlunoLogado);
if($totalRows_AlunoLogado=="") {
	header("Location:../index.php?loginErr");
}




//$colname_Boletim = $row_Matricula['vinculo_aluno_hash'];
if (isset($_GET['codigo'])) {
	
 $colname_Boletim = anti_injection($_GET['codigo']);

 mysql_select_db($database_SmecelNovo, $SmecelNovo);
 $query_Matricula = "
 SELECT 
 
 vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, 
 vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, 
 vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, vinculo_aluno_vacina_data_retorno, vinculo_aluno_dependencia,
 escola_id, escola_nome,
turma_id, turma_nome, turma_turno, turma_etapa, 
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno_nome  
 
 FROM smc_vinculo_aluno 
 
 INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma 
INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola 

 WHERE vinculo_aluno_hash = '$colname_Boletim' AND vinculo_aluno_dependencia = 'N' ORDER BY vinculo_aluno_id DESC LIMIT 0,1";
 $Matricula = mysql_query($query_Matricula, $SmecelNovo) or die(mysql_error());
 $row_Matricula = mysql_fetch_assoc($Matricula);
 $totalRows_Matricula = mysql_num_rows($Matricula);

} else {

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $query_Matricula = "
  
  SELECT 
  
  vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, 
  vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, 
  vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, vinculo_aluno_vacina_data_retorno, vinculo_aluno_dependencia,
  escola_id, escola_nome,
turma_id, turma_nome, turma_turno, turma_etapa, 
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno_nome   
  
  FROM smc_vinculo_aluno 

  INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma 
INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola 
  
  WHERE vinculo_aluno_id_aluno = '$row_AlunoLogado[aluno_id]' AND vinculo_aluno_dependencia = 'N' ORDER BY vinculo_aluno_id DESC LIMIT 0,1";
  $Matricula = mysql_query($query_Matricula, $SmecelNovo) or die(mysql_error());
  $row_Matricula = mysql_fetch_assoc($Matricula);
  $totalRows_Matricula = mysql_num_rows($Matricula);

  $colname_Boletim = $row_Matricula['vinculo_aluno_hash'];
  
  
}



mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AnoLetivo = "
SELECT ano_letivo_id, ano_letivo_ano, ano_letivo_inicio, ano_letivo_fim, ano_letivo_aberto, ano_letivo_id_sec, ano_letivo_resultado_final
FROM smc_ano_letivo 
WHERE ano_letivo_aberto = 'S' AND ano_letivo_id_sec = '$row_Matricula[vinculo_aluno_id_sec]'
ORDER BY ano_letivo_ano DESC LIMIT 1";
$AnoLetivo = mysql_query($query_AnoLetivo, $SmecelNovo) or die(mysql_error());
$row_AnoLetivo = mysql_fetch_assoc($AnoLetivo);
$totalRows_AnoLetivo = mysql_num_rows($AnoLetivo); 

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Boletim = sprintf("
  SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, 
  vinculo_aluno_boletim, aluno_id, aluno_nome, aluno_nascimento, turma_id, turma_nome, turma_etapa, turma_matriz_id, etapa_id, etapa_id_filtro, matriz_id, matriz_criterio_avaliativo, ca_id, ca_questionario_conceitos
  FROM smc_vinculo_aluno
  INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
  INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
  LEFT JOIN smc_etapa ON etapa_id = turma_etapa
  LEFT JOIN smc_matriz ON matriz_id = turma_matriz_id
  LEFT JOIN smc_criterios_avaliativos ON ca_id = matriz_criterio_avaliativo 
  WHERE vinculo_aluno_hash = %s", GetSQLValueString($colname_Boletim, "text"));
$Boletim = mysql_query($query_Boletim, $SmecelNovo) or die(mysql_error());
$row_Boletim = mysql_fetch_assoc($Boletim);
$totalRows_Boletim = mysql_num_rows($Boletim);



if($totalRows_Boletim=="") {
	header("Location:index.php?erro");
}

if ($row_Boletim['etapa_id_filtro'] == 1) {
  header("Location:boletimConceitos.php");
}

if ($row_Boletim['ca_questionario_conceitos']=="S") { 
  header("Location:boletimConceitosEF.php");
}


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AlunoBoletim = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia,
aluno_id, aluno_nome, aluno_nascimento, aluno_filiacao1, aluno_foto, aluno_hash,
turma_id, turma_nome, turma_matriz_id, turma_turno, turma_etapa, escola_id, escola_libera_boletim
FROM 
smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola 
WHERE vinculo_aluno_hash = '$row_Matricula[vinculo_aluno_hash]'";
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
$query_CriteriosAvaliativos = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes, ca_rec_paralela, ca_digitos FROM smc_criterios_avaliativos WHERE ca_id = '$row_Matriz[matriz_criterio_avaliativo]'";
$CriteriosAvaliativos = mysql_query($query_CriteriosAvaliativos, $SmecelNovo) or die(mysql_error());
$row_CriteriosAvaliativos = mysql_fetch_assoc($CriteriosAvaliativos);
$totalRows_CriteriosAvaliativos = mysql_num_rows($CriteriosAvaliativos);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Disciplinas = "
SELECT boletim_id, boletim_id_vinculo_aluno, boletim_id_disciplina, boletim_1v1, boletim_2v1, boletim_3v1, boletim_1v2, boletim_2v2, boletim_3v2, boletim_1v3, boletim_2v3, boletim_3v3, boletim_1v4, boletim_2v4, boletim_3v4, boletim_af, boletim_conselho,
disciplina_id, disciplina_nome 
FROM smc_boletim_disciplinas
INNER JOIN smc_disciplina ON disciplina_id = boletim_id_disciplina
WHERE boletim_id_vinculo_aluno = $row_Boletim[vinculo_aluno_id]
ORDER BY disciplina_nome ASC
";
$Disciplinas = mysql_query($query_Disciplinas, $SmecelNovo) or die(mysql_error());
$row_Disciplinas = mysql_fetch_assoc($Disciplinas);
$totalRows_Disciplinas = mysql_num_rows($Disciplinas);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_disciplinasMatriz = "
SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_id_disciplina, matriz_disciplina_reprova, disciplina_id, disciplina_nome, disciplina_cor_fundo 
FROM smc_matriz_disciplinas
INNER JOIN smc_disciplina ON disciplina_id = matriz_disciplina_id_disciplina 
WHERE matriz_disciplina_id_matriz = '$row_AlunoBoletim[turma_matriz_id]'";
$disciplinasMatriz = mysql_query($query_disciplinasMatriz, $SmecelNovo) or die(mysql_error());
$row_disciplinasMatriz = mysql_fetch_assoc($disciplinasMatriz);
$totalRows_disciplinasMatriz = mysql_num_rows($disciplinasMatriz);

$rec = 0;
if ($row_CriteriosAvaliativos['ca_rec_paralela']=="S") { 
  $rec = 1;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-117872281-1');
  </script>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo $row_AlunoLogado['aluno_nome']; ?>- SMECEL - Secretaria Municipal de Educação, Cultura, Esporte e Lazer</title>
  <!--Import Google Icon Font-->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <!--Import materialize.css-->
  <link type="text/css" rel="stylesheet" href="../css/materialize.min.css"  media="screen,projection"/>
  <link type="text/css" rel="stylesheet" href="../css/app.css"  media="screen,projection"/>
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
  <link type="text/css" rel="stylesheet" href="css/geral.css" media="screen,projection" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

  <style>
    table {
     width:100%;
     border-collapse: collapse;
     font-size:12px;
   }
   th, td {
     border:0px solid #ccc;
   }
   th, td {
     padding:5px;
     height:15px;
     line-height:15px;
   }

   table.bordasimples {
     border-collapse: collapse;
     font-size:7px;
   }
   table.bordasimples tr td {
     border:1px solid #eeeeee;
     padding:2px;
     font-size:12px;
   }
   table.bordasimples tr th {
     border:1px solid #eeeeee;
     padding:2px;
     font-size:9px;
   }

   
 </style>


</head>
<body class="indigo lighten-5">

  <?php require "menu_top.php"?>

  <div class="container">
    <div class="row white" style="margin: 10px 0;">

      <div class="col s12 m2 hide-on-small-only">
        <p>
        <?php 
        if (!empty($row_AlunoLogado['aluno_foto2'])) { ?>
            <img src="<?php echo URL_BASE.'aluno/fotos2/' . $row_AlunoLogado['aluno_foto2']; ?>" width="100%" class="hoverable">
        <?php } elseif (!empty($row_AlunoLogado['aluno_foto'])) { ?>
            <img src="<?php echo URL_BASE.'aluno/fotos/' . $row_AlunoLogado['aluno_foto']; ?>" width="100%" class="hoverable">
        <?php } else { ?>
            <img src="<?php echo URL_BASE.'aluno/fotos2/semfoto.jpg'; ?>" width="100%" class="hoverable">
        <?php } ?>
          <br>
          <small style="font-size:14px;">
            <?php echo current( str_word_count($row_AlunoLogado['aluno_nome'],2)); ?>
            <?php $word = explode(" ", trim($row_AlunoLogado['aluno_nome'])); echo $word[count($word)-1]; ?>
          </small>
        </p>

        <?php include "menu_esq.php"; ?>

      </div>

      <div class="col s12 m10">


        <h5><strong>Boletim Ano Letivo <?php echo $row_AlunoBoletim['vinculo_aluno_ano_letivo']; ?></strong></h5>
        <hr>
        <a href="boletim_ano.php" class="waves-effect waves-light btn-small btn-flat"><i class="material-icons left">arrow_back</i>voltar</a> 




        <?php if ($row_AlunoBoletim['escola_libera_boletim']=="S") { ?>


         <p>
          <blockquote>
            <?php if (($row_AnoLetivo['ano_letivo_resultado_final'] <= date("Y-m-d")) && $row_AnoLetivo['ano_letivo_resultado_final'] <> "") { echo "<strong>Resultado Final disponível.</strong><br>Verifique sua situação em cada Componente Curricular."; } else if ($row_AnoLetivo['ano_letivo_resultado_final'] == "") { echo "A data de divulgação do Resultado Final (RF) ainda será definida pela escola."; } else { echo "Resultado Final (RF) estará disponível à partir do dia ".date("d/m/Y", strtotime(($row_AnoLetivo['ano_letivo_resultado_final']))); }?>
          </blockquote>
        </p>
        
          <table class="ls-sm-space bordasimples striped grey lighten-5" width="100%">

            <tbody>

            <div class="row">

              <?php do { ?>





                  <?php $tmu = 0; ?>
                  <?php for ($p = 1; $p <= $row_CriteriosAvaliativos['ca_qtd_periodos']; $p++) { ?>
                    <?php $ru = 0; ?>
                    <?php for ($a = 1; $a <= $row_CriteriosAvaliativos['ca_qtd_av_periodos']; $a++) { ?>
                      
                        <!--
                        <?php 
                        mysql_select_db($database_SmecelNovo, $SmecelNovo);
                        $query_nota = "SELECT nota_id, nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_max, nota_min, nota_valor, nota_hash FROM smc_nota WHERE nota_id_matricula = '$row_AlunoBoletim[vinculo_aluno_id]' AND nota_id_disciplina = '$row_disciplinasMatriz[disciplina_id]' AND nota_periodo = '$p' AND nota_num_avaliacao = '$a'";
                        $nota = mysql_query($query_nota, $SmecelNovo) or die(mysql_error());
                        $row_nota = mysql_fetch_assoc($nota);
                        $totalRows_nota = mysql_num_rows($nota);
                        echo exibeTraco($row_nota['nota_valor'],$row_CriteriosAvaliativos['ca_nota_min_av'],$row_CriteriosAvaliativos['ca_digitos']);
                        $ru = $ru + $row_nota['nota_valor'];



                        ?>
                      
                    <?php } ?>


                    <?php  

                    if ($row_CriteriosAvaliativos['ca_rec_paralela']=="S") { 

                      mysql_select_db($database_SmecelNovo, $SmecelNovo);
                      $query_notaRecPar = "SELECT nota_id, nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_max, nota_min, nota_valor, nota_hash FROM smc_nota WHERE nota_id_matricula = '$row_AlunoBoletim[vinculo_aluno_id]' AND nota_id_disciplina = $row_disciplinasMatriz[disciplina_id] AND nota_periodo = '$p' AND nota_num_avaliacao = '98'";
                      $notaRecPar = mysql_query($query_notaRecPar, $SmecelNovo) or die(mysql_error());
                      $row_notaRecPar = mysql_fetch_assoc($notaRecPar);
                      $totalRows_notaRecPar = mysql_num_rows($notaRecPar);

                      ?>
                      <?php echo exibeTraco($row_notaRecPar['nota_valor'],$row_CriteriosAvaliativos['ca_nota_min_av'],$row_CriteriosAvaliativos['ca_digitos']); ?>
                    <?php } ?>


                    
                      <?php $mu = mediaUnidade($ru,$row_CriteriosAvaliativos['ca_arredonda_media'],$row_CriteriosAvaliativos['ca_aproxima_media'],$row_CriteriosAvaliativos['ca_media_min_periodo'],$row_CriteriosAvaliativos['ca_calculo_media_periodo'],$row_CriteriosAvaliativos['ca_qtd_av_periodos'],$row_CriteriosAvaliativos['ca_digitos']); ?>
                      <?php $tmu = $tmu + $mu; ?>
                    
                  <?php } ?>
                  
                    <?php $tp = totalPontos($tmu,$row_CriteriosAvaliativos['ca_digitos']); ?>
                 
                    <?php 
                    if (($row_AnoLetivo['ano_letivo_resultado_final'] <= date("Y-m-d")) && $row_AnoLetivo['ano_letivo_resultado_final'] <> "") { 
                      $mc = mediaCurso($tp,$row_CriteriosAvaliativos['ca_arredonda_media'],$row_CriteriosAvaliativos['ca_aproxima_media'],$row_CriteriosAvaliativos['ca_min_media_aprovacao_final'],$row_CriteriosAvaliativos['ca_qtd_periodos'],$row_CriteriosAvaliativos['ca_digitos']); 
                    } else { 
                      echo "-"; 
                    }
                    ?>
                  <a href="#">
                    <?php 
                    mysql_select_db($database_SmecelNovo, $SmecelNovo);
                    $query_notaAf = "SELECT nota_id, nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_max, nota_min, nota_valor, nota_hash FROM smc_nota WHERE nota_id_matricula = '$row_AlunoBoletim[vinculo_aluno_id]' AND nota_id_disciplina = '$row_disciplinasMatriz[disciplina_id]' AND nota_periodo = '99' AND nota_num_avaliacao = '99'";
                    $notaAf = mysql_query($query_notaAf, $SmecelNovo) or die(mysql_error());
                    $row_notaAf = mysql_fetch_assoc($notaAf);
                    $totalRows_notaAf = mysql_num_rows($notaAf);
                    echo $af = avaliacaoFinal($row_notaAf['nota_valor'],$row_CriteriosAvaliativos['ca_nota_min_recuperacao_final']);
                    ?>
                  </a> 
                  
                   <?php
                   if (($row_AnoLetivo['ano_letivo_resultado_final'] <= date("Y-m-d")) && $row_AnoLetivo['ano_letivo_resultado_final'] <> "") { 
                     if ($row_disciplinasMatriz['matriz_disciplina_reprova']=="S") {

                       echo $rf = resultadoFinal($mc, $af, $row_CriteriosAvaliativos['ca_nota_min_recuperacao_final'], $row_CriteriosAvaliativos['ca_min_media_aprovacao_final'],$row_CriteriosAvaliativos['ca_digitos']);

                     } else {
                       echo "**";
                     }
                   } else { 
                    echo "-"; 
                  }

                  ?>
                  
                -->

                <div class="col s12 m6 l4" >
                    <div class="card blue-grey darken-1 hoverable">
                      <div class="card-content white-text" style="background-color: <?php echo $row_disciplinasMatriz['disciplina_cor_fundo']; ?>; border: 0px solid #ff9800; border-radius: 0px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2); color: white; text-align: center;">
                        <span class="animate__animated animate__bounce animate__fast card-title truncate" style="margin: 0 0 10px; font-size: 14px; font-weight: normal;"><?php echo $row_disciplinasMatriz['disciplina_nome']; ?></span>
                        <p class="animate__animated animate__bounce" style="margin: 0; font-size: 52px; font-weight: bolder;">
                          
                        <?php if ($mc > $row_CriteriosAvaliativos['ca_min_media_aprovacao_final']) { ?>
                        <?php //echo $mc; ?>
                        <?php } ?>
                        <?php echo $rf; ?>

                        <br>

                        <?php if ($rf=="APR") { echo "<i class='material-icons medium'>sentiment_very_satisfied</i>"; } else { echo "<i class='material-icons medium'>sentiment_very_dissatisfied</i>"; } ?>

                        </p>
                      </div>
                    </div>
                  </div>       

            <?php } while ($row_disciplinasMatriz = mysql_fetch_assoc($disciplinasMatriz)); ?>

                </div>
          
        <br>

        <a href="boletim.php?codigo=<?php echo $colname_Boletim; ?>&verificacao=<?php echo $row_Matricula['vinculo_aluno_verificacao']; ?>" class="btn">VER BOLETIM</a>

         <br>                 

        <p>Legenda:</p>
        <small>
          APR = Aluno(a) aprovado(a)<br>	
          CON = Aluno(a) conservado(a)<br>	
        </small>




        <?php 
        mysql_free_result($nota);
        mysql_free_result($notaAf);
        ?>

      <?php } else { ?>


        <div class="card-panel orange lighten-4"><strong>Atenção: </strong> O acesso ao boletim do aluno foi bloqueado temporariamente pela escola. Aguarde a liberação.</div>



      <?php } ?>









    </div>





  </div>
</div>

<!--JavaScript at end of body for optimized loading--> 
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script> 
<script type="text/javascript" src="../js/materialize.min.js"></script> 
<script type="text/javascript">
  $(document).ready(function(){
   $('.sidenav').sidenav();
   $('.tabs').tabs();
   $('.dropdown-trigger').dropdown();
 });
</script>
</body>
</html>
<?php
mysql_free_result($Matricula);
mysql_free_result($AlunoLogado);
mysql_free_result($Boletim);
mysql_free_result($Matriz);
mysql_free_result($CriteriosAvaliativos);
mysql_free_result($Disciplinas);
mysql_free_result($disciplinasMatriz);

//mysql_free_result($alunoBoletim); 
?>

