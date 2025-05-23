<?php 
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Atualizacoes = "SELECT atualizacoes_id, atualizacoes_painel, atualizacoes_modulo, atualizacoes_texto, atualizacoes_data FROM smc_atualizacoes WHERE atualizacoes_painel <> '99' ORDER BY atualizacoes_id DESC LIMIT 0,1";
$Atualizacoes = mysql_query($query_Atualizacoes, $SmecelNovo) or die(mysql_error());
$row_Atualizacoes = mysql_fetch_assoc($Atualizacoes);
$totalRows_Atualizacoes = mysql_num_rows($Atualizacoes);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AtualizacoesVisualizadas = "SELECT atualizacao_ver_id, atualizacao_ver_cod_atualizacao, atualizacao_ver_cod_usuario, atualizacao_ver_sec, atualizacao_ver_escola, atualizacao_ver_professor, atualizacao_ver_aluno, atualizacao_ver_data FROM smc_atualizacao_ver WHERE atualizacao_ver_cod_atualizacao = '$row_Atualizacoes[atualizacoes_id]' AND atualizacao_ver_sec = '$row_Secretaria[sec_id]' AND atualizacao_ver_cod_usuario = '$row_UsuarioLogado[usu_id]'";
$AtualizacoesVisualizadas = mysql_query($query_AtualizacoesVisualizadas, $SmecelNovo) or die(mysql_error());
$row_AtualizacoesVisualizadas = mysql_fetch_assoc($AtualizacoesVisualizadas);
$totalRows_AtualizacoesVisualizadas = mysql_num_rows($AtualizacoesVisualizadas);
?>

<div class="ls-topbar"> 
<script src="js/telaCheia.js" type="text/javascript"></script>
  
  <!-- Barra de Notificações -->
  <div class="ls-notification-topbar"> 
    
    <!-- Links de apoio -->
    <div class="ls-alerts-list"> 
    <a href="#" class="ls-ico-bell-o" data-counter="8" data-ls-module="topbarCurtain" data-target="#ls-notification-curtain"><span>Notificações</span></a> 
    <a href="#" class="ls-ico-bullhorn" data-ls-module="topbarCurtain" data-target="#ls-help-curtain"><span>Ajuda</span></a> 
    <a href="#" class="ls-ico-question" data-ls-module="topbarCurtain" data-target="#ls-feedback-curtain"><span>Sugestões</span></a>
    <a href="#" class="ls-ico-screen" onclick="toggleFullScreen()"><span>Tela Cheia</span></a> </div>
    
    <!-- Dropdown com detalhes da conta de usuário -->
    <div data-ls-module="dropdown" class="ls-dropdown ls-user-account"> 
    <a href="#" class="ls-ico-user"> 
	<?php if ($row_Secretaria['sec_logo'] <> "") { ?>
          <img src="../../img/logo/secretaria/<?php echo $row_Secretaria['sec_logo']; ?>" alt="" />
	  <?php } else { ?>
		<img src="../../img/no-photo-user.jpg" alt="" /> 
	  <?php } ?>
	<span class="ls-name"><?php echo $row_UsuarioLogado['usu_nome']; ?> </span> (<?php echo $row_UsuarioLogado['usu_email']; ?>)
	</a>
      <nav class="ls-dropdown-nav ls-user-menu">
        <ul>
          <li><a href="mudar_escola.php">Acessar Painel Escolar</a></li>
          <li><a href="logs.php">Registros de acesso</a></li>
          <li><a href="registros.php">Registros de atividades</a></li>
          <li><a href="chamados.php">Chamados ao suporte</a></li>
          <li><a href="dados.php">Dados da secretaria</a></li>
          <li><a href="meus_dados.php">Meus dados</a></li>
          <li><a href="atualizacoes.php">Notas da versão
		  
	<?php if ($totalRows_AtualizacoesVisualizadas == 0) { ?>
		<span class="ls-tag-danger">NOVAS</span>
	<?php } ?>
		  
		  </a></li>
          <li><a href="tutoriais_video.php">Tutoriais</a></li>
          <li><a target="_blank" href="../pse">Painel PSE</a></li>
          <li><a target="_blank" href="../ctutelar">Painel C. Tutelar</a></li>
          <li><a href="<?php echo $logoutAction ?>">Sair</a></li>
        </ul>
      </nav>
    </div>
  </div>
  <span class="ls-show-sidebar ls-ico-menu"></span> <a href="index.php" class="ls-go-next"><span class="ls-text">Voltar à lista de serviços</span></a> 
  
  <!-- Nome do produto/marca com sidebar -->
  <h1 class="ls-brand-name"> <a href="index.php" class="ls-ico-home"> <small>SISTEMA DE GESTÃO ESCOLAR</small><?php echo $row_Secretaria['sec_nome']; ?></a> </h1>
  
  <!-- Nome do produto/marca sem sidebar quando for o pre-painel  --> 
</div>