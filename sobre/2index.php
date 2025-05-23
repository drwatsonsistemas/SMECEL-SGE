<?php
require_once('../Connections/SmecelNovo.php'); 

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Entidades = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_ibge_municipio, sec_regra_media FROM smc_sec WHERE sec_logo IS NOT NULL";
$Entidades = mysql_query($query_Entidades, $SmecelNovo) or die(mysql_error());
$row_Entidades = mysql_fetch_assoc($Entidades);
$totalRows_Entidades = mysql_num_rows($Entidades);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Escolas = "SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_ue, escola_situacao, escola_localizacao, escola_ibge_municipio FROM smc_escola";
$Escolas = mysql_query($query_Escolas, $SmecelNovo) or die(mysql_error());
$row_Escolas = mysql_fetch_assoc($Escolas);
$totalRows_Escolas = mysql_num_rows($Escolas);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turmas = "SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo FROM smc_turma";
$Turmas = mysql_query($query_Turmas, $SmecelNovo) or die(mysql_error());
$row_Turmas = mysql_fetch_assoc($Turmas);
$totalRows_Turmas = mysql_num_rows($Turmas);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Alunos = "SELECT aluno_id, aluno_cod_inep, aluno_cpf, aluno_nome, aluno_nascimento, aluno_filiacao1, aluno_filiacao2, aluno_sexo, aluno_raca, aluno_nacionalidade, aluno_uf_nascimento, aluno_municipio_nascimento, aluno_municipio_nascimento_ibge, aluno_aluno_com_deficiencia, aluno_nis, aluno_identidade, aluno_emissor, aluno_uf_emissor, aluno_data_espedicao, aluno_tipo_certidao, aluno_termo, aluno_folhas, aluno_livro, aluno_emissao_certidao, aluno_uf_cartorio, aluno_mucicipio_cartorio, aluno_nome_cartorio, aluno_num_matricula_modelo_novo, aluno_localizacao, aluno_cep, aluno_endereco, aluno_numero, aluno_complemento, aluno_bairro, aluno_uf, aluno_municipio, aluno_telefone, aluno_celular, aluno_email, aluno_sus, aluno_tipo_deficiencia, aluno_laudo, aluno_alergia, aluno_alergia_qual, aluno_destro, aluno_emergencia_avisar, aluno_emergencia_tel1, aluno_emergencia_tel2, aluno_prof_mae, aluno_tel_mae, aluno_escolaridade_mae, aluno_rg_mae, aluno_cpf_mae, aluno_prof_pai, aluno_tel_pai, aluno_escolaridade_pai, aluno_rg_pai, aluno_cpf_pai, aluno_hash, aluno_recebe_bolsa_familia, aluno_foto, aluno_def_bvisao, aluno_def_cegueira, aluno_def_auditiva, aluno_def_fisica, aluno_def_intelectual, aluno_def_surdez, aluno_def_surdocegueira, aluno_def_autista, aluno_def_superdotacao FROM smc_aluno";
$Alunos = mysql_query($query_Alunos, $SmecelNovo) or die(mysql_error());
$row_Alunos = mysql_fetch_assoc($Alunos);
$totalRows_Alunos = mysql_num_rows($Alunos);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matriculas = "SELECT * FROM smc_vinculo_aluno";
$Matriculas = mysql_query($query_Matriculas, $SmecelNovo) or die(mysql_error());
$row_Matriculas = mysql_fetch_assoc($Matriculas);
$totalRows_Matriculas = mysql_num_rows($Matriculas);



