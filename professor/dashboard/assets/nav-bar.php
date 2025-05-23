<div class="navbar-fixed">
                <nav class="ui-app__wrapper__navbar">
                    <div class="nav-wrapper">

                        <!-- //////////////////////////////////////////////////////// -->
                        <!-- navbar left side  -->
                        <div class="ui-app__wrapper__navbar__leftside--icons">

                            <!-- left sidenav toggle button(show and hide sidenav) -->
                            <div class="left ui-app__wrapper__navbar__leftside--icons__sidenav--toggle ui-app__wrapper__navbar__leftside--icon__item" data-target="ui-app__left-sidenav-slide-out" id="left-sidenav-toggle">
                                <i class="material-icons">menu</i>
                            </div>

                            <!-- Efnify app/brand title -->
                            <a href="index.php" class="brand-logo ui-app__wrapper__navbar__leftside--icon__item">SMECEL</a>

                            <!-- left sidenav toggle button(small and large sidenav) -->
                            <div class="ui-app__wrapper__navbar__leftside--icons__sidenav-small--toggle hide-on-med-and-down ui-app__wrapper__navbar__leftside--icon__item" id="left-sidenav-small-toggle">
                                <i class="material-icons">radio_button_checked</i>
                            </div>
                        </div>
                        <!-- End navbar left side  -->
                        <!-- ///////////////////////////////////////////////////////////////// -->

                        <!--Search box-->
                        <!-- ////////////////////////////////////////////////////////////// -->
                        <div class="ui-app__wrapper__navbar__leftside__search hide-on-med-and-down">
                            <!-- Search form -->
                            <form action="#">
                                <div class="input-field">
                                    <!--Search input-->
                                    <input id="search" type="search" autocomplete="off" placeholder="Faça sua busca">
                                    <!--End Search input-->
                                    <!--Search icon-->
                                    <label class="label-icon" for="search"><i class="material-icons">search</i></label>
                                    <!--End Search icon-->



                                </div>
                            </form>
                            <!-- End Search form -->
                        </div>
                        <!--End Search box-->
                        <!-- ////////////////////////////////////////////////////////////////// -->

                        <!-- ////////////////////////////////////////////////////////////////// -->
                        <!-- navbar right side  -->
                        <div class="ui-app__wrapper__navbar__rightside--icons right">
                            <!-- navbar menu list -->
                            <ul>
                                <!-- full menu demo -->
                                <!-- End full menu demo -->
                                
								<!-- refresh/reload page button -->
                                <li class="ui-app__wrapper__navbar__rightside--icons__item hide-on-small-only" id="app-page-refresh"><i class="material-icons">refresh</i></li>

                                <!-- notification list -->
                                <li class="ui-app__wrapper__navbar__rightside--icons__item ui-app__wrapper__navbar__rightside--notifications notification-badge" data-target='dropdown-notifications-target' data-notifications="0"><i class="material-icons">notifications</i></li>


                                <!-- User profile -->
                                <li class="ui-app__wrapper__navbar__rightside--users ui-app__wrapper__navbar__rightside--icons__item large" data-target="dropdown-user-profile-target">
									<i class="large material-icons">arrow_drop_down</i>
                                </li>

                            </ul>

                            <!-- User profile dropdown structure -->
                            <div id="dropdown-user-profile-target" class="ui-app__wrapper__navbar__rightside--users__dropdown dropdown-content">
                                <ul>
                                    <li><a href="#!"><i class="material-icons">perm_identity</i>Meus dados</a></li>
                                    <li><a href="javascript:void(0)" class="toggle-right-sidenav"><i class="material-icons">settings</i>Configurações</a></li>
                                    <li class="divider"></li>
                                    <li><a href="lock.html"><i class="material-icons">lock</i>Senha</a></li>
                                    <li><a href="<?php echo $logoutAction ?>"><i class="material-icons">power_settings_new</i>Sair</a></li>
                                </ul>
                            </div>
                            <!-- End user profile dropdown structure -->
							
                            <!-- Notifications dropdown structure -->
                            <div id="dropdown-notifications-target" class="ui-app__wrapper__navbar__rightside--notifications__dropdown dropdown-content">
                                <ul class="collection">
                                    <li class="collection-item avatar"> <img src="../assets/images/user1.jpg" alt="user profile picture" class="circle"> <span class="title">Brunch this weekend?</span>
                                        <p>Ali Connors <span class="body-1">&mdash; I'll be in your neighborhood doing errands this weekend.</span></p>
                                    </li>
                                    <li class="collection-item avatar"> <img src="../assets/images/user2.jpg" alt="user profile picture" class="circle"> <span class="title">Oui oui</span>
                                        <p>Sandra Adams <span class="body-1">&mdash; Do you have Paris recommendations? Have you ever been?</span></p>
                                    </li>
                                    <li class="collection-item avatar"> <img src="../assets/images/user3.jpg" alt="user profile picture" class="circle"> <span class="title">Birthday gift</span>
                                        <p>Trevor Hansen <span class="body-1">&mdash; Have any ideas about what we should get Heidi for her birthday?</span></p>
                                    </li>
                                    <li class="collection-item avatar"> <img src="../assets/images/user4.jpg" alt="user profile picture" class="circle"> <span class="title">Recipe to try</span>
                                        <p>Britta Holt <span class="body-1">&mdash; We should eat this: Grate, Squash, Corn, and tomatillo Tacos.</span></p>
                                    </li>
                                </ul>
                            </div>
                            <!-- End notifications dropdown structure -->

                        </div>
                        <!-- End navbar left side  -->
                        <!-- ////////////////////////////////////////////////////////// -->
                    </div>

                </nav>
            </div>