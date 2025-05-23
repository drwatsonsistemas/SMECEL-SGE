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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

$anoLetivo = $row_AnoLetivo['ano_letivo_ano'];
if (isset($_GET['ano'])) {

  if ($_GET['ano'] == "") {
    //echo "TURMA EM BRANCO";	
    header("Location: turmasAlunosVinculados.php?nada");
    exit;
  }

  $anoLetivo = anti_injection($_GET['ano']);
  $anoLetivo = (int) $anoLetivo;
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
aluno_endereco, aluno_numero, aluno_bairro, aluno_municipio, aluno_uf, aluno_raca, aluno_cep, 
aluno_nome_social,aluno_tel_pai, aluno_tel_mae, aluno_bio, aluno_face, aluno_insta, aluno_x,
matriz_id, matriz_criterio_avaliativo, ca_id, ca_questionario_conceitos, ca_forma_avaliacao,
CASE aluno_localizacao
WHEN 1 THEN 'ZONA URBANA'
WHEN 2 THEN 'ZONA RURAL'
END AS aluno_localizacao,
aluno_telefone, aluno_celular, aluno_email, aluno_laudo, aluno_emergencia_tel1, aluno_emergencia_tel2, aluno_aceite_termos,
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

$totalRows_DepEnviados = "";
if ($row_Matricula['aluno_aceite_termos'] == "S") {

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $query_DepEnviados = "
  SELECT * FROM smc_aluno_depoimentos_flag 
  INNER JOIN smc_aluno ON aluno_id = aluno_depoimento_flag_id_para 
  WHERE aluno_depoimento_flag_id_de = '$row_Matricula[vinculo_aluno_id_aluno]' ORDER BY aluno_depoimento_flag_id DESC";
  $DepEnviados = mysql_query($query_DepEnviados, $SmecelNovo) or die(mysql_error());
  $row_DepEnviados = mysql_fetch_assoc($DepEnviados);
  $totalRows_DepEnviados = mysql_num_rows($DepEnviados);

}

mysql_select_db($database_SmecelNovo, $SmecelNovo);

$query_Documentos = sprintf(
  "SELECT 
        d.id, 
        d.aluno_id, 
        d.arquivo_path, 
        d.tipo_documento,
        d.nome_original, 
        d.mime_type, 
        d.uploaded_at 
     FROM smc_aluno_documentos d 
     WHERE d.aluno_id = %s 
     ORDER BY d.uploaded_at DESC",
  GetSQLValueString($row_Matricula['vinculo_aluno_id_aluno'], "int")
);

$Documentos = mysql_query($query_Documentos, $SmecelNovo) or die(mysql_error());
$row_Documentos = mysql_fetch_assoc($Documentos);
$totalRows_Documentos = mysql_num_rows($Documentos);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Ocorrencia = "
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
$Ocorrencia = mysql_query($query_Ocorrencia, $SmecelNovo) or die(mysql_error());
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




$lugar = $row_Matricula['aluno_endereco'] . "+" . $row_Matricula['aluno_numero'] . "+" . $row_Matricula['aluno_bairro'] . "+" . $row_Matricula['aluno_municipio'] . "+" . $row_Matricula['aluno_uf'] . "+" . $row_Matricula['aluno_cep'];
$lugar = strtolower($lugar);
$lugar = str_replace("/", "", $lugar);
$lugar = str_replace(" ", "+", $lugar);
$maps = "https://www.google.com.br/maps/place/" . $lugar;
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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1") && isset($_POST['tipo_documento']) && isset($_FILES['aluno_documento'])) {
  $uploadDir = 'aluno/documentos/'; // Pasta onde os arquivos serão salvos

  if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
  }

  $aluno_id = $row_Matricula['vinculo_aluno_id_aluno']; // Pegar o ID do aluno via sessão, POST ou GET
  $tipoDocumento = $_POST['tipo_documento'];
  $nomeOriginal = $_FILES['aluno_documento']['name'];
  $mimeType = $_FILES['aluno_documento']['type'];
  $tamanho = $_FILES['aluno_documento']['size'];
  $tmpName = $_FILES['aluno_documento']['tmp_name'];

  // Criar nome único para evitar sobrescrever arquivos
  $filename = $aluno_id . '_' . preg_replace('/\s+/', '_', $nomeOriginal);
  $destination = $uploadDir . $filename;

  if (move_uploaded_file($tmpName, $destination)) {
    mysql_select_db($database_SmecelNovo, $SmecelNovo);

    // Inserir no banco
    $insertSQL = sprintf(
      "INSERT INTO smc_aluno_documentos (aluno_id, tipo_documento, arquivo_path, nome_original, mime_type, tamanho) 
           VALUES (%s, %s, %s, %s, %s, %s)",
      GetSQLValueString($aluno_id, "int"),
      GetSQLValueString($tipoDocumento, "text"),
      GetSQLValueString($filename, "text"),
      GetSQLValueString($nomeOriginal, "text"),
      GetSQLValueString($mimeType, "text"),
      GetSQLValueString($tamanho, "int")
    );

    mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

    $updateGoTo = "matriculaExibe.php?dadosEditados";
    if (isset($_SERVER['QUERY_STRING'])) {
      $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
      $updateGoTo .= $_SERVER['QUERY_STRING'];
    }
    header(sprintf("Location: %s", $updateGoTo));
  } else {
    echo "<script>alert('Erro ao salvar o documento.')</script>";
  }
}

function formata_tel($tel)
{
  //verificando se é celular
  $array_pre_numero = array("9", "8", "7");
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
  if ($tamanho > '10') {
    // não faz nada
    $telefone = $tel;
  }
  //igual
  if ($tamanho == '10') {
    $verificando_celular = substr($tel, 2, 1);
    if (in_array($verificando_celular, $array_pre_numero)) {
      $telefone .= substr($tel, 0, 2);
      $telefone .= "9"; // nono digito
      $telefone .= substr($tel, 2);
    } else {
      $telefone = $tel;
    }
  }
  if ($tamanho < '10') {
    $telefone = $tel;
  }
  return "55" . $telefone;
}

