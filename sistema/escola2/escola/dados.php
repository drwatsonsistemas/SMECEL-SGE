<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
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

?>
<?php include "fnc/session.php"; ?>
<?php


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}


if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  if($_POST['usu_senha'] != $_POST['usu_senha_confirmar'] AND $_POST['usu_senha_confirmar'] != ""){
    $updateGoTo = "dados.php?incorreta";
    if (isset($_SERVER['QUERY_STRING'])) {
      $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
      $updateGoTo .= $_SERVER['QUERY_STRING'];
    }
    header(sprintf("Location: %s", $updateGoTo));
    exit;
  }

  $updateSQL = sprintf("UPDATE smc_usu SET usu_nome=%s, usu_senha=%s, usu_contato=%s, usu_cargo=%s WHERE usu_id=%s",
   GetSQLValueString($_POST['usu_nome'], "text"),
   GetSQLValueString($_POST['usu_senha'], "text"),
   GetSQLValueString($_POST['usu_contato'], "text"),
   GetSQLValueString($_POST['usu_cargo'], "text"),
   GetSQLValueString($_POST['usu_id'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());
  
  
  $usu = $_POST['usu_id'];
  $esc = $_POST['escola_id'];
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
    '2', 
    '$_POST[usu_nome]', 
    '$dat')
    ";
    mysql_select_db($database_SmecelNovo, $SmecelNovo);
    $Result1 = mysql_query($sql, $SmecelNovo) or die(mysql_error());  


    $updateGoTo = "dados.php?atualizado";
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


  if ((isset($_POST["MM_insert2"])) && ($_POST["MM_insert2"] == "form2")) {


   if ($row_UsuLogado['usu_update']=="N") {
    header(sprintf("Location: secretaria.php?permissao"));
    break;
  }

//CADASTRO DA LOGO
  include('../funcoes/class.upload.php');

  $handle = new Upload($_FILES['usu_foto']);

  if ($handle->uploaded) 
  { 

    $handle->file_new_name_body 	 = 'fotousu_'.md5(date('YmdHis'));
    $handle->mime_check 			 = true;
    $handle->image_resize 			 = true;
    $handle->image_ratio_crop 		 = true;
    $handle->file_max_size 			 = '128M';
    $handle->image_x 				 = 360;
    $handle->image_y 				 = 360;
    $handle->Process('../../img/funcionarios/');

    if ($handle->processed) 
    {

      $nome_do_arquivo = $handle->file_dst_name;

      $insertSQL = sprintf("UPDATE smc_usu SET usu_foto='$nome_do_arquivo' WHERE usu_id='$row_UsuLogado[usu_id]'",
       GetSQLValueString($_POST['usu_id'], "int"));

      mysql_select_db($database_SmecelNovo, $SmecelNovo);
      $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

      $insertGoTo = "dados.php?foto";
      if (isset($_SERVER['QUERY_STRING'])) {
        $insertGoTo .= (strpos($insertGoTo, '?')) ? "" : "?";
        $insertGoTo .= $_SERVER['QUERY_STRING'];
      }
      header(sprintf("Location: %s", $insertGoTo));
    } 
    else 
    {
      echo '<span class="alert panel">';
      echo ' Erro ao enviar arquivo: ' . $handle->error . '';
      echo '</span>';
    }
  }

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
  <title>Atualizar Dados</title>
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
<?php include 'menu-top.php' ?>

<?php include 'menu-esc.php' ?>

  <main class="ls-main ">
    <div class="container-fluid">
      <h1 class="ls-title-intro ls-ico-home">Página inicial</h1>
      <?php if (isset($_GET["atualizado"])) { ?>
        <div class="ls-alert-success ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> Dados atualizados com sucesso. </div>
      <?php } ?>
      <?php if (isset($_GET["incorreta"])) { ?>
        <div class="ls-alert-warning ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> As senhas estão incorretas. </div>
      <?php } ?>
      <?php if (isset($_GET["preencher"])) { ?>
        <div class="ls-alert-info ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> <b>Atenção: </b> Preencha os campos de CONTATO e FUNÇÃO.</div>
      <?php } ?>
 
      



      <div class="row">

        <div class="col-md-10">
          <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form">
            <label class="ls-label col-sm-12 col-md-6"> <b class="ls-label-text">NOME</b>
              <input type="text" name="usu_nome" value="<?php echo htmlentities($row_UsuLogado['usu_nome'], ENT_COMPAT, 'utf-8'); ?>" size="32" required>
            </label>
            <label class="ls-label col-sm-12 col-md-6 <?php if(empty($row_UsuLogado['usu_contato'])) echo "ls-warning"; ?>"> <b class="ls-label-text">CONTATO</b>
              <input type="text" name="usu_contato" class="celular9" placeholder="( ) _____-____" value="<?php echo htmlentities($row_UsuLogado['usu_contato'], ENT_COMPAT, 'utf-8'); ?>" size="32" required>
              <?php if(empty($row_UsuLogado['usu_contato'])){ ?>
               <small class="ls-help-message">Preencha esse campo</small>
               <?php } ?>
            </label>
            <label class="ls-label col-sm-12 col-md-6 <?php if(empty($row_UsuLogado['usu_cargo'])) echo "ls-warning"; ?>"> <b class="ls-label-text">FUNÇÃO</b>
              <div class="ls-custom-select">
                <select class="ls-select" name="usu_cargo" required>
                  <option value="" <?php if(empty($row_UsuLogado['usu_cargo'])){echo "SELECTED"; } ?>>Escolha...</option>
                  <option value="1" <?php if (!(strcmp(1, htmlentities($row_UsuLogado['usu_cargo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>DIRETOR</option>
                  <option value="2" <?php if (!(strcmp(2, htmlentities($row_UsuLogado['usu_cargo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>VICE-DIRETOR</option>
                  <option value="3" <?php if (!(strcmp(3, htmlentities($row_UsuLogado['usu_cargo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>COORDENADOR PEDAGÓGICO</option>
                  <option value="4" <?php if (!(strcmp(4, htmlentities($row_UsuLogado['usu_cargo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>SECRETÁRIO ESCOLAR</option>
                  <option value="5" <?php if (!(strcmp(5, htmlentities($row_UsuLogado['usu_cargo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>AUXILIAR DE SECRETARIA</option>
                  <option value="6" <?php if (!(strcmp(6, htmlentities($row_UsuLogado['usu_cargo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>GESTOR DE MATRÍCULAS</option>
                  <option value="7" <?php if (!(strcmp(7, htmlentities($row_UsuLogado['usu_cargo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>OUTROS</option>
                </select>
              </div>
              <?php if(empty($row_UsuLogado['usu_cargo'])){ ?>
               <small class="ls-help-message">Preencha esse campo</small>
               <?php } ?>
            </label>

            <label class="ls-label col-sm-12 col-md-6">
              <b class="ls-label-text">SENHA</b>
              <div class="ls-prefix-group">
                <input type="password" name="usu_senha" id="password_field" value="<?php echo $row_UsuLogado['usu_senha']; ?>" size="32" required>
                <a class="ls-label-text-prefix ls-toggle-pass ls-ico-eye" data-toggle-class="ls-ico-eye, ls-ico-eye-blocked" data-target="#password_field" href="#"> </a> </div>
              </label>
              <label class="ls-label col-sm-12 col-md-6">
              <b class="ls-label-text">CONFIRMAR SENHA</b>
              <div class="ls-prefix-group">
                <input type="password" name="usu_senha_confirmar" id="password_field1"  size="32">
                <a class="ls-label-text-prefix ls-toggle-pass ls-ico-eye" data-toggle-class="ls-ico-eye, ls-ico-eye-blocked" data-target="#password_field1" href="#"> </a> </div>
              </label>
              <div class="ls-actions-btn">
                <input type="submit" value="ATUALIZAR DADOS" class="ls-btn-primary ls-btn">
              </div>
              <input type="hidden" name="MM_update" value="form1">
              <input type="hidden" name="usu_id" value="<?php echo $row_UsuLogado['usu_id']; ?>">
              <input type="hidden" name="escola_id" value="<?php echo $row_EscolaLogada['escola_id']; ?>">
            </form>
          </div>
          <div class="col-md-2">
            <img src="../../img/funcionarios/<?php echo $row_UsuLogado['usu_foto']; ?>" width="100%" class="">
            <button data-ls-module="modal" data-target="#myAwesomeModal" class="ls-btn ls-btn-xs ls-btn-block">Mudar foto</button>
          </div>    
        </div> 
        <hr>
        <div class="1ls-box col-md-6">
          <h4>PERMISSÕES</h4>
          <br>
          <p>Pode incluir dados?
            <?php if ($row_UsuLogado['usu_insert']=="S") { ?>
              <strong class="ls-tag-info">SIM</strong>
            <?php } else { ?>
              <strong class="ls-tag-warning">NÃO</strong>
            <?php } ?>
          </p>
          <p>Pode editar dados?
            <?php if ($row_UsuLogado['usu_update']=="S") { ?>
              <strong class="ls-tag-info">SIM</strong>
            <?php } else { ?>
              <strong class="ls-tag-warning">NÃO</strong>
            <?php } ?>
          </p>
          <p>Pode excluir dados?
            <?php if ($row_UsuLogado['usu_delete']=="S") { ?>
              <strong class="ls-tag-info">SIM</strong>
            <?php } else { ?>
              <strong class="ls-tag-warning">NÃO</strong>
            <?php } ?>
          </p>
        </div>
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
    <div class="ls-modal" id="myAwesomeModal">
      <form method="post" enctype="multipart/form-data" name="form2" action="<?php echo $editFormAction; ?>" autocomplete="off">
        <div class="ls-modal-box">
          <div class="ls-modal-header">
            <button data-dismiss="modal">&times;</button>
            <h4 class="ls-modal-title">ESCOLHA A FOTO DO USUÁRIO</h4>
          </div>
          <div class="ls-modal-body" id="myModalBody">
            <p>
              <div class="ls-alert-info"><strong>Atenção:</strong> <br>
                1 - Envie uma imagem com a mesma proporção de altura e largura. <br>
              </div>
              <label class="ls-label col-md-12"> <b class="ls-label-text">IMAGEM</b>
                <input type="file" name="usu_foto" value="" required>
              </label>
              <input type="hidden" name="MM_insert2" value="form2">
              <input type="hidden" name="escola_id" value="<?php echo $row_UsuLogado['usu_id']; ?>">
            </p>
          </div>
          <div class="ls-modal-footer">
            <button class="ls-btn ls-float-right" data-dismiss="modal">CANCELAR</button>
            <input type="submit" value="CADASTRAR IMAGEM" class="ls-btn-primary">
          </div>
        </div>
      </form>
    </div>
    <!-- /.modal --> 

    <!-- We recommended use jQuery 1.10 or up --> 
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
    <script src="js/locastyle.js"></script>
    <script src="js/mascara.js"></script>
  </body>
  </html>
  <?php
  mysql_free_result($UsuLogado);

  mysql_free_result($EscolaLogada);
  ?>
