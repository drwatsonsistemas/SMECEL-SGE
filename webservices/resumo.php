<?php require_once('../Connections/SmecelNovo.php'); ?>
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

//Drw4tson@smecel12345 
//info@smecel.com.br


//mysql_set_charset("utf8");

$sec = "1";
if (isset($_GET['sec'])) {
  $sec = $_GET['sec'];
}

//SECRETARIA
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT * FROM smc_sec WHERE sec_id = '$sec'";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

//ANO LETIVO
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AnoLetivo = "SELECT ano_letivo_id, ano_letivo_ano, ano_letivo_inicio, ano_letivo_fim, ano_letivo_aberto, ano_letivo_id_sec, ano_letivo_data_rematricula FROM smc_ano_letivo WHERE ano_letivo_id_sec = '$row_Secretaria[sec_id]' AND ano_letivo_aberto = 'S' ORDER BY ano_letivo_ano DESC LIMIT 1";
$AnoLetivo = mysql_query($query_AnoLetivo, $SmecelNovo) or die(mysql_error());
$row_AnoLetivo = mysql_fetch_assoc($AnoLetivo);
$totalRows_AnoLetivo = mysql_num_rows($AnoLetivo);

//MATRICULAS ATIVAS
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_MatriculasAtivas = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, 
vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, 
vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, 
vinculo_aluno_vacina_atualizada, escola_id, escola_situacao,
turma_id, turma_tipo_atendimento,turma_ano_letivo 
FROM smc_vinculo_aluno
INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
WHERE turma_tipo_atendimento = '1' AND vinculo_aluno_id_sec = '$row_Secretaria[sec_id]' AND vinculo_aluno_situacao = '1' AND vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND escola_situacao = '1'";
$MatriculasAtivas = mysql_query($query_MatriculasAtivas, $SmecelNovo) or die(mysql_error());
$row_MatriculasAtivas = mysql_fetch_assoc($MatriculasAtivas);
$totalRows_MatriculasAtivas = mysql_num_rows($MatriculasAtivas);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turmas = "
SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo, turma_tipo_atendimento, escola_id, escola_situacao
FROM smc_turma
INNER JOIN smc_escola ON escola_id = turma_id_escola  
WHERE turma_tipo_atendimento = '1' AND turma_id_sec = '$row_Secretaria[sec_id]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND escola_situacao = '1'";
$Turmas = mysql_query($query_Turmas, $SmecelNovo) or die(mysql_error());
$row_Turmas = mysql_fetch_assoc($Turmas);
$totalRows_Turmas = mysql_num_rows($Turmas);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_TurmasAee = "
SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo, turma_tipo_atendimento, escola_id, escola_situacao
FROM smc_turma
INNER JOIN smc_escola ON escola_id = turma_id_escola  
WHERE turma_id_sec = '$row_Secretaria[sec_id]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND escola_situacao = '1' AND turma_tipo_atendimento = '2'";
$TurmasAee = mysql_query($query_TurmasAee, $SmecelNovo) or die(mysql_error());
$row_TurmasAee = mysql_fetch_assoc($TurmasAee);
$totalRows_TurmasAee = mysql_num_rows($TurmasAee);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_TurmasAc = "
SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo, turma_tipo_atendimento, escola_id, escola_situacao
FROM smc_turma
INNER JOIN smc_escola ON escola_id = turma_id_escola  
WHERE turma_id_sec = '$row_Secretaria[sec_id]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND escola_situacao = '1' AND turma_tipo_atendimento = '3'";
$TurmasAc = mysql_query($query_TurmasAc, $SmecelNovo) or die(mysql_error());
$row_TurmasAc = mysql_fetch_assoc($TurmasAc);
$totalRows_TurmasAc = mysql_num_rows($TurmasAc);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Funcionarios = "
SELECT vinculo_id, vinculo_id_escola, vinculo_id_funcionario, vinculo_id_funcao, vinculo_carga_horaria, vinculo_data_inicio, vinculo_obs,
func_id, func_id_sec 
FROM smc_vinculo
INNER JOIN smc_func ON func_id = vinculo_id_funcionario
WHERE func_id_sec = '$row_Secretaria[sec_id]'";
$Funcionarios = mysql_query($query_Funcionarios, $SmecelNovo) or die(mysql_error());
$row_Funcionarios = mysql_fetch_assoc($Funcionarios);
$totalRows_Funcionarios = mysql_num_rows($Funcionarios);


$date_fim = date("Y-m-d"); // Data final
//$date_fim = date("Y-m-d", strtotime("-1 day"));
$date_ini = date("Y-m-d", strtotime("-7 days", strtotime($date_fim))); // Data inicial

$date_fim_br = date("d/m/Y", strtotime($date_fim));
$date_ini_br = date("d/m/Y", strtotime($date_ini));

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matriculas = "
SELECT 
    vinculo_aluno_data, 
    vinculo_aluno_situacao, 
    vinculo_aluno_datatransferencia,
    turma_id, 
    turma_tipo_atendimento 
FROM smc_vinculo_aluno
INNER JOIN smc_turma 
    ON turma_id = vinculo_aluno_id_turma
