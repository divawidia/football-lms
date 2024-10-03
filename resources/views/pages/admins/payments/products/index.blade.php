@extends('layouts.master')
@section('title')
    Products
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('modal')
    @include('pages.admins.payments.products.form-modal.products.create')
    @include('pages.admins.payments.products.form-modal.products.edit')
    @include('pages.admins.payments.products.form-modal.product-categories.create')
    @include('pages.admins.payments.products.form-modal.product-categories.edit')
    @include('pages.admins.payments.products.form-modal.taxes.create')
    @include('pages.admins.payments.products.form-modal.taxes.edit')
@endsection

@section('content')
    <div class="pt-32pt">
        <div class="container page__container d-flex flex-column">
            <h2 class="mb-0 text-left">@yield('title')</h2>
            <ol class="breadcrumb p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">
                    @yield('title')
                </li>
            </ol>
        </div>
    </div>

    <div class="container page__container page-section">
        <div class="page-separator">
            <div class="page-separator__text">Products</div>
            <button type="button" class="btn btn-sm btn-primary ml-auto " id="addProducts">
                    <span class="material-icons mr-2">
                        add
                    </span>
                Add New
            </button>
        </div>
        <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="productsTable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>description</th>
                            <th>Price</th>
                            <th>Payment Option</th>
                            <th>Subscription Cycle</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Created At</th>
                            <th>Last Updated</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="page-separator">
            <div class="page-separator__text">Product Categories</div>
            <button type="button" class="btn btn-sm btn-primary ml-auto addProductCategory">
                    <span class="material-icons mr-2">
                        add
                    </span>
                Add New
            </button>
        </div>
        <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="productCategoriesTable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>description</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Created At</th>
                            <th>Last Updated</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="page-separator">
            <div class="page-separator__text">Taxes</div>
            <button type="button" class="btn btn-sm btn-primary ml-auto " id="addTax">
                    <span class="material-icons mr-2">
                        add
                    </span>
                Add New
            </button>
        </div>
        <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="taxTable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>description</th>
                            <th>percentage</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Created At</th>
                            <th>Last Updated</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('addon-script')
    <script>
        $(document).ready(function () {
            const body = $('body');

            $('#productsTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! url()->current() !!}',
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'productName', name: 'productName'},
                    {data: 'category.categoryName', name: 'category.categoryName'},
                    {data: 'description', name: 'description'},
                    {data: 'price', name: 'price'},
                    {data: 'priceOption', name: 'priceOption'},
                    {data: 'subscriptionCycle', name: 'subscriptionCycle'},
                    {data: 'status', name: 'status'},
                    {data: 'createdBy', name: 'createdBy'},
                    {data: 'createdAt', name: 'createdAt'},
                    {data: 'updatedAt', name: 'updatedAt'},
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    },
                ]
            });

            $('#productCategoriesTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! route('product-categories.index') !!}',
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'categoryName', name: 'categoryName'},
                    {data: 'description', name: 'description'},
                    {data: 'status', name: 'status'},
                    {data: 'createdBy', name: 'createdBy'},
                    {data: 'createdAt', name: 'createdAt'},
                    {data: 'updatedAt', name: 'updatedAt'},
                    {data: 'action', name: 'action', orderable: false, searchable: false, width: '50%'},
                ]
            });

            $('#taxTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! route('taxes.index') !!}',
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'taxName', name: 'taxName'},
                    {data: 'description', name: 'description'},
                    {data: 'percentage', name: 'percentage'},
                    {data: 'status', name: 'status'},
                    {data: 'createdBy', name: 'createdBy'},
                    {data: 'createdAt', name: 'createdAt'},
                    {data: 'updatedAt', name: 'updatedAt'},
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    },
                ]
            })

            let subscriptionCycleForm = $('.subscriptionCycleForm');
            let subscriptionCycleSelect = $('.subscriptionCycle');

            // show add product form modal when add new product button clicked
            $('#addProducts').on('click', function (e) {
                e.preventDefault();
                $('#addProductModal').modal('show');
                subscriptionCycleForm.hide();
            });

            function subscriptionCycleDisplay(formId){
                const priceOption = $(formId + ' .priceOption');
                if (priceOption.val() === 'subscription'){
                    subscriptionCycleForm.show()
                    subscriptionCycleSelect.attr('required');
                }else if (priceOption.val() === 'one time payment') {
                    subscriptionCycleForm.hide();
                    subscriptionCycleSelect.val("(NULL)").removeAttr('required');
                }
            }

            function priceOptionFormOnChange(formId) {
                const priceOption = $(formId + ' .priceOption');
                priceOption.on('change', function (e) {
                    e.preventDefault();
                    subscriptionCycleDisplay(formId);
                });
            }

            priceOptionFormOnChange('#formAddProductModal');
            priceOptionFormOnChange('#formEditProductModal');

            // store product data when form submitted
            $('#formAddProductModal').on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('products.store') }}",
                    type: $(this).attr('method'),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    success: function () {
                        $('#addProductModal').modal('hide');
                        Swal.fire({
                            title: 'Product successfully created!',
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonColor: "#1ac2a1",
                            confirmButtonText:
                                'Ok!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        const response = JSON.parse(jqXHR.responseText);
                        $.each(response.errors, function (key, val) {
                            $('#formAddProductModal span.' + key).text(val[0]);
                            $("#formAddProductModal #" + key).addClass('is-invalid');
                        });
                        Swal.fire({
                            icon: "error",
                            title: "Something went wrong when creating data!",
                            text: errorThrown,
                        });
                    }
                });
            });

            // show edit product form modal when edit product button clicked
            body.on('click', '.edit-product', function () {
                const id = $(this).attr('id');
                $.ajax({
                    url: "{{ route('products.edit', ':id') }}".replace(':id', id),
                    type: 'GET',
                    success: function (res) {
                        $('#editProductModal').modal('show');

                        document.getElementById('product-title').textContent = 'Edit product ' + res.data.productName;
                        $('#productId').val(res.data.id);
                        $('#formEditProductModal #productName').val(res.data.productName);
                        $('#formEditProductModal #price').val(res.data.price);
                        $('#formEditProductModal #categoryId').val(res.data.categoryId);
                        $('#formEditProductModal #description').val(res.data.description);
                        $('#formEditProductModal #priceOption').val(res.data.priceOption);
                        $('#formEditProductModal #subscriptionCycle').val(res.data.subscriptionCycle);
                        subscriptionCycleDisplay('#formEditProductModal');
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        Swal.fire({
                            icon: "error",
                            title: "Something went wrong when updating data!",
                            text: errorThrown,
                        });
                    }
                });
            });

            // update product data when form submitted
            $('#formEditProductModal').on('submit', function (e) {
                e.preventDefault();
                const id = $('#productId').val()
                $.ajax({
                    url: "{{ route('products.update', ':id') }}".replace(':id', id),
                    type: $(this).attr('method'),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    success: function () {
                        $('#editTrainingVideoModal').modal('hide');
                        Swal.fire({
                            title: 'Product successfully updated!',
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonColor: "#1ac2a1",
                            confirmButtonText:
                                'Ok!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        const response = JSON.parse(jqXHR.responseText);
                        console.log(response)
                        $.each(response.errors, function (key, val) {
                            $('#formEditProductModal span.' + key).text(val[0]);
                            $("#formEditProductModal #" + key).addClass('is-invalid');
                        });
                        Swal.fire({
                            icon: "error",
                            title: "Something went wrong when updating data!",
                            text: errorThrown,
                        });
                    }
                });
            });

            // delete product data
            body.on('click', '.delete-product', function () {
                let id = $(this).attr('id');

                Swal.fire({
                    title: "Are you sure to delete this product?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#1ac2a1",
                    cancelButtonColor: "#E52534",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('products.destroy', ['product' => ':id']) }}".replace(':id', id),
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function (response) {
                                Swal.fire({
                                    title: 'Product successfully deleted!',
                                    icon: 'success',
                                    showCancelButton: false,
                                    confirmButtonColor: "#1ac2a1",
                                    confirmButtonText:
                                        'Ok!'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
                                    }
                                });
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                Swal.fire({
                                    icon: "error",
                                    title: "Something went wrong when deleting data!",
                                    text: errorThrown
                                });
                            }
                        });
                    }
                });
            });

            $('.addProductCategory').on('click', function () {
                // e.preventDefault();
                $('#addProductModal').modal('hide');
                $('#addProductCategoryModal').modal('show');
            });

            $('#formAddProductCategoryModal').on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('product-categories.store') }}",
                    type: $(this).attr('method'),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    success: function () {
                        $('#addProductModal').modal('hide');
                        Swal.fire({
                            title: 'Product category successfully created!',
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonColor: "#1ac2a1",
                            confirmButtonText:
                                'Ok!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        const response = JSON.parse(jqXHR.responseText);
                        $.each(response.errors, function (key, val) {
                            $('#formAddProductCategoryModal span.' + key).text(val[0]);
                            $("#formAddProductCategoryModal #" + key).addClass('is-invalid');
                        });
                        Swal.fire({
                            icon: "error",
                            title: "Something went wrong when creating data!",
                            text: errorThrown,
                        });
                    }
                });
            });

            const formEditProductCategory = '#formEditProductCategoryModal';
            const editProductCategoryModalId = '#editProductCategoryModal';

            // show edit product form modal when edit product button clicked
            body.on('click', '.editProductCategory', function () {
                const id = $(this).attr('id');
                $.ajax({
                    url: "{{ route('product-categories.edit', ':id') }}".replace(':id', id),
                    type: 'GET',
                    success: function (res) {
                        $(editProductCategoryModalId).modal('show');

                        document.getElementById('product-category-title').textContent = 'Edit product category ' + res.data.productName;
                        $('#productCategoryId').val(res.data.id);
                        $(formEditProductCategory + ' #categoryName').val(res.data.categoryName);
                        $(formEditProductCategory + ' #description').val(res.data.description);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        Swal.fire({
                            icon: "error",
                            title: "Something went wrong when updating data!",
                            text: errorThrown,
                        });
                    }
                });
            });

            // update product data when form submitted
            $(formEditProductCategory).on('submit', function (e) {
                e.preventDefault();
                const id = $('#productCategoryId').val()
                $.ajax({
                    url: "{{ route('product-categories.update', ':id') }}".replace(':id', id),
                    type: $(this).attr('method'),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    success: function () {
                        $(editProductCategoryModalId).modal('hide');
                        Swal.fire({
                            title: 'Product category successfully updated!',
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonColor: "#1ac2a1",
                            confirmButtonText:
                                'Ok!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        const response = JSON.parse(jqXHR.responseText);
                        console.log(response)
                        $.each(response.errors, function (key, val) {
                            $(formEditProductCategory + ' span.' + key).text(val[0]);
                            $(formEditProductCategory + " #" + key).addClass('is-invalid');
                        });
                        Swal.fire({
                            icon: "error",
                            title: "Something went wrong when updating data!",
                            text: errorThrown,
                        });
                    }
                });
            });

            // delete product data
            body.on('click', '.deleteProductCategory', function () {
                let id = $(this).attr('id');

                Swal.fire({
                    title: "Are you sure to delete this product category?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#1ac2a1",
                    cancelButtonColor: "#E52534",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('product-categories.destroy', ':id') }}".replace(':id', id),
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function () {
                                Swal.fire({
                                    title: 'Product category successfully deleted!',
                                    icon: 'success',
                                    showCancelButton: false,
                                    confirmButtonColor: "#1ac2a1",
                                    confirmButtonText:
                                        'Ok!'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
                                    }
                                });
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                Swal.fire({
                                    icon: "error",
                                    title: "Something went wrong when deleting data!",
                                    text: errorThrown
                                });
                            }
                        });
                    }
                });
            });

            const formAddTax = '#formAddTaxModal';
            const addTaxModal = '#addTaxModal';
            $('#addTax').on('click', function () {
                // e.preventDefault();
                $(addTaxModal).modal('show');
            });

            $(formAddTax).on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('taxes.store') }}",
                    type: $(this).attr('method'),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    success: function () {
                        $(addTaxModal).modal('hide');
                        Swal.fire({
                            title: 'Tax successfully created!',
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonColor: "#1ac2a1",
                            confirmButtonText:
                                'Ok!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        const response = JSON.parse(jqXHR.responseText);
                        $.each(response.errors, function (key, val) {
                            $(formAddTax + ' span.' + key).text(val[0]);
                            $(formAddTax + " #" + key).addClass('is-invalid');
                        });
                        Swal.fire({
                            icon: "error",
                            title: "Something went wrong when creating data!",
                            text: errorThrown,
                        });
                    }
                });
            });
        });
    </script>
@endpush
