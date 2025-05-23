<?php 
  $query_Notificacoes = "SELECT * FROM smc_aluno_notificacao WHERE aluno_notificacao_id_notificado = $row_AlunoLogado[aluno_id] AND aluno_notificacao_visualizado = 'N'";
  $Notificacoes = mysql_query($query_Notificacoes, $SmecelNovo) or die(mysql_error());
  $row_Notificacoes = mysql_fetch_assoc($Notificacoes);
  $totalRows_Notificacoes = mysql_num_rows($Notificacoes);
?>
<nav class="blue darken-4" role="navigation">
  <div class="nav-wrapper container"> <a id="logo-container" href="index.php" class="brand-logo"><i class="material-icons">home</i> SMECEL</a>
    
  <?php if ($row_AlunoLogado['aluno_aceite_termos']=="S") { ?>
  <div class="notification-icon1 right">
    <a href="notificacoes.php"><i class="material-icons">notifications</i><?php if ($totalRows_Notificacoes > 0) { ?><span class="notification-badge"><?php echo $totalRows_Notificacoes; ?></span><?php } ?></a> <!-- Número de notificações -->
  </div>
  <?php } ?>

	<ul class="right hide-on-med-and-down">
      <li><a class="waves-effect waves-light btn-flat white-text modal-trigger" href="<?php echo $logoutAction ?>"><i class="material-icons left">lock_outline</i>SAIR</a></li>
    </ul>
	
    <ul id="nav-mobile" class="sidenav">
	
	<li class="divider"></li>
	
	<?php if (($row_Matricula['turma_etapa']=="19") || ($row_Matricula['turma_etapa']=="20") || ($row_Matricula['turma_etapa']=="21") || ($row_Matricula['turma_etapa']=="22") || ($row_Matricula['turma_etapa']=="36") || ($row_Matricula['turma_etapa']=="37") || ($row_Matricula['turma_etapa']=="38")) { ?>
	<li><a href="<?php if ($row_AlunoLogado['aluno_aceite_termos']=="N") { echo "termos.php"; } else { echo "perfil.php"; }  ?>" class="orange-text darken-4"><i class="tiny material-icons">record_voice_over</i> EduConnect</a></li><li class="divider"></li>
	<?php } ?>

	<li><a href="index.php" class="purple-text darken-4"><i class="tiny material-icons">person</i> Perfil</a></li><li class="divider"></li>
	<li><a href="aulas.php" class="blue-text darken-4"><i class="tiny material-icons">assignment</i> Aulas</a></li><li class="divider"></li>
	<li><a href="calendario.php" class="light-green-text darken-4"><i class="tiny material-icons">date_range</i> Calendário</a></li><li class="divider"></li>
	<li><a href="avisos.php" class="red-text darken-4"><i class="tiny material-icons">notifications</i> Avisos</a></li><li class="divider"></li>
	<li><a href="boletim_ano.php" class="deep-purple-text darken-4"><i class="tiny material-icons">done_all</i> Rendimento</a></li><li class="divider"></li>
	<li><a href="horarios.php" class="teal-text darken-4"><i class="tiny material-icons">grid_on</i> Horários</a></li><li class="divider"></li>
	<li><a href="colegas.php" class="green-text darken-4"><i class="tiny material-icons">group</i> Colegas</a></li><li class="divider"></li>
	<li><a href="ocorrencias.php" class="lime-text darken-4"><i class="tiny material-icons">bug_report</i> Ocorrências</a></li><li class="divider"></li>
	<li><a href="faltas.php" class="deep-orange-text darken-4"><i class="tiny material-icons">alarm_off</i> Faltas</a></li><li class="divider"></li>
	<li><a href="foto.php" class="collection-item blue-text darken-1"><i class="tiny material-icons">insert_emoticon</i> Foto</a></li><li class="divider"></li>
	<li><a class="waves-effect waves-light btn-flat modal-trigger" href="<?php echo $logoutAction ?>"><i class="material-icons left">lock_outline</i>Sair</a></li>
    
	</ul>
    <a href="#" data-target="nav-mobile" class="sidenav-trigger"><i class="material-icons">menu</i></a> 
	</div>
</nav>