/*
*Author: Paulo Amaral
*Data: 14/12/2022
*Desc: Pegar as logos das prefeituras cadastradas
*/
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EntidadesLogo = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_ibge_municipio, sec_regra_media, sec_logo FROM smc_sec WHERE sec_logo IS NOT NULL LIMIT 1";
$EntidadesLogo = mysql_query($query_EntidadesLogo, $SmecelNovo) or die(mysql_error());
$row_EntidadesLogo = mysql_fetch_array($EntidadesLogo);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EntidadesLogo2 = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_ibge_municipio, sec_regra_media, sec_logo FROM smc_sec WHERE sec_logo IS NOT NULL";
$EntidadesLogo2 = mysql_query($query_EntidadesLogo2, $SmecelNovo) or die(mysql_error());
$row_EntidadesLogo2 = mysql_fetch_array($EntidadesLogo2);


?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
 <!-- Global site tag (gtag.js) - Google Analytics -->
 <script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
 <script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-117872281-1');
</script>
	<!-- basic -->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="viewport" content="initial-scale=1, maximum-scale=1">
	
	<title>SMECEL - Sistema de Gestão Escolar Municipal</title>
	<meta name="description" content="Tenha o controle das informações Educacionais em seu município na palma da mão" />
	 
	<link rel="canonical" href="https://www.smecel.com.br/sobre" />
	<meta property="og:locale" content="pt_BR" />
	<meta property="og:type" content="website" />
	<meta property="og:title" content="SMECEL - Sistema de Gestão Escolar Municipal" />
	<meta property="og:description" content="Tenha o controle das informações Educacionais em seu município na palma da mão" />
	<meta property="og:url" content="https://www.smecel.com.br/sobre" />
	<meta property="og:site_name" content="SMECEL" />
	<meta property="og:image" content="https://www.smecel.com.br/img/quadro1.jpg" />
	<meta property="og:image:width" content="600" />
	<meta property="og:image:height" content="400" />
	<meta property="og:image:type" content="image/jpeg" />
	<meta name="author" content="DR WATSON" />
	
	<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
	<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
 
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" href="css/responsive.css">
	<link rel="icon" href="images/fevicon.png" type="image/gif" />
	<link rel="stylesheet" href="css/jquery.mCustomScrollbar.min.css">
	<link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
	<link rel="stylesheet" href="css/owl.carousel.min.css">
	<link rel="stylesheet" href="css/owl.theme.default.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css" media="screen">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" integrity="sha512-c42qTSw/wPZ3/5LBzD+Bw5f7bSF2oxou6wEb+I/lqeaKV5FDIfMvvRp772y4jcJLKuGUOpbJMdg/BTl50fJYAw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Fredoka+One&display=swap" rel="stylesheet">
	<style>
		h1, h2, h3, h4, h5, h6 { font-family: 'Fredoka One', cursive; }

		.carousel-nav-icon {
			height: 48px;
			width: 48px;
		}
		.carousel-item .col, .col-sm, .col-md {

			margin: 8px;
			height: 300px;
			background-size: cover;
			background-position: center center;

		}
	</style>
</head>

