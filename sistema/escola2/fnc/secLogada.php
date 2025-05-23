<?php
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_ibge_municipio, sec_regra_media FROM smc_sec WHERE sec_id = '$row_UsuLogado[usu_sec]'";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

switch ($row_Secretaria['sec_regra_media']) {	
	case 1:
	  include('fnc/notas.php');
	  $av1 = "AV1";
	  $av2 = "AV2";
	  $av3 = "AV3";
	  $av1_max = "10";
	  $av2_max = "10";
	  $av3_max = "10";
	  $cancelaLink = "";
	  break;
	case 2;
	  include('fnc/notas1.php');
	  $av1 = "AP";
	  $av2 = "AF";
	  $av3 = "AQ";
	  $av1_max = "2";
	  $av2_max = "3";
	  $av3_max = "5";
	  $cancelaLink = "_";
	  break;
	default:
	  include('fnc/notas.php');
}
?>