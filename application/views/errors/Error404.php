<?php
defined('BASEPATH') or exit('No direct script access allowed');
$this->load->view('base/header/index');
?>
<!-- Page Content -->
<div class="page-container page-error">
    <div class="page-content">
        <!-- Page Inner -->
        <div class="page-inner">
            <div id="main-wrapper" class="container">
                <div class="row">
                    <div class="col-md-6 center">
                        <h1 class="error-page-logo">404</h1>
                        <p class="error-page-top-text">Oops.. Something went wrong..</p>
                        <p class="error-page-bottom-text">We can't seem to find the page you're looking for.</p>
                        <a href="<?= base_url(); ?>" class="btn btn-default m-b-xxs">Return Home</a>
                    </div>
                </div><!-- Row -->
            </div><!-- Main Wrapper -->
        </div><!-- /Page Inner -->
    </div><!-- /Page Content -->
</div>


<?php $this->load->view('base/scripts/index'); ?>