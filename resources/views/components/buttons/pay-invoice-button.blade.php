<a href="#" class="{{ $btnClass }} payInvoice" id="{{ $invoiceId }}" data-snaptoken="{{ $snapToken }}">
    <span class="material-icons mr-2">payment</span>
    {{ $btnText }}
</a>

@push('addon-script')
    <script type="text/javascript" src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.clientKey') }}"></script>
{{--    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.clientKey') }}"></script>--}}
    <script type="module">
        import { ajaxProcessing } from "{{ Vite::asset('resources/js/ajax-processing-data.js') }}" ;
        $(document).ready(function () {
            const body = $('body');

            body.on('click', '.payInvoice', function (e){
                e.preventDefault();
                const snapToken = $(this).attr('data-snapToken');
                const invoiceId = $(this).attr('id');

                snap.pay(snapToken, {
                    onSuccess: function(){
                        ajaxProcessing(
                            invoiceId,
                            '{{ route('invoices.set-paid', ':id') }}',
                            'PATCH',
                            '{{ csrf_token() }}',
                            null,
                            "Something went wrong when processing payment!"
                        )
                    },
                    onPending: function(){
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
                    onError: function(){
                        ajaxProcessing(
                            invoiceId,
                            '{{ route('invoices.set-uncollectible', ':id') }}',
                            'PATCH',
                            '{{ csrf_token() }}',
                            null,
                            "Something went wrong when processing payment!"
                        )
                    }
                });
            });
        });
    </script>
@endpush
