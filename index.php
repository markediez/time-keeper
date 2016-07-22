<?php
  ob_start();
  session_start();
  include('server.php');
  // If user is already logged in, redirect to time-keeper
  if(checkSession(false)) {
    redirect('time-keeper.php');
  }

  // If user attempted to log in, check credentials
  if (isset($_POST) && sizeof($_POST) > 0) {
    $id = login($_POST['username'], $_POST['password']);
    if ($id > 0) {
      setSession($_POST, $id);
      header("Refresh:0");
    } else {
      $error = true;
    }
  }
  // var_dump($_POST);
?>
<!DOCTYPE html>
<html>
  <head>
    <?php
      addHeaders("Time Keeper");
    ?>
  </head>
  <body>
    <div id="index" class="container-fluid">

      <!-- <div id="header" class="head flex">
        <div class="flex flex-vertical-center">
          <img src="svg/logo.svg" class="head-logo">
          <h1 class="head-title">Time Keeper</h1>
        </div>
      </div> -->

      <div id="login">
        <div class="head">
          <img src="svg/logo.svg" class="head-logo">
          <span class="head-title">Time Keeper</span>
          <?php if ($error) {?>
          <p class="error">Invalid credentials</p>
          <?php } ?>
        </div>
        <form id="login-form" method="POST">
          <div>
            <input class="form-control" type="text" placeholder="username" name="username" required>
            <input class="form-control form-item" type="password" placeholder="password" name="password" required>
          </div>
          <a class="btn btn-primary form-item" href="register.php">Register</a>
          <button id="login-button" class="btn btn-primary form-item">Login</button>
        </form>
      </div>

    </div>
  </body>
</html>
