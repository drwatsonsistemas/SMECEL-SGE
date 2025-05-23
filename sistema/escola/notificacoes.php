<?php require_once('../../Connections/SmecelNovo.php'); ?>
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
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, sec_id, sec_cidade, sec_uf 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_OC = "
SELECT ocorrencia_id, ocorrencia_id_turma, ocorrencia_id_escola, ocorrencia_id_professor, ocorrencia_data, ocorrencia_status, ocorrencia_descricao, turma_id, turma_nome, escola_id, escola_nome, func_id, func_nome
FROM smc_ocorrencia_turma
LEFT JOIN smc_turma ON turma_id = ocorrencia_id_turma
LEFT JOIN smc_escola ON escola_id = ocorrencia_id_escola
LEFT JOIN smc_func ON func_id = ocorrencia_id_professor
WHERE ocorrencia_id_escola = '$row_EscolaLogada[escola_id]' AND ocorrencia_status = '0' ORDER BY ocorrencia_data DESC
";
$OC = mysql_query($query_OC, $SmecelNovo) or die(mysql_error());
$row_OC = mysql_fetch_assoc($OC);
$totalRows_OC = mysql_num_rows($OC);

//exibe alerta do dia
$data = date('Y-m-d');
mysql_select_db($database_SmecelNovo, $SmecelNovo);
//$query_Agenda = "SELECT agenda_id, agenda_title, agenda_start, agenda_end, agenda_color FROM smc_agenda WHERE agenda_start = '$data'";
$query_Agenda = "SELECT agenda_id, agenda_title, agenda_start, agenda_end, agenda_color FROM smc_agenda WHERE agenda_start = CURDATE()";
$Agenda = mysql_query($query_Agenda, $SmecelNovo) or die(mysql_error());
$row_Agenda = mysql_fetch_assoc($Agenda);
$totalRows_Agenda = mysql_num_rows($Agenda);

//exibe alerta do dia seguinte
$data = date('Y-m-d');
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AgendaAmanha = "SELECT agenda_id, agenda_title, agenda_start, agenda_end, agenda_color FROM smc_agenda WHERE agenda_start = CURDATE()+1";
$AgendaAmanha = mysql_query($query_AgendaAmanha, $SmecelNovo) or die(mysql_error());
$row_AgendaAmanha = mysql_fetch_assoc($AgendaAmanha);
$totalRows_AgendaAmanha = mysql_num_rows($AgendaAmanha);

//ANIVERSARIANTES DO DIA
$dia = date('d');
$mes = date('m');
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Aniversarios = "SELECT func_id, func_id_sec, func_nome, func_data_nascimento, func_situacao FROM smc_func WHERE func_id_sec = '$row_UsuLogado[usu_sec]' AND func_situacao = '1' AND (Month(func_data_nascimento) = '$mes' AND Day(func_data_nascimento) = '$dia')";
$Aniversarios = mysql_query($query_Aniversarios, $SmecelNovo) or die(mysql_error());
$row_Aniversarios = mysql_fetch_assoc($Aniversarios);
$totalRows_Aniversarios = mysql_num_rows($Aniversarios);

