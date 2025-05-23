<?php require_once ('../../Connections/SmecelNovo.php'); ?>
<?php include '../funcoes/funcoes.php'; ?>
<?php include "fnc/session.php"; ?>
<?php
if (!function_exists('GetSQLValueString')) {
  function GetSQLValueString($theValue, $theType, $theDefinedValue = '', $theNotDefinedValue = '')
  {
    if (PHP_VERSION < 6) {
      $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
    }

    $theValue = function_exists('mysql_real_escape_string') ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

    switch ($theType) {
      case 'text':
      $theValue = ($theValue != '') ? "'" . $theValue . "'" : 'NULL';
      break;
      case 'long':
      case 'int':
      $theValue = ($theValue != '') ? intval($theValue) : 'NULL';
      break;
      case 'double':
      $theValue = ($theValue != '') ? doubleval($theValue) : 'NULL';
      break;
      case 'date':
      $theValue = ($theValue != '') ? "'" . $theValue . "'" : 'NULL';
      break;
      case 'defined':
      $theValue = ($theValue != '') ? $theDefinedValue : $theNotDefinedValue;
      break;
    }
    return $theValue;
  }
}

include 'usuLogado.php';
include 'fnc/anoLetivo.php';

$situacao = 1;
$texto = ' VINCULADOS';

