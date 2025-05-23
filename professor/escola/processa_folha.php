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
vinculo_id, vinculo_id_escola, vinculo_acesso, vinculo_id_funcionario, vinculo_id_funcao, vinculo_carga_horaria, DATE_FORMAT(vinculo_data_inicio, '%d/%m/%Y') AS vinculo_data_inicio, vinculo_obs, 
func_id, func_nome, funcao_id, funcao_nome, func_regime, func_senha_ativa, 
folha_extra_id,
folha_extra_id_func,
folha_extra_data,
folha_extra_tipo,
folha_extra_justificativa,
faltas_func_id,
faltas_func_id_func,
faltas_func_id_escola,
faltas_func_id_funcao,
faltas_func_data,
faltas_func_tipo_jutificativa,
faltas_func_obs,
faltas_func_anexo,
faltas_tipo_dia_aula,
falta_hash
FROM smc_vinculo 
INNER JOIN smc_func ON func_id = vinculo_id_funcionario
INNER JOIN smc_funcao ON funcao_id = vinculo_id_funcao
LEFT JOIN smc_faltas_func ON faltas_func_id_func = vinculo_id_funcionario
LEFT JOIN smc_folha_extra ON folha_extra_id_func = vinculo_id_funcionario
WHERE vinculo_id_escola = '$row_EscolaLogada[escola_id]'
AND vinculo_status = 1
";
$ListaVinculos = mysql_query($query_ListaVinculos, $SmecelNovo) or die(mysql_error());
$row_ListaVinculos = mysql_fetch_assoc($ListaVinculos);
$totalRows_ListaVinculos = mysql_num_rows($ListaVinculos);

do {
    $hash = md5($row_ListaVinculos['vinculo_id'] . date('Ymdhis'));
    mysql_select_db($database_SmecelNovo, $SmecelNovo);
    $insertSQL = sprintf("INSERT INTO smc_folha_lancamento 
    (folha_lanc_id_folha, folha_lanc_id_escola, folha_lanc_id_vinculo, folha_lanc_hash) VALUES (%s, %s, %s, %s)",
        GetSQLValueString($_POST['folha_id'], "int"),
        GetSQLValueString($row_EscolaLogada['escola_id'], "int"),
        GetSQLValueString($row_ListaVinculos['vinculo_id'], "int"),
        GetSQLValueString($hash, "text")
    );

    $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

} while ($row_ListaVinculos = mysql_fetch_assoc($ListaVinculos));