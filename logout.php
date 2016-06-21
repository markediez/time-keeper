<?php
  ob_start();
  session_start();

  var_dump($_SESSION);
  echo "<br><br>";
  echo $_SESSION[0];
  session_destroy();

  header('Location: index.php');
?>
