// *******************************************************************
// This function shows a toaster-like notification
// @param {String} type - success, warning, failure
// @param {String} msg - text to show
// *******************************************************************
function notify(type, msg) {
  let html = "<span id='notify' class = 'notify-" + type + " col-md-2'>" + msg + "</span>";
  let notification = $(html).appendTo("body");
  notification.css("opacity", 0);
  notification.css("padding-top", 0);
  notification.css("padding-bottom", 0);
  notification.animate({ opacity: "1", paddingTop: "1%", paddingBottom: "1%" }, 500, function() {
    setTimeout(function() {
      notification.animate({opacity: "0"}, 500, function() {
        notification.remove();
      })
    }, 500);
  });
}

// *******************************************************************
// This function transforms the form data into an object {name: value}
// @param {jQuery selector} form - selector of the form e.g. '#myForm'
// TODO: check checkbox, radiobutton, textarea
// *******************************************************************
function getFormData(form) {
  var values = {};

  // Grab each input data
  $(form + " input").each(function() {
    var name = $(this).attr("name");
    values[name] = $(this).val();
    console.log("hi");
  });

  // Grab each textarea
  $(form + " textarea").each(function() {
    var name = $(this).attr("name");
    values[name] = $(this).val();
  });

  // Grab each select data
  $(form + " select").each(function() {
    var name = $(this).attr("name");
    values[name] = $(this).find(":selected").val();
  });

  return values;
}

// *******************************************************************
// This function shows a loading animation
// @param {String} target - container that will hold the loading text
// *******************************************************************
function showLoading(target) {
  $(target).append('<div class="loading"><span class="animate-load">Loading...</span></div>');
  $(".loading span").addClass("animate-load");
}

// *******************************************************************
// This function removes the loading splash
// @param {String} target - container that will hold the loading text
// *******************************************************************
function hideLoading() {
  $(".loading").remove();
}

// *******************************************************************
// This function sets the container for the tooltip
// @param {jQuery} container - the selector for the container e.g. $("#item")
// @param {String} position - position of tooltip
// *******************************************************************
function addTooltip(container, position) {
  container.addClass("tooltip-container");
  $('<div class="tooltip-text"></div>').appendTo($(".tooltip-container"));

  switch(position) {
    case 'top':
      $(".tooltip-text").css("bottom", "103%");
      break;
    case 'right':
      $(".tooltip-text").css("left", "103%");
      break;
    case 'bottom':
      $(".tooltip-text").css("top", "103%");
      break;
    case 'left':
      $(".tooltip-text").css("right", "103%");
      break;
    default:;
  }
}

// *******************************************************************
// this function adds html to the tooltip
// @param {String} html - html to add to the tooltip
// *******************************************************************
function addTooltipHTML(html) {
  $(html).appendTo($(".tooltip-text"));
}

// *******************************************************************
// This function removes all tootips on the page
// *******************************************************************
function removeToolTip() {
  // Remove Details
  $(".tooltip-container").removeClass("tooltip-container");
  $(".tooltip-text").remove();
}

/*
 * AJAX Scripts
 */

 // *******************************************************************
 // This function shows a toaster-like notification
 // @param {String} type - success, warning, failure
 // @param {String} msg - text to show
 // *******************************************************************
function saveDataPost(url, values, callback) {
  $.ajax(
    {
      url: url,
      type: "POST",
      data: values,
      success: function(data, textStatus, jqXHR) {
        callback(data, textStatus, jqXHR);
        notify('success', 'Saved');
      },
      error: function(data, textStatus, jqXHR) {
        notify('failure', 'Something went wrong!');
      }
    }
  );
}

function getData(tableName, wantedColumns, targetValue, order ) {
  let data = {};
  data.tableName = tableName;
  $.extend(data, wantedColumns);
  $.extend(data, targetValue);
  $.extend(data, order);

  $.ajax(
    {
        url: "db/ajax/data-get.php",
        type: "POST",
        data: data,
        success: function(result, textStatus, jqXHR) {
          alert(result);
        },
        error: function(result, textStatus, jqXHR) {

        }
    }
  );
}

