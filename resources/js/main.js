import * as domFactory from "dom-factory";

(function() {
  'use strict';

  // Self Initialize DOM Factory Components
  domFactory.handler.autoInit()

  // ENABLE TOOLTIPS
  $('[data-toggle="tooltip"]').tooltip()

})()
