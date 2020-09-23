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
                        <div class="ml-md-auto py-2 py-md-0">
                            <a href="<?= base_url(); ?>store/categories" class="text-white border-0 mr-4">
                                <i class="flaticon-left-arrow-1 mr-2"></i>
                                Return to Categories dashboard </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="page-inner mt--5">
                <div class="row mt--2">
                    <div class="col-md-4">
                        <div class="card card-stats card-round card-nav">
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
                                                <h4 class="card-title">Add New product</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-stats card-round card-secondary">
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
                    <div class="col-md-12">
                        <?php $this->load->view('pages/store/categories/list/index'); ?>
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
    $(document).ready(function() {
        var table = "";
        table = $('#dataTable').DataTable({
            'ajax': '<?= base_url(); ?>store/request/categories/1',
            'columnDefs': [{
                    'targets': [0],
                    'searchable': false,
                    'orderable': false,
                    'className': 'dt-body-center',
                    'render': function(data, type, full, meta) {
                        return '<input type="checkbox" name="id[]" value="' +
                            $('<div/>').text(data).html() + '">';

                    }
                },
                {
                    'targets': [1],
                    'searchable': false,
                    'orderable': false,
                    'className': 'dt-body-center',
                    'render': function(data, type, full, meta) {
                        return '<div class="list-action">' +
                            '<strong class="mb-0"><a class="text-default font-bold" href="<?= base_url(); ?>store/categories/edit/' + full[0] + '">' + data + ' </a></strong>' +
                            '<div class="actions">' +
                            '<button type="button"class="btn bg-none text-primary action-text border-0 px-0 py-0" data-action="restore" onClick="moveTrash(' + full[0] + ');">Restore </button> | ' +
                            '<button type="button" class="btn bg-none text-danger action-text border-0 py-0 px-0" data-action="delete_permanently" data-id="' + full[0] + '" id="delete">Delete</button>' +
                            '</div>';
                        '</div>';
                    }
                }, {
                    'targets': [3],
                    'searchable': false,
                    'orderable': false,
                    'className': 'dt-body-center',
                    'render': function(data, type, full, meta) {
                        if (full[3] == null || full[3] == undefined) {
                            full[3] = '<?= base_url(); ?>assets/placeholders/placeholder.png';
                        }
                        return '<img src="' + full[3] + '" height="48" width="48" draggable="false"/>';
                    }
                }
            ],
            'order': [1, 'asc']
        });

        $("#selectAll").on('click', function() {
            var rows = table.rows({
                'search': 'applied'
            }).nodes();
            $('input[type="checkbox"]', rows).prop('checked', this.checked);
        });

        $("#listForm").on('submit', function(e) {
            e.preventDefault();
            var form = this;

            table.$('input[type="checkbox"]').each(function() {
                if (!$.contains(document, this)) {
                    if (this.checked) {
                        $(form).append(
                            $('<input>')
                            .attr('type', 'hidden')
                            .attr('name', this.name)
                            .val(this.value)
                        );
                    }
                }
            });

            $.post({
                url: "<?= base_url(); ?>client/store/remove/category",
                data: $(form).serialize(),
                dataType: "json",
                success: function(result) {
                    if (result.request) {
                        success(result.message);
                    } else {
                        failed(result.message);
                    }

                },

            });
        });

    });
</script>

<?php
$this->load->view('base/footer/index');
?>