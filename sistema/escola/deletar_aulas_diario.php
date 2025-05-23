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


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recebe os dados enviados via POST
    $input = json_decode(file_get_contents('php://input'), true);

    if (isset($input['aula'])) {

        $aulaHash = anti_injection($input['aula']);

        // Executa a exclusão
        $deleteSQL = sprintf(
            "DELETE FROM smc_plano_aula WHERE plano_aula_hash=%s",
            GetSQLValueString($aulaHash, "text")
        );

        mysql_select_db($database_SmecelNovo, $SmecelNovo);
        $Result1 = mysql_query($deleteSQL, $SmecelNovo);

        if ($Result1) {
            // Responde com sucesso
            echo json_encode(['success' => true]);
        } else {
            // Responde com erro
            echo json_encode(['success' => false, 'message' => mysql_error()]);
        }
        exit;
    }
}
?>

?>
