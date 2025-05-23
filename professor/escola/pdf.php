<?php
// Inicia o buffer de saída
ob_start();

require_once('../../Connections/SmecelNovo.php');
include('fnc/inverteData.php');
include "fnc/session.php";

// Função para tratar valores SQL
if (!function_exists("GetSQLValueString")) {
    function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") {
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

// Consulta informações da escola e do aluno
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EscolaLogada = "SELECT * FROM smc_escola WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);

$colname_Matricula = isset($_GET['hash']) ? $_GET['hash'] : "-1";
$query_Matricula = sprintf("SELECT * FROM smc_vinculo_aluno WHERE vinculo_aluno_hash = %s", GetSQLValueString($colname_Matricula, "text"));
$Matricula = mysql_query($query_Matricula, $SmecelNovo) or die(mysql_error());
$row_Matricula = mysql_fetch_assoc($Matricula);

// Inclui a biblioteca TCPDF
require_once('../../tcpdf/tcpdf.php');

// Criação do PDF
$tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$tcpdf->SetCreator(PDF_CREATOR);
$tcpdf->SetAuthor('Secretaria Escolar');
$tcpdf->SetTitle('Declaração de Matrícula');
$tcpdf->SetMargins(15, 20, 15);
$tcpdf->AddPage();

// Define a imagem da marca d'água (se houver)
$marcaDagua = ($row_EscolaLogada['escola_logo'] != "") ? "../../img/marcadagua/" . $row_EscolaLogada['escola_logo'] : "../../img/marcadagua/brasao_republica.png";
$tcpdf->Image($marcaDagua, 50, 50, 100, 100, '', '', '', false, 300, '', false, false, 0);

// Texto preto (sem transparência)
$tcpdf->SetTextColor(0, 0, 0); 

// HTML com cabeçalho na parte superior direita
$html = "
<style>
    body { font-size: 12px; }
    table { width: 100%; border-collapse: collapse; }
    td { vertical-align: top; }
    .right { text-align: right; }
    .center { text-align: center; }
    .justify { text-align: justify; }
    .assinatura { text-align: center; margin-top: 50px; }
    .negrito { font-weight: bold; }
</style>

<table>
    <tr>
        <td width='85%' class='right'>
            <h3 class='negrito'>" . $row_EscolaLogada['escola_nome'] . "</h3>
            <p>INEP: " . $row_EscolaLogada['escola_inep'] . "</p>
            <p>" . $row_EscolaLogada['escola_endereco'] . "</p>
            <p>" . $row_EscolaLogada['escola_email'] . "</p>
        </td>
        <td width='15%' valign='top'>";
if ($row_EscolaLogada['escola_logo'] != "") {
    $html .= "<img src='../../img/logo/" . $row_EscolaLogada['escola_logo'] . "' width='80px' />";
} else {
    $html .= "<img src='../../img/brasao_republica.png' width='60px' />";
}
$html .= "</td>
    </tr>
</table>

<h2 class='center'>DECLARAÇÃO DE MATRÍCULA</h2>

<p class='justify'>
    Declaramos para os devidos fins que o(a) aluno(a) <strong>" . ($row_Matricula['aluno_nome_social'] ?: $row_Matricula['aluno_nome']) . "</strong>, 
    nascido(a) em <strong>" . inverteData($row_Matricula['aluno_nascimento']) . "</strong>, 
    filho(a) de <strong>" . $row_Matricula['aluno_filiacao1'] . "</strong>, 
    encontra-se regularmente matriculado(a) na escola <strong>" . $row_EscolaLogada['escola_nome'] . "</strong>, 
    no ano letivo vigente.
</p>

<p class='justify'>
    A presente declaração é expedida a pedido do(a) interessado(a) para os fins que se fizerem necessários.
</p>

<p style='text-align:right'><strong>" . date("d/m/Y") . "</strong></p>

<div class='assinatura'>
    <p>_________________________________________________________</p>
    <p>Diretor(a) ou Secretário(a) Escolar</p>
</div>";

// Escreve no PDF
$tcpdf->writeHTML($html, true, false, true, false, '');

// Fecha o buffer de saída e gera o PDF
ob_end_clean();
$tcpdf->Output("Declaracao_Matricula.pdf", "I");

?>
