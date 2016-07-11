<?php
  class Shift {
    public $date; // date
    public $title; // strings
    public $start_time;
    public $end_time;
    public $duration; // float

    function getDuration() {
      return number_format((float)$this->duration, 2);
    }

    function getStartTime() {
      $st = new DateTime($this->start_time);
      return $st->format('H:i');
    }

    function getEndTime() {
      $et = new DateTime($this->end_time);
      return $et->format('H:i');
    }

    function getDay() {
      $day = new DateTime($this->start_time);
      return $day->format('j');
    }
  }

  class Job {
    public $title; // string
    public $id; // int
    public $shifts; // array of shift

    function __construct($title, $id) {
      $this->title = $title;
      $this->id = $id;
      $shifts = array();
    }

    function getTotalHours() {
      $totalDuration = 0;
      foreach($this->shifts as $day) {
        foreach($day as $shift) {
          $totalDuration += $shift->duration;
        }
      }

      return number_format($totalDuration, 2);
    }

    function getAllShifts() {
      $retShift = array();
      foreach($this->shifts as $day) {
        foreach($day as $shift) {
          array_push($retShift, $shift);
        }
      }

      return $retShift;
    }

    function addShift($title, $start_time, $end_time) {
      $interval = new DateTime($end_time);
      $interval = $interval->diff(new DateTime($start_time));

      $newShift = new Shift();
      $newShift->date = new DateTime($start_time);;
      $newShift->title = $title;
      $newShift->start_time = $start_time;
      $newShift->end_time = $end_time;
      $newShift->duration = $interval->h + ($interval->i / 60);

      $day = date('j', strtotime($start_time));
      if (!is_array($shifts[$day])) {
        $this->shifts[$day] = array();
      }

      array_push($this->shifts[$day], $newShift);
    }

    function getShifts($day) {
      return $this->shifts[$day];
    }

  } // class Job

  class Calendar {
    function buildCalendar() {
      // Get Jobs
      $db = new DBLite();
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

        // TEMP
        $query = "SELECT Jobs.id as job_id, Jobs.title as job_title, WorkLog.title as work_title, WorkLog.start_time, WorkLog.end_time, WorkLog.id as work_id FROM WorkLog INNER JOIN Jobs ON WorkLog.job_id = Jobs.id WHERE Worklog.user_id = :uid AND Worklog.start_time >= :sd AND Worklog.start_time <= :ed ORDER BY WorkLog.job_id ASC";
        $statement = $db->prepare($query);
        $statement->bindValue(':uid', $_SESSION['user_id']);
        $statement->bindValue(':sd', $start_date);
        $statement->bindValue(':ed', $end_date);
        $res = $statement->execute();

        $jobArray = array();
        $jobIndex = -1;
        $prev = -1;
        while($row = $res->fetchArray()) {
          if($prev != $row['job_id']) {
            $jobIndex++;
            $jobArray[$jobIndex] = new Job($row['job_title'], $row['job_id']);
            $prev = $row['job_id'];
          }

          $jobArray[$jobIndex]->addShift($row['work_title'], $row['start_time'], $row['end_time']);
        }
        // END TEMP

        // Disabled days

        for ($i = 1; $i <= 5; $i++) {
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
            foreach($jobArray as $job) {
              $shifts = $job->getShifts($dom);

              foreach($shifts as $shift) {
                // Add shift to day
                echo '<div class="event col-md-12 no-padding" onclick="showEventDetails(this,' . $toggle . ')" data-id="' . $job->id .'">';
                echo '<a class="col-md-12 no-padding"><span class="col-md-8 ">' . $job->title . '</span>';
                echo '<span class="col-md-4 ">' . $shift->getDuration() . '</span></a>';
                echo '</div>';
              }

            }
            echo '</div>'; // end event conatiner
            // echo '<div class="tooltip-text">';
            // echo '<span>Hello Motto</span>';
            // echo '</div>';
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
          echo '<div class="board-event col-md-12 no-padding">';
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
