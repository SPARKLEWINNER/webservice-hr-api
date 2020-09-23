<div class=" w-75">
    <div class="card-header bg-light border-0 px-3 rounded">
        <div class="d-flex align-items-center">
            <h4 class="card-title">Edit category</h4>
        </div>
    </div>
    <form class="form" id="categoryForm" onSubmit="requests(event)" autocomplete="on">
        <input type="hidden" name="id" value="<?= $edit[0]['id']; ?>" />
        <div class="form-group form-inline">
            <label for="categoryName" class="col-md-3 justify-content-center">Name</label>
            <input type="text" class="form-control col-md-9" name="category_name" id="categoryName" placeholder="Enter Product name" value="<?= $edit[0]['name']; ?>" required>
        </div>
        <div class=" form-group form-inline">
            <label for="categorySlug" class="col-md-3 justify-content-center">Slug</label>
            <input type="text" class="form-control col-md-9" name="category_slug" id="categorySlug" placeholder="Enter Product slug" value="<?= $edit[0]['slug']; ?>" required>
        </div>
        <div class="form-group form-inline">
            <label for="categoryDescription" class="col-md-3 justify-content-center">Description</label>
            <textarea name="category_description" class="form-control col-md-9" id=" categoryDescription" rows="20" cols="80"><?= $edit[0]['description']; ?></textarea>
        </div>
        <div class="form-group form-inline align-items-center border border-light">
            <input type="hidden" name="image" id="imageInput" value="<?= $edit[0]['thumbnail']; ?>" />
            <label for="" class=" col-md-3">Product image</label>
            <div class="col-md-9 pl-0 pr-0">
                <?php if (!isset($edit[0]['link'])) : ?>
                    <img src="<?= base_url(); ?>assets/placeholders/placeholder.png" class="featured-thumbnail edit" id="thumbnail" />
                <?php else : ?>
                    <img src="<?= $edit[0]['link']; ?>" class="featured-thumbnail edit" id="thumbnail" />
                <?php endif; ?>
                <div class="d-flex align-items-center mt-2" id="img-call">
                    <a href="#" class="px-2 border-1 btn  btn-primary btn-border" data-toggle="modal" data-target="#mediaModal">
                        <div class="px-2 text-white text-center text-primary">
                            Change image
                        </div>
                    </a>
                    <?php if (isset($edit[0]['link'])) : ?>
                        <a href="#" class="px-2 border-1 btn  btn-primary btn-border m-1" id="remove-thumbnail" onClick="removeImage()">
                            <div class="px-2 text-white text-center text-primary">
                                Remove image
                            </div>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="form-group text-right">
            <button type="button" class="btn bg-white text-danger border-0 border-danger rounded" onClick="moveTrash(<?= $edit[0]['id']; ?>)">Delete</button>
            <button type="submit" class="btn btn-primary rounded mr-2">Update</button>
        </div>
    </form>
</div>