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
