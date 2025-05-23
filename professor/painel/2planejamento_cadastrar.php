<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "../../sistema/funcoes/anoLetivo.php"; ?>
<?php include('../../sistema/funcoes/url_base.php'); ?>
<?php include('../../sistema/funcoes/inverteData.php'); ?>
<?php include('../../sistema/funcoes/anti_injection.php'); ?>
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
$MM_authorizedUsers = "7";
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

$colname_ProfLogado = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_ProfLogado = $_SESSION['MM_Username'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ProfLogado = sprintf("SELECT func_id, func_nome, func_email, func_foto FROM smc_func WHERE func_id = %s", GetSQLValueString($colname_ProfLogado, "text"));
$ProfLogado = mysql_query($query_ProfLogado, $SmecelNovo) or die(mysql_error());
$row_ProfLogado = mysql_fetch_assoc($ProfLogado);
$totalRows_ProfLogado = mysql_num_rows($ProfLogado);

if($totalRows_ProfLogado=="") {
	header("Location:../index.php?loginErr");
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Vinculos = "SELECT vinculo_id, vinculo_id_escola, vinculo_id_sec, vinculo_id_funcionario FROM smc_vinculo WHERE vinculo_id_funcionario = '$row_ProfLogado[func_id]'";
$Vinculos = mysql_query($query_Vinculos, $SmecelNovo) or die(mysql_error());
$row_Vinculos = mysql_fetch_assoc($Vinculos);
$totalRows_Vinculos = mysql_num_rows($Vinculos);
include "fnc/anoLetivo.php";

$colname_Componente = "-1";
if (isset($_GET['componente'])) {
  $colname_Componente = anti_injection($_GET['componente']);
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Componente = sprintf("SELECT disciplina_id, disciplina_area_conhecimento_id, disciplina_codigo_inep, disciplina_ordem, disciplina_nome, disciplina_nome_abrev, disciplina_cor_fundo, disciplina_bncc FROM smc_disciplina WHERE disciplina_id = %s", GetSQLValueString($colname_Componente, "int"));
$Componente = mysql_query($query_Componente, $SmecelNovo) or die(mysql_error());
$row_Componente = mysql_fetch_assoc($Componente);
$totalRows_Componente = mysql_num_rows($Componente);

$colname_Etapa = "-1";
if (isset($_GET['etapa'])) {
  $colname_Etapa = anti_injection($_GET['etapa']);
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Etapa = sprintf("SELECT etapa_id, etapa_id_filtro, etapa_nome, etapa_idade, etapa_limite_turma, etapa_nome_abrev FROM smc_etapa WHERE etapa_id = %s", GetSQLValueString($colname_Etapa, "int"));
$Etapa = mysql_query($query_Etapa, $SmecelNovo) or die(mysql_error());
$row_Etapa = mysql_fetch_assoc($Etapa);
$totalRows_Etapa = mysql_num_rows($Etapa);

$escola = "-1";
if (isset($_GET['cod'])) {
  $escola = anti_injection($_GET['cod']);
}

$componente = "-1";
if (isset($_GET['componente'])) {
  $componente = anti_injection($_GET['componente']);
}

$etapa = "-1";
if (isset($_GET['etapa'])) {
  $etapa = anti_injection($_GET['etapa']);
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
  ac_ce_et
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
  %s)",
                       
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
                       GetSQLValueString($_POST['ac_ce_et'], "text")
					   
					   
					   
					   
					   );

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

  $insertGoTo = "ac.php?cadastrado";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
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
<title><?php echo $row_ProfLogado['func_nome']?>-</title>

<!--Import Google Icon Font-->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<!--Import materialize.css-->
<link type="text/css" rel="stylesheet" href="../css/materialize.min.css"  media="screen,projection"/>
<link type="text/css" rel="stylesheet" href="../css/app.css"  media="screen,projection"/>

<!--Let browser know website is optimized for mobile-->
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
<style>
table {
	width:100%;
	border-collapse: collapse;
	font-size:12px;
}
th, td {
	border:1px solid #ccc;
	padding:5px;
	height:15px;
	line-height:15px;
}
</style>
</head>

<body class="indigo lighten-5">
<?php include ("menu_top.php"); ?>
<div class="section no-pad-bot" id="index-banner">
  <div class="container">
    <div class="row white" style="margin: 10px 0;">
      <div class="col s12 m2 hide-on-small-only">
        <p>
          <?php if ($row_ProfLogado['func_foto']=="") { ?>
          <img src="<?php echo URL_BASE.'professor/fotos/' ?>semfoto.jpg" width="100%" class="hoverable">
          <?php } else { ?>
          <img src="<?php echo URL_BASE.'professor/fotos/' ?><?php echo $row_ProfLogado['func_foto']; ?>" width="100%" class="hoverable">
          <?php } ?>
          <br>
          <small><a href="foto.php"><i class="tiny material-icons">photo_camera</i></a></small> <small style="font-size:14px;"> <?php echo current( str_word_count($row_ProfLogado['func_nome'],2)); ?>
          <?php $word = explode(" ", trim($row_ProfLogado['func_nome'])); echo $word[count($word)-1]; ?>
          </small> </p>
        <?php include "menu_esq.php"; ?>
      </div>
      <div class="col s12 m9">
      <h5 class="amber" style="padding:5px;">REGISTRO DE PLANEJAMENTO DE AULAS</h5>
      <hr>
      <a href="index.php" class="waves-effect waves-light btn-small btn-flat"><i class="material-icons left">arrow_back</i> Voltar</a>
      <blockquote>
        <div class="row">
          <div class="col s6"> <small>Componente curricular:<br>
            </small><?php echo $row_Componente['disciplina_nome']; ?> </div>
          <div class="col s6"> <small>Etapa:</small><br>
            <?php echo $row_Etapa['etapa_nome']; ?> </div>
        </div>
      </blockquote>
      <hr>
      <p>
      <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
        <div class="row">
          <div class="input-field col s6"> <small for="textarea1">Data inicial</small>
            <input type="date" name="ac_data_inicial" value="" size="32" required>
            <label for="first_name">Data inicial</label>
          </div>
          <div class="input-field col s6"> <small for="textarea1">Data final</small>
            <input type="date" name="ac_data_final" value="" size="32" required>
            <label for="last_name">Data final</label>
          </div>
        </div>
        <div class="row">
          <div class="col s12">
            <ul class="tabs">
              <li class="tab col s6"><a class="active" href="#ei">EDUCAÇÃO INFANTIL</a></li>
              <li class="tab col s6"><a href="#ef">ENSINO FUNDAMENTAL</a></li>
            </ul>
          </div>
          <div id="ei" class="col s12">
            <h5>Educação Infantil</h5>
            <div class="row">
              <div class="col s12">
                <h6>Direitos de aprendizagem</h6>
                <p class="col s2">
                  <label>
                    <input name="ac_da_conviver" type="checkbox" />
                    <span>Conviver</span> </label>
                </p>
                <p class="col s2">
                  <label>
                    <input  name="ac_da_brincar" type="checkbox" />
                    <span>Brincar</span> </label>
                </p>
                <p class="col s2">
                  <label>
                    <input name="ac_da_participar" type="checkbox" />
                    <span>Participar</span> </label>
                </p>
                <p class="col s2">
                  <label>
                    <input  name="ac_da_explorar" type="checkbox" />
                    <span>Explorar</span> </label>
                </p>
                <p class="col s2">
                  <label>
                    <input name="ac_da_expressar" type="checkbox" />
                    <span>Expressar</span> </label>
                </p>
                <p class="col s2">
                  <label>
                    <input name="ac_da_conhecerse" type="checkbox" />
                    <span>Conhecer-se</span> </label>
                </p>
              </div>
            </div>
            <div class="row">
              <div class="input-field col s12">
                <h5 for="textarea1">EO – O eu, o outro e o nós <a class="waves-effect waves-light modal-trigger" href="#modal-eo"><i class="material-icons">class</i></a> </h5>
                <!-- Modal Trigger --> 
                
                <!-- Modal Structure -->
                <div id="modal-eo" class="modal modal-fixed-footer">
                  <div class="modal-content">
                    <h4>EO – O eu, o outro e o nós</h4>
                    <p>
                    <table>
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
                  <div class="modal-footer"> <a href="#!" class="modal-close waves-effect waves-green btn-flat">FECHAR</a> </div>
                </div>
                <textarea name="ac_ce_eo" id="" class="materialize-textarea"></textarea>
              </div>
              <div class="input-field col s12">
                <h5 for="textarea1">TS – Traços, sons, cores e formas <a class="waves-effect waves-light modal-trigger" href="#modal-ts"><i class="material-icons">class</i></a> </h5>
                
                <!-- Modal Structure -->
                <div id="modal-ts" class="modal modal-fixed-footer">
                  <div class="modal-content">
                    <h4>TS – Traços, sons, cores e formas</h4>
                    <p>
                    <table>
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
                  <div class="modal-footer"> <a href="#!" class="modal-close waves-effect waves-green btn-flat">FECHAR</a> </div>
                </div>
                <textarea name="ac_ce_ts" id="" class="materialize-textarea"></textarea>
              </div>
              <div class="input-field col s12">
                <h5 for="textarea1">EF – Escuta, fala, pensamento e imaginação <a class="waves-effect waves-light modal-trigger" href="#modal-ef"><i class="material-icons">class</i></a> </h5>
                
                <!-- Modal Structure -->
                <div id="modal-ef" class="modal modal-fixed-footer">
                  <div class="modal-content">
                    <h4>EF – Escuta, fala, pensamento e imaginação</h4>
                    <p>
                    <table>
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
                  <div class="modal-footer"> <a href="#!" class="modal-close waves-effect waves-green btn-flat">FECHAR</a> </div>
                </div>
                <textarea name="ac_ce_ef" id="" class="materialize-textarea"></textarea>
              </div>
              <div class="input-field col s12">
                <h5 for="textarea1">CG – Corpo, gestos e movimento <a class="waves-effect waves-light modal-trigger" href="#modal-cg"><i class="material-icons">class</i></a> </h5>
                
                <!-- Modal Structure -->
                <div id="modal-cg" class="modal modal-fixed-footer">
                  <div class="modal-content">
                    <h4>CG – Corpo, gestos e movimento </h4>
                    <p>
                    <table>
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
                  <div class="modal-footer"> <a href="#!" class="modal-close waves-effect waves-green btn-flat">FECHAR</a> </div>
                </div>
                <textarea name="ac_ce_cg" id="" class="materialize-textarea"></textarea>
              </div>
              <div class="input-field col s12">
                <h5 for="textarea1">ET – Espaços, tempos, quantidades, relações e transformações <a class="waves-effect waves-light modal-trigger" href="#modal-et"><i class="material-icons">class</i></a> </h5>
                
                <!-- Modal Structure -->
                <div id="modal-et" class="modal modal-fixed-footer">
                  <div class="modal-content">
                    <h4>ET – Espaços, tempos, quantidades, relações e transformações</h4>
                    <p>
                    <table>
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
                  <div class="modal-footer"> <a href="#!" class="modal-close waves-effect waves-green btn-flat">FECHAR</a> </div>
                </div>
                <textarea name="ac_ce_et" id="" class="materialize-textarea"></textarea>
              </div>
            </div>
          </div>
          <div id="ef" class="col s12">
            <h5>Ensino Fundamental</h5>
            <div class="row">
              <div class="input-field col s12">
                <h6 for="textarea1">Unidade Temática</h6>
                <textarea name="ac_recursos" id="" class="materialize-textarea"></textarea>
              </div>
              <div class="input-field col s12">
                <h6 for="textarea1">Objetos de Conhecimento (EF)</h6>
                <textarea name="ac_objeto_conhecimento" id="" class="materialize-textarea"></textarea>
              </div>
              <div class="input-field col s12">
                <h6 for="textarea1">Habilidades (EF)</h6>
                <textarea name="ac_objetivo_especifico" id="" class="materialize-textarea"></textarea>
              </div>
            </div>
          </div>
        </div>
        <h4>Complemento</h4>
        <div class="row">
          <div class="input-field col s12">
            <h6 for="textarea1">Metodologia</h6>
            <textarea name="ac_metodologia" id="" class="materialize-textarea"></textarea>
          </div>
          <div class="input-field col s12">
            <h6 for="textarea1">Avaliação</h6>
            <textarea name="ac_avaliacao" id="" class="materialize-textarea"></textarea>
          </div>
          <div class="input-field col s12">
            <h6 for="textarea1">Observação</h6>
            <textarea name="ac_conteudo" id="" class="materialize-textarea"></textarea>
          </div>
          <div class="input-field col s12">
            <input type="submit" class="btn" value="REGISTRAR AC">
            <input type="hidden" name="MM_insert" value="form1">
          </div>
        </div>
        </div>
      </form>
      </p>
    </div>
  </div>
</div>
</div>

<!--JavaScript at end of body for optimized loading--> 
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script> 
<script type="text/javascript" src="../js/materialize.min.js"></script>
<?php include ("rodape.php"); ?>
<script type="text/javascript" src="../js/app.js"></script> 
<script type="text/javascript">
		$(document).ready(function(){
			$(".dropdown-trigger").dropdown();
			$('.sidenav').sidenav();
		});
	</script> 
<script src="https://cdn.tiny.cloud/1/iq1gy8qt7s1b9bj92c2fc7whie95augu8r8kz97dgoufkli8/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script> 
<script src="langs/pt_BR.js"></script> 
<script>
	

	tinymce.init({
	  selector: 'textarea',
	  
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


$(document).ready(function(){
    $('.modal').modal();
	$('.tabs').tabs();
  });


</script>
</body>
</html>
<?php
mysql_free_result($ProfLogado);

mysql_free_result($Componente);

mysql_free_result($Etapa);
?>