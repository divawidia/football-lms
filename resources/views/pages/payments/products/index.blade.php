@extends('layouts.master')
@section('title')
    Football Academy Products
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('modal')
    <x-modal.products.create-product :categories="$categories"/>
    <x-modal.products.edit-product :categories="$categories"/>
    <x-modal.product-categories.create-product-category/>
    <x-modal.product-categories.edit-product-category/>
    <x-modal.taxes.create-tax/>
    <x-modal.taxes.edit-tax/>
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

    <div class="container page-section">
        <div class="card">
            <div class="nav-tabs-container">
                <ul class="nav nav-pills text-capitalize">
                    <x-tabs.item title="Products" link="products" :active="true"/>
                    <x-tabs.item title="Product Categories" link="products-categories"/>
                    <x-tabs.item title="taxes" link="taxes"/>
                </ul>
            </div>
        </div>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="products-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Products</div>
                    <x-buttons.basic-button icon="add" text="Add New Product" additionalClass="addProducts" color="primary" iconColor="" margin="ml-auto"/>
                </div>
                <div class="card">
                    <div class="card-body">
                        <x-table :headers="['#', 'Name', 'Category', 'Description', 'Price', 'Payment Option', 'Subscription Cycle', 'Status', 'Created By', 'Created At', 'Last Updated', 'Action']" tableId="productsTable"/>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade show" id="products-categories-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Product Categories</div>
                    <x-buttons.basic-button icon="add" text="Add New Product Category" additionalClass="addProductCategory" color="primary" iconColor="" margin="ml-auto"/>
                </div>
                <div class="card">
                    <div class="card-body">
                        <x-table :headers="['#', 'Name', 'Description', 'Status', 'Created By', 'Created At', 'Last Updated', 'Action']" tableId="productCategoriesTable"/>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade show" id="taxes-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Taxes</div>
                    <x-buttons.basic-button icon="add" text="Add New Tax" additionalClass="addTax" color="primary" iconColor="" margin="ml-auto"/>
                </div>
                <div class="card">
                    <div class="card-body">
                        <x-table :headers="['#', 'Name', 'Description', 'Percentage', 'Status', 'Created By', 'Created At', 'Last Updated', 'Action']" tableId="taxTable"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('addon-script')
    <script>
        $(document).ready(function () {
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
                    {data: 'action', name: 'action', orderable: false, searchable: false},
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
                    {data: 'action', name: 'action', orderable: false, searchable: false},
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
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            })

            // delete product data
            processWithConfirmation(
                '.deleteProduct',
                "{{ route('products.destroy', ':id') }}",
                null,
                'DELETE',
                "Are you sure to delete this product?",
                "Something went wrong when deleting product!",
                "{{ csrf_token() }}"
            );
            processWithConfirmation(
                '.setDeactivateProduct',
                "{{ route('products.deactivate', ':id') }}",
                null,
                'PATCH',
                "Are you sure to deactivate this product?",
                "Something went wrong when deactivating product!",
                "{{ csrf_token() }}"
            );
            processWithConfirmation(
                '.setActivateProduct',
                "{{ route('products.activate', ':id') }}",
                null,
                'PATCH',
                "Are you sure to activate this product?",
                "Something went wrong when activating product!",
                "{{ csrf_token() }}"
            );

            // delete product category data
            processWithConfirmation(
                '.deleteProductCategory',
                "{{ route('product-categories.destroy', ':id') }}",
                null,
                'DELETE',
                "Are you sure to delete this product category?",
                "Something went wrong when deleting product category!",
                "{{ csrf_token() }}"
            );
            processWithConfirmation(
                '.setDeactivateProductCategory',
                "{{ route('product-categories.deactivate', ':id') }}",
                null,
                'PATCH',
                "Are you sure to deactivate this product category?",
                "Something went wrong when deactivating product category!",
                "{{ csrf_token() }}"
            );
            processWithConfirmation(
                '.setActivateProductCategory',
                "{{ route('product-categories.activate', ':id') }}",
                null,
                'PATCH',
                "Are you sure to activate this product category?",
                "Something went wrong when activating product category!",
                "{{ csrf_token() }}"
            );


            // delete product category data
            processWithConfirmation(
                '.deleteTax',
                "{{ route('taxes.destroy', ':id') }}",
                null,
                'DELETE',
                "Are you sure to delete this tax?",
                "Something went wrong when deleting tax!",
                "{{ csrf_token() }}"
            );
            processWithConfirmation(
                '.setDeactivateTax',
                "{{ route('taxes.deactivate', ':id') }}",
                null,
                'PATCH',
                "Are you sure to deactivate this tax?",
                "Something went wrong when deactivating tax!",
                "{{ csrf_token() }}"
            );
            processWithConfirmation(
                '.setActivateTax',
                "{{ route('taxes.activate', ':id') }}",
                null,
                'PATCH',
                "Are you sure to activate this tax?",
                "Something went wrong when activating tax!",
                "{{ csrf_token() }}"
            );
        });
    </script>
@endpush
