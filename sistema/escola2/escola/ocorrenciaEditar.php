<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include "fnc/inverteData.php"; ?>
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
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_tema,  sec_id, sec_cidade, sec_uf 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);


$colname_Matricula = "-1";
if (isset($_GET['cmatricula'])) {
  $colname_Matricula = $_GET['cmatricula'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Vinculo = sprintf("
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, aluno_id, aluno_nome, turma_id, turma_nome 
FROM smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma 
WHERE vinculo_aluno_hash = %s", GetSQLValueString($colname_Matricula, "text"));
$Vinculo = mysql_query($query_Vinculo, $SmecelNovo) or die(mysql_error());
$row_Vinculo = mysql_fetch_assoc($Vinculo);
$totalRows_Vinculo = mysql_num_rows($Vinculo);

$colname_Ocorrencia = "-1";
if (isset($_GET['ocorrencia'])) {
  $colname_Ocorrencia = $_GET['ocorrencia'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Ocorrencia = sprintf("
SELECT ocorrencia_id, ocorrencia_id_aluno, ocorrencia_id_turma, ocorrencia_id_escola, ocorrencia_ano_letivo, ocorrencia_data, 
ocorrencia_hora, ocorrencia_tipo, ocorrencia_afastamento_de, ocorrencia_afastamento_ate, ocorrencia_total_dias, ocorrencia_descricao,
CASE ocorrencia_tipo
WHEN 1 THEN 'OCORRÊNCIA DE ADVERTÊNCIA'
WHEN 2 THEN 'OCORRÊNCIA DE SUSPENSÃO'
WHEN 3 THEN 'OCORRÊNCIA'
WHEN 4 THEN 'CONVOCAÇÃO'
END AS ocorrencia_tipo_nome 
FROM smc_ocorrencia WHERE ocorrencia_id = %s", GetSQLValueString($colname_Ocorrencia, "int"));
$Ocorrencia = mysql_query($query_Ocorrencia, $SmecelNovo) or die(mysql_error());
$row_Ocorrencia = mysql_fetch_assoc($Ocorrencia);
$totalRows_Ocorrencia = mysql_num_rows($Ocorrencia);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
		if ($row_UsuLogado['usu_insert']=="N") {
		header(sprintf("Location: vinculoAlunoExibirTurma.php?permissao"));
		die;
	}
	
  $insertSQL = sprintf("UPDATE smc_ocorrencia SET ocorrencia_data = %s, ocorrencia_hora = %s, ocorrencia_tipo = %s, ocorrencia_afastamento_de = %s, ocorrencia_afastamento_ate = %s, ocorrencia_total_dias = %s, ocorrencia_descricao = %s WHERE ocorrencia_id = %s",
                       GetSQLValueString(inverteData($_POST['ocorrencia_data']), "date"),
                       GetSQLValueString($_POST['ocorrencia_hora'], "text"),
                       GetSQLValueString($_POST['ocorrencia_tipo'], "int"),
                       GetSQLValueString(inverteData($_POST['ocorrencia_afastamento_de']), "date"),
                       GetSQLValueString(inverteData($_POST['ocorrencia_afastamento_ate']), "date"),
                       GetSQLValueString($_POST['ocorrencia_total_dias'], "text"),
                       GetSQLValueString($_POST['ocorrencia_descricao'], "text"),
                      GetSQLValueString($colname_Ocorrencia, 'int')
                      );

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());  
  
  $insertGoTo = "ocorrenciaExibe.php?cmatricula=$colname_Matricula";
  if (isset($_SERVER['QUERY_STRING'])) {
    //$insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    //$insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}
?>
<!DOCTYPE html>
<html class="<?php echo $row_EscolaLogada['escola_tema']; ?>" lang="pt-br">

<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    gtag('js', new Date());

    gtag('config', 'UA-117872281-1');
  </script>

  <title>SMECEL - Sistema de Gestão Escolar</title>

  <meta charset="utf-8">
  <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
  <meta name="description" content="Sistema de Gestão Escolar.">
  <link rel="stylesheet" type="text/css" href="css/locastyle.css">
  <link rel="stylesheet" type="text/css" href="css/preloader.css">
  <link rel="icon" sizes="192x192" href="img/icone.png">
  <link rel="apple-touch-icon" href="img/icone.png">
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>

<body>
  <?php include_once("menu-top.php"); ?>
  <?php include_once("menu-esc.php"); ?>


  <main class="ls-main ">
    <div class="container-fluid">

      <h1 class="ls-title-intro ls-ico-home">EDITAR OCORRÊNCIA</h1>
      <!-- CONTEÚDO -->

      <div class="ls-modal" data-modal-blocked id="myAwesomeModal">
        <div class="ls-modal-box">
          <div class="ls-modal-header">
            <button data-dismiss="modal">&times;</button>
            <p>
            <h3>EDITAR OCORRÊNCIA</h3>
            </p>
            <div class="ls-box">
              <h4 class="ls-modal-title"><?php echo $row_Vinculo['aluno_nome']; ?></h4>
              <p class="ls-modal-title"><?php echo $row_Vinculo['turma_nome']; ?></p>
            </div>

          </div>
          <div class="ls-modal-body" id="myModalBody">


            <form method="post" name="form1" action="<?php echo $editFormAction ?>" class="ls-form ls-form-horizontal row">

              <fieldset>

                <label class="ls-label col-md-6"><b class="ls-label-text">DATA DA OCORRÊNCIA</b>
                  <input type="text" name="ocorrencia_data" value="<?php echo date('d/m/Y', strtotime($row_Ocorrencia['ocorrencia_data'])); ?>" size="32" class="date">
                </label>

                <label class="ls-label col-md-6"><b class="ls-label-text">HORA DA OCORRÊNCIA</b>
                  <input type="text" name="ocorrencia_hora" value="<?php if($row_Ocorrencia['ocorrencia_hora']!=''){echo date('H:i', strtotime($row_Ocorrencia['ocorrencia_hora']));} ?>" size="32" class="hora">
                </label>

                <label class="ls-label col-md-6">
                  <b class="ls-label-text">TIPO DE OCORRÊNCIA</b>
                  <div class="ls-custom-select">
                    <select name="ocorrencia_tipo" onChange="javascript:desabilita_datas(this.value);" required>
                      <option value="">-</option>
                      <option value="1" id="1" <?php if (!(strcmp(1, htmlentities($row_Ocorrencia['ocorrencia_tipo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>ADVERTÊNCIA</option>
                      <option value="2" id="2" <?php if (!(strcmp(2, htmlentities($row_Ocorrencia['ocorrencia_tipo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>SUSPENSÃO</option>
                      <option value="4" id="4" <?php if (!(strcmp(3, htmlentities($row_Ocorrencia['ocorrencia_tipo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>CONVOCAÇÃO</option>
                      <option value="3" id="3" <?php if (!(strcmp(4, htmlentities($row_Ocorrencia['ocorrencia_tipo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>OUTRAS</option>
                    </select>
                  </div>
                </label>

                <label class="ls-label col-md-6"><b class="ls-label-text">DATA DO AFASTAMENTO</b>
                  <input type="text" id="ocorrencia_afastamento_de" name="ocorrencia_afastamento_de" value="<?php if($row_Ocorrencia['ocorrencia_afastamento_de'] != ''){ echo date('d/m/Y', strtotime($row_Ocorrencia['ocorrencia_afastamento_de']));} ?>" size="32"
                    class="date">
                </label>

                <label class="ls-label col-md-6"><b class="ls-label-text">DATA DO RETORNO</b>
                  <input type="text" id="ocorrencia_afastamento_ate" name="ocorrencia_afastamento_ate" value="<?php if($row_Ocorrencia['ocorrencia_afastamento_de'] != ''){ echo date('d/m/Y', strtotime($row_Ocorrencia['ocorrencia_afastamento_ate']));} ?>"
                    size="32" class="date">
                </label>

                <label class="ls-label col-md-6"><b class="ls-label-text">Nº DE DIAS AFASTADOS</b>
                  <input type="text" id="ocorrencia_total_dias" name="ocorrencia_total_dias" value="<?php echo $row_Ocorrencia['ocorrencia_total_dias']; ?>" size="32">
                </label>

                <label class="ls-label col-md-12"><b class="ls-label-text">DESCRIÇÃO DA OCORRÊNCIA</b>
                  <textarea name="ocorrencia_descricao" cols="50" rows="5" required><?php echo $row_Ocorrencia['ocorrencia_descricao']; ?></textarea>
                </label>

              </fieldset>

              <div class="ls-actions-btn">
                <input type="submit" value="EDITAR OCORRÊNCIA" class="ls-btn-primary">
                <a href="matriculaExibe.php?cmatricula=<?php echo $row_Vinculo['vinculo_aluno_hash']; ?>"
                  class="ls-btn">CANCELAR</a>
              </div>


              <input type="hidden" name="ocorrencia_id_aluno"
                value="<?php echo $row_Vinculo['vinculo_aluno_id_aluno']; ?>">
              <input type="hidden" name="ocorrencia_id_turma"
                value="<?php echo $row_Vinculo['vinculo_aluno_id_turma']; ?>">
              <input type="hidden" name="ocorrencia_id_escola"
                value="<?php echo $row_Vinculo['vinculo_aluno_id_escola']; ?>">
              <input type="hidden" name="ocorrencia_ano_letivo"
                value="<?php echo $row_Vinculo['vinculo_aluno_ano_letivo']; ?>">
              <input type="hidden" name="MM_insert" value="form1">

              <input type="hidden" name="usu_id" value="<?php echo $row_UsuLogado['usu_id']; ?>">
              <input type="hidden" name="usu_escola" value="<?php echo $row_UsuLogado['usu_escola']; ?>">
              <input type="hidden" name="detalhes"
                value="<?php echo $row_Vinculo['aluno_nome']; ?> - <?php echo $row_Vinculo['turma_nome']; ?>">


            </form>


          </div>
        </div>
      </div><!-- /.modal -->

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
  <script>
    locastyle.modal.open("#myAwesomeModal");
  </script>

  <script type="text/javascript">
    function desabilita_datas(valor) {
      if (valor == 1) {
        document.getElementById("ocorrencia_afastamento_de").disabled = true; //Desabilitando
        document.getElementById("ocorrencia_afastamento_ate").disabled = true; //Desabilitando
        document.getElementById("ocorrencia_total_dias").disabled = true; //Desabilitando
      } else {
        document.getElementById("ocorrencia_afastamento_de").disabled = false; //Habilitando								
        document.getElementById("ocorrencia_afastamento_ate").disabled = false; //Habilitando								
        document.getElementById("ocorrencia_total_dias").disabled = false; //Habilitando								
      }
    }
  </script>
</body>

</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);
?>