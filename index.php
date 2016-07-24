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
  include('server.php');
  // If user is already logged in, redirect to time-keeper
  if(checkSession(false)) {
    redirect('time-keeper.php');
  }

  // If user attempted to log in, check credentials
  if (isset($_POST) && sizeof($_POST) > 0) {
    $error = false;
    $id = login($_POST['username'], $_POST['password']);
    if ($id > 0) {
      setSession($_POST, $id);
      header("Refresh:0");
    } else {
      $error = true;
    }
  }
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
      <div id="login">
        <div class="head">
          <img src="svg/logo.svg" class="head-logo">
          <span class="head-title">Time Keeper</span>
        </div>
        <?php if ($error) {?>
        <p class="error">Invalid credentials</p>
        <?php } ?>
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
