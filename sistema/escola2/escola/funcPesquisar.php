<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "../funcoes/funcoes.php"; ?>
<?php include "fnc/anti_injection.php"; ?>

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


$nomeBusca = "";
if (isset($_POST['nome'])) {
  $nomeBusca = anti_injection($_POST['nome']);
}

$nascimentoBusca = "";
if (isset($_POST['nascimento'])) {
  $nascimentoBusca = converteData($_POST['nascimento']);
}

$cpfBusca = "";
if (isset($_POST['cpf'])) {	
  $cpfBusca = anti_injection($_POST['cpf']);
}


if($nomeBusca != ""){ $nome_no_where = "AND func_nome LIKE '%".$nomeBusca."%'"; } else { $nome_no_where = ""; }
if($nascimentoBusca != ""){ $nascimento_no_where = "AND func_data_nascimento LIKE '%".$nascimentoBusca."%'"; } else { $nascimento_no_where = ""; }
if($cpfBusca != ""){ $cpf_no_where = "AND func_cpf LIKE '%".$cpfBusca."%'"; } else { $cpf_no_where = ""; }


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_buscaFuncionario = "
SELECT
  func_id, func_id_sec, func_nome, func_mae, func_pai, func_data_nascimento, func_uf_nascimento, 
  func_municipio_nascimento, func_estado_civil, func_sexo, func_escolaridade, func_cpf, func_rg_numero, 
  func_rg_emissor, func_titulo, func_titulo_secao, func_titulo_zona, func_pis, func_cnh_num, func_categoria, 
  func_ctps, func_ctps_serie, func_reservista, func_endereco, func_endereco_numero, func_endereco_bairro, 
  func_endereco_cep, func_endereco_uf, func_endereco_cidade, func_matricula, func_admissao, func_decreto, 
  func_lotacao, func_cargo, func_regime, func_grupo_sanquineo, func_fator_rh, func_email, func_telefone, 
  func_celular1, func_celular2, func_agencia_banco, func_conta_banco, func_nome_banco, func_area_concurso, 
  func_formacao, func_situacao, func_foto 
FROM 
  smc_func
