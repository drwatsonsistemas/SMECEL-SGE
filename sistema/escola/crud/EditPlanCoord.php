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



// Verificar permissões
// if ($row_UsuLogado['usu_update'] == "N") {
//     echo json_encode(['success' => false, 'message' => 'Você não tem permissão para atualizar planejamentos.']);
//     exit;
// }

// Processar campos de múltipla escolha (checkboxes)
$temas_integradores = isset($_POST['temas_integradores']) ? implode(',', $_POST['temas_integradores']) : '';
$atividades_diarias = isset($_POST['atividades_diarias']) ? implode(',', $_POST['atividades_diarias']) : '';
$intervencao_pedagogica = isset($_POST['intervencao_pedagogica']) ? implode(',', $_POST['intervencao_pedagogica']) : '';
$monitoramento_avaliacao = isset($_POST['monitoramento_avaliacao']) ? implode(',', $_POST['monitoramento_avaliacao']) : '';
$avaliacao_processual = isset($_POST['avaliacao_processual']) ? implode(',', $_POST['avaliacao_processual']) : '';

// Preparar o UPDATE para smc_planejamento_coordenador
$updateSQL = sprintf("UPDATE smc_planejamento_coordenador SET 
    data_inicio=%s, data_fim=%s, temas_integradores=%s, atividade_promovida=%s, 
    atividades_diarias=%s, intervencao_pedagogica=%s, intervencao_pedagogica_obs=%s, acoes_cnca_sabe=%s, 
    acompanhamento_projetos=%s, monitoramento_avaliacao=%s, monitoramento_avaliacao_obs=%s, formacao_continuada=%s, 
    competencias_socioemocionais=%s, atendimento_familias=%s, avaliacao_processual=%s 
    WHERE id_planejamento=%s",
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
    GetSQLValueString($avaliacao_processual, "text"),
    GetSQLValueString($_POST['id_planejamento'], "int")
);

// Executar o UPDATE
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$Result1 = mysql_query($updateSQL, $SmecelNovo);

if ($Result1) {
    echo json_encode(['success' => true, 'message' => 'Planejamento atualizado com sucesso!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar o planejamento: ' . mysql_error()]);
}
exit;
?>