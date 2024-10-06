@extends('layouts.master')
@section('title')
    Invoices
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('modal')
    @include('pages.admins.payments.invoices.form-modal.create')
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
            <div class="page-separator__text">Invoices</div>
            <button type="button" class="btn btn-sm btn-primary ml-auto " id="addInvoice">
                    <span class="material-icons mr-2">
                        add
                    </span>
                Add New
            </button>
        </div>
        <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="invoicesTable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Inovice Number</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Amount Due</th>
                            <th>Due Date</th>
                            <th>Status</th>
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

            $('#invoicesTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! url()->current() !!}',
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'invoiceNumber', name: 'invoiceNumber'},
                    {data: 'name', name: 'name'},
                    {data: 'email', name: 'email'},
                    {data: 'amountDue', name: 'amountDue'},
                    {data: 'dueDate', name: 'dueDate'},
                    {data: 'status', name: 'status'},
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

            // show add product form modal when add new product button clicked
            $('#addInvoice').on('click', function (e) {
                e.preventDefault();
                $('#addInvoiceModal').modal('show');
            });

            let i = 1;
            $('#addProduct').click(function(){
                i++;
                $('#productsField').append(
                    '<div class="row" id="row'+i+'">'+
                        '<div class="col-auto d-flex align-items-center">' +
                            '<label class="form-label"># '+i+'</label>' +
                        '</div>' +
                        '<div class="form-group col-7 col-lg-4">' +
                            '<label class="form-label" for="productId'+i+'">Product</label>' +
                            '<small class="text-danger">*</small>'+
                            '<select class="form-control form-select product-select" data-row="'+i+'" id="productId'+i+'" name="products['+i+'][productId]" required>'+
                                '<option disabled selected>Select product</option>'+
                                '@foreach($products AS $product)'+
                                '<option value="{{ $product->id }}">'+
                                    '{{ $product->productName }} ~ {{ $product->priceOption }}'+
                                '</option>'+
                                '@endforeach'+
                            '</select>'+
                            '<span class="invalid-feedback productId'+i+'" role="alert">'+
                                '<strong></strong>'+
                            '</span>'+
                        '</div>'+
                        '<div class="form-group col-3 col-lg-1">' +
                            '<label class="form-label" for="qty'+i+'">Qty</label>' +
                            '<small class="text-danger">*</small>'+
                            '<input type="number" id="qty'+i+'" name="products['+i+'][qty]" required class="form-control qty-form" placeholder="Input product qty" data-row="'+i+'">'+
                            '<span class="invalid-feedback qty'+i+'" role="alert">'+
                                '<strong></strong>'+
                            '</span>'+
                        '</div>'+
                        '<div class="form-group col-6 col-lg-3">' +
                            '<label class="form-label" for="price'+i+'">Price</label>' +
                            '<small class="text-danger">*</small>'+
                            '<div class="input-group input-group-merge">'+
                                '<div class="input-group-prepend">'+
                                    '<div class="input-group-text">Rp.</div>'+
                                '</div>'+
                                '<input type="number" id="price'+i+'" name="products['+i+'][price]" required class="form-control" disabled>'+
                                '<span class="invalid-feedback price'+i+'" role="alert">'+
                                    '<strong></strong>'+
                                '</span>'+
                                '<div class="input-group-append">'+
                                    '<div class="input-group-text" id="subscription-info'+i+'"></div>'+
                                '</div>'+
                            '</div>'+
                        '</div>'+
                        '<div class="form-group col-4 col-lg-2">' +
                            '<label class="form-label" for="ammount'+i+'">Total</label>' +
                            '<small class="text-danger">*</small>'+
                            '<div class="input-group input-group-merge">'+
                                '<div class="input-group-prepend">'+
                                    '<div class="input-group-text">Rp.</div>'+
                                '</div>'+
                                '<input type="number" id="amount'+i+'" name="products['+i+'][ammount]" required class="form-control" disabled>'+
                                '<span class="invalid-feedback price'+i+'" role="alert">'+
                                    '<strong></strong>'+
                                '</span>'+
                            '</div>'+
                        '</div>'+
                        '<div class="d-flex align-items-center col-1">' +
                            '<button type="button" id="'+i+'" class="btn btn-sm btn-danger btnRemoveProduct">'+
                                '<span class="material-icons">close</span>'+
                            '</button>'+
                        '</div>'+
                    '</div>'
                );
            });

            body.on('click', '.btnRemoveProduct', function(){
                const button_id = $(this).attr("id");
                $('#row'+button_id+'').remove();
                i -= 1;
            });

            // calculate and show product amount when qty form inputed
            body.on('change', '.qty-form',function() {
                // Capture user input for query parameters
                const rowId = $(this).attr('data-row');
                const qty = $(this).val();
                const productId = $('#productId'+rowId).val();

                // Send an AJAX request with query parameters
                $.ajax({
                    url: '{{ route('invoices.calculate-product-amount') }}',  // URL endpoint
                    type: 'GET',
                    data: {
                        qty: qty,   // Add query parameters here
                        productId: productId
                    },
                    success: function(response) {
                        // Process the response
                        console.log(response);
                        $('#price'+rowId).val(response.data.productPrice);
                        $('#amount'+rowId).val(response.data.amount);
                        $('#subscription-info'+rowId).text(response.data.subscription);
                    },
                    error: function(xhr, status, error) {
                        // Handle any errors
                        alert('Error:', error);
                    }
                });
            });

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

            // show edit product category form modal when edit product button clicked
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

            // update product category data when form submitted
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

            // delete product category data
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

            const formEditTax = '#formEditTaxModal';
            const editTaxModalId = '#editTaxModal';

            // show edit product form modal when edit product button clicked
            body.on('click', '.editTax', function () {
                const id = $(this).attr('id');
                $.ajax({
                    url: "{{ route('taxes.edit', ':id') }}".replace(':id', id),
                    type: 'GET',
                    success: function (res) {
                        $(editTaxModalId).modal('show');

                        document.getElementById('tax-title').textContent = 'Edit tax ' + res.data.taxName;
                        $('#taxId').val(res.data.id);
                        $(formEditTax + ' #taxName').val(res.data.taxName);
                        $(formEditTax + ' #percentage').val(res.data.percentage);
                        $(formEditTax + ' #description').val(res.data.description);
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
            $(formEditTax).on('submit', function (e) {
                e.preventDefault();
                const id = $('#taxId').val()
                $.ajax({
                    url: "{{ route('taxes.update', ':id') }}".replace(':id', id),
                    type: $(this).attr('method'),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    success: function () {
                        $(editTaxModalId).modal('hide');
                        Swal.fire({
                            title: 'Tax successfully updated!',
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
                            $(formEditTax + ' span.' + key).text(val[0]);
                            $(formEditTax + " #" + key).addClass('is-invalid');
                        });
                        Swal.fire({
                            icon: "error",
                            title: "Something went wrong when updating data!",
                            text: errorThrown,
                        });
                    }
                });
            });

            // delete product category data
            body.on('click', '.deleteTax', function () {
                let id = $(this).attr('id');

                Swal.fire({
                    title: "Are you sure to delete this tax?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#1ac2a1",
                    cancelButtonColor: "#E52534",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('taxes.destroy', ':id') }}".replace(':id', id),
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function () {
                                Swal.fire({
                                    title: 'Tax successfully deleted!',
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
        });
    </script>
@endpush
