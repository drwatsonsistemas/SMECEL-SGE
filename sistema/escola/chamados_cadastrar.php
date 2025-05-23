<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<?php require_once('../../Connections/SmecelNovo.php'); ?>
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

$error_message = "";
$success_message = "";

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  if ($row_UsuLogado['usu_insert'] == "N") {
    header(sprintf("Location: chamados.php?permissao"));
    die();
  }

  $time = time();

  // Processar o upload do arquivo
  $upload_dir = realpath(dirname(__FILE__) . "/../..") . "/chamados_anexo/"; // 

  // Caminho ajustado para a raiz do projeto
  $file_names = [];

  if (isset($_FILES['chamado_imagem']) && !empty($_FILES['chamado_imagem']['name'][0])) {
    $file_count = count($_FILES['chamado_imagem']['name']);
    if ($file_count > 8) {
      $error_message = "Você pode enviar no máximo 8 arquivos.";
    } else {
      for ($i = 0; $i < $file_count; $i++) {
        if ($_FILES['chamado_imagem']['error'][$i] == 0) {
          $file_tmp = $_FILES['chamado_imagem']['tmp_name'][$i];
          $file_ext = strtolower(pathinfo($_FILES['chamado_imagem']['name'][$i], PATHINFO_EXTENSION));
          $file_name = $time . "_" . rand(1000, 9999) . "_$i." . $file_ext;
          $file_destination = $upload_dir . $file_name;

          if (!file_exists($upload_dir)) {
            if (!mkdir($upload_dir, 0777, true)) {
              $error_message = "Falha ao criar o diretório de upload.";
              break;
            }
          }

          if (!is_writable($upload_dir)) {
            $error_message = "Diretório de upload não é gravável.";
            break;
          }

          if (move_uploaded_file($file_tmp, $file_destination)) {
            $file_names[] = $file_name;
          } else {
            $error_message = "Falha ao mover o arquivo: " . $_FILES['chamado_imagem']['name'][$i];
            break;
          }
        } else {
          $error_message = "Erro no upload do arquivo: " . $_FILES['chamado_imagem']['name'][$i];
          break;
        }
      }
    }
  }

  if (empty($error_message)) {
    $file_names_json = json_encode($file_names);
    $insertSQL = sprintf(
      "INSERT INTO smc_chamados (chamado_id_sec, chamado_id_escola, chamado_id_usuario, chamado_id_telefone, chamado_data_abertura, chamado_categoria, chamado_situacao, chamado_titulo, chamado_texto, chamado_imagem, chamado_visualizado, chamado_numero) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, '$time')",
      GetSQLValueString($_POST['chamado_id_sec'], "int"),
      GetSQLValueString($_POST['chamado_id_escola'], "int"),
      GetSQLValueString($_POST['chamado_id_usuario'], "int"),
      GetSQLValueString($_POST['chamado_id_telefone'], "text"),
      GetSQLValueString($_POST['chamado_data_abertura'], "date"),
      GetSQLValueString($_POST['chamado_categoria'], "text"),
      GetSQLValueString($_POST['chamado_situacao'], "text"),
      GetSQLValueString($_POST['chamado_titulo'], "text"),
      GetSQLValueString($_POST['chamado_texto'], "text"),
      GetSQLValueString($file_names_json, "text"),
      GetSQLValueString($_POST['chamado_visualizado'], "text")
    );

    mysql_select_db($database_SmecelNovo, $SmecelNovo);
    $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

    $insertGoTo = "chamados.php?cadastrado";
    if (isset($_SERVER['QUERY_STRING'])) {
      $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
      $insertGoTo .= $_SERVER['QUERY_STRING'];
    }
    header(sprintf("Location: %s", $insertGoTo));
    exit;
  }
}

// Consulta escola logada
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
?>

<!DOCTYPE html>
<html class="<?php echo $row_EscolaLogada['escola_tema']; ?>" lang="pt-br">

<head>
  <title>SMECEL - Sistema de Gestão Escolar</title>
  <meta charset="utf-8">
  <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
  <link rel="stylesheet" type="text/css" href="css/locastyle.css">
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>

