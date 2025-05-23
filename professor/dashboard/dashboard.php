<!DOCTYPE html>
<html lang="pt_br">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard - Efnify</title>
	<meta charset="UTF-8">
    <meta name="theme-color" content="#5c6bc0">
    <meta name="msapplication-navbutton-color" content="#5c6bc0">
    <meta name="apple-mobile-web-app-status-bar-style" content="#5c6bc0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="../cssn/materialize.min.css" />
    <link type="text/css" rel="stylesheet" href="../cssn/prism.css" />
    <link type="text/css" rel="stylesheet" href="../cssn/app.css" />
    <link type="text/css" rel="stylesheet" href="../cssn/helper.css" />
    <link type="text/css" rel="stylesheet" href="../cssn/responsive.css" />
    <link type="text/css" rel="stylesheet" href="../cssn/default.css" />
</head>
<body>

    <!-- //////////////////////////////////////////////////////////////////////////// -->
    <!--Efnify body-->
    <div class="ui-app">

        <!-- //////////////////////////////////////////////////////////////////////////// -->
        <!--Efnify body page wrapper -->
        <div class="ui-app__wrapper" id="app-layout-control">

            <!-- ////////////////s//////////////////////////////////////////////////////////// -->
            <!--prepage loader-->
            <div id="prepage-loader">
                <div class="ui-app__prepage-loader spinner">
                    <div class="double-bounce1"></div>
                    <div class="double-bounce2"></div>
                </div>
            </div>
            <!-- End prepage loader-->
            <!-- //////////////////////////////////////////////////////////////////////////// -->

            <!-- /////////////////////////////////////////////////////////////////// -->
            <!--navbar/header-->
			<?php include "assets/nav-bar.php"; ?>
            <!--End navbar/header-->
            <!-- //////////////////////////////////////////////////////////////////// -->


            <!-- //////////////////////////////////////////////////////////////////////////// -->
            <!--Left sidenav/sidebar-->
			<?php include "assets/aside-left.php"; ?>
            <!--End Left sidenav/sidebar-->
            <!-- //////////////////////////////////////////////////////////////////////////// -->

            <!-- //////////////////////////////////////////////////////////////////////////// -->
            <!--Right sidenav/sidebar-->
			<?php //include "assets/options-right.php"; ?>
            <!-- Right sidenav toggle (show and hide right sidenav on click button) 
            <a href="#" data-target="ui-app__right-sidenav-slide-out" class="ui-app__right-sidenav-toggle sidenav-trigger btn-floating waves-effect waves-light" id="right-sidenav-toggle"><i class="material-icons ">settings</i></a>
			-->
            <!--End Right sidenav/sidebar-->
            <!-- //////////////////////////////////////////////////////////////////////////// -->

            <!-- //////////////////////////////////////////////////////////////////////////// -->
            <!--Page Body-->
            <main>

			   <!-- Page heading -->
                <div class="row ui-app__row">
                    <div class="col s12 ui-app__header">
                        <!-- title -->
                        <h1 class="ui-app__header__title display-1">Dashboard</h1>
                        <!-- sub heading -->
                        <p class="ui-app__header__body subheading">Tela dashboard</p>

                    </div>
                </div>
                <!-- End page heading -->
                <!-- Page content -->
                <div class="row">
                    <!-- Analytics Header -->

                    <div class="col s12 m6 l3">
                        <div class="card ui-app__page-content">
                            <div class="card-content ui-app__page-content__analytics">

                                <div class="card-body">
                                    <div class="ui-app__page-content__analytics--data">
                                        <div class="right"><i class="material-icons ">supervised_user_circle</i>
                                        </div>
                                        <h3 class="headline">678450</h3>
                                        <div class="text-muted">Visitors online</div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col s12 m6 l3">
                        <div class="card ui-app__page-content">
                            <div class="card-content ui-app__page-content__analytics">

                                <div class="card-body">
                                    <div class="ui-app__page-content__analytics--data">
                                        <div class="right"><i class="material-icons ">monetization_on</i>
                                        </div>
                                        <h3 class="headline">5698</h3>
                                        <div class="text-muted">Total Sales</div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col s12 m6 l3">
                        <div class="card ui-app__page-content">
                            <div class="card-content ui-app__page-content__analytics ">

                                <div class="card-body">
                                    <div class="ui-app__page-content__analytics--data">
                                        <div class="right"><i class="material-icons ">library_books</i>
                                        </div>
                                        <h3 class="headline">560</h3>
                                        <div class="text-muted">Total Projects</div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col s12 m6 l3">
                        <div class="card ui-app__page-content">
                            <div class="card-content ui-app__page-content__analytics">

                                <div class="card-body">
                                    <div class="ui-app__page-content__analytics--data">
                                        <div class="right"><i class="material-icons ">attach_money</i>
                                        </div>
                                        <h3 class="headline">56980</h3>
                                        <div class="text-muted">Today Income</div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- End Analytics Header -->

                    <!-- sales chart -->
                    <div class="col s12">

                        <div class="card ui-app__page-content">
                            <div class="card-content">
                                <!-- title -->
                                <div class="card-title headline">Analytics report</div>

                                <div class="card-body">
                                    <div style="min-height: 375px">
                                        <canvas id="dashboard-analytics-report-chart"></canvas>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                    <!-- sales chart -->

                    <!-- Report Table -->
                    <div class="col s12 m12 l6">

                        <div class="card ui-app__page-content">
                            <div class="card-content">
                                <!-- title -->
                                <div class="card-title headline">Users report</div>

                                <div class="card-body">
                                    <table class="responsive-table">
                                        <thead>
                                            <tr>
                                                <th><i class="material-icons">people</i></th>
                                                <th>User</th>
                                                <th>Items</th>
                                                <th>Activity</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><img src="../assets/images/user1.jpg" alt="user profile picture" class="circle responsive-img" style="width: 2rem;height: 2rem;"></td>
                                                <td>
                                                    <div>Audrey Gill</div>
                                                    <div class="text-muted">Registered: May 23, 2018</div>
                                                </td>

                                                <td>03</td>
                                                <td>
                                                    <div class="text-muted">Last login</div>
                                                    <div>10 minutes ago</div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><img src="../assets/images/user2.jpg" alt="user profile picture" class="circle responsive-img" style="width: 2rem;height: 2rem;"></td>
                                                <td>
                                                    <div>Bernadette Arnold</div>
                                                    <div class="text-muted">Registered: June 22, 2018</div>
                                                </td>

                                                <td>01</td>
                                                <td>
                                                    <div class="text-muted">Last login</div>
                                                    <div>12 hours ago</div>
                                                </td>
                                            </tr>


                                            <tr>
                                                <td><img src="../assets/images/user3.jpg" alt="user profile picture" class="circle responsive-img" style="width: 2rem;height: 2rem;"></td>
                                                <td>
                                                    <div>Chloe Claire</div>
                                                    <div class="text-muted">Registered: May 22, 2018</div>
                                                </td>

                                                <td>07</td>
                                                <td>
                                                    <div class="text-muted">Last login</div>
                                                    <div>Just Now</div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><img src="../assets/images/user4.jpg" alt="user profile picture" class="circle responsive-img" style="width: 2rem;height: 2rem;"></td>
                                                <td>
                                                    <div>Dorothy Elizabeth</div>
                                                    <div class="text-muted">Registered: June 20, 2018</div>
                                                </td>

                                                <td>09</td>
                                                <td>
                                                    <div class="text-muted">Last login</div>
                                                    <div>30 min ago</div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><img src="../assets/images/user5.jpg" alt="user profile picture" class="circle responsive-img" style="width: 2rem;height: 2rem;"></td>
                                                <td>
                                                    <div>Carolyn Emma</div>
                                                    <div class="text-muted">Registered: June 12, 2018</div>
                                                </td>

                                                <td>06</td>
                                                <td>
                                                    <div class="text-muted">Last login</div>
                                                    <div>5 hours ago</div>
                                                </td>
                                            </tr>
                                        </tbody>

                                    </table>
                                </div>

                            </div>
                        </div>

                    </div>
                    <!-- Report Table -->

                    <!-- Notification report -->

                    <div class="col s12 m12 l6">

                        <div class="card ui-app__page-content">
                            <div class="card-content">
                                <!-- title -->
                                <div class="card-title headline">Recent notifications</div>

                                <div class="card-body">
                                    <ul class="collection" style="border:0px">
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

                            </div>
                        </div>

                    </div>
                    <!-- Notification report -->


                </div>
                <!--End Page content -->

            </main>
            <!--End page body-->
            <!-- //////////////////////////////////////////////////////////////////////////// -->


            <!-- //////////////////////////////////////////////////////////////////////////// -->
            <!--Footer-->
			<?php include "assets/foot.php"; ?>
            <!--End footer-->
            <!-- //////////////////////////////////////////////////////////////////////////// -->

        </div>
        <!-- End Efnify body page wrapper -->
        <!-- //////////////////////////////////////////////////////////////////////////// -->
    </div>

    <!-- End Efnify body -->
    <!-- //////////////////////////////////////////////////////////////////////////// -->


    <!-- //////////////////////////////////////////////////////////////////////////// -->
    <!--  Scripts-->

    <script src="../jsn/jquery.min.js"></script>
    <script src="../jsn/materialize.min.js"></script>
    <script src="../jsn/prism.js"></script>
    <script src="../jsn/Chart.min.js"></script>
    <script src="../jsn/app.js"></script>
    <script src="../jsn/search.js"></script>

    <!-- charts script (Only use for demo purpose) -->
    <script>
        // Analytics report
        var ctx = document.getElementById('dashboard-analytics-report-chart').getContext('2d');

        var dashboardAnalyticsReportChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                datasets: [{
                    label: "Visitors online",
                    borderColor: '#4db6ac',
                    pointBackgroundColor: '#4db6ac',
                    pointRadius: 0,
                    backgroundColor: '#4db6ac',
                    legendColor: '#4db6ac',
                    fill: true,
                    borderWidth: 2,
                    data: [154, 184, 175, 203, 210, 231, 240, 278, 252, 312, 320, 374]
                }, {
                    label: "Total Sales",
                    borderColor: '#64b5f6',
                    pointBackgroundColor: '#64b5f6',
                    pointRadius: 0,
                    backgroundColor: '#64b5f6',
                    legendColor: '#64b5f6',
                    fill: true,
                    borderWidth: 2,
                    data: [256, 230, 245, 287, 240, 250, 230, 295, 331, 431, 456, 521]
                }, {
                    label: "Total Projects",
                    borderColor: '#4dd0e1',
                    pointBackgroundColor: '#4dd0e1',
                    pointRadius: 0,
                    backgroundColor: '#4dd0e1',
                    legendColor: '#4dd0e1',
                    fill: true,
                    borderWidth: 2,
                    data: [542, 480, 430, 550, 530, 453, 380, 434, 568, 610, 700, 900]
                }, {
                    label: "Today Income",
                    borderColor: '#ba68c8',
                    pointBackgroundColor: '#ba68c8',
                    pointRadius: 0,
                    backgroundColor: '#ba68c8',
                    legendColor: '#ba68c8',
                    fill: true,
                    borderWidth: 2,
                    data: [592, 680, 739, 558, 638, 499, 380, 734, 568, 610, 780, 910]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    display: true
                },
                tooltips: {
                    bodySpacing: 4,
                    mode: "nearest",
                    intersect: 0,
                    position: "nearest",
                    xPadding: 10,
                    yPadding: 10,
                    caretPadding: 10
                },
                layout: {
                    padding: {
                        left: 5,
                        right: 5,
                        top: 15,
                        bottom: 15
                    }
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            fontStyle: "500",
                            beginAtZero: false,
                            maxTicksLimit: 5,
                            padding: 10
                        },
                        gridLines: {
                            drawTicks: false,
                            display: false
                        }
                    }],
                    xAxes: [{
                        gridLines: {
                            zeroLineColor: "transparent"
                        },
                        ticks: {
                            padding: 10,
                            fontStyle: "500"
                        }
                    }]
                }
            }
        });
    </script>

    <!--End scripts-->
    <!-- //////////////////////////////////////////////////////////////////////////// -->
</body>
<!--End body-->
<!-- //////////////////////////////////////////////////////////////////////////// -->

</html>
<!--End HTML-->
<!-- //////////////////////////////////////////////////////////////////////////// -->