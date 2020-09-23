    <div class="card w-100">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <h4 class="card-title">Products details</h4>
            </div>
        </div>
        <div class="card-body">
            <form class="form" id="loginForm" onSubmit="request(event)" autocomplete="on">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="productName">Product name</label>
                            <input type="text" class="form-control" name="product_name" id="productName" placeholder="Enter Product name" required>
                        </div>
                        <div class="form-group">
                            <label for="productDescription">Product description</label>
                            <textarea name="product_description" class="form-control" id=" productDescription" rows="10" cols="80"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="productPrice">Product price</label>
                            <input type="number" class="form-control" name="product_price" id="productPrice" placeholder="Enter Product price" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Category</label>
                            <select class="form-control" name="product_category" id="productCategory" autocomplete="off">

                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Publish</label>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <button type="button" class="btn btn-primary btn-border">Save as Draft</button>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <button type="button" class="btn btn-primary btn-border w-60">Preview</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="d-flex align-items-center justify-content-start">
                                        <i class="icon-energy"></i>
                                        <p class="ml-2 mb-0">Status: <span class="font-weight-bold">Draft</span></p>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-start">
                                        <i class="icon-calendar"></i>
                                        <p class="ml-2 mb-0">Published on: <span class="font-weight-bold">Oct 8, 2019 at 08:26</span></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex justify-content-start align-items-center">
                                <div class="form-group">
                                    <a href="#" class="pl-2 bg-white border-0 text-danger"><i class="icon-trash pr-2"></i>Move to Trash</a>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <button type="button" class="btn btn-info d-block border-0 rounded w-100">Publish</button>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group border border-light">
                                    <label for="">Product image</label>
                                    <a href="#" class="px-2 border-0 text-primary">
                                        <div class="py-5 bg-light text-center">
                                            <i class="flaticon-photo-camera pr-2"></i>Set product image
                                        </div>
                                    </a>
                                </div>
                                <div class="my-5 form-group border border-light">
                                    <label for="">Product gallery</label>
                                    <a href="#" class="px-2 border-0 text-primary">
                                        <div class="py-5 bg-light text-center">
                                            <i class="flaticon-picture pr-2"></i>Add product gallery image
                                        </div>
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>