function primeiro_nome($str)
{
  $nome = explode(" ", $str);
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
    function gtag() { dataLayer.push(arguments); }
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
  <link rel="stylesheet" type="text/css" href="css/locastyle.css">
  <link rel="stylesheet" type="text/css" href="css/preloader.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    .report-category {
      color: #2c3e50;
      margin: 20px 0 10px 0;
      padding-bottom: 5px;
      border-bottom: 2px solid #3498db;
    }

    .ls-btn {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 10px;
      transition: all 0.3s ease;
      width: 100%;
      min-height: 40px;
      padding: 8px 15px;
      white-space: normal;
      word-wrap: break-word;
      justify-content: flex-start;
      line-height: 1.2;
    }

    .ls-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    .ls-btn i {
      font-size: 1.1em;
      min-width: 20px;
      text-align: center;
      flex-shrink: 0;
    }

    .ls-tabs-btn-nav {
      display: flex;
      flex-wrap: wrap;
      margin: 0 -10px;
    }

    .ls-tabs-btn-nav li {
      padding: 0 10px;
      margin-bottom: 10px;
    }

    .ls-tabs-btn-nav li.col-md-12 {
      width: 100%;
      padding: 0;
      margin-bottom: 15px;
    }

    @media (max-width: 768px) {
      .ls-tabs-btn-nav li {
        width: 100%;
      }
    }

    /* Estilização do container do formulário */
    .form-container {
      max-width: 450px;
      margin: 20px auto;
      padding: 20px;
      background: #f8f9fa;
      border-radius: 8px;
      box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);

    }

    /* Títulos e rótulos */
    .form-container h3 {
      text-align: center;
      color: #333;
    }

    .form-container label {
      font-weight: bold;
      margin-top: 10px;
      display: block;
      color: #555;
    }

    /* Select estilizado */
    .form-container select,
    .form-container input[type="file"] {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 14px;
      background: #fff;
    }

    /* Botões */
    .form-container .btn {
      width: 100%;
      padding: 10px;
      margin-top: 15px;
      font-size: 16px;
      font-weight: bold;
      border: none;
      cursor: pointer;
      border-radius: 5px;
      text-align: center;
    }

    .btn-primary {
      background: #007bff;
      color: white;
    }

    .btn-primary:hover {
      background: #0056b3;
    }

    .btn-secondary {
      background: #28a745;
      color: white;
    }

    .btn-secondary:hover {
      background: #1e7e34;
    }
  </style>
</head>

