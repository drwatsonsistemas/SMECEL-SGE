<aside class="ls-sidebar">
  <div class="ls-sidebar-inner"> <a href="index.php" class="ls-go-prev"><span class="ls-text">Voltar à lista de serviços</span></a>
    <nav class="ls-menu">
      <ul>

        <li><a href="index.php" class="ls-ico-dashboard" title="Dashboard">início</a></li>

        <?php if ($row_UsuarioLogado['usu_m_ava']=="S") { ?>	
         <li> <a href="#" class="ls-ico-docs" title="Ambiente Virtual e Aprendizagem">Pedagógico</a>
          <ul>
            <li><a href="ava_resumo.php">Resumo</a></li>
            <li><a href="aulas.php">Aulas</a></li>
            <li><a href="planejamento.php">Planejamento</a></li>
            <li><a href="planejamento_coordenadores.php">Planejamento dos Coordenadores</a></li>
            <li><a href="conceitos.php" class="" title="Conceitos">Conceitos</a></li>
            <li><a href="criterios_avaliativos.php" class="" title="Critérios Avaliativos">Critérios Avaliativos</a></li>
            <li><a href="matriz.php" class="" title="Matriz Curricular">Matriz</a></li>
          </ul>
        </li>
      <?php } ?>
      

      <?php if ($row_UsuarioLogado['usu_m_administrativo']=="S") { ?>
        <li> <a href="#" class="ls-ico-multibuckets" title="Cadastros">Administrativo</a>
          <ul>
            <li><a href="turmas.php" class="" title="Turmas">Turmas <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></a></li>
            <li><a href="turmasPrevisao.php" class="" title="Turmas (previsão)">Turmas (previsão <?php echo $row_AnoLetivo['ano_letivo_ano']+1; ?>)</a></li>
            <li><a href="escolas.php" class="" title="Escolas">Escolas</a></li>
            <li><a href="usuarios.php" class="" title="Usuários">Usuários</a></li>
            <li><a href="funcoes.php" class="" title="Cargos e Funções">Cargos/Funções</a></li>
            <li><a href="funcionarios.php" class="" title="Funcionários">Funcionários</a></li>
            <li><a href="folha_pagamento.php">Folha de pagamento</a></li>
            <li><a href="calendario_escolar.php" class="" title="Calendário Escolar">Calendário Escolar</a></li>
            <li><a href="anoletivo.php" class="" title="Ano Letivo">Ano Letivo</a></li>
            <li><a href="ver_adm.php" class="" title="Ano Letivo">Verificações administrativas</a></li>
          </ul>
        </li>
      <?php } ?>

      <?php if ($row_UsuarioLogado['usu_m_formacao']=="S") { ?>
        <li> <a href="#" class="ls-ico-folder-open" title="Configurações">Material de apoio</a>
          <ul>
            <li><a href="formacao.php" class="">Cursos/Formação</a></li>
            <li><a href="material_apoio.php" class="">Material de apoio</a></li>
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
            <li><a href="resumo_geral.php" class="">Resumo geral</a></li>
            <li><a href="rel_censo.php" class="">CENSO</a></li>
            <li><a href="resumo_geral_tcm.php" class="">Questionário TCM</a></li>
            <li><a href="relatorios_alunos.php" class="">Alunos</a></li>
            <li><a href="rel_escolas.php" class="">Escolas</a></li>
            <li><a href="rel_turmas_horarios.php" class="">Horários</a></li>
            <li><a href="rel_turmas.php" class="">Turmas</a></li>
            <li><a href="matriculas.php" class="">Matrículas</a></li>
            <li><a href="funcionarios_vinculados.php" class="">Funcionários</a></li>
            <li><a href="aniversariantesMes.php" class="">Aniversariantes</a></li>
          </ul>
        </li>
      <?php } ?>

      <li> <a href="exportacoes.php" class="ls-ico-cloud-download" title="Exportação">Exportação</a>
        
      </li>

      <?php if ($row_UsuarioLogado['usu_m_graficos']=="S") { ?>
       <li> <a href="#" class="ls-ico-bars" title="Gráficos">Gráficos</a>
        <ul>
          <li><a href="graf_matriculas_por_situacao_matricula.php" class="">Matrícula/Situação</a></li>
          <li><a href="graf_matriculas_por_turno.php" class="">Matrícula/Turno</a></li>
          <li><a href="graf_matriculas_por_sexo.php" class="">Matrícula/Gênero</a></li>
          <li><a href="graf_matriculas_por_raca.php" class="">Matrícula/Raça</a></li>
          <li><a href="graf_matriculas_por_uf.php" class="">Matrícula/Naturalidade</a></li>
          <li><a href="graf_matriculas_por_deficiencia.php" class="">Matrícula/Deficiência</a></li>
          <li><a href="graf_matriculas_por_localidade.php" class="">Matrícula/Localidade</a></li>
          <li><a href="graf_matriculas_por_destro_canhoto.php" class="">Matrícula/Destro/Canhoto</a></li>
          <li><a href="graf_matriculas_por_bolsa_familia.php" class="">Matrícula/Bolsa-Família</a></li>
          <li><a href="graf_matriculas_por_transporte.php" class="">Matrícula/Transporte</a></li>
          <li><a href="graf_matriculas_por_vacina.php" class="">Matrícula/Situação Vacinal</a></li>
          <li><a href="graf_piramide_etaria_idade.php" class="">Matrículas/Faixa-etária</a></li>
          <li><a href="rendimento_final.php" class="">Rendimento Final</a></li>
        </ul>
      </li>
    <?php } ?>

    <?php if ($row_UsuarioLogado['usu_m_configuracoes']=="S") { ?>
      <li> <a href="#" class="ls-ico-cog" title="Configurações">Configurações</a>
        <ul>
          <li><a href="logs.php" class="ls-ico-ftp">Registros de acesso</a></li>
          <li><a href="registros.php" class="ls-ico-ftp">Registros de atividades</a></li>
          <li><a href="dados.php" class="ls-ico-ftp">Dados da Secretaria</a></li>
          <li><a href="meus_dados.php" class="ls-ico-eye">Meus dados</a></li>
          <li><a href="chamados.php" class="ls-ico-hours">Chamados ao suporte</a></li>
        </ul>
      </li>
    <?php } ?>

  </ul>
</nav>
</div>
</aside>