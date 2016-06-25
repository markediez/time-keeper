<?php
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
          <div class="col-md-6" style="height:500px; background-color:yellow;"></div>
        </div>
      </div>
      
    </div>
  </div>
</body>
</html>
