<?php
 
//require_once('Connections/ConnPref.php'); 
require('../../Connections/SmecelNovo.php');
$marca = $_GET['marca'];

/*
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ListaCidades = "SELECT te_veiculos_modelo_id, te_veiculos_modelo_id_marca, te_veiculos_modelo_cod_fipe, te_veiculos_modelo_nome FROM smc_te_veiculos_modelo WHERE te_veiculos_modelo_id_marca = '$marca'";
$ListaCidades = mysql_query($query_ListaCidades, $SmecelNovo) or die(mysql_error());
$row_ListaCidades = mysql_fetch_assoc($ListaCidades);
$totalRows_ListaCidades = mysql_num_rows($ListaCidades);
*/


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_modelo = "SELECT te_veiculos_modelo_id, te_veiculos_modelo_id_marca, te_veiculos_modelo_cod_fipe, te_veiculos_modelo_nome FROM smc_te_veiculos_modelo WHERE te_veiculos_modelo_id_marca = '$marca' ORDER BY te_veiculos_modelo_nome ASC";
$modelo = mysql_query($query_modelo, $SmecelNovo) or die(mysql_error());
$row_modelo = mysql_fetch_assoc($modelo);
$totalRows_modelo = mysql_num_rows($modelo);


 
//$sql = "SELECT * FROM cidades WHERE cid_estado_id = '$estado' ORDER BY cid_nome ASC";
 
//$rs = "SELECT * FROM cidades WHERE cid_estado_id = '$estado' ORDER BY cid_nome ASC";
//$qr = mysql_query($rs) or die(mysql_error());

echo "<select name='te_cad_veiculo_modelo_id' id='te_cad_veiculo_modelo_id'>\n";

				echo "<option value=\"\" selected=\"selected\">Escolha um modelo...</option>";
        	    do {
				echo "<option value=".$row_modelo['te_veiculos_modelo_id'].">".$row_modelo['te_veiculos_modelo_nome']."</option>";
        	     } while ($row_modelo = mysql_fetch_assoc($modelo));
				 echo "<option value='9999'>OUTROS</option>";

echo "</select>\n";

 
?>