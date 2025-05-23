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
        <a href="#">Olá, <?php echo $row_Usulogado['usu_nome']; ?></a>
        <ul class="dropdown">
          <li><a href="#">Dados da Secretaria</a></li>
          <li><a href="<?php echo $logoutAction ?>">Sair</a></li>
        </ul>
      </li>
      
    </ul>

    <!-- Left Nav Section -->
    <ul class="left">

    <li class="divider"></li>
    
    <li><a href="index.php">INÍCIO</a></li>
    
    <li class="divider"></li>
     
      <li class="has-dropdown"><a href="#">MENU 1</a>
        <ul class="dropdown">
          <li class="has-dropdown"><a href="#">MENU 1.1</a>
            <!-- Nested Dropdown -->
            <ul class="dropdown">
              <li><a href="#">MENU 1.1.1</a></li>
              <li><a href="#">MENU 1.1.2</a></li>
            </ul>
          </li>
          
          <li class="has-dropdown"><a href="#">MENU 1.2</a>
            <!-- Nested Dropdown -->
            <ul class="dropdown">
              <li><a href="#">MENU 1.2.1</a></li>
              <li><a href="#">MENU 1.2.2</a></li>
            </ul>
          </li>
                  
        </ul>
      </li>

    <li class="divider"></li>
    <!-- RELATÓRIOS -->

      <li class="has-dropdown"><a href="#">MENU 2</a>
        <ul class="dropdown">
          <li class="has-dropdown"><a href="#">MENU 2.1</a>
            <!-- Nested Dropdown -->
            <ul class="dropdown">
              <li><a href="#">MENU 2.1.1</a></li>
            </ul>
          </li>
          
          <li class="has-dropdown"><a href="#">MENU 2.2</a>
            <!-- Nested Dropdown -->
            <ul class="dropdown">
              <li><a href="#">MENU 2.2.1</a></li>
            </ul>
          </li>
          
          <li class="has-dropdown"><a href="#">MENU 2.3</a>
            <!-- Nested Dropdown -->
            <ul class="dropdown">
              <li><a href="#">MENU 2.3.1</a></li>
              <li><a href="#">MENU 2.3.2</a></li>
            </ul>
          </li>
                  
        </ul>
      </li>

    <li class="divider"></li>

      
    </ul>
  </section>
</nav>
</div>