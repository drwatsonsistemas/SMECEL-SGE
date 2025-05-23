  <ul class="collection truncate">

  <?php if (($row_Matricula['turma_etapa']=="19") || ($row_Matricula['turma_etapa']=="20") || ($row_Matricula['turma_etapa']=="21") || ($row_Matricula['turma_etapa']=="22") || ($row_Matricula['turma_etapa']=="36") || ($row_Matricula['turma_etapa']=="37") || ($row_Matricula['turma_etapa']=="38")) { ?>

    <a href="<?php if ($row_AlunoLogado['aluno_aceite_termos']=="N") { echo "termos.php"; } else { echo "perfil.php"; } ?>" class="collection-item orange-text"><i class="tiny material-icons">record_voice_over</i> EduConnect</a>
	<?php } ?>

	<a href="index.php" class="collection-item purple-text darken-4"><i class="tiny material-icons">person</i> Perfil</a>
	<a href="aulas.php" class="collection-item blue-text darken-4"><i class="tiny material-icons">assignment</i> Aulas</a>
	<a href="calendario.php" class="collection-item light-green-text darken-4"><i class="tiny material-icons">date_range</i> Calendário</a>
	<a href="avisos.php" class="collection-item red-text darken-4"><i class="tiny material-icons">notifications</i> Avisos</a>
	<a href="boletim_ano.php" class="collection-item deep-purple-text darken-4"><i class="tiny material-icons">done_all</i> Rendimento</a>
	<a href="horarios.php" class="collection-item teal-text darken-4"><i class="tiny material-icons">grid_on</i> Horários</a>
	<a href="colegas.php" class="collection-item green-text darken-4"><i class="tiny material-icons">group</i> Colegas</a>
	<a href="ocorrencias.php" class="collection-item lime-text darken-4"><i class="tiny material-icons">bug_report</i> Ocorrências</a>
	<a href="faltas.php" class="collection-item deep-orange-text darken-4"><i class="tiny material-icons">alarm_off</i> Faltas</a>
	<a href="foto.php" class="collection-item blue-text darken-1"><i class="tiny material-icons">insert_emoticon</i> Foto</a>
  </ul>
