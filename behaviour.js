function isValid(formID) {
  if(!$(formID)[0].checkValidity()) {
    $(formID).find(':submit').click();
    return false;
  } else {
    return true;
  }
}

function showToolTip(element, msg, position) {
  $('<span class="form-tooltip-' + position + ' col-md-4">' + msg + '</span>').insertAfter(element);

  $('.form-invalid').blur(function() {

    $('.tooltips .form-tooltip-bottom').fadeOut('fast', function() {
      $(this).remove();
    });

    $('.tooltips .form-tooltip-top').fadeOut('fast', function() {
      $(this).remove();
    });

    var animationEvent = whichAnimationEvent();
    $(this).removeClass('form-invalid');
  }); // end blur
}


// From internet 'friends'

// https://jonsuh.com/blog/detect-the-end-of-css-animations-and-transitions-with-javascript/
function whichAnimationEvent() {
  var t,
      el = document.createElement("fakeelement");

  var animations = {
    "animation"      : "animationend",
    "OAnimation"     : "oAnimationEnd",
    "MozAnimation"   : "animationend",
    "WebkitAnimation": "webkitAnimationEnd"
  }

  for (t in animations){
    if (el.style[t] !== undefined){
      return animations[t];
    }
  }
}
