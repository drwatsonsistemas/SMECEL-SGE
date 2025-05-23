<?php require_once('../../../Connections/SmecelNovo.php'); ?>
<?php include('../../../sistema/funcoes/inverteData.php'); ?>
<?php include('../../../sistema/funcoes/url_base.php'); ?>
<?php include('../../../sistema/funcoes/idade.php'); ?>
<?php include('../../../sistema/funcoes/anti_injection.php'); ?>
<?php include('session.php'); ?>

<?php
mysql_select_db($database_SmecelNovo, $SmecelNovo);
header('Content-Type: application/json');

// Definir a lista de palavras proibidas
$palavroes = [
    'idiota', 'burro', 'burra', 'estúpido', 'estúpida',
    'imbecil', 'palhaço', 'palhaça', 'merda', 'bosta', 'droga', 'inferno',
    'desgraçado', 'desgraçada', 'miserável', 'otário', 'otária',
    'ridículo', 'ridícula', 'porra', 'caralho', 'vagabundo', 'vagabunda',
    'bunda', 'fdp', 'seu lixo', 'idiotice', 'babaca',
    'cretino', 'cretina', 'nojento', 'nojenta', 'vacilão', 'vacilona',
    'mentecapto', 'mentecapta', 'cabeça de vento', 'pateta',
    'chato', 'chata', 'cabeçudo', 'cabeçuda', 'inútil',
    'zé ninguém', 'lerdo', 'lerda', 'preguiçoso', 'preguiçosa',
    'corno', 'corna', 'safado', 'safada', 'cachorro', 'cachorra',
    'tolice', 'parvo', 'parva', 'fedorento', 'fedorenta',
    'anta', 'lesado', 'lesada', 'tapado', 'tapada',
    'palerma', 'maluco', 'maluca', 'bully', 'covarde', 'moleque', 'molecona',
    'sem vergonha', 'desgraça', 'xoxo', 'bocó', 'piranha', 'vaca',
    'mermão', 'mermã', 'carcaça', 'porcalhão', 'porcalhona',
    'chupador', 'chupadora', 'desumano', 'desumana', 'imoral',
    'desonesto', 'desonesta', 'corrupto', 'corrupta',
    'zé ruela', 'canalha', 'seu merda', 'sua merda', 'bandido', 'bandida',
    'perdedor', 'perdedora', 'topeira', 'porcaria', 'porquinho', 'porquinha',
    'abominável', 'esquerdista', 'esquerdinha', 'coisa ruim', 'negligente',
    'boçal', 'falido', 'falida', 'crápula', 'desajustado', 'desajustada',
    'escória', 'naba', 'peste', 'bestão', 'bestona', 'careta',
    'tolo', 'tola', 'carcaju', 'fanfarrão', 'fanfarrona', 'maricas',
    'calhorda', 'inimigo', 'inimiga', 'chulé', 'analfabeto', 'analfabeta',
    'patuscada', 'esporro', 'lixo', 'encosto', 'não vale nada', 'desculpinha',
    'sabichão', 'sabichona', 'enrolador', 'enroladora', 'esquizofrênico',
    'esquizofrênica', 'patético', 'arrombado', 'arrombada', 'pancada', 'mala',
    'bostoso', 'bostosa', 'macaco', 'pelé', 'porca', 'esculacho',
    'ajudante', 'xexelento', 'meretriz', 'peidão', 'reles', 'imundo',
    'roliço', 'roliça', 'insuportável', 'ignóbil', 'putinha', 'mendigo',
    'mendiga', 'açoite', 'perverso', 'perversa', 'antipático', 'antipática',
    'falso', 'falsa', 'traste', 'desprezível', 'relapso', 'desalmado',
    'insensível', 'frustrado', 'frustrada', 'cúmulo', 'malicioso', 
    'arrogante', 'incapaz', 'caloteiro', 'caloteira', 'fraudador',
    'fraudadora', 'desajeitado', 'desajeitada', 'bastardo', 'bastarda',
    'recalcado', 'recalcada', 'podre', 'desqualificado', 'desqualificada',
    'irresponsável', 'aviltante', 'inconceitual', 'deselegante', 'odioso',
    'insensível', 'desleal', 'abominável', 'escandalosa', 'escandaloso',
    'injusto', 'injusta', 'embusteiro', 'embusteira', 'fraco', 'fraca',
    'inadequado', 'inadequada', 'mau caráter', 'inconveniente', 'gostosa','gostoso','piranha','gostosinha','gostosinho',
	'puta', 'quenga', 'vagabunda', 'pilantra','discarada', 'prostituta','ladrona','safadinha', 'porr4'
];

