!function (t) {
    var n = {};

    function e(o) {
        if (n[o]) return n[o].exports;
        var a = n[o] = {i: o, l: !1, exports: {}};
        return t[o].call(a.exports, a, a.exports, e), a.l = !0, a.exports
    }

    e.m = t, e.c = n, e.d = function (t, n, o) {
        e.o(t, n) || Object.defineProperty(t, n, {enumerable: !0, get: o})
    }, e.r = function (t) {
        "undefined" != typeof Symbol && Symbol.toStringTag && Object.defineProperty(t, Symbol.toStringTag, {value: "Module"}), Object.defineProperty(t, "__esModule", {value: !0})
    }, e.t = function (t, n) {
        if (1 & n && (t = e(t)), 8 & n) return t;
        if (4 & n && "object" == typeof t && t && t.__esModule) return t;
        var o = Object.create(null);
        if (e.r(o), Object.defineProperty(o, "default", {
            enumerable: !0,
            value: t
        }), 2 & n && "string" != typeof t) for (var a in t) e.d(o, a, function (n) {
            return t[n]
        }.bind(null, a));
        return o
    }, e.n = function (t) {
        var n = t && t.__esModule ? function () {
            return t.default
        } : function () {
            return t
        };
        return e.d(n, "a", n), n
    }, e.o = function (t, n) {
        return Object.prototype.hasOwnProperty.call(t, n)
    }, e.p = "/", e(e.s = 524)
}({
    524: function (t, n, e) {
        t.exports = e(525)
    }, 525: function (t, n, e) {
        "use strict";
        e.r(n);
        e(526)
    }, 526: function (t, n) {
        !function () {
            "use strict";
            $('[data-toggle="swal"]').on("click", (function () {
                !function t(n) {
                    var e = {
                        title: void 0 !== n.data("swal-title") ? n.data("swal-title") : "Title",
                        text: n.data("swal-text"),
                        type: void 0 !== n.data("swal-type") ? n.data("swal-type") : null,
                        html: n.data("swal-html"),
                        showCancelButton: n.data("swal-show-cancel-button"),
                        cancelButtonText: n.data("swal-cancel-button-text"),
                        closeOnCancel: void 0 === n.data("swal-close-on-cancel") || n.data("swal-close-on-cancel"),
                        confirmButtonText: n.data("swal-confirm-button-text"),
                        confirmButtonColor: void 0 !== n.data("swal-confirm-button-color") ? n.data("swal-confirm-button-color") : settings.colors.primary[500],
                        closeOnConfirm: void 0 === n.data("swal-close-on-confirm") || n.data("swal-close-on-confirm")
                    };
                    swal(e, (function (e) {
                        if (n.data("swal-confirm-cb") && e) return t($(n.data("swal-confirm-cb")));
                        n.data("swal-cancel-cb") && t($(n.data("swal-cancel-cb")))
                    }))
                }($(this))
            }))
        }()
    }
});
