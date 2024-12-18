@push('addon-script')
    <script>
            $('{{ $formId }}').on('submit', function(e) {
                e.preventDefault();
                const id = $('{{ $updateDataId }}').val();

                Swal.fire({
                    title: 'Processing...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: "{{ $routeUpdate }}".replace(':id', id),
                    type: $(this).attr('method'),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        Swal.close();
                        $('{{ $modalId }}').modal('hide');
                        Swal.fire({
                            title: res.message,
                            icon: 'success',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: "#1ac2a1",
                            confirmButtonText:
                                'Ok!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.reload();
                            }
                        });
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        Swal.close();
                        const response = JSON.parse(xhr.responseText);
                        $.each(response.errors, function(key, val) {
                            $('{{ $formId }} span.' + key + '_error').text(val[0]);
                            $("{{ $formId }} #" + key).addClass('is-invalid');
                        });
                        Swal.fire({
                            title: errorThrown,
                            text: response.message,
                            icon: 'error',
                        });
                    }
                });
            });
    </script>
@endpush
