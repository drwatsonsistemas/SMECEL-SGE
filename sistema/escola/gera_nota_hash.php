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

/*
SELECT
    COUNT(*) AS contador,
    nota_id, nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_max, nota_min, nota_valor, nota_hash
FROM
    smc_nota
GROUP BY
    nota_hash
HAVING
    COUNT(*) > 1
LIMIT 0, 10

SELECT nota_id, nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_max, nota_min, nota_valor, nota_hash 
FROM 
smc_nota
WHERE nota_num_avaliacao = '98'
*/

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_nota = "
SELECT nota_id, nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_max, nota_min, nota_valor, nota_hash 
FROM 
smc_nota
WHERE nota_periodo = '99' AND nota_num_avaliacao = '99' AND nota_id_disciplina = '0'
";
$nota = mysql_query($query_nota, $SmecelNovo) or die(mysql_error());
$row_nota = mysql_fetch_assoc($nota);
$totalRows_nota = mysql_num_rows($nota);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<table border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>nota_id</td>
    <td>nota_id_matricula</td>
    <td>nota_id_disciplina</td>
    <td>nota_periodo</td>
    <td>nota_num_avaliacao</td>
    <td>nota_max</td>
    <td>nota_min</td>
    <td>nota_valor</td>
    <td>nota_hash</td>
  </tr>
  <?php do { ?>
    <tr>
      <td><?php echo $row_nota['nota_id']; ?></td>
      <td><?php echo $row_nota['nota_id_matricula']; ?></td>
      <td><?php echo $row_nota['nota_id_disciplina']; ?></td>
      <td><?php echo $row_nota['nota_periodo']; ?></td>
      <td><?php echo $row_nota['nota_num_avaliacao']; ?></td>
      <td><?php echo $row_nota['nota_max']; ?></td>
      <td><?php echo $row_nota['nota_min']; ?></td>
      <td><?php echo $row_nota['nota_valor']; ?></td>
      <td><?php echo $row_nota['nota_hash']; ?></td>
    </tr>
    
    
    <?php 
	 $hash = md5($row_nota['nota_id'].$row_nota['nota_id_disciplina'].$row_nota['nota_periodo']."98");
	 $updateSQL = "UPDATE smc_nota SET nota_hash='$hash' WHERE nota_id='$row_nota[nota_id]'";

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());
	?>
    
    
    <?php } while ($row_nota = mysql_fetch_assoc($nota)); ?>
</table>
</body>
</html>
<?php
mysql_free_result($nota);
?>
