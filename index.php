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
    <div id="content" class="container-fluid">

      <div id="header" class="row">
        <div class="col-md-12">
          <h1>Time Keeper</h1>
        </div>
        <div class="col-md-4">
          <p class="error">Invalid credentials</p>
        </div>
      </div>

      <div id="login" class="row">
        <form id="login-form" class="col-md-4" method="POST">
          <div class="tooltips col-md-12 no-padding">
            <input class="col-md-12 form-control" type="text" placeholder="username" name="username" required>
            <input class="col-md-12 form-control form-item" type="password" placeholder="password" name="password" required>
          </div>
          <a class="col-md-4 col-md-offset-3 btn btn-primary form-item" href="register.php">Register</a>
          <button id="login-button" class="col-md-4 col-md-offset-1 btn btn-primary form-item">Login</button>
        </form>
      </div>

    </div>
  </body>
</html>
