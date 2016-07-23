<?php
/*
Copyright (c) 2016 Mark Diez

This file is part of Time Keeper

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/
include("server.php");
include("calendar.php");
// Move to Login if the passed values are invalid or no session
if(sizeof($_POST) > 0) {
  setSession($_POST);
}

// checkSession();
$db = new DBLite();
?>
<!DOCTYPE html>
<html>
<head>
  <?php
  addHeaders("Time Keeper");
  ?>
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <div id="panel" class="col-md-2">
        <?
          addPanel();
        ?>
      </div>

      <div id="content" class="col-md-10 flex">
        <?php
          $cal = new Calendar();
          $cal->buildCalendar();
        ?>

        <!-- <div class="tooltip-container col-md-3 no-padding"> -->
          <!-- <div class="tooltip-text"> -->
            <!-- <span class="tooltip-text col-md-12 no-padding">Hello Motto</span> -->
          <!-- </div> -->
        <!-- </div> -->
      </div> <!-- End Content -->
    </div> <!-- End Row -->
  </div> <!-- End container-fluid -->
</body>

<script type="text/javascript">
function getEventDetails(jobID) {

}

function showEventDetails(el, toggle = false) {
  // Close any open Details
  removeToolTip();

  // Set up tooltip
  addTooltip($(el).parent().parent(), (toggle) ? 'left' : 'right');

  // Add Job Title
  let title = $(el).children().children(":first").text();
  addTooltipHTML('<div class="job-header"><span class="job-title">' + title + '</span><a onclick="removeToolTip();"><i class="fa fa-close fa-lg event-close"></i></a></div>');

  showLoading(".tooltip-text");

  let values = {};
  values.jid = $(el).data("id");
  values.date = $(el).data("date");

  $.ajax({
    url: "db/ajax/get-event.php",
    data: values,
    success: function(result) {
      hideLoading();
      let currShift = undefined;
      let prevShift = undefined;
      let eventIndex = 1;
      let entries = '<div class="event-task-list">';
      for(let i = 0; i < result.length; i++) {
        currShift = result[i]['work_start'];
        // if a new shift occurs, set up the shift section;
        if (currShift.indexOf(prevShift) === -1) {
          if (prevShift !== undefined) {
              entries += '</div>'; // end previous shift
              addTooltipHTML(entries);
              entries = '<div class="event-task-list">';
          }
          prevShift = currShift;
          let newShiftTitle = result[i]['work_title'];
          let shiftStart = result[i]['work_start'];
          let shiftEnd = result[i]['work_end'];
          let pos = shiftStart.indexOf(" ");
          shiftStart = shiftStart.substring(pos + 1, pos + 6);
          pos = shiftEnd.indexOf(" ");
          shiftEnd = shiftEnd.substring(pos + 1,  pos + 6);
          addTooltipHTML('<div class="event-header"><span class="event-title">' + newShiftTitle + '</span><span class="event-time">' + shiftStart + ' - ' + shiftEnd + '</span></div>');
          eventIndex = 1;
        } // end if

        // Add entries of shift
        if (result[i]['entry'] != null) {
          entries = entries + '<span class="event-task"><span class="event-task-num">' + eventIndex + '.</span>' + result[i]['entry'] + '</span>';
        }

        eventIndex++;
      } // end for

      addTooltipHTML(entries); // addFinal Tasks



    },
    error: function(result) {
      alert("Something went wrong");
    }
  });
}

</script>
</html>
