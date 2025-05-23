<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "conf/session.php"; ?>
<!DOCTYPE html>
<html class="<?php echo TEMA; ?>" lang="pt-br">
<head>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-117872281-1');
</script>
<title>PROFESSOR | <?php echo $row_ProfLogado['func_nome']; ?> | SMECEL - Sistema de Gestão Escolar</title>
<meta charset="utf-8">
<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
<link rel="stylesheet" type="text/css" href="//assets.locaweb.com.br/locastyle/edge/stylesheets/locastyle.css">
<link rel="stylesheet" href="css/sweetalert2.min.css">
</head>
<body>
<?php include_once "inc/navebar.php"; ?>
<?php include_once "inc/sidebar.php"; ?>
<main class="ls-main">
  <div class="container-fluid">
    <h1 class="ls-title-intro ls-ico-home">Ano letivo <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h1>
    
    <div class="ls-box">
    &#128515; Seja bem-vind<?php if ($row_ProfLogado['func_sexo']==2) { echo "a"; } else { echo "o"; } ?>, <strong><?php $nomeProf = explode(" ", $row_ProfLogado['func_nome']); echo ucfirst(strtolower($nomeProf[0])); ?></strong>!
    </div>
        
<div class="row">
  <div class="col-md-1">.col-md-1</div>
  <div class="col-md-1">.col-md-1</div>
  <div class="col-md-1">.col-md-1</div>
  <div class="col-md-1">.col-md-1</div>
  <div class="col-md-1">.col-md-1</div>
  <div class="col-md-1">.col-md-1</div>
  <div class="col-md-1">.col-md-1</div>
  <div class="col-md-1">.col-md-1</div>
  <div class="col-md-1">.col-md-1</div>
  <div class="col-md-1">.col-md-1</div>
  <div class="col-md-1">.col-md-1</div>
  <div class="col-md-1">.col-md-1</div>
</div>



  <h2 class="doc-title-2">Abas <a class="ls-tag-info ls-float-right" href="/locawebstyle/documentacao/componentes">ver documentação</a></h2>

  <hr>
  <ul class="ls-tabs-nav">
  <li class="ls-active"><a data-ls-module="tabs" href="#track">Aba 1</a></li>
  <li><a data-ls-module="tabs" href="#laps">Aba 2</a></li>
</ul>
<div class="ls-tabs-container">
  <div id="track" class="ls-tab-content ls-active">
    <p>Voluptatem et labore incidunt ex non mollitia. occaecati est tenetur aliquam id aut neque reprehenderit tenetur perferendis dolorum. provident impedit eveniet rerum facilis consequatur et odio sunt officia quia et sint et. ut quaerat dolorem aliquam quisquam totam fugiat debitis adipisci saepe mollitia accusantium et eaque. fugit rerum odit voluptas. quia inventore quia iure vel. voluptatem eum molestiae quibusdam atque repellendus nostrum doloremque odit est quidem odio sunt</p>
    <p>Ut sequi aliquam fuga. cupiditate et sint sed molestias. ducimus eaque fugit eum doloremque quia quisquam vel nisi quibusdam culpa consequatur magnam aut. ut dolores quis nihil molestiae hic rerum omnis animi aspernatur. rerum quis minus quasi. nobis inventore modi cum dicta quis qui vero laudantium quibusdam</p>
  </div>
  <div id="laps" class="ls-tab-content">
    <p>Minima voluptas amet voluptatum qui quod accusamus est iure dolorem ipsa voluptas doloribus perferendis. id ullam rem illum quas mollitia. quidem iusto adipisci amet totam eius doloremque amet quo soluta corrupti facere aspernatur. laudantium eligendi praesentium sit voluptas autem. eaque ex et praesentium est non delectus autem voluptate id optio nobis qui. voluptatem ratione voluptates eos nostrum dolorem quia atque nulla</p>
  </div>
</div>


<div class="doc-section">
  <h2 class="doc-title-2">Tabelas <a class="ls-tag-info ls-float-right" href="/locawebstyle/documentacao/componentes/tabelas">ver documentação</a></h2>
  <hr>
  <table class="ls-table">
  <thead>
    <tr>
      <th>Título</th>
      <th class="hidden-xs">Campanha</th>
      <th>Status</th>
      <th class="hidden-xs">Data de envio</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><a href="" title="">Mensagem Teste Locastyle</a></td>
      <td class="hidden-xs">Campanha</td>
      <td>Enviada</td>
      <td class="hidden-xs">21/09/2013 as 20:00 PM</td>
    </tr>
    <tr>
      <td><a href="" title="">Mensagem Teste Locastyle</a></td>
      <td class="hidden-xs">Campanha</td>
      <td>Enviada</td>
      <td class="hidden-xs">21/09/2013 as 20:00 PM</td>
    </tr>
  </tbody>
</table>

</div>

<hr class="doc-hr">

<div class="doc-section">
  <h2 class="doc-title-2">Barra de progresso <a class="ls-tag-info ls-float-right" href="/locawebstyle/documentacao/componentes/barra-de-progresso">ver documentação</a></h2>
  <hr>
  <div data-ls-module="progressBar" role="progressbar" aria-valuenow="60"></div>

  <br>
  <div data-ls-module="progressBar" role="progressbar" aria-valuenow="100" class="ls-left-percentage"></div>
  <br>
  <div data-ls-module="progressBar" role="progressbar" aria-valuenow="80" class="ls-animated"></div>

</div>

<hr class="doc-hr">

<div class="doc-section">
  <h2 class="doc-title-2">Botão Switch <a class="ls-tag-info ls-float-right" href="/locawebstyle/documentacao/componentes/botao-switch">ver documentação</a></h2>
  <hr>
  <div class="ls-box">
  <h2 class="ls-title-5 ls-display-inline-block">Uso de envios excedentes</h2>

  <div data-ls-module="switchButton" class="ls-switch-btn ls-float-right">
    <input type="checkbox" id="teste">
    <label class="ls-switch-label" for="teste" name="label-teste" ls-switch-off="Desativado" ls-switch-on="Ativado"><span></span></label>
  </div>
</div>

</div>

