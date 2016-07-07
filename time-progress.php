<?php
  include('server.php');
  include('db/development/database.php');
  checkSession();


  if(isset($_REQUEST['log_id'])) {
    $db = new DBLite();
    // Checks if a job is in progress
    $query = "SELECT * FROM WorkLog WHERE id = :id AND user_id=:uid AND end_time IS NULL OR end_time = ''";
    $statement = $db->prepare($query);
    $statement->bindValue(':uid', $_SESSION['user_id']);
    $statement->bindValue(':id', $_GET['log_id']);
    $res = $statement->execute();
    $row = $res->fetchArray();

    // If there isn't a job in progress, or someone else is trying to hack,
    // Go back go index -- it will go back to logged in index if user is logged properly
    if(!$row || $_SESSION['user_id'] != $row['user_id']) {
      redirect('index.php');
    }
  } else {
    redirect('index.php'); // If the log_id is not set, that means a user is not logged in.
  }

  // Retrieves Jobs related to current user
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
      <div class="row">
        <div id="panel" class="col-md-2">
          <?php
            addPanel();
          ?>
        </div> <!-- End Panel -->

        <div id="content" class="col-md-10">

          <div id="clock" class="row">
            <h2 class="col-md-12"><?= $title ?></h2>
            <div id="clock-bg" class="col-md-6 col-md-offset-3">
              <div id="clock-inner" class="flex">
                <div id="clock-middle"></div>
                <div id="clock-seconds"></div>
              </div>
            </div>
          </div> <!-- End Clock -->

          <div id="description" class="col-md-10 col-md-offset-1">
            <div id="entries" class="row">

              <div class="col-md-12 saved-entry flex">
                <span class="col-md-1 flex flex-vertical-center flex-end">1.</span>
                <input id="entry-1" type="text" class="col-md-10 entry" value="Added this" disabled>
                <span class="col-md-1 flex flex-vertical-center flex-space-around">
                  <a onclick="toggleEntry('#entry-1')"><i class="fa fa-pencil " aria-hidden="true"></i></a>
                  <a onclick="deleteEntry('#entry-1')"><i class="fa fa-trash" aria-hidden="true"></i></a>
                </span>
              </div>

              <div class="col-md-12 saved-entry flex">
                <span class="col-md-1 flex flex-vertical-center flex-end">2.</span>
                <input id="entry-2" type="text" class="col-md-10 entry" value="Added this too" disabled>
                <span class="col-md-1 flex flex-vertical-center flex-space-around">
                  <a onclick="toggleEntry('#entry-2')"><i class="fa fa-pencil " aria-hidden="true"></i></a>
                  <a onclick="deleteEntry('#entry-2')"><i class="fa fa-trash" aria-hidden="true"></i></a>
                </span>
              </div>

              <div class="col-md-12 saved-entry flex">
                <span class="col-md-1 flex flex-vertical-center flex-end">3.</span>
                <input id="entry-3" type="text" class="col-md-10 entry" value="Added this three" disabled>
                <span class="col-md-1 flex flex-vertical-center flex-space-around">
                  <a onclick="toggleEntry('#entry-3')"><i class="fa fa-pencil " aria-hidden="true"></i></a>
                  <a onclick="deleteEntry('#entry-3')"><i class="fa fa-trash" aria-hidden="true"></i></a>
                </span>
              </div>
            </div>

            <input id="new-entry" type="text" class="form-control" placeholder="What have you done?">
            <button class="btn btn-default" onclick="toggleEntry('#entry-1')">Enable</button>
          </div>

          <div class="col-md-12">
            <a onclick="stopJob(<?=$_GET['log_id']?>);" class="btn btn-danger col-md-8 col-md-offset-2">Stop</a>
          </div>
        </div> <!-- End Content -->
      </div> <!--End Row-->
    </div> <!-- End Container Fluid -->
  </body>
</html>
