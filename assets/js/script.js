/*
Copyright (c) 2016 Mark Diez

This file is part of Time Keeper

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/
function redirect(url) {
  window.location.href = url;
}

// http://stackoverflow.com/questions/1144783/replacing-all-occurrences-of-a-string-in-javascript
function escapeRegExp(str) {
  return str.replace(/[.*+?^${}()|[\]\\]/g, "\\$&"); // $& means the whole matched string
}

String.prototype.replaceAll = function(search, replacement) {
    var target = this;
    search = escapeRegExp(search);
    return target.replace(new RegExp(search, 'g'), replacement);
};

// *******************************************************************
// This function shows a toaster-like notification
// @param {String} type - success, warning, failure
// @param {String} msg - text to show
// *******************************************************************
function notify(type, msg, callback) {
  var html = "<span id='notify' class = 'notify-" + type + " col-md-2'>" + msg + "</span>";
  var notification = $(html).appendTo("body");
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

function getData(tableName, wantedColumns, targetValue, order, callback ) {
  var data = {};
  data.tableName = tableName;
  data.where = targetValue;
  $.extend(data, wantedColumns);
  $.extend(data, order);

  $.ajax(
    {
        url: "db/ajax/data-get.php",
        type: "POST",
        data: data,
        success: function(result, textStatus, jqXHR) {
          callback(result, textStatus, jqXHR);
        },
        error: function(result, textStatus, jqXHR) {
          notify("failure", "Error: " + textStatus);
        }
    }
  );
}

function simpleQuery(tableName, action, values, where, options, callback) {
  var data = {};
  data.tableName = tableName;
  data.action = action;
  data.values = values;
  data.async = options.async;
  if (where != undefined) {
    data.where = where;
  }
  saveDataPost('db/ajax/data-save.php', data, function(result, textStatus, jqXHR) {
    if(typeof callback == 'function')
      callback(result, textStatus, jqXHR);
  });
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
}

/**
 * These scripts are not necessarily generalized. They just happened to be needed in all pages
 */

function getTimeNow() {
  var timeNow = new Date();
  var Y = timeNow.getFullYear();
  var m = ("0" + (timeNow.getMonth() + 1)).slice(-2);
  var d = ("0" + timeNow.getDate()).slice(-2);
  var H = ("0" + timeNow.getHours()).slice(-2);
  var i = ("0" + timeNow.getMinutes()).slice(-2);
  var s = ("0" + timeNow.getSeconds()).slice(-2);
  timeNow = Y + "-" + m + "-" + d + " " + H + ":" + i + ":" + s;
  return timeNow;
}


function showWork(user_id) {
  removeToolTip();
  addTooltip($("#links"), 'right');
  addTooltipHTML('<div class="job-header"><span class="job-title">Work</span><a onclick="removeToolTip();"><i class="fa fa-close fa-lg event-close"></i></a></div>');
  showLoading(".tooltip-text");

  $.ajax({
    url: "db/ajax/get-job.php",
    success: function(result) {
      hideLoading();
      var html = "";
      var res = JSON.parse(result);
      var startButton = '<button class="btn btn-primary" onclick="startJob(' + user_id + ');">Start</button>';
      if (res.status == "false") {
        var url = "time-progress.php?log_id=" + res.log_id;
        startButton = '<button class="btn btn-primary" onclick="redirect(\'' + url + '\');">In Progress</button>';
      }

      // List Jobs
      html = '<div id="job"><div id="job-form"><div class="select-multiple">';
      delete res.status;

      for(var i in res) {
        if(typeof res[i] == 'object')
          html += insertJob(res[i].id, res[i].title);
      }

      // Option to add a job
      html += '<input type="text" class="job-input" placeholder="Add a job">';

      // Start button
      html += '</div>' + startButton + '</div></div>';
      addTooltipHTML(html);

      // Add jobs on enter
      $(".job-input").keyup(function(e) {
        var obj = $(this);
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
            $(insertJob(result, obj.val())).insertBefore($(".job-input"));
            obj.val("");
          });
        }
      });
    },
    error: function(result, status) {
      notify('failure', "Error Code: " + status);
    }
  });
}

// *******************************************************************
// This function converts a span into an input
// @param {jQuery Object} jQueryEl - A jQuery object e.g. $(".item")
// @param {String} onblurFunctionCall - should be a string for a function call on blur
// *******************************************************************
function spanToTextInput(jQueryEl, onblurFunctionCall) {
  // http://stackoverflow.com/questions/1227286/get-class-list-for-element-with-jquery
  var classList = jQueryEl.attr("class").split(/\s+/);
  var textInput = '<input type="text" class="';
  var id = $(".span-input").length;

  if (onblurFunctionCall == undefined) {
    onblurFunctionCall = "";
  }

  for (var i = 0; i < classList.length; i++) {
    textInput += classList[i] + " ";
  }

  textInput += ' span-input span-input-' + id;
  textInput += '" onblur="';
  textInput += onblurFunctionCall + '">';

  jQueryEl.replaceWith($(textInput));
  $(".span-input-" + id).focus().val(jQueryEl.text());
  $(".span-input-" + id).keyup(function(e) {
    if (e.keyCode == 13) {
      $(this).blur();
    }
  });
}

// *******************************************************************
// This function converts a text input into a span
// @param {jQuery Object} jQueryEl - A jQuery object e.g. $(".item")
// *******************************************************************
function textInputToSpan(jQueryEl) {
  var classList = jQueryEl.attr("class").split(/\s+/);
  var spanInput = '<span class="';

  for (var i = 0; i < classList.length; i++) {
    // The if is necessary to remove span-input* class generated
    // by spanToTextInput
    if (classList[i].indexOf("span-input") === -1) {
      spanInput += classList[i] + " ";
    }
  }

  spanInput += '">';
  spanInput += jQueryEl.val();
  spanInput += '</span>';
  jQueryEl.replaceWith($(spanInput));
}

$(document).ready(function() {
  // http://stackoverflow.com/questions/1403615/use-jquery-to-hide-a-div-when-the-user-clicks-outside-of-it
  $(document).mouseup(function(e){
    var tooltipContainer = $(".tooltip-text");
    if (!tooltipContainer.is(e.target) && tooltipContainer.has(e.target).length === 0) {
      removeToolTip();
    }
  });
});
