<?php
/*
Copyright (c) 2016 Mark Diez

This file is part of Time Keeper

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/
include('assets/php/server.php');
include('assets/php/calendar.php');
// Move to Login if the passed values are invalid or no session
if(sizeof($_POST) > 0) {
  setSession($_POST);
}

checkSession();
?>
<!DOCTYPE html>
<html>
<head>
  <?php
  addHeaders("Time Keeper");
  ?>
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <div id="panel" class="col-md-2">
        <?
          addPanel();
        ?>
      </div>

      <div id="content" class="col-md-10 flex">
        <?php
          $cal = new Calendar(isset($_GET['date']) ? $_GET['date'] : date('Y-m'));
          $cal->buildCalendar();
        ?>
      </div> <!-- End Content -->
    </div> <!-- End Row -->
  </div> <!-- End container-fluid -->
  <?php
  addScripts();
  echo '<script type="text/javascript" src="' . getBaseURL() . '/assets/js/time-keeper.js"></script>';
  ?>
</body>
</html>
