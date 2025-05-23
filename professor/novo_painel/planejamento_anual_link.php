<?php require_once('../../Connections/SmecelNovoPDO.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
<?php
$colname_PlanejamentoAnual = "-1";
if (isset($_GET['plano'])) {
  $colname_PlanejamentoAnual = $_GET['plano'];
}

// Alterando para uso de PDO
$query_PlanejamentoAnual = "SELECT plano_anual_id, plano_anual_id_prof, plano_anual_id_etapa, plano_anual_id_componente, plano_anual_id_escola, plano_anual_ano, plano_anual_texto, plano_anual_link, plano_anual_hash FROM smc_plano_anual WHERE plano_anual_hash = :plano_anual_hash";
$stmt = $SmecelNovo->prepare($query_PlanejamentoAnual);
$stmt->bindParam(':plano_anual_hash', $colname_PlanejamentoAnual, PDO::PARAM_STR);
$stmt->execute();
$row_PlanejamentoAnual = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_PlanejamentoAnual = $stmt->rowCount();

if ($totalRows_PlanejamentoAnual == 0) {
  header("Location: planejamento_anual.php?erro");
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

  // Carregando a classe de upload
  include('../../sistema/funcoes/class.upload.php');

  // Instanciando a classe Upload
  $handle = new Upload($_FILES['plano_anual_link']);
  $nome = md5(date('YmdHis'));

  if ($handle->uploaded) {
    $handle->mime_check = true;
    $handle->file_max_size = '128M'; // Definindo o tamanho máximo de upload
    $handle->file_new_name_body = $nome;
    $handle->Process('../plano_anual/');

    if ($handle->processed) {
      $nome_da_imagem = $handle->file_dst_name;

      // Atualizando o banco de dados com o nome do arquivo
      $updateSQL = "UPDATE smc_plano_anual SET plano_anual_link = :plano_anual_link WHERE plano_anual_hash = :plano_anual_hash";

      // Preparar e executar a consulta de atualização
      $stmt = $SmecelNovo->prepare($updateSQL);
      $stmt->bindParam(':plano_anual_link', $nome_da_imagem, PDO::PARAM_STR);
      $stmt->bindParam(':plano_anual_hash', $_POST['plano_anual_hash'], PDO::PARAM_STR);
      $stmt->execute();

      // Redirecionar após a atualização
      $updateGoTo = "planejamento_anual.php";
      header(sprintf("Location: %s", $updateGoTo));
    }
  }
}
?>

<!DOCTYPE html>
<html class="<?php echo TEMA; ?>" lang="pt-br">

<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    gtag('js', new Date());

    gtag('config', 'UA-117872281-1');
  </script>
  <title>PROFESSOR | <?php echo $row_ProfLogado['func_nome']; ?> | SMECEL - Sistema de Gestão Escolar</title>
  <meta charset="utf-8">
  <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
  <link rel="stylesheet" type="text/css" href="css/locastyle.css">
  <link rel="stylesheet" href="css/sweetalert2.min.css">

  <style>
    /* Estilizando o campo de upload de arquivo */
    .custom-file-input {
      display: none;
      /* Esconde o campo original de input de arquivo */
    }

    .ls-label {
      display: flex;
      flex-direction: column;
      margin-bottom: 15px;
    }

    .ls-label-text {
      font-weight: bold;
      font-size: 14px;
      color: #333;
      margin-bottom: 5px;
    }

    /* Estilo do botão personalizado */
    .ls-label::before {
      content: 'Escolher arquivo';
      display: inline-block;
      background-color: #4CAF50;
      /* Cor de fundo do botão */
      color: white;
      padding: 10px 20px;
      border-radius: 5px;
      cursor: pointer;
      font-size: 14px;
      text-align: center;
      box-sizing: border-box;
    }

    .ls-label:hover::before {
      background-color: #45a049;
      /* Cor de fundo ao passar o mouse */
    }

    /* Exibindo o nome do arquivo selecionado */
    .file-name {
      display: block;
      margin-top: 10px;
      font-size: 13px;
      color: #555;
    }

    /* Estilo de borda para o campo */
    .ls-label input[type="file"] {
      border: 2px dashed #ccc;
      padding: 10px;
      width: 100%;
      margin-top: 10px;
      font-size: 14px;
    }

    /* Efeito quando o campo está focado */
    .ls-label input[type="file"]:focus {
      outline: none;
      border-color: #4CAF50;
    }

    /* Adicionando uma pequena animação ao hover no botão */
    .ls-label::before {
      transition: background-color 0.3s ease;
    }

    /* Efeito de destaque ao passar o mouse sobre o botão */
    .ls-label:hover::before {
      transform: scale(1.05);
    }
  </style>
</head>

<body>
  <?php include_once "inc/navebar.php"; ?>
  <?php include_once "inc/sidebar.php"; ?>
  <main class="ls-main">
    <div class="container-fluid">
      <h1 class="ls-title-intro ls-ico-home">Ano letivo <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h1>
      <p><a href="planejamento_anual.php" class="ls-btn ls-ico-chevron-left">Voltar</a></p>
      <hr>
      <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form"
        enctype="multipart/form-data">
        <fieldset>
          <label class="ls-label">
            <b class="ls-label-text">ARQUIVO</b>
            <input type="file" name="plano_anual_link" id="fileInput" class="custom-file-input" required>
            <span id="fileName" class="file-name"></span> <!-- Exibe o nome do arquivo selecionado -->
          </label>
        </fieldset>


        <div class="ls-actions-btn">
          <input type="submit" value="SALVAR" class="ls-btn-primary">
        </div>


        <input type="hidden" name="MM_update" value="form1">
        <input type="hidden" name="plano_anual_hash" value="<?php echo $row_PlanejamentoAnual['plano_anual_hash']; ?>">
      </form>

      <p>&nbsp;</p>
    </div>
    <?php //include_once "inc/footer.php"; ?>
  </main>
  <?php include_once "inc/notificacoes.php"; ?>
  <!-- We recommended use jQuery 1.10 or up -->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="js/locastyle.js"></script>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="js/sweetalert2.min.js"></script>
  <script>
    document.getElementById('fileInput').addEventListener('change', function () {
      var fileName = this.files.length > 0 ? this.files[0].name : 'Nenhum arquivo selecionado';
      document.getElementById('fileName').textContent = fileName;
    });

  </script>
  <script type="application/javascript">
    /*
    Swal.fire({
      //position: 'top-end',
      icon: 'success',
      title: 'Tudo certo por aqui',
      showConfirmButton: false,
      timer: 1500
    })
    */
  </script>



</body>

</html>