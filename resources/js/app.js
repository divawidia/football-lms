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
        input.onchange = evt => {
            preview.style.display = 'block';
            const [file] = input.files
            if (file) {
                preview.src = URL.createObjectURL(file)
            }
        }
    }

    // function to use ckeditor
    function initCkeditor(inputSelector) {
        const editor = document.querySelector(inputSelector);
        ClassicEditor.create(editor, {
            toolbar: ['bold', 'italic', 'link', 'bulletedList', 'numberedList'],
            height: '25em'
        }).catch(error => {
            alert(error);
        });
    }

    imagePreview('previewPhoto', 'preview');
    initCkeditor('.ckeditor')
})()
