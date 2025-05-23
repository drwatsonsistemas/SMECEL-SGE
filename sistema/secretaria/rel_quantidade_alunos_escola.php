<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include('../escola/fnc/idade.php'); ?>
<?php require_once('funcoes/anti_injection.php'); ?>


<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}
// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
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
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
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
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
    $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
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

require_once('funcoes/usuLogado.php');
require_once('funcoes/anoLetivo.php');


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Ano = "SELECT ano_letivo_id, ano_letivo_ano, ano_letivo_aberto, ano_letivo_id_sec FROM smc_ano_letivo WHERE ano_letivo_aberto = 'N' AND ano_letivo_id_sec = '$row_UsuarioLogado[usu_sec]' ORDER BY ano_letivo_ano DESC";
$Ano = mysql_query($query_Ano, $SmecelNovo) or die(mysql_error());
$row_Ano = mysql_fetch_assoc($Ano);
$totalRows_Ano = mysql_num_rows($Ano);

$anoLetivo = $row_AnoLetivo['ano_letivo_ano'];

if (isset($_GET['ano'])) {

	if ($_GET['ano'] == "") {
		//echo "TURMA EM BRANCO";	
		$anoLetivo = $row_AnoLetivo['ano_letivo_ano'];
	}

	$anoLetivo = anti_injection($_GET['ano']);
	$anoLetivo = (int) $anoLetivo;
}

$etapaDe='';
if(isset($_GET['etapaDe'])){
  $etapaDe = anti_injection($_GET['etapaDe']);
}

