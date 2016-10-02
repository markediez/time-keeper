/*
Copyright (c) 2016 Mark Diez

This file is part of Time Keeper

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

// *******************************************************************
// This JS file consists of functions specific for calendar page
// Create, Read, Edit, Delete, Start, and Stop a Job
// Shows work
// Toggles board
// *******************************************************************

/**
 * This function adds a new job
 * @param {int} id - user id
 * @param {String} title - job name
 */
function insertJob(id, title) {
  var html = "";
  html += '<span class="job-item" onclick="selectJob(this);"';
  html += ' data-id=' + id + '>' ;
  html += '<span class="job-edit">';
  html += title;
  html += '</span>';
  html += '<span class="job-action">';
  html += '<a onclick="editJob(' + id + ', this)">';
  html += '<i class="fa fa-pencil"></i></a>';
  html += '<i class="fa fa-trash" onclick="deleteJob(\'' + title + '\',' + id + ')"></i></span>';
  html += '</span>';
  return html;
} // function insertJob

/**
 * This function highlights a job to emulate an <select multiple>
 * @param {jQuery} el - select job element
 */
function selectJob(el) {
  // remove red outline if applicable
  $(".select-multiple").css("border-color", "#EEEEEE");
  $(".select-multiple").css("box-shadow", "");
  $(".active").removeClass("active");
  $(el).addClass("active");
} // function selectJob

/**
 * This function edits a job title
 * @param {int} id - id of job
 * @param {jQuery} el - job element to edit
 */
function editJob(id, el) {
  $(el).parent().parent().addClass("job-on-edit");
  $(".job-action").hide();
  spanToTextInput($(".job-item[data-id=" + id + "] .job-edit"), "saveJob(this)");
} // function editJob

/**
 * This function deletes a job and its logs
 * @param {String} title - title of job
 * @param {int} id - id of job
 */
function deleteJob(title, id) {
  if (confirm("Are you sure you want to delete \"" + title + "\" and all of its contents?" ) == true) {
    // delete entries
    getData("WorkLog", {}, {'job_id': id}, {}, function(data) {
      var dataArray = JSON.parse(data);
      var options = {'async': false};

      for(var workLogIndex = 0; workLogIndex < dataArray.length; workLogIndex++) {
        var worklog = dataArray[workLogIndex];
        simpleQuery("Entries", "delete", {}, {'log_id': worklog.id}, options);
      }

      // delete WorkLog
      simpleQuery("WorkLog", "delete", {}, {'job_id': id}, options);

      // delete job
      simpleQuery("Jobs", "delete", {}, {'id': id}, {'async': true }, function(data) {
        $(".job-item[data-id=" + id + "]").remove();
        location.reload();
      });

    });
  }
} // function deleteJob

/**
 * This function starts a log for a job
 * @param {int} user_id - user id
 */
function startJob(user_id) {
  var id = $(".job-item.active").data("id");
  if (id === null) {
    $(".select-multiple").css("border-color", "red");
    $(".select-multiple").css("box-shadow", "0 0 10px red");
  } else {
    var timeNow = getTimeNow();
    var values = {
      'tableName': "WorkLog",
      'action': "insert",
      'values': {
        'user_id': user_id,
        'job_id': id,
        'title': "",
        'start_time': timeNow
      }
    };
    ajaxByPost("db/ajax/data-save.php", values, function(data) {
      var url = "time-progress.php?log_id=" + data;
      window.location.href = url;
    });
  }
} // function startJob

/**
 * This function saves a job
 * @param {jQuery} input - the job element
 */
function saveJob(input) {
  var container = $(input).parent();
  var values = {
    'tableName': "Jobs",
    'action': "update",
    'where': {
      'id': container.data("id"),
    },
    'values': {
      'title': $(input).val()
    }
  };

  ajaxByPost("db/ajax/data-save.php", values, function(data) {
    container.removeClass("job-on-edit");
    textInputToSpan($(input));
    $(".job-action").show();
  });
} // function saveJob

