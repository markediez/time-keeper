/*
Copyright (c) 2016 Mark Diez

This file is part of Time Keeper

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

// *******************************************************************
// This JS file consists of functions to
// Create, Read, Edit, Delete, Start, and Stop a Job
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
    saveDataPost("db/ajax/data-save.php", values, function(data) {
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

  saveDataPost("db/ajax/data-save.php", values, function(data) {
    container.removeClass("job-on-edit");
    textInputToSpan($(input));
    $(".job-action").show();
  });
} // function saveJob

/**
 * This function stops a log of a job
 * @param {int} logID - id of log
 */
function stopJob(logID) {
  var values = {
    'tableName': "WorkLog",
    'action': "update",
    'values': {
      'end_time': getTimeNow()
    },
    'where': {
      'id': logID
    }
  };

  saveDataPost('db/ajax/data-save.php', values, function(data, status) {
    redirect('time-keeper.php');
  });
} // function stopJob
