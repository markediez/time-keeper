<?php
  include('../development/database.php');
  $username = $_REQUEST['username'];
  $password = $_REQUEST['password'];
  $email = $_REQUEST['email'];
  $currDateTime = date('Y-m-d H:i:s');
  $db = new DBLite();

  $statement = $db->prepare("INSERT INTO Users (role_id, username, password, email, created_at, updated_at)
            VALUES (2, :username, :password, :email, :start_time, :end_time)");

  $statement->bindParam(':username', $username, SQLITE3_TEXT);
  $statement->bindParam(':password', $password, SQLITE3_TEXT);
  $statement->bindParam(':email', $email, SQLITE3_TEXT);
  $statement->bindParam(':start_time', $currDateTime);
  $statement->bindParam(':end_time', $currDateTime);

  if($statement === false) {
    echo "Failure, statement is invalid.";
  } else {
    $res = $statement->execute();
    if($res !== false) {
      // Success
      echo "true";
    } else {
      // username/email already exists
      echo $db->lastErrorMsg();
    }
  }
?>
