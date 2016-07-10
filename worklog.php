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
      </div> <!-- End Content -->
    </div> <!-- End Row -->
  </div> <!-- End container-fluid -->
</body>

<script type="text/javascript">
$(document).ready(function() {

});

function showEventDetails(element) {
  console.log(element);
}
</script>
</html>
