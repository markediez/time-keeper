<?php
  ob_start();
  session_start();
  include('../development/database.php');
  if($_SESSION['valid']) {
    $uid = $_SESSION['user_id'];
    $title = $_REQUEST['title'];
    $currDateTime = date('Y-m-d H:i:s');
    $db = new DBLite();

    $statement = $db->prepare("INSERT INTO Jobs (user_id, title, created_at, updated_at)
              VALUES (:uid, :title, :start_time, :end_time)");

    $statement->bindParam(':uid', $uid, SQLITE3_INTEGER);
    $statement->bindParam(':title', $title, SQLITE3_TEXT);
    $statement->bindParam(':start_time', $currDateTime);
    $statement->bindParam(':end_time', $currDateTime);

    if($statement === false) {
      echo "Failure";
    } else {
      // Check to see if the title already exists
      $query = "SELECT * FROM Jobs WHERE user_id = :uid AND title = :title";
      $stmt = $db->prepare($query);
      $stmt->bindValue(':uid', $uid);
      $stmt->bindValue(':title', $title);
      $result = $stmt->execute();
      $row = $result->fetchArray();
      if($row !== false) {
        echo "exists";
        return;
      }
      if ($res = $statement->execute() !== false) {
        $query = "SELECT id FROM Jobs WHERE user_id = :uid ORDER BY id DESC";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':uid', $_SESSION['user_id']);
        $result = $stmt->execute();
        $row = $result->fetchArray();
        $id = $row['id'];
        echo 'true ' . $id;
      } else {
        echo $db->lastErrorMsg();
      }
    }
  } else {
    echo "Invalid Session";
  }
?>
