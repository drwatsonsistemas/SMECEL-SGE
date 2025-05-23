<div class="contain-to-grid fixed">
<nav class="top-bar" data-topbar role="navigation">
  <ul class="title-area">
    <li class="name">
      <h1><a href="index.php">Sistema Escolar</a></h1>
    </li>
     <!-- Remove the class "menu-icon" to get rid of menu icon. Take out "Menu" to just have icon alone -->
    <li class="toggle-topbar menu-icon"><a href="#"><span>Menu</span></a></li>
  </ul>

  <section class="top-bar-section">
    <!-- Right Nav Section -->
    <ul class="right">
      <li class="has-dropdown">
        <a href="#">Olá, <?php echo $row_UsuarioLogado['usu_nome']; ?></a>
        <ul class="dropdown">
          <li><a href="<?php echo $logoutAction ?>">Sair</a></li>
        </ul>
      </li>
      
    </ul>

    <!-- Left Nav Section -->
    <ul class="left">

    <li class="divider"></li>
    
    <li><a href="index.php">Início</a></li>
    <!-- INÍCIO -->
		
    <li class="divider"></li>
     
      <li class="has-dropdown"><a href="#">Principal</a>
        <ul class="dropdown">
          <li class="has-dropdown"><a href="#">Atividades</a>
            <!-- Nested Dropdown -->
            <ul class="dropdown">
              <li><a href="atividadesListarHoje.php">Listar</a></li>
            </ul>
          </li> 
          <li class="has-dropdown"><a href="#">Validação</a>
            <!-- Nested Dropdown -->
            <ul class="dropdown">
              <li><a href="atividadesListarCorrecao.php">Listar</a></li>
            </ul>
          </li>        
        </ul>
      </li>

    <li class="divider"></li>

	<li><a href="graficos.php">Gráficos</a></li>
    <!-- GRÁFICOS -->
  
    <li class="divider"></li>
    <!-- RELATÓRIOS -->

      <li class="has-dropdown"><a href="#">Relatórios</a>
		<ul class="dropdown">
          
          <li class="has-dropdown"><a href="#">Turmas</a>
            <!-- Nested Dropdown -->
            <ul class="dropdown">
              <li><a href="rel_turmas_escola.php">Por escola</a></li>
              <li><a href="rel_turmas_etapa.php">Por Etapa</a></li>
              <li><a href="rel_turmas_filtro_etapa.php">Por Filtro</a></li>
            </ul>
          </li>
          <li class="has-dropdown"><a href="#">Alunos</a>
            <!-- Nested Dropdown -->
            <ul class="dropdown">
              <li><a href="rel_alunos_vinculo_escola.php">Por escola</a></li>
            </ul>
          </li>
          <li class="has-dropdown"><a href="#">Atividades</a>
            <!-- Nested Dropdown -->
            <ul class="dropdown">
              <li><a href="rel_atividadesListarData.php">Por escola</a></li>
            </ul>
          </li>
        </ul>
      </li>

    <li class="divider"></li>
	

      
    </ul>
  </section>
</nav>
</div>

<br />