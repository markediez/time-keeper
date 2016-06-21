<?php
  include('../development/database.php');
  $db = new DBLite();

  $query = "SELECT username, password, id FROM Users WHERE username = :username AND password = :password";
  $statement = $db->prepare($query);
  $statement->bindValue(':username', $_REQUEST['username'], SQLITE3_TEXT);
  $statement->bindValue(':password', $_REQUEST['password'], SQLITE3_TEXT);

  if($res = $statement->execute()) {
    $row = $res->fetchArray(SQLITE3_ASSOC);
    if($row["id"] > 0)
      echo $row["id"];
    else
      echo "false";
  } else {
    echo "false";
  }
?>
