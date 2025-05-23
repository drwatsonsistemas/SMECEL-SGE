<?php require_once('../../Connections/SmecelNovoPDO.php'); ?>
<?php include('../escola/fnc/idade.php'); ?>
<?php require_once('funcoes/anti_injection.php'); ?>


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

require_once('funcoes/usuLogadoPDO.php');
require_once('funcoes/anoLetivoPDO.php');

$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = :sec_id";
$stmt = $SmecelNovo->prepare($query_Secretaria);
$stmt->bindValue(':sec_id', $row_UsuarioLogado['usu_sec'], PDO::PARAM_INT);
$stmt->execute();
$row_Secretaria = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_Secretaria = $stmt->rowCount();

$query_Ano = "SELECT ano_letivo_id, ano_letivo_ano, ano_letivo_aberto, ano_letivo_id_sec FROM smc_ano_letivo WHERE ano_letivo_aberto = 'N' AND ano_letivo_id_sec = :sec_id ORDER BY ano_letivo_ano DESC";
$stmt = $SmecelNovo->prepare($query_Ano);
$stmt->bindValue(':sec_id', $row_UsuarioLogado['usu_sec'], PDO::PARAM_INT);
$stmt->execute();
$anos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pegando o ano letivo mais recente
$anoLetivo = $row_AnoLetivo['ano_letivo_ano'];

if (isset($_GET['ano'])) {
  if ($_GET['ano'] == "") {
    $anoLetivo = $anos[0]['ano_letivo_ano'];
  }

  $anoLetivo = anti_injection($_GET['ano']);
  $anoLetivo = (int) $anoLetivo;
}

// Etapas
$etapa = 99;
$qry_etapa = "";
if (isset($_GET['etapa'])) {
  $etapa = anti_injection($_GET['etapa']);
  $qry_etapa = "AND etapa_id = :etapa_id";
}

// Buscando todas as etapas de uma vez
$query_Etapas = "SELECT etapa_id, etapa_nome FROM smc_etapa WHERE etapa_id_filtro IN (1, 3, 7)";
$stmt = $SmecelNovo->prepare($query_Etapas);
$stmt->execute();
$etapas = $stmt->fetchAll(PDO::FETCH_ASSOC); // Pegando todas as etapas de uma vez

$query_EtapasAc = "SELECT etapa_id, etapa_id_filtro, etapa_nome, etapa_idade, etapa_limite_turma, etapa_nome_abrev, etapa_ano_ef FROM smc_etapa WHERE etapa_id_filtro IN (1,3,7) $qry_etapa";
$stmt = $SmecelNovo->prepare($query_EtapasAc);
if (!empty($qry_etapa)) {
  $stmt->bindValue(':etapa_id', $etapa, PDO::PARAM_INT);
}
$stmt->execute();
$row_EtapasAc = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_EtapasAc = $stmt->rowCount();

$titulo_etapa = ($etapa != 99) ? $row_EtapasAc['etapa_nome'] : "TODAS AS ETAPAS";

// Escolas
$escola = 99;
$qry_escola = "";
if (isset($_GET['escola'])) {
  $escola = anti_injection($_GET['escola']);
  switch ($escola) {
    case 99:
      $qry_escola = "";
      $escola_titulo = "TODAS AS ESCOLAS";
      break;
    default:
      $qry_escola = " AND escola_id = :escola_id";
      $escola_titulo = "";
      break;
  }
}

$query_Escolas = "
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, escola_telefone2, escola_email, escola_inep, escola_cnpj, 
       escola_logo, escola_ue, escola_situacao, escola_localizacao, escola_ibge_municipio, escola_tema, escola_unidade_executora, escola_caixa_ux_prestacao_contas, escola_libera_boletim, 
       escola_latitude, escola_longitude, escola_localizacao_diferenciada 
FROM smc_escola
WHERE escola_id_sec = :escola_id_sec AND escola_situacao = '1' AND escola_ue = '1' $qry_escola";
$stmt = $SmecelNovo->prepare($query_Escolas);
$stmt->bindValue(':escola_id_sec', $row_UsuarioLogado['usu_sec'], PDO::PARAM_INT);
if (!empty($qry_escola)) {
  $stmt->bindValue(':escola_id', $escola, PDO::PARAM_INT);
}
$stmt->execute();
$escolas = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRows_Escolas = $stmt->rowCount();


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
  <script src="js/locastyle.js"></script>
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>

