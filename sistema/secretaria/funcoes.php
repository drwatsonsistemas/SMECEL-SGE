<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}
// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF'] . "?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")) {
  $logoutAction .= "&" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) && ($_GET['doLogout'] == "true")) {
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);

  $logoutGoTo = "../../index.php?exit";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "1,99";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup)
{
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False;

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) {
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers);
    $arrGroups = Explode(",", $strGroups);
    if (in_array($UserName, $arrUsers)) {
      $isValid = true;
    }
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) {
      $isValid = true;
    }
    if (($strUsers == "") && false) {
      $isValid = true;
    }
  }
  return $isValid;
}

$MM_restrictGoTo = "../../index.php?acessorestrito";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("", $MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?"))
    $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0)
    $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo . $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: " . $MM_restrictGoTo);
  exit;
}
?>
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

function formatarParaBanco($valor)
{
  return str_replace(',', '.', str_replace('.', '', $valor));
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {

  $media_salarial = formatarParaBanco($_POST['funcao_media_salarial']);
  $insertSQL = sprintf(
    "INSERT INTO smc_funcao (funcao_secretaria_id, funcao_nome, funcao_docencia, funcao_gestor_escolar, funcao_observacoes, funcao_media_salarial) VALUES (%s, %s, %s, %s, %s, %s)",
    GetSQLValueString($_POST['funcao_secretaria_id'], "int"),
    GetSQLValueString($_POST['funcao_nome'], "text"),
    GetSQLValueString(isset($_POST['funcao_docencia']) ? "true" : "", "defined", "'S'", "'N'"),
    GetSQLValueString(isset($_POST['funcao_gestor_escolar']) ? "true" : "", "defined", "'S'", "'N'"),
    GetSQLValueString($_POST['funcao_observacoes'], "text"),
    GetSQLValueString($media_salarial, "int")
  );

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

  $insertGoTo = "funcoes.php?cadastrado";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

require_once('funcoes/usuLogado.php');
require_once('funcoes/anoLetivo.php');

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Funcoes = "SELECT * FROM smc_funcao WHERE funcao_secretaria_id = '$row_Secretaria[sec_id]' ORDER BY funcao_nome ASC";
$Funcoes = mysql_query($query_Funcoes, $SmecelNovo) or die(mysql_error());
$row_Funcoes = mysql_fetch_assoc($Funcoes);
$totalRows_Funcoes = mysql_num_rows($Funcoes);
?>

<!DOCTYPE html>
<html class="ls-theme-green">

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
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>

<body>
  <?php include_once("menu_top.php"); ?>
  <?php include_once "menu.php"; ?>
  <main class="ls-main">
    <div class="container-fluid">
      <h1 class="ls-title-intro ls-ico-home">CARGOS E FUNÇÕES</h1>
      <div class="ls-box ls-board-box">

        <?php if (isset($_GET["cadastrado"])) { ?>
          <div class="ls-alert-success ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
            <strong>Aviso:</strong> Cargo/Função cadastrado com sucesso!
          </div>
        <?php } ?>
        <?php if (isset($_GET["novo"])) { ?>
          <div class="ls-alert-danger ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
            <strong>Aviso:</strong> Para cadastrar um novo funcionário, é necessário cadastrar os cargos/funções!
          </div>
        <?php } ?>


        <button data-ls-module="modal" data-target="#modalLarge" class="ls-btn-primary ls-ico-plus">CADASTRAR
          FUNÇÃO</button>
        <a href="impressao/rel_funcoes.php" class="ls-btn" target="_blank">IMPRIMIR</a>

        <?php if ($totalRows_Funcoes > 0) { // Show if recordset not empty ?>
          <table class="ls-table">
            <thead>
              <tr>
                <th width="50"></th>
                <th>CARGO / FUNÇÃO</th>
                <th>MÉDIA SALARIAL</th>
                <th width="100"></th>
              </tr>
            </thead>
            <?php
            $num = 1;
            ?>
            <?php do { ?>
              <tr id="linha_<?php echo $row_Funcoes['funcao_id']; ?>"> <!-- Adicione o id aqui -->
                <td><?php echo str_pad($num, 3, "0", STR_PAD_LEFT); ?></td>
                <td><?php echo $row_Funcoes['funcao_nome']; ?></td>
                <td><?php echo $row_Funcoes['funcao_media_salarial']; ?></td>
                <td>
                  <a href="funcoes_editar.php?cod=<?php echo $row_Funcoes['funcao_id']; ?>" class="ls-ico-pencil"></a>
                  <a href="#" class="ls-ico-remove ls-color-danger excluir-funcao"
                    data-id="<?php echo $row_Funcoes['funcao_id']; ?>"></a>
                </td>
              </tr>
              <?php
              $num++;
            } while ($row_Funcoes = mysql_fetch_assoc($Funcoes)); ?>
          </table>
        <?php } else { ?>
          <hr>
          <div class="ls-alert-warning"><strong>Atenção:</strong> Nenhuma função cadastrada.</div>
        <?php } // Show if recordset not empty ?>
        <div class="ls-modal" id="modalLarge">
          <div class="ls-modal-large">
            <div class="ls-modal-header">
              <button data-dismiss="modal">&times;</button>
              <h4 class="ls-modal-title">CADASTRAR CARGO/FUNÇÃO</h4>
            </div>
            <div class="ls-modal-body">
              <p>
              <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form-horizontal">
                <label class="ls-label col-md-12">
                  <b class="ls-label-text">NOME DA FUNÇÃO/CARGO</b>
                  <p class="ls-label-info">Informe o nome da função</p>
                  <input type="text" name="funcao_nome" value="" size="32" required>
                </label>
                <label class="ls-label col-md-12">
                  <b class="ls-label-text">MÉDIA SALARIAL</b>
                  <p class="ls-label-info">Insira a média salarial da função</p>
                  <div class="ls-prefix-group ls-field-md">
                    <span class="ls-label-text-prefix">R$</span>
                    <input type="text" class="money2" name="funcao_media_salarial" value="">
                  </div>
                </label>
                <label class="ls-label col-md-12">
                  <b class="ls-label-text">ATIVIDADE DE DOCÊNCIA</b>
                  <input type="checkbox" name="funcao_docencia" value="">
                  <p class="ls-label-info">Marque se essa função exerce docência, no caso, a função de Professor</p>
                </label>
                <label class="ls-label col-md-12">
                  <b class="ls-label-text">ATIVIDADE DE GESTÃO ESCOLAR</b>
                  <input type="checkbox" name="funcao_gestor_escolar" value="">
                  <p class="ls-label-info">Marque se essa função exerce gestão escolar, no caso direção escolar</p>
                </label>
                <label class="ls-label col-md-12">
                  <b class="ls-label-text">OBSERVAÇÕES</b>
                  <p class="ls-label-info">Informe detalhes sobre a função</p>
                  <textarea name="funcao_observacoes" cols="50" rows="5"></textarea>
                </label>
                <div class="ls-modal-footer">
                  <button type="submit" class="ls-btn-primary">SALVAR</button>
                  <button class="ls-btn ls-float-right" data-dismiss="modal">CANCELAR</button>
                </div>
                <input type="hidden" name="funcao_secretaria_id" value="<?php echo $row_Secretaria['sec_id']; ?>">
                <input type="hidden" name="MM_insert" value="form1">
              </form>
              </p>
            </div>
          </div>
        </div>
        <p>&nbsp;</p>
      </div>
    </div>
  </main>
  <?php include_once "notificacoes.php"; ?>

  <!-- We recommended use jQuery 1.10 or up -->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="js/locastyle.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="js/maiuscula.js"></script>
  <script src="js/semAcentos.js"></script>
  <script src="js/mascara.js"></script>

  <script>
$(document).ready(function () {
    $('.excluir-funcao').click(function (e) {
        e.preventDefault();
        var funcaoId = $(this).data('id');
        var linha = $('#linha_' + funcaoId); // Seleciona a linha com o id correspondente

        // Verifica se a linha foi encontrada
        if (linha.length === 0) {
            console.error('Linha não encontrada para o ID: ' + funcaoId);
            return;
        }

        Swal.fire({
            title: 'Tem certeza?',
            text: "Você não poderá reverter esta ação!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'funcoes/excluir_funcao.php',
                    type: 'POST',
                    data: { id: funcaoId },
                    success: function (response) {
                        console.log('Resposta bruta:', response);
                        if (response.success) {
                            Swal.fire(
                                'Excluído!',
                                'A função foi excluída com sucesso.',
                                'success'
                            );
                            // Remove a linha com animação
                            linha.fadeOut(300, function () {
                                $(this).remove();
                                // Opcional: Verifica se a tabela ficou vazia
                                if ($('.ls-table tr').length <= 1) { // Apenas o cabeçalho resta
                                    $('.ls-table').replaceWith('<hr><div class="ls-alert-warning"><strong>Atenção:</strong> Nenhuma função cadastrada.</div>');
                                }
                            });
                        } else {
                            Swal.fire(
                                'Erro!',
                                response.message || 'Erro ao excluir a função.',
                                'error'
                            );
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Erro AJAX:', status, error);
                        Swal.fire('Erro!', 'Erro na conexão com o servidor.', 'error');
                    }
                });
            }
        });
    });
});
  </script>

</body>

</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

mysql_free_result($Funcoes);
?>