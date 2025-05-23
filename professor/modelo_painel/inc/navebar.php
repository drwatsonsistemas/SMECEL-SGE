<!-- MENU TOP-->
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
    
    
	<?php if ($row_ProfLogado['func_foto']<>"") { ?>
    <img src="<?php echo URL_BASE; ?>/professor/fotos/<?php echo $row_ProfLogado['func_foto']; ?>" alt="" /> 
	<?php } else { ?>
		<img src="../../img/no-photo-user.jpg" alt="" /> 
         <?php } ?>
        
        
	<span class="ls-name"><?php echo $row_ProfLogado['func_nome']; ?> </span> (<?php echo $row_ProfLogado['func_email']; ?>)
	</a>
      <nav class="ls-dropdown-nav ls-user-menu">
        <ul>
          <li><a href="#.php">MEUS DADOS</a></li>
          <li><a href="#.php">ALTERAR SENHA</a></li>
          <li><a href="<?php echo $logoutAction ?>">SAIR</a></li>
        </ul>
      </nav>
    </div>
  </div>
  <span class="ls-show-sidebar ls-ico-menu"></span> <a href="index.php" class="ls-go-next"><span class="ls-text">Voltar</span></a> 
  <!-- Nome do produto/marca com sidebar -->
  <h1 class="ls-brand-name"> <a href="index.php" class="ls-ico-lamp"> <small><?php echo PREFEITURA; ?></small><?php echo PAINEL; ?></a> </h1>
  <!-- Nome do produto/marca sem sidebar quando for o pre-painel  --> 
</div>
<!-- MENU TOP-->