if (isset($_GET['vinculo'])) {
  $situacao = $_GET['vinculo'];

  if ($situacao == 2) {
    $texto = ' DESVINCULADOS';
  }
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EscolaLogada = "
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, 
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_tema,  sec_id, sec_cidade, sec_uf 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die (mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ListaVinculos = "
SELECT 
vinculo_id, vinculo_id_escola, vinculo_acesso, vinculo_id_funcionario, vinculo_id_funcao, vinculo_carga_horaria, DATE_FORMAT(vinculo_data_inicio, '%d/%m/%Y') AS vinculo_data_inicio, vinculo_obs, 
func_id, func_nome, funcao_id, funcao_nome, func_regime, func_senha_ativa, id_regime, regime_nome
FROM smc_vinculo 
INNER JOIN smc_func 
ON func_id = vinculo_id_funcionario 
INNER JOIN smc_funcao
ON funcao_id = vinculo_id_funcao
INNER JOIN smc_regime ON id_regime = func_regime
WHERE vinculo_id_escola = '$row_EscolaLogada[escola_id]'
AND vinculo_status = $situacao
ORDER BY func_nome ASC
";
$ListaVinculos = mysql_query($query_ListaVinculos, $SmecelNovo) or die (mysql_error());
$row_ListaVinculos = mysql_fetch_assoc($ListaVinculos);
$totalRows_ListaVinculos = mysql_num_rows($ListaVinculos);

// acesso cor

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

  <title>Listar Funcionários</title>

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
  <?php include_once ('menu-top.php'); ?>
  <?php include_once ('menu-esc.php'); ?>


  <main class="ls-main ">
    <div class="container-fluid">

      <h1 class="ls-title-intro ls-ico-home">LISTAR FUNCIONÁRIOS <?= $texto ?></h1>

      <?php
      if (isset($_GET['vinculado'])) {
        $funcionario = $_GET['vinculado'];
        ?>
        <div class="ls-alert-success ls-dismissable">
          <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
          Servidor(a) <strong><?php echo $funcionario; ?></strong> vinculado(a) com sucesso.
        </div>
      <?php } ?>


      <?php if (isset($_GET['senhagerada'])) { ?>
        <div class="ls-alert-success ls-dismissable">
          <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
          Uma nova senha foi enviada para o e-mail informado.
        </div>
      <?php } ?>

      <?php if (isset($_GET['acesso'])) { ?>
        <div class="ls-alert-success ls-dismissable">
          <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
          Acesso atualizado.
        </div>
      <?php } ?>

      <?php if (isset($_GET['excluido'])) { ?>
        <div class="ls-alert-warning ls-dismissable">
          <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
          Vínculo excluído com sucesso.
        </div>
      <?php } ?>

      <?php if (isset($_GET['erro'])) { ?>
        <div class="ls-alert-danger ls-dismissable">
          <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
          Ocorreu um erro na ação anterior. Um e-mail foi enviado ao administrador do sistema.
        </div>
      <?php } ?>

      <?php if (isset($_GET['permissao'])) { ?>
        <div class="ls-alert-danger ls-dismissable">
          <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
          VOCÊ NÃO TEM PERMISSÃO PARA REALIZAR ESTA AÇÃO.
        </div>
      <?php } ?>

      <div class="ls-box">

        <a class="ls-btn-primary ls-ico-user" href="funcPesquisar.php">Vincular funcionário</a>
        <!-- Desativado -->
        <div class="ls-group-btn ls-group-active ls-float-right">
          <a href="funcListar.php?vinculo=1" type="button" class="ls-btn-primary <?php if ($situacao == 1) { echo 'ls-active'; } ?>">Vinculados</a>
          <a href="funcListar.php?vinculo=2" type="button" class="ls-btn-primary <?php if ($situacao == 2) { echo 'ls-active'; } ?>">Desvinculados</a>
        </div>

      </div>		

      <div class="ls-box">

       <label class="ls-label col-md-12">
        <b class="ls-label-text">BUSQUE UM FUNCIONÁRIO</b>
        <input type="text" class="buscar-funcionario" alt="fonte-tabela" placeholder="Digite o nome ou parte do nome de um funcionário" autofocus/>
      </label>



    </div> 




    <div class="ls-box ls-sm-space">

      <?php if ($totalRows_ListaVinculos > 0) { ?>
        <table class="ls-table ls-table-striped ls-sm-space fonte-tabela" role="grid">
          <thead>
            <tr>
              <th width="40px" class="ls-txt-center hidden-xs">Nº</th>
              <th width="20px" class="hidden-xs"></th>
              <th>FUNCIONÁRIO</th>
              <th class="ls-txt-center">FUNÇÃO</th>
              <th class="ls-txt-center hidden-xs">REGIME</th>
              <th class="ls-txt-center hidden-xs" width="70px">C/H</th>
              <th class="ls-txt-center" width="70px">FALTAS</th>
              <th class="ls-txt-center" width="70px"></th>
              <th class="ls-txt-center" width="70px"></th>
            </tr>
          </thead>
          <tbody>
           <?php
           $contagem = 1;
           do {
            ?>
            <tr>
              <td class="ls-txt-center hidden-xs">
               <?php
               echo $contagem;
               $contagem++;

               $acesso_cor = '';
               if ($row_ListaVinculos['vinculo_acesso'] == 'N') {
                $acesso_cor = 'ls-color-danger';
              }else {
                $acesso_cor = 'ls-color-success';
              }
            ?></td>
            <td class="hidden-xs"><?php if ($row_ListaVinculos['func_senha_ativa'] == 1) { ?><i class="ls-ico-mobile <?= $acesso_cor ?>"></i><?php } ?></td>
            <td><a href="funcionarios_detalhes.php?codigo=<?php echo $row_ListaVinculos['vinculo_id']; ?>"><?php echo $row_ListaVinculos['func_nome']; ?></a></td>
            <td class="ls-txt-center"><?php echo $row_ListaVinculos['funcao_nome']; ?></td>
            <td class="ls-txt-center hidden-xs"><?php echo $row_ListaVinculos['regime_nome'] ?></td>
            <td class="ls-txt-center hidden-xs"><?php echo $row_ListaVinculos['vinculo_carga_horaria']; ?></td>
            <td class="ls-txt-center hidden-xs"><a href="faltasFuncionarioCadastrar.php?cod=<?php echo $row_ListaVinculos['vinculo_id']; ?>" class="ls-ico-calendar-more" title="Registro de faltas"></a></td>
            <td class="ls-txt-center">
             <a href="funcionarios_detalhes.php?codigo=<?php echo $row_ListaVinculos['vinculo_id']; ?>" title="Exibir informações do funcionário" class="ls-ico-search">&nbsp</a> 
           </td>
           <td class="ls-txt-center">
            <a href="javascript:func()" onclick="confirmaExclusao('<?php echo $row_ListaVinculos['vinculo_id']; ?>','<?php echo $row_ListaVinculos['func_nome']; ?>')" class="ls-ico-cancel-circle ls-divider ls-color-danger"></a>
          </td>

        </tr>
      <?php } while ($row_ListaVinculos = mysql_fetch_assoc($ListaVinculos)); ?>
    </tbody>
  </table>

</div>

<p class="ls-txt-right">Total: <?php echo $totalRows_ListaVinculos; ?></p>

<?php } else { ?>
  <br>
  <p><div class="ls-alert-info"><strong>Atenção:</strong> Nenhum funcionário vinculado nessa escola.</div></p>
<?php } ?>	

</div>
</main>

<aside class="ls-notification">
  <nav class="ls-notification-list" id="ls-notification-curtain" style="left: 1716px;">
    <h3 class="ls-title-2">Notificações</h3>
    <ul>
      <?php include 'notificacoes.php'; ?>
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

<script type="text/javascript">



</script>

<script language="Javascript">
	function confirmaExclusao(id,nome) {
   var resposta = confirm("Deseja realmente remover o vínculo do(a) colaborador(a) "+nome+" nesta escola?");
   if (resposta == true) {
     window.location.href = "excluirVinculo.php?cod="+id+"&nome="+nome;
   }
 }
</script>
<script type="text/javascript">
  $(function(){
    $(".buscar-funcionario").keyup(function(){
        //pega o css da tabela 
      var tabela = $(this).attr('alt');
      if( $(this).val() != ""){
        $("."+tabela+" tbody>tr").hide();
        $("."+tabela+" td:contains-ci('" + $(this).val() + "')").parent("tr").show();
      } else{
        $("."+tabela+" tbody>tr").show();
      }
    }); 
  });
  $.extend($.expr[":"], {
    "contains-ci": function(elem, i, match, array) {
      return (elem.textContent || elem.innerText || $(elem).text() || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
    }
  });
</script>

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

mysql_free_result($ListaVinculos);
?>
