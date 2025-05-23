<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "fnc/anti_injection.php"; ?>

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

if ((isset($_GET['aula'])) && ($_GET['aula'] != "")) {

  // Obtendo o hash da aula a ser excluída
  $aulaHash = anti_injection($_GET['aula']);

  // Executando a exclusão
  $deleteSQL = sprintf(
      "DELETE FROM smc_plano_aula WHERE plano_aula_hash=%s",
      GetSQLValueString($aulaHash, "text")
  );

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());

  if ($Result1) {
      // Redirecionando após exclusão bem-sucedida
      $deleteGoTo = "aulas_por_professor.php?deletado";
      if (isset($_SERVER['QUERY_STRING'])) {
          $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
          $deleteGoTo .= $_SERVER['QUERY_STRING'];
      }
      header(sprintf("Location: %s", $deleteGoTo));
      exit();
  } else {
      // Tratamento de erro opcional
      die("Erro ao excluir a aula: " . mysql_error());
  }
}
?>
