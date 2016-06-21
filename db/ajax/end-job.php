<?php
  ob_start();
  session_start();
  include('../development/database.php');
  if($_SESSION['valid']) {
    $lid = $_GET['log_id'];
    $currDateTime = date('Y-m-d H:i:s');
    $db = new DBLite();

    $statement = $db->prepare("UPDATE WorkLog SET end_time = :end_time, updated_at = :updated_at WHERE id = :log_id");

    $statement->bindParam(':log_id', $lid, SQLITE3_INTEGER);
    $statement->bindParam(':end_time', $currDateTime);
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
