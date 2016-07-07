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

          <div id="clock" class="col-md-4">
            <div id="clock-bg" class="col-md-6 col-md-offset-3">
              <div id="clock-inner" class="flex">
                <div id="clock-middle"></div>
                <div id="clock-seconds"></div>
              </div>
            </div>
          </div> <!-- End Clock -->

          <div id="description" class="col-md-8">
            <h2 class="row title"><?= $title ?></h2>
            <div id="entries" class="row" data-id="<?=$_REQUEST['log_id']?>">

              <div class="col-md-12 saved-entry flex no-padding">
                <span class="col-md-half flex flex-vertical-center flex-end no-padding">1.</span>
                <input id="entry-1" type="text" class="col-md-10 entry" value="Added this" onblur="toggleEntry('#entry-1')" disabled>
                <span class="col-md-1 flex flex-vertical-center flex-space-around no-padding">
                  <a onclick="toggleEntry('#entry-1')"><i class="fa fa-pencil " aria-hidden="true"></i></a>
                  <a onclick="deleteEntry('#entry-1')"><i class="fa fa-trash" aria-hidden="true"></i></a>
                </span>
              </div>

              <div class="col-md-12 saved-entry flex no-padding">
                <span class="col-md-half flex flex-vertical-center flex-end no-padding">2.</span>
                <input id="entry-2" type="text" class="col-md-10 entry" value="Added this" onblur="toggleEntry('#entry-2')" disabled>
                <span class="col-md-1 flex flex-vertical-center flex-space-around no-padding">
                  <a onclick="toggleEntry('#entry-2')"><i class="fa fa-pencil " aria-hidden="true"></i></a>
                  <a onclick="deleteEntry('#entry-2')"><i class="fa fa-trash" aria-hidden="true"></i></a>
                </span>
              </div>

              <div class="col-md-12 saved-entry flex no-padding">
                <span class="col-md-half flex flex-vertical-center flex-end no-padding">3.</span>
                <input id="entry-3" type="text" class="col-md-10 entry" value="Added this" onblur="toggleEntry('#entry-3')" disabled>
                <span class="col-md-1 flex flex-vertical-center flex-space-around no-padding">
                  <a onclick="toggleEntry('#entry-3')"><i class="fa fa-pencil " aria-hidden="true"></i></a>
                  <a onclick="deleteEntry('#entry-3')"><i class="fa fa-trash" aria-hidden="true"></i></a>
                </span>
              </div>

            </div> <!-- End Saved Entries -->

            <input id="new-entry" type="text" class="form-control row" placeholder="What have you done?">
            <a onclick="stopJob(<?=$_GET['log_id']?>);" class="btn btn-danger col-md-4 row">Stop</a>
          </div> <!-- End Description -->
        </div> <!-- End Content -->
      </div> <!--End Row-->
    </div> <!-- End Container Fluid -->
  </body>

  <!-- Scripts -->
  <script type="text/javascript">
  $(document).ready(function() {
    setEntryListener();

    $("#new-entry").keyup(function(e) {
      let text = $(this).val();
      if (e.keyCode == 13 && text !== "") {
        let number = $("#entries").children().length + 1;

        // Append entry
        let newEntry = $('<div class="col-md-12 saved-entry flex no-padding"><span class="col-md-half flex flex-vertical-center flex-end no-padding">' + number + '.</span><input id="entry-' + number + '" type="text" class="col-md-10 entry" value="' +   text + '" onblur="toggleEntry(\'#entry-' + number + '\')" disabled><span class="col-md-1 flex flex-vertical-center flex-space-around no-padding"><a onclick="toggleEntry(\'#entry-' + number + '\')"><i class="fa fa-pencil" aria-hidden="true"></i></a><a onclick="deleteEntry(\'#entry-' + number + '\')"><i class="fa fa-trash" aria-hidden="true"></i></a></span></div>')
        .appendTo($("#entries"));

        $(newEntry).keyup(function(e) {
          if (e.keyCode == 13) {
            let text = $(this).val();
            saveEntry(text);
            setEntryListener();
          }
        });

        $(this).val("");
      }
    }); // #new-entry
  });

  function setEntryListener() {
    $(".entry").keyup(function(e) {
      let entry = $(this);
      if (e.keyCode == 13) {
        let text = $(entry).val();
        saveEntry(text);
        $(entry).blur();
      }
    });
  }
  </script>
</html>
