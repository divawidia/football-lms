import './bootstrap.js';
import './vendor/ckeditor.js'
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
import './youtube'

(function() {
  'use strict';

  $('[data-toggle="tab"]').on('hide.bs.tab', function (e) {
    $(e.target).removeClass('active')
  })

  ///////////////////////////////////
  // Custom JavaScript can go here //
  ///////////////////////////////////

    // function to display image preview
    function imagePreview(inputId, previewId) {
        let preview = document.getElementById(previewId);
        let input = document.getElementById(inputId);
        $(input).on('change', function (e) {
            e.preventDefault();
            preview.style.display = 'block';
            const [file] = input.files
            if (file) {
                preview.src = URL.createObjectURL(file)
            }
        });
    }

    imagePreview('previewPhoto', 'preview');

    imagePreview('foto', 'preview');

    imagePreview('logo', 'preview');
    imagePreview('add_logo', 'opponentTeamPreview');
    imagePreview('add_logoTeam', 'teamPreview');
})()
