import PerfectScrollbar from 'perfect-scrollbar';

const container = document.querySelector('#container');
const sidebar = document.querySelector('.sidebar');
const tabs = document.querySelector('.nav-tabs-container');

function ps(selectorElement){
    new PerfectScrollbar(selectorElement, {
        wheelSpeed: 1,
        wheelPropagation: true,
        minScrollbarLength: 20
    });
}

function tabsPerfectScrollbar(scrollableTabs) {
    new PerfectScrollbar(scrollableTabs, {
        wheelSpeed: 1,
        wheelPropagation: true,
        suppressScrollY: true, // Disable vertical scrolling
    });
}

ps(container);
ps(sidebar);
if (tabs !== null) {
    tabsPerfectScrollbar(tabs);
}

