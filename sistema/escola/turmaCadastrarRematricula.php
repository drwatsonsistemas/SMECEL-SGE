<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "../funcoes/funcoes.php"; ?>
<?php //include "fnc/anoLetivo.php"; ?>
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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
	if ($row_UsuLogado['usu_insert']=="N") {
		header(sprintf("Location: turmaListar.php?permissao"));
		die;
	}
	
  $insertSQL = sprintf("INSERT INTO smc_turma (turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_ano_letivo, turma_multisseriada, turma_tipo_atendimento, turma_total_alunos) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['turma_id_escola'], "int"),
                       GetSQLValueString($_POST['turma_id_sec'], "int"),
                       GetSQLValueString($_POST['turma_matriz_id'], "int"),
                       GetSQLValueString($_POST['turma_nome'], "text"),
                       GetSQLValueString($_POST['turma_etapa'], "int"),
                       GetSQLValueString($_POST['turma_turno'], "int"),
                       GetSQLValueString($_POST['turma_ano_letivo']+1, "text"),
                       GetSQLValueString(isset($_POST['turma_multisseriada']) ? "true" : "", "defined","'1'","'0'"),
                       GetSQLValueString($_POST['turma_tipo_atendimento'], "text"),
                       GetSQLValueString($_POST['turma_total_alunos'], "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());
  
// ** REGISTRO DE LOG DE USUÁRIO **
$usu = $_POST['usu_id'];
$esc = $_POST['usu_escola'];
$detalhes = $_POST['detalhes'];
$turma = $_POST['turma_nome'];
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
'20', 
'($turma)', 
'$dat')
";
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$Result1 = mysql_query($sql, $SmecelNovo) or die(mysql_error());
// ** REGISTRO DE LOG DE USUÁRIO **
  

  $insertGoTo = "turmaListar.php?cadastrado";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Etapas = "SELECT etapa_id, etapa_id_filtro, etapa_nome FROM smc_etapa ORDER BY etapa_id ASC";
$Etapas = mysql_query($query_Etapas, $SmecelNovo) or die(mysql_error());
$row_Etapas = mysql_fetch_assoc($Etapas);
$totalRows_Etapas = mysql_num_rows($Etapas);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matriz = "SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash, matriz_ativa FROM smc_matriz WHERE matriz_ativa = 'S' AND matriz_id_secretaria = $row_EscolaLogada[escola_id_sec] ORDER BY matriz_id_etapa, matriz_nome ASC";
$Matriz = mysql_query($query_Matriz, $SmecelNovo) or die(mysql_error());
$row_Matriz = mysql_fetch_assoc($Matriz);
$totalRows_Matriz = mysql_num_rows($Matriz);



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
 
      <h1 class="ls-title-intro ls-ico-home">Cadastrar Turma para o Ano Letivo <?php echo $row_AnoLetivo['ano_letivo_ano']+1; ?> <strong class="ls-color-warning"> Rematrícula</strong></h1>
		
		        <?php if (isset($_GET["nova"])) { ?>
                <div class="ls-alert-warning ls-dismissable">
                  <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
                  CADASTRE AS TURMAS ANTES DE VINCULAR OS ALUNOS.
                </div>
              <?php } ?>
			  
			  
			  	<?php if ($totalRows_Matriz == 0) { ?>
                <div class="ls-alert-warning ls-dismissable">
                  <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
                  Não existe nenhuma Grade Curricular cadastrada pela Secretaria de Educação. Você poderá inserir uma nova turma, mas será necessário editar a turma e informar uma Grade Curricular posteriormente.
                </div>
              <?php } ?>
			  
		
        <form method="post" class="ls-form-horizontal" name="form1" action="<?php echo $editFormAction; ?>" data-ls-module="form">
        
        <fieldset>
        
        
            <label class="ls-label col-md-12">
              <b class="ls-label-text ls-sm-margin-top">NOME DA TURMA</b><br>
	          <p class="ls-label-info">Ex.: 5º ANO A MAT</p>        
              <input type="text" name="turma_nome" value="" size="32" required>
            </label>

            <label class="ls-label col-md-6 col-xs-12">
              <b class="ls-label-text">ETAPA</b><br>
              <p class="ls-label-info">Modalidade/Etapa</p>
		      <div class="ls-custom-select">
              <select name="turma_etapa" id="turma_etapa" class="ls-select" required>
                <option value="">Escolha...</option>
                <option value="0" >Não se aplica</option>
                <?php do { ?>
                <option value="<?php echo $row_Etapas['etapa_id']?>" ><?php echo $row_Etapas['etapa_nome']?></option>
                <?php } while ($row_Etapas = mysql_fetch_assoc($Etapas)); ?>
              </select>     
              </div>  
            </label>
            
             <label class="ls-label col-md-6 col-xs-12">
              <b class="ls-label-text">MATRIZ CURRICULAR</b><br>
              <p class="ls-label-info">Escolha a Matriz da turma</p>
		      <div class="ls-custom-select">
              <select name="turma_matriz_id" class="ls-select">
                <option value="">Escolha...</option>
                <option value="0" >Não se aplica</option>
				<?php if ($totalRows_Matriz > 0) { ?>
                <?php do { ?>
                <option value="<?php echo $row_Matriz['matriz_id']?>" ><?php echo $row_Matriz['matriz_nome']?></option>
                <?php } while ($row_Matriz = mysql_fetch_assoc($Matriz)); ?>
				<?php } ?>
			  </select>     
              </div>  
            </label>


            <label class="ls-label col-md-7">
              <b class="ls-label-text">TURNO</b><br>
              <p class="ls-label-info">Escolha o turno que a turma funciona</p>
		      <div class="ls-custom-select">
              <select name="turma_turno" class="ls-select" required>
                <option value="" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>Escolha...</option>
                <option value="0" <?php if (!(strcmp(0, ""))) {echo "SELECTED";} ?>>Integral</option>               
				<option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>Matutino</option>
                <option value="2" <?php if (!(strcmp(2, ""))) {echo "SELECTED";} ?>>Vespertino</option>
                <option value="3" <?php if (!(strcmp(3, ""))) {echo "SELECTED";} ?>>Noturno</option>
              </select> 
              </div>      
            </label>

            <label class="ls-label col-md-5">
              <b class="ls-label-text">LIMITE DE ALUNOS NA TURMA</b><br>
              <p class="ls-label-info">Informe o limite de alunos que a turma/sala comporta</p>
              <input type="number" name="turma_total_alunos" value="" size="12" min="0" max="100">        
            </label>

          
          <label class="ls-label col-md-12 col-sm-12">
              <b class="ls-label-text">TIPO DE ATENDIMENTO</b><br>
              <p class="ls-label-info">Escolha o tipo de atendimento da turma</p>
		      <div class="ls-custom-select">
              <select name="turma_tipo_atendimento" class="ls-select" required>
                <option value="1">1 - ESCOLARIZAÇÃO</option>
                <option value="2">2 - ATENDIMENTO EDUCACIONAL ESPECIALIZADO (AEE)</option>               
				<option value="3">3 - ATIVIDADE COMPLEMENTAR</option>
              </select> 
              </div>      
            </label>

            <label class="ls-label col-md-12 col-sm-12">
          <b class="ls-label-text">Turma Multisseriada</b>
          <br><p class="ls-label-info">
          <div data-ls-module="switchButton" class="ls-switch-btn">
          <input type="checkbox" name="turma_multisseriada" id="turma_multisseriada" value="">
          <label class="ls-switch-label" for="turma_multisseriada" name="label-teste" ls-switch-off="Não" ls-switch-on="Sim"><span></span></label>
          </div>
          </p>
		  </label>


            
            <hr>

              <div class="col-md-12">
			  <input type="submit" value="CADASTRAR TURMA" class="ls-btn-primary">        
              <a class="ls-btn-dark" href="turmaListar.php">CANCELAR</a>            
			  </div>

              
              </fieldset>

          <input type="hidden" name="turma_id_escola" value="<?php echo $row_EscolaLogada['escola_id']; ?>">
          <input type="hidden" name="turma_ano_letivo" value="<?php echo $row_AnoLetivo['ano_letivo_ano']; ?>">		  
   	   	    <input type="hidden" name="usu_id" value="<?php echo $row_UsuLogado['usu_id']; ?>">
   	   	    <input type="hidden" name="turma_id_sec" value="<?php echo $row_EscolaLogada['escola_id_sec']; ?>">
            <input type="hidden" name="usu_escola" value="<?php echo $row_UsuLogado['usu_escola']; ?>">
			<input type="hidden" name="detalhes" value="">
          <input type="hidden" name="MM_insert" value="form1">

		  
        </form>
		
		
		
        <p>&nbsp;</p>
<p></p>
        <hr>
        <h6 class="ls-title-5"></h6>
        <p></p>

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
          <li class="ls-txt-center hidden-xs">
            <a href="tutoriais_video.php" class="ls-btn-dark ls-btn-tour">Tutorial (Vídeos)</a>
          </li>
          <li><a href="#">&gt; Guia</a></li>
          <li><a href="#">&gt; Wiki</a></li>
        </ul>
      </nav>
    </aside>

    <!-- We recommended use jQuery 1.10 or up -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="js/locastyle.js"></script>
 
    <script type="text/javascript" src="../js/jquery.mask.min.js"></script>
    	  <script>
    jQuery(document).ready(function ($) {
      // Chamada da funcao upperText(); ao carregar a pagina
      upperText();
      // Funcao que faz o texto ficar em uppercase
      function upperText() {
        // Para tratar o colar
        $("input").bind('paste', function (e) {
          var el = $(this);
          setTimeout(function () {
            var text = $(el).val();
            el.val(text.toUpperCase());
          }, 100);
        });

        // Para tratar quando é digitado
        $("input").keypress(function () {
          var el = $(this);
          setTimeout(function () {
            var text = $(el).val();
            el.val(text.toUpperCase());
          }, 100);
        });
      }

      // Monitora o input do nome da turma para palavras relacionadas ao AEE
      $('input[name="turma_nome"]').on('input', function() {
        var texto = $(this).val().toLowerCase();
        var palavrasAEE = ['aee', 'atendimento', 'atendimento educacional', 'atendimento educacional especializado'];
        
        if (palavrasAEE.some(palavra => texto.includes(palavra))) {
          // Cria o toast usando o LocaStyle
          var toast = $('<div class="ls-alert-info ls-dismissable ">' +
            '<span data-ls-module="dismiss" class="ls-dismiss">&times;</span>' +
            'Lembre-se de selecionar o tipo de atendimento como "ATENDIMENTO EDUCACIONAL ESPECIALIZADO (AEE)" no campo abaixo.' +
            '</div>');
          
          // Insere o toast após o input do nome da turma
          $(this).closest('.ls-label').after(toast);
          
          // Remove o toast após 5 segundos
          setTimeout(function() {
            toast.fadeOut(function() {
              $(this).remove();
            });
          }, 5000);
        }
      });

      // Controle do select de etapas baseado no checkbox de turma multisseriada
      $('#turma_multisseriada').change(function() {
        var etapaSelect = $('#turma_etapa');
        var options = etapaSelect.find('option');
        
        if ($(this).is(':checked')) {
          // Se multisseriada estiver ativa, só permite etapas 23 e 25
          options.each(function() {
            var value = $(this).val();
            if (value === '' || value === '0' || value === '23' || value === '25') {
              $(this).show();
            } else {
              $(this).hide();
            }
          });
          // Reseta a seleção
          etapaSelect.val('');
        } else {
          // Se multisseriada estiver inativa, mostra todas as opções
          options.show();
          // Reseta a seleção
          etapaSelect.val('');
        }
      });
    });
       </script>
       
        <script language="javascript">
        function noTilde(objResp) {
        var varString = new String(objResp.value);
        var stringAcentos = new String('àâêôûãõáéíóúçüÀÂÊÔÛÃÕÁÉÍÓÚÇÜ[]');
        var stringSemAcento = new String('aaeouaoaeioucuAAEOUAOAEIOUCU');
        
        var i = new Number();
        var j = new Number();
        var cString = new String();
        var varRes = "";
        
          for (i = 0; i < varString.length; i++) {
            cString = varString.substring(i, i + 1);
              for (j = 0; j < stringAcentos.length; j++) {
              if (stringAcentos.substring(j, j + 1) == cString){
              cString = stringSemAcento.substring(j, j + 1);
              }
            }
            varRes += cString;
          }
          objResp.value = varRes;
          }
        $(function() {
            $("input:text").keyup(function() {
        noTilde(this);
        });
        });
      </script>


  </body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($Etapas);

mysql_free_result($Matriz);

mysql_free_result($AnoLetivo);
?>
