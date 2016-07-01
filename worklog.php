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

  if(!isset($_GET['date'])) {
    $_GET['date'] = date('Y-m');
  }
  $start_date = new DateTime($_GET['date']);
  $end_date = new DateTime($_GET['date']);
  $lastMonth = new DateTime($_GET['date']);
  $nextMonth = new DateTime($_GET['date']);

  $start_date->modify("first day of this month");
  $end_date->modify("last day of this month");
  $lastMonth->modify("-1 months");
  $nextMonth->modify("+1 months");
  $lastMonth = $lastMonth->format('Y-m');
  $nextMonth = $nextMonth->format('Y-m');
  $month = $start_date->format('F Y');
  $jobs = array();
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
        <div id="calendar" class="col-md-12 no-padding">
          <!-- Month Title -->
          <div class="col-md-12 text-center month no-padding">
            <a href="worklog.php?date=<?=$lastMonth?>" class="col-md-1 col-md-offset-4"><i class="fa fa-arrow-left fa-lg" aria-hidden="true"></i></a>
            <span id="month-name" class="col-md-2"><?=$month?></span>
            <a href="worklog.php?date=<?=$nextMonth?>" class="col-md-1"><i class="fa fa-arrow-right fa-lg" aria-hidden="true"></i></a>
          </div>

          <!-- Day Title -->
          <div class="week col-md-12 no-padding">
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
          $dow =  $start_date->format('D'); // Wed
          $end_day = $end_date->format('d');
          $start_day = 0;
          while ($start_day < 7) {
            if(strpos($day[$start_day], $dow) !== false) break;
            $start_day++;
          }
          $start_date->setTime(0,0,0);
          $end_date->setTime(23,59,59);
          $start_date = $start_date->format('Y-m-d H:i:s');
          $end_date = $end_date->format('Y-m-d H:i:s');
          $query = "SELECT Jobs.id as job_id, Jobs.title as job_title, WorkLog.title as work_title, WorkLog.start_time, WorkLog.end_time, WorkLog.id as work_id FROM WorkLog INNER JOIN Jobs ON WorkLog.job_id = Jobs.id WHERE Worklog.user_id = :uid AND Worklog.start_time >= :sd AND Worklog.start_time <= :ed ORDER BY WorkLog.start_time ASC";
          $statement = $db->prepare($query);
          $statement->bindValue(':uid', $_SESSION['user_id']);
          $statement->bindValue(':sd', $start_date);
          $statement->bindValue(':ed', $end_date);
          $res = $statement->execute();
          $log = array();
          $total_duration = array();
          while($row = $res->fetchArray()) {
            $day = strtotime($row['start_time']);
            $day = date('j', $day);

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
            $event['work_id'] = $row['work_id'];
            $event['duration'] = $duration;
            array_push($log[$day], $event);
            $total_duration[$row['job_id']] += $duration;
          }

          // Disabled days

          for ($i = 1; $i <= 5; $i++) {
            echo '<div class="week col-md-12 no-padding">';
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
                echo '<div class="event col-md-12 no-padding" onclick="showEventDetails(this)" data-id="' . $log[$dom][$event]['work_id'] .'">';
                echo '<a class="col-md-12 no-padding"><span class="col-md-8 ">' . $log[$dom][$event]['job_title'] . '</span>';
                echo '<span class="col-md-4 ">' . number_format((float)$log[$dom][$event]['duration'], 2) . '</span></a>';
                echo '</div>';

                if (!isset($jobs[$log[$dom][$event]['job_title']])) {
                  $jobs[$log[$dom][$event]['job_title']] = 0;
                }

                $jobs[$log[$dom][$event]['job_title']] += $log[$dom][$event]['duration'];
              }
              echo '</div>';
            }
            echo '</div>';
          }
          ?>
        </div> <!-- End Weeks -->
      </div> <!-- End Calendar -->
    </div> <!-- End Content -->
  </div> <!-- End Row -->

  <div id="board" class="col-md-2 no-padding">
    <!-- Fill by AJAX -->
    <?php
    foreach($jobs as $key => $value) {
      echo '<div class="board-post col-md-12 no-padding">';
      echo '<span class="col-md-8">' . $key .'</span>';
      echo '<span class="col-md-4">' . number_format($value, 2) .'</span>';
      echo '</div>';
    }
    ?>
  </div>
</div>
</body>

<script type="text/javascript">
$(document).ready(function() {

});

function showEventDetails(element) {
  console.log(element);
}
</script>
</html>
