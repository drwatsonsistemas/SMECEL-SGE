<?php 
  $query_Notificacoes = "SELECT * FROM smc_aluno_notificacao WHERE aluno_notificacao_id_notificado = $row_AlunoLogado[aluno_id] AND aluno_notificacao_visualizado = 'N'";
  $Notificacoes = mysql_query($query_Notificacoes, $SmecelNovo) or die(mysql_error());
  $row_Notificacoes = mysql_fetch_assoc($Notificacoes);
  $totalRows_Notificacoes = mysql_num_rows($Notificacoes);
?>
<nav class="<?= $row_Perfil['aluno_cor_fundo'] ?> darken-3" role="navigation">
  <div class="nav-wrapper container"> <a id="logo-container" href="perfil.php" class="brand-logo">EduConnect</a>

  <div class="notification-icon1 right">
    <a href="notificacoes.php"><i class="material-icons">notifications</i><?php if ($totalRows_Notificacoes > 0) { ?><span class="notification-badge"><?php echo $totalRows_Notificacoes; ?></span><?php } ?></a> <!-- Número de notificações -->
  </div>
    
	<ul class="right hide-on-med-and-down">
      <li class="right"><a class="waves-effect waves-light btn-flat white-text modal-trigger" href="<?php echo $logoutAction ?>"><i class="material-icons left">lock_outline</i>SAIR</a></li>
    </ul>
	
    <ul id="nav-mobile" class="sidenav">

	<li class="divider"></li>

  <!--<li><a href="home.php" class="collection-item blue-text" style="font-size: 1em;"><i class="material-icons left">home</i>HOME</a></li><li class="divider"></li>-->
  <li><a href="perfil.php" class="collection-item brown-text" style="font-size: 1em;"><i class="material-icons left">person</i>MEU PERFIL</a></li><li class="divider"></li>
  <li><a href="mural.php" class="collection-item blue-text" style="font-size: 1em;"><i class="material-icons left">message</i>MEUS RECADOS</a></li><li class="divider"></li>
    <li><a href="fans.php" class="collection-item orange-text" style="font-size: 1em;"><i class="material-icons left">star_border</i>MEUS FÃS</a></li><li class="divider"></li>
    <li><a href="favoritos.php" class="collection-item green-text" style="font-size: 1em;"><i class="material-icons left">favorite_border</i>MEUS FAVORITOS</a></li><li class="divider"></li>
    <!--<li><a href="" class="collection-item purple-text" style="font-size: 1em;"><i class="material-icons left">stars</i>CONQUISTAS</a></li><li class="divider"></li>-->

	<li><a href="index.php" class="collection-item black-text darken-4" style="font-size: 1em;"><i class="material-icons left">arrow_back</i>VOLTAR</a></li><li class="divider"></li>

	<li><a class="collection-item modal-trigger" style="font-size: 1em;" href="<?php echo $logoutAction ?>"><i class="material-icons left">lock_outline</i>SAIR</a></li><li class="divider"></li>
    
	</ul>
    <a href="#" data-target="nav-mobile" class="sidenav-trigger"><i class="material-icons">menu</i></a> 
	</div>
</nav>
