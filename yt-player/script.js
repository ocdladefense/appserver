var player,
    time_update_interval = 0;

function onYouTubeIframeAPIReady() {
    player = new YT.Player('video-placeholder', {
        width: 600,
        height: 400,
        videoId: 'Q6TRolTHbKY',
        playerVars: {
            color: 'white',
            rel: 0,
            enablejsapi: 1,
            playlist: '9w-ROKc0BWU,C7X_pKk7q4k,qtEXp5v4rlU,Z74Fe3_zUC8,Vgv7BdFmKaw,WC9dH3sDENg,zQdLbYIDQ-Y,4mxFb5VH12Y,1sccgTiY7jg,zQdLbYIDQ-Y,Mi71RC91ZPM'
        },
        events: {
            onReady: initialize
        }
    });
}

function initialize(){

    // Update the controls on load
    updateTimerDisplay();
    updateProgressBar();

    // Clear any old interval.
    clearInterval(time_update_interval);

    // Start interval to update elapsed time display and
    // the elapsed part of the progress bar every second.
    time_update_interval = setInterval(function () {
        updateTimerDisplay();
        updateProgressBar();
    }, 1000);


    $('#volume-input').val(Math.round(player.getVolume()));
}


// This function is called by initialize()
function updateTimerDisplay(){
    // Update current time text display.
    $('#current-time').text(formatTime( player.getCurrentTime() ));
    $('#duration').text(formatTime( player.getDuration() ));
}


// This function is called by initialize()
function updateProgressBar(){
    // Update the value of our progress bar accordingly.
    var elapsed = player.getCurrentTime();
    var duration = player.getDuration();
    var percent = toPercent(elapsed, duration);
    $('#progress-bar').val(toProgressBarValue(toPercent(elapsed, duration)));
}

function toPercent(numerator, denominator){
    return numerator / denominator;
}

function toProgressBarValue(percent){
    return percent * 100;
}

// Helper Functions
function formatTime(time){
    time = Math.round(time);

    var minutes = Math.floor(time / 60),
        seconds = time - minutes * 60;

    var hours = Math.floor(time / 60 / 60),
        minutes = Math.floor(time / 60 - hours * 60); //use Math.floor so the minutes don't get all crazy and show as long decimals

    seconds = seconds < 10 ? '0' + seconds : seconds; //adds an extra zero if the seconds number is less than 10

    minutes = minutes < 10 ? '0' + minutes : minutes; //adds an extra zero if the minute number is less than 10

    return hours + ":" + minutes + ":" + seconds;
}


//PUT IN CLASS LATER
const availableActions = {

    seekTo : {

        selector : "#progress-bar",
        respondTo : "mouseup touchend",
        handler : function (e) { 
            // Calculate the new time for the video.
            // new time in seconds = total duration in seconds * ( value of range input / 100 )
            var newTime = player.getDuration() * (e.target.value / 100);

            // Skip video to new time.
            player.seekTo(newTime); 
        }

    },

    play : {
        selector : "#play",
        respondTo : "click",
        handler : function() {
            player.playVideo();
        }
    },

    pause : {
        selector : "#pause",
        respondTo : "click",
        handler : function() {
            player.pauseVideo();
        }
    },

    mute : {
        selector : "#mute-toggle",
        respondTo : "click",
        handler : function() {
            var mute_toggle = $(this);

            if(player.isMuted()){
                player.unMute();
                mute_toggle.text('volume_up');
            }
            else{
                player.mute();
                mute_toggle.text('volume_off');
            }
        }
    },

    volume : {
        selector : "#volume-input",
        respondTo : "change",
        handler : function() {
            player.setVolume($(this).val());
        }
    },

    next : {
        selector : "#next",
        respondTo : "click",
        handler : function() {
            player.nextVideo();
        }
    },

    prev : {
        selector : "#prev",
        respondTo : "click",
        handler : function() {
            player.previousVideo();
        }
    },

    fullscreen : {
        selector : "#fullscreen",
        respondTo : "click",
        handler : function() {
            var element = document.getElementById("video-placeholder"); 
            var Promise = element.requestFullscreen();
        }
    }


};

//Running the code
function init() {
    for (var name in availableActions)
    {
        var action = availableActions[name];
        $(action.selector).on(action.respondTo, action.handler);
    }
}

init();



/* //OLD CODE

// Playback
$('#play').on('click', function () {
    player.playVideo();
});


$('#pause').on('click', function () {
    player.pauseVideo();
});


// Sound volume
$('#mute-toggle').on('click', function() {
    var mute_toggle = $(this);

    if(player.isMuted()){
        player.unMute();
        mute_toggle.text('volume_up');
    }
    else{
        player.mute();
        mute_toggle.text('volume_off');
    }
});

$('#volume-input').on('change', function () {
    player.setVolume($(this).val());
});


// Other options
$('#speed').on('change', function () {
    player.setPlaybackRate($(this).val());
});

$('#quality').on('change', function () {
    player.setPlaybackQuality($(this).val());
});


// Playlist
$('#next').on('click', function () {
    player.nextVideo()
});

$('#prev').on('click', function () {
    player.previousVideo()
});


// Load video
$('.thumbnail').on('click', function () {

    var url = $(this).attr('data-video-id');

    player.cueVideoById(url);

});


//Full Screen
$('#fullscreen').on('click', function () {
    var element = document.getElementById("video-placeholder"); 
    var Promise = element.requestFullscreen();
}); */

//Fullscreen
/* window.addEventListener('orientationchange', function(e){
    var isUpright = (window.orientation == 'portrait');
}); 

function toggleFullscreen() {
  let elem = document.querySelector("video");

  if (!document.fullscreenElement) {
    elem.requestFullscreen().catch(err => {
      alert(`Error attempting to enable full-screen mode: ${err.message} (${err.name})`);
    });
  } else {
    document.exitFullscreen();
  }
}

*/






$('pre code').each(function(i, block) {
    hljs.highlightBlock(block);
});