function simpleQuery(tableName, action, values) {
  let data = {};
  data.tableName = tableName;
  data.action = action;
  $.extend(data, values);
  saveDataPost('db/ajax/data-save.php', data, function(result, textStatus, jqXHR) {

  })
}

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

function toggleCollapse(id) {
  let openBoard = '#' + $('[data-collapse="false"]').attr("id");

  if($(id).attr("data-collapse") == "false") {
    console.log("Collapse is now true");
    $(id).attr("data-collapse", "true");
    $(id).slideUp();
  } else {
    console.log("Collapse is now false");
    $(id).attr("data-collapse", "false");
    $(id).slideDown();

    // Hide open board
    $(openBoard).attr("data-collapse", "true");
    $(openBoard).slideUp();
  }
}

/**
 * These scripts are not necessarily generalized. They just happened to be needed in all pages
 */
// *******************************************************************
// This function shows a toaster-like notification
// @param {String} type - success, warning, failure
// @param {String} msg - text to show
// *******************************************************************
function selectJob(el) {
  $(".active").removeClass("active");
  $(el).addClass("active");
}

function showWork() {
  removeToolTip();
  addTooltip($("#links"), 'right');
  addTooltipHTML('<div class="job-header"><span class="job-title">Work</span><a onclick="removeToolTip();"><i class="fa fa-close fa-lg event-close"></i></a></div>');
  addTooltipHTML('<div id="job"><div id="job-form"><div class="select-multiple"><span class="job-item active" onclick="selectJob(this);">job 1</span><span class="job-item" onclick="selectJob(this);">job 1</span><span class="job-item" onclick="selectJob(this);">job 1</span><span class="job-item" onclick="selectJob(this);">job 1</span><span class="job-item" onclick="selectJob(this);">job 1</span><span class="job-item" onclick="selectJob(this);">job 1</span></div><button class="btn btn-primary">Start</button></div></div>');


  // showLoading(".tooltip-text");

  // let values = {};
  // values.jid = $(el).data("id");
  // values.date = $(el).data("date");
  //
  // $.ajax({
  //   url: "db/ajax/get-event.php",
  //   data: values,
  //   success: function(result) {
  //     hideLoading();
  //     let currShift = undefined;
  //     let prevShift = undefined;
  //     let eventIndex = 1;
  //     let entries = '<div class="event-task-list">';
  //     for(let i = 0; i < result.length; i++) {
  //       console.log(result[i]);
  //       currShift = result[i]['work_start'];
  //       // if a new shift occurs, set up the shift section;
  //       if (currShift.indexOf(prevShift) === -1) {
  //         if (prevShift !== undefined) {
  //             entries += '</div>'; // end previous shift
  //             addTooltipHTML(entries);
  //             entries = '<div class="event-task-list">';
  //         }
  //         prevShift = currShift;
  //         let newShiftTitle = result[i]['work_title'];
  //         let shiftStart = result[i]['work_start'];
  //         let shiftEnd = result[i]['work_end'];
  //         let pos = shiftStart.indexOf(" ");
  //         shiftStart = shiftStart.substring(pos + 1, pos + 6);
  //         pos = shiftEnd.indexOf(" ");
  //         shiftEnd = shiftEnd.substring(pos + 1,  pos + 6);
  //         console.log("in");
  //         addTooltipHTML('<div class="event-header"><span class="event-title">' + newShiftTitle + '</span><span class="event-time">' + shiftStart + ' - ' + shiftEnd + '</span></div>');
  //         eventIndex = 1;
  //       } // end if
  //
  //       // Add entries of shift
  //       if (result[i]['entry'] != null) {
  //         entries = entries + '<span class="event-task"><span class="event-task-num">' + eventIndex + '.</span>' + result[i]['entry'] + '</span>';
  //       }
  //
  //       eventIndex++;
  //     } // end for
  //
  //     addTooltipHTML(entries); // addFinal Tasks
  //
  //   },
  //   error: function(result) {
  //     alert("Something went wrong");
  //   }
  // });
}
