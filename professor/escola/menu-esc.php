<aside class="ls-sidebar">

    <div class="ls-sidebar-inner">
      <a href="index.php"  class="ls-go-prev"><span class="ls-text">Voltar à lista de serviços</span></a>
      <nav class="ls-menu">
        <ul>
		
		
		<li><a href="index.php" class="ls-ico-dashboard" title="Dashboard">início</a></li>
    
    <li> <a href="#" class="ls-ico-docs" title="Pedagógico">Pedagógico</a>
      <ul>
        <li><a href="ava_painel.php">Resumo</a></li>
        <li><a href="ac-geral.php">Planejamento / período</a></li>
        <li><a href="pauta.php">Pauta</a></li>
        <li><a href="planejamento_anual.php">Planejamento Anual</a></li>
        <li><a href="ava_verifica_aulas.php">Verificar aulas</a></li>
        <li><a href="rendimento_mapa.php">Verificar notas</a></li>
        <li><a href="aulas_virtuais_calendario.php">Calendário de aulas</a></li>
        <li><a href="aulas_virtuais.php">Aulas Registradas</a></li>
        <li><a href="ava_aulas_turmas.php">Aulas por turma</a></li>
        <li><a href="ava_aulas_professores.php">Aulas por professor</a></li>
        <li><a href="login_professores.php">Login Professores</a></li>
        <li><a href="login_alunos.php">Login alunos</a></li>
        <li><a href="login_alunos_dia.php">Total login alunos/dia</a></li>
        <li><a href="ava_login_aluno_turma_dia.php">Frequência alunos/turma</a></li>
        <li><a href="alunos_que_acessaram.php">Panorâma de acessos (alunos)</a></li>
        <li><a href="alunos_que_acessaram_whats.php">Contactar alunos (via Whatsapp)</a></li>
        <li><a href="vinculoAlunoExibirEduConnect.php">EduConnect</a></li>
      </ul>
    </li>
	
    <?php $hoje = date("Y-m-d"); ?>
    
	<?php if ($hoje >= $row_AnoLetivo['ano_letivo_data_rematricula']) { ?>
    <li> <a href="#" class="ls-ico-windows ls-color-warning" title="Rematrícula">Rematrícula (<?php echo $row_AnoLetivo['ano_letivo_ano']+1; ?>)</a>
      <ul>
        <li><a href="turmaListarRematricula.php">Turmas (rematrícula)</a></li>
        <li><a href="alunoPesquisar.php?periodo=rematricula">Rematricular</a></li>
      </ul>
    </li>
	<?php } ?>


  <li> <a href="#" class="ls-ico-chart-bar-up" title="Fechamento">Fechamento</a>
      <ul>
        <li><a href="diarios_preenchidos.php">Diários</a></li>
        <li><a href="atas.php">Atas Finais</a></li>
        <li><a href="consolidar_resultados_finais.php">Consolidar resultados finais</a></li>
        <li><a href="resultado_final.php">Resultado final</a></li>
      </ul>
    </li>

    <li> <a href="#" class="ls-ico-windows" title="Turmas">Turmas</a>
      <ul>
        <li><a href="turmaListar.php">Turmas Escolarização</a></li>
        <li><a href="turmaListarAee.php">Turmas de AEE</a></li>
        <li><a href="turmaListarComplementar.php">Turmas Ativ. Compl.</a></li>
        <li><a href="turmaCadastrar.php">Cadastrar turma</a></li>
        <li><a href="rendimento_mapa_lancar.php">Mapa de Notas</a></li>
        <li><a href="aviso_turma.php">Avisos (painel do aluno)</a></li>
        <li><a href="ocorrencias.php">Ocorrências</a></li>
        <li><a href="comunicados.php">Gerar comunicados</a></li>
      </ul>
    </li>
    <li> <a href="#" class="ls-ico-accessibility" title="Alunos">Alunos</a>
      <ul>
        <li><a href="vinculoAlunoExibirTurma.php">Listar</a></li>
        <li><a href="alunoPesquisar.php">Matricular</a></li>
        <li><a href="vinculoAlunoFrequencia.php">Frequência</a></li>
        <li><a href="alunoPesquisar.php#pesquiseAntes">Cadastrar</a></li>
        <li><a href="vinculoAlunoExibirTurmaFoto.php">Fotos</a></li>
        <li><a href="aniversariantesMes.php">Aniversariantes</a></li>
      </ul>
    </li>
    <li> <a href="#" class="ls-ico-users" title="Funcionários">Funcionários</a>
      <ul>
        <li><a href="comunicacao_todas.php">Comunicação</a></li>
        <li><a href="funcListar.php">Listar</a></li>
        <li><a href="funcPesquisar.php">Pesquisar</a></li>
        <li><a href="folha_pagamento.php">Folha de pagamento</a></li>
        <li><a href="funcionariosAniversariantesMes.php">Aniversariantes</a></li>
        <li><a href="aviso_prof.php">Gerar avisos</a></li>
      </ul>
    </li>
    <li> <a href="#" class="ls-ico-qrcode" title="Alunos">Frequencia</a>
      <ul>
        <li><a href="portariaFrequencia.php">Consulta de entradas</a></li>
        <li><a href="portariaFrequenciaMin.php">Entradas (Resumido)</a></li>
        <li><a href="turmasFrequencia.php">Frequencia nas aulas</a></li>
      </ul>
    </li>
    <li> <a href="#" class="ls-ico-numbered-list" title="Alunos">Horários</a>
      <ul>
  
      <li><a href="grade.php">Grade</a></li>
        <li><a href="professorDisciplina.php">Lotação/professor</a></li>
        <li><a href="distribuicaoAulas.php">Distribuição de aulas</a></li>
      </ul>
    </li>
    <li> <a href="#" class="ls-ico-bullhorn" title="Extras">Extras</a>
      <ul>
        <li><a href="material_apoio.php">Material de Apoio</a></li>
        <li><a href="patrimonio.php">Patrimônio</a></li>
        <li><a href="oficios.php">Ofícios</a></li>
        <li><a href="calendario_escolar.php" title="Calendario">Calendário Escolar</a></li>
        <li><a href="secretaria.php">Dados da Escola</a></li>
        <li><a href="secretaria_dados_escola.php">Características da Escola</a></li>
        <li><a href="dados.php">Dados do Usuário</a></li>
        <li><a href="usuListar.php">Usuários</a></li>
        <li><a href="tutoriais_video.php">Tutoriais</a></li>
      </ul>
    </li>
    <li><a href="rel.php" class="ls-ico-stats" title="Relatórios">Relatórios</a></li>
          
		  
        </ul>
      </nav>
    </div>

</aside>