$(document).ready(function() {
  setEntryListener();

  $("#new-entry").keyup(function(e) {
    var thisEntry = $(this);
    var text = thisEntry.val();
    if (e.keyCode == 13 && text !== "") {
      var number = $("#entries").children().length + 1;

      // Append entry
      var newEntry = $('<div class="col-md-12 saved-entry flex no-padding"><span class="col-md-half flex flex-vertical-center flex-end no-padding entry-num">' + number + '.</span><input id="entry-' + number + '" type="text" name="entry" class="col-md-10 entry" value="' +   text + '" onblur="toggleEntry(\'#entry-' + number + '\')" disabled><span class="col-md-1 flex flex-vertical-center flex-space-around no-padding"><a onclick="toggleEntry(\'#entry-' + number + '\')"><i class="fa fa-pencil" aria-hidden="true"></i></a><a onclick="deleteEntry(\'#entry-' + number + '\')"><i class="fa fa-trash" aria-hidden="true"></i></a></span></div>')
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
}); // function onready

/**
 * This function sets the listenter to save entries on enter
 */
function setEntryListener() {
  $(".entry").keyup(function(e) {
    var entry = $(this);
    if (e.keyCode == 13) {
      var text = $(entry).val();

      saveEntry(entry);
      $(entry).blur();
    }
  });
} // function setEntryListener

/**
 * This function saves the title of the WorkLog
 * @param {jQuery} el - the title element
 */
function saveTitle(el) {
  var logID = $("#entries").data("id");
  var values = {
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
    notify("success", "Saved");
  });
} // function saveTitle

/**
 * This function saves an entry
 * @param {jQuery} el - entry element to save
 */
function saveEntry(el) {
  var text = el.val();
  if (text == "" || text == undefined) {
    return false;
  } else {
    var entryID = $(el).data("id");
    var logID = $("#entries").data("id");
    var values = {
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
        notify("success", "Saved");
        $(".entry:last").data("id", result);
      }
    });
  } // end else
} // function saveEntry

/**
 * This function deletes an entry
 * @param {jQuery} el - entry element to save
 */
function deleteEntry(target) {
  var values = {
    'tableName': "Entries",
    'action': "delete",
    'where': {
      'id': $(target).data("id")
    }
  };

  saveDataPost('db/ajax/data-save.php', values, function(result, textStatus, jqXHR) {
    notify("success", "Saved");
  });

  $(target).parent().remove();
  reindex();
} // fucntion deleteEntry


/**
 * This function edits an entry
 * @param {selector} target - css selector of target
 */
function toggleEntry(target) {
  var isDisabled = $(target).attr("disabled");
  if (isDisabled === undefined) {
    $(target).attr("disabled", "disabled");
    saveEntry($(target));
  } else {
    $(target).removeAttr("disabled");

    var text = $(target).val();
    $(target).focus().val("").val(text);
  }
} // function toggleEntry

/**
 * This function re-numbers the entries
 */
function reindex() {
  var i = 1;
  $(".entry-num").each(function() {
    $(this).text(i + ".");
    i++;
  });
} // function reindex

/**
 * This function stops a log of a job
 * @param {int} logID - id of log
 */
function stopJob(logID) {
  var values = {
    'tableName': "WorkLog",
    'action': "update",
    'values': {
      'end_time': getTimeNow()
    },
    'where': {
      'id': logID
    }
  };

  saveDataPost('db/ajax/data-save.php', values, function(data, status) {
    notify("success", "Saved");
    redirect('time-keeper.php');
  });
} // function stopJob
