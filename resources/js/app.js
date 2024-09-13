import './bootstrap.js';
// import './main'
import './perfect-scrollbar'
import './sidebar'
// import './sidebar-menu-collapse'
import './dropdown-tooltip'
// import './popover'
import './overlay'
import './mdk-carousel-control'
import './read-more'
import './image'
import './accordion'
import './player'

(function() {
  'use strict';

  $('[data-toggle="tab"]').on('hide.bs.tab', function (e) {
    $(e.target).removeClass('active')
  })

  ///////////////////////////////////
  // Custom JavaScript can go here //
  ///////////////////////////////////

})()