<hr class="doc-hr">

<div class="doc-section">
  <h2 class="doc-title-2">Botões <a class="ls-tag-info ls-float-right" href="/locawebstyle/documentacao/componentes/botoes">ver documentação</a></h2>
  <hr>
  <a href="#" class="ls-btn-primary">Primary</a>
<a href="#" class="ls-btn">Default</a>
<a href="#" class="ls-btn-dark">Dark</a>
<a href="#" class="ls-btn-danger">Danger</a>
<a href="#" class="ls-btn-primary-danger">Primary Danger</a>

</div>

<hr class="doc-hr">

<div class="doc-section">
  <h2 class="doc-title-2">Box <a class="ls-tag-info ls-float-right" href="/locawebstyle/documentacao/componentes/box-generico/">ver documentação</a></h2>
  <hr>

  <div class="ls-box ls-lg-space">
  <h5 class="ls-title-3">Box grande .ls-lg-space</h5>
  <p>Ea architecto eum tempore deleniti quae consequatur qui laborum qui ea molestiae non ipsam deserunt. ut doloribus harum blanditiis quam occaecati possimus officia. minus consequatur est itaque dolor. sint quidem animi ut. eos minus eum iure. iure ut ut aperiam beatae labore ducimus et similique molestias itaque porro</p>
</div>

<div class="ls-box ls-md-space">
  <h5 class="ls-title-3">Box médio .ls-md-space</h5>
  <p>Aut aspernatur eligendi consequuntur eaque commodi explicabo dolore praesentium tempore. qui exercitationem dolore nihil sit et ea molestiae rerum facilis consectetur quasi. earum et aliquid dolorem laudantium maxime impedit deserunt et porro. provident minima qui sit ea</p>
</div>

<div class="ls-box">
  <h5 class="ls-title-3">Box default (sem classe de tamanho)</h5>
  <p>A placeat excepturi aperiam corrupti rerum dolorem vero sunt quia ut velit debitis. quis harum ducimus veritatis eum eum eos. mollitia occaecati at cum consequatur tempora dolorem qui tempore officia aut eos. natus consequatur itaque odit</p>
</div>

<div class="ls-box ls-sm-space">
  <h5 class="ls-title-5">Box pequeno .ls-sm-space</h5>
  <p>Repellendus ab rerum qui et voluptas consectetur numquam voluptas deserunt autem amet. velit sapiente magni enim amet est quam est. nisi est autem nihil eaque laudantium molestiae odit rerum nam sit quis aut aut perspiciatis. libero deleniti et velit commodi amet placeat ut soluta quod laborum aut non totam dignissimos. officia sed repellat harum molestias recusandae consectetur reiciendis ea. velit modi quae neque et quod illum vitae non itaque. ex hic voluptatibus est voluptate velit ut</p>
</div>

<div class="ls-box ls-xs-space">
  <h5 class="ls-title-5">Box super pequeno .ls-xs-space</h5>
  <p>Nisi facere nihil libero. magnam pariatur ea iusto sed dolores ipsa beatae. vero tempore et id nostrum </p>
</div>

</div>


<hr class="doc-hr">

<div class="doc-section">
  <h2 class="doc-title-2">Box de filtro e busca<a class="ls-tag-info ls-float-right" href="/locawebstyle/documentacao/componentes/box-de-filtro">ver documentação</a></h2>
  <hr>


  <div class="ls-box-filter">
  <form action="" class="ls-form ls-form-inline">
    <label class="ls-label col-md-3 col-sm-4">
      <b class="ls-label-text">Período</b>
      <input type="text" name="cel2" class="datepicker ls-daterange" id="datepicker1" data-ls-daterange="#datepicker2">
    </label>

    <label class="ls-label col-md-3 col-sm-4">
      <b class="ls-label-text">a</b>
      <input type="text" name="cel2" class="datepicker ls-daterange" id="datepicker2">
    </label>
    <div class="ls-actions-btn">
      <button type="button" class="ls-btn">Filtrar</button>
    </div>
  </form>
</div>


  <div class="ls-box-filter">
  <form action="" class="ls-form ls-form-inline">
    <input type="hidden" name="status" value="">
    <label class="ls-label col-md-4 col-sm-4">
      <b class="ls-label-text">Período</b>
      <div class="ls-custom-select">
        <select name="period" id="select_period" class="ls-select">
            <option>Hoje</option>
            <option>Ontem</option>
            <option>Última semana</option>
            <option>Últimos 30 dias</option>
            <option>Últimos 6 meses</option>
            <option>Últimos 12 meses</option>
            <option>Personalizado</option>
            <option>Locawebstyle é o framework da Locaweb</option>
        </select>
      </div>
    </label>
    <label class="ls-label col-md-2 col-sm-2">
      <span id="new_feature_custom_filter_2" data-ls-module="popover" data-content="Escolha o período desejado e clique em 'Filtrar'."></span>
      <input type="text" name="range_start" class="datepicker ls-daterange" placeholder="dd/mm/aaaa" id="datepicker3" data-ls-daterange="#datepicker4">
    </label>
    <label class="ls-label col-md-2 col-sm-2">
      <span id="new_feature_custom_filter_3" data-ls-module="popover" data-content="Clique em 'Filtrar' para exibir  o período selecionado."></span>
      <input type="text" name="range_end" class="datepicker ls-daterange" placeholder="dd/mm/aaaa" id="datepicker4">
    </label>
    <label class="ls-label col-md-1 col-sm-1">
      <input type="submit" class="ls-btn-primary" value="Filtrar">
    </label>
    <div data-ls-module="dropdown" class="ls-dropdown ls-float-right" id="step4">
      <a href="#" class="ls-btn" role="combobox" aria-expanded="false">Exportar</a>
      <ul class="ls-dropdown-nav" aria-hidden="true">
        <li>
          <a href="" role="option" tabindex="-1">CSV</a>
        </li>
        <li>
          <a data-action="open_modal_export" data-ls-module="modal" data-report-ext="XLS" data-target="#modal_export" href="" role="option" tabindex="-1">XLS</a>
        </li>
        <li>
          <a class="ls-divider" data-action="open_modal_export" data-ls-module="modal" data-target="#modal_exported_reports" data-url="/panel/exports" href="" id="link_exported_reports" role="option" tabindex="-1">Relatórios exportados</a>
        </li>
      </ul>
    </div>
  </form>
