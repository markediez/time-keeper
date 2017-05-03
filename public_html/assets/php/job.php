<?php
/*
Copyright (c) 2016 Mark Diez

This file is part of Time Keeper

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/
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
            if ($shiftStart >= $start && $shiftStart <= $end) {
              $totalDuration += $shift->duration;
            }
          }
        }
      }

      return $totalDuration;
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
      $start = new DateTime($start_time);
      $end = new DateTime($end_time);
      $interval = $end->format('U') - $start->format('U');

      $newShift = new Shift();
      $newShift->date = new DateTime($start_time);;
      $newShift->title = $title;
      $newShift->start_time = $start_time;
      $newShift->end_time = $end_time;
      $newShift->duration = $interval / 3600;
      // if ($interval->days > 0) {
      //   $newShift->duration += 24 * $interval->days;
      // }

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
