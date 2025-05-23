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


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ListaVinculos = "
SELECT 
vinculo_id, vinculo_id_escola, vinculo_id_funcionario, vinculo_id_funcao, vinculo_carga_horaria, vinculo_status, DATE_FORMAT(vinculo_data_inicio, '%d/%m/%Y') AS vinculo_data_inicio, vinculo_obs, 
func_id, func_nome, funcao_id, funcao_nome, funcao_docencia, func_regime, func_senha_ativa 
FROM smc_vinculo 
INNER JOIN smc_func 
ON func_id = vinculo_id_funcionario 
INNER JOIN smc_funcao
ON funcao_id = vinculo_id_funcao 
WHERE vinculo_id_escola = '$row_EscolaLogada[escola_id]' AND funcao_docencia = 'S'
AND vinculo_status = '1'
ORDER BY func_nome ASC
";
$ListaVinculos = mysql_query($query_ListaVinculos, $SmecelNovo) or die(mysql_error());
$row_ListaVinculos = mysql_fetch_assoc($ListaVinculos);
$totalRows_ListaVinculos = mysql_num_rows($ListaVinculos);


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
 
        <h1 class="ls-title-intro ls-ico-home">Planejamentos por professor</h1>
        
	

		<div class="ls-box">

	<label class="ls-label col-md-12">
    <b class="ls-label-text">BUSQUE UM PROFESSOR</b>
	<input type="text" class="buscar-funcionario" alt="fonte-tabela" placeholder="Digite o nome ou parte do nome de um funcionário" autofocus/>
  </label>



</div> 
		
		


		<div class="ls-box ls-sm-space">
 		
		<?php if ( $totalRows_ListaVinculos > 0 ) { ?>
		<table class="ls-table ls-table-striped ls-sm-space fonte-tabela" role="grid">
          <thead>
          <tr>
            <th width="40px" class="ls-txt-center">Nº</th>
            <th width="20px" class=""></th>
            <th>PROFESSOR(A)</th>
            <th class="ls-txt-center">VER PLANEJAMENTOS</th>
          </tr>
          </thead>
           <tbody>
           <?php
		    $semAulas = 0;
		    $totalAulas = 0;
			$contagem = 1;
		   do { 
		   ?>
           
           <?php 
		   
	
		   
		   ?>
           
            <tr>
              <td class="ls-txt-center">
			  <?php 
			  echo $contagem;
			  $contagem++;
			  ?></td>
			  <td class=""><?php if ($row_ListaVinculos['func_senha_ativa']==1) { ?><i class="ls-ico-mobile"></i><?php } ?></td>
              <td class="<?= $row_ListaVinculos['func_id'] ?>"><?php echo $row_ListaVinculos['func_nome']; ?></td>
             
			  <td class="ls-txt-center"><a href="ac-professor.php?codigo=<?php echo $row_ListaVinculos['func_id']; ?>">Ver</a></td>
            </tr>
            <?php } while ($row_ListaVinculos = mysql_fetch_assoc($ListaVinculos)); ?>
        	</tbody>
        </table>
		
		</div>
				
		<p class="ls-txt-right">Total de professores: <strong><?php echo $totalRows_ListaVinculos; ?></strong></p>
      		<?php } else { ?>
		<br>
		<p><div class="ls-alert-info"><strong>Atenção:</strong> Nenhum funcionário vinculado nessa escola.</div></p>
		<?php } ?>	
         
		 </div>
    </main>

    <?php include_once ("menu-dir.php"); ?>

    <!-- We recommended use jQuery 1.10 or up -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="js/locastyle.js"></script>
 
    
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
