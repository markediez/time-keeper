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
  private $db, $date, $day;

  function __construct($date) {
    $this->db = new DBSql();
    $this->date = new DateTime($date);
    $this->day = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
    $this->setDates($date);

  }

  function setDates($passedDate) {
    $this->date = array();
    $this->date['base'] = new DateTime($passedDate);
    $this->date['start'] = clone $this->date['base'];
    $this->date['end'] = clone $this->date['base'];
    $this->date['lastMonth'] = clone $this->date['base'];
    $this->date['nextMonth'] = clone $this->date['base'];

    $this->date['start']->setTime(0, 0, 0);
    $this->date['start']->setDate($this->date['base']->format('Y'), $this->date['base']->format('n'), 1);
    $this->date['end']->setTime(23, 59, 59);
    $this->date['end']->setDate($this->date['base']->format('Y'), $this->date['base']->format('n'), $this->date['base']->format('t'));
    $this->date['lastMonth']->setDate($this->date['base']->format('Y'), $this->date['base']->format('n') - 1, $this->date['base']->format('j'));
    $this->date['nextMonth']->setDate($this->date['base']->format('Y'), $this->date['base']->format('n') + 1, $this->date['base']->format('j'));
  }

  function setHeaders() {
    // Month header
    echo '<div class="col-md-12 text-center month no-padding">';
    echo '<a href="time-keeper.php?date=' . $this->date['lastMonth']->format('Y-m') . '" class="col-md-1 col-md-offset-4">';
    echo '<i class="fa fa-arrow-left fa-lg" aria-hidden="true"></i>';
    echo '</a>';
    echo '<span id="month-name" class="col-md-2">' . $this->date['base']->format('F Y') . '</span>';
    echo '<a href="time-keeper.php?date=' . $this->date['nextMonth']->format('Y-m') . '" class="col-md-1">';
    echo '<i class="fa fa-arrow-right fa-lg" aria-hidden="true"></i>';
    echo '</a>';
    echo '</div>';

    // Days header
    echo '<div class="week col-md-12 no-padding">';
    for ($i = 0; $i < 7; $i++) {
      echo '<div class="day-head col-md-1 text-center">';
      echo "<span>" . $this->day[$i] . "</span>";
      echo '</div>';
    }
    echo '</div>';
  } // function setHeaders

  function getJobs() {
    // Get Jobs
    $userJobs = array();
    $query = "SELECT id, title FROM Jobs WHERE user_id = :uid";
    $statement = $this->db->prepare($query);
    $statement->bindValue(':uid', $_SESSION['user_id']);
    $statement->execute();
    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $userJobs[$row['id']] = $row['title'];
    }

    return $userJobs;
  }

  function getShifts() {
    // Grab and organize all shifts with jobs
    $query = "SELECT Jobs.id as job_id, Jobs.title as job_title, WorkLog.title as work_title, WorkLog.start_time, WorkLog.end_time, WorkLog.id as work_id FROM WorkLog INNER JOIN Jobs ON WorkLog.job_id = Jobs.id WHERE WorkLog.user_id = :uid AND WorkLog.start_time >= :sd AND WorkLog.start_time <= :ed ORDER BY WorkLog.job_id ASC";
    $statement = $this->db->prepare($query);
    $statement->bindValue(':uid', $_SESSION['user_id']);
    $statement->bindValue(':sd', $this->date['start']->format('Y-m-d H:i:s'));
    $statement->bindValue(':ed', $this->date['end']->format('Y-m-d H:i:s'));
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

    return $jobArray;
  } // function getShifts

  function setDays($jobArray) {
    $userJobs = $this->getJobs();

    // Disabled days
    $dow =  $this->date['start']->format('D');
    $end_day = $this->date['end']->format('j');
    $start_day = $this->date['start']->format('w');

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
        $thisDay->setDate($this->date['start']->format('Y'), $this->date['start']->format('n'), $dom);
        foreach($jobArray as $job) {
          if ($job->getTotalHours($thisDay) > 0) {
            echo '<div class="event col-md-12 no-padding" onclick="showEventDetails(this,' . $toggle . ')" data-id="' . $job->id .'" data-date="' . $this->date['base']->format('Y-m') . "-" . $dom . '">';
            echo '<a class="col-md-12 no-padding"><span class="col-md-8 ">' . $job->title . '</span>';
            echo '<span class="col-md-4 ">' . number_format($job->getTotalHours($thisDay), 2) . '</span></a>';
            echo '</div>';
          }
        }
        echo '</div>'; // end event container
        echo '</div>'; // End day
      }
      echo '</div>'; // end week
    }
    echo '</div>';
  } // function setDays

  function setBoard($jobArray) {
    echo '<div id="board" class="col-md-2 no-padding">';
    $i = 0;
    foreach($jobArray as $job) {
      echo '<div class="board-post col-md-12 no-padding" data-target="board-' . $i . '">';
      echo '<a onclick="toggleCollapse(\'#board-' . $i . '\')">';
      echo '<span class="col-md-8 half-padding">' . $job->title .'</span>';
      echo '<span class="col-md-4 text-right half-padding">' . number_format($job->getTotalHours(), 2) .'</span>';
      echo '</a>';
      echo '<div id="board-' . $i++ . '" class="board-extra col-md-12 no-padding" data-collapse="true">';
      $allShifts = $job->getAllShifts();
      foreach($allShifts as $shift) {
        $selector = '.event[data-id=' . $job->id .'][data-date=' . $this->date['base']->format('Y-m') . '-' . $shift->getDay() . ']';
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
    echo '</div>';
  } // function setBoard

  function buildCalendar() {
    // Start building calendar
    $jobArray = $this->getShifts();
    echo '<div id="calendar" class="col-md-12 no-padding">';
    $this->setHeaders(); // Calendar headers are anything within the month name and name of days
    $this->setDays($jobArray);
    echo '</div>';
    $this->setBoard($jobArray);
  } // function buildCalendar

}
?>
