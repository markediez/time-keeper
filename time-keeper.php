<?php
  include("server.php");
  include('db/development/database.php');
  $db = new DBLite();

  // Move to Login if the passed values are invalid or no session
  if(sizeof($_POST) > 0) {
    setSession($_POST);
  } else {
    checkSession();
  }

  // If there is currently a time log in progress, redirect to waiting
  $query = "SELECT id FROM WorkLog WHERE user_id=:uid and end_time IS NULL OR end_time = ''";
  $statement = $db->prepare($query);
  $statement->bindValue(':uid', $_SESSION['user_id']);
  $res = $statement->execute();
  $row = $res->fetchArray();
  if($row) {
    redirect('time-progress.php?log_id=' . $row['id']);
  }
?>
<!DOCTYPE html>
<html>
  <head>
    <?php
      addHeaders("Time Keeper");
    ?>
    <script type="text/javascript">
      $( document ).ready(function() {
        $('.clickable-row').click(function() {
          // Remove previous active
          $('.clickable-row.active').removeClass("active");
          $(this).addClass("active");
        });

        $('#title-input').keydown(function(event) {
          // Enter Key
          if(event.keyCode==13) {
            $('#start-button').click();
            return false;
          }
        });

        $('#job-input').keydown(function(event) {
          // Enter Key
          if(event.keyCode==13) {
            $('#job-button').click();
            return false;
          }
        });
      });
    </script>
  </head>
  <body>
    <div class="container-fluid">

      <div id="header" class="row">
        <div class="col-md-12">
          <h1>Time Keeper</h1>
          <a href="time-keeper.php" class="col-md-1">Work</a>
          <a href="worklog.php" class="col-md-1">Log</a>
          <a href="logout.php" class="col-md-1">Logout</a>
        </div>
      </div>

      <div id="time-keeper-form" class="row">
        <form id="time-start" class="col-md-4">
          <div class="col-md-12 no-padding">
            <input id="title-input" type="text" class="form-control" name="title" placeholder="Enter Title" required>
          </div>
          <div class="tooltips col-md-12 no-padding form-item">
          <div id="choices" class="col-md-12">
              <table class="table-choice">
                <?php
                  $statement = $db->prepare("SELECT id, title FROM Jobs WHERE user_id = :id");
                  $statement->bindValue(':id', $_SESSION['user_id']);
                  $result = $statement->execute();
                  while($row = $result->fetchArray()) {
                    echo "<tr class=\"clickable-row\">";
                      echo "<td data-id=" . $row['id'] .">";
                        echo $row['title'];
                      echo "</td>";
                      echo "<td></td>";
                    echo "</tr>";
                  }
                ?>
                <tr>
                  <td>
                    <div class="tooltips col-md-12 no-padding">
                      <input id="job-input" type="text" class="form-control" placeholder="Add a new job ..." name="title">
                    </div>
                  </td>
                  <td><a id="job-button" onclick="addJob()"><i class="fa fa-plus-square-o fa-lg" aria-hidden="true"></i></a></td>
                </tr>
              </table>
            </div>
          </div>
          <div class="col-md-12 form-item">
            <a id="start-button" class="col-md-6 col-md-offset-3 btn btn-primary" onclick="startJob()">Start</a>
          </div>
          <input type="submit" class="hidden">
        </form>
      </div>
    </div>
  </body>
</html>