</div>


  <div class="ls-box-filter">
  <form action="" class="ls-form ls-form-inline ls-float-left">
    <label class="ls-label col-md-8 col-sm-8">
      <b class="ls-label-text">Status</b>
      <div class="ls-custom-select">
        <select name="" class="ls-select">
          <option>Todos</option>
          <option>Ativos</option>
          <option>Desativados</option>
        </select>
      </div>
    </label>
  </form>

  <form  action="" class="ls-form ls-form-inline ls-float-right">
    <label class="ls-label" role="search">
      <b class="ls-label-text ls-hidden-accessible">Nome do cliente</b>
      <input type="text" id="q" name="q" aria-label="Faça sua busca por cliente" placeholder="Nome do cliente" required class="ls-field">
    </label>
    <div class="ls-actions-btn">
      <input type="submit" value="Buscar" class="ls-btn" title="Buscar">
    </div>
  </form>
</div>

</div>

<hr class="doc-hr">

<div class="doc-section">
  <h2 class="doc-title-2">Breadcrumb <a class="ls-tag-info ls-float-right" href="/locawebstyle/documentacao/componentes/breadcrumb">ver documentação</a></h2>
  <hr>
  <ol class="ls-breadcrumb">
  <li><a href="#">Início</a></li>
  <li><a href="#">Mensagens</a></li>
  <li>Contato</li>
</ol>

</div>

<hr class="doc-hr">

<div class="doc-section">
  <h2 class="doc-title-2">Formulários <a class="ls-tag-info ls-float-right" href="/locawebstyle/documentacao/formularios/">ver documentação</a></h2>
  <hr>
  <div class="row">
    <div class="col-md-12">
      <h3 class="doc-title-5">Formulários na horizontal</h3>
      <form action="" class="ls-form ls-form-horizontal row">
  <fieldset>
    <label class="ls-label col-md-4 col-xs-12">
      <b class="ls-label-text">Nome</b>
      <input type="text" name="nome" placeholder="Primeiro nome" class="ls-field" required>
    </label>
    <label class="ls-label col-md-4 col-xs-12">
      <b class="ls-label-text">Sobrenome</b>
      <input type="text" name="nome" placeholder="Sobrenome" class="ls-field" required>
    </label>  <label class="ls-label col-md-4 col-xs-12">
      <b class="ls-label-text">Nome</b>
      <input type="text" name="nome" placeholder="Primeiro nome" class="ls-field" required>
    </label>
    <label class="ls-label col-md-4 col-xs-12">
      <b class="ls-label-text">Sobrenome</b>
      <input type="text" name="nome" placeholder="Sobrenome" class="ls-field" required>
    </label>
  </fieldset>

    <hr>

  <fieldset>
    <!-- Exemplo com Checkbox -->
    <div class="ls-label col-md-12">
      <p>Selecione quais plataformas você tem:</p>
      <label class="ls-label-text">
        <input type="checkbox" name="dynacom" class="ls-field-checkbox">
        Dynacom
      </label>
      <label class="ls-label-text">
        <input type="checkbox" name="masterSystem" class="ls-field-checkbox">
        MasterSystem sem fio
      </label>
      <label class="ls-label-text">
        <input type="checkbox" name="nintendinho" class="ls-field-checkbox">
        Nintendinho
      </label>
      <label class="ls-label-text">
        <input type="checkbox" name="superNintendo" class="ls-field-checkbox">
        Super Nintendo
      </label>
    </div>
  </fieldset>

    <hr>
  <fieldset>
    <!-- Exemplo com Radio button -->
    <div class="ls-label col-md-12">
      <p>Escolha uma das plataformas:</p>
      <label class="ls-label-text">
        <input type="radio" name="plataforms" class="ls-field-radio">
        Dynacom
      </label>
      <label class="ls-label-text">
        <input type="radio" name="plataforms" class="ls-field-radio">
        MasterSystem sem fio
      </label>
      <label class="ls-label-text">
        <input type="radio" name="plataforms" class="ls-field-radio">
        Nintendinho
      </label>
      <label class="ls-label-text">
        <input type="radio" name="plataforms" class="ls-field-radio">
        Super Nintendo
      </label>
    </div>
  </fieldset>

  <div class="ls-actions-btn">
    <button class="ls-btn">Salvar</button>
    <button class="ls-btn-danger">Cancelar</button>
  </div>
</form>

      <br><br>
      <h3 class="doc-title-5">Formulários na vertical</h3>
      <form action="" class="ls-form row">
  <fieldset>
    <label class="ls-label col-md-3">
      <b class="ls-label-text">Nome</b>
      <p class="ls-label-info">Digite seu nome completo</p>
      <input type="text" name="nome" placeholder="Nome e sobrenome" required >
    </label>
    <label class="ls-label col-md-4">
      <b class="ls-label-text">E-mail</b>
      <p class="ls-label-info">Seu e-mail particular</p>
      <input type="text" name="email" placeholder="Escreva seu email" required >
    </label>
    <label class="ls-label col-md-5">
      <b class="ls-label-text">Rua</b>
      <input type="text" name="rua" placeholder="O nome da sua rua" required >
    </label>
  </fieldset>

  <hr>

  <fieldset>
    <!-- Exemplo com Checkbox -->
    <div class="ls-label col-md-5">
      <p>Selecione quais plataformas você tem:</p>
      <label class="ls-label-text">
        <input type="checkbox" name="dynacom">
        Dynacom
      </label>
      <label class="ls-label-text">
        <input type="checkbox" name="masterSystem">
        MasterSystem sem fio
      </label>
      <label class="ls-label-text">
        <input type="checkbox" name="nintendinho">
        Nintendinho
      </label>
      <label class="ls-label-text">
        <input type="checkbox" name="superNintendo">
        Super Nintendo
      </label>
    </div>
  </fieldset>

  <hr>

  <fieldset>
    <!-- Exemplo com Radio button -->
    <div class="ls-label col-md-5">
      <p>Escolha uma das plataformas:</p>
      <label class="ls-label-text">
        <input type="radio" name="plataforms">
        Dynacom
      </label>
      <label class="ls-label-text">
        <input type="radio" name="plataforms">
        MasterSystem sem fio
      </label>
      <label class="ls-label-text">
        <input type="radio" name="plataforms">
        Nintendinho
      </label>
      <label class="ls-label-text">
        <input type="radio" name="plataforms">
        Super Nintendo
      </label>
    </div>
  </fieldset>
  <div class="ls-actions-btn">
    <button class="ls-btn">Salvar</button>
    <button class="ls-btn-danger">Excluir</button>
  </div>
