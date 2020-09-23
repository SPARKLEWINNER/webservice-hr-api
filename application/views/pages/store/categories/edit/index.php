<?php

// $this->session->sess_destroy();
$this->load->view('base/header/index');
$this->load->view('base/navbar/index');
$this->load->view('base/sidebar/index');
?>

<div class="container">
    <div class="main-panel">

        <div class="content">
            <div class="panel-header bg-secondary-gradient">
                <div class="page-inner py-5">
                    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                        <div>
                            <h2 class="text-white pb-2 fw-bold">Categories dashboard</h2>
                            <h5 class="text-white op-7 mb-2">Welcome to Food App client</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="page-inner mt--5">

                <div class="row mt-5">
                    <div class="col-md-12">
                        <div class="d-block py-3">
                            <a href="<?= base_url(); ?>store/categories" class="text-primary btn-link border-0 mr-4">
                                <i class="flaticon-left-arrow-3 mr-2"></i>
                                Return to Categories Dashboard </a>
                        </div>
                        <?php $this->load->view('forms/categories/edit/index'); ?>
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
?>
<script>
    function requests(e) {
        let base_url = "<?php echo base_url(); ?>";
        e.preventDefault();
        $.post({
            url: base_url + "client/store/update",
            data: $("#categoryForm").serialize(),
            dataType: "json",
            success: function(result) {
                if (result.request) {
                    success(result.message);
                } else {
                    failed(result.message);
                }
            },

        });
    }

    function moveTrash(id) {
        if (id) {
            $.post({
                url: "<?= base_url(); ?>client/store/remove/category",
                data: {
                    "bulk_action": "delete",
                    "id": [id]
                },
                dataType: "json",
                success: function(result) {
                    if (result.request) {
                        success(result.message);
                    } else {
                        failed(result.message);
                    }

                },

            });
        }
    }
</script>
<?php
$this->load->view('base/footer/index');
?>