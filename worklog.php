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

function showEventDetails(el, toggle = false) {
  // TODO: Show at left side for friday and saturday
  // Close any open Details
  closeEventDetails();

  // Set up tooltip
  addTooltip($(el).parent().parent());

  // Add Job Title
  addTooltipHTML('<div class="job-header"><span class="job-title">DSS IT</span><a onclick="closeEventDetails();"><i class="fa fa-close fa-lg event-close"></i></a></div>');

  // Add Event / Shift Title
  addTooltipHTML('<div class="event-header"><span class="event-title">This is a test title</span><span class="event-time">13:00 - 15:00</span></div>');

  // Add Event / Shift Tasks
  addTooltipHTML('<div class="event-task-list"><span class="event-task"><span class="event-task-num">1.</span> This is this</span><span class="event-task"><span class="event-task-num">2.</span> This is this</span><span class="event-task"><span class="event-task-num">3.</span> This is this</span></div>');

  // Add Event / Shift Title
  addTooltipHTML('<div class="event-header"><span class="event-title">This is a test long title the quick brown fox jumps over the lazy dog near the riverbank</span><span class="event-time">16:30 - 18:00</span></div>');

  // Add Event / Shift Tasks
  addTooltipHTML('<div class="event-task-list"><span class="event-task"><span class="event-task-num">1.</span> This is this the quick brown fox jumps over the lazy dog near the riverbank</span><span class="event-task"><span class="event-task-num">2.</span> This is this</span><span class="event-task"><span class="event-task-num">3.</span> This is this</span></div>');

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
