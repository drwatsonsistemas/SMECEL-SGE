<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include('fnc/inverteData.php'); ?>
<?php include "fnc/anti_injection.php"; ?>


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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EscolaLogada = "
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, 
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, sec_id, sec_cidade, sec_uf, escola_tema 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

$anoLetivo = $row_AnoLetivo['ano_letivo_ano'];
	if (isset($_GET['ano'])) {
	
		if ($_GET['ano'] == "") {
		//echo "TURMA EM BRANCO";	
		header("Location: turmasAlunosVinculados.php?nada"); 
		exit;
		}
	
	$anoLetivo = anti_injection($_GET['ano']);
	$anoLetivo = (int)$anoLetivo;
}

$colname_Professor = "-1";
if (isset($_GET['professor'])) {
  $colname_Professor = $_GET['professor'];
}



mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Vinculo = "
SELECT *, func_id, func_nome
FROM smc_vinculo
INNER JOIN smc_func ON func_id = vinculo_id_funcionario
WHERE vinculo_id = '$colname_Professor'
";
$Vinculo = mysql_query($query_Vinculo, $SmecelNovo) or die(mysql_error());
$row_Vinculo = mysql_fetch_assoc($Vinculo);
$totalRows_Vinculo = mysql_num_rows($Vinculo);

$queryData = "";

if(isset($_GET['dataInicio']) && $_GET['dataInicio'] !== '' && isset($_GET['dataFinal']) && $_GET['dataFinal'] !== '') {
    // Caso o usuário pesquise por ambas as datas
    $dataInicio = anti_injection($_GET['dataInicio']);
    $dataFinal = anti_injection($_GET['dataFinal']);

    $dataInicio = "'" . date('Y-m-d', strtotime($dataInicio)) . "'";
    $dataFinal = "'" . date('Y-m-d', strtotime($dataFinal)) . "'";

    $queryData = "AND plano_aula_data BETWEEN $dataInicio AND $dataFinal";
} elseif(isset($_GET['dataInicio']) && $_GET['dataInicio'] !== '') {
    // Caso o usuário pesquise apenas pela data inicial
    $dataInicio = anti_injection($_GET['dataInicio']);
    $dataInicio = "'" . date('Y-m-d', strtotime($dataInicio)) . "'";
    $queryData = "AND plano_aula_data >= $dataInicio";
} elseif(isset($_GET['dataFinal']) && $_GET['dataFinal'] !== '') {
    // Caso o usuário pesquise apenas pela data final
    $dataFinal = anti_injection($_GET['dataFinal']);
    $dataFinal = "'" . date('Y-m-d', strtotime($dataFinal)) . "'";
    $queryData = "AND plano_aula_data <= $dataFinal";
} else {
    // Caso nenhuma data seja fornecida, não incluir a parte da consulta relacionada às datas
    // Isso evitará que a query busque datas antigas como '1970-01-01'
}


$queryComponente = "";

if(isset($_GET['componente']) && $_GET['componente'] > 0){
    // Se o componente for selecionado e for maior que zero
    $componente = anti_injection($_GET['componente']);
    $queryComponente = "AND disciplina_id = $componente";
}

$queryTurma = "";

if(isset($_GET['turma']) && $_GET['turma'] > 0){
    // Se o componente for selecionado e for maior que zero
    $turma = anti_injection($_GET['turma']);
    $queryTurma = "AND turma_id = $turma";
}


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Aulas = "SELECT *, turma_id, turma_nome, turma_ano_letivo, disciplina_id, disciplina_nome FROM smc_plano_aula 
INNER JOIN smc_turma ON turma_id = plano_aula_id_turma
INNER JOIN smc_disciplina ON disciplina_id = plano_aula_id_disciplina
WHERE plano_aula_id_professor = '$row_Vinculo[vinculo_id_funcionario]'
AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'
$queryTurma
$queryComponente
$queryData
ORDER BY plano_aula_data";

$Aulas = mysql_query($query_Aulas, $SmecelNovo) or die(mysql_error());
$row_Aulas = mysql_fetch_assoc($Aulas);
$totalRows_Aulas = mysql_num_rows($Aulas);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AulasSelect = "SELECT *, turma_id, turma_nome, turma_ano_letivo, disciplina_id, disciplina_nome FROM smc_plano_aula 
INNER JOIN smc_turma ON turma_id = plano_aula_id_turma
INNER JOIN smc_disciplina ON disciplina_id = plano_aula_id_disciplina
WHERE plano_aula_id_professor = '$row_Vinculo[vinculo_id_funcionario]'
AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'
GROUP BY disciplina_nome
";
$AulasS = mysql_query($query_AulasSelect, $SmecelNovo) or die(mysql_error());
$row_AulasS = mysql_fetch_assoc($AulasS);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AulasSelectTurmas = "SELECT *, turma_id, turma_nome, turma_ano_letivo, disciplina_id, disciplina_nome FROM smc_plano_aula 
INNER JOIN smc_turma ON turma_id = plano_aula_id_turma
INNER JOIN smc_disciplina ON disciplina_id = plano_aula_id_disciplina
WHERE plano_aula_id_professor = '$row_Vinculo[vinculo_id_funcionario]'
AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'
GROUP BY disciplina_nome
";
$AulasT = mysql_query($query_AulasSelectTurmas, $SmecelNovo) or die(mysql_error());
$row_AulasT = mysql_fetch_assoc($AulasT);
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
    <link rel="stylesheet" href="css/sweetalert2.all.css">

