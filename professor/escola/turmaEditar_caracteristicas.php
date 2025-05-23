<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>

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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	
	if ($row_UsuLogado['usu_update']=="N") {
		header(sprintf("Location: turmaListar.php?permissao"));
		break;
	}
	
  $updateSQL = sprintf("UPDATE smc_turma SET

turma_info_tipo_de_mediacao_didaticopedagogica_6=%s,
turma_info_hora_inicial_hora_7=%s,
turma_info_hora_inicial_minuto_8=%s,
turma_info_hora_final_hora_9=%s,
turma_info_hora_final_minuto_10=%s,

turma_info_segundafeira_12=%s,
turma_info_tercafeira_13=%s,
turma_info_quartafeira_14=%s,
turma_info_quintafeira_15=%s,
turma_info_sextafeira_16=%s,
turma_info_sabado_17=%s,
turma_info_formacao_geral_basica_21=%s,
turma_info_itinerario_formativo_22=%s,
turma_info_nao_se_aplica_23=%s,
turma_info_codigo_1_24=%s,
turma_info_codigo_2_25=%s,
turma_info_codigo_3_26=%s,
turma_info_codigo_4_27=%s,
turma_info_codigo_5_28=%s,
turma_info_codigo_6_29=%s,
turma_info_local_de_funcionamento_diferenciado_da_t_30=%s,
turma_info_modalidade_31=%s,
turma_info_periodos_semestrais_35=%s,
turma_info_ciclos_36=%s,
turma_info_grupos_nao_seriados_com_base_na_idade_ou_37=%s,
turma_info_modulos_38=%s,
turma_info_alternancia_regular_de_periodos_de_estud_39=%s,
turma_info_eletivas_40=%s,
turma_info_libras_41=%s,
turma_info_lingua_indigena_42=%s,
turma_info_lingua_literatura_estrangeira_espanhol_43=%s,
turma_info_lingua_literatura_estrangeira_frances_44=%s,
turma_info_lingua_literatura_estrangeira_outra_45=%s,
turma_info_projeto_de_vida_46=%s,
turma_info_trilhas_de_aprofundamento_aprendizagens_47=%s,
turma_info_outras_unidades_curriculares_obrigatoria_48=%s,
turma_info_1_quimica_49=%s,
turma_info_2_fisica_50=%s,
turma_info_3_matematica_51=%s,
turma_info_4_biologia_52=%s,
turma_info_5_ciencias_53=%s,
turma_info_6_lingua_literatura_portuguesa_54=%s,
turma_info_7_lingua_literatura_estrangeira_ingles_55=%s,
turma_info_8_lingua_literatura_estrangeira_espanhol_56=%s,
turma_info_9_lingua_literatura_estrangeira_outra_57=%s,
turma_info_10_arte_educacao_artistica_teatro_danca__58=%s,
turma_info_11_educacao_fisica_59=%s,
turma_info_12_historia_60=%s,
turma_info_13_geografia_61=%s,
turma_info_14_filosofia_62=%s,
turma_info_16_informatica_computacao_63=%s,
turma_info_17_areas_do_conhecimento_profissionaliza_64=%s,
turma_info_23_libras_65=%s,
turma_info_25_areas_do_conhecimento_pedagogicas_66=%s,
turma_info_26_ensino_religioso_67=%s,
turma_info_27_lingua_indigena_68=%s,
turma_info_28_estudos_sociais_69=%s,
turma_info_29_sociologia_70=%s,
turma_info_30_lingua_literatura_estrangeira_frances_71=%s,
turma_info_31_lingua_portuguesa_como_segunda_lingua_72=%s,
turma_info_32_estagio_curricular_supervisionado_73=%s,
turma_info_33_projeto_de_vida_74=%s,
turma_info_99_outras_areas_do_conhecimento_75=%s,
turma_info_classe_bilingue_de_surdos_tendo_a_libras_76=%s 
  
  
  WHERE turma_id=%s",
                       
                       GetSQLValueString($_POST['turma_info_tipo_de_mediacao_didaticopedagogica_6'], "text"),
                       GetSQLValueString($_POST['turma_info_hora_inicial_hora_7'], "text"),
                       GetSQLValueString($_POST['turma_info_hora_inicial_minuto_8'], "text"),
                       GetSQLValueString($_POST['turma_info_hora_final_hora_9'], "text"),
                       GetSQLValueString($_POST['turma_info_hora_final_minuto_10'], "text"),

                       //GetSQLValueString(isset($_POST['turma_info_domingo_11']) ? "true" : "", "defined","'1'","'0'"),
                       GetSQLValueString(isset($_POST['turma_info_segundafeira_12']) ? "true" : "", "defined","'1'","'0'"),
                       GetSQLValueString(isset($_POST['turma_info_tercafeira_13']) ? "true" : "", "defined","'1'","'0'"),
                       GetSQLValueString(isset($_POST['turma_info_quartafeira_14']) ? "true" : "", "defined","'1'","'0'"),
                       GetSQLValueString(isset($_POST['turma_info_quintafeira_15']) ? "true" : "", "defined","'1'","'0'"),
                       GetSQLValueString(isset($_POST['turma_info_sextafeira_16']) ? "true" : "", "defined","'1'","'0'"),
                       GetSQLValueString(isset($_POST['turma_info_sabado_17']) ? "true" : "", "defined","'1'","'0'"),

                       //GetSQLValueString($_POST['turma_info_domingo_11'], "text"),
                       //GetSQLValueString($_POST['turma_info_segundafeira_12'], "text"),
                       //GetSQLValueString($_POST['turma_info_tercafeira_13'], "text"),
                       //GetSQLValueString($_POST['turma_info_quartafeira_14'], "text"),
                       //GetSQLValueString($_POST['turma_info_quintafeira_15'], "text"),
                       //GetSQLValueString($_POST['turma_info_sextafeira_16'], "text"),
                       //GetSQLValueString($_POST['turma_info_sabado_17'], "text"),

                       GetSQLValueString($_POST['turma_info_formacao_geral_basica_21'], "text"),
                       GetSQLValueString($_POST['turma_info_itinerario_formativo_22'], "text"),
                       GetSQLValueString($_POST['turma_info_nao_se_aplica_23'], "text"),
                       GetSQLValueString($_POST['turma_info_codigo_1_24'], "text"),
                       GetSQLValueString($_POST['turma_info_codigo_2_25'], "text"),
                       GetSQLValueString($_POST['turma_info_codigo_3_26'], "text"),
                       GetSQLValueString($_POST['turma_info_codigo_4_27'], "text"),
                       GetSQLValueString($_POST['turma_info_codigo_5_28'], "text"),
                       GetSQLValueString($_POST['turma_info_codigo_6_29'], "text"),
                       GetSQLValueString($_POST['turma_info_local_de_funcionamento_diferenciado_da_t_30'], "text"),
                       GetSQLValueString($_POST['turma_info_modalidade_31'], "text"),
                       GetSQLValueString($_POST['turma_info_periodos_semestrais_35'], "text"),
                       GetSQLValueString($_POST['turma_info_ciclos_36'], "text"),
                       GetSQLValueString($_POST['turma_info_grupos_nao_seriados_com_base_na_idade_ou_37'], "text"),
                       GetSQLValueString($_POST['turma_info_modulos_38'], "text"),
                       GetSQLValueString($_POST['turma_info_alternancia_regular_de_periodos_de_estud_39'], "text"),
                       GetSQLValueString($_POST['turma_info_eletivas_40'], "text"),
                       GetSQLValueString($_POST['turma_info_libras_41'], "text"),
                       GetSQLValueString($_POST['turma_info_lingua_indigena_42'], "text"),
                       GetSQLValueString($_POST['turma_info_lingua_literatura_estrangeira_espanhol_43'], "text"),
                       GetSQLValueString($_POST['turma_info_lingua_literatura_estrangeira_frances_44'], "text"),
                       GetSQLValueString($_POST['turma_info_lingua_literatura_estrangeira_outra_45'], "text"),
                       GetSQLValueString($_POST['turma_info_projeto_de_vida_46'], "text"),
                       GetSQLValueString($_POST['turma_info_trilhas_de_aprofundamento_aprendizagens_47'], "text"),
                       GetSQLValueString($_POST['turma_info_outras_unidades_curriculares_obrigatoria_48'], "text"),

                       GetSQLValueString(isset($_POST['turma_info_1_quimica_49']) ? $_POST['turma_info_1_quimica_49'] : '', "text"),
GetSQLValueString(isset($_POST['turma_info_2_fisica_50']) ? $_POST['turma_info_2_fisica_50'] : '', "text"),
GetSQLValueString(isset($_POST['turma_info_3_matematica_51']) ? $_POST['turma_info_3_matematica_51'] : '', "text"),
GetSQLValueString(isset($_POST['turma_info_4_biologia_52']) ? $_POST['turma_info_4_biologia_52'] : '', "text"),
GetSQLValueString(isset($_POST['turma_info_5_ciencias_53']) ? $_POST['turma_info_5_ciencias_53'] : '', "text"),
GetSQLValueString(isset($_POST['turma_info_6_lingua_literatura_portuguesa_54']) ? $_POST['turma_info_6_lingua_literatura_portuguesa_54'] : '', "text"),
GetSQLValueString(isset($_POST['turma_info_7_lingua_literatura_estrangeira_ingles_55']) ? $_POST['turma_info_7_lingua_literatura_estrangeira_ingles_55'] : '', "text"),
GetSQLValueString(isset($_POST['turma_info_8_lingua_literatura_estrangeira_espanhol_56']) ? $_POST['turma_info_8_lingua_literatura_estrangeira_espanhol_56'] : '', "text"),
GetSQLValueString(isset($_POST['turma_info_9_lingua_literatura_estrangeira_outra_57']) ? $_POST['turma_info_9_lingua_literatura_estrangeira_outra_57'] : '', "text"),
GetSQLValueString(isset($_POST['turma_info_10_arte_educacao_artistica_teatro_danca__58']) ? $_POST['turma_info_10_arte_educacao_artistica_teatro_danca__58'] : '', "text"),
GetSQLValueString(isset($_POST['turma_info_11_educacao_fisica_59']) ? $_POST['turma_info_11_educacao_fisica_59'] : '', "text"),
GetSQLValueString(isset($_POST['turma_info_12_historia_60']) ? $_POST['turma_info_12_historia_60'] : '', "text"),
GetSQLValueString(isset($_POST['turma_info_13_geografia_61']) ? $_POST['turma_info_13_geografia_61'] : '', "text"),
GetSQLValueString(isset($_POST['turma_info_14_filosofia_62']) ? $_POST['turma_info_14_filosofia_62'] : '', "text"),
GetSQLValueString(isset($_POST['turma_info_16_informatica_computacao_63']) ? $_POST['turma_info_16_informatica_computacao_63'] : '', "text"),
GetSQLValueString(isset($_POST['turma_info_17_areas_do_conhecimento_profissionaliza_64']) ? $_POST['turma_info_17_areas_do_conhecimento_profissionaliza_64'] : '', "text"),
GetSQLValueString(isset($_POST['turma_info_23_libras_65']) ? $_POST['turma_info_23_libras_65'] : '', "text"),
GetSQLValueString(isset($_POST['turma_info_25_areas_do_conhecimento_pedagogicas_66']) ? $_POST['turma_info_25_areas_do_conhecimento_pedagogicas_66'] : '', "text"),
GetSQLValueString(isset($_POST['turma_info_26_ensino_religioso_67']) ? $_POST['turma_info_26_ensino_religioso_67'] : '', "text"),
GetSQLValueString(isset($_POST['turma_info_27_lingua_indigena_68']) ? $_POST['turma_info_27_lingua_indigena_68'] : '', "text"),
GetSQLValueString(isset($_POST['turma_info_28_estudos_sociais_69']) ? $_POST['turma_info_28_estudos_sociais_69'] : '', "text"),
GetSQLValueString(isset($_POST['turma_info_29_sociologia_70']) ? $_POST['turma_info_29_sociologia_70'] : '', "text"),
GetSQLValueString(isset($_POST['turma_info_30_lingua_literatura_estrangeira_frances_71']) ? $_POST['turma_info_30_lingua_literatura_estrangeira_frances_71'] : '', "text"),
GetSQLValueString(isset($_POST['turma_info_31_lingua_portuguesa_como_segunda_lingua_72']) ? $_POST['turma_info_31_lingua_portuguesa_como_segunda_lingua_72'] : '', "text"),
GetSQLValueString(isset($_POST['turma_info_32_estagio_curricular_supervisionado_73']) ? $_POST['turma_info_32_estagio_curricular_supervisionado_73'] : '', "text"),
GetSQLValueString(isset($_POST['turma_info_33_projeto_de_vida_74']) ? $_POST['turma_info_33_projeto_de_vida_74'] : '', "text"),
GetSQLValueString(isset($_POST['turma_info_99_outras_areas_do_conhecimento_75']) ? $_POST['turma_info_99_outras_areas_do_conhecimento_75'] : '', "text"),


                       GetSQLValueString($_POST['turma_info_classe_bilingue_de_surdos_tendo_a_libras_76'], "text"),



                       
                       GetSQLValueString($_POST['turma_id'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

// ** REGISTRO DE LOG DE USUÁRIO **
$usu = $_POST['usu_id'];
$esc = $_POST['usu_escola'];
$detalhes = $_POST['detalhes'];
date_default_timezone_set('America/Bahia');
$dat = date('Y-m-d H:i:s');

$sql = "
INSERT INTO smc_registros (
registros_id_escola, 
registros_id_usuario, 
registros_tipo, 
registros_complemento, 
registros_data_hora
) VALUES (
'$esc', 
'$usu', 
'21', 
'($detalhes)', 
'$dat')
";
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$Result1 = mysql_query($sql, $SmecelNovo) or die(mysql_error());
// ** REGISTRO DE LOG DE USUÁRIO **
  
  
  
  $updateGoTo = "turmaEditar_caracteristicas.php?editada";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}



mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EscolaLogada = "
SELECT * 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_listarEtapa = "SELECT etapa_id, etapa_id_filtro, etapa_nome FROM smc_etapa ORDER BY etapa_id ASC";
$listarEtapa = mysql_query($query_listarEtapa, $SmecelNovo) or die(mysql_error());
$row_listarEtapa = mysql_fetch_assoc($listarEtapa);
$totalRows_listarEtapa = mysql_num_rows($listarEtapa);

$colname_turmaEditar = "-1";
if (isset($_GET['c'])) {
  $colname_turmaEditar = $_GET['c'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_turmaEditar = sprintf("SELECT * FROM smc_turma WHERE turma_id_escola = $row_EscolaLogada[escola_id] AND turma_id = %s", GetSQLValueString($colname_turmaEditar, "int"));
$turmaEditar = mysql_query($query_turmaEditar, $SmecelNovo) or die(mysql_error());
$row_turmaEditar = mysql_fetch_assoc($turmaEditar);
$totalRows_turmaEditar = mysql_num_rows($turmaEditar);

if ($totalRows_turmaEditar == "") {
	//echo "TURMA EM BRANCO";	
	header("Location: turmaListar.php?nada"); 
 	exit;
	}
	
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matriz = "SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash, matriz_ativa FROM smc_matriz WHERE matriz_ativa = 'S' AND matriz_id_secretaria = $row_EscolaLogada[escola_id_sec] ORDER BY matriz_id_etapa, matriz_nome ASC";
$Matriz = mysql_query($query_Matriz, $SmecelNovo) or die(mysql_error());
$row_Matriz = mysql_fetch_assoc($Matriz);
$totalRows_Matriz = mysql_num_rows($Matriz);
	

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
<link rel="stylesheet" type="text/css" href="css/locastyle.css"><link rel="stylesheet" type="text/css" href="css/preloader.css">
<script src="js/locastyle.js"></script>
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
  <style>
    fieldset {
      padding: 10px;
      margin-left: 12px;
      background-color: #fafafa;
      border-radius: 10px;
    }
   
  </style>
</head>
  <body>
    <?php include_once ("menu-top.php"); ?>
          <?php include_once ("menu-esc.php"); ?>

      


    <main class="ls-main ">
      <div class="container-fluid">
 
        <h1 class="ls-title-intro ls-ico-home">CARACTERÍSTICAS: <?php echo htmlentities($row_turmaEditar['turma_nome'], ENT_COMPAT, 'utf-8'); ?></h1>
        
        <?php if (isset($_GET["editada"])) { ?>
      <div class="ls-alert-success ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> Dados atualizados com sucesso. </div>
      <?php } ?>
	                <?php if (isset($_GET["permissao"])) { ?>
                <div class="ls-alert-danger ls-dismissable">
                  <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
                  VOCÊ NÃO TEM PERMISSÃO PARA REALIZAR ESTA AÇÃO.
                </div>
              <?php } ?>

        <form method="post" class="ls-form ls-form-horizontal row" name="form1" action="<?php echo $editFormAction; ?>" data-ls-module="form">
        
<label class="ls-label col-md-12">
        <b class="ls-label-text">6 - Tipo de mediação didático-pedagógica</b>
        <div class="ls-custom-select">
            <select name="turma_info_tipo_de_mediacao_didaticopedagogica_6" class="ls-select">
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_turmaEditar['turma_info_tipo_de_mediacao_didaticopedagogica_6'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>1 - Presencial</option>
                <option value="2" <?php if (!(strcmp("2", htmlentities($row_turmaEditar['turma_info_tipo_de_mediacao_didaticopedagogica_6'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>2 - Semipresencial</option>
                <option value="3" <?php if (!(strcmp("3", htmlentities($row_turmaEditar['turma_info_tipo_de_mediacao_didaticopedagogica_6'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>3 – Educação a distância – EAD</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-12"><h4 class="ls-title-4 ls-color-theme ls-box ls-box-gray">7 a 10	- Horário de funcionamento</h4></label>


<label class="ls-label col-md-3">
        <b class="ls-label-text">7 - Hora Inicial - Hora</b>
        <div class="ls-custom-select">
            <select name="turma_info_hora_inicial_hora_7" class="ls-select">
                <option value=""></option>

                <option value="00" <?php if (!(strcmp("00", htmlentities($row_turmaEditar['turma_info_hora_inicial_hora_7'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>00</option>
                <option value="01" <?php if (!(strcmp("01", htmlentities($row_turmaEditar['turma_info_hora_inicial_hora_7'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>01</option>
                <option value="02" <?php if (!(strcmp("02", htmlentities($row_turmaEditar['turma_info_hora_inicial_hora_7'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>02</option>
                <option value="03" <?php if (!(strcmp("03", htmlentities($row_turmaEditar['turma_info_hora_inicial_hora_7'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>03</option>
                <option value="04" <?php if (!(strcmp("04", htmlentities($row_turmaEditar['turma_info_hora_inicial_hora_7'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>04</option>
                <option value="05" <?php if (!(strcmp("05", htmlentities($row_turmaEditar['turma_info_hora_inicial_hora_7'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>05</option>
                <option value="06" <?php if (!(strcmp("06", htmlentities($row_turmaEditar['turma_info_hora_inicial_hora_7'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>06</option>
                <option value="07" <?php if (!(strcmp("07", htmlentities($row_turmaEditar['turma_info_hora_inicial_hora_7'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>07</option>
                <option value="08" <?php if (!(strcmp("08", htmlentities($row_turmaEditar['turma_info_hora_inicial_hora_7'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>08</option>
                <option value="09" <?php if (!(strcmp("09", htmlentities($row_turmaEditar['turma_info_hora_inicial_hora_7'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>09</option>
                <option value="10" <?php if (!(strcmp("10", htmlentities($row_turmaEditar['turma_info_hora_inicial_hora_7'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>10</option>
                <option value="11" <?php if (!(strcmp("11", htmlentities($row_turmaEditar['turma_info_hora_inicial_hora_7'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>11</option>
                <option value="12" <?php if (!(strcmp("12", htmlentities($row_turmaEditar['turma_info_hora_inicial_hora_7'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>12</option>
                <option value="13" <?php if (!(strcmp("13", htmlentities($row_turmaEditar['turma_info_hora_inicial_hora_7'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>13</option>
                <option value="14" <?php if (!(strcmp("14", htmlentities($row_turmaEditar['turma_info_hora_inicial_hora_7'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>14</option>
                <option value="15" <?php if (!(strcmp("15", htmlentities($row_turmaEditar['turma_info_hora_inicial_hora_7'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>15</option>
                <option value="16" <?php if (!(strcmp("16", htmlentities($row_turmaEditar['turma_info_hora_inicial_hora_7'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>16</option>
                <option value="17" <?php if (!(strcmp("17", htmlentities($row_turmaEditar['turma_info_hora_inicial_hora_7'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>17</option>
                <option value="18" <?php if (!(strcmp("18", htmlentities($row_turmaEditar['turma_info_hora_inicial_hora_7'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>18</option>
                <option value="19" <?php if (!(strcmp("19", htmlentities($row_turmaEditar['turma_info_hora_inicial_hora_7'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>19</option>
                <option value="20" <?php if (!(strcmp("20", htmlentities($row_turmaEditar['turma_info_hora_inicial_hora_7'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>20</option>
                <option value="21" <?php if (!(strcmp("21", htmlentities($row_turmaEditar['turma_info_hora_inicial_hora_7'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>21</option>
                <option value="22" <?php if (!(strcmp("22", htmlentities($row_turmaEditar['turma_info_hora_inicial_hora_7'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>22</option>
                <option value="23" <?php if (!(strcmp("23", htmlentities($row_turmaEditar['turma_info_hora_inicial_hora_7'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>23</option>

            </select>
        </div>
    </label>
<label class="ls-label col-md-3">
        <b class="ls-label-text">8 - Hora Inicial - Minuto</b>
        <div class="ls-custom-select">
            <select name="turma_info_hora_inicial_minuto_8" class="ls-select">
                <option value=""></option>
                <option value="00" <?php if (!(strcmp("00", htmlentities($row_turmaEditar['turma_info_hora_inicial_minuto_8'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>00</option>
                <option value="05" <?php if (!(strcmp("05", htmlentities($row_turmaEditar['turma_info_hora_inicial_minuto_8'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>05</option>
                <option value="10" <?php if (!(strcmp("10", htmlentities($row_turmaEditar['turma_info_hora_inicial_minuto_8'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>10</option>
                <option value="15" <?php if (!(strcmp("15", htmlentities($row_turmaEditar['turma_info_hora_inicial_minuto_8'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>15</option>
                <option value="20" <?php if (!(strcmp("20", htmlentities($row_turmaEditar['turma_info_hora_inicial_minuto_8'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>20</option>
                <option value="25" <?php if (!(strcmp("25", htmlentities($row_turmaEditar['turma_info_hora_inicial_minuto_8'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>25</option>
                <option value="30" <?php if (!(strcmp("30", htmlentities($row_turmaEditar['turma_info_hora_inicial_minuto_8'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>30</option>
                <option value="35" <?php if (!(strcmp("35", htmlentities($row_turmaEditar['turma_info_hora_inicial_minuto_8'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>35</option>
                <option value="40" <?php if (!(strcmp("40", htmlentities($row_turmaEditar['turma_info_hora_inicial_minuto_8'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>40</option>
                <option value="45" <?php if (!(strcmp("45", htmlentities($row_turmaEditar['turma_info_hora_inicial_minuto_8'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>45</option>
                <option value="50" <?php if (!(strcmp("50", htmlentities($row_turmaEditar['turma_info_hora_inicial_minuto_8'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>50</option>
                <option value="55" <?php if (!(strcmp("55", htmlentities($row_turmaEditar['turma_info_hora_inicial_minuto_8'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>55</option>
            </select>
        </div>
    </label>
<label class="ls-label col-md-3">
        <b class="ls-label-text">9 - Hora Final - Hora</b>
        <div class="ls-custom-select">
            <select name="turma_info_hora_final_hora_9" class="ls-select">
                <option value=""></option>
                <option value="00" <?php if (!(strcmp("00", htmlentities($row_turmaEditar['turma_info_hora_final_hora_9'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>00</option>
                <option value="01" <?php if (!(strcmp("01", htmlentities($row_turmaEditar['turma_info_hora_final_hora_9'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>01</option>
                <option value="02" <?php if (!(strcmp("02", htmlentities($row_turmaEditar['turma_info_hora_final_hora_9'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>02</option>
                <option value="03" <?php if (!(strcmp("03", htmlentities($row_turmaEditar['turma_info_hora_final_hora_9'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>03</option>
                <option value="04" <?php if (!(strcmp("04", htmlentities($row_turmaEditar['turma_info_hora_final_hora_9'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>04</option>
                <option value="05" <?php if (!(strcmp("05", htmlentities($row_turmaEditar['turma_info_hora_final_hora_9'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>05</option>
                <option value="06" <?php if (!(strcmp("06", htmlentities($row_turmaEditar['turma_info_hora_final_hora_9'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>06</option>
                <option value="07" <?php if (!(strcmp("07", htmlentities($row_turmaEditar['turma_info_hora_final_hora_9'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>07</option>
                <option value="08" <?php if (!(strcmp("08", htmlentities($row_turmaEditar['turma_info_hora_final_hora_9'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>08</option>
                <option value="09" <?php if (!(strcmp("09", htmlentities($row_turmaEditar['turma_info_hora_final_hora_9'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>09</option>
                <option value="10" <?php if (!(strcmp("10", htmlentities($row_turmaEditar['turma_info_hora_final_hora_9'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>10</option>
                <option value="11" <?php if (!(strcmp("11", htmlentities($row_turmaEditar['turma_info_hora_final_hora_9'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>11</option>
                <option value="12" <?php if (!(strcmp("12", htmlentities($row_turmaEditar['turma_info_hora_final_hora_9'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>12</option>
                <option value="13" <?php if (!(strcmp("13", htmlentities($row_turmaEditar['turma_info_hora_final_hora_9'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>13</option>
                <option value="14" <?php if (!(strcmp("14", htmlentities($row_turmaEditar['turma_info_hora_final_hora_9'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>14</option>
                <option value="15" <?php if (!(strcmp("15", htmlentities($row_turmaEditar['turma_info_hora_final_hora_9'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>15</option>
                <option value="16" <?php if (!(strcmp("16", htmlentities($row_turmaEditar['turma_info_hora_final_hora_9'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>16</option>
                <option value="17" <?php if (!(strcmp("17", htmlentities($row_turmaEditar['turma_info_hora_final_hora_9'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>17</option>
                <option value="18" <?php if (!(strcmp("18", htmlentities($row_turmaEditar['turma_info_hora_final_hora_9'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>18</option>
                <option value="19" <?php if (!(strcmp("19", htmlentities($row_turmaEditar['turma_info_hora_final_hora_9'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>19</option>
                <option value="20" <?php if (!(strcmp("20", htmlentities($row_turmaEditar['turma_info_hora_final_hora_9'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>20</option>
                <option value="21" <?php if (!(strcmp("21", htmlentities($row_turmaEditar['turma_info_hora_final_hora_9'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>21</option>
                <option value="22" <?php if (!(strcmp("22", htmlentities($row_turmaEditar['turma_info_hora_final_hora_9'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>22</option>
                <option value="23" <?php if (!(strcmp("23", htmlentities($row_turmaEditar['turma_info_hora_final_hora_9'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>23</option>


            </select>
        </div>
    </label>
<label class="ls-label col-md-3">
        <b class="ls-label-text">10 - Hora Final - Minuto</b>
        <div class="ls-custom-select">
            <select name="turma_info_hora_final_minuto_10" class="ls-select">
                <option value=""></option>
                <option value="00" <?php if (!(strcmp("00", htmlentities($row_turmaEditar['turma_info_hora_final_minuto_10'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>00</option>
                <option value="05" <?php if (!(strcmp("05", htmlentities($row_turmaEditar['turma_info_hora_final_minuto_10'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>05</option>
                <option value="10" <?php if (!(strcmp("10", htmlentities($row_turmaEditar['turma_info_hora_final_minuto_10'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>10</option>
                <option value="15" <?php if (!(strcmp("15", htmlentities($row_turmaEditar['turma_info_hora_final_minuto_10'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>15</option>
                <option value="20" <?php if (!(strcmp("20", htmlentities($row_turmaEditar['turma_info_hora_final_minuto_10'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>20</option>
                <option value="25" <?php if (!(strcmp("25", htmlentities($row_turmaEditar['turma_info_hora_final_minuto_10'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>25</option>
                <option value="30" <?php if (!(strcmp("30", htmlentities($row_turmaEditar['turma_info_hora_final_minuto_10'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>30</option>
                <option value="35" <?php if (!(strcmp("35", htmlentities($row_turmaEditar['turma_info_hora_final_minuto_10'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>35</option>
                <option value="40" <?php if (!(strcmp("40", htmlentities($row_turmaEditar['turma_info_hora_final_minuto_10'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>40</option>
                <option value="45" <?php if (!(strcmp("45", htmlentities($row_turmaEditar['turma_info_hora_final_minuto_10'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>45</option>
                <option value="50" <?php if (!(strcmp("50", htmlentities($row_turmaEditar['turma_info_hora_final_minuto_10'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>50</option>
                <option value="55" <?php if (!(strcmp("55", htmlentities($row_turmaEditar['turma_info_hora_final_minuto_10'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>55</option>


            </select>
        </div>
    </label>

    <!--
      <label class="ls-label col-md-2 col-sm-12">
          <b class="ls-label-text">11 - Domingo</b>
          <br><p class="ls-label-info">
          <div data-ls-module="switchButton" class="ls-switch-btn">
          <input type="checkbox" name="turma_info_domingo_11" id="turma_info_domingo_11" value=""  <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_domingo_11'], ENT_COMPAT, 'utf-8'),"1"))) {echo "checked=\"checked\"";} ?>>
          <label class="ls-switch-label" for="turma_info_domingo_11" name="turma_info_domingo_11" ls-switch-off="Não" ls-switch-on="Sim"><span></span></label>
          </div>
          </p>
		  </label>
-->

<label class="ls-label col-md-12"><h4 class="ls-title-4 ls-color-theme ls-box ls-box-gray">11 a 17 -	Dias da Semana</h4></label>

  <label class="ls-label col-md-2 col-sm-12">
    <b class="ls-label-text">Segunda-feira</b>
    <br><p class="ls-label-info">
    <div data-ls-module="switchButton" class="ls-switch-btn">
    <input type="checkbox" name="turma_info_segundafeira_12" id="turma_info_segundafeira_12" value=""  <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_segundafeira_12'], ENT_COMPAT, 'utf-8'),"1"))) {echo "checked=\"checked\"";} ?>>
    <label class="ls-switch-label" for="turma_info_segundafeira_12" name="turma_info_segundafeira_12" ls-switch-off="Não" ls-switch-on="Sim"><span></span></label>
    </div>
    </p>
</label>

<label class="ls-label col-md-2 col-sm-12">
    <b class="ls-label-text">Terça-feira</b>
    <br><p class="ls-label-info">
    <div data-ls-module="switchButton" class="ls-switch-btn">
    <input type="checkbox" name="turma_info_tercafeira_13" id="turma_info_tercafeira_13" value=""  <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_tercafeira_13'], ENT_COMPAT, 'utf-8'),"1"))) {echo "checked=\"checked\"";} ?>>
    <label class="ls-switch-label" for="turma_info_tercafeira_13" name="turma_info_tercafeira_13" ls-switch-off="Não" ls-switch-on="Sim"><span></span></label>
    </div>
    </p>
</label>

<label class="ls-label col-md-2 col-sm-12">
    <b class="ls-label-text">Quarta-feira</b>
    <br><p class="ls-label-info">
    <div data-ls-module="switchButton" class="ls-switch-btn">
    <input type="checkbox" name="turma_info_quartafeira_14" id="turma_info_quartafeira_14" value=""  <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_quartafeira_14'], ENT_COMPAT, 'utf-8'),"1"))) {echo "checked=\"checked\"";} ?>>
    <label class="ls-switch-label" for="turma_info_quartafeira_14" name="turma_info_quartafeira_14" ls-switch-off="Não" ls-switch-on="Sim"><span></span></label>
    </div>
    </p>
</label>

<label class="ls-label col-md-2 col-sm-12">
    <b class="ls-label-text">Quinta-feira</b>
    <br><p class="ls-label-info">
    <div data-ls-module="switchButton" class="ls-switch-btn">
    <input type="checkbox" name="turma_info_quintafeira_15" id="turma_info_quintafeira_15" value=""  <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_quintafeira_15'], ENT_COMPAT, 'utf-8'),"1"))) {echo "checked=\"checked\"";} ?>>
    <label class="ls-switch-label" for="turma_info_quintafeira_15" name="turma_info_quintafeira_15" ls-switch-off="Não" ls-switch-on="Sim"><span></span></label>
    </div>
    </p>
</label>

<label class="ls-label col-md-2 col-sm-12">
    <b class="ls-label-text">Sexta-feira</b>
    <br><p class="ls-label-info">
    <div data-ls-module="switchButton" class="ls-switch-btn">
    <input type="checkbox" name="turma_info_sextafeira_16" id="turma_info_sextafeira_16" value=""  <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_sextafeira_16'], ENT_COMPAT, 'utf-8'),"1"))) {echo "checked=\"checked\"";} ?>>
    <label class="ls-switch-label" for="turma_info_sextafeira_16" name="turma_info_sextafeira_16" ls-switch-off="Não" ls-switch-on="Sim"><span></span></label>
    </div>
    </p>
</label>

<label class="ls-label col-md-2 col-sm-12">
    <b class="ls-label-text">Sábado</b>
    <br><p class="ls-label-info">
    <div data-ls-module="switchButton" class="ls-switch-btn">
    <input type="checkbox" name="turma_info_sabado_17" id="turma_info_sabado_17" value=""  <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_sabado_17'], ENT_COMPAT, 'utf-8'),"1"))) {echo "checked=\"checked\"";} ?>>
    <label class="ls-switch-label" for="turma_info_sabado_17" name="turma_info_sabado_17" ls-switch-off="Não" ls-switch-on="Sim"><span></span></label>
    </div>
    </p>
</label>
     




<label class="ls-label col-md-12"><h4 class="ls-title-4 ls-color-theme ls-box ls-box-gray">21 a 23 -	Estrutura curricular	</h4></label>


<label class="ls-label col-md-4">
        <b class="ls-label-text">21 - Formação geral básica</b>
        <div class="ls-custom-select">
            <select name="turma_info_formacao_geral_basica_21" class="ls-select"><option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_turmaEditar['turma_info_formacao_geral_basica_21'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_turmaEditar['turma_info_formacao_geral_basica_21'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>
<label class="ls-label col-md-4">
        <b class="ls-label-text">22 - Itinerário formativo</b>
        <div class="ls-custom-select">
            <select name="turma_info_itinerario_formativo_22" class="ls-select"><option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_turmaEditar['turma_info_itinerario_formativo_22'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_turmaEditar['turma_info_itinerario_formativo_22'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>
<label class="ls-label col-md-4">
        <b class="ls-label-text">23 - Não se aplica</b>
        <div class="ls-custom-select">
            <select name="turma_info_nao_se_aplica_23" class="ls-select"><option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_turmaEditar['turma_info_nao_se_aplica_23'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_turmaEditar['turma_info_nao_se_aplica_23'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

    <label class="ls-label col-md-12"><h4 class="ls-title-4 ls-color-theme ls-box ls-box-gray">24 a 29 -	Tipo de atividade complementar</h4></label>


<label class="ls-label col-md-2">
        <b class="ls-label-text">24 - Código 1</b>
        <input type="text" name="turma_info_codigo_1_24" value="<?php echo $row_turmaEditar["turma_info_codigo_1_24"]; ?>" maxlength="5">
    </label>
<label class="ls-label col-md-2">
        <b class="ls-label-text">25 - Código 2</b>
        <input type="text" name="turma_info_codigo_2_25" value="<?php echo $row_turmaEditar["turma_info_codigo_2_25"]; ?>" maxlength="5">
    </label>
<label class="ls-label col-md-2">
        <b class="ls-label-text">26 - Código 3</b>
        <input type="text" name="turma_info_codigo_3_26" value="<?php echo $row_turmaEditar["turma_info_codigo_3_26"]; ?>" maxlength="5">
    </label>
<label class="ls-label col-md-2">
        <b class="ls-label-text">27 - Código 4</b>
        <input type="text" name="turma_info_codigo_4_27" value="<?php echo $row_turmaEditar["turma_info_codigo_4_27"]; ?>" maxlength="5">
    </label>
<label class="ls-label col-md-2">
        <b class="ls-label-text">28 - Código 5</b>
        <input type="text" name="turma_info_codigo_5_28" value="<?php echo $row_turmaEditar["turma_info_codigo_5_28"]; ?>" maxlength="5">
    </label>
<label class="ls-label col-md-2">
        <b class="ls-label-text">29 - Código 6</b>
        <input type="text" name="turma_info_codigo_6_29" value="<?php echo $row_turmaEditar["turma_info_codigo_6_29"]; ?>" maxlength="5">
    </label>

<label class="ls-label col-md-6">
        <b class="ls-label-text">30 - Local de funcionamento diferenciado da turma</b>
        <div class="ls-custom-select">
            <select name="turma_info_local_de_funcionamento_diferenciado_da_t_30" class="ls-select">
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_turmaEditar['turma_info_local_de_funcionamento_diferenciado_da_t_30'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>A turma não está em local de funcionamento diferenciado</option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_turmaEditar['turma_info_local_de_funcionamento_diferenciado_da_t_30'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>Sala anexa</option>
                <option value="2" <?php if (!(strcmp("2", htmlentities($row_turmaEditar['turma_info_local_de_funcionamento_diferenciado_da_t_30'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>Unidade de atendimento socioeducativo</option>
                <option value="3" <?php if (!(strcmp("3", htmlentities($row_turmaEditar['turma_info_local_de_funcionamento_diferenciado_da_t_30'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>Unidade prisional</option>
            </select>
        </div>
    </label>
<label class="ls-label col-md-6">
        <b class="ls-label-text">31 - Modalidade</b>
        <div class="ls-custom-select">
            <select name="turma_info_modalidade_31" class="ls-select">
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_turmaEditar['turma_info_modalidade_31'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>1 - Ensino regular</option>
                <option value="2" <?php if (!(strcmp("2", htmlentities($row_turmaEditar['turma_info_modalidade_31'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>> 2 - Educação especial</option>
                <option value="3" <?php if (!(strcmp("3", htmlentities($row_turmaEditar['turma_info_modalidade_31'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>3 - Educação de jovens e adultos (EJA)</option>
                <option value="4" <?php if (!(strcmp("4", htmlentities($row_turmaEditar['turma_info_modalidade_31'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>4 - Educação profissional</option>
            </select>
        </div>
    </label>

    <label class="ls-label col-md-12"><h4 class="ls-title-4 ls-color-theme ls-box ls-box-gray">34 a 39 -	Formas de organização da turma</h4></label>


    <label class="ls-label col-md-4">
        <b class="ls-label-text">35 - Períodos semestrais</b>
        <div class="ls-custom-select">
            <select name="turma_info_periodos_semestrais_35" class="ls-select">
                <option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_turmaEditar['turma_info_periodos_semestrais_35'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_turmaEditar['turma_info_periodos_semestrais_35'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>
<label class="ls-label col-md-4">
        <b class="ls-label-text">36 - Ciclo(s)</b>
        <div class="ls-custom-select">
            <select name="turma_info_ciclos_36" class="ls-select"><option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_turmaEditar['turma_info_ciclos_36'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_turmaEditar['turma_info_ciclos_36'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>
<label class="ls-label col-md-4">
        <b class="ls-label-text">37 - Grupos não seriados com base na idade ou competência</b>
        <div class="ls-custom-select">
            <select name="turma_info_grupos_nao_seriados_com_base_na_idade_ou_37" class="ls-select"><option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_turmaEditar['turma_info_grupos_nao_seriados_com_base_na_idade_ou_37'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_turmaEditar['turma_info_grupos_nao_seriados_com_base_na_idade_ou_37'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>
<label class="ls-label col-md-12">
        <b class="ls-label-text">38 - Módulos</b>
        <div class="ls-custom-select">
            <select name="turma_info_modulos_38" class="ls-select"><option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_turmaEditar['turma_info_modulos_38'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_turmaEditar['turma_info_modulos_38'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>
<label class="ls-label col-md-12">
        <b class="ls-label-text">39 - Alternância regular de períodos de estudos (proposta pedagógica de formação por alternância: tempo-escola e tempo-comunidade)</b>
        <div class="ls-custom-select">
            <select name="turma_info_alternancia_regular_de_periodos_de_estud_39" class="ls-select"><option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_turmaEditar['turma_info_alternancia_regular_de_periodos_de_estud_39'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_turmaEditar['turma_info_alternancia_regular_de_periodos_de_estud_39'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

    <label class="ls-label col-md-12"><h4 class="ls-title-4 ls-color-theme ls-box ls-box-gray">40 a 48	- Unidade curricular	</h4></label>


<label class="ls-label col-md-4">
        <b class="ls-label-text">40 - Eletivas</b>
        <div class="ls-custom-select">
            <select name="turma_info_eletivas_40" class="ls-select"><option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_turmaEditar['turma_info_eletivas_40'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_turmaEditar['turma_info_eletivas_40'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>
<label class="ls-label col-md-4">
        <b class="ls-label-text">41 - Libras</b>
        <div class="ls-custom-select">
            <select name="turma_info_libras_41" class="ls-select"><option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_turmaEditar['turma_info_libras_41'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_turmaEditar['turma_info_libras_41'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>
<label class="ls-label col-md-4">
        <b class="ls-label-text">42 - Língua indígena</b>
        <div class="ls-custom-select">
            <select name="turma_info_lingua_indigena_42" class="ls-select"><option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_turmaEditar['turma_info_lingua_indigena_42'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_turmaEditar['turma_info_lingua_indigena_42'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>
<label class="ls-label col-md-4">
        <b class="ls-label-text">43 - Língua/Literatura estrangeira - Espanhol</b>
        <div class="ls-custom-select">
            <select name="turma_info_lingua_literatura_estrangeira_espanhol_43" class="ls-select"><option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_turmaEditar['turma_info_lingua_literatura_estrangeira_espanhol_43'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_turmaEditar['turma_info_lingua_literatura_estrangeira_espanhol_43'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>
<label class="ls-label col-md-4">
        <b class="ls-label-text">44 - Língua/Literatura estrangeira - Francês</b>
        <div class="ls-custom-select">
            <select name="turma_info_lingua_literatura_estrangeira_frances_44" class="ls-select"><option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_turmaEditar['turma_info_lingua_literatura_estrangeira_frances_44'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_turmaEditar['turma_info_lingua_literatura_estrangeira_frances_44'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>
<label class="ls-label col-md-4">
        <b class="ls-label-text">45 - Língua/Literatura estrangeira - outra</b>
        <div class="ls-custom-select">
            <select name="turma_info_lingua_literatura_estrangeira_outra_45" class="ls-select"><option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_turmaEditar['turma_info_lingua_literatura_estrangeira_outra_45'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_turmaEditar['turma_info_lingua_literatura_estrangeira_outra_45'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>
<label class="ls-label col-md-12">
        <b class="ls-label-text">46 - Projeto de vida</b>
        <div class="ls-custom-select">
            <select name="turma_info_projeto_de_vida_46" class="ls-select"><option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_turmaEditar['turma_info_projeto_de_vida_46'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_turmaEditar['turma_info_projeto_de_vida_46'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>
<label class="ls-label col-md-12">
        <b class="ls-label-text">47 - Trilhas de aprofundamento/aprendizagens</b>
        <div class="ls-custom-select">
            <select name="turma_info_trilhas_de_aprofundamento_aprendizagens_47" class="ls-select"><option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_turmaEditar['turma_info_trilhas_de_aprofundamento_aprendizagens_47'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_turmaEditar['turma_info_trilhas_de_aprofundamento_aprendizagens_47'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>
<label class="ls-label col-md-12">
        <b class="ls-label-text">48 - Outra(s) unidade(s) curricular(es) obrigatória(s)</b>
        <input type="text" name="turma_info_outras_unidades_curriculares_obrigatoria_48" value="<?php echo $row_turmaEditar["turma_info_outras_unidades_curriculares_obrigatoria_48"]; ?>" maxlength="256">
    </label>

    <label class="ls-label col-md-12"><h4 class="ls-title-4 ls-color-theme ls-box ls-box-gray">49 a 75	- Áreas do conhecimento/componentes curriculares	</h4></label>

    

    <fieldset>
    <div class='ls-label col-md-12'>
      <p><b class='ls-label-text'>49 - 1. Química</b></p>
      <label class='ls-label-text'>
       <input type='radio' name='turma_info_1_quimica_49' class='s-field-radio' value='0' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_1_quimica_49'], ENT_COMPAT, 'utf-8'),'0'))) {echo 'checked=checked';} ?>>
        Não oferece a área do conhec/comp curricular
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_1_quimica_49' class='s-field-radio' value='1' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_1_quimica_49'], ENT_COMPAT, 'utf-8'),'1'))) {echo 'checked=checked';} ?>>
        Sim, oferece (com docente vinculado)
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_1_quimica_49' class='s-field-radio' value='2' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_1_quimica_49'], ENT_COMPAT, 'utf-8'),'2'))) {echo 'checked=checked';} ?>>
        Sim, oferece (sem docente vinculado)
      </label>
    </div>
  </fieldset><br>
<fieldset>
    <div class='ls-label col-md-12'>
      <p><b class='ls-label-text'>50 - 2. Física</b></p>
      <label class='ls-label-text'>
       <input type='radio' name='turma_info_2_fisica_50' class='s-field-radio' value='0' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_2_fisica_50'], ENT_COMPAT, 'utf-8'),'0'))) {echo 'checked=checked';} ?>>
        Não oferece a área do conhec/comp curricular
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_2_fisica_50' class='s-field-radio' value='1' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_2_fisica_50'], ENT_COMPAT, 'utf-8'),'1'))) {echo 'checked=checked';} ?>>
        Sim, oferece (com docente vinculado)
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_2_fisica_50' class='s-field-radio' value='2' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_2_fisica_50'], ENT_COMPAT, 'utf-8'),'2'))) {echo 'checked=checked';} ?>>
        Sim, oferece (sem docente vinculado)
      </label>
    </div>
  </fieldset><br>
<fieldset>
    <div class='ls-label col-md-12'>
      <p><b class='ls-label-text'>51 - 3. Matemática</b></p>
      <label class='ls-label-text'>
       <input type='radio' name='turma_info_3_matematica_51' class='s-field-radio' value='0' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_3_matematica_51'], ENT_COMPAT, 'utf-8'),'0'))) {echo 'checked=checked';} ?>>
        Não oferece a área do conhec/comp curricular
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_3_matematica_51' class='s-field-radio' value='1' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_3_matematica_51'], ENT_COMPAT, 'utf-8'),'1'))) {echo 'checked=checked';} ?>>
        Sim, oferece (com docente vinculado)
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_3_matematica_51' class='s-field-radio' value='2' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_3_matematica_51'], ENT_COMPAT, 'utf-8'),'2'))) {echo 'checked=checked';} ?>>
        Sim, oferece (sem docente vinculado)
      </label>
    </div>
  </fieldset><br>
<fieldset>
    <div class='ls-label col-md-12'>
      <p><b class='ls-label-text'>52 - 4. Biologia</b></p>
      <label class='ls-label-text'>
       <input type='radio' name='turma_info_4_biologia_52' class='s-field-radio' value='0' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_4_biologia_52'], ENT_COMPAT, 'utf-8'),'0'))) {echo 'checked=checked';} ?>>
        Não oferece a área do conhec/comp curricular
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_4_biologia_52' class='s-field-radio' value='1' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_4_biologia_52'], ENT_COMPAT, 'utf-8'),'1'))) {echo 'checked=checked';} ?>>
        Sim, oferece (com docente vinculado)
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_4_biologia_52' class='s-field-radio' value='2' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_4_biologia_52'], ENT_COMPAT, 'utf-8'),'2'))) {echo 'checked=checked';} ?>>
        Sim, oferece (sem docente vinculado)
      </label>
    </div>
  </fieldset><br>
<fieldset>
    <div class='ls-label col-md-12'>
      <p><b class='ls-label-text'>53 - 5. Ciências</b></p>
      <label class='ls-label-text'>
       <input type='radio' name='turma_info_5_ciencias_53' class='s-field-radio' value='0' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_5_ciencias_53'], ENT_COMPAT, 'utf-8'),'0'))) {echo 'checked=checked';} ?>>
        Não oferece a área do conhec/comp curricular
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_5_ciencias_53' class='s-field-radio' value='1' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_5_ciencias_53'], ENT_COMPAT, 'utf-8'),'1'))) {echo 'checked=checked';} ?>>
        Sim, oferece (com docente vinculado)
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_5_ciencias_53' class='s-field-radio' value='2' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_5_ciencias_53'], ENT_COMPAT, 'utf-8'),'2'))) {echo 'checked=checked';} ?>>
        Sim, oferece (sem docente vinculado)
      </label>
    </div>
  </fieldset><br>
<fieldset>
    <div class='ls-label col-md-12'>
      <p><b class='ls-label-text'>54 - 6. Língua/Literatura Portuguesa</b></p>
      <label class='ls-label-text'>
       <input type='radio' name='turma_info_6_lingua_literatura_portuguesa_54' class='s-field-radio' value='0' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_6_lingua_literatura_portuguesa_54'], ENT_COMPAT, 'utf-8'),'0'))) {echo 'checked=checked';} ?>>
        Não oferece a área do conhec/comp curricular
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_6_lingua_literatura_portuguesa_54' class='s-field-radio' value='1' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_6_lingua_literatura_portuguesa_54'], ENT_COMPAT, 'utf-8'),'1'))) {echo 'checked=checked';} ?>>
        Sim, oferece (com docente vinculado)
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_6_lingua_literatura_portuguesa_54' class='s-field-radio' value='2' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_6_lingua_literatura_portuguesa_54'], ENT_COMPAT, 'utf-8'),'2'))) {echo 'checked=checked';} ?>>
        Sim, oferece (sem docente vinculado)
      </label>
    </div>
  </fieldset><br>
<fieldset>
    <div class='ls-label col-md-12'>
      <p><b class='ls-label-text'>55 - 7. Língua/Literatura Estrangeira – Inglês</b></p>
      <label class='ls-label-text'>
       <input type='radio' name='turma_info_7_lingua_literatura_estrangeira_ingles_55' class='s-field-radio' value='0' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_7_lingua_literatura_estrangeira_ingles_55'], ENT_COMPAT, 'utf-8'),'0'))) {echo 'checked=checked';} ?>>
        Não oferece a área do conhec/comp curricular
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_7_lingua_literatura_estrangeira_ingles_55' class='s-field-radio' value='1' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_7_lingua_literatura_estrangeira_ingles_55'], ENT_COMPAT, 'utf-8'),'1'))) {echo 'checked=checked';} ?>>
        Sim, oferece (com docente vinculado)
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_7_lingua_literatura_estrangeira_ingles_55' class='s-field-radio' value='2' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_7_lingua_literatura_estrangeira_ingles_55'], ENT_COMPAT, 'utf-8'),'2'))) {echo 'checked=checked';} ?>>
        Sim, oferece (sem docente vinculado)
      </label>
    </div>
  </fieldset><br>
<fieldset>
    <div class='ls-label col-md-12'>
      <p><b class='ls-label-text'>56 - 8. Língua/Literatura Estrangeira – Espanhol</b></p>
      <label class='ls-label-text'>
       <input type='radio' name='turma_info_8_lingua_literatura_estrangeira_espanhol_56' class='s-field-radio' value='0' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_8_lingua_literatura_estrangeira_espanhol_56'], ENT_COMPAT, 'utf-8'),'0'))) {echo 'checked=checked';} ?>>
        Não oferece a área do conhec/comp curricular
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_8_lingua_literatura_estrangeira_espanhol_56' class='s-field-radio' value='1' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_8_lingua_literatura_estrangeira_espanhol_56'], ENT_COMPAT, 'utf-8'),'1'))) {echo 'checked=checked';} ?>>
        Sim, oferece (com docente vinculado)
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_8_lingua_literatura_estrangeira_espanhol_56' class='s-field-radio' value='2' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_8_lingua_literatura_estrangeira_espanhol_56'], ENT_COMPAT, 'utf-8'),'2'))) {echo 'checked=checked';} ?>>
        Sim, oferece (sem docente vinculado)
      </label>
    </div>
  </fieldset><br>
<fieldset>
    <div class='ls-label col-md-12'>
      <p><b class='ls-label-text'>57 - 9. Língua/Literatura Estrangeira – outra</b></p>
      <label class='ls-label-text'>
       <input type='radio' name='turma_info_9_lingua_literatura_estrangeira_outra_57' class='s-field-radio' value='0' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_9_lingua_literatura_estrangeira_outra_57'], ENT_COMPAT, 'utf-8'),'0'))) {echo 'checked=checked';} ?>>
        Não oferece a área do conhec/comp curricular
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_9_lingua_literatura_estrangeira_outra_57' class='s-field-radio' value='1' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_9_lingua_literatura_estrangeira_outra_57'], ENT_COMPAT, 'utf-8'),'1'))) {echo 'checked=checked';} ?>>
        Sim, oferece (com docente vinculado)
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_9_lingua_literatura_estrangeira_outra_57' class='s-field-radio' value='2' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_9_lingua_literatura_estrangeira_outra_57'], ENT_COMPAT, 'utf-8'),'2'))) {echo 'checked=checked';} ?>>
        Sim, oferece (sem docente vinculado)
      </label>
    </div>
  </fieldset><br>
<fieldset>
    <div class='ls-label col-md-12'>
      <p><b class='ls-label-text'>58 - 10. Arte (Educação Artística, Teatro, Dança, Música, Artes Plásticas e outras)</b></p>
      <label class='ls-label-text'>
       <input type='radio' name='turma_info_10_arte_educacao_artistica_teatro_danca__58' class='s-field-radio' value='0' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_10_arte_educacao_artistica_teatro_danca__58'], ENT_COMPAT, 'utf-8'),'0'))) {echo 'checked=checked';} ?>>
        Não oferece a área do conhec/comp curricular
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_10_arte_educacao_artistica_teatro_danca__58' class='s-field-radio' value='1' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_10_arte_educacao_artistica_teatro_danca__58'], ENT_COMPAT, 'utf-8'),'1'))) {echo 'checked=checked';} ?>>
        Sim, oferece (com docente vinculado)
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_10_arte_educacao_artistica_teatro_danca__58' class='s-field-radio' value='2' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_10_arte_educacao_artistica_teatro_danca__58'], ENT_COMPAT, 'utf-8'),'2'))) {echo 'checked=checked';} ?>>
        Sim, oferece (sem docente vinculado)
      </label>
    </div>
  </fieldset><br>
<fieldset>
    <div class='ls-label col-md-12'>
      <p><b class='ls-label-text'>59 - 11. Educação Física</b></p>
      <label class='ls-label-text'>
       <input type='radio' name='turma_info_11_educacao_fisica_59' class='s-field-radio' value='0' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_11_educacao_fisica_59'], ENT_COMPAT, 'utf-8'),'0'))) {echo 'checked=checked';} ?>>
        Não oferece a área do conhec/comp curricular
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_11_educacao_fisica_59' class='s-field-radio' value='1' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_11_educacao_fisica_59'], ENT_COMPAT, 'utf-8'),'1'))) {echo 'checked=checked';} ?>>
        Sim, oferece (com docente vinculado)
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_11_educacao_fisica_59' class='s-field-radio' value='2' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_11_educacao_fisica_59'], ENT_COMPAT, 'utf-8'),'2'))) {echo 'checked=checked';} ?>>
        Sim, oferece (sem docente vinculado)
      </label>
    </div>
  </fieldset><br>
<fieldset>
    <div class='ls-label col-md-12'>
      <p><b class='ls-label-text'>60 - 12. História</b></p>
      <label class='ls-label-text'>
       <input type='radio' name='turma_info_12_historia_60' class='s-field-radio' value='0' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_12_historia_60'], ENT_COMPAT, 'utf-8'),'0'))) {echo 'checked=checked';} ?>>
        Não oferece a área do conhec/comp curricular
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_12_historia_60' class='s-field-radio' value='1' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_12_historia_60'], ENT_COMPAT, 'utf-8'),'1'))) {echo 'checked=checked';} ?>>
        Sim, oferece (com docente vinculado)
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_12_historia_60' class='s-field-radio' value='2' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_12_historia_60'], ENT_COMPAT, 'utf-8'),'2'))) {echo 'checked=checked';} ?>>
        Sim, oferece (sem docente vinculado)
      </label>
    </div>
  </fieldset><br>
<fieldset>
    <div class='ls-label col-md-12'>
      <p><b class='ls-label-text'>61 - 13. Geografia</b></p>
      <label class='ls-label-text'>
       <input type='radio' name='turma_info_13_geografia_61' class='s-field-radio' value='0' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_13_geografia_61'], ENT_COMPAT, 'utf-8'),'0'))) {echo 'checked=checked';} ?>>
        Não oferece a área do conhec/comp curricular
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_13_geografia_61' class='s-field-radio' value='1' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_13_geografia_61'], ENT_COMPAT, 'utf-8'),'1'))) {echo 'checked=checked';} ?>>
        Sim, oferece (com docente vinculado)
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_13_geografia_61' class='s-field-radio' value='2' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_13_geografia_61'], ENT_COMPAT, 'utf-8'),'2'))) {echo 'checked=checked';} ?>>
        Sim, oferece (sem docente vinculado)
      </label>
    </div>
  </fieldset><br>
<fieldset>
    <div class='ls-label col-md-12'>
      <p><b class='ls-label-text'>62 - 14. Filosofia</b></p>
      <label class='ls-label-text'>
       <input type='radio' name='turma_info_14_filosofia_62' class='s-field-radio' value='0' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_14_filosofia_62'], ENT_COMPAT, 'utf-8'),'0'))) {echo 'checked=checked';} ?>>
        Não oferece a área do conhec/comp curricular
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_14_filosofia_62' class='s-field-radio' value='1' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_14_filosofia_62'], ENT_COMPAT, 'utf-8'),'1'))) {echo 'checked=checked';} ?>>
        Sim, oferece (com docente vinculado)
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_14_filosofia_62' class='s-field-radio' value='2' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_14_filosofia_62'], ENT_COMPAT, 'utf-8'),'2'))) {echo 'checked=checked';} ?>>
        Sim, oferece (sem docente vinculado)
      </label>
    </div>
  </fieldset><br>
<fieldset>
    <div class='ls-label col-md-12'>
      <p><b class='ls-label-text'>63 - 16. Informática/ Computação</b></p>
      <label class='ls-label-text'>
       <input type='radio' name='turma_info_16_informatica_computacao_63' class='s-field-radio' value='0' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_16_informatica_computacao_63'], ENT_COMPAT, 'utf-8'),'0'))) {echo 'checked=checked';} ?>>
        Não oferece a área do conhec/comp curricular
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_16_informatica_computacao_63' class='s-field-radio' value='1' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_16_informatica_computacao_63'], ENT_COMPAT, 'utf-8'),'1'))) {echo 'checked=checked';} ?>>
        Sim, oferece (com docente vinculado)
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_16_informatica_computacao_63' class='s-field-radio' value='2' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_16_informatica_computacao_63'], ENT_COMPAT, 'utf-8'),'2'))) {echo 'checked=checked';} ?>>
        Sim, oferece (sem docente vinculado)
      </label>
    </div>
  </fieldset><br>
<fieldset>
    <div class='ls-label col-md-12'>
      <p><b class='ls-label-text'>64 - 17. Áreas do conhecimento profissionalizantes</b></p>
      <label class='ls-label-text'>
       <input type='radio' name='turma_info_17_areas_do_conhecimento_profissionaliza_64' class='s-field-radio' value='0' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_17_areas_do_conhecimento_profissionaliza_64'], ENT_COMPAT, 'utf-8'),'0'))) {echo 'checked=checked';} ?>>
        Não oferece a área do conhec/comp curricular
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_17_areas_do_conhecimento_profissionaliza_64' class='s-field-radio' value='1' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_17_areas_do_conhecimento_profissionaliza_64'], ENT_COMPAT, 'utf-8'),'1'))) {echo 'checked=checked';} ?>>
        Sim, oferece (com docente vinculado)
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_17_areas_do_conhecimento_profissionaliza_64' class='s-field-radio' value='2' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_17_areas_do_conhecimento_profissionaliza_64'], ENT_COMPAT, 'utf-8'),'2'))) {echo 'checked=checked';} ?>>
        Sim, oferece (sem docente vinculado)
      </label>
    </div>
  </fieldset><br>
<fieldset>
    <div class='ls-label col-md-12'>
      <p><b class='ls-label-text'>65 - 23. Libras</b></p>
      <label class='ls-label-text'>
       <input type='radio' name='turma_info_23_libras_65' class='s-field-radio' value='0' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_23_libras_65'], ENT_COMPAT, 'utf-8'),'0'))) {echo 'checked=checked';} ?>>
        Não oferece a área do conhec/comp curricular
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_23_libras_65' class='s-field-radio' value='1' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_23_libras_65'], ENT_COMPAT, 'utf-8'),'1'))) {echo 'checked=checked';} ?>>
        Sim, oferece (com docente vinculado)
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_23_libras_65' class='s-field-radio' value='2' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_23_libras_65'], ENT_COMPAT, 'utf-8'),'2'))) {echo 'checked=checked';} ?>>
        Sim, oferece (sem docente vinculado)
      </label>
    </div>
  </fieldset><br>
<fieldset>
    <div class='ls-label col-md-12'>
      <p><b class='ls-label-text'>66 - 25. Áreas do conhecimento pedagógicas</b></p>
      <label class='ls-label-text'>
       <input type='radio' name='turma_info_25_areas_do_conhecimento_pedagogicas_66' class='s-field-radio' value='0' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_25_areas_do_conhecimento_pedagogicas_66'], ENT_COMPAT, 'utf-8'),'0'))) {echo 'checked=checked';} ?>>
        Não oferece a área do conhec/comp curricular
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_25_areas_do_conhecimento_pedagogicas_66' class='s-field-radio' value='1' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_25_areas_do_conhecimento_pedagogicas_66'], ENT_COMPAT, 'utf-8'),'1'))) {echo 'checked=checked';} ?>>
        Sim, oferece (com docente vinculado)
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_25_areas_do_conhecimento_pedagogicas_66' class='s-field-radio' value='2' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_25_areas_do_conhecimento_pedagogicas_66'], ENT_COMPAT, 'utf-8'),'2'))) {echo 'checked=checked';} ?>>
        Sim, oferece (sem docente vinculado)
      </label>
    </div>
  </fieldset><br>
<fieldset>
    <div class='ls-label col-md-12'>
      <p><b class='ls-label-text'>67 - 26. Ensino Religioso</b></p>
      <label class='ls-label-text'>
       <input type='radio' name='turma_info_26_ensino_religioso_67' class='s-field-radio' value='0' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_26_ensino_religioso_67'], ENT_COMPAT, 'utf-8'),'0'))) {echo 'checked=checked';} ?>>
        Não oferece a área do conhec/comp curricular
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_26_ensino_religioso_67' class='s-field-radio' value='1' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_26_ensino_religioso_67'], ENT_COMPAT, 'utf-8'),'1'))) {echo 'checked=checked';} ?>>
        Sim, oferece (com docente vinculado)
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_26_ensino_religioso_67' class='s-field-radio' value='2' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_26_ensino_religioso_67'], ENT_COMPAT, 'utf-8'),'2'))) {echo 'checked=checked';} ?>>
        Sim, oferece (sem docente vinculado)
      </label>
    </div>
  </fieldset><br>
<fieldset>
    <div class='ls-label col-md-12'>
      <p><b class='ls-label-text'>68 - 27. Língua Indígena</b></p>
      <label class='ls-label-text'>
       <input type='radio' name='turma_info_27_lingua_indigena_68' class='s-field-radio' value='0' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_27_lingua_indigena_68'], ENT_COMPAT, 'utf-8'),'0'))) {echo 'checked=checked';} ?>>
        Não oferece a área do conhec/comp curricular
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_27_lingua_indigena_68' class='s-field-radio' value='1' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_27_lingua_indigena_68'], ENT_COMPAT, 'utf-8'),'1'))) {echo 'checked=checked';} ?>>
        Sim, oferece (com docente vinculado)
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_27_lingua_indigena_68' class='s-field-radio' value='2' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_27_lingua_indigena_68'], ENT_COMPAT, 'utf-8'),'2'))) {echo 'checked=checked';} ?>>
        Sim, oferece (sem docente vinculado)
      </label>
    </div>
  </fieldset><br>
<fieldset>
    <div class='ls-label col-md-12'>
      <p><b class='ls-label-text'>69 - 28. Estudos Sociais</b></p>
      <label class='ls-label-text'>
       <input type='radio' name='turma_info_28_estudos_sociais_69' class='s-field-radio' value='0' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_28_estudos_sociais_69'], ENT_COMPAT, 'utf-8'),'0'))) {echo 'checked=checked';} ?>>
        Não oferece a área do conhec/comp curricular
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_28_estudos_sociais_69' class='s-field-radio' value='1' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_28_estudos_sociais_69'], ENT_COMPAT, 'utf-8'),'1'))) {echo 'checked=checked';} ?>>
        Sim, oferece (com docente vinculado)
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_28_estudos_sociais_69' class='s-field-radio' value='2' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_28_estudos_sociais_69'], ENT_COMPAT, 'utf-8'),'2'))) {echo 'checked=checked';} ?>>
        Sim, oferece (sem docente vinculado)
      </label>
    </div>
  </fieldset><br>
<fieldset>
    <div class='ls-label col-md-12'>
      <p><b class='ls-label-text'>70 - 29. Sociologia</b></p>
      <label class='ls-label-text'>
       <input type='radio' name='turma_info_29_sociologia_70' class='s-field-radio' value='0' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_29_sociologia_70'], ENT_COMPAT, 'utf-8'),'0'))) {echo 'checked=checked';} ?>>
        Não oferece a área do conhec/comp curricular
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_29_sociologia_70' class='s-field-radio' value='1' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_29_sociologia_70'], ENT_COMPAT, 'utf-8'),'1'))) {echo 'checked=checked';} ?>>
        Sim, oferece (com docente vinculado)
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_29_sociologia_70' class='s-field-radio' value='2' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_29_sociologia_70'], ENT_COMPAT, 'utf-8'),'2'))) {echo 'checked=checked';} ?>>
        Sim, oferece (sem docente vinculado)
      </label>
    </div>
  </fieldset><br>
<fieldset>
    <div class='ls-label col-md-12'>
      <p><b class='ls-label-text'>71 - 30. Língua/Literatura Estrangeira – Francês</b></p>
      <label class='ls-label-text'>
       <input type='radio' name='turma_info_30_lingua_literatura_estrangeira_frances_71' class='s-field-radio' value='0' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_30_lingua_literatura_estrangeira_frances_71'], ENT_COMPAT, 'utf-8'),'0'))) {echo 'checked=checked';} ?>>
        Não oferece a área do conhec/comp curricular
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_30_lingua_literatura_estrangeira_frances_71' class='s-field-radio' value='1' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_30_lingua_literatura_estrangeira_frances_71'], ENT_COMPAT, 'utf-8'),'1'))) {echo 'checked=checked';} ?>>
        Sim, oferece (com docente vinculado)
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_30_lingua_literatura_estrangeira_frances_71' class='s-field-radio' value='2' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_30_lingua_literatura_estrangeira_frances_71'], ENT_COMPAT, 'utf-8'),'2'))) {echo 'checked=checked';} ?>>
        Sim, oferece (sem docente vinculado)
      </label>
    </div>
  </fieldset><br>
<fieldset>
    <div class='ls-label col-md-12'>
      <p><b class='ls-label-text'>72 - 31. Língua Portuguesa como Segunda Língua</b></p>
      <label class='ls-label-text'>
       <input type='radio' name='turma_info_31_lingua_portuguesa_como_segunda_lingua_72' class='s-field-radio' value='0' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_31_lingua_portuguesa_como_segunda_lingua_72'], ENT_COMPAT, 'utf-8'),'0'))) {echo 'checked=checked';} ?>>
        Não oferece a área do conhec/comp curricular
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_31_lingua_portuguesa_como_segunda_lingua_72' class='s-field-radio' value='1' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_31_lingua_portuguesa_como_segunda_lingua_72'], ENT_COMPAT, 'utf-8'),'1'))) {echo 'checked=checked';} ?>>
        Sim, oferece (com docente vinculado)
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_31_lingua_portuguesa_como_segunda_lingua_72' class='s-field-radio' value='2' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_31_lingua_portuguesa_como_segunda_lingua_72'], ENT_COMPAT, 'utf-8'),'2'))) {echo 'checked=checked';} ?>>
        Sim, oferece (sem docente vinculado)
      </label>
    </div>
  </fieldset><br>
<fieldset>
    <div class='ls-label col-md-12'>
      <p><b class='ls-label-text'>73 - 32. Estágio curricular supervisionado</b></p>
      <label class='ls-label-text'>
       <input type='radio' name='turma_info_32_estagio_curricular_supervisionado_73' class='s-field-radio' value='0' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_32_estagio_curricular_supervisionado_73'], ENT_COMPAT, 'utf-8'),'0'))) {echo 'checked=checked';} ?>>
        Não oferece a área do conhec/comp curricular
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_32_estagio_curricular_supervisionado_73' class='s-field-radio' value='1' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_32_estagio_curricular_supervisionado_73'], ENT_COMPAT, 'utf-8'),'1'))) {echo 'checked=checked';} ?>>
        Sim, oferece (com docente vinculado)
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_32_estagio_curricular_supervisionado_73' class='s-field-radio' value='2' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_32_estagio_curricular_supervisionado_73'], ENT_COMPAT, 'utf-8'),'2'))) {echo 'checked=checked';} ?>>
        Sim, oferece (sem docente vinculado)
      </label>
    </div>
  </fieldset><br>
<fieldset>
    <div class='ls-label col-md-12'>
      <p><b class='ls-label-text'>74 - 33. Projeto de vida</b></p>
      <label class='ls-label-text'>
       <input type='radio' name='turma_info_33_projeto_de_vida_74' class='s-field-radio' value='0' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_33_projeto_de_vida_74'], ENT_COMPAT, 'utf-8'),'0'))) {echo 'checked=checked';} ?>>
        Não oferece a área do conhec/comp curricular
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_33_projeto_de_vida_74' class='s-field-radio' value='1' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_33_projeto_de_vida_74'], ENT_COMPAT, 'utf-8'),'1'))) {echo 'checked=checked';} ?>>
        Sim, oferece (com docente vinculado)
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_33_projeto_de_vida_74' class='s-field-radio' value='2' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_33_projeto_de_vida_74'], ENT_COMPAT, 'utf-8'),'2'))) {echo 'checked=checked';} ?>>
        Sim, oferece (sem docente vinculado)
      </label>
    </div>
  </fieldset><br>
<fieldset>
    <div class='ls-label col-md-12'>
      <p><b class='ls-label-text'>75 - 99. Outras áreas do conhecimento</b></p>
      <label class='ls-label-text'>
       <input type='radio' name='turma_info_99_outras_areas_do_conhecimento_75' class='s-field-radio' value='0' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_99_outras_areas_do_conhecimento_75'], ENT_COMPAT, 'utf-8'),'0'))) {echo 'checked=checked';} ?>>
        Não oferece a área do conhec/comp curricular
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_99_outras_areas_do_conhecimento_75' class='s-field-radio' value='1' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_99_outras_areas_do_conhecimento_75'], ENT_COMPAT, 'utf-8'),'1'))) {echo 'checked=checked';} ?>>
        Sim, oferece (com docente vinculado)
      </label>
      <label class='ls-label-text'>
        <input type='radio' name='turma_info_99_outras_areas_do_conhecimento_75' class='s-field-radio' value='2' <?php if (!(strcmp(htmlentities($row_turmaEditar['turma_info_99_outras_areas_do_conhecimento_75'], ENT_COMPAT, 'utf-8'),'2'))) {echo 'checked=checked';} ?>>
        Sim, oferece (sem docente vinculado)
      </label>
    </div>
  </fieldset><br>








<label class="ls-label col-md-12">
        <b class="ls-label-text">76 - Classe bilíngue de surdos tendo a Libras (Língua Brasileira de Sinais) como língua de instrução, ensino, comunicação e interação e a língua portuguesa escrita como segunda língua</b>
        <div class="ls-custom-select">
            <select name="turma_info_classe_bilingue_de_surdos_tendo_a_libras_76" class="ls-select"><option value=""></option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_turmaEditar['turma_info_classe_bilingue_de_surdos_tendo_a_libras_76'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_turmaEditar['turma_info_classe_bilingue_de_surdos_tendo_a_libras_76'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
            </select>
        </div>
    </label>      

 

			<hr>
            
			<div class="col-md-12">
              <input type="submit" value="EDITAR TURMA" class="ls-btn-primary">        
              <a class="ls-btn-dark" href="turmaListar.php">CANCELAR</a>            
            </div>
			
          <input type="hidden" name="turma_id" value="<?php echo $row_turmaEditar['turma_id']; ?>">
          <input type="hidden" name="MM_update" value="form1">
		  
		    <input type="hidden" name="usu_id" value="<?php echo $row_UsuLogado['usu_id']; ?>">
            <input type="hidden" name="usu_escola" value="<?php echo $row_UsuLogado['usu_escola']; ?>">
			<input type="hidden" name="detalhes" value="<?php echo htmlentities($row_turmaEditar['turma_nome'], ENT_COMPAT, 'utf-8'); ?>">

		  
        </form>
        <p>&nbsp;</p>
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
          <li class="ls-txt-center hidden-xs">
            <a href="tutoriais_video.php" class="ls-btn-dark ls-btn-tour">Tutorial (Vídeos)</a>
          </li>
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

mysql_free_result($EscolaLogada);

mysql_free_result($listarEtapa);

mysql_free_result($turmaEditar);
?>
