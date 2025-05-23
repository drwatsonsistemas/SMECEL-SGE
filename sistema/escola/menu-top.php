<div class="ls-topbar ">
  <div class="ls-notification-topbar">
    <div class="ls-alerts-list"> 
    <a href="#" class="ls-ico-bell-o" data-counter="8" data-ls-module="topbarCurtain" data-target="#ls-notification-curtain"><span>Notificações</span></a> 
    <a href="#" class="ls-ico-bullhorn" data-ls-module="topbarCurtain" data-target="#ls-help-curtain"><span>Ajuda</span></a> 
    <a href="#" class="ls-ico-question" data-ls-module="topbarCurtain" data-target="#ls-feedback-curtain"><span>Sugestões</span></a> 
    </div>
    <div data-ls-module="dropdown" class="ls-dropdown ls-user-account"> <a href="#" class="ls-ico-user"> <img src="../../img/funcionarios/<?php echo $row_UsuLogado['usu_foto']; ?>" alt="" /> <span class="ls-name"><?php echo $row_UsuLogado['usu_nome']; ?></span> (Cód usuário: <?php echo $row_UsuLogado['usu_id']; ?>) </a>
      <nav class="ls-dropdown-nav ls-user-menu">
        <ul>
          <li><a href="#">Código da Escola: <b>(<?php echo $row_EscolaLogada['escola_id']; ?>)</b></a></li>
          <li><a href="trocar_escola.php">Mudar escola</a></li>
          <li><a href="dados.php">Meus dados</a></li>
          <li><a href="chamados.php">Suporte / Helpdesk</a></li>
          <li><a href="atualizacoes.php">Notas da versão
      <?php $atualizacao = "N";  if ($totalRows_AtualizacoesVisualizadas == 0) { $atualizacao = "S"; ?>
        <span class="ls-tag-danger">NOVAS</span>
      <?php } ?>
		  </a></li>
          <li><a href="tutoriais_video.php">Tutoriais</a></li>
          <li><a href="<?php echo $logoutAction ?>">Sair</a></li>
        </ul>
      </nav>
    </div>
  </div>
  <span class="ls-show-sidebar ls-ico-menu"></span> <a href="index.php" class="ls-go-next"><span class="ls-text">Voltar à lista de serviços</span></a>
  <h1 class="ls-brand-name"> <a href="index.php" class="ls-ico-home"> <small>SECRETARIA MUNICIPAL DE EDUCAÇÃO</small> <?php echo $row_EscolaLogada['escola_nome']; ?> </a> </h1>
</div>
