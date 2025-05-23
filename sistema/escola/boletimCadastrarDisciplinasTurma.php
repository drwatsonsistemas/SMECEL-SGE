<?php require_once('../../Connections/SmecelNovo.php'); ?>
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
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

$colname_Turma = "-1";
if (isset($_GET['turma'])) {
  $colname_Turma = $_GET['turma'];
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turma = sprintf("
  SELECT turma_id, turma_nome, turma_turno, turma_etapa, turma_matriz_id, matriz_id, matriz_nome, matriz_criterio_avaliativo
  FROM smc_turma 
  INNER JOIN smc_matriz ON matriz_id = turma_matriz_id
  WHERE turma_id = %s", GetSQLValueString($colname_Turma, "int"));
$Turma = mysql_query($query_Turma, $SmecelNovo) or die(mysql_error());
$row_Turma = mysql_fetch_assoc($Turma);
$totalRows_Turma = mysql_num_rows($Turma);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Criterios = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes, ca_rec_paralela FROM smc_criterios_avaliativos WHERE ca_id = '$row_Turma[matriz_criterio_avaliativo]'";
$Criterios = mysql_query($query_Criterios, $SmecelNovo) or die(mysql_error());
$row_Criterios = mysql_fetch_assoc($Criterios);
$totalRows_Criterios = mysql_num_rows($Criterios);

$numPeriodos = $row_Criterios['ca_qtd_periodos'];
$numAvaliacoes = $row_Criterios['ca_qtd_av_periodos'];
$notaMax = $row_Criterios['ca_nota_max_av'];
$notaMin = $row_Criterios['ca_nota_min_av'];
$notaMinAv = $row_Criterios['ca_nota_min_recuperacao_final'];

if ($totalRows_Turma==0) {
  header("Location:turmaListar.php?erro");  
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  
  if ($row_UsuLogado['usu_insert']=="N") {
    die(header(sprintf("Location: turmaListar.php?permissao")));
  }
  
  $matriz = $_POST['matriz'];  
  $turma = $_POST['turma'];  

  // Busca todos os alunos da turma
  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $query_Alunos = "
    SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
    vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
    vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia,
    aluno_id, aluno_nome, aluno_nascimento, aluno_filiacao1, aluno_foto, aluno_hash
    FROM smc_vinculo_aluno
    INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
    WHERE vinculo_aluno_situacao = '1' AND vinculo_aluno_boletim = '1' 
    AND vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' 
    AND vinculo_aluno_id_escola = '$row_UsuLogado[usu_escola]' 
    AND vinculo_aluno_id_turma = '$turma'
    ORDER BY aluno_nome ASC";
  $Alunos = mysql_query($query_Alunos, $SmecelNovo) or die(mysql_error());
  
  // Busca as disciplinas da matriz
  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $query_disciplinas = "SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_id_disciplina FROM smc_matriz_disciplinas WHERE matriz_disciplina_id_matriz = $matriz";
  $disciplinas = mysql_query($query_disciplinas, $SmecelNovo) or die(mysql_error());
  
  // Para cada aluno
  while($row_Alunos = mysql_fetch_assoc($Alunos)) {
    $idVinculo = $row_Alunos['vinculo_aluno_id'];
    
    // Para cada disciplina
    mysql_data_seek($disciplinas, 0);
    while($row_disciplinas = mysql_fetch_assoc($disciplinas)) {
      $idDisciplina = $row_disciplinas['matriz_disciplina_id_disciplina'];
      
      // Para cada período
      for ($p = 1; $p <= $numPeriodos; $p++) {
        // Para cada avaliação
        for ($a = 1; $a <= $numAvaliacoes; $a++) {
          mysql_select_db($database_SmecelNovo, $SmecelNovo);
          $query_nota = "
            SELECT nota_id, nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_max, nota_min, nota_valor, nota_hash 
            FROM smc_nota 
            WHERE nota_id_matricula = '$idVinculo' 
            AND nota_id_disciplina = '$idDisciplina' 
            AND nota_periodo = '$p' 
            AND nota_num_avaliacao = '$a'";
          $nota = mysql_query($query_nota, $SmecelNovo) or die(mysql_error());
          $row_nota = mysql_fetch_assoc($nota);
          $totalRows_nota = mysql_num_rows($nota);

          if ($totalRows_nota==0) {
            $hash = md5($idVinculo.$idDisciplina.$p.$a);
            $query = mysql_query("INSERT INTO smc_nota (nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_max, nota_min, nota_hash) VALUES ('$idVinculo','$idDisciplina','$p','$a','$notaMax','$notaMin','$hash')");
          }
        }

        // Verifica se tem recuperação paralela
        if ($row_Criterios['ca_rec_paralela']=="S") {
          
          mysql_select_db($database_SmecelNovo, $SmecelNovo);
          $query_notaRecPar = "
            SELECT nota_id, nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_max, nota_min, nota_valor, nota_hash 
            FROM smc_nota 
            WHERE nota_id_matricula = '$idVinculo' 
            AND nota_id_disciplina = '$idDisciplina' 
            AND nota_periodo = '$p' 
            AND nota_num_avaliacao = '98'";
          $notaRecPar = mysql_query($query_notaRecPar, $SmecelNovo) or die(mysql_error());
          $row_notaRecPar = mysql_fetch_assoc($notaRecPar);
          $totalRows_notaRecPar = mysql_num_rows($notaRecPar);


          if ($totalRows_notaRecPar==0) {
            $hashRec = md5($idVinculo.$idDisciplina.$p."98");
            $queryRec = mysql_query("INSERT INTO smc_nota (
                nota_id_matricula, 
                nota_id_disciplina, 
                nota_periodo, 
                nota_num_avaliacao, 
                nota_max, 
                nota_min, 
                nota_hash
            ) VALUES (
                '$idVinculo',
                '$idDisciplina',
                '$p',
                '98',
                '$notaMax', 
                '$notaMinAv',
                '$hashRec'
            )");
            
           
          }
        }
      }

      // Gera nota de avaliação final por disciplina
      $hashAf = md5($idVinculo.$idDisciplina."99"."99");
      $query = mysql_query("INSERT INTO smc_nota (nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_max, nota_min, nota_hash) VALUES ('$idVinculo','$idDisciplina','99','99','','$notaMinAv','$hashAf')");
    }
  }

  // Registra o log
  $usu = $_POST['usu_id'];
  $esc = $_POST['usu_escola'];
  $detalhes = "Geração em massa de notas para turma: " . $row_Turma['turma_nome'];
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
    '11', 
    " . GetSQLValueString($detalhes, "text") . ", 
    '$dat')
  ";
  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($sql, $SmecelNovo) or die(mysql_error());

  $updateGoTo = "turmaListar.php?boletimcadastrado";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}
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
</head>
<body>
<?php include_once ("menu-top.php"); ?>
          <?php include_once ("menu-esc.php"); ?>
<main class="ls-main ">
  <div class="container-fluid">
 
    <h1 class="ls-title-intro ls-ico-home">GERAR NOTAS DA TURMA</h1>
    <!-- CONTEÚDO -->
    
    <div class="ls-alert-warning">
      <strong>Atenção!</strong> Esta funcionalidade serve para gerar notas que foram adicionadas após a geração do boletim. Exemplos de uso:
      <ul style="margin-left: 20px;">
        <li>Quando foi gerado o boletim dos alunos, e depois adicionaram uma 4ª avaliação (por exemplo)</li>
        <li>Quando foi adicionada a recuperação paralela logo após terem gerado os boletins</li>
        <li>Mensagens como "Informe o código" ao fazer o lançamento de notas</li>
      </ul><br>
      <p>Esta funcionalidade pode gerar erros.</p>
    </div>

    <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form-horizontal row" data-ls-module="form">
      <fieldset>
        <div class="row">
          <label class="ls-label col-md-6">
            <b class="ls-label-text">Turma</b>
            <input type="text" value="<?php echo $row_Turma['turma_nome']; ?>" class="ls-field" disabled>
          </label>
          <label class="ls-label col-md-6">
            <b class="ls-label-text">Matriz</b>
            <input type="text" value="<?php echo $row_Turma['matriz_nome']; ?>" class="ls-field" disabled>
          </label>
        </div>
      </fieldset>
      <div class="ls-actions-btn">
        <input type="submit" value="GERAR NOTAS PARA TODA TURMA" class="ls-btn-primary">
        <a class="ls-btn-danger" href="turmaListar.php">Cancelar</a> </div>
      <input type="hidden" name="MM_update" value="form1">
      <input type="hidden" name="matriz" value="<?php echo $row_Turma['matriz_id']; ?>">
      <input type="hidden" name="turma" value="<?php echo $row_Turma['turma_id']; ?>">
      <input type="hidden" name="usu_id" value="<?php echo $row_UsuLogado['usu_id']; ?>">
      <input type="hidden" name="usu_escola" value="<?php echo $row_UsuLogado['usu_escola']; ?>">
    </form>
    <p>&nbsp;</p>
    <!-- CONTEÚDO --> 
  </div>
</main>
<?php include_once ("menu-dir.php"); ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>
 
</body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($Criterios);

mysql_free_result($EscolaLogada);

mysql_free_result($Turma);
?>
