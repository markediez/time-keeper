<?php
  include('server.php');

  if ($_POST['g-recaptcha-response'] == '') {
    $error = true;
    $errorResponse = "Are you a robot?";
  } else {
    // If you're not a robot and the captcha is valid then continue registering
    if (verifyCaptcha($_POST['g-recaptcha-response'])) {
      $validReg = register($_POST['username'], $_POST['password'], $_POST['email']);

      // If it was invalid, then show error
      if ($validReg <= 0) {
        $error = true;

        // Check if username or email error
        $uError = "";
        $eError = "";
        if (strpos($validReg, "username")) {
          $errorResponse = "Username is not available<br>";
          $uError = "error-input";
        }
        if (strpos($validReg, "email"))  {
          $errorResponse = "Email is not available<br>";
          $eError = "error-input";
        }

      } else {
        // On successful registration, setSession and log in
        setSession($_POST, $validReg);
        header("Location: index.php");
      } // if validReg

    } // end captcha verification
  }
?>
<!DOCTYPE html>
<html>
  <head>
    <?php
      addHeaders("Time Keeper");
    ?>
  <script src='https://www.google.com/recaptcha/api.js'></script>
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
        </div>
        <form id="register-form" method="POST">
          <?php if ($error) {?>
          <p class="error"><?=$errorResponse?></p>
          <?php } ?>
          <div>
            <input id="username" class="col-md-12 form-control <?=$uError?>" type="text" placeholder="username" name="username" value="<?=$_POST['username']?>" required>
          </div>
          <div>
            <input class="col-md-12 form-control form-item" type="password" placeholder="password" name="password" required>
          </div>
          <div>
            <input id="email" class="col-md-12 form-control form-item <?=$eError?>" type="email" placeholder="email" name="email" value="<?=$_POST['email']?>" required>
          </div>
          <div class="g-recaptcha" data-sitekey="<?=$KEY['captcha_form']?>"></div>
          <button id="register-button" class="btn btn-primary form-item">Register</button>
          <a id="register-alter" href="/">
            <span class="small">Already have an account? Login</span>
          </a>
        </form>
      </div>
    </div>
  </body>
</html>
