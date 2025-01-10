export function clearModalFormValidation(formId){
    $(formId+' .invalid-feedback').empty();
    $(formId+' select').removeClass('is-invalid');
    $(formId+' input').removeClass('is-invalid');
}

export function showModal(buttonId, modalId, formId,callback) {
    $(buttonId).on('click', function (e) {
        e.preventDefault();
        $(modalId).modal('show');
        clearModalFormValidation(formId)

        if (typeof callback === 'function') {
            callback();
        }
    });
}