WHERE
  func_id_sec = '$row_EscolaLogada[escola_id_sec]'
  AND func_situacao = '1' 
  $nome_no_where 
  $nascimento_no_where 
  $cpf_no_where 
  ORDER BY func_nome ASC
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
 
        <h1 class="ls-title-intro ls-ico-users">Pesquisar funcionários</h1>
        
                
              <?php if (isset($_GET["nada"])) { ?>
                <div class="ls-alert-danger ls-dismissable">
                  <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
                  <strong>Informações inválidas.</strong> A equipe de suporte já foi comunicada do erro.
                </div>
              <?php } ?>
        
		<div class="ls-alert-info"><strong>Dica:</strong> Se não tiver certeza do nome completo, informe apenas o primeiro nome, sobrenome ou parte deles. Você também poderá usar uma combinação do CPF ou data de nascimento.</div>        
        
        <form action="" method="post" class="ls-form ls-form-horizontal row">
          <fieldset>
                <label class="ls-label col-md-6">
            <b class="ls-label-text">NOME</b>
            <input type="text" name="nome" placeholder="Nome completo ou parte" >
                </label>
                <label class="ls-label col-md-3">
            <b class="ls-label-text">NASCIMENTO</b>
            <input type="text" name="nascimento" placeholder="Data de nascimento" class="date">
                </label>
            <label class="ls-label col-md-3">
              <b class="ls-label-text">CPF</b>
                  <input type="text" name="cpf" placeholder="CPF" class="cpf" onblur="javascript: validarCPF(this);">
            </label>
          </fieldset>
            
           
              <div class="ls-actions-btn">
                <button class="ls-btn">Pesquisar</button>
                <button type="reset" class="ls-btn-danger">Limpar</button>
              </div>
        </form>
		
  <?php if ($totalRows_buscaFuncionario > 0) { // Show if recordset not empty ?>
  <?php if (($nomeBusca == "") and ($nascimentoBusca == "") and ($cpfBusca == "")) { ?>
  <div class="ls-alert-danger"><strong>Atenção: </strong> Informe um dos campos acima e clique em pesquisar.</div>        
  <?php } else { ?>
    <div class="ls-box">
  <h1>Funcionários encontrados</h1>
  <table class="ls-table ls-table-striped">
    <thead>
      <tr>
        <th>Nome</th>
        <th>Mãe</th>
        <th>Nascimento</th>
        <th>CPF</th>
        <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
      <?php do { ?>
        <tr>
          <td><?php echo $row_buscaFuncionario['func_nome']; ?></td>
          <td><?php echo $row_buscaFuncionario['func_mae']; ?></td>
          <td><?php echo converteData($row_buscaFuncionario['func_data_nascimento']); ?></td>
          <td><?php echo $row_buscaFuncionario['func_cpf']; ?></td>
          <td><a href="funcVincular.php?cod=<?php echo $row_buscaFuncionario['func_id']; ?>" class="ls-btn-primary">Vincular</a></td>
        </tr>
        <?php } while ($row_buscaFuncionario = mysql_fetch_assoc($buscaFuncionario)); ?>
    </tbody>
  </table>
  </div>
  <?php } ?>
  <?php } else { ?>
  Nenhum funcionário encontrado.
  </div>
  <?php } ?>
  
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
      
      <script type="text/javascript" language="javascript">
          function validarCPF( cpf ){
              var vcpf = cpf.value;
              var filtro = /^\d{3}.\d{3}.\d{3}-\d{2}$/i;
              
			  if(vcpf!="") {
              if(!filtro.test(vcpf))
              {
                  window.alert("CPF inválido. Tente novamente.");vcpf
                  return false;
              }
             
              vcpf = remove(vcpf, ".");
              vcpf = remove(vcpf, "-");
              
              if(vcpf.length != 11 || vcpf == "00000000000" || vcpf == "11111111111" ||
                  vcpf == "22222222222" || vcpf == "33333333333" || vcpf == "44444444444" ||
                  vcpf == "55555555555" || vcpf == "66666666666" || vcpf == "77777777777" ||
                  vcpf == "88888888888" || vcpf == "99999999999")
              {
                  window.alert("CPF inválido. Tente novamente.");vcpf
                  return false;
             }
      
              soma = 0;
              for(i = 0; i < 9; i++)
              {
                  soma += parseInt(vcpf.charAt(i)) * (10 - i);
              }
              
              resto = 11 - (soma % 11);
              if(resto == 10 || resto == 11)
              {
                  resto = 0;
              }
              if(resto != parseInt(vcpf.charAt(9))){
                  window.alert("CPF inválido. Tente novamente.");vcpf
                  return false;
              }
              
              soma = 0;
              for(i = 0; i < 10; i ++)
              {
                  soma += parseInt(vcpf.charAt(i)) * (11 - i);
              }
              resto = 11 - (soma % 11);
              if(resto == 10 || resto == 11)
              {
                  resto = 0;
              }
              
              if(resto != parseInt(vcpf.charAt(10))){
                  window.alert("CPF inválido. Tente novamente.");vcpf
                  return false;
              }
              
              return true;
           }
		  }
           
          function remove(str, sub) {
              i = str.indexOf(sub);
              r = "";
              if (i == -1) return str;
              {
                  r += str.substring(0,i) + remove(str.substring(i + sub.length), sub);
              }
              
              return r;
          }
          
          /**
             * MASCARA ( mascara(o,f) e execmascara() ) CRIADAS POR ELCIO LUIZ
             * elcio.com.br - https://elcio.com.br/ajax/mascara/
             */
          function mascara(o,f){
              v_obj=o
              v_fun=f
              setTimeout("execmascara()",1)
          }
      
          function execmascara(){
              v_obj.value=v_fun(v_obj.value)
          }
      
          function cpf_mask(v){
              v=v.replace(/\D/g,"")                 //Remove tudo o que não é dígito
              v=v.replace(/(\d{3})(\d)/,"$1.$2")    //Coloca ponto entre o terceiro e o quarto dígitos
              v=v.replace(/(\d{3})(\d)/,"$1.$2")    //Coloca ponto entre o setimo e o oitava dígitos
              v=v.replace(/(\d{3})(\d)/,"$1-$2")   //Coloca ponto entre o decimoprimeiro e o decimosegundo dígitos
              return v
          }
      </script>
  </body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($buscaFuncionario);
?>