</form>

      <br><br>
      <h3 class="doc-title-5">Formulários inline</h3>
      <form action="" class="ls-form ls-form-inline row">
  <label class="ls-label col-md-5">
    <b class="ls-label-text">Nome</b>
    <input type="text" name="nome" placeholder="Nome e sobrenome" required >
  </label>
  <label class="ls-label col-md-4">
    <b class="ls-label-text">E-mail</b>
    <input type="text" name="email" placeholder="Escreva seu email" required >
  </label>
  <div class="ls-actions-btn">
    <button class="ls-btn">Salvar</button>
    <button class="ls-btn-danger">Cancelar</button>
  </div>
</form>

    </div>
  </div>
</div>
<hr class="doc-hr">

<div class="doc-section">
  <h2 class="doc-title-2">Contador de Caracteres <a class="ls-tag-info ls-float-right" href="/locawebstyle/documentacao/formularios/contador">ver documentação</a></h2>
  <hr>
  <form action="" class="ls-form ls-form-horizontal row">
  <fieldset>
    <label class="ls-label col-md-4">
      <b class="ls-label-text">Nome</b>
      <div class="ls-prefix-group">
        <input type="text" data-ls-module="charCounter" maxlength="20" name="nome" placeholder="Digite seu nome completo" required>
        <span class="ls-label-text-prefix ls-ico-user"></span>
      </div>
    </label>
    <label class="ls-label col-md-8">
      <b class="ls-label-text">Mensagem</b>
      <textarea data-ls-module="charCounter" maxlength="100"></textarea>
    </label>
  </fieldset>
</form>

</div>

<hr class="doc-hr">

<div class="doc-section">
  <h2 class="doc-title-2">Select personalizado <a class="ls-tag-info ls-float-right" href="/locawebstyle/documentacao/formularios/select-customizado/">ver documentação</a></h2>
  <hr>
  <div class="row">
    <div class="col-md-5">
      <div class="ls-custom-select">
    <select class="ls-select">
        <option value="1"> Opção 1 </option>
        <option value="2"> Opção 2 </option>
        <option value="3"> Opção 3 </option>
        <option value="4"> Opção 4 </option>
    </select>
</div>

    </div>
  </div>
</div>

<hr class="doc-hr">

<div class="doc-section">
  <h2 class="doc-title-2">Prefixos e sufixos de campos de formulário <a class="ls-tag-info ls-float-right" href="/locawebstyle/documentacao/formularios/prefixos-sufixos">ver documentação</a></h2>
  <hr>
  <div class="row">
    <div class="col-md-12">
      <form action="" class="ls-form row">

  <label class="ls-label col-md-8">
    <b class="ls-label-text">Escreva o endereço do seu site</b>

    <div class="ls-prefix-group">
      <span class="ls-label-text-prefix">URL</span>
      <input type="text" name="nome" required >
    </div>
  </label>

  <label class="ls-label col-md-8">
    <b class="ls-label-text">Escreva o endereço do seu site</b>

    <div class="ls-prefix-group">
      <span class="ls-label-text-prefix">http://</span>
      <input type="text" name="nome" required >
      <span class="ls-label-text-prefix">.subdominio.com.br</span>
    </div>
  </label>

  <label class="ls-label col-md-6">
    <div class="ls-prefix-group">
      <span class="ls-label-text-prefix">Seu nome:</span>
      <input type="text" name="nome" placeholder="Completo, por favor..." required>
    </div>
  </label>

  <label class="ls-label col-md-12 col-sm-12">
    <b class="ls-label-text">Maximo de backups armazenados</b>
    <div class="row">
      <div class="ls-prefix-group col-md-4 col-sm-8 col-lg-3">
        <a href="" class="ls-label-text-prefix ls-bg-white">-</a>
        <input type="number" value="8" class="ls-txt-center ls-no-spin">
        <a href="" class="ls-label-text-prefix ls-bg-white">+</a>
      </div>
    </div>
  </label>
</form>

    </div>
  </div>
</div>

<hr class="doc-hr">

<div class="doc-section">
  <h2 class="doc-title-2">Collapses <a class="ls-tag-info ls-float-right" href="/locawebstyle/documentacao/componentes/collapse">ver documentação</a></h2>
  <hr >
    <div data-ls-module="collapse" data-target="#10" class="ls-collapse ">
    <a href="#" class="ls-collapse-header">
      <h3 class="ls-collapse-title">Título 1</h3>
    </a>
    <div class="ls-collapse-body" id="10">
      <p>
        Nulla reiciendis est ipsam natus quaerat labore est est iure quaerat. recusandae magni occaecati porro. et unde repellat neque quia sunt et sint eos atque exercitationem sed. est et voluptatibus quibusdam deserunt sed ex quo fuga eum et. nam officiis enim praesentium dolorem tenetur nam eum suscipit provident facere facilis voluptate ut inventore. ducimus doloremque enim aut dolorem odio qui consequatur sit est impedit omnis voluptatum. harum ut perferendis quo delectus et molestiae repellendus dolorem facilis id iure velit
      </p>
    </div>
  </div>
  <div data-ls-module="collapse" data-target="#11" class="ls-collapse ">
    <a href="#" class="ls-collapse-header">
      <h3 class="ls-collapse-title">Título 2</h3>
    </a>
    <div class="ls-collapse-body" id="11">
      <p>
        Nulla reiciendis est ipsam natus quaerat labore est est iure quaerat. recusandae magni occaecati porro. et unde repellat neque quia sunt et sint eos atque exercitationem sed. est et voluptatibus quibusdam deserunt sed ex quo fuga eum et. nam officiis enim praesentium dolorem tenetur nam eum suscipit provident facere facilis voluptate ut inventore. ducimus doloremque enim aut dolorem odio qui consequatur sit est impedit omnis voluptatum. harum ut perferendis quo delectus et molestiae repellendus dolorem facilis id iure velit
      </p>
    </div>
  </div>

