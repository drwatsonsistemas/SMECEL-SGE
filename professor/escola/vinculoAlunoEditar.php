<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include('fnc/inverteData.php'); ?>
<?php //include('fnc/anoLetivo.php'); ?>
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
		header(sprintf("Location: vinculoAlunoExibirTurma.php?permissao"));
		exit;
	}
	
  $idTurma = $_POST['vinculo_aluno_id_turma'];
  if($_POST['vinculo_aluno_nao_reprova'] == "on"){
    $vinculo_aluno_nao_reprova = "S";
  }else{
    $vinculo_aluno_nao_reprova = "N";
  }
	//exit(var_dump($_POST['vinculo_aluno_historico_transferencia'] . $_POST['vinculo_aluno_da_casa']));
  $updateSQL = sprintf("UPDATE smc_vinculo_aluno SET vinculo_aluno_id_turma=%s, vinculo_aluno_situacao=%s, vinculo_aluno_transporte=%s, vinculo_aluno_ponto_id=%s, vinculo_aluno_multietapa=%s, vinculo_aluno_datatransferencia=%s, vinculo_aluno_data=%s, vinculo_aluno_internet=%s, vinculo_aluno_vacina_atualizada=%s, vinculo_aluno_dependencia=%s, vinculo_aluno_repetente=%s, vinculo_aluno_da_casa=%s, vinculo_aluno_historico_transferencia=%s,vinculo_aluno_id_cuidador=%s, vinculo_aluno_id_matriz_multi=%s, vinculo_aluno_nao_reprova=%s WHERE vinculo_aluno_id=%s",
   GetSQLValueString($_POST['vinculo_aluno_id_turma'], "int"),
   GetSQLValueString($_POST['vinculo_aluno_situacao'], "text"),
   GetSQLValueString($_POST['vinculo_aluno_transporte'], "text"),
   GetSQLValueString($_POST['vinculo_aluno_ponto_id'], "int"),
   GetSQLValueString($_POST['vinculo_aluno_multietapa'], "int"),
   GetSQLValueString(inverteData($_POST['vinculo_aluno_datatransferencia']), "date"),
   GetSQLValueString($_POST['vinculo_aluno_data'], "date"),
   GetSQLValueString($_POST['vinculo_aluno_internet'], "text"),
   GetSQLValueString($_POST['vinculo_aluno_vacina_atualizada'], "text"),
   GetSQLValueString($_POST['vinculo_aluno_dependencia'], "text"),
   GetSQLValueString($_POST['vinculo_aluno_repetente'], "text"),
   GetSQLValueString($_POST['vinculo_aluno_da_casa'], "text"),
   GetSQLValueString($_POST['vinculo_aluno_historico_transferencia'], "text"),
   GetSQLValueString($_POST['vinculo_aluno_id_cuidador'], "int"),
   GetSQLValueString($_POST['vinculo_aluno_matriz_multietapa'], "text"),
   GetSQLValueString($vinculo_aluno_nao_reprova, "text"),
   GetSQLValueString($_POST['vinculo_aluno_id'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());
  
// ** REGISTRO DE LOG DE USUÁRIO **
  $usu = $_POST['usu_id'];
  $esc = $_POST['usu_escola'];
  $detalhes = $_POST['detalhes'];

  $situacao1 = $_POST['vinculo_aluno_situacao'];


  switch($situacao1)
  {
    case '1';
    $situacao = 'MATRICULADO';
    break;
    case '2';
    $situacao = 'TRANSFERIDO';
    break;
    case '3';
    $situacao = 'DEIXOU DE FREQUENTAR';
    break;
    case '4';
    $situacao = 'FALECIDO';
    break;
    case '5';
    $situacao = 'OUTROS';
    break;
  }



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
   '16', 
   '($detalhes SITUACAO: $situacao)', 
   '$dat')
   ";
   mysql_select_db($database_SmecelNovo, $SmecelNovo);
   $Result1 = mysql_query($sql, $SmecelNovo) or die(mysql_error());
// ** REGISTRO DE LOG DE USUÁRIO **

   $updateGoTo = "matriculaExibe.php?vinculoEditado";
   if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

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

$colname_VinculoEditar = "-1";
if (isset($_GET['cmatricula'])) {
  $colname_VinculoEditar = $_GET['cmatricula'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_VinculoEditar = sprintf("
  SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
  vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_ponto_id, vinculo_aluno_data, 
  vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao,
  vinculo_aluno_vacina_atualizada, vinculo_aluno_vacina_data_retorno, 
  vinculo_aluno_datatransferencia, vinculo_aluno_internet, vinculo_aluno_multietapa, vinculo_aluno_nao_reprova,
  vinculo_aluno_dependencia, vinculo_aluno_repetente,vinculo_aluno_da_casa,vinculo_aluno_historico_transferencia, vinculo_aluno_id_cuidador,vinculo_aluno_id_matriz_multi,
  aluno_id, aluno_nome, turma_id, turma_nome, turma_multisseriada 
  FROM smc_vinculo_aluno 
  INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
  INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
  WHERE vinculo_aluno_hash = %s", GetSQLValueString($colname_VinculoEditar, "text"));
$VinculoEditar = mysql_query($query_VinculoEditar, $SmecelNovo) or die(mysql_error());
$row_VinculoEditar = mysql_fetch_assoc($VinculoEditar);
$totalRows_VinculoEditar = mysql_num_rows($VinculoEditar);

if ($totalRows_VinculoEditar == "") {
	//echo "TURMA EM BRANCO";	
	header("Location: turmasAlunosVinculados.php?nada"); 
  exit;
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turmas = "SELECT 
turma_id, turma_id_escola, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo,
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno_nome 
FROM smc_turma 
WHERE turma_ano_letivo = '$row_VinculoEditar[vinculo_aluno_ano_letivo]' AND turma_id_escola = '$row_UsuLogado[usu_escola]'
ORDER BY turma_turno, turma_etapa, turma_nome ASC";
$Turmas = mysql_query($query_Turmas, $SmecelNovo) or die(mysql_error());
$row_Turmas = mysql_fetch_assoc($Turmas);
$totalRows_Turmas = mysql_num_rows($Turmas);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Pontos = "SELECT te_ponto_id, te_ponto_id_sec, te_ponto_descricao, te_ponto_endereco, te_ponto_num, te_ponto_bairro, te_ponto_latitude, te_ponto_longitude, te_ponto_obs FROM smc_te_ponto WHERE te_ponto_id_sec = '$row_UsuLogado[usu_sec]' ORDER BY te_ponto_descricao ASC";
$Pontos = mysql_query($query_Pontos, $SmecelNovo) or die(mysql_error());
$row_Pontos = mysql_fetch_assoc($Pontos);
$totalRows_Pontos = mysql_num_rows($Pontos);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Etapa = "SELECT etapa_id, etapa_id_filtro, etapa_nome, etapa_idade, etapa_limite_turma, etapa_nome_abrev FROM smc_etapa";
$Etapa = mysql_query($query_Etapa, $SmecelNovo) or die(mysql_error());
$row_Etapa = mysql_fetch_assoc($Etapa);
$totalRows_Etapa = mysql_num_rows($Etapa);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matriz = sprintf("SELECT matriz_id, matriz_id_secretaria, matriz_nome, matriz_ativa FROM smc_matriz WHERE matriz_ativa = 'S' AND matriz_id_secretaria = %s", GetSQLValueString($row_EscolaLogada['sec_id'], "int"));
$Matriz = mysql_query($query_Matriz, $SmecelNovo) or die(mysql_error());
$row_Matriz = mysql_fetch_assoc($Matriz);
$totalRows_Matriz = mysql_num_rows($Matriz);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ListaVinculos = "
SELECT 
vinculo_id, vinculo_id_escola, vinculo_acesso, vinculo_id_funcionario, vinculo_id_funcao, vinculo_carga_horaria, DATE_FORMAT(vinculo_data_inicio, '%d/%m/%Y') AS vinculo_data_inicio, vinculo_obs, 
func_id, func_nome, funcao_id, funcao_nome, func_regime, func_senha_ativa 
FROM smc_vinculo 
INNER JOIN smc_func 
ON func_id = vinculo_id_funcionario 
INNER JOIN smc_funcao
ON funcao_id = vinculo_id_funcao 
WHERE vinculo_id_escola = '$row_EscolaLogada[escola_id]'
AND vinculo_status = 1
ORDER BY func_nome ASC
";
$ListaVinculos = mysql_query($query_ListaVinculos, $SmecelNovo) or die (mysql_error());
$row_ListaVinculos = mysql_fetch_assoc($ListaVinculos);
$totalRows_ListaVinculos = mysql_num_rows($ListaVinculos);

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
  <link rel="stylesheet" type="text/css" href="css/locastyle.css">  <link rel="stylesheet" type="text/css" href="css/preloader.css">
  <script src="js/locastyle.js"></script>
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<body onLoad="javascript:mudarTurmaNao()">
  <?php include_once ("menu-top.php"); ?>
  <?php include_once ("menu-esc.php"); ?>
  <main class="ls-main ">
    <div class="container-fluid">
      <!-- CONTEÚDO -->




      <p>&nbsp;</p>
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


  <div class="ls-modal" data-modal-blocked  id="modalLarge" style="top:-150px;">
    <div class="ls-modal-large">
      <div class="ls-modal-header">
        <button data-dismiss="modal">&times;</button>
        <h4 class="ls-modal-title">
          <strong><?php echo $row_VinculoEditar['aluno_nome']; ?> - <?php echo $row_VinculoEditar['turma_nome']; ?></strong><br>
          Matrícula realizada em <?php echo date("d/m/Y", strtotime($row_VinculoEditar['vinculo_aluno_data'])); ?>
        </h4>
      </div>
      <div class="ls-modal-body">


        <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form ls-form-horizontal row">
          <fieldset>
            <label class="ls-label col-md-12 ls-txt-right">
              <div class="ls-box ls-xs-space"> <b class="ls-label-text"><small>MUDAR ALUNO DE TURMA?</small></b>
                <label class="ls-label-text">
                  <input type="radio" id="mudandodeturma" name="mudandodeturma" value="1" onclick="javascript:mudarTurmaNao();" checked>
                  <small>NÃO</small> </label>
                  <label class="ls-label-text">
                    <input type="radio" id="mudandodeturma" name="mudandodeturma" value="2" onclick="javascript:mudarTurmaSim();">
                    <small>SIM</small> </label>
                  </div>
                </label>
                <label class="ls-label col-sm-12 vinculo_aluno_id_turma">
                  <b class="ls-label-text">TURMA</b>
                  <div class="ls-custom-select ls-field-lg">
                    <select name="vinculo_aluno_id_turma" id="vinculo_aluno_id_turma" class="ls-select" required>
                      <?php do { ?>
                        <option value="<?php echo $row_Turmas['turma_id']?>" <?php if (!(strcmp($row_Turmas['turma_id'], htmlentities($row_VinculoEditar['vinculo_aluno_id_turma'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>><?php echo $row_Turmas['turma_nome']?> <?php echo $row_Turmas['turma_turno_nome']?> (<?php echo $row_Turmas['turma_ano_letivo']?>)</option>
                      <?php } while ($row_Turmas = mysql_fetch_assoc($Turmas)); ?>
                    </select>
                  </div>
                </label>
                <label class="ls-label col-md-12 vinculo_aluno_data"><b class="ls-label-text">DATA DA MATRÍCULA</b>
                  <input type="date" name="vinculo_aluno_data" id="vinculo_aluno_data" class="ls-field-lg date1" value="<?php echo $row_VinculoEditar['vinculo_aluno_data']; ?>" size="32">
                </label>
                <label class="ls-label col-md-12 vinculo_aluno_situacao">
                  <b class="ls-label-text">SITUAÇÃO</b>
                  <div class="ls-custom-select ls-field-lg" >
                    <select name="vinculo_aluno_situacao" id="vinculo_aluno_situacao">
                      <option value="1" <?php if (!(strcmp(1, htmlentities($row_VinculoEditar['vinculo_aluno_situacao'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>MATRICULADO</option>
                      <option value="2" <?php if (!(strcmp(2, htmlentities($row_VinculoEditar['vinculo_aluno_situacao'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>TRANSFERIDO</option>
                      <option value="3" <?php if (!(strcmp(3, htmlentities($row_VinculoEditar['vinculo_aluno_situacao'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>DEIXOU DE FREQUENTAR</option>
                      <option value="4" <?php if (!(strcmp(4, htmlentities($row_VinculoEditar['vinculo_aluno_situacao'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>FALECIDO</option>
                      <option value="5" <?php if (!(strcmp(5, htmlentities($row_VinculoEditar['vinculo_aluno_situacao'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>OUTROS</option>
                    </select>
                  </div>
                </label>
                <div id="data_ocorrencia" class="ls-display-none">
                  <label class="ls-label col-md-12 vinculo_aluno_datatransferencia" ><b class="ls-label-text">DATA DA OCORRÊNCIA (transferência, desistência, falecimento etc.)</b>
                    <input type="date" name="vinculo_aluno_datatransferencia" id="vinculo_aluno_datatransferencia" class="ls-field-lg date1" value="<?php 
                    if ($row_VinculoEditar['vinculo_aluno_datatransferencia']<>"") { 
                      echo htmlentities($row_VinculoEditar['vinculo_aluno_datatransferencia'], ENT_COMPAT, 'utf-8');
                    } else {
                      echo date("Y-m-d");
                    }
                  ?>" size="32">
                </label>
              </div>
              <label class="ls-label col-sm-12 vinculo_aluno_internet1">
                <b class="ls-label-text">Aluno repetente nessa etapa de ensino?</b> <br>
                <p>
                  <label class="ls-label-text">
                    <input type="radio" name="vinculo_aluno_repetente" value="S" <?php if (!(strcmp(htmlentities($row_VinculoEditar['vinculo_aluno_repetente'], ENT_COMPAT, 'utf-8'),"S"))) {echo "checked=\"checked\"";} ?> />
                  SIM </label>
                  <label class="ls-label-text">
                    <input type="radio" name="vinculo_aluno_repetente" value="N" <?php if (!(strcmp(htmlentities($row_VinculoEditar['vinculo_aluno_repetente'], ENT_COMPAT, 'utf-8'),"N"))) {echo "checked=\"checked\"";} ?>/>
                  NÃO </label>
                </p>
              </label>
              <label class="ls-label col-sm-12 vinculo_aluno_internet1">
                <b class="ls-label-text">Aluno(a) possui acesso à internet?</b> <br>
                <p>
                  <label class="ls-label-text">
                    <input type="radio" name="vinculo_aluno_internet" value="S" <?php if (!(strcmp(htmlentities($row_VinculoEditar['vinculo_aluno_internet'], ENT_COMPAT, 'utf-8'),"S"))) {echo "checked=\"checked\"";} ?> />
                  SIM </label>
                  <label class="ls-label-text">
                    <input type="radio" name="vinculo_aluno_internet" value="N" <?php if (!(strcmp(htmlentities($row_VinculoEditar['vinculo_aluno_internet'], ENT_COMPAT, 'utf-8'),"N"))) {echo "checked=\"checked\"";} ?>/>
                  NÃO </label>
                </p>
              </label>
              <label class="ls-label col-sm-12 ">
                <b class="ls-label-text">ALUNO MATRICULADO É DA ESCOLA (DA CASA) OU DE OUTRA ESCOLA/CIDADE (DE FORA)</b> <br>
                <br>
                <p>
                  <label class="ls-label-text">
                    <input type="radio" name="vinculo_aluno_da_casa" value="C" <?php if (!(strcmp(htmlentities($row_VinculoEditar['vinculo_aluno_da_casa'], ENT_COMPAT, 'utf-8'),"C"))) {echo "checked=\"checked\"";} ?> />
                  DA CASA </label>
                  <label class="ls-label-text">
                    <input type="radio" name="vinculo_aluno_da_casa" value="F" <?php if (!(strcmp(htmlentities($row_VinculoEditar['vinculo_aluno_da_casa'], ENT_COMPAT, 'utf-8'),"F"))) {echo "checked=\"checked\"";} ?>/>
                  DE FORA </label>
                </p>
              </label>
              <label class="ls-label col-sm-12 " id="historico">
                <b class="ls-label-text">TRANSFERIDO COM HISTÓRICO OU DECLARAÇÃO</b><br>
                <br>
                <p>
                  <label class="ls-label-text">
                    <input type="radio" name="vinculo_aluno_historico_transferencia" value="H" id="vinculo_aluno_historico_transferencia_h" <?php if (!(strcmp(htmlentities($row_VinculoEditar['vinculo_aluno_historico_transferencia'], ENT_COMPAT, 'utf-8'),"H"))) {echo "checked=\"checked\"";} ?> />
                  HISTÓRICO </label>
                  <label class="ls-label-text">
                    <input type="radio" name="vinculo_aluno_historico_transferencia" value="D" id="vinculo_aluno_historico_transferencia_d" <?php if (!(strcmp(htmlentities($row_VinculoEditar['vinculo_aluno_historico_transferencia'], ENT_COMPAT, 'utf-8'),"D"))) {echo "checked=\"checked\"";} ?> />
                  DECLARAÇÃO </label>
                </p>
              </label>
              <label class="ls-label col-sm-12 vinculo_aluno_transporte1">
                <b class="ls-label-text">UTILIZA TRANSPORTE ESCOLAR?</b><br>
                <label class="ls-label-text">
                  <input type="radio" name="vinculo_aluno_transporte" id="vinculo_aluno_transporte" onclick="javascript:transporte_sim();" value="S" <?php if (!(strcmp(htmlentities($row_VinculoEditar['vinculo_aluno_transporte'], ENT_COMPAT, 'utf-8'),"S"))) {echo "checked=\"checked\"";} ?>>
                Sim </label>
                <label class="ls-label-text">
                  <input type="radio" name="vinculo_aluno_transporte" id="vinculo_aluno_transporte" onclick="javascript:transporte_nao();" value="N" <?php if (!(strcmp(htmlentities($row_VinculoEditar['vinculo_aluno_transporte'], ENT_COMPAT, 'utf-8'),"N"))) {echo "checked=\"checked\"";} ?>>
                Não </label>
              </label>
              <label class="ls-label col-sm-12 vinculo_aluno_ponto_id" id="ponto" <?php if (!(strcmp(htmlentities($row_VinculoEditar['vinculo_aluno_transporte'], ENT_COMPAT, 'utf-8'),"N"))) { echo "style='display:none'"; } else { echo "style=\"display:block\""; } ?> <?php if ($row_VinculoEditar['vinculo_aluno_transporte']=="N") { echo " style='display:none'"; } ?>>
                <b class="ls-label-text">PONTO</b>
                <div class="ls-custom-select ls-field-lg">
                  <select name="vinculo_aluno_ponto_id" id="vinculo_aluno_ponto_id" class="ls-select">
                    <option value="">-</option>
                    <?php do { ?>
                      <option value="<?php echo $row_Pontos['te_ponto_id']?>" <?php if (!(strcmp($row_Pontos['te_ponto_id'], htmlentities($row_VinculoEditar['vinculo_aluno_ponto_id'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>><?php echo $row_Pontos['te_ponto_descricao']?></option>
                    <?php } while ($row_Pontos = mysql_fetch_assoc($Pontos)); ?>
                  </select>
                </div>
              </label>
              <label class="ls-label col-md-12 ls-box" >
                <b class="ls-label-text">VINCULAR CUIDADOR</b>
                <a href="#" class="ls-ico-help" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="O cuidador de alunos especiais na escola desempenha um papel crucial no suporte às necessidades individuais desses estudantes. Sua responsabilidade inclui oferecer assistência personalizada, adaptar o ambiente escolar, colaborar com professores e terapeutas, tudo para garantir o bem-estar e o desenvolvimento integral desses alunos." data-title="Caso o aluno possua um cuidador"></a>
                <div class="ls-custom-select ls-field-lg">
                  <select name="vinculo_aluno_id_cuidador" class="ls-select">
                    <option value="" <?php if (!(strcmp(-1, ""))) {echo "SELECTED";} ?>>-</option>
                    <?php do { ?>
                      <option value="<?php echo $row_ListaVinculos['vinculo_id'] ?>"
                        <?php if (!(strcmp($row_ListaVinculos['vinculo_id'], htmlentities($row_VinculoEditar['vinculo_aluno_id_cuidador'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>
                        ><?php echo $row_ListaVinculos['func_nome'] ?></option>
                      <?php } while ($row_ListaVinculos = mysql_fetch_assoc($ListaVinculos)); ?>
                    </select>
                  </div>
                  <br>
                </label>
                <div class="ls-label col-md-12 ls-box">
                  <b class="ls-label-text">ALUNO ESPECIAL (não reprova)</b>
                  <a href="#" class="ls-ico-help" data-trigger="hover" data-ls-module="popover" data-placement="left"
                    data-content="Caso o aluno seja considerado especial na etapa de ensino dos anos finais e sua avaliação seja feita por meio de relatórios descritivos em vez de notas, essa opção deverá ser marcada."
                    data-title="Caso o aluno seja especial">
                  </a>
                  <p>
                    <label class="ls-label-text">
                      <input type="checkbox" name="vinculo_aluno_nao_reprova" class="ls-field"
                      <?php if (!(strcmp(htmlentities($row_VinculoEditar['vinculo_aluno_nao_reprova'], ENT_COMPAT, 'utf-8'),"S"))) {echo "checked=\"checked\"";} ?>
                      >
                      SIM
                    </label>
                  </p>
                </div>
                <label class="ls-label col-sm-12" <?php if ($row_VinculoEditar['turma_multisseriada']==0) { echo "style='display:none'"; }?>>
                  <b class="ls-label-text">ETAPA MULTISSERIADA</b>
                  <div class="ls-custom-select ls-field-lg">
                    <select name="vinculo_aluno_multietapa" id="vinculo_aluno_multietapa" class="ls-select">
                      <option value="">-</option>
                      <?php do { ?>
                        <option value="<?php echo $row_Etapa['etapa_id']?>" <?php if (!(strcmp($row_Etapa['etapa_id'], htmlentities($row_VinculoEditar['vinculo_aluno_multietapa'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>><?php echo $row_Etapa['etapa_nome']?></option>
                      <?php } while ($row_Etapa = mysql_fetch_assoc($Etapa)); ?>
                    </select>
                  </div>
                </label>

                <label class="ls-label col-sm-12" <?php if ($row_VinculoEditar['turma_multisseriada']==0) { echo "style='display:none'"; }?>>
                  <b class="ls-label-text">MATRIZ MULTISSERIADA</b>
                  <div class="ls-custom-select ls-field-lg">
                    <select name="vinculo_aluno_matriz_multietapa" id="vinculo_aluno_matriz_multietapa" class="ls-select">
                      <option value="">-</option>
                      <?php do { ?>
                        <option value="<?php echo $row_Matriz['matriz_id']?>" <?php if (!(strcmp($row_Matriz['matriz_id'], htmlentities($row_VinculoEditar['vinculo_aluno_id_matriz_multi'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>><?php echo $row_Matriz['matriz_nome']?></option>
                      <?php } while ($row_Matriz = mysql_fetch_assoc($Matriz)); ?>
                    </select>
                  </div>
                </label>

                <label class="ls-label col-sm-12 vinculo_aluno_vacina_atualizada1">
                  <b class="ls-label-text">CARTEIRA DE VACINAÇÃO DO ALUNO ESTÁ ATUALIZADA? </b> <br>
                  <p>
                    <label class="ls-label-text">
                      <input type="radio" name="vinculo_aluno_vacina_atualizada" value="S" <?php if (!(strcmp(htmlentities($row_VinculoEditar['vinculo_aluno_vacina_atualizada'], ENT_COMPAT, 'utf-8'),"S"))) {echo "checked=\"checked\"";} ?> />
                    SIM </label>
                    <label class="ls-label-text">
                      <input type="radio" name="vinculo_aluno_vacina_atualizada" value="N" <?php if (!(strcmp(htmlentities($row_VinculoEditar['vinculo_aluno_vacina_atualizada'], ENT_COMPAT, 'utf-8'),"N"))) {echo "checked=\"checked\"";} ?>/>
                    NÃO </label>
                    <label class="ls-label-text">
                      <input type="radio" name="vinculo_aluno_vacina_atualizada" value="I" <?php if (!(strcmp(htmlentities($row_VinculoEditar['vinculo_aluno_vacina_atualizada'], ENT_COMPAT, 'utf-8'),"I"))) {echo "checked=\"checked\"";} ?>/>
                    SEM INFORMAÇÃO </label>
                  </p>
                </label>
                <label class="ls-label col-sm-12 vinculo_aluno_dependencia1">
                  <b class="ls-label-text">MATRÍCULA DE DEPENDÊNCIA?</b><br>
                  <label class="ls-label-text">
                    <input type="radio" name="vinculo_aluno_dependencia" id="vinculo_aluno_dependencia" value="S" <?php if (!(strcmp(htmlentities($row_VinculoEditar['vinculo_aluno_dependencia'], ENT_COMPAT, 'utf-8'),"S"))) {echo "checked=\"checked\"";} ?>>
                  Sim </label>
                  <label class="ls-label-text">
                    <input type="radio" name="vinculo_aluno_dependencia" id="vinculo_aluno_dependencia" value="N" <?php if (!(strcmp(htmlentities($row_VinculoEditar['vinculo_aluno_dependencia'], ENT_COMPAT, 'utf-8'),"N"))) {echo "checked=\"checked\"";} ?>>
                  Não </label>
                </label>
                <div class="ls-modal-footer">
                  <input type="submit" value="SALVAR ALTERAÇÃO" class="ls-btn-primary ls-btn-lg">
                  <a href="matriculaExibe.php?cmatricula=<?php echo $row_VinculoEditar['vinculo_aluno_hash']; ?>" class="ls-btn ls-btn-lg">CANCELAR</a> 
                  <!--<a href="javascript:func()" onclick="confirmaExclusao('<?php echo $row_VinculoEditar['vinculo_aluno_hash']; ?>','<?php echo $row_VinculoEditar['aluno_nome']; ?>')" class="ls-btn-danger ls-float-right ls-btn-lg">EXCLUIR VINCULO</a> </div>--> 
                  <a onclick="confirmaExclusao('<?php echo $row_VinculoEditar['vinculo_aluno_hash']; ?>','<?php echo $row_VinculoEditar['aluno_nome']; ?>')" class="ls-btn-danger ls-float-right ls-btn-lg">EXCLUIR VINCULO</a>
                </div>
                <div class="ls-actions-btn"> </div>
              </fieldset>

              <input type="hidden" name="MM_update" value="form1">
              <input type="hidden" name="vinculo_aluno_id" value="<?php echo $row_VinculoEditar['vinculo_aluno_id']; ?>">
              <input type="hidden" name="usu_id" value="<?php echo $row_UsuLogado['usu_id']; ?>">
              <input type="hidden" name="usu_escola" value="<?php echo $row_UsuLogado['usu_escola']; ?>">
              <input type="hidden" name="detalhes" value="<?php echo $row_VinculoEditar['aluno_nome']; ?> - <?php echo $row_VinculoEditar['turma_nome']; ?>">
            </form>


          </div>

        </div>
      </div>


      <!-- We recommended use jQuery 1.10 or up --> 
      <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
      <script src="js/locastyle.js"></script> 
      <script type="text/javascript" src="../js/jquery.mask.min.js"></script> 
      <script src="js/mascara.js"></script> 
      <script>
       locastyle.modal.open("#modalLarge");
     </script> 
     <script type="text/javascript">
      $(document).ready(function() {
    // Função para verificar e aplicar as classes/atributos com base na situação atual
        function verificarSituacao() {
          var situacao = $('#vinculo_aluno_situacao').val();
          
          if(situacao != 1){
            $("#data_ocorrencia").removeClass('ls-display-none');
            $("#vinculo_aluno_datatransferencia").attr("required", "true");
          } else {
            $("#data_ocorrencia").addClass('ls-display-none');
            $('#vinculo_aluno_datatransferencia').removeAttr('required');
          }
        }

    // Verificar a situação quando a página for carregada
        verificarSituacao();

    // Verificar a situação quando o valor do campo for alterado
        $('#vinculo_aluno_situacao').change(function() {
          verificarSituacao();
        });
      });
    </script>
    <script language="Javascript">
     function confirmaExclusao(id, nome) {
       var resposta = confirm("Deseja realmente remover o vínculo deste aluno?");
       if (resposta == true) {
         window.location.href = "matriculaExcluir.php?hash="+id+"&nome="+nome;
       }
     }
   </script> 
   <script type="text/javascript">

    function mudarTurmaSim()
    {
	/*
	document.getElementById("vinculo_aluno_situacao").disabled = true; //Habilitando
	document.getElementById("vinculo_aluno_datatransferencia").disabled = true; //Habilitando
	document.getElementById("vinculo_aluno_transporte").disabled = true; //Habilitando
	document.getElementById("vinculo_aluno_id_turma").disabled = false; //Habilitando
	*/

     $(".vinculo_aluno_vacina_atualizada1").css("display", "none");
     $(".vinculo_aluno_dependencia1").css("display", "none");
     $(".vinculo_aluno_internet1").css("display", "none");
     $(".vinculo_aluno_data").css("display", "none");
     $(".vinculo_aluno_situacao").css("display", "none");
     $(".vinculo_aluno_datatransferencia").css("display", "none");
     $(".vinculo_aluno_transporte1").css("display", "none");
     $(".vinculo_aluno_ponto_id").css("display", "none");
     $(".vinculo_aluno_id_turma").css("display", "block");

   }
   function mudarTurmaNao()
   {
	/*
	document.getElementById("vinculo_aluno_situacao").disabled = false; //Habilitando
	document.getElementById("vinculo_aluno_datatransferencia").disabled = false; //Habilitando
	document.getElementById("vinculo_aluno_transporte").disabled = false; //Habilitando
	document.getElementById("vinculo_aluno_id_turma").disabled = true; //Habilitando
	*/
     $(".vinculo_aluno_vacina_atualizada1").css("display", "block");
     $(".vinculo_aluno_dependencia1").css("display", "block");
     $(".vinculo_aluno_internet1").css("display", "block");
     $(".vinculo_aluno_data").css("display", "block");
     $(".vinculo_aluno_situacao").css("display", "block");
     $(".vinculo_aluno_datatransferencia").css("display", "block");
     $(".vinculo_aluno_transporte1").css("display", "block");
     $(".vinculo_aluno_ponto_id").css("display", "block");
     $(".vinculo_aluno_id_turma").css("display", "none");
   }

   function transporte_sim()
   {
	document.getElementById("ponto").style.display = "block"; //Habilitando
}
function transporte_nao()
{
	document.getElementById("ponto").style.display = "none"; //Habilitando
}
</script>
</body>
</html>
<?php
mysql_free_result($Pontos);

mysql_free_result($Etapa);

mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($VinculoEditar);
?>
