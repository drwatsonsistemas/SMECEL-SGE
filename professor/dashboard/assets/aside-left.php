            <aside class="ui-app__left-sidenav sidenav sidenav-fixed" id="ui-app__left-sidenav-slide-out">
			
				<div style="padding:10px;" class="center-align">

					<?php if ($row_ProfLogado['func_foto']=="") { ?>
					<img src="<?php echo URL_BASE.'professor/fotos/' ?>semfoto.jpg" width="50%" class="hoverable">
					<?php } else { ?>
					<img src="<?php echo URL_BASE.'professor/fotos/' ?><?php echo $row_ProfLogado['func_foto']; ?>" width="50%" class="hoverable">
					<?php } ?>
					<br>
					<small><a href="foto.php"><i class="tiny material-icons">photo_camera</i></a></small>		
					<small style="font-size:14px;">
					<?php echo current( str_word_count($row_ProfLogado['func_nome'],2)); ?>
					<?php $word = explode(" ", trim($row_ProfLogado['func_nome'])); echo $word[count($word)-1]; ?>
					</small>

				</div>	
						
				<hr>		
                <!-- sidenav menu list -->
                <ul class="no-margin">
                    <!-- dashboard menu -->



                    <!-- sidenav main menu list -->
                    <li>
                        <ul class="ui-app__left-sidenav__menu collapsible collapsible-accordion ui-app__scrollbar ">


                            <li class="no-menu-list">
                                <a href="index.php" class="collapsible-header"><i class="material-icons left">dashboard</i>Início</a>
                                <div class="collapsible-body">
                                </div>
                            </li>


                            <li class="no-menu-list">
                                <a href="avisos.php" class="collapsible-header"><i class="material-icons left">add_alert</i>Avisos</a>
                                <div class="collapsible-body">
                                </div>
                            </li>


                            <li class="no-menu-list">
                                <a href="forum.php" class="collapsible-header"><i class="material-icons left">forum</i>Fórum</a>
                                <div class="collapsible-body">
                                </div>
                            </li>

                            <li class="no-menu-list">
                                <a href="documentos.php" class="collapsible-header"><i class="material-icons left">archive</i>Documentos</a>
                                <div class="collapsible-body">
                                </div>
                            </li>


                            <li class="no-menu-list">
                                <a href="tutoriais.php" class="collapsible-header"><i class="material-icons left">ondemand_video</i>Tutoriais</a>
                                <div class="collapsible-body">
                                </div>
                            </li>

                            <li>
                                <a class="collapsible-header waves-effect waves-default"><i class="material-icons left">build</i>Extras<i class="material-icons right">arrow_drop_down</i></a>
                                <div class="collapsible-body">
                                    <ul>
                                        <li><a href="range-slider.html" class="waves-effect waves-default">Foto</a></li>
                                        <li><a href="sweetalert.html" class="waves-effect waves-default">Alterar senha</a></li>
                                    </ul>
                                </div>
                            </li>
                        </ul>

                    </li>
                    <!--end sidenav main menu list -->
					



                </ul>
                <!-- end sidenav menu list -->
            </aside>