<body>
	<div class="header_section">
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					<img src="https://smecel.com.br/img/logo_smecel_background_flattened.png" width="200px" class="">
				</div>
			</div>
		</div>
		<br><br><br><br>
		<div class="banner_section layout_padding">
			<div class="container">
				<div class="row">
					<div class="col-sm-7">
						<h1 class="laranja">SMECEL - SISTEMA DE GESTÃO ESCOLAR</h1><br>
						<h1 class="branca">Simplifique a gestão educacional em seu município com um sistema completo e intuitivo, pensado exclusivamente para ser uma ferramenta de apoio na Educação Pública Municipal.</h1>
					</div>
					<div class="col-sm-5" style="padding-top: 0rem;">
						<div class=""><img src="images/gestao.png" width="100%" class="image_1 animated wow animate__zoomInLeft"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	

	<div class="about_section layout_padding">
		<div class="container">
			<div class="row  animated fadeInLeft wow">
				<div class="col-md-3">
					<div><img src="images/sobre.png" class="image_2"></div>
				</div>
				<div class="col-md-9">
					<h1 class="services_taital"><span>Sobre </span> <img src="images/book.png"> <span style="color: #1f1f1f">SMECEL</span></h1>
					<p class="ipsum_text">O SMECEL começou a ser desenvolvido em meados de 2015, inicialmente para uso da Secretaria de Educação no município de Itagimirim-BA. O desenvolvimento partia do contato direto com Coordenadores, Secretários Escolares, Professores, Alunos, e todos os envolvidos diretamente com a educação, tendo se tornado uma ferramenta totalmente moldada com base na realidade diária dos municípios.</p>
				</div>
			</div>
		</div>
	</div>
	
	
	
	
	
	<div class="team_section_2 layout_padding" style="background-color: #ffffff;">
		<div class="container">


			<div class="images_main animated wow fadeInRight">
				<div class="row">
					<div class="col-sm-8">
						<h1 class="consectetur_text_2">TUDO ONLINE</h1>
						<p class="dummy_text">Tudo que você precisa está na palma da mão. O sistema é totalmente online e você acessa de onde e quando quiser. Nosso layout é responsivo e se adapta aos diversos dispositivos atuais: computador, notebook, tablet ou smartphone.</p>
					</div>
					<div class="col-sm-4">
						<div class="image_4"><img class="" src="images/movel.gif"></div>
					</div>
				</div>
				<br>
			</div>	  


			<div class="images_main animated wow fadeInRight">
				<div class="row">

					<div class="col-sm-4">
						<div class="image_4"><img class="" src="images/relatorio.gif"></div>
					</div>

					<div class="col-sm-8">
						<h1 class="consectetur_text_2">GRÁFICOS E RELATÓRIOS</h1>
						<p class="dummy_text">Todas as informações lançadas no sistema são transformadas em gráficos e relatórios onde a equipe técnica e pedagógica é capaz de analizar e tomar decisões acertivas. Relatório de rendimentos, frequência, conteúdo de aula e outros dados são gerados com um clique. Tudo rápido e prático.</p>
					</div>

				</div>
				<br>
			</div>	  




		</div>
	</div>
	
	
	
	
	
	<!-- about section end -->
	<!-- choose section start -->
	<div class="choose_section layout_padding">
		<div class="container">
			<h1 class="choose_taital"><span>Onde </span> <img src="images/loc.png"> <span style="color: #1f1f1f">estamos</span></h1>
			<p class="choose_text">O SMECEL está presente em diversos municípios e fica ainda melhor em cada implantação</p>
			<div class="choose_section_2 layout_padding">
				<div class="row">
					<div class="col-lg-3 col-sm-6">
						<div class="choose_box animated fadeInDown wow" data-wow-delay=".2s">
							<h1 class="client_taital"><?= $totalRows_Entidades ?>+</h1>
							<h4 class="client_text">Municípios</h4>
						</div>
					</div>
					<div class="col-lg-3 col-sm-6">
						<div class="choose_box animated fadeInDown wow" data-wow-delay=".4s">
							<h1 class="client_taital"><?= $totalRows_Escolas ?>+</h1>
							<h4 class="client_text">Escolas</h4>
						</div>
					</div>
					<div class="col-lg-3 col-sm-6">
						<div class="choose_box animated fadeInDown wow" data-wow-delay=".6s">
							<h1 class="client_taital"><?= $totalRows_Turmas ?>+</h1>
							<h4 class="client_text">Turmas</h4>
						</div>
					</div>
					<div class="col-lg-3 col-sm-6">
						<div class="choose_box animated fadeInDown wow" data-wow-delay=".8s">
							<h1 class="client_taital"><?= $totalRows_Matriculas ?>+</h1>
							<h4 class="client_text">Matrículas</h4>
						</div>
					</div>
				</div>
				<br>
				
			</div>
		</div>
	</div>



	
	<div class="team_section_2 layout_padding" style="background-color: #ffffff;">
		<div class="container">


			<div class="images_main animated wow fadeInRight">
				<div class="row">
					<div class="col-sm-9">
						<h1 class="consectetur_text_2">TRANSPORTE ESCOLAR</h1>
						<p class="dummy_text">No módulo Transporte Escolar, a Secretaria de Educação cadastra todos os pontos de parada e as rotas dos veículos que fazem o transporte escolar. O sistema trás informações como distância e tempo gasto na rota, consumo dos veículos, etc.</p>
					</div>
					<div class="col-sm-3">
						<div class="image_4"><img class="" src="images/transporte.gif"></div>
					</div>
				</div>
				<br>
			</div>	  


			<div class="images_main animated wow fadeInRight">
				<div class="row">


					<div class="col-sm-9">
						<h1 class="consectetur_text_2">AMBIENTE VIRTUAL DE APRENDIZAGEM</h1>
						<p class="dummy_text">O AVA - Ambiente Virtual de Aprendizagem, é uma excelente ferramenta para ajudar os professores na criação de aulas online, utilizando recursos como vídeos, imagens, infográficos e muito mais. Durante a pandemia, o AVA auxiliou os municípios a manterem a continuidade das aulas.</p>
					</div>

					<div class="col-sm-3">
						<div class="image_4"><img class="" src="images/ava.gif"></div>
					</div>


				</div>
				<br>
			</div>

			<div class="images_main animated wow fadeInRight">
				<div class="row">
					<div class="col-sm-9">
						<h1 class="consectetur_text_2">MERENDA ESCOLAR</h1>
						<p class="dummy_text">No módulo Merenda Escolar, a equipe de nutricionistas pode cadastrar todas as preparações e cardápios utilizados nas Unidades Escolares do município. O sistema trás todas as informações dos alimentos com base na tabela TACO, exibindo o valor nutricional de cada preparação.</p>
					</div>
					<div class="col-sm-3">
						<div class="image_4"><img class="" src="images/merenda.gif"></div>
					</div>
				</div>
				<br>
			</div>				




		</div>
	</div>



	<div class="team_section layout_padding">
		<div class="container">



			<h1 class="choose_taital"><span>Módulos </span> <img src="images/integracao.png"> <span style="color: #1f1f1f">Integrados</span></h1>
			<p class="choose_text">O SMECEL possui diversos módulos que trabalham de forma integrada, proporcionando muito mais produtividade e praticidade com as informações disponibilizadas pelas escolas.</p>



			<div class="team_section_2 layout_padding">
				<div class="container">


					<div class="images_main_1 animated wow fadeInRight">
						<div class="row">

							<div class="col-sm-5">
								<div class="image_4"><img class="" src="images/sec-educacao.png"></div>
							</div>

							<div class="col-sm-7">
								<h2 class="consectetur_text_2">PAINEL DA SECRETARIA</h2>
								<p class="dummy_text">
								É o painel acessado pela equipe técnica e pedagógica da Secretaria de Educação. Neste painel, são cadastradas as informações principais da rede de ensino. 
								<br>
								<br>&#8226;Matriz Curricular
								<br>&#8226;Grupos de Conceitos
								<br>&#8226;Critérios Avaliativos
								<br>&#8226;Calendário Letivo
								<br>&#8226;Funcionários
								<br>&#8226;Módulo Transporte Escolar
								<br>&#8226;Módulo Merenda Escolar
								<br>&#8226;Acompanhamento da aulas e planejamento
								<br>&#8226;Cadastro dos Anos Letivos
								<br>&#8226;Cadastro de curso de formação para professores
								<br>&#8226;Diversos gráficos e relatórios
								</p>
							</div>

						</div>
						<br>
					</div>


					<div class="images_main_1 animated wow fadeInRight">
						<div class="row">

							<div class="col-sm-5">
								<div class="image_4"><img class="" src="images/sec-escola.png"></div>
							</div>

							<div class="col-sm-7">
								<h2 class="consectetur_text_2">PAINEL DA ESCOLA</h2>
								<p class="dummy_text">
								Painel que dá suporte aos Secretários Escolares dentro das escolas do município. O sistema otimiza toda a rotina administrativa e pedagógica de forma prática e fácil. 
								<br>
								<br>&#8226;Gestão de Turmas e Matrículas
								<br>&#8226;Gestão de Funcionários
								<br>&#8226;Geração de Boletins e Atas de Resultados Finais
								<br>&#8226;Gráfico de Rendimento por turma e Componente Curricular
								<br>&#8226;Uma infinidade de relatórios e gráficos
								<br>&#8226;Gerador de ofícios
								<br>&#8226;Controle de Grade de Horários
								<br>&#8226;Controle de Frequência
								<br>&#8226;Módulo de Patrimônio Escolar 
								<br>&#8226;Muito mais...
								
								</p>
							</div>

						</div>
						<br>
					</div>


					<div class="images_main_1 animated wow fadeInLeft">
						<div class="row">

							<div class="col-sm-5">
								<div class="image_4"><img class="" src="images/sec-professor.png"></div>
							</div>

							<div class="col-sm-7">
								<h2 class="consectetur_text_2">PAINEL DO PROFESSOR</h2>
								<p class="dummy_text">Com o Painel do Professor, a escola substitui o diário físico pelo Diário Digital, e pode lançar o conteudo das aulas, frequência e rendimento dos alunos. O professor também acompanha os avisos da Unidade de Ensino. <br><br>&#8226;Cadastro de conteúdo das aulas<br>&#8226;Lançamento de Frequência dos alunos<br>&#8226;Lançamento de rendimento<br>&#8226;Lançamento de parecer descritivo<br>&#8226;Registro de conceito<br>&#8226;Planejamento das Aulas</p>
							</div>

						</div>
						<br>
					</div>


					<div class="images_main_1 animated wow fadeInLeft">
						<div class="row">

							<div class="col-sm-5">
								<div class="image_4 wow"><img class="" src="images/sec-aluno.png"></div>
							</div>

							<div class="col-sm-7">
								<h2 class="consectetur_text_2">PAINEL DO ALUNO</h2>
								<p class="dummy_text">Neste painel, os alunos podem acompanhar as suas notas no boletim, o quadro de horário das aulas, avisos da escola e outras informações. Os pais ou responsáveis também podem acompanhar a vida escolar dos alunos. <br><br>&#8226;Visualização do boletim<br>&#8226;Visualização do quadro de horários<br>&#8226;Acompanhamento de avisos<br>&#8226;Acompanhamento de frequência<br>&#8226;Acompanhamento ocorrências</p>
							</div>

						</div>
						<br>
					</div>


					<div class="images_main_1 animated wow fadeInLeft">
						<div class="row">

							<div class="col-sm-5">
								<div class="image_4"><img class="" src="images/sec-pse.png"></div>
							</div>

							<div class="col-sm-7">
								<h2 class="consectetur_text_2">PAINEL PSE</h2>
								<p class="dummy_text">No painel do PSE - Programa Saúde na Escola, os profissionais da saúde têm acesso à relação de alunos das escolas, e podem realizar o lançamento das ações realizadas pelo programa. O sistema trás gráficos com base nos lançamentos. <br><br>&#8226;Acompanhamento dos dados dos alunos<br>&#8226;Acompanhamento da Acuidade Visual<br>&#8226;Acompanhamento do Consumo Alimentar<br>&#8226;Acompanhamento Antropométrico<br>&#8226;Geração de Gráficos e Relatórios</p>
							</div>

						</div>
						<br>
					</div>


					<div class="images_main_1 animated wow fadeInLeft">
						<div class="row">

							<div class="col-sm-5">
								<div class="image_4"><img class="" src="images/sec-conselho.png"></div>
							</div>

							<div class="col-sm-7">
								<h2 class="consectetur_text_2">PAINEL DO CONSELHO TUTELAR</h2>
								<p class="dummy_text">No painel disponível para o Conselho Tutelar, os profissionais podem acompanhar o relatório de frequência dos alunos do município, e têm a autonomia de entrar em contato com a escola ou com os pais e responsáveis pelo aluno.<br><br>&#8226;Controle de frequência dos alunos em tempo real<br>&#8226;Mapa de faltas por aluno/Componente Curricular/Campo de Experiência<br>&#8226;Dados de contato dos pais ou responsáveis</p>
							</div>

						</div>
						<br>
					</div>




				</div>
			</div>
		</div>
	</div>
	
	<div class="container">
  <div class="row">
  
  <h1 class="choose_taital"><img src="images/mapa.gif" width="200px"><br> <span>Municípios </span> <span style="color: #1f1f1f">parceiros  &#x2764;&#xFE0F;</span></h1><br><br><br>
	
	  </div>	
