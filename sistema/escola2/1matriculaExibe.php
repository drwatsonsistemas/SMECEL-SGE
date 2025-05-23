<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include('fnc/inverteData.php'); ?>
<?php include('fnc/idade.php'); ?>
<?php include('../funcoes/url_base.php'); ?>
<?php include "fnc/anti_injection.php"; ?>

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

$anoLetivo = $row_AnoLetivo['ano_letivo_ano'];
if (isset($_GET['ano'])) {
	
  if ($_GET['ano'] == "") {
		//echo "TURMA EM BRANCO";	
    header("Location: turmasAlunosVinculados.php?nada"); 
    exit;
  }

  $anoLetivo = anti_injection($_GET['ano']);
  $anoLetivo = (int)$anoLetivo;
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

$colname_Matricula = "-1";
if (isset($_GET['cmatricula'])) {
  $colname_Matricula = $_GET['cmatricula'];
  $cmatricula = GetSQLValueString($colname_Matricula, "text");
}


if (!isset($_GET['cmatricula'])) {
  header("Location: vinculoAlunoExibirTurma.php?erro");
  exit;
}


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matricula = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia,
aluno_id, aluno_nome, aluno_nascimento, aluno_hash, aluno_foto, aluno_filiacao1, aluno_filiacao2,
aluno_endereco, aluno_numero, aluno_bairro, aluno_municipio, aluno_uf, aluno_cep, matriz_id, matriz_criterio_avaliativo, ca_id, ca_questionario_conceitos, ca_forma_avaliacao,
CASE aluno_localizacao
WHEN 1 THEN 'ZONA URBANA'
WHEN 2 THEN 'ZONA RURAL'
END AS aluno_localizacao,
aluno_telefone, aluno_celular, aluno_email, aluno_laudo, aluno_emergencia_tel1, aluno_emergencia_tel2,
turma_id, turma_nome, turma_etapa, turma_matriz_id, etapa_id, etapa_id_filtro,
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno_nome,
CASE vinculo_aluno_situacao
WHEN 1 THEN 'MATRICULADO(A)'
WHEN 2 THEN '<span class=\"ls-color-danger\">TRANSFERIDO(A)</span>'
WHEN 3 THEN '<span class=\"ls-color-danger\">DESISTENTE</span>'
WHEN 4 THEN 'FALECIDO(A)'
WHEN 5 THEN 'OUTROS'
END AS vinculo_aluno_situacao_nome,
CASE vinculo_aluno_transporte
WHEN 'S' THEN 'UTILIZA'
WHEN 'N' THEN 'NÃO UTILIZA'
END AS vinculo_aluno_transporte_nome 
FROM smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
LEFT JOIN smc_etapa ON etapa_id = turma_etapa
LEFT JOIN smc_matriz ON matriz_id = turma_matriz_id  
LEFT JOIN smc_criterios_avaliativos ON ca_id = matriz_criterio_avaliativo 
WHERE vinculo_aluno_hash = $cmatricula AND vinculo_aluno_id_escola = $row_EscolaLogada[escola_id]";
$Matricula = mysql_query($query_Matricula, $SmecelNovo) or die(mysql_error());
$row_Matricula = mysql_fetch_assoc($Matricula);
$totalRows_Matricula = mysql_num_rows($Matricula);

if ($totalRows_Matricula == 0) { 
  header("Location: vinculoAlunoExibirTurma.php?erro");
  exit;
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ocorrencia = "
SELECT ocorrencia_id, ocorrencia_id_aluno, ocorrencia_id_turma, ocorrencia_id_escola, 
ocorrencia_ano_letivo, ocorrencia_data, ocorrencia_hora, ocorrencia_tipo,
CASE ocorrencia_tipo
WHEN 1 THEN 'ADVERTÊNCIA'
WHEN 2 THEN 'SUSPENSÃO'
WHEN 3 THEN 'OUTRAS'
END AS ocorrencia_tipo_nome, 
ocorrencia_afastamento_de, ocorrencia_afastamento_ate, ocorrencia_total_dias, ocorrencia_descricao 
FROM smc_ocorrencia
WHERE ocorrencia_id_aluno = '$row_Matricula[vinculo_aluno_id_aluno]' AND ocorrencia_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'";
$Ocorrencia = mysql_query($query_ocorrencia, $SmecelNovo) or die(mysql_error());
$row_Ocorrencia = mysql_fetch_assoc($Ocorrencia);
$totalRows_Ocorrencia = mysql_num_rows($Ocorrencia);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_FaltasAulas = "
SELECT faltas_alunos_id, faltas_alunos_matricula_id, faltas_alunos_disciplina_id, faltas_alunos_numero_aula, 
faltas_alunos_data, faltas_alunos_justificada 
FROM smc_faltas_alunos
WHERE faltas_alunos_matricula_id = '$row_Matricula[vinculo_aluno_id]' AND faltas_alunos_justificada = 'N'";
$FaltasAulas = mysql_query($query_FaltasAulas, $SmecelNovo) or die(mysql_error());
$row_FaltasAulas = mysql_fetch_assoc($FaltasAulas);
$totalRows_FaltasAulas = mysql_num_rows($FaltasAulas);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AulasTurma = "
SELECT * FROM smc_plano_aula WHERE plano_aula_id_turma = '$row_Matricula[vinculo_aluno_id_turma]'";
$AulasTurma = mysql_query($query_AulasTurma, $SmecelNovo) or die(mysql_error());
$row_AulasTurma = mysql_fetch_assoc($AulasTurma);
$totalRows_AulasTurma = mysql_num_rows($AulasTurma);




$lugar = $row_Matricula['aluno_endereco']."+".$row_Matricula['aluno_numero']."+".$row_Matricula['aluno_bairro']."+".$row_Matricula['aluno_municipio']."+".$row_Matricula['aluno_uf']."+".$row_Matricula['aluno_cep'];
$lugar = strtolower($lugar);
$lugar = str_replace("/","",$lugar); 
$lugar = str_replace(" ","+",$lugar); 
$maps = "https://www.google.com.br/maps/place/".$lugar;
// https://www.google.com.br/maps/place/Av. Paulista, 1578 - Bela Vista, São Paulo - SP


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_VinculosAnteriores = "
SELECT 
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_ponto_id, 
vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, 
vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, vinculo_aluno_vacina_data_retorno, vinculo_aluno_conselho, vinculo_aluno_conselho_parecer, vinculo_aluno_internet, 
vinculo_aluno_multietapa, vinculo_aluno_rel_aval,
turma_id, turma_nome, 
escola_id, escola_nome 
FROM 
smc_vinculo_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola
WHERE vinculo_aluno_id_aluno = '$row_Matricula[vinculo_aluno_id_aluno]'
ORDER BY vinculo_aluno_ano_letivo DESC
";
$VinculosAnteriores = mysql_query($query_VinculosAnteriores, $SmecelNovo) or die(mysql_error());
$row_VinculosAnteriores = mysql_fetch_assoc($VinculosAnteriores);
$totalRows_VinculosAnteriores = mysql_num_rows($VinculosAnteriores);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_LogsAcesso = "
SELECT 
login_aluno_id, login_aluno_id_aluno, login_aluno_data_hora, login_aluno_ip, login_aluno_ano 
FROM smc_login_aluno
WHERE login_aluno_id_aluno = '$row_Matricula[vinculo_aluno_id_aluno]'
ORDER BY login_aluno_id DESC
";
$LogsAcesso = mysql_query($query_LogsAcesso, $SmecelNovo) or die(mysql_error());
$row_LogsAcesso = mysql_fetch_assoc($LogsAcesso);
$totalRows_LogsAcesso = mysql_num_rows($LogsAcesso);

function formata_tel($tel){
 //verificando se é celular
 $array_pre_numero = array ("9","8","7");
 // retirando espaços
 $tel = trim($tel);
 // seria melhor cirar uma white list.
 // tratando manualmente
 $tel = str_replace("-", "", $tel);
 $tel = str_replace("(", "", $tel);
 $tel = str_replace(")", "", $tel);
 $tel = str_replace("_", "", $tel);
 $tel = str_replace(" ", "", $tel);
 //---------------------
 $tamanho = strlen($tel);
 // maior
 if($tamanho  > '10'){
  // não faz nada
  $telefone = $tel;
}
 //igual
if($tamanho == '10'){
  $verificando_celular = substr($tel, 2, 1);
  if(in_array($verificando_celular, $array_pre_numero)){
    $telefone.= substr($tel, 0, 2);
  $telefone.= "9"; // nono digito
  $telefone.= substr($tel, 2);
}
else{
 $telefone = $tel;
}
}
if($tamanho < '10'){
  $telefone = $tel;
}
return "55".$telefone;
}

function primeiro_nome($str){
  $nome = explode(" ",$str);
  $primeiro_nome = $nome[0];
  $primeiro_nome = strtolower($primeiro_nome);
  $primeiro_nome = ucfirst($primeiro_nome);

  return $primeiro_nome;
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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <?php include_once ("menu-top.php"); ?>
  <?php include_once ("menu-esc.php"); ?>
  <main class="ls-main">
    <div class="container-fluid">

      <h1 class="ls-title-intro ls-ico-home">MATRÍCULA Nº <strong><?php echo $row_Matricula['vinculo_aluno_id']; ?></strong> - Ano Letivo <?php echo $row_Matricula['vinculo_aluno_ano_letivo']; ?></h1>
      <!-- CONTEÚDO -->

      <div class="row">
        <div class="col-sm-12">
          <?php if (isset($_GET["erro"])) { ?>
            <div class="ls-alert-danger ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> OCORREU UM ERRO NA AÇÃO ANTERIOR. UM E-MAIL FOI ENVIADO AO ADMINISTRADOR DO SISTEMA. </div>
          <?php } ?>
          <?php if (isset($_GET["dadosEditados"])) { ?>
            <div class="ls-alert-success ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> OS DADOS DO ALUNO FORAM SALVOS COM SUCESSO. </div>
          <?php } ?>
          <?php if (isset($_GET["ocorrenciaRegistrada"])) { ?>
            <div class="ls-alert-success ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> OCORRÊNCIA DO ALUNO REGISTRADO COM SUCESSO. </div>
          <?php } ?>
          <?php if (isset($_GET["boletimcadastrado"])) { ?>
            <div class="ls-alert-success ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> BOLETIM CADASTRADO COM SUCESSO. </div>
          <?php } ?>
          <?php if (isset($_GET["vinculoEditado"])) { ?>
            <div class="ls-alert-success ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> VÍNCULO DO ALUNO EDITADO COM SUCESSO. </div>
          <?php } ?>
          <?php if (isset($_GET["excluido"])) { ?>
            <div class="ls-alert-info ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> VÍNCULO EXCLUIDO COM SUCESSO. </div>
          <?php } ?>

          <?php if (isset($_GET["resetado"])) { ?>
            <div class="ls-alert-info ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> BOLETIM/QUESTIONÁRIO RESETADO COM SUCESSO. </div>
          <?php } ?>
          <?php if (isset($_GET["aprovadoConselho"])) { ?>
            <br>
            <div class="ls-alert-warning">Conselho de classe lançado com sucesso.</div>
          <?php } ?>
          <?php if (isset($_GET["reprovadoFaltas"])) { ?>
            <br>
            <div class="ls-alert-danger">Aluno REPROVADO por faltas.</div>
          <?php } ?>


        </div>
      </div>


      <div class="row">  
       <div class="col-sm-12">
        <a href="vinculoAlunoExibirTurma.php?ano=<?php echo $anoLetivo; ?>" class="ls-btn-primary">Voltar</a>
        <div data-ls-module="dropdown" class="ls-dropdown"> <a href="#" class="ls-btn">LISTAGEM</a>
          <ul class="ls-dropdown-nav">
            <li><a href="vinculoAlunoExibirTurma.php?ct=<?php echo $row_Matricula['turma_id']; ?>&ano=<?php echo $anoLetivo; ?>">Relação de alunos da turma <?php echo $row_Matricula['turma_nome']; ?></a></li>
            <li><a href="vinculoAlunoExibirTurma.php?ano=<?php echo $anoLetivo; ?>">Relação de turmas da escola</a></li>
          </ul>
        </div>
        <div data-ls-module="dropdown" class="ls-dropdown"> <a href="#" class="ls-btn">GERENCIAMENTO</a>
          <ul class="ls-dropdown-nav">
           <?php if ($row_Matricula['vinculo_aluno_boletim']==1) { ?>
            <li>
              <?php if ($row_Matricula['etapa_id_filtro'] == 1) { ?>
                <a href="boletimResetarIndividualConceito.php?cmatricula=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" class="ls-btn ls-btn-xs ls-btn-secondary" id="resetarButtonConceito">Resetar questionário de conceito</a>
              <?php } else { ?>
                <?php if ($row_Matricula['ca_questionario_conceitos'] == "S") { ?>
                  <a href="conceitoEfVer.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" class="ls-btn-primary ls-btn-xs">Resetar Questionário - EF (em breve)</a>
                <?php } ?>

                <?php if ($row_Matricula['ca_forma_avaliacao'] == "N") { ?>
                  <a href="boletimResetarIndividualNotas.php?cmatricula=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" class="ls-btn ls-btn-xs ls-btn-secondary" id="resetarButtonNotas">Resetar boletim</a>
                <?php } ?>

                <?php if ($row_Matricula['ca_forma_avaliacao'] == "Q") { ?>
                  <a href="boletimVerQQ.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" class="ls-btn ls-btn-xs">RESETAR QQ EM BREVE</a>
                <?php } ?>
              <?php } ?>
            </li>
          <?php } ?>
        </ul>
      </div>
      <br><br>
    </div>
  </div>  

  <div class="row">
    <div class="col-sm-12">

      <div class="row">

        <div class="ls-box">
          <header class="ls-info-header">
            <h5 class="ls-title-5">
              Aluno(a) <strong><?php echo $row_Matricula['aluno_nome']; ?></strong><br><br>
              Turma: <strong><?php echo $row_Matricula['turma_nome']; ?> - <?php echo $row_Matricula['turma_turno_nome']; ?></strong><br><br>
              Ano Letivo: <strong><?php echo $row_Matricula['vinculo_aluno_ano_letivo']; ?></strong>
            </h5>
          </header>

          <div class="col-md-2 col-sm-12">  
            <?php if ($row_Matricula['aluno_foto'] == "") { ?>
              <img src="../../aluno/fotos/semfoto.jpg" width="100%">
            <?php } else { ?>
              <img src="<?php echo URL_BASE.'aluno/fotos/' ?><?php echo $row_Matricula['aluno_foto']; ?>" width="100%">
            <?php } ?>
            <br>
            <small><a class="ls-tag" href="celular.php?aluno=<?php echo htmlentities($row_Matricula['aluno_hash'], ENT_COMPAT, 'utf-8'); ?>&cmatricula=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>">ALTERAR FOTO</a></small> 
          </div>	

          <div class="col-md-10 col-sm-12">


            <ul class="ls-tabs-nav" id="awesome-dropdown-tab">
              <li class="ls-active"><a data-ls-module="tabs" href="#tab1">MATRÍCULA</a></li>
              <li><a data-ls-module="tabs" href="#tab2">DADOS</a></li>
              <li><a data-ls-module="tabs" href="#tab6">CONTATO</a></li>
              <li><a data-ls-module="tabs" href="#tab3">DECLARAÇÕES</a></li>
              <li><a data-ls-module="tabs" href="#tab4">DADOS DE ACESSO</a></li>
              <li><a data-ls-module="tabs" href="#tab5">VINCULOS ANTERIORES</a></li>
            </ul>

            <div class="ls-tabs-container" id="awesome-tab-content">
              <div id="tab1" class="ls-tab-content ls-active">
                <p>
                  <p><strong>MATRÍCULA:</strong> <?php echo str_pad($row_Matricula['vinculo_aluno_id'], 5, '0', STR_PAD_LEFT); ?> </p>
                  <p><strong>TURMA:</strong> <?php echo $row_Matricula['turma_nome']; ?> </p>
                  <p><strong>TURNO:</strong> <?php echo $row_Matricula['turma_turno_nome']; ?></p>
                  <p><strong>SITUAÇÃO:</strong> <?php echo $row_Matricula['vinculo_aluno_situacao_nome']; ?>
                  <?php if ($row_Matricula['vinculo_aluno_situacao'] == "2") { ?>
                    <span class="ls-background-danger"> - TRANSFERÊNCIA EM <?php echo inverteData($row_Matricula['vinculo_aluno_datatransferencia']); ?></span>
                  <?php } ?>
                </p>
                <p><strong>TRANSPORTE ESCOLAR:</strong> <?php echo $row_Matricula['vinculo_aluno_transporte_nome']; ?></p>
                <a class="ls-ico-pencil ls-btn-primary ls-btn-xs" class="ls-ico-pencil2" href="vinculoAlunoEditar.php?cmatricula=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>">EDITAR MATRÍCULA</a>
              </p>
            </div>
            <div id="tab2" class="ls-tab-content">
              <p>

                <p><strong>NOME:</strong> <?php echo $row_Matricula['aluno_nome']; ?></p>
                <p><strong>NASCIMENTO:</strong> <?php echo inverteData($row_Matricula['aluno_nascimento']); ?> (<?php echo idade($row_Matricula['aluno_nascimento']); ?> anos)</p>
                <p><strong>FILIAÇÃO:</strong> <?php echo $row_Matricula['aluno_filiacao1']; ?></p>
                <p><strong>FILIAÇÃO:</strong> <?php echo $row_Matricula['aluno_filiacao2']; ?></p>
                <p>
                  <?php //echo "<strong>ENDEREÇO:</strong> <a href=\"{$maps}\" target=\"_blank\">VER NO MAPA</a>"; ?>

                  <a class="ls-ico-pencil ls-btn-primary ls-btn-xs" href="alunoEditar.php?hash=<?php echo $row_Matricula['aluno_hash']; ?>&cmatricula=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>">EDITAR DADOS</a> 
                </p>

              </p>
            </div>

            <div id="tab6" class="ls-tab-content">
              <p>

                <p><strong>ENDEREÇO:</strong> <?php echo $row_Matricula['aluno_endereco']; ?>, <?php echo $row_Matricula['aluno_numero']; ?> - <?php echo $row_Matricula['aluno_bairro']; ?> (<?php echo $row_Matricula['aluno_localizacao']; ?>)</p>
                <p><strong>E-MAIL:</strong> <?php echo $row_Matricula['aluno_email']; ?></p>
                <p><strong>TELEFONE(S):</strong> 

                  <br><br>

                  <?php if ($row_Matricula['aluno_telefone'] <> "") { ?>
                    <a class="ls-btn ls-btn-xs ls-ico-bell-o" href="https://api.whatsapp.com/send?phone=<?php echo formata_tel($row_Matricula['aluno_telefone']); ?>&text=Ol%C3%A1%20<?php echo primeiro_nome($row_Matricula['aluno_nome']); ?>." target="_blank"><?php echo $row_Matricula['aluno_telefone']; ?></a>
                  <?php } ?>

                  <?php if ($row_Matricula['aluno_celular'] <> "") { ?>
                    <a class="ls-btn ls-btn-xs ls-ico-bell-o" href="https://api.whatsapp.com/send?phone=<?php echo formata_tel($row_Matricula['aluno_celular']); ?>&text=Ol%C3%A1%20<?php echo primeiro_nome($row_Matricula['aluno_nome']); ?>." target="_blank"><?php echo $row_Matricula['aluno_celular']; ?></a>
                  <?php } ?>

                  <?php if ($row_Matricula['aluno_emergencia_tel1'] <> "") { ?>
                    <a class="ls-btn ls-btn-xs ls-ico-bell-o" href="https://api.whatsapp.com/send?phone=<?php echo formata_tel($row_Matricula['aluno_emergencia_tel1']); ?>&text=Ol%C3%A1%20<?php echo primeiro_nome($row_Matricula['aluno_nome']); ?>." target="_blank"><?php echo $row_Matricula['aluno_emergencia_tel1']; ?></a>
                  <?php } ?>

                  <?php if ($row_Matricula['aluno_emergencia_tel2'] <> "") { ?>
                    <a class="ls-btn ls-btn-xs ls-ico-bell-o" href="https://api.whatsapp.com/send?phone=<?php echo formata_tel($row_Matricula['aluno_emergencia_tel2']); ?>&text=Ol%C3%A1%20<?php echo primeiro_nome($row_Matricula['aluno_nome']); ?>." target="_blank"><?php echo $row_Matricula['aluno_emergencia_tel2']; ?></a>
                  <?php } ?>

                  <br>


                </p>

                <p>
                  <?php //echo "<strong>ENDEREÇO:</strong> <a href=\"{$maps}\" target=\"_blank\">VER NO MAPA</a>"; ?>

                  <a class="ls-ico-pencil ls-btn-primary ls-btn-xs" href="alunoEditar.php?hash=<?php echo $row_Matricula['aluno_hash']; ?>&cmatricula=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>">EDITAR DADOS</a> 
                </p>

              </p>
            </div>

            <div id="tab3" class="ls-tab-content">




              <div class="ls-tabs-btn">  
                <ul class="ls-tabs-btn-nav">

                  <li class="col-md-3 col-xs-6"><a class="ls-btn" href="print_form_matricula.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank"> Ficha de Matrícula</a></li>
                  <?php if($row_Matricula['ca_forma_avaliacao'] == "Q"){ ?>
                    <li class="col-md-3 col-xs-6"><a class="ls-btn" href="fichaIndividualAlunoMN.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank"> Ficha Individual</a></li>
                  <?php }else{ ?>
                  <li class="col-md-3 col-xs-6"><a class="ls-btn" href="fichaIndividualAluno.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank"> Ficha Individual</a></li>
                  <?php } ?>
                  <li class="col-md-3 col-xs-6"><a class="ls-btn" href="print_comprovante_matricula.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank"> Comprovante de Matrícula</a></li>
                  <li class="col-md-3 col-xs-6"><a class="ls-btn" href="print_declaracao_matricula.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank"> Declaração de Matrícula</a></li>

                  <li class="col-md-3 col-xs-6"><a class="ls-btn" href="imprimir/print_bolsa_familia.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank"> Dec. Bolsa Família</a></li>
                  <li class="col-md-3 col-xs-6"><a class="ls-btn" href="imprimir/print_bolsa_familia_faltas.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank"> Dec. Bolsa Família c/faltas</a></li>
                  <li class="col-md-3 col-xs-6"><a class="ls-btn" href="print_declaracao_transferencia.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank"> Transferência (aprovado)</a></li>
                  <li class="col-md-3 col-xs-6"><a class="ls-btn" href="print_declaracao_transferencia_desistente.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank"> Transferência (desistente)</a></li>

                  <li class="col-md-3 col-xs-6"><a class="ls-btn" href="print_declaracao_escola_publica.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank"> Declaração / Escola Pública</a></li>
                  <li class="col-md-3 col-xs-6"><a class="ls-btn" href="print_declaracao_transferencia.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank"> Conclusão de Curso</a></li>
                  <li class="col-md-3 col-xs-6"><a class="ls-btn" href="print_declaracao_transferencia_conservado.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank"> Declaração aluno conservado</a></li>
                  <li class="col-md-3 col-xs-6"><a class="ls-btn" href="print_declaracao_transferencia_em_curso.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank"> Declaração Transf. em Curso</a></li>

                  <li class="col-md-3 col-xs-6"><a class="ls-btn" href="print_parecer_aluno.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank"> Parecer do aluno</a></li>
                  <li class="col-md-3 col-xs-6"><a class="ls-btn" href="print_dec_trans_curso_notas.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank"> Transf. em Curso (notas)</a></li>   
                  <li class="col-md-3 col-xs-6"><a class="ls-btn" href="print_dec_trans_curso_conceitos.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank"> Transf. em Curso (conceitos)</a></li>   
                  <li class="col-md-3 col-xs-6"><a class="ls-btn" href="diario_frequencia_individual.php?ct=<?php echo $row_Matricula['turma_id']; ?>&ano=<?php echo $row_Matricula['vinculo_aluno_ano_letivo']; ?>&hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank"> Frequência Individual</a></li>
                  <li class="col-md-3 col-xs-6"><a class="ls-btn" href="boletimVerImprimir.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank"> Boletim individual</a></li>
                  <?php if($row_Matricula['ca_forma_avaliacao'] == "Q"){ ?>
                    <li class="col-md-3 col-xs-6"><a class="ls-btn" href="termo_compromisso_matricula_MN.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank"> Termo de compromisso de matrícula</a></li>
                  <?php } ?>

                  <?php if($row_Matricula['aluno_laudo'] == 1){ ?>
                    <li class="col-md-3 col-xs-6"><a class="ls-btn" href="carteirinha_deficiencia_individual.php?hash=<?php echo $row_Matricula['aluno_hash']; ?>" target="_blank"> CIPTA</a></li>
                  <?php } ?>



                </ul>
              </div>






            </div>
            <div id="tab4" class="ls-tab-content">
              <p>



                <div class="ls-box">
                  <h3 class="ls-title-5"><strong>Dados de acesso ao painel do aluno</strong></h3>
                  Nascimento: <strong><?php echo inverteData($row_Matricula['aluno_nascimento']); ?></strong><br>
                  Código: <strong><?php echo str_pad($row_Matricula['aluno_id'], 5, '0', STR_PAD_LEFT); ?></strong><br>
                  Senha: <strong><?php echo substr($row_Matricula['aluno_hash'],0,5); ?></strong> 
                </div>

                <p>Total de acessos: <strong><?php echo $totalRows_LogsAcesso; ?></strong></p>
                <p>Último Acesso: <strong><?php echo date("d/m/Y à\s H:i", strtotime($row_LogsAcesso['login_aluno_data_hora'])); ?></strong></p>


              </p>
            </div>
            <div id="tab5" class="ls-tab-content">
              <p>

                <h3 class="ls-title-5"><strong>Vínculos anteriores</strong></h3>
                <table class="ls-table ls-sm-space" width="100%">
                  <thead>
                   <tr>
                     <th width="100" class="ls-txt-center">MATRÍCULA</th>
                     <th width="100" class="ls-txt-center">ANO</th>
                     <th width="200" class="ls-txt-center">TURMA</th>
                     <th class="ls-txt-center">ESCOLA</th>
                     <th width="100"></th>
                   </tr>
                 </thead>

                 <tbody>
                  <?php do { ?>
                    <tr>
                     <td class="ls-txt-center"><?php echo str_pad($row_VinculosAnteriores['vinculo_aluno_id'], 5, '0', STR_PAD_LEFT); ?></td>
                     <td class="ls-txt-center"><?php echo $row_VinculosAnteriores['vinculo_aluno_ano_letivo']; ?></td>
                     <td class="ls-txt-center"><?php echo $row_VinculosAnteriores['turma_nome']; ?></td>
                     <td class="ls-txt-center"><?php echo $row_VinculosAnteriores['escola_nome']; ?></td>
                     <td class="ls-txt-center"><?php if ($row_VinculosAnteriores['escola_id'] == $row_Matricula['vinculo_aluno_id_escola']) { ?><a href="matriculaExibe.php?cmatricula=<?php echo $row_VinculosAnteriores['vinculo_aluno_hash']; ?>">VER</a><?php } ?></td>

                   </tr>
                 <?php } while ($row_VinculosAnteriores = mysql_fetch_assoc($VinculosAnteriores)); ?>
               </tbody>
             </table>
           </p>
         </div>
       </div>



     </div>

   </div>


   <div class="ls-box ls-board-box">
    <div id="sending-stats" class="row ls-clearfix">
      <div class="col-sm-12 col-md-4">
        <div class="ls-box">
          <div class="ls-box-head">
            <h6 class="ls-title-4 <?php if ($totalRows_Ocorrencia > 0) { echo "ls-color-danger"; } ?>">OCORRÊNCIAS</h6>
          </div>
          <div class="ls-box-body"> <span class="ls-board-data"> <strong class="<?php if ($totalRows_Ocorrencia > 0) { echo "ls-color-danger"; } ?>"><?php echo $totalRows_Ocorrencia; ?> <small class="<?php if ($totalRows_Ocorrencia > 0) { echo "ls-color-danger"; } ?>">ocorrência(s)</small></strong> </span> </div>
          <div class="ls-box-footer"> <a href="ocorrenciaExibe.php?cmatricula=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" class="ls-btn ls-btn-xs">Visualizar</a> </div>
        </div>
      </div>



      <?php 

      $perfrequencia = number_format((($totalRows_FaltasAulas/$totalRows_AulasTurma) * 100),0); 

      if ($perfrequencia > 100) {

        $perfrequencia = 100;

      }

      ?>

      <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
      <script type="text/javascript">
        google.charts.load('current', {'packages':['gauge']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {

          var data = google.visualization.arrayToDataTable([
            ['Label', 'Value'],
            [' ', 0]
            ]);

          var options = {
            width: 120, height: 100,
            redFrom: 75, redTo: 100,
            yellowFrom:50, yellowTo: 75,
            greenFrom:0, greenTo: 25,
            minorTicks: 5,
            animation:{
              duration: 5000,
              easing: 'out'
            }
          };

          var chart = new google.visualization.Gauge(document.getElementById('chart_div'));

          chart.draw(data, options);



          setInterval(function() {
            data.setValue(0, 1, <?php echo $perfrequencia; ?>);
            chart.draw(data, options);
          }, 1000);



        }
      </script>

      <div class="col-sm-6 col-lg-3">
        <div class="ls-box">
          <div class="ls-box-head">
            <h6 class="ls-title-4">PERCENTUAL DE FREQUÊNCIA<a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Número de faltas sem justificativa (<?php echo $totalRows_FaltasAulas; ?>) pelo número de aulas registradas até o momento (<?php echo $totalRows_AulasTurma; ?>)" data-title="PERCENTUAL DE FALTAS"></a> </h6>
          </div>
          <div class="ls-box-body">
            <div class="ls-half-board-data">

              <div id="chart_div" style="width: 100%"></div>

            </div>

          </div>
        </div>
      </div>

      <div class="col-sm-12 col-md-4">
        <div class="ls-box">
          <div class="ls-box-head">
            <h6 class="ls-title-4 <?php if ($totalRows_FaltasAulas > 0) { echo "ls-color-danger"; } ?>">FALTAS </h6>
          </div>
          <div class="ls-box-body"> 


            <strong class="<?php if ($totalRows_FaltasAulas > 0) { echo "ls-color-danger"; } ?>"><?php echo $totalRows_FaltasAulas ?> <small class="<?php if ($totalRows_FaltasAulas > 0) { echo "ls-color-danger"; } ?>"></small></strong> 

            <div data-ls-module="progressBar" role="progressbar" class="ls-animated" aria-valuenow="<?php echo $perfrequencia; ?>"></div> 

            <span class="ls-board-data">              

            </span> </div>
            <div class="ls-box-footer"> <a href="faltasMostrar.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" class="ls-btn ls-btn-xs">Visualizar</a>  </div>
          </div>
        </div>
        <div class="col-sm-12 col-md-4">
          <div class="ls-box">
            <div class="ls-box-head">
              <h6 class="ls-title-4">RENDIMENTO</h6>
            </div>
            <?php if ($row_Matricula['vinculo_aluno_boletim']==0) { ?>
              <div class="ls-box-body"> <span class="ls-board-data ls-transparent-25"> <strong><span class="ls-ico-cancel-circle"></span> <small>boletim não gerado</small></strong> </span> </div>
              <div class="ls-box-footer">
                <?php if ($row_Matricula['etapa_id_filtro'] == 1) { ?>
                  <a href="boletimCadastrarConceitos.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" class="ls-btn-primary ls-btn-xs">Gerar qustionário - EI</a>
                <?php } else { ?>

                  <?php if ($row_Matricula['ca_questionario_conceitos']=="S") { ?>

                    <a href="boletimCadastrarConceitosEf.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" class="ls-btn-primary ls-btn-xs">Gerar questionário - EF</a>

                  <?php } else { ?>

                    <a href="boletimCadastrarDisciplinas.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" class="ls-btn-primary ls-btn-xs">Gerar boletim</a>

                  <?php } ?>


                <?php } ?>
              </div>
            <?php } ?>
            <?php if ($row_Matricula['vinculo_aluno_boletim']==1) { ?>


              <?php if ($row_Matricula['etapa_id_filtro'] == 1) { ?>
                <div class="ls-box-body"> <span class="ls-board-data"> <strong><span class="ls-ico-checkmark-circle ls-color-success"></span> <small class="ls-color-success">relatório gerado</small></strong> </span> </div>
                <div class="ls-box-footer">
                  <div class="ls-display-flex ls-flex-column">
                    <a href="conceitoVer.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" class="ls-btn ls-btn-xs ls-btn-primary mb-2">Visualizar</a>

                  </div>

                </div>
              <?php } else { ?>
                <div class="ls-box-body"> <span class="ls-board-data"> <strong><span class="ls-ico-checkmark-circle ls-color-success"></span> <small class="ls-color-success">boletim gerado</small></strong> </span> </div>
                <div class="ls-box-footer">






                  <?php if ($row_Matricula['ca_questionario_conceitos']=="S") { ?>

                    <a href="conceitoEfVer.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" class="ls-btn-primary ls-btn-xs">Visualizar Questionário - EF</a> 
                  <?php } else { ?>

                    <?php if ($row_Matricula['ca_forma_avaliacao']=="N") { ?>
                      <a href="boletimVer.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" class="ls-btn ls-btn-xs">Visualizar</a>

                    <?php } ?>

                    <?php if ($row_Matricula['ca_forma_avaliacao']=="Q") { ?>
                      <a href="boletimVerQQ.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" class="ls-btn ls-btn-xs">Visualizar</a> 
                    <?php } ?>

                  <?php } ?>





                <?php } ?>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>




    <hr>
  </div>
</div>

<!-- CONTEÚDO --> 
</div>
</div>
</div>

</main>
<?php include_once ("menu-dir.php"); ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script> 
<script>
  $(document).ready(function(){
    $('#resetarButtonConceito').on('click', function(e){
      e.preventDefault(); // Impede o comportamento padrão do link
      var link = $(this).attr('href');
      Swal.fire({
        title: "Você tem certeza?",
        text: "Ao resetar o questionário, perderá todos os conceitos já lançados!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sim, tenho certeza!"
      }).then((result) => {
        if (result.isConfirmed) {
          const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
              confirmButton: "ls-btn-primary ",
              cancelButton: "ls-btn-primary-danger ls-sm-margin-right"
            },
            buttonsStyling: true
          });
          swalWithBootstrapButtons.fire({
            title: "Tem certeza mesmo?",
            text: "Essa ação é irreversível!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Confirmo!",
            cancelButtonText: "Nãooo, cancela!",
            reverseButtons: true
          }).then((result) => {
            if (result.isConfirmed) {
              window.location.href = link; // Redireciona para o link do href
            } else if (
    /* Read more about handling dismissals below */
              result.dismiss === Swal.DismissReason.cancel
              ) {
              swalWithBootstrapButtons.fire({
                title: "Cancelado!",
                text: "Ufa! Foi por pouco :)",
                icon: "error"
              });
            }
          });
        }
      });
        //
    });

    $('#resetarButtonNotas').on('click', function(e){
      e.preventDefault(); // Impede o comportamento padrão do link
      var link = $(this).attr('href');
      Swal.fire({
        title: "Você tem certeza?",
        text: "Ao resetar o boletim, perderá todos as notas já lançadas!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sim, tenho certeza!"
      }).then((result) => {
        if (result.isConfirmed) {
          const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
              confirmButton: "ls-btn-primary ",
              cancelButton: "ls-btn-primary-danger ls-sm-margin-right"
            },
            buttonsStyling: true
          });
          swalWithBootstrapButtons.fire({
            title: "Tem certeza mesmo?",
            text: "Essa ação é irreversível!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Confirmo!",
            cancelButtonText: "Nãooo, cancela!",
            reverseButtons: true
          }).then((result) => {
            if (result.isConfirmed) {
              window.location.href = link; // Redireciona para o link do href
            } else if (
    /* Read more about handling dismissals below */
              result.dismiss === Swal.DismissReason.cancel
              ) {
              swalWithBootstrapButtons.fire({
                title: "Cancelado!",
                text: "Ufa! Foi por pouco :)",
                icon: "error"
              });
            }
          });
        }
      });
        //
    });
  });
</script>
</body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($FaltasAulas);

mysql_free_result($LogsAcesso);

mysql_free_result($VinculosAnteriores);

mysql_free_result($Ocorrencia);

mysql_free_result($Matricula);
?>
