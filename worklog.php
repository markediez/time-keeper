<?php
include("server.php");
include('db/development/database.php');
checkSession();
$db = new DBLite();
?>
<!DOCTYPE html>
<html>
<head>
  <?php
  addHeaders("Time Keeper");
  // Get Jobs
  $userJobs = array();
  $query = "SELECT id, title FROM Jobs WHERE user_id = :uid";
  $statement = $db->prepare($query);
  $statement->bindValue(':uid', $_SESSION['user_id']);
  $res = $statement->execute();
  while($row = $res->fetchArray()) {
    $userJobs[$row['id']] = $row['title'];
  }
  $start_date = new DateTime('first day of this month');
  $end_date = new DateTime('last day of this month');
  $month = $start_date->format('F Y');
  ?>
</head>
<body>
  <div class="container-fluid">

    <div id="header" class="row">
      <div class="col-md-12">
        <h1>Time Keeper</h1>
        <a href="time-keeper.php" class="col-md-1">Work</a>
        <a href="worklog.php" class="col-md-1">Log</a>
        <a href="logout.php" class="col-md-1">Logout</a>
      </div>
    </div>

    <div id="log" class="row">
      <div id="calendar" class="col-md-9 col-md-offset-1">
        <!-- Month Title -->
        <div class="week col-md-12 text-center">
          <div class="month col-md-12"><span><?=$month?></span></div>
        </div>

        <!-- Day Title -->
        <div class="week col-md-12">
          <?php
          $day=array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
          for ($i = 0; $i < 7; $i++) {
            echo '<div class="day-head col-md-1 text-center">';
            echo "<span>$day[$i]</span>";
            echo '</div>';
          }
          ?>
        </div>

        <!-- Weeks -->
        <?php
        $dow =  $start_date->format('D');
        $end_day = $end_date->format('d');
        $start_day = 0;
        while ($start_day < 7) {
          if(strpos($day[$start_day], $dow) !== false) break;
          $start_day++;
        }
        $start_date->setTime(0,0,0);
        $end_date->setTime(0,0,0);
        $start_date = $start_date->format('Y-m-d H:i:s');
        $end_date = $end_date->format('Y-m-d H:i:s');
        $query = "SELECT Jobs.id as job_id, Jobs.title as job_title, WorkLog.title as work_title, WorkLog.start_time, WorkLog.end_time FROM WorkLog INNER JOIN Jobs ON WorkLog.job_id = Jobs.id WHERE Worklog.user_id = :uid AND Worklog.start_time BETWEEN :sd AND :ed ORDER BY WorkLog.start_time ASC";
        $statement = $db->prepare($query);
        $statement->bindValue(':uid', $_SESSION['user_id']);
        $statement->bindValue(':sd', $start_date);
        $statement->bindValue(':ed', $end_date);
        $res = $statement->execute();

        $log = array();
        $total_duration = array();
        while($row = $res->fetchArray()) {
          $day = strtotime($row['start_time']);
          $day = date('d', $day);
          $sd = new DateTime($row['start_time']);
          $ed = new DateTime($row['end_time']);
          $interval = $ed->diff($sd);
          $duration = $interval->h + ($interval->i / 60);
          if(!is_array($log[$day])) {
            $log[$day] = array();
          }

          $event = array();
          $event['job_title'] = $row['job_title'];
          $event['work_title'] = $row['work_title'];
          $event['duration'] = $duration;
          array_push($log[$day], $event);
          $total_duration[$row['job_id']] += $duration;
        }
        // Disabled days

        for ($i = 1; $i <= 5; $i++) {
          echo '<div class="week col-md-12">';
          for ($j = 1; $j <= 7; $j++) {
            $day_num = 7 * ($i - 1) + $j;
            $dom = $day_num - $start_day;
            if($dom <= 0 || $dom  > $end_day) {
              echo '<div class="day col-md-1 no-padding disabled">';
            } else {
              echo '<div class="day col-md-1 no-padding">';
              echo '<span class="event-date col-md-9 no-padding">' . ($day_num - $start_day) . '</span>';
            }
            for ($event = 0; $event < sizeof($log[$dom]); $event++) {
              echo '<div class="event col-md-12 no-padding">';
              echo '<span class="col-md-8 ">' . $log[$dom][$event]['job_title'] . '</span>';
              echo '<span class="col-md-4 ">' . $log[$dom][$event]['duration'] . '</span>';
              echo '</div>';
            }
            echo '</div>';
          }
          echo '</div>';
        }
        ?>
      </div> <!-- End Calendar -->
      <div id="legend-container" class="col-md-2" style="background-color:purple; height: 100px;">
        <?php
          $query="";
        ?>
        <span>HI THERE</span>
      </div>
    </div> <!-- End Legend -->
  </div> <!-- End Log -->
</div>
</body>
</html>