</div>
	
	<br><br><br>

<div class="choose_section layout_padding">	
<div class="container">
  <div class="row">
  
  

	<?php do { ?>
         <div class="col-sm-12 col-md-3 center"  style="text-align:center">
          <img src="../img/logo/secretaria/<?php echo $row_EntidadesLogo2['sec_logo']; ?>" width="100px" class="rounded-circle">
		  <p class="laranja center" style="text-align:center"><?php echo $row_EntidadesLogo2['sec_nome']; ?></p>
		 </div>	
    <?php } while ($row_EntidadesLogo2 = mysql_fetch_assoc($EntidadesLogo2)); ?>

  </div>	
</div>
</div>
	
	


<footer class="bg-primary text-white text-center text-lg-start">
  <!-- Grid container -->
  <div class="container p-4">
    <!--Grid row-->
    <div class="row">
      <!--Grid column-->
      <div class="col-lg-12 col-md-12 mb-4 mb-md-0">
        <h5 class="text-uppercase">DR WATSON SISTEMAS LTDA</h5>

        <p>
          CNPJ 10.593.149/0001-85<br>
		  (73) 3289-2704<br>
		  comercial@smecel.com.br<br>
		  Itagimirim - BA<br>
        </p>
      </div>

    </div>
    <!--Grid row-->
  </div>
  <!-- Grid container -->

  <!-- Copyright -->
  <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
    © 2014-<?php echo date("Y"); ?> | <a class="text-white" href="https://www.smecel.com.br/">www.smecel.com.br</a>
  </div>
  <!-- Copyright -->
