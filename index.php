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
            event.preventDefault();
            $('#login-button').click();
            console.log('stopped!');
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

      <div id="login" class="row">
        <form id="login-form" class="col-md-4" action="time-keeper.php" method="POST">
          <div class="tooltips col-md-12 no-padding">
            <input class="col-md-12 form-control" type="text" placeholder="username" name="username" required>
            <input class="col-md-12 form-control form-item" type="password" placeholder="password" name="password" required>
          </div>
          <a class="col-md-4 col-md-offset-3 btn btn-primary form-item" href="register.php">Register</a>
          <a id="login-button" class="col-md-4 col-md-offset-1 btn btn-primary form-item" onclick="postFormSubmit('#login-form', '#login-form input', 'http://localhost:8888/db/ajax/login.php')">Login</a>
          <input type="submit" class="hide">
        </form>
      </div>

    </div>
  </body>
</html>
