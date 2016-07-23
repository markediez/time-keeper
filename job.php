<?php
  include("shift.php");

  class Job {
    public $title; // string
    public $id; // int
    public $shifts; // array of shift

    function __construct($title, $id) {
      $this->title = $title;
      $this->id = $id;
      $shifts = array();
    }

    function getTotalHours($date = null) {
      $totalDuration = 0;

      if ($date == null) {
        foreach($this->shifts as $day) {
          foreach($day as $shift) {
            $totalDuration += $shift->duration;
          }
        }
      } else {
        $start = clone $date;
        $end = clone $date;
        $start->setTime(0,0,0);
        $end->setTime(23,59,59);
        foreach($this->shifts as $day) {
          foreach($day as $shift) {
            $shiftStart = new DateTime($shift->start_time);
            $shiftEnd = new DateTime($shift->end_time);
            if ($shiftStart >= $start && $shiftEnd <= $end) {
              $totalDuration += $shift->duration;
            }
          }
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
      if (!is_array($this->shifts[$day])) {
        $this->shifts[$day] = array();
      }

      array_push($this->shifts[$day], $newShift);
    }

    function getShifts($day) {
      return $this->shifts[$day];
    }

  } // class Job
?>