// Verificar se os dados foram recebidos corretamente
if (isset($_POST['aluno_id']) && isset($_POST['aluno_depoimento'])) {
    
    // Capturar os dados enviados via POST
    $user_id = anti_injection($_POST['aluno_id']);
    $fan_id = anti_injection($row_AlunoLogado['aluno_id']); // Supondo que `$row_AlunoLogado` já esteja definido
    $depoimento = anti_injection($_POST['aluno_depoimento']);
    
    // Verificar se a mensagem contém palavras proibidas
    $mensagem_contaminada = false;
    $palavras_encontradas = [];
    
    foreach ($palavroes as $palavra) {
        if (stripos($depoimento, $palavra) !== false) {
            $mensagem_contaminada = true;
            $palavras_encontradas[] = $palavra;
        }
    }
    
    // Se a mensagem for imprópria, retorna erro e não insere no banco
    if ($mensagem_contaminada) {



        $palavras_encontradas_str = implode(', ', $palavras_encontradas);
        $response = [
            'success' => false,
            //'message' => "Sua mensagem contém as seguintes palavras possivelmente inadequadas: <strong>{$palavras_encontradas_str}</strong>. <br><br>Por favor, revise."
            'message' => "Sua mensagem contém palavras ou termos possivelmente inadequados nesta mensagem. <br><br>Por favor, revise."
        ];

        $insertSQLFlag = sprintf("INSERT INTO smc_aluno_depoimentos_flag (aluno_depoimento_flag_id_de, aluno_depoimento_flag_id_para, aluno_depoimento_flag_texto) 
        VALUES (%s, %s, %s)",
        GetSQLValueString($fan_id, 'int'),
        GetSQLValueString($user_id, 'int'),
        GetSQLValueString($depoimento." Palavras: ".$palavras_encontradas_str, 'text'));

        $Result1Flag = mysql_query($insertSQLFlag, $SmecelNovo);



    } else {
        // Inserir a mensagem no banco de dados, se estiver "limpa"
        $insertSQL = sprintf("INSERT INTO smc_aluno_depoimentos (aluno_depoimento_id_de, aluno_depoimento_id_para, aluno_depoimento_texto) 
            VALUES (%s, %s, %s)",
            GetSQLValueString($fan_id, 'int'),
            GetSQLValueString($user_id, 'int'),
            GetSQLValueString($depoimento, 'text'));

        $Result1 = mysql_query($insertSQL, $SmecelNovo);

        $insertSQLAtualizacao = sprintf("INSERT INTO smc_aluno_notificacao (aluno_notificacao_id_notificado, aluno_notificacao_tipo, aluno_notificacao_id_notificador) 
			VALUES (%s,'2', %s)",
			GetSQLValueString($user_id, 'int'),
			GetSQLValueString($fan_id, 'int'));
		$Result1Atualizacao = mysql_query($insertSQLAtualizacao, $SmecelNovo) or die(mysql_error());

        // Verificar se a inserção foi bem-sucedida
        if ($Result1) {
            $response = ['success' => true, 'message' => 'Depoimento enviado com sucesso!'];
        } else {
            $response = ['success' => false, 'message' => 'Erro ao enviar depoimento: ' . mysql_error()];
        }
    }
} else {
    $response = ['success' => false, 'message' => 'Dados não recebidos.'];
}

// Retornar a resposta em formato JSON
echo json_encode($response);
exit; // Garante que nada mais será enviado após o JSON
?>
