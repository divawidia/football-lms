(function () {
    "use strict";

    function t(t) {
        if (!t.id) return t.text;
        var e = $(t.element).data("avatar-src");
        return e ? $('<span class="avatar avatar-xs mr-2"><img class="avatar-img rounded-circle" src="' + e + '" alt="' + t.text + '"></span><span>' + t.text + "</span>") : t.text
    }

    $.fn.select2.defaults.set("theme", "bootstrap4"), $('[data-toggle="select"]').each((function () {
        var e = $(this), r = {
            dropdownParent: e.closest(".modal").length ? e.closest(".modal") : $(document.body),
            minimumResultsForSearch: e.data("minimum-results-for-search"),
            tags: e.data("tags"),
            multiple: e.data("multiple"),
            placeholder: e.data("placeholder"),
            templateResult: t
        };
        e.select2(r)
    }))
}())
