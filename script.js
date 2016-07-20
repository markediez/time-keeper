// *******************************************************************
// This function shows a toaster-like notification
// @param {String} type - success, warning, failure
// @param {String} msg - text to show
// *******************************************************************
function notify(type, msg, callback) {
  let html = "<span id='notify' class = 'notify-" + type + " col-md-2'>" + msg + "</span>";
  let notification = $(html).appendTo("body");
  notification.css("opacity", 0);
  notification.css("padding-top", 0);
  notification.css("padding-bottom", 0);
  notification.animate({ opacity: "1", paddingTop: "1%", paddingBottom: "1%" }, 500, function() {
    setTimeout(function() {
      notification.animate({opacity: "0"}, 500, function() {
        notification.remove();
        if (typeof callback == 'function') {
          callback();
        }
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
    $(id).attr("data-collapse", "true");
    $(id).slideUp();
  } else {
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
  // remove red outline if applicable
  $(".select-multiple").css("border-color", "#EEEEEE");
  $(".select-multiple").css("box-shadow", "");
  $(".active").removeClass("active");
  $(el).addClass("active");
}

function startJob(user_id) {
  let id = $(".job-item.active").data("id");
  if (id === null) {
    $(".select-multiple").css("border-color", "red");
    $(".select-multiple").css("box-shadow", "0 0 10px red");
  } else {
    let timeNow = new Date();
    let Y = timeNow.getFullYear();
    let m = ("0" + (timeNow.getMonth() + 1)).slice(-2);
    let d = ("0" + timeNow.getDate()).slice(-2);
    let H = ("0" + timeNow.getHours()).slice(-2);
    let i = ("0" + timeNow.getMinutes()).slice(-2);
    let s = ("0" + timeNow.getSeconds()).slice(-2);
    timeNow = Y + "-" + m + "-" + d + " " + H + ":" + i + ":" + s;
    let values = {
      'tableName': "WorkLog",
      'action': "insert",
      'values': {
        'user_id': user_id,
        'job_id': id,
        'title': "",
        'start_time': timeNow
      }
    };
    saveDataPost("db/ajax/data-save.php", values, function(data) {
      let url = "time-progress.php?log_id=" + data;
      window.location.href = url;
    });
  }
} // end startJob


function showWork(user_id) {
  removeToolTip();
  addTooltip($("#links"), 'right');
  addTooltipHTML('<div class="job-header"><span class="job-title">Work</span><a onclick="removeToolTip();"><i class="fa fa-close fa-lg event-close"></i></a></div>');
  showLoading(".tooltip-text");

  $.ajax({
    url: "db/ajax/get-job.php",
    success: function(result) {
      hideLoading();
      let html = "";
      let res = JSON.parse(result);
      if (res.status == "false") {
        showLoading(".tooltip-text");
        notify("success", "There is a job in progress...", function(){
          let url = "time-progress.php?log_id=" + res.log_id;
          window.location.href = url;
        });
      } else {
        // List Jobs
        html = '<div id="job"><div id="job-form"><div class="select-multiple">';
        delete res.status;

        for(let i in res) {
          html += '<span class="job-item" onclick="selectJob(this);"';
          html += ' data-id=' + res[i].id + '>' ;
          html += res[i].title;
          html += '<span class="job-action"><i class="fa fa-pencil"></i>';
          html += '<i class="fa fa-trash"></i></span>';
          html += '</span>';
        }

        // Option to add a job
        html += '<input type="text" class="job-input" placeholder="Add a job">';

        // Start button
        html += '</div><button class="btn btn-primary" onclick="startJob(' + user_id + ');">Start</button></div></div>';
        addTooltipHTML(html);

        // Add jobs on enter
        $(".job-input").keyup(function(e) {
          let obj = $(this);
          if (e.keyCode == 13 && obj.val() != "") {
            values = {
              'tableName': "Jobs",
              'action': "insert",
              'values': {
                'user_id': user_id,
                'title': obj.val()
              }
            };
            saveDataPost("db/ajax/data-save.php", values, function(result) {
              //insertBefore
              let jobRow = '<span class="job-item" onclick="selectJob(this);"';
              jobRow += ' data-id=' + result + '>' ;
              jobRow += obj.val();
              jobRow += '<span class="job-action"><i class="fa fa-pencil"></i>';
              jobRow += '<i class="fa fa-trash"></i></span>';
              jobRow += '</span>';

              $(jobRow).insertBefore($(".job-input"));
              obj.val("");
            });
          }
        });
      }
    },
    error: function(result, status) {
      notify('failure', "Error Code: " + status);
    }
  });
}
