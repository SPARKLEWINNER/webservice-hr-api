<div class="modal " id="mediaModal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header no-bd px-4">
                <h5 class="modal-title">
                    <span class="fw-mediumbold">
                        Media Library</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-pills nav-secondary px-2" id="pills-tab" role="tablist">
                    <li class="nav-item submenu">
                        <a class="nav-link active show" id="pills-upload-tab" data-toggle="pill" href="#pills-upload" role="tab" aria-controls="pills-upload" aria-selected="false">Upload Files</a>
                    </li>
                    <li class="nav-item submenu">
                        <a class="nav-link" id="pills-library-tab" data-toggle="pill" href="#pills-library" role="tab" aria-controls="pills-library" aria-selected="true" onClick="requestMedia()">Media Library</a>
                    </li>
                </ul>
                <div class="tab-content mt-2" id="pills-tabContent">
                    <?php
                    $this->load->view('base/media/upload/index');
                    $this->load->view('base/media/library/index');
                    ?>

                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="modalFetureInput" data-src="" />
                <button type="button" class="btn btn-primary" id="featured-btn" disabled>Set featured image</button>
            </div>
        </div>
    </div>
</div>

<script>
    let base_url = "<?php echo base_url(); ?>";
    var layout = $("#media-container");
    var checkPut = $("#imageInput");
    var thumbnail = $("#thumbnail");
    var removeThumbnail = $("#remove-thumbnail");
    var featuredBtn = $("#featured-btn");
    var featuredModal = $("#modalFetureInput");

    $("input[type='radio']").click(function() {
        var previousValue = $(this).attr('previousValue');
        var name = $(this).attr('name');
        if (previousValue == 'checked') {
            $(this).prop("checked", false);
            $(this).attr('previousValue', false);
        } else {
            $("input[name=" + name + "]:radio").attr('previousValue', false);
            $(this).attr('previousValue', 'checked');
        }
    });



    function request(i) {

        if (i != '' || i != undefined) {
            $.get({
                url: base_url + "client/media/request/image/" + i,
                dataType: "json",
                success: function(result) {
                    if (result.data) {
                        showDetails(result.data[0]);
                    }
                },

            });
        }
    }

    function requestMedia() {
        layout.empty();
        $.get({
            url: base_url + "client/media/request/image",
            dataType: "json",
            success: function(result) {
                if (result.data) {
                    $.each(result.data, function(i, v) {
                        imageLayout(v);
                    });
                }
            },
        });
    }


    function requestRemove(id) {

        if (id != '' || id != undefined) {
            $.post({
                url: "<?= base_url(); ?>client/media/remove/media",
                data: {
                    'bulk_action': 'delete_permanent',
                    'id': id
                },
                dataType: "json",
                success: function(result) {
                    if (result.request) {
                        requestMedia();
                    }
                },

            });
        }

    }

    function imageLayout(d) {
        if (d) {
            layout.append(
                '<label class="imagecheck" id="mediaThumbnailInput" onClick="request(' + d.id + ')">' +
                '<input name="imagecheck" type = "radio" value="' + d.id + '" data-id="' + d.id + '" class = "imagecheck-input radio">' +
                '<figure class = "imagecheck-figure rounded-0" >' +
                '<img src = "' + d.link + '" alt="title" class="imagecheck-image" draggable = "false" >' +
                '</figure>' +
                '</label>'
            )
        }
    }

    function showDetails(d) {
        var container = $('#headerContainer');
        var img = $("#imgThumbnail");
        var title = $("#imgTitle");
        var date = $("#imgUploadDate");
        var size = $("#imgSize");
        var resolution = $("#imgResolution");
        var action = $("#imgActionDelete");


        img.empty();
        title.empty();
        date.empty();
        size.empty();
        resolution.empty();

        container.removeClass("d-none");
        featuredModal.val(d.id).data('src', d.link);
        img.attr('src', d.link);
        title.text(d.orig_name);
        size.text(d.file_size);
        date.text(new Date(Date.parse(d.created_at)).toDateString());
        action.attr('data-id', d.id);
        action.attr('onClick', 'requestRemove(' + d.id + ')');

        featuredBtn.removeAttr('disabled');
    }

    $('a[data-target="#mediaModal"]').on("click", function(e) {
        e.preventDefault();
        featuredModal.val('').data('src', '');
        requestMedia();
    });


    function removeImage() {
        let url = "<?= $this->uri->segment(3); ?>";
        thumbnail.attr('src', base_url + 'assets/placeholders/placeholder.png');
        removeThumbnail.addClass("d-none");
        checkPut.val('');
        if (url != "edit") {
            thumbnail.addClass("d-block");
        }
    }

    featuredBtn.click(function(e) {
        e.preventDefault();
        removeImage();
        checkPut.val(featuredModal.val());
        thumbnail.attr('src', featuredModal.data('src')).removeClass("d-none");
        removeThumbnail.removeClass("d-none");
        $("#mediaModal").modal('hide');
    });


    Dropzone.options.uploadMedia = {
        maxFilesize: 4,
        acceptedFiles: 'image/*',
    }
</script>