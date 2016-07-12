<?php
include("server.php");
include("calendar.php");
include('db/development/database.php');
checkSession();
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
$(document).ready(function() {

});

function addTooltip(container) {
  container.addClass("tooltip-container");
  $('<div class="tooltip-text"></div>').appendTo($(".tooltip-container"));
}

function addTooltipHTML(html) {
  $(html).appendTo($(".tooltip-text"));
}

function getEventDetails(jobID) {

}

function showEventDetails(el, toggle = false) {
  // console.log(el);
  // Close any open Details
  closeEventDetails();

  // Set up tooltip
  addTooltip($(el).parent().parent());

  // Add Job Title
  let title = $(el).children().children(":first").text();
  addTooltipHTML('<div class="job-header"><span class="job-title">' + title + '</span><a onclick="closeEventDetails();"><i class="fa fa-close fa-lg event-close"></i></a></div>');

  let values = {};
  values.jid = $(el).data("id");
  values.date = $(el).data("date");

  $.ajax({
    url: "db/ajax/get-event.php",
    data: values,
    success: function(result) {
      let currShift = undefined;
      let prevShift = undefined;
      let eventIndex = 1;
      let entries = '<div class="event-task-list">';
      for(let i = 0; i < result.length; i++) {
        console.log(result[i]);
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
          console.log("in");
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

  if (toggle) {
    $(".tooltip-text").css("right", "103%");
  } else {
    $(".tooltip-text").css("left", "103%");
  }
}

function closeEventDetails() {
  // Remove Details
  $(".tooltip-container").removeClass("tooltip-container");
  $(".tooltip-text").remove();
}

</script>
</html>
