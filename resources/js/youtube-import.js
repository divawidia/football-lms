// This code loads the IFrame Player API code asynchronously.
const tag = document.createElement('script');

tag.src = "https://www.youtube.com/iframe_api";
const firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

let player;

// Load the YouTube Iframe API and create a player
function onYouTubeIframeAPIReady(videoId, playerId) {
    player = new YT.Player(playerId, {
        height: '250',
        width: '100%',
        videoId: videoId,
        // playerVars: {
        //     'playsinline': 1
        // },
        events: {
            'onReady': onPlayerReady,
            'onStateChange': onPlayerStateChange
        }
    });
}

// When the player is ready, get the video duration and show video
function onPlayerReady() {
    const duration = player.getDuration(); // Get the duration in seconds
    // event.target.playVideo();
    $('.totalDuration').val(duration);
}

let done = false;

function onPlayerStateChange(event) {
    if (event.data === YT.PlayerState.PLAYING && !done) {
        setTimeout(stopVideo, 6000);
        done = true;
    }
}

function stopVideo() {
    player.stopVideo();
}

// Extract video ID from the URL
function extractVideoID(url) {
    const regex = /(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/;
    const match = url.match(regex);
    return (match && match[1]) ? match[1] : null;
}

// Handle form submission
function showYoutubePreview(inputId, formId, playerId) {
    $(inputId).on('change', function (e) {
        e.preventDefault(); // Prevent form submission

        let preview = $(formId + ' #preview-container');
        let player = $(playerId);
        let errorSpan = $(formId + ' span.lessonVideoURL');
        let inputUrl = $(inputId);

        errorSpan.text('');
        inputUrl.removeClass('is-invalid');

        if (player.attr('src') !== undefined) {
            player.remove();
            preview.html('<div id="' + playerId.replace(/^#/, '') + '"></div>')
        }

        // Get the YouTube URL from the input
        const url = inputUrl.val();

        // Extract the video ID
        const videoID = extractVideoID(url);
        $(formId + ' #videoId').val(videoID);

        if (videoID) {
            onYouTubeIframeAPIReady(videoID, playerId.replace(/^#/, ''));
        } else {
            errorSpan.text('Invalid youtube URL');
            inputUrl.addClass('is-invalid');
        }
    });
}

showYoutubePreview('#lessonVideoURL', '#formAddLessonModal', '#create-player');
showYoutubePreview('#edit-lessonVideoURL', '#formEditLessonModal', '#edit-player');