</div>

<hr class="doc-hr">

<div class="doc-section">
  <h2 class="doc-title-2">Dropdown <a class="ls-tag-info ls-float-right" href="/locawebstyle/documentacao/componentes/dropdown">ver documentação</a></h2>
  <hr>
  <div data-ls-module="dropdown" class="ls-dropdown">
  <a href="#" class="ls-btn-primary">Users</a>

  <ul class="ls-dropdown-nav">
      <li><a href="#">Emma Rich</a></li>
      <li><a href="#">Denis McKenzie</a></li>
      <li><a href="#">Lori Barton</a></li>
      <li><a href="#">Skyler Heath</a></li>
      <li><a href="#">Leland Henry</a></li>
  </ul>
</div>

</div>

<hr class="doc-hr">

<div class="doc-section">
  <h2 class="doc-title-2">Filtro de exibição e Paginação <a class="ls-tag-info ls-float-right" href="/locawebstyle/documentacao/componentes/paginacao">ver documentação</a></h2>
  <hr>
  <div class="ls-pagination-filter">
  <ul class="ls-pagination">
    <li><a href="#">&laquo; Anterior</a></li>
    <li><a href="#">1</a></li>
    <li><a href="#">2</a></li>
    <li><a href="#">3</a></li>
    <li><a href="#">4</a></li>
    <li><a href="#">5</a></li>
    <li><a href="#">Próximo &raquo;</a></li>
  </ul>

  <div class="ls-filter-view">
      Exibir
    <div class="ls-custom-select">
      <select name="" id="" class="ls-select">
        <option value="10">10</option>
        <option value="30">30</option>
        <option value="50">50</option>
        <option value="100">100</option>
      </select>
    </div>
      ítens por página
  </div>
</div>

</div>

<hr class="doc-hr">

<div class="doc-section">
  <h2 class="doc-title-2">Footer</h2>
  <hr>
  <footer class="ls-footer" role="contentinfo">
  <nav class="ls-footer-menu">
      <h2 class="ls-title-footer">suporte e ajuda</h2>
      <ul class="ls-footer-list">
        <li>
          <a href="#" target="_blank" class="bg-customer-support">
            <span class="visible-lg">Atendimento</span>
          </a>
        </li>
        <li>
          <a href="#" target="_blank" class="bg-my-tickets">
            <span class="visible-lg">Meus Chamados</span>
          </a>
        </li>
        <li>
          <a href="#" target="_blank" class="bg-help-desk">
            <span class="visible-lg">Central de Ajuda (Wiki)</span>
          </a>
        </li>
        <li>
          <a href="#" target="_blank" class="bg-statusblog">
            <span class="visible-lg">Statusblog</span>
          </a>
        </li>
      </ul>
  </nav>
  <div class="ls-footer-info">
    <span class="last-access ls-ico-screen"><strong>Último acesso: </strong>99/99/9999 99:99:99</span>
    <div class="set-ip"><strong>IP:</strong> 000.00.00.000</div>
    <p class="ls-copy-right">Copyright © 1997-2017 Serviços de Internet S/A.</p>
  </div>
</footer>

</div>

<hr class="doc-hr">

<div class="doc-section">
  <h2 class="doc-title-2">Ícones <a class="ls-tag-info ls-float-right" href="/locawebstyle/documentacao/css/icones">ver documentação</a></h2>
  <hr>
  <ul class="list-icons">