//ANIVERSARIANTES DO DIA SEGUINTE
$dia = date('d')+1;
$mes = date('m');
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AniversariosAmanha = "SELECT func_id, func_id_sec, func_nome, func_data_nascimento, func_situacao FROM smc_func WHERE func_id_sec = '$row_UsuLogado[usu_sec]' AND func_situacao = '1' AND (Month(func_data_nascimento) = '$mes' AND Day(func_data_nascimento) = '$dia')";
$AniversariosAmanha = mysql_query($query_AniversariosAmanha, $SmecelNovo) or die(mysql_error());
$row_AniversariosAmanha = mysql_fetch_assoc($AniversariosAmanha);
$totalRows_AniversariosAmanha = mysql_num_rows($AniversariosAmanha);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Atualizacoes = "SELECT atualizacoes_id, atualizacoes_painel, atualizacoes_modulo, atualizacoes_texto, atualizacoes_data FROM smc_atualizacoes WHERE atualizacoes_painel IN (2,3,4,5,6)  ORDER BY atualizacoes_id DESC LIMIT 0,1";
$Atualizacoes = mysql_query($query_Atualizacoes, $SmecelNovo) or die(mysql_error());
$row_Atualizacoes = mysql_fetch_assoc($Atualizacoes);
$totalRows_Atualizacoes = mysql_num_rows($Atualizacoes);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AtualizacoesVisualizadas = "SELECT atualizacao_ver_id, atualizacao_ver_cod_atualizacao, atualizacao_ver_cod_usuario, atualizacao_ver_sec, atualizacao_ver_escola, atualizacao_ver_professor, atualizacao_ver_aluno, atualizacao_ver_data FROM smc_atualizacao_ver WHERE atualizacao_ver_cod_atualizacao = '$row_Atualizacoes[atualizacoes_id]' AND atualizacao_ver_sec = '$row_UsuLogado[usu_sec]' AND atualizacao_ver_cod_usuario = '$row_UsuLogado[usu_id]'";
$AtualizacoesVisualizadas = mysql_query($query_AtualizacoesVisualizadas, $SmecelNovo) or die(mysql_error());
$row_AtualizacoesVisualizadas = mysql_fetch_assoc($AtualizacoesVisualizadas);
$totalRows_AtualizacoesVisualizadas = mysql_num_rows($AtualizacoesVisualizadas);
?>

<?php if ($totalRows_Agenda<>0) { ?>
  <?php do { ?>
    <li class="ls-dismissable">
     <a href="#"><small>HOJE:</small><br><?php echo $row_Agenda['agenda_title']; ?></a>
   </li>
 <?php } while ($row_Agenda = mysql_fetch_assoc($Agenda)); ?>
<?php } ?> 

<?php if ($totalRows_AgendaAmanha<>0) { ?>
  <?php do { ?>
    <li class="ls-dismissable">
     <a href="#"><small>AMANHÃ É:</small><br><?php echo $row_AgendaAmanha['agenda_title']; ?></a>
   </li>
 <?php } while ($row_AgendaAmanha = mysql_fetch_assoc($AgendaAmanha)); ?>
<?php } ?> 

<?php if ($totalRows_Aniversarios<>0) { ?>
  <?php do { ?>
    <li class="ls-dismissable">
     <a href="#"><small>Hoje é aniversário de<br></small><?php echo $row_Aniversarios['func_nome']; ?></a>
   </li>
 <?php } while ($row_Aniversarios = mysql_fetch_assoc($Aniversarios)); ?>
<?php } ?>

<?php if ($totalRows_AniversariosAmanha<>0) { ?>
  <?php do { ?>
    <li class="ls-dismissable">
     <a href="#"><small>AMANHÃ É ANIVERSÁRIO DE<br></small><?php echo $row_AniversariosAmanha['func_nome']; ?></a>
   </li>
 <?php } while ($row_AniversariosAmanha = mysql_fetch_assoc($AniversariosAmanha)); ?>
<?php } ?>

<?php if ($totalRows_OC<>0) { ?>
  <?php do { ?>
    <li class="ls-dismissable">
     <a href="ocorrencia.php?oc=<?= $row_OC['ocorrencia_id'] ?>"><small>NOVA OCORRÊNCIA<br></small>Professora <?php echo $row_OC['func_nome']; ?> na turma: <?php echo $row_OC['turma_nome']; ?></a>
   </li>
 <?php } while ($row_OC = mysql_fetch_assoc($OC)); ?>
<?php } ?>

<?php if ($totalRows_AtualizacoesVisualizadas == 0) { ?>
  <li class="ls-dismissable"> 
    <a href="atualizacoes.php">Ver atualizações no sistema</a>
  </li>
<?php } ?>


<?php
mysql_free_result($Agenda);
mysql_free_result($AgendaAmanha);
mysql_free_result($Aniversarios);
mysql_free_result($AniversariosAmanha);
mysql_free_result($Atualizacoes);
mysql_free_result($AtualizacoesVisualizadas);
?>
