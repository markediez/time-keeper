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
  $(".event").click(function(e) {
    console.log("pageX: " + e.pageX + " pageY: " + e.pageY);
    let y = e.pageX + "px";
    let x = e.pageY + "px";
    // $(".tooltip-container").css({top: x, left: y});
  });
});

function showEventDetails(el) {
  console.log(el);
  console.log($(el).parent());
  $(el).parent().parent().addClass("tooltip-container");
  $('<div class="tooltip-text"><span>Hello Motto</span></div>').appendTo($(".tooltip-container"));
}
</script>
</html>
