<!DOCTYPE html>
<html>
  <head>
    <?php
      include('server.php');
      addHeaders("Time Keeper");
    ?>
  </head>
  <body>
    <div id="index" class="container-fluid">

      <div id="header">
        <div>
        </div>
      </div>

      <div id="register" class="form">
        <div class="head">
          <img src="svg/logo.svg" class="head-logo">
          <span class="head-title">Time Keeper</span>
          <?php if ($error) {?>
          <p class="error">Something went wrong</p>
          <?php } ?>
        </div>
        <form id="register-form">
          <div>
            <input id="username" class="col-md-12 form-control" type="text" placeholder="username" name="username" required>
          </div>
          <div>
            <input class="col-md-12 form-control form-item" type="password" placeholder="password" name="password" required>
          </div>
          <div>
            <input id="email" class="col-md-12 form-control form-item" type="email" placeholder="email" name="email" required>
          </div>
          <a id="register-button" class="btn btn-primary form-item" onclick="return postFormSubmit('#register-form', '#register-form .tooltips input', 'http://localhost:8888/db/ajax/register.php')">Register</a>
          <a id="register-alter" href="/">
            <span class="small">Already have an account? Login</span>
          </a>
        </form>
      </div>
    </div>
  </body>
</html>
