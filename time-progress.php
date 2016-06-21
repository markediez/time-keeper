<?php
  include('server.php');
  include('db/development/database.php');
  checkSession();
  if(isset($_REQUEST['log_id'])) {
    $db = new DBLite();
    // Grab the row
    $query = "SELECT * FROM WorkLog WHERE id = :id AND user_id=:uid AND end_time IS NULL OR end_time = ''";
    $statement = $db->prepare($query);
    $statement->bindValue(':uid', $_SESSION['user_id']);
    $statement->bindValue(':id', $_GET['log_id']);
    $res = $statement->execute();
    $row = $res->fetchArray();
    if(!$row || $_SESSION['user_id'] != $row['user_id']) {
      redirect('index.php');
    }
  } else {
    redirect('index.php');
  }

  $query = "SELECT title FROM Jobs WHERE id = :jid AND user_id = :uid";
  $statement = $db->prepare($query);
  $statement->bindValue(':jid', $row['job_id']);
  $statement->bindValue(':uid', $_SESSION['user_id']);
  $res = $statement->execute();
  $jobRow = $res->fetchArray();
  $title = $jobRow['title'] . ": " . $row['title'];
?>
<!DOCTYPE html>
<html>
  <head>
    <?php
      addHeaders("In Progress")
    ?>
  </head>
  <body>
    <div class="container-fluid">
      <div id="header" class="row">
        <div class="col-md-12 no-padding">
          <h1>Time Keeper</h1>
        </div>
      </div>

      <div id="content" class="row">
        <div id="clock" class="col-md-4">
          <h2 class="col-md-12"><?= $title ?></h2>
          <div id="clock-bg" class="col-md-6 col-md-offset-3">
            <div id="clock-inner" class="flex">
              <div id="clock-middle"></div>
              <div id="clock-seconds"></div>
            </div>
          </div>
          <div class="col-md-12">
            <a onclick="stopJob(<?=$_GET['log_id']?>);" class="btn btn-danger col-md-8 col-md-offset-2">Stop</a>
          </div>
        </div>
      </div>

    </div>
  </body>
</html>