</footer>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<a href="https://wa.me/5573998685288?text=Sobre%20o%20SMECEL" style="position:fixed;width:60px;height:60px;bottom:40px;right:40px;background-color:#25d366;color:#FFF;border-radius:50px;text-align:center;font-size:30px;box-shadow: 1px 1px 2px #888;
  z-index:1000;" target="_blank">
<i style="margin-top:16px" class="fa fa-whatsapp"></i>
</a>



	<!-- copyright section end -->
	<!-- Javascript files-->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.2/jquery.min.js" integrity="sha512-tWHlutFnuG0C6nQRlpvrEhE4QpkG1nn2MOUMWmUeRePl4e3Aki0VB6W1v3oLjFtd0hVOtRQ9PHpSfN6u6/QXkQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js" integrity="sha512-Eak/29OTpb36LLo2r47IpVzPBLXnAMPAVypbSZiZ4Qkf8p/7S/XRG5xp7OKWPPYfJT6metI+IORkR5G8F900+g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script src="js/popper.min.js"></script>
	<script src="js/bootstrap.bundle.min.js"></script>
	<script src="js/jquery-3.0.0.min.js"></script>
	<script src="js/plugin.js"></script>
	<!-- sidebar -->
	<script src="js/jquery.mCustomScrollbar.concat.min.js"></script>
	<script src="js/custom.js"></script>
	<!-- javascript --> 
	<script src="js/owl.carousel.js"></script>
	<script src="https:cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.js"></script>
	<script>
		new WOW().init();

	</script>

</body>
</html>
<?php mysql_free_result($EntidadesLogo); ?> 