<li><span class="ls-ico-cart">ls-ico-cart</span></li>
<li><span class="ls-ico-bullhorn">ls-ico-bullhorn</span></li>
<li><span class="ls-ico-dashboard">ls-ico-dashboard</span></li>
<li><span class="ls-ico-bell-o">ls-ico-bell-o</span></li>
<li><span class="ls-ico-home">ls-ico-home</span></li>
<li><span class="ls-ico-users">ls-ico-users</span></li>
<li><span class="ls-ico-stats">ls-ico-stats</span></li>
<li><span class="ls-ico-envelope">ls-ico-envelope</span></li>
<li><span class="ls-ico-envelop">ls-ico-envelop</span></li>
<li><span class="ls-ico-cog">ls-ico-cog</span></li>
<li><span class="ls-ico-user">ls-ico-user</span></li>
<li><span class="ls-ico-question">ls-ico-question</span></li>
<li><span class="ls-ico-chevron-up">ls-ico-chevron-up</span></li>
<li><span class="ls-ico-chevron-right">ls-ico-chevron-right</span></li>
<li><span class="ls-ico-chevron-down">ls-ico-chevron-down</span></li>
<li><span class="ls-ico-chevron-left">ls-ico-chevron-left</span></li>
<li><span class="ls-ico-shaft-up-left">ls-ico-shaft-up-left</span></li>
<li><span class="ls-ico-shaft-up">ls-ico-shaft-up</span></li>
<li><span class="ls-ico-shaft-up-right">ls-ico-shaft-up-right</span></li>
<li><span class="ls-ico-shaft-right">ls-ico-shaft-right</span></li>
<li><span class="ls-ico-shaft-down-right">ls-ico-shaft-down-right</span></li>
<li><span class="ls-ico-shaft-down">ls-ico-shaft-down</span></li>
<li><span class="ls-ico-shaft-down-left">ls-ico-shaft-down-left</span></li>
<li><span class="ls-ico-shaft-left">ls-ico-shaft-left</span></li>
<li><span class="ls-ico-circle-up">ls-ico-circle-up</span></li>
<li><span class="ls-ico-circle-right">ls-ico-circle-right</span></li>
<li><span class="ls-ico-circle-down">ls-ico-circle-down</span></li>
<li><span class="ls-ico-circle-left">ls-ico-circle-left</span></li>
<li><span class="ls-ico-menu">ls-ico-menu</span></li>
<li><span class="ls-ico-pencil">ls-ico-pencil</span></li>
<li><span class="ls-ico-pencil2">ls-ico-pencil2</span></li>
<li><span class="ls-ico-paint-format">ls-ico-paint-format</span></li>
<li><span class="ls-ico-image">ls-ico-image</span></li>
<li><span class="ls-ico-images">ls-ico-images</span></li>
<li><span class="ls-ico-qrcode">ls-ico-qrcode</span></li>
<li><span class="ls-ico-list">ls-ico-list</span></li>
<li><span class="ls-ico-list2">ls-ico-list2</span></li>
<li><span class="ls-ico-numbered-list">ls-ico-numbered-list</span></li>
<li><span class="ls-ico-menu2">ls-ico-menu2</span></li>
<li><span class="ls-ico-insert-template">ls-ico-insert-template</span></li>
<li><span class="ls-ico-windows">ls-ico-windows</span></li>
<li><span class="ls-ico-code">ls-ico-code</span></li>
<li><span class="ls-ico-screen">ls-ico-screen</span></li>
<li><span class="ls-ico-camera">ls-ico-camera</span></li>
<li><span class="ls-ico-folder">ls-ico-folder</span></li>
<li><span class="ls-ico-folder-open">ls-ico-folder-open</span></li>
<li><span class="ls-ico-download">ls-ico-download</span></li>
<li><span class="ls-ico-upload">ls-ico-upload</span></li>
<li><span class="ls-ico-spinner">ls-ico-spinner</span></li>
<li><span class="ls-ico-search">ls-ico-search</span></li>
<li><span class="ls-ico-zoomin">ls-ico-zoomin</span></li>
<li><span class="ls-ico-zoomout">ls-ico-zoomout</span></li>
<li><span class="ls-ico-stats">ls-ico-stats</span></li>
<li><span class="ls-ico-bars">ls-ico-bars</span></li>
<li><span class="ls-ico-remove">ls-ico-remove</span></li>
<li><span class="ls-ico-accessibility">ls-ico-accessibility</span></li>
<li><span class="ls-ico-tree">ls-ico-tree</span></li>
<li><span class="ls-ico-cloud">ls-ico-cloud</span></li>
<li><span class="ls-ico-cloud-download">ls-ico-cloud-download</span></li>
<li><span class="ls-ico-cloud-upload">ls-ico-cloud-upload</span></li>
<li><span class="ls-ico-download2">ls-ico-download2</span></li>
<li><span class="ls-ico-upload2">ls-ico-upload2</span></li>
<li><span class="ls-ico-globe">ls-ico-globe</span></li>
<li><span class="ls-ico-earth">ls-ico-earth</span></li>
<li><span class="ls-ico-link">ls-ico-link</span></li>
<li><span class="ls-ico-flag">ls-ico-flag</span></li>
<li><span class="ls-ico-attachment">ls-ico-attachment</span></li>
<li><span class="ls-ico-eye">ls-ico-eye</span></li>
<li><span class="ls-ico-eye-blocked">ls-ico-eye-blocked</span></li>
<li><span class="ls-ico-star">ls-ico-star</span></li>
<li><span class="ls-ico-star2">ls-ico-star2</span></li>
<li><span class="ls-ico-star3">ls-ico-star3</span></li>
<li><span class="ls-ico-thumbs-up">ls-ico-thumbs-up</span></li>
<li><span class="ls-ico-thumbs-up2">ls-ico-thumbs-up2</span></li>
<li><span class="ls-ico-info">ls-ico-info</span></li>
<li><span class="ls-ico-cancel-circle">ls-ico-cancel-circle</span></li>
<li><span class="ls-ico-checkmark-circle">ls-ico-checkmark-circle</span></li>
<li><span class="ls-ico-close">ls-ico-close</span></li>
<li><span class="ls-ico-checkmark">ls-ico-checkmark</span></li>
<li><span class="ls-ico-minus">ls-ico-minus</span></li>
<li><span class="ls-ico-plus">ls-ico-plus</span></li>
<li><span class="ls-ico-checkbox-checked">ls-ico-checkbox-checked</span></li>
<li><span class="ls-ico-checkbox-unchecked">ls-ico-checkbox-unchecked</span></li>
<li><span class="ls-ico-checkbox-partial">ls-ico-checkbox-partial</span></li>
<li><span class="ls-ico-radio-checked">ls-ico-radio-checked</span></li>
<li><span class="ls-ico-radio-unchecked">ls-ico-radio-unchecked</span></li>
<li><span class="ls-ico-domain">ls-ico-domain</span></li>
<li><span class="ls-ico-edit-admin">ls-ico-edit-admin</span></li>
<li><span class="ls-ico-calendar">ls-ico-calendar</span></li>
<li><span class="ls-ico-calendar-more">ls-ico-calendar-more</span></li>
<li><span class="ls-ico-calendar-check">ls-ico-calendar-check</span></li>
<li><span class="ls-ico-chart-bar-up">ls-ico-chart-bar-up</span></li>
<li><span class="ls-ico-lamp">ls-ico-lamp</span></li>
<li><span class="ls-ico-arrow-down">ls-ico-arrow-down</span></li>
<li><span class="ls-ico-arrow-left">ls-ico-arrow-left</span></li>
<li><span class="ls-ico-arrow-up">ls-ico-arrow-up</span></li>
<li><span class="ls-ico-arrow-right">ls-ico-arrow-right</span></li>
<li><span class="ls-ico-export">ls-ico-export</span></li>
<li><span class="ls-ico-table-alt">ls-ico-table-alt</span></li>
<li><span class="ls-ico-mobile">ls-ico-mobile</span></li>
<li><span class="ls-ico-user-add">ls-ico-user-add</span></li>
<li><span class="ls-ico-list3">ls-ico-list3</span></li>
<li><span class="ls-ico-text">ls-ico-text</span></li>
<li><span class="ls-ico-text2">ls-ico-text2</span></li>
<li><span class="ls-ico-document">ls-ico-document</span></li>
<li><span class="ls-ico-docs">ls-ico-docs</span></li>
<li><span class="ls-ico-book">ls-ico-book</span></li>
<li><span class="ls-ico-target">ls-ico-target</span></li>
<li><span class="ls-ico-hours">ls-ico-hours</span></li>
<li><span class="ls-ico-month">ls-ico-month</span></li>
<li><span class="ls-ico-week">ls-ico-week</span></li>
<li><span class="ls-ico-mysql">ls-ico-mysql</span></li>
<li><span class="ls-ico-postgres">ls-ico-postgres</span></li>
<li><span class="ls-ico-ftp">ls-ico-ftp</span></li>
<li><span class="ls-ico-origins">ls-ico-origins</span></li>
<li><span class="ls-ico-history">ls-ico-history</span></li>
<li><span class="ls-ico-blank">ls-ico-blank</span></li>
<li><span class="ls-ico-trophy">ls-ico-trophy</span></li>
<li><span class="ls-ico-bukets">ls-ico-bukets</span></li>
<li><span class="ls-ico-multibuckets">ls-ico-multibuckets</span></li>
<li><span class="ls-ico-location">ls-ico-location</span></li>
<li><span class="ls-ico-cake">ls-ico-cake</span></li>
</ul>

