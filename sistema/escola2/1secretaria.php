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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EscolaLogada = "
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, 
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_tema, escola_unidade_executora, 
escola_caixa_ux_prestacao_contas, escola_libera_boletim, escola_numero_salas, escola_foto_aluno, escola_assinatura,
sec_id, sec_cidade, sec_uf 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	
		
	if ($row_UsuLogado['usu_update']=="N") {
		header(sprintf("Location: secretaria.php?permissao"));
		break;
	}
	
  $updateSQL = sprintf("UPDATE smc_escola SET escola_nome=%s, escola_cep=%s, escola_endereco=%s, escola_num=%s, escola_bairro=%s, escola_telefone1=%s, escola_telefone2=%s, escola_email=%s, escola_tema=%s, escola_unidade_executora=%s, escola_caixa_ux_prestacao_contas=%s, escola_libera_boletim=%s, escola_foto_aluno=%s, escola_assinatura=%s, escola_numero_salas=%s WHERE escola_id=%s",
                       GetSQLValueString($_POST['escola_nome'], "text"),
                       GetSQLValueString($_POST['escola_cep'], "text"),
                       GetSQLValueString($_POST['escola_endereco'], "text"),
                       GetSQLValueString($_POST['escola_num'], "text"),
                       GetSQLValueString($_POST['escola_bairro'], "text"),
                       GetSQLValueString($_POST['escola_telefone1'], "text"),
                       GetSQLValueString($_POST['escola_telefone2'], "text"),
                       GetSQLValueString($_POST['escola_email'], "text"),
                       GetSQLValueString($_POST['escola_tema'], "text"),
                       GetSQLValueString($_POST['escola_unidade_executora'], "text"),
                       GetSQLValueString($_POST['escola_caixa_ux_prestacao_contas'], "text"),
                       GetSQLValueString($_POST['escola_libera_boletim'], "text"),
                       GetSQLValueString($_POST['escola_foto_aluno'], "text"),
                       GetSQLValueString($_POST['escola_assinatura'], "text"),
                       GetSQLValueString($_POST['escola_numero_salas'], "text"),
                       GetSQLValueString($_POST['escola_id'], "int"));

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
'1', 
'', 
'$dat')
";
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$Result1 = mysql_query($sql, $SmecelNovo) or die(mysql_error());
  
  

  $updateGoTo = "secretaria.php?atualizado";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}


