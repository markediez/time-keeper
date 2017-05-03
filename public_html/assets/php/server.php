<?php
/*
Copyright (c) 2016 Mark Diez

This file is part of Time Keeper

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/
ob_start();
session_start();
date_default_timezone_set("America/Los_Angeles");
include(realpath(dirname(__FILE__)) . "/../../../config/config.php");
include(realpath(dirname(__FILE__)) . "/../../db/development/database.php");

function addHeaders($title) {
  echo "<title>$title</title>";
  echo '<meta charset="utf-8">';
  echo "<link href='https://fonts.googleapis.com/css?family=Abel' rel='stylesheet' type='text/css'>";
  echo "<link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>";
  echo '<link rel="stylesheet" href="http://' . $_SERVER['HTTP_HOST'] . '/vendor/stylesheets/bootstrap.min.css">';
  echo '<link rel="stylesheet" href="http://' . $_SERVER['HTTP_HOST'] . '/vendor/stylesheets/font-awesome.min.css">';
  echo '<link rel="stylesheet" href="http://' . $_SERVER['HTTP_HOST'] . '/assets/stylesheets/style.css">';
}

function addScripts() {
  echo '<script type="text/javascript" src="http://' . $_SERVER['HTTP_HOST'] . '/vendor/js/jquery.js"></script>';
  echo '<script type="text/javascript" src="http://' . $_SERVER['HTTP_HOST'] . '/assets/js/script.js"></script>';
}

function setSession($post, $id = null) {

  $index = 0;
  $_SESSION[$index++] = $post['user_id'];
  $_SESSION[$index++] = $post['username'];
  $_SESSION[$index++] = true;
  $_SESSION[$index++] = time();

  $_SESSION['user_id'] = $id;
  $_SESSION['username'] = $post['username'];
  $_SESSION['valid'] = ($_SESSION['user_id']) ? true : false;
  $_SESSION['timeout'] = time();
  $_SESSION['folder'] = getcwd();
}

function checkSession($redirect = true) {
  // var_dump($_SESSION);
  if ($_SESSION['valid']) {
    return true;
  } else {
    if ($redirect) {
      redirect("index.php");
    } else {
      $_SESSION['valid'] = false;
      return false;
    }
  }
}

function redirect($url, $statusCode = 303) {
  header('Location: ' . $url, true, $statusCode);
  die();
}

function addPanel() {?>
  <div id="logo" class="row">
    <img src="svg/logo.svg" class="img-responsive col-md-4">
    <span class="col-md-8">Time Keeper</span>
  </div>
  <div id="links" class="row">
    <a onclick="showWork(<?=$_SESSION['user_id']?>);" class="col-md-12 btn btn-panel">Work</a>
    <a href="time-keeper.php" class="col-md-12 btn btn-panel">Calendar</a>
    <a href="logout.php" class="col-md-12 btn btn-panel">Logout</a>
  </div>
<?php }

function login($username, $password) {
  $db = new DBSql();

  $query = "SELECT * FROM Users WHERE username = :user AND password = :password";
  $stmt = $db->prepare($query);
  $stmt->bindParam(':user', $username);
  $stmt->bindParam(':password', $password);
  $stmt->execute();
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  $db = null;
  $stmt = null;
  return $row['id'];
}

function register($username, $password, $email) {
  $db = new DBSql();
  $currDateTime = date('Y-m-d H:i:s');

  $statement = $db->prepare("INSERT INTO Users (role_id, username, password, email, created_at, updated_at)
            VALUES (2, :username, :password, :email, :start_time, :end_time)");

  $statement->bindParam(':username', $username);
  $statement->bindParam(':password', $password);
  $statement->bindParam(':email', $email);
  $statement->bindParam(':start_time', $currDateTime);
  $statement->bindParam(':end_time', $currDateTime);
  try {
    $statement->execute();
    return $db->lastInsertId();
  } catch(PDOException $e) {
    return $e->getMessage();
  }
}

function verifyCaptcha($response) {
  // http://stackoverflow.com/questions/5647461/how-do-i-send-a-post-request-with-php
  $url = 'https://www.google.com/recaptcha/api/siteverify';
  $data = array('secret' => $GLOBALS['KEY']['captcha_secret'],
                'response' => $response);

  // use key 'http' even if you send the request to https://...
  $options = array(
      'http' => array(
          'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
          'method'  => 'POST',
          'content' => http_build_query($data)
      )
  );
  $context  = stream_context_create($options);
  $result = file_get_contents($url, false, $context);
  if ($result === FALSE) { /* Handle error */ }

  $result = json_decode($result);
  return $result->success;
}

?>