$etapaAte='';
if(isset($_GET['etapaAte'])){
  $etapaAte = anti_injection($_GET['etapaAte']);
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EtapasDe = "SELECT etapa_id, etapa_id_filtro, etapa_nome, etapa_idade, etapa_limite_turma, etapa_nome_abrev, etapa_ano_ef FROM smc_etapa WHERE etapa_id_filtro IN (1,3,7)";
$EtapasDe = mysql_query($query_EtapasDe, $SmecelNovo) or die(mysql_error());
$row_EtapasDe = mysql_fetch_assoc($EtapasDe);
$totalRows_EtapasDe = mysql_num_rows($EtapasDe);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EtapasAc = "SELECT etapa_id, etapa_id_filtro, etapa_nome, etapa_idade, etapa_limite_turma, etapa_nome_abrev, etapa_ano_ef FROM smc_etapa WHERE etapa_id_filtro IN (1,3,7)";
$EtapasAc = mysql_query($query_EtapasAc, $SmecelNovo) or die(mysql_error());
$row_EtapasAte = mysql_fetch_assoc($EtapasAc);
$totalRows_EtapasAc = mysql_num_rows($EtapasAc);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Escolas = "
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, escola_telefone2, escola_email, escola_inep, escola_cnpj, 
escola_logo, escola_ue, escola_situacao, escola_localizacao, escola_ibge_municipio, escola_tema, escola_unidade_executora, escola_caixa_ux_prestacao_contas, escola_libera_boletim, 
escola_latitude, escola_longitude, escola_localizacao_diferenciada 
FROM smc_escola
WHERE escola_id_sec = '$row_UsuarioLogado[usu_sec]' AND escola_situacao = '1' AND escola_ue = '1'
";
$Escolas = mysql_query($query_Escolas, $SmecelNovo) or die(mysql_error());
$row_Escolas = mysql_fetch_assoc($Escolas);
$totalRows_Escolas = mysql_num_rows($Escolas);

function buscarEtapasNoIntervalo($etapaDe, $etapaAte) {
  include('../../Connections/SmecelNovo.php');

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $query_Etapas = "SELECT etapa_id, etapa_id_filtro, etapa_nome, etapa_idade, etapa_limite_turma, etapa_nome_abrev, etapa_ano_ef FROM smc_etapa WHERE etapa_id_filtro IN (1,3,7)";
  $Etapas = mysql_query($query_Etapas, $SmecelNovo) or die(mysql_error());

  if (!$Etapas) {
    die('Invalid query: ' . mysql_error());
  }

  $etapas = []; 
  
  while ($row_Etapas = mysql_fetch_assoc($Etapas)) {
    $etapas[$row_Etapas['etapa_id']] = $row_Etapas['etapa_nome'];
  }

  $etapasSelecionadas = [];
  $adicionar = false;

  foreach ($etapas as $id_etapa => $nome_etapa) {
    if ($id_etapa == $etapaDe) {
      $adicionar = true;
    }
    if ($adicionar) {
      $etapasSelecionadas[] = $id_etapa;
    }
    if ($id_etapa == $etapaAte) {
      break;
    }
  }

  return $etapasSelecionadas;
}

?>

<!DOCTYPE html>
<html class="ls-theme-green">
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
  <link rel="stylesheet" type="text/css" href="css/locastyle.css">
  <script src="js/locastyle.js"></script>  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<body>
  <?php include_once("menu_top.php"); ?>
  <?php include_once "menu.php"; ?>
  <main class="ls-main">
    <div class="container-fluid">
      <h1 class="ls-title-intro ls-ico-home">Quantidades de alunos por etapa</h1>
      <!-- CONTEUDO -->

      <div class="ls-box-filter1">
      <div data-ls-module="dropdown" class="ls-dropdown ls-float-right1 ">
          <a href="#" class="ls-btn">ANO LETIVO: <?php echo $anoLetivo; ?></a>
          <ul class="ls-dropdown-nav">

            <li>
              <a href="rel_quantidade_alunos_escola.php?ano=<?php echo $row_AnoLetivo['ano_letivo_ano']?><?php 
                if(isset($_GET['etapaDe'])) echo '&etapaDe='.$_GET['etapaDe']; 
                if(isset($_GET['etapaAte'])) echo '&etapaAte='.$_GET['etapaAte']; 
              ?>" target="" title="Diários">
                ANO LETIVO <?php echo $row_AnoLetivo['ano_letivo_ano']; ?>
              </a>
            </li>

            <?php do { ?>
              <li>
                <a href="rel_quantidade_alunos_escola.php?ano=<?php echo $row_Ano['ano_letivo_ano']?><?php 
                    if(isset($_GET['etapaDe'])) echo '&etapaDe='.$_GET['etapaDe']; 
                    if(isset($_GET['etapaAte'])) echo '&etapaAte='.$_GET['etapaAte']; 
                ?>" target="" title="Diários">
                  ANO LETIVO <?php echo $row_Ano['ano_letivo_ano']; ?>
                </a>
              </li>
            <?php } while ($row_Ano = mysql_fetch_assoc($Ano)); ?>

          </ul>
        </div>
      </div>

      <div class="ls-box-filter">

        <form method="GET" action="rel_quantidade_alunos_escola.php" class="ls-form ls-form-horizontal row">

        <?php if(isset($_GET['ano'])) { ?>
      <input type="hidden" name="ano" value="<?php echo htmlspecialchars($_GET['ano']); ?>">
    <?php } ?>

  <label class="ls-label col-md-5 col-xs-12">
    <b class="ls-label-text">De</b>
    <p class="ls-label-info">Selecione a primeira etapa</p>
    <div class="ls-custom-select">
      <select name="etapaDe" class="ls-select">
        <option value="todas" <?php if ($etapaDe == 99) { echo " selected"; } ?>>TODAS</option>
        <?php do { ?>
          <option value="<?php echo $row_EtapasDe['etapa_id']; ?>" 
            <?php if ($etapaDe == $row_EtapasDe['etapa_id']) { echo " selected"; } ?>>
              <?php echo $row_EtapasDe['etapa_nome']; ?>
          </option>
        <?php } while ($row_EtapasDe = mysql_fetch_assoc($EtapasDe)); ?>
      </select>
    </div>
  </label>

  <label class="ls-label col-md-5 col-xs-12">
    <b class="ls-label-text">Até</b>
    <p class="ls-label-info">Selecione a última</p>
    <div class="ls-custom-select">
      <select name="etapaAte" class="ls-select">
        <option value="todas" <?php if ($etapaAte == 99) { echo " selected"; } ?>>TODAS</option>
        <?php do { ?>
          <option value="<?php echo $row_EtapasAte['etapa_id']; ?>" 
            <?php if ($etapaAte == $row_EtapasAte['etapa_id']) { echo " selected"; } ?>>
              <?php echo $row_EtapasAte['etapa_nome']; ?>
          </option>
        <?php } while ($row_EtapasAte = mysql_fetch_assoc($EtapasAc)); ?>
      </select>
    </div>
  </label>

  <div class="col-md-2">
    <input type="submit" value="Buscar" class="ls-btn" title="Buscar">
  </div>
</form>

  </div>    

    <div class="ls-txt-right">
      <a class="ls-btn ls-ico-windows" href="impressao/rel_quantidade_alunos_escola.php?etapaDe=<?php echo $etapaDe; ?>&etapaAte=<?php echo $etapaAte; ?><?php if(isset($_GET['ano'])){echo "&ano=".$_GET['ano'];} ?>" target="_blank"> Imprimir</a>
    </div> 


  <hr>
  <?php $totalAlunosMunicipio = 0; ?>
  <?php do { ?>
    <?php
// Verificar se o usuário selecionou "TODAS" ou se não há parâmetros
    if ($etapaDe === null || $etapaAte === null || ($etapaDe == "todas" || $etapaAte == "todas")) {
    // Se ambos são 99 ou não há parâmetros, seleciona todas as etapas
    $etapasStr = ''; // Não adicionar cláusula WHERE com etapas
  } elseif ($etapaAte < $etapaDe) {
    // Verificar se etapaAte é menor que etapaDe
    echo '<div class="ls-alert-warning"><strong>Atenção:</strong> A primeira etapa selecionada precisa ter um valor menor que a segunda etapa selecionada.</div>';
    $etapasStr = '';
    die();
  } else {
    // Buscar etapas selecionadas dentro do intervalo
    $etapasSelecionadas = buscarEtapasNoIntervalo($etapaDe, $etapaAte);
    $etapasStr = implode(",", $etapasSelecionadas);
  }

// Construir a consulta SQL
  $query_ContagemAlunosPorEtapa = "
  SELECT 
  etapa_id, 
  etapa_nome, 
  etapa, 
  COUNT(*) AS quantidade_alunos 
  FROM (
    SELECT 
    CASE 
    WHEN vinculo_aluno_multietapa IS NOT NULL AND vinculo_aluno_multietapa <> '' 
    THEN vinculo_aluno_multietapa 
    ELSE turma_etapa 
    END AS etapa
    FROM smc_vinculo_aluno
    INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
    INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
    WHERE vinculo_aluno_situacao = '1' 
    AND vinculo_aluno_ano_letivo = {$anoLetivo} 
    AND turma_tipo_atendimento = '1'
    AND vinculo_aluno_id_escola = '{$row_Escolas['escola_id']}'
    " . ($etapasStr ? "AND (turma_etapa IN ($etapasStr) OR vinculo_aluno_multietapa IN ($etapasStr))" : "") . "
    ) AS subquery
  INNER JOIN smc_etapa ON etapa_id = subquery.etapa
  GROUP BY etapa_id
  ";

  $ContagemAlunosPorEtapa = mysql_query($query_ContagemAlunosPorEtapa, $SmecelNovo) or die(mysql_error());

  ?>

  <br><br>
  <h3 class="ls-txt-center"><?php echo $row_Escolas['escola_nome']; ?></h3>
  <br>

  <?php if (mysql_num_rows($ContagemAlunosPorEtapa) > 0) { ?>
    <table class="ls-table" width="100%">
      <thead>
        <tr>
          <th class="ls-txt-center">ETAPA</th>
          <th class="ls-txt-center">QUANTIDADE DE ALUNOS</th>
        </tr>
      </thead>
      <tbody>
        <?php 
        $totalAlunosEscola = 0;
        while ($row_ContagemAlunosPorEtapa = mysql_fetch_assoc($ContagemAlunosPorEtapa)) { ?>
          <tr>
            <td class="ls-txt-center"><?php echo $row_ContagemAlunosPorEtapa['etapa_nome']; ?></td>
            <td class="ls-txt-center"><?php echo $row_ContagemAlunosPorEtapa['quantidade_alunos']; ?></td>
          </tr>
          <?php 
          $totalAlunosEscola+=$row_ContagemAlunosPorEtapa['quantidade_alunos'];
          $totalAlunosMunicipio+=$row_ContagemAlunosPorEtapa['quantidade_alunos'];
        } 
        
        ?>
        <tr><td colspan="2">Quantidade de alunos na escola: <?php echo $totalAlunosEscola ?></td></tr>
        
      </tbody>

    </table>
  <?php } else { ?>
    <hr>
    <div class="ls-alert-warning">
      Nenhuma informação encontrada.
    </div>
  <?php } ?>

<?php } while ($row_Escolas = mysql_fetch_assoc($Escolas)); ?>    
<div class="ls-box">Quantidade de alunos no município: <?php echo $totalAlunosMunicipio ?></div>


<p>&nbsp;</p>
<!-- CONTEUDO -->    
</div>
</main>
<?php include_once "notificacoes.php"; ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>

</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

mysql_free_result($Escolas);
?>