if ((isset($_POST["MM_insert2"])) && ($_POST["MM_insert2"] == "form2")) {
	
		
	if ($row_UsuLogado['usu_update']=="N") {
		header(sprintf("Location: secretaria.php?permissao"));
		break;
	}
	
//CADASTRO DA LOGO
include('../funcoes/class.upload.php');

$handle = new Upload($_FILES['logo']);

if ($handle->uploaded) 
{ 

$handle->file_new_name_body 	 = $novo_nome;
$handle->mime_check 			 = true;
$handle->image_resize            = true;
$handle->image_ratio	         = false;
$handle->image_x                 = 200;
$handle->image_y                 = 200;
$handle->Process('../../img/logo/');

// Miniatura
		$handle->mime_check 			 = true;
		$handle->image_resize            = true;
		$handle->image_ratio           	 = false;
		$handle->image_x                 = 400;
		$handle->image_y       			 = 400;
		$handle->image_overlay_color   = '#FFFFFF';
		$handle->image_overlay_opacity = 70;
		//$handle->jpeg_quality            = 20;
		$handle->file_new_name_body		 = $novo_nome;
		$handle->Process('../../img/marcadagua/');

if ($handle->processed) 
{

$nome_do_arquivo = $handle->file_dst_name;

$insertSQL = sprintf("UPDATE smc_escola SET escola_logo='$nome_do_arquivo' WHERE escola_id=%s",
                       GetSQLValueString($_POST['escola_id'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());
  
  $insertGoTo = "secretaria.php?foto";
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
     
    <h1 class="ls-title-intro ls-ico-home">Dados da Escola/Setor</h1>

    <?php if (isset($_GET["contato"])) { ?>
      <div class="ls-alert-danger ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
      
                  PREENCHA OS DADOS DE CONTATO DA SUA ESCOLA. <br><br>Telefone e E-mail<br><br>Obs.: Se a escola não possuir dados de contato, informe os dados de contato do(a) diretor(a) ou responsável. 
    </div>

    <?php } ?>  

    <?php if (isset($_GET["atualizado"])) { ?>
      <div class="ls-alert-success ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> Dados atualizados com sucesso. </div>
      <?php } ?>
	                <?php if (isset($_GET["permissao"])) { ?>
                <div class="ls-alert-danger ls-dismissable">
                  <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
                  VOCÊ NÃO TEM PERMISSÃO PARA REALIZAR ESTA AÇÃO.
                </div>
              <?php } ?>
    <div class="row">
      <div class="col-sm-7">
        <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form ls-form-horizontal row">
          <label class="ls-label col-md-12"> <b class="ls-label-text">ESCOLA/SETOR</b>
            <input type="text" name="escola_nome" value="<?php echo htmlentities($row_EscolaLogada['escola_nome'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label class="ls-label col-md-4"> <b class="ls-label-text">CEP</b>
            <input type="text" name="escola_cep" value="<?php echo htmlentities($row_EscolaLogada['escola_cep'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label class="ls-label col-md-8"> <b class="ls-label-text">ENDEREÇO</b>
            <input type="text" name="escola_endereco" value="<?php echo htmlentities($row_EscolaLogada['escola_endereco'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label class="ls-label col-md-3"> <b class="ls-label-text">NÚMERO</b>
            <input type="text" name="escola_num" value="<?php echo htmlentities($row_EscolaLogada['escola_num'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label class="ls-label col-md-9"> <b class="ls-label-text">BAIRRO</b>
            <input type="text" name="escola_bairro" value="<?php echo htmlentities($row_EscolaLogada['escola_bairro'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label class="ls-label col-md-6"> <b class="ls-label-text">TELEFONE 1</b>
            <input type="text" name="escola_telefone1" class="celular9" placeholder="( ) _____-____" value="<?php echo htmlentities($row_EscolaLogada['escola_telefone1'], ENT_COMPAT, 'utf-8'); ?>" size="32" required>
          </label>
          <label class="ls-label col-md-6"> <b class="ls-label-text">TELEFONE 2</b>
            <input type="text" name="escola_telefone2" class="celular9" placeholder="( ) _____-____" value="<?php echo htmlentities($row_EscolaLogada['escola_telefone2'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label class="ls-label col-md-12"> <b class="ls-label-text">E-MAIL</b>
            <input type="text" name="escola_email" value="<?php echo htmlentities($row_EscolaLogada['escola_email'], ENT_COMPAT, 'utf-8'); ?>" size="32" required>
          </label>
          
          <label class="ls-label col-md-12"> 
		  <b class="ls-label-text">COR DO TEMA</b>
          <div class="ls-custom-select">
          <select name="escola_tema" class="ls-select">
                <option value="ls-theme-dark-yellow" <?php if (!(strcmp("ls-theme-dark-yellow", htmlentities($row_EscolaLogada['escola_tema'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Amarelo Escuro</option>
                <option value="ls-theme-yellow-gold" <?php if (!(strcmp("ls-theme-yellow-gold", htmlentities($row_EscolaLogada['escola_tema'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Amarelo Ouro</option>
                <option value="ls-theme-blue" <?php if (!(strcmp("ls-theme-blue", htmlentities($row_EscolaLogada['escola_tema'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Azul</option>
                <option value="ls-theme-light-blue" <?php if (!(strcmp("ls-theme-light-blue", htmlentities($row_EscolaLogada['escola_tema'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Azul Claro</option>
                <option value="ls-theme-indigo" <?php if (!(strcmp("ls-theme-indigo", htmlentities($row_EscolaLogada['escola_tema'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Azul Indigo</option>
                <option value="ls-theme-royal-blue" <?php if (!(strcmp("ls-theme-royal-blue", htmlentities($row_EscolaLogada['escola_tema'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Azul Real</option>
                <option value="ls-theme-turquoise" <?php if (!(strcmp("ls-theme-turquoise", htmlentities($row_EscolaLogada['escola_tema'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Azul Turqueza</option>
                <option value="ls-theme-cyanogen" <?php if (!(strcmp("ls-theme-cyanogen", htmlentities($row_EscolaLogada['escola_tema'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Azul Cyanogen</option>
                <option value="ls-theme-gray" <?php if (!(strcmp("ls-theme-gray", htmlentities($row_EscolaLogada['escola_tema'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Cinza</option>
                <option value="ls-theme-gold" <?php if (!(strcmp("ls-theme-gold", htmlentities($row_EscolaLogada['escola_tema'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Gold</option>
                <option value="ls-theme-orange" <?php if (!(strcmp("ls-theme-orange", htmlentities($row_EscolaLogada['escola_tema'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Laranja</option>
                <option value="ls-theme-light-brown" <?php if (!(strcmp("ls-theme-light-brown", htmlentities($row_EscolaLogada['escola_tema'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Marrom Claro</option>
                <option value="ls-theme-purple" <?php if (!(strcmp("ls-theme-purple", htmlentities($row_EscolaLogada['escola_tema'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Roxo</option>
                <option value="ls-theme-green" <?php if (!(strcmp("ls-theme-green", htmlentities($row_EscolaLogada['escola_tema'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Verde</option>
                <option value="ls-theme-light-green" <?php if (!(strcmp("ls-theme-light-green", htmlentities($row_EscolaLogada['escola_tema'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Verde Claro</option>
                <option value="ls-theme-green-lemon" <?php if (!(strcmp("ls-theme-green-lemon", htmlentities($row_EscolaLogada['escola_tema'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Verde Limão</option>
                <option value="ls-theme-dark-green" <?php if (!(strcmp("ls-theme-dark-green", htmlentities($row_EscolaLogada['escola_tema'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Verde Escuro</option>
                <option value="ls-theme-moss" <?php if (!(strcmp("ls-theme-moss", htmlentities($row_EscolaLogada['escola_tema'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Verde Musgo</option>
                <option value="ls-theme-light-red" <?php if (!(strcmp("ls-theme-light-red", htmlentities($row_EscolaLogada['escola_tema'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Vermelho Claro</option>
                <option value="ls-theme-wine" <?php if (!(strcmp("ls-theme-wine", htmlentities($row_EscolaLogada['escola_tema'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Vinho</option>
              </select>
          </div>
          </label>
		  
          <label class="ls-label col-md-12"> 
		  <b class="ls-label-text">UNIDADE EXECUTORA</b>
          <div class="ls-custom-select">
          <select name="escola_unidade_executora" class="ls-select">
                <option value="" <?php if (!(strcmp("", htmlentities($row_EscolaLogada['escola_unidade_executora'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>></option>
                <option value="S" <?php if (!(strcmp("S", htmlentities($row_EscolaLogada['escola_unidade_executora'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>POSSUI</option>
                <option value="N" <?php if (!(strcmp("N", htmlentities($row_EscolaLogada['escola_unidade_executora'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>NÃO POSSUI</option>
              </select>
          </div>
          </label>
		  
          <label class="ls-label col-md-12">
        <b class="ls-label-text">PRESTAÇÃO DE CONTAS</b>
        <div class="ls-custom-select">
          <select name="escola_caixa_ux_prestacao_contas" class="ls-select">
            <option value="" <?php if (!(strcmp("", htmlentities($row_EscolaLogada['escola_caixa_ux_prestacao_contas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>></option>
            <option value="S" <?php if (!(strcmp("S", htmlentities($row_EscolaLogada['escola_caixa_ux_prestacao_contas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>ADIMPLENTE</option>
            <option value="N" <?php if (!(strcmp("N", htmlentities($row_EscolaLogada['escola_caixa_ux_prestacao_contas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>INADIMPLENTE</option>
          </select>
        </div>
        </label>
		  
		  
        <label class="ls-label col-md-12">
        <b class="ls-label-text">LIBERAR BOLETIM NO PAINEL DO ALUNO?</b>
        <div class="ls-custom-select">
          <select name="escola_libera_boletim" class="ls-select">
            <option value="S" <?php if (!(strcmp("S", htmlentities($row_EscolaLogada['escola_libera_boletim'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>SIM</option>
            <option value="N" <?php if (!(strcmp("N", htmlentities($row_EscolaLogada['escola_libera_boletim'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>NÃO</option>
          </select>
        </div>
        </label>

        <label class="ls-label col-md-12">
        <b class="ls-label-text">CADASTRO DE FOTO DO ALUNO NO PAINEL?</b>
        <div class="ls-custom-select">
          <select name="escola_foto_aluno" class="ls-select">
            <option value="S" <?php if (!(strcmp("S", htmlentities($row_EscolaLogada['escola_foto_aluno'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>SIM</option>
            <option value="N" <?php if (!(strcmp("N", htmlentities($row_EscolaLogada['escola_foto_aluno'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>NÃO</option>
          </select>
        </div>
        </label>

        <label class="ls-label col-md-12">
        <b class="ls-label-text">RESPONSÁVEL PELAS ASSINATURAS DA FICHA DE MATRÍCULA</b>
        <div class="ls-custom-select">
          <select name="escola_assinatura" class="ls-select">
            <option value="SECRETARIO(A) ESCOLAR" <?php if (!(strcmp("SECRETARIO(A) ESCOLAR", htmlentities($row_EscolaLogada['escola_assinatura'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>SECRETÁRIO(A) ESCOLAR</option>
            <option value="DIRETOR(A) ESCOLAR" <?php if (!(strcmp("DIRETOR(A) ESCOLAR", htmlentities($row_EscolaLogada['escola_assinatura'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>DIRETOR(A) ESCOLAR</option>
            <option value="COORDENADOR(A) ESCOLAR" <?php if (!(strcmp("COORDENADOR(A) ESCOLAR", htmlentities($row_EscolaLogada['escola_assinatura'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>COORDENADOR(A) ESCOLAR</option>
            <option value="GESTOR(A) DE MATRICULA" <?php if (!(strcmp("GESTOR(A) DE MATRICULA", htmlentities($row_EscolaLogada['escola_assinatura'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>GESTOR(A) DE MATRÍCULA</option>
          </select>
        </div>
        </label>




          <label class="ls-label col-md-12"> <b class="ls-label-text">Nº DE SALAS PARA TURMAS DE ESCOLARIZAÇÃO</b>
            <input type="number" name="escola_numero_salas" value="<?php echo htmlentities($row_EscolaLogada['escola_numero_salas'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
		  
		  
		  
          <hr>
          <input type="submit" value="ATUALIZAR" class="ls-btn-primary">
          <input type="hidden" name="MM_update" value="form1">
          <input type="hidden" name="escola_id" value="<?php echo $row_EscolaLogada['escola_id']; ?>">
          <input type="hidden" name="usu_id" value="<?php echo $row_UsuLogado['usu_id']; ?>">

<hr>

        </form>
      </div>
      <div class="col-sm-5 ls-box">
        <p>Brasão da Escola<br>
          <img src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt="Logo da <?php echo $row_EscolaLogada['escola_nome']; ?>" title="Logo da <?php echo $row_EscolaLogada['escola_nome']; ?>" /></p>
        <p>Marca D'água da Escola<br>
          <img src="../../img/marcadagua/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt="Logo da <?php echo $row_EscolaLogada['escola_nome']; ?>" title="Logo da <?php echo $row_EscolaLogada['escola_nome']; ?>" /></p>
        <hr>
        <button data-ls-module="modal" data-target="#myAwesomeModal" class="ls-btn-primary">Cadastrar/Alterar Brasão da Escola</button>
        <hr>
        <div class="ls-modal" id="myAwesomeModal">
          <form method="post" enctype="multipart/form-data" name="form2" action="<?php echo $editFormAction; ?>" autocomplete="off">
            <div class="ls-modal-box">
              <div class="ls-modal-header">
                <button data-dismiss="modal">&times;</button>
                <h4 class="ls-modal-title">ESCOLHA O BRASÃO DA ESCOLA</h4>
              </div>
              <div class="ls-modal-body" id="myModalBody">
                <p>
                <div class="ls-alert-info"><strong>Atenção:</strong> <br>
                  1 - Envie uma imagem com a mesma proporção de altura e largura. <br>
                  2 - A imagem enviada também irá gerar a imagem de marca d'água da escola.</div>
                <label class="ls-label col-md-12"> <b class="ls-label-text">IMAGEM</b>
                  <input type="file" name="logo" value="" required>
                </label>
                <input type="hidden" name="MM_insert2" value="form2">
                <input type="hidden" name="escola_id" value="<?php echo $row_EscolaLogada['escola_id']; ?>">
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
        
      </div>
    </div>
    <p>&nbsp;</p>
    <hr>
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
<script src="js/mascara.js"></script>

</body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);
?>
