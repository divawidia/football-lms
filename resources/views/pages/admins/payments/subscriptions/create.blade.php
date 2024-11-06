@extends('layouts.master')
@section('title')
    Create New Subscription
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('content')
    <div class="pt-32pt">
        <div class="container">
            <h2 class="mb-0">
                @yield('title')
            </h2>
            <ol class="breadcrumb p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('subscriptions.index') }}">Subscriptions</a></li>
                <li class="breadcrumb-item active">
                    @yield('title')
                </li>
            </ol>
        </div>
    </div>

    <div class="container page-section" style="max-width: 1000px">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('subscriptions.store') }}">
                @csrf
                <div class="form-group">
                    <div class="d-flex flex-row align-items-center mb-2">
                        <label class="form-label" for="receiverUserId">Players Contact</label>
                        <small class="text-danger">*</small>
                    </div>
                    <select class="form-control form-select" id="receiverUserId" name="receiverUserId" required data-toggle="select">
                        <option disabled selected>Select player</option>
                        @foreach($contacts AS $contact)
                            <option value="{{ $contact->id }}" data-avatar-src="{{ Storage::url($contact->foto) }}">
                                {{ $contact->firstName }} {{ $contact->lastName }} ~ {{ $contact->email }}
                            </option>
                        @endforeach
                    </select>
                    @error('receiverUserId')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="page-separator">
                    <div class="page-separator__text">Subscriptions</div>
                </div>
                <div id="productsField">
                    <div class="row">
                        <div class="form-group col-lg-4">
                            <label class="form-label" for="productId">Product</label>
                            <small class="text-danger">*</small>
                            <select class="form-control form-select product-select" id="productId" name="productId" required>
{{--                                <option disabled selected>Select subscriptions product</option>--}}
{{--                                @foreach($products AS $product)--}}
{{--                                    <option value="{{ $product->id }}" @selected( old('products[1][productId]') )>--}}
{{--                                        {{ $product->productName }} ~ {{ $product->subscriptionCycle }} cycle--}}
{{--                                    </option>--}}
{{--                                @endforeach--}}
                            </select>
                            @error('productId]')
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-lg-4">
                            <label class="form-label" for="price1">Price</label>
                            <div class="input-group input-group-merge">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        Rp.
                                    </div>
                                </div>
                                <input type="number"
                                       id="price1"
                                       name="productPrice"
                                       class="form-control"
                                       required
                                       readonly="readonly">
                                @error('productPrice')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <div class="input-group-append">
                                    <div class="input-group-text" id="subscription-info1">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-lg-4">
                            <label class="form-label" for="taxId">Include Tax</label>
                            <small>(Optional)</small>
                            <select class="form-control form-select" id="taxId" name="taxId" required data-toggle="select">
                                <option disabled selected>Select tax</option>
                                @foreach($taxes AS $tax)
                                    <option value="{{ $tax->id }}" @selected(old('taxId'))>
                                        {{ $tax->taxName }} ~ {{ $tax->percentage }}%
                                    </option>
                                @endforeach
                            </select>
                            @error('taxId')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="page-separator"></div>
                <div class="d-flex justify-content-end">
                    <a class="btn btn-secondary mx-2" href="{{ url()->previous()}}">
                        <span class="material-icons mr-2">close</span>
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary"><span class="material-icons mr-2">add</span>
                        Submit
                    </button>
                </div>
            </form>
            </div>
        </div>
    </div>
@endsection

@push('addon-script')
    <script>
        $(document).ready(function (message) {
            const body = $('body');

            function getAvailablePlayerSubscriptionProduct(userId) {
                const productSelectId = $('#productId');

                // Send an AJAX request with query parameters
                $.ajax({
                    url: '{{ route('subscriptions.available-product') }}',  // URL endpoint
                    type: 'GET',
                    data: {
                        userId: userId,
                    },
                    success: function (response) {
                        productSelectId.empty();
                        productSelectId.html('<option disabled selected>Select subscriptions product</option>');
                        $.each(response.data, function (key, value) {
                            productSelectId.append('<option value="' + value.id + '">' + value.productName + ' ~ '+value.subscriptionCycle+'</option>');
                        });
                    },
                    error: function (xhr, status, error) {
                        // Handle any errors
                        alert('Error:', error);
                    }
                });
            }

            function getProductAmount(rowId){
                // Capture user input for query parameters
                let qty = 1;
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
                        console.log(response)
                        // Process the response
                        $('#price'+rowId).val(response.data.productPrice);
                        $('#subscription-info'+rowId).text(response.data.subscription);
                    },
                    error: function(xhr, status, error) {
                        // Handle any errors
                        alert('Error:', error);
                    }
                });
            }
            $('#receiverUserId').on('change',function() {
                const userId = $(this).val();
                getAvailablePlayerSubscriptionProduct(userId);
            });

            body.on('change', '.product-select',function() {
                const rowId = $(this).attr('data-row');
                getProductAmount(rowId);
            });

        })
    </script>
@endpush
