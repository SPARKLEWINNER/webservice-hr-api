<form method="POST" id="listForm">

    <div class="card">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-end">
                <div class="form-group w-100">
                    <?php if ($this->uri->segment('3') == "trash") : ?>
                        <a href="<?= base_url(); ?>store/categories" class=" btn-link text-primary">Return to Published lists</a>
                    <?php else : ?>
                        <a href="<?= base_url(); ?>store/categories/trash" class=" btn-link text-danger"><i class="fas fa-trash-alt mr-2"></i>Trash</a>
                    <?php endif; ?>
                </div>
                <div class=" form-group p-0 <?= ($this->uri->segment('3') == "trash") ? "w-25" : "w-50"; ?>">
                    <select class=" form-control my-0" name="bulk_action" id="bulkAction" autocomplete="off">
                        <option disabled selected>Bulk Actions</option>
                        <?php if ($this->uri->segment('3') == "trash") : ?>
                            <option value="restore">Restore</option>
                            <option value="delete_permanent">Delete permanently</option>
                        <?php else : ?>
                            <option value="delete">Delete</option>
                        <?php endif; ?>
                    </select>
                </div>
                <button class="btn btn-primary btn-border ml-3 my-0" type="submit">
                    Apply
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <div id="add-row_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4">
                    <div class="row">
                        <div class="col-sm-12">
                            <table id="dataTable" class="display table table-hover dataTable" role="grid" aria-describedby="add-row_info">
                                <thead>
                                    <tr role="row">
                                        <th class="p-0">
                                            <input type="checkbox" name="select_all" id="selectAll">
                                        </th>
                                        <th class="sorting_asc" tabindex="0" aria-controls="add-row" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Name: activate to sort column descending" style="width: 213px;">Name</th>
                                        <th class="sorting" tabindex="0" aria-controls="add-row" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" style="width: 311px;">Slug</th>
                                        <th class="sorting_asc" tabindex="0" aria-controls="add-row" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Name: activate to sort column descending" style="width: 213px;">Thumbnail</th>
                                        <!-- <th style="width: 127px;" class="sorting" tabindex="0" aria-controls="add-row" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Count</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>