WHERE 
    turma_tipo_atendimento = '1' 
    AND vinculo_aluno_situacao = '1' 
    AND vinculo_aluno_data BETWEEN '$date_ini' AND '$date_fim'
    AND vinculo_aluno_id_sec = '$row_Secretaria[sec_id]'
";
$Matriculas = mysql_query($query_Matriculas, $SmecelNovo) or die(mysql_error());
$totalRows_Matriculas = mysql_num_rows($Matriculas);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_MatriculasT = "
SELECT 
    vinculo_aluno_data, 
    vinculo_aluno_situacao, 
    vinculo_aluno_datatransferencia,
    turma_id, 
    turma_tipo_atendimento 
FROM smc_vinculo_aluno
INNER JOIN smc_turma 
    ON turma_id = vinculo_aluno_id_turma
WHERE 
    turma_tipo_atendimento = '1' 
    AND vinculo_aluno_situacao = '2' 
    AND vinculo_aluno_datatransferencia BETWEEN '$date_ini' AND '$date_fim'
    AND vinculo_aluno_id_sec = '$row_Secretaria[sec_id]'
";
$MatriculasT = mysql_query($query_MatriculasT, $SmecelNovo) or die(mysql_error());
$totalRows_MatriculasT = mysql_num_rows($MatriculasT);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_MatriculasPorEscolas = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, 
vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, escola_id, escola_nome, escola_ue, escola_situacao, COUNT(*) AS total,
turma_id, turma_tipo_atendimento  
FROM smc_vinculo_aluno
INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
WHERE turma_tipo_atendimento = '1' AND escola_ue = '1' AND vinculo_aluno_id_sec = '$row_Secretaria[sec_id]' AND vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND vinculo_aluno_situacao = '1' AND escola_situacao = '1'
GROUP BY escola_id, escola_nome, escola_ue, escola_situacao";
$MatriculasPorEscolas = mysql_query($query_MatriculasPorEscolas, $SmecelNovo) or die(mysql_error());
$row_MatriculasPorEscolas = mysql_fetch_assoc($MatriculasPorEscolas);
$totalRows_MatriculasPorEscolas = mysql_num_rows($MatriculasPorEscolas);

