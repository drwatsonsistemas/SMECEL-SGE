<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "../funcoes/funcoes.php"; ?>
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
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_tema,  sec_id, sec_cidade, sec_uf 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);


$nomeBusca = "-1";
if (isset($_GET['nome'])) {
  $nomeBusca = $_GET['nome'];
}

$nascimentoBusca = "-1";
if (isset($_GET['nascimento'])) {
  $nascimentoBusca = converteData($_GET['nascimento']);
}

$maeBusca = "-1";
if (isset($_GET['mae'])) {	
  $maeBusca = $_GET['mae'];
}


if($nomeBusca != ""){ $nome_no_where = "AND aluno_nome LIKE '%".$nomeBusca."%'"; } else { $nome_no_where = ""; }
if($nascimentoBusca != ""){ $nascimento_no_where = "AND aluno_nascimento LIKE '%".$nascimentoBusca."%'"; } else { $nascimento_no_where = ""; }
if($maeBusca != ""){ $mae_no_where = "AND aluno_filiacao1 LIKE '%".$maeBusca."%'"; } else { $mae_no_where = ""; }


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_buscaFuncionario = "
SELECT
aluno_id,
aluno_cod_inep,
aluno_cpf,
aluno_nome,
aluno_nascimento,
aluno_filiacao1,
aluno_filiacao2,
aluno_sexo,
aluno_raca,
aluno_nacionalidade,
aluno_uf_nascimento,
aluno_municipio_nascimento,
aluno_aluno_com_deficiencia,
aluno_nis,
aluno_identidade,
aluno_emissor,
aluno_uf_emissor,
aluno_data_espedicao,
aluno_tipo_certidao,
aluno_termo,
aluno_folhas,
aluno_livro,
aluno_emissao_certidao,
aluno_uf_cartorio,
aluno_mucicipio_cartorio,
aluno_nome_cartorio,
aluno_num_matricula_modelo_novo,
aluno_localizacao,
aluno_cep,
aluno_endereco,
aluno_numero,
aluno_complemento,
aluno_bairro,
aluno_uf,
aluno_municipio,
aluno_telefone,
aluno_celular,
aluno_email,
aluno_hash
FROM 
  smc_aluno
WHERE
aluno_id <> 0
  $nome_no_where 
  $nascimento_no_where 
  $mae_no_where 
  ORDER BY aluno_nome ASC
