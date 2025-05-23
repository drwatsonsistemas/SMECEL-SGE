<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include('fnc/inverteData.php'); ?>
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
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, sec_id, sec_cidade, sec_uf, escola_tema 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

$colname_feed_ac = "-1";
if (isset($_GET['ac'])) {
  $colname_feed_ac = $_GET['ac'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_feed_ac = sprintf("
  SELECT ac_id, ac_id_professor, ac_id_escola, ac_id_componente, ac_id_etapa, ac_ano_letivo, ac_data_inicial, ac_data_final, ac_conteudo, 
  ac_objetivo_especifico, ac_objeto_conhecimento, ac_metodologia, ac_recursos, ac_avaliacao, ac_criacao, ac_da_conviver, ac_da_brincar, 
  ac_da_participar, ac_da_explorar, ac_da_expressar, ac_da_conhecerse, ac_ce_eo, ac_ce_ts, ac_ce_ef, ac_ce_cg, ac_ce_et, ac_ce_di, ac_status,ac_unid_tematica,
  ac_correcao, ac_feedback, func_id, func_nome, disciplina_id, disciplina_nome, etapa_id, etapa_nome  
  FROM smc_ac 
  LEFT JOIN smc_func ON func_id = ac_id_professor
  LEFT JOIN smc_disciplina ON disciplina_id = ac_id_componente 
  LEFT JOIN smc_etapa ON etapa_id = ac_id_etapa
  WHERE ac_id = %s", GetSQLValueString($colname_feed_ac, "int"));
$feed_ac = mysql_query($query_feed_ac, $SmecelNovo) or die(mysql_error());
$row_Ac = mysql_fetch_assoc($feed_ac);
$totalRows_feed_ac = mysql_num_rows($feed_ac);

$trocar = array("\"", "\'","'");



$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE smc_ac SET ac_status=%s, ac_correcao=%s, ac_feedback=%s WHERE ac_id=%s",
   GetSQLValueString($_POST['ac_status'], "text"),
   GetSQLValueString($_POST['ac_correcao'], "text"),
   GetSQLValueString($_POST['ac_feedback'], "text"),
   GetSQLValueString($_POST['ac_id'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

  $updateGoTo = "ac-professor.php?codigo=$row_Ac[ac_id_professor]";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$query_ac_label = "SELECT * FROM smc_ac_label
WHERE ac_id_ac = '$row_Ac[ac_id]'";
$ac_label = mysql_query($query_ac_label, $SmecelNovo) or die(mysql_error());
$rowAcLabel = mysql_fetch_assoc($ac_label);
$TotalrowAcLabel = mysql_num_rows($ac_label);
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
<body>
  <?php include_once ("menu-top.php"); ?>
  <?php include_once ("menu-esc.php"); ?>
  <main class="ls-main ">
    <div class="container-fluid">
      <h1 class="ls-title-intro ls-ico-home">ACOMPANHAMENTO DE PLANEJAMENTO</h1>
      <!-- CONTEÚDO -->

      <div class="ls-box">
       <h5 class="ls-title-5">Professor(a): <?php echo $row_Ac['func_nome']; ?> - <?php echo $row_Ac['disciplina_nome']; ?> <br>Etapa: <?php echo $row_Ac['etapa_nome']; ?></h5>
       <br>
       Relatório feito em <?php if ($row_Ac['ac_criacao']=="") { ?><?php echo inverteData($row_Ac['ac_data_inicial']); ?><?php } else { ?><?php echo date("d/m/Y - H:i", strtotime($row_Ac['ac_criacao'])); ?><?php } ?>
     </div>

     <div class="ls-box ls-box-gray">
      <?php if ($TotalrowAcLabel == 0) { ?>
        <?php if ($row_Ac['ac_unid_tematica']<>"") { ?>
          <br>
          <div class='ls-box'>
            <h4>UNIDADE TEMÁTICA</h4>
            <p><?php echo str_replace($trocar, "", $row_Ac['ac_unid_tematica']); ?></p>
          </div>
        <?php } ?>

        <?php if ($row_Ac['ac_objetivo_especifico']<>"") { ?>
          <br>
          <div class='ls-box'>
            <h4>OBJETIVOS DE APRENDIZAGEM E DESENVOLVIMENTO</h4>
            <p><?php echo str_replace($trocar, "", $row_Ac['ac_objetivo_especifico']); ?></p>
          </div>
        <?php } ?>
        <?php if ($row_Ac['ac_objeto_conhecimento']<>"") { ?>
          <br>
          <div class='ls-box'>
            <h4>OBJETOS DE CONHECIMENTO/SABERES E CONHECIMENTO</h4>
            <p><?php echo str_replace($trocar, "", $row_Ac['ac_objeto_conhecimento']); ?></p>
          </div>
        <?php } ?>
        <?php if ($row_Ac['ac_recursos']<>"") { ?>
          <br>
          <div class='ls-box'>
            <h4>HABILIDADES</h4>
            <p><?php echo str_replace($trocar, "", $row_Ac['ac_recursos']); ?></p>
          </div>
        <?php } ?>
        <?php if ($row_Ac['ac_metodologia']<>"") { ?>
          <br>
          <div class='ls-box'>
            <h4>METODOLOGIA</h4>
            <p><?php echo str_replace($trocar, "", $row_Ac['ac_metodologia']); ?></p>
          </div>
        <?php } ?>
        <?php if ($row_Ac['ac_avaliacao']<>"") { ?>
          <br>
          <div class='ls-box'>
            <h4>AVALIAÇÃO</h4>
            <p><?php echo str_replace($trocar, "", $row_Ac['ac_avaliacao']); ?></p>
          </div>
        <?php } ?>
        <?php if ($row_Ac['ac_conteudo']<>"") { ?>
          <br>
          <div class='ls-box'>
            <h4>OBSERVAÇÃO/RECURSOS</h4>
            <p><?php echo str_replace($trocar, "", $row_Ac['ac_conteudo']); ?></p>
          </div>
        <?php } ?>
      <?php }else{ 
        do{ 
          $titulo = '';
          switch ($rowAcLabel['ac_id_tipo']) {
            case '1':
            $titulo = "Unidade temática";
            break;
            case '2':
            $titulo = "Objetivos de Aprendizagem e desenvolvimento";
            break;
            case '3':
            $titulo = "Objetos de conhecimento/saberes e conhecimento/conteúdo";
            break;
            case '4':
            $titulo = "Habilidades";
            break;
            case '5':
            $titulo = "Metodologia";
            break;
            case '6':
            $titulo = "Avaliação";
            break;
            case '7':
            $titulo = "Observação";
            break;
            case '8':
            $titulo = "Recursos";
            break;
            default:
            $titulo = "";
            break;
          }

          ?>


          <div class='ls-box'>
            <h4><?= $titulo ?></h4>
            <p><?php echo str_replace($trocar, "", $rowAcLabel['ac_conteudo']); ?></p>
          </div>

        <?php }
        while($rowAcLabel = mysql_fetch_assoc($ac_label)); 

      } ?>
      <?php if (
        $row_Ac['ac_da_conviver']=="S" || 
        $row_Ac['ac_da_brincar']=="S" || 
        $row_Ac['ac_da_participar']=="S" || 
        $row_Ac['ac_da_explorar']=="S" || 
        $row_Ac['ac_da_expressar']=="S" || 
        $row_Ac['ac_da_conhecerse']=="S" 
      ) { ?>
        <div class='ls-box1'>
          <h4>DIREITOS DE APRENDIZAGEM</h4>
          <br>
          <p>
            <label>
              <input type='checkbox' disabled='disabled' <?php if ($row_Ac['ac_da_conviver']=="S") { echo "checked"; } ?> />
              <span>Conviver</span> </label>
              <label>
                <input type='checkbox' disabled='disabled' <?php if ($row_Ac['ac_da_brincar']=="S") { echo "checked"; } ?> />
                <span>Brincar</span> </label>
                <label>
                  <input type='checkbox' disabled='disabled' <?php if ($row_Ac['ac_da_participar']=="S") { echo "checked"; } ?> />
                  <span>Participar</span> </label>
                  <label>
                    <input type='checkbox' disabled='disabled' <?php if ($row_Ac['ac_da_explorar']=="S") { echo "checked"; } ?> />
                    <span>Explorar</span> </label>
                    <label>
                      <input type='checkbox' disabled='disabled' <?php if ($row_Ac['ac_da_expressar']=="S") { echo "checked"; } ?> />
                      <span>Expressar</span> </label>
                      <label>
                        <input type='checkbox' disabled='disabled' <?php if ($row_Ac['ac_da_conhecerse']=="S") { echo "checked"; } ?> />
                        <span>Conhecer-se</span> </label>
                      </p>
                    </div>
                  <?php } ?>
                  <?php if ($row_Ac['ac_ce_eo']<>"") { ?>
                    <div class='ls-box1'>
                      <h4>EO – O eu, o outro e o nós</h4>
                      <p><?php echo str_replace($trocar, "", $row_Ac['ac_ce_eo']); ?></p>
                    </div>
                  <?php } ?>
                  <?php if ($row_Ac['ac_ce_ts']<>"") { ?>
                    <div class='ls-box1'>
                      <h4>TS – Traços, sons, cores e formas</h4>
                      <p><?php echo str_replace($trocar, "", $row_Ac['ac_ce_ts']); ?></p>
                    </div>
                  <?php } ?>
                  <?php if ($row_Ac['ac_ce_ef']<>"") { ?>
                    <div class='ls-box1'>
                      <h4>EF – Escuta, fala, pensamento e imaginação</h4>
                      <p><?php echo str_replace($trocar, "", $row_Ac['ac_ce_ef']); ?></p>
                    </div>
                  <?php } ?>
                  <?php if ($row_Ac['ac_ce_cg']<>"") { ?>
                    <div class='ls-box1'>
                      <h4>CG – Corpo, gestos e movimento</h4>
                      <p><?php echo str_replace($trocar, "", $row_Ac['ac_ce_cg']); ?></p>
                    </div>
                  <?php } ?>
                  <?php if ($row_Ac['ac_ce_et']<>"") { ?>
                    <div class='ls-box1'>
                      <h4>ET – Espaços, tempos, quantidades, relações e transformações</h4>
                      <p><?php echo str_replace($trocar, "", $row_Ac['ac_ce_et']); ?></p>
                    </div>
                  <?php } ?>
                  <?php if ($row_Ac['ac_ce_di']<>"") { ?>
                    <div class='ls-box1'>
                      <h4>ET – Espaços, tempos, quantidades, relações e transformações</h4>
                      <p><?php echo str_replace($trocar, "", $row_Ac['ac_ce_di']); ?></p>
                    </div>
                  <?php } ?>
                </div>


                <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form ls-box">

                  <h5 class="ls-title-5">Parecer da Coordenação Pedagógica</h5>

                  <label class="ls-label">
                    <b class="ls-label-text">Status</b>
                    <div class="ls-custom-select">
                      <select class="ls-custom" name="ac_correcao">
                        <option value="0" <?php if (!(strcmp(0, htmlentities($row_Ac['ac_correcao'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>&#128077; Tudo certo</option>
                        <option value="1" <?php if (!(strcmp(1, htmlentities($row_Ac['ac_correcao'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>&#9995; Necessita correção no planejamento</option>
                        <option value="2" <?php if (!(strcmp(2, htmlentities($row_Ac['ac_correcao'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>&#128076; Planejamento corrigido</option>
                      </select>
                    </div>
                  </label>
                  <label class="ls-label"> <b class="ls-label-text">Instrução da Coordenação Pedagógica</b>
                    <textarea name="ac_feedback" cols="50" rows="5"><?php echo htmlentities($row_Ac['ac_feedback'], ENT_COMPAT, 'utf-8'); ?></textarea>
                  </label>
                  <div class="ls-actions-btn">
                    <input class="ls-btn-primary" type="submit" value="REGISTRAR ACOMPANHAMENTO">
                    <a href="<?php echo "ac-professor.php?codigo=$row_Ac[ac_id_professor]" ?>" class="ls-btn">VOLTAR</a>
                  </div>
                  <input type="hidden" name="ac_id" value="<?php echo $row_Ac['ac_id']; ?>">
                  <input type="hidden" name="ac_status" value="1">
                  <input type="hidden" name="MM_update" value="form1">
                  <input type="hidden" name="ac_id" value="<?php echo $row_Ac['ac_id']; ?>">
                </form>
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

            <!-- We recommended use jQuery 1.10 or up --> 
            <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
            <script src="js/locastyle.js"></script>
          </body>
          </html>
          <?php
          mysql_free_result($feed_ac);

          mysql_free_result($UsuLogado);

          mysql_free_result($EscolaLogada);
          ?>
