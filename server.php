<?php
ob_start();
session_start();
include("db/development/database.php");
include("config.php");

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
}

function checkSession($redirect = true) {
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

  var_dump($_SESSION);
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
    <a href="time-keeper.php" class="col-md-12 btn btn-panel">Log</a>
    <a href="logout.php" class="col-md-12 btn btn-panel">Logout</a>
  </div>
<?php }

function login($username, $password) {
  $db = new DBLite();
  $query = "SELECT * FROM Users WHERE username = \"$username\" AND password = \"$password\"";
  $query = $db->escapeString($query);
  $stmt = $db->prepare($query);
  $res = $stmt->execute();
  $row = $res->fetchArray();

  if ($row['id'] > 0) {
    return $row['id'];
  } else {
    return 0;
  }
}

function register($username, $password, $email) {
  $db = new DBLite();
  $currDateTime = date('Y-m-d H:i:s');

  $statement = $db->prepare("INSERT INTO Users (role_id, username, password, email, created_at, updated_at)
            VALUES (2, :username, :password, :email, :start_time, :end_time)");

  $statement->bindParam(':username', $username, SQLITE3_TEXT);
  $statement->bindParam(':password', $password, SQLITE3_TEXT);
  $statement->bindParam(':email', $email, SQLITE3_TEXT);
  $statement->bindParam(':start_time', $currDateTime);
  $statement->bindParam(':end_time', $currDateTime);

  if($statement === false) {
    return "Failure, statement is invalid.";
  } else {
    $res = $statement->execute();
    if($res !== false) {
      // Success
      return $db->lastInsertRowID();
    } else {
      // username/email already exists
      return $db->lastErrorMsg();
    }
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
