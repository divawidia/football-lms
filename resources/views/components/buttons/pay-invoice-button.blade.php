<a href="#" class="{{ $btnClass }} payInvoice" id="{{ $invoiceId }}" data-snaptoken="{{ $snapToken }}">
    <span class="material-icons mr-2">payment</span>
    {{ $btnText }}
</a>

@push('addon-script')
{{--    <script type="text/javascript"--}}
{{--            src="https://app.midtrans.com/snap/snap.js"--}}
{{--            data-client-key="{{ config('services.midtrans.clientKey') }}"></script>--}}
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.clientKey') }}"></script>
    <script>
        $(document).ready(function () {
            const body = $('body');

            function paymentSuccess(invoiceId){
                $.ajax({
                    url: '{{ route('invoices.set-paid', ':id') }}'.replace(':id', invoiceId),
                    method: 'PATCH',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function() {
                        Swal.fire({
                            title: 'Invoice successfully paid!',
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
                    // error: function(jqXHR, textStatus, errorThrown) {
                    //     Swal.fire({
                    //         icon: "error",
                    //         title: "Something went wrong when processing payment!",
                    //         text: errorThrown,
                    //     });
                    // }
                });
            }

            function paymentExpired(invoiceId){
                $.ajax({
                    url: '{{ route('invoices.set-uncollectible', ':id') }}'.replace(':id', invoiceId),
                    method: 'PATCH',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function() {
                        Swal.fire({
                            title: 'Something wrong when processing Invoice payment!',
                            icon: 'error',
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
                    error: function(jqXHR, textStatus, errorThrown) {
                        Swal.fire({
                            icon: "error",
                            title: "Something went wrong when processing payment!",
                            text: errorThrown,
                        });
                    }
                });
            }

            body.on('click', '.payInvoice', function (e){
                e.preventDefault();
                const snapToken = $(this).attr('data-snapToken');
                const invoiceId = $(this).attr('id');

                snap.pay(snapToken, {
                    onSuccess: function(result){
                        paymentSuccess(invoiceId);
                    },
                    onPending: function(result){
                        Swal.fire({
                            title: 'Invoice payment still pending!',
                            icon: 'info',
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
                    onError: function(result){
                        paymentExpired(invoiceId)
                    }
                });
            });
        });
    </script>
@endpush
