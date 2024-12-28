<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mt-4">
    <div class="row">
        <div class="col-12">
            <div class="form-heading">
                <h4>Create Purchase Items</h4>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-heading">
                                <h4>Purchase Item</h4>
                            </div>
                            <div class="staff-search-table">

                                <div class="row">

                                    <div class="col-12 col-md-6 col-xl-5">
                                        <div class="input-block local-forms">
                                            <label>Select Product</label>
                                            <select class="form-control select2 product" name="product" id="product">
                                                <option value="">Select Product</option>
                                                @foreach ($products as $item)
                                                    <option value="{{ $item->id }}">{{ Str::limit($item->name,30) }}</option>
                                                @endforeach
                                            </select>

                                            <small class="text-danger font-weight-bold err_product"></small>
                                            <small class="text-danger err_product_ids"></small>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6 col-xl-5">
                                        <div class="input-block local-forms ">
                                            <label>Qty</label>
                                            <input type="text" name="qty" class="form-control qty"
                                                value="{{ old('qty') }}" id="qty" placeholder="Enter the Qty">
                                            <small class="text-danger font-weight-bold err_qty"></small>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6 col-xl-2">
                                        <button class="btn btn-lg btn-secondary text-uppercase btn-bottom"
                                            type="button" id="add_button">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4" id="purchaseItem_info_table" style="display: none">
            <div class="table-responsive">
                <table class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary">Product</th>
                            <th class="text-uppercase text-secondary">Category</th>
                            <th class="text-uppercase text-secondary">Sub Category</th>
                            <th class="text-uppercase text-secondary">Unit</th>
                            <th class="text-uppercase text-secondary">Qty</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="input_field_table">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