</head>
  <body>
    <?php include_once ("menu-top.php"); ?>
          <?php include_once ("menu-esc.php"); ?>


    <main class="ls-main ">
      <div class="container-fluid">
 
        <h1 class="ls-title-intro ls-ico-home"><?= $row_Vinculo['func_nome'] ?> | Ano Letivo <?php echo $anoLetivo; ?></h1>
		<!-- CONTEÚDO -->
		
        <div class="ls-box-filter">
        <form action="" class="ls-form ls-form-horizontal row">
            <label class="ls-label col-md-3 col-xs-12">
                <b class="ls-label-text">Data Inicial</b>
                <input type="date" name="dataInicio" class="1datepicker ls-daterange" id="datepicker1" autocomplete="off" value="<?php echo isset($_GET['dataInicio']) ? $_GET['dataInicio'] : ''; ?>" >

            </label>
            <label class="ls-label col-md-3 col-xs-12">
                <b class="ls-label-text">Data Final</b>
                <input type="date" name="dataFinal" class="1datepicker 1ls-daterange" id="1datepicker2" autocomplete="off" value="<?php echo isset($_GET['dataFinal']) ? $_GET['dataFinal'] : ''; ?>" >
                
            </label>
            
            <label class="ls-label col-md-3 col-xs-12">
                <b class="ls-label-text">COMPONENTE</b>
                <div class="ls-custom-select">
                    <select name="componente" class="ls-select">
                    <option value="0">TODOS</option>
                        <?php do { ?>
                            <option value="<?php echo $row_AulasS['disciplina_id']; ?>"><?php echo $row_AulasS['disciplina_nome']; ?></option>
                        <?php } while ($row_AulasS = mysql_fetch_assoc($AulasS)); ?>
                    </select>
                </div>
                </label>

                <label class="ls-label col-md-3 col-xs-12">
                <b class="ls-label-text">TURMA</b>
                <div class="ls-custom-select">
                    <select name="turma" class="ls-select">
                    <option value="0">TODOS</option>
                        <?php do { ?>
                            <option value="<?php echo $row_AulasT['turma_id']; ?>"><?php echo $row_AulasT['turma_nome']; ?></option>
                        <?php } while ($row_AulasT = mysql_fetch_assoc($AulasT)); ?>
                    </select>
                </div>
                </label>
        
                <input type="hidden" name="professor" value="<?= $colname_Professor ?>">
        
            <div class="ls-actions-btn ">
            <input type="submit" value="Buscar" class="ls-btn-success" title="FILTRAR">
            <a href="aulas_por_professor.php?professor=<?= $colname_Professor ?>" class="ls-btn">LIMPAR</a>
            </div>
        
        </form>

        
        
        </div>
        
        <hr>
        <?php if($totalRows_Aulas > 0){ ?>
        <table class="ls-table ls-sm-space">
        <tr>
            	<th width="110" class="ls-txt-center">DATA</th>
            	<th width="110" class="ls-txt-center">COD</th>
            	<th width="80" class="ls-txt-center"></th>
            	<th class="ls-txt-center">CONTEÚDO</th>
                <th class="ls-txt-center">COMPONENTE</th>
                <th class="ls-txt-center">TURMA</th>
        </tr>
        <?php do{ ?>
        <tr>
            <td class="ls-txt-center"><?php echo inverteData($row_Aulas['plano_aula_data']); ?></td>
            <td class="ls-txt-center"><?php echo $row_Aulas['plano_aula_id']; ?></td>
            <td class="ls-txt-center">
                <a href="javascript:func()" onclick="confirmaExclusao('<?php echo $row_Aulas['plano_aula_id']; ?>','<?php echo $row_Aulas['plano_aula_hash']; ?>','<?php echo $row_Vinculo['vinculo_id']; ?>','<?php echo $anoLetivo; ?>')" ><span class="ls-ico-remove ls-color-danger ls-ico-center"></span></a>
            </td>
            <td class="ls-txt-center"><?php echo $row_Aulas['plano_aula_texto']; ?></td>
            <td class="ls-txt-center"><?php echo $row_Aulas['disciplina_nome']; ?></td>
            <td class="ls-txt-center"><?php echo $row_Aulas['turma_nome']; ?></td>
        </tr>
        <?php }while($row_Aulas = mysql_fetch_assoc($Aulas)); ?>
        </table>
        <?php }else { ?>
            <div class="ls-alert-warning"><strong>Ops!</strong> Nenhum resultado encontrado.</div>
        <?php } ?>
        <br>

          
              
<!-- CONTEÚDO -->
      </div>
    </main>
    <?php include_once ("menu-dir.php"); ?>

    <!-- We recommended use jQuery 1.10 or up -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
	<script src="js/locastyle.js"></script>

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script src="js/sweetalert2.min.js"></script>
    
  <script>
	function confirmaExclusao(aula,hash,professor,ano) {
		
		
		
     var resposta = confirm("Deseja realmente remover a aula "+aula+"? A exclusão não poderá ser desfeita.");
     	if (resposta == true) {
     	     window.location.href = "aula_deletar.php?professor="+professor+"&ano="+ano+"&aula="+hash;
    	 }
		 
		 
		 
		 
		 
		 
		 
		 
		 
	}
	</script>
    


                
            
            <?php if (isset($_GET["deletado"])) { ?>
              <script>
            Swal.fire({
              //position: 'top-end',
              icon: 'success',
              title: 'Aula deletada com sucesso',
              showConfirmButton: false,
              timer: 1500
            })
                          </script>
              <?php } ?>
              
              

              
              
 
  </body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($Aulas);

mysql_free_result($EscolaLogada);
?>
