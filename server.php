<?php
ob_start();
session_start();

function addHeaders($title) {
  echo "<title>$title</title>";
  echo '<meta charset="utf-8">';
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

?>
