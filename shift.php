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
?>
