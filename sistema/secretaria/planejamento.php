<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php require_once('funcoes/anti_injection.php'); ?>
<?php include('../funcoes/inverteData.php'); ?>
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
$query_Escola = "SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_ue, escola_situacao, escola_localizacao, escola_ibge_municipio, escola_tema, escola_unidade_executora, escola_caixa_ux_prestacao_contas FROM smc_escola WHERE escola_id_sec = '$row_UsuarioLogado[usu_sec]' AND escola_situacao = '1' AND escola_ue = '1'";
$Escola = mysql_query($query_Escola, $SmecelNovo) or die(mysql_error());
$row_Escola = mysql_fetch_assoc($Escola);
$totalRows_Escola = mysql_num_rows($Escola);


$colname_Escola = "-1";
if (isset($_GET['escola'])) {
  $colname_Escola = (int)anti_injection($_GET['escola']);
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Ac = "
SELECT 

ac_id, ac_id_professor, ac_id_etapa, ac_id_componente, ac_id_escola, ac_ano_letivo, ac_data_inicial, ac_data_final, ac_conteudo, 
ac_objetivo_especifico, ac_objeto_conhecimento, ac_metodologia, ac_recursos, ac_avaliacao, ac_criacao,

ac_da_conviver,
ac_da_brincar,
ac_da_participar,
ac_da_explorar,
ac_da_expressar,
ac_da_conhecerse,


ac_ce_eo,
ac_ce_ts,
ac_ce_ef,
ac_ce_cg,
ac_ce_et,
ac_ce_di,
ac_unid_tematica,
func_id, func_nome, disciplina_id, disciplina_nome, etapa_id, etapa_nome, escola_id, escola_id_sec, escola_nome 
FROM smc_ac
LEFT JOIN smc_func ON func_id = ac_id_professor
LEFT JOIN smc_disciplina ON disciplina_id = ac_id_componente 
LEFT JOIN smc_etapa ON etapa_id = ac_id_etapa
LEFT JOIN smc_escola ON escola_id = ac_id_escola




WHERE ac_id_escola = '$colname_Escola' AND escola_id_sec = '$row_UsuarioLogado[usu_sec]'
ORDER BY ac_id DESC
";
$Ac = mysql_query($query_Ac, $SmecelNovo) or die(mysql_error());
$row_Ac = mysql_fetch_assoc($Ac);
$totalRows_Ac = mysql_num_rows($Ac);

$nomeMenu = "ESCOLHA UMA ESCOLA";
if (isset($_GET['escola']) && $totalRows_Ac > 0) {
  $nomeMenu = $row_Ac['escola_nome'];
}

$query_ac_label = "SELECT * FROM smc_ac_label
WHERE ac_id_ac = '$row_Ac[ac_id]'";
$ac_label = mysql_query($query_ac_label, $SmecelNovo) or die(mysql_error());
$rowAcLabel = mysql_fetch_assoc($ac_label);
echo $TotalrowAcLabel = mysql_num_rows($ac_label);

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
      <h1 class="ls-title-intro ls-ico-home">PLANEJAMENTO</h1>
      <!-- CONTEUDO -->


      <div data-ls-module="dropdown" class="ls-dropdown">
        <a href="#" class="ls-btn-primary"><?php echo $nomeMenu; ?></a>
        <ul class="ls-dropdown-nav">
         <?php do { ?>
          <li><a href="planejamento.php?escola=<?php echo $row_Escola['escola_id']; ?>"><?php echo $row_Escola['escola_nome']; ?></a></li>
        <?php } while ($row_Escola = mysql_fetch_assoc($Escola)); ?>
      </ul>
    </div>  



    <hr>
    <h1 id="status"></h1>
    <?php if ($totalRows_Ac > 0) { // Show if recordset not empty ?>
      <table class="ls-table ls-sm-space">
        <thead>
          <tr>
            <th>PROFESSOR</th>
            <th class="ls-txt-center">COMPONENTE</th>
            <th class="ls-txt-center">ETAPA</th>
            <th class="ls-txt-center" width="100">ANO</th>
            <th class="ls-txt-center" width="120">DATA INICIAL</th>
            <th class="ls-txt-center" width="120">DATA FINAL</th>
            <th class="ls-txt-center" width="120">DIAS</th>
            <th class="ls-txt-center">CADASTRO</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
          </tr>
        </thead>
        <tbody>
          <?php 

          $trocar = array("\"", "\'","'");

          do { 

            $query_ac_label = "SELECT * FROM smc_ac_label
            WHERE ac_id_ac = '$row_Ac[ac_id]'";
            $ac_label = mysql_query($query_ac_label, $SmecelNovo) or die(mysql_error());
            $rowAcLabel = mysql_fetch_assoc($ac_label);
            $TotalrowAcLabel = mysql_num_rows($ac_label);

            ?>
            <tr id="linha-<?php echo $row_Ac['ac_id']; ?>">
              <td><a href="ac-professor.php?codigo=<?php echo $row_Ac['func_id']; ?>"><?php echo $row_Ac['func_nome']; ?></a></td>
              <td class="ls-txt-center"><?php echo $row_Ac['disciplina_nome']; ?></td>
              <td class="ls-txt-center"><?php echo $row_Ac['etapa_nome']; ?></td>
              <td class="ls-txt-center"><?php echo $row_Ac['ac_ano_letivo']; ?></td>
              <td class="ls-txt-center"><?php echo inverteData($row_Ac['ac_data_inicial']); ?></td>
              <td class="ls-txt-center"><?php echo inverteData($row_Ac['ac_data_final']); ?></td>
              <td class="ls-txt-center"><?php $diferenca = strtotime($row_Ac['ac_data_final']) - strtotime($row_Ac['ac_data_inicial']); echo $dias = floor($diferenca / (60 * 60 * 24))+1; ?></td>
              <td class="ls-txt-center"><?php echo date("d/m/Y - H:i", strtotime($row_Ac['ac_criacao'])); ?></td>
              <td class="ls-txt-center">

                <button data-ls-module="modal" data-action="" data-content="

                <?php if ($TotalrowAcLabel == 0) { ?>
                  <?php if ($row_Ac['ac_unid_tematica']<>"") { ?>
                    <div class='ls-box'>
                      <h4>UNIDADE TEMÁTICA</h4>
                      <p><?php echo str_replace($trocar, "", $row_Ac['ac_unid_tematica']); ?></p>
                    </div>
                  <?php } ?>
                  <?php if ($row_Ac['ac_objetivo_especifico']<>"") { ?>
                    <div class='ls-box'>
                      <h4>OBJETIVOS DE APRENDIZAGEM E DESENVOLVIMENTO</h4>
                      <p><?php echo str_replace($trocar, "", $row_Ac['ac_objetivo_especifico']); ?></p>
                    </div>
                  <?php } ?>

                  <?php if ($row_Ac['ac_objeto_conhecimento']<>"") { ?>
                    <div class='ls-box'>
                      <h4>OBJETOS DE CONHECIMENTO/SABERES E CONHECIMENTO/CONTEÚDO</h4>
                      <p><?php echo str_replace($trocar, "", $row_Ac['ac_objeto_conhecimento']); ?></p>
                    </div>
                  <?php } ?>

                  <?php if ($row_Ac['ac_recursos']<>"") { ?>
                    <div class='ls-box'>
                      <h4>HABILIDADES</h4>
                      <p><?php echo str_replace($trocar, "", $row_Ac['ac_recursos']); ?></p>
                    </div>
                  <?php } ?>

                  <?php if ($row_Ac['ac_metodologia']<>"") { ?>
                    <div class='ls-box'>
                      <h4>METODOLOGIA</h4>
                      <p><?php echo str_replace($trocar, "", $row_Ac['ac_metodologia']); ?></p>
                    </div>
                  <?php } ?>

                  <?php if ($row_Ac['ac_avaliacao']<>"") { ?>
                    <div class='ls-box'>
                      <h4>AVALIAÇÃO</h4>
                      <p><?php echo str_replace($trocar, "", $row_Ac['ac_avaliacao']); ?></p>
                    </div>
                  <?php } ?>

                  <?php if ($row_Ac['ac_conteudo']<>"") { ?>
                    <div class='ls-box'>
                      <h4>OBSERVAÇÃO/RECURSOS</h4>
                      <p><?php echo str_replace($trocar, "", $row_Ac['ac_conteudo']); ?></p>
                    </div>
                  <?php } ?>
                <?php }else{

                  do{ 
                    $titulo = '';
                    switch ($rowAcLabel['ac_id_tipo']) {
                      case '1':
                      $titulo = "Unidade temática";
                      break;
                      case '2':
                      $titulo = "Objetivos de Aprendizagem e desenvolvimento";
                      break;
                      case '3':
                      $titulo = "Objetos de conhecimento/saberes e conhecimento/conteúdo";
                      break;
                      case '4':
                      $titulo = "Habilidades";
                      break;
                      case '5':
                      $titulo = "Metodologia";
                      break;
                      case '6':
                      $titulo = "Avaliação";
                      break;
                      case '7':
                      $titulo = "Observação";
                      break;
                      case '8':
                      $titulo = "Recursos";
                      break;
                      default:
                      $titulo = "";
                      break;
                    }

                    ?>


                    <div class='ls-box'>
                      <h4><?= $titulo ?></h4>
                      <p><?php echo str_replace($trocar, "", $rowAcLabel['ac_conteudo']); ?></p>
                    </div>

                  <?php }
                  while($rowAcLabel = mysql_fetch_assoc($ac_label)); 

                } ?>



                <?php if (
                  $row_Ac['ac_da_conviver']=="S" || 
                  $row_Ac['ac_da_brincar']=="S" || 
                  $row_Ac['ac_da_participar']=="S" || 
                  $row_Ac['ac_da_explorar']=="S" || 
                  $row_Ac['ac_da_expressar']=="S" || 
                  $row_Ac['ac_da_conhecerse']=="S" 
                ) { ?>   
                  <div class='ls-box'>
                    <h4>DIREITOS DE APRENDIZAGEM</h4>
                    <hr>
                    <p>

                      <label>
                        <input type='checkbox' disabled='disabled' <?php if ($row_Ac['ac_da_conviver']=="S") { echo "checked"; } ?> />
                        <span>Conviver</span> 
                      </label>
                      <label>
                        <input type='checkbox' disabled='disabled' <?php if ($row_Ac['ac_da_brincar']=="S") { echo "checked"; } ?> />
                        <span>Brincar</span> 
                      </label>
                      <label>
                        <input type='checkbox' disabled='disabled' <?php if ($row_Ac['ac_da_participar']=="S") { echo "checked"; } ?> />
                        <span>Participar</span> 
                      </label>
                      <label>
                        <input type='checkbox' disabled='disabled' <?php if ($row_Ac['ac_da_explorar']=="S") { echo "checked"; } ?> />
                        <span>Explorar</span> 
                      </label>
                      <label>
                        <input type='checkbox' disabled='disabled' <?php if ($row_Ac['ac_da_expressar']=="S") { echo "checked"; } ?> />
                        <span>Expressar</span> 
                      </label>
                      <label>
                        <input type='checkbox' disabled='disabled' <?php if ($row_Ac['ac_da_conhecerse']=="S") { echo "checked"; } ?> />
                        <span>Conhecer-se</span> 
                      </label>
                    </p>

                  </div>    
                <?php } ?>  







                <?php if ($row_Ac['ac_ce_eo']<>"") { ?> 
                  <div class='ls-box'>
                    <h4>EO – O eu, o outro e o nós</h4>
                    <p><?php echo str_replace($trocar, "", $row_Ac['ac_ce_eo']); ?></p>
                  </div>
                <?php } ?>

                <?php if ($row_Ac['ac_ce_ts']<>"") { ?>
                  <div class='ls-box'>
                    <h4>TS – Traços, sons, cores e formas</h4>
                    <p><?php echo str_replace($trocar, "", $row_Ac['ac_ce_ts']); ?></p>
                  </div>
                <?php } ?>

                <?php if ($row_Ac['ac_ce_ef']<>"") { ?>
                  <div class='ls-box'>
                    <h4>EF – Escuta, fala, pensamento e imaginação</h4>
                    <p><?php echo str_replace($trocar, "", $row_Ac['ac_ce_ef']); ?></p>
                  </div>
                <?php } ?>

                <?php if ($row_Ac['ac_ce_cg']<>"") { ?>
                  <div class='ls-box'>
                    <h4>CG – Corpo, gestos e movimento</h4>
                    <p><?php echo str_replace($trocar, "", $row_Ac['ac_ce_cg']); ?></p>
                  </div>
                <?php } ?>

                <?php if ($row_Ac['ac_ce_et']<>"") { ?>
                  <div class='ls-box'>
                    <h4>ET – Espaços, tempos, quantidades, relações e transformações</h4>
                    <p><?php echo str_replace($trocar, "", $row_Ac['ac_ce_et']); ?></p>
                  </div>
                <?php } ?>

                <?php if ($row_Ac['ac_ce_di']<>"") { ?>
                  <div class='ls-box'>
                    <h4>ET – Espaços, tempos, quantidades, relações e transformações</h4>
                    <p><?php echo str_replace($trocar, "", $row_Ac['ac_ce_di']); ?></p>
                  </div>
                <?php } ?>









                " data-title="<?php echo $row_Ac['func_nome']; ?> - Componente: <?php echo $row_Ac['disciplina_nome']; ?> - Etapa: <?php echo $row_Ac['etapa_nome']; ?>" data-class="ls-btn-danger" data-save="" data-close="Fechar" class="ls-btn-primary"> Ver planejamento </button>


              </td>
              <td>
                <a href="#" id="<?php echo $row_Ac['ac_id']; ?>" prof="<?php echo $row_Ac['func_id']; ?>" class="ls-btn-danger ls-ico-remove deletar"></a>
              </td>
            </tr>
          <?php } while ($row_Ac = mysql_fetch_assoc($Ac)); ?>
        </tbody>
      </table>
    <?php } else { ?>

      <p>Nenhum planejamento cadastrado</p>

    <?php } // Show if recordset not empty ?>


    <p>&nbsp;</p>
    <!-- CONTEUDO -->    
  </div>
</main>
<?php include_once "notificacoes.php"; ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $(".deletar").on('click', function() {


      var id    = $(this).attr('id');
      var prof  = $(this).attr('prof');


      Swal.fire({
        title: 'Deletar este planejamento?',
        text: "Esta ação não poderá ser desfeita.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sim, deletar!'
      }).then((result) => {
        if (result.isConfirmed) {


          $.ajax({
            type : 'POST',
            url  : 'funcoes/deletar_planejamento.php',
            data : {
              id        :id,
              prof      :prof
            },
            success:function(data){

              $("#linha-"+id).remove();

              $('#status').html(data);

              setTimeout(function(){




            //location.reload();          
              },2000);

            }
          })

          return true;  







        }
      })


    });
  });
</script>
</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

mysql_free_result($Escola);

mysql_free_result($Ac);
?>