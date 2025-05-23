<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include "fnc/inverteData.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
<?php include('../funcoes/url_base.php'); ?>


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
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, sec_id, sec_cidade, sec_uf 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);




if(isset($_POST['queryString'])) {
				
		$ano = $_POST['ano'];
				
		//$queryString = $_POST['queryString'];
		$queryString = anti_injection($_POST['queryString']);
		
		if(strlen($queryString) > 2) {
			$query = mysql_query("
			SELECT 
			aluno_id, aluno_nome, DATE_FORMAT(aluno_nascimento, '%d/%m/%Y') AS aluno_nascimento, aluno_filiacao1, aluno_foto,
			vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, vinculo_aluno_hash, vinculo_aluno_id_turma, 
			CASE vinculo_aluno_situacao
			WHEN 1 THEN 'MATRICULADO'
			WHEN 2 THEN 'TRANSFERIDO'
			WHEN 3 THEN 'DEIXOU DE FREQUENTAR'
			WHEN 4 THEN 'FALECIDO'
			WHEN 5 THEN 'OUTROS'
			END AS vinculo_aluno_situacao,
			turma_id, turma_nome, 
			CASE turma_turno
			WHEN 1 THEN 'MATUTINO'
			WHEN 2 THEN 'VESPERTINO'
			WHEN 3 THEN 'NOTURNO'
			END AS turma_turno
			FROM smc_aluno
			INNER JOIN smc_vinculo_aluno ON vinculo_aluno_id_aluno = aluno_id
			INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
			WHERE (aluno_nome LIKE '$queryString%' OR aluno_filiacao1 LIKE '$queryString%')
			AND vinculo_aluno_ano_letivo = '$ano' AND vinculo_aluno_id_escola = '$row_EscolaLogada[escola_id]'	
			ORDER BY aluno_nome ASC
			LIMIT 50"
			) or die("Erro na consulta");
			
			//$row_Noticia = mysql_fetch_assoc($query);
			$totalRows_Noticia = mysql_num_rows($query);
			
			
			
			echo '
			<p>
			'.$totalRows_Noticia.' aluno(s) encontrado(s).
			</p>';
			echo '
			<table class="ls-table ls-table-bordered ls-bg-header ls-table-striped ls-sm-space">
			<thead>
			<tr>
				<th width="75">FOTO</th>
				<th>DADOS DO(A) ALUNO(A)</th>
				<th>DADOS DA TURMA</th>
				<th width="120"></th>
			</tr>
			</thead>
			<tbody>
			';
						
			while ($result = mysql_fetch_array($query)) {
			
					
			
			if ($result[4]=="") {
				$foto = URL_BASE.'aluno/fotos/semfoto.jpg';
			} else {
				$foto = URL_BASE.'aluno/fotos/'.$result[4];
			}

			
			echo '
			
			<tr>
				<td height="100" width="100">
					<a style="cursor:pointer;" onClick="exibe(\''.$result[9].'\');" onClick="fill(\''.$result[9].'\');"><img src="'.$foto.'" width="75px"></a>
				</td>
				<td>
					<a style="cursor:pointer;" onClick="exibe(\''.$result[9].'\');" onClick="fill(\''.$result[9].'\');">
					<p>NOME: <strong>'.$result[1].'</strong></p> <p>DATA DE NASCIMENTO: <strong>'.$result[2].'</strong></p><p>FILIAÇÃO: <strong>'.$result[3].'</strong></p>
					</a>
				</td>
				<td>
					<a style="cursor:pointer;" onClick="exibe(\''.$result[9].'\');" onClick="fill(\''.$result[9].'\');">
					<p>TURMA: <strong>'.$result[13].'</strong></p>
					<p>TURNO: <strong>'.$result[14].'</strong></p>
					<p>SITUAÇÃO: <strong>'.$result[11].'</strong></p>
					</a>
				</td>
				<td>
					<a class="ls-btn-primary" onClick="exibe(\''.$result[9].'\');" onClick="fill(\''.$result[9].'\');">Ver aluno</a>
				</td>
			</tr>
			';
				
			
				}
				
			echo '
			</tbody>
			</table>';
			
			mysql_free_result($query);
		}
	}	else {
		if ($totalRows_Noticia == 0) { 
		mysql_free_result($query);
		header("Location: vinculoAlunoExibirTurma.php?erro");
		exit;
}
	}


?>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);
?>
