import { ajaxProcessing, processWithConfirmation, processModalForm } from './ajax-processing-data'
import './bootstrap'
import './image'
import { clearModalFormValidation, showModal } from './modal'
import './perfect-scrollbar'
import './player'
import './preloader'
import './select2'
import './sidebar'
import { onYouTubeIframeAPIReadyForAdmin } from './youtube'
import {subscriptionCycleDisplay} from './product-form-modal'

(function() {
    'use strict';

    $('[data-toggle="tab"]').on('hide.bs.tab', function (e) {
        $(e.target).removeClass('active')
    })

    $(".skills-range-slider").ionRangeSlider({
        min: 0,
        max: 100,
        step: 25,
        grid: true,
        values: [
            "Poor", "Needs Work", "Average Fair", "Good", "Excellent"
        ]
    });

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

    imagePreview('#logo', '#preview');
    imagePreview('add_logo', 'opponentTeamPreview');
    imagePreview('add_logoTeam', 'teamPreview');
    imagePreview('#previewPhoto', '#preview');

    window.ajaxProcessing = ajaxProcessing;
    window.processWithConfirmation = processWithConfirmation;
    window.processModalForm = processModalForm;

    window.clearModalFormValidation = clearModalFormValidation;
    window.showModal = showModal;

    window.onYouTubeIframeAPIReadyForAdmin = onYouTubeIframeAPIReadyForAdmin;

    window.subscriptionCycleDisplay = subscriptionCycleDisplay;
})()
