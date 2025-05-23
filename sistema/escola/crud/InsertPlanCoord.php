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
?>
<?php
require_once('../../../Connections/SmecelNovo.php');
include "../fnc/session.php";
include "../usuLogado.php";
include "../fnc/anoLetivo.php";

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

ob_start(); // Inicia o buffer de saída

// Verificar permissões
// if ($row_UsuLogado['usu_insert'] == "N") {
//     ob_end_clean();
//     header('Content-Type: application/json');
//     echo json_encode(['success' => false, 'message' => 'Você não tem permissão para inserir planejamentos.']);
//     exit;
// }

// Validar campos obrigatórios
if (empty($_POST['data_inicio']) || empty($_POST['data_fim'])) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Datas de início e fim são obrigatórias.']);
    exit;
}

// Processar checkboxes
$temas_integradores = isset($_POST['temas_integradores']) ? implode(',', $_POST['temas_integradores']) : '';
$atividades_diarias = isset($_POST['atividades_diarias']) ? implode(',', $_POST['atividades_diarias']) : '';
$intervencao_pedagogica = isset($_POST['intervencao_pedagogica']) ? implode(',', $_POST['intervencao_pedagogica']) : '';
$monitoramento_avaliacao = isset($_POST['monitoramento_avaliacao']) ? implode(',', $_POST['monitoramento_avaliacao']) : '';
$avaliacao_processual = isset($_POST['avaliacao_processual']) ? implode(',', $_POST['avaliacao_processual']) : '';

// Preparar e executar o INSERT
$insertSQL = sprintf("INSERT INTO smc_planejamento_coordenador (
    id_coordenador, id_escola, data_inicio, data_fim, temas_integradores, atividade_promovida, 
    atividades_diarias, intervencao_pedagogica, intervencao_pedagogica_obs, acoes_cnca_sabe, acompanhamento_projetos, 
    monitoramento_avaliacao, monitoramento_avaliacao_obs, formacao_continuada, competencias_socioemocionais, 
    atendimento_familias, avaliacao_processual
) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($row_UsuLogado['usu_id'], "int"),
    GetSQLValueString($row_EscolaLogada['escola_id'], "int"),
    GetSQLValueString($_POST['data_inicio'], "date"),
    GetSQLValueString($_POST['data_fim'], "date"),
    GetSQLValueString($temas_integradores, "text"),
    GetSQLValueString($_POST['atividade_promovida'], "text"),
    GetSQLValueString($atividades_diarias, "text"),
    GetSQLValueString($intervencao_pedagogica, "text"),
    GetSQLValueString($_POST['intervencao_pedagogica_obs'], "text"),
    GetSQLValueString($_POST['acoes_cnca_sabe'], "text"),
    GetSQLValueString($_POST['acompanhamento_projetos'], "text"),
    GetSQLValueString($monitoramento_avaliacao, "text"),
    GetSQLValueString($_POST['monitoramento_avaliacao_obs'], "text"),
    GetSQLValueString($_POST['formacao_continuada'], "text"),
    GetSQLValueString($_POST['competencias_socioemocionais'], "text"),
    GetSQLValueString($_POST['atendimento_familias'], "text"),
    GetSQLValueString($avaliacao_processual, "text")
);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$Result1 = mysql_query($insertSQL, $SmecelNovo);

ob_end_clean();
header('Content-Type: application/json');
if ($Result1) {
    echo json_encode(['success' => true, 'message' => 'Planejamento cadastrado com sucesso!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar o planejamento: ' . mysql_error()]);
}
exit;