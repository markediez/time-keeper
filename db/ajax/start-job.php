<?php
  ob_start();
  session_start();
  include('../development/database.php');
  
  if($_SESSION['valid']) {
    $uid = $_SESSION['user_id'];
    $title = $_REQUEST['title'];
    $jid = $_REQUEST['job_id'];
    $currDateTime = date('Y-m-d H:i:s');
    $db = new DBLite();

    $statement = $db->prepare("INSERT INTO WorkLog (user_id, job_id, title, start_time, created_at, updated_at)
              VALUES (:uid, :jid, :title, :start_time, :created_at, :updated_at)");

    $statement->bindParam(':uid', $uid, SQLITE3_INTEGER);
    $statement->bindParam(':jid', $jid, SQLITE3_INTEGER);
    $statement->bindParam(':title', $title, SQLITE3_TEXT);
    $statement->bindParam(':start_time', $currDateTime);
    $statement->bindParam(':created_at', $currDateTime);
    $statement->bindParam(':updated_at', $currDateTime);

    if($statement === false) {
      echo "Failure";
    } else {
      if ($statement->execute() !== false) {
        echo "true";
      } else {
        echo $db->lastErrorMsg();
      }
    }
  } else {
    echo "Invalid Session";
  }
?>
