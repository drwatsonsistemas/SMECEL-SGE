<aside class="ls-sidebar">
  <div class="ls-sidebar-inner"> <a href="index.php" class="ls-go-prev"><span class="ls-text">Voltar à lista de serviços</span></a>
    <nav class="ls-menu">
      <ul>

        <li><a href="index.php" class="ls-ico-dashboard" title="Dashboard">início</a></li>
		
	<?php if ($row_UsuarioLogado['usu_m_ava']=="S") { ?>	
	<li> <a href="#" class="ls-ico-domain" title="Ambiente Virtual e Aprendizagem">AVA</a>
      <ul>
        <li><a href="ava_resumo.php">Resumo</a></li>
        <li><a href="aulas.php">Aulas</a></li>
        <li><a href="planejamento.php">Planejamento</a></li>
      </ul>
    </li>
	<?php } ?>
      
        
		<?php if ($row_UsuarioLogado['usu_m_administrativo']=="S") { ?>
		<li> <a href="#" class="ls-ico-multibuckets" title="Cadastros">Administrativo</a>
          <ul>
            <li><a href="escolas.php" class="" title="Escolas">1 - Escolas</a></li>
            <li><a href="usuarios.php" class="" title="Usuários">2 - Usuários</a></li>
            <li><a href="funcoes.php" class="" title="Cargos e Funções">3 - Cargos/Funções</a></li>
            <li><a href="funcionarios.php" class="" title="Funcionários">4 - Funcionários</a></li>
            <li><a href="conceitos.php" class="" title="Conceitos">5 - Conceitos</a></li>
            <li><a href="criterios_avaliativos.php" class="" title="Critérios Avaliativos">6 - Critérios Avaliativos</a></li>
            <li><a href="matriz.php" class="" title="Matriz Curricular">7 - Matriz</a></li>
            <li><a href="calendario_escolar.php" class="" title="Calendário Escolar">8 - Calendário Escolar</a></li>
			<li><a href="anoletivo.php" class="" title="Ano Letivo">9 - Ano Letivo</a></li>
          </ul>
        </li>
		<?php } ?>

        <?php if ($row_UsuarioLogado['usu_m_formacao']=="S") { ?>
		<li> <a href="#" class="ls-ico-chart-bar-up" title="Configurações">Formação</a>
          <ul>
            <li><a href="formacao.php" class="">Formação</a></li>
          </ul>
        </li>
		<?php } ?>
		
		<?php if ($row_UsuarioLogado['usu_m_transporte']=="S") { ?>
        <li> <a href="#" class="ls-ico-flag" title="Transporte Escolar">Transporte Escolar</a>
          <ul>
            <li><a href="motoristas.php" class="">Motoristas</a></li>
            <li><a href="veiculos.php" class="">Veículos</a></li>
            <li><a href="pontos.php" class="">Pontos</a></li>
            <li><a href="rotas.php" class="">Rotas</a></li>
          </ul>
        </li>
        <?php } ?>
       
	   
		<?php if ($row_UsuarioLogado['usu_m_merenda']=="S") { ?>
        <li> <a href="#" class="ls-ico-cart" title="Merenda Escolar">Merenda Escolar</a>
          <ul>
            <li><a href="alimentos.php" class="">Alimentos</a></li>
            <li><a href="preparacao.php" class="">Preparações</a></li>
            <li><a href="cardapio.php" class="">Cardápios</a></li>
          </ul>
        </li>
		<?php } ?>
        
        
        <!--
        
        
        <li> <a href="#" class="ls-ico-users" title="Recursos Humanos">Recursos Humanos</a>
          <ul>
            <li><a href="folha.php" class="">Folha</a></li>
            <li><a href="#" class="">Licenças</a></li>
          </ul>
        </li>
        
        
        
       

      
        <li> <a href="#" class="ls-ico-book" title="Transporte Escolar">Biblioteca</a>
          <ul>
            <li><a href="#" class="">Acervo</a></li>
            <li><a href="#" class="">Autores</a></li>
            <li><a href="#" class="">Editoras</a></li>
          </ul>
        </li>
     
        
        -->
		<?php if ($row_UsuarioLogado['usu_m_patrimonio']=="S") { ?>
        <li> <a href="#" class="ls-ico-ftp" title="Patrimônio Escolar">Patrimônio</a>
          <ul>
            <li><a href="patrimonio.php" class="">Ver</a></li>
          </ul>
        </li>
        <?php } ?>     

        <?php if ($row_UsuarioLogado['usu_m_relatorios']=="S") { ?>
		<li> <a href="#" class="ls-ico-numbered-list" title="Relatórios">Relatórios</a>
          <ul>
            <li><a href="rel_escolas.php" class="">Escolas</a></li>
            <li><a href="rel_turmas_horarios.php" class="">Horários</a></li>
            <li><a href="rel_turmas.php" class="">Turmas</a></li>
            <li><a href="matriculas.php" class="">Matrículas</a></li>
            <li><a href="funcionarios_vinculados.php" class="">Funcionários</a></li>
            <li><a href="aniversariantesMes.php" class="">Aniversariantes</a></li>
          </ul>
        </li>
		<?php } ?>

         <?php if ($row_UsuarioLogado['usu_m_graficos']=="S") { ?>
		 <li> <a href="#" class="ls-ico-bars" title="Gráficos">Gráficos</a>
          <ul>
            <li><a href="graf_matriculas_por_situacao_matricula.php" class="">Matrícula/Situação</a></li>
            <li><a href="graf_matriculas_por_turno.php" class="">Matrícula/Turno</a></li>
            <li><a href="graf_matriculas_por_sexo.php" class="">Matrícula/Gênero</a></li>
            <li><a href="graf_matriculas_por_uf.php" class="">Matrícula/Naturalidade</a></li>
            <li><a href="graf_matriculas_por_deficiencia.php" class="">Matrícula/Deficiência</a></li>
            <li><a href="graf_matriculas_por_localidade.php" class="">Matrícula/Localidade</a></li>
            <li><a href="graf_matriculas_por_destro_canhoto.php" class="">Matrícula/Destro/Canhoto</a></li>
            <li><a href="graf_matriculas_por_bolsa_familia.php" class="">Matrícula/Bolsa-Família</a></li>
            <li><a href="graf_matriculas_por_transporte.php" class="">Matrícula/Transporte</a></li>
            <li><a href="graf_matriculas_por_vacina.php" class="">Matrícula/Situação Vacinal</a></li>
            <li><a href="rendimento_final.php" class="">Rendimento Final</a></li>
          </ul>
        </li>
		<?php } ?>

        <?php if ($row_UsuarioLogado['usu_m_configuracoes']=="S") { ?>
		<li> <a href="#" class="ls-ico-cog" title="Configurações">Configurações</a>
          <ul>
            <li><a href="logs.php" class="ls-ico-ftp">Registros de acesso</a></li>
            <li><a href="registros.php" class="ls-ico-ftp">Registros de atividades</a></li>
            <li><a href="dados.php" class="ls-ico-ftp">Dados</a></li>
            <li><a href="senha.php" class="ls-ico-eye">Alterar senha</a></li>
            <li><a href="chamados.php" class="ls-ico-hours">Chamados ao suporte</a></li>
          </ul>
        </li>
		<?php } ?>

      </ul>
    </nav>
  </div>
</aside>