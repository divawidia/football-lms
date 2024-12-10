const body = $('body');

export function processWithConfirmation(btnClass, processRoute, routeAfterProcess, method, confirmationText, errorText, csrfToken) {
    body.on('click', btnClass, function () {
        const id = $(this).attr('id');
        Swal.fire({
            title: confirmationText,
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#1ac2a1",
            cancelButtonColor: "#E52534",
            confirmButtonText: "Yes!"
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: processRoute.replace(':id', id),
                    type: method,
                    data: {
                        _token: csrfToken
                    },
                    success: function (response) {
                        Swal.fire({
                            title: response.message,
                            icon: 'success',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: "#1ac2a1",
                            confirmButtonText:
                                'Ok!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = routeAfterProcess;
                            }
                        });
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        const response = JSON.parse(jqXHR.responseText);
                        Swal.fire({
                            icon: "error",
                            title: errorText,
                            text: response.message
                        });
                    }
                });
            }
        });
    });
}

export function processModalForm(formId, route, updateDataId = null, modalId){
    $(formId).on('submit', function(e) {
        e.preventDefault();
        const id = $(updateDataId).val();

        Swal.fire({
            title: 'Processing...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: route.replace(':id', id),
            type: $(this).attr('method'),
            data: new FormData(this),
            contentType: false,
            processData: false,
            success: function(res) {
                Swal.close();
                $(modalId).modal('hide');
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
                    $(formId+' span.' + key + '_error').text(val[0]);
                    $(formId+" #" + key).addClass('is-invalid');
                });
                Swal.fire({
                    title: errorThrown,
                    text: response.message,
                    icon: 'error',
                });
            }
        });
    });
}
