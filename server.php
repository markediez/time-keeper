<?php
ob_start();
session_start();

function addHeaders($title) {
  echo "<title>$title</title>";
  echo '<meta charset="utf-8">';
  echo "<link href='https://fonts.googleapis.com/css?family=Abel' rel='stylesheet' type='text/css'>";
  echo "<link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>";
  echo '<link rel="stylesheet" href="http://' . $_SERVER['HTTP_HOST'] . '/vendor/bootstrap-3.3.6-dist/css/bootstrap.min.css">';
  echo '<link rel="stylesheet" href="http://' . $_SERVER['HTTP_HOST'] . '/vendor/font-awesome-4.6.3/css/font-awesome.min.css">';
  echo '<link rel="stylesheet" href="http://' . $_SERVER['HTTP_HOST'] . '/style.css">';
  echo '<script type="text/javascript" src="http://' . $_SERVER['HTTP_HOST'] . '/vendor/jquery/jquery.js"></script>';
  echo '<script type="text/javascript" src="http://' . $_SERVER['HTTP_HOST'] . '/behaviour.js"></script>';
  echo '<script type="text/javascript" src="http://' . $_SERVER['HTTP_HOST'] . '/script.js"></script>';
}

function setSession($post) {
  $index = 0;
  $_SESSION[$index++] = $post['user_id'];
  $_SESSION[$index++] = $post['username'];
  $_SESSION[$index++] = true;
  $_SESSION[$index++] = time();

  $_SESSION['user_id'] = $post['user_id'];
  $_SESSION['username'] = $post['username'];
  $_SESSION['valid'] = true;
  $_SESSION['timeout'] = time();
}

function checkSession() {
  if ($_SESSION['valid']) {
    return true;
  } else {
    $_SESSION['valid'] = false;
    redirect('index.php');
    return false;
  }
}

function redirect($url, $statusCode = 303) {
  header('Location: ' . $url, true, $statusCode);
  die();
}

function addPanel() { ?>
  <div id="logo" class="row">
    <img src="svg/logo.svg" class="img-responsive col-md-4">
    <span class="col-md-8">Time Keeper</span>
  </div>
  <div id="links" class="row">
    <a href="time-keeper.php" class="col-md-12 btn btn-panel">Work</a>
    <a href="worklog.php" class="col-md-12 btn btn-panel">Log</a>
    <a href="logout.php" class="col-md-12 btn btn-panel">Logout</a>
  </div>
<?php }

?>
