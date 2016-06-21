<!DOCTYPE html>
<html>
  <head>
    <?php
      include('server.php');
      addHeaders("Time Keeper");
    ?>
    <script type="text/javascript">
      $( document ).ready(function() {
        $('input').keydown(function(event) {
          // Enter Key
          if(event.keyCode==13) {
            $('#register-button').click();
            return false;
          }
        });
      });
    </script>
  </head>
  <body>
    <div class="container-fluid">

      <div id="header" class="row">
        <div class="col-md-12">
          <h1>Time Keeper</h1>
        </div>
      </div>

      <div id="register" class="row">
        <form id="register-form" class="col-md-4">
          <div class="tooltips col-md-12 no-padding">
            <input id="username" class="col-md-12 form-control" type="text" placeholder="username" name="username" required>
          </div>
          <div class="tooltips col-md-12 no-padding">
            <input class="col-md-12 form-control form-item" type="password" placeholder="password" name="password" required>
          </div>
          <div class="tooltips col-md-12 no-padding">
            <input id="email" class="col-md-12 form-control form-item" type="email" placeholder="email" name="email" required>
          </div>
          <a id="register-button" class="col-md-4 col-md-offset-8 btn btn-primary form-item" onclick="return postFormSubmit('#register-form', '#register-form .tooltips input', 'http://localhost:8888/db/ajax/register.php')">Register</a>
          <a class='col-md-4 col-md-offset-8 text-right no-padding' href="/">
            <span class="small">Already have an account? Login</span>
          </a>
          <!-- <input type="submit" class="hide"> -->
        </form>
      </div>
    </div>
  </body>
</html>
