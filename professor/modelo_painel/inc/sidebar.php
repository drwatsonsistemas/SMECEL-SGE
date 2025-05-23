<aside class="ls-sidebar">
  <div class="ls-sidebar-inner"> <a href="index.php" class="ls-go-prev"><span class="ls-text">VOLTAR AO INÍCIO</span></a>
    <nav class="ls-menu">
      <ul>
      	
		<?php if ($row_ProfLogado['func_foto']<>"") { ?>
        <li class="ls-display-none-sm1"><img src="<?php echo URL_BASE; ?>/professor/fotos/<?php echo $row_ProfLogado['func_foto']; ?>" alt="" style="width: 100%; height: 200px; object-fit: cover; object-position: 0px -30px;"></li>
		<?php } ?>
		 
         
        <li><a href="index.php" class="ls-ico-dashboard" title="Dashboard">INÍCIO</a></li>
        <li> <a href="#" class="ls-ico-docs" title="DIÁRIO">DIÁRIO</a>
          <ul>
            <li><a href="#.php" class="">AULAS</a></li>
            <li><a href="#.php" class="">FEQUÊNCIA</a></li>
            <li><a href="#.php" class="">PLANEJAMENTO</a></li>
            <li><a href="#.php" class="">RENDIMENTO</a></li>
          </ul>
        </li>
        <li><a href="#.php" class="ls-ico-dashboard" title="Dashboard">CONFIGURAÇÕES</a></li>        
      </ul>
    </nav>
  </div>
</aside>