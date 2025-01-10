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

    // function to display image preview
    function imagePreview(input, preview) {
        $(input).on('change', function (e) {
            e.preventDefault();
            $(preview).css('display', 'block');
            const [file] = this.files;
            if (file) {
                $(preview).attr('src', URL.createObjectURL(file));
            }
        });
    }

    imagePreview('#createCompetitionForm #logo', '#createCompetitionForm #preview');

    imagePreview('#foto', '#preview');

    imagePreview('logo', 'preview');
    imagePreview('add_logo', 'opponentTeamPreview');
    imagePreview('add_logoTeam', 'teamPreview');
})()