";
$buscaFuncionario = mysql_query($query_buscaFuncionario, $SmecelNovo) or die(mysql_error());
$row_buscaFuncionario = mysql_fetch_assoc($buscaFuncionario);
$totalRows_buscaFuncionario = mysql_num_rows($buscaFuncionario);

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

    <title>Pesquisar Funcionários</title>

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
 
        <h1 class="ls-title-intro ls-ico-users">Pesquisar aluno para rematrícula</h1>
        
                
              <?php if (isset($_GET["nada"])) { ?>
                <div class="ls-alert-danger ls-dismissable">
                  <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
                  <strong>Informações inválidas.</strong> A equipe de suporte já foi comunicada do erro.
                </div>
              <?php } ?>
                
				
				<p>Pesquise o aluno para depois matriculá-lo em uma turma.</p>
				<hr>
				
        <form action="" method="get" class="ls-form ls-form-horizontal row">
          <fieldset>
                <label class="ls-label col-md-5">
            <b class="ls-label-text">NOME DO ALUNO</b>
            <input type="text" class="ls-field-lg" name="nome" pattern=".{4,}" required title="Informe no mínimo 4 caracteres" placeholder="Nome completo ou parte" required>
                </label>
                <label class="ls-label col-md-2">
            <b class="ls-label-text">NASCIMENTO</b>
            <input type="text" class="ls-field-lg date" name="nascimento" placeholder="Data de nascimento">
                </label>
            <label class="ls-label col-md-5">
              <b class="ls-label-text">NOME DA MÃE</b>
                  <input type="text" class="ls-field-lg" name="mae" placeholder="Nome da mãe">
            </label>
          </fieldset>
            
           
              <div class="ls-actions-btn">
                <button class="ls-btn-primary ls-btn-lg ls-ico-search">Pesquisar</button>
                <a href="alunoPesquisar.php" class="ls-btn-danger ls-btn-lg">Limpar</a>
              </div>
        </form>



		
  <?php if (($nomeBusca == "") and ($nascimentoBusca == "") and ($maeBusca == "")) { ?>
  <div class="ls-alert-danger"><strong>Atenção: </strong> Informe um dos campos acima e clique em pesquisar.</div>  
  <?php } else { ?>
  <?php if ($totalRows_buscaFuncionario > 0) { // Show if recordset not empty ?>
  <div class="ls-alert-info ls-dismissable">
    <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
    <strong>Resultado.</strong> Foram encontrados <?php echo $totalRows_buscaFuncionario; ?> registros para esta pesquisa. Confira se todos os campos coincidem com os dados do(a) aluno(a).
  </div>
  <table class="ls-table ls-table-striped">
    <thead>
      <tr>
        <th>Nome</th>
        <th>Nascimento</th>
        <th>Filiação</th>
        <th>Vincular</th>
        </tr>
    </thead>
    <tbody>
      <?php do { ?>
        <tr>
          <td><?php echo $row_buscaFuncionario['aluno_nome']; ?></td>
          <td><?php echo converteData($row_buscaFuncionario['aluno_nascimento']); ?></td>
          <td><?php echo $row_buscaFuncionario['aluno_filiacao1']; ?></td>
          <td><a href="vinculoAlunoCadastrarRematricula.php?c=<?php echo $row_buscaFuncionario['aluno_hash']; ?>" class="ls-ico-user-add"> Rematricular</a></td>
        </tr>
        <?php } while ($row_buscaFuncionario = mysql_fetch_assoc($buscaFuncionario)); ?>
    </tbody>
  </table>
  
  <p>
  <a class="ls-btn-primary ls-ico-windows" href="alunoCadastrar.php">Cadastrar aluno</a>
  </p>
  <hr>
  <?php } else { ?>
  Nenhum resultado.
  <hr>
  <p>
  <a class="ls-btn-alert ls-ico-windows" href="alunoCadastrar.php">Cadastrar aluno</a>
  </p>
  <hr>
  <hr>
  <?php } ?>

  <?php } // Show if recordset not empty ?>
  
      </div>
    </main>
	
	
<span data-ls-module="modal" data-target="#pesquiseAntes"></span>	

<div class="ls-modal" id="pesquiseAntes">
  <div class="ls-modal-box">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title">Cadastro de Aluno</h4>
    </div>
    <div class="ls-modal-body" id="myModalBody">
      <p><strong>Atenção</strong></p>
	  <p>Antes de realizar um novo cadastro, faça uma pesquisa para saber se já existe um registro do aluno.</p>
    </div>
    <div class="ls-modal-footer">
      <a href="alunoPesquisar.php" class="ls-btn ls-float-right" data-dismiss="modal">Fechar</a>
      <a href="alunoPesquisar.php" class="ls-btn-primary" data-dismiss="modal">Pesquisar</a>
    </div>
  </div>
</div><!-- /.modal -->
	
	

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
	<script src="js/mascara.js"></script>

	  <script>
      jQuery(document).ready(function($) {
       // Chamada da funcao upperText(); ao carregar a pagina
       upperText();
       // Funcao que faz o texto ficar em uppercase
       function upperText() {
      // Para tratar o colar
       $("input").bind('paste', function(e) {
       var el = $(this);
       setTimeout(function() {
       var text = $(el).val();
       el.val(text.toUpperCase());
       }, 100);
       });
       
      // Para tratar quando é digitado
       $("input").keypress(function() {
       var el = $(this);
       setTimeout(function() {
       var text = $(el).val();
       el.val(text.toUpperCase());
       }, 100);
       });
       }
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

mysql_free_result($buscaFuncionario);
?>
