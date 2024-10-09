@extends('layouts.master')
@section('title')
    Invoices
@endsection
@section('page-title')
    @yield('title')
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
            <a href="{{ route('invoices.archived') }}" class="btn btn-sm btn-danger ml-auto ">
                <span class="material-icons mr-2 text-danger">
                    delete
                </span>
                Archived Invoice
            </a>
            <button type="button" class="btn btn-sm btn-primary ml-3 " id="addInvoice">
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
                    {data: 'ammount', name: 'ammount'},
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
            $('#addProduct').click(function () {
                i++;
                $('#productsField').append(
                    '<div class="row" id="row' + i + '">' +
                    '<div class="col-auto d-flex align-items-center">' +
                    '<label class="form-label"># ' + i + '</label>' +
                    '</div>' +
                    '<div class="form-group col-7 col-lg-4">' +
                    '<label class="form-label" for="productId' + i + '">Product</label>' +
                    '<small class="text-danger">*</small>' +
                    '<select class="form-control form-select product-select" data-row="' + i + '" id="productId' + i + '" name="products[' + i + '][productId]" required>' +
                    '<option disabled selected>Select product</option>' +
                    '@foreach($products AS $product)' +
                    '<option value="{{ $product->id }}">' +
                    '{{ $product->productName }} ~ {{ $product->priceOption }}' +
                    '</option>' +
                    '@endforeach' +
                    '</select>' +
                    '<span class="invalid-feedback productId' + i + '" role="alert">' +
                    '<strong></strong>' +
                    '</span>' +
                    '</div>' +
                    '<div class="form-group col-3 col-lg-1">' +
                    '<label class="form-label" for="qty' + i + '">Qty</label>' +
                    '<small class="text-danger">*</small>' +
                    '<input type="number" id="qty' + i + '" name="products[' + i + '][qty]" required class="form-control qty-form" placeholder="Input product qty" data-row="' + i + '">' +
                    '<span class="invalid-feedback qty' + i + '" role="alert">' +
                    '<strong></strong>' +
                    '</span>' +
                    '</div>' +
                    '<div class="form-group col-6 col-lg-3">' +
                    '<label class="form-label" for="price' + i + '">Price</label>' +
                    '<small class="text-danger">*</small>' +
                    '<div class="input-group input-group-merge">' +
                    '<div class="input-group-prepend">' +
                    '<div class="input-group-text">Rp.</div>' +
                    '</div>' +
                    '<input type="number" id="price' + i + '" name="products[' + i + '][price]" required class="form-control" readonly="true">' +
                    '<span class="invalid-feedback price' + i + '" role="alert">' +
                    '<strong></strong>' +
                    '</span>' +
                    '<div class="input-group-append">' +
                    '<div class="input-group-text" id="subscription-info' + i + '"></div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<div class="form-group col-4 col-lg-2">' +
                    '<label class="form-label" for="amount' + i + '">Total</label>' +
                    '<small class="text-danger">*</small>' +
                    '<div class="input-group input-group-merge">' +
                    '<div class="input-group-prepend">' +
                    '<div class="input-group-text">Rp.</div>' +
                    '</div>' +
                    '<input type="number" id="amount' + i + '" name="products[' + i + '][ammount]" required class="form-control" readonly="true">' +
                    '<span class="invalid-feedback ammount' + i + '" role="alert">' +
                    '<strong></strong>' +
                    '</span>' +
                    '</div>' +
                    '</div>' +
                    '<div class="d-flex align-items-center col-1">' +
                    '<button type="button" id="' + i + '" class="btn btn-sm btn-danger btnRemoveProduct">' +
                    '<span class="material-icons">close</span>' +
                    '</button>' +
                    '</div>' +
                    '</div>'
                );
            });

            body.on('click', '.btnRemoveProduct', function () {
                const button_id = $(this).attr("id");
                $('#row' + button_id + '').remove();
                i -= 1;
            });

            function getProductAmount(rowId) {
                // Capture user input for query parameters
                let qty = $('#qty' + rowId).val();
                if (qty === '') {
                    qty = 0;
                }
                const productId = $('#productId' + rowId).val();

                // Send an AJAX request with query parameters
                $.ajax({
                    url: '{{ route('invoices.calculate-product-amount') }}',  // URL endpoint
                    type: 'GET',
                    data: {
                        qty: qty,   // Add query parameters here
                        productId: productId
                    },
                    success: function (response) {
                        // Process the response
                        $('#price' + rowId).val(response.data.productPrice);
                        $('#amount' + rowId).val(response.data.amount);
                        $('#subscription-info' + rowId).text(response.data.subscription);
                    },
                    error: function (xhr, status, error) {
                        // Handle any errors
                        alert('Error:', error);
                    }
                });
            }

            // calculate and show product amount when qty form inputed
            body.on('change', '.qty-form', function () {
                const rowId = $(this).attr('data-row');
                getProductAmount(rowId);
            });

            body.on('change', '.product-select', function () {
                const rowId = $(this).attr('data-row');
                getProductAmount(rowId);
            });

            // store product data when form submitted
            $('#formAddInvoiceModal').on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('invoices.store') }}",
                    type: $(this).attr('method'),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    success: function () {
                        $('#addInvoiceModal').modal('hide');
                        Swal.fire({
                            title: 'Product successfully created!',
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonColor: "#1ac2a1",
                            confirmButtonText:
                                'Ok!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.href();
                            }
                        });
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        const response = JSON.parse(jqXHR.responseText);
                        $.each(response.errors, function (key, val) {
                            $('#formAddInvoiceModal span.' + key).text(val[0]);
                            $("#formAddInvoiceModal #" + key).addClass('is-invalid');
                        });
                        Swal.fire({
                            icon: "error",
                            title: "Something went wrong when creating data!",
                            text: errorThrown,
                        });
                    }
                });
            });

            // archive invoice
            body.on('click', '.deleteInvoice', function () {
                const id = $(this).attr('id');
                Swal.fire({
                    title: "Are you sure to archive this invoice?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#1ac2a1",
                    cancelButtonColor: "#E52534",
                    confirmButtonText: "Yes, archive it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('invoices.destroy', ['invoice' => ':id']) }}".replace(':id', id),
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function () {
                                Swal.fire({
                                    title: 'Invoice successfully archived!',
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
                                    title: "Something went wrong when archiving data!",
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
