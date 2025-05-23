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

require_once('../../Connections/SmecelNovo.php');
include "fnc/session.php";
include "usuLogado.php";
include "fnc/anoLetivo.php";

if (!isset($_POST['turma_id'])) {
    echo "Erro: ID da turma não fornecido.";
    exit;
}

$turma_id = $_POST['turma_id'];

mysql_select_db($database_SmecelNovo, $SmecelNovo);

$query_alunos = sprintf(
    "SELECT 
        a.aluno_id,
        a.aluno_nome,
        va.vinculo_aluno_situacao,
        va.vinculo_aluno_resultado_final,
        CASE va.vinculo_aluno_situacao
            WHEN 1 THEN '<span class=\"ls-sm-margin-right ls-tag-info\">MATRICULADO</span>'
            WHEN 2 THEN '<span class=\"ls-sm-margin-right ls-tag-danger\"><b>TRANSFERIDO</b></span>'
            WHEN 3 THEN '<span class=\" ls-sm-margin-rightls-tag-danger\"><b>DESISTENTE</b></span>'
            WHEN 4 THEN '<span class=\" ls-sm-margin-rightls-tag-danger\"><b>FALECIDO</b></span>'
            WHEN 5 THEN '<span class=\"ls-sm-margin-right ls-tag-danger\"><b>OUTROS</b></span>'
        END AS situacao_tag,
        CASE va.vinculo_aluno_resultado_final
            WHEN 1 THEN '<span class=\"ls-sm-margin-left ls-tag-success\">APR</span>'
            WHEN 2 THEN '<span class=\"ls-sm-margin-left ls-tag-danger\">REP</span>'
            ELSE ''
        END AS resultado_tag
    FROM smc_aluno a
    INNER JOIN smc_vinculo_aluno va ON va.vinculo_aluno_id_aluno = a.aluno_id
    WHERE va.vinculo_aluno_id_turma = %s
    ORDER BY a.aluno_nome",
    GetSQLValueString($turma_id, "int")
);

$result_alunos = mysql_query($query_alunos, $SmecelNovo) or die(mysql_error());

while ($row_alunos = mysql_fetch_assoc($result_alunos)) {
    $situacao = $row_alunos['vinculo_aluno_situacao'];
    $situacao_tag = $row_alunos['situacao_tag'];
    $resultado = $row_alunos['vinculo_aluno_resultado_final'];
    $resultado_tag = $row_alunos['resultado_tag'];
    
    // Verifica se o aluno está matriculado e não está reprovado
    $pode_arrastar = ($situacao == 1 && $resultado != 2);
    
    echo sprintf(
        '<div id="aluno_%s" class="aluno-item%s" draggable="%s" ondragstart="drag(event)" data-situacao="%s" data-resultado="%s">
            %s
            <div class="situacao-tag">%s</div>
            %s
        </div>',
        $row_alunos['aluno_id'],
        (!$pode_arrastar ? ' nao-matriculado' : ''),
        ($pode_arrastar ? 'true' : 'false'),
        $situacao,
        $resultado,
        $row_alunos['aluno_nome'],
        $situacao_tag,
        $resultado_tag
    );
}

mysql_free_result($result_alunos);
?> 