function showEventDetails(el, toggle) {
  // Close any open Details
  removeToolTip();

  if (toggle == undefined) {
    toggle = false;
  }

  // Set up tooltip
  addTooltip($(el).parent().parent(), (toggle) ? 'left' : 'right');

  // Add Job Title
  var title = $(el).children().children(":first").text();
  addTooltipHTML('<div class="job-header"><span class="job-title">' + title + '</span><a onclick="removeToolTip();"><i class="fa fa-close fa-lg event-close"></i></a></div>');

  showLoading(".tooltip-text");

  var values = {};
  values.jid = $(el).data("id");
  values.date = $(el).data("date");

  $.ajax({
    url: "db/ajax/get-event.php",
    data: values,
    success: function(result) {
      var htmlFragment = "";
      hideLoading();
      var currShift = undefined;
      var prevShift = undefined;
      var eventIndex = 1;
      var entries = '<div class="shift-task-list">';
      var inProgress = false;

      var shiftId = -1;

      for(var i = 0; i < result.length; i++) {
        currShift = result[i]['work_start'];

        // if a new shift occurs, set up the shift section;
        if (currShift.indexOf(prevShift) === -1) {
          if (prevShift !== undefined) {
              entries += '</div>'; // end previous shift
              entries = entries.replaceAll("\\", "");
              htmlFragment += entries;
              htmlFragment += '<div class="shift-action"><a onClick="editShift(' + shiftId + ')">Edit</a><a onClick="deleteShift(this,' + shiftId + ')">Delete</a></div>';
              htmlFragment += '</div>'; // close shift-container
              entries = '<div class="shift-task-list">';
              inProgress = false;
          }
          prevShift = currShift;
          var newShiftTitle = result[i]['work_title'];
          var shiftStart = result[i]['work_start'];
          var shiftEnd = result[i]['work_end'];
          shiftId = result[i]['work_id'];

          var pos = shiftStart.indexOf(" ");
          shiftStart = shiftStart.substring(pos + 1, pos + 6);
          if (shiftEnd != null) {
            pos = shiftEnd.indexOf(" ");
            shiftEnd = shiftEnd.substring(pos + 1,  pos + 6);
          } else {
            shiftEnd = "xx:xx";
            inProgress = true;
          }

          newShiftTitle = newShiftTitle.replaceAll("\\", "");
          htmlFragment += '<div class="shift-container"><div class="shift-header"><span class="shift-title">' + newShiftTitle + '</span><span class="shift-time">' + shiftStart + ' - ' + shiftEnd + '</span></div>';
          eventIndex = 1;
        } // end if

        // Add entries of shift
        if (result[i]['entry'] != null) {
          entries = entries + '<span class="shift-task"><span class="shift-task-num">' + eventIndex + '.</span>' + result[i]['entry'] + '</span>';
        }

        eventIndex++;
      } // end for

      if (inProgress) {
        entries = entries + '<span class="shift-task shift-progress animate-load">In Progress</span>';
      }

      entries = entries.replaceAll("\\", "");
      htmlFragment += entries; // addFinal Tasks
      htmlFragment += '</div>'; // close shift-container
      if (!inProgress) {
        htmlFragment += '<div class="shift-action"><a onClick="editShift(' + shiftId + ')">Edit</a><a onClick="deleteShift(this,' + shiftId + ')">Delete</a></div>';
      }

      addTooltipHTML(htmlFragment);
    },
    error: function(result) {
      alert("Something went wrong");
    }
  });
} // function showEventDetails

function deleteShift(el, shiftId) {
  if (confirm("Are you sure you want to delete this log?") == true) {
    // delete WorkLog
    simpleQuery("WorkLog", "delete", {}, {'id': shiftId}, {async:true}, function(result, status, xhr) {
      notify("success", "Successfully deleted the log", null);
      $(el).parent().parent().remove();
    });

    // delete the log's entries
    simpleQuery("Entries", "delete", {}, {'log_id': shiftId}, {async:true}, null);
  }
}

/**
 * Creates a view to edit a work log
 * @param  {int} shiftId - WorkLog id
 * @return {void}         Persists changes on save
 */
function editShift(shiftId) {
  var editFragment = new DocumentFragment();
  var loaded = 0;

  getData("WorkLog", {}, {'id': shiftId}, {}, function(result, status, xhr) {
    var data = JSON.parse(result);
    data = data[0];

    // DateTime
    $(editFragment).prepend('<div class="edit-date"><span>End</span><input type="text" value="' + data.end_time + '"></span></div>');
    loaded++;

    $(editFragment).prepend('<div class="edit-date"><span>Start</span><input type="text" value="' + data.start_time + '"></span></div>');

    // Title
    $(editFragment).prepend('<div class="job-header" data-id="' + shiftId + '"><input type="text" class="edit-title" value="' + data.title + '"><div><a onclick="saveShift();"><i class="fa fa-check fa-lg event-close"></i></a><a onclick="removeToolTip();"><i class="fa fa-close fa-lg event-close"></i></a></div></div>');

    renderShift(loaded, editFragment);
  });

  getData("Entries", {}, {'log_id': shiftId}, { 'orderCol': 'created_at', 'orderBy': 'DESC'}, function(result, status, xhr) {
    var data = JSON.parse(result);
    var html = "";
    for (var i = 0; i < data.length; i++) {
      html += "<textarea class='edit-entry' rows='3' data-id='" + data[i].id + "'>" + data[i].entry + "</textarea></li>"
    }

    $(editFragment).append(html);
    loaded++;
    renderShift(loaded, editFragment);
  });
}

function saveShift() {
  // Save title and datetime
  var workLogId = $(".tooltip-text").children()[0].getAttribute("data-id");
  var title = $("input", ".tooltip-text")[0].value;
  var st = $("input", ".tooltip-text")[1].value;
  var et = $("input", ".tooltip-text")[2].value;
  simpleQuery("WorkLog", "update", {"title": title, "start_time": st, "end_time": et}, {"id": workLogId}, {asyn:true}, null);

  // Save entries
  $("textarea", ".tooltip-text").each(function(x) {
    var el = $(this);
    var entryId = $(el).data("id");
    var entryText = $(el).val();
    simpleQuery("Entries", "update", {"entry": entryText}, {"id": entryId}, {asyn:true}, null);
  });

  notify("success", "Saved", null);
  removeToolTip();
}

function renderShift(loaded, fragment) {
  if (loaded == 2) {
    replaceTooltipHTML(fragment);
  }
}

function toggleCollapse(id) {
  var openBoard = '#' + $('[data-collapse="false"]').attr("id");

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
} // function toggleCollapse
