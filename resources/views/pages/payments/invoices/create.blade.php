@extends('layouts.master')
@section('title')
    Create New Invoice
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
                        <li class="breadcrumb-item"><a href="{{ route('invoices.index') }}">Invoices</a></li>
                        <li class="breadcrumb-item active">
                            @yield('title')
                        </li>
                    </ol>
        </div>
    </div>

    <div class="container page-section" style="max-width: 1200px">
        <div class="list-group">
            <form method="POST" action="{{ route('invoices.store') }}">
                @csrf
                <div class="list-group-item">
                    <div role="group" aria-labelledby="label-question" class="m-0 form-group">
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
                                <span class="invalid-feedback receiverUserId" role="alert">
                                    <strong></strong>
                                </span>
                            @enderror
                        </div>

                        <div class="page-separator">
                            <div class="page-separator__text">Product Items</div>
                            <button type="button"  class="btn btn-primary btn-sm ml-auto" id="addProduct">
                                <span class="material-icons mr-2">add</span>
                                Add more
                            </button>
                        </div>
                        <div id="productsField">
                            <div class="row">
                                <div class="col-auto d-flex align-items-center">
                                    <label class="form-label"># 1</label>
                                </div>
                                <div class="form-group col-7 col-lg-4">
                                    <label class="form-label" for="productId1">Product</label>
                                    <small class="text-danger">*</small>
                                    <select class="form-control form-select product-select" data-row="1" id="productId1" name="products[1][productId]" required>
                                        <option disabled selected>Select product</option>
                                        @foreach($products AS $product)
                                            <option value="{{ $product->id }}" @selected( old('products[1][productId]') )>
                                                {{ $product->productName }} ~ {{ $product->priceOption }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('products[1][productId]')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-3 col-lg-1">
                                    <label class="form-label" for="qty1">Qty</label>
                                    <small class="text-danger">*</small>
                                    <input type="number"
                                           id="qty1"
                                           data-row="1"
                                           name="products[1][qty]"
                                           required
                                           class="form-control qty-form"
                                           placeholder="Input product's qty ..."
                                            value="{{ old('products[1][qty]') }}">
                                    @error('products[1][qty]')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-6 col-lg-3">
                                    <label class="form-label" for="price1">Price</label>
                                    <div class="input-group input-group-merge">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                Rp.
                                            </div>
                                        </div>
                                        <input type="number"
                                               id="price1"
                                               name="products[1][price]"
                                               class="form-control"
                                               required
                                               readonly="readonly"
                                               value="{{ old('products[1][price]') }}">
                                        @error('products[1][price]')
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
                                <div class="form-group col-4 col-lg-2">
                                    <label class="form-label" for="amount1">Total</label>
                                    <div class="input-group input-group-merge">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                Rp.
                                            </div>
                                        </div>
                                        <input type="number"
                                               id="amount1"
                                               name="products[1][ammount]"
                                               class="form-control amount-form"
                                               required
                                               readonly="readonly"
                                               value="{{ old('products[1][ammount]') }}">
                                        @error('products[1][productId]')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                        </div>
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <div class="page-separator">
                            <div class="page-separator__text">General Info</div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-6">
                                <label class="form-label" for="taxId">Tax</label>
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
                </div>
{{--                <div class="list-group-item d-flex flex-column align-items-end">--}}
{{--                    <p class="form-label" id="subtotal"></p>--}}
{{--                    <p class="form-label" id="tax"></p>--}}
{{--                    <p class="form-label" id="totalAmount"></p>--}}
{{--                </div>--}}

                <div class="list-group-item d-flex justify-content-end">
                    <a class="btn btn-secondary mx-2" href="{{ url()->previous() }}"><span class="material-icons mr-2">close</span> Cancel</a>
                    <button type="submit" class="btn btn-primary"><span class="material-icons mr-2">add</span> Submit</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('addon-script')
    <script>
        $(document).ready(function () {
            const body = $('body');

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
                            '@error('products["+i+"][productId]')'+
                                '<span class="invalid-feedback" role="alert">'+
                                    '<strong>{{ $message }}</strong>'+
                                '</span>'+
                            '@enderror'+
                        '</div>'+
                        '<div class="form-group col-3 col-lg-1">' +
                            '<label class="form-label" for="qty'+i+'">Qty</label>' +
                            '<small class="text-danger">*</small>'+
                            '<input type="number" id="qty'+i+'" name="products['+i+'][qty]" required class="form-control qty-form" placeholder="Input product qty" data-row="'+i+'">'+
                            '@error("products['+i+'][productId]")'+
                                '<span class="invalid-feedback" role="alert">'+
                                    '<strong>{{ $message }}</strong>'+
                                '</span>'+
                            '@enderror'+
                        '</div>'+
                        '<div class="form-group col-6 col-lg-3">' +
                            '<label class="form-label" for="price'+i+'">Price</label>' +
                            '<small class="text-danger">*</small>'+
                            '<div class="input-group input-group-merge">'+
                                '<div class="input-group-prepend">'+
                                    '<div class="input-group-text">Rp.</div>'+
                                '</div>'+
                                '<input type="number" id="price'+i+'" name="products['+i+'][price]" required class="form-control" readonly="true">'+
                                '@error("products['+i+'][price]")'+
                                    '<span class="invalid-feedback" role="alert">'+
                                        '<strong>{{ $message }}</strong>'+
                                    '</span>'+
                                '@enderror'+
                                '<div class="input-group-append">'+
                                    '<div class="input-group-text" id="subscription-info'+i+'"></div>'+
                                '</div>'+
                            '</div>'+
                        '</div>'+
                        '<div class="form-group col-4 col-lg-2">' +
                            '<label class="form-label" for="amount'+i+'">Total</label>' +
                            '<small class="text-danger">*</small>'+
                            '<div class="input-group input-group-merge">'+
                                '<div class="input-group-prepend">'+
                                    '<div class="input-group-text">Rp.</div>'+
                                '</div>'+
                                '<input type="number" id="amount'+i+'" name="products['+i+'][ammount]" required class="form-control" readonly="true">'+
                                '@error("products['+i+'][price]")'+
                                    '<span class="invalid-feedback" role="alert">'+
                                        '<strong>{{ $message }}</strong>'+
                                    '</span>'+
                                '@enderror'+
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

            function getProductAmount(rowId){
                // Capture user input for query parameters
                let qty = $('#qty'+rowId).val();
                if (qty === ''){
                    qty = 0;
                }
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
                        $('#price'+rowId).val(response.data.productPrice);
                        $('#amount'+rowId).val(response.data.amount);
                        $('#subscription-info'+rowId).text(response.data.subscription);
                    },
                    error: function(xhr, status, error) {
                        // Handle any errors
                        alert('Error:', error);
                    }
                });
            }

            // calculate and show product amount when qty form inputed
            body.on('change', '.qty-form',function() {
                const rowId = $(this).attr('data-row');
                getProductAmount(rowId);
            });

            body.on('change', '.product-select',function() {
                const rowId = $(this).attr('data-row');
                getProductAmount(rowId);
            });
        });
    </script>
@endpush