<body>
  <?php include_once("menu-top.php"); ?>
  <?php include_once("menu-esc.php"); ?>
  <main class="ls-main">
    <div class="container-fluid">

      <h1 class="ls-title-intro ls-ico-home">MATRÍCULA Nº
        <strong><?php echo $row_Matricula['vinculo_aluno_id']; ?></strong> - Ano Letivo
        <?php echo $row_Matricula['vinculo_aluno_ano_letivo']; ?>
      </h1>
      <!-- CONTEÚDO -->

      <div class="ls-modal" id="confirmarInformacoes">
        <div class="ls-modal-box">
          <div class="ls-modal-header">
            <button data-dismiss="modal">&times;</button>
            <h4 class="ls-modal-title">CONFIRMAR DADOS</h4>
          </div>
          <div class="ls-modal-body" id="myModalBody">
            <p>

            <p><strong>NOME:</strong> <?php echo $row_Matricula['aluno_nome']; ?></p>
            <p><strong>CELULAR:</strong> <?php echo $row_Matricula['aluno_celular']; ?></p>
            <p><strong>TELEFONE:</strong> <?php echo $row_Matricula['aluno_telefone']; ?></p>
            <p><strong>TELEFONE MÃE:</strong> <?php echo $row_Matricula['aluno_tel_mae']; ?></p>
            <p><strong>TELEFONE PAI:</strong> <?php echo $row_Matricula['aluno_tel_pai']; ?></p>
            <p><strong>EMAIL:</strong> <?php echo $row_Matricula['aluno_email']; ?></p>
            <p><strong>NASCIMENTO:</strong> <?php echo inverteData($row_Matricula['aluno_nascimento']); ?>
              (<?php echo idade($row_Matricula['aluno_nascimento']); ?> anos)</p>
            <p>
              <?php //echo "<strong>ENDEREÇO:</strong> <a href=\"{$maps}\" target=\"_blank\">VER NO MAPA</a>"; ?>


            </p>
            <p><strong>ENDEREÇO: </strong>
              <?php
              echo "{$row_Matricula['aluno_endereco']}, nº {$row_Matricula['aluno_numero']}, {$row_Matricula['aluno_bairro']},";
              echo "<br>{$row_Matricula['aluno_municipio']} - {$row_Matricula['aluno_uf']},";
              echo "<br>CEP: {$row_Matricula['aluno_cep']}";
              ?>
            </p>
            <?php if (empty($row_Matricula['aluno_raca']) || $row_Matricula['aluno_raca'] == '6') { // '6' significa 'não declarada' ?>
              <div class="ls-alert-danger">
                <strong>Atenção:</strong> O campo de cor/raça do aluno está incompleto ou marcado como "Não Declarado".
                Por favor, atualize essa informação para continuar.
              </div>
            <?php } ?>


            </p>
          </div>
          <div class="ls-modal-footer" style="display: flex; justify-content: space-between; align-items: center;">
            <!-- Botão de Atualizar Dados alinhado à esquerda -->
            <a class="ls-btn ls-ico-pencil ls-btn-primary"
              href="alunoEditar.php?hash=<?php echo $row_Matricula['aluno_hash']; ?>&cmatricula=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>">
              ATUALIZAR DADOS
            </a>

            <!-- Contêiner para os botões de impressão alinhados à direita -->
            <div style="display: flex; gap: 10px; margin-left:10px">
              <a class="ls-btn" target="_blank"
                href="print_comprovante_matricula.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>">
                IMPRIMIR COMPROVANTE DE MATRÍCULA
              </a>
              <a class="ls-btn" target="_blank"
                href="print_form_matricula.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>">
                IMPRIMIR FICHA DE MATRÍCULA
              </a>
            </div>
          </div>

        </div>
      </div><!-- /.modal -->


      <div class="row">
        <div class="col-sm-12">
          <?php if (isset($_GET["erro"])) { ?>
            <div class="ls-alert-danger ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
              OCORREU UM ERRO NA AÇÃO ANTERIOR. UM E-MAIL FOI ENVIADO AO ADMINISTRADOR DO SISTEMA. </div>
          <?php } ?>
          <?php if (isset($_GET["dadosEditados"])) { ?>
            <div class="ls-alert-success ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
              OS DADOS DO ALUNO FORAM SALVOS COM SUCESSO. </div>
          <?php } ?>
          <?php if (isset($_GET["ocorrenciaRegistrada"])) { ?>
            <div class="ls-alert-success ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
              OCORRÊNCIA DO ALUNO REGISTRADO COM SUCESSO. </div>
          <?php } ?>
          <?php if (isset($_GET["boletimcadastrado"])) { ?>
            <div class="ls-alert-success ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
              BOLETIM CADASTRADO COM SUCESSO. </div>
          <?php } ?>
          <?php if (isset($_GET["vinculoEditado"])) { ?>
            <div class="ls-alert-success ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
              VÍNCULO DO ALUNO EDITADO COM SUCESSO. </div>
          <?php } ?>
          <?php if (isset($_GET["excluido"])) { ?>
            <div class="ls-alert-info ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
              VÍNCULO EXCLUIDO COM SUCESSO. </div>
          <?php } ?>

          <?php if (isset($_GET["resetado"])) { ?>
            <div class="ls-alert-info ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
              BOLETIM/QUESTIONÁRIO RESETADO COM SUCESSO. </div>
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
              <li><a
                  href="vinculoAlunoExibirTurma.php?ct=<?php echo $row_Matricula['turma_id']; ?>&ano=<?php echo $anoLetivo; ?>">Relação
                  de alunos da turma <?php echo $row_Matricula['turma_nome']; ?></a></li>
              <li><a href="vinculoAlunoExibirTurma.php?ano=<?php echo $anoLetivo; ?>">Relação de turmas da escola</a>
              </li>
            </ul>
          </div>
          <div data-ls-module="dropdown" class="ls-dropdown"> <a href="#" class="ls-btn">GERENCIAMENTO</a>
            <ul class="ls-dropdown-nav">
              <?php if ($row_Matricula['vinculo_aluno_boletim'] == 1) { ?>
                <li>
                  <?php if ($row_Matricula['etapa_id_filtro'] == 1) { ?>
                    <a href="boletimResetarIndividualConceito.php?cmatricula=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>"
                      class="ls-btn ls-btn-xs ls-btn-secondary" id="resetarButtonConceito">Resetar questionário de
                      conceito</a>
                  <?php } else { ?>
                    <?php if ($row_Matricula['ca_questionario_conceitos'] == "S") { ?>
                      <a href="boletimResetarIndividualConceitoEF.php?cmatricula=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>"
                        class="ls-btn-primary ls-btn-xs">Resetar Questionário - EF</a>
                    <?php } ?>

                    <?php if ($row_Matricula['ca_forma_avaliacao'] == "N") { ?>
                      <a href="boletimResetarIndividualNotas.php?cmatricula=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>"
                        class="ls-btn ls-btn-xs ls-btn-secondary" id="resetarButtonNotas">Resetar boletim</a>
                    <?php } ?>

                    <?php if ($row_Matricula['ca_forma_avaliacao'] == "Q") { ?>
                      <a href="boletimVerQQ.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>"
                        class="ls-btn ls-btn-xs">RESETAR QQ EM BREVE</a>
                    <?php } ?>
                  <?php } ?>
                </li>
              <?php } ?>
            </ul>
          </div>
          <a href="vinculoAlunoCadastrar.php?c=<?php echo $row_Matricula['aluno_hash']; ?>"
            class="ls-btn-primary">MATRICULAR EM ATV. COMPLEMENTAR</a>

          <br><br>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-12">

          <div class="row">

            <div class="ls-box">
              <header class="ls-info-header">
                <h5 class="ls-title-5">
                  Aluno(a) <strong>
                    <?php if ($row_Matricula['aluno_nome_social'] == '') {
                      echo $row_Matricula['aluno_nome'];
                    } else {
                      echo $row_Matricula['aluno_nome_social'];
                    } ?>
                  </strong><br><br>
                  Turma: <strong><?php echo $row_Matricula['turma_nome']; ?> -
                    <?php echo $row_Matricula['turma_turno_nome']; ?></strong><br><br>
                  Ano Letivo: <strong><?php echo $row_Matricula['vinculo_aluno_ano_letivo']; ?></strong>
                </h5>
              </header>

              <div class="col-md-2 col-sm-12">
                <?php if ($row_Matricula['aluno_foto'] == "") { ?>
                  <img src="../../aluno/fotos/semfoto.jpg" width="100%">
                <?php } else { ?>
                  <img src="<?php echo URL_BASE . 'aluno/fotos/' ?><?php echo $row_Matricula['aluno_foto']; ?>"
                    width="100%">
                <?php } ?>
                <br>
                <small><a class="ls-tag"
                    href="celular.php?aluno=<?php echo htmlentities($row_Matricula['aluno_hash'], ENT_COMPAT, 'utf-8'); ?>&cmatricula=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>">ALTERAR
                    FOTO</a></small>
              </div>

              <div class="col-md-10 col-sm-12">


                <ul class="ls-tabs-nav" id="awesome-dropdown-tab" style="">
                  <li class="ls-active"><a data-ls-module="tabs" href="#tab1">MATRÍCULA</a></li>
                  <li><a data-ls-module="tabs" href="#tab2">DADOS</a></li>
                  <li><a data-ls-module="tabs" href="#tab6">CONTATO</a></li>
                  <li><a data-ls-module="tabs" href="#tab3">DECLARAÇÕES</a></li>
                  <li><a data-ls-module="tabs" href="#tab4">DADOS DE ACESSO</a></li>
                  <li><a data-ls-module="tabs" href="#tab5">VINCULOS ANTERIORES</a></li>
                  <li><a data-ls-module="tabs" href="#tab8">DOCUMENTOS</a></li>
                  <li><a data-ls-module="tabs" href="#tab7"><?php if ($totalRows_DepEnviados > 0) { ?><span
                          class="ls-ico-info ls-ico-left ls-color-danger"></span><?php } ?> EDUCONNECT</a></li>
                </ul>

                <div class="ls-tabs-container" id="awesome-tab-content">
                  <div id="tab1" class="ls-tab-content ls-active">
                    <p>
                    <p><strong>MATRÍCULA:</strong>
                      <?php echo str_pad($row_Matricula['vinculo_aluno_id'], 5, '0', STR_PAD_LEFT); ?> </p>
                    <p><strong>TURMA:</strong> <?php echo $row_Matricula['turma_nome']; ?> </p>
                    <p><strong>TURNO:</strong> <?php echo $row_Matricula['turma_turno_nome']; ?></p>
                    <p><strong>SITUAÇÃO:</strong> <?php echo $row_Matricula['vinculo_aluno_situacao_nome']; ?>
                      <?php if ($row_Matricula['vinculo_aluno_situacao'] == "2") { ?>
                        <span class="ls-background-danger"> - TRANSFERÊNCIA EM
                          <?php echo inverteData($row_Matricula['vinculo_aluno_datatransferencia']); ?></span>
                      <?php } ?>
                    </p>
                    <p><strong>TRANSPORTE ESCOLAR:</strong>
                      <?php echo $row_Matricula['vinculo_aluno_transporte_nome']; ?></p>
                    <a class="ls-ico-pencil ls-btn-primary ls-btn-xs" class="ls-ico-pencil2"
                      href="vinculoAlunoEditar.php?cmatricula=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>">EDITAR
                      MATRÍCULA</a>
                    </p>
                  </div>
                  <div id="tab2" class="ls-tab-content">
                    <p>

                    <p><strong>NOME:</strong> <?php echo $row_Matricula['aluno_nome']; ?></p>
                    <?php if ($row_Matricula['aluno_nome_social'] != '') { ?>
                      <p><strong>NOME SOCIAL:</strong> <?php echo $row_Matricula['aluno_nome_social']; ?></p>
                    <?php } ?>
                    <p><strong>NASCIMENTO:</strong> <?php echo inverteData($row_Matricula['aluno_nascimento']); ?>
                      (<?php echo idade($row_Matricula['aluno_nascimento']); ?> anos)</p>
                    <p><strong>FILIAÇÃO:</strong> <?php echo $row_Matricula['aluno_filiacao1']; ?></p>
                    <p><strong>FILIAÇÃO:</strong> <?php echo $row_Matricula['aluno_filiacao2']; ?></p>
                    <p>
                      <?php //echo "<strong>ENDEREÇO:</strong> <a href=\"{$maps}\" target=\"_blank\">VER NO MAPA</a>"; ?>

                      <a class="ls-ico-pencil ls-btn-primary ls-btn-xs"
                        href="alunoEditar.php?hash=<?php echo $row_Matricula['aluno_hash']; ?>&cmatricula=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>">EDITAR
                        DADOS</a>
                    </p>

                    </p>
                  </div>

                  <div id="tab6" class="ls-tab-content">
                    <p>

                    <p><strong>ENDEREÇO:</strong> <?php echo $row_Matricula['aluno_endereco']; ?>,
                      <?php echo $row_Matricula['aluno_numero']; ?> - <?php echo $row_Matricula['aluno_bairro']; ?>
                      (<?php echo $row_Matricula['aluno_localizacao']; ?>)
                    </p>
                    <p><strong>E-MAIL:</strong> <?php echo $row_Matricula['aluno_email']; ?></p>
                    <p><strong>TELEFONE(S):</strong>

                      <br><br>

                      <?php if ($row_Matricula['aluno_telefone'] <> "") { ?>
                      <p><small>Telefone</small><br>
                        <a class="ls-btn ls-btn-xs ls-ico-bell-o"
                          href="https://api.whatsapp.com/send?phone=<?php echo formata_tel($row_Matricula['aluno_telefone']); ?>&text=Ol%C3%A1%20<?php echo primeiro_nome($row_Matricula['aluno_nome']); ?>."
                          target="_blank"><?php echo $row_Matricula['aluno_telefone']; ?></a>
                      </p>
                    <?php } ?>

                    <?php if ($row_Matricula['aluno_celular'] <> "") { ?>
                      <p><small>Celular</small><br>
                        <a class="ls-btn ls-btn-xs ls-ico-bell-o"
                          href="https://api.whatsapp.com/send?phone=<?php echo formata_tel($row_Matricula['aluno_celular']); ?>&text=Ol%C3%A1%20<?php echo primeiro_nome($row_Matricula['aluno_nome']); ?>."
                          target="_blank"><?php echo $row_Matricula['aluno_celular']; ?></a>
                      </p>
                    <?php } ?>

                    <?php if ($row_Matricula['aluno_emergencia_tel1'] <> "") { ?>
                      <p><small>Emergencia 1</small><br>
                        <a class="ls-btn ls-btn-xs ls-ico-bell-o"
                          href="https://api.whatsapp.com/send?phone=<?php echo formata_tel($row_Matricula['aluno_emergencia_tel1']); ?>&text=Ol%C3%A1%20<?php echo primeiro_nome($row_Matricula['aluno_nome']); ?>."
                          target="_blank"><?php echo $row_Matricula['aluno_emergencia_tel1']; ?></a>
                      </p>
                    <?php } ?>

                    <?php if ($row_Matricula['aluno_emergencia_tel2'] <> "") { ?>
                      <p><small>Emergencia 2</small><br>
                        <a class="ls-btn ls-btn-xs ls-ico-bell-o"
                          href="https://api.whatsapp.com/send?phone=<?php echo formata_tel($row_Matricula['aluno_emergencia_tel2']); ?>&text=Ol%C3%A1%20<?php echo primeiro_nome($row_Matricula['aluno_nome']); ?>."
                          target="_blank"><?php echo $row_Matricula['aluno_emergencia_tel2']; ?></a>
                      </p>
                    <?php } ?>

                    <?php if ($row_Matricula['aluno_tel_mae'] <> "") { ?>
                      <p><small>Mãe</small><br>
                        <a class="ls-btn ls-btn-xs ls-ico-bell-o"
                          href="https://api.whatsapp.com/send?phone=<?php echo formata_tel($row_Matricula['aluno_tel_mae']); ?>&text=Ol%C3%A1%20<?php echo primeiro_nome($row_Matricula['aluno_nome']); ?>."
                          target="_blank"><?php echo $row_Matricula['aluno_emergencia_tel2']; ?></a>
                      </p>
                    <?php } ?>

                    <?php if ($row_Matricula['aluno_tel_pai'] <> "") { ?>
                      <p><small>Pai</small><br>
                        <a class="ls-btn ls-btn-xs ls-ico-bell-o"
                          href="https://api.whatsapp.com/send?phone=<?php echo formata_tel($row_Matricula['aluno_tel_pai']); ?>&text=Ol%C3%A1%20<?php echo primeiro_nome($row_Matricula['aluno_nome']); ?>."
                          target="_blank"><?php echo $row_Matricula['aluno_emergencia_tel2']; ?></a>
                      </p>
                    <?php } ?>


                    <br>



                    </p>

                    <p>
                      <?php //echo "<strong>ENDEREÇO:</strong> <a href=\"{$maps}\" target=\"_blank\">VER NO MAPA</a>"; ?>

                      <a class="ls-ico-pencil ls-btn-primary ls-btn-xs"
                        href="alunoEditar.php?hash=<?php echo $row_Matricula['aluno_hash']; ?>&cmatricula=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>">EDITAR
                        DADOS</a>
                    </p>

                    </p>
                  </div>

                  <div id="tab3" class="ls-tab-content">




                    <div class="ls-tabs-btn">
                      <ul class="ls-tabs-btn-nav">
                        <!-- Documentos de Matrícula -->
                        <li class="col-md-12">
                          <h4 class="report-category"><i class="fas fa-file-alt"></i> Documentos de Matrícula</h4>
                        </li>
                        <li class="col-md-3 col-xs-6"><a class="ls-btn"
                            href="print_form_matricula.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>"
                            target="_blank"><i class="fas fa-file-signature"></i> Ficha de Matrícula</a></li>
                        <li class="col-md-3 col-xs-6"><a class="ls-btn"
                            href="print_form_matriculav2.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>"
                            target="_blank"><i class="fas fa-file-signature"></i> Ficha de Matrícula (MODELO 2)</a></li>
                        <li class="col-md-3 col-xs-6"><a class="ls-btn"
                            href="print_comprovante_matricula.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>"
                            target="_blank"><i class="fas fa-receipt"></i> Comprovante de Matrícula</a></li>
                        <?php if ($row_Matricula['ca_forma_avaliacao'] == "Q") { ?>
                          <li class="col-md-3 col-xs-6"><a class="ls-btn"
                              href="termo_compromisso_matricula_MN.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>"
                              target="_blank"><i class="fas fa-file-contract"></i> Termo de compromisso de matrícula</a>
                          </li>
                        <?php } ?>

                        <!-- Declarações -->
                        <li class="col-md-12">
                          <h4 class="report-category"><i class="fas fa-certificate"></i> Declarações</h4>
                        </li>
                        <li class="col-md-3 col-xs-6"><a class="ls-btn"
                            href="print_declaracao_matricula.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>"
                            target="_blank"><i class="fas fa-file-alt"></i> Declaração de Matrícula</a></li>
                        <li class="col-md-3 col-xs-6"><a class="ls-btn"
                            href="print_declaracao_escola_publica.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>"
                            target="_blank"><i class="fas fa-school"></i> Declaração / Escola Pública</a></li>
                        <li class="col-md-3 col-xs-6"><a class="ls-btn"
                            href="print_declaracao_transferencia_conservado.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>"
                            target="_blank"><i class="fas fa-user-clock"></i> Declaração aluno conservado</a></li>
                        <li class="col-md-3 col-xs-6"><a class="ls-btn"
                            href="print_declaracao_transferencia.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>"
                            target="_blank"><i class="fas fa-graduation-cap"></i> Conclusão de Curso</a></li>

                        <!-- Bolsa Família -->
                        <li class="col-md-12">
                          <h4 class="report-category"><i class="fas fa-hand-holding-usd"></i> Bolsa Família</h4>
                        </li>
                        <li class="col-md-3 col-xs-6"><a class="ls-btn"
                            href="imprimir/print_bolsa_familia.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>"
                            target="_blank"><i class="fas fa-file-invoice"></i> Dec. Bolsa Família</a></li>
                        <li class="col-md-3 col-xs-6"><a class="ls-btn"
                            href="imprimir/print_bolsa_familia_faltas.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>"
                            target="_blank"><i class="fas fa-file-invoice"></i> Dec. Bolsa Família c/faltas</a></li>
                        <li class="col-md-3 col-xs-6"><a class="ls-btn"
                            href="imprimir/print_bolsa_familia_faltas_dias.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>"
                            target="_blank"><i class="fas fa-file-invoice"></i> Dec. Bolsa Família c/faltas (dias)</a>
                        </li>

                        <!-- Transferências -->
                        <li class="col-md-12">
                          <h4 class="report-category"><i class="fas fa-exchange-alt"></i> Transferências</h4>
                        </li>
                        <li class="col-md-3 col-xs-6"><a class="ls-btn"
                            href="print_declaracao_transferencia.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>"
                            target="_blank"><i class="fas fa-file-export"></i> Transferência (aprovado)</a></li>
                        <li class="col-md-3 col-xs-6"><a class="ls-btn"
                            href="print_declaracao_transferencia_desistente.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>"
                            target="_blank"><i class="fas fa-file-export"></i> Transferência (desistente)</a></li>
                        <li class="col-md-3 col-xs-6"><a class="ls-btn"
                            href="print_declaracao_transferencia_em_curso.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>"
                            target="_blank"><i class="fas fa-file-export"></i> Declaração Transf. em Curso</a></li>
                        <li class="col-md-3 col-xs-6"><a class="ls-btn"
                            href="print_dec_trans_curso_notas.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>"
                            target="_blank"><i class="fas fa-file-export"></i> Transf. em Curso (notas)</a></li>
                        <li class="col-md-3 col-xs-6"><a class="ls-btn"
                            href="print_dec_trans_curso_conceitos.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>"
                            target="_blank"><i class="fas fa-file-export"></i> Transf. em Curso (conceitos)</a></li>

                        <!-- Avaliações e Frequência -->
                        <li class="col-md-12">
                          <h4 class="report-category"><i class="fas fa-chart-bar"></i> Avaliações e Frequência</h4>
                        </li>
                        <?php if ($row_Matricula['ca_forma_avaliacao'] == "Q") { ?>
                          <li class="col-md-3 col-xs-6"><a class="ls-btn"
                              href="fichaIndividualAlunoMN.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>"
                              target="_blank"><i class="fas fa-file-contract"></i> Ficha Individual</a></li>
                        <?php } else { ?>
                          <li class="col-md-3 col-xs-6"><a class="ls-btn"
                              href="fichaIndividualAluno.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>"
                              target="_blank"><i class="fas fa-file-contract"></i> Ficha Individual</a></li>
                        <?php } ?>
                        <li class="col-md-3 col-xs-6"><a class="ls-btn"
                            href="print_parecer_aluno.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>"
                            target="_blank"><i class="fas fa-comment-alt"></i> Parecer do aluno</a></li>
                        <li class="col-md-3 col-xs-6"><a class="ls-btn"
                            href="diario_frequencia_individual.php?ct=<?php echo $row_Matricula['turma_id']; ?>&ano=<?php echo $row_Matricula['vinculo_aluno_ano_letivo']; ?>&hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>"
                            target="_blank"><i class="fas fa-calendar-check"></i> Frequência Individual</a></li>
                        <li class="col-md-3 col-xs-6"><a class="ls-btn"
                            href="boletimVerImprimir.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>"
                            target="_blank"><i class="fas fa-file-pdf"></i> Boletim individual</a></li>

                        <!-- CIPTA (se aplicável) -->
                        <?php if ($row_Matricula['aluno_laudo'] == 1) { ?>
                          <li class="col-md-12">
                            <h4 class="report-category"><i class="fas fa-id-card"></i> CIPTA</h4>
                          </li>
                          <li class="col-md-3 col-xs-6"><a class="ls-btn"
                              href="carteirinha_deficiencia_individual.php?hash=<?php echo $row_Matricula['aluno_hash']; ?>"
                              target="_blank"><i class="fas fa-id-card"></i> CIPTA (modelo 1)</a></li>
                          <li class="col-md-3 col-xs-6"><a class="ls-btn"
                              href="carteirinha_deficiencia_individual_2.php?hash=<?php echo $row_Matricula['aluno_hash']; ?>"
                              target="_blank"><i class="fas fa-id-card"></i> CIPTA (modelo 2)</a></li>
                          <li class="col-md-3 col-xs-6"><a class="ls-btn"
                              href="carteirinha_deficiencia_individual_3.php?hash=<?php echo $row_Matricula['aluno_hash']; ?>"
                              target="_blank"><i class="fas fa-id-card"></i> CIPTA (modelo 3)</a></li>
                        <?php } ?>

                        <!-- Educação Infantil (se aplicável) -->
                        <?php if ($row_Matricula['etapa_id_filtro'] == 1) { ?>
                          <li class="col-md-12">
                            <h4 class="report-category"><i class="fas fa-baby"></i> Educação Infantil</h4>
                          </li>
                          <li class="col-md-3 col-xs-6"><a class="ls-btn"
                              href="imprimir/print_declaracao_transferencia_ei.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>"
                              target="_blank"><i class="fas fa-file-export"></i> Decl. Trans. (Ed. Infantil)</a></li>
                        <?php } ?>
                      </ul>
                    </div>


                  </div>
                  <div id="tab4" class="ls-tab-content">
                    <p>



                    <div class="ls-box">
                      <h3 class="ls-title-5"><strong>Dados de acesso ao painel do aluno</strong></h3>
                      Nascimento: <strong><?php echo inverteData($row_Matricula['aluno_nascimento']); ?></strong><br>
                      Código:
                      <strong><?php echo str_pad($row_Matricula['aluno_id'], 5, '0', STR_PAD_LEFT); ?></strong><br>
                      Senha: <strong><?php echo substr($row_Matricula['aluno_hash'], 0, 5); ?></strong>
                    </div>

                    <p>Total de acessos: <strong><?php echo $totalRows_LogsAcesso; ?></strong></p>
                    <p>Último Acesso:
                      <strong><?php echo date("d/m/Y à\s H:i", strtotime($row_LogsAcesso['login_aluno_data_hora'])); ?></strong>
                    </p>


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
                            <td class="ls-txt-center">
                              <?php echo str_pad($row_VinculosAnteriores['vinculo_aluno_id'], 5, '0', STR_PAD_LEFT); ?>
                            </td>
                            <td class="ls-txt-center"><?php echo $row_VinculosAnteriores['vinculo_aluno_ano_letivo']; ?>
                            </td>
                            <td class="ls-txt-center"><?php echo $row_VinculosAnteriores['turma_nome']; ?></td>
                            <td class="ls-txt-center"><?php echo $row_VinculosAnteriores['escola_nome']; ?></td>
                            <td class="ls-txt-center">
                              <?php if ($row_VinculosAnteriores['escola_id'] == $row_Matricula['vinculo_aluno_id_escola']) { ?><a
                                  href="matriculaExibe.php?cmatricula=<?php echo $row_VinculosAnteriores['vinculo_aluno_hash']; ?>">VER</a><?php } ?>
                            </td>

                          </tr>
                        <?php } while ($row_VinculosAnteriores = mysql_fetch_assoc($VinculosAnteriores)); ?>
                      </tbody>
                    </table>
                    </p>
                  </div>

                  <div id="tab8" class="ls-tab-content">
                    <p>
                    <h3 class="ls-title-5"><strong>Documentos do Aluno</strong></h3>
                    <button class="ls-btn ls-btn-primary" onclick="abrirModal()">➕ Adicionar Documento</button>

                    <!-- Modal de Upload -->
                    <div id="modal-documento" class="ls-modal" style="display: none;">
                      <div class="ls-modal-box">
                        <div class="ls-modal-header">
                          <button data-dismiss="modal" onclick="fecharModal()">&times;</button>
                          <h4 class="ls-modal-title">Adicionar Documento</h4>
                        </div>
                        <div class="ls-modal-body">
                          <div class="form-container">
                            <h3>📄 Enviar Documento</h3>
                            <form id="form-documento" name="form1" action="<?php echo $editFormAction; ?>" method="POST"
                              enctype="multipart/form-data">
                              <label>📌 Selecione o Tipo de Documento:</label>
                              <select name="tipo_documento" id="tipo_documento" required>
                                <option value="">Escolha...</option>
                                <option value="Certidão de Nascimento">Certidão de Nascimento</option>
                                <option value="RG">RG (Registro Geral)</option>
                                <option value="CPF">CPF</option>
                                <option value="Título de Eleitor">Título de Eleitor</option>
                                <option value="CNH">CNH</option>
                                <option value="Passaporte">Passaporte</option>
                                <option value="CTPS">Carteira de Trabalho</option>
                                <option value="Reservista">Certificado de Reservista</option>
                                <option value="Casamento">Certidão de Casamento</option>
                                <option value="Histórico Escolar">Histórico Escolar</option>
                                <option value="Outros">Outros</option>
                              </select>

                              <label>📂 Escolha um Arquivo:</label>
                              <input type="file" id="aluno_documento" name="aluno_documento"
                                accept="image/*,application/pdf">


                              <!-- Botão de envio -->
                              <input type="hidden" name="MM_update" value="form1">
                              <button type="submit" class="btn btn-primary">📤 Enviar Documento</button>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>

                    <?php if ($totalRows_Documentos > 0) { ?>
                      <table class="ls-table ls-sm-space" width="100%">
                        <thead>
                          <tr>
                            <th width="50" class="ls-txt-center">#</th>
                            <th width="200" class="ls-txt-center">Nome do Arquivo</th>
                            <th width="150" class="ls-txt-center">Tipo de documento</th>
                            <th width="150" class="ls-txt-center">Data de Upload</th>
                            <th width="150" class="ls-txt-center">Ação</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php do { ?>
                            <tr id="doc-<?php echo $row_Documentos['id']; ?>">
                              <td class="ls-txt-center"><?php echo $row_Documentos['id']; ?></td>
                              <td class="ls-txt-center"><?php echo $row_Documentos['nome_original']; ?></td>
                              <td class="ls-txt-center"><?php echo strtoupper($row_Documentos['tipo_documento']); ?></td>
                              <td class="ls-txt-center">
                                <?php echo date("d/m/Y H:i", strtotime($row_Documentos['uploaded_at'])); ?>
                              </td>
                              <td class="ls-txt-center">
                                <a href="aluno/documentos/<?php echo $row_Documentos['arquivo_path']; ?>" target="_blank"
                                  class="ls-btn ls-btn-sm ls-btn-primary">
                                  📥 Baixar
                                </a>
                                <button class="ls-ico-close ls-btn ls-btn-sm ls-btn-danger excluir-doc"
                                  data-id="<?php echo $row_Documentos['id']; ?>">
                                  Excluir
                                </button>
                              </td>
                            </tr>
                          <?php } while ($row_Documentos = mysql_fetch_assoc($Documentos)); ?>
                        </tbody>
                      </table>

                    <?php } else { ?>
                      <p class="ls-txt-center"><strong>Nenhum documento encontrado.</strong></p>
                    <?php } ?>

                    </p>
                  </div>


                  <div id="tab7" class="ls-tab-content">

                    <p>
                      <?php if ($row_Matricula['aluno_aceite_termos'] == "S") {
                        echo "<span class=\"ls-ico-checkmark-circle ls-ico-left ls-color-success\"></span> Aceitou os termos para participar do EDUCCONECT";
                      } else {
                        echo "<span class=\"ls-ico-info ls-ico-left ls-color-warning\"></span> Ainda não aceitou os termos para participar do EDUCCONECT";
                      } ?>

                      <?php if ($row_Matricula['aluno_aceite_termos'] == "S") { ?>


                      <table>
                        <tr>
                          <td width="15%">

                            <?php
                            if (!empty($row_Matricula['aluno_foto2'])) { ?>
                              <img src="<?php echo URL_BASE . '../../aluno/fotos2/' . $row_Matricula['aluno_foto2']; ?>"
                                width="90%" class="hoverable">
                            <?php } elseif (!empty($row_Matricula['aluno_foto'])) { ?>
                              <img src="<?php echo URL_BASE . '../../aluno/fotos/' . $row_Matricula['aluno_foto']; ?>"
                                width="90%" class="hoverable">
                            <?php } else { ?>
                              <img src="<?php echo URL_BASE . '../../aluno/fotos2/semfoto.jpg'; ?>" width="100%"
                                class="hoverable">
                            <?php } ?>
                          </td>
                          <td>

                            Bio: <?php echo $row_Matricula['aluno_bio']; ?><br>

                            <?php if ($row_Matricula['aluno_face'] != '') { ?>
                              Facebook: <a href="https://www.facebook.com/<?= $row_Matricula['aluno_face'] ?>"
                                target="_blank">
                                <svg style="margin-right:10px ;" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
                                  width="25" height="25" viewBox="0 0 50 50">
                                  <path
                                    d="M25,3C12.85,3,3,12.85,3,25c0,11.03,8.125,20.137,18.712,21.728V30.831h-5.443v-5.783h5.443v-3.848 c0-6.371,3.104-9.168,8.399-9.168c2.536,0,3.877,0.188,4.512,0.274v5.048h-3.612c-2.248,0-3.033,2.131-3.033,4.533v3.161h6.588 l-0.894,5.783h-5.694v15.944C38.716,45.318,47,36.137,47,25C47,12.85,37.15,3,25,3z">
                                  </path>
                                </svg>
                              </a><br>
                            <?php } ?>

                            <?php if ($row_Matricula['aluno_insta'] != '') { ?>
                              Instagram: <a href="https://www.instagram.com/<?= $row_Matricula['aluno_insta'] ?>"
                                target="_blank">
                                <svg style="margin-right:10px ;" xmlns="http://www.w3.org/2000/svg" width="25" height="25"
                                  viewBox="0 0 24 24">
                                  <path
                                    d="M 8 3 C 5.243 3 3 5.243 3 8 L 3 16 C 3 18.757 5.243 21 8 21 L 16 21 C 18.757 21 21 18.757 21 16 L 21 8 C 21 5.243 18.757 3 16 3 L 8 3 z M 8 5 L 16 5 C 17.654 5 19 6.346 19 8 L 19 16 C 19 17.654 17.654 19 16 19 L 8 19 C 6.346 19 5 17.654 5 16 L 5 8 C 5 6.346 6.346 5 8 5 z M 17 6 A 1 1 0 0 0 16 7 A 1 1 0 0 0 17 8 A 1 1 0 0 0 18 7 A 1 1 0 0 0 17 6 z M 12 7 C 9.243 7 7 9.243 7 12 C 7 14.757 9.243 17 12 17 C 14.757 17 17 14.757 17 12 C 17 9.243 14.757 7 12 7 z M 12 9 C 13.654 9 15 10.346 15 12 C 15 13.654 13.654 15 12 15 C 10.346 15 9 13.654 9 12 C 9 10.346 10.346 9 12 9 z">
                                  </path>
                                </svg> <?= $row_Matricula['aluno_insta'] ?>
                              </a><br>
                            <?php } ?>

                            <?php if ($row_Matricula['aluno_x'] != '') { ?>
                              X: <a href="https://x.com/<?= $row_Matricula['aluno_x'] ?>" target="_blank">
                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 512 509.64">
                                  <rect width="512" height="509.64" rx="115.61" ry="115.61" />
                                  <path fill="#fff" fill-rule="nonzero"
                                    d="M323.74 148.35h36.12l-78.91 90.2 92.83 122.73h-72.69l-56.93-74.43-65.15 74.43h-36.14l84.4-96.47-89.05-116.46h74.53l51.46 68.04 59.53-68.04zm-12.68 191.31h20.02l-129.2-170.82H180.4l130.66 170.82z" />
                                </svg>
                              </a><br>
                            <?php } ?>

                          </td>

                        </tr>

                      </table>












                    <?php } ?>



                    <?php if ($totalRows_DepEnviados > 0) { ?>
                      <div data-ls-module="collapse" data-target="#0" class="ls-collapse ">
                        <a href="#" class="ls-collapse-header">
                          <h3 class="ls-collapse-title">MENSAGENS FILTRADAS (<?php echo $totalRows_DepEnviados; ?>)</h3>
                        </a>
                        <div class="ls-collapse-body" id="0">

                          <table class="ls-table">
                            <thead>
                              <tr>
                                <th>MENSAGEM</th>
                                <th>ENVIADO PARA</th>
                                <th width="200px">DATA/HORA</th>
                              </tr>
                            </thead>

                            <tbody>
                              <?php do { ?>


                                <tr>
                                  <td><span class="ls-ico-info ls-ico-left ls-color-danger"></span>
                                    <?php echo nl2br($row_DepEnviados['aluno_depoimento_flag_texto']); ?></td>
                                  <td><?php echo $row_DepEnviados['aluno_nome']; ?> | CPF:
                                    <?php echo $row_DepEnviados['aluno_cpf']; ?>
                                  </td>
                                  <td>
                                    <?php echo date("d/m - H\hi", strtotime($row_DepEnviados['aluno_depoimento_flag_data'])) ?>
                                  </td>
                                </tr>



                              <?php } while ($row_DepEnviados = mysql_fetch_assoc($DepEnviados)); ?>
                            </tbody>

                          </table>
                        </div>
                      </div>
                    <?php } ?>




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
                      <h6 class="ls-title-4 <?php if ($totalRows_Ocorrencia > 0) {
                        echo "ls-color-danger";
                      } ?>">
                        OCORRÊNCIAS</h6>
                    </div>
                    <div class="ls-box-body"> <span class="ls-board-data"> <strong class="<?php if ($totalRows_Ocorrencia > 0) {
                      echo "ls-color-danger";
                    } ?>"><?php echo $totalRows_Ocorrencia; ?>
                          <small class="<?php if ($totalRows_Ocorrencia > 0) {
                            echo "ls-color-danger";
                          } ?>">ocorrência(s)</small></strong>
                      </span> </div>
                    <div class="ls-box-footer"> <a
                        href="ocorrenciaExibe.php?cmatricula=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>"
                        class="ls-btn-primary ls-btn-xs">Visualizar</a> </div>
                  </div>
                </div>



                <?php

                $perfrequencia = number_format((($totalRows_FaltasAulas / $totalRows_AulasTurma) * 100), 0);

                if ($perfrequencia > 100) {

                  $perfrequencia = 100;

                }

                ?>

                <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
                <script type="text/javascript">
                  google.charts.load('current', { 'packages': ['gauge'] });
                  google.charts.setOnLoadCallback(drawChart);

                  function drawChart() {

                    var data = google.visualization.arrayToDataTable([
                      ['Label', 'Value'],
                      [' ', 0]
                    ]);

                    var options = {
                      width: 120, height: 100,
                      redFrom: 75, redTo: 100,
                      yellowFrom: 50, yellowTo: 75,
                      greenFrom: 0, greenTo: 25,
                      minorTicks: 5,
                      animation: {
                        duration: 5000,
                        easing: 'out'
                      }
                    };

                    var chart = new google.visualization.Gauge(document.getElementById('chart_div'));

                    chart.draw(data, options);



                    setInterval(function () {
                      data.setValue(0, 1, <?php echo $perfrequencia; ?>);
                      chart.draw(data, options);
                    }, 1000);



                  }
                </script>

                <div class="col-sm-6 col-lg-3">
                  <div class="ls-box">
                    <div class="ls-box-head">
                      <h6 class="ls-title-4">PERCENTUAL DE FREQUÊNCIA<a href="#" class="ls-ico-help ls-float-right"
                          data-trigger="hover" data-ls-module="popover" data-placement="left"
                          data-content="Número de faltas sem justificativa (<?php echo $totalRows_FaltasAulas; ?>) pelo número de aulas registradas até o momento (<?php echo $totalRows_AulasTurma; ?>)"
                          data-title="PERCENTUAL DE FALTAS"></a> </h6>
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
                      <h6 class="ls-title-4 <?php if ($totalRows_FaltasAulas > 0) {
                        echo "ls-color-danger";
                      } ?>">FALTAS
                      </h6>
                    </div>
                    <div class="ls-box-body">


                      <strong class="<?php if ($totalRows_FaltasAulas > 0) {
                        echo "ls-color-danger";
                      } ?>"><?php echo $totalRows_FaltasAulas ?>
                        <small class="<?php if ($totalRows_FaltasAulas > 0) {
                          echo "ls-color-danger";
                        } ?>"></small></strong>

                      <div data-ls-module="progressBar" role="progressbar" class="ls-animated"
                        aria-valuenow="<?php echo $perfrequencia; ?>"></div>

                      <span class="ls-board-data">

                      </span>
                    </div>
                    <div class="ls-box-footer"> <a
                        href="faltasMostrar.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>"
                        class="ls-btn-primary ls-btn-xs">Visualizar</a> </div>
                  </div>
                </div>
                <div class="col-sm-12 col-md-4">
                  <div class="ls-box">
                    <div class="ls-box-head">
                      <h6 class="ls-title-4">RENDIMENTO</h6>
                    </div>
                    <?php if ($row_Matricula['vinculo_aluno_boletim'] == 0) { ?>
                      <div class="ls-box-body"> <span class="ls-board-data ls-transparent-25"> <strong><span
                              class="ls-ico-cancel-circle"></span> <small>boletim não gerado</small></strong> </span>
                      </div>
                      <div class="ls-box-footer">
                        <?php if ($row_Matricula['etapa_id_filtro'] == 1) { ?>
                          <a href="boletimCadastrarConceitos.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>"
                            class="ls-btn-primary ls-btn-xs">Gerar qustionário - EI</a>
                        <?php } else { ?>

                          <?php if ($row_Matricula['ca_questionario_conceitos'] == "S") { ?>

                            <a href="boletimCadastrarConceitosEf.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>"
                              class="ls-btn-primary ls-btn-xs">Gerar questionário - EF</a>

                          <?php } else { ?>

                            <a href="boletimCadastrarDisciplinas.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>"
                              class="ls-btn-primary ls-btn-xs">Gerar boletim</a>

                          <?php } ?>


                        <?php } ?>
                      </div>
                    <?php } ?>
                    <?php if ($row_Matricula['vinculo_aluno_boletim'] == 1) { ?>


                      <?php if ($row_Matricula['etapa_id_filtro'] == 1) { ?>
                        <div class="ls-box-body"> <span class="ls-board-data"> <strong><span
                                class="ls-ico-checkmark-circle ls-color-success"></span> <small
                                class="ls-color-success">relatório gerado</small></strong> </span> </div>
                        <div class="ls-box-footer">
                          <div class="ls-display-flex ls-flex-column">
                            <a href="conceitoVer.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>"
                              class="ls-btn-primary ls-btn-xs mb-2">Visualizar</a>

                          </div>

                        </div>
                      <?php } else { ?>
                        <div class="ls-box-body"> <span class="ls-board-data"> <strong><span
                                class="ls-ico-checkmark-circle ls-color-success"></span> <small
                                class="ls-color-success">boletim gerado</small></strong> </span> </div>
                        <div class="ls-box-footer">






                          <?php if ($row_Matricula['ca_questionario_conceitos'] == "S") { ?>

                            <a href="conceitoEfVer.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>"
                              class="ls-btn-primary ls-btn-xs">Visualizar Questionário - EF</a>
                          <?php } else { ?>

                            <?php if ($row_Matricula['ca_forma_avaliacao'] == "N") { ?>
                              <a href="boletimVer.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>"
                                class="ls-btn-primary ls-btn-xs">Visualizar</a>

                            <?php } ?>

                            <?php if ($row_Matricula['ca_forma_avaliacao'] == "Q") { ?>
                              <a href="boletimVerQQ.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>"
                                class="ls-btn-primary ls-btn-xs">Visualizar</a>
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
  <?php include_once("menu-dir.php"); ?>

  <!-- We recommended use jQuery 1.10 or up -->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="js/locastyle.js"></script>
  <?php if (isset($_GET['cadastrado'])) { ?>
    <script>
      locastyle.modal.open("#confirmarInformacoes");
    </script>
  <?php } ?>

  <script>

    function abrirModal() {
      document.getElementById("modal-documento").style.display = "block";
      document.querySelector(".ls-tabs-nav").style.display = "none";
    }

    function fecharModal() {
      document.getElementById("modal-documento").style.display = "none";
      document.querySelector(".ls-tabs-nav").style.display = "";

    }
  </script>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      // Adiciona evento de clique para todos os botões de excluir
      document.querySelectorAll(".excluir-doc").forEach(button => {
        button.addEventListener("click", function () {
          let docId = this.getAttribute("data-id");

          if (confirm("Tem certeza que deseja excluir este documento?")) {
            // Faz a requisição AJAX para excluir
            let formData = new FormData();
            formData.append("id", docId);

            fetch("fnc/excluirDocumento.php", {
              method: "POST",
              body: formData
            })
              .then(response => response.json())
              .then(data => {
                if (data.success) {
                  // Remove a linha da tabela se o documento for excluído
                  document.getElementById("doc-" + docId).remove();
                  alert("Documento excluído com sucesso!");
                } else {
                  alert("Erro ao excluir documento: " + data.error);
                }
              })
              .catch(error => console.error("Erro na requisição:", error));
          }
        });
      });
    });
  </script>

  <script>
    $(document).ready(function () {
      $('#resetarButtonConceito').on('click', function (e) {
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

      $('#resetarButtonNotas').on('click', function (e) {
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

mysql_free_result($Documentos);

mysql_free_result($AulasTurma);

?>