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

$colname_AlterarStatus = "-1";
if (isset($_GET['c'])) {
  $colname_AlterarStatus = $_GET['c'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AlterarStatus = sprintf("
  SELECT 
  vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, 
  aluno_id, aluno_nome, aluno_nascimento, aluno_filiacao1,
  turma_id, turma_nome, turma_turno, turma_etapa, turma_matriz_id, 
  etapa_id, etapa_nome, 
  matriz_id, matriz_nome, matriz_criterio_avaliativo
  FROM smc_vinculo_aluno 
  INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
  INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
  INNER JOIN smc_etapa ON etapa_id = turma_etapa
  INNER JOIN smc_matriz ON matriz_id = turma_matriz_id 
  WHERE vinculo_aluno_boletim = '0' AND (vinculo_aluno_id_escola = $row_EscolaLogada[escola_id] AND vinculo_aluno_hash = %s)", GetSQLValueString($colname_AlterarStatus, "text"));
$AlterarStatus = mysql_query($query_AlterarStatus, $SmecelNovo) or die(mysql_error());
$row_AlterarStatus = mysql_fetch_assoc($AlterarStatus);
$totalRows_AlterarStatus = mysql_num_rows($AlterarStatus);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Criterios = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes, ca_rec_paralela, ca_forma_avaliacao FROM smc_criterios_avaliativos WHERE ca_id = '$row_AlterarStatus[matriz_criterio_avaliativo]'";
$Criterios = mysql_query($query_Criterios, $SmecelNovo) or die(mysql_error());
$row_Criterios = mysql_fetch_assoc($Criterios);
$totalRows_Criterios = mysql_num_rows($Criterios);

$numPeriodos = $row_Criterios['ca_qtd_periodos'];
$numAvaliacoes = $row_Criterios['ca_qtd_av_periodos'];
$notaMax = $row_Criterios['ca_nota_max_av'];
$notaMin = $row_Criterios['ca_nota_min_av'];
$notaMinAv = $row_Criterios['ca_nota_min_recuperacao_final'];


if ($totalRows_AlterarStatus==0) {
	header("Location:vinculoAlunoExibirTurma.php?erro");	
}


if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	
	if ($row_UsuLogado['usu_insert']=="N") {
		header(sprintf("Location: vinculoAlunoExibirTurma.php?permissao"));
		break;
	}
	
  $matriz = $_POST['matriz'];	
  $idVinculo = $_POST['aluno'];	

  $updateSQL = sprintf("UPDATE smc_vinculo_aluno SET vinculo_aluno_boletim=%s WHERE vinculo_aluno_id=%s",
   GetSQLValueString($_POST['vinculo_aluno_boletim'], "int"),
   GetSQLValueString($_POST['aluno'], "int"));

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
   '11', 
   '($detalhes)', 
   '$dat')
   ";
   mysql_select_db($database_SmecelNovo, $SmecelNovo);
   $Result1 = mysql_query($sql, $SmecelNovo) or die(mysql_error());
// ** REGISTRO DE LOG DE USUÁRIO **




  //Inserir disciplinas no loop


   mysql_select_db($database_SmecelNovo, $SmecelNovo);
   $query_disciplinas = "SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_id_disciplina FROM smc_matriz_disciplinas WHERE matriz_disciplina_id_matriz = $matriz";
   $disciplinas = mysql_query($query_disciplinas, $SmecelNovo) or die(mysql_error());
   $row_disciplinas = mysql_fetch_assoc($disciplinas);
   $totalRows_disciplinas = mysql_num_rows($disciplinas);

   if ($totalRows_disciplinas == 0) {

    if($row_Criterios['ca_forma_avaliacao'] == "Q"){
      $updateGoTo = "boletimVerQQ.php?semdisciplina";
    }else{
      $updateGoTo = "boletimVer.php?semdisciplina";
    }

    if (isset($_SERVER['QUERY_STRING'])) {
      $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
      $updateGoTo .= $_SERVER['QUERY_STRING'];
    }
    header(sprintf("Location: %s", $updateGoTo));
  }

  
  do { 
//Loop disciplinas
    for ($p = 1; $p <= $numPeriodos; $p++) {
		//Loop períodos	
     for ($a = 1; $a <= $numAvaliacoes; $a++) {
			//loop avaliaçoes

      $idDisciplina = $row_disciplinas['matriz_disciplina_id_disciplina'];
      $hash = md5($idVinculo.$idDisciplina.$p.$a);
						//$query = mysql_query("INSERT INTO smc_boletim_disciplinas (boletim_id_vinculo_aluno, boletim_id_disciplina) VALUES ('$idVinculo','$idDisciplina')");
      $query = mysql_query("INSERT INTO smc_nota (nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_max, nota_min, nota_hash) VALUES ('$idVinculo','$idDisciplina','$p','$a','$notaMax','$notaMin','$hash')");


    }

    if ($row_Criterios['ca_rec_paralela']=="S") {



				//Gera nota de recuperação por unidade
      $hashRec = md5($idVinculo.$idDisciplina.$p."98");
      $queryRec = mysql_query("INSERT INTO smc_nota (nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_max, nota_min, nota_hash) VALUES ('$idVinculo','$idDisciplina','$p','98','','$notaMinAv','$hashRec')");

    }
  }

//Gera nota de avaliação final por disciplina
  $hashAf = md5($idVinculo.$idDisciplina."99"."99");
  $query = mysql_query("INSERT INTO smc_nota (nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_max, nota_min, nota_hash) VALUES ('$idVinculo','$idDisciplina','99','99','','$notaMinAv','$hashAf')");

} while ($row_disciplinas = mysql_fetch_assoc($disciplinas));


if($row_Criterios['ca_forma_avaliacao'] == "Q"){
  $updateGoTo = "boletimVerQQ.php?semdisciplina";
}else{
  $updateGoTo = "boletimVer.php?semdisciplina";
}
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

      <h1 class="ls-title-intro ls-ico-home">CADASTRAR BOLETIM</h1>
      <!-- CONTEÚDO -->

      <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form-horizontal row" data-ls-module="form">
        <fieldset>
          <div class="row">
            <label class="ls-label col-md-4"> <b class="ls-label-text">Nome</b>
              <input type="text" value="<?php echo $row_AlterarStatus['aluno_nome']; ?>" class="ls-field" disabled>
            </label>
            <label class="ls-label col-md-4"> <b class="ls-label-text">Nascimento</b>
              <input type="text" value="<?php echo inverteData($row_AlterarStatus['aluno_nascimento']); ?>" class="ls-field" disabled>
            </label>
            <label class="ls-label col-md-4"> <b class="ls-label-text">Filiação</b>
              <input type="text" value="<?php echo $row_AlterarStatus['aluno_filiacao1']; ?>" class="ls-field" disabled>
            </label>
          </div>
          <div class="row">
            <label class="ls-label col-md-3"> <b class="ls-label-text">Série</b>
              <input type="text" value="<?php echo $row_AlterarStatus['turma_nome']; ?> - <?php if ($row_AlterarStatus['turma_turno']==1) { echo "MATUTINO"; } else if ($row_AlterarStatus['turma_turno']==2) { echo "VESPERTINO"; }  else if ($row_AlterarStatus['turma_turno']==3) { echo "NOTURNO"; } else { echo "INTEGRAL"; } ?>" class="ls-field" disabled>
            </label>
            <label class="ls-label col-md-3"> <b class="ls-label-text">Matriz</b>
              <input type="text" value="<?php echo $row_AlterarStatus['matriz_nome']; ?>" class="ls-field" disabled>
            </label>
            <label class="ls-label col-md-4"> <b class="ls-label-text">Etapa</b>
              <input type="text" value="<?php echo $row_AlterarStatus['etapa_nome']; ?>" class="ls-field" disabled>
            </label>
            <label class="ls-label col-md-2"> <b class="ls-label-text">Ano Letivo</b>
              <input type="text" value="<?php echo $row_AlterarStatus['vinculo_aluno_ano_letivo']; ?>" class="ls-field" disabled>
            </label>
          </div>
        </fieldset>
        <div class="ls-actions-btn">
          <input type="submit" value="GERAR BOLETIM" class="ls-btn-primary">
          <a class="ls-btn-danger" href="turmasAlunosVinculados.php">Cancelar</a> </div>
          <input type="hidden" name="vinculo_aluno_boletim" value="1">
          <input type="hidden" name="MM_update" value="form1">
          <input type="hidden" name="matriz" value="<?php echo $row_AlterarStatus['matriz_id']; ?>">
          <input type="hidden" name="aluno" value="<?php echo $row_AlterarStatus['vinculo_aluno_id']; ?>">
          <input type="hidden" name="usu_id" value="<?php echo $row_UsuLogado['usu_id']; ?>">
          <input type="hidden" name="usu_escola" value="<?php echo $row_UsuLogado['usu_escola']; ?>">
          <input type="hidden" name="detalhes" value="<?php echo $row_AlterarStatus['aluno_nome']; ?> - <?php echo $row_AlterarStatus['turma_nome']; ?>">
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

  mysql_free_result($AlterarStatus);

//mysql_free_result($VerAluno);
  ?>
