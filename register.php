<?php
/*
Copyright (c) <2016> <Mark Diez>

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/
  include('assets/php/server.php');

  if (isset($_POST['g-recaptcha-response']) && $_POST['g-recaptcha-response'] == '') {
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
            <input id="username" class="col-md-12 form-control <?=$uError?>" type="text" placeholder="username" name="username" value="<?=$_POST['username']?>" maxlength="50" required>
          </div>
          <div>
            <input class="col-md-12 form-control form-item" type="password" placeholder="password" name="password" required>
          </div>
          <div>
            <input id="email" class="col-md-12 form-control form-item <?=$eError?>" type="email" placeholder="email" name="email" value="<?=$_POST['email']?>" maxlength="100" required>
          </div>
          <div class="tos">
            <!-- MIT LICENSE -->
            <div class="disclaimer">
              <p>
                THE SERVICE(TIME KEEPER) IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SERVICE OR THE USE OR OTHER DEALINGS IN THE SERVICE.
              </p>
              <p>
                BY AGREEING, YOU ACKNOWLEDGE THAT THE AUTHORS OR COPYRIGHT HOLDERS OF TIME KEEPER ARE NOT LIABLE FOR ANY LOSS OF DATA NOR DOES IT GUARANTEE ANY PRIVACY TO THE CONTENTS POSTED BY ITS USERS.
              </p>
              <p>
                PLEASE TAKE CARE TO AVOID POSTING OR STORING SENSITIVE INFORMATION.
              </p>
            </div>
            <div class="disclaimer-check">
              <input type="checkbox" name="tos" required>
              <span>I Agree</span>
            </div>
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
