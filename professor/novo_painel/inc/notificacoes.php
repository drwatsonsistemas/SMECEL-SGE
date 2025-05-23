<?php
try {
    // Consulta para obter os comunicados
    $query_comunicados = "
        SELECT com_topico_id, com_topico_id_escola, com_topico_id_prof, com_topico_data, com_topico_texto, 
               com_topico_hash, com_topico_atualizacao, com_topico_visualizado, com_topico_quem, 
               func_id, func_nome, escola_id, escola_nome  
        FROM comun_esc_prof_topico
        INNER JOIN smc_func ON func_id = com_topico_id_prof
        INNER JOIN smc_escola ON escola_id = com_topico_id_escola
        WHERE com_topico_id_prof = :prof_id AND com_topico_visualizado = 'N' AND com_topico_quem = 'C'
        ORDER BY com_topico_atualizacao DESC";

    // Preparar a consulta
    $stmt = $SmecelNovo->prepare($query_comunicados);
    $stmt->bindParam(':prof_id', $row_ProfLogado['func_id'], PDO::PARAM_INT);
    $stmt->execute();

    // Obter os resultados
    $comunicados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $totalRows_comunicados = count($comunicados);

} catch (PDOException $e) {
    die("Erro ao carregar os comunicados: " . $e->getMessage());
}
?>

<aside class="ls-notification">
  <nav class="ls-notification-list" id="ls-notification-curtain" style="left: 1716px;">
    <h3 class="ls-title-2">Notificações</h3>
    <ul>
      <?php if ($totalRows_comunicados > 0) { ?>
        <li class="ls-dismissable">
          <a href="mensagens.php">
            Há <?php echo $totalRows_comunicados; ?> 
            <?php echo $totalRows_comunicados == 1 ? "mensagem" : "mensagens"; ?> para você
          </a>
        </li>
      <?php } ?>
    </ul>
  </nav>
  <nav class="ls-notification-list" id="ls-help-curtain" style="left: 1756px;">
    <h3 class="ls-title-2">Feedback</h3>
    <ul>
      <li><a href="../painel/index.php" target="_blank">&gt; Voltar para o painel antigo</a></li>
      <li><a href="https://webmail.smecel.com.br" target="_blank">&gt; Acesse o webmail</a></li>
    </ul>
  </nav>
  <nav class="ls-notification-list" id="ls-feedback-curtain" style="left: 1796px;">
    <h3 class="ls-title-2">Ajuda</h3>
    <ul>
      <li><a href="tutoriais_video.php">&gt; Tutoriais</a></li>
    </ul>
  </nav>
</aside>
