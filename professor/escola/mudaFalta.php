<?php require_once('../../Connections/SmecelNovo.php'); ?>
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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_faltas = "
SELECT faltas_alunos_id, faltas_alunos_matricula_id, faltas_alunos_disciplina_id, faltas_alunos_numero_aula, faltas_alunos_data, faltas_alunos_justificada, faltas_alunos_justificativa 
FROM smc_faltas_alunos 
WHERE faltas_alunos_numero_aula = ''  GROUP BY faltas_alunos_data ORDER BY faltas_alunos_data DESC LIMIT 0,2";
$faltas = mysql_query($query_faltas, $SmecelNovo) or die(mysql_error());
$row_faltas = mysql_fetch_assoc($faltas);
$totalRows_faltas = mysql_num_rows($faltas);



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
  <?php do { ?>
  
  <?php 
	mysql_select_db($database_SmecelNovo, $SmecelNovo);
	$query_data = "
	SELECT faltas_alunos_id, faltas_alunos_matricula_id, faltas_alunos_disciplina_id, faltas_alunos_numero_aula, faltas_alunos_data, faltas_alunos_justificada, faltas_alunos_justificativa 
	FROM smc_faltas_alunos 
	WHERE faltas_alunos_numero_aula = '' AND faltas_alunos_data = '$row_faltas[faltas_alunos_data]' GROUP BY faltas_alunos_matricula_id ORDER BY faltas_alunos_matricula_id DESC";
	$data = mysql_query($query_data, $SmecelNovo) or die(mysql_error());
	$row_data = mysql_fetch_assoc($data);
	$totalRows_data = mysql_num_rows($data);
	
	
 ?>
  
      <p>
	  ID: <?php echo $row_faltas['faltas_alunos_id']; ?> | 
      MAT: <?php echo $row_faltas['faltas_alunos_matricula_id']; ?> | 
      DATA: <?php echo $row_faltas['faltas_alunos_data']; ?> | 
      AULA: <?php echo $row_faltas['faltas_alunos_numero_aula']; ?>
      </p>
      
      
    <?php do { ?>
    
    
      <?php 
	mysql_select_db($database_SmecelNovo, $SmecelNovo);
	$query_mat = "
	SELECT faltas_alunos_id, faltas_alunos_matricula_id, faltas_alunos_disciplina_id, faltas_alunos_numero_aula, faltas_alunos_data, faltas_alunos_justificada, faltas_alunos_justificativa 
	FROM smc_faltas_alunos 
	WHERE faltas_alunos_numero_aula = '' AND faltas_alunos_data = '$row_faltas[faltas_alunos_data]' AND faltas_alunos_matricula_id = '$row_data[faltas_alunos_matricula_id]' ORDER BY faltas_alunos_matricula_id DESC";
	$mat = mysql_query($query_mat, $SmecelNovo) or die(mysql_error());
	$row_mat = mysql_fetch_assoc($mat);
	$totalRows_mat = mysql_num_rows($mat);
	
	
 ?>

    
    
    |-- <?php echo $row_data['faltas_alunos_matricula_id']; ?><br />
    
    <?php 
	$num = 1;
	do { ?>
    
    |--|-- ID: #<?php echo $row_mat['faltas_alunos_id']; ?> | <?php echo $row_mat['faltas_alunos_matricula_id']; ?> DATA: <?php echo $row_mat['faltas_alunos_data']; ?> AULA: <?php echo $num; ?><br />
    
    <?php 
	
	
	$alterar = "UPDATE smc_faltas_alunos SET faltas_alunos_numero_aula = '$num' WHERE faltas_alunos_id = '$row_mat[faltas_alunos_id]'";
	$up = mysql_query($alterar, $SmecelNovo) or die(mysql_error());
	
	
	?>
    
    
    
    
    
    
    
    
    <?php $num++; ?>
    
	<?php } while ($row_mat = mysql_fetch_assoc($mat)); ?> 
    
	<?php } while ($row_data = mysql_fetch_assoc($data)); ?> 
    
    TOTAL: <?php echo $totalRows_data; ?><br /> 

    <?php } while ($row_faltas = mysql_fetch_assoc($faltas)); ?>
</table>
</body>
</html>
<?php
mysql_free_result($faltas);
?>
