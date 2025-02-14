function subscriptionCycleDisplay(formId) {
    const priceOption = $(formId + ' #priceOption');
    if (priceOption.val() === 'subscription') {
        $(formId+' .subscriptionCycleForm').show()
        $(formId+' #subscriptionCycle').attr('required');
    } else if (priceOption.val() === 'one time payment') {
        $(formId+' .subscriptionCycleForm').hide();
        $(formId+' #subscriptionCycle').val("(NULL)").removeAttr('required');
    }
}

function priceOptionFormOnChange(formId) {
    const priceOption = $(formId + ' #priceOption');
    priceOption.on('change', function (e) {
        e.preventDefault();
        subscriptionCycleDisplay(formId);
    });
}

priceOptionFormOnChange('#formAddProductModal');
priceOptionFormOnChange('#formEditProductModal');

export { subscriptionCycleDisplay, priceOptionFormOnChange };
