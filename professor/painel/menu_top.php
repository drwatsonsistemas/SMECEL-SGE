    <nav class="light-blue lighten-1" role="navigation">
    <div class="nav-wrapper container"><a id="logo-container" href="index.php" class="brand-logo"><i class="material-icons">school</i>SMECEL</a>

      <ul class="right hide-on-med-and-down">
      
		<li><a href="<?php echo $logoutAction ?>">SAIR</a></li>
	  
	  </ul>
      

      <ul id="nav-mobile" class="sidenav">
		<li class="divider"></li>
        <li><a href="index.php" class="collection-item purple-text darken-4"><i class="tiny material-icons">person</i> Início</a></li><li class="divider"></li>
        <li><a href="chamada.php" class="collection-item red-text darken-4"><i class="tiny material-icons">location_on</i> Chamada</a></li><li class="divider"></li>
        <li><a href="formacoes.php" class="collection-item black-text darken-5"><i class="tiny material-icons">beenhere</i> Cursos</a></li><li class="divider"></li>
        <li><a href="foto.php" class="collection-item lime-text darken-4"><i class="tiny material-icons">photo_camera</i> Foto</a></li><li class="divider"></li>
        <li><a href="senha.php" class="collection-item blue-text darken-4"><i class="tiny material-icons">lock</i> Senha</a></li><li class="divider"></li>
        <li><a href="tutoriais.php" class="collection-item green-text darken-4"><i class="tiny material-icons">video_library</i> Tutoriais</a></li><li class="divider"></li>
        <li><a class="waves-effect waves-light btn-flat modal-trigger" href="<?php echo $logoutAction ?>">Sair</a></li>
      </ul>
      <a href="#" data-target="nav-mobile" class="sidenav-trigger"><i class="material-icons">menu</i></a>
    </div>
  </nav>
  
 <div class="container">
   <!--<div class="card-panel center">
    <span class="blue-text text-darken-2"><strong>Estamos mudando para o novo o painel do professor.</strong> Algumas funcionalidades ainda estão em migração, mas você já pode testar o novo painel. <br /><br /><a href="../novo_painel/index.php" class="btn purple">clique para acessar</a></span>
  </div>-->
  <div class="card-panel center">
    <span class="blue-text text-darken-2"><strong>Você está no painel do AVA. Para retornar ao painel do professor e acessar outras funcionalidades, clique no botão abaixo. <br /><br /><a href="../novo_painel/index.php" class="btn purple">sair do AVA</a></span>
  </div>
  </div>