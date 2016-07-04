<?php
  class Shift {
    public $date; // date
    public $title; // strings
    public $duration; // float

    function getDuration() {
      return number_format((float)$this->duration, 2);
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

    function addShift($title, $start_time, $end_time) {
      $interval = new DateTime($end_time);
      $interval = $interval->diff(new DateTime($start_time));

      $newShift = new Shift();
      $newShift->date = new DateTime($start_time);;
      $newShift->title = $title;
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
  //   $days; // array of weeks
  //
  //   // $month - should be a DateTime
  //   // $session_id = user_id
    // function __construct($month = new DateTime(), $session_id) {
    //   $this->month = $month;
    // }
  }
?>