dd<?php
include('server.php');
include('db/development/database.php');
$db = new DBLite();
?>
<!DOCTYPE html>
<html>
<head>
  <?php
  addHeaders('Test Grounds');
  ?>
  <style>
  #panel {
    height:100vh;
    background-color: pink;
  }
  #content {
    height: 100vh;
    background: green;
    overflow-y: auto;
    overflow-X: auto;
  }
  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <div id="panel" class="col-md-2">
        <span> Blah</span>
      </div>

      <div id="content" class="col-md-10">
        <div class="row">
          <div class="col-md-6" style="height:500px; background-color:blue;"></div>
          <div class="col-md-6" style="height:500px; background-color:gray;"></div>

          <div class="col-md-6" style="height:500px; background-color:orange;"></div>
          <div class="col-md-6" style="height:500px; background-color:yellow;">
            <h1>HELLO</h1>
            <?php
              $start_date = new DateTime("2016-06");
              $start_date->modify("first day of this month");
              $end_date = new DateTime("2016-06");
              $end_date->modify("last day of this month");
              echo "<h2>" . $start_date->format('Y-m-d H:i:s') . "</h1>";
              echo $end_date->format('Y-m-d H:i:s');
            ?>
          </div>
        </div>
      </div>

    </div>
  </div>
</body>
</html>