</div>

<div class="doc-section">
  <h2 class="doc-title-2">Ícones de Painel<a class="ls-tag-info ls-float-right" href="/locawebstyle/documentacao/css/icones-panel">ver documentação</a></h2>
  <hr>
  <ul class="list-icons">
  <li><span class="ls-ico-panel-streaming">ls-ico-panel-streaming</span></li>
  <li><span class="ls-ico-panel-hospedagem">ls-ico-panel-hospedagem</span></li>
  <li><span class="ls-ico-panel-emkt">ls-ico-panel-emkt</span></li>
  <li><span class="ls-ico-panel-cloud">ls-ico-panel-cloud</span></li>
  <li><span class="ls-ico-panel-smtp">ls-ico-panel-smtp</span></li>
  <li><span class="ls-ico-panel-dns">ls-ico-panel-dns</span></li>
  <li><span class="ls-ico-panel-backup">ls-ico-panel-backup</span></li>
  <li><span class="ls-ico-panel-domains">ls-ico-panel-domains</span></li>
  <li><span class="ls-ico-panel-storage">ls-ico-panel-storage</span></li>
  <li><span class="ls-ico-panel-pabx">ls-ico-panel-pabx</span></li>
</ul>

</div>

<hr class="doc-hr">

<div class="doc-section">
  <h2 class="doc-title-2">Listas <a class="ls-tag-info ls-float-right" href="/locawebstyle/documentacao/componentes/listas">ver documentação</a></h2>
  <hr>
  <div class="ls-list">
  <header class="ls-list-header">
    <div class="ls-list-title col-md-9">
      <a href="#" >Identificador da conta</a>
      <small>Fake Product Name I </small>
    </div>
    <div class="col-md-3 ls-txt-right">
      <a href="<#" class="ls-btn-primary">Administrar</a>
    </div>
  </header>
  <div class="ls-list-content ">
    <div class="col-xs-12 col-md-6">
      <span class="ls-list-label">Status</span>
      <strong>Publicado</strong>
    </div>
    <div class="col-xs-12 col-md-6">
      <span class="ls-list-label">Status</span>
      <strong>Publicado</strong>
    </div>
  </div>
</div>

<div class="ls-list">
  <header class="ls-list-header">
    <div class="col-md-9">
      <img class="ls-list-image" src="../../assets/images/locastyle/logo-locaweb.png" />
    </div>
    <div class="col-md-3 ls-txt-right">
      <a href="<#" class="ls-btn-primary">Administrar</a>
    </div>
  </header>
  <div class="ls-list-content ">
    <div class="col-xs-12 col-md-6">
      <span class="ls-list-label">Status</span>
      <strong>Publicado</strong>
    </div>
    <div class="col-xs-12 col-md-6">
      <span class="ls-list-label">Status</span>
      <strong>Publicado</strong>
    </div>
  </div>
</div>

<div class="ls-list">
  <header class="ls-list-header">
    <div class="col-md-9">
      <div class="ls-list-title ">
        <a href="#" >Identificador da conta</a>
      </div>
      <div class="ls-list-description">
        <p>Nihil quam veniam ab rem necessitatibus aliquam possimus inventore. officia quos natus aut et excepturi ut sequi accusantium dolorum. numquam quae voluptatem voluptatem quo non reiciendis in suscipit ducimus ut quae nihil voluptatem. aut sit sapiente commodi quis eum est. aut dignissimos quo rerum itaque culpa aspernatur. amet ipsum expedita illum numquam distinctio quis ullam placeat</p>
        <ul>
            <li>Item 0</li>
            <li>Item 1</li>
            <li>Item 2</li>
            <li>Item 3</li>
            <li>Item 4</li>
        </ul>
      </div>
    </div>
    <div class="col-md-3 ls-txt-right">
      <a href="<#" class="ls-btn-primary">Administrar</a>
    </div>
  </header>
  <div class="ls-list-content ">
    <div class="col-xs-12 col-md-6">
      <span class="ls-list-label">Status</span>
      <strong>Publicado</strong>
    </div>
    <div class="col-xs-12 col-md-6">
      <span class="ls-list-label">Status</span>
      <strong>Publicado</strong>
    </div>
  </div>
</div>

</div>

<hr class="doc-hr">

<div class="doc-section">
  <h2 class="doc-title-2">Modal <a class="ls-tag-info ls-float-right" href="/locawebstyle/documentacao/componentes/modal">ver documentação</a></h2>
  <hr>
  <button data-ls-module="modal" data-target="#myAwesomeModal" class="ls-btn-primary">Ative a modal</button>

