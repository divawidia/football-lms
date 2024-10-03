@extends('layouts.master')
@section('title')
    Products
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('modal')
    @include('pages.admins.payments.products.form-modal.products.create')
    @include('pages.admins.payments.products.form-modal.product-categories.create')
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
            const productsTable = $('#productsTable').DataTable({
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

            const taxTable = $('#taxTable').DataTable({
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
            let subscriptionCycleSelect = $('#subscriptionCycle');

            $('#addProducts').on('click', function (e) {
                e.preventDefault();
                $('#addProductModal').modal('show');
                subscriptionCycleForm.hide();
            });

            $('#paymentOption').on('change', function (e) {
                e.preventDefault();
                if ($(this).val() === 'subscription'){
                    subscriptionCycleForm.show()
                    subscriptionCycleSelect.attr('required');
                }else {
                    subscriptionCycleForm.hide();
                    subscriptionCycleSelect.val("Select product's subscription cycle").removeAttr('required');
                }
            });

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
                            $('span.' + key).text(val[0]);
                            $("#add-" + key).addClass('is-invalid');
                        });
                        Swal.fire({
                            icon: "error",
                            title: "Something went wrong when creating data!",
                            text: errorThrown,
                        });
                    }
                });
            });

            $('.addProductCategory').on('click', function (e) {
                e.preventDefault();
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
                            $('span.' + key).text(val[0]);
                            $("#add-" + key).addClass('is-invalid');
                        });
                        Swal.fire({
                            icon: "error",
                            title: "Something went wrong when creating data!",
                            text: errorThrown,
                        });
                    }
                });
            });


            $('body').on('click', '.delete-team', function () {
                let id = $(this).attr('id');

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#1ac2a1",
                    cancelButtonColor: "#E52534",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('team-managements.destroy', ['team' => ':id']) }}".replace(':id', id),
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function (response) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Team successfully deleted!",
                                });
                                datatable.ajax.reload();
                            },
                            error: function (error) {
                                Swal.fire({
                                    icon: "error",
                                    title: "Oops...",
                                    text: "Something went wrong when deleting data!",
                                });
                            }
                        });
                    }
                });
            });

            $('body').on('click', '.delete-opponentTeam', function () {
                let id = $(this).attr('id');

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#1ac2a1",
                    cancelButtonColor: "#E52534",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('opponentTeam-managements.destroy', ['team' => ':id']) }}".replace(':id', id),
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function (response) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Team successfully deleted!",
                                });
                                opponentTable.ajax.reload();
                            },
                            error: function (error) {
                                Swal.fire({
                                    icon: "error",
                                    title: "Oops...",
                                    text: "Something went wrong when deleting data!",
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
