@push('addon-script')
    <script>
        $(document).ready(function () {
            const body = $('body');

            body.on('click', '{{ $btnClass }}', function () {
                const id = $(this).attr('id');
                Swal.fire({
                    title: "{{ $confirmationText }}",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#1ac2a1",
                    cancelButtonColor: "#E52534",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ $processRoute }}".replace(':id', id),
                            type: '{{ $method }}',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function () {
                                Swal.fire({
                                    title: '{{ $successText }}',
                                    icon: 'success',
                                    showCancelButton: false,
                                    allowOutsideClick: false,
                                    confirmButtonColor: "#1ac2a1",
                                    confirmButtonText:
                                        'Ok!'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = "{{ $routeAfterProcess }}";
                                    }
                                });
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                Swal.fire({
                                    icon: "error",
                                    title: "{{ $errorText }}",
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