<div class="ls-modal" id="myAwesomeModal">
  <div class="ls-modal-box">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title">Modal title</h4>
    </div>
    <div class="ls-modal-body">
      <p>Et quis fugit libero. Amet fugit illum alias. Consequatur repellendus eos quibusdam sunt necessitatibus porro voluptatibus. Aut eveniet sint non cupiditate. Repellat saepe iure harum corporis enim dignissimos totam temporibus. Commodi veniam explicabo quibusdam labore beatae veniam. Saepe error corrupti alias numquam nisi vero aut suscipit. Voluptate iusto et sed impedit. Voluptas dolorem similique eos ratione libero tenetur culpa assumenda et eum odit molestiae consequatur temporibus. Sit excepturi quia omnis est quia rerum explicabo minus natus laboriosam molestiae dignissimos. Et quia quia vitae vel nulla dolorem reprehenderit ipsam vel. Optio fugiat nihil error ut. Explicabo quis nulla est a enim inventore tempora magni corrupti aliquam cumque enim ipsa. Iusto nemo repudiandae debitis maiores accusantium quidem mollitia. Quaerat ex repellat molestias id aliquam temporibus eaque dolor fugit veniam et omnis laudantium</p>
    </div>
    <div class="ls-modal-footer">
      <button class="ls-btn ls-float-right" data-dismiss="modal">Close</button>
      <button type="submit" class="ls-btn-primary">Save changes</button>
    </div>
  </div>
</div><!-- /.modal -->

</div>

<hr class="doc-hr">

<div class="doc-section">
  <h2 class="doc-title-2">Popover <a class="ls-tag-info ls-float-right" href="/locawebstyle/documentacao/componentes/popover">ver documentação</a></h2>
  <hr>
  <a href="#" class="ls-btn-primary" data-ls-module="popover" data-trigger="hover" data-title="Titulo do popover 3" data-content="<p>Conteúdo do popover 3</p>" data-placement="left">
  Hover (left)
</a>
<a href="#" class="ls-btn-primary" data-ls-module="popover" data-ls-popover="open" data-title="Titulo do popover 2" data-content="<ul class='ls-no-list-style'>
  <li>Lista 1</li>
  <li>Lista 2</li>
  <li>Lista 3</li>
</ul>" data-placement="top">
  Start opened
</a>
<a href="#" class="ls-btn-primary" data-ls-module="popover" data-title="Titulo do popover 2" data-content="<ul class='ls-no-list-style'>
  <li>Lista 1</li>
  <li>Lista 2</li>
  <li>Lista 3</li>
</ul>" data-placement="top">
  Click (top)
</a>
<a href="#" class="ls-btn-primary" data-ls-module="popover" data-title="Titulo do popover 1" data-content="<p>Conteúdo do popover 1 Conteúdo do popover 1 Conteúdo do popover 1 Conteúdo do popover 1 Conteúdo do popover 1 </p>" data-placement="bottom">
  Click (bottom)
</a>
<a href="#" class="ls-btn-primary" data-custom-class="ls-color-success" data-ls-module="popover" data-title="Titulo do popover 4" data-content="<p>Conteúdo do popover 4</p>" data-placement="right">
  Click (right)
</a>

<a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Aqui usamos a classe <strong class='ls-color-danger'>.ls-ico-help</strong>" data-title="Popover com ícone de ajuda"></a>

</div>

<hr class="doc-hr">

<div class="doc-section">
  <h2 class="doc-title-2">Tags <a class="ls-tag-info ls-float-right" href="/locawebstyle/documentacao/componentes/tags">ver documentação</a></h2>
  <hr>
  <a href="#" class="ls-tag">Default</a>
<a href="#" class="ls-tag-primary">Primary</a>
<a href="#" class="ls-tag-success">Success</a>
<a href="#" class="ls-tag-info">Info</a>
<a href="#" class="ls-tag-warning">Warning</a>
<a href="#" class="ls-tag-danger">Danger</a>

  <br><br>
  <br><br>
  <h1 class="ls-title-1">Exemplo de Título <span class="ls-tag">Novo</span></h1>
<hr>
<h2 class="ls-title-2">Exemplo de Título <span class="ls-tag">Novo</span></h2>
<hr>
<h3 class="ls-title-3">Exemplo de Título <span class="ls-tag">Novo</span></h3>
<hr>
<h4 class="ls-title-4">Exemplo de Título <span class="ls-tag">Novo</span></h4>
<hr>
<h5 class="ls-title-5">Exemplo de Título <span class="ls-tag">Novo</span></h5>
<hr>
<h6 class="ls-title-6">Exemplo de Título <span class="ls-tag">Novo</span></h6>

</div>

<hr class="doc-hr">

<div class="doc-section">
  <h2 class="doc-title-2">Tooltip <a class="ls-tag-info ls-float-right" href="/locawebstyle/documentacao/componentes/tooltip">ver documentação</a></h2>
  <hr>
  <a href="#" class="ls-tooltip-top ls-btn" aria-label="Conteúdo exibido acima">Cima</a>
<a href="#" class="ls-tooltip-right ls-btn" aria-label="Conteúdo exibido no lado direito">Direita</a>
<a href="#" class="ls-tooltip-bottom ls-btn" aria-label="Conteúdo exibido abaixo">Baixo</a>
<a href="#" class="ls-tooltip-left ls-btn" aria-label="Conteúdo exibido no lado esquerdo">Esquerda</a>
<a href="#" class="ls-tooltip-top-left ls-btn" aria-label="Conteúdo exibido no topo para a esquerda">Topo Esquerda</a>
<a href="#" class="ls-tooltip-right ls-btn" disabled aria-label="Conteúdo exibido com botão desativado">Desativado</a>

</div>

<hr class="doc-hr">

<div class="doc-section">
  <h2 class="doc-title-2">Separador com texto</h2>
  <hr>
  <hr data-ls-text="Exemplo de texto" />

</div>
    
    
  </div>
<?php //include_once "inc/footer.php"; ?>
</main>
<?php include_once "inc/notificacoes.php"; ?>
<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="//assets.locaweb.com.br/locastyle/edge/javascripts/locastyle.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="js/sweetalert2.min.js"></script>
<script type="application/javascript">
/*
Swal.fire({
  //position: 'top-end',
  icon: 'success',
  title: 'Tudo certo por aqui',
  showConfirmButton: false,
  timer: 1500
})
*/
</script>
</body>
</html>