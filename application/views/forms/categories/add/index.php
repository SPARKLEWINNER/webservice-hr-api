<div class=" w-100">
    <div class="card-header bg-light border-0 px-3 rounded">
        <div class="d-flex align-items-center">
            <h4 class="card-title">Add new Category</h4>
        </div>
    </div>
    <form class="form" id="categoryForm" onSubmit="requests(event)" autocomplete="on">
        <div class="form-group">
            <label for="categoryName">Name</label>
            <input type="text" class="form-control" name="category_name" id="categoryName" placeholder="Enter Product name" required>
        </div>
        <div class="form-group">
            <label for="categorySlug">Slug</label>
            <input type="text" class="form-control" name="category_slug" id="categorySlug" placeholder="Enter Product name" required>
        </div>
        <div class="form-group">
            <label for="categoryDescription">Description</label>
            <textarea name="category_description" class="form-control" id=" categoryDescription" rows="5" cols="80"></textarea>
        </div>
        <div class="form-group border border-light">
            <input type="hidden" name="image" id="imageInput" />
            <label for="">Product image</label>
            <img src="" class="featured-thumbnail d-none" id="thumbnail" />
            <div class="d-flex align-items-center" id="img-call">
                <a href="#" class="px-2 border-1 btn  btn-primary btn-border" data-toggle="modal" data-target="#mediaModal">
                    <div class="px-2 text-white text-center text-primary">
                        Upload / Add Image
                    </div>
                </a>
                <a href="#" class="px-2 border-1 btn  btn-primary btn-border m-1 d-none" id="remove-thumbnail" onClick="removeImage()">
                    <div class="px-2 text-white text-center text-primary">
                        Remove image
                    </div>
                </a>
            </div>
        </div>
        <div class="form-group text-right">
            <button type="submit" class="btn btn-info border-0 rounded">Save</button>
        </div>
    </form>
</div>