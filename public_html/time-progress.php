<?php
/*
Copyright (c) 2016 Mark Diez

This file is part of Time Keeper

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/
  include('assets/php/server.php');
  checkSession();

  if(isset($_REQUEST['log_id'])) {
    $db = new DBSql();
    // Checks if a job is in progress
    $query = "SELECT * FROM WorkLog WHERE id = :id AND user_id=:uid AND end_time IS NULL OR end_time = ''";
    $statement = $db->prepare($query);
    $statement->bindValue(':uid', $_SESSION['user_id']);
    $statement->bindValue(':id', $_GET['log_id']);
    $statement->execute();
    $row = $statement->fetch(PDO::FETCH_ASSOC);

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
  $statement->execute();
  $jobRow = $statement->fetch(PDO::FETCH_ASSOC);
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

          <div id="clock" class="col-md-4">
            <div id="clock-bg" class="col-md-6 col-md-offset-3">
              <div id="clock-inner" class="flex">
                <div id="clock-middle"></div>
                <div id="clock-seconds"></div>
              </div>
            </div>
          </div> <!-- End Clock -->

          <div id="description" class="col-md-8">
            <div class="row title">
              <span><?= $jobRow['title'] . ": " ?></span>
              <?php $row['title'] = str_replace("\\","", $row['title']); ?>
              <input type="text" placeholder="<click me to change title>" class="title-input" value="<?=$row['title']?>" onblur="saveTitle(this);">
            </div>
            <div id="entries" class="row" data-id="<?=$_REQUEST['log_id']?>">
              <?php
                // Query for all notes related to the worklog
                $noteQuery = "SELECT * FROM Entries WHERE log_id = :lid ORDER BY created_at ASC";
                $stmt = $db->prepare($noteQuery);
                $stmt->bindValue('lid', $_REQUEST['log_id']);
                $stmt->execute();

                // show them all
                $i = 1;
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
              ?>
                <div class="col-md-12 saved-entry flex no-padding">
                  <span class="entry-num col-md-half flex flex-vertical-center flex-end no-padding"><?=$i?>.</span>
                  <?php $row['entry'] = str_replace("\\","", $row['entry']); ?>
                  <input id="entry-<?=$i?>" name="entry" type="text" class="col-md-10 entry" value="<?=$row['entry']?>" onblur="toggleEntry('#entry-<?=$i?>')" data-id="<?=$row['id']?>" disabled>
                  <span class="col-md-1 flex flex-vertical-center flex-space-around no-padding">
                    <a onclick="toggleEntry('#entry-<?=$i?>')"><i class="fa fa-pencil " aria-hidden="true"></i></a>
                    <a onclick="deleteEntry('#entry-<?=$i?>')"><i class="fa fa-trash" aria-hidden="true"></i></a>
                  </span>
                </div>
              <?php
                  $i++;
                }
              ?>
            </div> <!-- End Saved Entries -->

            <div id="progress-action" class="flex row">
              <input id="new-entry" type="text" class="form-control" placeholder="What have you done?">
              <a onclick='addEntry()' class="btn btn-primary col-md-2 btn-inline">Add Entry</a>
              <a onclick="stopJob(<?=$_GET['log_id']?>);" class="btn btn-primary col-md-2 btn-inline">Stop</a>
            </div>
          </div> <!-- End Description -->
        </div> <!-- End Content -->
      </div> <!--End Row-->
    </div> <!-- End Container Fluid -->
    <?php
      addScripts();
      echo '<script type="text/javascript" src="http://' . $_SERVER['HTTP_HOST'] . '/assets/js/time-progress.js"></script>';
      echo '<script type="text/javascript" src="http://' . $_SERVER['HTTP_HOST'] . '/assets/js/time-keeper.js"></script>';
    ?>
  </body>
</html>
