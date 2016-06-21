/*
 * AJAX Scripts
 */

function addJob() {
  var job_name = $('#job-input').val();

  if(job_name != '') {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
      if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        if(xmlhttp.responseText.indexOf('true') !== -1) {
          var id = xmlhttp.responseText.substring(xmlhttp.responseText.indexOf(' ') + 1);
          $('.table-choice tr:last').before('<tr class="clickable-row"><td data-id="'+ id +'">' + job_name + '</td><td></td></tr>');
          $('.clickable-row').click(function() {
            // Remove previous active
            $('.clickable-row.active').removeClass("active");
            $(this).addClass("active");
          });
          $('#job-input').val('');
        } else if(xmlhttp.responseText.indexOf('Invalid') !== -1) {
          window.location.href = "index.php";
        } else {
          $('#job-input').addClass('form-invalid');
          $('#job-input').focus();
          showToolTip('#job-input', 'This job already exists!', 'top');
        }
      }
    };

    xmlhttp.open("GET", "http://localhost:8888/db/ajax/add-job.php?title=" + job_name, true);
    xmlhttp.send();
  }
}

function startJob() {
  if(isValid('#time-start')) {
    var jobID = $('.active td').data('id');
    var title = $('#title-input').val();
    if (!jobID) {
      showToolTip('#choices', 'Please select a job', 'top');
      $('#choices').addClass('form-invalid');
      $('#choices').css('border','1px solid rgba(255,0,0,1)');
      $('td').click(function() {
        $('.tooltips .form-tooltip-top').fadeOut('fast', function() {
          $(this).remove();
        });

        var animationEvent = whichAnimationEvent();
        $(this).removeClass('form-invalid');
      })
    } else {
      // Run AJAX
      var xmlhttp = new XMLHttpRequest();

      xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
          window.location.href="time-keeper.php";
        }
      }

      xmlhttp.open("GET", "http://localhost:8888/db/ajax/start-job.php?job_id=" + jobID + "&title=" + title, true);
      xmlhttp.send();
    } // end else
  } // end if
} // end startJob

function stopJob(logID) {
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function() {
    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
      window.location.href = 'time-keeper.php';
    }
  }

  xmlhttp.open("GET", 'http://localhost:8888/db/ajax/end-job.php?log_id=' + logID, true);
  xmlhttp.send();
}

function postFormSubmit(formID, elements, url) {
  if(isValid(formID)) {
    // Get params
    var inputs = $(elements);
    var params = inputs[0].name + "=" + inputs[0].value;
    for(var i = 1; i < $(elements).length; i++) {
      if(inputs[i].name != '') {
        params += "&" + inputs[i].name + "=" + inputs[i].value;
      }
    }

    // Run AJAX
    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function() {
      if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        switch(formID) {
          case '#login-form':
            if(xmlhttp.responseText.indexOf('false') !== -1) {
              alert("xmlhttp.responseText");
              // showToolTip(formID);
            } else { // Logged In
              $(formID).append('<input type="hidden" name="user_id" value="' + xmlhttp.responseText + '">');
              $(formID).find(':submit').click();

            }
            break;
          case '#register-form':
            if(xmlhttp.responseText.indexOf('username') !== -1) {
              $(formID + ' #username').addClass('form-invalid');
              $(formID + ' #username').focus();
              showToolTip($(formID + ' #username'), "This username is already in use!", 'bottom');
            } else if (xmlhttp.responseText.indexOf('email') !== -1) {
              $(formID + ' #email').addClass('form-invalid');
              $(formID + ' #email').focus();
              showToolTip($(formID + ' #email'), "This email address is already in use!", 'bottom');
            } else {
              window.location.href = 'index.php';
            }
            break;
          default:
            alert(xmlhttp.responseText);
        }
      } // end if
    }; // end xmlhttp

    xmlhttp.open("POST", url, true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send(params);
  }
}
