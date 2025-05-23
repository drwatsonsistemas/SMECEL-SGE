<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include('fnc/inverteData.php'); ?>


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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_alunosemid = "
SELECT 
aluno_id, aluno_cod_inep, aluno_cpf, aluno_num_matricula_modelo_novo, aluno_nis, aluno_nome, 
aluno_nascimento, aluno_filiacao1, aluno_filiacao2, aluno_municipio_nascimento_ibge, aluno_hash 
FROM smc_aluno 
WHERE aluno_nascimento IS NOT NULL AND aluno_municipio_nascimento_ibge IS NOT NULL AND aluno_cod_inep IS NULL";
$alunosemid = mysql_query($query_alunosemid, $SmecelNovo) or die(mysql_error());
$row_alunosemid = mysql_fetch_assoc($alunosemid);
$totalRows_alunosemid = mysql_num_rows($alunosemid);

function retiraAcentos ($string) {
	
		$string = str_replace('Á', 'A', $string);
		$string = str_replace('À', 'A', $string);
		$string = str_replace('Â', 'A', $string);
		$string = str_replace('Ã', 'A', $string);
		$string = str_replace('É', 'E', $string);
		$string = str_replace('È', 'E', $string);
		$string = str_replace('Ê', 'E', $string);
		$string = str_replace('Í', 'I', $string);
		$string = str_replace('Ì', 'I', $string);
		$string = str_replace('Î', 'I', $string);
		$string = str_replace('Ó', 'O', $string);
		$string = str_replace('Ò', 'O', $string);
		$string = str_replace('Õ', 'O', $string);
		$string = str_replace('Ô', 'O', $string);
		$string = str_replace('Ú', 'U', $string);
		$string = str_replace('Ù', 'U', $string);
		$string = str_replace('Û', 'U', $string);
		$string = str_replace('Ü', 'U', $string);
		$string = str_replace('Ç', 'C', $string);
		$string = str_replace('\'', '', $string);
		$string = str_replace('  ', ' ', $string);
		$string = str_replace('-', '', $string);
		$string = str_replace('.', '', $string);
		
		return $string;
	}


function certidaonascimento($num) {

	$num = str_replace(' ', '', $num);
	$num = str_replace('.', '', $num);
	$num = str_replace('-', '', $num);
	$num = trim($num);
	
	
	$cont = strlen($num);
	
	if ($cont == 30) {
		
		$num = $num."XX";
		
	}
	
		$num = substr($num,0,32);


	return $num;
	
}

function cpf($num) {
	
	$num = str_replace(' ', '', $num);
	$num = str_replace('.', '', $num);
	$num = str_replace('-', '', $num);
	$num = trim($num);
	$num = substr($num,0,11);


	return $num;	
	
}

function nis($num) {
	
	$num = str_replace(' ', '', $num);
	$num = str_replace('.', '', $num);
	$num = str_replace('-', '', $num);
	$num = trim($num);
	$num = substr($num,0,11);

	return $num;	
	
}




?>

<?php
$name = date('YmdHis').".txt";
$text = "";
?>


<?php do { ?>
<small>
<a href="alunoEditar.php?hash=<?php echo $row_alunosemid['aluno_hash']; ?>" target="_blank"><?php echo $row_alunosemid['aluno_id']; ?></a>|
<?php //echo cpf($row_alunosemid['aluno_cpf']); ?>|
<?php //echo certidaonascimento($row_alunosemid['aluno_num_matricula_modelo_novo']); ?>|
<?php echo retiraAcentos($row_alunosemid['aluno_nome']); ?>|
<?php echo inverteData($row_alunosemid['aluno_nascimento']); ?>|
<?php echo retiraAcentos($row_alunosemid['aluno_filiacao1']); ?>|
<?php echo retiraAcentos($row_alunosemid['aluno_filiacao2']); ?>|
<?php echo $row_alunosemid['aluno_municipio_nascimento_ibge']; ?>|
</small><br>



<?php

if ((preg_match('/\d+/', mb_convert_kana(retiraAcentos($row_alunosemid['aluno_nome']), 'n')) > 0) || (preg_match('/\d+/', mb_convert_kana(retiraAcentos($row_alunosemid['aluno_filiacao1']), 'n')) > 0) || (preg_match('/\d+/', mb_convert_kana(retiraAcentos($row_alunosemid['aluno_filiacao2']), 'n')) > 0)) {
} else {
$text .= $row_alunosemid['aluno_id']."|||".retiraAcentos(trim(mb_strtoupper($row_alunosemid['aluno_nome'])))."|".inverteData($row_alunosemid['aluno_nascimento'])."|".retiraAcentos(trim(mb_strtoupper($row_alunosemid['aluno_filiacao1'])))."|".retiraAcentos(trim(mb_strtoupper($row_alunosemid['aluno_filiacao2'])))."|".$row_alunosemid['aluno_municipio_nascimento_ibge']."|\n";
} 

//$text .= $row_alunosemid['aluno_id']."|".cpf($row_alunosemid['aluno_cpf'])."|".certidaonascimento($row_alunosemid['aluno_num_matricula_modelo_novo'])."|".nis($row_alunosemid['aluno_nis'])."|".retiraAcentos(trim($row_alunosemid['aluno_nome']))."|".inverteData($row_alunosemid['aluno_nascimento'])."|".retiraAcentos(trim($row_alunosemid['aluno_filiacao1']))."|".retiraAcentos(trim($row_alunosemid['aluno_filiacao2']))."|".$row_alunosemid['aluno_municipio_nascimento_ibge']."|\n";
//$text .= $row_alunosemid['aluno_id']."|".cpf($row_alunosemid['aluno_cpf'])."|||".retiraAcentos(trim($row_alunosemid['aluno_nome']))."|".inverteData($row_alunosemid['aluno_nascimento'])."|".retiraAcentos(trim($row_alunosemid['aluno_filiacao1']))."|".retiraAcentos(trim($row_alunosemid['aluno_filiacao2']))."|".$row_alunosemid['aluno_municipio_nascimento_ibge']."|\n";

?>


<?php } while ($row_alunosemid = mysql_fetch_assoc($alunosemid)); ?>

<?php
$file = fopen($name, 'a');
fwrite($file, $text);
fclose($file);
?>
<br><br>
<?php echo $totalRows_alunosemid; ?> registros.
<br><br>
<a href="<?php echo $name; ?>" target="_blank">Baixar arquivo</a>
<br><br>


<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($alunosemid);
?>
