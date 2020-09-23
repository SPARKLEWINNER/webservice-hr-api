<?php

// $this->session->sess_destroy();
$this->load->view('base/header/index');
$this->load->view('base/navbar/index');
$this->load->view('base/sidebar/index');
?>

<div class="container">
    <div class="main-panel">

        <div class="content">
            <div class="panel-header bg-info-gradient">
                <div class="page-inner py-5">
                    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                        <div>
                            <h2 class="text-white pb-2 fw-bold">Add new product</h2>
                            <h5 class="text-white op-7 mb-2">Welcome to Food App client</h5>
                        </div>
                        <div class="ml-md-auto py-2 py-md-0">
                            <a href="<?= base_url(); ?>store/products" class="text-white border-0 mr-4">
                                <i class="flaticon-left-arrow-1 mr-2"></i>
                                Return to Product dashboard </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="page-inner mt--5">
                <div class="row mt--2">
                    <div class="col-md-4">
                        <div class="card card-stats card-round card-nav card-info">
                            <a href="<?= base_url(); ?>products/add">
                                <div class="card-body ">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-info bubble-shadow-small">
                                                <i class="flaticon-tea-cup"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ml-3 ml-sm-0">
                                            <div class="numbers">
                                                <h4 class="card-title text-white">Add New product</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-stats card-round">
                            <a href="<?= base_url(); ?>products/categories">
                                <div class="card-body ">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-secondary bubble-shadow-small">
                                                <i class="flaticon-box-3"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ml-3 ml-sm-0">
                                            <div class="numbers">
                                                <h4 class="card-title">Categories</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-stats card-round">
                            <a href="<?= base_url(); ?>products/tags">
                                <div class="card-body ">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-success bubble-shadow-small">
                                                <i class="icon-tag"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ml-3 ml-sm-0">
                                            <div class="numbers">
                                                <h4 class="card-title">Tags</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <?php $this->load->view('forms/products/add/index'); ?>
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
?>
<script>
    CKEDITOR.replace('product_description', {
        toolbarGroups: [{
                name: 'clipboard',
                groups: ['clipboard', 'undo']
            },
            {
                name: 'editing',
                groups: ['find', 'selection', 'spellchecker', 'editing']
            },
            {
                name: 'links',
                groups: ['links']
            },
            {
                name: 'insert',
                groups: ['insert']
            },
            {
                name: 'forms',
                groups: ['forms']
            },
            {
                name: 'tools',
                groups: ['tools']
            },
            {
                name: 'document',
                groups: ['mode', 'document', 'doctools']
            },
            {
                name: 'others',
                groups: ['others']
            },
            '/',
            {
                name: 'basicstyles',
                groups: ['basicstyles', 'cleanup']
            },
            {
                name: 'paragraph',
                groups: ['list', 'indent', 'blocks', 'align', 'bidi', 'paragraph']
            },
            {
                name: 'styles',
                groups: ['styles']
            },
            {
                name: 'colors',
                groups: ['colors']
            },
            {
                name: 'about',
                groups: ['about']
            }
        ],
        removeButtons: 'Underline,Subscript,Superscript,Scayt,Image,Unlink,Anchor,PasteText,PasteFromWord,Paste,Source,Maximize,About'
    });
</script>
<?php
$this->load->view('base/footer/index');
?>