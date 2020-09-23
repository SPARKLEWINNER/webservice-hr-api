<!--   Core JS Files   -->
<script src="<?= base_url(); ?>assets/js/core/jquery.3.2.1.min.js"></script>
<script src="<?= base_url(); ?>assets/js/core/popper.min.js"></script>
<script src="<?= base_url(); ?>assets/js/core/bootstrap.min.js"></script>

<!-- jQuery UI -->
<script src="<?= base_url(); ?>assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
<script src="<?= base_url(); ?>assets/js/plugin/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js"></script>

<!-- jQuery Scrollbar -->
<script src="<?= base_url(); ?>assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

<!-- Chart JS -->
<script src="<?= base_url(); ?>assets/js/plugin/chart.js/chart.min.js"></script>

<!-- jQuery Sparkline -->
<script src="<?= base_url(); ?>assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

<!-- Chart Circle -->
<script src="<?= base_url(); ?>assets/js/plugin/chart-circle/circles.min.js"></script>

<!-- Datatables -->
<script src="<?= base_url(); ?>assets/js/plugin/datatables/datatables.min.js"></script>

<!-- Bootstrap Notify -->
<script src="<?= base_url(); ?>assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>

<!-- jQuery Vector Maps -->
<script src="<?= base_url(); ?>assets/js/plugin/jqvmap/jquery.vmap.min.js"></script>
<script src="<?= base_url(); ?>assets/js/plugin/jqvmap/maps/jquery.vmap.world.js"></script>

<!-- Sweet Alert -->
<script src="<?= base_url(); ?>assets/js/plugin/sweetalert/sweetalert.min.js"></script>

<script src="<?= base_url(); ?>assets/js/atlantis.min.js"></script>

<script src="<?= base_url(); ?>assets/js/plugin/ckeditor/ckeditor.js"></script>
<script src="<?= base_url(); ?>assets/js/plugin/dropzone/dropzone.min.js"></script>

<script>
    function success(msg) {
        swal(msg, {
            icon: "success",
            buttons: {
                confirm: {
                    className: 'btn btn-success'
                }
            },
        }).then(function() {
            window.location = '<?= base_url() . $this->uri->segment("1") . "/" . $this->uri->segment("2"); ?>'
        });

        setTimeout(function() {
            window.location = '<?= base_url() . $this->uri->segment("1") . "/" . $this->uri->segment("2"); ?>'
        }, 2000);

    }

    function failed(msg) {
        swal({
            type: 'error',
            title: 'Process Failed',
            text: msg,
        }).then(function() {
            location.reload();
        });

        setTimeout(function() {
            location.reload();
        }, 2000);
    }
</script>