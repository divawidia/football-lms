import './perfect-scrollbar'
import './sidebar'
import './image'
import './player'
import './youtube'
import './select2'

(function() {
    'use strict';

    $('[data-toggle="tab"]').on('hide.bs.tab', function (e) {
        $(e.target).removeClass('active')
    })

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