<body>
  <?php include_once("menu-top.php"); ?>
  <?php include_once("menu-esc.php"); ?>
  <main class="ls-main">
    <div class="container-fluid">
      <h1 class="ls-title-intro ls-ico-home">ABRIR CHAMADO</h1>

      <?php if ($success_message) { ?>
        <div class="ls-alert-success"><?php echo $success_message; ?></div>
      <?php } ?>
      <?php if ($error_message) { ?>
        <div class="ls-alert-danger"><?php echo $error_message; ?></div>
      <?php } ?>

      <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form ls-form-horizontal"
        enctype="multipart/form-data">
        <label class="ls-label col-md-6" required>
          <b class="ls-label-text">SOLICITANTE</b>
          <input type="text" name="solicitante" value="<?php echo $row_UsuLogado['usu_nome']; ?>" size="32" disabled>
        </label>
        <label class="ls-label col-md-6" required>
          <b class="ls-label-text">E-MAIL</b>
          <input type="text" name="email" value="<?php echo $row_UsuLogado['usu_email']; ?>" size="32" disabled>
        </label>
        <label
          class="ls-label col-md-6 <?php echo $error_message && strpos($error_message, 'telefone') !== false ? 'ls-error' : ''; ?>"
          required>
          <b class="ls-label-text">TELEFONE/CELULAR</b>
          <input type="text" name="chamado_id_telefone" value="" size="32" class="celular" required>
          <?php if ($error_message && strpos($error_message, 'telefone') !== false) { ?>
            <small class="ls-help-message"><?php echo $error_message; ?></small>
          <?php } ?>
        </label>
        <label
          class="ls-label col-md-6 <?php echo $error_message && strpos($error_message, 'categoria') !== false ? 'ls-error' : ''; ?>">
          <b class="ls-label-text">TIPO DE CHAMADO</b>
          <div class="ls-custom-select">
            <select name="chamado_categoria" class="ls-select" required>
              <option value="">-</option>
              <option value="DÚVIDA">DÚVIDA</option>
              <option value="SUPORTE">SUPORTE</option>
              <option value="SUGESTÃO">SUGESTÃO</option>
              <option value="ELOGIO">ELOGIO</option>
              <option value="CRÍTICA">CRÍTICA</option>
              <option value="OUTROS">OUTROS</option>
            </select>
          </div>
          <?php if ($error_message && strpos($error_message, 'categoria') !== false) { ?>
            <small class="ls-help-message"><?php echo $error_message; ?></small>
          <?php } ?>
        </label>
        <label
          class="ls-label col-md-12 <?php echo $error_message && strpos($error_message, 'titulo') !== false ? 'ls-error' : ''; ?>">
          <b class="ls-label-text">TÍTULO/ASSUNTO</b>
          <input type="text" name="chamado_titulo" value="" size="32" required>
          <?php if ($error_message && strpos($error_message, 'titulo') !== false) { ?>
            <small class="ls-help-message"><?php echo $error_message; ?></small>
          <?php } ?>
        </label>
        <label
          class="ls-label col-md-12 <?php echo $error_message && strpos($error_message, 'texto') !== false ? 'ls-error' : ''; ?>">
          <b class="ls-label-text">TEXTO DETALHADO</b>
          <textarea name="chamado_texto" id="" cols="50" rows="6" required></textarea>
          <?php if ($error_message && strpos($error_message, 'texto') !== false) { ?>
            <small class="ls-help-message"><?php echo $error_message; ?></small>
          <?php } ?>
        </label>
        <label
          class="ls-label col-md-12 <?php echo $error_message && strpos($error_message, 'arquivo') !== false ? 'ls-error' : ''; ?>">
          <b class="ls-label-text">ANEXOS (MÁXIMO 8 ARQUIVOS - IMAGEM, PDF, ETC)</b>
          <input type="file" name="chamado_imagem[]" accept="image/*,.pdf" multiple />
          <small class="ls-help-message">Você pode enviar até 8 arquivos.</small>
          <?php if ($error_message && strpos($error_message, 'arquivo') !== false) { ?>
            <small class="ls-help-message ls-error"><?php echo $error_message; ?></small>
          <?php } ?>
        </label>
        <label class="ls-label col-md-12">
          <div class="ls-actions-btn">
            <input class="ls-btn" type="submit" value="REGISTRAR CHAMADO">
            <a href="chamados.php" class="ls-btn-danger">VOLTAR</a>
          </div>
        </label>
        <input type="hidden" name="chamado_id_sec" value="<?php echo $row_UsuLogado['usu_sec']; ?>">
        <input type="hidden" name="chamado_id_escola" value="<?php echo $row_UsuLogado['usu_escola']; ?>">
        <input type="hidden" name="chamado_id_usuario" value="<?php echo $row_UsuLogado['usu_id']; ?>">
        <input type="hidden" name="chamado_data_abertura" value="<?php echo date('Y-m-d'); ?>">
        <input type="hidden" name="chamado_situacao" value="A">
        <input type="hidden" name="chamado_visualizado" value="N">
        <input type="hidden" name="MM_insert" value="form1">
      </form>
      <p> </p>
    </div>
  </main>

  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="js/locastyle.js"></script>
  <script src="https://cdn.tiny.cloud/1/iq1gy8qt7s1b9bj92c2fc7whie95augu8r8kz97dgoufkli8/tinymce/5/tinymce.min.js"
    referrerpolicy="origin"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
  <script>
    // Inicialização segura do TinyMCE
    $(document).ready(function () {
      tinymce.init({
        selector: '#mytextarea',
        height: 400,
        toolbar: 'bold italic | bullist numlist | alignleft aligncenter alignright alignjustify | link h2 h3 blockquote',
        plugins: 'advlist autolink link autolink lists charmap print preview paste',
        statusbar: false,
        menubar: false,
        paste_as_text: true,
        content_css: '//www.tinymce.com/css/codepen.min.css',
        setup: function (editor) {
          editor.on('init', function () {
            console.log('TinyMCE inicializado com sucesso');
          });
          editor.on('error', function (e) {
            console.error('Erro no TinyMCE:', e);
          });
        }
      });

      $('.celular').mask('(00) 00000-0000'); // Máscara para telefone
    });
  </script>
</body>

</html>
<?php
mysql_free_result($EscolaLogada);
?>