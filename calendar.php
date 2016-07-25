<?php
/*
Copyright (c) 2016 Mark Diez

This file is part of Time Keeper

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/
  include("job.php");
  class Calendar {
    function buildCalendar() {
      date_default_timezone_set('America/Los_Angeles');
      // Get Jobs
      $db = new DBSql();
      $userJobs = array();
      $query = "SELECT id, title FROM Jobs WHERE user_id = :uid";
      $statement = $db->prepare($query);
      $statement->bindValue(':uid', $_SESSION['user_id']);
      $statement->execute();
      while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        $userJobs[$row['id']] = $row['title'];
      }

      if(!isset($_GET['date'])) {
        $_GET['date'] = date('Y-m');
      }

      $d = new DateTime($_GET['date']);
      $start_date = new DateTime($_GET['date']);
      $end_date = new DateTime($_GET['date']);
      $lastMonth = new DateTime($_GET['date']);
      $nextMonth = new DateTime($_GET['date']);

      $start_date->setTime(0,0,0);
      $start_date->setDate($d->format('Y'), $d->format('n'), 1);
      $end_date->setTime(23,59,59);
      $end_date->setDate($d->format('Y'), $d->format('n'), $d->format('t'));

      $lastMonth->setDate($d->format('Y'), $d->format('n') - 1, $d->format('j'));
      $nextMonth->setDate($d->format('Y'), $d->format('n') + 1, $d->format('j'));
      $lastMonth = $lastMonth->format('Y-m');
      $nextMonth = $nextMonth->format('Y-m');
      $month = $start_date->format('F Y');
      $jobs = array();

      $thisMonth = $start_date->format('Y-m');
      ?>
      <div id="calendar" class="col-md-12 no-padding">
        <!-- Month Title -->
        <div class="col-md-12 text-center month no-padding">
          <a href="time-keeper.php?date=<?=$lastMonth?>" class="col-md-1 col-md-offset-4"><i class="fa fa-arrow-left fa-lg" aria-hidden="true"></i></a>
          <span id="month-name" class="col-md-2"><?=$month?></span>
          <a href="time-keeper.php?date=<?=$nextMonth?>" class="col-md-1"><i class="fa fa-arrow-right fa-lg" aria-hidden="true"></i></a>
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

        // Grab and organize all shifts with jobs
        $query = "SELECT Jobs.id as job_id, Jobs.title as job_title, WorkLog.title as work_title, WorkLog.start_time, WorkLog.end_time, WorkLog.id as work_id FROM WorkLog INNER JOIN Jobs ON WorkLog.job_id = Jobs.id WHERE WorkLog.user_id = :uid AND WorkLog.start_time >= :sd AND WorkLog.start_time <= :ed ORDER BY WorkLog.job_id ASC";
        $statement = $db->prepare($query);
        $statement->bindValue(':uid', $_SESSION['user_id']);
        $statement->bindValue(':sd', $start_date);
        $statement->bindValue(':ed', $end_date);
        $statement->execute();

        $jobArray = array();
        $jobIndex = -1;
        $prev = -1;
        while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
          if($prev != $row['job_id']) {
            $jobIndex++;
            $jobArray[$jobIndex] = new Job($row['job_title'], $row['job_id']);
            $prev = $row['job_id'];
          }

          $jobArray[$jobIndex]->addShift($row['work_title'], $row['start_time'], $row['end_time']);
        }
        // End grabbing data

        // Disabled days
        for ($i = 1; $i <= 6; $i++) {
          echo '<div class="week col-md-12 no-padding">';
          for ($j = 1; $j <= 7; $j++) {
            $day_num = 7 * ($i - 1) + $j;
            $dom = $day_num - $start_day;
            if($dom <= 0 || $dom  > $end_day) {
              echo '<div class="day col-md-1 no-padding disabled">';
            } else {
              // echo '<div class="day col-md-1 no-padding tooltip-container">';
              echo '<div class="day col-md-1 no-padding">';
              echo '<span class="event-date col-md-9 no-padding">' . ($day_num - $start_day) . '</span>';
            }

            // Shifts or Events of each day
            $toggle = ($j > 5) ? "true" : "false";
            echo '<div class="event-container col-md-12 no-padding">';
            $thisDay = new DateTime();
            $thisDay->setDate(date('Y'), date('n'), $dom);
            foreach($jobArray as $job) {
              if ($job->getTotalHours($thisDay) > 0) {
                echo '<div class="event col-md-12 no-padding" onclick="showEventDetails(this,' . $toggle . ')" data-id="' . $job->id .'" data-date="' . $thisMonth . "-" . $dom . '">';
                echo '<a class="col-md-12 no-padding"><span class="col-md-8 ">' . $job->title . '</span>';
                echo '<span class="col-md-4 ">' . $job->getTotalHours($thisDay) . '</span></a>';
                echo '</div>';
              }
            }
            echo '</div>'; // end event container
            echo '</div>'; // End day
          }
          echo '</div>'; // end week
        }
        ?>
      </div> <!-- End Weeks -->
    </div> <!-- End Calendar -->


    <div id="board" class="col-md-2 no-padding">
      <!-- Fill by AJAX -->
      <?php
      $i = 0;
      foreach($jobArray as $job) {
        echo '<div class="board-post col-md-12 no-padding" data-target="board-' . $i . '">';
        echo '<a onclick="toggleCollapse(\'#board-' . $i . '\')">';
        echo '<span class="col-md-8 half-padding">' . $job->title .'</span>';
        echo '<span class="col-md-4 text-right half-padding">' . $job->getTotalHours() .'</span>';
        echo '</a>';
        echo '<div id="board-' . $i++ . '" class="board-extra col-md-12 no-padding" data-collapse="true">';
        $allShifts = $job->getAllShifts();
        foreach($allShifts as $shift) {
          // var_dump($shift);

          $selector = '.event[data-id=' . $job->id .'][data-date=' . $thisMonth . '-' . $shift->getDay() . ']';
          echo '<div onclick="' . "$('$selector').click()" . '"class="board-event col-md-12 no-padding">';
          echo '<span class="col-md-8 half-padding">';
          echo '<span class="col-md-1 no-padding bold">' . $shift->getDay() . '</span>';
          echo '<span class="col-md-11 no-padding half-padding-left">' . $shift->title . '</span>';
          echo '</span>';
          echo '<span class="col-md-4 text-right no-padding half-padding-right board-title">' . $shift->getStartTime() . ' - ' . $shift->getEndTime() .'</span>';
          echo '</div>';
        }
        echo '</div>';
        echo '</div>';
      }
      ?>
    </div>
      <?php
    }
  }
?>
