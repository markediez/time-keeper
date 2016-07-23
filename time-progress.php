<?php
/*
Copyright (c) 2016 Mark Diez

This file is part of Time Keeper

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/
  include('server.php');
  checkSession();


  if(isset($_REQUEST['log_id'])) {
    $db = new DBLite();
    // Checks if a job is in progress
    $query = "SELECT * FROM WorkLog WHERE id = :id AND user_id=:uid AND end_time IS NULL OR end_time = ''";
    $query = $db->escapeString($query);
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
              <input type="text" placeholder="<click me to change title>" class="title-input" value="<?=$row['title']?>" onblur="saveTitle(this);">
            </div>
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
                  <span class="entry-num col-md-half flex flex-vertical-center flex-end no-padding"><?=$i?>.</span>
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
            <a onclick="stopJob(<?=$_GET['log_id']?>);" class="btn btn-primary col-md-4 row">Stop</a>
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
      let thisEntry = $(this);
      let text = thisEntry.val();
      if (e.keyCode == 13 && text !== "") {
        let number = $("#entries").children().length + 1;

        // Append entry
        let newEntry = $('<div class="col-md-12 saved-entry flex no-padding"><span class="col-md-half flex flex-vertical-center flex-end no-padding entry-num">' + number + '.</span><input id="entry-' + number + '" type="text" name="entry" class="col-md-10 entry" value="' +   text + '" onblur="toggleEntry(\'#entry-' + number + '\')" disabled><span class="col-md-1 flex flex-vertical-center flex-space-around no-padding"><a onclick="toggleEntry(\'#entry-' + number + '\')"><i class="fa fa-pencil" aria-hidden="true"></i></a><a onclick="deleteEntry(\'#entry-' + number + '\')"><i class="fa fa-trash" aria-hidden="true"></i></a></span></div>')
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

    $(".title-input").keyup(function(e) {
      if(e.keyCode == 13) {
        $(this).blur();
      }
    });
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

  function saveTitle(el) {
    let logID = $("#entries").data("id");
    let values = {
      'tableName': "WorkLog",
      'action': "update",
      'where': {
        'id': logID
      },
      'values': {
        'title': el.value
      }
    };

    saveDataPost('db/ajax/data-save.php', values, function(result, textStatus, jqXHR) {

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
        'where': {
          'id': entryID
        },
        'values': {
          'log_id': logID,
          'entry': text
        }
      };

      saveDataPost('db/ajax/data-save.php', values, function(result, textStatus, jqXHR) {
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
      'where': {
        'id': $(target).data("id")
      }
    };

    saveDataPost('db/ajax/data-save.php', values, function(result, textStatus, jqXHR) {
    });

    $(target).parent().remove();
    reindex();
  }

  function toggleEntry(target) {
    let isDisabled = $(target).attr("disabled");
    if (isDisabled === undefined) {
      $(target).attr("disabled", "disabled");
      saveEntry($(target));
    } else {
      $(target).removeAttr("disabled");

      let text = $(target).val();
      $(target).focus().val("").val(text);
    }
  }

  function reindex() {
    let i = 1;
    $(".entry-num").each(function() {
      $(this).text(i + ".");
      i++;
    });
  }

  </script>
</html>
