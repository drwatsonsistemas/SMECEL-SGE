<aside class="ls-sidebar">
  <div class="ls-sidebar-inner"> <a href="index.php" class="ls-go-prev"><span class="ls-text">VOLTAR AO INÍCIO</span></a>
    <nav class="ls-menu">
      <ul>
      	
		<?php if ($row_ProfLogado['func_foto']<>"") { ?>
        <li class="ls-display-none-sm1"><img src="<?php echo URL_BASE; ?>/professor/fotos/<?php echo $row_ProfLogado['func_foto']; ?>" alt="" style="width: 100%; height: 200px; object-fit: cover; object-position: 0px -30px;"></li>
		<?php } ?>
		 
         
        <li><a href="index.php" class="ls-ico-dashboard" title="Dashboard">INÍCIO</a></li>

        <li class="ls-submenu-parent" aria-expanded="false" aria-hidden="true">
            <a href="#" class="ls-ico-list" title="Configurações" role="menuitem">DIÁRIO DIGITAL</a>
            <ul class="ls-submenu" role="menu">
              <li><a href="aulas_calendario.php?target=aulas" class="ls-submenu-item ls-ico-chevron-right" role="menuitem">CONTEÚDO</a></li>
              <li><a href="selecionar.php?target=frequencia&data=<?php echo date("Y-m-d"); ?>" class="ls-submenu-item ls-ico-chevron-right" role="menuitem">FREQUÊNCIA</a></li>
              <li><a href="rendimento.php" class="ls-submenu-item ls-ico-chevron-right" role="menuitem">RENDIMENTO</a></li>
            </ul>
          </li>

        <li><a href="planejamento_mapa.php" class="ls-ico-chart-bar-up">PLANEJAMENTO</a></li>
        <li><a href="ocorrencia.php" class="ls-ico-bullhorn">OCORRÊNCIA</a></li>

        <li class="ls-submenu-parent" aria-expanded="false" aria-hidden="true">
            <a href="#" class="ls-ico-cog" title="Configurações" role="menuitem">EXTRAS</a>
            <ul class="ls-submenu" role="menu">
              <li><a href="senha.php" class="ls-submenu-item ls-ico-chevron-right" role="menuitem">ALTERAR SENHA</a></li>
              <li><a href="foto.php" class="ls-submenu-item ls-ico-chevron-right" role="menuitem">ALTERAR FOTO</a></li>
              <li><a href="pauta.php" class="ls-submenu-item ls-ico-chevron-right" role="menuitem">HTP - HORAS DE TRABALHO PEDAGÓGICO</a></li>
              <li><a href="material_apoio.php" class="ls-submenu-item ls-ico-chevron-right" role="menuitem">MATERIAL APOIO</a></li>
              <li><a href="planejamento_mapa.php" class="ls-submenu-item ls-ico-chevron-right" role="menuitem">CALENDÁRIO</a></li>
              <li><a href="mensagens.php" class="ls-submenu-item ls-ico-chevron-right" role="menuitem">COMUNICAÇÃO</a></li>
              <li><a href="grade_analitica.php" class="ls-submenu-item ls-ico-chevron-right" role="menuitem">HORÁRIOS</a></li>
              <li><a href="logins.php" class="ls-submenu-item ls-ico-chevron-right" role="menuitem">LOGINS</a></li>
              <li><a href="avisos.php" class="ls-submenu-item ls-ico-chevron-right" role="menuitem">AVISOS</a></li>
              <li><a href="../painel/index.php" class="ls-submenu-item ls-ico-chevron-right" role="menuitem">AVA</a></li>
              <li><a href="tutoriais_video.php" class="ls-submenu-item ls-ico-chevron-right" role="menuitem">TUTORIAIS</a></li>
            </ul>
          </li>

        

        <!--<li><a href="#" class="ls-ico-dashboard" title="Dashboard">CONFIGURAÇÕES</a></li>-->        
      </ul>
    </nav>
  </div>
</aside>