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
              <?php
                // Query for all notes related to the worklog
                $noteQuery = "SELECT * FROM Entries WHERE log_id = :lid ORDER BY created_at ASC";
                $stmt = $db->prepare($noteQuery);
                $stmt->bindValue('lid', $_REQUEST['log_id']);
                $res = $stmt->execute();

                // show them all
                $i = 1;
                while ($row = $res->fetchArray()) {
              ?>
                <div class="col-md-12 saved-entry flex no-padding">
                  <span class="col-md-half flex flex-vertical-center flex-end no-padding"><?=$i?>.</span>
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

            <input id="new-entry" type="text" class="form-control row" placeholder="What have you done?">
            <a onclick="stopJob(<?=$_GET['log_id']?>);" class="btn btn-danger col-md-4 row">Stop</a>
          </div> <!-- End Description -->

          <button class="btn btn-default" onclick="notify('success', 'Test')">Test Notify</button>
          <button class="btn btn-default" onclick="notify('failure', 'Test')">Test Notify</button>
          <button class="btn btn-default" onclick="notify('warning', 'Test')">Test Notify</button>
        </div> <!-- End Content -->
      </div> <!--End Row-->
    </div> <!-- End Container Fluid -->
  </body>

  <!-- Scripts -->
  <script type="text/javascript">
  $(document).ready(function() {
    setEntryListener();

    $("#new-entry").keyup(function(e) {
      let thisEntry = $(this);
      let text = thisEntry.val();
      if (e.keyCode == 13 && text !== "") {
        let number = $("#entries").children().length + 1;

        // Append entry
        let newEntry = $('<div class="col-md-12 saved-entry flex no-padding"><span class="col-md-half flex flex-vertical-center flex-end no-padding">' + number + '.</span><input id="entry-' + number + '" type="text" name="entry" class="col-md-10 entry" value="' +   text + '" onblur="toggleEntry(\'#entry-' + number + '\')" disabled><span class="col-md-1 flex flex-vertical-center flex-space-around no-padding"><a onclick="toggleEntry(\'#entry-' + number + '\')"><i class="fa fa-pencil" aria-hidden="true"></i></a><a onclick="deleteEntry(\'#entry-' + number + '\')"><i class="fa fa-trash" aria-hidden="true"></i></a></span></div>')
        .appendTo($("#entries"));

        $(newEntry).keyup(function(e) {
          if (e.keyCode == 13) {
            saveEntry($(this));
            setEntryListener();
          }
        });

        saveEntry(thisEntry);
        $(this).val("");
      }
    }); // #new-entry
  });

  function setEntryListener() {
    $(".entry").keyup(function(e) {
      let entry = $(this);
      if (e.keyCode == 13) {
        let text = $(entry).val();

        saveEntry(entry);
        $(entry).blur();
      }
    });
  }

  function saveEntry(el) {
    let text = el.val();
    if (text == "" || text == undefined) {
      return false;
    } else {
      let entryID = $(el).data("id");
      let logID = $("#entries").data("id");
      let values = {
        'tableName': "Entries",
        'action': entryID ? "update" : "insert",
        'id': entryID,
        'values': {
          'log_id': logID,
          'entry': text
        }
      };

      saveDataPost('db/ajax/data-save.php', values, function(result, textStatus, jqXHR) {
        // result == 0 when an update takes place
        if(result > 0) {
          $(".entry:last").data("id", result);
        }
      });
    } // end else
  }

  function deleteEntry(target) {
    let values = {
      'tableName': "Entries",
      'action': "delete",
      'id': $(target).data("id")
    };

    saveDataPost('db/ajax/data-save.php', values, function(result, textStatus, jqXHR) {
      notify('default', "Entry Deleted Successfully" )
    });

    $(target).parent().remove();
  }

  function toggleEntry(target) {
    let isDisabled = $(target).attr("disabled");
    if (isDisabled === undefined) {
      $(target).attr("disabled", "disabled");
    } else {
      $(target).removeAttr("disabled");

      let text = $(target).val();
      $(target).focus().val("").val(text);
    }
  }

  </script>
</html>
