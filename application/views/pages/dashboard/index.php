<?php

// $this->session->sess_destroy();
$this->load->view('base/header/index');
$this->load->view('base/navbar/index');
$this->load->view('base/sidebar/index');
?>

<div class="container">
    <div class="main-panel">

        <div class="content">
            <div class="panel-header bg-primary-gradient">
                <div class="page-inner py-5">
                    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                        <div>
                            <h2 class="text-white pb-2 fw-bold">Dashboard</h2>
                            <h5 class="text-white op-7 mb-2">Welcome to Food App client</h5>
                        </div>
                        <div class="ml-md-auto py-2 py-md-0">
                            <a href="#" class="btn btn-white btn-border btn-round mr-2">Settings</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="page-inner mt--5">
                <div class="row mt--2">
                    <div class="col-md-4">
                        <div class="card card-stats card-round card-nav">
                            <a href="<?= base_url(); ?>store">
                                <div class="card-body ">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-warning bubble-shadow-small">
                                                <i class="flaticon-shopping-bag"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ml-3 ml-sm-0">
                                            <div class="numbers">
                                                <h4 class="card-title">Store management</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-stats card-round">
                            <div class="card-body ">
                                <div class="row align-items-center">
                                    <div class="col-icon">
                                        <div class="icon-big text-center icon-success bubble-shadow-small">
                                            <i class="flaticon-coins "></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ml-3 ml-sm-0">
                                        <div class="numbers">
                                            <h4 class="card-title">Point-of-Sale</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-stats card-round">
                            <div class="card-body ">
                                <div class="row align-items-center">
                                    <div class="col-icon">
                                        <div class="icon-big text-center icon-primary bubble-shadow-small">
                                            <i class="flaticon-file-1"></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ml-3 ml-sm-0">
                                        <div class="numbers">
                                            <h4 class="card-title">Reports</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-7">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-head-row">
                                    <div class="card-title">User Statistics</div>
                                    <div class="card-tools">
                                        <a href="#" class="btn btn-info btn-border btn-round btn-sm mr-2">
                                            <span class="btn-label">
                                                <i class="fa fa-pencil"></i>
                                            </span>
                                            Export
                                        </a>
                                        <a href="#" class="btn btn-info btn-border btn-round btn-sm">
                                            <span class="btn-label">
                                                <i class="fa fa-print"></i>
                                            </span>
                                            Print
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart-container" style="min-height: 375px">
                                    <div class="chartjs-size-monitor" style="position: absolute; left: 0px; top: 0px; right: 0px; bottom: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;">
                                        <div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                            <div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div>
                                        </div>
                                        <div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                            <div style="position:absolute;width:200%;height:200%;left:0; top:0"></div>
                                        </div>
                                    </div>
                                    <canvas id="statisticsChart" width="445" height="375" class="chartjs-render-monitor" style="display: block; width: 445px; height: 375px;"></canvas>
                                </div>
                                <div id="myChartLegend">
                                    <ul class="0-legend html-legend">
                                        <li><span style="background-color:#f3545d"></span>Subscribers</li>
                                        <li><span style="background-color:#fdaf4b"></span>New Visitors</li>
                                        <li><span style="background-color:#177dff"></span>Active Users</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">Overall statistics</div>
                                <div class="card-category">Daily information about statistics in system</div>
                                <div class="d-flex flex-wrap justify-content-around pb-2 pt-4">
                                    <div class="px-2 pb-2 pb-md-4 text-center flex-wrap  justify-content-center align-items-center">
                                        <div id="circles-1">
                                            <div class="circles-wrp" style="position: relative; display: inline-block;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="90" height="90">
                                                    <path fill="transparent" stroke="#f1f1f1" stroke-width="7" d="M 44.99154756204665 3.500000860767564 A 41.5 41.5 0 1 1 44.942357332570026 3.500040032273624 Z" class="circles-maxValueStroke"></path>
                                                    <path fill="transparent" stroke="#FF9E27" stroke-width="7" d="M 44.99154756204665 3.500000860767564 A 41.5 41.5 0 1 1 20.644357636259837 78.60137921350231 " class="circles-valueStroke"></path>
                                                </svg>
                                                <div class="circles-text" style="position: absolute; top: 0px; left: 0px; text-align: center; width: 100%; font-size: 31.5px; height: 90px; line-height: 90px;">5</div>
                                            </div>
                                        </div>
                                        <h4 class="fw-bold mt-3 mb-0">New Users</h4>
                                    </div>
                                    <div class="px-2 pb-2 pb-md-4 text-center flex-wrap  justify-content-center align-items-center">
                                        <div id="circles-2">
                                            <div class="circles-wrp" style="position: relative; display: inline-block;"><svg xmlns="http://www.w3.org/2000/svg" width="90" height="90">
                                                    <path fill="transparent" stroke="#f1f1f1" stroke-width="7" d="M 44.99154756204665 3.500000860767564 A 41.5 41.5 0 1 1 44.942357332570026 3.500040032273624 Z" class="circles-maxValueStroke"></path>
                                                    <path fill="transparent" stroke="#2BB930" stroke-width="7" d="M 44.99154756204665 3.500000860767564 A 41.5 41.5 0 1 1 5.5495771787290025 57.88076625138973 " class="circles-valueStroke"></path>
                                                </svg>
                                                <div class="circles-text" style="position: absolute; top: 0px; left: 0px; text-align: center; width: 100%; font-size: 31.5px; height: 90px; line-height: 90px;">36</div>
                                            </div>
                                        </div>
                                        <h4 class="fw-bold mt-3 mb-0">Sales</h4>
                                    </div>
                                    <div class="px-2 pb-2 pb-md-4 text-center flex-wrap  justify-content-center align-items-center">
                                        <div id="circles-3">
                                            <div class="circles-wrp" style="position: relative; display: inline-block;"><svg xmlns="http://www.w3.org/2000/svg" width="90" height="90">
                                                    <path fill="transparent" stroke="#f1f1f1" stroke-width="7" d="M 44.99154756204665 3.500000860767564 A 41.5 41.5 0 1 1 44.942357332570026 3.500040032273624 Z" class="circles-maxValueStroke"></path>
                                                    <path fill="transparent" stroke="#F25961" stroke-width="7" d="M 44.99154756204665 3.500000860767564 A 41.5 41.5 0 0 1 69.44267714510887 78.53812060894248 " class="circles-valueStroke"></path>
                                                </svg>
                                                <div class="circles-text" style="position: absolute; top: 0px; left: 0px; text-align: center; width: 100%; font-size: 31.5px; height: 90px; line-height: 90px;">12</div>
                                            </div>
                                        </div>
                                        <h4 class="fw-bold mt-3 mb-0">Subscribers</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body pb-0">
                                <div class="h1 fw-bold float-right text-warning">+7%</div>
                                <h2 class="mb-2">213</h2>
                                <p class="text-muted">Transactions</p>
                                <div class="pull-in sparkline-fix">
                                    <div id="lineChart"><canvas width="512" height="70" style="display: inline-block; width: 512.844px; height: 70px; vertical-align: top;"></canvas></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Top Products</div>
                            </div>
                            <div class="card-body pb-0">
                                <div class="d-flex">
                                    <div class="avatar">
                                        <img src="../assets/img/logoproduct.svg" alt="..." class="avatar-img rounded-circle">
                                    </div>
                                    <div class="flex-1 pt-1 ml-2">
                                        <h6 class="fw-bold mb-1">CSS</h6>
                                        <small class="text-muted">Cascading Style Sheets</small>
                                    </div>
                                    <div class="d-flex ml-auto align-items-center">
                                        <h3 class="text-info fw-bold">+$17</h3>
                                    </div>
                                </div>
                                <div class="separator-dashed"></div>
                                <div class="d-flex">
                                    <div class="avatar">
                                        <img src="../assets/img/logoproduct.svg" alt="..." class="avatar-img rounded-circle">
                                    </div>
                                    <div class="flex-1 pt-1 ml-2">
                                        <h6 class="fw-bold mb-1">J.CO Donuts</h6>
                                        <small class="text-muted">The Best Donuts</small>
                                    </div>
                                    <div class="d-flex ml-auto align-items-center">
                                        <h3 class="text-info fw-bold">+$300</h3>
                                    </div>
                                </div>
                                <div class="separator-dashed"></div>
                                <div class="d-flex">
                                    <div class="avatar">
                                        <img src="../assets/img/logoproduct3.svg" alt="..." class="avatar-img rounded-circle">
                                    </div>
                                    <div class="flex-1 pt-1 ml-2">
                                        <h6 class="fw-bold mb-1">Ready Pro</h6>
                                        <small class="text-muted">Bootstrap 4 Admin Dashboard</small>
                                    </div>
                                    <div class="d-flex ml-auto align-items-center">
                                        <h3 class="text-info fw-bold">+$350</h3>
                                    </div>
                                </div>
                                <div class="separator-dashed"></div>
                                <div class="pull-in">
                                    <div class="chartjs-size-monitor" style="position: absolute; left: 0px; top: 0px; right: 0px; bottom: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;">
                                        <div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                            <div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div>
                                        </div>
                                        <div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                            <div style="position:absolute;width:200%;height:200%;left:0; top:0"></div>
                                        </div>
                                    </div>
                                    <canvas id="topProductsChart" width="228" height="150" class="chartjs-render-monitor" style="display: block; width: 228px; height: 150px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title fw-mediumbold">Suggested People</div>
                                <div class="card-list">
                                    <div class="item-list">
                                        <div class="avatar">
                                            <img src="../assets/img/jm_denis.jpg" alt="..." class="avatar-img rounded-circle">
                                        </div>
                                        <div class="info-user ml-3">
                                            <div class="username">Jimmy Denis</div>
                                            <div class="status">Graphic Designer</div>
                                        </div>
                                        <button class="btn btn-icon btn-primary btn-round btn-xs">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                    <div class="item-list">
                                        <div class="avatar">
                                            <img src="../assets/img/chadengle.jpg" alt="..." class="avatar-img rounded-circle">
                                        </div>
                                        <div class="info-user ml-3">
                                            <div class="username">Chad</div>
                                            <div class="status">CEO Zeleaf</div>
                                        </div>
                                        <button class="btn btn-icon btn-primary btn-round btn-xs">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                    <div class="item-list">
                                        <div class="avatar">
                                            <img src="../assets/img/talha.jpg" alt="..." class="avatar-img rounded-circle">
                                        </div>
                                        <div class="info-user ml-3">
                                            <div class="username">Talha</div>
                                            <div class="status">Front End Designer</div>
                                        </div>
                                        <button class="btn btn-icon btn-primary btn-round btn-xs">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                    <div class="item-list">
                                        <div class="avatar">
                                            <img src="../assets/img/mlane.jpg" alt="..." class="avatar-img rounded-circle">
                                        </div>
                                        <div class="info-user ml-3">
                                            <div class="username">John Doe</div>
                                            <div class="status">Back End Developer</div>
                                        </div>
                                        <button class="btn btn-icon btn-primary btn-round btn-xs">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                    <div class="item-list">
                                        <div class="avatar">
                                            <img src="../assets/img/talha.jpg" alt="..." class="avatar-img rounded-circle">
                                        </div>
                                        <div class="info-user ml-3">
                                            <div class="username">Talha</div>
                                            <div class="status">Front End Designer</div>
                                        </div>
                                        <button class="btn btn-icon btn-primary btn-round btn-xs">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                    <div class="item-list">
                                        <div class="avatar">
                                            <img src="../assets/img/jm_denis.jpg" alt="..." class="avatar-img rounded-circle">
                                        </div>
                                        <div class="info-user ml-3">
                                            <div class="username">Jimmy Denis</div>
                                            <div class="status">Graphic Designer</div>
                                        </div>
                                        <button class="btn btn-icon btn-primary btn-round btn-xs">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-primary bg-primary-gradient">
                            <div class="card-body">
                                <h4 class="mt-3 b-b1 pb-2 mb-4 fw-bold">Active user right now</h4>
                                <h1 class="mb-4 fw-bold">17</h1>
                                <h4 class="mt-3 b-b1 pb-2 mb-5 fw-bold">Page view per minutes</h4>
                                <div id="activeUsersChart"><canvas width="294" height="100" style="display: inline-block; width: 294px; height: 100px; vertical-align: top;"></canvas></div>
                                <h4 class="mt-5 pb-3 mb-0 fw-bold">Top active pages</h4>
                                <ul class="list-unstyled">
                                    <li class="d-flex justify-content-between pb-1 pt-1"><small>/product/readypro/index.html</small> <span>7</span></li>
                                    <li class="d-flex justify-content-between pb-1 pt-1"><small>/product/atlantis/demo.html</small> <span>10</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card full-height">
                            <div class="card-header">
                                <div class="card-title">Feed Activity</div>
                            </div>
                            <div class="card-body">
                                <ol class="activity-feed">
                                    <li class="feed-item feed-item-secondary">
                                        <time class="date" datetime="9-25">Sep 25</time>
                                        <span class="text">Responded to need <a href="#">"Volunteer opportunity"</a></span>
                                    </li>
                                    <li class="feed-item feed-item-success">
                                        <time class="date" datetime="9-24">Sep 24</time>
                                        <span class="text">Added an interest <a href="#">"Volunteer Activities"</a></span>
                                    </li>
                                    <li class="feed-item feed-item-info">
                                        <time class="date" datetime="9-23">Sep 23</time>
                                        <span class="text">Joined the group <a href="single-group.php">"Boardsmanship Forum"</a></span>
                                    </li>
                                    <li class="feed-item feed-item-warning">
                                        <time class="date" datetime="9-21">Sep 21</time>
                                        <span class="text">Responded to need <a href="#">"In-Kind Opportunity"</a></span>
                                    </li>
                                    <li class="feed-item feed-item-danger">
                                        <time class="date" datetime="9-18">Sep 18</time>
                                        <span class="text">Created need <a href="#">"Volunteer Opportunity"</a></span>
                                    </li>
                                    <li class="feed-item">
                                        <time class="date" datetime="9-17">Sep 17</time>
                                        <span class="text">Attending the event <a href="single-event.php">"Some New Event"</a></span>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card full-height">
                            <div class="card-header">
                                <div class="card-head-row">
                                    <div class="card-title">Support Tickets</div>
                                    <div class="card-tools">
                                        <ul class="nav nav-pills nav-secondary nav-pills-no-bd nav-sm" id="pills-tab" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link" id="pills-today" data-toggle="pill" href="#pills-today" role="tab" aria-selected="true">Today</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link active" id="pills-week" data-toggle="pill" href="#pills-week" role="tab" aria-selected="false">Week</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="pills-month" data-toggle="pill" href="#pills-month" role="tab" aria-selected="false">Month</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="avatar avatar-online">
                                        <span class="avatar-title rounded-circle border border-white bg-info">J</span>
                                    </div>
                                    <div class="flex-1 ml-3 pt-1">
                                        <h6 class="text-uppercase fw-bold mb-1">Joko Subianto <span class="text-warning pl-3">pending</span></h6>
                                        <span class="text-muted">I am facing some trouble with my viewport. When i start my</span>
                                    </div>
                                    <div class="float-right pt-1">
                                        <small class="text-muted">8:40 PM</small>
                                    </div>
                                </div>
                                <div class="separator-dashed"></div>
                                <div class="d-flex">
                                    <div class="avatar avatar-offline">
                                        <span class="avatar-title rounded-circle border border-white bg-secondary">P</span>
                                    </div>
                                    <div class="flex-1 ml-3 pt-1">
                                        <h6 class="text-uppercase fw-bold mb-1">Prabowo Widodo <span class="text-success pl-3">open</span></h6>
                                        <span class="text-muted">I have some query regarding the license issue.</span>
                                    </div>
                                    <div class="float-right pt-1">
                                        <small class="text-muted">1 Day Ago</small>
                                    </div>
                                </div>
                                <div class="separator-dashed"></div>
                                <div class="d-flex">
                                    <div class="avatar avatar-away">
                                        <span class="avatar-title rounded-circle border border-white bg-danger">L</span>
                                    </div>
                                    <div class="flex-1 ml-3 pt-1">
                                        <h6 class="text-uppercase fw-bold mb-1">Lee Chong Wei <span class="text-muted pl-3">closed</span></h6>
                                        <span class="text-muted">Is there any update plan for RTL version near future?</span>
                                    </div>
                                    <div class="float-right pt-1">
                                        <small class="text-muted">2 Days Ago</small>
                                    </div>
                                </div>
                                <div class="separator-dashed"></div>
                                <div class="d-flex">
                                    <div class="avatar avatar-offline">
                                        <span class="avatar-title rounded-circle border border-white bg-secondary">P</span>
                                    </div>
                                    <div class="flex-1 ml-3 pt-1">
                                        <h6 class="text-uppercase fw-bold mb-1">Peter Parker <span class="text-success pl-3">open</span></h6>
                                        <span class="text-muted">I have some query regarding the license issue.</span>
                                    </div>
                                    <div class="float-right pt-1">
                                        <small class="text-muted">2 Day Ago</small>
                                    </div>
                                </div>
                                <div class="separator-dashed"></div>
                                <div class="d-flex">
                                    <div class="avatar avatar-away">
                                        <span class="avatar-title rounded-circle border border-white bg-danger">L</span>
                                    </div>
                                    <div class="flex-1 ml-3 pt-1">
                                        <h6 class="text-uppercase fw-bold mb-1">Logan Paul <span class="text-muted pl-3">closed</span></h6>
                                        <span class="text-muted">Is there any update plan for RTL version near future?</span>
                                    </div>
                                    <div class="float-right pt-1">
                                        <small class="text-muted">2 Days Ago</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <footer class="footer">
            <div class="container-fluid">
                <nav class="pull-left">
                    <ul class="nav">
                        <li class="nav-item">
                            <a class="nav-link" href="https://www.themekita.com">
                                ThemeKita
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                Help
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                Licenses
                            </a>
                        </li>
                    </ul>
                </nav>
                <div class="copyright ml-auto">
                    2018, made with <i class="fa fa-heart heart text-danger"></i> by <a href="https://www.themekita.com">ThemeKita</a>
                </div>
            </div>
        </footer>
    </div>

</div>

<?php
$this->load->view('base/scripts/index');
$this->load->view('base/footer/index');
?>