$matriculasPorEscolaTexto = "";
if ($totalRows_MatriculasPorEscolas > 0) {
    do {
        $escolaNome = $row_MatriculasPorEscolas['escola_nome'];
        $totalAlunos = $row_MatriculasPorEscolas['total'];
        $matriculasPorEscolaTexto .= "- $escolaNome: <strong>$totalAlunos</strong><br>";
    } while ($row_MatriculasPorEscolas = mysql_fetch_assoc($MatriculasPorEscolas));
} else {
    $matriculasPorEscolaTexto = "Nenhuma matrícula encontrada.";
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_MatriculasPorEtapa = "
SELECT 
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, 
vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, 
vinculo_aluno_vacina_atualizada, turma_id, turma_etapa, turma_tipo_atendimento, turma_ano_letivo, etapa_id, etapa_nome, etapa_nome_abrev, COUNT(*) AS total, escola_id, escola_situacao 
FROM smc_vinculo_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
INNER JOIN smc_etapa ON etapa_id = turma_etapa
INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola
WHERE turma_tipo_atendimento = '1' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND vinculo_aluno_situacao = '1' AND vinculo_aluno_id_sec = '$row_Secretaria[sec_id]' AND escola_situacao = '1'
GROUP BY etapa_id, etapa_nome, etapa_nome_abrev
ORDER BY etapa_id 
";
$MatriculasPorEtapa = mysql_query($query_MatriculasPorEtapa, $SmecelNovo) or die(mysql_error());
$row_MatriculasPorEtapa = mysql_fetch_assoc($MatriculasPorEtapa);
$totalRows_MatriculasPorEtapa = mysql_num_rows($MatriculasPorEtapa);

$matriculasPorEtapaTexto = "";
if ($totalRows_MatriculasPorEtapa > 0) {
    do {
        $etapaNome = $row_MatriculasPorEtapa['etapa_nome'];
        $etapaAbrev = $row_MatriculasPorEtapa['etapa_nome_abrev'];
        $totalAlunosEtapa = $row_MatriculasPorEtapa['total'];
        $matriculasPorEtapaTexto .= "- $etapaNome ($etapaAbrev): <strong>$totalAlunosEtapa</strong><br>";
    } while ($row_MatriculasPorEtapa = mysql_fetch_assoc($MatriculasPorEtapa));
} else {
    $matriculasPorEtapaTexto = "Nenhuma matrícula encontrada.";
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AlunosSituacao = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola,  vinculo_aluno_id_sec,
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, COUNT(vinculo_aluno_id) AS total,
turma_id, turma_tipo_atendimento,turma_ano_letivo,escola_id, escola_situacao,
CASE vinculo_aluno_situacao
WHEN 1 THEN 'Matriculados'
WHEN 2 THEN 'Transferidos'
WHEN 3 THEN 'Desistentes'
WHEN 4 THEN 'Falecidos'
WHEN 5 THEN 'Outros'
END AS vinculo_aluno_situacao
FROM smc_vinculo_aluno 
INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
WHERE vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND vinculo_aluno_id_sec = '$row_Secretaria[sec_id]' 
AND escola_situacao = '1' AND turma_tipo_atendimento = '1'
GROUP BY vinculo_aluno_situacao ASC";
$AlunosSituacao = mysql_query($query_AlunosSituacao, $SmecelNovo) or die(mysql_error());
$row_AlunosSituacao = mysql_fetch_assoc($AlunosSituacao);
$totalRows_AlunosSituacao = mysql_num_rows($AlunosSituacao);

$alunosPorSituacaoTexto = "";
if ($totalRows_AlunosSituacao > 0) {
    do {
        $situacao = $row_AlunosSituacao['vinculo_aluno_situacao'];
        $totalAlunosSituacao = $row_AlunosSituacao['total'];
        
        // Formatando a saída com cor, se necessário
        $alunosPorSituacaoTexto .= "- $situacao: <strong>$totalAlunosSituacao</strong><br>";
        
    } while ($row_AlunosSituacao = mysql_fetch_assoc($AlunosSituacao));
} else {
    $alunosPorSituacaoTexto = "Nenhuma situação de aluno encontrada.";
}


$nome = $row_Secretaria['sec_nome_secretario'];
$primeiroNome = explode(" ",$nome);
$email = $row_Secretaria['sec_email'];
$cidade = ucwords(strtolower($row_Secretaria['sec_cidade']));
$anoLetivo = $row_AnoLetivo['ano_letivo_ano'];

	


require_once("../classes/class.phpmailer.php");
 
$mail = new PHPMailer(true);
 
$mail->IsSMTP(); // Define que a mensagem será SMTP
 
try {
     $mail->Host = 'smtp.smecel.com.br'; // Endereço do servidor SMTP (Autenticação, utilize o host smtp.seudomínio.com.br)
     $mail->SMTPAuth   = true;  // Usar autenticação SMTP (obrigatório para smtp.seudomínio.com.br)
     $mail->Port       = 587; //  Usar 587 porta SMTP
     $mail->Username = 'info@smecel.com.br'; // Usuário do servidor SMTP (endereço de email)
     $mail->Password = 'Drw4tson@smecel12345'; // Senha do servidor SMTP (senha do email usado)
	   $mail->IsHTML(true);
	   $mail->CharSet = "UTF-8";
     $mail->SetFrom('info@smecel.com.br', 'ATUALIZAÇÕES SMECEL'); //Seu e-mail
     $mail->Subject = "SMECEL | Resumo semanal";//Assunto do e-mail
     $mail->AddAddress($email);
     $mail->AddBCC('rcamaral@outlook.com'); // Cópia Oculta
     $mail->MsgHTML("
      Olá, $primeiroNome[0]!<br><br>

      Acompanhe o resumo semanal da Rede Municipal de Ensino em $cidade.<br>

      <blockquote>

      -Ano letivo: <strong>$anoLetivo</strong><br><br>
      -Período: entre <strong>$date_ini_br</strong> e <strong>$date_fim_br</strong><br><br>

      -Matrículas realizadas na última semana: <strong>$totalRows_Matriculas</strong><br>
      -Transferências realizadas na última semana: <strong>$totalRows_MatriculasT</strong><br><br>
      
      -Total de turmas de Ensino Regular: <strong>$totalRows_Turmas</strong><br>
      -Total de turmas de Atividade Educacional Especializada: <strong>$totalRows_TurmasAee</strong><br>
      -Total de turmas de Atividade Complementar: <strong>$totalRows_TurmasAc</strong><br><br>
      
      -Total de funcionários vinculados: <strong>$totalRows_Funcionarios</strong><br><br>
      
      Total de matrículas por escola:<br> $matriculasPorEscolaTexto<br>

      Total de matrículas por etapa de ensino:<br> $matriculasPorEtapaTexto<br>

      Total de matrículas por situação:<br> $alunosPorSituacaoTexto<br>

      -Total de matrículas ativas: <strong>$totalRows_MatriculasAtivas</strong><br><br>

      </blockquote>

      <i>Para informações mais detalhadas, acesse o painel administrativo do seu município utilizando seu login e senha.</i>

      <p>Atenciosamente,<br><br>Equipe de suporte.<br>www.smecel.com.br</p>
      <img src=\"https://www.smecel.com.br/img/logo_smecel_background_flattened.png\" width=\"150\">
    "); 
 
     ////Caso queira colocar o conteudo de um arquivo utilize o método abaixo ao invés da mensagem no corpo do e-mail.
     //$mail->MsgHTML(file_get_contents('arquivo.html'));
 
     $mail->Send();
 		
	   $mail->ClearAllRecipients();
     $mail->ClearAttachments();
		
    }catch (phpmailerException $e) {
      echo $e->errorMessage(); //Mensagem de erro costumizada do PHPMailer
}
	
    //header("Location: ". $MM_redirectLoginFailed );


mysql_free_result($Secretaria);
mysql_free_result($AnoLetivo);
?>