<body>
  <?php include_once("menu_top_pdo.php"); ?>
  <?php include_once "menu.php"; ?>
  <main class="ls-main">
    <div class="container-fluid">
      <h1 class="ls-title-intro ls-ico-home">Relação de alunos por etapa</h1>
      <div class="ls-box-filter">
        <form class="ls-form row">
          <!-- Dropdown para Etapas -->
          <div class="col-md-3 col-sm-12">
            <label class="ls-label">
              <b class="ls-label-text">Etapa de Ensino</b>
              <div class="ls-custom-select">
                <select id="etapa-select" name="etapa" class="ls-select">
                  <option value="99" selected>TODAS</option>
                  <?php foreach ($etapas as $row_Etapas) { ?>
                    <option value="<?php echo $row_Etapas['etapa_id']; ?>">
                      <?php echo $row_Etapas['etapa_nome']; ?>
                    </option>
                  <?php } ?>
                </select>
              </div>
            </label>
          </div>

          <!-- Dropdown para Ano Letivo -->
          <div class="col-md-3 col-sm-12">
            <label class="ls-label">
              <b class="ls-label-text">Ano Letivo</b>
              <div class="ls-custom-select">
                <select id="ano-select" name="ano" class="ls-select">
                  <option value="<?php echo $anoLetivo; ?>" selected><?php echo $anoLetivo; ?></option>
                  <?php foreach ($anos as $row_Ano) { ?>
                    <option value="<?php echo $row_Ano['ano_letivo_ano']; ?>">
                      <?php echo htmlspecialchars($row_Ano['ano_letivo_ano']); ?>
                    </option>
                  <?php } ?>
                </select>
              </div>
            </label>
          </div>

          <!-- Dropdown para Escolas -->
          <div class="col-md-3 col-sm-12">
            <label class="ls-label">
              <b class="ls-label-text">Unidade Escolar</b>
              <div class="ls-custom-select">
                <select id="escola-select" name="escola" class="ls-select">
                  <option value="99" selected>TODAS</option>
                  <?php foreach ($escolas as $row_Escolas) { ?>
                    <option value="<?php echo $row_Escolas['escola_id']; ?>">
                      <?php echo htmlspecialchars($row_Escolas['escola_nome']); ?>
                    </option>
                  <?php } ?>
                </select>
              </div>
            </label>
          </div>

          <!-- Botão de Impressão -->
          <div class="col-md-3 col-sm-12 ls-txt-right">
            <a id="btn-imprimir" class="ls-btn ls-btn-primary ls-ico-print" href="#" target="_blank">Imprimir</a>
          </div>
        </form>
      </div>



      <hr>

      <h2 id="etapa-titulo" class="ls-title-2">Etapa: TODAS</h2>
      <hr>

      <div id="alunos-container">
        <!-- Spinner de carregamento -->


        <!-- Tabela de alunos -->
        <table id="alunos-table" width="100%" class="ls-table">
          <thead>
            <tr>
              <th class="ls-txt-center" width="55px">#</th>
              <th class="ls-txt-center">ALUNO</th>
              <th class="ls-txt-center">IDADE</th>
              <th class="ls-txt-center">TURMA</th>
              <th class="ls-txt-center">ESCOLA</th>
            </tr>
          </thead>
          <tbody>

          </tbody>
        </table>
        <div id="loading-spinner" style="display: none; text-align: center; margin: 20px 0;">
          <img src="images/spinner.gif" alt="Carregando..." width="100" />
        </div>
      </div>


      <div id="alunos-total" class="ls-box ls-box-gray">
        <h5 class="ls-title-5">Total de alunos matriculados na etapa TODAS: 0</h5>
      </div>


      <p>&nbsp;</p>
      <!-- CONTEUDO -->
    </div>
  </main>

  <?php include_once "notificacoes.php"; ?>

  <!-- We recommended use jQuery 1.10 or up -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="js/locastyle.js"></script>
  <script>
    $(document).ready(function () {
      function carregarDados() {
        // Mostra o spinner
        $('#loading-spinner').show();
        $('#alunos-table tbody').empty(); // Limpa a tabela

        // Valores padrão se não houver seleção
        let etapa = $('#etapa-select').val() || 99;
        let ano = $('#ano-select').val() || new Date().getFullYear();
        let escola = $('#escola-select').val() || 99;

        let printUrl = `impressao/print_rel_lista_alunos_etapa.php?etapa=${etapa}&escola=${escola}&ano=${ano}`;
        $('#btn-imprimir').attr('href', printUrl);

        let etapaNome = $('#etapa-select option:selected').text(); // Obtém o nome da etapa selecionada
        $('#etapa-titulo').text(`Etapa: ${etapaNome}`);

        $.ajax({
          url: 'requests/fetch_alunos_por_etapa.php',
          method: 'POST',
          data: { etapa: etapa, ano: ano, escola: escola },
          success: function (response) {
            // Esconde o spinner
            $('#loading-spinner').hide();

            let tableBody = $('#alunos-table tbody');
            if (response.total_alunos > 0) {
              Object.keys(response.etapas).forEach(etapaNome => {
                tableBody.append(`
              <tr>
                <td colspan="5" class="ls-txt-center"><strong>${etapaNome}</strong></td>
              </tr>
            `);

                response.etapas[etapaNome].forEach((aluno, index) => {
                  let row = `
                <tr>
                  <td class="ls-txt-center">${index + 1}</td>
                  <td>${aluno.aluno_nome}</td>
                  <td class="ls-txt-center">${aluno.idade}</td>
                  <td class="ls-txt-center">${aluno.turma_nome}</td>
                  <td class="ls-txt-center">${aluno.escola_nome}</td>
                </tr>
              `;
                  tableBody.append(row);
                });
              });
            } else {
              tableBody.append('<tr><td colspan="5" class="ls-txt-center">Nenhum aluno encontrado.</td></tr>');
            }

            $('#alunos-total h5').text(`Total de alunos: ${response.total_alunos}`);
          },
          error: function () {
            // Esconde o spinner em caso de erro
            $('#loading-spinner').hide();
            alert('Erro ao carregar os dados. Tente novamente.');
          },
        });
      }

      // Carrega todos os dados automaticamente ao abrir a página
      carregarDados();

      // Recarrega os dados ao mudar os filtros
      $('#etapa-select, #ano-select, #escola-select').on('change', carregarDados);
    });
  </script>

</body>

</html>