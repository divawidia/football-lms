import PerfectScrollbar from 'perfect-scrollbar';

const container = document.querySelector('#container');
const sidebar = document.querySelector('.sidebar');

function ps(selectorElement){
    new PerfectScrollbar(selectorElement, {
        wheelSpeed: 2,
        wheelPropagation: true,
        minScrollbarLength: 20
    });
}

ps(container);
ps(sidebar);

// (function() {
//   'use strict';
//
//   $('[data-perfect-scrollbar]').each(function() {
//     const $element = $(this)
//     const element = this
//     const ps = new PerfectScrollbar(element, {
//       wheelPropagation: void 0 !== $element.data('perfect-scrollbar-wheel-propagation')
//         ? $element.data('perfect-scrollbar-wheel-propagation')
//         : false,
//       suppressScrollY: void 0 !== $element.data('perfect-scrollbar-suppress-scroll-y')
//         ? $element.data('perfect-scrollbar-suppress-scroll-y')
//         : false,
//       swipeEasing: false
//     })
//     Object.defineProperty(element, 'PerfectScrollbar', {
//       configurable: true,
//       writable: false,
//       value: ps
//     })
//   })
//
// })()
